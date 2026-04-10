@extends('setup.layout')

@section('content')
<div class="setup-body">
    <div class="setup-steps">
        <div class="setup-step completed"></div>
        <div class="setup-step completed"></div>
        <div class="setup-step completed"></div>
        <div class="setup-step active"></div>
        <div class="setup-step"></div>
        <div class="setup-step"></div>
    </div>

    <h2>
        <span style="color: var(--primary)">📧</span>
        {{ __('setup.steps.mail.title') }}
    </h2>
    <p style="color: var(--text-muted); margin-bottom: 2rem;">{{ __('setup.steps.mail.subtitle', ['current' => $currentStep, 'total' => $totalSteps]) }}</p>

    @if (!empty($errors))
        <div class="alert alert-error">
            <div style="font-weight: 700;">{{ __('setup.steps.database.validation_errors') }}</div>
            <ul style="margin-top: 0.5rem; padding-left: 1.5rem; margin-bottom: 0;">
                @foreach ($errors as $field => $messages)
                    @foreach ((array)$messages as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                @endforeach
            </ul>
        </div>
    @endif

    <form id="mailForm" method="POST" action="{{ route('setup.mail.store', ['setup_token' => app(\App\Services\Installation\SetupSession::class)->getToken()]) }}">
        @csrf
        <input type="hidden" name="_setup_token" value="{{ app(\App\Services\Installation\SetupSession::class)->getToken() }}">

        <div class="form-group">
            <label class="form-label"><strong>{{ __('setup.steps.mail.driver') }}</strong></label>
            <div style="display: grid; gap: 0.75rem; margin-bottom: 1.5rem;">
                @foreach($drivers as $key => $driver)
                    <label style="border: 2px solid var(--border); padding: 1rem; border-radius: 1rem; cursor: pointer; display: flex; align-items: center; gap: 1rem; transition: all 0.2s;" id="label-{{ $key }}">
                        <input type="radio" name="mail_driver" value="{{ $key }}"
                               class="driver-select" style="cursor: pointer; width: 1.25rem; height: 1.25rem; margin: 0;"
                               {{ ($formData['mail_driver'] ?? 'log') === $key ? 'checked' : '' }}
                               onchange="updateFormFields()">
                        <div style="flex: 1;">
                            <div style="font-weight: 700; color: var(--text-main);">{{ $driver['name'] }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $driver['description'] }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div id="smtp-fields" style="display: none;">
            <div style="background: rgba(79, 70, 229, 0.05); padding: 1rem; border-radius: 1rem; margin-bottom: 1.5rem; border: 1px solid var(--border); font-size: 0.875rem;">
                <div style="font-weight: 700; margin-bottom: 0.5rem;">💡 {{ __('setup.steps.mail.examples_title') }}</div>
                <ul style="margin: 0; padding-left: 1.25rem; color: var(--text-main);">
                    <li><strong>Gmail:</strong> smtp.gmail.com:587 (TLS)</li>
                    <li><strong>Mailtrap:</strong> smtp.mailtrap.io:2525 (TLS)</li>
                </ul>
            </div>

            <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">{{ __('setup.steps.mail.host') }}</label>
                    <input type="text" name="mail_host" id="smtp-host" placeholder="smtp.mailtrap.io" value="{{ $formData['mail_host'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('setup.steps.mail.port') }}</label>
                    <input type="number" name="mail_port" id="smtp-port" placeholder="2525" value="{{ $formData['mail_port'] ?? '' }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('setup.steps.mail.encryption') }}</label>
                <select name="mail_encryption" id="smtp-encryption">
                    <option value="">{{ __('setup.steps.mail.encryption_none') }}</option>
                    <option value="tls" {{ ($formData['mail_encryption'] ?? '') === 'tls' ? 'selected' : '' }}>TLS (587)</option>
                    <option value="ssl" {{ ($formData['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL (465)</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">{{ __('setup.steps.mail.username') }}</label>
                    <input type="text" name="mail_username" id="smtp-username" placeholder="user@example.com" value="{{ $formData['mail_username'] ?? '' }}">
                </div>
                <div class="form-group" style="position: relative;">
                    <label class="form-label">{{ __('setup.steps.mail.password') }}</label>
                    <input type="password" name="mail_password" id="smtp-password" value="{{ $formData['mail_password'] ?? '' }}">
                    <span class="password-toggle" onclick="togglePassword(event, 'smtp-password')" style="position: absolute; right: 1rem; top: 2.75rem; cursor: pointer;">👁️</span>
                </div>
            </div>
        </div>

        <div id="sendmail-fields" style="display: none;">
            <div class="form-group">
                <label class="form-label">{{ __('setup.steps.mail.path') }}</label>
                <input type="text" name="mail_path" id="sendmail-path" placeholder="/usr/sbin/sendmail -t -i" value="{{ $formData['mail_path'] ?? '' }}">
            </div>
        </div>

        <div id="log-fields" style="display: none;">
            <div style="background: rgba(16, 185, 129, 0.05); padding: 1rem; border-radius: 1rem; margin-bottom: 1.5rem; border: 1px solid var(--success); color: var(--success);">
                ✅ <strong>{{ __('setup.steps.mail.log_mode') }}</strong>
                <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem;">{{ __('setup.steps.mail.log_help') }}</p>
            </div>
        </div>

        <div style="border-top: 1px solid var(--border); margin: 2rem 0; padding-top: 1.5rem;">
            <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1.25rem;">{{ __('setup.steps.mail.from_section') }}</h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">{{ __('setup.steps.mail.from_address') }}</label>
                    <input type="email" name="mail_from_address" placeholder="noreply@votre-domaine.com" value="{{ $formData['mail_from_address'] ?? 'noreply@example.com' }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('setup.steps.mail.from_name') }}</label>
                    <input type="text" name="mail_from_name" placeholder="API Manager" value="{{ $formData['mail_from_name'] ?? 'API Manager' }}" required>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2.5rem;">
            <button type="button" id="test-connection" class="btn btn-secondary" style="flex: 1;" onclick="testSmtpConnection(event)">
                <span id="test-text">{{ __('setup.steps.mail.test_connection') }}</span>
                <div id="test-spinner" class="spinner" style="display: none; width: 1.25rem; height: 1.25rem;"></div>
            </button>
            <button type="submit" class="btn btn-primary" style="flex: 1.5;">
                {{ __('setup.steps.mail.continue') }}
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </div>
    </form>
</div>

<script>
    // Initialiser l'affichage des champs
    function updateFormFields() {
        const driver = document.querySelector('input[name="mail_driver"]:checked').value;

        // Masquer tous les champs
        document.getElementById('smtp-fields').style.display = 'none';
        document.getElementById('sendmail-fields').style.display = 'none';
        document.getElementById('log-fields').style.display = 'none';
        document.getElementById('test-connection').style.display = 'none';

        // Afficher les champs du driver sélectionné
        switch(driver) {
            case 'smtp':
                document.getElementById('smtp-fields').style.display = 'block';
                document.getElementById('test-connection').style.display = 'block';
                break;
            case 'sendmail':
                document.getElementById('sendmail-fields').style.display = 'block';
                break;
            case 'log':
                document.getElementById('log-fields').style.display = 'block';
                break;
        }

        // Mettre à jour couleur des labels radio
        document.querySelectorAll('[id^="label-"]').forEach(label => {
            label.style.borderColor = 'var(--border)';
            label.style.backgroundColor = 'transparent';
        });

        const selectedLabel = document.getElementById('label-' + driver);
        if (selectedLabel) {
            selectedLabel.style.borderColor = 'var(--primary)';
            selectedLabel.style.backgroundColor = 'rgba(79, 70, 229, 0.05)';
        }
    }

    // Toggle password visibility
    function togglePassword(event, fieldId) {
        if (event) event.preventDefault();
        const field = document.getElementById(fieldId);
        const icon = event.currentTarget;

        if (field.type === 'password') {
            field.type = 'text';
            icon.textContent = '🔓';
        } else {
            field.type = 'password';
            icon.textContent = '👁️';
        }
    }

    // Tester la connexion SMTP
    function testSmtpConnection(event) {
        if (event) event.preventDefault();

        const testBtn = document.getElementById('test-connection');
        const testText = document.getElementById('test-text');
        const testSpinner = document.getElementById('test-spinner');

        // Préparer les données
        const data = {
            mail_driver: 'smtp',
            mail_host: document.getElementById('smtp-host').value,
            mail_port: document.getElementById('smtp-port').value,
            mail_username: document.getElementById('smtp-username').value,
            mail_password: document.getElementById('smtp-password').value,
            mail_encryption: document.getElementById('smtp-encryption').value,
        };

        // Afficher loading
        testBtn.disabled = true;
        testText.style.display = 'none';
        testSpinner.style.display = 'block';

        // Envoyer requête
        fetch('{{ route("setup.mail.test", ["setup_token" => app(\App\Services\Installation\SetupSession::class)->getToken()]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Setup-Token': document.querySelector('input[name="_setup_token"]').value,
            },
            body: JSON.stringify(data),
        })
        .then(response => response.json())
        .then(result => {
            testBtn.disabled = false;
            testText.style.display = 'block';
            testSpinner.style.display = 'none';

            if (result.success) {
                alert('✅ ' + result.message);
            } else {
                let errorMsg = result.message || 'Connexion échouée';
                if (result.errors && result.errors.connection) {
                    errorMsg = result.errors.connection;
                }
                alert('❌ ' + errorMsg);
            }
        })
        .catch(error => {
            testBtn.disabled = false;
            testText.style.display = 'block';
            testSpinner.style.display = 'none';
            alert('❌ Erreur lors du test: ' + error.message);
        });
    }

    // Initialiser au chargement
    document.addEventListener('DOMContentLoaded', updateFormFields);
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

    .info-box a {
        color: #667eea;
        text-decoration: none;
    }

    .info-box a:hover {
        text-decoration: underline;
    }

    code {
        background: #e5e7eb;
        padding: 2px 6px;
        border-radius: 3px;
        font-family: monospace;
        font-size: 12px;
    }

    .setup-header h1 {
        margin-bottom: 10px;
    }

    .setup-header p {
        color: #666;
        font-size: 14px;
    }
</style>
@endsection
