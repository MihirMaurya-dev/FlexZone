let currentSlide = 0;
const sliderWrapper = document.getElementById('sliderWrapper');
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');

function updateSlider() {
    if (sliderWrapper) {
        sliderWrapper.style.transform = `translateX(-${currentSlide * 50}%)`;
    }
    if (slides) {
        slides.forEach((slide, index) => {
            slide.classList.toggle('active', index === currentSlide);
        });
    }
    if (dots) {
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide);
        });
    }
}

function nextSlide() {
    if (currentSlide < slides.length - 1) {
        currentSlide++;
        updateSlider();
    }
}

function prevSlide() {
    if (currentSlide > 0) {
        currentSlide--;
        updateSlider();
    }
}

function goToSlide(index) {
    currentSlide = index;
    updateSlider();
}

function handleLogin(event) {
    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;
    if (!email || !password) {
        window.showMessage('Please fill in all fields');
        return;
    }
    window.handleFormSubmit(event, '../php/auth/login.php', () => {
        window.location.href = 'home.php';
    }, {
        loadingText: 'Logging in...'
    });
}

function handleRegister(event) {
    const username = document.getElementById('registerUsername').value.trim();
    const email = document.getElementById('registerEmail').value.trim();
    const password = document.getElementById('registerPassword').value;
    if (!username || username.length < 3) {
        window.showMessage('Username must be at least 3 characters');
        return;
    }
    if (!email) {
        window.showMessage('Please enter a valid email');
        return;
    }
    if (!password || password.length < 8) {
        window.showMessage('Password must be at least 8 characters');
        return;
    }
    window.handleFormSubmit(event, '../php/auth/signup.php', (data) => {
        window.showMessage('Registration successful! Please login.', 'success');
        document.getElementById('registerForm').reset();
        prevSlide();
        document.getElementById('loginEmail').value = email;
        document.getElementById('loginPassword').focus();
    }, {
        loadingText: 'Creating account...'
    });
}
