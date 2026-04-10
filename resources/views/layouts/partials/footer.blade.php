<!-- Footer partagé -->
<footer class="mx-auto mt-20 max-w-7xl px-4 pb-16">
    <div class="group/footer relative overflow-hidden rounded-[2.5rem] border border-black/5 bg-white/70 p-10 shadow-2xl backdrop-blur-xl transition-all duration-500 hover:border-black/10 md:p-16 dark:border-white/5 dark:bg-zinc-900/40 dark:shadow-none dark:hover:border-white/10">
        <!-- Décoration d'arrière-plan subtile -->
        <div class="absolute -top-24 -right-24 h-64 w-64 rounded-full bg-blue-500/5 blur-3xl transition-opacity group-hover/footer:opacity-100 dark:bg-blue-400/10"></div>
        <div class="absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-purple-500/5 blur-3xl transition-opacity group-hover/footer:opacity-100 dark:bg-purple-400/10"></div>

        <div class="relative">
            <!-- Header du footer -->
            <div class="mb-16 flex flex-col items-center justify-between gap-8 border-b border-black/5 pb-12 md:flex-row md:items-start dark:border-white/5">
                <div class="text-center md:text-left">
                    <div class="flex items-center justify-center gap-3 md:justify-start">
                        <span class="text-3xl">🚀</span>
                        <h3 class="text-3xl font-black tracking-tighter text-white transition-colors dark:text-white">
                            API<span class="text-blue-400 dark:text-blue-400">Manager</span>
                        </h3>
                    </div>
                    <p class="mt-3 max-w-md text-base leading-relaxed text-zinc-200 transition-colors dark:text-zinc-400">
                        {{ __('app.footer.production') ?? 'Plateforme de gestion et d’analyse d’API haute performance.' }}
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden flex-col items-end md:flex">
                        <span class="text-xs font-bold tracking-widest text-white/70 uppercase dark:text-zinc-500">{{ __('app.nav.theme') ?? 'Theme' }}</span>
                        <span class="text-[10px] text-zinc-300/80 dark:text-zinc-400/60">{{ __('app.theme.toggle_label') ?? 'Mode Sombre/Clair' }}</span>
                    </div>
                    <x-theme-toggle />
                </div>
            </div>

            <!-- Grille du contenu -->
            <div class="grid grid-cols-1 gap-12 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Section Documentation -->
                <div class="flex flex-col items-center md:items-start">
                    <div class="mb-6 flex items-center gap-2 text-white dark:text-white">
                        <svg class="h-5 w-5 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <h4 class="text-sm font-bold tracking-widest uppercase">{{ __('app.footer.docs') }}</h4>
                    </div>
                    <ul class="space-y-4 text-sm font-medium">
                        <li><a href="{{ route('docs.index') }}" class="group flex items-center text-zinc-200 transition-all hover:text-white dark:text-zinc-400 dark:hover:text-blue-400"><span class="mr-0 h-px w-0 bg-white transition-all group-hover:mr-2 group-hover:w-3 dark:bg-blue-400"></span>{{ __('app.footer.docs') }}</a></li>
                        <li><a href="{{ route('docs.database') }}" class="group flex items-center text-zinc-200 transition-all hover:text-white dark:text-zinc-400 dark:hover:text-blue-400"><span class="mr-0 h-px w-0 bg-white transition-all group-hover:mr-2 group-hover:w-3 dark:bg-blue-400"></span>{{ __('app.footer.database') }}</a></li>
                        <li><a href="{{ route('docs.deployment') }}" class="group flex items-center text-zinc-200 transition-all hover:text-white dark:text-zinc-400 dark:hover:text-blue-400"><span class="mr-0 h-px w-0 bg-white transition-all group-hover:mr-2 group-hover:w-3 dark:bg-blue-400"></span>{{ __('app.footer.deployment') }}</a></li>
                    </ul>
                </div>

                <!-- Section Admin -->
                <div class="flex flex-col items-center md:items-start">
                    <div class="mb-6 flex items-center gap-2 text-white dark:text-white">
                        <svg class="h-5 w-5 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <h4 class="text-sm font-bold tracking-widest uppercase">{{ __('app.nav.admin') }}</h4>
                    </div>
                    <ul class="space-y-4 text-sm font-medium">
                        @if(auth()->check())
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="group flex cursor-pointer items-center text-zinc-200 transition-all hover:text-red-400 dark:text-zinc-400 dark:hover:text-red-400">
                                        <span class="mr-0 h-px w-0 bg-red-400 transition-all group-hover:mr-2 group-hover:w-3"></span>
                                        {{ __('app.footer.logout') ?? 'Logout' }}
                                    </button>
                                </form>
                            </li>
                            <li><a href="/admin" class="group flex items-center text-zinc-200 transition-all hover:text-white dark:text-zinc-400 dark:hover:text-blue-400"><span class="mr-0 h-px w-0 bg-white transition-all group-hover:mr-2 group-hover:w-3 dark:bg-blue-400"></span>{{ __('app.footer.admin_panel') ?? 'Admin Panel' }}</a></li>
                        @else
                            <li><a href="/admin/login" class="group flex items-center text-zinc-200 transition-all hover:text-white dark:text-zinc-400 dark:hover:text-blue-400"><span class="mr-0 h-px w-0 bg-white transition-all group-hover:mr-2 group-hover:w-3 dark:bg-blue-400"></span>{{ __('app.footer.admin') }}</a></li>
                        @endif
                    </ul>
                </div>

                <!-- Section Projets -->
                <div class="flex flex-col items-center md:items-start">
                    <div class="mb-6 flex items-center gap-2 text-white dark:text-white">
                        <svg class="h-5 w-5 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <h4 class="text-sm font-bold tracking-widest uppercase">{{ __('app.footer.projects') ?? 'Projects' }}</h4>
                    </div>
                    <ul class="space-y-4 text-sm font-medium">
                        <li><a href="https://moussouni.dev" target="_blank" rel="noopener noreferrer" class="group flex items-center text-zinc-200 transition-all hover:text-white dark:text-zinc-400 dark:hover:text-blue-400"><span class="mr-0 h-px w-0 bg-white transition-all group-hover:mr-2 group-hover:w-3 dark:bg-blue-400"></span>🌐 Moussouni.dev</a></li>
                        <li><a href="https://kdrama.moussouni.dev" target="_blank" rel="noopener noreferrer" class="group flex items-center text-zinc-200 transition-all hover:text-white dark:text-zinc-400 dark:hover:text-blue-400"><span class="mr-0 h-px w-0 bg-white transition-all group-hover:mr-2 group-hover:w-3 dark:bg-blue-400"></span>🎬 K-Drama</a></li>
                    </ul>
                </div>

                <!-- Section Contact -->
                <div class="flex flex-col items-center md:items-start">
                    <div class="mb-6 flex items-center gap-2 text-white dark:text-white">
                        <svg class="h-5 w-5 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <h4 class="text-sm font-bold tracking-widest uppercase">{{ __('app.footer.contact') ?? 'Contact' }}</h4>
                    </div>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('contact.show') }}" class="group flex items-center text-zinc-200 transition-all hover:text-white dark:text-zinc-400 dark:hover:text-blue-400">
                                <span class="mr-0 h-px w-0 bg-white transition-all group-hover:mr-2 group-hover:w-3 dark:bg-blue-400"></span>
                                {{ __('app.footer.contact_me') ?? 'Contact Me' }}
                            </a>
                        </li>
                        <li class="mt-8 flex flex-wrap gap-3">
                            <!-- Badge Environnement -->
                            <div class="flex items-center gap-2 rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 backdrop-blur-sm dark:bg-black/20">
                                <div class="h-2 w-2 animate-pulse rounded-full {{ config('app.env') === 'production' ? 'bg-green-500' : 'bg-amber-500' }}"></div>
                                <span class="text-[10px] font-bold text-white/70 uppercase">{{ trim(__('app.footer.environment'), ':') }}</span>
                                <span class="text-[10px] font-black text-white uppercase">{{ config('app.env') }}</span>
                            </div>

                            <!-- Badge Debug (La Lampe) -->
                            <div class="flex items-center gap-2 rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 backdrop-blur-sm dark:bg-black/20">
                                @if(config('app.debug'))
                                    <div class="h-2.5 w-2.5 rounded-full bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.8)]"></div>
                                    <span class="text-[10px] font-black text-green-400 uppercase">Debug: ON</span>
                                @else
                                    <div class="h-2.5 w-2.5 rounded-full bg-red-600 shadow-[0_0_10px_rgba(220,38,38,0.8)]"></div>
                                    <span class="text-[10px] font-black text-red-500 uppercase">Debug: OFF</span>
                                @endif
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="mt-20 flex flex-col items-center justify-between gap-6 border-t border-white/10 pt-10 md:flex-row dark:border-white/5">
                <p class="text-xs font-medium text-white/60 transition-colors dark:text-zinc-500">
                    {{ __('app.footer.copyright', ['year' => date('Y')]) }}
                </p>
                <div class="flex items-center gap-6">
                    <span class="text-[10px] font-bold tracking-[0.2em] text-white/40 uppercase dark:text-zinc-600">Built with Laravel 12 & Tailwind 4</span>
                </div>
            </div>
        </div>
    </div>
</footer>
