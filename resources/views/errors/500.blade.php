<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur Serveur 500</title>
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
            margin-bottom: 25px;
        }
        .log-section {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
            max-height: 300px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #374151;
            line-height: 1.4;
        }
        .log-section h4 {
            color: #111827;
            margin-bottom: 10px;
            font-size: 13px;
        }
        .log-entry {
            margin-bottom: 10px;
            padding: 8px;
            background: white;
            border-left: 3px solid #dc2626;
            padding-left: 12px;
        }
        .info-box {
            background: #dbeafe;
            border: 1px solid #93c5fd;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
            color: #1e40af;
            font-size: 13px;
        }
        .info-box strong {
            display: block;
            margin-bottom: 5px;
        }
        .actions {
            display: flex;
            gap: 10px;
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
            background: #3b82f6;
            color: white;
        }
        .btn-primary:hover {
            background: #2563eb;
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
        <div class="error-code">500</div>
        <div class="error-title">Erreur Serveur Interne</div>
        <div class="error-description">
            Une erreur est survenue lors du traitement de votre requête. Veuillez réessayer ou contacter l'administrateur.
        </div>

        @if(config('app.debug'))
            <div class="info-box">
                <strong>⚠️ Mode Debug Activé</strong>
                Les informations détaillées des erreurs s'affichent ci-dessous.
                <br><small style="opacity: 0.8;">Désactivez ce mode en production en mettant APP_DEBUG=false dans .env</small>
            </div>

            <div class="log-section">
                <h4>📋 Logs Récents (dernier en haut)</h4>
                @php
                    $logFile = storage_path('logs/laravel.log');
                    if (file_exists($logFile)) {
                        $lines = array_reverse(file($logFile));
                        $displayed = 0;
                        foreach ($lines as $line) {
                            if ($displayed >= 20) break;
                            $trimmed = trim($line);
                            if (!empty($trimmed)) {
                                echo '<div class="log-entry">' . htmlspecialchars($trimmed) . '</div>';
                                $displayed++;
                            }
                        }
                    } else {
                        echo '<p style="color: #9ca3af;">Aucun log trouvé</p>';
                    }
                @endphp
            </div>

            <div class="info-box" style="background: #fef2f2; border-color: #fecaca; color: #991b1b;">
                <strong>📁 Fichier Log Complet</strong>
                {{ $logFile ?? storage_path('logs/laravel.log') }}
                <br><small style="opacity: 0.8;">Consultez ce fichier pour les informations complètes</small>
            </div>
        @else
            <div class="info-box">
                <strong>💡 Activer le Mode Debug</strong>
                Pour voir les détails des erreurs, mettez APP_DEBUG=true dans le fichier .env et redémarrez l'application.
            </div>
        @endif

        <div class="actions">
            <a href="{{ url('/') }}" class="btn btn-primary">← Retour à l'accueil</a>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">← Page précédente</a>
        </div>
    </div>
</body>
</html>
