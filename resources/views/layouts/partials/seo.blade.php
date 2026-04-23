@if(isset($seo_meta))
    <!-- SEO Meta Tags -->
    <title>{{ $seo_meta->title ?? config('app.name') }}</title>
    <meta name="description" content="{{ $seo_meta->description }}">
    @if($seo_meta->keywords)
        <meta name="keywords" content="{{ $seo_meta->keywords }}">
    @endif
    <meta name="robots" content="{{ $seo_meta->robots ?? 'index, follow' }}">
    @if($seo_meta->canonical_url)
        <link rel="canonical" href="{{ $seo_meta->canonical_url }}">
    @endif

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $seo_meta->og_title ?? $seo_meta->title }}">
    <meta property="og:description" content="{{ $seo_meta->og_description ?? $seo_meta->description }}">
    @if($seo_meta->og_image)
        <meta property="og:image" content="{{ asset('storage/' . $seo_meta->og_image) }}">
    @endif

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $seo_meta->og_title ?? $seo_meta->title }}">
    <meta property="twitter:description" content="{{ $seo_meta->og_description ?? $seo_meta->description }}">
    @if($seo_meta->og_image)
        <meta property="twitter:image" content="{{ asset('storage/' . $seo_meta->og_image) }}">
    @endif
@else
    <title>@yield('title', 'API Manager') - {{ config('app.name') }}</title>
@endif
