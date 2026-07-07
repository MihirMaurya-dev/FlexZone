document.addEventListener('DOMContentLoaded', function() {
    const usernameEl = document.getElementById('username');
    const startButtons = document.querySelectorAll('.workout-option');
    setupWorkoutButtons();
    updateDailySuggestion();
    loadRecentActivity();
    loadDailyQuote();
    loadWeeklyGoal();
    window.apiFetch('../php/api/user/get_user.php').then(userData => {
        if (userData.status === "success") {
            if (usernameEl) usernameEl.textContent = userData.username;
            return window.apiFetch('../php/api/user/get_profile.php');
        } else {
            window.location.href = 'join.php';
            throw new Error('User not logged in');
        }
    }).then(profileData => {
        if (profileData.status === 'success') {
            if (!profileData.profile.weight_kg || !profileData.profile.height_cm) {
                window.location.href = 'onboarding.php';
                return;
            }
            setupDailyChallenge(profileData.profile.challenge_data);
            setupHydrationTracker(profileData.profile.hydration_data);
            checkWorkoutReminders();
        }
    }).catch(error => {
        if (error.message !== 'User not logged in') {
            console.error('Home page setup error:', error);
        }
    });

    function setupWorkoutButtons() {
        startButtons.forEach(button => {
            button.onclick = (e) => {
                const type = e.currentTarget.getAttribute('data-type');
                if (type === 'custom_builder') {
                    window.location.href = 'custom_workout.php';
                } else if (type === 'custom_targeted') {
                    const muscle = e.currentTarget.getAttribute('data-muscle');
                    const eq = e.currentTarget.getAttribute('data-equipment');
                    const dur = e.currentTarget.getAttribute('data-duration') || '30';
                    let url = `workout_preview.php?type=custom&duration=${dur}`;
                    if(muscle) url += `&muscle=${encodeURIComponent(muscle)}`;
                    if(eq) url += `&equipment=${encodeURIComponent(eq)}`;
                    window.location.href = url;
                } else {
                    window.location.href = `workout_preview.php?type=${encodeURIComponent(type)}`;
                }
            };
        });
    }

    function updateDailySuggestion() {
        const greetingContainer = document.getElementById('greeting-container');
        if (!greetingContainer) return;

        const defaultDays = ["Active Recovery • 20 Mins", "Chest & Triceps • 45 Mins", "Back & Biceps • 50 Mins", "Leg Day • 50 Mins", "Shoulders & Abs • 40 Mins", "Full Body Intensity • 60 Mins", "Cardio & Core • 30 Mins"];
        const fallbackSuggestion = defaultDays[new Date().getDay()];

        window.apiFetch('../php/api/workouts/get_workout_history.php?limit=1')
            .then(data => {
                if (data.status === 'success' && data.history && data.history.length > 0) {
                    const lastWorkout = data.history[0].workout_name.toLowerCase();
                    let suggestion = fallbackSuggestion;

                    if (lastWorkout.includes('chest')) {
                        suggestion = "Back & Biceps • 50 Mins";
                    } else if (lastWorkout.includes('back')) {
                        suggestion = "Leg Day • 50 Mins";
                    } else if (lastWorkout.includes('leg')) {
                        suggestion = "Chest & Triceps • 45 Mins";
                    } else if (lastWorkout.includes('shoulder') || lastWorkout.includes('arm')) {
                        suggestion = "Cardio & Core • 30 Mins";
                    } else if (lastWorkout.includes('core') || lastWorkout.includes('abs')) {
                        suggestion = "Full Body Intensity • 60 Mins";
                    } else if (lastWorkout.includes('full body') || lastWorkout.includes('intensity')) {
                        suggestion = "Active Recovery • 20 Mins";
                    } else {
                        // For generic "Beginner Workout" etc, suggest something random but complementary
                        suggestion = "Try a Custom Workout • Target Weaknesses";
                    }

                    greetingContainer.textContent = suggestion;
                } else {
                    greetingContainer.textContent = fallbackSuggestion;
                }
            })
            .catch(() => {
                greetingContainer.textContent = fallbackSuggestion;
            });
    }

    function setupDailyChallenge(serverDataJson) {
        const challengeText = document.getElementById('challenge-text');
        const challengeCheckbox = document.getElementById('challenge-checkbox');
        const challenges = ["Do 50 Pushups", "Hold a plank for 2 min", "Drink 3L of water", "Walk 10,000 steps", "No sugar for 24h", "Do 50 Squats", "Stretch for 15 min"];
        if (!challengeText || !challengeCheckbox) return;
        const today = new Date().toDateString();
        
        let savedData = JSON.parse(localStorage.getItem('daily_challenge') || '{}');
        let serverData = {};
        try { serverData = serverDataJson ? JSON.parse(serverDataJson) : {}; } catch(e){}
        
        if (serverData.date === today) {
            savedData = serverData;
        }

        const syncData = (data) => {
            localStorage.setItem('daily_challenge', JSON.stringify(data));
            window.apiFetch('../php/api/user/sync_daily_data.php', {
                method: 'POST',
                body: JSON.stringify({ challenge_data: data })
            }).catch(e => console.error("Sync error", e));
        };

        if (savedData.date !== today) {
            const newData = {
                date: today,
                text: challenges[Math.floor(Math.random() * challenges.length)],
                completed: false
            };
            syncData(newData);
            challengeText.textContent = newData.text;
            challengeCheckbox.checked = false;
        } else {
            challengeText.textContent = savedData.text;
            challengeCheckbox.checked = savedData.completed;
            localStorage.setItem('daily_challenge', JSON.stringify(savedData));
        }
        
        challengeCheckbox.onchange = (e) => {
            const currentData = JSON.parse(localStorage.getItem('daily_challenge'));
            currentData.completed = e.target.checked;
            syncData(currentData);
        };
    }

    function setupHydrationTracker(serverDataJson) {
        const currentEl = document.getElementById('hydro-current');
        const plusBtn = document.getElementById('hydro-plus');
        const minusBtn = document.getElementById('hydro-minus');
        if (!currentEl) return;
        const today = new Date().toDateString();
        
        let savedData = JSON.parse(localStorage.getItem('hydration_tracker') || '{}');
        let serverData = {};
        try { serverData = serverDataJson ? JSON.parse(serverDataJson) : {}; } catch(e){}
        
        if (serverData.date === today) {
            savedData = serverData;
        }

        let count = savedData.date === today ? (savedData.count || 0) : 0;
        
        const updateDisplay = () => {
            currentEl.textContent = count;
            const data = { date: today, count: count };
            localStorage.setItem('hydration_tracker', JSON.stringify(data));
            window.apiFetch('../php/api/user/sync_daily_data.php', {
                method: 'POST',
                body: JSON.stringify({ hydration_data: data })
            }).catch(e => console.error("Sync error", e));
        };
        
        currentEl.textContent = count;
        localStorage.setItem('hydration_tracker', JSON.stringify({ date: today, count: count }));
        
        plusBtn.onclick = () => {
            if (count < 12) count++;
            updateDisplay();
        };
        minusBtn.onclick = () => {
            if (count > 0) count--;
            updateDisplay();
        };
    }

    function loadRecentActivity() {
        const container = document.getElementById('recent-workout-content');
        if (!container) return;
        window.apiFetch('../php/api/workouts/get_workout_history.php?limit=1').then(data => {
            if (data.status === 'success' && data.history?.length > 0) {
                const last = data.history[0];
                container.innerHTML = ` <p class="recent-name">${last.workout_name || 'Workout'}</p> <p class="recent-details">${new Date(last.log_date).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' })} • ${Math.round(last.duration_seconds / 60)}m • ${last.calories_burned}kcal</p> `;
            } else {
                container.innerHTML = `<p class="recent-name">No recent activity</p><p class="recent-details">Time to get moving!</p>`;
            }
        }).catch(() => {});
    }
    
    function checkWorkoutReminders() {
        if (!sessionStorage.getItem('reminder_shown')) {
            window.apiFetch('../php/api/user/check_reminders.php').then(data => {
                if (data.status === 'success' && data.remind) {
                    setTimeout(() => {
                        window.showMessage("🔔 " + data.message, 'success', 8000);
                        sessionStorage.setItem('reminder_shown', 'true');
                    }, 1500);
                }
            }).catch(() => {});
        }
    }

    function loadDailyQuote() {
        const quoteEl = document.getElementById('quote-text');
        if (!quoteEl) return;
        const quotes = ["The only bad workout is the one that didn't happen.", "Motivation is what gets you started. Habit is what keeps you going.", "Your body can stand almost anything. It’s your mind that you have to convince.", "Fitness is not about being better than someone else. It’s about being better than you were yesterday.", "Discipline is doing what needs to be done, even if you don't want to do it."];
        quoteEl.textContent = `"${quotes[Math.floor(Math.random() * quotes.length)]}"`;
    }

    function loadWeeklyGoal() {
        const progressText = document.querySelector('.progress-text span');
        const progressCircle = document.querySelector('.progress-ring-container circle:last-child');
        if (!progressText || !progressCircle) return;
        window.apiFetch('../php/api/user/get_user_stats.php').then(data => {
            if (data.status === 'success') {
                const workouts = data.stats.workouts_this_week;
                const goal = data.stats.weekly_goal || 5;
                progressText.textContent = `${workouts}/${goal}`;
                const circumference = 465;
                progressCircle.style.strokeDashoffset = circumference - (Math.min(workouts / goal, 1) * circumference);
            }
        }).catch(() => {});
    }
});
