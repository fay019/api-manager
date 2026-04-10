@extends('layouts.app')

@section('title', __('auth.login'))

@section('styles')
    <style>
        * {
            box-sizing: border-box;
        }

        .login-page {
            display: flex;
            flex-direction: column;
            padding: 20px;
            background: #f9fafb;
        }

        html.dark .login-page {
            background: rgb(15, 23, 42);
        }

        .login-container {
            max-width: 900px;
            margin: 0 auto;
            width: 100%;
        }

        .login-header {
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

        html.dark .login-header {
            background: rgb(30, 41, 59);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .login-header::before {
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

        .login-header > * {
            position: relative;
            z-index: 1;
        }

        .login-header h1 {
            font-size: 2.5em;
            margin-bottom: 15px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .login-header p {
            font-size: 1.1em;
            opacity: 0.95;
            font-weight: 500;
        }

        .login-content {
            background: white;
            border-radius: 16px;
            padding: 50px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            border: 1px solid #f3f4f6;
            max-width: 500px;
            margin: 0 auto;
        }

        html.dark .login-content {
            background: rgb(30, 41, 59);
            border-color: rgb(55, 65, 81);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group:last-of-type {
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

        .alert-error {
            background-color: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }

        html.dark .alert-error {
            background-color: rgb(45, 7, 7);
            border-color: rgb(239, 68, 68);
            color: #fca5a5;
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
            width: 100%;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            margin-top: 20px;
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

        .divider {
            border-top: 2px solid #f3f4f6;
            margin: 30px 0;
            text-align: center;
        }

        html.dark .divider {
            border-top-color: rgb(55, 65, 81);
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
            width: auto;
            justify-content: flex-start;
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

        @media (max-width: 768px) {
            .login-container {
                padding: 10px;
            }

            .login-header {
                padding: 50px 30px;
                margin-bottom: 30px;
            }

            .login-header h1 {
                font-size: 2em;
            }

            .login-content {
                padding: 30px;
            }
        }
    </style>
@endsection

@section('content')
<div class="login-page">
    <div class="login-container">
        <div class="login-header">
            <h1>🔐 {{ __('auth.login') }}</h1>
            <p>{{ __('auth.profile_description') }}</p>
        </div>

        <div class="login-content">
            @if ($errors->any())
                <div class="alert alert-error">
                    <span>⚠️</span>
                    <span>{{ $errors->first('email') }}</span>
                </div>
            @endif

            <form action="{{ route('login.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="email">{{ __('auth.email') }}</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="@error('email') error @enderror"
                        placeholder="your@email.com"
                    />
                    @error('email')
                        <div style="color: #ef4444; font-size: 0.85em; margin-top: 8px; font-weight: 600;">❌ {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">{{ __('auth.password') }}</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="@error('password') error @enderror"
                        placeholder="••••••••"
                    />
                    @error('password')
                        <div style="color: #ef4444; font-size: 0.85em; margin-top: 8px; font-weight: 600;">❌ {{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    ✓ {{ __('auth.login') }}
                </button>
            </form>

            <div class="divider">
                <a href="{{ route('home') }}" class="btn-link">← {{ __('auth.back_home') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
