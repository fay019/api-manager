@extends('setup.layout')

@section('content')
<div class="setup-header">
    <h1>{{ __('setup.steps.admin.title') }}</h1>
    <p>{{ __('setup.steps.admin.subtitle', ['current' => $currentStep, 'total' => $totalSteps]) }}</p>
</div>

@if (!empty($errors))
    <div class="alert alert-error">
        <strong>{{ __('setup.steps.database.validation_errors') }}</strong>
        <ul style="margin-top: 8px; padding-left: 20px; margin-bottom: 0;">
            @foreach ($errors as $field => $messages)
                @foreach ((array)$messages as $message)
                    <li>{{ $message }}</li>
                @endforeach
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form id="adminForm" method="POST" action="{{ route('setup.admin.store', ['setup_token' => app(\App\Services\Installation\SetupSession::class)->getToken()]) }}">
    <input type="hidden" name="_setup_token" value="{{ app(\App\Services\Installation\SetupSession::class)->getCsrfToken() }}">

    <div class="info-box">
        ⚠️ <strong>{{ __('setup.steps.admin.info_title') }}</strong>
        <p style="margin: 8px 0 0 0; font-size: 13px;">{{ __('setup.steps.admin.info_text') }}</p>
    </div>

    <!-- Nom complet -->
    <div class="form-group">
        <label class="form-label">{{ __('setup.steps.admin.name') }}</label>
        <input type="text" name="admin_name" id="admin-name"
               class="form-control @if(isset($errors['admin_name'])) is-invalid @endif"
               placeholder="Jean Dupont" value="{{ $formData['admin_name'] ?? '' }}" required>
        <small style="color: #666; margin-top: 5px; display: block;">{{ __('setup.steps.admin.name_help') }}</small>
        @if(isset($errors['admin_name'])) <span class="error">{{ is_array($errors['admin_name']) ? $errors['admin_name'][0] : $errors['admin_name'] }}</span> @endif
    </div>

    <!-- Email -->
    <div class="form-group">
        <label class="form-label">{{ __('setup.steps.admin.email') }}</label>
        <input type="email" name="admin_email" id="admin-email"
               class="form-control @if(isset($errors['admin_email'])) is-invalid @endif"
               placeholder="admin@example.com" value="{{ $formData['admin_email'] ?? '' }}" required>
        <small style="color: #666; margin-top: 5px; display: block;">{{ __('setup.steps.admin.email_help') }}</small>
        @if(isset($errors['admin_email'])) <span class="error">{{ is_array($errors['admin_email']) ? $errors['admin_email'][0] : $errors['admin_email'] }}</span> @endif
    </div>

    <!-- Mot de passe -->
    <div class="form-group password-group">
        <label class="form-label">{{ __('setup.steps.admin.password') }}</label>
        <input type="password" name="admin_password" id="admin-password"
               class="form-control @if(isset($errors['admin_password'])) is-invalid @endif"
               placeholder="{{ __('setup.steps.admin.password_help') }}" value="" required
               onchange="updatePasswordStrength()" onkeyup="updatePasswordStrength()">
        <span class="password-toggle" onclick="togglePassword(event, 'admin-password')">👁️</span>
        @if(isset($errors['admin_password'])) <span class="error">{{ is_array($errors['admin_password']) ? $errors['admin_password'][0] : $errors['admin_password'] }}</span> @endif

        <!-- Indicateur force password -->
        <div style="margin-top: 10px;">
            <div style="display: flex; gap: 4px; margin-bottom: 8px;">
                <div class="password-strength-bar" id="strength-1"></div>
                <div class="password-strength-bar" id="strength-2"></div>
                <div class="password-strength-bar" id="strength-3"></div>
                <div class="password-strength-bar" id="strength-4"></div>
            </div>
            <div style="font-size: 12px; color: #666;">
                {{ __('setup.steps.admin.strength') }} <span id="strength-text">{{ __('setup.steps.admin.strength_text.weak') }}</span>
            </div>
        </div>

        <!-- Checklist requirements -->
        <div class="password-requirements">
            <div class="requirement-item">
                <span id="req-length" class="requirement-icon">❌</span>
                <span>{{ __('setup.steps.admin.requirements.length') }}</span>
            </div>
            <div class="requirement-item">
                <span id="req-upper" class="requirement-icon">❌</span>
                <span>{{ __('setup.steps.admin.requirements.upper') }}</span>
            </div>
            <div class="requirement-item">
                <span id="req-lower" class="requirement-icon">❌</span>
                <span>{{ __('setup.steps.admin.requirements.lower') }}</span>
            </div>
            <div class="requirement-item">
                <span id="req-digit" class="requirement-icon">❌</span>
                <span>{{ __('setup.steps.admin.requirements.digit') }}</span>
            </div>
            <div class="requirement-item">
                <span id="req-special" class="requirement-icon">❌</span>
                <span>{{ __('setup.steps.admin.requirements.special') }}</span>
            </div>
        </div>

        <small style="color: #666; margin-top: 10px; display: block; font-weight: 600;">
            {{ __('setup.steps.admin.password_example') }}
        </small>
    </div>

    <!-- Confirmation mot de passe -->
    <div class="form-group password-group">
        <label class="form-label">{{ __('setup.steps.admin.password_confirm') }}</label>
        <input type="password" name="admin_password_confirmation" id="admin-password-confirm"
               class="form-control @if(isset($errors['admin_password_confirmation'])) is-invalid @endif"
               placeholder="Répétez le mot de passe" value="" required>
        <span class="password-toggle" onclick="togglePassword(event, 'admin-password-confirm')">👁️</span>
        @if(isset($errors['admin_password_confirmation'])) <span class="error">{{ is_array($errors['admin_password_confirmation']) ? $errors['admin_password_confirmation'][0] : $errors['admin_password_confirmation'] }}</span> @endif
    </div>

    <!-- Conditions checkbox -->
    <div style="margin: 20px 0;">
        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 13px;">
            <input type="checkbox" id="admin-agreement" required
                   style="width: 18px; height: 18px; cursor: pointer;">
            <span>{{ __('setup.steps.admin.agreement') }}</span>
        </label>
    </div>

    <!-- Actions -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary" id="submit-btn">{{ __('setup.steps.admin.continue') }}</button>
    </div>
</form>

<script>
    // Toggle password visibility
    function togglePassword(event, fieldId) {
        const field = document.getElementById(fieldId);
        const type = field.type === 'password' ? 'text' : 'password';
        field.type = type;
        event.target.textContent = type === 'password' ? '👁️' : '👁️‍🗨️';
    }

    // Update password strength indicator
    function updatePasswordStrength() {
        const password = document.getElementById('admin-password').value;

        // Check requirements
        const hasLength = password.length >= 8;
        const hasUpper = /[A-Z]/.test(password);
        const hasLower = /[a-z]/.test(password);
        const hasDigit = /\d/.test(password);
        const hasSpecial = /[@$!%*?&]/.test(password);

        // Update requirement icons
        const icons = {
            'req-length': hasLength,
            'req-upper': hasUpper,
            'req-lower': hasLower,
            'req-digit': hasDigit,
            'req-special': hasSpecial
        };

        for (const [id, met] of Object.entries(icons)) {
            const el = document.getElementById(id);
            if (el) {
                el.textContent = met ? '✅' : '❌';
                el.classList.toggle('met', met);
            }
        }

        // Calculate strength (0-5)
        const strength = [hasLength, hasUpper, hasLower, hasDigit, hasSpecial].filter(Boolean).length;

        // Update strength bars
        updateStrengthBars(strength);

        // Update strength text
        let strengthText = "{{ __('setup.steps.admin.strength_text.weak') }}";
        let strengthColor = '#dc2626';

        if (strength === 1 || strength === 2) {
            strengthText = "{{ __('setup.steps.admin.strength_text.weak') }}";
            strengthColor = '#dc2626';
        } else if (strength === 3) {
            strengthText = "{{ __('setup.steps.admin.strength_text.medium') }}";
            strengthColor = '#f59e0b';
        } else if (strength === 4) {
            strengthText = "{{ __('setup.steps.admin.strength_text.good') }}";
            strengthColor = '#3b82f6';
        } else if (strength === 5) {
            strengthText = "{{ __('setup.steps.admin.strength_text.strong') }}";
            strengthColor = '#22c55e';
        }

        const strengthTextEl = document.getElementById('strength-text');
        strengthTextEl.textContent = strengthText;
        strengthTextEl.style.color = strengthColor;

        // Enable/disable submit button
        const isValid = hasLength && hasUpper && hasLower && hasDigit && hasSpecial;
        document.getElementById('submit-btn').disabled = !isValid;
    }

    // Update strength bars
    function updateStrengthBars(strength) {
        for (let i = 1; i <= 4; i++) {
            const bar = document.getElementById(`strength-${i}`);
            if (i <= strength) {
                if (strength === 1 || strength === 2) {
                    bar.style.backgroundColor = '#dc2626';
                } else if (strength === 3) {
                    bar.style.backgroundColor = '#f59e0b';
                } else if (strength === 4) {
                    bar.style.backgroundColor = '#3b82f6';
                } else if (strength === 5) {
                    bar.style.backgroundColor = '#22c55e';
                }
            } else {
                bar.style.backgroundColor = '#e5e7eb';
            }
        }
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', () => {
        updatePasswordStrength();
    });
</script>

<style>
    .password-requirements {
        margin-top: 12px;
        padding: 12px;
        background: #f3f4f6;
        background: var(--border);
        border-radius: 8px;
        font-size: 12px;
        border: 1px solid transparent;
        transition: all 0.3s ease;
    }

    .requirement-item {
        margin: 6px 0;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #4b5563;
        color: var(--text-muted);
    }

    .requirement-icon {
        color: #9ca3af;
        transition: all 0.2s ease;
    }

    .requirement-icon.met {
        color: #10b981;
    }

    html.dark .password-requirements {
        background: rgba(255, 255, 255, 0.03);
        border-color: var(--border);
    }

    html.dark .requirement-item {
        color: #9ca3af;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-control.is-invalid {
        border-color: #dc2626;
    }

    .form-control:disabled {
        background: #f3f4f6;
        cursor: not-allowed;
    }

    .error {
        color: #dc2626;
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }

    .info-box {
        background: #f3f4f6;
        border-left: 4px solid #667eea;
        padding: 12px 16px;
        border-radius: 4px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #333;
    }

    .password-strength-bar {
        flex: 1;
        height: 4px;
        background: #e5e7eb;
        border-radius: 2px;
        transition: background-color 0.3s;
    }

    .setup-header h1 {
        margin-bottom: 10px;
    }

    .setup-header p {
        color: #666;
        font-size: 14px;
    }

    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn:disabled:hover {
        transform: none;
    }
</style>
@endsection
