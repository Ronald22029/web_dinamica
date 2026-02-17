<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Ronald.Dev</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        
        * { box-sizing: border-box; } /* Evita que los inputs se salgan del borde */

        body {
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        .aurora-bg {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 50% 50%, rgba(59, 130, 246, 0.3), transparent 70%),
                        radial-gradient(circle at 80% 20%, rgba(139, 92, 246, 0.3), transparent 70%);
            z-index: -1;
            filter: blur(60px);
        }

        .login-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            padding: 40px;
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            width: 100%;
            max-width: 420px;
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.7);
            text-align: center;
            color: white;
            margin: 20px;
        }

        h1 { margin: 0 0 10px; font-weight: 800; font-size: 2rem; letter-spacing: -1.5px; background: linear-gradient(to right, #fff, #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        p { color: #94a3b8; margin-bottom: 35px; font-size: 0.95rem; }

        .input-group { margin-bottom: 22px; text-align: left; }
        label { display: block; margin-bottom: 10px; font-size: 0.85rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
        
        input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.05);
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        input:focus {
            outline: none;
            border-color: #3b82f6;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }

        button {
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            border: none;
            background: #3b82f6;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.4);
        }
        
        button:hover { background: #2563eb; transform: translateY(-2px); box-shadow: 0 15px 25px -5px rgba(59, 130, 246, 0.5); }

        .error {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 0.85rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
    </style>
</head>
<body>
    <div class="aurora-bg"></div>
    
    <div class="login-card">
        <h1>Ronald.Dev</h1>
        <p>Panel Administrativo</p>

        @if ($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf
            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="admin@ronald.dev" required value="{{ old('email') }}">
            </div>

            <div class="input-group">
                <label>Contraseña</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>