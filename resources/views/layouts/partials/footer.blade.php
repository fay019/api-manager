<footer class="mx-auto mt-20 max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="group/footer relative overflow-hidden rounded-2xl border border-gray-200 bg-white/80 p-8 shadow-sm backdrop-blur-sm transition-all duration-300 hover:border-gray-300 sm:p-12 dark:border-gray-800 dark:bg-gray-900/80 dark:hover:border-gray-700">
        <!-- Décoration d'arrière-plan avec blobs animés -->
        <div class="absolute -top-32 -right-32 h-80 w-80 rounded-full bg-indigo-500/5 blur-3xl transition-opacity duration-300 group-hover/footer:opacity-100 dark:bg-indigo-400/5"></div>
        <div class="absolute -bottom-32 -left-32 h-80 w-80 rounded-full bg-purple-500/5 blur-3xl transition-opacity duration-300 group-hover/footer:opacity-100 dark:bg-purple-400/5"></div>

        <div class="relative space-y-12">
            <!-- Header du footer -->
            <div class="border-b border-gray-200 pb-8 dark:border-gray-800">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <div class="flex items-center gap-3">
                            <svg class="h-8 w-8 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                                API<span class="text-indigo-600 dark:text-indigo-400">Manager</span>
                            </h3>
                        </div>
                        <p class="mt-3 max-w-sm text-sm leading-relaxed text-gray-600 dark:text-gray-400">
                            {{ __('app.footer.platform_description') }}
                        </p>
                    </div>
                    <div class="flex flex-col items-start gap-2 sm:items-end">
                        <span class="text-xs font-semibold text-gray-600 uppercase dark:text-gray-500">{{ __('app.nav.theme') }}</span>
                        <x-theme-toggle />
                    </div>
                </div>
            </div>

            <!-- Grille du contenu -->
            <div class="grid grid-cols-2 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Section Documentation -->
                <div>
                    <div class="mb-6 flex items-center gap-2">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <h4 class="text-xs font-bold text-gray-900 uppercase tracking-widest dark:text-white">{{ __('app.footer.docs') }}</h4>
                    </div>
                    <ul class="space-y-3">
                        <li><a href="{{ route('docs.index') }}" class="group inline-flex items-center gap-2 text-sm text-gray-700 transition-colors hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400"><span class="h-px w-0 bg-indigo-600 transition-all group-hover:w-3 dark:bg-indigo-400"></span>{{ __('app.footer.docs') }}</a></li>
                        <li><a href="{{ route('docs.database') }}" class="group inline-flex items-center gap-2 text-sm text-gray-700 transition-colors hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400"><span class="h-px w-0 bg-indigo-600 transition-all group-hover:w-3 dark:bg-indigo-400"></span>{{ __('app.footer.database') }}</a></li>
                        <li><a href="{{ route('docs.deployment') }}" class="group inline-flex items-center gap-2 text-sm text-gray-700 transition-colors hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400"><span class="h-px w-0 bg-indigo-600 transition-all group-hover:w-3 dark:bg-indigo-400"></span>{{ __('app.footer.deployment') }}</a></li>
                    </ul>
                </div>

                <!-- Section Client/Admin -->
                <div>
                    <div class="mb-6 flex items-center gap-2">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <h4 class="text-xs font-bold text-gray-900 uppercase tracking-widest dark:text-white">
                            @if(auth('client')->check())
                                {{ __('app.nav.client') ?? 'Client' }}
                            @elseif(auth()->check() && auth()->user()->is_admin)
                                {{ __('app.nav.admin') }}
                            @else
                                {{ __('app.nav.user') }}
                            @endif
                        </h4>
                    </div>
                    <ul class="space-y-3">
                        @if(auth('client')->check())
                            <li><a href="{{ route('client.profile.edit') }}" class="group inline-flex items-center gap-2 text-sm text-gray-700 transition-colors hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400"><span class="h-px w-0 bg-indigo-600 transition-all group-hover:w-3 dark:bg-indigo-400"></span>{{ __('app.footer.my_profile') }}</a></li>
                            <li>
                                <form action="{{ route('client.logout') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="group inline-flex items-center gap-2 text-sm text-gray-700 transition-colors hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400">
                                        <span class="h-px w-0 bg-red-600 transition-all group-hover:w-3 dark:bg-red-400"></span>
                                        {{ __('app.footer.logout') }}
                                    </button>
                                </form>
                            </li>
                        @elseif(auth()->check())
                            <li><a href="{{ route('profile.edit') }}" class="group inline-flex items-center gap-2 text-sm text-gray-700 transition-colors hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400"><span class="h-px w-0 bg-indigo-600 transition-all group-hover:w-3 dark:bg-indigo-400"></span>{{ __('app.footer.my_profile') }}</a></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="group inline-flex items-center gap-2 text-sm text-gray-700 transition-colors hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400">
                                        <span class="h-px w-0 bg-red-600 transition-all group-hover:w-3 dark:bg-red-400"></span>
                                        {{ __('app.footer.logout') }}
                                    </button>
                                </form>
                            </li>
                            @if(auth()->user()->is_admin)
                                <li><a href="/admin" class="group inline-flex items-center gap-2 text-sm text-gray-700 transition-colors hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400"><span class="h-px w-0 bg-indigo-600 transition-all group-hover:w-3 dark:bg-indigo-400"></span>{{ __('app.footer.admin_panel') }}</a></li>
                            @endif
                        @else
                            <li><a href="{{ route('client.login') }}" class="group inline-flex items-center gap-2 text-sm text-gray-700 transition-colors hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400"><span class="h-px w-0 bg-indigo-600 transition-all group-hover:w-3 dark:bg-indigo-400"></span>{{ __('app.footer.client_login') ?? 'Client Login' }}</a></li>
                            <li><a href="{{ route('login.show') }}" class="group inline-flex items-center gap-2 text-sm text-gray-700 transition-colors hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400"><span class="h-px w-0 bg-indigo-600 transition-all group-hover:w-3 dark:bg-indigo-400"></span>{{ __('app.footer.login') }}</a></li>
                        @endif
                    </ul>
                </div>

                <!-- Section Projets -->
                <div>
                    <div class="mb-6 flex items-center gap-2">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <h4 class="text-xs font-bold text-gray-900 uppercase tracking-widest dark:text-white">{{ __('app.footer.projects') }}</h4>
                    </div>
                    <ul class="space-y-3">
                        <li><a href="https://moussouni.dev" target="_blank" rel="noopener noreferrer" class="group inline-flex items-center gap-2 text-sm text-gray-700 transition-colors hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400"><span class="h-px w-0 bg-indigo-600 transition-all group-hover:w-3 dark:bg-indigo-400"></span>Moussouni.dev</a></li>
                        <li><a href="https://kdrama.moussouni.dev" target="_blank" rel="noopener noreferrer" class="group inline-flex items-center gap-2 text-sm text-gray-700 transition-colors hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400"><span class="h-px w-0 bg-indigo-600 transition-all group-hover:w-3 dark:bg-indigo-400"></span>K-Drama</a></li>
                    </ul>
                </div>

                <!-- Section Contact -->
                <div>
                    <div class="mb-6 flex items-center gap-2">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <h4 class="text-xs font-bold text-gray-900 uppercase tracking-widest dark:text-white">{{ __('app.footer.contact') }}</h4>
                    </div>
                    <ul class="space-y-3">
                        <li><a href="{{ route('contact.show') }}" class="group inline-flex items-center gap-2 text-sm text-gray-700 transition-colors hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400"><span class="h-px w-0 bg-indigo-600 transition-all group-hover:w-3 dark:bg-indigo-400"></span>{{ __('app.footer.contact_me') }}</a></li>
                        <li class="mt-4 flex flex-wrap gap-2">
                            <!-- Badge Environnement -->
                            <div class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 dark:border-gray-800 dark:bg-gray-800/50">
                                <div class="h-2 w-2 animate-pulse rounded-full {{ config('app.env') === 'production' ? 'bg-green-500' : 'bg-amber-500' }}"></div>
                                <span class="text-[10px] font-semibold text-gray-700 uppercase dark:text-gray-400">{{ trim(__('app.footer.environment'), ':') }}</span>
                                <span class="text-[10px] font-black text-gray-900 uppercase dark:text-white">{{ config('app.env') }}</span>
                            </div>

                            <!-- Badge Debug -->
                            <div class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 dark:border-gray-800 dark:bg-gray-800/50">
                                @if(config('app.debug'))
                                    <div class="h-2.5 w-2.5 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></div>
                                    <span class="text-[10px] font-black text-green-600 uppercase dark:text-green-400">{{ __('app.footer.debug_on') }}</span>
                                @else
                                    <div class="h-2.5 w-2.5 rounded-full bg-red-600 shadow-[0_0_8px_rgba(220,38,38,0.6)]"></div>
                                    <span class="text-[10px] font-black text-red-600 uppercase dark:text-red-500">{{ __('app.footer.debug_off') }}</span>
                                @endif
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="border-t border-gray-200 pt-8 dark:border-gray-800">
                <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                    <p class="text-xs text-gray-600 dark:text-gray-500">
                        {{ __('app.footer.copyright', ['year' => date('Y')]) }}
                    </p>
                    <span class="text-[10px] font-semibold tracking-widest text-gray-500 uppercase dark:text-gray-600">{{ __('app.footer.built_with') }}</span>
                </div>
            </div>
        </div>
    </div>
</footer>
