document.addEventListener('DOMContentLoaded', function() {
    const customWorkoutForm = document.getElementById('custom-workout-form');
    if (customWorkoutForm) {
        customWorkoutForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const muscleGroup = document.getElementById('muscle_group').value;
            const duration = document.getElementById('duration').value;
            if (duration < 5 || duration > 120) {
                window.showMessage('Duration must be between 5 and 120 minutes.');
                return;
            }
            const equipmentCheckboxes = document.querySelectorAll('input[name="equipment"]:checked');
            const selectedEquipment = Array.from(equipmentCheckboxes).map(cb => cb.value).join(',');
            let url = `workout_preview.php?type=custom&muscle=${encodeURIComponent(muscleGroup)}&duration=${encodeURIComponent(duration)}`;
            if (selectedEquipment) {
                url += `&equipment=${encodeURIComponent(selectedEquipment)}`;
            }
            window.location.href = url;
        });
    }
});