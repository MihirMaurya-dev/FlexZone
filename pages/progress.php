<?php
define('FLEXZONE_APP', true);
require_once '../php/config/db_connection.php';
requireLogin();
include '../includes/header.php';
?>
    <style>
        .progress-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 768px) { .progress-grid { grid-template-columns: 1fr; } }
        .upload-box {
            border: 2px dashed var(--border-color);
            padding: 20px;
            text-align: center;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .upload-box:hover { border-color: var(--primary-color); background: rgba(59, 130, 246, 0.05); }
        .timeline-item {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 16px;
            box-shadow: var(--shadow);
        }
        .timeline-photos {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            margin-top: 10px;
        }
        .timeline-photos img {
            width: 80px; height: 80px; object-fit: cover; border-radius: 8px;
        }
    </style>
    <main class="dashboard">
        <h1 style="margin-bottom: 20px;">Track Progress</h1>
        <div class="progress-grid">
            <!-- Log Form -->
            <div class="main-card">
                <h2>Log Measurements</h2>
                <form id="progress-form">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Date</label>
                        <input type="date" name="log_date" required class="form-input" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--body-bg); color: var(--text-color);">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div class="form-group">
                            <label>Weight (kg)</label>
                            <input type="number" step="0.1" name="weight_kg" class="form-input" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--body-bg); color: var(--text-color);">
                        </div>
                        <div class="form-group">
                            <label>Chest (cm)</label>
                            <input type="number" step="0.1" name="chest_cm" class="form-input" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--body-bg); color: var(--text-color);">
                        </div>
                        <div class="form-group">
                            <label>Waist (cm)</label>
                            <input type="number" step="0.1" name="waist_cm" class="form-input" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--body-bg); color: var(--text-color);">
                        </div>
                        <div class="form-group">
                            <label>Arms (cm)</label>
                            <input type="number" step="0.1" name="arms_cm" class="form-input" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--body-bg); color: var(--text-color);">
                        </div>
                        <div class="form-group">
                            <label>Thighs (cm)</label>
                            <input type="number" step="0.1" name="thighs_cm" class="form-input" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--body-bg); color: var(--text-color);">
                        </div>
                    </div>
                    <h3 style="margin: 15px 0 10px;">Progress Photos</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
                        <div class="upload-box" onclick="document.getElementById('photo_front').click()">
                            <i class='bx bx-camera'></i><br><small>Front</small>
                            <input type="file" name="photo_front" id="photo_front" accept="image/*" style="display: none;">
                        </div>
                        <div class="upload-box" onclick="document.getElementById('photo_side').click()">
                            <i class='bx bx-camera'></i><br><small>Side</small>
                            <input type="file" name="photo_side" id="photo_side" accept="image/*" style="display: none;">
                        </div>
                        <div class="upload-box" onclick="document.getElementById('photo_back').click()">
                            <i class='bx bx-camera'></i><br><small>Back</small>
                            <input type="file" name="photo_back" id="photo_back" accept="image/*" style="display: none;">
                        </div>
                    </div>
                    <button type="submit" class="start-btn" style="position: static; margin-top: 20px;">Save Entry</button>
                </form>
            </div>
            <!-- History Timeline -->
            <div class="main-card">
                <h2>History</h2>
                <div id="progress-timeline" style="margin-top: 15px; max-height: 600px; overflow-y: auto;">
                    <p style="text-align: center; color: var(--secondary-text);">Loading history...</p>
                </div>
            </div>
        </div>
    </main>
<?php
include '../includes/navbar.php';
include '../includes/footer.php';
?>