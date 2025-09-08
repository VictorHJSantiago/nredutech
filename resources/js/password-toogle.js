
function initializePasswordToggles() {
    const toggleIcons = document.querySelectorAll('.toggle-password');
    toggleIcons.forEach(function (icon) {
        icon.addEventListener('click', function () {
            const passwordInput = icon.previousElementSibling;
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            if (type === 'password') {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePasswordToggles);
} else {
    initializePasswordToggles();
}