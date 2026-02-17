<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiteSetting;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // 1. Ver el Panel y listar posts
    public function index() {
        $setting = SiteSetting::first();
        $posts = Post::latest()->get();
        return view('admin', compact('setting', 'posts'));
    }

    // 2. Guardar Configuración General
    public function updateSettings(Request $request) {
        $setting = SiteSetting::firstOrNew();
        $setting->title = $request->title;
        $setting->hero_text = $request->hero_text;
        $setting->save();
        return response()->json(['message' => 'Configuración actualizada']);
    }

    // 3. Crear Nueva Publicación
    public function storePost(Request $request) {
        $post = new Post();
        $this->saveOrUpdatePost($request, $post);
        return response()->json(['message' => 'Publicación creada', 'post' => $post]);
    }

    // 4. Editar Publicación Existente
    public function updatePost(Request $request, $id) {
        $post = Post::findOrFail($id);
        $this->saveOrUpdatePost($request, $post);
        return response()->json(['message' => 'Publicación actualizada', 'post' => $post]);
    }

    // 5. Eliminar Publicación
    public function deletePost($id) {
        $post = Post::find($id);
        if ($post) {
            // Opcional: Borrar imagen del storage si existe
            if ($post->image && str_contains($post->image, '/storage/')) {
                $path = str_replace('/storage/', '', $post->image);
                Storage::disk('public')->delete($path);
            }
            
            $post->delete();
            return response()->json(['message' => 'Eliminado correctamente']);
        }
        return response()->json(['error' => 'No encontrado'], 404);
    }

    // --- LÓGICA COMPARTIDA (PRIVADA) ---
    // Esta función maneja toda la magia de guardar, ya sea nuevo o edición
    private function saveOrUpdatePost(Request $request, Post $post) {
        // Validaciones básicas
        $request->validate([
            'title' => 'required',
            'category' => 'required',
        ]);

        // Asignar datos de texto
        $post->title = $request->title;
        $post->excerpt = $request->excerpt ?? ''; 
        $post->content = $request->content; // Importante para editar el contenido
        $post->category = $request->category;
        
        // Convertir "true"/"false" a booleano real
        $post->is_carousel = filter_var($request->is_carousel, FILTER_VALIDATE_BOOLEAN);

        // --- LÓGICA INTELIGENTE DE MULTIMEDIA ---
        
        // A. Si el usuario envió un VIDEO
        if ($request->filled('video_url')) {
            $post->video_url = $request->video_url;
            $post->image = null; // El video reemplaza a la imagen
        } 
        // B. Si el usuario subió un ARCHIVO DE IMAGEN (image_file)
        else if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('posts', 'public');
            $post->image = '/storage/' . $path;
            $post->video_url = null; // La imagen reemplaza al video
        } 
        // C. Si el usuario puso un LINK DE IMAGEN (image_url)
        else if ($request->filled('image_url')) {
            $post->image = $request->image_url;
            $post->video_url = null;
        }
        
        // Si no se envió nada nuevo de multimedia en la edición, 
        // se mantienen los datos que ya tenía el post.

        $post->save();
    }
}