<?php
define('FLEXZONE_APP', true);
require_once '../php/config/db_connection.php';
requireLogin();
include '../includes/header.php';
?>
    <main class="dashboard">
        <h1 style="margin-bottom: 24px; font-size: 1.5rem;">Your Progress</h1>
        <div class="stats-container" style="margin-bottom: 30px;">
            <div class="stat-card card-gradient-indigo">
                <h3>Total Time</h3>
                <p id="total-time">0m</p>
            </div>
            <div class="stat-card card-gradient-green">
                <h3>Calories</h3>
                <p id="total-calories">0</p>
            </div>
            <div class="stat-card card-gradient-blue">
                <h3>This Week</h3>
                <p id="weekly-workouts">0</p>
            </div>
            <div class="stat-card card-gradient-orange">
                <h3>Total Workouts</h3>
                <p id="total-workouts">0</p>
            </div>
        </div>

        <div class="main-card card-gradient-blue">
            <h2>Weekly Activity</h2>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="activityChart"></canvas>
            </div>
        </div>
        <div class="main-card card-gradient-cyan">
            <h2>Weight Trend</h2>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="weightChart"></canvas>
            </div>
        </div>
    </main>
<?php
include '../includes/navbar.php';
include '../includes/footer.php';
?>