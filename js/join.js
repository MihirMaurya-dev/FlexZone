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
    event.preventDefault();
    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;
    const submitBtn = event.target.querySelector('button[type="submit"]');
    
    if (!email || !password) {
        window.showMessage('Please fill in all fields');
        return;
    }
    
    // Show loading state
    const originalBtnContent = submitBtn.innerHTML;
    submitBtn.classList.add('loading');
    submitBtn.innerHTML = 'Logging in...';
    submitBtn.disabled = true;

    const formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);
    
    fetch('../php/auth/login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.href = 'home.php';
        } else {
            window.showMessage(data.message || 'Invalid credentials');
            // Reset button
            submitBtn.classList.remove('loading');
            submitBtn.innerHTML = originalBtnContent;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Login Error:', error);
        window.showMessage('An error occurred during login. Please try again.');
        // Reset button
        submitBtn.classList.remove('loading');
        submitBtn.innerHTML = originalBtnContent;
        submitBtn.disabled = false;
    });
}

function handleRegister(event) {
    event.preventDefault();
    const username = document.getElementById('registerUsername').value.trim();
    const email = document.getElementById('registerEmail').value.trim();
    const password = document.getElementById('registerPassword').value;
    const submitBtn = event.target.querySelector('button[type="submit"]');

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
    
    // Show loading state
    const originalBtnContent = submitBtn.innerHTML;
    submitBtn.classList.add('loading');
    submitBtn.innerHTML = 'Creating account...';
    submitBtn.disabled = true;

    const formData = new FormData();
    formData.append('username', username);
    formData.append('email', email);
    formData.append('password', password);
    
    fetch('../php/auth/signup.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.showMessage('Registration successful! Please login.', 'success');
            document.getElementById('registerForm').reset();
            prevSlide();
            document.getElementById('loginEmail').value = email;
            document.getElementById('loginPassword').focus();
        } else {
            window.showMessage(data.message || 'An unexpected error occurred');
        }
        // Reset button
        submitBtn.classList.remove('loading');
        submitBtn.innerHTML = originalBtnContent;
        submitBtn.disabled = false;
    })
    .catch(error => {
        console.error('Registration Error:', error);
        window.showMessage('An error occurred during registration. Please try again.');
        // Reset button
        submitBtn.classList.remove('loading');
        submitBtn.innerHTML = originalBtnContent;
        submitBtn.disabled = false;
    });
}
