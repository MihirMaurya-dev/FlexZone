document.addEventListener('DOMContentLoaded', function() {
  const UIElements = {
    exerciseName: document.getElementById('exercise-name'),
    metric: document.getElementById('exercise-metric'),
    restTimer: document.getElementById('rest-timer'),
    visual: {
      container: document.querySelector('.exercise-visual'),
      videoPlayer: document.querySelector('.exercise-visual video'),
      placeholderImg: document.getElementById('visual-placeholder-img'),
      placeholderText: document.getElementById('visual-placeholder-text')
    },
    nav: {
      container: document.querySelector('.workout-nav'),
      prevBtn: document.getElementById('prev-btn'),
      nextBtn: document.getElementById('next-btn'),
      pauseBtn: document.getElementById('pause-btn'),
      exitBtn: document.getElementById('exit-btn'),
      repeatBtn: document.getElementById('repeat-btn'),
      addRestBtn: document.getElementById('add-rest-btn')
    }
  };
  
  let state = {
    executionPlan: [],
    currentStepIndex: 0,
    timerInterval: null,
    timeLeft: 0,
    isPaused: false,
    totalWorkoutTimeSeconds: 0,
    totalCaloriesBurned: 0,
    userWeightKg: 70,
    workoutType: 'beginner'
  };

  async function init() {
    bindEventListeners();
    try {
      const profile = await window.apiFetch('../php/api/user/get_profile.php');
      if (profile.status === 'success' && profile.profile.weight_kg > 0) state.userWeightKg = parseFloat(profile.profile.weight_kg);

      const urlParams = new URLSearchParams(window.location.search);
      state.workoutType = urlParams.get('type') || 'beginner';
      let fetchUrl = `../php/api/workouts/generate_workout.php?type=${encodeURIComponent(state.workoutType)}`;
      ['muscle', 'duration', 'equipment'].forEach(p => { if (urlParams.get(p)) fetchUrl += `&${p}=${encodeURIComponent(urlParams.get(p))}`; });

      const data = await window.apiFetch(fetchUrl);
      if (data?.status === 'success' && data.workout?.length > 0) {
        state.executionPlan = [];
        data.workout.forEach((ex, i) => {
          state.executionPlan.push(ex);
          if (i < data.workout.length - 1) state.executionPlan.push({ type: 'rest', duration_seconds: 30 });
        });
        loadStep();
      } else throw new Error(data.message || "No exercises found.");
    } catch (error) {
      handleWorkoutError("Unable to load workout plan. Please try again.<br><button onclick='window.location.reload()' class='btn btn-primary' style='margin-top: 15px;'>Retry</button>");
    }
  }

  function loadStep() {
    if (state.currentStepIndex >= state.executionPlan.length) return finishWorkout();
    if (state.currentStepIndex < 0) state.currentStepIndex = 0;
    
    clearInterval(state.timerInterval);
    state.isPaused = false;
    UIElements.nav.pauseBtn.textContent = 'Pause';
    
    const step = state.executionPlan[state.currentStepIndex];
    step.type === 'rest' ? loadRestStep(step) : loadExerciseStep(step);
    
    UIElements.nav.prevBtn.disabled = (state.currentStepIndex === 0);
    UIElements.nav.nextBtn.textContent = (state.currentStepIndex === state.executionPlan.length - 1) ? 'Finish' : 'Next';
  }

  function loadExerciseStep(ex) {
    UIElements.exerciseName.textContent = ex.exercise_name;
    Object.assign(UIElements.metric.style, { display: 'block' });
    Object.assign(UIElements.restTimer.style, { display: 'none' });
    Object.assign(UIElements.nav.pauseBtn.style, { display: 'inline-block' });
    Object.assign(UIElements.nav.repeatBtn.style, { display: 'none' });
    Object.assign(UIElements.nav.prevBtn.style, { display: 'inline-block' });
    Object.assign(UIElements.nav.addRestBtn.style, { display: 'none' });
    
    updateVisuals(ex);
    const duration = parseInt(ex.duration_seconds || ex.default_duration);
    if (!isNaN(duration) && duration > 0) {
      state.timeLeft = duration;
      UIElements.metric.textContent = formatTime(state.timeLeft);
      UIElements.nav.nextBtn.textContent = 'Skip';
      startTimer('exercise');
    } else {
      UIElements.metric.textContent = `${ex.reps || 10} Reps`;
      UIElements.nav.nextBtn.textContent = 'Done';
      state.timeLeft = 0;
      startTimer('manual');
    }
  }

  function loadRestStep(step) {
    UIElements.exerciseName.textContent = "REST";
    UIElements.metric.style.display = 'none';
    UIElements.restTimer.style.display = 'block';
    UIElements.nav.pauseBtn.style.display = 'none';
    UIElements.nav.repeatBtn.style.display = 'inline-block';
    UIElements.nav.prevBtn.style.display = 'none';
    UIElements.nav.addRestBtn.style.display = 'inline-block';
    UIElements.nav.nextBtn.textContent = 'Skip Rest';
    
    updateVisuals(null);
    state.timeLeft = step.duration_seconds;
    UIElements.restTimer.textContent = formatTime(state.timeLeft);
    startTimer('rest');
  }

  function startTimer(mode) {
    clearInterval(state.timerInterval);
    state.timerInterval = setInterval(() => {
      if (state.isPaused) return;
      if (state.timeLeft > 0) {
        state.timeLeft--;
        if (mode === 'exercise') {
          state.totalWorkoutTimeSeconds++;
          UIElements.metric.textContent = formatTime(state.timeLeft);
          calculateCalories();
        } else UIElements.restTimer.textContent = formatTime(state.timeLeft);
      } else if (mode !== 'manual') goToNextStep();
    }, 1000);
  }

  function goToNextStep() { state.currentStepIndex++; loadStep(); }
  function goToPrevStep() { if (state.currentStepIndex > 0) { state.currentStepIndex--; loadStep(); } }
  function repeatCurrentExercise() { if (state.currentStepIndex > 0 && state.executionPlan[state.currentStepIndex].type === 'rest') state.currentStepIndex--; loadStep(); }
  function addRestTime() { state.timeLeft += 15; UIElements.restTimer.textContent = formatTime(state.timeLeft); }
  
  function togglePause() {
    state.isPaused = !state.isPaused;
    UIElements.nav.pauseBtn.textContent = state.isPaused ? 'Resume' : 'Pause';
    state.isPaused ? UIElements.visual.videoPlayer.pause() : UIElements.visual.videoPlayer.play().catch(() => {});
  }

  function exitWorkout() { if (confirm('Exit? Progress will not be saved.')) window.location.href = 'home.php'; }

  function finishWorkout() {
    clearInterval(state.timerInterval);
    UIElements.exerciseName.textContent = "Workout Complete! 🎉";
    UIElements.visual.container.innerHTML = '<div class="spinner"></div><p style="color: var(--secondary-text); margin-top: 10px;">Saving results...</p>';
    UIElements.nav.container.style.display = 'none';
    
    const workoutData = { 
        duration: state.totalWorkoutTimeSeconds, 
        calories: Math.round(state.totalCaloriesBurned), 
        name: `${state.workoutType.charAt(0).toUpperCase() + state.workoutType.slice(1)} Workout` 
    };

    window.apiFetch('../php/api/workouts/save_workout.php', { 
        method: 'POST', 
        headers: { 'Content-Type': 'application/json' }, 
        body: JSON.stringify(workoutData) 
    })
    .then(data => {
        if (data.status === 'success') {
            let msg = `Complete! ${Math.round(state.totalWorkoutTimeSeconds / 60)} min. ${Math.round(state.totalCaloriesBurned)} kcal.`;
            window.showMessage(msg + " Progress saved!", 'success');
            setTimeout(() => { window.location.href = 'home.php'; }, 2000);
        } else {
            window.showMessage(data.message || 'Error saving workout.');
            setTimeout(() => { window.location.href = 'home.php'; }, 3000);
        }
    })
    .catch(err => {
        console.error('Save workout error:', err);
        window.showMessage('Network error while saving progress.');
        setTimeout(() => { window.location.href = 'home.php'; }, 3000);
    });
  }

  function calculateCalories() {
    const cur = state.executionPlan[state.currentStepIndex];
    if (cur?.met_value) state.totalCaloriesBurned += (cur.met_value * state.userWeightKg * 3.5) / 12000;
  }

  function updateVisuals(ex) {
    const { videoPlayer, placeholderImg, placeholderText } = UIElements.visual;
    if (ex?.image_url) {
      const src = window.getAvatarPath(ex.image_url).replace('default_avatar.png', 'exercises/placeholder.png');
      const isVideo = src.endsWith('.mp4') || src.endsWith('.webm');
      videoPlayer.style.display = isVideo ? 'block' : 'none';
      placeholderImg.style.display = isVideo ? 'none' : 'block';
      placeholderText.style.display = 'none';
      if (isVideo) { if (videoPlayer.src !== src) videoPlayer.src = src; videoPlayer.play().catch(() => {}); }
      else placeholderImg.src = src;
    } else {
      videoPlayer.style.display = 'none';
      placeholderImg.style.display = 'none';
      placeholderText.style.display = 'block';
      placeholderText.textContent = ex ? "No visual available" : "Take a breather 💪";
    }
  }

  function formatTime(s) {
    const mins = Math.floor(s / 60);
    return `${String(mins).padStart(2, '0')}:${String(s % 60).padStart(2, '0')}`;
  }

  function handleWorkoutError(msg) {
    clearInterval(state.timerInterval);
    UIElements.exerciseName.textContent = "Error";
    UIElements.visual.container.innerHTML = `<div style="padding: 30px;">${msg}</div>`;
    UIElements.nav.container.innerHTML = `<button onclick="window.location.href='home.php'" class="btn btn-primary">Go Home</button>`;
  }

  function bindEventListeners() {
    UIElements.nav.nextBtn.onclick = goToNextStep;
    UIElements.nav.prevBtn.onclick = goToPrevStep;
    UIElements.nav.repeatBtn.onclick = repeatCurrentExercise;
    UIElements.nav.addRestBtn.onclick = addRestTime;
    UIElements.nav.pauseBtn.onclick = togglePause;
    UIElements.nav.exitBtn.onclick = exitWorkout;
  }
  init();
});
