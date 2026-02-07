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
    </div>
    <div class="onboarding-container">
        <header class="onboarding-header">
            <h1>Let's Get to Know You</h1>
            <p>Help us customize your FlexZone experience.</p>
        </header>
        <div class="progress-bar-container">
            <div class="progress-bar" id="progressBar"></div>
        </div>
        <form id="onboardingForm" onsubmit="return false;">
            <!-- Step 1: Personal Info -->
            <div class="step active" id="step1">
                <h2>Personal Details</h2>
                <div class="form-group">
                    <label for="age">Age</label>
                    <div class="input-with-icon">
                        <i class='bx bx-calendar'></i>
                        <input type="number" id="age" name="age" placeholder="e.g. 25" min="10" max="100">
                    </div>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <div class="radio-group">
                        <label class="radio-card">
                            <input type="radio" name="gender" value="male">
                            <span class="card-content">
                                <i class='bx bx-male'></i>
                                <span>Male</span>
                            </span>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="gender" value="female">
                            <span class="card-content">
                                <i class='bx bx-female'></i>
                                <span>Female</span>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="step-buttons">
                    <button class="btn next-btn" onclick="nextStep(1)">Next</button>
                </div>
            </div>
            <!-- Step 2: Body Stats -->
            <div class="step" id="step2">
                <h2>Body Stats</h2>
                <div class="form-group">
                    <label for="height">Height (cm)</label>
                    <div class="input-with-icon">
                        <i class='bx bx-ruler'></i>
                        <input type="number" id="height" name="height" placeholder="e.g. 175">
                    </div>
                </div>
                <div class="form-group">
                    <label for="weight">Current Weight (kg)</label>
                    <div class="input-with-icon">
                        <i class='bx bx-body'></i>
                        <input type="number" id="weight" name="weight" placeholder="e.g. 70" step="0.1">
                    </div>
                </div>
                <div class="step-buttons">
                    <button class="btn prev-btn" onclick="prevStep(2)">Back</button>
                    <button class="btn next-btn" onclick="nextStep(2)">Next</button>
                </div>
            </div>
            <!-- Step 3: Goals -->
            <div class="step" id="step3">
                <h2>Your Goal</h2>
                <div class="form-group">
                    <div class="radio-group vertical">
                        <label class="radio-card">
                            <input type="radio" name="goal" value="lose_weight">
                            <span class="card-content">
                                <i class='bx bx-trending-down'></i>
                                <span>Lose Weight</span>
                            </span>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="goal" value="maintain">
                            <span class="card-content">
                                <i class='bx bx-minus'></i>
                                <span>Maintain Weight</span>
                            </span>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="goal" value="build_muscle">
                            <span class="card-content">
                                <i class='bx bx-dumbbell'></i>
                                <span>Build Muscle</span>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="step-buttons">
                    <button class="btn prev-btn" onclick="prevStep(3)">Back</button>
                    <button class="btn next-btn" onclick="nextStep(3)">Next</button>
                </div>
            </div>
            <!-- Step 4: Activity Level -->
            <div class="step" id="step4">
                <h2>Activity Level</h2>
                <p class="subtitle">How active are you on a daily basis?</p>
                <div class="form-group">
                    <div class="radio-group vertical">
                        <label class="radio-card">
                            <input type="radio" name="activity" value="sedentary">
                            <span class="card-content">
                                <i class='bx bx-chair'></i>
                                <span>Sedentary (Office job)</span>
                            </span>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="activity" value="light">
                            <span class="card-content">
                                <i class='bx bx-walk'></i>
                                <span>Lightly Active (1-3 days/week)</span>
                            </span>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="activity" value="moderate">
                            <span class="card-content">
                                <i class='bx bx-run'></i>
                                <span>Moderately Active (3-5 days/week)</span>
                            </span>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="activity" value="active">
                            <span class="card-content">
                                <i class='bx bx-cycling'></i>
                                <span>Very Active (6-7 days/week)</span>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="step-buttons">
                    <button class="btn prev-btn" onclick="prevStep(4)">Back</button>
                    <button class="btn finish-btn" onclick="finishOnboarding()">Finish</button>
                </div>
            </div>
        </form>
    </div>
<?php
include '../includes/footer.php';
?>