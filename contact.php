<?php
/**
 * Protofilia Portfolio - Contact Page
 */
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Contact';
$pageDescription = 'Get in touch with me for collaborations, freelance work, or just to say hi';
$settings = get_settings();

include __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="section-badge">
            <i data-lucide="mail" style="width:14px;height:14px;"></i>
            Contact
        </div>
        <h1 class="page-title">Get In Touch</h1>
        <p class="page-subtitle">Have a question or want to work together? Drop me a message!</p>
    </div>
</div>

<!-- Contact Section -->
<section class="section" style="padding-top: 40px;">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Info -->
            <div class="contact-info-card fade-in">
                <h3>Let's Connect</h3>
                <p>Feel free to reach out through any of these channels or use the contact form.</p>
                
                <div class="contact-detail">
                    <div class="contact-detail-icon">
                        <i data-lucide="mail"></i>
                    </div>
                    <div class="contact-detail-text">
                        <h4>Email</h4>
                        <p><a href="mailto:<?php echo sanitize($settings['email']); ?>"><?php echo sanitize($settings['email']); ?></a></p>
                    </div>
                </div>
                
                <div class="contact-detail">
                    <div class="contact-detail-icon">
                        <i data-lucide="phone"></i>
                    </div>
                    <div class="contact-detail-text">
                        <h4>Mobile</h4>
                        <p><a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $settings['phone'] ?? ''); ?>"><?php echo sanitize($settings['phone'] ?? '+94 71 1350 958'); ?></a></p>
                    </div>
                </div>
                
                <div class="contact-detail">
                    <div class="contact-detail-icon">
                        <i data-lucide="linkedin"></i>
                    </div>
                    <div class="contact-detail-text">
                        <h4>LinkedIn</h4>
                        <p><a href="<?php echo sanitize($settings['linkedin']); ?>" target="_blank">Connect with me</a></p>
                    </div>
                </div>
                
                <div class="contact-detail">
                    <div class="contact-detail-icon">
                        <i data-lucide="map-pin"></i>
                    </div>
                    <div class="contact-detail-text">
                        <h4>Location</h4>
                        <p><?php echo sanitize($settings['location'] ?? 'Matara, Sri Lanka 🇱🇰'); ?></p>
                    </div>
                </div>
                
                <div style="margin-top: 28px;">
                    <h4 style="font-size: 14px; margin-bottom: 14px;">Follow Me</h4>
                    <div class="social-links">
                        <?php if (!empty($settings['linkedin'])): ?>
                        <a href="<?php echo sanitize($settings['linkedin']); ?>" target="_blank" class="social-link"><i data-lucide="linkedin"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="contact-form-card fade-in">
                <h3 style="font-size: 24px; font-weight: 700; margin-bottom: 8px;">Send a Message</h3>
                <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 28px;">
                    Fill out the form below and I'll get back to you as soon as possible.
                </p>
                
                <form action="<?php echo SITE_URL; ?>/api/contact.php" method="POST" onsubmit="submitContactForm(event)" id="contactForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" id="name" name="name" class="form-input" placeholder="John Doe" required>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" class="form-input" placeholder="john@example.com" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-input" placeholder="Project Inquiry" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message" class="form-label">Message</label>
                        <textarea id="message" name="message" class="form-textarea" placeholder="Tell me about your project..." required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                        <i data-lucide="send"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
