<div
    x-data="{
        theme: localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),
        init() {
            this.apply();
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                if (!localStorage.getItem('theme')) {
                    this.theme = e.matches ? 'dark' : 'light';
                    this.apply();
                }
            });
            // Synchronisation entre plusieurs instances du composant (ex: navbar et footer)
            window.addEventListener('theme-changed', (e) => {
                this.theme = e.detail.theme;
            });
        },
        toggle() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            this.apply();
            // Notifier les autres instances
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: this.theme } }));
        },
        apply() {
            document.documentElement.classList.add('transition-colors', 'duration-300');
            document.documentElement.classList.toggle('dark', this.theme === 'dark');
            localStorage.setItem('theme', this.theme);
        }
    }"
    class="relative inline-flex items-center justify-center"
>
    <button
        @click="toggle()"
        type="button"
        class="group relative inline-flex h-10 w-10 items-center justify-center rounded-lg border border-black/10 bg-black/5 p-2 transition-all hover:bg-black/10 dark:border-white/15 dark:bg-white/10 dark:hover:bg-white/20"
        aria-label="{{ __('app.theme.toggle_label') }}"
    >
        <!-- Icône Soleil -->
        <svg
            x-cloak
            x-show="theme === 'light'"
            x-transition:enter="transition duration-300"
            x-transition:enter-start="scale-0 rotate-90"
            x-transition:enter-end="scale-100 rotate-0"
            class="h-5 w-5 stroke-zinc-800"
            viewBox="0 0 24 24"
            fill="none"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <circle cx="12" cy="12" r="5"></circle>
            <line x1="12" y1="1" x2="12" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="23"></line>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
            <line x1="1" y1="12" x2="3" y2="12"></line>
            <line x1="21" y1="12" x2="23" y2="12"></line>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
        </svg>

        <!-- Icône Lune -->
        <svg
            x-cloak
            x-show="theme === 'dark'"
            x-transition:enter="transition duration-300"
            x-transition:enter-start="scale-0 -rotate-90"
            x-transition:enter-end="scale-100 rotate-0"
            class="h-5 w-5 stroke-white"
            viewBox="0 0 24 24"
            fill="none"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
        </svg>

        <div
            class="invisible absolute top-full right-0 mt-5 flex scale-95 flex-col items-end opacity-0 transition-all duration-300 group-hover:visible group-hover:scale-100 group-hover:opacity-100"
        >
            <div
                class="flex items-center justify-center whitespace-nowrap rounded-md border border-black/10 bg-zinc-900 !p-2.5 !mt-1 text-xs font-medium text-white shadow-xl backdrop-blur-sm transition-all duration-300 dark:border-zinc-200 dark:bg-zinc-50 dark:text-zinc-900"
            >
                <span x-text="theme === 'dark' ? '{{ __('app.theme.switch_light') }}' : '{{ __('app.theme.switch_dark') }}'"></span>
            </div>
        </div>
    </button>
</div>
