<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- ====== SEO Básico ====== --}}
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}">
    <link rel="canonical" href="{{ $seo['url'] }}">
    <meta name="theme-color" content="#3b82f6">

    {{-- ====== Open Graph (Facebook, WhatsApp, LinkedIn) ====== --}}
    <meta property="og:type"        content="{{ $seo['type'] }}">
    <meta property="og:title"       content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:url"         content="{{ $seo['url'] }}">
    <meta property="og:site_name"   content="{{ $seo['site_name'] }}">
    @if($seo['image'])
    <meta property="og:image"       content="{{ $seo['image'] }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    @endif

    {{-- ====== Twitter / X Card ====== --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $seo['title'] }}">
    <meta name="twitter:description" content="{{ $seo['description'] }}">
    @if($seo['image'])
    <meta name="twitter:image"       content="{{ $seo['image'] }}">
    @endif

    {{-- ====== Structured Data (JSON-LD) ====== --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $seo['site_name'],
        'url' => $seo['url'],
        'description' => $seo['description'],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <home-page :initial-data="{{ json_encode($data) }}"></home-page>
    </div>
</body>
</html>