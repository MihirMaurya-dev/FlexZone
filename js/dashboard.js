document.addEventListener('DOMContentLoaded', function() {
    const totalTimeEl = document.getElementById('total-time');
    const totalCaloriesEl = document.getElementById('total-calories');
    const weeklyWorkoutsEl = document.getElementById('weekly-workouts');
    const totalWorkoutsEl = document.getElementById('total-workouts');
    const activityChartCanvas = document.getElementById('activityChart');
    const weightChartCanvas = document.getElementById('weightChart');
    let activityChartInstance = null;
    let weightChartInstance = null;

    const updateStatsText = (data) => {
        if (totalTimeEl) totalTimeEl.textContent = `${Math.round((data.stats.total_duration || 0) / 60)} Minutes`;
        if (totalCaloriesEl) totalCaloriesEl.textContent = `${data.stats.total_calories || 0} kcal`;
        if (weeklyWorkoutsEl) weeklyWorkoutsEl.textContent = data.stats.workouts_this_week || 0;
        if (totalWorkoutsEl) totalWorkoutsEl.textContent = data.stats.total_workouts || 0;
    };

    const setErrorText = () => {
        [totalTimeEl, totalCaloriesEl, weeklyWorkoutsEl, totalWorkoutsEl].forEach(el => {
            if (el) el.textContent = 'Error';
        });
    };

    const getChartStyles = () => ({
        secondaryText: getComputedStyle(document.body).getPropertyValue('--secondary-text').trim() || '#6B7280',
        borderColor: getComputedStyle(document.body).getPropertyValue('--border-color').trim() || '#ddd'
    });

    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js not loaded!');
        const charts = document.querySelectorAll('canvas');
        charts.forEach(c => {
            const msg = document.createElement('p');
            msg.style.color = 'red';
            msg.textContent = 'Error: Charting library not loaded.';
            c.parentNode.appendChild(msg);
            c.style.display = 'none';
        });
    }

    window.apiFetch('../php/api/user/get_user_stats.php')
        .then(data => {
            if (data.status === 'success') {
                updateStatsText(data);
                if (typeof Chart !== 'undefined' && data.stats.last_7_days?.length > 0) {
                    const activityLabels = data.stats.last_7_days.map(day => {
                        const date = new Date(day.date + 'T00:00:00');
                        return date.toLocaleDateString('en-GB', {
                            day: 'numeric',
                            month: 'short'
                        });
                    });
                    const activityData = data.stats.last_7_days.map(day => Math.round(day.duration / 60));
                    if (activityChartCanvas) {
                        if (activityChartInstance) activityChartInstance.destroy();
                        const primaryColor = getComputedStyle(document.body).getPropertyValue('--primary-color').trim() || '#3B82F6';
                        const { secondaryText, borderColor } = getChartStyles();
                        activityChartInstance = new Chart(activityChartCanvas.getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: activityLabels,
                                datasets: [{
                                    label: 'Workout Time (Minutes)',
                                    data: activityData,
                                    backgroundColor: primaryColor + '99',
                                    borderColor: primaryColor,
                                    borderWidth: 1,
                                    borderRadius: 5
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
                                            callback: v => v + ' min'
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
                                    legend: {
                                        display: false
                                    }
                                }
                            }
                        });
                        activityChartCanvas.style.display = 'block';
                    }
                } else if (activityChartCanvas) {
                    activityChartCanvas.style.display = 'none';
                    if (activityChartInstance) {
                        activityChartInstance.destroy();
                        activityChartInstance = null;
                    }
                    if (!activityChartCanvas.parentNode.querySelector('.chart-error-message')) {
                        const msgDiv = document.createElement('div');
                        msgDiv.className = 'chart-error-message';
                        msgDiv.textContent = 'No workout activity recorded in the last 7 days.';
                        activityChartCanvas.parentNode.appendChild(msgDiv);
                    }
                }
            } else {
                setErrorText();
            }
        })
        .catch(err => {
            console.error('Dashboard Stats Error:', err);
            setErrorText();
        });

    window.apiFetch('../php/api/user/get_weight_history.php')
        .then(data => {
            if (data.status === 'success' && data.history.length > 0 && typeof Chart !== 'undefined') {
                const weightLabels = data.history.map(item => {
                    const datePart = item.log_date?.split(' ')[0];
                    if (!datePart) return '';
                    const [y, m, d] = datePart.split('-').map(Number);
                    return new Date(y, m - 1, d).toLocaleDateString('en-GB', {
                        day: 'numeric',
                        month: 'short'
                    });
                });
                const units = window.getUserUnits();
                const weightData = data.history.map(item => {
                    if (units === 'lbs') return parseFloat((item.weight_kg * 2.20462).toFixed(1));
                    return parseFloat(item.weight_kg);
                });
                if (weightChartCanvas) {
                    if (weightChartInstance) weightChartInstance.destroy();
                    const accentColor = '#2ecc71';
                    const { secondaryText, borderColor } = getChartStyles();
                    weightChartInstance = new Chart(weightChartCanvas.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: weightLabels,
                            datasets: [{
                                label: `Weight (${units})`,
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
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                    weightChartCanvas.style.display = 'block';
                    weightChartCanvas.parentNode.querySelector('.chart-error-message')?.remove();
                }
            } else if (weightChartCanvas) {
                weightChartCanvas.style.display = 'none';
                if (weightChartInstance) {
                    weightChartInstance.destroy();
                    weightChartInstance = null;
                }
                if (!weightChartCanvas.parentNode.querySelector('.chart-error-message')) {
                    const msgDiv = document.createElement('div');
                    msgDiv.className = 'chart-error-message';
                    msgDiv.innerHTML = 'No weight history logged yet.<br>Log your weight on the profile page to see trends.';
                    weightChartCanvas.parentNode.appendChild(msgDiv);
                }
            }
        })
        .catch(err => console.error('Dashboard Weight Error:', err));

    window.addEventListener('beforeunload', () => {
        if (activityChartInstance) activityChartInstance.destroy();
        if (weightChartInstance) weightChartInstance.destroy();
    });
});
