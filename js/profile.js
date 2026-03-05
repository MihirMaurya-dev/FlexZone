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
                window.handleFormSubmit(null, '../php/api/user/upload_avatar.php', () => {
                    loadProfileData();
                    window.showMessage('Avatar updated!', 'success');
                }, {
                    formData,
                    submitBtn: avatarBtn,
                    loadingText: "<i class='bx bx-loader-alt bx-spin'></i>"
                }).finally(() => {
                    avatarInput.value = '';
                });
            }
        });
    }
    const sidebarLinks = document.querySelectorAll('.settings-nav a');
    const mainContent = document.querySelector('.profile-content');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');
            if (href.startsWith('#')) {
                e.preventDefault();
                const targetId = href.substring(1);
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    sidebarLinks.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');
                    targetSection.scrollIntoView({
                        behavior: "smooth"
                    });
                }
            }
        });
    });
});
let userEquipment = [];
let userSettings = {};

function loadProfileData() {
    window.apiFetch('../php/api/user/get_profile_full.php').then(data => {
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
            window.showMessage(`Error loading profile data: ${data.message || 'The server returned an error.'}`);
        }
    }).catch(err => {
        window.showMessage(`A network error occurred. Unable to load your profile. Please check your connection and try again.`);
    });
}

function updateHeaderStats(user, stats) {
    document.getElementById('display-username').textContent = user.username || 'User';
    document.getElementById('display-email').textContent = user.email || '';
    document.getElementById('user-avatar').src = window.getAvatarPath(user.avatar);
    document.getElementById('streak-count').textContent = stats.streak_current || 0;
    document.getElementById('total-workouts').textContent = stats.total_workouts || 0;
    document.getElementById('last-workout-date').textContent = stats.last_workout || '--';
}

function renderHeatmap(activity) {
    const container = document.getElementById('activity-heatmap');
    if (!container) return;
    container.innerHTML = '';
    const daysToRender = 100;
    const today = new Date();
    for (let i = daysToRender - 1; i >= 0; i--) {
        const date = new Date(today);
        date.setDate(today.getDate() - i);
        const dateStr = date.toISOString().split('T')[0];
        const count = activity[dateStr] || 0;
        const level = Math.min(count, 4);
        const cell = document.createElement('div');
        cell.className = 'day-cell';
        if (level > 0) cell.setAttribute('data-level', level);
        cell.title = `${date.toLocaleDateString()}: ${count} workouts`;
        container.appendChild(cell);
    }
}

function renderBadges(badges) {
    const container = document.getElementById('badges-list');
    if (!container) return;
    container.innerHTML = badges.map(badge => ` <div class="badge-item ${badge.unlocked ? 'unlocked' : 'locked'}"> <i class='bx ${badge.icon} badge-icon'></i> <span style="font-size: 0.8rem; font-weight: 600; text-align: center;">${badge.name}</span> </div> `).join('');
}
const equipmentData = [{
    id: 'kettlebell',
    name: 'Kettlebell',
    icon: 'bx-dumbbell'
}, {
    id: 'bench',
    name: 'Bench',
    icon: 'bx-git-commit'
}, {
    id: 'barbell',
    name: 'Barbell',
    icon: 'bx-shopping-bag'
}, {
    id: 'dumbbell',
    name: 'Dumbbell',
    icon: 'bx-chair'
}, {
    id: 'pullup_bar',
    name: 'Pull-up Bar',
    icon: 'bx-menu'
}, {
    id: 'treadmill',
    name: 'Treadmill',
    icon: 'bx-run'
}, {
    id: 'bike',
    name: 'Exercise Bike',
    icon: 'bx-cycling'
}, {
    id: 'bands',
    name: 'Resistance Bands',
    icon: 'bx-infinite'
}];

function renderEquipmentGrid() {
    const grid = document.getElementById('equipment-grid');
    if (!grid) return;
    grid.innerHTML = equipmentData.map(item => ` <div class="equip-item" data-id="${item.id}" onclick="toggleEquipment('${item.id}')"> <i class='bx ${item.icon}'></i> <span>${item.name}</span> </div> `).join('');
}

function toggleEquipment(id) {
    const index = userEquipment.indexOf(id);
    if (index === -1) {
        userEquipment.push(id);
    } else {
        userEquipment.splice(index, 1);
    }
    updateGarageSelection();
}

function updateGarageSelection() {
    document.querySelectorAll('.equip-item').forEach(el => {
        const id = el.getAttribute('data-id');
        el.classList.toggle('selected', userEquipment.includes(id));
    });
}

function saveGarage() {
    const formData = new FormData();
    formData.append('equipment', JSON.stringify(userEquipment));
    window.handleFormSubmit(null, '../php/api/workouts/save_garage.php', () => {
        window.showMessage('Equipment saved successfully!', 'success');
    }, {
        formData
    });
}

function updateSettingsForm() {
    if (userSettings.units) {
        document.getElementById(`unit-${userSettings.units === 'kg' ? 'kg' : 'lbs'}`).checked = true;
        const weightLabel = document.getElementById('weight-label');
        if (weightLabel) weightLabel.textContent = `Weight (${userSettings.units})`;
    }
    if (userSettings.notif_workouts !== undefined) {
        document.querySelector('input[name="notif_workouts"]').checked = userSettings.notif_workouts;
    }
    if (userSettings.notif_weekly !== undefined) {
        document.querySelector('input[name="notif_weekly"]').checked = userSettings.notif_weekly;
    }
}

function saveSettings(e) {
    const form = document.getElementById('settings-form');
    const units = form.querySelector('input[name="units"]:checked')?.value || 'kg';
    const settings = {
        units: units,
        notif_workouts: form.querySelector('input[name="notif_workouts"]').checked,
        notif_weekly: form.querySelector('input[name="notif_weekly"]').checked
    };
    const formData = new FormData();
    formData.append('settings', JSON.stringify(settings));
    window.handleFormSubmit(e, '../php/api/user/save_settings.php', () => {
        window.showMessage('Settings saved!', 'success');
        userSettings = settings;
        localStorage.setItem('userSettings', JSON.stringify(settings));
        const weightLabel = document.getElementById('weight-label');
        if (weightLabel) weightLabel.textContent = `Weight (${units})`;
    }, {
        formData
    });
}

function exportData() {
    window.location.href = '../php/api/user/export_history.php';
}

function logWeight(e) {
    const weightInput = document.getElementById('weight-input');
    let weightVal = parseFloat(weightInput.value);
    
    if (!weightVal || weightVal <= 0) {
        window.showMessage("Please enter a valid weight.");
        return;
    }

    const units = window.getUserUnits();
    if (units === 'lbs') {
        weightVal = weightVal / 2.20462;
    }

    const formData = new FormData();
    formData.append('weight_kg', weightVal.toFixed(2));

    window.handleFormSubmit(e, '../php/api/user/log_weight.php', () => {
        window.showMessage('Weight logged successfully!', 'success');
        weightInput.value = '';
        loadProfileData();
    }, {
        formData,
        loadingText: 'Logging...'
    });
}
