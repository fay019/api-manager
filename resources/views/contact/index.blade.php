@extends('layouts.app')

@section('title', __('contact.page_title'))

@section('content')
<div class="contact-container">
    <div class="contact-header">
        <h1>{{ __('contact.page_title') }}</h1>
        <p class="contact-subtitle">{{ __('contact.subtitle') }}</p>
    </div>

    @if ($errors->has('error'))
        <div class="alert alert-error">
            {{ $errors->first('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('contact.store') }}" method="POST" class="contact-form">
        @csrf

        <!-- Honeypot field (hidden from users) -->
        <input type="text" name="website" style="display: none;" tabindex="-1" autocomplete="off">

        <!-- Timestamp field (hidden) -->
        <input type="hidden" name="form_timestamp" value="{{ time() }}">

        <!-- Language field (hidden) -->
        <input type="hidden" name="language" value="{{ app()->getLocale() }}">

        <!-- Name Field -->
        <div class="form-group">
            <label for="name">{{ __('contact.name') }} *</label>
            <input
                type="text"
                id="name"
                name="name"
                class="form-input @error('name') is-invalid @enderror"
                value="{{ old('name') }}"
                required
                minlength="3"
                maxlength="50"
                placeholder="{{ __('contact.name_placeholder') }}"
            >
            @error('name')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Email Field -->
        <div class="form-group">
            <label for="email">{{ __('contact.email') }} *</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-input @error('email') is-invalid @enderror"
                value="{{ old('email') }}"
                required
                placeholder="{{ __('contact.email_placeholder') }}"
            >
            @error('email')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Subject Field -->
        <div class="form-group">
            <label for="subject">{{ __('contact.subject') }} *</label>
            <input
                type="text"
                id="subject"
                name="subject"
                class="form-input @error('subject') is-invalid @enderror"
                value="{{ old('subject') }}"
                required
                minlength="5"
                maxlength="100"
                placeholder="{{ __('contact.subject_placeholder') }}"
            >
            @error('subject')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Message Field -->
        <div class="form-group">
            <label for="message">{{ __('contact.message') }} *</label>
            <textarea
                id="message"
                name="message"
                class="form-textarea @error('message') is-invalid @enderror"
                required
                minlength="10"
                maxlength="2000"
                rows="6"
                placeholder="{{ __('contact.message_placeholder') }}"
            >{{ old('message') }}</textarea>
            @error('message')
                <span class="form-error">{{ $message }}</span>
            @enderror
            <p class="form-hint">{{ __('contact.message_hint') }}</p>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-submit">
            {{ __('contact.send_button') }}
        </button>
    </form>

    <!-- Email Alternative -->
    @if($contactEmail = \App\Models\Setting::get('contact_email'))
    <div class="email-alternative">
        <p>{{ __('contact.or_email') }}</p>
        <a href="mailto:{{ $contactEmail }}" class="email-link">{{ $contactEmail }}</a>
    </div>
    @endif
</div>

<style>
    .contact-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .contact-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .contact-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #fff;
        margin: 0 0 10px 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .contact-subtitle {
        font-size: 1.1rem;
        color: rgba(255, 255, 255, 0.8);
        margin: 0;
    }

    .contact-form {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group:last-of-type {
        margin-bottom: 30px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #fff;
        font-size: 0.95rem;
    }

    .form-input,
    .form-textarea {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.95);
        color: #333;
        font-family: inherit;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-input:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #667eea;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-input.is-invalid,
    .form-textarea.is-invalid {
        border-color: #dc2626;
        background: #fef2f2;
    }

    .form-textarea {
        resize: vertical;
        font-family: 'Courier New', monospace;
    }

    .email-alternative {
        text-align: center;
        margin-top: 30px;
        padding-top: 30px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }

    .email-alternative p {
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 10px;
        font-size: 0.95rem;
    }

    .email-link {
        display: inline-block;
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        padding: 8px 16px;
        border: 2px solid #667eea;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .email-link:hover {
        background: #667eea;
        color: #fff;
    }

    html.dark .email-link {
        color: #818cf8;
        border-color: #818cf8;
    }

    html.dark .email-link:hover {
        background: #818cf8;
        color: #1f2937;
    }

    .form-error {
        display: block;
        color: #fca5a5;
        font-size: 0.85rem;
        margin-top: 6px;
    }

    .form-hint {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.7);
        margin: 6px 0 0 0;
    }

    .btn-submit {
        width: 100%;
        padding: 14px 20px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    .alert {
        padding: 15px 20px;
        border-radius: 6px;
        margin-bottom: 25px;
        font-weight: 500;
    }

    .alert-success {
        background: rgba(34, 197, 94, 0.15);
        border: 1px solid rgba(34, 197, 94, 0.3);
        color: #86efac;
    }

    .alert-error {
        background: rgba(220, 38, 38, 0.15);
        border: 1px solid rgba(220, 38, 38, 0.3);
        color: #fca5a5;
    }

    html.dark .form-input,
    html.dark .form-textarea {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.2);
        color: #f3f4f6;
    }

    html.dark .form-input:focus,
    html.dark .form-textarea:focus {
        background: rgba(255, 255, 255, 0.12);
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.1);
    }

    html.dark .form-input.is-invalid,
    html.dark .form-textarea.is-invalid {
        background: rgba(220, 38, 38, 0.1);
        border-color: #dc2626;
    }

    @media (max-width: 640px) {
        .contact-container {
            margin: 30px auto;
        }

        .contact-header h1 {
            font-size: 1.8rem;
        }

        .contact-form {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }
    }

    .alert {
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .alert.fade-out {
        animation: fadeOut 0.5s ease forwards;
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-10px);
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');

        alerts.forEach(alert => {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                alert.classList.add('fade-out');
                // Remove from DOM after animation completes
                setTimeout(function() {
                    alert.remove();
                }, 500);
            }, 5000);
        });
    });
</script>
@endsection
