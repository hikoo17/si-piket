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
    const isVisible = passwordInput.type === 'text';
    passwordInput.type = isVisible ? 'password' : 'text';
    passwordToggle.setAttribute('aria-label', isVisible ? 'Tampilkan kata sandi' : 'Sembunyikan kata sandi');
});

loginForm?.addEventListener('submit', () => {
    loginSubmit.disabled = true;
    loader?.classList.remove('hidden');
    submitLabel.textContent = 'Memproses...';
    submitArrow?.classList.add('hidden');
});
