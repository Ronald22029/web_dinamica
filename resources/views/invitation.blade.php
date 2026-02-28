<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $invitation->title }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Meta Tags para Redes Sociales -->
    <meta property="og:title" content="{{ $invitation->title }}">
    <meta property="og:description" content="{{ $invitation->description }}">
    @if($invitation->gallery_images && count($invitation->gallery_images) > 0)
        <meta property="og:image" content="{{ url($invitation->gallery_images[0]) }}">
    @endif
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div id="app">
        <!-- Render dinámico según el template guardado -->
        @if($invitation->template === 'boda1')
            <boda-1 :invitation='@json($invitation)'></boda-1>
        @elseif($invitation->template === 'boda2')
            <boda-2 :invitation='@json($invitation)'></boda-2>
        @else
            <!-- Fallback -->
            <boda-1 :invitation='@json($invitation)'></boda-1>
        @endif
    </div>
</body>
</html>
