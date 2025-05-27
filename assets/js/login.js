$(document).ready(function () {
    const hasRegisterError = document.body.dataset.registerError === 'true';

    function determineFormToShow() {
        const urlHash = window.location.hash;
        const urlParams = new URLSearchParams(window.location.search);
        const showParam = urlParams.get('show');

        const shouldShowRegister = urlHash === '#register' || showParam === 'register' || hasRegisterError;

        if (shouldShowRegister) {
            showRegisterForm();
        } else {
            showLoginForm();
        }
    }

    determineFormToShow();
    setTimeout(determineFormToShow, 50);
});

function showRegisterForm() {
    $('#loginForm').addClass('hidden');
    $('#registerForm').removeClass('hidden');
    window.location.hash = 'register';
}

function showLoginForm() {
    $('#registerForm').addClass('hidden');
    $('#loginForm').removeClass('hidden');
    if (window.location.hash === '#register') {
        history.replaceState(null, null, window.location.pathname);
    }
}

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const toggle = document.getElementById(fieldId + 'Toggle');
    if (field.type === 'password') {
        field.type = 'text';
        toggle.classList.remove('fa-eye');
        toggle.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        toggle.classList.remove('fa-eye-slash');
        toggle.classList.add('fa-eye');
    }
}
