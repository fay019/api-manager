@props(['name', 'class' => 'w-5 h-5'])

@php
    $icon = App\Models\ExternalIcon::where('slug', $name)->where('is_active', true)->first();
    $svg = $icon?->getSvg() ?? $icon?->getUrl();
    $color = $icon?->color;
@endphp

@if($svg)
    @php
        $isFullSvg = str_contains($svg, '<svg');
        $isUrl = filter_var($svg, FILTER_VALIDATE_URL) || str_starts_with($svg, 'http');

        if ($color && !$isUrl) {
            if (str_contains($svg, 'stroke="currentColor"')) {
                $svg = str_replace('stroke="currentColor"', 'stroke="'.$color.'"', $svg);
            }
            if (str_contains($svg, 'fill="currentColor"')) {
                $svg = str_replace('fill="currentColor"', 'fill="'.$color.'"', $svg);
            }
        }

        if ($isUrl) {
            $svgWithClasses = '<img src="'.$svg.'" '.$attributes->merge(['class' => $class])->toHtml().' style="'.($color ? 'filter: drop-shadow(0 0 0 '.$color.');' : '').'" />';
        } elseif ($isFullSvg) {
            $svgWithClasses = preg_replace('/<svg\s+([^>]*)>/', '<svg $1 '.$attributes->merge(['class' => $class])->toHtml().($color ? ' style="color: '.$color.'"' : '').'>', $svg);
        } else {
            // Si c'est juste un fragment, on l'entoure d'un SVG avec les attributs par défaut
            $svgWithClasses = '<svg '.$attributes->merge(['class' => $class])->toHtml().' fill="'.($color ?? 'none').'" viewBox="0 0 24 24" stroke-width="1.5" stroke="'.($color ?? 'currentColor').'">'.$svg.'</svg>';
        }
    @endphp
    {!! $svgWithClasses !!}
@else
    <span class="inline-block {{ $class }} bg-gray-200 dark:bg-gray-700 rounded" title="Icon {{ $name }} not found"></span>
@endif
