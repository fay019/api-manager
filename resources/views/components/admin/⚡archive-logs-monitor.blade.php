<?php

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

new class extends Component
{
    public $isRunning = false;
    public $result = null;

    #[\Livewire\Attributes\On('check-archive-status')]
    public function checkStatus()
    {
        $this->isRunning = DB::table('jobs')
            ->where('payload', 'like', '%ArchiveApiRequestLogsJob%')
            ->exists();

        $this->result = Cache::get('archive_logs_result');

        if (!$this->isRunning && $this->result) {
            $this->dispatch('archive-completed', result: $this->result);
        }
    }

    public function mount()
    {
        $this->checkStatus();
    }
};
?>

<div wire:poll-2s="checkStatus">
    @if ($isRunning)
        <div class="p-3 bg-amber-50 border border-amber-200 rounded text-sm text-amber-700">
            ⏳ {{ __('filament.log.archive_queued_message') ?? 'Archive in progress...' }}
        </div>
    @elseif ($result && $result['status'] === 'completed')
        <div class="p-3 bg-green-50 border border-green-200 rounded">
            <div class="text-sm text-green-700 font-medium">
                ✓ {{ __('Archivage terminé!') }}
            </div>
            <div class="text-xs text-green-600 mt-2 font-mono whitespace-pre-wrap break-words max-h-48 overflow-y-auto">
                {{ $result['output'] }}
            </div>
        </div>
    @elseif ($result && $result['status'] === 'failed')
        <div class="p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
            ✗ Erreur: {{ $result['error'] }}
        </div>
    @endif
</div>