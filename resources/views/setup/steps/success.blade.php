@extends('setup.layout')

@section('content')
<div class="success-container">
    <div class="success-icon">✅</div>

    <div class="setup-header">
        <h1>Installation Réussie!</h1>
        <p>Étape {{ $currentStep }}/{{ $totalSteps }}</p>
    </div>

    <div class="success-content">
        <p style="font-size: 16px; margin-bottom: 20px; color: #333;">
            Félicitations! L'application a été installée et configurée avec succès.
        </p>

        <div class="info-box success-box">
            <strong>✨ Prochaines étapes:</strong>
            <ul style="margin: 12px 0 0 0; padding-left: 20px; font-size: 13px;">
                <li>Vous allez être redirigé vers la page de connexion</li>
                <li>Connectez-vous avec l'email et mot de passe de l'administrateur que vous avez créé</li>
                <li>Vous pourrez alors accéder au tableau de bord complet</li>
            </ul>
        </div>

        <div style="margin: 20px 0; padding: 15px; background: #fffbeb; border-radius: 6px; border-left: 3px solid #f59e0b;">
            <strong style="color: #92400e;">⚠️ Actions manuelles obligatoires:</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px; font-size: 12px; color: #b45309; text-align: left;">
                <li><strong>Vider vos cookies :</strong> Si vous rencontrez une erreur à la connexion, videz les cookies de votre navigateur pour ce domaine.</li>
                <li><strong>Permissions SQLite :</strong> Vérifiez que le dossier <code>database/</code> est accessible en écriture pour l'application.</li>
                <li><strong>Sécurité :</strong> Assurez-vous que le dossier <code>storage/app/setup/</code> est désormais vide.</li>
            </ul>
        </div>

        <div style="margin: 20px 0; padding: 15px; background: #f3f4f6; border-radius: 6px; border-left: 3px solid #667eea;">
            <strong style="color: #333;">ℹ️ Informations importantes:</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px; font-size: 12px; color: #666; text-align: left;">
                <li>L'application est maintenant bloquée contre les réinstallations</li>
                <li>Les informations de configuration sont sauvegardées dans .env</li>
                <li>Vous pouvez créer d'autres utilisateurs dans le tableau de bord</li>
                <li>Conservez vos identifiants de connexion en lieu sûr</li>
            </ul>
        </div>
    </div>

    <div class="form-actions">
        <a href="/admin" class="btn btn-primary">
            🚀 Accéder au Tableau de Bord
        </a>
    </div>

    <!-- Compteur décompte -->
    <div style="text-align: center; margin-top: 30px; color: #999; font-size: 12px;">
        Redirection automatique dans <span id="countdown">5</span> secondes...
    </div>
</div>

<script>
    // Décompte redirection
    let count = 5;
    const countdownEl = document.getElementById('countdown');

    const interval = setInterval(() => {
        count--;
        countdownEl.textContent = count;

        if (count === 0) {
            clearInterval(interval);
            window.location.href = '/admin';
        }
    }, 1000);
</script>

<style>
    .success-container {
        text-align: center;
    }

    .success-icon {
        font-size: 60px;
        margin-bottom: 20px;
        animation: bounce 1s ease-in-out infinite;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .success-content {
        margin-bottom: 30px;
    }

    .success-box {
        background: #dcfce7;
        border-left-color: #22c55e;
        color: #166534;
    }

    .info-box {
        background: #f3f4f6;
        border-left: 3px solid #667eea;
        padding: 12px 16px;
        border-radius: 4px;
        font-size: 13px;
        color: #333;
    }

    .setup-header h1 {
        margin-bottom: 10px;
        color: #22c55e;
    }

    .setup-header p {
        color: #666;
        font-size: 14px;
    }
</style>
@endsection

