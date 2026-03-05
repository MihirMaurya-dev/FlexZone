// Utility functions - available immediately
window.showLoading = function(elementId) {
    const el = document.getElementById(elementId);
    if (el) {
        el.innerHTML = '<div class="spinner"></div>';
    }
};

window.getAvatarPath = function(avatar) {
    if (!avatar) return '../assets/default_avatar.png';
    if (avatar.startsWith('http')) return avatar;
    if (avatar.startsWith('assets/')) return '../' + avatar;
    return '../assets/' + avatar;
};

window.getUserUnits = function() {
    try {
        const settings = JSON.parse(localStorage.getItem('userSettings') || '{}');
        return settings.units || 'kg';
    } catch (e) {
        return 'kg';
    }
};

window.formatWeight = function(weightKg, units = null) {
    if (weightKg === null || weightKg === undefined || weightKg === '') return '--';
    const targetUnits = units || window.getUserUnits();
    if (targetUnits === 'lbs') {
        const weightLbs = parseFloat(weightKg) * 2.20462;
        return `${weightLbs.toFixed(1)} lbs`;
    }
    return `${parseFloat(weightKg).toFixed(1)} kg`;
};

window.apiFetch = async function(url, options = {}) {
    try {
        const response = await fetch(url, options);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return await response.json();
    } catch (error) {
        console.error(`API Fetch Error [${url}]:`, error);
        throw error;
    }
};

window.handleFormSubmit = async function(event, url, successCallback, options = {}) {
    if (event) event.preventDefault();
    
    // Correctly identify form
    const form = event ? (event.target.tagName === 'FORM' ? event.target : event.target.form || event.currentTarget) : null;
    const submitBtn = options.submitBtn || (form ? form.querySelector('button[type="submit"]') : null);
    
    const originalContent = submitBtn ? submitBtn.innerHTML : null;
    if (submitBtn) {
        submitBtn.disabled = true;
        if (options.loadingText) submitBtn.innerHTML = options.loadingText;
    }

    try {
        const formData = options.formData || (form ? new FormData(form) : new FormData());
        const data = await window.apiFetch(url, {
            method: 'POST',
            body: formData
        });

        if (data.status === 'success') {
            if (successCallback) successCallback(data);
        } else {
            window.showMessage(data.message || 'An error occurred');
        }
    } catch (error) {
        window.showMessage('Network error. Please try again.');
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalContent;
        }
    }
};

window.showMessage = function(message, type = 'error', duration = 5000) {
    let container = document.getElementById('global-message-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'global-message-container';
        container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; width: 90%; max-width: 350px; pointer-events: none; display: flex; flex-direction: column; align-items: flex-end;';
        document.body.appendChild(container);
    }
    const messageEl = document.createElement('div');
    messageEl.className = `auth-message ${type}`;
    messageEl.style.pointerEvents = 'auto';
    messageEl.style.marginBottom = '10px';
    messageEl.style.boxShadow = '0 4px 15px rgba(0,0,0,0.2)';
    messageEl.textContent = message;
    container.appendChild(messageEl);
    setTimeout(() => {
        messageEl.classList.add('hiding');
        setTimeout(() => {
            messageEl.remove();
        }, 300);
    }, duration);
};

document.addEventListener('DOMContentLoaded', function() {
    const currentTheme = localStorage.getItem('theme');
    const toggleSwitch = document.getElementById('checkbox');
    const htmlEl = document.documentElement;
    
    const applyTheme = (theme) => {
        if (theme === 'dark') {
            htmlEl.classList.add('dark');
        } else {
            htmlEl.classList.remove('dark');
        }
    };

    if (toggleSwitch) {
        if (currentTheme) {
            toggleSwitch.checked = (currentTheme === 'dark');
        } else {
            toggleSwitch.checked = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
    }

    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    const handleSystemThemeChange = (e) => {
        if (!localStorage.getItem('theme')) {
            const newTheme = e.matches ? 'dark' : 'light';
            applyTheme(newTheme);
            if (toggleSwitch) toggleSwitch.checked = e.matches;
        }
    };
    mediaQuery.addEventListener('change', handleSystemThemeChange);

    if (toggleSwitch) {
        toggleSwitch.addEventListener('change', function() {
            if (this.checked) {
                applyTheme('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                applyTheme('light');
                localStorage.setItem('theme', 'light');
            }
        });
    }

    const headerAvatar = document.getElementById('header-avatar');
    if (headerAvatar) {
        window.apiFetch('../php/api/user/get_profile_full.php').then(data => {
            if (data.status === 'success') {
                if (data.user.avatar) {
                    headerAvatar.src = window.getAvatarPath(data.user.avatar);
                }
                if (data.settings) {
                    localStorage.setItem('userSettings', JSON.stringify(data.settings));
                }
            }
        }).catch(() => {});
    }

    const menuIcon = document.getElementById('menu-icon');
    const navbar = document.querySelector('.navbar');
    if (menuIcon && navbar) {
        menuIcon.addEventListener('click', () => {
            navbar.classList.toggle('active');
            if (navbar.classList.contains('active')) {
                menuIcon.classList.remove('bx-menu');
                menuIcon.classList.add('bx-x');
            } else {
                menuIcon.classList.remove('bx-x');
                menuIcon.classList.add('bx-menu');
            }
        });
        navbar.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if (navbar.classList.contains('active')) {
                    navbar.classList.remove('active');
                    menuIcon.classList.remove('bx-x');
                    menuIcon.classList.add('bx-menu');
                }
            });
        });
        document.addEventListener('click', (event) => {
            const isClickInsideNavbar = navbar.contains(event.target);
            const isClickOnMenuIcon = menuIcon.contains(event.target);
            if (!isClickInsideNavbar && !isClickOnMenuIcon && navbar.classList.contains('active')) {
                navbar.classList.remove('active');
                menuIcon.classList.remove('bx-x');
                menuIcon.classList.add('bx-menu');
            }
        });
    }
});
