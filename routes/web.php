<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

$host = request()->getHost();

// Si estamos en producción (eleden.site), usamos subdominios.
// Si estamos en local (localhost o 127.0.0.1), usamos prefijos simples.
$isProduction = ($host === 'eleden.site' || $host === 'admin.eleden.site');

if ($isProduction) {
    /* --- PRODUCCIÓN: admin.eleden.site --- */
    Route::domain('admin.eleden.site')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        
        Route::middleware(['auth'])->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('admin.index');
            Route::post('/settings', [AdminController::class, 'updateSettings']);
            Route::post('/posts', [AdminController::class, 'storePost']);
            Route::delete('/posts/{id}', [AdminController::class, 'deletePost']);
            Route::put('/posts/{id}', [AdminController::class, 'storePost']);
        });

        Route::get('/', function () {
            return redirect()->route('login');
        })->middleware('guest');
    });

    /* --- PRODUCCIÓN: eleden.site --- */
    Route::domain('eleden.site')->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('/categoria/{category}', [HomeController::class, 'index']);
    });

} else {
    /* --- LOCAL: localhost:8000 --- */
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/categoria/{category}', [HomeController::class, 'index']);

    Route::prefix('admin')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        
        Route::middleware(['auth'])->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('admin.index');
            Route::post('/settings', [AdminController::class, 'updateSettings']);
            Route::post('/posts', [AdminController::class, 'storePost']);
            Route::delete('/posts/{id}', [AdminController::class, 'deletePost']);
            Route::put('/posts/{id}', [AdminController::class, 'storePost']);
        });
    });
}

// Rutas compartidas (siempre iguales)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::fallback(function () {
    return redirect()->route('home');
});