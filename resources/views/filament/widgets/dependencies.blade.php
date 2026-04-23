<x-filament-widgets::widget>
    @php
        $dependencies = $this->getDependencies();
    @endphp

    <style>
        .fi-config-icon svg {
            fill: currentColor;
            stroke: currentColor;
        }
        .fi-config-icon svg * {
            fill: currentColor;
            stroke: currentColor;
        }
    </style>

    <section class="fi-section">
        <div class="fi-section-header-ctn">
            <h3 class="fi-section-header-heading" style="display: flex; align-items: center; gap: 0.75rem; padding-left: 1rem; padding-top: 1rem;">
                <div style="width: 1.25rem; height: 1.25rem; display: flex; align-items: center; justify-content: center; color: rgb(107, 114, 128);">
                    @svg('heroicon-o-cube-transparent', '')
                </div>
                <span>{{ __('filament.dependencies') ?? 'Dependencies' }}</span>
            </h3>
        </div>

        <div class="fi-section-content-ctn">
            <div class="fi-section-content">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                    @forelse($dependencies as $dep)
                        <div style="position: relative; display: flex; flex-direction: column; border-radius: 0.75rem; border: 1px solid rgb(229, 231, 235); background-color: white; padding: 1rem; transition: all 0.2s;"
                             onmouseover="this.style.borderColor='rgb(209, 213, 219)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)';"
                             onmouseout="this.style.borderColor='rgb(229, 231, 235)'; this.style.boxShadow='none';">

                            <!-- Icon & Label -->
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <div class="fi-config-icon" style="display: flex; align-items: center; justify-content: center; height: 2rem; width: 2rem; border-radius: 0.5rem; background-color: {{ $dep['color'] }}15; color: {{ $dep['color'] }};">
                                    @svg($dep['icon'], 'w-5 h-5')
                                </div>
                                <span style="font-size: 0.875rem; font-weight: 500; color: rgb(55, 65, 81);">{{ $dep['name'] }}</span>
                            </div>

                            <!-- Version -->
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; flex: 1;">
                                <code style="flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 0.75rem; font-family: monospace; color: rgb(75, 85, 99);">{{ $dep['version'] }}</code>
                            </div>
                        </div>
                    @empty
                        <div style="grid-column: 1 / -1; padding: 2rem; text-align: center; color: rgb(107, 114, 128);">
                            {{ __('filament.dependencies_not_found') ?? 'No dependencies found' }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</x-filament-widgets::widget>
