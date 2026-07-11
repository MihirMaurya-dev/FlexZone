<?php
define('FLEXZONE_APP', true);
require_once 'php/config/db_connection.php';

if (isLoggedIn()) {
    header("Location: pages/home.php");
    exit;
}

$basePath = './';
$pageTitle = 'FlexZone – Unleash Your True Potential';
$activePage = 'index';
require_once 'includes/head.php';
?>

<!-- ========== HEADER ========== -->
<header class="main-header" id="main-header" style="backdrop-filter: blur(20px); border-bottom: 1px solid var(--glass-border); background: var(--glass-bg);">
    <a href="index.php" class="logo">Flex<span style="color: #F97316;">Zone</span></a>
    <div style="display:flex; align-items:center; gap:20px;">
        <div class="theme-switch-wrapper" style="box-shadow:none; background:transparent; border:none; padding:0;">
            <label class="theme-switch" for="checkbox">
                <input type="checkbox" id="checkbox" />
                <div class="slider round"></div>
            </label>
        </div>
        <a href="pages/join.php" class="btn-glass" style="padding:8px 24px; font-size:0.9rem;">Login</a>
    </div>
</header>

<!-- ========== HERO ========== -->
<main class="hero">


    <div class="hero-content">
        <div class="badge">
            <i class='bx bxs-bolt'></i> FlexZone 2.0 is Here ✨
        </div>

        <h1>Unleash Your <br><span class="gradient-text">True Potential.</span></h1>
        
        <p>The only fitness platform that adapts to your body, your schedule, and your goals. Track workouts, analyze trends, and dominate your fitness journey.</p>

        <div class="cta-group">
            <a href="pages/join.php?action=register" class="btn-glow">
                Start Free Trial <i class='bx bx-right-arrow-alt'></i>
            </a>
            <a href="#features" class="btn-glass">
                See Features
            </a>
        </div>
    </div>

    <!-- Floating 3D Visual -->
    <div class="hero-visual" id="heroVisual">
        <div class="mockup-card mockup-main">
            <div class="mockup-header">
                <div style="display:flex; gap:8px;">
                    <div style="width:12px; height:12px; border-radius:50%; background:#EF4444;"></div>
                    <div style="width:12px; height:12px; border-radius:50%; background:#F59E0B;"></div>
                    <div style="width:12px; height:12px; border-radius:50%; background:#10B981;"></div>
                </div>
                <div class="mockup-line" style="width: 40%;"></div>
            </div>
            <div class="mockup-chart"></div>
            <div class="mockup-line" style="width: 80%; margin-bottom:12px;"></div>
            <div class="mockup-line" style="width: 60%;"></div>
        </div>
        <div class="mockup-card mockup-side-1">
            <div class="mockup-line" style="width: 100%; margin-bottom:20px; height:60px; border-radius:12px; background: rgba(249,115,22,0.1);"></div>
            <div class="mockup-line" style="width: 70%; margin-bottom:12px;"></div>
            <div class="mockup-line" style="width: 40%;"></div>
        </div>
        <div class="mockup-card mockup-side-2">
            <div style="width:100%; height:100px; border-radius:50%; border: 8px solid rgba(249,115,22,0.2); border-top-color:#F97316; margin:0 auto 20px;"></div>
            <div class="mockup-line" style="width: 100%; margin-bottom:12px;"></div>
            <div class="mockup-line" style="width: 80%; margin: 0 auto;"></div>
        </div>
    </div>
</main>

<!-- ========== MARQUEE ========== -->
<div class="marquee-wrapper">
    <div class="marquee-content">
        <span>NO EXCUSES</span> • <span>PUSH LIMITS</span> • <span>TRACK EVERY REP</span> • <span>TRUSTED BY 10,000+ ATHLETES</span> • 
        <span>NO EXCUSES</span> • <span>PUSH LIMITS</span> • <span>TRACK EVERY REP</span> • <span>TRUSTED BY 10,000+ ATHLETES</span> • 
    </div>
</div>

<!-- ========== BENTO FEATURES ========== -->
<section id="features" class="bento-section">
    <div class="section-header">
        <h2>Everything You Need. <br><span class="gradient-text">Nothing You Don't.</span></h2>
        <p>A beautifully designed ecosystem for serious athletes and beginners alike.</p>
    </div>

    <div class="bento-grid">
        <!-- Card 1 -->
        <div class="bento-card bento-large">
            <i class='bx bx-line-chart'></i>
            <h3>Deep Session Logs & Analytics</h3>
            <p>Visualize your journey with dynamic weekly activity charts, weight trend graphs, and muscle group heatmaps. Never guess your progress again.</p>
            <div class="visual">
                <div class="css-chart">
                    <div class="bar" style="height: 40%;"></div>
                    <div class="bar" style="height: 60%;"></div>
                    <div class="bar" style="height: 35%;"></div>
                    <div class="bar" style="height: 80%; background: var(--primary-gradient);"></div>
                    <div class="bar" style="height: 50%;"></div>
                    <div class="bar" style="height: 90%;"></div>
                </div>
            </div>
        </div>
        
        <!-- Card 2 -->
        <div class="bento-card bento-wide">
            <i class='bx bx-dumbbell'></i>
            <h3>Smart Custom Workouts</h3>
            <p>Generate plans tailored to your exact equipment, experience level, and goals. From bodyweight to full gym setups.</p>
        </div>
        
        <!-- Card 3 -->
        <div class="bento-card bento-square">
            <i class='bx bx-trophy'></i>
            <h3>Achievements</h3>
            <p>Climb the global leaderboard and unlock milestone badges.</p>
        </div>
        
        <!-- Card 4 -->
        <div class="bento-card bento-square">
            <i class='bx bx-body'></i>
            <h3>Body Metrics</h3>
            <p>Log measurements and transform with progress photos.</p>
        </div>
    </div>
</section>

<!-- ========== PARALLAX CTA ========== -->
<div class="parallax-cta-wrapper">
    <section class="parallax-cta">
        <h2>Ready to transform?</h2>
        <p>Join FlexZone today. No credit card required.</p>
        <a href="pages/join.php?action=register" class="btn-glow">
            Create Free Account
        </a>
    </section>
</div>

<!-- ========== FOOTER ========== -->
<footer class="footer" id="footer" style="margin-top:0; border-top:none; background:var(--hero-bg);">
    <div class="social" style="display:flex; gap:20px; justify-content:center; margin-bottom:20px;">
        <a href="#" style="font-size:1.5rem; color:var(--hero-sub);"><i class='bx bxl-instagram-alt'></i></a>
        <a href="#" style="font-size:1.5rem; color:var(--hero-sub);"><i class='bx bxl-twitter'></i></a>
        <a href="#" style="font-size:1.5rem; color:var(--hero-sub);"><i class='bx bxl-youtube'></i></a>
    </div>
    <div style="display:flex; gap:20px; justify-content:center; margin-bottom:20px;">
        <a href="terms.html" style="color:var(--hero-sub); text-decoration:none;">Terms</a>
        <a href="privacy.html" style="color:var(--hero-sub); text-decoration:none;">Privacy</a>
        <a href="contact.html" style="color:var(--hero-sub); text-decoration:none;">Contact</a>
    </div>
    <p style="color:var(--hero-sub); text-align:center;">&copy; FlexZone <?php echo date("Y"); ?> — All Rights Reserved</p>
</footer>

<!-- ========== JS ========== -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 3D Parallax Effect on Hero Visual
    const visual = document.getElementById('heroVisual');
    const cards = document.querySelectorAll('.mockup-card');
    
    document.addEventListener('mousemove', (e) => {
        if(!visual) return;
        const x = (window.innerWidth / 2 - e.pageX) / 50;
        const y = (window.innerHeight / 2 - e.pageY) / 50;
        
        cards.forEach((card, index) => {
            const depth = (index + 1) * 0.5;
            card.style.transform = `rotateY(${x * depth}deg) rotateX(${y * depth}deg) translateZ(${depth * 20}px)`;
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
