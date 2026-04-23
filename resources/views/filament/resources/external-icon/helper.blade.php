<x-filament-widgets::widget>
    <x-filament::section collapsible collapsed icon="heroicon-o-information-circle" icon-color="primary">
        <x-slot name="heading">
            {{ __('filament.external_icon.helper_heading') }}
        </x-slot>

        <div class="grid md:grid-cols-3 gap-6">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 font-medium uppercase tracking-wider">{{ __('filament.external_icon.helper_blade_title') }}</p>
                <div class="bg-gray-50 dark:bg-black/20 p-3 rounded-lg border border-gray-100 dark:border-gray-800">
                    <code class="text-xs font-mono text-primary-600 dark:text-primary-400">
                        &lt;x-icon name="home" class="w-6 h-6" /&gt;
                    </code>
                </div>
                <p class="text-xs text-gray-500 mt-2">{!! __('filament.external_icon.helper_blade_desc', ['slug' => '<span class="font-mono bg-gray-100 dark:bg-gray-800 px-1 rounded">slug</span>']) !!}</p>
            </div>

            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 font-medium uppercase tracking-wider">{{ __('filament.external_icon.helper_php_title') }}</p>
                <div class="bg-gray-50 dark:bg-black/20 p-3 rounded-lg border border-gray-100 dark:border-gray-800">
                    <code class="text-xs font-mono text-primary-600 dark:text-primary-400">
                        app(App\Services\IconService::class)->getIcon('home')
                    </code>
                </div>
                <p class="text-xs text-gray-500 mt-2">{{ __('filament.external_icon.helper_php_desc') }}</p>
            </div>

            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 font-medium uppercase tracking-wider">{{ __('filament.external_icon.helper_cdn_title') }}</p>
                <div class="bg-gray-50 dark:bg-black/20 p-3 rounded-lg border border-gray-100 dark:border-gray-800">
                    <code class="text-xs font-mono text-primary-600 dark:text-primary-400">
                        https://example.com/icon.png
                    </code>
                </div>
                <p class="text-xs text-gray-500 mt-2">{{ __('filament.external_icon.helper_cdn_desc') }}</p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
