<?php
/**
 * Protofilia Admin - Manage Projects
 */
require_once __DIR__ . '/../includes/functions.php';

if (!is_admin_logged_in()) {
    redirect(SITE_URL . '/admin/login.php');
}

$action = $_GET['action'] ?? '';
$editId = $_GET['edit'] ?? '';
$projects = get_projects();
$messages = get_messages();
$unreadCount = count_unread_messages();
$totalProjects = count($projects);
$editProject = null;

// Load project for editing
if ($editId) {
    $editProject = get_project_by_id($editId);
    $action = 'edit';
}

// Handle Delete
if (isset($_GET['delete'])) {
    $result = delete_project($_GET['delete']);
    set_flash('success', 'Project deleted successfully.');
    redirect(SITE_URL . '/admin/manage-projects.php');
}

// Handle Create/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectData = [
        'title' => sanitize($_POST['title'] ?? ''),
        'slug' => generate_slug($_POST['title'] ?? ''),
        'description' => sanitize($_POST['description'] ?? ''),
        'category' => sanitize($_POST['category'] ?? ''),
        'tech_stack' => sanitize($_POST['tech_stack'] ?? ''),
        'live_url' => sanitize($_POST['live_url'] ?? ''),
        'github_url' => sanitize($_POST['github_url'] ?? ''),
        'is_featured' => isset($_POST['is_featured']),
        'sort_order' => intval($_POST['sort_order'] ?? 0),
    ];
    
    // Handle image upload
    if (!empty($_FILES['image']['tmp_name'])) {
        $uploadResult = upload_project_image($_FILES['image']);
        if (isset($uploadResult['url'])) {
            $projectData['image_url'] = $uploadResult['url'];
        }
    } elseif (!empty($_POST['image_url'])) {
        $projectData['image_url'] = sanitize($_POST['image_url']);
    }
    
    if (isset($_POST['project_id']) && !empty($_POST['project_id'])) {
        // Update
        $result = update_project($_POST['project_id'], $projectData);
        set_flash('success', 'Project updated successfully!');
    } else {
        // Create
        $result = create_project($projectData);
        set_flash('success', 'Project created successfully!');
    }
    
    redirect(SITE_URL . '/admin/manage-projects.php');
}

$currentAdminPage = 'manage-projects';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects | <?php echo SITE_NAME; ?> Admin</title>
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
        .sidebar-toggle:hover { border-color: var(--border-accent); background: var(--bg-card-hover); }

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
        .sidebar-user-name { font-size: 13px; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user-role { font-size: 11px; color: var(--text-muted); }

        /* Section heads */
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
            .admin-main { margin-left: 0; padding: 20px; padding-top: 72px; }
            .admin-header { flex-direction: column; align-items: flex-start; gap: 16px; }
        }
        @media (max-width: 600px) {
            .admin-main { padding: 16px; padding-top: 68px; }
            .form-row { grid-template-columns: 1fr; }
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
                    <a href="<?php echo SITE_URL; ?>/admin/manage-projects.php" class="admin-nav-link active">
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
                    <a href="<?php echo SITE_URL; ?>/admin/settings.php" class="admin-nav-link <?php echo $currentAdminPage === 'settings' ? 'active' : ''; ?>">
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
            
            <?php if ($action === 'new' || $action === 'edit'): ?>
            <!-- Project Form -->
            <div class="admin-header">
                <div>
                    <h1 class="admin-title"><?php echo $editProject ? 'Edit Project' : 'New Project'; ?></h1>
                    <p class="admin-subtitle"><?php echo $editProject ? 'Update project details' : 'Add a new project to your portfolio'; ?></p>
                </div>
                <a href="<?php echo SITE_URL; ?>/admin/manage-projects.php" class="btn btn-secondary btn-sm">
                    <i data-lucide="arrow-left"></i> Back
                </a>
            </div>
            
            <div class="contact-form-card">
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($editProject): ?>
                    <input type="hidden" name="project_id" value="<?php echo sanitize($editProject['id']); ?>">
                    <?php endif; ?>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="title" class="form-label">Project Title *</label>
                            <input type="text" id="title" name="title" class="form-input" 
                                   placeholder="My Awesome Project" required
                                   value="<?php echo sanitize($editProject['title'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="category" class="form-label">Category</label>
                            <input type="text" id="category" name="category" class="form-input" 
                                   placeholder="Web App, Mobile, Design..."
                                   value="<?php echo sanitize($editProject['category'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-textarea" 
                                  placeholder="Describe your project..."><?php echo sanitize($editProject['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="tech_stack" class="form-label">Tech Stack <span style="color: var(--text-muted); font-weight: 400;">(comma separated)</span></label>
                        <input type="text" id="tech_stack" name="tech_stack" class="form-input" 
                               placeholder="PHP, JavaScript, Supabase, CSS"
                               value="<?php echo sanitize($editProject['tech_stack'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="live_url" class="form-label">Live URL</label>
                            <input type="url" id="live_url" name="live_url" class="form-input" 
                                   placeholder="https://myproject.com"
                                   value="<?php echo sanitize($editProject['live_url'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="github_url" class="form-label">GitHub URL</label>
                            <input type="url" id="github_url" name="github_url" class="form-input" 
                                   placeholder="https://github.com/user/repo"
                                   value="<?php echo sanitize($editProject['github_url'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="image" class="form-label">Project Image</label>
                            <input type="file" id="image" name="image" class="form-input" accept="image/*"
                                   onchange="previewImage(this, 'imagePreview')">
                            <?php if (!empty($editProject['image_url'])): ?>
                            <img id="imagePreview" src="<?php echo sanitize($editProject['image_url']); ?>" 
                                 style="margin-top: 10px; max-height: 120px; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                            <input type="hidden" name="image_url" value="<?php echo sanitize($editProject['image_url']); ?>">
                            <?php else: ?>
                            <img id="imagePreview" src="" style="margin-top: 10px; max-height: 120px; border-radius: var(--radius-sm); display: none;">
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" id="sort_order" name="sort_order" class="form-input" 
                                   placeholder="0" min="0"
                                   value="<?php echo sanitize($editProject['sort_order'] ?? '0'); ?>">
                            <div style="margin-top: 14px;">
                                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 14px; color: var(--text-secondary);">
                                    <input type="checkbox" name="is_featured" 
                                           <?php echo !empty($editProject['is_featured']) ? 'checked' : ''; ?>
                                           style="width: 18px; height: 18px; accent-color: var(--accent-primary);">
                                    Featured Project
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 12px; margin-top: 12px;">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="check"></i> <?php echo $editProject ? 'Update Project' : 'Create Project'; ?>
                        </button>
                        <a href="<?php echo SITE_URL; ?>/admin/manage-projects.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
            
            <?php else: ?>
            <!-- Projects List -->
            <div class="admin-header">
                <div>
                    <h1 class="admin-title">Projects</h1>
                    <p class="admin-subtitle">Manage your portfolio projects (<?php echo $totalProjects; ?> total)</p>
                </div>
                <a href="?action=new" class="btn btn-primary btn-sm">
                    <i data-lucide="plus"></i> New Project
                </a>
            </div>
            
            <div class="data-table-wrapper" style="overflow-x: auto;">
                <table class="data-table" style="min-width: 700px;">
                    <thead>
                        <tr>
                            <th style="width: 60px;">Image</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Tech</th>
                            <th>Status</th>
                            <th style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($projects)): ?>
                            <?php foreach ($projects as $project): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($project['image_url'])): ?>
                                    <img src="<?php echo sanitize($project['image_url']); ?>" 
                                         alt="" style="width: 50px; height: 36px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-subtle);">
                                    <?php else: ?>
                                    <div style="width: 50px; height: 36px; background: var(--bg-glass); border: 1px solid var(--border-subtle); border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                        <i data-lucide="image" style="width: 16px; height: 16px; color: var(--text-muted);"></i>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td style="font-weight: 600;"><?php echo sanitize($project['title']); ?></td>
                                <td><span class="tech-tag"><?php echo sanitize($project['category'] ?? 'N/A'); ?></span></td>
                                <td style="max-width: 180px;">
                                    <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                        <?php
                                        $techs = is_array($project['tech_stack'] ?? '') ? $project['tech_stack'] : explode(',', $project['tech_stack'] ?? '');
                                        foreach (array_slice(array_filter($techs), 0, 3) as $tech): ?>
                                        <span class="tech-tag" style="font-size: 10px;"><?php echo sanitize(trim($tech)); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($project['is_featured'])): ?>
                                        <span class="table-badge featured">⭐ Featured</span>
                                    <?php else: ?>
                                        <span class="table-badge draft">Normal</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="?edit=<?php echo $project['id']; ?>" class="btn btn-ghost btn-sm" title="Edit" style="padding: 6px 10px;">
                                            <i data-lucide="pencil"></i>
                                        </a>
                                        <button onclick="confirmDelete('<?php echo $project['id']; ?>')" class="btn btn-ghost btn-sm" title="Delete" style="padding: 6px 10px; color: var(--accent-red);">
                                            <i data-lucide="trash-2"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 60px 20px;">
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                        <i data-lucide="folder-plus" style="width: 40px; height: 40px; color: var(--text-muted); opacity: 0.4;"></i>
                                        <p style="font-size: 16px; font-weight: 600; margin: 0;">No projects yet</p>
                                        <p style="color: var(--text-muted); font-size: 14px; margin: 0 0 12px;">Start building your portfolio!</p>
                                        <a href="?action=new" class="btn btn-primary btn-sm"><i data-lucide="plus"></i> Add Project</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    <script>
        lucide.createIcons();

        // Sidebar toggle
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
        
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this project? This action cannot be undone.')) {
                window.location.href = '?delete=' + id;
            }
        }

        // Auto-hide flash
        const flash = document.getElementById('flashMessage');
        if (flash) {
            setTimeout(() => {
                flash.style.transition = 'opacity 0.4s, transform 0.4s';
                flash.style.opacity = '0';
                flash.style.transform = 'translateY(-10px)';
                setTimeout(() => flash.remove(), 400);
            }, 4000);
        }
    </script>
</body>
</html>
