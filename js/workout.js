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
    },
    progress: {
      track: document.getElementById('step-progress-track'),
      current: document.getElementById('step-current'),
      total: document.getElementById('step-total'),
      elapsed: document.getElementById('workout-elapsed')
    },
    modal: {
      overlay: document.getElementById('exit-modal-overlay'),
      statSteps: document.getElementById('exit-stat-steps'),
      statTime: document.getElementById('exit-stat-time'),
      cancelBtn: document.getElementById('exit-modal-cancel'),
      confirmBtn: document.getElementById('exit-modal-confirm')
    }
  };
  
  let state = {
    executionPlan: [],
    currentStepIndex: 0,
    timerInterval: null,
    elapsedInterval: null,
    elapsedSeconds: 0,
    timeLeft: 0,
    isPaused: false,
    totalWorkoutTimeSeconds: 0,
    totalCaloriesBurned: 0,
    userWeightKg: 70,
    workoutType: 'beginner',
    planKey: null
  };

  async function init() {
    bindEventListeners();
    try {
      const profile = await window.apiFetch('../php/api/user/get_profile.php');
      if (profile.status === 'success' && profile.profile.weight_kg > 0) state.userWeightKg = parseFloat(profile.profile.weight_kg);

      const urlParams = new URLSearchParams(window.location.search);
      state.workoutType = urlParams.get('type') || 'beginner';
      state.planKey = urlParams.get('planKey') || null;

      let workout = null;
      // Try the typed key first (set by workout_preview.js)
      if (state.planKey) {
        const saved = sessionStorage.getItem(state.planKey);
        if (saved) {
          try {
            const parsed = JSON.parse(saved);
            if (Array.isArray(parsed) && parsed.length > 0) workout = parsed;
          } catch(e) { console.error('Plan parse error:', e); }
        }
      }
      // Legacy fallback: old generic key (keeps backward compat)
      if (!workout) {
        const saved = sessionStorage.getItem('active_workout_plan');
        if (saved) {
          try {
            const parsed = JSON.parse(saved);
            if (Array.isArray(parsed) && parsed.length > 0) workout = parsed;
          } catch(e) { console.error('Plan parse error:', e); }
        }
      }

      if (!workout) {
        let fetchUrl = `../php/api/workouts/generate_workout.php?type=${encodeURIComponent(state.workoutType)}`;
        ['muscle', 'duration', 'equipment'].forEach(p => { if (urlParams.get(p)) fetchUrl += `&${p}=${encodeURIComponent(urlParams.get(p))}`; });

        const data = await window.apiFetch(fetchUrl);
        if (data?.status === 'success' && data.workout?.length > 0) {
          workout = data.workout;
        } else {
          throw new Error(data?.message || "No exercises found.");
        }
      }

      state.executionPlan = [];
      workout.forEach((ex, i) => {
        state.executionPlan.push(ex);
        if (i < workout.length - 1) {
          state.executionPlan.push({ type: 'rest', duration_seconds: ex.rest_seconds || 30 });
        }
      });
      buildProgressSegments();
      startElapsedTimer();
      loadStep();
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

    updateProgressUI();
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

  /* ---- Progress Bar ---- */
  function buildProgressSegments() {
    const track = UIElements.progress.track;
    if (!track) return;
    track.innerHTML = '';
    state.executionPlan.forEach((step, i) => {
      const seg = document.createElement('div');
      seg.classList.add('step-segment');
      if (step.type === 'rest') seg.classList.add('rest');
      seg.dataset.index = i;
      track.appendChild(seg);
    });
    // Set total label to exercise count only (skip rest steps)
    const exerciseCount = state.executionPlan.filter(s => s.type !== 'rest').length;
    if (UIElements.progress.total) UIElements.progress.total.textContent = exerciseCount;
  }

  function updateProgressUI() {
    const track = UIElements.progress.track;
    if (!track) return;
    const segments = track.querySelectorAll('.step-segment');
    segments.forEach((seg, i) => {
      seg.classList.remove('active', 'done');
      if (i < state.currentStepIndex) seg.classList.add('done');
      else if (i === state.currentStepIndex) seg.classList.add('active');
    });
    // Step counter shows exercise number (not rest)
    const exerciseStepsBefore = state.executionPlan
      .slice(0, state.currentStepIndex + 1)
      .filter(s => s.type !== 'rest').length;
    if (UIElements.progress.current) {
      UIElements.progress.current.textContent = exerciseStepsBefore;
    }
  }

  /* ---- Elapsed Timer ---- */
  function startElapsedTimer() {
    clearInterval(state.elapsedInterval);
    state.elapsedInterval = setInterval(() => {
      if (!state.isPaused) {
        state.elapsedSeconds++;
        if (UIElements.progress.elapsed) {
          UIElements.progress.elapsed.textContent = formatTime(state.elapsedSeconds);
        }
      }
    }, 1000);
  }
  
  function togglePause() {
    state.isPaused = !state.isPaused;
    UIElements.nav.pauseBtn.textContent = state.isPaused ? 'Resume' : 'Pause';
    state.isPaused ? UIElements.visual.videoPlayer.pause() : UIElements.visual.videoPlayer.play().catch(() => {});
  }

  function openExitModal() {
    const overlay = UIElements.modal.overlay;
    if (!overlay) return;
    // Populate live stats
    const stepsDone = state.currentStepIndex;
    if (UIElements.modal.statSteps) UIElements.modal.statSteps.textContent = stepsDone;
    if (UIElements.modal.statTime)  UIElements.modal.statTime.textContent  = formatTime(state.elapsedSeconds);
    overlay.classList.add('visible');
    // Trap focus on the modal
    setTimeout(() => { if (UIElements.modal.cancelBtn) UIElements.modal.cancelBtn.focus(); }, 50);
  }

  function closeExitModal() {
    const overlay = UIElements.modal.overlay;
    if (overlay) overlay.classList.remove('visible');
  }

  function confirmExit() {
    clearInterval(state.elapsedInterval);
    clearInterval(state.timerInterval);
    if (state.planKey) sessionStorage.removeItem(state.planKey);
    sessionStorage.removeItem('active_workout_plan');
    window.location.href = 'home.php';
  }

  function exitWorkout() { openExitModal(); }

  function finishWorkout() {
    clearInterval(state.timerInterval);
    clearInterval(state.elapsedInterval);
    if (state.planKey) sessionStorage.removeItem(state.planKey);
    sessionStorage.removeItem('active_workout_plan');
    UIElements.exerciseName.textContent = "Workout Complete! 🎉";
    UIElements.visual.container.innerHTML = '<div class="spinner"></div><p style="color: var(--secondary-text); margin-top: 10px;">Saving results...</p>';
    UIElements.nav.container.style.display = 'none';
    
    const workoutData = { 
        duration: state.totalWorkoutTimeSeconds, 
        calories: Math.round(state.totalCaloriesBurned), 
        name: urlParams.get('muscle') ? `${urlParams.get('muscle')} Workout` : `${state.workoutType.charAt(0).toUpperCase() + state.workoutType.slice(1)} Workout` 
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
    UIElements.nav.nextBtn.onclick    = goToNextStep;
    UIElements.nav.prevBtn.onclick    = goToPrevStep;
    UIElements.nav.repeatBtn.onclick  = repeatCurrentExercise;
    UIElements.nav.addRestBtn.onclick = addRestTime;
    UIElements.nav.pauseBtn.onclick   = togglePause;
    UIElements.nav.exitBtn.onclick    = exitWorkout;

    // Modal buttons
    if (UIElements.modal.cancelBtn)  UIElements.modal.cancelBtn.onclick  = closeExitModal;
    if (UIElements.modal.confirmBtn) UIElements.modal.confirmBtn.onclick = confirmExit;

    // Close on overlay click (outside the card)
    if (UIElements.modal.overlay) {
      UIElements.modal.overlay.addEventListener('click', (e) => {
        if (e.target === UIElements.modal.overlay) closeExitModal();
      });
    }

    // Escape key closes the modal
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && UIElements.modal.overlay?.classList.contains('visible')) closeExitModal();
    });
  }
  init();
});
