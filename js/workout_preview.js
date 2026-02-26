document.addEventListener('DOMContentLoaded', function() {
    const titleEl = document.getElementById('workout-title');
    const detailsEl = document.getElementById('workout-details');
    const listEl = document.getElementById('exercise-list');
    const startBtn = document.getElementById('start-workout-btn');
    const urlParams = new URLSearchParams(window.location.search);
    const workoutType = urlParams.get('type');
    const muscleGroup = urlParams.get('muscle');
    const duration = urlParams.get('duration');
    const equipment = urlParams.get('equipment');
    if (!workoutType) {
        titleEl.textContent = "Error";
        detailsEl.textContent = "No workout type specified.";
        startBtn.disabled = true;
        return;
    }
    let fetchUrl = `../php/api/workouts/generate_workout.php?type=${encodeURIComponent(workoutType)}`;
    let workoutPlayerUrl = `workout.php?type=${encodeURIComponent(workoutType)}`;
    if (workoutType === 'custom') {
        if (muscleGroup) {
            fetchUrl += `&muscle=${encodeURIComponent(muscleGroup)}`;
            workoutPlayerUrl += `&muscle=${encodeURIComponent(muscleGroup)}`;
        }
        if (duration) {
            fetchUrl += `&duration=${encodeURIComponent(duration)}`;
            workoutPlayerUrl += `&duration=${encodeURIComponent(duration)}`;
        }
        if (equipment) {
            fetchUrl += `&equipment=${encodeURIComponent(equipment)}`;
            workoutPlayerUrl += `&equipment=${encodeURIComponent(equipment)}`;
        }
    }
    const capitalize = (s) => s.charAt(0).toUpperCase() + s.slice(1);
    const displayType = workoutType === 'custom' ? (muscleGroup || 'Custom') : workoutType;
    titleEl.textContent = `${capitalize(displayType)} Workout`.replace(/_/g, ' ');
    listEl.innerHTML = '<div class="loading-spinner"><div class="spinner"></div><p>Generating your workout...</p></div>';
    window.apiFetch(fetchUrl).then(data => {
        if (data && data.status === 'success' && data.workout?.length > 0) {
            const exercises = data.workout;
            const totalDurationSecs = exercises.reduce((sum, ex) => {
                const exerciseTime = ex.duration_seconds || (ex.reps || 10) * 4;
                return sum + (ex.sets || 1) * exerciseTime + ((ex.sets > 1 ? ex.sets - 1 : 0) * (ex.rest_seconds || 0));
            }, 0);
            detailsEl.textContent = `Approx. ${Math.round(totalDurationSecs / 60)} minutes • ${exercises.length} exercises`;
            listEl.innerHTML = '';
            exercises.forEach(exercise => {
                const exerciseCard = document.createElement('div');
                exerciseCard.className = 'exercise-item';
                const durationOrReps = exercise.duration_seconds ? `${exercise.duration_seconds} sec` : `${exercise.reps || 10} reps`;
                const visualSrc = window.getAvatarPath(exercise.image_url).replace('default_avatar.png', 'exercises/placeholder.png');
                const isVideo = visualSrc.endsWith('.mp4') || visualSrc.endsWith('.webm');
                exerciseCard.innerHTML = isVideo ? ` <video src="${visualSrc}" class="exercise-visual-thumb" autoplay loop muted playsinline onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"></video><img src="../assets/exercises/placeholder.png" class="exercise-visual-thumb" style="display:none;"> ` : ` <img src="${visualSrc}" class="exercise-visual-thumb" onerror="this.src='../assets/exercises/placeholder.png';"> `;
                exerciseCard.innerHTML += ` <div class="exercise-info"> <h3>${exercise.exercise_name}</h3> <p>${durationOrReps}${exercise.equipment && exercise.equipment !== 'None' ? ` (${exercise.equipment})` : ''}</p> </div> `;
                listEl.appendChild(exerciseCard);
            });
            startBtn.disabled = false;
            startBtn.onclick = () => window.location.href = workoutPlayerUrl;
        } else {
            titleEl.textContent = "No Workout Found";
            detailsEl.textContent = data.message || "Could not generate a workout. Try adjusting filters.";
            listEl.innerHTML = `<div style="text-align: center; padding: 40px; color: var(--secondary-text);"><i class='bx bx-error-circle' style="font-size: 3rem; margin-bottom: 16px; color: var(--accent-color);"></i><p>Check selection or add exercises.</p><button onclick="window.location.href='home.php'" class="btn btn-primary">Go Back Home</button></div>`;
            startBtn.disabled = true;
        }
    }).catch(error => {
        titleEl.textContent = "Connection Error";
        detailsEl.textContent = "Failed to load workout plan. Please try again.";
        listEl.innerHTML = `<div style="text-align: center; padding: 40px;"><i class='bx bx-wifi-off' style="font-size: 3rem; margin-bottom: 16px; color: #EF4444;"></i><p>Unable to connect</p><button onclick="window.location.reload()" class="btn btn-primary"><i class='bx bx-refresh'></i> Retry</button></div>`;
        startBtn.disabled = true;
    });
});
