@extends('setup.layout')

@section('title', 'Base de Données - Installation')

@section('content')
<div class="setup-header">
    <h1>Base de Données</h1>
    <p>Étape 2 sur 3</p>
</div>

<div class="setup-steps">
    <div class="setup-step completed"></div>
    <div class="setup-step active"></div>
    <div class="setup-step"></div>
</div>

@if ($errors->any())
    <div class="alert alert-error">
        <strong>Erreur:</strong> {{ $errors->first() }}
    </div>
@endif

<form id="dbForm" method="POST" action="{{ route('setup.save-database') }}">
    @csrf

    <div class="form-group">
        <label for="db_connection">Type de Base de Données</label>
        <select id="db_connection" name="db_connection" required onchange="updateDbForm()">
            <option value="sqlite">SQLite (Simple, pour dev)</option>
            <option value="mysql">MySQL (Recommandé)</option>
            <option value="pgsql">PostgreSQL</option>
        </select>
    </div>

    <div id="sqliteFields" style="display: none;">
        <div class="form-group">
            <label for="db_database">Chemin de la Base de Données</label>
            <input
                type="text"
                id="db_database"
                name="db_database"
                placeholder="database/database.sqlite"
                value="{{ old('db_database', 'database/database.sqlite') }}"
            >
        </div>
    </div>

    <div id="mysqlFields" style="display: none;">
        <div class="form-group">
            <label for="db_host">Hôte</label>
            <input
                type="text"
                id="db_host"
                name="db_host"
                placeholder="localhost"
                value="{{ old('db_host', 'localhost') }}"
            >
        </div>

        <div class="form-group">
            <label for="db_port">Port</label>
            <input
                type="number"
                id="db_port"
                name="db_port"
                placeholder="3306"
                value="{{ old('db_port', 3306) }}"
            >
        </div>

        <div class="form-group">
            <label for="db_database">Nom de la Base de Données</label>
            <input
                type="text"
                id="db_database"
                name="db_database"
                placeholder="api_manager"
                value="{{ old('db_database', 'api_manager') }}"
                required
            >
        </div>

        <div class="form-group">
            <label for="db_username">Utilisateur</label>
            <input
                type="text"
                id="db_username"
                name="db_username"
                placeholder="root"
                value="{{ old('db_username', 'root') }}"
            >
        </div>

        <div class="form-group password-group">
            <label for="db_password">Mot de Passe</label>
            <input
                type="password"
                id="db_password"
                name="db_password"
                placeholder="(laisser vide si pas de mot de passe)"
            >
            <span class="password-toggle">👁️</span>
        </div>

        <div style="margin-top: 20px;">
            <button type="button" class="btn btn-secondary" onclick="testDatabase()" style="flex: none;">
                🔍 Tester la connexion
            </button>
        </div>

        <div id="testResult" style="display: none; margin-top: 15px;"></div>
    </div>

    <div class="info-box" style="margin-top: 20px;">
        <strong>SQLite:</strong> Facile à démarrer, idéal pour développement<br>
        <strong>MySQL/PostgreSQL:</strong> Production, meilleure performance
    </div>

    <div class="form-actions" style="margin-top: 30px;">
        <a href="{{ route('setup.general') }}" class="btn btn-secondary">
            ← Retour
        </a>
        <button type="submit" class="btn btn-primary" id="submitBtn">
            Suivant →
        </button>
    </div>
</form>

@endsection

@section('scripts')
<script>
    function updateDbForm() {
        const connection = document.getElementById('db_connection').value;
        const sqliteFields = document.getElementById('sqliteFields');
        const mysqlFields = document.getElementById('mysqlFields');

        if (connection === 'sqlite') {
            sqliteFields.style.display = 'block';
            mysqlFields.style.display = 'none';
        } else {
            sqliteFields.style.display = 'none';
            mysqlFields.style.display = 'block';
        }
    }

    function testDatabase() {
        const form = document.getElementById('dbForm');
        const formData = new FormData(form);

        const btn = event.target;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Test en cours...';

        fetch('{{ route("setup.test-database") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            const resultDiv = document.getElementById('testResult');
            resultDiv.style.display = 'block';

            if (data.success) {
                resultDiv.className = 'alert alert-success';
                resultDiv.textContent = '✅ ' + data.message;
                document.getElementById('submitBtn').disabled = false;
            } else {
                resultDiv.className = 'alert alert-error';
                resultDiv.textContent = '❌ ' + data.message;
                document.getElementById('submitBtn').disabled = true;
            }
        })
        .catch(error => {
            const resultDiv = document.getElementById('testResult');
            resultDiv.className = 'alert alert-error';
            resultDiv.textContent = '❌ Erreur: ' + error.message;
            resultDiv.style.display = 'block';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '🔍 Tester la connexion';
        });
    }

    // Initial form display
    updateDbForm();
</script>
@endsection
