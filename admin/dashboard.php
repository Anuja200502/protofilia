<?php
/**
 * Protofilia Admin - Dashboard
 */
require_once __DIR__ . '/../includes/functions.php';

if (!is_admin_logged_in()) {
    redirect(SITE_URL . '/admin/login.php');
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    redirect(SITE_URL . '/admin/login.php');
}

$projects = get_projects();
$messages = get_messages();
$unreadCount = count_unread_messages();
$totalProjects = count($projects);
$totalMessages = count($messages);
$featuredCount = count(array_filter($projects, fn($p) => !empty($p['is_featured'])));
$currentAdminPage = basename($_SERVER['PHP_SELF'], '.php');

// Handle message actions
if (isset($_GET['read'])) {
    mark_message_read($_GET['read']);
    set_flash('success', 'Message marked as read.');
    redirect(SITE_URL . '/admin/dashboard.php#messages');
}

if (isset($_GET['delete_msg'])) {
    delete_message($_GET['delete_msg']);
    set_flash('success', 'Message deleted successfully.');
    redirect(SITE_URL . '/admin/dashboard.php#messages');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | <?php echo SITE_NAME; ?> Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <style>
        /* === Admin-specific overrides === */
        body {
            background: var(--bg-primary);
            overflow-x: hidden;
        }
        /* Remove floating bg effects from admin */
        .bg-grid, .bg-glow, .bg-glow-1, .bg-glow-2, .bg-glow-3 {
            display: none !important;
        }

        /* Sidebar toggle for mobile */
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

        /* Sidebar overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 1049;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .sidebar-overlay.active {
            opacity: 1;
        }

        /* Admin sidebar scrollbar */
        .admin-sidebar::-webkit-scrollbar { width: 4px; }
        .admin-sidebar::-webkit-scrollbar-track { background: transparent; }
        .admin-sidebar::-webkit-scrollbar-thumb { background: var(--bg-tertiary); border-radius: 4px; }

        /* Sidebar user info */
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

        /* Welcome banner */
        .welcome-banner {
            background: var(--gradient-card);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 28px 32px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        .welcome-banner h1 {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }
        .welcome-banner p {
            font-size: 14px;
            color: var(--text-muted);
        }
        .welcome-banner .welcome-time {
            font-size: 13px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .welcome-banner .welcome-time i { width: 14px; height: 14px; }

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

        /* Message preview modal */
        .msg-modal-body .msg-field {
            margin-bottom: 16px;
        }
        .msg-modal-body .msg-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }
        .msg-modal-body .msg-value {
            font-size: 14px;
            color: var(--text-primary);
            line-height: 1.6;
        }
        .msg-modal-body .msg-value.msg-content {
            background: var(--bg-glass);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-sm);
            padding: 16px;
            white-space: pre-wrap;
            word-break: break-word;
            max-height: 300px;
            overflow-y: auto;
        }

        /* Quick action cards */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin-bottom: 28px;
        }
        .quick-action {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            text-decoration: none;
            color: var(--text-secondary);
            font-size: 13px;
            font-weight: 500;
            transition: var(--transition-normal);
        }
        .quick-action:hover {
            border-color: var(--border-accent);
            color: var(--text-primary);
            background: var(--bg-card-hover);
            transform: translateY(-2px);
        }
        .quick-action i { width: 18px; height: 18px; flex-shrink: 0; }

        /* Responsive admin */
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
            .welcome-banner {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }
        @media (max-width: 600px) {
            .admin-main { padding: 16px; padding-top: 68px; }
            .dashboard-stats { grid-template-columns: 1fr 1fr; gap: 12px; }
            .stat-card { padding: 16px; }
            .stat-card-value { font-size: 24px; }
            .stat-card-icon { width: 36px; height: 36px; }
            .stat-card-icon i { width: 18px; height: 18px; }
            .welcome-banner { padding: 20px; }
            .welcome-banner h1 { font-size: 20px; }
            .quick-actions { grid-template-columns: 1fr 1fr; }
            .data-table-wrapper { border-radius: var(--radius-md); }
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
                    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="admin-nav-link <?php echo $currentAdminPage === 'dashboard' ? 'active' : ''; ?>">
                        <i data-lucide="layout-dashboard"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?php echo SITE_URL; ?>/admin/manage-projects.php" class="admin-nav-link <?php echo $currentAdminPage === 'manage-projects' ? 'active' : ''; ?>">
                        <i data-lucide="folder-kanban"></i> Projects
                        <?php if ($totalProjects > 0): ?>
                        <span class="badge"><?php echo $totalProjects; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="#messages" class="admin-nav-link" onclick="closeSidebar()">
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
                    <a href="?logout=1" class="admin-nav-link" style="color: var(--accent-red);">
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

            <!-- Welcome Banner -->
            <div class="welcome-banner">
                <div>
                    <h1>Welcome back! 👋</h1>
                    <p>Here's what's happening with your portfolio today.</p>
                </div>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <span class="welcome-time">
                        <i data-lucide="calendar"></i>
                        <?php echo date('F j, Y'); ?>
                    </span>
                    <a href="<?php echo SITE_URL; ?>/admin/manage-projects.php?action=new" class="btn btn-primary btn-sm">
                        <i data-lucide="plus"></i> New Project
                    </a>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon">
                            <i data-lucide="folder-kanban"></i>
                        </div>
                    </div>
                    <div class="stat-card-value"><?php echo $totalProjects; ?></div>
                    <div class="stat-card-label">Total Projects</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon cyan">
                            <i data-lucide="star"></i>
                        </div>
                    </div>
                    <div class="stat-card-value"><?php echo $featuredCount; ?></div>
                    <div class="stat-card-label">Featured Projects</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon pink">
                            <i data-lucide="mail"></i>
                        </div>
                    </div>
                    <div class="stat-card-value"><?php echo $totalMessages; ?></div>
                    <div class="stat-card-label">Total Messages</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon gold">
                            <i data-lucide="bell-ring"></i>
                        </div>
                    </div>
                    <div class="stat-card-value"><?php echo $unreadCount; ?></div>
                    <div class="stat-card-label">Unread Messages</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="<?php echo SITE_URL; ?>/admin/manage-projects.php?action=new" class="quick-action">
                    <i data-lucide="plus-circle" style="color: var(--accent-green);"></i>
                    Add Project
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/manage-projects.php" class="quick-action">
                    <i data-lucide="settings" style="color: var(--accent-secondary);"></i>
                    Manage Projects
                </a>
                <a href="<?php echo SITE_URL; ?>" target="_blank" class="quick-action">
                    <i data-lucide="eye" style="color: var(--accent-cyan);"></i>
                    View Portfolio
                </a>
                <a href="#messages" class="quick-action">
                    <i data-lucide="inbox" style="color: var(--accent-pink);"></i>
                    View Messages
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/settings.php" class="quick-action">
                    <i data-lucide="settings" style="color: var(--accent-secondary);"></i>
                    Profile Settings
                </a>
            </div>
            
            <!-- Recent Projects -->
            <div style="margin-bottom: 32px;">
                <div class="section-head">
                    <h2><i data-lucide="folder-kanban"></i> Recent Projects</h2>
                    <a href="<?php echo SITE_URL; ?>/admin/manage-projects.php" class="btn btn-ghost btn-sm">
                        View All <i data-lucide="arrow-right"></i>
                    </a>
                </div>
                
                <div class="data-table-wrapper" style="overflow-x: auto;">
                    <table class="data-table" style="min-width: 600px;">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th style="width: 80px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($projects)): ?>
                                <?php foreach (array_slice($projects, 0, 5) as $project): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 600;"><?php echo sanitize($project['title']); ?></div>
                                        <?php if (!empty($project['tech_stack'])): ?>
                                        <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                                            <?php echo truncate(sanitize($project['tech_stack'] ?? ''), 40); ?>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="tech-tag"><?php echo sanitize($project['category'] ?? 'N/A'); ?></span></td>
                                    <td>
                                        <?php if (!empty($project['is_featured'])): ?>
                                            <span class="table-badge featured">⭐ Featured</span>
                                        <?php else: ?>
                                            <span class="table-badge draft">Normal</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="color: var(--text-muted); font-size: 13px; white-space: nowrap;">
                                        <?php echo format_date($project['created_at'] ?? 'now'); ?>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="<?php echo SITE_URL; ?>/admin/manage-projects.php?edit=<?php echo $project['id']; ?>" 
                                               class="btn btn-ghost btn-sm" title="Edit Project" style="padding: 6px 10px;">
                                                <i data-lucide="pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 48px 20px;">
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                            <i data-lucide="folder-plus" style="width: 40px; height: 40px; color: var(--text-muted); opacity: 0.4;"></i>
                                            <p style="color: var(--text-muted); font-size: 14px; margin: 0;">No projects yet</p>
                                            <a href="<?php echo SITE_URL; ?>/admin/manage-projects.php?action=new" style="color: var(--accent-secondary); font-size: 13px;">
                                                Add your first project →
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Messages -->
            <div id="messages">
                <div class="section-head">
                    <h2>
                        <i data-lucide="mail"></i> Messages
                        <?php if ($unreadCount > 0): ?>
                        <span style="background: var(--accent-primary); color: #fff; font-size: 11px; padding: 2px 8px; border-radius: var(--radius-full); font-weight: 700;">
                            <?php echo $unreadCount; ?> new
                        </span>
                        <?php endif; ?>
                    </h2>
                </div>
                
                <div class="data-table-wrapper" style="overflow-x: auto;">
                    <table class="data-table" style="min-width: 600px;">
                        <thead>
                            <tr>
                                <th>From</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($messages)): ?>
                                <?php foreach ($messages as $msg): ?>
                                <tr style="<?php echo empty($msg['is_read']) ? 'background: rgba(108, 92, 231, 0.03);' : ''; ?>">
                                    <td>
                                        <div style="font-weight: <?php echo empty($msg['is_read']) ? '700' : '500'; ?>;">
                                            <?php echo sanitize($msg['name']); ?>
                                        </div>
                                        <div style="font-size: 12px; color: var(--text-muted);">
                                            <?php echo sanitize($msg['email']); ?>
                                        </div>
                                    </td>
                                    <td style="max-width: 250px;">
                                        <div style="font-weight: <?php echo empty($msg['is_read']) ? '600' : '400'; ?>;">
                                            <?php echo sanitize($msg['subject'] ?? 'No Subject'); ?>
                                        </div>
                                        <div style="font-size: 12px; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 220px;">
                                            <?php echo truncate(sanitize($msg['message']), 60); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (empty($msg['is_read'])): ?>
                                            <span class="table-badge unread">● Unread</span>
                                        <?php else: ?>
                                            <span class="table-badge read">Read</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="color: var(--text-muted); font-size: 13px; white-space: nowrap;">
                                        <?php echo format_date($msg['created_at'] ?? 'now'); ?>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <button onclick="viewMessage(<?php echo htmlspecialchars(json_encode($msg), ENT_QUOTES); ?>)" 
                                                    class="btn btn-ghost btn-sm" title="View Message" style="padding: 6px 10px;">
                                                <i data-lucide="eye"></i>
                                            </button>
                                            <?php if (empty($msg['is_read'])): ?>
                                            <a href="?read=<?php echo $msg['id']; ?>" class="btn btn-ghost btn-sm" title="Mark as Read" style="padding: 6px 10px;">
                                                <i data-lucide="check"></i>
                                            </a>
                                            <?php endif; ?>
                                            <button onclick="confirmDelete('<?php echo $msg['id']; ?>')" 
                                                    class="btn btn-ghost btn-sm" title="Delete" style="padding: 6px 10px; color: var(--accent-red);">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 48px 20px;">
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                            <i data-lucide="inbox" style="width: 40px; height: 40px; color: var(--text-muted); opacity: 0.4;"></i>
                                            <p style="color: var(--text-muted); font-size: 14px; margin: 0;">No messages yet</p>
                                            <span style="color: var(--text-muted); font-size: 12px;">Messages from visitors will appear here</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Message View Modal -->
    <div class="modal-overlay" id="messageModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Message Details</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body msg-modal-body">
                <div class="msg-field">
                    <div class="msg-label">From</div>
                    <div class="msg-value" id="msgFrom"></div>
                </div>
                <div class="msg-field">
                    <div class="msg-label">Email</div>
                    <div class="msg-value">
                        <a id="msgEmail" href="" style="color: var(--accent-secondary); text-decoration: none;"></a>
                    </div>
                </div>
                <div class="msg-field">
                    <div class="msg-label">Subject</div>
                    <div class="msg-value" id="msgSubject"></div>
                </div>
                <div class="msg-field">
                    <div class="msg-label">Date</div>
                    <div class="msg-value" id="msgDate" style="font-size: 13px; color: var(--text-muted);"></div>
                </div>
                <div class="msg-field">
                    <div class="msg-label">Message</div>
                    <div class="msg-value msg-content" id="msgContent"></div>
                </div>
            </div>
            <div class="modal-footer">
                <a id="msgReply" href="" class="btn btn-primary btn-sm">
                    <i data-lucide="reply"></i> Reply via Email
                </a>
                <button onclick="closeModal()" class="btn btn-secondary btn-sm">Close</button>
            </div>
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

        // === Message Modal ===
        function viewMessage(msg) {
            document.getElementById('msgFrom').textContent = msg.name || 'Unknown';
            document.getElementById('msgEmail').textContent = msg.email || '';
            document.getElementById('msgEmail').href = 'mailto:' + (msg.email || '');
            document.getElementById('msgSubject').textContent = msg.subject || 'No Subject';
            document.getElementById('msgContent').textContent = msg.message || '';
            document.getElementById('msgDate').textContent = msg.created_at ? new Date(msg.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : '';
            document.getElementById('msgReply').href = 'mailto:' + (msg.email || '') + '?subject=Re: ' + encodeURIComponent(msg.subject || '');
            
            document.getElementById('messageModal').classList.add('active');
            document.body.style.overflow = 'hidden';
            lucide.createIcons();

            // Auto-mark as read
            if (!msg.is_read && msg.id) {
                fetch('?read=' + msg.id, { method: 'GET' });
            }
        }

        function closeModal() {
            document.getElementById('messageModal').classList.remove('active');
            document.body.style.overflow = '';
        }

        // Close modal on ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
                closeSidebar();
            }
        });

        // Close modal on overlay click
        document.getElementById('messageModal').addEventListener('click', (e) => {
            if (e.target === e.currentTarget) closeModal();
        });
        
        // === Confirm Delete ===
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this message? This cannot be undone.')) {
                window.location.href = '?delete_msg=' + id;
            }
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
    </script>
</body>
</html>