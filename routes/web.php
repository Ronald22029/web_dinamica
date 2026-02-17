<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

// --------------------------------------------------------------------------
// 1. RUTAS DE AUTENTICACIÓN
// --------------------------------------------------------------------------
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --------------------------------------------------------------------------
// 2. PANEL DE ADMINISTRACIÓN (Ruta Raíz - PROTEGIDA)
// --------------------------------------------------------------------------
// Al entrar a la raíz '/', si no estás logueado te pedirá login.
// Si estás logueado, verás el Dashboard del Admin.
Route::middleware(['auth'])->group(function () {
    
    // Dashboard Principal
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

    // Guardar configuraciones (Título, Texto Hero)
    Route::post('/admin/settings', [AdminController::class, 'updateSettings']);

    // --- Gestión de Posts (CRUD) ---
    
    // Crear nuevo post
    Route::post('/admin/posts', [AdminController::class, 'storePost']);
    
    // Editar post existente (Asegúrate de tener el método 'updatePost' en AdminController)
    Route::put('/admin/posts/{id}', [AdminController::class, 'updatePost']);
    
    // Eliminar post
    Route::delete('/admin/posts/{id}', [AdminController::class, 'deletePost']);
});

// --------------------------------------------------------------------------
// 3. SITIO WEB PÚBLICO (Accesible vía "/sitio")
// --------------------------------------------------------------------------
// Ejemplo: www.tuweb.com/sitio
Route::prefix('sitio')->group(function () {
    
    // Portada del sitio web
    Route::get('/', [HomeController::class, 'index'])->name('home');
    
    // Filtrar por categoría
    Route::get('/categoria/{category}', [HomeController::class, 'index'])->name('home.category');

    // Ver un post individual
    // (Asegúrate de tener el método 'show' en HomeController)
    Route::get('/post/{id}', [HomeController::class, 'show'])->name('post.show');
});