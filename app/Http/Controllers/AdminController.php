<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiteSetting;
use App\Models\Post;
use Illuminate\Support\Facades\Storage; // Importante para manejar archivos

class AdminController extends Controller
{
    public function index() {
        $setting = SiteSetting::first();
        $posts = Post::latest()->get();
        return view('admin', compact('setting', 'posts'));
    }

    public function updateSettings(Request $request) {
        $setting = SiteSetting::firstOrNew();
        $setting->title = $request->title;
        $setting->hero_text = $request->hero_text;
        $setting->save();
        return response()->json(['message' => 'Configuración actualizada']);
    }

    // --- AQUÍ ESTABA EL PROBLEMA ---
    public function storePost(Request $request) {
        $request->validate([
            'title' => 'required',
            'category' => 'required',
            'content' => 'required'
        ]);

        $post = new Post();
        $post->title = $request->title;
        // Si no envían excerpt, guardamos una cadena vacía para que no de error SQL
        $post->excerpt = $request->excerpt ?? ''; 
        $post->content = $request->content;
        $post->category = $request->category;
        
        // Lógica para guardar la imagen real
        if ($request->hasFile('image_file')) {
            // Guardar en storage/app/public/posts
            $path = $request->file('image_file')->store('posts', 'public');
            $post->image = '/storage/' . $path;
        } else {
            // Si no hay archivo, intentar usar la URL o poner null
            $post->image = $request->image; 
        }

        // Lógica del Carrusel (con protección por si la columna no existe aún)
        // Convertimos el string "true"/"false" a booleano real
        $isCarousel = $request->is_carousel === 'true' || $request->is_carousel === true;
        
        // Solo intentamos guardar si la columna existe (o si ya corriste la migración)
        $post->is_carousel = $isCarousel; 
        
        $post->save();

        return response()->json(['message' => 'Publicación creada', 'post' => $post]);
    }

    public function deletePost($id) {
        $post = Post::find($id);
        if ($post) {
            $post->delete();
            return response()->json(['message' => 'Eliminado correctamente']);
        }
        return response()->json(['error' => 'No encontrado'], 404);
    }
}