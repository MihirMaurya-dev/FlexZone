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

document.addEventListener('DOMContentLoaded', () => {
    const heightInput = document.getElementById('height');
    const weightInput = document.getElementById('weight');
    const bmiDisplay = document.getElementById('bmi-display');

    function calculateBMI() {
        const height = parseFloat(heightInput.value);
        const weight = parseFloat(weightInput.value);

        if (height > 0 && weight > 0) {
            const heightInMeters = height / 100;
            const bmi = weight / (heightInMeters * heightInMeters);
            let category = '';
            let color = '';

            if (bmi < 18.5) {
                category = 'Underweight';
                color = '#F59E0B'; // yellow
            } else if (bmi >= 18.5 && bmi < 24.9) {
                category = 'Normal weight';
                color = '#10B981'; // green
            } else if (bmi >= 25 && bmi < 29.9) {
                category = 'Overweight';
                color = '#F59E0B'; // yellow
            } else {
                category = 'Obese';
                color = '#EF4444'; // red
            }

            bmiDisplay.style.display = 'block';
            bmiDisplay.style.backgroundColor = color + '20'; // 20% opacity
            bmiDisplay.style.color = color;
            bmiDisplay.style.border = `1px solid ${color}50`;
            bmiDisplay.innerHTML = `Your BMI: <strong>${bmi.toFixed(1)}</strong> (${category})`;
        } else {
            bmiDisplay.style.display = 'none';
        }
    }

    if (heightInput && weightInput) {
        heightInput.addEventListener('input', calculateBMI);
        weightInput.addEventListener('input', calculateBMI);
    }
});
