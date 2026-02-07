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
      await fetchUserWeight();
      await fetchWorkoutPlan();
      loadStep();
    } catch (error) {
      console.error("Initialization failed:", error);
      handleWorkoutError(
        "Unable to load workout plan. Please check your connection and try again.<br>" +
        `<button onclick="window.location.reload()" class="btn btn-primary" style="margin-top: 15px;">Retry</button>`
      );
    }
  }
  async function fetchUserWeight() {
    try {
      const response = await fetch('../php/api/user/get_profile.php');
      const data = await response.json();
      if (data.status === 'success' && data.profile.weight_kg > 0) {
        state.userWeightKg = parseFloat(data.profile.weight_kg);
      } else {
        console.warn('User weight not available, using default for calorie calculation.');
      }
    } catch (error) {
      console.error("Error fetching user weight, using default.", error);
    }
  }
  async function fetchWorkoutPlan() {
    const urlParams = new URLSearchParams(window.location.search);
    state.workoutType = urlParams.get('type') || 'beginner';
    let fetchUrl = `../php/api/workouts/generate_workout.php?type=${encodeURIComponent(state.workoutType)}`;
    if (urlParams.get('muscle')) fetchUrl += `&muscle=${encodeURIComponent(urlParams.get('muscle'))}`;
    if (urlParams.get('duration')) fetchUrl += `&duration=${encodeURIComponent(urlParams.get('duration'))}`;
    if (urlParams.get('equipment')) fetchUrl += `&equipment=${encodeURIComponent(urlParams.get('equipment'))}`;
    const response = await fetch(fetchUrl);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    if (data && data.status === 'success' && data.workout && data.workout.length > 0) {
      state.executionPlan = [];
      data.workout.forEach((exercise, index) => {
        state.executionPlan.push(exercise);
        if (index < data.workout.length - 1) {
          state.executionPlan.push({ type: 'rest', duration_seconds: 30 });
        }
      });
    } else {
      throw new Error(data.message || "No exercises found for this workout plan.");
    }
  }
  function loadStep() {
    if (state.currentStepIndex >= state.executionPlan.length) {
      return finishWorkout();
    }
    if (state.currentStepIndex < 0) state.currentStepIndex = 0;
    clearInterval(state.timerInterval);
    state.isPaused = false;
    UIElements.nav.pauseBtn.textContent = 'Pause';
    const step = state.executionPlan[state.currentStepIndex];
    if (step.type === 'rest') {
      loadRestStep(step);
    } else {
      loadExerciseStep(step);
    }
    UIElements.nav.prevBtn.disabled = (state.currentStepIndex === 0);
    UIElements.nav.nextBtn.textContent = (state.currentStepIndex === state.executionPlan.length - 1) ? 'Finish' : 'Next';
  }
  function loadExerciseStep(exercise) {
    UIElements.exerciseName.textContent = exercise.exercise_name;
    UIElements.metric.style.display = 'block';
    UIElements.restTimer.style.display = 'none';
    UIElements.nav.pauseBtn.style.display = 'inline-block';
    UIElements.nav.repeatBtn.style.display = 'none';
    UIElements.nav.prevBtn.style.display = 'inline-block';
    UIElements.nav.addRestBtn.style.display = 'none';
    updateVisuals(exercise);
    const duration = parseInt(exercise.duration_seconds || exercise.default_duration);
    if (!isNaN(duration) && duration > 0) {
      state.timeLeft = duration;
      UIElements.metric.textContent = formatTime(state.timeLeft);
      // Show Skip button for timed exercises
      UIElements.nav.nextBtn.style.display = 'inline-block';
      UIElements.nav.nextBtn.textContent = 'Skip';
      startTimer('exercise');
    } else {
      UIElements.metric.textContent = `${exercise.reps || 10} Reps`;
      UIElements.nav.nextBtn.style.display = 'inline-block';
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
    UIElements.nav.nextBtn.style.display = 'inline-block';
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
        } else {
          UIElements.restTimer.textContent = formatTime(state.timeLeft);
        }
      } else if (mode !== 'manual') {
        goToNextStep();
      }
    }, 1000);
  }
  function goToNextStep() {
    if (state.currentStepIndex < state.executionPlan.length) {
      state.currentStepIndex++;
    }
    loadStep();
  }
  function goToPrevStep() {
    if (state.currentStepIndex > 0) {
      state.currentStepIndex--;
      loadStep();
    }
  }
  function repeatCurrentExercise() {
    if (state.currentStepIndex > 0 && state.executionPlan[state.currentStepIndex].type === 'rest') {
      state.currentStepIndex--;
    }
    loadStep();
  }
  function addRestTime() {
    state.timeLeft += 15;
    UIElements.restTimer.textContent = formatTime(state.timeLeft);
  }
  function togglePause() {
    state.isPaused = !state.isPaused;
    UIElements.nav.pauseBtn.textContent = state.isPaused ? 'Resume' : 'Pause';
    if (state.isPaused) {
      UIElements.visual.videoPlayer.pause();
    } else {
      UIElements.visual.videoPlayer.play().catch(e => console.warn("Autoplay prevented:", e));
    }
  }
  function exitWorkout() {
    if (confirm('Are you sure you want to exit? Your progress will not be saved.')) {
      window.location.href = 'home.php';
    }
  }
  function finishWorkout() {
    clearInterval(state.timerInterval);
    UIElements.exerciseName.textContent = "Workout Complete! 🎉";
    UIElements.metric.textContent = "✓";
    UIElements.restTimer.style.display = 'none';
    UIElements.visual.container.innerHTML = '<span style="font-size: 1.2em; color: var(--secondary-text);">Saving your progress...</span>';
    UIElements.nav.container.style.display = 'none';
    const workoutData = {
      duration: state.totalWorkoutTimeSeconds,
      calories: Math.round(state.totalCaloriesBurned),
      name: `${state.workoutType.charAt(0).toUpperCase() + state.workoutType.slice(1)} Workout`
    };
    fetch('../php/api/workouts/save_workout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(workoutData)
      })
      .then(res => res.json())
      .then(data => {
        let message = `Workout Complete! Time: ${Math.round(state.totalWorkoutTimeSeconds / 60)} min. Calories: ${Math.round(state.totalCaloriesBurned)} kcal.`;
        if (data.status === 'success') {
            window.showMessage(message + " Progress saved!", 'success');
        } else {
            window.showMessage(message + ` Save error: ${data.message}`);
        }
        // Delay redirect to allow reading the message
        setTimeout(() => { window.location.href = 'home.php'; }, 3000);
      })
      .catch(err => {
          console.error('Save error:', err);
          window.location.href = 'home.php';
      });
  }
  function calculateCalories() {
    const currentExercise = state.executionPlan[state.currentStepIndex];
    if (currentExercise && currentExercise.met_value) {
      const caloriesPerSecond = (currentExercise.met_value * state.userWeightKg * 3.5) / 12000;
      state.totalCaloriesBurned += caloriesPerSecond;
    }
  }
  function updateVisuals(exercise) {
    const { videoPlayer, placeholderImg, placeholderText } = UIElements.visual;
    placeholderImg.onerror = function() {
        this.src = '../assets/exercises/placeholder.png';
        this.onerror = null;
    };
    if (exercise && exercise.image_url) {
      let visualPath = exercise.image_url;
      if (!visualPath.startsWith('../') && !visualPath.startsWith('http')) {
          visualPath = '../' + visualPath;
      }
      const isVideo = visualPath.endsWith('.mp4') || visualPath.endsWith('.webm');
      videoPlayer.style.display = isVideo ? 'block' : 'none';
      placeholderImg.style.display = isVideo ? 'none' : 'block';
      placeholderText.style.display = 'none';
      if (isVideo) {
        if (videoPlayer.src !== visualPath) videoPlayer.src = visualPath;
        videoPlayer.load();
        videoPlayer.play().catch(e => console.warn("Autoplay prevented:", e));
      } else {
        if (placeholderImg.getAttribute('src') !== visualPath) {
             placeholderImg.src = visualPath;
        }
      }
    } else {
      videoPlayer.style.display = 'none';
      placeholderImg.style.display = 'none';
      placeholderText.style.display = 'block';
      placeholderText.textContent = exercise ? "No visual available" : "Take a breather 💪";
    }
  }
  function formatTime(seconds) {
    if (isNaN(seconds) || seconds < 0) return "00:00";
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
  }
  function handleWorkoutError(message) {
    clearInterval(state.timerInterval);
    UIElements.exerciseName.textContent = "Error Loading Workout";
    UIElements.metric.style.display = 'none';
    UIElements.restTimer.style.display = 'none';
    UIElements.visual.container.innerHTML = `<div style="text-align: center; padding: 30px; color: var(--secondary-text);">${message}</div>`;
    UIElements.nav.container.innerHTML = `<button onclick="window.location.href='home.php'" class="btn" style="background:var(--primary-color);color:white;">Go Home</button>`;
  }
  function bindEventListeners() {
    UIElements.nav.nextBtn.addEventListener('click', goToNextStep);
    UIElements.nav.prevBtn.addEventListener('click', goToPrevStep);
    UIElements.nav.repeatBtn.addEventListener('click', repeatCurrentExercise);
    UIElements.nav.addRestBtn.addEventListener('click', addRestTime);
    UIElements.nav.pauseBtn.addEventListener('click', togglePause);
    UIElements.nav.exitBtn.addEventListener('click', exitWorkout);
  }
  init();
});
