<style>
    :root {
        --mas-bg: white;
        --mas-bg-header: #f9fafb;
        --mas-text-dark: #111827;
        --mas-text-muted: #6b7280;
        --mas-border: #d1d5db;
        --mas-border-light: #e5e7eb;
    }

    html.dark {
        --mas-bg: #1f2937;
        --mas-bg-header: #111827;
        --mas-text-dark: #f3f4f6;
        --mas-text-muted: #d1d5db;
        --mas-border: #374151;
        --mas-border-light: #374151;
    }
</style>

<x-filament-panels::page>
    <!-- Home Page Settings -->
    <div style="margin-bottom: 2rem;">
        {{ $this->form }}
    </div>

    <!-- Documentation Table -->
    <div style="background: var(--mas-bg); border: 1px solid var(--mas-border); border-radius: 0.5rem; overflow: hidden; margin-bottom: 2rem;">
        <!-- Table Header -->
        <div style="background: var(--mas-bg-header); padding: 1rem; border-bottom: 1px solid var(--mas-border-light);">
            <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--mas-text-dark); margin: 0 0 0.25rem 0;">Gestion de la Documentation</h3>
            <p style="font-size: 0.875rem; color: var(--mas-text-muted); margin: 0;">Gérez vos fichiers de documentation .md</p>
        </div>

        @php
            $docs = \App\Models\DocumentationSetting::all();
        @endphp

        @if($docs->isEmpty())
            <!-- Empty State -->
            <div style="padding: 4rem 2rem; text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📚</div>
                <h4 style="font-size: 1.125rem; font-weight: 600; color: var(--mas-text-dark); margin: 0 0 0.5rem 0;">Aucune Documentation Trouvée</h4>
                <p style="font-size: 0.875rem; color: var(--mas-text-muted); margin: 0 0 1.5rem 0;">
                    Cliquez sur le bouton "Scan Documentation" pour découvrir les fichiers .md dans votre projet.
                </p>
                <p style="font-size: 0.75rem; color: var(--mas-text-muted); margin: 0;">
                    📍 Emplacements scannés : <code style="background: var(--mas-bg-header); padding: 0.25rem 0.5rem; border-radius: 0.25rem;">README.md</code>,
                    <code style="background: var(--mas-bg-header); padding: 0.25rem 0.5rem; border-radius: 0.25rem;">DEPLOYMENT.md</code>,
                    <code style="background: var(--mas-bg-header); padding: 0.25rem 0.5rem; border-radius: 0.25rem;">/docs/**/*.md</code>
                </p>
            </div>
        @else
            <!-- Table Content -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--mas-border-light); background: var(--mas-bg-header);">
                            <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--mas-text-dark); font-size: 0.875rem;">Documentation</th>
                            <th style="text-align: center; padding: 1rem; font-weight: 600; color: var(--mas-text-dark); font-size: 0.875rem;">Icône</th>
                            <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--mas-text-dark); font-size: 0.875rem;">Chemin du fichier</th>
                            <th style="text-align: center; padding: 1rem; font-weight: 600; color: var(--mas-text-dark); font-size: 0.875rem;">Statut</th>
                            <th style="text-align: center; padding: 1rem; font-weight: 600; color: var(--mas-text-dark); font-size: 0.875rem;">Visible</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($docs as $index => $doc)
                            @php
                                $metadata = \App\Services\DocumentationScanner::getMetadata($doc->doc_name);
                                $filePath = base_path($doc->path);
                                $fileExists = file_exists($filePath);
                                $fieldName = 'doc_' . $doc->doc_name . '_visible';
                                $isLast = $index === $docs->count() - 1;
                            @endphp

                            <tr style="border-bottom: {{ $isLast ? 'none' : '1px solid var(--mas-border-light)' }}; background: {{ $index % 2 === 0 ? 'var(--mas-bg)' : 'var(--mas-bg-header)' }};">
                                <!-- Documentation Name -->
                                <td style="padding: 1rem; vertical-align: middle;">
                                    <div>
                                        <p style="font-weight: 500; color: var(--mas-text-dark); margin: 0;">
                                            {{ $metadata['label'] ?? ucfirst($doc->doc_name) }}
                                        </p>
                                        <p style="font-size: 0.75rem; color: var(--mas-text-muted); margin: 0.25rem 0 0 0;">
                                            {{ $metadata['description'] ?? '' }}
                                        </p>
                                    </div>
                                </td>

                                <!-- Icon Selector -->
                                <td style="padding: 1rem; vertical-align: middle; text-align: center;">
                                    @php
                                        $curatedIcons = \App\Models\DocumentationSetting::getCuratedIcons();
                                    @endphp
                                    <select
                                        id="icon-select-{{ $doc->doc_name }}"
                                        onchange="@this.call('updateDocumentationIcon', '{{ $doc->doc_name }}', this.value)"
                                        style="padding: 0.5rem; border: 1px solid var(--mas-border); border-radius: 0.375rem; background-color: var(--mas-bg); color: var(--mas-text-dark); cursor: pointer; font-size: 1rem; min-width: 60px;"
                                    >
                                        @foreach ($curatedIcons as $icon => $label)
                                            <option value="{{ $icon }}" @if($doc->icon === $icon) selected @endif>
                                                {{ $icon }} {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <!-- File Path -->
                                <td style="padding: 1rem; vertical-align: middle;">
                                    <code style="font-size: 0.75rem; background: var(--mas-bg-header); padding: 0.25rem 0.5rem; border-radius: 0.25rem; color: var(--mas-text-muted);">
                                        {{ $doc->path }}
                                    </code>
                                </td>

                                <!-- Status -->
                                <td style="padding: 1rem; vertical-align: middle; text-align: center;">
                                    @if ($fileExists)
                                        <span style="display: inline-block; font-size: 0.75rem; color: #16a34a; background: #f0fdf4; padding: 0.35rem 0.65rem; border-radius: 0.25rem; white-space: nowrap; border: 1px solid #dcfce7;">
                                            ✓ Existe
                                        </span>
                                    @else
                                        <span style="display: inline-block; font-size: 0.75rem; color: #dc2626; background: #fef2f2; padding: 0.35rem 0.65rem; border-radius: 0.25rem; white-space: nowrap; border: 1px solid #fee2e2;">
                                            ✗ Manquant
                                        </span>
                                    @endif
                                </td>

                                <!-- Toggle Visible -->
                                <td style="padding: 1rem; vertical-align: middle; text-align: center;">
                                    <input
                                        id="doc-visibility-{{ $doc->doc_name }}"
                                        type="checkbox"
                                        onchange="@this.call('toggleDocVisibility', '{{ $doc->doc_name }}', this.checked)"
                                        style="width: 1rem; height: 1rem; cursor: pointer; accent-color: #3b82f6;"
                                        @if ($this->data[$fieldName] ?? false) checked @endif
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <div style="margin-bottom: 2rem; display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
        <div style="font-size: 0.75rem; color: var(--mas-text-muted); background: var(--mas-bg-header); padding: 0.25rem 0.5rem; border-radius: 0.25rem; border: 1px solid var(--mas-border-light);">
            ENV: {{ app()->environment() }} | DEBUG: {{ config('app.debug') ? 'ON' : 'OFF' }} | RESET: {{ config('installation.wizard.security.allow_production_reset') ? 'ALLOWED' : 'BLOCKED' }}
        </div>

        <button
            id="scan-docs-btn"
            onclick="@this.call('scanDocumentation')"
            type="button"
            class="fi-color fi-color-info fi-bg-color-600 hover:fi-bg-color-500 dark:fi-bg-color-600 dark:hover:fi-bg-color-500 fi-text-color-0 hover:fi-text-color-0 dark:fi-text-color-0 dark:hover:fi-text-color-0 fi-btn fi-size-md fi-ac-btn-action"
        >
            <svg class="fi-icon fi-size-md" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Scanner la Documentation
        </button>

        <button
            id="cleanup-btn"
            onclick="@this.call('cleanupMissing')"
            type="button"
            class="fi-color fi-color-warning fi-bg-color-600 hover:fi-bg-color-500 dark:fi-bg-color-600 dark:hover:fi-bg-color-500 fi-text-color-0 hover:fi-text-color-0 dark:fi-text-color-0 dark:hover:fi-text-color-0 fi-btn fi-size-md fi-ac-btn-action"
        >
            <svg class="fi-icon fi-size-md" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            Nettoyer les Fichiers Manquants
        </button>

        @if(!app()->environment('production') || config('installation.wizard.security.allow_production_reset', false))
            <div style="margin-left: auto;">
                <button
                    id="reset-btn"
                    onclick="if(confirm('ATTENTION: Cette action est DESTRUCTIVE. Elle effacera la base de données (si SQLite), supprimera le verrouillage de l\'installation et vous redirigera vers l\'installateur. Êtes-vous sûr?')) { @this.call('resetApplication') }"
                    type="button"
                    class="fi-color fi-color-danger fi-bg-color-600 hover:fi-bg-color-500 dark:fi-bg-color-600 dark:hover:fi-bg-color-500 fi-text-color-0 hover:fi-text-color-0 dark:fi-text-color-0 dark:hover:fi-text-color-0 fi-btn fi-size-md fi-ac-btn-action"
                >
                    <svg class="fi-icon fi-size-md" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"></path>
                    </svg>
                    Réinitialiser l'application
                </button>
            </div>
        @endif
    </div>
</x-filament-panels::page>
