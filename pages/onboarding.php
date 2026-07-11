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

    <form id="onboardingForm" onsubmit="event.preventDefault();">
        <!-- Step 1: Basics -->
        <div class="step active" id="step1">
            <h1>Let's get started</h1>
            <p class="subtitle">Tell us a bit about yourself</p>
            <div class="form-group">
                <label>Age</label>
                <div class="lens-slider-wrapper">
                    <div class="lens-value"><span id="ageDisplay">25</span><small> yrs</small></div>
                    <div class="lens-ruler" id="ageRuler">
                        <div class="lens-ticks" id="ageTicks"></div>
                    </div>
                    <div class="lens-pointer"></div>
                    <input type="hidden" name="age" id="age" value="25" required>
                </div>
            </div>
            <div class="form-group">
                <label>Gender</label>
                <div class="radio-group">
                    <label class="radio-card">
                        <input type="radio" name="gender" value="male" required>
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
            <button type="button" class="btn next-btn" onclick="nextStep(1)">Continue</button>
        </div>

        <!-- Step 2: Body -->
        <div class="step" id="step2">
            <h1>Body Stats</h1>
            <p class="subtitle">Your current measurements</p>
            <div class="form-group">
                <label>Height</label>
                <div class="lens-slider-wrapper">
                    <div class="lens-value"><span id="heightDisplay">175</span><small> cm</small></div>
                    <div class="lens-ruler" id="heightRuler">
                        <div class="lens-ticks" id="heightTicks"></div>
                    </div>
                    <div class="lens-pointer"></div>
                    <input type="hidden" name="height_cm" id="height" value="175" required>
                </div>
            </div>
            <div class="form-group">
                <label>Weight</label>
                <div class="lens-slider-wrapper">
                    <div class="lens-value"><span id="weightDisplay">70</span><small> kg</small></div>
                    <div class="lens-ruler" id="weightRuler">
                        <div class="lens-ticks" id="weightTicks"></div>
                    </div>
                    <div class="lens-pointer"></div>
                    <input type="hidden" name="weight_kg" id="weight" value="70" required>
                </div>
            </div>
            
            <div id="bmi-display" style="display: none; margin-bottom: 20px; padding: 12px; border-radius: 8px; text-align: center; font-weight: 500;">
                <!-- BMI will be injected here -->
            </div>
            <div class="step-buttons">
                <button type="button" class="btn prev-btn" onclick="prevStep(2)">Back</button>
                <button type="button" class="btn next-btn" onclick="nextStep(2)">Continue</button>
            </div>
        </div>

        <!-- Step 3: Goal -->
        <div class="step" id="step3">
            <h1>Your Goal</h1>
            <p class="subtitle">What do you want to achieve?</p>
            <div class="radio-group vertical">
                <label class="radio-card">
                    <input type="radio" name="goal" value="lose_weight" required>
                    <span class="card-content">
                        <span class="title">Lose Weight</span>
                        <span class="desc">Burn fat and get leaner</span>
                    </span>
                </label>
                <label class="radio-card">
                    <input type="radio" name="goal" value="build_muscle">
                    <span class="card-content">
                        <span class="title">Build Muscle</span>
                        <span class="desc">Gain strength and mass</span>
                    </span>
                </label>
                <label class="radio-card">
                    <input type="radio" name="goal" value="general_fitness">
                    <span class="card-content">
                        <span class="title">General Fitness</span>
                        <span class="desc">Stay healthy and active</span>
                    </span>
                </label>
            </div>
            <div class="step-buttons">
                <button type="button" class="btn prev-btn" onclick="prevStep(3)">Back</button>
                <button type="button" class="btn next-btn" onclick="nextStep(3)">Continue</button>
            </div>
        </div>

        <!-- Step 4: Activity -->
        <div class="step" id="step4">
            <h1>Activity Level</h1>
            <p class="subtitle">How active are you daily?</p>
            <div class="radio-group vertical">
                <label class="radio-card">
                    <input type="radio" name="activity" value="sedentary" required>
                    <span class="card-content">
                        <span class="title">Sedentary</span>
                        <span class="desc">Little or no exercise</span>
                    </span>
                </label>
                <label class="radio-card">
                    <input type="radio" name="activity" value="light">
                    <span class="card-content">
                        <span class="title">Lightly Active</span>
                        <span class="desc">Light exercise 1-3 days/week</span>
                    </span>
                </label>
                <label class="radio-card">
                    <input type="radio" name="activity" value="moderate">
                    <span class="card-content">
                        <span class="title">Moderately Active</span>
                        <span class="desc">Moderate exercise 3-5 days/week</span>
                    </span>
                </label>
                <label class="radio-card">
                    <input type="radio" name="activity" value="active">
                    <span class="card-content">
                        <span class="title">Very Active</span>
                        <span class="desc">Hard exercise 6-7 days/week</span>
                    </span>
                </label>
                <label class="radio-card">
                    <input type="radio" name="activity" value="extra_active">
                    <span class="card-content">
                        <span class="title">Extra Active</span>
                        <span class="desc">Physical job or 2x daily training</span>
                    </span>
                </label>
            </div>
            <div class="step-buttons">
                <button type="button" class="btn prev-btn" onclick="prevStep(4)">Back</button>
                <button type="button" class="btn finish-btn" onclick="finishOnboarding()">Finish</button>
            </div>
        </div>
    </form>
</div>

<script>
function initLensSlider(rulerId, ticksId, displayId, inputId, min, max, startVal) {
    const ruler = document.getElementById(rulerId);
    const ticksContainer = document.getElementById(ticksId);
    const display = document.getElementById(displayId);
    const input = document.getElementById(inputId);
    const tickWidth = 20; 
    
    let html = '';
    for(let i = min; i <= max; i++) {
        let isLong = (i % 5 === 0);
        html += `<div class="lens-tick ${isLong ? 'long' : ''}">
                    ${isLong ? `<span class="lens-tick-label">${i}</span>` : ''}
                 </div>`;
    }
    ticksContainer.innerHTML = html;
    
    setTimeout(() => {
        ruler.scrollLeft = (startVal - min) * tickWidth;
    }, 50);
    
    ruler.addEventListener('scroll', () => {
        let index = Math.round(ruler.scrollLeft / tickWidth);
        let val = min + index;
        if(val > max) val = max;
        if(val < min) val = min;
        display.textContent = val;
        input.value = val;
        
        // Custom event so other scripts (like BMI calc) know it changed
        input.dispatchEvent(new Event('input', { bubbles: true }));
    });
    
    // Convert vertical mouse wheel scroll to horizontal scroll
    ruler.addEventListener('wheel', (e) => {
        e.preventDefault();
        ruler.scrollLeft += e.deltaY;
    }, { passive: false });
}

document.addEventListener('DOMContentLoaded', () => {
    initLensSlider('ageRuler', 'ageTicks', 'ageDisplay', 'age', 14, 100, 25);
    initLensSlider('heightRuler', 'heightTicks', 'heightDisplay', 'height', 120, 250, 175);
    initLensSlider('weightRuler', 'weightTicks', 'weightDisplay', 'weight', 30, 200, 70);
});
</script>

<?php include '../includes/footer.php'; ?>
