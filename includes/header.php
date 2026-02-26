<?php
if (!isset($pageTitle)) $pageTitle = 'Home';
if (!isset($pageDescription)) $pageDescription = SITE_DESCRIPTION;
$settings = get_settings();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo sanitize($pageDescription); ?>">
    <meta name="author" content="<?php echo sanitize($settings['name']); ?>">
    <title><?php echo sanitize($pageTitle); ?> | <?php echo SITE_NAME; ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Stylesheet -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-grid"></div>
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>
    <div class="bg-glow bg-glow-3"></div>

    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container nav-container">
            <a href="<?php echo SITE_URL; ?>" class="nav-logo">
                <span class="logo-icon">
                    <i data-lucide="hexagon" class="logo-hex"></i>
                    <span class="logo-letter">A</span>
                </span>
                <span class="logo-text"><?php echo SITE_NAME; ?></span>
            </a>
            
            <ul class="nav-menu" id="navMenu">
                <li><a href="<?php echo SITE_URL; ?>" class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>">
                    <i data-lucide="home"></i> Home
                </a></li>
                <li><a href="<?php echo SITE_URL; ?>/about.php" class="nav-link <?php echo $currentPage === 'about' ? 'active' : ''; ?>">
                    <i data-lucide="user"></i> About
                </a></li>
                <li><a href="<?php echo SITE_URL; ?>/projects.php" class="nav-link <?php echo $currentPage === 'projects' ? 'active' : ''; ?>">
                    <i data-lucide="folder-kanban"></i> Projects
                </a></li>
                <li><a href="<?php echo SITE_URL; ?>/contact.php" class="nav-link <?php echo $currentPage === 'contact' ? 'active' : ''; ?>">
                    <i data-lucide="mail"></i> Contact
                </a></li>
            </ul>
            
            <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
                <span class="hamburger"></span>
            </button>
        </div>
    </nav>
    
    <!-- Flash Messages -->
    <?php $flash = get_flash(); if ($flash): ?>
    <div class="flash-message flash-<?php echo $flash['type']; ?>" id="flashMessage">
        <div class="container">
            <i data-lucide="<?php echo $flash['type'] === 'success' ? 'check-circle' : 'alert-circle'; ?>"></i>
            <span><?php echo $flash['message']; ?></span>
            <button onclick="this.parentElement.parentElement.remove()" class="flash-close">&times;</button>
        </div>
    </div>
    <?php endif; ?>

    <main>