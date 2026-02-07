document.addEventListener('DOMContentLoaded', function() {
    const totalTimeEl = document.getElementById('total-time');
    const totalCaloriesEl = document.getElementById('total-calories');
    const weeklyWorkoutsEl = document.getElementById('weekly-workouts');
    const totalWorkoutsEl = document.getElementById('total-workouts');
    const activityChartCanvas = document.getElementById('activityChart');
    const weightChartCanvas = document.getElementById('weightChart');
    let activityChartInstance = null;
    let weightChartInstance = null;

    fetch('../php/api/user/get_user_stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                if (totalTimeEl) totalTimeEl.textContent = `${Math.round((data.stats.total_duration || 0) / 60)} Minutes`;
                if (totalCaloriesEl) totalCaloriesEl.textContent = `${data.stats.total_calories || 0} kcal`;
                if (weeklyWorkoutsEl) weeklyWorkoutsEl.textContent = data.stats.workouts_this_week || 0;
                if (totalWorkoutsEl) totalWorkoutsEl.textContent = data.stats.total_workouts || 0;
                if (data.stats.last_7_days && data.stats.last_7_days.length > 0) {
                    const activityLabels = data.stats.last_7_days.map(day => {
                        const date = new Date(day.date + 'T00:00:00');
                        return date.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
                    });
                    const activityData = data.stats.last_7_days.map(day => Math.round(day.duration / 60));
                    if (activityChartCanvas) {
                        const activityCtx = activityChartCanvas.getContext('2d');
                        if (activityChartInstance) {
                            activityChartInstance.destroy();
                        }
                        const primaryColor = getComputedStyle(document.body).getPropertyValue('--primary-color').trim() || '#3B82F6';
                        const secondaryText = getComputedStyle(document.body).getPropertyValue('--secondary-text').trim() || '#6B7280';
                        const borderColor = getComputedStyle(document.body).getPropertyValue('--border-color').trim() || '#ddd';
                        activityChartInstance = new Chart(activityCtx, {
                            type: 'bar',
                            data: {
                                labels: activityLabels,
                                datasets: [{
                                    label: 'Workout Time (Minutes)',
                                    data: activityData,
                                    backgroundColor: primaryColor + '99',
                                    borderColor: primaryColor,
                                    borderWidth: 1,
                                    borderRadius: 5,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            color: secondaryText,
                                            callback: function (value) { return value + ' min'; }
                                        },
                                        grid: {
                                            color: borderColor
                                        }
                                    },
                                    x: {
                                        ticks: {
                                            color: secondaryText
                                        },
                                        grid: {
                                            display: false
                                        }
                                    }
                                },
                                plugins: {
                                    legend: { display: false }
                                }
                            }
                        });
                        activityChartCanvas.style.display = 'block';
                    }
                } else {
                     if (activityChartCanvas) {
                        activityChartCanvas.style.display = 'none';
                        if (activityChartInstance) { activityChartInstance.destroy(); activityChartInstance = null; }
                        let existingMsg = activityChartCanvas.parentNode.querySelector('.chart-error-message');
                        if (!existingMsg) {
                            const msgDiv = document.createElement('div');
                            msgDiv.className = 'chart-error-message';
                            msgDiv.textContent = 'No workout activity recorded in the last 7 days.';
                            activityChartCanvas.parentNode.appendChild(msgDiv);
                        }
                    }
                }
            } else {
                console.error('Could not load user stats:', data.message);
                if (totalTimeEl) totalTimeEl.textContent = 'Error';
                if (totalCaloriesEl) totalCaloriesEl.textContent = 'Error';
                if (weeklyWorkoutsEl) weeklyWorkoutsEl.textContent = 'Error';
                if (totalWorkoutsEl) totalWorkoutsEl.textContent = 'Error';
            }
        })
        .catch(error => {
            console.error('Error fetching user stats:', error);
            if (totalTimeEl) totalTimeEl.textContent = 'Error';
            if (totalCaloriesEl) totalCaloriesEl.textContent = 'Error';
            if (weeklyWorkoutsEl) weeklyWorkoutsEl.textContent = 'Error';
            if (totalWorkoutsEl) totalWorkoutsEl.textContent = 'Error';
        });
    fetch('../php/api/user/get_weight_history.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.history.length > 0) {
                const weightLabels = data.history.map(item => {
                    if (!item.log_date) return '';
                    const datePart = item.log_date.split(' ')[0]; 
                    if (!datePart) return item.log_date;
                    const [year, month, day] = datePart.split('-').map(Number);
                    const date = new Date(year, month - 1, day);
                    if (isNaN(date.getTime())) {
                        return datePart;
                    }
                    return date.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
                });
                const weightData = data.history.map(item => item.weight_kg);
                if (weightChartCanvas) {
                    const weightCtx = weightChartCanvas.getContext('2d');
                    if (weightChartInstance) {
                        weightChartInstance.destroy();
                    }
                    const accentColor = '#2ecc71';
                    const secondaryText = getComputedStyle(document.body).getPropertyValue('--secondary-text').trim() || '#6B7280';
                    const borderColor = getComputedStyle(document.body).getPropertyValue('--border-color').trim() || '#ddd';
                    weightChartInstance = new Chart(weightCtx, {
                        type: 'line',
                        data: {
                            labels: weightLabels,
                            datasets: [{
                                label: 'Weight (kg)',
                                data: weightData,
                                borderColor: accentColor,
                                backgroundColor: accentColor + '1A',
                                fill: true,
                                tension: 0.1,
                                pointBackgroundColor: accentColor,
                                pointRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: false,
                                    ticks: {
                                         color: secondaryText
                                    },
                                     grid: {
                                        color: borderColor
                                    }
                                },
                                x: {
                                     ticks: {
                                         color: secondaryText
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            plugins: {
                                legend: { display: false }
                            }
                        }
                    });
                    weightChartCanvas.style.display = 'block';
                    let existingMessage = weightChartCanvas.parentNode.querySelector('.chart-error-message');
                    if (existingMessage) {
                        existingMessage.remove();
                    }
                }
            } else {
                if (weightChartCanvas) {
                    weightChartCanvas.style.display = 'none';
                    if (weightChartInstance) {
                        weightChartInstance.destroy();
                        weightChartInstance = null;
                    }
                    let existingMessage = weightChartCanvas.parentNode.querySelector('.chart-error-message');
                    if (!existingMessage) {
                        const messageDiv = document.createElement('div');
                        messageDiv.className = 'chart-error-message';
                        messageDiv.innerHTML = 'No weight history logged yet.<br>Log your weight on the profile page to see trends.';
                        weightChartCanvas.parentNode.appendChild(messageDiv);
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error fetching weight history:', error);
             if (weightChartCanvas) {
                 weightChartCanvas.style.display = 'none';
                 if (weightChartInstance) { weightChartInstance.destroy(); weightChartInstance = null; }
                 let existingMsg = weightChartCanvas.parentNode.querySelector('.chart-error-message');
                 if (!existingMsg) {
                        const msgDiv = document.createElement('div');
                        msgDiv.className = 'chart-error-message';
                        msgDiv.textContent = 'Error loading weight history.';
                        weightChartCanvas.parentNode.appendChild(msgDiv);
                 }
             }
        });
    window.addEventListener('beforeunload', () => {
        if (activityChartInstance) activityChartInstance.destroy();
        if (weightChartInstance) weightChartInstance.destroy();
    });
});