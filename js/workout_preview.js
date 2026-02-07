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
    fetch(fetchUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data && data.status === 'success' && data.workout && data.workout.length > 0) {
                const exercises = data.workout;
                const totalDurationSecs = exercises.reduce((sum, ex) => {
                    const sets = ex.sets || 1;
                    const rest = ex.rest_seconds || 0;
                    let exerciseTime = 0;
                    if (ex.duration_seconds) {
                        exerciseTime = ex.duration_seconds;
                    } else {
                        exerciseTime = (ex.reps || 10) * 4;
                    }
                    const totalForThisEx = (sets * exerciseTime) + ((sets > 1 ? sets - 1 : 0) * rest);
                    return sum + totalForThisEx;
                }, 0);
                const approxMinutes = Math.round(totalDurationSecs / 60);
                detailsEl.textContent = `Approx. ${approxMinutes} minutes • ${exercises.length} exercises`;
                listEl.innerHTML = '';
                exercises.forEach(exercise => {
                    const exerciseCard = document.createElement('div');
                    exerciseCard.className = 'exercise-item';
                    const durationOrReps = exercise.duration_seconds
                        ? `${exercise.duration_seconds} sec`
                        : `${exercise.reps || 10} reps`;
                    let visualSrc = exercise.image_url ? exercise.image_url : '../assets/exercises/placeholder.png';
                    if (visualSrc !== '../assets/exercises/placeholder.png' && !visualSrc.startsWith('../') && !visualSrc.startsWith('http')) {
                         visualSrc = '../' + visualSrc;
                    }
                    const isVideo = visualSrc.endsWith('.mp4') || visualSrc.endsWith('.webm');
                    const visualElement = isVideo ? 
                        `<video src="${visualSrc}" class="exercise-visual-thumb" 
                                autoplay loop muted playsinline 
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                         </video>
                         <img src="../assets/exercises/placeholder.png" alt="${exercise.exercise_name}" 
                              class="exercise-visual-thumb" style="display:none;">` 
                        : 
                        `<img src="${visualSrc}" alt="${exercise.exercise_name}" 
                              class="exercise-visual-thumb"
                              onerror="this.src='../assets/exercises/placeholder.png';">`;
                    exerciseCard.innerHTML = `
                        ${visualElement}
                        <div class="exercise-info">
                            <h3>${exercise.exercise_name}</h3>
                            <p>${durationOrReps}${exercise.equipment && exercise.equipment !== 'None' ? ` (${exercise.equipment})` : ''}</p>
                        </div>
                    `;
                    listEl.appendChild(exerciseCard);
                });
                startBtn.disabled = false;
                startBtn.addEventListener('click', () => {
                    window.location.href = workoutPlayerUrl;
                });
            } else {
                titleEl.textContent = "No Workout Found";
                detailsEl.textContent = data.message || "Could not generate a workout for these criteria. Try adjusting filters.";
                listEl.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: var(--secondary-text);">
                        <i class='bx bx-error-circle' style="font-size: 3rem; margin-bottom: 16px; color: var(--accent-color);"></i>
                        <p>Please check your selections or add more exercises to the database.</p>
                        <button onclick="window.location.href='home.php'" style="margin-top: 20px; padding: 12px 24px; background: var(--primary-color); color: white; border: none; border-radius: 12px; cursor: pointer; font-weight: 600;">
                            Go Back Home
                        </button>
                    </div>
                `;
                startBtn.disabled = true;
            }
        })
        .catch(error => {
            console.error("Error fetching workout preview:", error);
            titleEl.textContent = "Connection Error";
            detailsEl.textContent = "Failed to load workout plan. Please check your connection and try again.";
            listEl.innerHTML = `
                <div style="text-align: center; padding: 40px; color: var(--secondary-text);">
                    <i class='bx bx-wifi-off' style="font-size: 3rem; margin-bottom: 16px; color: #EF4444;"></i>
                    <p style="margin-bottom: 8px;">Unable to connect to server</p>
                    <p style="font-size: 0.9rem; color: var(--secondary-text); margin-bottom: 20px;">Error: ${error.message}</p>
                    <button onclick="window.location.reload()" style="margin-top: 10px; padding: 12px 24px; background: var(--primary-color); color: white; border: none; border-radius: 12px; cursor: pointer; font-weight: 600;">
                        <i class='bx bx-refresh'></i> Retry
                    </button>
                </div>
            `;
            startBtn.disabled = true;
        });
});