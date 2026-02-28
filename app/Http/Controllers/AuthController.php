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
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ], [
            'login.required' => 'El campo usuario o correo es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $login = $request->input('login');
        $password = $request->input('password');

        // Intentar primero usando el campo como 'email', luego como 'name'
        if (Auth::attempt(['email' => $login, 'password' => $password]) || 
            Auth::attempt(['name' => $login, 'password' => $password])) {
            
            $request->session()->regenerate();
            
            $host = request()->getHost();
            $url = ($host === 'admin.eleden.site') ? '/' : '/admin';
            
            return redirect($url);
        }

        return back()->withErrors(['login' => 'Las credenciales proporcionadas son incorrectas.']);
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