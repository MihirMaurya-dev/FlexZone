<?php
define('FLEXZONE_APP', true);
require_once 'php/config/db_connection.php';
if (isLoggedIn()) {
    header("Location: pages/home.php");
    exit;
}
$basePath = './';
$pageTitle = 'FlexZone - Minimal Fitness';
$activePage = 'index';
require_once 'includes/head.php';
?>
    <header class="main-header">
        <a href="index.php" class="logo">Flex<span>Zone</span></a>
        <div style="display: flex; align-items: center; gap: 20px;">
            <div class="theme-switch-wrapper">
                <label class="theme-switch" for="checkbox">
                    <input type="checkbox" id="checkbox" />
                    <div class="slider round"></div>
                </label>
            </div>
            <a href="pages/join.php" style="font-weight: 600; color: var(--text-color);">Login</a>
        </div>
    </header>
    <main class="landing-hero">
        <div class="hero-content">
            <h1>Track. Train.<br>Transform.</h1>
            <p>Welcome to FlexZone, your all-in-one solution to track your progress, build custom workouts, and achieve your fitness goals.</p>
            <a href="pages/join.php" class="start-btn hero-btn">Get Started For Free</a>
        </div>
    </main>
    <section id="features" class="landing-features">
        <div class="features-container">
            <div class="feature-card">
                <i class='bx bx-line-chart' style="color: var(--primary-color);"></i>
                <h3>Insights</h3>
                <p>Visualize your journey with clean, dynamic data visualizations.</p>
            </div>
            <div class="feature-card">
                <i class='bx bx-dumbbell' style="color: var(--primary-color);"></i>
                <h3>Workouts</h3>
                <p>Curated programs or custom routines tailored to your kit.</p>
            </div>
            <div class="feature-card">
                <i class='bx bx-history' style="color: var(--primary-color);"></i>
                <h3>Logs</h3>
                <p>Never lose track of a session. Simple logging, deep history.</p>
            </div>
        </div>
    </section>
    <section id="about" class="about" style="padding: 40px 24px; max-width: 800px; margin: 0 auto; text-align: center;">
        <div class="about-img" style="margin-bottom: 24px;">
            <img src="assets/about.jpg" alt="Fitness motivation" style="width: 100%; border-radius: 24px; box-shadow: var(--shadow);">
        </div>
        <div class="about-content">
            <h2 style="margin-bottom: 16px;">Why Us?</h2>
            <p style="margin-bottom: 12px;">It's a shame for a man to grow old without seeing the beauty and strength of which his body is capable.</p>
            <p style="margin-bottom: 12px;">FlexZone provides effective workout plans and tracking tools. Let us help you achieve your true potential.</p>
            <p style="font-weight: 600; font-style: italic;">"I HAVE NO ENEMIES"</p>
        </div>
    </section>
    <footer class="footer" id="footer">
        <div class="social">
            <a href="#" aria-label="Instagram"><i class='bx bxl-instagram-alt'></i></a>
            <a href="#" aria-label="Facebook"><i class='bx bxl-facebook-square'></i></a>
            <a href="#" aria-label="LinkedIn"><i class='bx bxl-linkedin-square'></i></a>
        </div>
        <div class="footer-links" style="display: flex; gap: 20px; flex-wrap: wrap; justify-content: center; margin-bottom: 16px;">
            <a href="terms.html">Terms</a>
            <a href="privacy.html">Privacy</a>
            <a href="faq.html">FAQ</a>
            <a href="contact.html">Contact</a>
        </div>
        <p class="copyright">
            &copy; FlexZone <?php echo date("Y"); ?> - All Rights Reserved
        </p>
    </footer>
<?php require_once 'includes/footer.php'; ?>