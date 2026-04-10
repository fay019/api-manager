@php
    $locales = [
        'en' => __('app.locale.english'),
        'fr' => __('app.locale.french'),
        'de' => __('app.locale.german'),
    ];
    $currentLocale = app()->getLocale();
@endphp

<div class="locale-switcher">
    @foreach($locales as $locale => $label)
        @if($locale === $currentLocale)
            <span class="locale-button active" title="{{ $label }}">
                {{ strtoupper($locale) }}
            </span>
        @else
            <a href="{{ route('locale.switch', $locale) }}" class="locale-button" title="{{ $label }}">
                {{ strtoupper($locale) }}
            </a>
        @endif
    @endforeach
</div>

<style>
    .locale-switcher {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .locale-button {
        padding: 6px 12px;
        border-radius: 4px;
        border: 1px solid #d1d5db;
        background: white;
        color: #374151;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
    }

    .locale-button:hover:not(.active) {
        background: #f3f4f6;
        border-color: #9ca3af;
    }

    .locale-button.active {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    html.dark .locale-button {
        border-color: #4b5563;
        background: #374151;
        color: #f3f4f6;
    }

    html.dark .locale-button:hover:not(.active) {
        background: #4b5563;
        border-color: #6b7280;
    }

    html.dark .locale-button.active {
        background: #818cf8;
        color: white;
        border-color: #818cf8;
    }
</style>
