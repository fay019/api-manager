<x-filament-widgets::widget>
    @php
        $config = [
            'laravel' => app()->version(),
            'php' => PHP_VERSION,
            'git' => $this->getGitVersion(),
            'branch' => $this->getGitBranch(),
            'commit' => $this->getGitCommit(),
        ];

        $items = [
            ['name' => __('filament.config.laravel'), 'value' => $config['laravel'], 'icon' => 'devicon-laravel', 'color' => '#dc2626'],
            ['name' => __('filament.config.php'), 'value' => $config['php'], 'icon' => 'devicon-php', 'color' => '#2563eb'],
            ['name' => __('filament.config.git'), 'value' => $config['git'], 'icon' => 'devicon-git', 'color' => '#ea580c'],
            ['name' => __('filament.config.branch'), 'value' => $config['branch'], 'icon' => 'lucide-git-fork', 'color' => '#f59e0b'],
            ['name' => __('filament.config.commit'), 'value' => $config['commit'], 'icon' => 'lucide-git-commit', 'color' => '#10b981'],
        ];
    @endphp

    <section class="fi-section">
        <div class="fi-section-header-ctn">
            <h3 class="fi-section-header-heading" style="display: flex; align-items: center; gap: 0.75rem; padding-left: 1rem; padding-top: 1rem;">
                <div style="width: 1.25rem; height: 1.25rem; display: flex; align-items: center; justify-content: center; color: rgb(107, 114, 128);">
                    @svg('heroicon-o-cog-6-tooth', '')
                </div>
                <span>{{ __('filament.configuration') ?? 'Configuration' }}</span>
            </h3>
        </div>

        <div class="fi-section-content-ctn">
            <div class="fi-section-content">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                    @foreach($items as $item)
                        <div style="position: relative; display: flex; flex-direction: column; border-radius: 0.75rem; border: 1px solid rgb(229, 231, 235); background-color: white; padding: 1rem; transition: all 0.2s;"
                             onmouseover="this.style.borderColor='rgb(209, 213, 219)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)';"
                             onmouseout="this.style.borderColor='rgb(229, 231, 235)'; this.style.boxShadow='none';">

                            <!-- Icon & Label -->
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <div style="display: flex; align-items: center; justify-content: center; height: 2rem; width: 2rem; border-radius: 0.5rem; background-color: {{ $item['color'] }}15; color: {{ $item['color'] }};">
                                    @svg($item['icon'], 'w-5 h-5')
                                </div>
                                <span style="font-size: 0.875rem; font-weight: 500; color: rgb(55, 65, 81);">{{ $item['name'] }}</span>
                            </div>

                            <!-- Value -->
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; flex: 1;">
                                <code style="flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 0.75rem; font-family: monospace; color: rgb(75, 85, 99);">{{ $item['value'] }}</code>
                                @if($item['name'] === __('filament.config.commit'))
                                    <div style="position: relative;" x-data="{ copied: false, showTooltip: false }">
                                        <button type="button"
                                            @click="
                                                const text = '{{ $item['value'] }}';
                                                const textarea = document.createElement('textarea');
                                                textarea.value = text;
                                                document.body.appendChild(textarea);
                                                textarea.select();
                                                document.execCommand('copy');
                                                document.body.removeChild(textarea);
                                                copied = true;
                                                showTooltip = true;
                                                setTimeout(() => { copied = false; showTooltip = false; }, 2000);
                                            "
                                            @mouseenter="if (!copied) showTooltip = true"
                                            @mouseleave="if (!copied) showTooltip = false"
                                            :style="{ color: copied ? 'rgb(34, 197, 94)' : 'rgb(156, 163, 175)' }"
                                            style="flex-shrink: 0; transition: color 0.2s; background: none; border: none; cursor: pointer; padding: 0.25rem;">
                                            <svg x-show="!copied" style="width: 1rem; height: 1rem; display: block;" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M8 16.5a2 2 0 11-4 0 2 2 0 014 0zM15 16.5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                                            </svg>
                                            <svg x-show="copied" style="width: 1rem; height: 1rem; display: block;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                        <div x-show="showTooltip" style="position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%); margin-bottom: 0.5rem; color: white; padding: 0.5rem 0.75rem; border-radius: 0.375rem; font-size: 0.75rem; white-space: nowrap; pointer-events: none; z-index: 10;" :style="{ backgroundColor: copied ? 'rgb(34, 197, 94)' : 'rgb(31, 41, 55)' }">
                                            <span x-text="copied ? '{{ __('filament.config.copied') }}' : '{{ __('filament.config.copy_hash') }}'"></span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</x-filament-widgets::widget>
