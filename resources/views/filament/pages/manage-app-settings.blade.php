<x-filament-panels::page>
    <!-- Home Page Settings -->
    <div style="margin-bottom: 2rem;">
        {{ $this->form }}
    </div>

    <!-- Documentation Table -->
    <div style="background: white; border: 1px solid #d1d5db; border-radius: 0.5rem; overflow: hidden; margin-bottom: 2rem;">
        <!-- Table Header -->
        <div style="background: #f9fafb; padding: 1rem; border-bottom: 1px solid #e5e7eb;">
            <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin: 0 0 0.25rem 0;">Documentation Management</h3>
            <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">View and manage your documentation files</p>
        </div>

        @php
            $docs = \App\Models\DocumentationSetting::all();
        @endphp

        @if($docs->isEmpty())
            <!-- Empty State -->
            <div style="padding: 4rem 2rem; text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📚</div>
                <h4 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin: 0 0 0.5rem 0;">No Documentation Found</h4>
                <p style="font-size: 0.875rem; color: #6b7280; margin: 0 0 1.5rem 0;">
                    It looks like you don't have any documentation files yet. Click the "Scan Documentation" button to discover .md files in your project.
                </p>
                <p style="font-size: 0.75rem; color: #9ca3af; margin: 0;">
                    📍 Scanned locations: <code style="background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">README.md</code>,
                    <code style="background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">DEPLOYMENT.md</code>,
                    <code style="background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">/docs/*.md</code>
                </p>
            </div>
        @else
            <!-- Table Content -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <th style="text-align: left; padding: 1rem; font-weight: 600; color: #111827; font-size: 0.875rem;">Documentation</th>
                            <th style="text-align: left; padding: 1rem; font-weight: 600; color: #111827; font-size: 0.875rem;">File Path</th>
                            <th style="text-align: center; padding: 1rem; font-weight: 600; color: #111827; font-size: 0.875rem;">Status</th>
                            <th style="text-align: center; padding: 1rem; font-weight: 600; color: #111827; font-size: 0.875rem;">Visible</th>
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

                            <tr style="border-bottom: {{ $isLast ? 'none' : '1px solid #e5e7eb' }}; background: {{ $index % 2 === 0 ? 'white' : '#f9fafb' }};">
                                <!-- Documentation Name -->
                                <td style="padding: 1rem; vertical-align: middle;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <span style="font-size: 1.5rem;">{{ $metadata['icon'] ?? '📄' }}</span>
                                        <div>
                                            <p style="font-weight: 500; color: #111827; margin: 0;">
                                                {{ $metadata['label'] ?? ucfirst($doc->doc_name) }}
                                            </p>
                                            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">
                                                {{ $metadata['description'] ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- File Path -->
                                <td style="padding: 1rem; vertical-align: middle;">
                                    <code style="font-size: 0.75rem; background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 0.25rem; color: #374151;">
                                        {{ $doc->path }}
                                    </code>
                                </td>

                                <!-- Status -->
                                <td style="padding: 1rem; vertical-align: middle; text-align: center;">
                                    @if ($fileExists)
                                        <span style="display: inline-block; font-size: 0.75rem; color: #16a34a; background: #f0fdf4; padding: 0.35rem 0.65rem; border-radius: 0.25rem; white-space: nowrap; border: 1px solid #dcfce7;">
                                            ✓ Exists
                                        </span>
                                    @else
                                        <span style="display: inline-block; font-size: 0.75rem; color: #dc2626; background: #fef2f2; padding: 0.35rem 0.65rem; border-radius: 0.25rem; white-space: nowrap; border: 1px solid #fee2e2;">
                                            ✗ Missing
                                        </span>
                                    @endif
                                </td>

                                <!-- Toggle Visible -->
                                <td style="padding: 1rem; vertical-align: middle; text-align: center;">
                                    <input
                                        type="checkbox"
                                        wire:key="{{ $fieldName }}"
                                        wire:change="toggleDocVisibility('{{ $doc->doc_name }}', $event.target.checked)"
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
    <div style="margin-bottom: 2rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <x-filament::button
            wire:click="scanDocumentation"
            color="info"
            icon="heroicon-m-arrow-path"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50 cursor-not-allowed"
        >
            <span wire:loading.remove>Scan Documentation</span>
            <span wire:loading>Scanning...</span>
        </x-filament::button>

        <x-filament::button
            wire:click="cleanupMissing"
            color="warning"
            icon="heroicon-m-trash"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50 cursor-not-allowed"
        >
            <span wire:loading.remove>Clean Up Missing Files</span>
            <span wire:loading>Cleaning...</span>
        </x-filament::button>
    </div>
</x-filament-panels::page>
