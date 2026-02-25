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

        // Meta descriptions mejoradas (120-160 caracteres)
        $metaDescription = "Descubre las últimas tendencias en tecnología, eventos únicos y proyectos de portafolio en {$siteTitle}. Tu fuente de inspiración digital para el mundo moderno.";

        if ($category) {
            $query->where('category', $category);
            $pageTitle = ucfirst($category) . " - " . $siteTitle;
            $heroText = "Explora los últimos artículos sobre " . ucfirst($category);
            $metaDescription = "Explora los mejores artículos sobre " . ucfirst($category) . " en {$siteTitle}. Contenido actualizado con las últimas novedades y tendencias del sector.";
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

        // Meta description mejorada: usar excerpt truncado a 160 chars
        $metaDescription = $post->excerpt 
            ? \Illuminate\Support\Str::limit($post->excerpt, 155, '...') 
            : "Lee el artículo \"{$post->title}\" en {$siteTitle}. Contenido de calidad sobre {$post->category}.";

        // SEO — datos para las meta tags del <head> (server-side)
        $seo = [
            'title'       => $post->title . ' - ' . $siteTitle,
            'description' => $metaDescription,
            'url'         => url()->current(),
            'image'       => $this->absoluteImageUrl($post->image),
            'site_name'   => $siteTitle,
            'type'        => 'article',
        ];

        // Artículos relacionados (misma categoría, excluir el actual, máximo 3)
        $relatedPosts = Post::where('category', $post->category)
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(3)
            ->get();

        // Datos para Vue
        $data = [
            'meta_title' => $post->title . ' - ' . $siteTitle,
            'meta_description' => $metaDescription,
            'post' => $post,
            'related_posts' => $relatedPosts,
        ];

        return view('post', compact('data', 'seo'));
    }

    /**
     * Genera el sitemap.xml dinámico.
     */
    public function sitemap()
    {
        $posts = Post::latest()->get();

        return response()
            ->view('sitemap', ['posts' => $posts])
            ->header('Content-Type', 'application/xml');
    }
}