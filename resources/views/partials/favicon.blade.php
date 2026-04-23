@php
    $siteSettings = \App\Models\SiteSetting::first();
    $ogImage = $siteSettings?->og_image ? \Illuminate\Support\Facades\Storage::url($siteSettings->og_image) : null;
@endphp

@if(File::exists(public_path('favicon/favicon-32x32.png')))
    @php
        $v = File::lastModified(public_path('favicon/favicon-32x32.png'));
    @endphp
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png?v={{ $v }}">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png?v={{ $v }}">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png?v={{ $v }}">
    <link rel="icon" type="image/png" sizes="192x192" href="/favicon/android-192x192.png?v={{ $v }}">
    <link rel="icon" type="image/png" sizes="512x512" href="/favicon/android-512x512.png?v={{ $v }}">
    <link rel="manifest" href="/favicon/site.webmanifest?v={{ $v }}">
@endif

@if($ogImage)
    <meta property="og:image" content="{{ $ogImage }}?v={{ File::exists(public_path('favicon/favicon-32x32.png')) ? File::lastModified(public_path('favicon/favicon-32x32.png')) : time() }}">
@endif

@if($siteSettings?->site_name)
    <meta property="og:site_name" content="{{ $siteSettings->site_name }}">
@endif
