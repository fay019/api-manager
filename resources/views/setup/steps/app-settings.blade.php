@extends('setup.layout')

@section('content')
<h2>Paramètres Applicatifs</h2>
<p>Étape 2/7 - Configuration de l'application</p>

<form method="POST" action="{{ route('setup.app-settings.store', ['setup_token' => app(\App\Services\Installation\SetupSession::class)->getToken()]) }}">
    <input type="hidden" name="_setup_token" value="{{ app(\App\Services\Installation\SetupSession::class)->getCsrfToken() }}">
    <!-- Token debug: {{ app(\App\Services\Installation\SetupSession::class)->getCsrfToken() }} -->

    <div class="form-group">
        <label class="form-label">Nom de l'application</label>
        <input type="text" name="app_name" class="form-control @if(isset($errors['app_name'])) is-invalid @endif"
               value="{{ old('app_name', $formData['app_name'] ?? '') }}" required>
        @if(isset($errors['app_name'])) <span class="invalid-feedback">{{ is_array($errors['app_name']) ? $errors['app_name'][0] : $errors['app_name'] }}</span> @endif
    </div>

    <div class="form-group">
        <label class="form-label">URL de l'application</label>
        <input type="url" name="app_url" class="form-control @if(isset($errors['app_url'])) is-invalid @endif"
               value="{{ old('app_url', $formData['app_url'] ?? '') }}" required>
        @if(isset($errors['app_url'])) <span class="invalid-feedback">{{ is_array($errors['app_url']) ? $errors['app_url'][0] : $errors['app_url'] }}</span> @endif
    </div>

    <div class="form-group">
        <label class="form-label">Environnement</label>
        <select name="app_env" class="form-control @if(isset($errors['app_env'])) is-invalid @endif" required>
            <option value="">-- Sélectionner --</option>
            @foreach($environments ?? ['local' => 'Local (Développement)', 'staging' => 'Staging (Test)', 'production' => 'Production'] as $key => $label)
                <option value="{{ $key }}" @selected(old('app_env', $formData['app_env'] ?? '') === $key)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @if(isset($errors['app_env'])) <span class="invalid-feedback">{{ is_array($errors['app_env']) ? $errors['app_env'][0] : $errors['app_env'] }}</span> @endif
    </div>

    <div class="form-group">
        <label class="form-label">Mode Debug</label>
        <div class="form-check">
            <input type="checkbox" name="app_debug" value="1" class="form-check-input @if(isset($errors['app_debug'])) is-invalid @endif"
                   @checked(old('app_debug', $formData['app_debug'] ?? false))>
            <label class="form-check-label">Activer le mode debug</label>
        </div>
        @if(isset($errors['app_debug'])) <span class="invalid-feedback" style="display: block;">{{ is_array($errors['app_debug']) ? $errors['app_debug'][0] : $errors['app_debug'] }}</span> @endif
    </div>

    <div class="form-group">
        <label class="form-label">Fuseau horaire</label>
        <select name="timezone" class="form-control @if(isset($errors['timezone'])) is-invalid @endif" required>
            <option value="">-- Sélectionner --</option>
            @foreach($timezones ?? [] as $tz)
                <option value="{{ $tz }}" @selected(old('timezone', $formData['timezone'] ?? '') === $tz)>
                    {{ $tz }}
                </option>
            @endforeach
        </select>
        @if(isset($errors['timezone'])) <span class="invalid-feedback">{{ is_array($errors['timezone']) ? $errors['timezone'][0] : $errors['timezone'] }}</span> @endif
    </div>

    <div class="form-group">
        <label class="form-label">Langue par défaut</label>
        <select name="locale" class="form-control @if(isset($errors['locale'])) is-invalid @endif" required>
            <option value="">-- Sélectionner --</option>
            @foreach($locales ?? ['fr' => 'Français', 'en' => 'English', 'es' => 'Español'] as $code => $label)
                <option value="{{ $code }}" @selected(old('locale', $formData['locale'] ?? '') === $code)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @if(isset($errors['locale'])) <span class="invalid-feedback">{{ is_array($errors['locale']) ? $errors['locale'][0] : $errors['locale'] }}</span> @endif
    </div>

    <button type="submit" class="btn btn-primary">Continuer →</button>
</form>
@endsection
