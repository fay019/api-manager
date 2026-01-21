@extends('setup.layout')

@section('title', 'Confirmation - Installation')

@section('content')
<div class="setup-header">
    <h1>Confirmation</h1>
    <p>Étape 3 sur 3</p>
</div>

<div class="setup-steps">
    <div class="setup-step completed"></div>
    <div class="setup-step completed"></div>
    <div class="setup-step active"></div>
</div>

@if ($errors->any())
    <div class="alert alert-error">
        <strong>Erreur:</strong> {{ $errors->first() }}
    </div>
@endif

<div style="background: #f3f4f6; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
    <strong>Vérifiez votre configuration:</strong>
</div>

<div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
    <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px; font-size: 14px;">
        <strong>Nom du Site:</strong>
        <span>{{ $setup['setup.site_name'] ?? 'N/A' }}</span>

        <strong>URL:</strong>
        <span>{{ $setup['setup.site_url'] ?? 'N/A' }}</span>

        <strong>Email Admin:</strong>
        <span>{{ $setup['setup.admin_email'] ?? 'N/A' }}</span>

        <strong>Type BD:</strong>
        <span>{{ ucfirst($setup['setup.db_connection'] ?? 'N/A') }}</span>

        @if (isset($setup['setup.db_connection']) && in_array($setup['setup.db_connection'], ['mysql', 'pgsql']))
            <strong>Hôte BD:</strong>
            <span>{{ $setup['setup.db_host'] ?? 'N/A' }}</span>

            <strong>Port BD:</strong>
            <span>{{ $setup['setup.db_port'] ?? 'N/A' }}</span>
        @endif

        <strong>Base de Données:</strong>
        <span>{{ $setup['setup.db_database'] ?? 'N/A' }}</span>

        @if (isset($setup['setup.db_connection']) && in_array($setup['setup.db_connection'], ['mysql', 'pgsql']))
            <strong>Utilisateur BD:</strong>
            <span>{{ $setup['setup.db_username'] ?? 'N/A' }}</span>
        @endif
    </div>
</div>

<div class="info-box">
    <strong>Prêt à installer!</strong><br>
    En cliquant sur "Finaliser", nous allons:<br>
    • Configurer votre .env<br>
    • Créer les tables<br>
    • Créer votre compte admin<br>
    • Activer l'application
</div>

<form method="POST" action="{{ route('setup.finish') }}">
    @csrf

    <div class="form-actions">
        <a href="{{ route('setup.database') }}" class="btn btn-secondary">
            ← Retour
        </a>
        <button type="submit" class="btn btn-primary" id="finishBtn">
            ✨ Finaliser
        </button>
    </div>
</form>

@endsection

@section('scripts')
<style>
    .installation-container {
        display: none;
        text-align: center;
        padding: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 8px;
        color: white;
    }

    .installation-container.active {
        display: block;
    }

    .installation-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-top: 4px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .installation-steps {
        text-align: left;
        max-width: 400px;
        margin: 20px auto;
        font-size: 14px;
    }

    .installation-step {
        padding: 8px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .installation-step:last-child {
        border-bottom: none;
    }

    .step-icon {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }

    .step-icon.pending {
        background: rgba(255, 255, 255, 0.2);
        color: rgba(255, 255, 255, 0.5);
    }

    .step-icon.active {
        background: white;
        color: #667eea;
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    .step-icon.done {
        background: rgba(76, 175, 80, 0.8);
        color: white;
    }

    .installation-message {
        margin-top: 30px;
        font-size: 12px;
        opacity: 0.8;
    }
</style>

<div id="installationContainer" class="installation-container">
    <div class="installation-spinner"></div>
    <h2>Installation en cours...</h2>
    <p>Veuillez patienter, ne fermez pas cette page.</p>

    <div class="installation-steps">
        <div class="installation-step">
            <div class="step-icon active" id="step1Icon">⚙️</div>
            <span id="step1Text">Configuration .env</span>
        </div>
        <div class="installation-step">
            <div class="step-icon pending" id="step2Icon">2</div>
            <span id="step2Text">Migrations bases de données</span>
        </div>
        <div class="installation-step">
            <div class="step-icon pending" id="step3Icon">3</div>
            <span id="step3Text">Initialisation données</span>
        </div>
        <div class="installation-step">
            <div class="step-icon pending" id="step4Icon">4</div>
            <span id="step4Text">Création compte admin</span>
        </div>
    </div>

    <div class="installation-message">
        ⏱️ Cette opération peut prendre jusqu'à 30-60 secondes selon votre serveur.
    </div>
</div>

<script>
    document.getElementById('finishBtn').addEventListener('click', function() {
        // Cacher le formulaire
        document.querySelector('.form-actions').style.display = 'none';
        document.querySelector('div[style="background: #f3f4f6"]').style.display = 'none';
        document.querySelector('div[style="background: white"]').style.display = 'none';
        document.querySelector('.info-box').style.display = 'none';

        // Afficher le container d'installation
        document.getElementById('installationContainer').classList.add('active');

        // Augmenter le timeout pour les longues opérations
        this.form.submit();
    });

    // Optionnel: Mettre à jour les étapes si la requête prend trop longtemps
    setTimeout(() => {
        const container = document.getElementById('installationContainer');
        if (container.classList.contains('active')) {
            const message = container.querySelector('.installation-message');
            if (message) {
                message.innerHTML = '⚠️ L\'installation prend plus de temps que prévu... Vérifiez votre connexion et attendez.';
            }
        }
    }, 15000);
</script>
@endsection
