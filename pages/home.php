<?php
define('FLEXZONE_APP', true);
require_once '../php/config/db_connection.php';
requireLogin();
include '../includes/header.php';
?>

<main class="dashboard">
    <div class="welcome-message">
        <h1>Ready to sweat, <span id="username">User</span>?</h1>
        <p id="greeting-container">Loading...</p>
    </div>

    <div class="quote-container">
        <p id="quote-text">"The only bad workout is the one that didn't happen."</p>
    </div>

    <div class="workout-selector">
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
            <div class="workout-option card-gradient-orange" data-type="custom_builder">
                <i class='bx bx-dumbbell'></i>
                <h3>Custom</h3>
                <p>Build your own routine</p>
            </div>
        </div>
    </div>

    <div class="workout-selector" style="margin-top: 30px;">
        <h2>Targeted Workouts</h2>
        <div class="options-container" style="flex-wrap: wrap; gap: 15px;">
            <div class="workout-option card-gradient-cyan" data-type="custom_targeted" data-muscle="Full Body" data-duration="20" data-equipment="None" style="flex: 1 1 calc(33% - 15px); min-width: 140px;">
                <i class='bx bxs-hot'></i>
                <h3>HIIT</h3>
                <p>High Intensity</p>
            </div>
            <div class="workout-option card-gradient-purple" data-type="custom_targeted" data-muscle="Full Body" data-equipment="Barbell,Dumbbell" style="flex: 1 1 calc(33% - 15px); min-width: 140px;">
                <i class='bx bx-dumbbell'></i>
                <h3>Powerlifting</h3>
                <p>Heavy Lifts</p>
            </div>
            <div class="workout-option card-gradient-red" data-type="custom_targeted" data-muscle="Arms" style="flex: 1 1 calc(33% - 15px); min-width: 140px;">
                <i class='bx bx-body'></i>
                <h3>Arms</h3>
                <p>Biceps & Triceps</p>
            </div>
            <div class="workout-option card-gradient-blue" data-type="custom_targeted" data-muscle="Back" style="flex: 1 1 calc(50% - 15px); min-width: 140px;">
                <i class='bx bx-accessibility'></i>
                <h3>Back</h3>
                <p>Lats & Traps</p>
            </div>
            <div class="workout-option card-gradient-green" data-type="custom_targeted" data-muscle="Core" style="flex: 1 1 calc(50% - 15px); min-width: 140px;">
                <i class='bx bx-layer'></i>
                <h3>Abs & Core</h3>
                <p>Six Pack</p>
            </div>
            <div class="workout-option card-gradient-orange" data-type="custom_targeted" data-muscle="Chest" style="flex: 1 1 calc(50% - 15px); min-width: 140px;">
                <i class='bx bx-stop-circle'></i>
                <h3>Chest</h3>
                <p>Pecs & Push</p>
            </div>
            <div class="workout-option card-gradient-pink" data-type="custom_targeted" data-muscle="Legs" style="flex: 1 1 calc(50% - 15px); min-width: 140px;">
                <i class='bx bx-walk'></i>
                <h3>Legs</h3>
                <p>Quads & Glutes</p>
            </div>
            <div class="workout-option card-gradient-cyan" data-type="custom_targeted" data-muscle="Shoulders" style="flex: 1 1 calc(50% - 15px); min-width: 140px;">
                <i class='bx bx-target-lock'></i>
                <h3>Shoulders</h3>
                <p>Delts & Traps</p>
            </div>
            <div class="workout-option card-gradient-purple" data-type="custom_targeted" data-muscle="Full Body" style="flex: 1 1 calc(50% - 15px); min-width: 140px;">
                <i class='bx bx-male'></i>
                <h3>Full Body</h3>
                <p>Total Conditioning</p>
            </div>
            <div class="workout-option card-gradient-orange" data-type="custom_targeted" data-muscle="Full Body" data-equipment="None" style="flex: 1 1 calc(50% - 15px); min-width: 140px;">
                <i class='bx bx-street-view'></i>
                <h3>Bodyweight</h3>
                <p>No Equipment</p>
            </div>
            <div class="workout-option card-gradient-red" data-type="custom_targeted" data-muscle="Full Body" data-category="Cardio" style="flex: 1 1 calc(100% - 15px); min-width: 140px;">
                <i class='bx bx-run'></i>
                <h3>Cardio</h3>
                <p>Endurance & Stamina</p>
            </div>
        </div>
    </div>

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

    <div class="main-card card-gradient-indigo">
        <h2>Weekly Goal</h2>
        <div class="progress-ring-container">
            <svg width="160" height="160" viewBox="0 0 160 160">
                <circle cx="80" cy="80" r="74" fill="none" stroke="var(--border-color)" stroke-width="6" />
                <circle cx="80" cy="80" r="74" fill="none" stroke="var(--primary-color)" stroke-width="6" stroke-dasharray="465" stroke-dashoffset="465" stroke-linecap="round" transform="rotate(-90 80 80)" />
            </svg>
            <div class="progress-text">
                <span>0/5</span>
                <small>Workouts</small>
            </div>
        </div>
    </div>

    <div class="mini-widgets-grid">
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
