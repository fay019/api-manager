<x-filament-panels::page>
    <x-filament-actions::modals />

    {{ $this->form }}

    @if ($showModal)
        <!-- Backdrop -->
        <div class="!fixed !inset-0 !z-40 !bg-black/40 !backdrop-blur-sm"
             style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999; background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px);"
             wire:click="$set('showModal', false)">
        </div>

        <!-- Modal Container -->
        <div class="!fixed !inset-0 !z-50 !flex !items-center !justify-center !p-4"
             style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 10000; display: flex; align-items: center; justify-content: center; padding: 1rem;">
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden"
                 style="width: 100%; max-width: 48rem;"
                 @click.stop>

                <!-- Header -->
                <div style="background: linear-gradient(to right, #2563eb, #4f46e5); padding: 1.5rem 2rem; display: flex; align-items: center; justify-content: space-between; border-radius: 0.75rem;">
                    <h2 style="font-size: 1.5rem; font-weight: bold; color: white; margin: 0;">{{ __('filament.ia_settings.test_modal_title') }}</h2>
                    <button wire:click="$set('showModal', false)" style="color: white; background: none; border: none; cursor: pointer; padding: 0.25rem; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;" onmouseover="this.style.color='#dbeafe'; this.style.transform='scale(1.2)';" onmouseout="this.style.color='white'; this.style.transform='scale(1)';">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px; stroke-width: 2.5;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-8 max-h-[calc(90vh-200px)] overflow-y-auto">
                    @if ($testError)
                        <div style="padding: 1.5rem; background-color: #fef2f2; border: 2px solid #fca5a5; border-radius: 0.75rem;">
                            <div style="display: flex; gap: 1rem;">
                                <div style="flex-shrink: 0; padding-top: 0.125rem;">
                                    <svg class="w-6 h-6" fill="#dc2626" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p style="font-weight: bold; color: #991b1b; font-size: 1.125rem; margin: 0;">{{ __('filament.ia_settings.test_error_title') }}</p>
                                    <p style="font-size: 0.875rem; color: #7f1d1d; margin-top: 0.5rem; margin-bottom: 0;">{{ $testError }}</p>
                                </div>
                            </div>
                        </div>
                    @elseif ($testResult)
                        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                            <!-- Model Card -->
                            <div style="padding: 1.5rem; background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%); border: 1px solid #bfdbfe; border-radius: 0.75rem;">
                                <p style="font-size: 0.875rem; font-weight: bold; color: #1e40af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">{{ __('filament.ia_settings.test_model') }}</p>
                                <code style="display: block; font-size: 1.125rem; font-weight: 600; color: #1e3a8a; background-color: #ffffff; padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid #bfdbfe; font-family: monospace;">
                                    {{ $testResult['model'] }}
                                </code>
                            </div>

                            <!-- Prompt & Response -->
                            <div style="padding: 1.5rem; background: linear-gradient(135deg, #faf5ff 0%, #faf5ff 100%); border: 1px solid #d8b4fe; border-radius: 0.75rem;">
                                <p style="font-size: 0.875rem; font-weight: bold; color: #6b21a8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">{{ __('filament.ia_settings.test_prompt') }}</p>
                                <p style="color: #3f0f5c; font-weight: 600; margin-bottom: 1rem; font-style: italic;">Bonjour</p>

                                <p style="font-size: 0.875rem; font-weight: bold; color: #6b21a8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">{{ __('filament.ia_settings.test_response') }}</p>
                                <p style="color: #3f0f5c; line-height: 1.6; white-space: pre-wrap; word-break: break-word; background-color: #ffffff; padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid #d8b4fe;">
                                    {{ $testResult['response'] }}
                                </p>
                            </div>

                            <!-- Metrics Grid -->
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                                <!-- Duration -->
                                <div style="padding: 1.25rem; background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fcd34d; border-radius: 0.75rem; text-align: center;">
                                    <p style="font-size: 0.75rem; font-weight: bold; color: #92400e; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">{{ __('filament.ia_settings.test_duration') }}</p>
                                    <div style="display: flex; align-items: baseline; justify-content: center; gap: 0.25rem;">
                                        <p style="font-size: 1.875rem; font-weight: 900; color: #78350f;">{{ $testResult['duration_ms'] }}</p>
                                        <p style="font-size: 0.875rem; font-weight: 600; color: #92400e;">{{ __('filament.ia_settings.test_duration_unit') }}</p>
                                    </div>
                                </div>

                                <!-- Prompt Tokens -->
                                <div style="padding: 1.25rem; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #86efac; border-radius: 0.75rem; text-align: center;">
                                    <p style="font-size: 0.75rem; font-weight: bold; color: #15803d; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">{{ __('filament.ia_settings.test_prompt_tokens') }}</p>
                                    <p style="font-size: 1.875rem; font-weight: 900; color: #166534;">{{ $testResult['prompt_eval_count'] }}</p>
                                </div>

                                <!-- Response Tokens -->
                                <div style="padding: 1.25rem; background: linear-gradient(135deg, #ecf0ff 0%, #e0e7ff 100%); border: 1px solid #a5b4fc; border-radius: 0.75rem; text-align: center;">
                                    <p style="font-size: 0.75rem; font-weight: bold; color: #3730a3; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">{{ __('filament.ia_settings.test_response_tokens') }}</p>
                                    <p style="font-size: 1.875rem; font-weight: 900; color: #312e81;">{{ $testResult['eval_count'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Footer -->
                <div style="padding: 1.5rem 2rem; display: flex; justify-content: flex-end;"></div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
