<style>
    :root {
        --settings-text-dark: #111827;
        --settings-text-muted: #6b7280;
        --settings-text-light: #374151;
        --settings-bg-card: white;
        --settings-bg-header: #f9fafb;
        --settings-bg-input: #f3f4f6;
        --settings-border: #d1d5db;
        --settings-border-light: #e5e7eb;
        --settings-info-bg: #fffbeb;
        --settings-info-border: #fde68a;
        --settings-info-text: #92400e;
        --settings-info-text-dark: #b45309;
        --settings-danger-bg: white;
        --settings-danger-header-bg: #fef2f2;
        --settings-danger-border: #fee2e2;
        --settings-danger-text-dark: #991b1b;
        --settings-danger-text: #b91c1c;
        --settings-env-dev-bg: #dbeafe;
        --settings-env-dev-text: #1e40af;
        --settings-env-staging-bg: #fef3c7;
        --settings-env-staging-text: #92400e;
        --settings-env-prod-bg: #fee2e2;
        --settings-env-prod-text: #991b1b;
    }

    html.dark {
        --settings-text-dark: #f3f4f6;
        --settings-text-muted: #d1d5db;
        --settings-text-light: #d1d5db;
        --settings-bg-card: #1f2937;
        --settings-bg-header: #111827;
        --settings-bg-input: #374151;
        --settings-border: #374151;
        --settings-border-light: #374151;
        --settings-info-bg: #78350f;
        --settings-info-border: #b45309;
        --settings-info-text: #fef3c7;
        --settings-info-text-dark: #fcd34d;
        --settings-danger-bg: #7f1d1d;
        --settings-danger-header-bg: #991b1b;
        --settings-danger-border: #b91c1c;
        --settings-danger-text-dark: #fecaca;
        --settings-danger-text: #f87171;
        --settings-env-dev-bg: #1e3a8a;
        --settings-env-dev-text: #93c5fd;
        --settings-env-staging-bg: #78350f;
        --settings-env-staging-text: #fcd34d;
        --settings-env-prod-bg: #7f1d1d;
        --settings-env-prod-text: #fecaca;
    }

    .settings-card {
        background: var(--settings-bg-card);
        border: 1px solid var(--settings-border);
        border-radius: 0.5rem;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .settings-header {
        background: var(--settings-bg-header);
        padding: 1rem;
        border-bottom: 1px solid var(--settings-border-light);
    }

    .settings-header h3 {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--settings-text-dark);
        margin: 0 0 0.25rem 0;
    }

    .settings-header p {
        font-size: 0.875rem;
        color: var(--settings-text-muted);
        margin: 0;
    }

    .settings-content {
        padding: 1.5rem;
    }

    .settings-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--settings-text-muted);
        margin-bottom: 0.5rem;
    }

    .settings-value {
        padding: 0.75rem;
        background: var(--settings-bg-input);
        border-radius: 0.375rem;
        color: var(--settings-text-dark);
    }

    .settings-info-box {
        background: var(--settings-info-bg);
        border: 1px solid var(--settings-info-border);
        border-radius: 0.5rem;
        padding: 1rem;
    }

    .settings-info-box p {
        color: var(--settings-info-text);
    }

    .settings-info-box p:last-child {
        color: var(--settings-info-text-dark);
    }

    .settings-danger-card {
        background: var(--settings-danger-bg);
        border: 1px solid var(--settings-danger-border);
        border-radius: 0.5rem;
        overflow: hidden;
        margin-top: 2rem;
    }

    .settings-danger-header {
        background: var(--settings-danger-header-bg);
        padding: 1rem;
        border-bottom: 1px solid var(--settings-danger-border);
    }

    .settings-danger-header h3 {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--settings-danger-text-dark);
        margin: 0 0 0.25rem 0;
    }

    .settings-danger-header p {
        font-size: 0.875rem;
        color: var(--settings-danger-text);
        margin: 0;
    }

    .settings-danger-content {
        padding: 1.5rem;
    }

    .settings-danger-content p {
        font-size: 0.875rem;
        color: var(--settings-text-light);
        margin-bottom: 1.5rem;
    }

    .env-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 0.25rem;
        display: inline-block;
    }

    .env-dev {
        background: var(--settings-env-dev-bg);
        color: var(--settings-env-dev-text);
    }

    .env-staging {
        background: var(--settings-env-staging-bg);
        color: var(--settings-env-staging-text);
    }

    .env-prod {
        background: var(--settings-env-prod-bg);
        color: var(--settings-env-prod-text);
    }
</style>

<x-filament-panels::page>
    <!-- Application Settings -->
    <div class="settings-card">
        <!-- Header -->
        <div class="settings-header">
            <h3>Paramètres Généraux</h3>
            <p>Information générale de l'application</p>
        </div>

        <!-- Content -->
        <div class="settings-content">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                <!-- Nom du Site -->
                <div>
                    <label class="settings-label">Nom du Site</label>
                    <div class="settings-value">
                        {{ env('APP_NAME', 'API Manager') }}
                    </div>
                </div>

                <!-- URL Application -->
                <div>
                    <label class="settings-label">URL de l'Application</label>
                    <div class="settings-value">
                        {{ env('APP_URL', 'http://localhost:8000') }}
                    </div>
                </div>

                <!-- Environnement -->
                <div>
                    <label class="settings-label">Environnement</label>
                    <div class="settings-value">
                        @if(env('APP_ENV') === 'local')
                            <span class="env-badge env-dev">
                                Développement
                            </span>
                        @elseif(env('APP_ENV') === 'staging')
                            <span class="env-badge env-staging">
                                Staging
                            </span>
                        @else
                            <span class="env-badge env-prod">
                                Production
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Coming Soon Features -->
    <div class="settings-info-box">
        <div style="display: flex; gap: 0.75rem;">
            <div style="font-size: 1.25rem; flex-shrink: 0;">ℹ️</div>
            <div>
                <p style="font-weight: 600; margin: 0 0 0.25rem 0;">Configurations Futures</p>
                <p style="font-size: 0.875rem; margin: 0;">
                    Les configurations suivantes seront disponibles bientôt: Email (SMTP), Cache & Performance, Queue & Jobs, et Paramètres API.
                </p>
            </div>
        </div>
    </div>
    <!-- Danger Zone: Application Reset -->
    @if(!app()->environment('production') || config('installation.wizard.security.allow_production_reset', false))
        <div class="settings-danger-card">
            <!-- Header -->
            <div class="settings-danger-header">
                <h3>Zone de Danger</h3>
                <p>Actions destructives pour l'application</p>
            </div>

            <div class="settings-danger-content">
                <p>
                    La réinitialisation supprimera la base de données (si SQLite), les logs, et remettra l'application en mode installation.
                </p>

                {{ $this->resetApplicationAction }}
            </div>
        </div>
    @endif
</x-filament-panels::page>
