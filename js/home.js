document.addEventListener('DOMContentLoaded', function() {
    const usernameEl = document.getElementById('username');
    const startButtons = document.querySelectorAll('.workout-option');
    setupWorkoutButtons();
    updateDailySuggestion();
    setupDailyChallenge();
    setupHydrationTracker();
    loadRecentActivity();
    loadDailyQuote();
    loadWeeklyGoal();
    fetch('../php/api/user/get_user.php')
        .then(response => response.json())
        .then(userData => {
            if (userData.status === "success") {
                if (usernameEl) usernameEl.textContent = userData.username;
                return fetch('../php/api/user/get_profile.php');
            } else {
                window.location.href = 'join.php';
                throw new Error('User not logged in');
            }
        })
        .then(response => response.json())
        .then(profileData => {
            if (profileData.status === 'success' && (!profileData.profile.weight_kg || !profileData.profile.height_cm)) {
                window.location.href = 'onboarding.php';
            } 
        })
        .catch(error => {
            if (error.message !== 'User not logged in') {
                 console.error('Error during home page setup:', error);
            }
        });
    function setupWorkoutButtons() {
        startButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                const workoutType = event.currentTarget.getAttribute('data-type');
                if (workoutType === 'custom') {
                    window.location.href = 'custom_workout.php';
                } else if (workoutType) {
                    window.location.href = `workout_preview.php?type=${encodeURIComponent(workoutType)}`;
                }
            });
        });
    }
    function updateDailySuggestion() {
        const greetingContainer = document.getElementById('greeting-container');
        if (!greetingContainer) return;
        const days = [
            "Active Recovery • 20 Mins",
            "Chest & Triceps • 45 Mins",
            "Back & Biceps • 50 Mins",
            "Leg Day • 50 Mins",
            "Shoulders & Abs • 40 Mins",
            "Full Body Intensity • 60 Mins",
            "Cardio & Core • 30 Mins"
        ];
        const today = new Date().getDay();
        greetingContainer.textContent = days[today];
    }
    function setupDailyChallenge() {
        const challengeText = document.getElementById('challenge-text');
        const challengeCheckbox = document.getElementById('challenge-checkbox');
        const challenges = [
            "Do 50 Pushups throughout the day",
            "Hold a plank for 2 minutes total",
            "Drink 3 liters of water",
            "Walk 10,000 steps",
            "No sugar for 24 hours",
            "Do 50 Squats",
            "Stretch for 15 minutes"
        ];
        if (!challengeText || !challengeCheckbox) return;
        const today = new Date().toDateString();
        const savedData = JSON.parse(localStorage.getItem('daily_challenge') || '{}');
        if (savedData.date !== today) {
            const randomChallenge = challenges[Math.floor(Math.random() * challenges.length)];
            const newData = { date: today, text: randomChallenge, completed: false };
            localStorage.setItem('daily_challenge', JSON.stringify(newData));
            challengeText.textContent = randomChallenge;
            challengeCheckbox.checked = false;
        } else {
            challengeText.textContent = savedData.text;
            challengeCheckbox.checked = savedData.completed;
        }
        challengeCheckbox.addEventListener('change', (e) => {
            const currentData = JSON.parse(localStorage.getItem('daily_challenge'));
            currentData.completed = e.target.checked;
            localStorage.setItem('daily_challenge', JSON.stringify(currentData));
        });
    }
    function setupHydrationTracker() {
        const currentEl = document.getElementById('hydro-current');
        const plusBtn = document.getElementById('hydro-plus');
        const minusBtn = document.getElementById('hydro-minus');
        if (!currentEl) return;
        const today = new Date().toDateString();
        const savedData = JSON.parse(localStorage.getItem('hydration_tracker') || '{}');
        let count = 0;
        if (savedData.date === today) {
            count = savedData.count || 0;
        }
        const updateDisplay = () => {
            currentEl.textContent = count;
            localStorage.setItem('hydration_tracker', JSON.stringify({ date: today, count: count }));
        };
        updateDisplay();
        plusBtn.addEventListener('click', () => {
            if (count < 12) count++;
            updateDisplay();
        });
        minusBtn.addEventListener('click', () => {
            if (count > 0) count--;
            updateDisplay();
        });
    }
    function loadRecentActivity() {
        const container = document.getElementById('recent-workout-content');
        if (!container) return;
        fetch('../php/api/workouts/get_workout_history.php?limit=1')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && data.history && data.history.length > 0) {
                    const last = data.history[0];
                    const date = new Date(last.log_date).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
                    const mins = Math.round(last.duration_seconds / 60);
                    container.innerHTML = `
                        <p class="recent-name">${last.workout_name || 'Workout'}</p>
                        <p class="recent-details">${date} • ${mins}m • ${last.calories_burned}kcal</p>
                    `;
                } else {
                    container.innerHTML = `
                        <p class="recent-name">No recent activity</p>
                        <p class="recent-details">Time to get moving!</p>
                    `;
                }
            })
            .catch(err => console.error("Error loading recent activity", err));
    }
    function loadDailyQuote() {
        const quoteEl = document.getElementById('quote-text');
        if (!quoteEl) return;
        const quotes = [
            "The only bad workout is the one that didn't happen.",
            "Motivation is what gets you started. Habit is what keeps you going.",
            "Your body can stand almost anything. It’s your mind that you have to convince.",
            "Fitness is not about being better than someone else. It’s about being better than you were yesterday.",
            "Discipline is doing what needs to be done, even if you don't want to do it."
        ];
        const randomQuote = quotes[Math.floor(Math.random() * quotes.length)];
        quoteEl.textContent = `"${randomQuote}"`;
    }
    function loadWeeklyGoal() {
        const progressText = document.querySelector('.progress-text span');
        const progressCircle = document.querySelector('.progress-ring-container circle:last-child');
        if (!progressText || !progressCircle) return;
        fetch('../php/api/user/get_user_stats.php')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const workouts = data.stats.workouts_this_week;
                    const goal = data.stats.weekly_goal || 5; 
                    progressText.textContent = `${workouts}/${goal}`;
                    const circumference = 465;
                    const offset = circumference - (Math.min(workouts / goal, 1) * circumference);
                    progressCircle.style.strokeDashoffset = offset;
                }
            })
            .catch(err => console.error("Error loading weekly goal", err));
    }
});