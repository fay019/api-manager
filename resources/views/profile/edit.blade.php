@extends('layouts.app')

@section('title', __('auth.my_profile'))

@section('styles')
    <style>
        * {
            box-sizing: border-box;
        }

        .profile-page {
            display: flex;
            flex-direction: column;
            padding: 20px;
            background: #f9fafb;
        }

        html.dark .profile-page {
            background: rgb(15, 23, 42);
        }

        .profile-container {
            max-width: 900px;
            margin: 0 auto;
            width: 100%;
        }

        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 80px 40px;
            text-align: center;
            color: white;
            margin-bottom: 40px;
            box-shadow: 0 20px 60px rgba(102, 126, 234, 0.2);
            position: relative;
            overflow: hidden;
        }

        html.dark .profile-header {
            background: rgb(30, 41, 59);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: 0;
        }

        .profile-header > * {
            position: relative;
            z-index: 1;
        }

        .profile-header h1 {
            font-size: 2.5em;
            margin-bottom: 15px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .profile-header p {
            font-size: 1.1em;
            opacity: 0.95;
            font-weight: 500;
        }

        .profile-content {
            background: white;
            border-radius: 16px;
            padding: 50px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            border: 1px solid #f3f4f6;
        }

        html.dark .profile-content {
            background: rgb(30, 41, 59);
            border-color: rgb(55, 65, 81);
        }

        .form-section {
            margin-bottom: 50px;
        }

        .form-section:last-of-type {
            margin-bottom: 0;
        }

        .form-section h2 {
            font-size: 1.4em;
            font-weight: 700;
            margin-bottom: 30px;
            color: #1f2937;
            padding-bottom: 15px;
            border-bottom: 2px solid #f3f4f6;
            letter-spacing: -0.3px;
        }

        html.dark .form-section h2 {
            color: #f3f4f6;
            border-bottom-color: rgb(55, 65, 81);
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-group label {
            display: block;
            font-size: 0.95em;
            font-weight: 700;
            margin-bottom: 10px;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85em;
        }

        html.dark .form-group label {
            color: #e5e7eb;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: #f9fafb;
            color: #1f2937;
            font-weight: 500;
        }

        html.dark .form-group input {
            background-color: rgb(55, 65, 81);
            border-color: rgb(75, 85, 99);
            color: #e5e7eb;
        }

        .form-group input::placeholder {
            color: #9ca3af;
        }

        html.dark .form-group input::placeholder {
            color: #6b7280;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background-color: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15), 0 0 0 2px #667eea;
        }

        html.dark .form-group input:focus {
            background-color: rgb(55, 65, 81);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.25), 0 0 0 2px #667eea;
        }

        .form-group input.error {
            border-color: #ef4444;
            background-color: #fef2f2;
        }

        html.dark .form-group input.error {
            background-color: rgb(45, 7, 7);
        }

        .form-group input.error:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15), 0 0 0 2px #ef4444;
        }

        .form-group .hint {
            font-size: 0.85em;
            color: #6b7280;
            margin-top: 8px;
            font-weight: 500;
        }

        html.dark .form-group .hint {
            color: #9ca3af;
        }

        .form-group .error-message {
            color: #ef4444;
            font-size: 0.85em;
            margin-top: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        html.dark .form-group .error-message {
            color: #fca5a5;
        }

        .alert {
            padding: 18px 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid;
        }

        .alert-success {
            background-color: #ecfdf5;
            border-color: #86efac;
            color: #166534;
        }

        html.dark .alert-success {
            background-color: rgb(5, 46, 22);
            border-color: rgb(34, 197, 94);
            color: #86efac;
        }

        .actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-top: 50px;
            padding-top: 40px;
            border-top: 2px solid #f3f4f6;
        }

        html.dark .actions {
            border-top-color: rgb(55, 65, 81);
        }

        .btn {
            padding: 14px 28px;
            border-radius: 10px;
            font-size: 0.95em;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            letter-spacing: 0.3px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        html.dark .btn-primary {
            background: linear-gradient(135deg, #5b6fd9 0%, #6d3f9a 100%) !important;
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.6) !important;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }

        html.dark .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 50px rgba(102, 126, 234, 0.5) !important;
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-link {
            background: none;
            color: #667eea;
            padding: 0;
            font-weight: 700;
            text-decoration: none;
            font-size: 0.95em;
            box-shadow: none;
            gap: 4px;
        }

        .btn-link:hover {
            color: #5568d3;
            text-decoration: underline;
        }

        html.dark .btn-link {
            color: #a5b4fc;
        }

        html.dark .btn-link:hover {
            color: #c7d2fe;
        }

        .btn-danger {
            color: #ef4444;
            background: none;
            padding: 0;
            font-weight: 700;
            font-size: 0.95em;
            box-shadow: none;
            gap: 4px;
        }

        .btn-danger:hover {
            color: #dc2626;
            text-decoration: underline;
        }

        html.dark .btn-danger {
            color: #fca5a5;
        }

        html.dark .btn-danger:hover {
            color: #ef4444;
        }

        .divider {
            border-top: 2px solid #f3f4f6;
            margin: 50px 0;
        }

        html.dark .divider {
            border-top-color: rgb(55, 65, 81);
        }

        .logout-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #f3f4f6;
        }

        html.dark .logout-section {
            border-top-color: rgb(55, 65, 81);
        }

        @media (max-width: 768px) {
            .profile-container {
                padding: 10px;
            }

            .profile-header {
                padding: 50px 30px;
                margin-bottom: 30px;
            }

            .profile-header h1 {
                font-size: 2em;
            }

            .profile-content {
                padding: 30px;
            }

            .actions {
                flex-direction: column-reverse;
                align-items: stretch;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .btn-link {
                width: auto;
            }

            .grid-2 {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }
    </style>
@endsection

@section('content')
<div class="profile-page">
    <div class="profile-container">
        <div class="profile-header">
            <h1>👤 {{ __('auth.my_profile') }}</h1>
            <p>{{ __('auth.profile_description') }}</p>
        </div>

        <div class="profile-content">
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success">
                    <span>✓</span>
                    <span>{{ __('auth.profile_updated') }}</span>
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <!-- User Information -->
                <div class="form-section">
                    <h2>✏️ {{ __('auth.name') }} & {{ __('auth.email') }}</h2>

                    <div class="grid-2">
                        <div class="form-group">
                            <label for="name">{{ __('auth.name') }}</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $user->name) }}"
                                required
                                class="@error('name') error @enderror"
                            />
                            @error('name')
                                <div class="error-message">❌ {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">{{ __('auth.email') }}</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                required
                                class="@error('email') error @enderror"
                            />
                            @error('email')
                                <div class="error-message">❌ {{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Password Change -->
                <div class="form-section">
                    <h2>🔐 {{ __('auth.change_password') }}</h2>
                    <p style="color: #6b7280; margin-bottom: 25px; font-size: 0.95em; font-weight: 500;">
                        💡 {{ __('auth.leave_empty') }}
                    </p>

                    <div class="grid-2">
                        <div class="form-group">
                            <label for="password">{{ __('auth.password') }}</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="@error('password') error @enderror"
                            />
                            <div class="hint">{{ __('auth.password_hint') }}</div>
                            @error('password')
                                <div class="error-message">❌ {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">{{ __('auth.password_confirmation') }}</label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="@error('password_confirmation') error @enderror"
                            />
                            @error('password_confirmation')
                                <div class="error-message">❌ {{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="actions">
                    <a href="{{ route('home') }}" class="btn btn-link">← {{ __('auth.back_home') }}</a>
                    <button type="submit" class="btn btn-primary">💾 {{ __('auth.save_changes') }}</button>
                </div>
            </form>

            <!-- Logout -->
            <div class="logout-section">
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        ↪️ {{ __('auth.logout') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
