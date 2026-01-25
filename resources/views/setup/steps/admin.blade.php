@extends('setup.layout')

@section('content')
<div class="setup-header">
    <h1>Créer l'Administrateur</h1>
    <p>Étape {{ $currentStep }}/{{ $totalSteps }}</p>
</div>

@if (!empty($errors))
    <div class="alert alert-error">
        <strong>Erreurs de validation:</strong>
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
        ⚠️ <strong>Information importante:</strong>
        <p style="margin: 8px 0 0 0; font-size: 13px;">Cet utilisateur aura accès administrateur complet à l'application. Conservez les informations en lieu sûr.</p>
    </div>

    <!-- Nom complet -->
    <div class="form-group">
        <label class="form-label">Nom Complet</label>
        <input type="text" name="admin_name" id="admin-name"
               class="form-control @if(isset($errors['admin_name'])) is-invalid @endif"
               placeholder="Jean Dupont" value="{{ $formData['admin_name'] ?? '' }}" required>
        <small style="color: #666; margin-top: 5px; display: block;">Votre nom complet ou celui du propriétaire</small>
        @if(isset($errors['admin_name'])) <span class="error">{{ is_array($errors['admin_name']) ? $errors['admin_name'][0] : $errors['admin_name'] }}</span> @endif
    </div>

    <!-- Email -->
    <div class="form-group">
        <label class="form-label">Adresse Email</label>
        <input type="email" name="admin_email" id="admin-email"
               class="form-control @if(isset($errors['admin_email'])) is-invalid @endif"
               placeholder="admin@example.com" value="{{ $formData['admin_email'] ?? '' }}" required>
        <small style="color: #666; margin-top: 5px; display: block;">Email unique pour la connexion</small>
        @if(isset($errors['admin_email'])) <span class="error">{{ is_array($errors['admin_email']) ? $errors['admin_email'][0] : $errors['admin_email'] }}</span> @endif
    </div>

    <!-- Mot de passe -->
    <div class="form-group password-group">
        <label class="form-label">Mot de Passe</label>
        <input type="password" name="admin_password" id="admin-password"
               class="form-control @if(isset($errors['admin_password'])) is-invalid @endif"
               placeholder="Minimum 8 caractères" value="" required
               onchange="updatePasswordStrength()" onkeyup="updatePasswordStrength()">
        <span class="password-toggle" onclick="togglePassword('admin-password')">👁️</span>
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
                Force: <span id="strength-text">Faible</span>
            </div>
        </div>

        <!-- Checklist requirements -->
        <div style="margin-top: 12px; padding: 10px; background: #f3f4f6; border-radius: 4px; font-size: 12px;">
            <div style="margin: 4px 0; display: flex; gap: 6px;">
                <span id="req-length" style="color: #999;">❌</span>
                <span>Minimum 8 caractères</span>
            </div>
            <div style="margin: 4px 0; display: flex; gap: 6px;">
                <span id="req-upper" style="color: #999;">❌</span>
                <span>Au moins une MAJUSCULE</span>
            </div>
            <div style="margin: 4px 0; display: flex; gap: 6px;">
                <span id="req-lower" style="color: #999;">❌</span>
                <span>Au moins une minuscule</span>
            </div>
            <div style="margin: 4px 0; display: flex; gap: 6px;">
                <span id="req-digit" style="color: #999;">❌</span>
                <span>Au moins un chiffre (0-9)</span>
            </div>
            <div style="margin: 4px 0; display: flex; gap: 6px;">
                <span id="req-special" style="color: #999;">❌</span>
                <span>Au moins un caractère spécial (@$!%*?&)</span>
            </div>
        </div>

        <small style="color: #666; margin-top: 10px; display: block; font-weight: 600;">
            Exemple mot de passe fort: Azerty123!
        </small>
    </div>

    <!-- Confirmation mot de passe -->
    <div class="form-group password-group">
        <label class="form-label">Confirmer le Mot de Passe</label>
        <input type="password" name="admin_password_confirmation" id="admin-password-confirm"
               class="form-control @if(isset($errors['admin_password_confirmation'])) is-invalid @endif"
               placeholder="Répétez le mot de passe" value="" required>
        <span class="password-toggle" onclick="togglePassword('admin-password-confirm')">👁️</span>
        @if(isset($errors['admin_password_confirmation'])) <span class="error">{{ is_array($errors['admin_password_confirmation']) ? $errors['admin_password_confirmation'][0] : $errors['admin_password_confirmation'] }}</span> @endif
    </div>

    <!-- Conditions checkbox -->
    <div style="margin: 20px 0;">
        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 13px;">
            <input type="checkbox" id="admin-agreement" required
                   style="width: 18px; height: 18px; cursor: pointer;">
            <span>J'accepte la responsabilité de cet accès administrateur</span>
        </label>
    </div>

    <!-- Actions -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary" id="submit-btn">Continuer →</button>
    </div>
</form>

<script>
    // Toggle password visibility
    function togglePassword(fieldId) {
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
        document.getElementById('req-length').textContent = hasLength ? '✅' : '❌';
        document.getElementById('req-length').style.color = hasLength ? '#22c55e' : '#999';
        document.getElementById('req-upper').textContent = hasUpper ? '✅' : '❌';
        document.getElementById('req-upper').style.color = hasUpper ? '#22c55e' : '#999';
        document.getElementById('req-lower').textContent = hasLower ? '✅' : '❌';
        document.getElementById('req-lower').style.color = hasLower ? '#22c55e' : '#999';
        document.getElementById('req-digit').textContent = hasDigit ? '✅' : '❌';
        document.getElementById('req-digit').style.color = hasDigit ? '#22c55e' : '#999';
        document.getElementById('req-special').textContent = hasSpecial ? '✅' : '❌';
        document.getElementById('req-special').style.color = hasSpecial ? '#22c55e' : '#999';

        // Calculate strength (0-5)
        const strength = [hasLength, hasUpper, hasLower, hasDigit, hasSpecial].filter(Boolean).length;

        // Update strength bars
        updateStrengthBars(strength);

        // Update strength text
        let strengthText = 'Faible';
        let strengthColor = '#dc2626';

        if (strength === 1 || strength === 2) {
            strengthText = 'Faible';
            strengthColor = '#dc2626';
        } else if (strength === 3) {
            strengthText = 'Moyen';
            strengthColor = '#f59e0b';
        } else if (strength === 4) {
            strengthText = 'Bon';
            strengthColor = '#3b82f6';
        } else if (strength === 5) {
            strengthText = 'Très Fort';
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
