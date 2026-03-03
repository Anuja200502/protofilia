<?php
/**
 * Protofilia Admin - Profile Settings
 */
require_once __DIR__ . '/../includes/functions.php';

if (!is_admin_logged_in()) {
    redirect(SITE_URL . '/admin/login.php');
}

$settings = get_settings();
$projects = get_projects();
$messages = get_messages();
$unreadCount = count_unread_messages();
$totalProjects = count($projects);
$totalMessages = count($messages);
$currentAdminPage = 'settings';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings | <?php echo SITE_NAME; ?> Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <style>
        body { background: var(--bg-primary); overflow-x: hidden; }
        .bg-grid, .bg-glow, .bg-glow-1, .bg-glow-2, .bg-glow-3 { display: none !important; }

        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 16px;
            left: 16px;
            z-index: 1100;
            width: 44px;
            height: 44px;
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            cursor: pointer;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            transition: var(--transition-normal);
        }
        .sidebar-toggle i { width: 20px; height: 20px; }
        .sidebar-toggle:hover {
            border-color: var(--border-accent);
            background: var(--bg-card-hover);
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 1049;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .sidebar-overlay.active { opacity: 1; }

        .admin-sidebar::-webkit-scrollbar { width: 4px; }
        .admin-sidebar::-webkit-scrollbar-track { background: transparent; }
        .admin-sidebar::-webkit-scrollbar-thumb { background: var(--bg-tertiary); border-radius: 4px; }

        .sidebar-user {
            padding: 16px 24px;
            margin-top: auto;
            border-top: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar-user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(108, 92, 231, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .sidebar-user-avatar i { width: 18px; height: 18px; color: var(--accent-secondary); }
        .sidebar-user-info { overflow: hidden; }
        .sidebar-user-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar-user-role {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Section headers */
        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-subtle);
        }
        .section-head h2 {
            font-size: 18px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-head h2 i { width: 18px; height: 18px; color: var(--accent-secondary); }

        /* Settings form styling */
        .settings-container {
            max-width: 860px;
        }
        .settings-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 32px;
            margin-bottom: 24px;
            transition: var(--transition-normal);
        }
        .settings-card:hover {
            border-color: var(--border-accent);
        }
        .settings-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-subtle);
        }
        .settings-card-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .settings-card-icon.purple { background: rgba(108, 92, 231, 0.12); color: var(--accent-secondary); }
        .settings-card-icon.cyan { background: rgba(0, 206, 209, 0.12); color: var(--accent-cyan); }
        .settings-card-icon.pink { background: rgba(236, 72, 153, 0.12); color: var(--accent-pink); }
        .settings-card-icon.green { background: rgba(16, 185, 129, 0.12); color: var(--accent-green); }
        .settings-card-icon i { width: 20px; height: 20px; }
        .settings-card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
        }
        .settings-card-desc {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .settings-form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .settings-form-row.full {
            grid-template-columns: 1fr;
        }
        .settings-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .settings-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .settings-label i { width: 14px; height: 14px; opacity: 0.6; }
        .settings-label .required {
            color: var(--accent-red);
            font-size: 11px;
        }
        .settings-input {
            width: 100%;
            padding: 10px 14px;
            background: var(--bg-glass);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: var(--transition-normal);
            outline: none;
        }
        .settings-input:focus {
            border-color: var(--accent-secondary);
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.1);
        }
        .settings-input::placeholder {
            color: var(--text-muted);
            opacity: 0.6;
        }
        textarea.settings-input {
            min-height: 100px;
            resize: vertical;
            line-height: 1.6;
        }
        .settings-hint {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* Preview card */
        .preview-card {
            background: var(--gradient-card);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 28px 32px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .preview-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(108, 92, 231, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }
        .preview-avatar i { width: 28px; height: 28px; color: var(--accent-secondary); }
        .preview-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .preview-info h2 {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 2px;
        }
        .preview-info p {
            font-size: 14px;
            color: var(--text-muted);
        }
        .preview-info .preview-email {
            font-size: 12px;
            color: var(--accent-secondary);
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .preview-info .preview-email i { width: 12px; height: 12px; }

        /* Photo upload */
        .photo-upload-area {
            display: flex;
            align-items: center;
            gap: 24px;
            padding: 20px;
            border: 2px dashed var(--border-subtle);
            border-radius: var(--radius-md);
            transition: var(--transition-normal);
            cursor: pointer;
        }
        .photo-upload-area:hover {
            border-color: var(--accent-secondary);
            background: rgba(108, 92, 231, 0.03);
        }
        .photo-upload-area.dragover {
            border-color: var(--accent-secondary);
            background: rgba(108, 92, 231, 0.08);
        }
        .photo-preview {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(108, 92, 231, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
            border: 2px solid var(--border-subtle);
        }
        .photo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .photo-preview i { width: 32px; height: 32px; color: var(--text-muted); }
        .photo-upload-text h4 {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 4px;
        }
        .photo-upload-text p {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.5;
        }
        .photo-upload-text .btn-upload {
            margin-top: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: rgba(108, 92, 231, 0.12);
            color: var(--accent-secondary);
            border: 1px solid rgba(108, 92, 231, 0.3);
            border-radius: var(--radius-sm);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-normal);
        }
        .photo-upload-text .btn-upload:hover {
            background: rgba(108, 92, 231, 0.2);
        }
        .photo-upload-text .btn-upload i { width: 14px; height: 14px; }

        /* Submit bar */
        .settings-submit {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 0;
        }
        .settings-submit .btn {
            min-width: 180px;
            justify-content: center;
        }
        .settings-submit-hint {
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .settings-submit-hint i { width: 14px; height: 14px; }

        /* Responsive */
        @media (max-width: 968px) {
            .sidebar-toggle { display: flex; }
            .admin-sidebar {
                position: fixed;
                left: -280px;
                z-index: 1050;
                width: 260px;
                transition: left 0.3s ease;
            }
            .admin-sidebar.open { left: 0; }
            .sidebar-overlay { display: block; }
            .admin-main {
                margin-left: 0;
                padding: 20px;
                padding-top: 72px;
            }
            .preview-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }
        @media (max-width: 600px) {
            .admin-main { padding: 16px; padding-top: 68px; }
            .settings-form-row { grid-template-columns: 1fr; }
            .settings-card { padding: 20px; }
            .settings-submit { flex-direction: column; align-items: stretch; }
            .settings-submit-hint { justify-content: center; }
        }
    </style>
</head>
<body>
    <!-- Mobile sidebar toggle -->
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
        <i data-lucide="menu"></i>
    </button>

    <!-- Mobile overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-sidebar-header">
                <a href="<?php echo SITE_URL; ?>" class="nav-logo" style="text-decoration: none;" target="_blank">
                    <span class="logo-icon" style="width: 36px; height: 36px;">
                        <i data-lucide="hexagon" class="logo-hex" style="width: 36px; height: 36px;"></i>
                        <span class="logo-letter" style="font-size: 14px;">A</span>
                    </span>
                    <span class="logo-text" style="font-size: 18px;"><?php echo SITE_NAME; ?></span>
                </a>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 8px;">Admin Panel</p>
            </div>
            
            <ul class="admin-nav">
                <li>
                    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="admin-nav-link">
                        <i data-lucide="layout-dashboard"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?php echo SITE_URL; ?>/admin/manage-projects.php" class="admin-nav-link">
                        <i data-lucide="folder-kanban"></i> Projects
                        <?php if ($totalProjects > 0): ?>
                        <span class="badge"><?php echo $totalProjects; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php#messages" class="admin-nav-link">
                        <i data-lucide="mail"></i> Messages
                        <?php if ($unreadCount > 0): ?>
                        <span class="badge"><?php echo $unreadCount; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo SITE_URL; ?>/admin/settings.php" class="admin-nav-link active">
                        <i data-lucide="settings"></i> Profile Settings
                    </a>
                </li>
                <li style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border-subtle);">
                    <a href="<?php echo SITE_URL; ?>" target="_blank" class="admin-nav-link">
                        <i data-lucide="external-link"></i> View Site
                    </a>
                </li>
                <li>
                    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php?logout=1" class="admin-nav-link" style="color: var(--accent-red);">
                        <i data-lucide="log-out"></i> Logout
                    </a>
                </li>
            </ul>

            <div class="sidebar-user">
                <div class="sidebar-user-avatar">
                    <i data-lucide="user"></i>
                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name"><?php echo sanitize($_SESSION['admin_email'] ?? 'Admin'); ?></div>
                    <div class="sidebar-user-role">Administrator</div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="admin-main">
            <!-- Flash Message -->
            <?php $flash = get_flash(); if ($flash): ?>
            <div class="flash-message flash-<?php echo $flash['type']; ?>" id="flashMessage" 
                 style="position: relative; top: 0; margin-bottom: 20px; border-radius: var(--radius-md); padding: 14px 20px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i data-lucide="<?php echo $flash['type'] === 'success' ? 'check-circle' : 'alert-circle'; ?>" style="width:18px;height:18px;flex-shrink:0;"></i>
                    <span style="flex: 1;"><?php echo $flash['message']; ?></span>
                    <button onclick="this.closest('.flash-message').remove()" class="flash-close">&times;</button>
                </div>
            </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="section-head">
                <h2><i data-lucide="settings"></i> Profile Settings</h2>
                <a href="<?php echo SITE_URL; ?>" target="_blank" class="btn btn-ghost btn-sm">
                    <i data-lucide="eye"></i> Preview Site
                </a>
            </div>

            <!-- Profile Preview Card -->
            <div class="preview-card">
                <div class="preview-avatar" id="previewAvatar">
                    <?php if (!empty($settings['avatar_url'])): ?>
                        <img src="<?php echo sanitize($settings['avatar_url']); ?>" alt="Profile">
                    <?php else: ?>
                        <i data-lucide="user-circle"></i>
                    <?php endif; ?>
                </div>
                <div class="preview-info">
                    <h2 id="previewName"><?php echo sanitize($settings['name'] ?? 'Your Name'); ?></h2>
                    <p id="previewTitle"><?php echo sanitize($settings['title'] ?? 'Your Title'); ?></p>
                    <div class="preview-email">
                        <i data-lucide="mail"></i>
                        <span id="previewEmail"><?php echo sanitize($settings['email'] ?? 'email@example.com'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Settings Form -->
            <form action="<?php echo SITE_URL; ?>/api/settings.php" method="POST" enctype="multipart/form-data" class="settings-container">
                
                <!-- Profile Photo -->
                <div class="settings-card">
                    <div class="settings-card-header">
                        <div class="settings-card-icon green">
                            <i data-lucide="camera"></i>
                        </div>
                        <div>
                            <div class="settings-card-title">Profile Photo</div>
                            <div class="settings-card-desc">Upload a profile picture — shown on the About page and admin panel</div>
                        </div>
                    </div>

                    <label for="avatarInput" class="photo-upload-area" id="photoUploadArea">
                        <div class="photo-preview" id="photoPreview">
                            <?php if (!empty($settings['avatar_url'])): ?>
                                <img src="<?php echo sanitize($settings['avatar_url']); ?>" alt="Current photo" id="photoPreviewImg">
                            <?php else: ?>
                                <i data-lucide="user-circle" id="photoPreviewIcon"></i>
                            <?php endif; ?>
                        </div>
                        <div class="photo-upload-text">
                            <h4>Upload Profile Photo</h4>
                            <p>Drag and drop or click to select. JPG, PNG or WebP. Max 2MB.</p>
                            <span class="btn-upload">
                                <i data-lucide="upload"></i> Choose File
                            </span>
                        </div>
                        <input type="file" id="avatarInput" name="avatar" accept="image/jpeg,image/png,image/webp" style="display:none;">
                    </label>
                </div>
                
                <!-- Personal Info -->
                <div class="settings-card">
                    <div class="settings-card-header">
                        <div class="settings-card-icon purple">
                            <i data-lucide="user"></i>
                        </div>
                        <div>
                            <div class="settings-card-title">Personal Information</div>
                            <div class="settings-card-desc">Your name, title, and bio that appear on the portfolio</div>
                        </div>
                    </div>

                    <div class="settings-form-row">
                        <div class="settings-field">
                            <label class="settings-label">
                                <i data-lucide="user"></i> Full Name <span class="required">*</span>
                            </label>
                            <input type="text" name="name" class="settings-input" 
                                   value="<?php echo sanitize($settings['name'] ?? ''); ?>" 
                                   placeholder="e.g. Anuja Kodikara" required
                                   id="inputName">
                        </div>
                        <div class="settings-field">
                            <label class="settings-label">
                                <i data-lucide="briefcase"></i> Title / Role
                            </label>
                            <input type="text" name="title" class="settings-input" 
                                   value="<?php echo sanitize($settings['title'] ?? ''); ?>" 
                                   placeholder="e.g. Full Stack Developer"
                                   id="inputTitle">
                        </div>
                    </div>

                    <div class="settings-form-row full">
                        <div class="settings-field">
                            <label class="settings-label">
                                <i data-lucide="file-text"></i> Bio / About
                            </label>
                            <textarea name="bio" class="settings-input" 
                                      placeholder="Tell people about yourself..."><?php echo sanitize($settings['bio'] ?? ''); ?></textarea>
                            <span class="settings-hint">This text appears on your About page</span>
                        </div>
                    </div>
                </div>

                <!-- Contact Details -->
                <div class="settings-card">
                    <div class="settings-card-header">
                        <div class="settings-card-icon cyan">
                            <i data-lucide="phone"></i>
                        </div>
                        <div>
                            <div class="settings-card-title">Contact Details</div>
                            <div class="settings-card-desc">How people can reach you — shown on the Contact page</div>
                        </div>
                    </div>

                    <div class="settings-form-row">
                        <div class="settings-field">
                            <label class="settings-label">
                                <i data-lucide="mail"></i> Email Address <span class="required">*</span>
                            </label>
                            <input type="email" name="email" class="settings-input" 
                                   value="<?php echo sanitize($settings['email'] ?? ''); ?>" 
                                   placeholder="hello@example.com" required
                                   id="inputEmail">
                        </div>
                        <div class="settings-field">
                            <label class="settings-label">
                                <i data-lucide="phone"></i> Phone Number
                            </label>
                            <input type="text" name="phone" class="settings-input" 
                                   value="<?php echo sanitize($settings['phone'] ?? ''); ?>" 
                                   placeholder="+94 71 1350 958">
                            <span class="settings-hint">Include country code (e.g. +94)</span>
                        </div>
                    </div>

                    <div class="settings-form-row full">
                        <div class="settings-field">
                            <label class="settings-label">
                                <i data-lucide="map-pin"></i> Location
                            </label>
                            <input type="text" name="location" class="settings-input" 
                                   value="<?php echo sanitize($settings['location'] ?? ''); ?>" 
                                   placeholder="e.g. Matara, Sri Lanka 🇱🇰">
                        </div>
                    </div>
                </div>

                <!-- Social Links -->
                <div class="settings-card">
                    <div class="settings-card-header">
                        <div class="settings-card-icon pink">
                            <i data-lucide="share-2"></i>
                        </div>
                        <div>
                            <div class="settings-card-title">Social Links</div>
                            <div class="settings-card-desc">Your social media profiles — shown in header, footer, and contact page</div>
                        </div>
                    </div>

                    <div class="settings-form-row">
                        <div class="settings-field">
                            <label class="settings-label">
                                <i data-lucide="linkedin"></i> LinkedIn
                            </label>
                            <input type="url" name="linkedin" class="settings-input" 
                                   value="<?php echo sanitize($settings['linkedin'] ?? ''); ?>" 
                                   placeholder="https://linkedin.com/in/username">
                        </div>
                        <div class="settings-field">
                            <label class="settings-label">
                                <i data-lucide="github"></i> GitHub
                            </label>
                            <input type="url" name="github" class="settings-input" 
                                   value="<?php echo sanitize($settings['github'] ?? ''); ?>" 
                                   placeholder="https://github.com/username">
                        </div>
                    </div>

                    <div class="settings-form-row full">
                        <div class="settings-field">
                            <label class="settings-label">
                                <i data-lucide="twitter"></i> Twitter / X
                            </label>
                            <input type="url" name="twitter" class="settings-input" 
                                   value="<?php echo sanitize($settings['twitter'] ?? ''); ?>" 
                                   placeholder="https://twitter.com/username">
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="settings-submit">
                    <div class="settings-submit-hint">
                        <i data-lucide="info"></i>
                        Changes will be reflected on the live site immediately
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    <script>
        lucide.createIcons();

        // === Sidebar Toggle (Mobile) ===
        const sidebar = document.getElementById('adminSidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            sidebar.classList.add('open');
            sidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        if (sidebarToggle) sidebarToggle.addEventListener('click', openSidebar);
        if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeSidebar();
        });

        // === Live Preview Update ===
        const nameInput = document.getElementById('inputName');
        const titleInput = document.getElementById('inputTitle');
        const emailInput = document.getElementById('inputEmail');

        if (nameInput) {
            nameInput.addEventListener('input', () => {
                document.getElementById('previewName').textContent = nameInput.value || 'Your Name';
            });
        }
        if (titleInput) {
            titleInput.addEventListener('input', () => {
                document.getElementById('previewTitle').textContent = titleInput.value || 'Your Title';
            });
        }
        if (emailInput) {
            emailInput.addEventListener('input', () => {
                document.getElementById('previewEmail').textContent = emailInput.value || 'email@example.com';
            });
        }

        // === Auto-hide flash ===
        const flash = document.getElementById('flashMessage');
        if (flash) {
            setTimeout(() => {
                flash.style.transition = 'opacity 0.4s, transform 0.4s';
                flash.style.opacity = '0';
                flash.style.transform = 'translateY(-10px)';
                setTimeout(() => flash.remove(), 400);
            }, 4000);
        }

        // === Photo Upload Preview ===
        const avatarInput = document.getElementById('avatarInput');
        const photoPreview = document.getElementById('photoPreview');
        const previewAvatar = document.getElementById('previewAvatar');
        const uploadArea = document.getElementById('photoUploadArea');

        if (avatarInput) {
            avatarInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    if (file.size > 2 * 1024 * 1024) {
                        alert('File size must be less than 2MB');
                        this.value = '';
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        photoPreview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                        previewAvatar.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Drag and drop
        if (uploadArea) {
            ['dragenter', 'dragover'].forEach(evt => {
                uploadArea.addEventListener(evt, (e) => {
                    e.preventDefault();
                    uploadArea.classList.add('dragover');
                });
            });
            ['dragleave', 'drop'].forEach(evt => {
                uploadArea.addEventListener(evt, (e) => {
                    e.preventDefault();
                    uploadArea.classList.remove('dragover');
                });
            });
            uploadArea.addEventListener('drop', (e) => {
                const file = e.dataTransfer.files[0];
                if (file && file.type.startsWith('image/')) {
                    avatarInput.files = e.dataTransfer.files;
                    avatarInput.dispatchEvent(new Event('change'));
                }
            });
        }
    </script>
</body>
</html>
