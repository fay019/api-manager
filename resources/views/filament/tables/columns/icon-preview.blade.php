<div class="flex items-center justify-center px-4 py-2">
    @php
        $record = $getRecord();
    @endphp

    @if($record->type === 'svg')
        <div style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; {{ $record->color ? 'color: '.$record->color.';' : 'color: currentColor;' }}" class="text-gray-500">
            @if(str_contains($record->source, '<svg'))
                @php
                    $svg = $record->source;
                    if ($record->color) {
                        // Injection de fill ou stroke selon ce qui est présent
                        if (str_contains($svg, 'stroke="currentColor"')) {
                            $svg = str_replace('stroke="currentColor"', 'stroke="'.$record->color.'"', $svg);
                        }
                        if (str_contains($svg, 'fill="currentColor"')) {
                            $svg = str_replace('fill="currentColor"', 'fill="'.$record->color.'"', $svg);
                        }
                    }
                @endphp
                {!! preg_replace('/<svg\s+([^>]*)>/', '<svg $1 style="width: 32px; height: 32px; display: block;">', $svg) !!}
            @else
                <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $record->color ?? 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="{{ $record->color ?? 'currentColor' }}" style="width: 32px; height: 32px; display: block;">
                    {!! $record->source !!}
                </svg>
            @endif
        </div>
    @elseif($record->type === 'cdn')
        <img src="{{ $record->source }}" style="width: 32px; height: 32px; object-fit: contain; display: block;" alt="{{ $record->name }}" />
    @endif
</div>
