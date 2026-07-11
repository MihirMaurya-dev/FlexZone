<?php
define('FLEXZONE_APP', true);
require_once 'php/config/db_connection.php';

if (isLoggedIn()) {
    header("Location: pages/home.php");
    exit;
}

$basePath = './';
$pageTitle = 'FlexZone – Track. Train. Transform.';
$activePage = 'index';
require_once 'includes/head.php';
?>

<!-- ========== HEADER ========== -->
<header class="main-header" id="main-header">
    <a href="index.php" class="logo">Flex<span>Zone</span></a>
    <div style="display:flex; align-items:center; gap:20px;">
        <div class="theme-switch-wrapper" style="box-shadow:none; background:transparent; border:none; padding:0;">
            <label class="theme-switch" for="checkbox">
                <input type="checkbox" id="checkbox" />
                <div class="slider round"></div>
            </label>
        </div>
        <a href="pages/join.php" class="hero-btn" style="padding:10px 24px; font-size:0.9rem;">Login</a>
    </div>
</header>

<!-- ========== HERO ========== -->
<main class="landing-hero">
    <!-- Animated Background Orbs -->
    <div class="hero-bg">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <div class="hero-content">
        <div class="hero-badge">
            <i class='bx bxs-bolt'></i> Your All-In-One Fitness Companion
        </div>

        <h1>Track. Train.<br><span class="gradient-text">Transform.</span></h1>

        <p>Welcome to FlexZone — generate custom workouts, track every session, and watch your progress unfold with powerful analytics.</p>

        <div class="hero-cta-group">
            <a href="pages/join.php?action=register" class="hero-btn">
                <i class='bx bxs-rocket'></i> Get Started Free
            </a>
            <a href="#features" class="hero-btn-secondary">
                <i class='bx bx-play-circle'></i> See Features
            </a>
        </div>

        <!-- Animated Stats Bar -->
        <div class="stats-bar">
            <div class="stat-pill">
                <span class="stat-num" data-target="46" data-suffix="+">0</span>
                <span class="stat-label">Exercises</span>
            </div>
            <div class="stat-pill">
                <span class="stat-num" data-target="4">0</span>
                <span class="stat-label">Workout Modes</span>
            </div>
            <div class="stat-pill">
                <span class="stat-num" data-target="100" data-suffix="%">0</span>
                <span class="stat-label">Free Forever</span>
            </div>
        </div>
    </div>

    <!-- Scroll Hint -->
    <div class="scroll-hint" onclick="document.getElementById('features').scrollIntoView({behavior:'smooth'})">
        <span>Scroll</span>
        <i class='bx bx-chevron-down'></i>
    </div>
</main>

<!-- ========== FEATURES ========== -->
<section id="features" class="landing-features">
    <div class="section-heading">
        <span class="section-tag">What's Inside</span>
        <h2>Everything You Need to Succeed</h2>
        <p>A complete fitness ecosystem designed for every level.</p>
    </div>

    <div class="features-container">
        <div class="feature-card">
            <div class="icon-wrap"><i class='bx bx-line-chart'></i></div>
            <h3>Powerful Insights</h3>
            <p>Visualize your journey with dynamic weekly activity charts, weight trend graphs, and streak heatmaps.</p>
        </div>
        <div class="feature-card">
            <div class="icon-wrap"><i class='bx bx-dumbbell'></i></div>
            <h3>Smart Workouts</h3>
            <p>Generate beginner, intermediate, or advanced plans — or build fully custom routines tailored to your equipment.</p>
        </div>
        <div class="feature-card">
            <div class="icon-wrap"><i class='bx bx-history'></i></div>
            <h3>Deep Session Logs</h3>
            <p>Never lose track of a session. Every workout auto-saves with duration, calories, and a complete activity log.</p>
        </div>
        <div class="feature-card">
            <div class="icon-wrap"><i class='bx bx-trophy'></i></div>
            <h3>Leaderboard & Badges</h3>
            <p>Compete with other athletes, climb the global leaderboard, and unlock achievement badges for your milestones.</p>
        </div>
        <div class="feature-card">
            <div class="icon-wrap"><i class='bx bx-body'></i></div>
            <h3>Body Measurements</h3>
            <p>Log chest, waist, arms, and thighs over time. Upload progress photos and track your physical transformation.</p>
        </div>
        <div class="feature-card">
            <div class="icon-wrap"><i class='bx bxs-moon'></i></div>
            <h3>Dark & Light Modes</h3>
            <p>FlexZone adapts to your preference — a sleek dark mode for night sessions and a clean light mode for daytime use.</p>
        </div>
    </div>
</section>

<!-- ========== PARALLAX CTA STRIPE ========== -->
<section class="parallax-stripe" id="parallax-stripe">
    <div class="parallax-stripe-content">
        <h2>Ready to Start Your Journey?</h2>
        <p>Join FlexZone today. No credit card, no commitment — just results.</p>
        <a href="pages/join.php?action=register" class="hero-btn">
            <i class='bx bxs-user-plus'></i> Create Free Account
        </a>
    </div>
</section>

<!-- ========== ABOUT ========== -->
<section id="about" class="about-section">
    <div class="about-img">
        <img src="assets/about.png" alt="Fitness motivation" loading="lazy">
    </div>
    <div class="about-content">
        <h2>Why <span>FlexZone</span>?</h2>
        <p>It's a shame for a man to grow old without seeing the beauty and strength of which his body is capable.</p>
        <p>FlexZone was built to remove every excuse — smart workouts, real-time calorie tracking, and progress measurement in one place.</p>
        <p class="quote">"I HAVE NO ENEMIES — only opportunities to grow stronger."</p>
    </div>
</section>

<!-- ========== FOOTER ========== -->
<footer class="footer" id="footer">
    <div class="social">
        <a href="#" aria-label="Instagram"><i class='bx bxl-instagram-alt'></i></a>
        <a href="#" aria-label="Facebook"><i class='bx bxl-facebook-square'></i></a>
        <a href="#" aria-label="LinkedIn"><i class='bx bxl-linkedin-square'></i></a>
    </div>
    <div style="display:flex; gap:20px; flex-wrap:wrap; justify-content:center;">
        <a href="terms.html" style="color:var(--secondary-text); font-size:0.9rem;">Terms</a>
        <a href="privacy.html" style="color:var(--secondary-text); font-size:0.9rem;">Privacy</a>
        <a href="faq.html" style="color:var(--secondary-text); font-size:0.9rem;">FAQ</a>
        <a href="contact.html" style="color:var(--secondary-text); font-size:0.9rem;">Contact</a>
    </div>
    <p class="copyright">&copy; FlexZone <?php echo date("Y"); ?> — All Rights Reserved</p>
</footer>

<!-- ========== LANDING JS ========== -->
<script>
(function() {
    /* ---- Header scroll glass effect & Scroll Hint Fade ---- */
    const header = document.getElementById('main-header');
    const scrollHint = document.querySelector('.scroll-hint');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 60) {
            header.style.background = 'var(--card-bg)';
            header.style.backdropFilter = 'blur(20px)';
            header.style.boxShadow = '0 1px 0 var(--border-color)';
        } else {
            header.style.background = 'transparent';
            header.style.backdropFilter = 'none';
            header.style.boxShadow = 'none';
        }

        if (scrollHint) {
            if (window.scrollY > 100) {
                scrollHint.style.opacity = '0';
                scrollHint.style.pointerEvents = 'none';
            } else {
                scrollHint.style.opacity = '0.5';
                scrollHint.style.pointerEvents = 'auto';
            }
        }
    }, { passive: true });

    /* ---- Parallax orbs on mouse move ---- */
    const orbs = document.querySelectorAll('.hero-bg .orb');
    document.addEventListener('mousemove', (e) => {
        const x = (e.clientX / window.innerWidth - 0.5) * 2;
        const y = (e.clientY / window.innerHeight - 0.5) * 2;
        orbs.forEach((orb, i) => {
            const factor = (i + 1) * 12;
            orb.style.transform = `translate(${x * factor}px, ${y * factor}px)`;
        });
    });

    /* ---- Parallax stripe on scroll ---- */
    const stripe = document.getElementById('parallax-stripe');
    if (stripe) {
        window.addEventListener('scroll', () => {
            const rect = stripe.getBoundingClientRect();
            const offset = -(rect.top * 0.3);
            stripe.style.backgroundPositionY = offset + 'px';
        }, { passive: true });
    }

    /* ---- Animated counter ---- */
    function animateCounter(el) {
        const target = parseInt(el.dataset.target, 10);
        const suffix = el.dataset.suffix || '';
        const duration = 1500;
        const startTime = performance.now();

        function step(now) {
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / duration, 1);
            // easeOutCubic
            const ease = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.floor(ease * target) + suffix;
            if (progress < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    /* ---- Intersection Observer for counters + feature cards ---- */
    const counterEls = document.querySelectorAll('.stat-num[data-target]');
    const featureCards = document.querySelectorAll('.feature-card');

    const counterTriggered = new Set();
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;

            // Counters
            if (entry.target.classList.contains('stats-bar')) {
                counterEls.forEach(el => {
                    if (!counterTriggered.has(el)) {
                        counterTriggered.add(el);
                        animateCounter(el);
                    }
                });
            }

            // Feature cards staggered fade-in
            if (entry.target.classList.contains('feature-card')) {
                const idx = [...featureCards].indexOf(entry.target);
                setTimeout(() => {
                    entry.target.classList.add('in-view');
                    entry.target.style.transition = `opacity 0.5s ease ${idx * 80}ms, transform 0.5s cubic-bezier(0.34,1.56,0.64,1) ${idx * 80}ms, border-color 0.3s, box-shadow 0.3s`;
                }, 0);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.25 });

    const statsBar = document.querySelector('.stats-bar');
    if (statsBar) observer.observe(statsBar);
    featureCards.forEach(card => observer.observe(card));
})();
</script>

<?php require_once 'includes/footer.php'; ?>
