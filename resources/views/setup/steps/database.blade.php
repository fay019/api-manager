@extends('setup.layout')

@section('content')
<div class="setup-header">
    <h1>Base de Données</h1>
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

<form id="databaseForm" method="POST" action="{{ route('setup.database.store', ['setup_token' => app(\App\Services\Installation\SetupSession::class)->getToken()]) }}">
    <input type="hidden" name="_setup_token" value="{{ app(\App\Services\Installation\SetupSession::class)->getCsrfToken() }}">

    <!-- Sélection du type de BD -->
    <div class="form-group">
        <label class="form-label"><strong>Type de Base de Données</strong></label>
        <div style="display: grid; gap: 10px; margin-bottom: 20px;">
            @foreach($drivers as $key => $driver)
                <label style="border: 1px solid #d1d5db; padding: 12px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: all 0.3s;" id="label-{{ $key }}">
                    <input type="radio" name="database_driver" value="{{ $key }}"
                           class="driver-select" style="cursor: pointer; width: 18px; height: 18px;"
                           {{ ($formData['database_driver'] ?? '') === $key ? 'checked' : '' }}
                           onchange="updateFormFields()">
                    <div>
                        <div style="font-weight: 600; color: #333;">{{ $driver['name'] }}</div>
                        <div style="font-size: 12px; color: #666;">{{ $driver['description'] }}</div>
                    </div>
                </label>
            @endforeach
        </div>
        @if(isset($errors['database_driver'])) <span class="error">{{ is_array($errors['database_driver']) ? $errors['database_driver'][0] : $errors['database_driver'] }}</span> @endif
    </div>

    <!-- Champs conditionnels par driver -->
    <div id="sqlite-fields" style="display: none;">
        <div class="form-group">
            <label class="form-label">Base de Données</label>
            <div style="padding: 12px; background: #f3f4f6; border-radius: 6px; border: 1px solid #e5e7eb;">
                <code>database/database.sqlite</code>
                <small style="color: #666; margin-top: 8px; display: block;">La base de données sera créée automatiquement dans le répertoire racine de l'application.</small>
            </div>
        </div>
    </div>

    <div id="mysql-fields" style="display: none;">
        <div class="form-group">
            <label class="form-label">Serveur / Host</label>
            <input type="text" name="database_host" id="mysql-host"
                   class="form-control @if(isset($errors['database_host'])) is-invalid @endif"
                   placeholder="localhost" value="{{ $formData['database_host'] ?? '' }}">
            @if(isset($errors['database_host'])) <span class="error">{{ is_array($errors['database_host']) ? $errors['database_host'][0] : $errors['database_host'] }}</span> @endif
        </div>

        <div class="form-group">
            <label class="form-label">Port</label>
            <input type="number" name="database_port" id="mysql-port"
                   class="form-control @if(isset($errors['database_port'])) is-invalid @endif"
                   placeholder="3306" value="{{ $formData['database_port'] ?? '' }}"
                   min="1" max="65535">
            @if(isset($errors['database_port'])) <span class="error">{{ is_array($errors['database_port']) ? $errors['database_port'][0] : $errors['database_port'] }}</span> @endif
        </div>

        <div class="form-group">
            <label class="form-label">Base de Données</label>
            <input type="text" name="database_database" id="mysql-database"
                   class="form-control @if(isset($errors['database_database'])) is-invalid @endif"
                   placeholder="api_manager" value="{{ $formData['database_database'] ?? '' }}">
            @if(isset($errors['database_database'])) <span class="error">{{ is_array($errors['database_database']) ? $errors['database_database'][0] : $errors['database_database'] }}</span> @endif
        </div>

        <div class="form-group">
            <label class="form-label">Utilisateur</label>
            <input type="text" name="database_username" id="mysql-username"
                   class="form-control @if(isset($errors['database_username'])) is-invalid @endif"
                   placeholder="root" value="{{ $formData['database_username'] ?? '' }}">
            @if(isset($errors['database_username'])) <span class="error">{{ is_array($errors['database_username']) ? $errors['database_username'][0] : $errors['database_username'] }}</span> @endif
        </div>

        <div class="form-group password-group">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="database_password" id="mysql-password"
                   class="form-control @if(isset($errors['database_password'])) is-invalid @endif"
                   placeholder="(laisser vide si pas de mot de passe)" value="{{ $formData['database_password'] ?? '' }}">
            <span class="password-toggle" onclick="togglePassword(event, 'mysql-password')">👁️</span>
            @if(isset($errors['database_password'])) <span class="error">{{ is_array($errors['database_password']) ? $errors['database_password'][0] : $errors['database_password'] }}</span> @endif
        </div>
    </div>

    <div id="pgsql-fields" style="display: none;">
        <div class="form-group">
            <label class="form-label">Serveur / Host</label>
            <input type="text" name="database_host" id="pgsql-host"
                   class="form-control @if(isset($errors['database_host'])) is-invalid @endif"
                   placeholder="localhost" value="{{ $formData['database_host'] ?? '' }}">
            @if(isset($errors['database_host'])) <span class="error">{{ is_array($errors['database_host']) ? $errors['database_host'][0] : $errors['database_host'] }}</span> @endif
        </div>

        <div class="form-group">
            <label class="form-label">Port</label>
            <input type="number" name="database_port" id="pgsql-port"
                   class="form-control @if(isset($errors['database_port'])) is-invalid @endif"
                   placeholder="5432" value="{{ $formData['database_port'] ?? '' }}"
                   min="1" max="65535">
            @if(isset($errors['database_port'])) <span class="error">{{ is_array($errors['database_port']) ? $errors['database_port'][0] : $errors['database_port'] }}</span> @endif
        </div>

        <div class="form-group">
            <label class="form-label">Base de Données</label>
            <input type="text" name="database_database" id="pgsql-database"
                   class="form-control @if(isset($errors['database_database'])) is-invalid @endif"
                   placeholder="api_manager" value="{{ $formData['database_database'] ?? '' }}">
            @if(isset($errors['database_database'])) <span class="error">{{ is_array($errors['database_database']) ? $errors['database_database'][0] : $errors['database_database'] }}</span> @endif
        </div>

        <div class="form-group">
            <label class="form-label">Utilisateur</label>
            <input type="text" name="database_username" id="pgsql-username"
                   class="form-control @if(isset($errors['database_username'])) is-invalid @endif"
                   placeholder="postgres" value="{{ $formData['database_username'] ?? '' }}">
            @if(isset($errors['database_username'])) <span class="error">{{ is_array($errors['database_username']) ? $errors['database_username'][0] : $errors['database_username'] }}</span> @endif
        </div>

        <div class="form-group password-group">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="database_password" id="pgsql-password"
                   class="form-control @if(isset($errors['database_password'])) is-invalid @endif"
                   placeholder="(laisser vide si pas de mot de passe)" value="{{ $formData['database_password'] ?? '' }}">
            <span class="password-toggle" onclick="togglePassword(event, 'pgsql-password')">👁️</span>
            @if(isset($errors['database_password'])) <span class="error">{{ is_array($errors['database_password']) ? $errors['database_password'][0] : $errors['database_password'] }}</span> @endif
        </div>
    </div>

    <!-- Bouton test connexion -->
    <div style="margin-bottom: 20px;">
        <button type="button" class="btn btn-secondary" id="testBtn" style="background: #f3f4f6; color: #333;"
                onclick="testDatabaseConnection(event)">
            🔧 Tester la connexion
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
        const driver = document.querySelector('input[name="database_driver"]:checked').value;

        // Masquer tous les champs
        document.getElementById('sqlite-fields').style.display = 'none';
        document.getElementById('mysql-fields').style.display = 'none';
        document.getElementById('pgsql-fields').style.display = 'none';

        // Afficher les champs du driver sélectionné
        if (driver === 'sqlite') {
            document.getElementById('sqlite-fields').style.display = 'block';
        } else if (driver === 'mysql') {
            document.getElementById('mysql-fields').style.display = 'block';
        } else if (driver === 'pgsql') {
            document.getElementById('pgsql-fields').style.display = 'block';
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

    // Tester la connexion
    function testDatabaseConnection(event) {
        if (event) event.preventDefault();
        const driver = document.querySelector('input[name="database_driver"]:checked').value;
        const testBtn = document.getElementById('testBtn');
        const testLoading = document.getElementById('testLoading');
        const testResult = document.getElementById('testResult');

        // Préparer les données
        const data = {
            database_driver: driver,
        };

        if (driver === 'sqlite') {
            // SQLite utilise toujours le chemin par défaut: database/database.sqlite
            data.database_database = 'database.sqlite';
        } else if (driver === 'mysql') {
            data.database_host = document.getElementById('mysql-host').value;
            data.database_port = document.getElementById('mysql-port').value;
            data.database_database = document.getElementById('mysql-database').value;
            data.database_username = document.getElementById('mysql-username').value;
            data.database_password = document.getElementById('mysql-password').value;
        } else if (driver === 'pgsql') {
            data.database_host = document.getElementById('pgsql-host').value;
            data.database_port = document.getElementById('pgsql-port').value;
            data.database_database = document.getElementById('pgsql-database').value;
            data.database_username = document.getElementById('pgsql-username').value;
            data.database_password = document.getElementById('pgsql-password').value;
        }

        // Afficher loading
        testBtn.disabled = true;
        testLoading.classList.add('active');
        testResult.style.display = 'none';

        // Envoyer requête
        fetch('{{ route("setup.database.test", ["setup_token" => app(\App\Services\Installation\SetupSession::class)->getToken()]) }}', {
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
                    } else if (result.errors && result.errors.exception) {
                        errorMsg = result.errors.exception;
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

    .setup-header h1 {
        margin-bottom: 10px;
    }

    .setup-header p {
        color: #666;
        font-size: 14px;
    }
</style>
@endsection
