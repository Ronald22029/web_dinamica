<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController; // <--- Importar AuthController

// --- Parte Pública ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/categoria/{category}', [HomeController::class, 'index']);

// --- Rutas de Autenticación ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Parte Admin (PROTEGIDA) ---
// Todo lo que esté dentro de este grupo requiere login
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
    Route::post('/admin/settings', [AdminController::class, 'updateSettings']);
    Route::post('/admin/posts', [AdminController::class, 'storePost']);
    Route::delete('/admin/posts/{id}', [AdminController::class, 'deletePost']);
});