@php
    $currentRouteName = Route::current()?->getName();
    $isHome = $currentRouteName === 'home';
    $isDocs = $currentRouteName === 'docs.index' || $currentRouteName === 'docs.show';
    $isAdmin = $currentRouteName && str_starts_with($currentRouteName, 'admin');
    $isContact = $currentRouteName === 'contact.show';
@endphp

<header class="fixed top-0 left-0 z-50 w-full border-b border-gray-200 bg-white/80 dark:border-gray-800 dark:bg-gray-900/80 backdrop-blur-sm transition-all duration-300">
    <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">
            <!-- Logo -->
            <div class="flex items-center gap-2 min-w-fit">
                @if($isHome)
                    <span class="inline-flex items-center gap-2 text-xl font-bold text-gray-900 dark:text-white">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        {{ __('app.nav.api_manager') ?? 'API Manager' }}
                    </span>
                @else
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xl font-bold text-gray-900 hover:text-indigo-600 dark:text-white dark:hover:text-indigo-400 transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        {{ __('app.nav.api_manager') ?? 'API Manager' }}
                    </a>
                @endif
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center gap-1">
                <a
                    href="{{ route('docs.index') }}"
                    class="px-3 py-2 text-sm font-medium rounded-lg transition-all {{ $isDocs ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300' : 'text-gray-700 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-indigo-400' }}"
                >
                    {{ __('app.nav.all_docs') ?? 'Docs' }}
                </a>

                @if(auth()->check() && auth()->user()->is_admin)
                    <a
                        href="/admin"
                        class="px-3 py-2 text-sm font-medium rounded-lg transition-all {{ $isAdmin ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300' : 'text-gray-700 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-indigo-400' }}"
                    >
                        {{ __('app.nav.admin') ?? 'Admin' }}
                    </a>
                @endif

                <a
                    href="{{ route('contact.show') }}"
                    class="px-3 py-2 text-sm font-medium rounded-lg transition-all {{ $isContact ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300' : 'text-gray-700 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-indigo-400' }}"
                >
                    {{ __('app.nav.contact') ?? 'Contact' }}
                </a>
            </div>

            <!-- Desktop Right Controls -->
            <div class="hidden md:flex items-center gap-2">
                <x-locale-switcher-flags />
                <x-theme-toggle />

                @if(auth()->check())
                    <a
                        href="{{ route('profile.edit') }}"
                        class="p-2 rounded-lg text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors"
                        title="{{ __('auth.my_profile') }}"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button
                            type="submit"
                            class="p-2 rounded-lg text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors"
                            title="{{ __('auth.logout') }}"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                @else
                    <a
                        href="{{ route('login.show') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors"
                    >
                        {{ __('auth.login') }}
                    </a>
                @endif
            </div>

            <!-- Mobile Menu Button -->
            <button
                x-data
                @click="$dispatch('toggle-mobile-menu')"
                class="md:hidden p-2 rounded-lg text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors"
                aria-label="Open Menu"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div
            x-data="{ open: false }"
            @toggle-mobile-menu.window="open = !open"
            x-show="open"
            @click.away="open = false"
            x-transition
            class="md:hidden border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900"
        >
            <div class="space-y-1 px-4 py-4">
                <a
                    href="{{ route('docs.index') }}"
                    @click="open = false"
                    class="block px-3 py-2 rounded-lg text-sm font-medium {{ $isDocs ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }} transition-colors"
                >
                    {{ __('app.nav.all_docs') ?? 'Docs' }}
                </a>

                @if(auth()->check() && auth()->user()->is_admin)
                    <a
                        href="/admin"
                        @click="open = false"
                        class="block px-3 py-2 rounded-lg text-sm font-medium {{ $isAdmin ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }} transition-colors"
                    >
                        {{ __('app.nav.admin') ?? 'Admin' }}
                    </a>
                @endif

                <a
                    href="{{ route('contact.show') }}"
                    @click="open = false"
                    class="block px-3 py-2 rounded-lg text-sm font-medium {{ $isContact ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }} transition-colors"
                >
                    {{ __('app.nav.contact') ?? 'Contact' }}
                </a>

                <div class="border-t border-gray-200 dark:border-gray-800 pt-4 mt-4 space-y-3">
                    <div class="flex items-center justify-between px-3">
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('app.nav.language') ?? 'Language' }}</span>
                        <x-locale-switcher-flags />
                    </div>
                    <div class="flex items-center justify-between px-3">
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('app.nav.theme') }}</span>
                        <x-theme-toggle />
                    </div>
                </div>

                @if(auth()->check())
                    <div class="border-t border-gray-200 dark:border-gray-800 pt-4 mt-4 space-y-2">
                        <a
                            href="{{ route('profile.edit') }}"
                            @click="open = false"
                            class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors"
                        >
                            {{ __('auth.my_profile') }}
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/30 transition-colors"
                            >
                                {{ __('auth.logout') }}
                            </button>
                        </form>
                    </div>
                @else
                    <div class="border-t border-gray-200 dark:border-gray-800 pt-4 mt-4">
                        <a
                            href="{{ route('login.show') }}"
                            @click="open = false"
                            class="block w-full px-3 py-2 rounded-lg text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors text-center"
                        >
                            {{ __('auth.login') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </nav>
</header>
