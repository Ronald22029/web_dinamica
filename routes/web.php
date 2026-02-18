<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

$host = request()->getHost();
$isProduction = ($host === 'eleden.site' || $host === 'admin.eleden.site');

if ($isProduction) {
    /* --- PRODUCCIÓN: admin.eleden.site --- */
    Route::domain('admin.eleden.site')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

        Route::middleware(['auth'])->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('admin.index');
            Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
            Route::post('/posts', [AdminController::class, 'storePost']);
            Route::delete('/posts/{id}', [AdminController::class, 'deletePost']);
            Route::put('/posts/{id}', [AdminController::class, 'updatePost']);
        });
    });

    /* --- PRODUCCIÓN: eleden.site --- */
    Route::domain('eleden.site')->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('/categoria/{category}', [HomeController::class, 'index']);
        Route::get('/post/{id}', [HomeController::class, 'show']);
    });

} else {
    /* --- LOCAL: localhost o 127.0.0.1 --- */
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/categoria/{category}', [HomeController::class, 'index']);
    Route::get('/post/{id}', [HomeController::class, 'show']);
    
    Route::prefix('admin')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        
        Route::middleware(['auth'])->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('admin.index');
            Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
            Route::post('/posts', [AdminController::class, 'storePost']);
            Route::delete('/posts/{id}', [AdminController::class, 'deletePost']);
            Route::put('/posts/{id}', [AdminController::class, 'updatePost']);
        });
    });
}

// Rutas compartidas de Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Fallback inteligente: Solo redirige si no es un subdominio de admin
Route::fallback(function () use ($isProduction, $host) {
    if ($isProduction && $host === 'admin.eleden.site') {
        return redirect()->route('login');
    }
    return redirect()->route('home');
});