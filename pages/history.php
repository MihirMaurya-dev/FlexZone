<?php
define('FLEXZONE_APP', true);
require_once '../php/config/db_connection.php';
requireLogin();
$activePage = 'history';
include '../includes/header.php';
?>

<main class="dashboard">
    <div class="main-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 15px;">
            <h2 style="margin-bottom: 0;">Workout History</h2>
            <a href="../php/api/user/export_history.php" class="ghost-btn" style="text-decoration: none; font-size: 1.2rem; padding: 6px 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;" title="Export History">
                <i class='bx bx-export'></i>
            </a>
        </div>

        <!-- Heatmap Container -->
        <div class="heatmap-container" style="margin-bottom: 30px; overflow-x: auto; padding-bottom: 10px;">
            <div id="calendar-heatmap" class="calendar-heatmap">
                <div style="text-align: center; padding: 20px;"><div class="spinner"></div></div>
            </div>
            <div class="heatmap-legend" style="display: flex; justify-content: flex-end; align-items: center; gap: 5px; font-size: 0.8rem; color: var(--secondary-text); margin-top: 10px;">
                <span>Less</span>
                <div class="heatmap-cell" style="background: var(--surface-color);"></div>
                <div class="heatmap-cell" style="background: var(--primary-color); opacity: 0.25;"></div>
                <div class="heatmap-cell" style="background: var(--primary-color); opacity: 0.5;"></div>
                <div class="heatmap-cell" style="background: var(--primary-color); opacity: 0.75;"></div>
                <div class="heatmap-cell" style="background: var(--primary-color); opacity: 1;"></div>
                <span>More</span>
            </div>
        </div>

        <!-- Filters -->
        <div class="history-filters" style="display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap;">
            <div class="form-group" style="margin: 0; flex: 1; min-width: 150px;">
                <input type="date" id="filter-date" class="form-input" style="padding: 8px 12px; height: auto;">
            </div>
            <div class="form-group" style="margin: 0; flex: 1; min-width: 150px;">
                <select id="filter-type" class="form-input" style="padding: 8px 12px; height: auto;">
                    <option value="">All Workouts</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                    <option value="custom">Custom</option>
                </select>
            </div>
            <button id="clear-filters" class="ghost-btn" style="padding: 8px 15px; height: auto;">Clear</button>
        </div>
        
        <div class="table-container">
            <table id="history-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Workout</th>
                        <th>Duration (min)</th>
                        <th>Kcal</th>
                    </tr>
                </thead>
                <tbody id="history-body">
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 30px;">
                            <div class="spinner"></div> Loading history...
                        </td>
                    </tr>
                </tbody>
                <tfoot id="history-foot"></tfoot>
            </table>
        </div>
        
        <div class="pagination-controls" id="pagination-controls" style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
            <button id="prev-page" class="ghost-btn" disabled>Previous</button>
            <span id="page-info" style="color: var(--secondary-text); font-size: 0.9rem;">Page 1</span>
            <button id="next-page" class="ghost-btn" disabled>Next</button>
        </div>
    </div>
</main>

<?php
include '../includes/navbar.php';
include '../includes/footer.php';
?>
