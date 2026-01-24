<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Ronald.Dev</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0f172a;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Fondo Aurora Animado */
        .aurora-bg {
            position: absolute;
            top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle at 50% 50%, rgba(59, 130, 246, 0.4), transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(139, 92, 246, 0.4), transparent 40%);
            animation: aurora 10s infinite alternate;
            z-index: 0;
        }
        @keyframes aurora { from { transform: rotate(0deg); } to { transform: rotate(10deg); } }

        /* Tarjeta Glass */
        .login-card {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            padding: 40px;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 100%;
            max-width: 400px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            text-align: center;
            color: white;
        }

        h1 { margin-bottom: 10px; font-weight: 800; letter-spacing: -1px; }
        p { color: #94a3b8; margin-bottom: 30px; font-size: 0.9rem; }

        .input-group { margin-bottom: 20px; text-align: left; }
        label { display: block; margin-bottom: 8px; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; }
        input {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(0, 0, 0, 0.2);
            color: white;
            font-family: inherit;
            box-sizing: border-box; /* Importante para que no se salga */
            transition: 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #3b82f6;
            background: rgba(0, 0, 0, 0.4);
        }

        button {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            border: none;
            background: #3b82f6;
            color: white;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        button:hover { background: #2563eb; transform: translateY(-2px); }

        .error {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
    </style>
</head>
<body>
    <div class="aurora-bg"></div>
    
    <div class="login-card">
        <h1>Ronald.Dev</h1>
        <p>Acceso al Panel Administrativo</p>

        @if ($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf
            <div class="input-group">
                <label>Correo Electrónico</label>
                <input type="email" name="email" placeholder="admin@ronald.dev" required>
            </div>

            <div class="input-group">
                <label>Contraseña</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit">Ingresar al Sistema</button>
        </form>
    </div>
</body>
</html>