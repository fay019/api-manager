@extends('setup.layout')

@section('content')
<div class="setup-header">
    <h1>{{ __('setup.steps.review.title') }}</h1>
    <p>{{ __('setup.steps.review.subtitle', ['current' => $currentStep, 'total' => $totalSteps]) }}</p>
</div>

@if (!$isComplete)
    <div class="alert alert-error">
        <strong>⚠️ {{ __('setup.steps.review.incomplete') }}</strong>
        <ul style="margin-top: 8px; padding-left: 20px; margin-bottom: 0;">
            @foreach ($warnings as $warning)
                <li>{{ $warning }}</li>
            @endforeach
        </ul>
        <p style="margin-top: 8px; font-size: 12px;">{{ __('setup.steps.review.incomplete_help') }}</p>
    </div>
@else
    <div class="alert alert-success">
        ✅ {{ __('setup.steps.review.complete') }}
    </div>
@endif

<!-- Section Paramètres Applicatifs -->
<div class="review-section">
    <div class="review-header">
        <h3>📋 {{ __('setup.steps.review.sections.app') }}</h3>
    </div>
    <div class="review-grid">
        <div class="review-item">
            <div class="review-label">{{ __('setup.steps.review.labels.app_name') }}</div>
            <div class="review-value">{{ $appSettings['app_name'] }}</div>
        </div>
        <div class="review-item">
            <div class="review-label">{{ __('setup.steps.review.labels.app_url') }}</div>
            <div class="review-value">{{ $appSettings['app_url'] }}</div>
        </div>
        <div class="review-item">
            <div class="review-label">{{ __('setup.steps.review.labels.environment') }}</div>
            <div class="review-value">
                <span class="badge badge-{{ $appSettings['app_env'] === 'production' ? 'danger' : 'success' }}">
                    {{ ucfirst($appSettings['app_env']) }}
                </span>
            </div>
        </div>
        <div class="review-item">
            <div class="review-label">{{ __('setup.steps.review.labels.timezone') }}</div>
            <div class="review-value">{{ $appSettings['timezone'] }}</div>
        </div>
        <div class="review-item">
            <div class="review-label">{{ __('setup.steps.review.labels.locale') }}</div>
            <div class="review-value">{{ strtoupper($appSettings['locale']) }}</div>
        </div>
    </div>
</div>

<!-- Section Base de Données -->
<div class="review-section">
    <div class="review-header">
        <h3>🗄️ {{ __('setup.steps.review.sections.database') }}</h3>
    </div>
    <div class="review-grid">
        <div class="review-item">
            <div class="review-label">{{ __('setup.steps.review.labels.type') }}</div>
            <div class="review-value">{{ ucfirst($database['driver']) }}</div>
        </div>
        @if ($database['driver'] !== 'sqlite')
            <div class="review-item">
                <div class="review-label">{{ __('setup.steps.review.labels.host') }}</div>
                <div class="review-value">{{ $database['host'] }}:{{ $database['port'] }}</div>
            </div>
            <div class="review-item">
                <div class="review-label">{{ __('setup.steps.review.labels.username') }}</div>
                <div class="review-value">{{ $database['username'] }}</div>
            </div>
        @endif
        <div class="review-item">
            <div class="review-label">{{ __('setup.steps.review.labels.database') }}</div>
            <div class="review-value">{{ $database['database'] }}</div>
        </div>
    </div>
</div>

<!-- Section Email -->
<div class="review-section">
    <div class="review-header">
        <h3>📧 {{ __('setup.steps.review.sections.mail') }}</h3>
    </div>
    <div class="review-grid">
        <div class="review-item">
            <div class="review-label">{{ __('setup.steps.review.labels.type') }}</div>
            <div class="review-value">{{ ucfirst($mail['driver']) }}</div>
        </div>
        @if ($mail['driver'] === 'smtp')
            <div class="review-item">
                <div class="review-label">{{ __('setup.steps.review.labels.smtp_server') }}</div>
                <div class="review-value">{{ $mail['host'] }}:{{ $mail['port'] }}</div>
            </div>
        @endif
        <div class="review-item">
            <div class="review-label">{{ __('setup.steps.review.labels.from') }}</div>
            <div class="review-value">{{ $mail['from_address'] }} ({{ $mail['from_name'] }})</div>
        </div>
    </div>
</div>

<!-- Section Administrateur -->
<div class="review-section">
    <div class="review-header">
        <h3>👤 {{ __('setup.steps.review.sections.admin') }}</h3>
    </div>
    <div class="review-grid">
        <div class="review-item">
            <div class="review-label">{{ __('setup.steps.review.labels.full_name') }}</div>
            <div class="review-value">{{ $admin['name'] }}</div>
        </div>
        <div class="review-item">
            <div class="review-label">{{ __('setup.steps.review.labels.email') }}</div>
            <div class="review-value">{{ $admin['email'] }}</div>
        </div>
        <div class="review-item">
            <div class="review-label">{{ __('setup.steps.review.labels.password') }}</div>
            <div class="review-value review-password">••••••••</div>
        </div>
    </div>
</div>

<!-- Info importantes -->
<div class="review-section">
    <div class="info-box info-review">
        ℹ️ <strong>{{ __('setup.steps.review.notes_title') }}</strong>
        <ul class="info-list">
            <li>{{ __('setup.steps.review.notes.passwords') }}</li>
            <li>{{ __('setup.steps.review.notes.login') }}</li>
            <li>{{ __('setup.steps.review.notes.safe') }}</li>
            <li>{{ __('setup.steps.review.notes.others') }}</li>
        </ul>
    </div>
</div>

<!-- Actions -->
<form method="POST" action="{{ route('setup.install', ['setup_token' => app(\App\Services\Installation\SetupSession::class)->getToken()]) }}">
    <input type="hidden" name="_setup_token" value="{{ app(\App\Services\Installation\SetupSession::class)->getCsrfToken() }}">
    <div class="form-actions">
        <button type="submit" class="btn btn-primary" {{ !$isComplete ? 'disabled' : '' }}>
            🚀 {{ __('setup.steps.review.install') }}
        </button>
    </div>
</form>

<style>
    .review-section {
        margin-bottom: 25px;
    }

    .review-header {
        border-bottom: 2px solid var(--primary);
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .review-header h3 {
        margin: 0;
        color: var(--text-main);
        font-size: 1.1rem;
        font-weight: 700;
    }

    .review-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
    }

    .review-item {
        padding: 12px;
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 12px;
        border-left: 4px solid var(--primary);
        transition: all 0.2s ease;
    }

    .review-item:hover {
        border-color: var(--primary);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .review-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-muted);
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .review-value {
        font-size: 0.95rem;
        color: var(--text-main);
        word-break: break-word;
        font-weight: 500;
    }

    .review-password {
        color: var(--text-muted);
        letter-spacing: 2px;
    }

    .badge {
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.1);
        color: var(--error);
    }

    .info-review {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-left: 4px solid var(--primary);
        padding: 16px;
        border-radius: 12px;
        color: var(--text-main);
    }

    .info-list {
        margin: 10px 0 0 0;
        padding-left: 20px;
        font-size: 0.85rem;
        color: var(--text-muted);
        line-height: 1.5;
    }

    .info-list li {
        margin-bottom: 4px;
    }

    .setup-header h1 {
        margin-bottom: 10px;
    }

    .setup-header p {
        color: var(--text-muted);
        font-size: 1rem;
    }

    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn:disabled:hover {
        transform: none;
    }

    html.dark .review-item {
        background: rgba(31, 41, 55, 0.5);
    }

    html.dark .info-review {
        background: rgba(31, 41, 55, 0.5);
    }
</style>
@endsection
