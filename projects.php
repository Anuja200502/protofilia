<?php
/**
 * Protofilia Portfolio - Projects Page
 */
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Projects';
$pageDescription = 'Explore my portfolio of web development projects';
$projects = get_projects();
$categories = get_categories();

include __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="section-badge">
            <i data-lucide="folder-kanban" style="width:14px;height:14px;"></i>
            Portfolio
        </div>
        <h1 class="page-title">My Projects</h1>
        <p class="page-subtitle">A showcase of everything I've built and contributed to</p>
    </div>
</div>

<!-- Projects Section -->
<section class="section" style="padding-top: 40px;">
    <div class="container">
        
        <!-- Filter Bar -->
        <?php if (!empty($categories)): ?>
        <div class="filter-bar fade-in">
            <button class="filter-btn active" onclick="filterProjects('all')">All</button>
            <?php foreach ($categories as $cat): ?>
            <button class="filter-btn" onclick="filterProjects('<?php echo sanitize($cat); ?>')"><?php echo sanitize($cat); ?></button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Projects Grid -->
        <div class="projects-grid">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project): ?>
                <div class="project-card fade-in" data-category="<?php echo sanitize($project['category'] ?? 'general'); ?>">
                    <div class="project-card-image">
                        <?php if (!empty($project['image_url'])): ?>
                            <img src="<?php echo sanitize($project['image_url']); ?>" 
                                 alt="<?php echo sanitize($project['title']); ?>" loading="lazy">
                        <?php else: ?>
                            <div class="project-placeholder">
                                <i data-lucide="image"></i>
                            </div>
                        <?php endif; ?>
                        <div class="project-overlay">
                            <div style="display: flex; gap: 10px;">
                                <?php if (!empty($project['live_url'])): ?>
                                <a href="<?php echo sanitize($project['live_url']); ?>" target="_blank" class="btn btn-sm btn-primary">
                                    <i data-lucide="external-link"></i> Live
                                </a>
                                <?php endif; ?>
                                <?php if (!empty($project['github_url'])): ?>
                                <a href="<?php echo sanitize($project['github_url']); ?>" target="_blank" class="btn btn-sm btn-secondary">
                                    <i data-lucide="github"></i> Code
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="project-card-body">
                        <?php if (!empty($project['category'])): ?>
                            <span class="project-category"><?php echo sanitize($project['category']); ?></span>
                        <?php endif; ?>
                        <h3 class="project-card-title"><?php echo sanitize($project['title']); ?></h3>
                        <p class="project-card-desc"><?php echo sanitize($project['description'] ?? ''); ?></p>
                        
                        <?php if (!empty($project['tech_stack'])): ?>
                        <div class="project-tech">
                            <?php 
                            $techs = is_array($project['tech_stack']) ? $project['tech_stack'] : explode(',', $project['tech_stack']);
                            foreach ($techs as $tech): ?>
                                <span class="tech-tag"><?php echo sanitize(trim($tech)); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="project-card-footer">
                            <?php if (!empty($project['live_url'])): ?>
                            <a href="<?php echo sanitize($project['live_url']); ?>" target="_blank" class="project-link">
                                <i data-lucide="external-link"></i> Live Demo
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($project['github_url'])): ?>
                            <a href="<?php echo sanitize($project['github_url']); ?>" target="_blank" class="project-link">
                                <i data-lucide="github"></i> Source Code
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state" style="grid-column: 1/-1;">
                    <i data-lucide="folder-open"></i>
                    <h3>No Projects Yet</h3>
                    <p>Projects will appear here once they're added from the admin panel.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
