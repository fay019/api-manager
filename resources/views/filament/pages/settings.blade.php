<x-filament-panels::page>
    <!-- Application Settings -->
    <div style="background: white; border: 1px solid #d1d5db; border-radius: 0.5rem; overflow: hidden; margin-bottom: 2rem;">
        <!-- Header -->
        <div style="background: #f9fafb; padding: 1rem; border-bottom: 1px solid #e5e7eb;">
            <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin: 0 0 0.25rem 0;">Paramètres Généraux</h3>
            <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Information générale de l'application</p>
        </div>

        <!-- Content -->
        <div style="padding: 1.5rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                <!-- Nom du Site -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #6b7280; margin-bottom: 0.5rem;">Nom du Site</label>
                    <div style="padding: 0.75rem; background: #f3f4f6; border-radius: 0.375rem; color: #111827;">
                        {{ env('APP_NAME', 'API Manager') }}
                    </div>
                </div>

                <!-- URL Application -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #6b7280; margin-bottom: 0.5rem;">URL de l'Application</label>
                    <div style="padding: 0.75rem; background: #f3f4f6; border-radius: 0.375rem; color: #111827;">
                        {{ env('APP_URL', 'http://localhost:8000') }}
                    </div>
                </div>

                <!-- Environnement -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #6b7280; margin-bottom: 0.5rem;">Environnement</label>
                    <div style="padding: 0.75rem; background: #f3f4f6; border-radius: 0.375rem; color: #111827;">
                        @if(env('APP_ENV') === 'local')
                            <span style="background: #dbeafe; color: #1e40af; padding: 0.25rem 0.75rem; border-radius: 0.25rem; display: inline-block;">
                                Développement
                            </span>
                        @elseif(env('APP_ENV') === 'staging')
                            <span style="background: #fef3c7; color: #92400e; padding: 0.25rem 0.75rem; border-radius: 0.25rem; display: inline-block;">
                                Staging
                            </span>
                        @else
                            <span style="background: #fee2e2; color: #991b1b; padding: 0.25rem 0.75rem; border-radius: 0.25rem; display: inline-block;">
                                Production
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Coming Soon Features -->
    <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 0.5rem; padding: 1rem;">
        <div style="display: flex; gap: 0.75rem;">
            <div style="font-size: 1.25rem; flex-shrink: 0;">ℹ️</div>
            <div>
                <p style="font-weight: 600; color: #92400e; margin: 0 0 0.25rem 0;">Configurations Futures</p>
                <p style="font-size: 0.875rem; color: #b45309; margin: 0;">
                    Les configurations suivantes seront disponibles bientôt: Email (SMTP), Cache & Performance, Queue & Jobs, et Paramètres API.
                </p>
            </div>
        </div>
    </div>
    <!-- Danger Zone: Application Reset -->
    @if(!app()->environment('production'))
        <div style="background: white; border: 1px solid #fee2e2; border-radius: 0.5rem; overflow: hidden; margin-top: 2rem;">
            <!-- Header -->
            <div style="background: #fef2f2; padding: 1rem; border-bottom: 1px solid #fee2e2;">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: #991b1b; margin: 0 0 0.25rem 0;">Zone de Danger</h3>
                <p style="font-size: 0.875rem; color: #b91c1c; margin: 0;">Actions destructives pour l'application</p>
            </div>

            <div style="padding: 1.5rem;">
                <p style="font-size: 0.875rem; color: #374151; margin-bottom: 1.5rem;">
                    La réinitialisation supprimera la base de données (si SQLite), les logs, et remettra l'application en mode installation.
                </p>

                {{ $this->resetApplicationAction }}
            </div>
        </div>
    @endif
</x-filament-panels::page>
