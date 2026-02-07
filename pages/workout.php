<?php
define('FLEXZONE_APP', true);
require_once '../php/config/db_connection.php';
requireLogin();
$bodyClass = 'no-scroll';
include '../includes/header.php';
?>
    <main class="dashboard">
        <div class="live-workout-container">
            <button id="exit-btn" class="logout-btn exit-btn-top">Exit</button>
            <div class="exercise-visual">
                <video autoplay loop muted playsinline key="video-player" style="max-width:100%; max-height:100%; border-radius:15px; object-fit: contain; display: none;">
                    </video>
                 <img id="visual-placeholder-img" src="../assets/exercises/placeholder.png" alt="Exercise visual area" style="max-width:100%; max-height:100%; border-radius:15px; object-fit: contain; display: none;">
                <span id="visual-placeholder-text">Loading visual...</span>
            </div>
            <h2 id="exercise-name">Loading...</h2>
            <div id="exercise-metric" class="timer-display">--</div>
            <div id="rest-timer" class="timer-display" style="display: none;"></div>
            <div class="workout-nav">
                <button id="prev-btn" class="ghost-btn nav-btn" disabled>Previous</button>
                <button id="repeat-btn" class="ghost-btn nav-btn">Repeat</button>
                <button id="pause-btn" class="ghost-btn nav-btn">Pause</button>
                <button id="add-rest-btn" class="ghost-btn nav-btn" style="display: none;">+15s</button>
                <button id="next-btn" class="start-btn nav-btn" style="position: static; width: auto; box-shadow: none;">Next</button>
            </div>
        </div>
    </main>
<?php
include '../includes/footer.php';
?>