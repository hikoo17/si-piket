import { createIcons, LoaderCircle } from 'lucide';

createIcons({ icons: { LoaderCircle } });

const passwordInput = document.getElementById('password');
const passwordToggle = document.getElementById('password-toggle');
const loginForm = document.getElementById('login-form');
const loginSubmit = document.getElementById('login-submit');
const loader = loginSubmit?.querySelector('svg.lucide-loader-circle');
const submitLabel = loginSubmit?.querySelector('[data-submit-label]');
const submitArrow = loginSubmit?.querySelector('[data-submit-arrow]');

passwordToggle?.addEventListener('click', () => {
    const isPassword = passwordInput.type === 'password';
    passwordInput.type = isPassword ? 'text' : 'password';
    passwordToggle.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');

    const eyeIcon = document.getElementById('icon-eye');
    const eyeSlashIcon = document.getElementById('icon-eye-slash');

    if (isPassword) {
        eyeIcon?.classList.remove('hidden');
        eyeSlashIcon?.classList.add('hidden');
    } else {
        eyeIcon?.classList.add('hidden');
        eyeSlashIcon?.classList.remove('hidden');
    }
});

loginForm?.addEventListener('submit', () => {
    loginSubmit.disabled = true;
    loader?.classList.remove('hidden');
    submitLabel.textContent = 'Memproses...';
    submitArrow?.classList.add('hidden');
});
