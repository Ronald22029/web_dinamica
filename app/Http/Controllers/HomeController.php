<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiteSetting;
use App\Models\Post;

class HomeController extends Controller
{
    public function index($category = null)
    {
        // 1. Configuración General
        $setting = SiteSetting::first();
        $siteTitle = $setting ? $setting->title : 'Ronald.Dev';
        $heroText = $setting ? $setting->hero_text : 'Bienvenido a mi portafolio.';

        // 2. Filtros y Metadatos
        $query = Post::query();
        $pageTitle = $siteTitle;

        if ($category) {
            $query->where('category', $category);
            $pageTitle = ucfirst($category) . " - " . $siteTitle;
            $heroText = "Explora los últimos artículos sobre " . ucfirst($category);
        }

        // 3. Obtener posts normales y Carrusel
        $posts = $query->latest()->get();
        // Intentamos obtener posts marcados para carrusel, si la columna existe
        // Si no has corrido la migración del paso anterior, esto podría fallar, 
        // así que por seguridad traemos los últimos 5 si falla.
        try {
            $carouselPosts = Post::where('is_carousel', true)->latest()->take(5)->get();
        } catch (\Exception $e) {
            $carouselPosts = [];
        }

        // 4. Empaquetar datos para Vue
        $data = [
            'meta_title' => $pageTitle,
            'meta_description' => "Lo mejor de $pageTitle",
            'hero_title' => $category ? ucfirst($category) : $siteTitle,
            'hero_text' => $heroText,
            'current_category' => $category ?? 'home',
            'posts' => $posts,
            'carousel_posts' => $carouselPosts
        ];

        return view('welcome', compact('data'));
    }
}