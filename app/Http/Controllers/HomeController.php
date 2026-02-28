<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiteSetting;
use App\Models\Post;
use App\Models\Invitation;

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

    public function invitations()
    {
        $setting = SiteSetting::first();
        $siteTitle = $setting ? $setting->title : 'ELEDEN';
        
        $pageTitle = "Invitaciones - " . $siteTitle;
        $metaDescription = "Descubre nuestras invitaciones digitales personalizadas. Diseño único para eventos especiales como bodas, quinceaños y cumpleaños.";

        $invitations = Invitation::where('is_demo', true)->latest()->get();

        $seo = [
            'title'       => $pageTitle,
            'description' => $metaDescription,
            'url'         => url()->current(),
            'image'       => null,
            'site_name'   => $siteTitle,
            'type'        => 'website',
        ];

        $data = [
            'meta_title' => $pageTitle,
            'meta_description' => $metaDescription,
            'hero_title' => 'Invitaciones',
            'hero_text' => 'Catálogo de invitaciones digitales para tus eventos.',
            'current_category' => 'invitaciones',
            'invitations' => $invitations,
        ];

        // Usaremos la vista welcome.blade.php que montará un nuevo componente general o manejaremos la lógica en Vue
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

    public function showInvitation($slug)
    {
        if ($slug === 'boda1') {
            $invitation = new \App\Models\Invitation([
                'title' => 'Boda Especial (Demo)',
                'slug' => 'boda1',
                'template' => 'boda1',
                'client_name' => 'Marcela & Alejandro',
                'event_date' => date('Y-m-d', strtotime('+30 days')),
                'data' => [
                    'bride' => 'Marcela',
                    'groom' => 'Alejandro',
                    'ceremony_time' => '16:00',
                    'ceremony_location' => 'Parroquia San Miguel Arcángel',
                    'reception_time' => '19:00',
                    'reception_location' => 'Hacienda Los Arcángeles',
                    'theme_color' => '#ba966a',
                    'itinerary' => [
                        ['time' => '16:00', 'activity' => 'Ceremonia Religiosa', 'description' => 'Acompáñanos a dar el sí.'],
                        ['time' => '18:00', 'activity' => 'Recepción y Cóctel', 'description' => 'Llegada a la hacienda.'],
                        ['time' => '19:00', 'activity' => 'Cena', 'description' => 'Un festín para celebrar.'],
                        ['time' => '21:00', 'activity' => 'Primer Baile', 'description' => 'El comienzo de la fiesta.']
                    ],
                    'padrinos' => [
                        ['role' => 'Padrinos de Anillos', 'name' => 'Luis & Valentina'],
                        ['role' => 'Padrinos de Civil', 'name' => 'Carlos & Andrea']
                    ]
                ],
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1519225421980-715cb0215aed?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1520854221256-17451cc331bf?auto=format&fit=crop&w=800&q=80'
                ]
            ]);
            return view('invitation', compact('invitation'));
        }

        if ($slug === 'boda2') {
            $invitation = new \App\Models\Invitation([
                'title' => 'Boda Moderna (Demo)',
                'slug' => 'boda2',
                'template' => 'boda2',
                'client_name' => 'Sofia & Roberto',
                'event_date' => date('Y-m-d', strtotime('+45 days')),
                'data' => [
                    'bride' => 'Sofia',
                    'groom' => 'Roberto',
                    'ceremony_time' => '17:30',
                    'ceremony_location' => 'Jardín de las Rosas',
                    'reception_time' => '20:00',
                    'reception_location' => 'Salón Vista Bella',
                    'theme_color' => '#2c3e50',
                    'music_url' => '/storage/audio/Boda2.mp3',
                    'whatsapp_rsvp' => '59178945612',
                    'itinerary' => [
                        ['time' => '17:30', 'activity' => 'Bienvenida', 'description' => 'Cóctel de entrada.'],
                        ['time' => '18:30', 'activity' => 'Intercambio de Votos', 'description' => 'Bajo el atardecer.'],
                        ['time' => '21:00', 'activity' => 'Fiesta Moderna', 'description' => 'DJ y mucha diversión.']
                    ],
                    'padrinos' => [
                        ['role' => 'Padrinos de Honor', 'name' => 'Dr. Fernando & Elena'],
                    ]
                ],
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?auto=format&fit=crop&w=800&q=80'
                ]
            ]);
            return view('invitation', compact('invitation'));
        }

        $invitation = \App\Models\Invitation::where('slug', $slug)->firstOrFail();
        return view('invitation', compact('invitation'));
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