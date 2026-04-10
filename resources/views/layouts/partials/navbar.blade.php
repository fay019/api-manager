@php
    $isHome = Route::current()->getName() === 'home';
    $isDocs = Route::current()->getName() === 'docs.index' || Route::current()->getName() === 'docs.show';
    $isAdmin = str_starts_with(Route::current()->getName(), 'admin');
    $isContact = Route::current()->getName() === 'contact.show';
@endphp

<header
    class="fixed top-0 left-0 z-50 h-14 w-full border-b border-black/10 bg-white shadow-xs transition-colors duration-300 dark:border-white/10 dark:bg-zinc-900"
    id="navbar-header"
>
    <nav class="h-full px-4" id="navbar-nav">
        <div class="flex h-full items-center justify-between gap-4">
            <!-- Logo -->
            <div class="flex items-center min-w-fit">
                @if($isHome)
                    <span class="text-xl font-bold text-blue-600 transition-colors hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                        🚀 {{ __('app.nav.api_manager') ?? 'API Manager' }}
                    </span>
                @else
                    <a href="{{ route('home') }}" class="text-xl font-bold text-blue-600 transition-colors hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                        🚀 {{ __('app.nav.api_manager') ?? 'API Manager' }}
                    </a>
                @endif
            </div>

            <!-- Menu Toggle Button (Mobile) -->
            <button
                class="flex items-center justify-center rounded-md p-2 transition-all hover:bg-blue-600/10 md:hidden ml-auto"
                id="nav-toggle"
                aria-label="Open Menu"
            >
                <svg class="h-7 w-7 stroke-blue-600 dark:stroke-blue-400" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>

            <!-- Close Button (Mobile) -->
            <button
                class="hidden absolute top-6 right-4 items-center justify-center p-2 transition-all hover:bg-blue-600/10 z-[101]"
                id="nav-close"
                aria-label="Close Menu"
            >
                <svg class="h-6 w-6 stroke-blue-600 dark:stroke-blue-400" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            <!-- Menu -->
            <div
                class="fixed top-14 left-0 hidden h-[calc(100vh-3.5rem)] w-full flex-col overflow-y-auto bg-white/95 backdrop-blur-md transition-all duration-300 dark:bg-zinc-900/95 md:static md:top-0 md:flex md:h-auto md:w-auto md:flex-row md:bg-transparent md:dark:bg-transparent"
                id="nav-menu"
            >
                <ul class="flex w-full flex-col gap-2 p-8 md:w-auto md:flex-row md:items-center md:gap-4 md:p-0 lg:gap-8">
                    <li>
                        <a
                            href="{{ route('docs.index') }}"
                            class="block rounded-lg px-4 py-3 text-sm font-semibold transition-all md:rounded-none md:border-b-2 md:px-3 md:py-2 {{ $isDocs ? 'bg-blue-600 text-white md:border-blue-600 md:bg-transparent md:text-blue-600 dark:md:border-blue-400 dark:md:text-blue-400' : 'text-zinc-600 hover:bg-blue-600/10 dark:text-zinc-300 md:border-transparent md:hover:bg-transparent md:hover:text-blue-600 dark:md:hover:text-blue-400' }}"
                        >
                            {{ __('app.nav.all_docs') ?? 'Docs' }}
                        </a>
                    </li>
                    @if(auth()->check())
                        <li>
                            <a
                                href="/admin"
                                class="block rounded-lg px-4 py-3 text-sm font-semibold transition-all md:rounded-none md:border-b-2 md:px-3 md:py-2 {{ $isAdmin ? 'bg-blue-600 text-white md:border-blue-600 md:bg-transparent md:text-blue-600 dark:md:border-blue-400 dark:md:text-blue-400' : 'text-zinc-600 hover:bg-blue-600/10 dark:text-zinc-300 md:border-transparent md:hover:bg-transparent md:hover:text-blue-600 dark:md:hover:text-blue-400' }}"
                            >
                                {{ __('app.nav.admin') ?? 'Admin' }}
                            </a>
                        </li>
                    @endif
                    <li>
                        <a
                            href="{{ route('contact.show') }}"
                            class="block rounded-lg px-4 py-3 text-sm font-semibold transition-all md:rounded-none md:border-b-2 md:px-3 md:py-2 {{ $isContact ? 'bg-blue-600 text-white md:border-blue-600 md:bg-transparent md:text-blue-600 dark:md:border-blue-400 dark:md:text-blue-400' : 'text-zinc-600 hover:bg-blue-600/10 dark:text-zinc-300 md:border-transparent md:hover:bg-transparent md:hover:text-blue-600 dark:md:hover:text-blue-400' }}"
                        >
                            {{ __('app.nav.contact') ?? 'Contact' }}
                        </a>
                    </li>

                    <!-- Mobile-only controls -->
                    <li class="mt-auto flex flex-col gap-4 border-t border-black/10 pt-8 dark:border-white/10 md:hidden">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-zinc-500">{{ __('app.nav.language') ?? 'Language' }}</span>
                            <x-locale-switcher-flags />
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-zinc-500">{{ __('app.nav.theme') }}</span>
                            <x-theme-toggle />
                        </div>

                        <!-- Auth buttons (mobile) -->
                        <div class="border-t border-black/10 pt-4 dark:border-white/10">
                            @if(auth()->check())
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('profile.edit') }}" class="block rounded-lg px-4 py-3 text-sm font-semibold text-zinc-600 hover:bg-blue-600/10 dark:text-zinc-300 dark:hover:text-blue-400 flex-1">
                                        {{ __('auth.my_profile') }}
                                    </a>
                                    <form action="{{ route('logout') }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full block rounded-lg px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-600/10 dark:text-red-400">
                                            {{ __('auth.logout') }}
                                        </button>
                                    </form>
                                </div>
                            @else
                                <a href="{{ route('login.show') }}" class="block rounded-lg px-4 py-3 text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-center">
                                    {{ __('auth.login') }}
                                </a>
                            @endif
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Desktop-only controls -->
            <div class="hidden items-center gap-4 md:flex lg:gap-6">
                <x-locale-switcher-flags />
                <x-theme-toggle />

                <!-- Auth buttons -->
                @if(auth()->check())
                    <a href="{{ route('profile.edit') }}" class="text-zinc-600 hover:text-blue-600 dark:text-zinc-300 dark:hover:text-blue-400 transition-colors" title="{{ __('auth.my_profile') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-zinc-600 hover:text-blue-600 dark:text-zinc-300 dark:hover:text-blue-400 transition-colors" title="{{ __('auth.logout') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login.show') }}" class="text-zinc-600 hover:text-blue-600 dark:text-zinc-300 dark:hover:text-blue-400 transition-colors" title="{{ __('auth.login') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </nav>
</header>

<style>
    /* Utility class for mobile menu show state */
    #nav-menu.show {
        display: flex !important;
        z-index: 100;
    }

    #nav-menu.show ~ #nav-toggle {
        display: none !important;
    }

    #nav-menu.show ~ #nav-close {
        display: flex !important;
    }

    /* Main content spacing adjustment - added here or globally */
    .main-content {
        margin-top: 3.5rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const navToggle = document.getElementById('nav-toggle');
        const navClose = document.getElementById('nav-close');
        const navMenu = document.getElementById('nav-menu');
        const navLinks = document.querySelectorAll('#nav-menu a');

        // Toggle menu on hamburger click
        if (navToggle) {
            navToggle.addEventListener('click', () => {
                navMenu.classList.add('show');
            });
        }

        // Close menu on X click
        if (navClose) {
            navClose.addEventListener('click', () => {
                navMenu.classList.remove('show');
            });
        }

        // Close menu on link click
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('show');
            });
        });
    });
</script>
