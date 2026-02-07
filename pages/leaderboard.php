<?php
define('FLEXZONE_APP', true);
require_once '../php/config/db_connection.php';
requireLogin();
include '../includes/header.php';
?>
    <main class="dashboard">
        <div class="main-card">
            <h2>Leaderboard</h2>
            <p style="margin-bottom: 20px;">Top users by total workout time.</p>
            <div class="table-container">
                <table id="leaderboard-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>User</th>
                            <th>Time (min)</th>
                            <th>Workouts</th>
                            <th>Kcal</th>
                        </tr>
                    </thead>
                    <tbody id="leaderboard-body">
                        <tr><td colspan="5">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
            <div id="user-rank-info" style="margin-top: 20px; text-align: center; color: var(--secondary-text); font-size: 0.9rem;">
            </div>
        </div>
    </main>
<?php
include '../includes/navbar.php';
include '../includes/footer.php';
?>