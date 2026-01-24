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
        // Validamos que lleguen los datos correctos
        $request->validate([
            'title' => 'required',
            'category' => 'required',
            'content' => 'required'
        ]);

        $post = new Post();
        $post->title = $request->title;
        $post->excerpt = $request->excerpt; // Resumen
        $post->content = $request->content; // Contenido completo
        $post->category = $request->category;
        $post->image = $request->image; // URL de la imagen
        $post->save();

        return response()->json(['message' => 'Publicación creada', 'post' => $post]);
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