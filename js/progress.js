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
    }
});

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
