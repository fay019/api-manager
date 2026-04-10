@extends('setup.layout')

@section('content')
<div class="setup-body">
    <div class="setup-steps">
        <div class="setup-step completed"></div>
        <div class="setup-step completed"></div>
        <div class="setup-step active"></div>
        <div class="setup-step"></div>
        <div class="setup-step"></div>
        <div class="setup-step"></div>
    </div>

    <h2>
        <span style="color: var(--primary)">🗄️</span>
        {{ __('setup.steps.database.title') }}
    </h2>
    <p style="color: var(--text-muted); margin-bottom: 2rem;">{{ __('setup.steps.database.subtitle', ['current' => $currentStep, 'total' => $totalSteps]) }}</p>

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

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form id="databaseForm" method="POST" action="{{ route('setup.database.store', ['setup_token' => app(\App\Services\Installation\SetupSession::class)->getToken()]) }}">
        @csrf
        <input type="hidden" name="_setup_token" value="{{ app(\App\Services\Installation\SetupSession::class)->getToken() }}">

        <div class="form-group">
            <label class="form-label"><strong>{{ __('setup.steps.database.driver') }}</strong></label>
            <div style="display: grid; gap: 0.75rem; margin-bottom: 1.5rem;">
                @foreach($drivers as $key => $driver)
                    <label style="border: 2px solid var(--border); padding: 1rem; border-radius: 1rem; cursor: pointer; display: flex; align-items: center; gap: 1rem; transition: all 0.2s;" id="label-{{ $key }}">
                        <input type="radio" name="database_driver" value="{{ $key }}"
                               class="driver-select" style="cursor: pointer; width: 1.25rem; height: 1.25rem; margin: 0;"
                               {{ ($formData['database_driver'] ?? 'sqlite') === $key ? 'checked' : '' }}
                               onchange="updateFormFields()">
                        <div style="flex: 1;">
                            <div style="font-weight: 700; color: var(--text-main);">{{ $driver['name'] }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $driver['description'] }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div id="sqlite-fields" style="display: none;">
            <div class="form-group">
                <label class="form-label">{{ __('setup.steps.database.database') }}</label>
                <div style="padding: 1rem; background: rgba(79, 70, 229, 0.05); border-radius: 0.75rem; border: 1px solid var(--border);">
                    <code style="color: var(--primary); font-weight: 600;">database/database.sqlite</code>
                    <small style="color: var(--text-muted); margin-top: 0.5rem; display: block;">{{ __('setup.steps.database.sqlite_help') }}</small>
                </div>
            </div>
        </div>

        <div id="mysql-fields" style="display: none;">
            <div style="display: grid; gap: 1rem; margin-top: 2rem;">
                <div class="form-group">
                    <label class="form-label">{{ __('setup.steps.database.host') }}</label>
                    <input type="text" name="database_host" id="mysql-host"
                           placeholder="127.0.0.1" value="{{ $formData['database_host'] ?? '127.0.0.1' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('setup.steps.database.port') }}</label>
                    <input type="number" name="database_port" id="mysql-port"
                           placeholder="3306" value="{{ $formData['database_port'] ?? '3306' }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('setup.steps.database.database') }}</label>
                <input type="text" name="database_database" id="mysql-database"
                       placeholder="api_manager" value="{{ $formData['database_database'] ?? 'api_manager' }}">
            </div>

            <div style="display: grid; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">{{ __('setup.steps.database.username') }}</label>
                    <input type="text" name="database_username" id="mysql-username"
                           placeholder="root" value="{{ $formData['database_username'] ?? 'root' }}">
                </div>
                <div class="form-group" style="position: relative;">
                    <label class="form-label">{{ __('setup.steps.database.password') }}</label>
                    <input type="password" name="database_password" id="mysql-password"
                           placeholder="{{ __('setup.steps.database.password_placeholder') }}" value="{{ $formData['database_password'] ?? '' }}">
                    <span class="password-toggle" onclick="togglePassword(event, 'mysql-password')" style="position: absolute; right: 1rem; top: 2.75rem; cursor: pointer;">👁️</span>
                </div>
            </div>
        </div>

        <div id="pgsql-fields" style="display: none;">
            <div style="display: grid; gap: 1rem; margin-top: 2rem;">
                <div class="form-group">
                    <label class="form-label">{{ __('setup.steps.database.host') }}</label>
                    <input type="text" name="database_host" id="pgsql-host"
                           placeholder="127.0.0.1" value="{{ $formData['database_host'] ?? '127.0.0.1' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('setup.steps.database.port') }}</label>
                    <input type="number" name="database_port" id="pgsql-port"
                           placeholder="5432" value="{{ $formData['database_port'] ?? '5432' }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('setup.steps.database.database') }}</label>
                <input type="text" name="database_database" id="pgsql-database"
                       placeholder="api_manager" value="{{ $formData['database_database'] ?? 'api_manager' }}">
            </div>

            <div style="display: grid; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">{{ __('setup.steps.database.username') }}</label>
                    <input type="text" name="database_username" id="pgsql-username"
                           placeholder="postgres" value="{{ $formData['database_username'] ?? 'postgres' }}">
                </div>
                <div class="form-group" style="position: relative;">
                    <label class="form-label">{{ __('setup.steps.database.password') }}</label>
                    <input type="password" name="database_password" id="pgsql-password"
                           placeholder="{{ __('setup.steps.database.password_placeholder') }}" value="{{ $formData['database_password'] ?? '' }}">
                    <span class="password-toggle" onclick="togglePassword(event, 'pgsql-password')" style="position: absolute; right: 1rem; top: 2.75rem; cursor: pointer;">👁️</span>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2.5rem;">
            <button type="button" id="test-connection" class="btn btn-secondary" style="flex: 1;"
                    onclick="testDatabaseConnection(event)">
                <span id="test-text">{{ __('setup.steps.database.test_connection') }}</span>
                <div id="test-spinner" class="spinner" style="display: none; width: 1.25rem; height: 1.25rem;"></div>
            </button>
            <button type="submit" class="btn btn-primary" style="flex: 1.5;">
                {{ __('setup.steps.database.continue') }}
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </div>

        <!-- Zone de résultat du test -->
        <div id="testResult" style="display: none; margin-top: 1.5rem;">
            <div id="testMessage" class="alert"></div>
        </div>
    </form>
</div>

<script>
    // Initialiser l'affichage des champs
    function updateFormFields() {
        const driverSelect = document.querySelector('input[name="database_driver"]:checked');
        if (!driverSelect) return;

        const driver = driverSelect.value;

        // Masquer tous les champs
        document.getElementById('sqlite-fields').style.display = 'none';
        document.getElementById('mysql-fields').style.display = 'none';
        document.getElementById('pgsql-fields').style.display = 'none';

        // Désactiver tous les inputs qui ne sont pas du driver sélectionné
        // pour éviter d'envoyer des données vides/inutiles qui pourraient
        // fausser la validation
        document.querySelectorAll('#mysql-fields input, #pgsql-fields input').forEach(input => {
            input.disabled = true;
        });

        // Afficher les champs du driver sélectionné et activer ses inputs
        if (driver === 'sqlite') {
            document.getElementById('sqlite-fields').style.display = 'block';
        } else if (driver === 'mysql') {
            document.getElementById('mysql-fields').style.display = 'block';
            document.querySelectorAll('#mysql-fields input').forEach(input => {
                input.disabled = false;
            });
        } else if (driver === 'pgsql') {
            document.getElementById('pgsql-fields').style.display = 'block';
            document.querySelectorAll('#pgsql-fields input').forEach(input => {
                input.disabled = false;
            });
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
        const field = document.getElementById(fieldId);
        const type = field.type === 'password' ? 'text' : 'password';
        field.type = type;
        event.target.textContent = type === 'password' ? '👁️' : '👁️‍🗨️';
    }

    // Tester la connexion
    function testDatabaseConnection(event) {
        if (event) event.preventDefault();

        const driverSelect = document.querySelector('input[name="database_driver"]:checked');
        if (!driverSelect) return;

        const driver = driverSelect.value;
        const testBtn = document.getElementById('test-connection');
        const testText = document.getElementById('test-text');
        const testSpinner = document.getElementById('test-spinner');
        const testResult = document.getElementById('testResult');
        const msgDiv = document.getElementById('testMessage');

        // Préparer les données
        const data = {
            database_driver: driver,
        };

        if (driver === 'sqlite') {
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
        testText.style.display = 'none';
        testSpinner.style.display = 'inline-block';
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
                testBtn.disabled = false;
                testText.style.display = 'inline';
                testSpinner.style.display = 'none';
                testResult.style.display = 'block';

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
                testBtn.disabled = false;
                testText.style.display = 'inline';
                testSpinner.style.display = 'none';
                testResult.style.display = 'block';

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
