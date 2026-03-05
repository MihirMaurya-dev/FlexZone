<?php
define('FLEXZONE_APP', true);
require_once '../php/config/db_connection.php';
requireLogin();
$activePage = 'profile';
include '../includes/header.php';
?>

<div class="profile-layout">
    <aside class="profile-sidebar">
        <div class="user-card">
            <div class="avatar-container">
                <img id="user-avatar" src="../assets/default_avatar.png" alt="Profile">
                <button class="edit-avatar-btn"><i class='bx bxs-camera'></i></button>
                <input type="file" id="avatar-upload" name="avatar" accept="image/*" style="display: none;">
            </div>
            <h2 id="display-username">User</h2>
            <p id="display-email">email@example.com</p>
            <button class="logout-btn" onclick="window.location.href='../php/auth/logout.php'" style="margin-top: 15px; width: 100%;">
                <i class='bx bx-log-out'></i> Logout
            </button>
        </div>

        <nav class="settings-nav">
            <a href="#activity" class="active"><i class='bx bx-grid-alt'></i> Activity</a>
            <a href="#badges"><i class='bx bx-medal'></i> Badges</a>
            <a href="#equipment"><i class='bx bx-dumbbell'></i> My Garage</a>
            <a href="#settings"><i class='bx bx-cog'></i> Settings</a>
            <a href="#" onclick="exportData()"><i class='bx bx-download'></i> Export Data</a>
            <a href="../help.html"><i class='bx bx-help-circle'></i> Help Center</a>
            <a href="../credits.html"><i class='bx bx-info-circle'></i> Credits</a>
        </nav>
    </aside>

    <main class="profile-content">
        <section id="activity" class="card">
            <h2>Training Consistency</h2>
            <div class="stats-header">
                <div class="stat-box">
                    <div class="icon-bg fire"><i class='bx bxs-hot'></i></div>
                    <div class="stat-info">
                        <h3>Streak</h3>
                        <p><span id="streak-count">0</span> Days</p>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="icon-bg trophy"><i class='bx bxs-trophy'></i></div>
                    <div class="stat-info">
                        <h3>Total</h3>
                        <p><span id="total-workouts">0</span> Workouts</p>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="icon-bg time"><i class='bx bxs-calendar'></i></div>
                    <div class="stat-info">
                        <h3>Last Session</h3>
                        <p id="last-workout-date">--</p>
                    </div>
                </div>
            </div>
            <div class="heatmap-container" id="activity-heatmap"></div>
        </section>

        <section id="weight" class="card">
            <h2>Log Weight</h2>
            <form id="weight-form" onsubmit="logWeight(event)">
                <div style="display: flex; gap: 15px; align-items: flex-end;">
                    <div class="setting-group" style="flex: 1; margin-bottom: 0;">
                        <label id="weight-label">Weight (kg)</label>
                        <input type="number" step="0.1" id="weight-input" name="weight_kg" class="form-input" placeholder="0.0" required>
                    </div>
                    <button type="submit" class="save-btn" style="width: auto; padding: 12px 30px;">Log</button>
                </div>
            </form>
        </section>

        <section id="badges" class="card">
            <h2>Achievements</h2>
            <div class="badges-grid" id="badges-list" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 15px; margin-top: 15px;">
                <!-- Loaded via JS -->
            </div>
        </section>

        <section id="equipment" class="card">
            <h2>My Garage</h2>
            <p style="margin-bottom: 15px; color: var(--secondary-text);">Select the equipment you have available.</p>
            <div class="equipment-grid" id="equipment-grid"></div>
            <button class="save-btn" onclick="saveGarage()">Update Equipment</button>
        </section>

        <section id="settings" class="card">
            <h2>Preferences</h2>
            
            <div class="setting-group" style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 1px solid var(--border-color); margin-bottom: 20px;">
                <label style="margin-bottom: 0;">Display Theme</label>
                <div class="theme-toggle">
                    <input type="checkbox" class="checkbox" id="checkbox">
                    <label for="checkbox" class="theme-label">
                        <i class="bx bxs-moon"></i>
                        <i class="bx bxs-sun"></i>
                        <div class="ball"></div>
                    </label>
                </div>
            </div>

            <form id="settings-form" onsubmit="saveSettings(event)">
                <div class="setting-group">
                    <label>Weight Units</label>
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
                            <span>Workout Reminders</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="notif_weekly" checked>
                            <span>Weekly Progress Report</span>
                        </label>
                    </div>
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
