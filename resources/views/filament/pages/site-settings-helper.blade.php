<div>
    <x-filament::section
        heading="Comment ça marche ?"
        collapsible
        collapsed
    >
        <x-slot name="icon">
            <x-heroicon-o-information-circle class="fi-icon fi-size-md text-primary-600" style="width: 1.25rem; height: 1.25rem;" />
        </x-slot>

        <div class="grid md:grid-cols-3 gap-4" style="display: grid; gap: 1rem;">
            {{-- Étape 1 --}}
            <div class="p-4 rounded-xl transition-all" style="padding: 1rem; border-radius: 0.75rem; background-color: rgba(249, 250, 251, 0.5); border: 1px solid #f3f4f6;">
                <div class="flex items-center gap-3 mb-3" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <div class="p-2 rounded-lg" style="padding: 0.5rem; background-color: #eff6ff; border-radius: 0.5rem; border: 1px solid rgba(59, 130, 246, 0.2);">
                        <x-heroicon-o-arrow-up-tray class="fi-icon fi-size-md text-blue-600" style="width: 1.25rem; height: 1.25rem; color: #2563eb;" />
                    </div>
                    <h3 class="text-sm font-bold text-gray-900" style="font-size: 0.875rem; font-weight: 700; color: #111827; margin: 0;">
                        1. Configuration
                    </h3>
                </div>
                <p class="text-[11px] leading-relaxed text-gray-600" style="font-size: 11px; line-height: 1.625; color: #4b5563; margin: 0;">
                    Définissez le nom de votre site et uploadez une image source de haute qualité (PNG ou SVG) pour le favicon, ainsi qu'une image pour le SEO (OpenGraph).
                </p>
            </div>

            {{-- Étape 2 --}}
            <div class="p-4 rounded-xl transition-all" style="padding: 1rem; border-radius: 0.75rem; background-color: rgba(249, 250, 251, 0.5); border: 1px solid #f3f4f6;">
                <div class="flex items-center gap-3 mb-3" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <div class="p-2 rounded-lg" style="padding: 0.5rem; background-color: #f0fdf4; border-radius: 0.5rem; border: 1px solid rgba(34, 197, 94, 0.2);">
                        <x-heroicon-o-cpu-chip class="fi-icon fi-size-md text-green-600" style="width: 1.25rem; height: 1.25rem; color: #16a34a;" />
                    </div>
                    <h3 class="text-sm font-bold text-gray-900" style="font-size: 0.875rem; font-weight: 700; color: #111827; margin: 0;">
                        2. Génération
                    </h3>
                </div>
                <p class="text-[11px] leading-relaxed text-gray-600" style="font-size: 11px; line-height: 1.625; color: #4b5563; margin: 0;">
                    Cliquez sur <strong>"Enregistrer"</strong> puis sur <strong>"Générer les Favicons"</strong>. Le système créera automatiquement toutes les tailles standards et le manifest.
                </p>
            </div>

            {{-- Étape 3 --}}
            <div class="p-4 rounded-xl transition-all" style="padding: 1rem; border-radius: 0.75rem; background-color: rgba(249, 250, 251, 0.5); border: 1px solid #f3f4f6;">
                <div class="flex items-center gap-3 mb-3" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <div class="p-2 rounded-lg" style="padding: 0.5rem; background-color: #faf5ff; border-radius: 0.5rem; border: 1px solid rgba(168, 85, 247, 0.2);">
                        <x-heroicon-o-rocket-launch class="fi-icon fi-size-md text-purple-600" style="width: 1.25rem; height: 1.25rem; color: #9333ea;" />
                    </div>
                    <h3 class="text-sm font-bold text-gray-900" style="font-size: 0.875rem; font-weight: 700; color: #111827; margin: 0;">
                        3. Déploiement
                    </h3>
                </div>
                <p class="text-[11px] leading-relaxed text-gray-600" style="font-size: 11px; line-height: 1.625; color: #4b5563; margin: 0;">
                    Les fichiers sont générés dans <code>public/favicon/</code> et injectés automatiquement dans le <code>&lt;head&gt;</code> de votre site.
                </p>
            </div>
        </div>

        <div class="mt-6 p-4 rounded-xl border border-gray-200 bg-gray-900 text-gray-300 font-mono text-[10px]" style="margin-top: 1.5rem; padding: 1rem; border-radius: 0.75rem; background-color: #111827; color: #d1d5db; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace; font-size: 10px; line-height: 1.5; overflow-x: auto;">
            <div class="text-gray-500 mb-2">// Code injecté dans votre &lt;head&gt;</div>
            <div>&lt;link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png"&gt;</div>
            <div>&lt;link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png"&gt;</div>
            <div class="text-gray-500 mt-2">// iOS</div>
            <div>&lt;link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png"&gt;</div>
            <div class="text-gray-500 mt-2">// Android / PWA</div>
            <div>&lt;link rel="icon" type="image/png" sizes="192x192" href="/favicon/android-192x192.png"&gt;</div>
            <div>&lt;link rel="icon" type="image/png" sizes="512x512" href="/favicon/android-512x512.png"&gt;</div>
            <div>&lt;link rel="manifest" href="/favicon/site.webmanifest"&gt;</div>
            <div class="text-gray-500 mt-2">// SEO / OpenGraph</div>
            <div>&lt;meta property="og:image" content="..."&gt;</div>
            <div>&lt;meta property="og:site_name" content="..."&gt;</div>
        </div>
    </x-filament::section>
</div>
