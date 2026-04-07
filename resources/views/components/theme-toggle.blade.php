<button
    id="theme-toggle"
    aria-label="Toggle theme"
    style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 0.5rem; padding: 0.5rem; cursor: pointer; transition: all 0.3s ease; color: white; position: relative;"
    onmouseover="this.style.background='rgba(255, 255, 255, 0.25)'; this.style.borderColor='rgba(255, 255, 255, 0.3)'; showTooltip();"
    onmouseout="this.style.background='rgba(255, 255, 255, 0.15)'; this.style.borderColor='rgba(255, 255, 255, 0.2)'; hideTooltip();"
>
    <!-- Icône soleil (visible en mode clair) -->
    <svg id="theme-toggle-sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
        <circle cx="12" cy="12" r="5"></circle>
        <line x1="12" y1="1" x2="12" y2="3"></line>
        <line x1="12" y1="21" x2="12" y2="23"></line>
        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
        <line x1="1" y1="12" x2="3" y2="12"></line>
        <line x1="21" y1="12" x2="23" y2="12"></line>
        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
    </svg>

    <!-- Icône lune (visible en mode sombre) -->
    <svg id="theme-toggle-moon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
    </svg>

    <!-- Tooltip -->
    <div id="theme-tooltip" style="display: none; position: absolute; bottom: -45px; left: 50%; transform: translateX(-50%); background: rgba(0, 0, 0, 0.9); color: white; padding: 0.5rem 0.75rem; border-radius: 0.25rem; font-size: 12px; white-space: nowrap; z-index: 1000; pointer-events: none;">
        <span id="tooltip-text">Switch to Dark Mode</span>
    </div>
</button>

<script>
function showTooltip() {
    const tooltip = document.getElementById('theme-tooltip');
    const html = document.documentElement;
    const tooltipText = document.getElementById('tooltip-text');

    const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
    tooltipText.textContent = currentTheme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode';

    tooltip.style.display = 'block';
}

function hideTooltip() {
    const tooltip = document.getElementById('theme-tooltip');
    tooltip.style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('theme-toggle');
    const sunIcon = document.getElementById('theme-toggle-sun');
    const moonIcon = document.getElementById('theme-toggle-moon');
    const html = document.documentElement;

    // Récupérer le thème sauvegardé ou utiliser la préférence système
    function getInitialTheme() {
        const saved = localStorage.getItem('theme');
        if (saved) return saved;

        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return 'dark';
        }
        return 'light';
    }

    // Appliquer le thème initial
    function applyTheme(theme) {
        if (theme === 'dark') {
            html.classList.add('dark');
            // En mode dark, montrer l'icone lune
            moonIcon.style.display = 'block';
            sunIcon.style.display = 'none';
        } else {
            html.classList.remove('dark');
            // En mode light, montrer l'icone soleil
            sunIcon.style.display = 'block';
            moonIcon.style.display = 'none';
        }
        localStorage.setItem('theme', theme);
    }

    // Initialiser
    const initialTheme = getInitialTheme();
    applyTheme(initialTheme);

    // Toggle au clic
    toggle.addEventListener('click', function() {
        const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        applyTheme(newTheme);
    });

    // Écouter les changements de préférence système
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.getItem('theme')) {
            applyTheme(e.matches ? 'dark' : 'light');
        }
    });
});
</script>
