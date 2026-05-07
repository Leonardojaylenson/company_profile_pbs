// ============================================================
// public/js/main.js — PT. Prima Bahari Sejahtera
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // ── 1. AOS INIT ─────────────────────────────────────────
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 700,
            once: true,
            offset: 60,
            easing: 'ease-out-cubic',
        });
    }

    // ── 2. NAVBAR SCROLL EFFECT ──────────────────────────────
    const nav = document.getElementById('mainNav');
    if (nav) {
        const onScroll = () => {
            nav.classList.toggle('scrolled', window.scrollY > 50);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll(); // run once on load
    }

    // ── 3. BACK TO TOP ───────────────────────────────────────
    const btn = document.getElementById('backToTop');
    if (btn) {
        window.addEventListener('scroll', () => {
            btn.classList.toggle('show', window.scrollY > 400);
        }, { passive: true });

        btn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ── 4. STAT COUNTER ANIMATION ────────────────────────────
    const counters = document.querySelectorAll('.stat-number[data-count]');
    if (counters.length) {
        const animateCounter = (el) => {
            const target = parseInt(el.dataset.count, 10);
            if (isNaN(target)) return;
            const duration = 2000;
            const step = Math.ceil(target / (duration / 16));
            let current = 0;
            const timer = setInterval(() => {
                current = Math.min(current + step, target);
                el.textContent = current.toLocaleString('id-ID');
                if (current >= target) clearInterval(timer);
            }, 16);
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });

        counters.forEach(el => observer.observe(el));
    }

    // ── 5. SMOOTH SCROLL FOR ANCHOR LINKS ────────────────────
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                const offset = 80; // navbar height
                const top = target.getBoundingClientRect().top + window.scrollY - offset;
                window.scrollTo({ top, behavior: 'smooth' });
            }
        });
    });

    // ── 6. NAVBAR COLLAPSE ON MOBILE LINK CLICK ──────────────
    const navCollapse = document.getElementById('navMain');
    if (navCollapse) {
        navCollapse.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    const bsCollapse = bootstrap.Collapse.getInstance(navCollapse);
                    if (bsCollapse) bsCollapse.hide();
                }
            });
        });
    }

    // ── 7. CONTACT FORM FEEDBACK (if exists) ─────────────────
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function () {
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
            }
        });
    }

    // ── 8. FLASH MESSAGE AUTO DISMISS ────────────────────────
    document.querySelectorAll('.alert-dismissible').forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getInstance(alert);
            if (bsAlert) bsAlert.close();
            else alert.remove();
        }, 5000);
    });

});