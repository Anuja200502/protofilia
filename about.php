<?php
/**
 * Protofilia Portfolio - About Page
 */
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'About Me';
$pageDescription = 'Learn more about my journey, skills, and what drives me';
$settings = get_settings();

include __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="section-badge">
            <i data-lucide="user" style="width:14px;height:14px;"></i>
            About Me
        </div>
        <h1 class="page-title">Get to Know Me</h1>
        <p class="page-subtitle">My journey, skills, and what drives me to create</p>
    </div>
</div>

<!-- About Content -->
<section class="section" style="padding-top: 40px;">
    <div class="container">
        <div class="about-grid">
            <div class="about-image-wrapper fade-in">
                <div class="about-image">
                    <div class="about-image-placeholder">
                        <i data-lucide="user-circle"></i>
                        <span style="font-size: 14px;">Profile Photo</span>
                    </div>
                </div>
                <div class="about-image-badge">
                    <span class="badge-number">1+</span>
                    <span class="badge-text">Years of<br>Experience</span>
                </div>
            </div>
            
            <div class="about-text fade-in">
                <h2>I'm <span style="background: var(--gradient-hero); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"><?php echo sanitize($settings['name']); ?></span></h2>
                <p>
                    <?php echo nl2br(sanitize($settings['bio'] ?? 'A passionate developer from Sri Lanka with a love for creating beautiful, functional, and user-friendly web applications.')); ?>
                </p>
                <p>
                    I'm a self-driven developer who loves turning ideas into real, working products. 
                    I enjoy learning new technologies, solving problems through code, and building 
                    projects that make a real impact. Every project I take on is an opportunity to 
                    grow and push my boundaries further.
                </p>
                
                <div class="skills-grid">
                    <div class="skill-card">
                        <div class="skill-icon">
                            <i data-lucide="code-2"></i>
                        </div>
                        <div class="skill-info">
                            <h4>Frontend</h4>
                            <p>HTML, CSS, JavaScript, Bootstrap</p>
                        </div>
                    </div>
                    <div class="skill-card">
                        <div class="skill-icon">
                            <i data-lucide="server"></i>
                        </div>
                        <div class="skill-info">
                            <h4>Backend</h4>
                            <p>PHP, Node.js</p>
                        </div>
                    </div>
                    <div class="skill-card">
                        <div class="skill-icon">
                            <i data-lucide="database"></i>
                        </div>
                        <div class="skill-info">
                            <h4>Database</h4>
                            <p>Supabase, MySQL, PostgreSQL</p>
                        </div>
                    </div>
                    <div class="skill-card">
                        <div class="skill-icon">
                            <i data-lucide="palette"></i>
                        </div>
                        <div class="skill-info">
                            <h4>Design</h4>
                            <p>UI/UX, Responsive Design</p>
                        </div>
                    </div>
                    <div class="skill-card">
                        <div class="skill-icon">
                            <i data-lucide="git-branch"></i>
                        </div>
                        <div class="skill-info">
                            <h4>Tools</h4>
                            <p>Git, GitHub, VS Code</p>
                        </div>
                    </div>
                    <div class="skill-card">
                        <div class="skill-icon">
                            <i data-lucide="smartphone"></i>
                        </div>
                        <div class="skill-info">
                            <h4>Mobile</h4>
                            <p>Flutter, Responsive Web</p>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 36px; display: flex; gap: 16px;">
                    <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary">
                        <i data-lucide="send"></i> Contact Me
                    </a>
                    <a href="<?php echo sanitize($settings['github'] ?? '#'); ?>" target="_blank" class="btn btn-secondary">
                        <i data-lucide="github"></i> GitHub
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
