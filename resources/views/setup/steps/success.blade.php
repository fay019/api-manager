@extends('setup.layout')

@section('content')
<div class="success-container">
    <div class="success-icon">✅</div>

    <div class="setup-header">
        <h1>{{ __('setup.steps.success.title') }}</h1>
        <p>{{ __('setup.steps.success.subtitle', ['current' => $currentStep, 'total' => $totalSteps]) }}</p>
    </div>

    <div class="success-content">
        <p style="font-size: 16px; margin-bottom: 20px; color: #333;">
            {{ __('setup.steps.success.congratulations') }}
        </p>

        <div class="info-box success-box">
            <strong>{{ __('setup.steps.success.next_steps_title') }}</strong>
            <ul style="margin: 12px 0 0 0; padding-left: 20px; font-size: 13px;">
                <li>{{ __('setup.steps.success.next_steps.redirect') }}</li>
                <li>{{ __('setup.steps.success.next_steps.login') }}</li>
                <li>{{ __('setup.steps.success.next_steps.dashboard') }}</li>
            </ul>
        </div>

        <div style="margin: 20px 0; padding: 15px; background: #fffbeb; border-radius: 6px; border-left: 3px solid #f59e0b;">
            <strong style="color: #92400e;">{{ __('setup.steps.success.manual_actions_title') }}</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px; font-size: 12px; color: #b45309; text-align: left;">
                <li><strong>{{ __('setup.steps.success.manual_actions.cookies') }}</strong></li>
                <li><strong>{{ __('setup.steps.success.manual_actions.sqlite') }}</strong></li>
                <li><strong>{{ __('setup.steps.success.manual_actions.security') }}</strong></li>
            </ul>
        </div>

        <div style="margin: 20px 0; padding: 15px; background: #f3f4f6; border-radius: 6px; border-left: 3px solid #667eea;">
            <strong style="color: #333;">{{ __('setup.steps.success.important_info_title') }}</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px; font-size: 12px; color: #666; text-align: left;">
                <li>{{ __('setup.steps.success.important_info.blocked') }}</li>
                <li>{{ __('setup.steps.success.important_info.env') }}</li>
                <li>{{ __('setup.steps.success.important_info.users') }}</li>
                <li>{{ __('setup.steps.success.important_info.safe') }}</li>
            </ul>
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('login.show') }}" class="btn btn-primary">
            🚀 {{ __('setup.steps.success.access_login') }}
        </a>
    </div>

    <!-- Compteur décompte -->
    <div style="text-align: center; margin-top: 30px; color: #999; font-size: 12px;">
        {{ __('setup.steps.success.redirection', ['seconds' => '<span id="countdown">5</span>']) }}
    </div>
</div>

<script>
    // Décompte redirection
    let count = 5;
    const countdownEl = document.getElementById('countdown');

    const interval = setInterval(() => {
        count--;
        countdownEl.textContent = count;

        if (count === 0) {
            clearInterval(interval);
            window.location.href = "{{ route('login.show') }}";
        }
    }, 1000);
</script>

<style>
    .success-container {
        text-align: center;
    }

    .success-icon {
        font-size: 60px;
        margin-bottom: 20px;
        animation: bounce 1s ease-in-out infinite;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .success-content {
        margin-bottom: 30px;
    }

    .success-box {
        background: #dcfce7;
        border-left-color: #22c55e;
        color: #166534;
    }

    .info-box {
        background: #f3f4f6;
        border-left: 3px solid #667eea;
        padding: 12px 16px;
        border-radius: 4px;
        font-size: 13px;
        color: #333;
    }

    .setup-header h1 {
        margin-bottom: 10px;
        color: #22c55e;
    }

    .setup-header p {
        color: #666;
        font-size: 14px;
    }
</style>
@endsection

