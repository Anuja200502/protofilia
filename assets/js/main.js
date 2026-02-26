/**
 * Protofilia - Main JavaScript
 * ============================
 */

document.addEventListener('DOMContentLoaded', () => {
    initNavbar();
    initMobileNav();
    initScrollAnimations();
    initFlashAutoHide();
});

/* --- Navbar Scroll Effect --- */
function initNavbar() {
    const navbar = document.getElementById('navbar');
    if (!navbar) return;

    const onScroll = () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll(); // Check initial state
}

/* --- Mobile Navigation --- */
function initMobileNav() {
    const toggle = document.getElementById('navToggle');
    const menu = document.getElementById('navMenu');
    if (!toggle || !menu) return;

    toggle.addEventListener('click', () => {
        toggle.classList.toggle('active');
        menu.classList.toggle('active');
        document.body.style.overflow = menu.classList.contains('active') ? 'hidden' : '';
    });

    // Close menu on link click
    menu.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            toggle.classList.remove('active');
            menu.classList.remove('active');
            document.body.style.overflow = '';
        });
    });
}

/* --- Scroll Animations --- */
function initScrollAnimations() {
    const elements = document.querySelectorAll('.fade-in');
    if (!elements.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    elements.forEach(el => observer.observe(el));
}

/* --- Flash Message Auto-Hide --- */
function initFlashAutoHide() {
    const flash = document.getElementById('flashMessage');
    if (!flash) return;

    setTimeout(() => {
        flash.style.transition = 'opacity 0.5s, transform 0.5s';
        flash.style.opacity = '0';
        flash.style.transform = 'translateY(-20px)';
        setTimeout(() => flash.remove(), 500);
    }, 5000);
}

/* --- Contact Form Submit --- */
function submitContactForm(event) {
    event.preventDefault();

    const form = event.target;
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;

    btn.innerHTML = '<div class="spinner"></div> Sending...';
    btn.disabled = true;

    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', data.message || 'Message sent successfully!');
                form.reset();
            } else {
                showNotification('error', data.message || 'Failed to send message.');
            }
        })
        .catch(error => {
            showNotification('error', 'Network error. Please try again.');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
}

/* --- Notification Helper --- */
function showNotification(type, message) {
    // Remove any existing notification
    const existing = document.querySelector('.flash-message');
    if (existing) existing.remove();

    const icon = type === 'success' ? 'check-circle' : 'alert-circle';
    const flash = document.createElement('div');
    flash.className = `flash-message flash-${type}`;
    flash.id = 'flashMessage';
    flash.innerHTML = `
        <div class="container">
            <i data-lucide="${icon}"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="flash-close">&times;</button>
        </div>
    `;

    document.body.insertBefore(flash, document.querySelector('main'));
    lucide.createIcons();
    initFlashAutoHide();
}

/* --- Project Filter --- */
function filterProjects(category) {
    const cards = document.querySelectorAll('.project-card');
    const buttons = document.querySelectorAll('.filter-btn');

    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    cards.forEach(card => {
        const cardCategory = card.dataset.category;
        if (category === 'all' || cardCategory === category) {
            card.style.display = '';
            card.style.animation = 'fadeInUp 0.4s ease-out';
        } else {
            card.style.display = 'none';
        }
    });
}

/* --- Confirm Delete --- */
function confirmDelete(id, type) {
    if (confirm(`Are you sure you want to delete this ${type}?`)) {
        window.location.href = `?delete=${id}`;
    }
}

/* --- Image Preview --- */
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview) return;

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

/* --- Counter Animation --- */
function animateCounters() {
    const counters = document.querySelectorAll('.stat-number');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        if (isNaN(target)) return;

        let current = 0;
        const increment = target / 40;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                counter.textContent = target + '+';
                clearInterval(timer);
            } else {
                counter.textContent = Math.ceil(current) + '+';
            }
        }, 50);
    });
}

// Initialize counter when hero is visible
const heroSection = document.querySelector('.hero');
if (heroSection) {
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                counterObserver.unobserve(entry.target);
            }
        });
    });
    counterObserver.observe(heroSection);
}