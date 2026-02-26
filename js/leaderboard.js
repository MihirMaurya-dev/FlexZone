document.addEventListener('DOMContentLoaded', function() {
    const leaderboardBody = document.getElementById('leaderboard-body');
    leaderboardBody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 30px;"><div class="spinner"></div> Loading leaderboard...</td></tr>';
    window.apiFetch('../php/api/get_leaderboard.php')
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
                    const rankDisplay = index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : index + 1;
                    row.innerHTML = `
                        <td style="font-size: 1.2rem; font-weight: 600;">${rankDisplay}</td>
                        <td style="font-weight: 500;">${user.username || 'Anonymous'}</td> 
                        <td>${Math.round(user.total_duration / 60)}</td>
                        <td>${user.total_workouts || 0}</td>
                        <td>${user.total_calories || 0}</td>
                    `;
                    if (index < 3) row.style.background = 'rgba(59, 130, 246, 0.05)';
                    leaderboardBody.appendChild(row);
                });
                const userRankInfo = document.getElementById('user-rank-info');
                if (data.user_rank && userRankInfo) userRankInfo.innerHTML = `<p>Your rank: <strong>#${data.user_rank}</strong></p>`;
            } else {
                leaderboardBody.innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 30px; color: var(--secondary-text);">Could not load leaderboard: ${data.message || 'Unknown error'}</td></tr>`;
            }
        })
        .catch(error => {
            leaderboardBody.innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px;">
                        <i class='bx bx-error-circle' style="font-size: 2rem; color: #EF4444; display: block; margin-bottom: 12px;"></i>
                        <p style="color: var(--secondary-text); margin-bottom: 16px;">An error occurred while loading the leaderboard.</p>
                        <button onclick="window.location.reload()" class="btn btn-primary">Retry</button>
                    </td>
                </tr>
            `;
        });
});
