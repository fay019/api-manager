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

        @if (isset($setup['setup.db_connection']) && in($setup['setup.db_connection'], ['mysql', 'pgsql']))
            <strong>Hôte BD:</strong>
            <span>{{ $setup['setup.db_host'] ?? 'N/A' }}</span>

            <strong>Port BD:</strong>
            <span>{{ $setup['setup.db_port'] ?? 'N/A' }}</span>
        @endif

        <strong>Base de Données:</strong>
        <span>{{ $setup['setup.db_database'] ?? 'N/A' }}</span>

        @if (isset($setup['setup.db_connection']) && in($setup['setup.db_connection'], ['mysql', 'pgsql']))
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
<script>
    document.getElementById('finishBtn').addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<span class="spinner"></span> Installation...';
    });
</script>
@endsection
