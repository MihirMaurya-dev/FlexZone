<?php
define('FLEXZONE_APP', true);
require_once '../php/config/db_connection.php';
requireLogin();
$activePage = 'custom_workout';
include '../includes/header.php';
?>

<main class="dashboard">
    <div class="main-card">
        <h1 style="color: var(--primary-color); margin-bottom: 10px;">Custom Workout</h1>
        <p style="color: var(--secondary-text); margin-bottom: 30px;">Tailor your session to your goals and gear.</p>

        <form id="custom-workout-form">
            <div class="form-group">
                <label for="muscle_group">Target Muscle Group</label>
                <select id="muscle_group" name="muscle_group" class="form-input">
                    <option value="Full Body">Full Body</option>
                    <option value="Chest">Chest</option>
                    <option value="Back">Back</option>
                    <option value="Legs">Legs</option>
                    <option value="Shoulders">Shoulders</option>
                    <option value="Arms">Arms</option>
                    <option value="Core">Core</option>
                </select>
            </div>

            <div class="form-group">
                <label for="duration">Target Duration (minutes)</label>
                <input type="number" id="duration" name="duration" value="30" min="5" max="120" class="form-input">
            </div>

            <div class="form-group">
                <label>Available Equipment</label>
                <div class="checkbox-group-vertical" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
                    <label class="checkbox-item"><input type="checkbox" name="equipment" value="Dumbbell"> <span>Dumbbells</span></label>
                    <label class="checkbox-item"><input type="checkbox" name="equipment" value="Barbell"> <span>Barbell</span></label>
                    <label class="checkbox-item"><input type="checkbox" name="equipment" value="Kettlebell"> <span>Kettlebell</span></label>
                    <label class="checkbox-item"><input type="checkbox" name="equipment" value="Resistance Band"> <span>Bands</span></label>
                    <label class="checkbox-item"><input type="checkbox" name="equipment" value="Bench"> <span>Bench</span></label>
                    <label class="checkbox-item"><input type="checkbox" name="equipment" value="Pull-up Bar"> <span>Pull-up Bar</span></label>
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
