<?php
define('FLEXZONE_APP', true);
require_once '../php/config/db_connection.php';
if (isLoggedIn()) {
    header("Location: home.php");
    exit;
}
include '../includes/header.php';
?>
    <!-- Theme Toggle -->
    <div class="theme-switch-wrapper">
        <label class="theme-switch" for="checkbox">
            <input type="checkbox" id="checkbox" />
            <div class="slider round"></div>
        </label>
        <span>Dark Mode</span>
    </div>
    <!-- Main Slider Container -->
    <div class="slider-container">
        <!-- Slider Wrapper (moves horizontally) -->
        <div class="slider-wrapper" id="sliderWrapper">
            <!-- Slide 1: Login Form -->
            <div class="slide active">
                <div class="form-card">
                    <h1>Welcome Back!</h1>
                    <p class="subtitle">Login to your FlexZone account</p>
                    <form id="loginForm" onsubmit="handleLogin(event)">
                        <div class="input-box">
                            <i class='bx bxs-envelope'></i>
                            <input type="email" id="loginEmail" placeholder="Email" required autocomplete="email" />
                        </div>
                        <div class="input-box">
                            <i class='bx bxs-lock'></i>
                            <input type="password" id="loginPassword" placeholder="Password" required autocomplete="current-password" />
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class='bx bx-log-in'></i> Login
                        </button>
                    </form>
                    <div class="toggle-text">
                        Don't have an account? 
                        <a href="#" onclick="nextSlide(); return false;">Register here</a>
                    </div>
                </div>
            </div>
            <!-- Slide 2: Register Form -->
            <div class="slide">
                <div class="form-card">
                    <h1>Join FlexZone!</h1>
                    <p class="subtitle">Create your free account</p>
                    <form id="registerForm" onsubmit="handleRegister(event)">
                        <div class="input-box">
                            <i class='bx bxs-user'></i>
                            <input type="text" id="registerUsername" placeholder="Username (min 3 chars)" required autocomplete="username" />
                        </div>
                        <div class="input-box">
                            <i class='bx bxs-envelope'></i>
                            <input type="email" id="registerEmail" placeholder="Email" required autocomplete="email" />
                        </div>
                        <div class="input-box">
                            <i class='bx bxs-lock'></i>
                            <input type="password" id="registerPassword" placeholder="Password (min 8 chars)" required autocomplete="new-password" />
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class='bx bx-user-plus'></i> Register
                        </button>
                    </form>
                    <div class="toggle-text">
                        Already have an account? 
                        <a href="#" onclick="prevSlide(); return false;">Login here</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Slider Indicators (Dots) -->
        <div class="slider-indicators">
            <span class="dot active" onclick="goToSlide(0)"></span>
            <span class="dot" onclick="goToSlide(1)"></span>
        </div>
    </div>
<?php
include '../includes/footer.php';
?>