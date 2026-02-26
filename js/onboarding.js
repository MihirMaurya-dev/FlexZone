let currentStep = 1;
const totalSteps = 4;

function nextStep(step) {
    if (!validateStep(step)) return;
    document.getElementById(`step${step}`).classList.remove('active');
    currentStep = step + 1;
    document.getElementById(`step${currentStep}`).classList.add('active');
    updateProgressBar();
}

function prevStep(step) {
    document.getElementById(`step${step}`).classList.remove('active');
    currentStep = step - 1;
    document.getElementById(`step${currentStep}`).classList.add('active');
    updateProgressBar();
}

function updateProgressBar() {
    const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
    document.getElementById('progressBar').style.width = `${progress}%`;
}

function validateStep(step) {
    if (step === 1) {
        const age = document.getElementById('age').value;
        const gender = document.querySelector('input[name="gender"]:checked');
        if (!age || age < 10 || age > 100) {
            window.showMessage('Please enter a valid age (10-100)');
            return false;
        }
        if (!gender) {
            window.showMessage('Please select your gender');
            return false;
        }
    } else if (step === 2) {
        const height = document.getElementById('height').value;
        const weight = document.getElementById('weight').value;
        if (!height || height < 50 || height > 250) {
            window.showMessage('Please enter a valid height (50-250 cm)');
            return false;
        }
        if (!weight || weight < 20 || weight > 300) {
            window.showMessage('Please enter a valid weight (20-300 kg)');
            return false;
        }
    } else if (step === 3) {
        const goal = document.querySelector('input[name="goal"]:checked');
        if (!goal) {
            window.showMessage('Please select your primary fitness goal');
            return false;
        }
    }
    return true;
}

function finishOnboarding() {
    const activity = document.querySelector('input[name="activity"]:checked');
    if (!activity) {
        window.showMessage('Please select your activity level');
        return;
    }
    const form = document.getElementById('onboardingForm');
    const formData = new FormData(form);
    const finishBtn = document.querySelector('.finish-btn');
    window.handleFormSubmit(null, '../php/api/user/update_profile.php', () => {
        window.location.href = 'home.php';
    }, {
        formData,
        submitBtn: finishBtn,
        loadingText: 'Saving...'
    });
}
