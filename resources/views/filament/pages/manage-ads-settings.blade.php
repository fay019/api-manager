<style>
    :root {
        --ads-text-dark: #1f2937;
        --ads-text-muted: #6b7280;
        --ads-bg-card: white;
        --ads-border: #d1d5db;
    }

    html.dark {
        --ads-text-dark: #f3f4f6;
        --ads-text-muted: #d1d5db;
        --ads-bg-card: #1f2937;
        --ads-border: #374151;
    }

    .form-section-wrapper {
        margin-bottom: 1.5rem;
        background: var(--ads-bg-card);
        border: 1px solid var(--ads-border);
        border-radius: 0.5rem;
        padding: 1.5rem;
    }

    .ads-form {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .ads-button-group {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
        padding-top: 1.5rem;
        border-top: 1px solid var(--ads-border);
        margin-top: 1rem;
    }
</style>

<x-filament-panels::page>
    <form wire:submit.prevent="save" class="ads-form">
        <div class="form-section-wrapper">
            {{ $this->form }}
        </div>

        <div class="ads-button-group">
            <x-filament::button type="submit" wire:loading.attr="disabled">
                {{ __('filament.actions.save') }}
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
