document.addEventListener('DOMContentLoaded', () => {
    const dateInput = document.querySelector('input[name="log_date"]');
    if (dateInput) dateInput.valueAsDate = new Date();
    loadProgressHistory();
    const form = document.getElementById('progress-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Saving...';
            submitBtn.disabled = true;
            const formData = new FormData(form);
            fetch('../php/api/user/save_progress.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.showMessage('Progress saved!', 'success');
                    form.reset();
                    dateInput.valueAsDate = new Date();
                    loadProgressHistory();
                } else {
                    window.showMessage('Error: ' + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                window.showMessage('Network error occurred.');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});
function loadProgressHistory() {
    const container = document.getElementById('progress-timeline');
    fetch('../php/api/user/get_progress.php')
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                container.innerHTML = '';
                if (data.history.length === 0) {
                    container.innerHTML = '<p style="text-align: center; color: var(--secondary-text); padding: 20px;">No entries yet. Log your first measurement!</p>';
                    return;
                }
                data.history.forEach(entry => {
                    const date = new Date(entry.log_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
                    let photosHtml = '';
                    if (entry.photo_front) photosHtml += `<img src="../${entry.photo_front}" onclick="window.open(this.src)">`;
                    if (entry.photo_side) photosHtml += `<img src="../${entry.photo_side}" onclick="window.open(this.src)">`;
                    if (entry.photo_back) photosHtml += `<img src="../${entry.photo_back}" onclick="window.open(this.src)">`;
                    const item = document.createElement('div');
                    item.className = 'timeline-item';
                    item.innerHTML = `
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <strong style="color: var(--primary-color);">${date}</strong>
                            <span>${entry.weight_kg ? entry.weight_kg + 'kg' : ''}</span>
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