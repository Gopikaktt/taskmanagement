@extends('layouts.base')

@section('title', 'Staff Portal Login - TaskFlow')

@section('navbar')
<nav class="navbar">
    <div class="nav-container">
        <a href="{{ route('home') }}" class="brand-logo">
            <div class="brand-icon" style="background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%);">
                <i class="fa-solid fa-check-double"></i>
            </div>
            <span>TaskFlow</span>
        </a>
        <a href="{{ route('admin.login') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-shield-halved"></i> Admin Login &rarr;
        </a>
    </div>
</nav>
@endsection

@section('content')
<div style="max-width: 440px; margin: 3.5rem auto;">
    <div class="card" style="padding: 2.25rem;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #ffffff; margin-bottom: 1rem; box-shadow: var(--shadow-sm);">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <h2 style="font-size: 1.5rem; font-weight: 800; letter-spacing: -0.025em; color: var(--text-primary);">Staff Portal</h2>
            <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 0.25rem;">Sign in to your staff member account</p>
        </div>

        <form action="{{ route('staff.login.submit') }}" method="POST" autocomplete="on">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" autocomplete="username" value="{{ old('email', $rememberedEmail ?? '') }}" required autofocus>
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" autocomplete="current-password" value="{{ old('password', $rememberedPassword ?? '') }}" required>
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <label for="remember" class="form-check" style="font-size: 0.85rem; color: var(--text-secondary); cursor: pointer; user-select: none;">
                    <input type="checkbox" id="remember" name="remember" value="1" class="form-check-input" {{ old('remember', !empty($rememberedEmail)) ? 'checked' : '' }}>
                    <span>Remember me</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 0.95rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="fa-solid fa-right-to-bracket"></i> Sign In
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const rememberCheckbox = document.getElementById('remember');
    const form = emailInput ? emailInput.closest('form') : null;
    const STORAGE_KEY_EMAIL = 'taskflow_remembered_staff_email';
    const STORAGE_KEY_PASS = 'taskflow_remembered_staff_pass';

    function extractCleanEmail(val) {
        if (!val || typeof val !== 'string') return '';
        // If pipe format exists (e.g. HMAC hash|email), take the part after the last pipe
        if (val.includes('|')) {
            val = val.split('|').pop();
        }
        const match = val.match(/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/);
        return match ? match[0] : '';
    }

    // 1. Clean up any corrupted or signed value currently in the email input
    if (emailInput && emailInput.value) {
        emailInput.value = extractCleanEmail(emailInput.value);
    }

    // 2. Read and clean stored email
    const storedEmail = extractCleanEmail(localStorage.getItem(STORAGE_KEY_EMAIL));
    if (storedEmail) {
        localStorage.setItem(STORAGE_KEY_EMAIL, storedEmail);
    } else {
        localStorage.removeItem(STORAGE_KEY_EMAIL);
    }

    // 3. Pre-fill email
    if (emailInput) {
        if (!emailInput.value && storedEmail) {
            emailInput.value = storedEmail;
        } else if (emailInput.value) {
            localStorage.setItem(STORAGE_KEY_EMAIL, emailInput.value);
        }
    }

    // 4. Pre-fill password if remembered
    const storedPass = localStorage.getItem(STORAGE_KEY_PASS);
    if (passwordInput && !passwordInput.value && storedPass) {
        try {
            passwordInput.value = atob(storedPass);
        } catch (e) {
            passwordInput.value = storedPass;
        }
    }

    // 5. Ensure "Remember me" checkbox is checked if credentials are saved
    if (rememberCheckbox) {
        if ((emailInput && emailInput.value) || storedPass) {
            rememberCheckbox.checked = true;
        }
    }

    // 6. Notify browser of programmatic value changes
    if (emailInput && emailInput.value) {
        emailInput.dispatchEvent(new Event('input', { bubbles: true }));
        emailInput.dispatchEvent(new Event('change', { bubbles: true }));
    }
    if (passwordInput && passwordInput.value) {
        passwordInput.dispatchEvent(new Event('input', { bubbles: true }));
        passwordInput.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // 7. On form submit, update or clear remembered credentials
    if (form) {
        form.addEventListener('submit', function () {
            if (rememberCheckbox && rememberCheckbox.checked) {
                const cleanMail = extractCleanEmail(emailInput ? emailInput.value.trim() : '');
                if (cleanMail) {
                    localStorage.setItem(STORAGE_KEY_EMAIL, cleanMail);
                }
                if (passwordInput && passwordInput.value) {
                    try {
                        localStorage.setItem(STORAGE_KEY_PASS, btoa(passwordInput.value));
                    } catch (e) {
                        localStorage.setItem(STORAGE_KEY_PASS, passwordInput.value);
                    }
                }
            } else {
                localStorage.removeItem(STORAGE_KEY_EMAIL);
                localStorage.removeItem(STORAGE_KEY_PASS);
            }
        });
    }
});
</script>
@endpush
