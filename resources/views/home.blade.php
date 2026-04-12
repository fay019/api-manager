@extends('layouts.app')

@section('title', __('app.home.title'))

@section('content')
<div class="flex flex-col min-h-screen">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-indigo-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-950 dark:to-gray-900 pt-16 pb-20">
        <!-- Decorative background elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-indigo-200/20 rounded-full blur-3xl dark:bg-indigo-900/10"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-blue-200/20 rounded-full blur-3xl dark:bg-blue-900/10"></div>
        </div>

        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold tracking-tight text-gray-900 dark:text-white mb-6">
                {{ __('app.home.header_title') }}
            </h1>
            <p class="text-lg sm:text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-2xl mx-auto leading-relaxed">
                {{ __('app.home.header_subtitle') }}
            </p>
            <div class="inline-block px-4 py-2 bg-indigo-100 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 rounded-full text-sm font-semibold">
                {{ __('app.home.header_version') }}
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Quick Navigation Section -->
        <section class="mb-20">
            <div class="mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ __('app.home.quick_nav_title') }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ __('app.home.quick_nav_subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @if(auth()->check() && auth()->user()->is_admin)
                    <a href="/admin" class="group relative bg-white dark:bg-gray-800 rounded-xl p-8 border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:shadow-lg hover:border-indigo-400 dark:hover:border-indigo-500">
                        <div class="absolute inset-0 rounded-xl bg-gradient-to-br from-indigo-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity dark:from-indigo-950 dark:to-transparent"></div>
                        <div class="relative">
                            <div class="text-4xl mb-4">📊</div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ __('app.home.admin_panel') }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('app.home.admin_panel_desc') }}</p>
                        </div>
                    </a>
                @endif

                <a href="{{ route('docs.index') }}" class="group relative bg-white dark:bg-gray-800 rounded-xl p-8 border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:shadow-lg hover:border-indigo-400 dark:hover:border-indigo-500">
                    <div class="absolute inset-0 rounded-xl bg-gradient-to-br from-indigo-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity dark:from-indigo-950 dark:to-transparent"></div>
                    <div class="relative">
                        <div class="text-4xl mb-4">📚</div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ __('app.home.all_docs') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('app.home.all_docs_desc') }}</p>
                    </div>
                </a>

                <a href="/api/v1/promo/banner.json" class="group relative bg-white dark:bg-gray-800 rounded-xl p-8 border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:shadow-lg hover:border-indigo-400 dark:hover:border-indigo-500">
                    <div class="absolute inset-0 rounded-xl bg-gradient-to-br from-indigo-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity dark:from-indigo-950 dark:to-transparent"></div>
                    <div class="relative">
                        <div class="text-4xl mb-4">📡</div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ __('app.home.api_test') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('app.home.api_test_desc') }}</p>
                    </div>
                </a>
            </div>
        </section>

        <!-- Getting Started Section -->
        <section class="mb-20">
            <div class="mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ __('app.home.getting_started') }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ __('app.home.getting_started_subtitle') }}
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-8 sm:p-10">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">{{ __('app.home.test_the_api') }}</h3>
                <ol class="space-y-4 mb-8">
                    <li class="flex gap-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 rounded-full flex items-center justify-center font-semibold">1</span>
                        <span class="text-gray-700 dark:text-gray-300 pt-1">{{ __('app.home.check_health') }}</span>
                    </li>
                </ol>

                <div class="bg-gray-900 dark:bg-black rounded-lg p-4 mb-6 overflow-x-auto">
                    <code class="text-green-400 font-mono text-sm">curl http://api-manager.test/api/v1/health</code>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('app.home.api_docs_link') }}
                </p>
            </div>
        </section>

        <!-- API Endpoints Section -->
        <section class="mb-20">
            <div class="mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ __('app.home.available_endpoints') }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ __('app.home.endpoints_subtitle') }}
                </p>
            </div>

            <div class="space-y-4">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-4">
                        <span class="inline-block px-3 py-1 bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 rounded font-semibold text-xs">GET</span>
                        <div>
                            <code class="text-indigo-600 dark:text-indigo-400 font-mono font-semibold">/api/v1/health</code>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('app.home.health_endpoint') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-4">
                        <span class="inline-block px-3 py-1 bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 rounded font-semibold text-xs">GET</span>
                        <div>
                            <code class="text-indigo-600 dark:text-indigo-400 font-mono font-semibold">/api/v1/promo/banner.json</code>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('app.home.promo_banner') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-4">
                        <span class="inline-block px-3 py-1 bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 rounded font-semibold text-xs">POST</span>
                        <div>
                            <code class="text-indigo-600 dark:text-indigo-400 font-mono font-semibold">/api/v1/promo/event</code>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('app.home.promo_event') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Core Features Section -->
        <section>
            <div class="mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ __('app.home.core_features') }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ __('app.home.features_subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:shadow-md transition-shadow">
                    <div class="text-2xl mb-4">✓</div>
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('app.home.feature_modular') }}</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('app.home.feature_modular_desc') }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:shadow-md transition-shadow">
                    <div class="text-2xl mb-4">✓</div>
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('app.home.feature_keys') }}</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('app.home.feature_keys_desc') }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:shadow-md transition-shadow">
                    <div class="text-2xl mb-4">✓</div>
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('app.home.feature_cors') }}</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('app.home.feature_cors_desc') }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:shadow-md transition-shadow">
                    <div class="text-2xl mb-4">✓</div>
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('app.home.feature_rate') }}</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('app.home.feature_rate_desc') }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:shadow-md transition-shadow">
                    <div class="text-2xl mb-4">✓</div>
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('app.home.feature_logs') }}</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('app.home.feature_logs_desc') }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:shadow-md transition-shadow">
                    <div class="text-2xl mb-4">✓</div>
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('app.home.feature_events') }}</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('app.home.feature_events_desc') }}</p>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
