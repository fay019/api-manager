<div style="max-height: fit-content; overflow-y: auto; padding: 0; background: linear-gradient(135deg, #f8f9fa 0%, #f3f4f6 100%);">
    @if($versions->isEmpty())
        <div style="text-align: center; padding: 3rem; color: #6b7280;">
            <p style="font-size: 0.875rem; margin: 0;">No version history available</p>
        </div>
    @else
        <div style="padding: 1rem 1.5rem; position: relative;">
            @foreach($versions as $index => $version)
                @php
                    $isCurrentVersion = $index === 0;
                    $nextVersion = $versions->get($index + 1);
                    $diff = $nextVersion ? $page->getVersionDiff($nextVersion->version, $version->version) : [];
                @endphp

                <div style="position: relative; margin-bottom: 1rem; display: flex; gap: 1rem;">
                    <!-- Timeline line and dot -->
                    <div style="display: flex; flex-direction: column; align-items: center; min-width: 2.8rem;">
                        <!-- Dot -->
                        <div style="
                            width: 2.5rem;
                            height: 2.5rem;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-weight: bold;
                            font-size: 0.75rem;
                            color: white;
                            background: {{ $isCurrentVersion ? 'linear-gradient(135deg, #3b82f6 0%, #1e40af 100%)' : '#d1d5db' }};
                            box-shadow: {{ $isCurrentVersion ? '0 4px 12px rgba(59, 130, 246, 0.3)' : 'none' }};
                            position: relative;
                        ">
                            v{{ $version->version }}
                        </div>

                        <!-- Connecting line to next version -->
                        @if(!$loop->last)
                            <div style="
                                width: 2px;
                                height: 2.5rem;
                                background: linear-gradient(to bottom, {{ $isCurrentVersion ? '#3b82f6' : '#d1d5db' }}, #e5e7eb);
                                margin: 0.5rem 0;
                            "></div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div style="flex: 1; padding-top: 0.25rem;">
                        <!-- Main card -->
                        <div style="
                            background: white;
                            border-radius: 0.625rem;
                            padding: 0.75rem;
                            border: 2px solid {{ $isCurrentVersion ? '#3b82f6' : '#e5e7eb' }};
                            box-shadow: {{ $isCurrentVersion ? '0 2px 8px rgba(59, 130, 246, 0.15)' : '0 1px 3px rgba(0, 0, 0, 0.05)' }};
                            transition: all 0.2s ease;
                        ">
                            <!-- Header row -->
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; gap: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    @if($isCurrentVersion)
                                        <span style="
                                            display: inline-flex;
                                            align-items: center;
                                            gap: 0.5rem;
                                            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
                                            color: white;
                                            padding: 0.25rem 0.625rem;
                                            border-radius: 9999px;
                                            font-size: 0.7rem;
                                            font-weight: 600;
                                        ">
                                            <span>●</span> CURRENT
                                        </span>
                                    @else
                                        <span style="
                                            display: inline-flex;
                                            align-items: center;
                                            gap: 0.5rem;
                                            background: #f3f4f6;
                                            color: #6b7280;
                                            padding: 0.25rem 0.625rem;
                                            border-radius: 9999px;
                                            font-size: 0.7rem;
                                            font-weight: 600;
                                        ">
                                            <span style="opacity: 0.5;">●</span> v{{ $version->version }}
                                        </span>
                                    @endif
                                </div>
                                <span style="font-size: 0.7rem; color: #9ca3af; white-space: nowrap;">
                                    {{ $version->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <!-- Creator info -->
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem; font-size: 0.8rem;">
                                <span style="color: #6b7280;">👤</span>
                                <span style="color: #6b7280;">
                                    <strong style="color: #374151;">
                                        @if($version->creator)
                                            {{ $version->creator->name ?? 'System' }}
                                        @else
                                            System
                                        @endif
                                    </strong>
                                </span>
                            </div>

                            <!-- Timestamp -->
                            <div style="font-size: 0.7rem; color: #9ca3af; margin-bottom: 0.5rem;">
                                📅 {{ $version->created_at->format('d/m/Y H:i:s') }}
                            </div>

                            <!-- Diff section -->
                            @if($diff && count($diff) > 0)
                                <details style="cursor: pointer; margin-top: 0.5rem; border-top: 1px solid #e5e7eb; padding-top: 0.5rem;">
                                    <summary style="
                                        list-style: none;
                                        color: #3b82f6;
                                        font-weight: 600;
                                        font-size: 0.8rem;
                                        cursor: pointer;
                                        display: flex;
                                        align-items: center;
                                        gap: 0.5rem;
                                        user-select: none;
                                    ">
                                        <span style="display: inline-block; transition: transform 0.2s;">📝</span>
                                        @if($isCurrentVersion)
                                            Latest changes
                                        @else
                                            Changes ({{ count($diff) }} field{{ count($diff) !== 1 ? 's' : '' }})
                                        @endif
                                    </summary>

                                    <div style="margin-top: 0.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
                                        @foreach($diff as $field => $change)
                                            <div style="
                                                background: #f9fafb;
                                                border-left: 3px solid #fbbf24;
                                                padding: 0.5rem;
                                                border-radius: 0.375rem;
                                                font-size: 0.8rem;
                                            ">
                                                <div style="font-weight: 600; color: #374151; margin-bottom: 0.35rem;">
                                                    {{ ucfirst(str_replace('_', ' ', $field)) }}
                                                </div>

                                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                                                    <!-- Old value -->
                                                    <div style="
                                                        background: #fee2e2;
                                                        border: 1px solid #fecaca;
                                                        border-radius: 0.375rem;
                                                        padding: 0.35rem;
                                                        font-family: 'Courier New', monospace;
                                                        font-size: 0.7rem;
                                                        color: #991b1b;
                                                        word-break: break-word;
                                                    ">
                                                        <div style="opacity: 0.7; margin-bottom: 0.15rem; font-weight: 600; font-size: 0.65rem;">Before:</div>
                                                        <div style="text-decoration: line-through; opacity: 0.8;">
                                                            {{ is_array($change['old']) ? json_encode($change['old'], JSON_UNESCAPED_SLASHES) : ($change['old'] ?? '—') }}
                                                        </div>
                                                    </div>

                                                    <!-- New value -->
                                                    <div style="
                                                        background: #dcfce7;
                                                        border: 1px solid #bbf7d0;
                                                        border-radius: 0.375rem;
                                                        padding: 0.35rem;
                                                        font-family: 'Courier New', monospace;
                                                        font-size: 0.7rem;
                                                        color: #15803d;
                                                        word-break: break-word;
                                                    ">
                                                        <div style="opacity: 0.7; margin-bottom: 0.15rem; font-weight: 600; font-size: 0.65rem;">After:</div>
                                                        <div>
                                                            {{ is_array($change['new']) ? json_encode($change['new'], JSON_UNESCAPED_SLASHES) : ($change['new'] ?? '—') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </details>
                            @elseif(!$isCurrentVersion)
                                <div style="
                                    font-size: 0.7rem;
                                    color: #9ca3af;
                                    font-style: italic;
                                    border-top: 1px solid #e5e7eb;
                                    padding-top: 0.5rem;
                                    margin-top: 0.5rem;
                                ">
                                    No changes from next version
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    details > summary {
        list-style: none;
    }

    details > summary::-webkit-details-marker {
        display: none;
    }

    details[open] > summary span {
        transform: rotate(90deg);
    }
</style>
