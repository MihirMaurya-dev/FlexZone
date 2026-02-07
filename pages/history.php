<?php
define('FLEXZONE_APP', true);
require_once '../php/config/db_connection.php';
requireLogin();
include '../includes/header.php';
?>
    <main class="dashboard">
        <div class="main-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin-bottom: 0;">Workout History</h2>
                <a href="../php/api/user/export_history.php" class="ghost-btn" style="text-decoration: none; font-size: 0.9rem; padding: 6px 12px;">
                    <i class='bx bx-export'></i> Export
                </a>
            </div>
            <div class="table-container">
                <table id="history-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Workout</th>
                            <th>Duration</th>
                            <th>Kcal</th>
                        </tr>
                    </thead>
                    <tbody id="history-body">
                        <tr><td colspan="4">Loading history...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
<?php
include '../includes/navbar.php';
include '../includes/footer.php';
?>