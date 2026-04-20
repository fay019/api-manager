@php
    $locales = [
        'en' => __('app.locale.english'),
        'fr' => __('app.locale.french'),
        'de' => __('app.locale.german'),
    ];
    $currentLocale = app()->getLocale();
@endphp

<div class="filament-locale-switcher">
    <form id="locale-form" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="locale" id="locale-input">
    </form>

    @foreach($locales as $locale => $label)
        @if($locale === $currentLocale)
            <span class="filament-locale-button active" title="{{ $label }}">
                {{ strtoupper($locale) }}
            </span>
        @else
            <button type="button" onclick="switchLocale('{{ $locale }}')" class="filament-locale-button" title="{{ $label }}">
                {{ strtoupper($locale) }}
            </button>
        @endif
    @endforeach
</div>

<script>
    function switchLocale(locale) {
        document.getElementById('locale-input').value = locale;
        const form = document.getElementById('locale-form');
        form.action = '/locale/' + locale;
        form.submit();
    }
</script>

<style>
    .filament-locale-switcher {
        display: flex;
        gap: 3px;
        align-items: center;
        margin: 0 12px;
    }

    /* Light mode (Filament default) */
    .filament-locale-button {
        padding: 4px 7px;
        border-radius: 3px;
        border: 1px solid #e5e7eb;
        background: #f3f4f6;
        color: #374151;
        font-size: 0.7rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
        text-align: center;
        min-width: 28px;
    }

    .filament-locale-button:hover:not(.active) {
        background: #e5e7eb;
        border-color: #9ca3af;
    }

    .filament-locale-button.active {
        background: #667eea;
        color: #fff;
        border-color: #667eea;
        cursor: default;
        font-weight: 700;
    }

    /* Dark mode (Filament dark mode) */
    :is(.dark) .filament-locale-button {
        border-color: rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.1);
        color: #f3f4f6;
    }

    :is(.dark) .filament-locale-button:hover:not(.active) {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.4);
    }

    :is(.dark) .filament-locale-button.active {
        background: #818cf8;
        color: #fff;
        border-color: #818cf8;
    }
</style>
