let currentStep = 1;
const totalSteps = 4;
document.addEventListener('DOMContentLoaded', () => {
    updateProgressBar();
});
function updateProgressBar() {
    const progressBar = document.getElementById('progressBar');
    const percentage = (currentStep / totalSteps) * 100;
    progressBar.style.width = percentage + '%';
}
function showStep(step) {
    document.querySelectorAll('.step').forEach(el => {
        el.classList.remove('active');
    });
    const currentStepEl = document.getElementById(`step${step}`);
    if (currentStepEl) {
        currentStepEl.classList.add('active');
    }
    updateProgressBar();
}
function nextStep(step) {
    if (validateStep(step)) {
        if (currentStep < totalSteps) {
            currentStep++;
            showStep(currentStep);
        }
    }
}
function prevStep(step) {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
    }
}
function validateStep(step) {
    let isValid = true;
    const stepEl = document.getElementById(`step${step}`);
    stepEl.querySelectorAll('input').forEach(input => {
        input.style.borderColor = 'var(--border-color)';
    });
    if (step === 1) {
        const age = document.getElementById('age').value;
        const gender = document.querySelector('input[name="gender"]:checked');
        if (!age || age < 10 || age > 100) {
            window.showMessage('Please enter a valid age between 10 and 100.');
            document.getElementById('age').style.borderColor = 'red';
            isValid = false;
        } else if (!gender) {
            window.showMessage('Please select a gender.');
            isValid = false;
        }
    } else if (step === 2) {
        const height = document.getElementById('height').value;
        const weight = document.getElementById('weight').value;
        if (!height || height < 50 || height > 300) {
             window.showMessage('Please enter a valid height (50-300 cm).');
             document.getElementById('height').style.borderColor = 'red';
             isValid = false;
        }
        if (!weight || weight < 20 || weight > 500) {
             window.showMessage('Please enter a valid weight (20-500 kg).');
             document.getElementById('weight').style.borderColor = 'red';
             isValid = false;
        }
    } else if (step === 3) {
        const goal = document.querySelector('input[name="goal"]:checked');
        if (!goal) {
            window.showMessage('Please select a goal.');
            isValid = false;
        }
    }
    return isValid;
}
function finishOnboarding() {
    const activity = document.querySelector('input[name="activity"]:checked');
    if (!activity) {
        window.showMessage('Please select an activity level.');
        return;
    }
    const age = document.getElementById('age').value;
    const gender = document.querySelector('input[name="gender"]:checked').value;
    const height = document.getElementById('height').value;
    const weight = document.getElementById('weight').value;
    const goalRaw = document.querySelector('input[name="goal"]:checked').value;
    const activityLevel = document.querySelector('input[name="activity"]:checked').value;
    let dbGoal = 'general_fitness';
    if (goalRaw === 'lose_weight') dbGoal = 'weight_loss';
    if (goalRaw === 'build_muscle') dbGoal = 'muscle_gain';
    const currentYear = new Date().getFullYear();
    const birthYear = currentYear - age;
    const dob = `${birthYear}-01-01`;
    const formData = new FormData();
    formData.append('height_cm', height);
    formData.append('weight_kg', weight);
    formData.append('dob', dob);
    formData.append('fitness_goal', dbGoal);
    formData.append('gender', gender);
    formData.append('activity_level', activityLevel);
    const userProfile = {
        age,
        gender,
        height,
        weight,
        goal: goalRaw,
        activity: activityLevel
    };
    localStorage.setItem('userProfile', JSON.stringify(userProfile));
    fetch('../php/api/user/update_profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.href = 'home.php';
        } else {
            console.error('Update failed:', data.message);
            window.showMessage('Something went wrong saving your profile. Redirecting anyway...');
            setTimeout(() => { window.location.href = 'home.php'; }, 2000);
        }
    })
    .catch(err => {
        console.error('Error:', err);
        window.showMessage('Network error. Redirecting to dashboard...');
        setTimeout(() => { window.location.href = 'home.php'; }, 2000);
    });
}