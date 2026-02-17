<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['meta_title'] }}</title>
    <meta name="description" content="{{ $data['meta_description'] }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <post-page :initial-data="{{ json_encode($data) }}"></post-page>
    </div>
</body>
</html>