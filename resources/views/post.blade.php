<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- ====== SEO Básico ====== --}}
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $seo['url'] }}">
    <meta name="theme-color" content="#3b82f6">

    {{-- ====== Favicon ====== --}}
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/logo.jpg">

    {{-- ====== Open Graph (Facebook, WhatsApp, LinkedIn) ====== --}}
    <meta property="og:type"        content="{{ $seo['type'] }}">
    <meta property="og:title"       content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:url"         content="{{ $seo['url'] }}">
    <meta property="og:site_name"   content="{{ $seo['site_name'] }}">
    <meta property="og:locale"      content="es_ES">
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

    {{-- ====== Structured Data (JSON-LD) — Article ====== --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $data['post']['title'] ?? $seo['title'],
        'description' => $seo['description'],
        'image' => $seo['image'] ?? '',
        'url' => $seo['url'],
        'datePublished' => $data['post']['created_at'] ?? now()->toIso8601String(),
        'dateModified' => $data['post']['updated_at'] ?? now()->toIso8601String(),
        'author' => [
            '@type' => 'Organization',
            'name' => $seo['site_name'],
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => $seo['site_name'],
            'logo' => [
                '@type' => 'ImageObject',
                'url' => url('/images/logo.jpg'),
            ],
        ],
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => $seo['url'],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    {{-- ====== BreadcrumbList (JSON-LD) ====== --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Inicio',
                'item' => url('/'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => ucfirst($data['post']['category'] ?? ''),
                'item' => url('/categoria/' . ($data['post']['category'] ?? '')),
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $data['post']['title'] ?? '',
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <post-page :initial-data="{{ json_encode($data) }}"></post-page>
    </div>
</body>
</html>