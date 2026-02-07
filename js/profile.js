document.addEventListener('DOMContentLoaded', () => {
    loadProfileData();
    renderEquipmentGrid();
    const avatarBtn = document.querySelector('.edit-avatar-btn');
    const avatarInput = document.getElementById('avatar-upload');
    if (avatarBtn && avatarInput) {
        avatarBtn.addEventListener('click', () => avatarInput.click());
        avatarInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const formData = new FormData();
                formData.append('avatar', this.files[0]);
                const originalIcon = avatarBtn.innerHTML;
                avatarBtn.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i>";
                fetch('../php/api/user/upload_avatar.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        loadProfileData();
                        window.showMessage('Avatar updated!', 'success');
                    } else {
                        window.showMessage(data.message || 'Upload failed');
                    }
                })
                .catch(err => {
                    console.error('Avatar upload error:', err);
                    window.showMessage('Upload failed due to network error');
                })
                .finally(() => {
                    avatarBtn.innerHTML = originalIcon;
                    avatarInput.value = '';
                });
            }
        });
    }
    document.querySelectorAll('.settings-nav a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            if (targetElement) {
                document.querySelectorAll('.settings-nav a').forEach(a => a.classList.remove('active'));
                this.classList.add('active');
                const headerOffset = 100;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                window.scrollTo({
                    top: offsetPosition,
                    behavior: "smooth"
                });
            }
        });
    });
});


let userEquipment = [];
let userSettings = {};
function loadProfileData() {
    fetch('../php/api/user/get_profile_full.php')
        .then(res => {
            if (!res.ok) {
                throw new Error(`Network response was not ok, status: ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            if (data.status === 'success') {
                updateHeaderStats(data.user, data.stats);
                renderHeatmap(data.activity);
                renderBadges(data.badges);
                if (data.garage && Array.isArray(data.garage)) {
                    userEquipment = data.garage;
                    updateGarageSelection();
                }
                if (data.settings) {
                    userSettings = data.settings;
                    updateSettingsForm();
                }
            } else {
                console.error("Failed to load profile:", data.message);
                window.showMessage(`Error loading profile data: ${data.message || 'The server returned an error.'}`);
            }
        })
        .catch(err => {
            console.error("Network error:", err);
            window.showMessage(`A network error occurred. Unable to load your profile. Please check your connection and try again. Details: ${err.message}`);
        });
}
function updateHeaderStats(user, stats) {
    document.getElementById('display-username').textContent = user.username || 'User';
    document.getElementById('display-email').textContent = user.email || '';
    if (user.avatar) {
        let avatarSrc;
        if (user.avatar.startsWith('http')) {
            avatarSrc = user.avatar;
        } else if (user.avatar.startsWith('assets/')) {
            avatarSrc = '../' + user.avatar;
        } else {
            avatarSrc = '../assets/' + user.avatar;
        }
        document.getElementById('user-avatar').src = avatarSrc;
    } else {
        document.getElementById('user-avatar').src = '../assets/default_avatar.png';
    }
    document.getElementById('streak-count').textContent = stats.streak_current || 0;
    document.getElementById('total-workouts').textContent = stats.total_workouts || 0;
    document.getElementById('last-workout-date').textContent = stats.last_workout || '--';
}
function renderHeatmap(activityData) {
    const container = document.getElementById('activity-heatmap');
    container.innerHTML = '';
    let tooltip = document.getElementById('heatmap-tooltip');
    if (!tooltip) {
        tooltip = document.createElement('div');
        tooltip.id = 'heatmap-tooltip';
        tooltip.className = 'custom-tooltip';
        document.body.appendChild(tooltip);
    }
    const today = new Date();
    const totalDays = 53 * 7; 
    const startDate = new Date();
    startDate.setDate(today.getDate() - totalDays);
    for (let i = 0; i < totalDays; i++) {
        const d = new Date(startDate);
        d.setDate(startDate.getDate() + i);
        const dateStr = d.toISOString().split('T')[0];
        const count = activityData[dateStr] || 0;
        let level = 0;
        if (count >= 1) level = 1;
        if (count >= 2) level = 2;
        if (count >= 4) level = 3;
        if (count >= 6) level = 4;
        const cell = document.createElement('div');
        cell.className = 'day-cell';
        cell.dataset.level = level;
        cell.addEventListener('mouseenter', (e) => {
            const rect = cell.getBoundingClientRect();
            tooltip.textContent = `${dateStr}: ${count} workouts`;
            tooltip.style.left = `${rect.left + window.scrollX - 40}px`;
            tooltip.style.top = `${rect.top + window.scrollY - 30}px`;
            tooltip.classList.add('visible');
        });
        cell.addEventListener('mouseleave', () => {
            tooltip.classList.remove('visible');
        });
        container.appendChild(cell);
    }
}
function renderBadges(badges) {
    const container = document.getElementById('badges-list');
    container.innerHTML = '';
    badges.forEach(badge => {
        const div = document.createElement('div');
        div.className = `badge-item ${badge.unlocked ? 'unlocked' : 'locked'}`;
        div.innerHTML = `
            <div class="badge-icon"><i class='bx ${badge.icon}'></i></div>
            <span>${badge.name}</span>
        `;
        container.appendChild(div);
    });
}
const AVAILABLE_EQUIPMENT = [
    { id: 'dumbbell', name: 'Dumbbells', icon: 'bx-dumbbell' },
    { id: 'barbell', name: 'Barbell', icon: 'bx-git-commit' },
    { id: 'kettlebell', name: 'Kettlebell', icon: 'bx-shopping-bag' },
    { id: 'bench', name: 'Bench', icon: 'bx-chair' },
    { id: 'pullup_bar', name: 'Pull-up Bar', icon: 'bx-menu' },
    { id: 'treadmill', name: 'Treadmill', icon: 'bx-run' },
    { id: 'bike', name: 'Bike', icon: 'bx-cycling' },
    { id: 'bands', name: 'Resistance Bands', icon: 'bx-infinite' }
];
function renderEquipmentGrid() {
    const grid = document.getElementById('equipment-grid');
    grid.innerHTML = '';
    AVAILABLE_EQUIPMENT.forEach(item => {
        const div = document.createElement('div');
        div.className = 'equip-item';
        div.dataset.id = item.id;
        div.onclick = () => toggleEquipment(item.id, div);
        div.innerHTML = `
            <i class='bx ${item.icon}'></i>
            <span>${item.name}</span>
        `;
        grid.appendChild(div);
    });
}
function toggleEquipment(id, element) {
    const index = userEquipment.indexOf(id);
    if (index === -1) {
        userEquipment.push(id);
        element.classList.add('selected');
    } else {
        userEquipment.splice(index, 1);
        element.classList.remove('selected');
    }
}
function updateGarageSelection() {
    const items = document.querySelectorAll('.equip-item');
    items.forEach(el => {
        if (userEquipment.includes(el.dataset.id)) {
            el.classList.add('selected');
        } else {
            el.classList.remove('selected');
        }
    });
}
function saveGarage() {
    const formData = new FormData();
    formData.append('equipment', JSON.stringify(userEquipment));
    fetch('../php/api/workouts/save_garage.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            window.showMessage('Equipment saved successfully!', 'success');
        } else {
            window.showMessage(data.message || 'Failed to save equipment');
        }
    })
    .catch(err => {
        console.error('Error saving garage:', err);
        window.showMessage('Network error. Changes saved locally only.', 'success');
    });
}
function updateSettingsForm() {
    if (userSettings.units) {
        if (userSettings.units === 'kg') {
            document.getElementById('unit-kg').checked = true;
        } else {
            document.getElementById('unit-lbs').checked = true;
        }
    }
    if (userSettings.notif_workouts !== undefined) {
        document.querySelector('input[name="notif_workouts"]').checked = userSettings.notif_workouts;
    }
    if (userSettings.notif_weekly !== undefined) {
        document.querySelector('input[name="notif_weekly"]').checked = userSettings.notif_weekly;
    }
}
function saveSettings(e) {
    e.preventDefault();
    const form = document.getElementById('settings-form');
    const units = form.querySelector('input[name="units"]:checked')?.value || 'kg';
    const notif_workouts = form.querySelector('input[name="notif_workouts"]').checked;
    const notif_weekly = form.querySelector('input[name="notif_weekly"]').checked;
    const settings = {
        units: units,
        notif_workouts: notif_workouts,
        notif_weekly: notif_weekly
    };
    const postData = new FormData();
    postData.append('settings', JSON.stringify(settings));
    fetch('../php/api/user/save_settings.php', {
        method: 'POST',
        body: postData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            window.showMessage('Settings saved!', 'success');
            userSettings = settings;
        } else {
            window.showMessage('Settings saved locally.', 'success');
        }
    })
    .catch(err => {
        console.error('Error saving settings:', err);
        window.showMessage('Error saving settings.');
    });
}
function exportData() {
    window.location.href = '../php/api/user/export_history.php';
}
function logWeight(e) {
    e.preventDefault();
    const weightInput = document.getElementById('weight-input');
    const weight = weightInput.value;
    if (!weight || weight <= 0) {
        window.showMessage("Please enter a valid weight.");
        return;
    }
    const formData = new FormData();
    formData.append('weight_kg', weight);
    fetch('../php/api/user/log_weight.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            window.showMessage('Weight logged successfully!', 'success');
            weightInput.value = '';
            loadProfileData();
        } else {
            window.showMessage(`Error logging weight: ${data.message}`);
        }
    })
    .catch(err => {
        console.error('Error logging weight:', err);
        window.showMessage('An error occurred while logging weight.');
    });
}