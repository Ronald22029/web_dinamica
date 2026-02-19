<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiteSetting;
use App\Models\Post;

class HomeController extends Controller
{
    /**
     * Genera la URL absoluta de una imagen (para OG tags).
     */
    private function absoluteImageUrl(?string $image): ?string
    {
        if (!$image) {
            return null;
        }
        // Si ya es una URL absoluta, retornarla tal cual
        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }
        // Si es relativa, convertirla a absoluta
        return url($image);
    }

    public function index($category = null)
    {
        // 1. Configuración General
        $setting = SiteSetting::first();
        $siteTitle = $setting ? $setting->title : 'ELEDEN';
        $heroText = $setting ? $setting->hero_text : 'Bienvenido a mi portafolio.';

        // 2. Filtros y Metadatos
        $query = Post::query();
        $pageTitle = $siteTitle;
        $metaDescription = "Lo mejor de $siteTitle";

        if ($category) {
            $query->where('category', $category);
            $pageTitle = ucfirst($category) . " - " . $siteTitle;
            $heroText = "Explora los últimos artículos sobre " . ucfirst($category);
            $metaDescription = "Artículos sobre " . ucfirst($category) . " en $siteTitle";
        }

        // 3. Obtener posts
        $posts = $query->latest()->get();
        
        // Intentar obtener posts del carrusel (si falla, devuelve vacío)
        try {
            $carouselPosts = Post::where('is_carousel', true)->latest()->take(5)->get();
        } catch (\Exception $e) {
            $carouselPosts = [];
        }

        // 4. Imagen por defecto para OG (primer post con imagen o null)
        $defaultImage = $posts->first(fn($p) => $p->image)?->image;

        // 5. SEO — datos para las meta tags del <head> (server-side)
        $seo = [
            'title'       => $pageTitle,
            'description' => $metaDescription,
            'url'         => url()->current(),
            'image'       => $this->absoluteImageUrl($defaultImage),
            'site_name'   => $siteTitle,
            'type'        => 'website',
        ];

        // 6. Empaquetar datos para Vue
        $data = [
            'meta_title' => $pageTitle,
            'meta_description' => $metaDescription,
            'hero_title' => $category ? ucfirst($category) : $siteTitle,
            'hero_text' => $heroText,
            'current_category' => $category ?? 'home',
            'posts' => $posts,
            'carousel_posts' => $carouselPosts
        ];

        return view('welcome', compact('data', 'seo'));
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);
        
        $siteTitle = 'ELEDEN';
        $setting = SiteSetting::first();
        if ($setting) {
            $siteTitle = $setting->title;
        }

        // SEO — datos para las meta tags del <head> (server-side)
        $seo = [
            'title'       => $post->title . ' - ' . $siteTitle,
            'description' => $post->excerpt ?? '',
            'url'         => url()->current(),
            'image'       => $this->absoluteImageUrl($post->image),
            'site_name'   => $siteTitle,
            'type'        => 'article',
        ];

        // Datos para Vue
        $data = [
            'meta_title' => $post->title . ' - ' . $siteTitle,
            'meta_description' => $post->excerpt,
            'post' => $post
        ];

        return view('post', compact('data', 'seo'));
    }
}