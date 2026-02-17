<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Mostrar formulario de login
    public function showLogin() {
        return view('auth.login');
    }

    // Procesar login
    public function login(Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        
        // Si estamos en producción, la raíz '/' es el panel.
        // En local, el panel está en '/admin'.
        $host = request()->getHost();
        $url = ($host === 'admin.eleden.site') ? '/' : '/admin';
        
        return redirect($url);
    }

    return back()->withErrors(['email' => 'Credenciales incorrectas']);
}

    // Cerrar sesión
    // Cerrar sesión
public function logout(Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Si estamos en local, el login está en /admin/login
    $host = request()->getHost();
    $loginUrl = ($host === 'admin.eleden.site') ? '/login' : '/admin/login';

    return redirect($loginUrl);
}
}