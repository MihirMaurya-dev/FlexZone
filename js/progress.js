document.addEventListener('DOMContentLoaded', () => {
    const dateInput = document.querySelector('input[name="log_date"]');
    if (dateInput) dateInput.valueAsDate = new Date();
    loadProgressHistory();
    const form = document.getElementById('progress-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            window.handleFormSubmit(e, '../php/api/user/save_progress.php', () => {
                window.showMessage('Progress saved!', 'success');
                form.reset();
                if (dateInput) dateInput.valueAsDate = new Date();
                loadProgressHistory();
            }, {
                loadingText: 'Saving...'
            });
        });
    const overloadForm = document.getElementById('overload-form');
    if (overloadForm) {
        overloadForm.addEventListener('submit', function(e) {
            window.handleFormSubmit(e, '../php/api/user/save_overload.php', () => {
                window.showMessage('Exercise logged!', 'success');
                overloadForm.reset();
                loadOverloadChart();
            }, { loadingText: 'Logging...' });
        });
    }

    const overloadFilter = document.getElementById('overload-filter');
    if (overloadFilter) {
        overloadFilter.addEventListener('change', loadOverloadChart);
    }
    
    setTimeout(loadOverloadChart, 500);
});

let overloadChartInstance = null;

function loadOverloadChart() {
    const filterEl = document.getElementById('overload-filter');
    const canvas = document.getElementById('overloadChart');
    if (!filterEl || !canvas) return;
    
    const exercise = filterEl.value;
    
    window.apiFetch(`../php/api/user/get_overload.php?exercise=${encodeURIComponent(exercise)}`)
        .then(data => {
            if (data.status === 'success') {
                const history = data.history || [];
                
                if (history.length === 0) {
                    canvas.style.display = 'none';
                    if (overloadChartInstance) overloadChartInstance.destroy();
                    let msg = canvas.parentNode.querySelector('.chart-error-message');
                    if (!msg) {
                        msg = document.createElement('div');
                        msg.className = 'chart-error-message';
                        msg.style.textAlign = 'center';
                        msg.style.padding = '20px';
                        canvas.parentNode.appendChild(msg);
                    }
                    msg.textContent = `No data for ${exercise} yet. Log your first set!`;
                    return;
                }
                
                canvas.style.display = 'block';
                const msg = canvas.parentNode.querySelector('.chart-error-message');
                if (msg) msg.remove();

                const labels = history.map(item => new Date(item.log_date).toLocaleDateString('en-GB', {day: 'numeric', month: 'short'}));
                const rmData = history.map(item => item.estimated_1rm);
                const actualData = history.map(item => item.weight);

                const primaryColor = getComputedStyle(document.body).getPropertyValue('--primary-color').trim() || '#3B82F6';
                const secondaryColor = '#10B981';

                if (overloadChartInstance) overloadChartInstance.destroy();
                
                if (typeof Chart === 'undefined') return;

                overloadChartInstance = new Chart(canvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Estimated 1RM',
                                data: rmData,
                                borderColor: primaryColor,
                                backgroundColor: primaryColor + '20',
                                fill: true,
                                tension: 0.2
                            },
                            {
                                label: 'Actual Weight',
                                data: actualData,
                                borderColor: secondaryColor,
                                borderDash: [5, 5],
                                fill: false,
                                tension: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        scales: {
                            y: { beginAtZero: false }
                        }
                    }
                });
            }
        });
}

function loadProgressHistory() {
    const container = document.getElementById('progress-timeline');
    window.apiFetch('../php/api/user/get_progress.php').then(data => {
        if (data.status === 'success') {
            container.innerHTML = '';
            if (data.history.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: var(--secondary-text); padding: 20px;">No entries yet. Log your first measurement!</p>';
                return;
            }
            data.history.forEach(entry => {
                const date = new Date(entry.log_date).toLocaleDateString('en-GB', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                });
                const photosHtml = [entry.photo_front, entry.photo_side, entry.photo_back].filter(p => p).map(p => ` <img src="../${p}" onclick="window.open(this.src)"> `).join('');
                const item = document.createElement('div');
                item.className = 'timeline-item';
                item.innerHTML = `
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <strong style="color: var(--primary-color);">${date}</strong>
                        <span>${window.formatWeight(entry.weight_kg)}</span>
                    </div>
                    <div style="font-size: 0.9em; display: grid; grid-template-columns: 1fr 1fr; gap: 4px; color: var(--secondary-text);">
                        ${entry.chest_cm ? `<span>Chest: ${entry.chest_cm}cm</span>` : ''}
                        ${entry.waist_cm ? `<span>Waist: ${entry.waist_cm}cm</span>` : ''}
                        ${entry.arms_cm ? `<span>Arms: ${entry.arms_cm}cm</span>` : ''}
                        ${entry.thighs_cm ? `<span>Thighs: ${entry.thighs_cm}cm</span>` : ''}
                    </div>
                    ${photosHtml ? `<div class="timeline-photos">${photosHtml}</div>` : ''}
                `;
                container.appendChild(item);
            });
        }
    });
}
