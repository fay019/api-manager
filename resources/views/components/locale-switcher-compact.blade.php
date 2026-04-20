<div class="locale-switcher-compact">
    <form id="locale-form-compact" method="POST" style="display: inline;">
        @csrf
        <select name="locale" class="locale-select" onchange="switchLocaleCompact(this.value)">
            @php
                $locales = [
                    'en' => ['label' => '🇬🇧 English', 'flag' => '🇬🇧'],
                    'fr' => ['label' => '🇫🇷 Français', 'flag' => '🇫🇷'],
                    'de' => ['label' => '🇩🇪 Deutsch', 'flag' => '🇩🇪'],
                ];
                $currentLocale = app()->getLocale();
            @endphp

            @foreach($locales as $locale => $info)
                <option value="{{ $locale }}" {{ $currentLocale === $locale ? 'selected' : '' }}>
                    {{ $info['label'] }}
                </option>
            @endforeach
        </select>
    </form>

    <script>
        function switchLocaleCompact(locale) {
            const form = document.getElementById('locale-form-compact');
            form.action = '/locale/' + locale;
            form.submit();
        }
    </script>
</div>

<style>
    .locale-switcher-compact {
        display: flex;
        align-items: center;
    }

    .locale-select {
        padding: 6px 10px;
        border: 1px solid rgba(102, 126, 234, 0.3);
        border-radius: 6px;
        background: white;
        color: #667eea;
        font-weight: 600;
        font-size: 0.9em;
        cursor: pointer;
        transition: all 0.2s ease;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23667eea' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 8px center;
        padding-right: 28px;
    }

    .locale-select:hover {
        border-color: rgba(102, 126, 234, 0.6);
        background-color: rgba(102, 126, 234, 0.05);
    }

    .locale-select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Dark mode */
    html.dark .locale-select {
        background-color: #374151;
        color: #818cf8;
        border-color: rgba(129, 140, 248, 0.3);
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23818cf8' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    }

    html.dark .locale-select:hover {
        border-color: rgba(129, 140, 248, 0.6);
        background-color: #4b5563;
    }

    html.dark .locale-select:focus {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.1);
    }
</style>
