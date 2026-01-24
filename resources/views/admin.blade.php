<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background-color: #f3f4f6;">
    <div id="app">
        <admin-panel :initial-data="{{ json_encode($setting) }}"></admin-panel>
    </div>
</body>
</html>