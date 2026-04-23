<x-filament-widgets::widget>
    @php
        $status = $this->getJobStatus();
        $isRunning = $status['isRunning'];
        $result = $status['result'];
    @endphp

    @if ($isRunning)
        <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded">
            <div class="flex items-center gap-3">
                <div class="animate-spin">⏳</div>
                <div>
                    <h3 class="font-semibold text-amber-800">{{ __('filament.log.archive_queued_title') ?? 'Archiving in Progress' }}</h3>
                    <p class="text-sm text-amber-700">{{ __('filament.log.archive_queued_message') ?? 'Log archival is being processed...' }}</p>
                </div>
            </div>
        </div>
    @elseif ($result && $result['status'] === 'completed')
        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
            <div class="flex items-start gap-3">
                <div class="text-2xl">✓</div>
                <div class="flex-1">
                    <h3 class="font-semibold text-green-800">{{ __('Archivage Terminé') }}</h3>
                    <p class="text-sm text-green-700 mt-2 font-mono whitespace-pre-wrap text-xs max-h-32 overflow-y-auto">
                        {{ $result['output'] }}
                    </p>
                </div>
            </div>
        </div>
    @elseif ($result && $result['status'] === 'failed')
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
            <div class="flex items-start gap-3">
                <div class="text-2xl">✗</div>
                <div>
                    <h3 class="font-semibold text-red-800">{{ __('Erreur lors de l\'archivage') }}</h3>
                    <p class="text-sm text-red-700 mt-1">{{ $result['error'] }}</p>
                </div>
            </div>
        </div>
    @endif
</x-filament-widgets::widget>
