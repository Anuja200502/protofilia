    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-glow"></div>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="<?php echo SITE_URL; ?>" class="nav-logo">
                        <span class="logo-icon">
                            <i data-lucide="hexagon" class="logo-hex"></i>
                            <span class="logo-letter">A</span>
                        </span>
                        <span class="logo-text"><?php echo SITE_NAME; ?></span>
                    </a>
                    <p class="footer-tagline"><?php echo SITE_DESCRIPTION; ?></p>
                </div>
                
                <div class="footer-links">
                    <h4>Navigation</h4>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>">Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/about.php">About</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/projects.php">Projects</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/contact.php">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-social">
                    <h4>Connect</h4>
                    <div class="social-links">
                        <?php if (!empty($settings['github'])): ?>
                        <a href="<?php echo sanitize($settings['github']); ?>" target="_blank" class="social-link" title="GitHub">
                            <i data-lucide="github"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($settings['linkedin'])): ?>
                        <a href="<?php echo sanitize($settings['linkedin']); ?>" target="_blank" class="social-link" title="LinkedIn">
                            <i data-lucide="linkedin"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($settings['twitter'])): ?>
                        <a href="<?php echo sanitize($settings['twitter']); ?>" target="_blank" class="social-link" title="Twitter">
                            <i data-lucide="twitter"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($settings['email'])): ?>
                        <a href="mailto:<?php echo sanitize($settings['email']); ?>" class="social-link" title="Email">
                            <i data-lucide="mail"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2026 <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Main JS -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
