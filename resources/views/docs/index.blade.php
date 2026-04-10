@extends('layouts.app')

@section('title', __('app.docs.page_title'))

@section('styles')
    <style>
        body {
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        h1 {
            color: white;
            margin-bottom: 10px;
            font-size: 2em;
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 40px;
            font-size: 1.1em;
        }

        .docs-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        @media (max-width: 768px) {
            .docs-grid {
                grid-template-columns: 1fr;
            }
        }

        .doc-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            color: inherit;
            display: block;
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .doc-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transform: translateY(-5px);
            border-color: rgba(79, 70, 229, 0.3);
            background: white;
        }

        .doc-card h3 {
            color: #4f46e5;
            margin-bottom: 12px;
            font-size: 1.4em;
            font-weight: 700;
        }

        .doc-card p {
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .doc-card .icon {
            font-size: 2.5em;
            margin-bottom: 15px;
            filter: drop-shadow(0 4px 3px rgba(0, 0, 0, 0.07));
        }

        .doc-card .meta {
            color: #9ca3af;
            font-size: 0.8em;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .features {
            background: rgba(255, 255, 255, 0.8);
            padding: 30px;
            border-radius: 24px;
            margin-top: 40px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .features h3 {
            color: #1f2937;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .features ul {
            list-style: none;
        }

        .features li {
            padding: 10px 0;
            color: #4b5563;
            padding-left: 25px;
            position: relative;
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        }

        .features li:last-child {
            border-bottom: none;
        }

        .features li:before {
            content: "→";
            position: absolute;
            left: 0;
            color: #4f46e5;
            font-weight: bold;
        }

        .dark .doc-card {
            background: rgba(24, 24, 27, 0.8);
            color: #f3f4f6;
            border-color: rgba(255, 255, 255, 0.05);
            box-shadow: none;
        }

        .dark .doc-card:hover {
            background: rgba(39, 39, 42, 0.9);
            border-color: rgba(129, 140, 248, 0.3);
        }

        .dark .doc-card h3 {
            color: #818cf8;
        }

        .dark .doc-card p {
            color: #a1a1aa;
        }

        .dark .doc-card .meta {
            color: #71717a;
            border-top-color: rgba(255, 255, 255, 0.05);
        }

        .dark .features {
            background: rgba(24, 24, 27, 0.8);
            color: #f3f4f6;
            border-color: rgba(255, 255, 255, 0.05);
            box-shadow: none;
        }

        .dark .features h3 {
            color: #f4f4f5;
        }

        .dark .features li {
            color: #a1a1aa;
            border-bottom-color: rgba(255, 255, 255, 0.05);
        }

        .dark .features li:before {
            color: #818cf8;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <h1>📚 {{ __('app.docs.title') }}</h1>
        <p class="subtitle">{{ __('app.docs.subtitle') }}</p>

        @if(empty($visibleDocs))
            <!-- Empty State - Friendly Welcome -->
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; padding: 60px 30px; text-align: center; box-shadow: 0 8px 16px rgba(102, 126, 234, 0.2); margin: 40px 0; color: white;">
                <div style="font-size: 4rem; margin-bottom: 20px; animation: bounce 2s infinite;">📚</div>
                <h2 style="font-size: 1.8em; color: white; margin-bottom: 15px; font-weight: 600;">{{ __('app.docs.coming_soon') }}</h2>
                <p style="color: rgba(255,255,255,0.95); font-size: 1.05em; margin-bottom: 25px; line-height: 1.8;">
                    {{ __('app.docs.preparing') }} <br>
                    <strong>{{ __('app.docs.good_things') }}</strong>
                </p>
                <p style="color: rgba(255,255,255,0.9); font-size: 0.95em; margin-bottom: 30px;">
                    {{ __('app.docs.check_health') }}
                    @if(auth()->check() && auth()->user()->is_admin)
                        {{ __('app.docs.explore_admin') }}
                    @endif
                </p>
                <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                    <a href="/api/v1/health" style="background: rgba(255,255,255,0.2); color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; border: 2px solid rgba(255,255,255,0.3); transition: all 0.3s;">
                        {{ __('app.docs.api_health') }}
                    </a>
                    @if(auth()->check() && auth()->user()->is_admin)
                        <a href="/admin" style="background: white; color: #667eea; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.3s;">
                            {{ __('app.docs.admin_panel') }}
                        </a>
                    @endif
                </div>
                @if(auth()->check() && auth()->user()->is_admin)
                    <p style="color: rgba(255,255,255,0.7); font-size: 0.85em; margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.2);">
                        <strong>@lang('app.docs.admin_note')</strong> <a href="/admin" style="color: white; text-decoration: underline;">{{ __('app.docs.admin_note') }}</a>
                    </p>
                @endif
            </div>
        @else
            <div class="docs-grid">
                @foreach($visibleDocs as $docName)
                    @php
                        $metadata = \App\Services\DocumentationScanner::getMetadata($docName);
                    @endphp
                    <a href="{{ route('docs.show', $docName) }}" class="doc-card">
                        <div class="icon">{{ $metadata['icon'] }}</div>
                        <h3>{{ $metadata['label'] }}</h3>
                        <p>{{ $metadata['description'] }}</p>
                        <div class="meta">{{ ucfirst($docName) }}</div>
                    </a>
                @endforeach
            </div>
        @endif

        <div class="features">
            <h3>{{ __('app.docs.key_resources') }}</h3>
            <ul>
                @if(auth()->check() && auth()->user()->is_admin)
                    <li><strong>{{ __('app.docs.resource_admin') }}</strong> <a href="/admin" style="color: #667eea;">/admin</a></li>
                @endif
                <li><strong>{{ __('app.docs.resource_health') }}</strong> <a href="/api/v1/health" style="color: #667eea;">/api/v1/health</a></li>
                <li><strong>{{ __('app.docs.resource_banner') }}</strong> <a href="/api/v1/promo/banner.json" style="color: #667eea;">/api/v1/promo/banner.json</a></li>
                <li><strong>{{ __('app.docs.resource_code') }}</strong> {{ __('Check the project root directory') }}</li>
            </ul>
        </div>
    </div>
@endsection
