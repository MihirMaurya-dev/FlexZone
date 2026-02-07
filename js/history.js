document.addEventListener('DOMContentLoaded', function() {
    const historyBody = document.getElementById('history-body');
    const limit = 50;
    const page = 1;
    fetch(`../php/api/workouts/get_workout_history.php?limit=${limit}&page=${page}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                historyBody.innerHTML = '';
                if (data.history.length > 0) {
                    data.history.forEach(log => {
                        const row = document.createElement('tr');
                        const durationMinutes = (log.duration_seconds / 60).toFixed(2); 
                        const logDate = new Date(log.log_date.replace(' ', 'T') + 'Z');
                        const formattedDate = logDate.toLocaleDateString('en-GB', {
                            day: 'numeric', month: 'long', year: 'numeric'
                        });
                        row.innerHTML = `
                            <td>${formattedDate}</td>
                            <td>${log.workout_name || 'Workout'}</td> 
                            <td>${durationMinutes}</td>
                            <td>${log.calories_burned || 0}</td> 
                        `;
                        historyBody.appendChild(row);
                    });
                } else {
                    historyBody.innerHTML = '<tr><td colspan="4">You have no workout history yet. Start a workout!</td></tr>';
                }
            } else {
                historyBody.innerHTML = `<tr><td colspan="4">Error loading history: ${data.message || 'Unknown error'}</td></tr>`;
            }
        })
        .catch(error => {
            console.error("Error fetching workout history:", error);
            historyBody.innerHTML = '<tr><td colspan="4">An error occurred while loading your history. Please try again later.</td></tr>';
        });
});