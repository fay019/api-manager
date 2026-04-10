@extends('setup.layout')

@section('content')
<div class="setup-body">
    <div class="setup-steps">
        <div class="setup-step completed"></div>
        <div class="setup-step active"></div>
        <div class="setup-step"></div>
        <div class="setup-step"></div>
        <div class="setup-step"></div>
        <div class="setup-step"></div>
    </div>

    <h2>
        <span style="color: var(--primary)">⚙️</span>
        {{ __('setup.steps.app_settings.title') }}
    </h2>
    <p style="color: var(--text-muted); margin-bottom: 2rem;">{{ __('setup.steps.app_settings.subtitle') }}</p>

    <form method="POST" action="{{ route('setup.app-settings.store', ['setup_token' => app(\App\Services\Installation\SetupSession::class)->getToken()]) }}">
        @csrf
        <input type="hidden" name="_setup_token" value="{{ app(\App\Services\Installation\SetupSession::class)->getToken() }}">

        <div class="form-group">
            <label class="form-label">{{ __('setup.steps.app_settings.app_name') }}</label>
            <input type="text" name="app_name" class="form-control @if(isset($errors['app_name'])) is-invalid @endif"
                   value="{{ old('app_name', $formData['app_name'] ?? 'API Manager') }}" required placeholder="Ex: Mon Application API">
            @if(isset($errors['app_name'])) <span class="error">{{ is_array($errors['app_name']) ? $errors['app_name'][0] : $errors['app_name'] }}</span> @endif
        </div>

        <div class="form-group">
            <label class="form-label">{{ __('setup.steps.app_settings.app_url') }}</label>
            <input type="url" name="app_url" class="form-control @if(isset($errors['app_url'])) is-invalid @endif"
                   value="{{ old('app_url', $formData['app_url'] ?? (isset($_SERVER['HTTP_HOST']) ? (isset($_SERVER['HTTPS']) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'] : 'http://localhost')) }}" required placeholder="https://votre-site.com">
            @if(isset($errors['app_url'])) <span class="error">{{ is_array($errors['app_url']) ? $errors['app_url'][0] : $errors['app_url'] }}</span> @endif
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label">{{ __('setup.steps.app_settings.environment') }}</label>
                <select name="app_env" required>
                    @foreach($environments ?? [
                        'local' => __('setup.steps.app_settings.environments.local'),
                        'staging' => __('setup.steps.app_settings.environments.staging'),
                        'production' => __('setup.steps.app_settings.environments.production')
                    ] as $key => $label)
                        <option value="{{ $key }}" @selected(old('app_env', $formData['app_env'] ?? 'local') === $key)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('setup.steps.app_settings.locale') }}</label>
                <select name="locale" required>
                    @foreach($locales ?? ['fr' => '🇫🇷 Français', 'en' => '🇺🇸 English', 'de' => '🇩🇪 Deutsch'] as $code => $label)
                        <option value="{{ $code }}" @selected(old('locale', $formData['locale'] ?? app()->getLocale()) === $code)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">{{ __('setup.steps.app_settings.timezone') }}</label>
            <select name="timezone" required>
                @foreach($timezones ?? ['UTC', 'Europe/Paris', 'America/New_York'] as $tz)
                    <option value="{{ $tz }}" @selected(old('timezone', $formData['timezone'] ?? 'Europe/Paris') === $tz)>
                        {{ $tz }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="background: rgba(79, 70, 229, 0.05); padding: 1.25rem; border-radius: 1rem; margin-bottom: 2rem; border: 1px solid var(--border);">
            <div class="form-group" style="margin-bottom: 0.75rem;">
                <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; margin-bottom: 0;">
                    <input type="checkbox" name="app_debug" value="1" style="width: auto;" @checked(old('app_debug', $formData['app_debug'] ?? true))>
                    <span>{{ __('setup.steps.app_settings.debug_enable') }}</span>
                </label>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; margin-bottom: 0.25rem;">
                    <input type="checkbox" name="allow_production_reset" value="1" style="width: auto;" @checked(old('allow_production_reset', $formData['allow_production_reset'] ?? false))>
                    <span>{{ __('setup.steps.app_settings.reset_allow') }}</span>
                </label>
                <p style="margin: 0 0 0 2rem; font-size: 0.75rem; color: var(--text-muted);">{{ __('setup.steps.app_settings.reset_help') }}</p>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            {{ __('setup.steps.app_settings.continue') }}
            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </button>
    </form>
</div>
@endsection
