<?php
define('FLEXZONE_APP', true);
require_once '../php/config/db_connection.php';
requireLogin();

$bodyClass = 'no-scroll';
$activePage = 'workout';
include '../includes/header.php';
?>

<main class="dashboard">
    <div class="live-workout-container">

        <!-- ===== PROGRESS HEADER ===== -->
        <div class="workout-progress-header">
            <button id="exit-btn" class="ghost-btn exit-inline-btn">
                <i class='bx bx-x'></i> Exit
            </button>

            <div class="step-counter-wrap">
                <span id="step-label" class="step-label">Step <span id="step-current">–</span> of <span id="step-total">–</span></span>
            </div>

            <div class="workout-timer-badge" id="workout-elapsed-badge">
                <i class='bx bx-time-five'></i>
                <span id="workout-elapsed">00:00</span>
            </div>
        </div>

        <!-- Segmented progress bar -->
        <div class="step-progress-track" id="step-progress-track">
            <!-- Filled dynamically by JS -->
        </div>

        <!-- ===== VISUAL ===== -->
        <div class="exercise-visual">
            <video autoplay loop muted playsinline id="video-player"
                style="max-width:100%; max-height:100%; border-radius:15px; object-fit:contain; display:none;"></video>
            <img id="visual-placeholder-img" src="../assets/exercises/placeholder.png" alt="Exercise"
                style="max-width:100%; max-height:100%; border-radius:15px; object-fit:contain; display:none;">
            <span id="visual-placeholder-text">Loading visual...</span>
        </div>

        <h2 id="exercise-name">Loading...</h2>

        <div id="exercise-metric" class="timer-display">--</div>
        <div id="rest-timer" class="timer-display" style="display:none;"></div>

        <div class="workout-nav">
            <button id="prev-btn"     class="ghost-btn nav-btn" disabled>Previous</button>
            <button id="repeat-btn"   class="ghost-btn nav-btn" style="display:none;">Repeat</button>
            <button id="pause-btn"    class="ghost-btn nav-btn">Pause</button>
            <button id="add-rest-btn" class="ghost-btn nav-btn" style="display:none;">+15s</button>
            <button id="next-btn"     class="start-btn nav-btn" style="position:static; width:auto; box-shadow:none;">Next</button>
        </div>

    </div>
</main>

<!-- ===== EXIT CONFIRMATION MODAL ===== -->
<div class="exit-modal-overlay" id="exit-modal-overlay">
    <div class="exit-modal" id="exit-modal" role="dialog" aria-modal="true" aria-labelledby="exit-modal-title">

        <div class="exit-modal-icon">
            <i class='bx bx-run'></i>
        </div>

        <h3 id="exit-modal-title">Quit this workout?</h3>
        <p class="exit-modal-sub">Your progress for this session won't be saved.</p>

        <div class="exit-modal-stats">
            <div class="exit-stat">
                <i class='bx bx-dumbbell'></i>
                <span id="exit-stat-steps">–</span>
                <small>Steps done</small>
            </div>
            <div class="exit-stat-divider"></div>
            <div class="exit-stat">
                <i class='bx bx-time-five'></i>
                <span id="exit-stat-time">00:00</span>
                <small>Elapsed</small>
            </div>
        </div>

        <div class="exit-modal-actions">
            <button id="exit-modal-cancel" class="ghost-btn exit-modal-btn">
                Keep Going 💪
            </button>
            <button id="exit-modal-confirm" class="exit-modal-btn exit-modal-confirm-btn">
                Exit Workout
            </button>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
