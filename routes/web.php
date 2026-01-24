<?php

use Illuminate\Support\Facades\Route;
// IMPORTANTE: Estas dos líneas son las que le dicen a Laravel dónde están tus archivos
use App\Http\Controllers\HomeController; 
use App\Http\Controllers\AdminController;

// --- Parte Pública (Lo que ve el usuario) ---
// Portada
Route::get('/', [HomeController::class, 'index'])->name('home');
// Categorías (Eventos, Tecnología, Portafolio)
Route::get('/categoria/{category}', [HomeController::class, 'index']);


// --- Parte Admin (Panel de control) ---
Route::get('/admin', [AdminController::class, 'index']);
Route::post('/admin/settings', [AdminController::class, 'updateSettings']);
Route::post('/admin/posts', [AdminController::class, 'storePost']);
Route::delete('/admin/posts/{id}', [AdminController::class, 'deletePost']);