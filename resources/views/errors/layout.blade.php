<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur - @yield('title')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }
        .error-code {
            font-size: 72px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 10px;
        }
        .error-title {
            font-size: 24px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 15px;
        }
        .error-description {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        .btn-primary:hover {
            opacity: 0.9;
        }
        .btn-secondary {
            background: #e5e7eb;
            color: #111827;
        }
        .btn-secondary:hover {
            background: #d1d5db;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-code">@yield('code')</div>
        <div class="error-title">@yield('title')</div>
        <div class="error-description">@yield('message')</div>
        <div class="actions">
            <a href="{{ url('/') }}" class="btn btn-primary">← Retour à l'accueil</a>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">← Page précédente</a>
        </div>
    </div>
</body>
</html>
