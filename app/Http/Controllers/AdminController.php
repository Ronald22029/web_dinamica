<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiteSetting;
use App\Models\Post;
use App\Models\Invitation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\Models\User;

class AdminController extends Controller
{
    // 1. Ver el Panel y listar posts
    public function index() {
        $user = Auth::user(); // Added this line
        $setting = SiteSetting::first();
        $posts = Post::latest()->get();
        
        // Si es cliente, solo ve sus propias invitaciones (no demos)
        if ($user->role === 'client') {
            $invitations = Invitation::with('user')->where('user_id', $user->id)->latest()->get();
        } else {
            $invitations = Invitation::with('user')->latest()->get();
        }
        
        // Detectamos si estamos en producción para el botón "Ver Web"
        $host = request()->getHost();
        $homeUrl = ($host === 'admin.eleden.site') ? 'https://eleden.site' : url('/');
        // En producción (subdominio) no hay prefijo; en local se usa /admin
        $adminBaseUrl = ($host === 'admin.eleden.site') ? '' : '/admin';

        // Enviamos todo compactado
        return view('admin', [
            'initialData' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ],
                'setting' => $setting,
                'posts' => $posts,
                'invitations' => $invitations,
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

    // --- INVITATIONS LOGIC ---
    
    public function storeInvitation(Request $request) {
        $user = Auth::user();
        // Solo administradores pueden crear nuevas invitaciones
        if ($user->role === 'client') {
            return response()->json(['error' => 'Los clientes no pueden crear nuevas invitaciones'], 403);
        }
        try {
            $invitation = new Invitation();
            $this->saveOrUpdateInvitation($request, $invitation);
            return response()->json(['message' => 'Invitación creada', 'invitation' => $invitation]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saving invitation:', ['msg' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateInvitation(Request $request, $id) {
        $user = Auth::user();
        $invitation = Invitation::findOrFail($id);
        
        // Autorización
        if ($user->role === 'client' && $invitation->user_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Los clientes no pueden cambiar la plantilla base de su invitación
        if ($user->role === 'client') {
            $request->merge(['template' => $invitation->template]);
        }

        $this->saveOrUpdateInvitation($request, $invitation);
        return response()->json(['message' => '¡Invitación actualizada correctamente!']);
    }

    public function deleteInvitation($id) {
        $user = Auth::user();
        
        // Los clientes no pueden eliminar invitaciones
        if ($user->role === 'client') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $invitation = Invitation::find($id);
        if ($invitation) {
            // Eliminar imagenes asociadas si existen
        if ($invitation->gallery_images) {
            foreach ($invitation->gallery_images as $img) {
                if (str_contains($img, '/storage/')) {
                    $path = str_replace('/storage/', '', $img);
                    Storage::disk('public')->delete($path);
                }
            }
        }

        // Eliminar MP3 si existe
        if (isset($invitation->data['music_url'])) {
            $musicPath = str_replace('/storage/', '', $invitation->data['music_url']);
            Storage::disk('public')->delete($musicPath);
        }

        $invitation->delete();
            return response()->json(['message' => 'Invitación eliminada correctamente']);
        }
        return response()->json(['error' => 'No encontrado'], 404);
    }

    private function saveOrUpdateInvitation(Request $request, Invitation $invitation) {
        $request->validate([
            'title' => 'required',
            'music_file' => 'nullable|file|mimes:mp3,wav|max:10240', // 10MB limit
        ]);

        $invitation->title = $request->title;
        $invitation->client_name = $request->client_name ?: null;
        $invitation->event_date = $request->event_date ?: null;
        $invitation->description = $request->description ?: null;
        $invitation->preview_url = $request->preview_url ?: null;

        // Nuevos campos
        $baseSlug = $request->slug ?: \Illuminate\Support\Str::slug($request->title);
        $slug = $baseSlug;
        $counter = 1;
        while (\App\Models\Invitation::where('slug', $slug)->where('id', '!=', $invitation->id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        $invitation->slug = $slug;
        $invitation->template = $request->template ?? 'boda1';
        $invitation->is_demo = $request->has('is_demo') ? filter_var($request->is_demo, FILTER_VALIDATE_BOOLEAN) : false;
        
        if ($request->has('data')) {
            $invitation->data = json_decode($request->data, true) ?? [];
        }

        // Manejo de la galería
        $gallery = $invitation->gallery_images ?? [];
        
        // Procesar imagenes enviadas a traves de FormData list
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('invitations', 'public');
                $gallery[] = '/storage/' . $path;
            }
        }
        
        // Procesar imágenes que se mantuvieron de la edición (JSON list enviado)
        if ($request->has('kept_images')) {
            $keptImages = json_decode($request->kept_images, true) ?? [];
            
            // Determinar qué imágenes se eliminaron y borrarlas del disco
            $deletedImages = array_diff($gallery, $keptImages);
            foreach ($deletedImages as $deletedImage) {
                if (str_contains($deletedImage, '/storage/')) {
                    $path = str_replace('/storage/', '', $deletedImage);
                    Storage::disk('public')->delete($path);
                }
            }
            
            // Agregar las imagenes nuevas cargadas a las imagenes que se mantienen
            $newGallery = $keptImages;
            if ($request->hasFile('images')) {
                 foreach ($request->file('images') as $file) {
                    $path = $file->store('invitations', 'public');
                    $newGallery[] = '/storage/' . $path;
                }
            }
            $gallery = $newGallery;
        }

        $invitation->gallery_images = $gallery;

        // Manejo de archivo de música (.mp3) directos a public_path('audio')
    if ($request->hasFile('music_file')) {
        // Borrar anterior si existe dentro de /audio directo
        $data = $invitation->data ?? [];
        if (isset($data['music_url'])) {
            $oldMusicPath = str_replace('/audio/', '', $data['music_url']);
            $fullPath = public_path('audio/' . ltrim($oldMusicPath, '/'));
            if (file_exists($fullPath) && is_file($fullPath)) {
                @unlink($fullPath);
            }
        }

        $file = $request->file('music_file');
        // Limpiar nombre original para evitar caracteres raros
        $originalName = preg_replace('/[^A-Za-z0-9\-\_\.]/', '', $file->getClientOriginalName());
        $filename = time() . '_' . $originalName;
        $file->move(public_path('audio'), $filename);
        $data['music_url'] = '/audio/' . $filename;
        $invitation->data = $data;
    } elseif ($request->input('remove_music') == '1') {
        // El admin eliminó la música — borrar archivo del disco
        $data = $invitation->data ?? [];
        if (isset($data['music_url'])) {
            $oldMusicPath = str_replace('/audio/', '', $data['music_url']);
            $fullPath = public_path('audio/' . ltrim($oldMusicPath, '/'));
            if (file_exists($fullPath) && is_file($fullPath)) {
                @unlink($fullPath);
            }
            unset($data['music_url']);
            $invitation->data = $data;
        }
    }
        $invitation->save();
    }

    // --- RSVP & STATS ---

    public function storeRSVP(Request $request, $id) {
        $invitation = Invitation::findOrFail($id);
        $request->validate([
            'name' => 'required',
            'attending' => 'required',
        ]);

        $response = $invitation->responses()->create([
            'name' => $request->name,
            'attending' => $request->attending,
            'guests' => $request->guests ?? 1,
            'message' => $request->message
        ]);

        return response()->json(['message' => 'Respuesta guardada', 'response' => $response]);
    }

    public function incrementVisits($id) {
        $invitation = Invitation::findOrFail($id);
        $invitation->increment('visits_count');
        return response()->json(['success' => true, 'visits' => $invitation->visits_count]);
    }

    public function getInvitationStats($id) {
        $invitation = Invitation::with('responses')->findOrFail($id);
        return response()->json([
            'visits' => $invitation->visits_count,
            'responses' => $invitation->responses
        ]);
    }

    public function assignUser(Request $request, $id) {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Solo administradores pueden crear usuarios'], 403);
        }

        $invitation = Invitation::with('user')->findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => [
                'required',
                'min:3',
                // Único excepto para el usuario ya asignado a esta invitación
                \Illuminate\Validation\Rule::unique('users', 'email')->ignore($invitation->user_id),
            ],
            'password' => $invitation->user_id ? 'nullable|min:6' : 'required|min:6',
        ]);

        // Si ya existe un usuario asignado, actualizamos sus datos
        if ($invitation->user) {
            $invitation->user->name = $request->name;
            $invitation->user->email = $request->email;
            if ($request->filled('password')) {
                $invitation->user->password = Hash::make($request->password);
            }
            $invitation->user->save();

            return response()->json([
                'message' => 'Datos del usuario actualizados con éxito',
                'user' => $invitation->user
            ]);
        }

        // Si no hay usuario, creamos uno nuevo y lo asignamos
        $clientUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client'
        ]);

        $invitation->user_id = $clientUser->id;
        $invitation->save();

        return response()->json(['message' => 'Usuario creado y asignado con éxito', 'user' => $clientUser]);
    }
}