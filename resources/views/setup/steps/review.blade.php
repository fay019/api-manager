@extends('setup.layout')

@section('content')
<div class="setup-header">
    <h1>Réviser la Configuration</h1>
    <p>Étape {{ $currentStep }}/{{ $totalSteps }}</p>
</div>

@if (!$isComplete)
    <div class="alert alert-error">
        <strong>⚠️ Configuration incomplète!</strong>
        <ul style="margin-top: 8px; padding-left: 20px; margin-bottom: 0;">
            @foreach ($warnings as $warning)
                <li>{{ $warning }}</li>
            @endforeach
        </ul>
        <p style="margin-top: 8px; font-size: 12px;">Veuillez compléter les étapes précédentes avant de continuer.</p>
    </div>
@else
    <div class="alert alert-success">
        ✅ Configuration complète et valide. Prêt pour l'installation.
    </div>
@endif

<!-- Section Paramètres Applicatifs -->
<div class="review-section">
    <div class="review-header">
        <h3>📋 Paramètres Applicatifs</h3>
    </div>
    <div class="review-grid">
        <div class="review-item">
            <div class="review-label">Nom de l'Application</div>
            <div class="review-value">{{ $appSettings['app_name'] }}</div>
        </div>
        <div class="review-item">
            <div class="review-label">URL d'accès</div>
            <div class="review-value">{{ $appSettings['app_url'] }}</div>
        </div>
        <div class="review-item">
            <div class="review-label">Environnement</div>
            <div class="review-value">
                <span style="background: {{ $appSettings['app_env'] === 'production' ? '#fee2e2' : '#dcfce7' }}; padding: 2px 8px; border-radius: 3px; font-size: 12px;">
                    {{ ucfirst($appSettings['app_env']) }}
                </span>
            </div>
        </div>
        <div class="review-item">
            <div class="review-label">Fuseau Horaire</div>
            <div class="review-value">{{ $appSettings['timezone'] }}</div>
        </div>
        <div class="review-item">
            <div class="review-label">Langue</div>
            <div class="review-value">{{ strtoupper($appSettings['locale']) }}</div>
        </div>
    </div>
</div>

<!-- Section Base de Données -->
<div class="review-section">
    <div class="review-header">
        <h3>🗄️ Base de Données</h3>
    </div>
    <div class="review-grid">
        <div class="review-item">
            <div class="review-label">Type</div>
            <div class="review-value">{{ ucfirst($database['driver']) }}</div>
        </div>
        @if ($database['driver'] !== 'sqlite')
            <div class="review-item">
                <div class="review-label">Serveur</div>
                <div class="review-value">{{ $database['host'] }}:{{ $database['port'] }}</div>
            </div>
            <div class="review-item">
                <div class="review-label">Utilisateur</div>
                <div class="review-value">{{ $database['username'] }}</div>
            </div>
        @endif
        <div class="review-item">
            <div class="review-label">Base de Données</div>
            <div class="review-value">{{ $database['database'] }}</div>
        </div>
    </div>
</div>

<!-- Section Email -->
<div class="review-section">
    <div class="review-header">
        <h3>📧 Configuration Email</h3>
    </div>
    <div class="review-grid">
        <div class="review-item">
            <div class="review-label">Type</div>
            <div class="review-value">{{ ucfirst($mail['driver']) }}</div>
        </div>
        @if ($mail['driver'] === 'smtp')
            <div class="review-item">
                <div class="review-label">Serveur SMTP</div>
                <div class="review-value">{{ $mail['host'] }}:{{ $mail['port'] }}</div>
            </div>
        @endif
        <div class="review-item">
            <div class="review-label">Adresse Source</div>
            <div class="review-value">{{ $mail['from_address'] }} ({{ $mail['from_name'] }})</div>
        </div>
    </div>
</div>

<!-- Section Administrateur -->
<div class="review-section">
    <div class="review-header">
        <h3>👤 Administrateur</h3>
    </div>
    <div class="review-grid">
        <div class="review-item">
            <div class="review-label">Nom Complet</div>
            <div class="review-value">{{ $admin['name'] }}</div>
        </div>
        <div class="review-item">
            <div class="review-label">Email</div>
            <div class="review-value">{{ $admin['email'] }}</div>
        </div>
        <div class="review-item">
            <div class="review-label">Mot de Passe</div>
            <div class="review-value" style="color: #999;">••••••••</div>
        </div>
    </div>
</div>

<!-- Info importantes -->
<div class="review-section">
    <div class="info-box">
        ℹ️ <strong>Notes importantes:</strong>
        <ul style="margin: 8px 0 0 0; padding-left: 20px; font-size: 12px;">
            <li>Les informations sensibles (mots de passe) ne sont pas affichées</li>
            <li>Une fois l'installation complète, vous devrez vous connecter avec l'email et mot de passe de l'administrateur</li>
            <li>Conservez ces informations en lieu sûr</li>
            <li>Vous pourrez créer d'autres utilisateurs après l'installation</li>
        </ul>
    </div>
</div>

<!-- Actions -->
<form method="POST" action="{{ route('setup.install', ['setup_token' => app(\App\Services\Installation\SetupSession::class)->getToken()]) }}">
    <input type="hidden" name="_setup_token" value="{{ app(\App\Services\Installation\SetupSession::class)->getCsrfToken() }}">
    <div class="form-actions">
        <button type="submit" class="btn btn-primary" {{ !$isComplete ? 'disabled' : '' }}>
            🚀 Installer l'Application →
        </button>
    </div>
</form>

<style>
    .review-section {
        margin-bottom: 25px;
    }

    .review-header {
        border-bottom: 2px solid #667eea;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .review-header h3 {
        margin: 0;
        color: #333;
        font-size: 16px;
    }

    .review-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
    }

    .review-item {
        padding: 12px;
        background: #f9fafb;
        border-radius: 6px;
        border-left: 3px solid #667eea;
    }

    .review-label {
        font-size: 12px;
        font-weight: 600;
        color: #666;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .review-value {
        font-size: 14px;
        color: #333;
        word-break: break-word;
    }

    .info-box {
        background: #f3f4f6;
        border-left: 4px solid #667eea;
        padding: 12px 16px;
        border-radius: 4px;
        font-size: 13px;
        color: #333;
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
