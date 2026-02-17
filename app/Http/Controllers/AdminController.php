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
        
        // Detectamos si estamos en producción para el botón "Ver Web"
        $host = request()->getHost();
        $homeUrl = ($host === 'admin.eleden.site') ? 'https://eleden.site' : url('/');
        // En producción (subdominio) no hay prefijo; en local se usa /admin
        $adminBaseUrl = ($host === 'admin.eleden.site') ? '' : '/admin';

        // Enviamos todo compactado
        return view('admin', [
            'initialData' => [
                'setting' => $setting,
                'posts' => $posts,
                'homeUrl' => $homeUrl,
                'adminBaseUrl' => $adminBaseUrl
            ]
        ]);
    }

    // ... (El resto de tus funciones updateSettings, storePost, etc., se quedan igual)
    
    public function updateSettings(Request $request) {
        $setting = SiteSetting::firstOrNew();
        $setting->title = $request->title;
        $setting->hero_text = $request->hero_text;
        $setting->save();
        return response()->json(['message' => 'Configuración actualizada']);
    }

    public function storePost(Request $request) {
        $post = new Post();
        $this->saveOrUpdatePost($request, $post);
        return response()->json(['message' => 'Publicación creada', 'post' => $post]);
    }

    public function updatePost(Request $request, $id) {
        $post = Post::findOrFail($id);
        $this->saveOrUpdatePost($request, $post);
        return response()->json(['message' => 'Publicación actualizada', 'post' => $post]);
    }

    public function deletePost($id) {
        $post = Post::find($id);
        if ($post) {
            if ($post->image && str_contains($post->image, '/storage/')) {
                $path = str_replace('/storage/', '', $post->image);
                Storage::disk('public')->delete($path);
            }
            $post->delete();
            return response()->json(['message' => 'Eliminado correctamente']);
        }
        return response()->json(['error' => 'No encontrado'], 404);
    }

    private function saveOrUpdatePost(Request $request, Post $post) {
        $request->validate([
            'title' => 'required',
            'category' => 'required',
        ]);

        $post->title = $request->title;
        $post->excerpt = $request->excerpt ?? ''; 
        $post->content = $request->content; 
        $post->category = $request->category;
        $post->is_carousel = filter_var($request->is_carousel, FILTER_VALIDATE_BOOLEAN);

        if ($request->filled('video_url')) {
            $post->video_url = $request->video_url;
            $post->image = null; 
        } 
        else if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('posts', 'public');
            $post->image = '/storage/' . $path;
            $post->video_url = null; 
        } 
        else if ($request->filled('image_url')) {
            $post->image = $request->image_url;
            $post->video_url = null;
        }
        
        $post->save();
    }
}