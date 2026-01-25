@extends('setup.layout')

@section('content')
<div class="setup-header">
    <h1>Configuration Email</h1>
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

<form id="mailForm" method="POST" action="{{ route('setup.mail.store', ['setup_token' => app(\App\Services\Installation\SetupSession::class)->getToken()]) }}">
    <input type="hidden" name="_setup_token" value="{{ app(\App\Services\Installation\SetupSession::class)->getCsrfToken() }}">

    <!-- Sélection du type email -->
    <div class="form-group">
        <label class="form-label"><strong>Serveur Email</strong></label>
        <div style="display: grid; gap: 10px; margin-bottom: 20px;">
            @foreach($drivers as $key => $driver)
                <label style="border: 1px solid #d1d5db; padding: 12px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: all 0.3s;" id="label-{{ $key }}">
                    <input type="radio" name="mail_driver" value="{{ $key }}"
                           class="driver-select" style="cursor: pointer; width: 18px; height: 18px;"
                           {{ ($formData['mail_driver'] ?? '') === $key ? 'checked' : '' }}
                           onchange="updateFormFields()">
                    <div>
                        <div style="font-weight: 600; color: #333;">{{ $driver['name'] }}</div>
                        <div style="font-size: 12px; color: #666;">{{ $driver['description'] }}</div>
                    </div>
                </label>
            @endforeach
        </div>
        @if(isset($errors['mail_driver'])) <span class="error">{{ is_array($errors['mail_driver']) ? $errors['mail_driver'][0] : $errors['mail_driver'] }}</span> @endif
    </div>

    <!-- Champs SMTP -->
    <div id="smtp-fields" style="display: none;">
        <div class="info-box">
            💡 <strong>Exemples:</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px; font-size: 12px;">
                <li><strong>Gmail:</strong> smtp.gmail.com:587 (TLS) - utiliser une <a href="https://myaccount.google.com/apppasswords" target="_blank" style="color: #667eea;">App Password</a></li>
                <li><strong>Mailtrap:</strong> smtp.mailtrap.io:2525 (TLS)</li>
                <li><strong>Sendgrid:</strong> smtp.sendgrid.net:587 (TLS) - user: apikey</li>
            </ul>
        </div>

        <div class="form-group">
            <label class="form-label">Serveur SMTP</label>
            <input type="text" name="mail_host" id="smtp-host"
                   class="form-control @if(isset($errors['mail_host'])) is-invalid @endif"
                   placeholder="smtp.mailtrap.io" value="{{ $formData['mail_host'] ?? '' }}">
            @if(isset($errors['mail_host'])) <span class="error">{{ is_array($errors['mail_host']) ? $errors['mail_host'][0] : $errors['mail_host'] }}</span> @endif
        </div>

        <div class="form-group">
            <label class="form-label">Port</label>
            <input type="number" name="mail_port" id="smtp-port"
                   class="form-control @if(isset($errors['mail_port'])) is-invalid @endif"
                   placeholder="2525" value="{{ $formData['mail_port'] ?? '' }}"
                   min="1" max="65535">
            <small style="color: #666; margin-top: 5px; display: block;">Port TLS: 587, SSL: 465, Standard: 25</small>
            @if(isset($errors['mail_port'])) <span class="error">{{ is_array($errors['mail_port']) ? $errors['mail_port'][0] : $errors['mail_port'] }}</span> @endif
        </div>

        <div class="form-group">
            <label class="form-label">Chiffrement</label>
            <select name="mail_encryption" id="smtp-encryption"
                    class="form-control @if(isset($errors['mail_encryption'])) is-invalid @endif">
                <option value="">Aucun</option>
                <option value="tls" {{ ($formData['mail_encryption'] ?? '') === 'tls' ? 'selected' : '' }}>TLS (587)</option>
                <option value="ssl" {{ ($formData['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL (465)</option>
            </select>
            @if(isset($errors['mail_encryption'])) <span class="error">{{ is_array($errors['mail_encryption']) ? $errors['mail_encryption'][0] : $errors['mail_encryption'] }}</span> @endif
        </div>

        <div class="form-group">
            <label class="form-label">Nom d'utilisateur</label>
            <input type="text" name="mail_username" id="smtp-username"
                   class="form-control @if(isset($errors['mail_username'])) is-invalid @endif"
                   placeholder="utilisateur@example.com" value="{{ $formData['mail_username'] ?? '' }}">
            @if(isset($errors['mail_username'])) <span class="error">{{ is_array($errors['mail_username']) ? $errors['mail_username'][0] : $errors['mail_username'] }}</span> @endif
        </div>

        <div class="form-group password-group">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="mail_password" id="smtp-password"
                   class="form-control @if(isset($errors['mail_password'])) is-invalid @endif"
                   placeholder="" value="{{ $formData['mail_password'] ?? '' }}">
            <span class="password-toggle" onclick="togglePassword(event, 'smtp-password')">👁️</span>
            @if(isset($errors['mail_password'])) <span class="error">{{ is_array($errors['mail_password']) ? $errors['mail_password'][0] : $errors['mail_password'] }}</span> @endif
        </div>
    </div>

    <!-- Champs Sendmail -->
    <div id="sendmail-fields" style="display: none;">
        <div class="form-group">
            <label class="form-label">Chemin du binaire sendmail</label>
            <input type="text" name="mail_path" id="sendmail-path"
                   class="form-control @if(isset($errors['mail_path'])) is-invalid @endif"
                   placeholder="/usr/sbin/sendmail -t -i" value="{{ $formData['mail_path'] ?? '' }}">
            <small style="color: #666; margin-top: 5px; display: block;">Défaut: /usr/sbin/sendmail -t -i</small>
            @if(isset($errors['mail_path'])) <span class="error">{{ is_array($errors['mail_path']) ? $errors['mail_path'][0] : $errors['mail_path'] }}</span> @endif
        </div>
    </div>

    <!-- Champs Log (aucun) -->
    <div id="log-fields" style="display: none;">
        <div class="info-box">
            ✅ <strong>Mode Log activé</strong>
            <p style="margin: 8px 0 0 0; font-size: 13px;">Les emails seront enregistrés dans <code>storage/logs/</code> (développement)</p>
        </div>
    </div>

    <!-- Champs Mailgun -->
    <div id="mailgun-fields" style="display: none;">
        <div class="info-box">
            🔑 <strong>Configuration Mailgun</strong>
            <p style="margin: 8px 0 0 0; font-size: 13px;">Cette configuration sera finalisée après l'installation</p>
        </div>
    </div>

    <!-- Adresse source commune -->
    <div style="border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 20px;">
        <div class="form-group">
            <label class="form-label"><strong>Adresse Source (From)</strong></label>
        </div>

        <div class="form-group">
            <label class="form-label">Adresse Email</label>
            <input type="email" name="mail_from_address" id="mail-from-address"
                   class="form-control @if(isset($errors['mail_from_address'])) is-invalid @endif"
                   placeholder="noreply@example.com" value="{{ $formData['mail_from_address'] ?? '' }}" required>
            <small style="color: #666; margin-top: 5px; display: block;">Adresse email par défaut pour tous les emails</small>
            @if(isset($errors['mail_from_address'])) <span class="error">{{ is_array($errors['mail_from_address']) ? $errors['mail_from_address'][0] : $errors['mail_from_address'] }}</span> @endif
        </div>

        <div class="form-group">
            <label class="form-label">Nom Source</label>
            <input type="text" name="mail_from_name" id="mail-from-name"
                   class="form-control @if(isset($errors['mail_from_name'])) is-invalid @endif"
                   placeholder="API Manager" value="{{ $formData['mail_from_name'] ?? '' }}" required>
            <small style="color: #666; margin-top: 5px; display: block;">Nom affiché comme expéditeur</small>
            @if(isset($errors['mail_from_name'])) <span class="error">{{ is_array($errors['mail_from_name']) ? $errors['mail_from_name'][0] : $errors['mail_from_name'] }}</span> @endif
        </div>
    </div>

    <!-- Bouton test connexion (seulement SMTP) -->
    <div id="test-button-container" style="margin-bottom: 20px; margin-top: 20px; display: none;">
        <button type="button" class="btn btn-secondary" id="testBtn" style="background: #f3f4f6; color: #333;"
                onclick="testSmtpConnection(event)">
            🔧 Tester la connexion SMTP
        </button>
    </div>

    <!-- Zone de test -->
    <div id="testResult" style="display: none; margin-bottom: 20px;">
        <div id="testMessage" class="alert"></div>
    </div>

    <!-- Zone loading -->
    <div id="testLoading" class="loading" style="margin-bottom: 20px;">
        <div class="spinner"></div>
        <p style="margin-top: 10px; color: #666;">Test en cours...</p>
    </div>

    <!-- Actions -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Continuer →</button>
    </div>
</form>

<script>
    // Initialiser l'affichage des champs
    function updateFormFields() {
        const driver = document.querySelector('input[name="mail_driver"]:checked').value;

        // Masquer tous les champs
        document.getElementById('smtp-fields').style.display = 'none';
        document.getElementById('sendmail-fields').style.display = 'none';
        document.getElementById('log-fields').style.display = 'none';
        document.getElementById('mailgun-fields').style.display = 'none';
        document.getElementById('test-button-container').style.display = 'none';

        // Afficher les champs du driver sélectionné
        switch(driver) {
            case 'smtp':
                document.getElementById('smtp-fields').style.display = 'block';
                document.getElementById('test-button-container').style.display = 'block';
                break;
            case 'sendmail':
                document.getElementById('sendmail-fields').style.display = 'block';
                break;
            case 'log':
                document.getElementById('log-fields').style.display = 'block';
                break;
            case 'mailgun':
                document.getElementById('mailgun-fields').style.display = 'block';
                break;
        }

        // Mettre à jour couleur des labels radio
        document.querySelectorAll('[id^="label-"]').forEach(label => {
            label.style.borderColor = '#d1d5db';
            label.style.backgroundColor = 'transparent';
        });

        const selectedLabel = document.getElementById('label-' + driver);
        if (selectedLabel) {
            selectedLabel.style.borderColor = '#667eea';
            selectedLabel.style.backgroundColor = 'rgba(102, 126, 234, 0.05)';
        }
    }

    // Toggle password visibility
    function togglePassword(event, fieldId) {
        const field = document.getElementById(fieldId);
        const type = field.type === 'password' ? 'text' : 'password';
        field.type = type;
        event.target.textContent = type === 'password' ? '👁️' : '👁️‍🗨️';
    }

    // Tester la connexion SMTP
    function testSmtpConnection(event) {
        if (event) event.preventDefault();
        const testBtn = document.getElementById('testBtn');
        const testLoading = document.getElementById('testLoading');
        const testResult = document.getElementById('testResult');

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
        testLoading.classList.add('active');
        testResult.style.display = 'none';

        // Envoyer requête
        fetch('{{ route("setup.mail.test", ["setup_token" => app(\App\Services\Installation\SetupSession::class)->getToken()]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Setup-Token': document.querySelector('input[name="_setup_token"]').value,
            },
            body: JSON.stringify(data),
        })
            .then(response => response.json())
            .then(result => {
                testLoading.classList.remove('active');
                testBtn.disabled = false;
                testResult.style.display = 'block';

                const msgDiv = document.getElementById('testMessage');
                if (result.success) {
                    msgDiv.className = 'alert alert-success';
                    msgDiv.innerHTML = '✅ ' + result.message;
                } else {
                    msgDiv.className = 'alert alert-error';
                    let errorMsg = result.message || 'Connexion échouée';
                    if (result.errors && result.errors.connection) {
                        errorMsg = result.errors.connection;
                    } else if (result.errors && result.errors.smtp) {
                        errorMsg = result.errors.smtp;
                    }
                    msgDiv.innerHTML = '❌ ' + errorMsg;
                }
            })
            .catch(error => {
                testLoading.classList.remove('active');
                testBtn.disabled = false;
                testResult.style.display = 'block';

                const msgDiv = document.getElementById('testMessage');
                msgDiv.className = 'alert alert-error';
                msgDiv.innerHTML = '❌ Erreur lors du test: ' + error.message;
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
