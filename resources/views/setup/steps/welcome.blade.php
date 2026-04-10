@extends('setup.layout')

@section('content')
<div class="setup-body">
    <div class="setup-steps">
        <div class="setup-step active"></div>
        <div class="setup-step"></div>
        <div class="setup-step"></div>
        <div class="setup-step"></div>
        <div class="setup-step"></div>
        <div class="setup-step"></div>
    </div>

    <div class="flex justify-between items-center mb-6">
        <h2 class="mb-0">
            <span style="color: var(--primary)">👋</span>
            {{ __('setup.steps.welcome.title') }}
        </h2>

        <form id="locale-form" action="{{ route('setup.welcome.locale', ['setup_token' => app(\App\Services\Installation\SetupSession::class)->getToken()]) }}" method="POST" class="flex items-center gap-2">
            @csrf
            <input type="hidden" name="_setup_token" value="{{ app(\App\Services\Installation\SetupSession::class)->getToken() }}">
            <select name="locale" id="locale" onchange="changeLocale(this.value)" style="padding: 0.5rem 2rem 0.5rem 1rem; font-size: 0.875rem; border-radius: 0.5rem; border: 1px solid var(--border); background-color: var(--card-bg); color: var(--text-main);">
                <option value="fr" {{ app()->getLocale() === 'fr' ? 'selected' : '' }}>🇫🇷 Français</option>
                <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>🇺🇸 English</option>
                <option value="de" {{ app()->getLocale() === 'de' ? 'selected' : '' }}>🇩🇪 Deutsch</option>
            </select>
        </form>
    </div>

    @if($canContinue)
        <div class="alert alert-success">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ __('setup.steps.welcome.success') }}
        </div>

        <p style="color: var(--text-muted); line-height: 1.6; margin-bottom: 2rem;">
            {{ __('setup.steps.welcome.description', ['default' => 'Bienvenue dans l\'assistant d\'installation. Nous allons configurer votre application en quelques étapes simples.']) }}
        </p>

        <a href="{{ route('setup.app-settings', ['setup_token' => app(\App\Services\Installation\SetupSession::class)->getToken()]) }}" class="btn btn-primary">
            {{ __('setup.steps.welcome.continue') }}
            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </a>
    @else
        <div class="alert alert-error">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            {{ __('setup.steps.welcome.error') }}
        </div>

        <ul style="background: rgba(239, 68, 68, 0.05); padding: 1rem 1rem 1rem 2.5rem; border-radius: 0.75rem; color: var(--error); margin-bottom: 2rem;">
            @foreach($checkResults['errors'] as $error)
                <li style="margin-bottom: 0.5rem;">{{ $error }}</li>
            @endforeach
        </ul>

        <button class="btn btn-secondary" onclick="location.reload()" style="width: 100%">
            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            {{ __('setup.steps.welcome.retry') }}
        </button>
    @endif
</div>
@endsection

@section('scripts')
<script>
    function changeLocale(locale) {
        document.getElementById('locale-form').submit();
    }
</script>
@endsection
