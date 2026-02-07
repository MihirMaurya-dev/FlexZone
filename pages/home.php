<?php
define('FLEXZONE_APP', true);
require_once '../php/config/db_connection.php';
requireLogin();
include '../includes/header.php';
?>
    <main class="dashboard">
        <!-- Section 1: Greeting -->
        <div class="welcome-message">
            <h1>Ready to sweat, <span id="username">User</span>?</h1>
            <p id="greeting-container">Leg Day • 45 Minutes</p>
        </div>
        <!-- Section 2.8: Quote (Moved Up) -->
        <div class="quote-container">
            <p id="quote-text">"The only bad workout is the one that didn't happen."</p>
        </div>
        <!-- Section 2: Quick Actions (Moved Up) -->
        <div class="workout-selector" data-intro="Choose a workout plan to get started!" data-step="3">
            <h2>Quick Actions</h2>
            <div class="options-container">
                <div class="workout-option card-gradient-green" data-type="beginner">
                    <i class='bx bx-leaf'></i>
                    <h3>Beginner</h3>
                    <p>Start journey</p>
                </div>
                <div class="workout-option card-gradient-blue" data-type="intermediate">
                    <i class='bx bxs-bolt'></i>
                    <h3>Intermediate</h3>
                    <p>Challenge</p>
                </div>
                <div class="workout-option card-gradient-red" data-type="advanced">
                    <i class='bx bx-crown'></i>
                    <h3>Advanced</h3>
                    <p>Mastery</p>
                </div>
            <div class="workout-option card-gradient-orange" data-type="custom">
                <i class="fas fa-dumbbell"></i>
                <h3>Custom</h3>
                <p>Build your own routine</p>
            </div>
            </div>
        </div>
        <!-- Section 1.5: Daily Challenge -->
        <div class="challenge-card card-gradient-orange" id="daily-challenge-card">
            <div class="challenge-header">
                <i class='bx bxs-flame'></i>
                <h3>Daily Challenge</h3>
            </div>
            <p id="challenge-text">Loading...</p>
            <div class="challenge-check">
                <input type="checkbox" id="challenge-checkbox">
                <label for="challenge-checkbox">Mark as Complete</label>
            </div>
        </div>
        <!-- Section 2: Main Card (Weekly Progress) -->
        <div class="main-card card-gradient-indigo" data-intro="Track your weekly workout goals here." data-step="2">
            <h2>Weekly Goal</h2>
            <div class="progress-ring-container">
                <!-- Simple CSS Circular Progress Mockup -->
                <svg width="160" height="160" viewBox="0 0 160 160">
                    <circle cx="80" cy="80" r="74" fill="none" stroke="var(--border-color)" stroke-width="6" />
                    <circle cx="80" cy="80" r="74" fill="none" stroke="var(--primary-color)" stroke-width="6" 
                            stroke-dasharray="465" stroke-dashoffset="186" stroke-linecap="round" transform="rotate(-90 80 80)" />
                </svg>
                <div class="progress-text">
                    <span>3/5</span>
                    <small>Workouts</small>
                </div>
            </div>
        </div>
        <!-- Section 2.5: Mini Widgets (Hydration + Recent) -->
        <div class="mini-widgets-grid">
            <!-- Hydration -->
            <div class="widget-card hydration-card card-gradient-cyan">
                <div class="widget-header">
                    <i class='bx bxs-droplet'></i>
                    <h3>Hydration</h3>
                </div>
                <div class="hydration-controls">
                    <button id="hydro-minus" class="hydro-btn"><i class='bx bx-minus'></i></button>
                    <div class="hydro-count">
                        <span id="hydro-current">0</span>
                        <small>/ 8 cups</small>
                    </div>
                    <button id="hydro-plus" class="hydro-btn"><i class='bx bx-plus'></i></button>
                </div>
            </div>
            <!-- Recent Activity -->
            <div class="widget-card recent-card card-gradient-pink" onclick="window.location.href='history.php'">
                <div class="widget-header">
                    <i class='bx bx-history'></i>
                    <h3>Last Workout</h3>
                </div>
                <div id="recent-workout-content">
                    <p class="recent-name">No data yet</p>
                    <p class="recent-details">Start your first workout!</p>
                </div>
            </div>
        </div>
    </main>
<?php
include '../includes/navbar.php';
include '../includes/footer.php';
?>