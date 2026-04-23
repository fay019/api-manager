<x-filament-panels::page>
    <div style="display: flex !important; flex-direction: column !important; gap: 3rem !important;">
        {{-- Section Paramètres Généraux --}}
        <x-filament::section
            icon="heroicon-o-cog-6-tooth"
            heading="{{ __('filament.settings.general_section') }}"
            description="{!! __('filament.settings.general_section_desc') !!}"
            collapsible
        >
            <style>
                .settings-column {
                    background-color: #f3f4f6 !important;
                    padding: 1rem !important;
                    border-radius: 0.5rem !important;
                }
                .dark .settings-column {
                    background-color: #1f2937 !important;
                }
                .settings-label {
                    font-size: 0.75rem !important;
                    font-weight: 600 !important;
                    color: #4b5563 !important;
                    text-transform: uppercase !important;
                    letter-spacing: 0.05em !important;
                    margin-bottom: 0.5rem !important;
                }
                .dark .settings-label {
                    color: #9ca3af !important;
                }
                .settings-value {
                    font-size: 1.125rem !important;
                    font-weight: 600 !important;
                    color: #1f2937 !important;
                }
                .dark .settings-value {
                    color: #f3f4f6 !important;
                }
                .settings-value-small {
                    font-size: 0.875rem !important;
                    font-weight: 600 !important;
                    color: #1f2937 !important;
                    overflow: hidden !important;
                    text-overflow: ellipsis !important;
                    white-space: nowrap !important;
                }
                .dark .settings-value-small {
                    color: #f3f4f6 !important;
                }
            </style>
            <div class="space-y-6">
                {{-- Info Columns --}}
                <div style="display: grid !important; grid-template-columns: repeat(3, 1fr) !important; gap: 1.5rem !important;">
                    <div class="settings-column">
                        <p class="settings-label">{{ __('filament.settings.site_name') }}</p>
                        <p class="settings-value">{{ config('app.name') }}</p>
                    </div>
                    <div class="settings-column">
                        <p class="settings-label">{{ __('filament.settings.app_url') }}</p>
                        <p class="settings-value-small">{{ config('app.url') }}</p>
                    </div>
                    <div class="settings-column">
                        <p class="settings-label">{{ __('filament.settings.environment') }}</p>
                        <div>
                            @if(app()->environment('production'))
                                <x-filament::badge color="danger">Production</x-filament::badge>
                            @else
                                <x-filament::badge color="warning">{{ ucfirst(app()->environment()) }}</x-filament::badge>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- Section Email de Contact --}}
        <x-filament::section
            icon="heroicon-o-envelope"
            heading="{{ __('filament.settings.contact_section') }}"
            description="{{ __('filament.settings.contact_section_desc') }}"
            collapsible
        >
            <form wire:submit.prevent="save" style="display: flex !important; flex-direction: column !important; gap: 1.5rem !important; align-items: flex-start !important;">
                <div style="max-width: 300px !important;">
                    {{ $this->form }}
                </div>
                <x-filament::button type="submit" size="sm">{{ __('filament.settings.save_button') }}</x-filament::button>
            </form>
        </x-filament::section>

        {{-- Section Zone de Danger --}}
        <x-filament::section
            icon="heroicon-o-exclamation-triangle"
            heading="{{ __('filament.settings.danger_zone') }}"
            description="{!! __('filament.settings.danger_zone_desc') !!}"
            color="danger"
            collapsible
        >
            <div style="display: flex !important; flex-direction: column !important; gap: 1.5rem !important; align-items: flex-start !important;">
                <p class="text-sm text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/10 p-3 rounded border border-red-200 dark:border-red-900/30">
                    {{ __('filament.settings.reset_warning') }}
                </p>
                <x-filament::button
                    color="danger"
                    size="sm"
                    icon="heroicon-m-fire"
                    wire:click="resetApplication"
                    wire:confirm="{{ __('filament.settings.reset_confirm') }}"
                    outlined
                >
                    {{ __('filament.settings.reset_button') }}
                </x-filament::button>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
