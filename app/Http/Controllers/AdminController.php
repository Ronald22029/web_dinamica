<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiteSetting;
use App\Models\Post; // <--- Importamos el modelo de Posts

class AdminController extends Controller
{
    public function index() {
        // Carga la configuración Y todos los posts ordenados por fecha
        $setting = SiteSetting::first();
        $posts = Post::latest()->get();
        
        return view('admin', compact('setting', 'posts'));
    }

    // Guardar Configuración General (Título y Subtítulo)
    public function updateSettings(Request $request) {
        $setting = SiteSetting::firstOrNew(); // Crea uno si no existe
        $setting->title = $request->title;
        $setting->hero_text = $request->hero_text;
        $setting->save();

        return response()->json(['message' => 'Configuración actualizada']);
    }

    // Crear una nueva Publicación
    public function storePost(Request $request) {
        $request->validate([
            'title' => 'required',
            'category' => 'required',
            // 'image' ahora puede ser archivo O texto (si prefieres pegar link)
        ]);

        $post = new Post();
        $post->title = $request->title;
        $post->excerpt = $request->excerpt;
        $post->content = $request->content;
        $post->category = $request->category;
        
        // --- LÓGICA DE IMAGEN ---
        if ($request->hasFile('image_file')) {
            // Guardar archivo en storage/app/public/posts
            $path = $request->file('image_file')->store('posts', 'public');
            $post->image = '/storage/' . $path; // Guardamos la ruta pública
        } else {
            $post->image = $request->image_url; // Por si usas link externo
        }

        // Checkbox del carrusel (viene como string "true" o null)
        $post->is_carousel = $request->is_carousel === 'true' ? true : false;
        
        $post->save();

        return response()->json(['message' => 'Publicado', 'post' => $post]);
    }

    // Borrar una Publicación
    public function deletePost($id) {
        $post = Post::find($id);
        if ($post) {
            $post->delete();
            return response()->json(['message' => 'Eliminado correctamente']);
        }
        return response()->json(['error' => 'No encontrado'], 404);
    }
}