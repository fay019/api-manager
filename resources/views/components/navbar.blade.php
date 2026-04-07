<div class="navbar">
    {{ $slot }}
</div>

<style>
    .navbar {
        background: white;
        border-bottom: 1px solid #eee;
        border-radius: 8px;
        padding: 20px 40px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .navbar a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        font-size: 1.1em;
    }

    .navbar a:hover {
        color: #764ba2;
    }

    html.dark .navbar {
        background: #374151;
        border-bottom-color: #4b5563;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    html.dark .navbar a {
        color: #818cf8;
    }

    html.dark .navbar a:hover {
        color: #a5b4fc;
    }
</style>
