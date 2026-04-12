@extends('layouts.app')

@section('title', __('app.docs.page_title'))

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="mb-12">
        <h1 class="text-5xl sm:text-6xl font-bold text-gray-900 dark:text-white mb-3">
            📚 {{ __('app.docs.title') }}
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400">
            {{ __('app.docs.subtitle') }}
        </p>
    </div>

    @if(empty($visibleDocs))
        <!-- Empty State -->
        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 dark:from-indigo-900 dark:to-purple-900 rounded-2xl p-12 sm:p-16 text-center text-white mb-12">
            <div class="text-6xl mb-6 animate-bounce">📚</div>
            <h2 class="text-3xl font-bold mb-4">{{ __('app.docs.coming_soon') }}</h2>
            <p class="text-lg opacity-95 mb-6 leading-relaxed">
                {{ __('app.docs.preparing') }} <br>
                <strong>{{ __('app.docs.good_things') }}</strong>
            </p>
            <p class="text-base opacity-90 mb-8">
                {{ __('app.docs.check_health') }}
                @if(auth()->check() && auth()->user()->is_admin)
                    {{ __('app.docs.explore_admin') }}
                @endif
            </p>
            <div class="flex gap-4 justify-center flex-wrap">
                <a href="/api/v1/health" class="px-6 py-3 bg-white/20 hover:bg-white/30 border-2 border-white/30 rounded-lg font-semibold transition-all">
                    {{ __('app.docs.api_health') }}
                </a>
                @if(auth()->check() && auth()->user()->is_admin)
                    <a href="/admin" class="px-6 py-3 bg-white text-indigo-600 hover:bg-gray-100 rounded-lg font-semibold transition-all">
                        {{ __('app.docs.admin_panel') }}
                    </a>
                @endif
            </div>
            @if(auth()->check() && auth()->user()->is_admin)
                <p class="text-sm opacity-70 mt-8 pt-6 border-t border-white/20">
                    <strong>{{ __('app.docs.admin_note') }}</strong> <a href="/admin" class="underline hover:opacity-100">{{ __('app.docs.admin_note') }}</a>
                </p>
            @endif
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
            @foreach($visibleDocs as $docName)
                @php
                    $metadata = \App\Services\DocumentationScanner::getMetadata($docName);
                @endphp
                <a href="{{ route('docs.show', $docName) }}" class="group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-8 transition-all duration-300 hover:shadow-lg hover:border-indigo-400 dark:hover:border-indigo-500 hover:-translate-y-1">
                    <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">{{ $metadata['icon'] }}</div>
                    <h3 class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mb-3">{{ $metadata['label'] }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">{{ $metadata['description'] }}</p>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-500 pt-4 border-t border-gray-200 dark:border-gray-700">
                        {{ ucfirst($docName) }}
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <!-- Key Resources -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-8 shadow-sm">
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ __('app.docs.key_resources') }}</h3>
        <ul class="space-y-3">
            @if(auth()->check() && auth()->user()->is_admin)
                <li class="flex items-start gap-3 pb-3 border-b border-gray-200 dark:border-gray-700">
                    <span class="text-indigo-600 dark:text-indigo-400 font-bold mt-1">→</span>
                    <div>
                        <strong class="text-gray-900 dark:text-white">{{ __('app.docs.resource_admin') }}</strong>
                        <a href="/admin" class="text-indigo-600 dark:text-indigo-400 hover:underline ml-2">/admin</a>
                    </div>
                </li>
            @endif
            <li class="flex items-start gap-3 pb-3 border-b border-gray-200 dark:border-gray-700">
                <span class="text-indigo-600 dark:text-indigo-400 font-bold mt-1">→</span>
                <div>
                    <strong class="text-gray-900 dark:text-white">{{ __('app.docs.resource_health') }}</strong>
                    <a href="/api/v1/health" class="text-indigo-600 dark:text-indigo-400 hover:underline ml-2">/api/v1/health</a>
                </div>
            </li>
            <li class="flex items-start gap-3 pb-3 border-b border-gray-200 dark:border-gray-700">
                <span class="text-indigo-600 dark:text-indigo-400 font-bold mt-1">→</span>
                <div>
                    <strong class="text-gray-900 dark:text-white">{{ __('app.docs.resource_banner') }}</strong>
                    <a href="/api/v1/promo/banner.json" class="text-indigo-600 dark:text-indigo-400 hover:underline ml-2">/api/v1/promo/banner.json</a>
                </div>
            </li>
            <li class="flex items-start gap-3">
                <span class="text-indigo-600 dark:text-indigo-400 font-bold mt-1">→</span>
                <div>
                    <strong class="text-gray-900 dark:text-white">{{ __('app.docs.resource_code') }}</strong>
                    <span class="text-gray-600 dark:text-gray-400 ml-2">{{ __('app.docs.resource_code_desc') }}</span>
                </div>
            </li>
        </ul>
    </div>
</div>
@endsection
