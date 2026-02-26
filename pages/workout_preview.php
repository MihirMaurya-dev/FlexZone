<?php
define('FLEXZONE_APP', true);
require_once '../php/config/db_connection.php';
requireLogin();
$activePage = 'workout_preview';
include '../includes/header.php';
?>

<main class="dashboard">
    <div class="preview-card">
        <div style="width: 100%; display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; margin-bottom: 24px;">
            <div style="text-align: left;">
                <a href="javascript:history.back()" class="ghost-btn">
                    <i class='bx bx-arrow-back'></i> Back
                </a>
            </div>
            
            <div class="preview-header" style="text-align: center;">
                <h1 id="workout-title" style="margin: 0; font-size: 1.3rem;">Loading...</h1>
                <p id="workout-details" style="margin: 0; font-size: 0.9rem;">Fetching plan...</p>
            </div>
            
            <div style="visibility: hidden; pointer-events: none;">
                <a class="ghost-btn"><i class='bx bx-arrow-back'></i> Back</a>
            </div>
        </div>

        <div class="exercise-list" id="exercise-list">
            <div style="text-align: center; padding: 40px;">
                <div class="spinner"></div>
                <p style="color: var(--secondary-text); margin-top: 10px;">Generating your routine...</p>
            </div>
        </div>

        <button class="start-btn" id="start-workout-btn" disabled style="position: static; width: 100%; margin-top: 20px;">
            Start Workout
        </button>
    </div>
</main>

<?php
include '../includes/navbar.php';
include '../includes/footer.php';
?>
