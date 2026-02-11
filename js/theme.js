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
    window.showLoading = function(elementId) {
        const el = document.getElementById(elementId);
        if (el) {
            el.innerHTML = '<div class="spinner"></div>';
        }
    };

    window.showMessage = function(message, type = 'error', duration = 5000) {
        let container = document.getElementById('global-message-container');
        
        // Create container if it doesn't exist
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

        // Auto-hide
        setTimeout(() => {
            messageEl.classList.add('hiding');
            setTimeout(() => {
                messageEl.remove();
            }, 300);
        }, duration);
    };
    const headerAvatar = document.getElementById('header-avatar');
    if (headerAvatar) {
        fetch('../php/api/user/get_profile_full.php')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                                if (data.user.avatar) {
                                    let avatarSrc;
                                    if (data.user.avatar.startsWith('http')) {
                                        avatarSrc = data.user.avatar;
                                    } else if (data.user.avatar.startsWith('assets/')) {
                                        avatarSrc = '../' + data.user.avatar;
                                    } else {
                                        avatarSrc = '../assets/' + data.user.avatar;
                                    }
                                    headerAvatar.src = avatarSrc;
                                }
                }
            })
            .catch(err => console.warn("Could not load header profile info"));
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