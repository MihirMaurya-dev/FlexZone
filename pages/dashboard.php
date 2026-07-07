<?php
define('FLEXZONE_APP', true);
require_once '../php/config/db_connection.php';
requireLogin();
$activePage = 'dashboard';
include '../includes/header.php';
?>

<main class="dashboard">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1 style="margin: 0; font-size: 1.5rem;">Your Progress</h1>
        <select id="dashboard-time-filter" class="form-input" style="width: auto; padding: 6px 12px; margin: 0; height: auto;">
            <option value="all">All Time</option>
            <option value="30">Last 30 Days</option>
            <option value="7">Last 7 Days</option>
        </select>
    </div>

    <div class="stats-container" style="margin-bottom: 30px;">
        <div class="stat-card card-gradient-indigo">
            <h3>Total Time</h3>
            <p id="total-time">0 Minutes</p>
        </div>
        <div class="stat-card card-gradient-green">
            <h3>Calories</h3>
            <p id="total-calories">0 kcal</p>
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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0;">Activity</h2>
            <div class="chart-toggles" id="activity-chart-toggles">
                <button class="toggle-btn active" data-range="7">7D</button>
                <button class="toggle-btn" data-range="30">30D</button>
                <button class="toggle-btn" data-range="90">3M</button>
            </div>
        </div>
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
