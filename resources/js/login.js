document.addEventListener('DOMContentLoaded', function () {
    const tabLogin = document.getElementById('tab-login');
    const tabRegister = document.getElementById('tab-register');
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');

    function showLogin() {
        loginForm.classList.remove('hidden');
        registerForm.classList.add('hidden');

        tabLogin.classList.add('border-secondary', 'text-secondary');
        tabLogin.classList.remove('border-transparent', 'text-gray-500');

        tabRegister.classList.add('border-transparent', 'text-gray-500');
        tabRegister.classList.remove('border-secondary', 'text-secondary');
    }

    function showRegister() {
        registerForm.classList.remove('hidden');
        loginForm.classList.add('hidden');

        tabRegister.classList.add('border-secondary', 'text-secondary');
        tabRegister.classList.remove('border-transparent', 'text-gray-500');

        tabLogin.classList.add('border-transparent', 'text-gray-500');
        tabLogin.classList.remove('border-secondary', 'text-secondary');
    }

    tabLogin.addEventListener('click', showLogin);
    tabRegister.addEventListener('click', showRegister);

    document.getElementById('link-to-register').addEventListener('click', showRegister);
    document.getElementById('link-to-login').addEventListener('click', showLogin);

    const toggleBtn = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    const toggleIcon = toggleBtn.querySelector('.material-icons');

    toggleBtn.addEventListener('click', function () {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.textContent = 'visibility';
        } else {
            passwordInput.type = 'password';
            toggleIcon.textContent = 'visibility_off';
        }
    });
});