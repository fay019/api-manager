@extends('setup.layout')

@section('title', 'Infos Générales - Installation')

@section('content')
<div class="setup-header">
    <h1>Infos Générales</h1>
    <p>Étape 1 sur 3</p>
</div>

<div class="setup-steps">
    <div class="setup-step active"></div>
    <div class="setup-step"></div>
    <div class="setup-step"></div>
</div>

@if ($errors->any())
    <div class="alert alert-error">
        <strong>Erreur:</strong>
        <ul style="margin: 5px 0 0 20px; padding: 0;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('setup.save-general') }}">
    @csrf

    <div class="form-group">
        <label for="site_name">Nom du Site</label>
        <input
            type="text"
            id="site_name"
            name="site_name"
            placeholder="ex: Mon API Manager"
            value="{{ old('site_name', 'API Manager') }}"
            required
        >
        @error('site_name')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="site_url">URL de l'Application</label>
        <input
            type="url"
            id="site_url"
            name="site_url"
            placeholder="https://api.example.com"
            value="{{ old('site_url', 'http://localhost:8000') }}"
            required
        >
        @error('site_url')
            <div class="error">{{ $message }}</div>
        @enderror
        <div style="font-size: 12px; color: #666; margin-top: 5px;">
            Utilisée dans les emails et configurations
        </div>
    </div>

    <div class="form-group">
        <label for="admin_email">Email Administrateur</label>
        <input
            type="email"
            id="admin_email"
            name="admin_email"
            placeholder="admin@example.com"
            value="{{ old('admin_email', 'admin@example.com') }}"
            required
        >
        @error('admin_email')
            <div class="error">{{ $message }}</div>
        @enderror
        <div style="font-size: 12px; color: #666; margin-top: 5px;">
            Utilisé pour vous connecter au panel admin
        </div>
    </div>

    <div class="form-group password-group">
        <label for="admin_password">Mot de Passe</label>
        <input
            type="password"
            id="admin_password"
            name="admin_password"
            placeholder="••••••••"
            minlength="8"
            required
        >
        <span class="password-toggle">👁️</span>
        @error('admin_password')
            <div class="error">{{ $message }}</div>
        @enderror
        <div style="font-size: 12px; color: #666; margin-top: 5px;">
            Minimum 8 caractères
        </div>
    </div>

    <div class="form-group password-group">
        <label for="admin_password_confirmation">Confirmer le Mot de Passe</label>
        <input
            type="password"
            id="admin_password_confirmation"
            name="admin_password_confirmation"
            placeholder="••••••••"
            minlength="8"
            required
        >
        <span class="password-toggle">👁️</span>
        @error('admin_password_confirmation')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-actions">
        <a href="{{ route('setup.index') }}" class="btn btn-secondary">
            ← Retour
        </a>
        <button type="submit" class="btn btn-primary">
            Suivant →
        </button>
    </div>
</form>
@endsection
