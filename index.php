<?php
/**
 * Protofilia Portfolio - Home Page
 */
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Home';
$pageDescription = 'Creative portfolio showcasing my best projects and skills';
$settings = get_settings();
$allProjects = get_projects();
$projectCount = count($allProjects);
$featuredProjects = get_projects(true, 3);

include __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <div class="hero-badge">
                    <span class="dot"></span>
                    Available for freelance work
                </div>
                
                <h1 class="hero-title">
                    Hi, I'm <span class="highlight"><?php echo sanitize($settings['name']); ?></span><br>
                    I build digital<br>experiences.
                </h1>
                
                <p class="hero-subtitle">
                    <?php echo sanitize($settings['title']); ?> — crafting beautiful, performant, 
                    and user-centric web applications that make a difference.
                </p>
                
                <div class="hero-actions">
                    <a href="<?php echo SITE_URL; ?>/projects.php" class="btn btn-primary">
                        View My Work <i data-lucide="arrow-right"></i>
                    </a>
                    <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-secondary">
                        <i data-lucide="message-circle"></i> Let's Talk
                    </a>
                </div>
                
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number" data-target="<?php echo $projectCount; ?>">0+</div>
                        <div class="stat-label">Projects Completed</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" data-target="2">0+</div>
                        <div class="stat-label">Years Experience</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" data-target="5">0+</div>
                        <div class="stat-label">Happy Clients</div>
                    </div>
                </div>
            </div>
            
            <div class="hero-visual">
                <div class="hero-card">
                    <div class="hero-card-header">
                        <span class="dot-red">●</span>
                        <span class="dot-yellow">●</span>
                        <span class="dot-green">●</span>
                        <span style="margin-left: 8px; opacity: 0.5;">portfolio.js</span>
                    </div>
                    <div class="hero-code">
                        <span class="keyword">const</span> <span class="variable">developer</span> = <span class="bracket">{</span><br>
                        &nbsp;&nbsp;<span class="variable">name</span>: <span class="string">"<?php echo sanitize($settings['name']); ?>"</span>,<br>
                        &nbsp;&nbsp;<span class="variable">role</span>: <span class="string">"<?php echo sanitize($settings['title']); ?>"</span>,<br>
                        &nbsp;&nbsp;<span class="variable">passion</span>: <span class="string">"Building amazing things"</span>,<br>
                        &nbsp;&nbsp;<span class="function">create</span>: () =&gt; <span class="bracket">{</span><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="keyword">return</span> <span class="string">"✨ Magic"</span>;<br>
                        &nbsp;&nbsp;<span class="bracket">}</span><br>
                        <span class="bracket">}</span>;<br>
                        <br>
                        <span class="comment">// Let's build something awesome!</span><br>
                        <span class="variable">developer</span>.<span class="function">create</span>();<span class="typing-cursor"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Projects Section -->
<section class="section" id="featured">
    <div class="container">
        <div class="section-header fade-in">
            <div class="section-badge">
                <i data-lucide="sparkles" style="width:14px;height:14px;"></i>
                Featured Work
            </div>
            <h2 class="section-title">Selected Projects</h2>
            <p class="section-subtitle">A curated collection of my latest and most impactful work</p>
        </div>
        
        <div class="projects-grid">
            <?php if (!empty($featuredProjects)): ?>
                <?php foreach ($featuredProjects as $project): ?>
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
                            <a href="<?php echo SITE_URL; ?>/projects.php?slug=<?php echo sanitize($project['slug'] ?? ''); ?>" class="btn btn-sm btn-secondary">
                                View Details <i data-lucide="external-link"></i>
                            </a>
                        </div>
                    </div>
                    <div class="project-card-body">
                        <?php if (!empty($project['category'])): ?>
                            <span class="project-category"><?php echo sanitize($project['category']); ?></span>
                        <?php endif; ?>
                        <h3 class="project-card-title"><?php echo sanitize($project['title']); ?></h3>
                        <p class="project-card-desc"><?php echo truncate(sanitize($project['description'] ?? '')); ?></p>
                        
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
                                <i data-lucide="github"></i> Code
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state" style="grid-column: 1/-1;">
                    <i data-lucide="folder-open"></i>
                    <h3>Projects Coming Soon</h3>
                    <p>Featured projects will appear here once added from the admin panel.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 48px;" class="fade-in">
            <a href="<?php echo SITE_URL; ?>/projects.php" class="btn btn-secondary">
                View All Projects <i data-lucide="arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section">
    <div class="container">
        <div class="contact-form-card fade-in" style="text-align: center; padding: 64px 32px;">
            <div class="section-badge" style="margin-bottom: 20px;">
                <i data-lucide="handshake" style="width:14px;height:14px;"></i>
                Let's Collaborate
            </div>
            <h2 style="font-size: 32px; font-weight: 800; margin-bottom: 12px;">Have a project in mind?</h2>
            <p style="color: var(--text-secondary); max-width: 500px; margin: 0 auto 32px; font-size: 16px;">
                I'm always open to discussing new opportunities and creative ideas. Let's connect!
            </p>
            <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary">
                <i data-lucide="send"></i> Get In Touch
            </a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>