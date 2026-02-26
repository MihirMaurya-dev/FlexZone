<?php
define('FLEXZONE_APP', true);
require_once '../php/config/db_connection.php';
requireLogin();
include '../includes/header.php';
?>

<div class="theme-switch-wrapper">
    <label class="theme-switch" for="checkbox">
        <input type="checkbox" id="checkbox" />
        <div class="slider round"></div>
    </label>
    <span>Dark Mode</span>
</div>

<div class="onboarding-container">
    <div class="progress-bar-container">
        <div class="progress-bar" id="progressBar"></div>
    </div>

    <form id="onboardingForm">
        <!-- Step 1: Basics -->
        <div class="onboarding-step active" id="step1">
            <h1>Let's get started</h1>
            <p>Tell us a bit about yourself</p>
            <div class="form-group">
                <label>Age</label>
                <input type="number" name="age" id="age" placeholder="25" class="form-input" required>
            </div>
            <div class="form-group">
                <label>Gender</label>
                <div class="radio-group">
                    <label class="radio-card">
                        <input type="radio" name="gender" value="male" required>
                        <span class="radio-content">
                            <i class='bx bx-male'></i>
                            <span>Male</span>
                        </span>
                    </label>
                    <label class="radio-card">
                        <input type="radio" name="gender" value="female">
                        <span class="radio-content">
                            <i class='bx bx-female'></i>
                            <span>Female</span>
                        </span>
                    </label>
                </div>
            </div>
            <button type="button" class="btn next-btn" onclick="nextStep(1)">Continue</button>
        </div>

        <!-- Step 2: Body -->
        <div class="onboarding-step" id="step2">
            <h1>Body Stats</h1>
            <p>Your current measurements</p>
            <div class="form-group">
                <label>Height (cm)</label>
                <div class="input-with-icon">
                    <i class='bx bx-ruler'></i>
                    <input type="number" name="height_cm" id="height" placeholder="175" class="form-input" required>
                </div>
            </div>
            <div class="form-group">
                <label>Weight (kg)</label>
                <div class="input-with-icon">
                    <i class='bx bx-body'></i>
                    <input type="number" name="weight_kg" id="weight" placeholder="70" class="form-input" required step="0.1">
                </div>
            </div>
            <div class="btn-group">
                <button type="button" class="btn back-btn" onclick="prevStep(2)">Back</button>
                <button type="button" class="btn next-btn" onclick="nextStep(2)">Continue</button>
            </div>
        </div>

        <!-- Step 3: Goal -->
        <div class="onboarding-step" id="step3">
            <h1>Your Goal</h1>
            <p>What do you want to achieve?</p>
            <div class="radio-group vertical">
                <label class="radio-card">
                    <input type="radio" name="goal" value="lose_weight" required>
                    <span class="radio-content">
                        <span class="title">Lose Weight</span>
                        <span class="desc">Burn fat and get leaner</span>
                    </span>
                </label>
                <label class="radio-card">
                    <input type="radio" name="goal" value="build_muscle">
                    <span class="radio-content">
                        <span class="title">Build Muscle</span>
                        <span class="desc">Gain strength and mass</span>
                    </span>
                </label>
                <label class="radio-card">
                    <input type="radio" name="goal" value="general_fitness">
                    <span class="radio-content">
                        <span class="title">General Fitness</span>
                        <span class="desc">Stay healthy and active</span>
                    </span>
                </label>
            </div>
            <div class="btn-group">
                <button type="button" class="btn back-btn" onclick="prevStep(3)">Back</button>
                <button type="button" class="btn next-btn" onclick="nextStep(3)">Continue</button>
            </div>
        </div>

        <!-- Step 4: Activity -->
        <div class="onboarding-step" id="step4">
            <h1>Activity Level</h1>
            <p>How active are you daily?</p>
            <div class="radio-group vertical">
                <label class="radio-card">
                    <input type="radio" name="activity" value="sedentary" required>
                    <span class="radio-content">
                        <span class="title">Sedentary</span>
                        <span class="desc">Office job, little exercise</span>
                    </span>
                </label>
                <label class="radio-card">
                    <input type="radio" name="activity" value="moderate">
                    <span class="radio-content">
                        <span class="title">Moderate</span>
                        <span class="desc">Exercise 3-4 times/week</span>
                    </span>
                </label>
                <label class="radio-card">
                    <input type="radio" name="activity" value="active">
                    <span class="radio-content">
                        <span class="title">Very Active</span>
                        <span class="desc">Daily heavy exercise</span>
                    </span>
                </label>
            </div>
            <div class="btn-group">
                <button type="button" class="btn back-btn" onclick="prevStep(4)">Back</button>
                <button type="button" class="btn finish-btn" onclick="finishOnboarding()">Finish</button>
            </div>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
