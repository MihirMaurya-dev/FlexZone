document.addEventListener('DOMContentLoaded', function() {
    const leaderboardBody = document.getElementById('leaderboard-body');
    leaderboardBody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 30px;"><div class="spinner"></div> Loading leaderboard...</td></tr>';
    fetch('../php/api/get_leaderboard.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.status === "success") {
                leaderboardBody.innerHTML = '';
                if (data.leaderboard.length === 0) {
                    leaderboardBody.innerHTML = `
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: var(--secondary-text);">
                                <i class='bx bx-trophy' style="font-size: 3rem; display: block; margin-bottom: 16px; opacity: 0.3;"></i>
                                Be the first! No workout data available yet.
                            </td>
                        </tr>
                    `;
                    return;
                }
                data.leaderboard.forEach((user, index) => {
                    const row = document.createElement('tr');
                    let rankDisplay;
                    if (index === 0) rankDisplay = '🥇';
                    else if (index === 1) rankDisplay = '🥈';
                    else if (index === 2) rankDisplay = '🥉';
                    else rankDisplay = index + 1;
                    const totalMinutes = Math.round(user.total_duration / 60);
                    row.innerHTML = `
                        <td style="font-size: 1.2rem; font-weight: 600;">${rankDisplay}</td>
                        <td style="font-weight: 500;">${user.username ? user.username : 'Anonymous'}</td> 
                        <td>${totalMinutes}</td>
                        <td>${user.total_workouts || 0}</td>
                        <td>${user.total_calories || 0}</td>
                    `;
                    if (index < 3) {
                        row.style.background = 'rgba(59, 130, 246, 0.05)';
                    }
                    leaderboardBody.appendChild(row);
                });
                if (data.user_rank) {
                    const userRankInfo = document.getElementById('user-rank-info');
                    if (userRankInfo) {
                        userRankInfo.innerHTML = `<p>Your rank: <strong>#${data.user_rank}</strong></p>`;
                    }
                }
            } else {
                leaderboardBody.innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 30px; color: var(--secondary-text);">Could not load leaderboard: ${data.message || 'Unknown error'}</td></tr>`;
            }
        })
        .catch(error => {
            console.error("Error fetching leaderboard:", error);
            leaderboardBody.innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px;">
                        <i class='bx bx-error-circle' style="font-size: 2rem; color: #EF4444; display: block; margin-bottom: 12px;"></i>
                        <p style="color: var(--secondary-text); margin-bottom: 16px;">An error occurred while loading the leaderboard.</p>
                        <button onclick="window.location.reload()" style="padding: 10px 20px; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                            <i class='bx bx-refresh'></i> Retry
                        </button>
                    </td>
                </tr>
            `;
        });
});