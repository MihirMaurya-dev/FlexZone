<?php
define('FLEXZONE_APP', true);
require_once '../php/config/db_connection.php';
requireLogin();
include '../includes/header.php';
?>
    <main class="dashboard">
        <div class="main-card">
            <div style="width: 100%; display: flex; justify-content: flex-start; margin-bottom: 20px;">
                 <a href="home.php" class="ghost-btn"><i class='bx bx-arrow-back'></i> Back</a>
            </div>
            <h1 style="margin-bottom: 20px;">Build Your Custom Workout</h1>
            <form id="custom-workout-form">
                <div class="form-group">
                    <label for="muscle_group">Choose a Muscle Group</label>
                    <select id="muscle_group" name="muscle_group">
                        <option value="Full Body">Full Body</option>
                        <option value="Legs">Legs</option>
                        <option value="Chest">Chest</option>
                        <option value="Core">Core</option>
                        <option value="Back">Back</option>
                        <option value="Shoulders">Shoulders</option>
                        <option value="Arms">Arms (Biceps & Triceps)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="duration">Workout Duration (minutes)</label>
                    <select id="duration" name="duration">
                        <option value="15">15 Minutes (Short)</option>
                        <option value="30" selected>30 Minutes (Standard)</option>
                        <option value="45">45 Minutes (Long)</option>
                         <option value="60">60 Minutes (Extra Long)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Available Equipment</label>
                    <p style="font-size: 0.9em; color: var(--secondary-text); margin-bottom: 10px;">(Bodyweight exercises are always included)</p>
                    <div class="checkbox-group" style="flex-wrap: wrap;">
                        <label><input type="checkbox" name="equipment" value="Dumbbells"> Dumbbells</label>
                        <label><input type="checkbox" name="equipment" value="Chair"> Chair</label>
                        <label><input type="checkbox" name="equipment" value="Pull-up Bar"> Pull-up Bar</label>
                    </div>
                </div>
                <button type="submit" class="start-btn" style="position: static; width: 100%; margin-top: 20px;">Generate & Preview</button>
            </form>
        </div>
    </main>
<?php
include '../includes/navbar.php';
include '../includes/footer.php';
?>