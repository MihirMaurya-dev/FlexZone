<?php
define('FLEXZONE_APP', true);
require_once '../php/config/db_connection.php';
requireLogin();
include '../includes/header.php';
?>
    <div class="profile-layout">
        <!-- SIDEBAR -->
        <aside class="profile-sidebar">
            <div class="user-card">
                <div class="avatar-container">
                    <img src="../assets/default_avatar.png" alt="Profile" id="user-avatar">
                    <button class="edit-avatar-btn"><i class='bx bxs-camera'></i></button>
                    <input type="file" id="avatar-upload" accept="image/*" style="display: none;">
                </div>
                <h2 id="display-username">Loading...</h2>
                <p id="display-email">user@example.com</p>
            </div>
            <nav class="settings-nav">
                <a href="#overview" class="active"><i class='bx bx-stats'></i> Overview</a>
                <a href="#garage"><i class='bx bx-dumbbell'></i> My Garage</a>
                <a href="history.php"><i class='bx bx-history'></i> History</a>
                <a href="#settings"><i class='bx bx-cog'></i> Settings</a>
                <a href="../help.html"><i class='bx bx-help-circle'></i> Help</a>
                <a href="../credits.html"><i class='bx bx-info-circle'></i> Credits</a>
                <a href="../php/auth/logout.php" class="logout-nav"><i class='bx bx-log-out'></i> Logout</a>
            </nav>
        </aside>
        <!-- MAIN CONTENT -->
        <main class="profile-content">
            <!-- 1. HEADER STATS -->
            <section id="overview" class="stats-header">
                <div class="stat-box">
                    <div class="icon-bg fire"><i class='bx bxs-bolt' style="font-size: 32px;"></i></div>
                    <div class="stat-info">
                        <h3>Streak</h3>
                        <p><span id="streak-count">0</span> Days</p>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="icon-bg trophy"><i class='bx bxs-trophy'></i></div>
                    <div class="stat-info">
                        <h3>Workouts</h3>
                        <p><span id="total-workouts">0</span></p>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="icon-bg time"><i class='bx bx-calendar'></i></div>
                    <div class="stat-info">
                        <h3>Last Workout</h3>
                        <p><span id="last-workout-date">--</span></p>
                    </div>
                </div>
            </section>
            <!-- 2. QUICK STATS -->
            <section class="quick-stats-grid">
                <!-- Weight Log Card -->
                <div class="card">
                    <h3>Log Today's Weight</h3>
                    <form id="weight-log-form" onsubmit="logWeight(event)" style="margin-top: 15px;">
                        <div class="setting-group" style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 8px;">Weight (kg)</label>
                            <input type="number" step="0.1" id="weight-input" class="form-input" placeholder="0.0" required>
                        </div>
                        <button type="submit" class="save-btn" style="width: 100%;">Log Entry</button>
                    </form>
                </div>
            </section>
            <!-- 3. HEATMAP -->
            <section class="heatmap-section card">
                <h3>Activity History</h3>
                <div class="heatmap-container" id="activity-heatmap"></div>
            </section>
            <!-- Badges Section -->
            <section class="card" style="margin-bottom: 24px;">
                <h3>Achievements</h3>
                <div id="badges-list" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 12px; margin-top: 16px;"></div>
            </section>
            <!-- 4. GARAGE -->
            <section id="garage" class="garage-section card">
                <h3>My Garage</h3>
                <div class="equipment-grid" id="equipment-grid"></div>
                <button class="save-btn" onclick="saveGarage()">Save Equipment</button>
            </section>
            <!-- 5. SETTINGS -->
            <section id="settings" class="settings-section card">
                <h3>Settings</h3>
                <form id="settings-form" onsubmit="saveSettings(event)">
                    <div class="setting-group">
                        <label>App Appearance</label>
                        <div class="setting-row">
                            <span>Dark Mode</span>
                            <div class="theme-switch-wrapper" style="margin: 0;">
                                <label class="theme-switch" for="checkbox">
                                    <input type="checkbox" id="checkbox" />
                                    <div class="slider round"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="setting-group">
                        <label>Units</label>
                        <div class="toggle-options">
                            <input type="radio" name="units" value="kg" id="unit-kg" checked>
                            <label for="unit-kg">Metric (kg)</label>
                            <input type="radio" name="units" value="lbs" id="unit-lbs">
                            <label for="unit-lbs">Imperial (lbs)</label>
                        </div>
                    </div>
                    <div class="setting-group">
                        <label>Notifications</label>
                        <div class="checkbox-group-vertical">
                            <label class="checkbox-item">
                                <input type="checkbox" name="notif_workouts" checked>
                                <span>Workout reminders</span>
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox" name="notif_weekly" checked>
                                <span>Weekly summaries</span>
                            </label>
                        </div>
                    </div>
                    <div class="setting-group">
                        <label>Data</label>
                        <button type="button" class="ghost-btn" onclick="exportData()" style="width: 100%; text-align: center;">
                            <i class='bx bx-download'></i> Export Workout History
                        </button>
                    </div>
                    <button type="submit" class="save-btn" style="margin-top: 20px;">Save Settings</button>
                </form>
            </section>
        </main>
    </div>
<?php
include '../includes/navbar.php';
include '../includes/footer.php';
?>