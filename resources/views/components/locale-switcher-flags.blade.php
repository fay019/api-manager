@php
    $locales = [
        'en' => ['flag' => '🇬🇧', 'label' => __('app.locale.english')],
        'fr' => ['flag' => '🇫🇷', 'label' => __('app.locale.french')],
        'de' => ['flag' => '🇩🇪', 'label' => __('app.locale.german')],
    ];
    $currentLocale = app()->getLocale();
@endphp

<select class="locale-select" onchange="if(this.value) window.location.href = this.value;">
    @foreach($locales as $locale => $data)
        <option value="{{ route('locale.switch', $locale) }}" {{ $currentLocale === $locale ? 'selected' : '' }}>
            {{ $data['flag'] }} {{ $data['label'] }}
        </option>
    @endforeach
</select>

<style>
    .locale-select {
        padding: 0.5rem 0.75rem;
        border: 1px solid rgba(54, 100, 244, 0.3);
        border-radius: 0.375rem;
        background: white;
        color: #424242;
        font-weight: 500;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
        outline: none;
    }

    .locale-select:hover {
        border-color: rgba(54, 100, 244, 0.6);
        box-shadow: 0 0 8px rgba(54, 100, 244, 0.1);
    }

    .locale-select:focus {
        border-color: #3664F4;
        box-shadow: 0 0 8px rgba(54, 100, 244, 0.2);
    }

    html.dark .locale-select {
        background: #374151;
        color: #e2e8f0;
        border-color: rgba(129, 140, 248, 0.2);
    }

    html.dark .locale-select:hover {
        border-color: rgba(129, 140, 248, 0.5);
        box-shadow: 0 0 8px rgba(129, 140, 248, 0.15);
    }

    html.dark .locale-select:focus {
        border-color: #818cf8;
        box-shadow: 0 0 8px rgba(129, 140, 248, 0.25);
    }
</style>
