<x-filament-widgets::widget>
    <x-filament::section
        :heading="__('filament.seo_meta.helper_heading')"
        collapsible
        collapsed
    >
        <x-slot name="icon">
            <x-heroicon-o-information-circle class="fi-icon fi-size-md text-primary-600" style="width: 1.25rem; height: 1.25rem;" />
        </x-slot>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4" style="display: grid; gap: 1rem;">
            {{-- Scanning Logic --}}
            <div class="p-4 rounded-xl transition-all" style="padding: 1rem; border-radius: 0.75rem; background-color: rgba(249, 250, 251, 0.5); border: 1px solid #f3f4f6;">
                <div class="flex items-center gap-3 mb-3" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <div class="p-2 rounded-lg" style="padding: 0.5rem; background-color: #eff6ff; border-radius: 0.5rem; border: 1px solid rgba(59, 130, 246, 0.2);">
                        <x-heroicon-o-magnifying-glass-circle class="fi-icon fi-size-md text-primary-600 dark:text-primary-400" style="width: 1.25rem; height: 1.25rem; color: #2563eb;" />
                    </div>
                    <h3 class="text-sm font-bold text-gray-900" style="font-size: 0.875rem; font-weight: 700; color: #111827; margin: 0;">
                        {{ __('filament.seo_meta.helper_scan_title') }}
                    </h3>
                </div>
                <p class="text-[11px] leading-relaxed text-gray-600" style="font-size: 11px; line-height: 1.625; color: #4b5563; margin: 0;">
                    {{ __('filament.seo_meta.helper_scan_desc') }}
                </p>
            </div>

            {{-- Priority Logic --}}
            <div class="p-4 rounded-xl transition-all" style="padding: 1rem; border-radius: 0.75rem; background-color: rgba(249, 250, 251, 0.5); border: 1px solid #f3f4f6;">
                <div class="flex items-center gap-3 mb-3" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <div class="p-2 rounded-lg" style="padding: 0.5rem; background-color: #eff6ff; border-radius: 0.5rem; border: 1px solid rgba(59, 130, 246, 0.2);">
                        <x-heroicon-o-arrows-right-left class="fi-icon fi-size-md text-primary-600 dark:text-primary-400" style="width: 1.25rem; height: 1.25rem; color: #2563eb;" />
                    </div>
                    <h3 class="text-sm font-bold text-gray-900" style="font-size: 0.875rem; font-weight: 700; color: #111827; margin: 0;">
                        {{ __('filament.seo_meta.helper_priority_title') }}
                    </h3>
                </div>
                <p class="text-[11px] leading-relaxed text-gray-600" style="font-size: 11px; line-height: 1.625; color: #4b5563; margin: 0;">
                    {{ __('filament.seo_meta.helper_priority_desc') }}
                </p>
            </div>

            {{-- Social Sharing --}}
            <div class="p-4 rounded-xl transition-all" style="padding: 1rem; border-radius: 0.75rem; background-color: rgba(249, 250, 251, 0.5); border: 1px solid #f3f4f6;">
                <div class="flex items-center gap-3 mb-3" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <div class="p-2 rounded-lg" style="padding: 0.5rem; background-color: #eff6ff; border-radius: 0.5rem; border: 1px solid rgba(59, 130, 246, 0.2);">
                        <x-heroicon-o-share class="fi-icon fi-size-md text-primary-600 dark:text-primary-400" style="width: 1.25rem; height: 1.25rem; color: #2563eb;" />
                    </div>
                    <h3 class="text-sm font-bold text-gray-900" style="font-size: 0.875rem; font-weight: 700; color: #111827; margin: 0;">
                        {{ __('filament.seo_meta.helper_tags_title') }}
                    </h3>
                </div>
                <p class="text-[11px] leading-relaxed text-gray-600" style="font-size: 11px; line-height: 1.625; color: #4b5563; margin: 0;">
                    {{ __('filament.seo_meta.helper_tags_desc') }}
                </p>
            </div>

            {{-- Ignore Logic --}}
            <div class="p-4 rounded-xl transition-all" style="padding: 1rem; border-radius: 0.75rem; background-color: rgba(249, 250, 251, 0.5); border: 1px solid #f3f4f6;">
                <div class="flex items-center gap-3 mb-3" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <div class="p-2 rounded-lg" style="padding: 0.5rem; background-color: #eff6ff; border-radius: 0.5rem; border: 1px solid rgba(59, 130, 246, 0.2);">
                        <x-heroicon-o-eye-slash class="fi-icon fi-size-md text-primary-600 dark:text-primary-400" style="width: 1.25rem; height: 1.25rem; color: #2563eb;" />
                    </div>
                    <h3 class="text-sm font-bold text-gray-900" style="font-size: 0.875rem; font-weight: 700; color: #111827; margin: 0;">
                        {{ __('filament.seo_meta.helper_ignore_title') }}
                    </h3>
                </div>
                <p class="text-[11px] leading-relaxed text-gray-600" style="font-size: 11px; line-height: 1.625; color: #4b5563; margin: 0;">
                    {{ __('filament.seo_meta.helper_ignore_desc') }}
                </p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
