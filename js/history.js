document.addEventListener('DOMContentLoaded', function() {
    const historyBody = document.getElementById('history-body');
    const heatmapContainer = document.getElementById('calendar-heatmap');
    const filterDate = document.getElementById('filter-date');
    const filterType = document.getElementById('filter-type');
    const clearFiltersBtn = document.getElementById('clear-filters');
    const prevPageBtn = document.getElementById('prev-page');
    const nextPageBtn = document.getElementById('next-page');
    const pageInfo = document.getElementById('page-info');

    let currentPage = 1;
    const limit = 20;

    function loadHeatmap() {
        window.apiFetch('../php/api/workouts/get_heatmap_data.php?months=6').then(data => {
            if (data.status === 'success') {
                renderHeatmap(data.heatmap);
            }
        });
    }

    function renderHeatmap(heatmapData) {
        heatmapContainer.innerHTML = '';
        
        // Generate last 6 months of dates (approx 180 days)
        const today = new Date();
        const dates = [];
        for (let i = 180; i >= 0; i--) {
            const d = new Date(today);
            d.setDate(today.getDate() - i);
            dates.push(d);
        }

        dates.forEach(dateObj => {
            const dateStr = dateObj.toISOString().split('T')[0];
            const dataForDate = heatmapData[dateStr];
            
            const cell = document.createElement('div');
            cell.className = 'heatmap-cell';
            cell.title = dateObj.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });

            if (dataForDate) {
                const dur = dataForDate.duration / 60; // mins
                cell.title += ` - ${dataForDate.count} workouts (${dur.toFixed(0)} mins)`;
                
                if (dur > 60) cell.classList.add('level-4');
                else if (dur > 30) cell.classList.add('level-3');
                else if (dur > 15) cell.classList.add('level-2');
                else cell.classList.add('level-1');
            } else {
                cell.title += ' - No workouts';
            }

            // Click to filter by date
            cell.addEventListener('click', () => {
                filterDate.value = dateStr;
                currentPage = 1;
                loadHistory();
            });

            heatmapContainer.appendChild(cell);
        });
    }

    function loadHistory() {
        const dateVal = filterDate.value;
        const typeVal = filterType.value;
        let url = `../php/api/workouts/get_workout_history.php?limit=${limit}&page=${currentPage}`;
        if (dateVal) url += `&date=${encodeURIComponent(dateVal)}`;
        if (typeVal) url += `&type=${encodeURIComponent(typeVal)}`;

        historyBody.innerHTML = '<tr><td colspan="4" style="text-align:center;"><div class="spinner"></div> Loading...</td></tr>';

        window.apiFetch(url).then(data => {
            if (data.status === "success") {
                historyBody.innerHTML = '';
                if (data.history.length > 0) {
                    data.history.forEach(log => {
                        const row = document.createElement('tr');
                        const formattedDate = new Date(log.log_date.replace(' ', 'T') + 'Z').toLocaleDateString('en-GB', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });
                        row.innerHTML = `
                            <td>${formattedDate}</td>
                            <td>${log.workout_name || 'Workout'}</td>
                            <td>${(log.duration_seconds / 60).toFixed(2)}</td>
                            <td>${log.calories_burned || 0}</td>
                        `;
                        historyBody.appendChild(row);
                    });
                } else {
                    historyBody.innerHTML = '<tr><td colspan="4" style="text-align:center;">No workouts found for these filters.</td></tr>';
                }

                // Update pagination
                const totalPages = Math.ceil(data.total / limit);
                pageInfo.textContent = `Page ${currentPage} of ${totalPages || 1}`;
                prevPageBtn.disabled = currentPage <= 1;
                nextPageBtn.disabled = currentPage >= totalPages || totalPages === 0;

            } else {
                historyBody.innerHTML = `<tr><td colspan="4">Error loading history: ${data.message || 'Unknown error'}</td></tr>`;
            }
        }).catch(() => {
            historyBody.innerHTML = '<tr><td colspan="4">An error occurred while loading your history. Please try again later.</td></tr>';
        });
    }

    // Event Listeners
    if (filterDate) filterDate.addEventListener('change', () => { currentPage = 1; loadHistory(); });
    if (filterType) filterType.addEventListener('change', () => { currentPage = 1; loadHistory(); });
    
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', () => {
            filterDate.value = '';
            filterType.value = '';
            currentPage = 1;
            loadHistory();
        });
    }

    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                loadHistory();
            }
        });
    }

    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', () => {
            currentPage++;
            loadHistory();
        });
    }

    // Initial Load
    loadHeatmap();
    loadHistory();
});
