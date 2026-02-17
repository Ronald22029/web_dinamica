<template>
  <div class="admin-layout">
    
    <transition name="toast-fade">
      <div v-if="notification.show" class="toast-notification" :class="notification.type">
        <span class="toast-icon">
          {{ notification.type === 'success' ? '✅' : '⚠️' }}
        </span>
        <div class="toast-content">
          <strong class="toast-title">{{ notification.type === 'success' ? '¡Éxito!' : 'Atención' }}</strong>
          <p class="toast-message">{{ notification.message }}</p>
        </div>
        <button @click="closeNotification" class="toast-close">×</button>
      </div>
    </transition>

    <aside class="sidebar">
      <div class="sidebar-header">
        <h2>Panel Admin</h2>
      </div>
      <nav>
        <button 
          :class="{ active: currentTab === 'settings' }" 
          @click="currentTab = 'settings'">
          ⚙️ Configuración
        </button>
        <button 
          :class="{ active: currentTab === 'posts' }" 
          @click="currentTab = 'posts'">
          📝 Publicaciones
        </button>
        
        <button @click="logout" class="logout-btn">
          🔴 Cerrar Sesión
        </button>
      </nav>
      <a href="https://eleden.site" class="back-link">← Ver Web</a>
    </aside>

    <main class="content">
      
      <div v-if="currentTab === 'settings'" class="panel slide-in">
        <h3>Configuración General</h3>
        <div class="form-group">
          <label>Título de la Web</label>
          <input v-model="settings.title" type="text" class="input">
        </div>
        <div class="form-group">
          <label>Texto de Bienvenida (Hero)</label>
          <textarea v-model="settings.hero_text" class="input"></textarea>
        </div>
        <button @click="saveSettings" class="btn btn-primary">Guardar Configuración</button>
      </div>

      <div v-if="currentTab === 'posts'" class="panel slide-in">
        
        <div class="header-action">
          <h3>{{ isEditing ? '✏️ Editando Publicación' : '📝 Nueva Publicación' }}</h3>
          <button v-if="isEditing" @click="resetForm" class="btn-cancel">Cancelar Edición</button>
        </div>
        
        <div class="create-post-form">
          <div class="row">
            <div class="col">
              <input v-model="newPost.title" placeholder="Título del artículo" class="input title-input">
            </div>
            <div class="col-small">
              <select v-model="newPost.category" class="input">
                <option value="" disabled>Categoría</option>
                <option value="eventos">Eventos</option>
                <option value="tecnologia">Tecnología</option>
                <option value="portafolio">Portafolio</option>
              </select>
            </div>
          </div>

          <div class="media-selector">
            <label>Tipo de Portada:</label>
            <div class="media-tabs">
              <button @click="mediaType = 'upload'" :class="{ active: mediaType === 'upload' }">📤 Subir Foto</button>
              <button @click="mediaType = 'link'" :class="{ active: mediaType === 'link' }">🔗 Link Foto</button>
              <button @click="mediaType = 'video'" :class="{ active: mediaType === 'video' }">🎥 Video</button>
            </div>

            <div v-if="isEditing && !previewImage" class="current-media-info">
              <small>Actualmente guardado: 
                <span v-if="newPost.video_url">Video Externo</span>
                <span v-else-if="newPost.image">Imagen</span>
                <span v-else>Nada</span>
              </small>
            </div>

            <div v-if="mediaType === 'upload'" class="media-input-area">
              <label class="file-label">
                📸 Elegir Archivo
                <input type="file" @change="handleFileUpload" class="file-input" accept="image/*">
              </label>
              <span v-if="previewImage" class="file-success">Imagen lista para subir ✓</span>
            </div>

            <div v-if="mediaType === 'link'" class="media-input-area">
              <input v-model="newPost.image_url" placeholder="Ej: https://imgur.com/foto.jpg" class="input">
            </div>

            <div v-if="mediaType === 'video'" class="media-input-area">
              <input v-model="newPost.video_url" placeholder="Link de YouTube o Facebook" class="input">
            </div>
          </div>

          <input v-model="newPost.excerpt" placeholder="Resumen corto (gancho)" class="input">
          
          <div class="checkbox-group">
            <label>
              <input type="checkbox" v-model="newPost.is_carousel">
              🌟 Destacar en el Carrusel Principal
            </label>
          </div>

          <textarea v-model="newPost.content" placeholder="Contenido completo..." class="input area-large"></textarea>
          
          <button @click="savePost" class="btn" :class="isEditing ? 'btn-warning' : 'btn-success'">
            {{ isEditing ? 'Actualizar Cambios' : 'Publicar Artículo' }}
          </button>
        </div>

        <div class="divider"></div>

        <h3>Historial ({{ posts.length }})</h3>
        <div class="posts-list">
          <div v-for="post in posts" :key="post.id" class="post-item" :class="{ 'editing-item': post.id === editingId }">
            <div class="post-info">
              <strong>{{ post.title }}</strong>
              <div class="post-badges">
                <span class="badge">{{ post.category }}</span>
                <span v-if="post.video_url" class="badge video-badge">🎥 Video</span>
                <span v-if="post.is_carousel" class="badge star-badge">🌟 Carrusel</span>
              </div>
            </div>
            <div class="actions">
              <button @click="editPost(post)" class="btn-icon edit-icon">✏️</button>
              <button @click="deletePost(post.id)" class="btn-icon delete-icon">🗑️</button>
            </div>
          </div>
        </div>
      </div>

    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
  initialData: Object 
});

const currentTab = ref('posts'); 
const mediaType = ref('upload');
const settings = ref(props.initialData.setting || { title: '', hero_text: '' });
const posts = ref(props.initialData.posts || []);

// Notificaciones
const notification = ref({ show: false, message: '', type: 'success' });

// Estado de Edición
const isEditing = ref(false);
const editingId = ref(null);

const newPost = ref({
  title: '',
  category: '',
  excerpt: '',
  content: '',
  is_carousel: false,
  image_file: null,
  image_url: '',
  video_url: '',
  image: null 
});

const previewImage = ref(false);

// --- FUNCIONES DE NOTIFICACIÓN ---
const showToast = (message, type = 'success') => {
  notification.value = { show: true, message, type };
  // Ocultar automáticamente después de 3 segundos
  setTimeout(() => {
    notification.value.show = false;
  }, 3500);
};

const closeNotification = () => {
  notification.value.show = false;
};

// --- LOGICA DE NEGOCIO ---

const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    newPost.value.image_file = file;
    previewImage.value = true;
    showToast('Imagen cargada correctamente', 'success');
  }
};

const saveSettings = async () => {
  try {
    await axios.post('/settings', settings.value);
    showToast('Configuración actualizada correctamente');
  } catch (e) {
    showToast('Error al guardar configuración', 'error');
  }
};

const editPost = (post) => {
  isEditing.value = true;
  editingId.value = post.id;
  
  newPost.value = { 
    ...post,
    image_file: null, 
    image_url: '',    
    is_carousel: !!post.is_carousel 
  };
  
  previewImage.value = false;

  if (post.video_url) {
    mediaType.value = 'video';
    newPost.value.video_url = post.video_url; 
  } else if (post.image && post.image.startsWith('http') && !post.image.includes('/storage/')) {
    mediaType.value = 'link';
    newPost.value.image_url = post.image;
  } else {
    mediaType.value = 'upload';
  }

  window.scrollTo({ top: 0, behavior: 'smooth' });
  showToast('Modo edición activado', 'success');
};

const resetForm = () => {
  isEditing.value = false;
  editingId.value = null;
  newPost.value = {
    title: '', category: '', excerpt: '', content: '',
    is_carousel: false, image_file: null, image_url: '', video_url: ''
  };
  previewImage.value = false;
  mediaType.value = 'upload';
};

const savePost = async () => {
  if (!newPost.value.title || !newPost.value.category) {
    return showToast('Por favor completa el título y la categoría', 'error');
  }

  let formData = new FormData();
  formData.append('title', newPost.value.title);
  formData.append('category', newPost.value.category);
  formData.append('excerpt', newPost.value.excerpt || '');
  formData.append('content', newPost.value.content || '');
  formData.append('is_carousel', newPost.value.is_carousel ? 'true' : 'false');
  
  if (mediaType.value === 'upload' && newPost.value.image_file) {
    formData.append('image_file', newPost.value.image_file);
  } else if (mediaType.value === 'link' && newPost.value.image_url) {
    formData.append('image_url', newPost.value.image_url);
  } else if (mediaType.value === 'video' && newPost.value.video_url) {
    formData.append('video_url', newPost.value.video_url);
  }

  try {
    let url = '/posts';
    let msg = '¡Publicación creada exitosamente!';

    if (isEditing.value) {
      url = `/posts/${editingId.value}`; 
      formData.append('_method', 'PUT'); 
      msg = '¡Publicación actualizada correctamente!';
    }

    await axios.post(url, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    });
    
    // Mostramos el mensaje bonito y esperamos antes de recargar
    showToast(msg, 'success');
    setTimeout(() => {
      window.location.reload(); 
    }, 1500);

  } catch (e) {
    console.error(e);
    showToast('Ocurrió un error al guardar. Revisa la consola.', 'error');
  }
};

const deletePost = async (id) => {
  if (!confirm('¿Seguro que deseas eliminar esta publicación?')) return;
  try {
    await axios.delete(`/posts/${id}`);
    posts.value = posts.value.filter(post => post.id !== id);
    if (editingId.value === id) resetForm();
    showToast('Publicación eliminada', 'success');
  } catch (e) {
    showToast('Error al eliminar', 'error');
  }
};

const logout = async () => {
  try {
    await axios.post('/logout');
    window.location.href = '/login';
  } catch (e) { 
    showToast('Error al cerrar sesión', 'error'); 
  }
};
</script>

<style scoped>
/* TOAST NOTIFICATION STYLES */
.toast-notification {
  position: fixed; top: 20px; right: 20px;
  background: white;
  padding: 16px 20px;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
  display: flex; align-items: flex-start; gap: 12px;
  z-index: 9999;
  min-width: 300px;
  border-left: 5px solid;
}
.toast-notification.success { border-left-color: #10b981; }
.toast-notification.error { border-left-color: #ef4444; }

.toast-icon { font-size: 1.2rem; }
.toast-content { flex: 1; }
.toast-title { display: block; font-size: 0.95rem; color: #1e293b; margin-bottom: 2px; }
.toast-message { font-size: 0.85rem; color: #64748b; margin: 0; }
.toast-close { background: none; border: none; font-size: 1.5rem; color: #94a3b8; cursor: pointer; line-height: 0.5; }

/* Animación de entrada/salida */
.toast-fade-enter-active, .toast-fade-leave-active { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.toast-fade-enter-from, .toast-fade-leave-to { opacity: 0; transform: translateX(30px); }

/* ESTILOS GENERALES (Mantenidos) */
.admin-layout { display: flex; min-height: 100vh; font-family: 'Segoe UI', sans-serif; background: #f1f5f9; }
.sidebar { width: 260px; background: #0f172a; color: white; padding: 25px; display: flex; flex-direction: column; }
.sidebar-header h2 { margin-bottom: 40px; font-size: 1.4rem; font-weight: 700; color: #fff; text-align: center; letter-spacing: 1px;}
.sidebar nav button {
  width: 100%; text-align: left; padding: 14px 16px; background: transparent; border: none;
  color: #94a3b8; cursor: pointer; font-size: 1rem; margin-bottom: 8px; border-radius: 8px;
  transition: all 0.2s; font-weight: 500; display: flex; align-items: center; gap: 10px;
}
.sidebar nav button:hover { background: rgba(255,255,255,0.05); color: white; }
.sidebar nav button.active { background: #3b82f6; color: white; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
.logout-btn { margin-top: 40px !important; color: #ef4444 !important; border: 1px solid rgba(239, 68, 68, 0.2) !important; }
.logout-btn:hover { background: rgba(239, 68, 68, 0.1) !important; }
.back-link { margin-top: auto; color: #64748b; text-decoration: none; padding: 12px; text-align: center; font-size: 0.95rem; transition: 0.2s; border-radius: 8px; border: 1px dashed #334155; }
.back-link:hover { color: white; background: rgba(255,255,255,0.05); border-color: #94a3b8; }
.content { flex: 1; padding: 40px; overflow-y: auto; }
.panel { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); max-width: 900px; margin: 0 auto; animation: slideUp 0.4s ease-out; }
@keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.header-action { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
h3 { margin: 0; color: #1e293b; font-size: 1.5rem; font-weight: 700; }
.input { width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 15px; font-size: 0.95rem; transition: 0.2s; outline: none; box-sizing: border-box; }
.input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
.title-input { font-size: 1.1rem; font-weight: 600; }
.area-large { height: 200px; resize: vertical; line-height: 1.6; }
.row { display: flex; gap: 20px; }
.col { flex: 1; }
.col-small { width: 200px; }
.media-selector { background: #f8fafc; padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #e2e8f0; }
.media-tabs { display: flex; gap: 10px; margin-top: 10px; margin-bottom: 15px; }
.media-tabs button { padding: 8px 16px; border: 1px solid #cbd5e1; background: white; border-radius: 6px; cursor: pointer; color: #64748b; font-weight: 600; font-size: 0.9rem; transition: 0.2s; }
.media-tabs button.active { background: #0f172a; color: white; border-color: #0f172a; }
.current-media-info { margin-bottom: 10px; color: #f59e0b; font-weight: 600; }
.file-label { display: inline-block; background: #e2e8f0; color: #475569; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: 0.2s; }
.file-label:hover { background: #cbd5e1; }
.file-input { display: none; }
.file-success { margin-left: 10px; color: #10b981; font-weight: bold; }
.btn { padding: 14px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 1rem; transition: 0.2s; width: 100%; margin-top: 10px;}
.btn-primary { background: #3b82f6; color: white; }
.btn-success { background: #10b981; color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); }
.btn-success:hover { background: #059669; }
.btn-warning { background: #f59e0b; color: white; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2); }
.btn-warning:hover { background: #d97706; }
.btn-cancel { background: transparent; color: #64748b; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 6px; cursor: pointer; }
.btn-cancel:hover { background: #f1f5f9; color: #0f172a; }
.divider { height: 1px; background: #e2e8f0; margin: 40px 0; }
.post-item { display: flex; justify-content: space-between; align-items: center; padding: 16px; background: #f8fafc; border-radius: 10px; margin-bottom: 10px; transition: 0.2s; border: 1px solid transparent; }
.post-item:hover { background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
.post-item.editing-item { border-color: #f59e0b; background: #fffbeb; }
.post-info { display: flex; flex-direction: column; gap: 5px; }
.post-badges { display: flex; gap: 8px; }
.badge { background: #e2e8f0; padding: 3px 8px; border-radius: 6px; font-size: 0.75rem; color: #475569; text-transform: uppercase; font-weight: 700; }
.video-badge { background: #fee2e2; color: #ef4444; }
.star-badge { background: #fef3c7; color: #d97706; }
.actions { display: flex; gap: 8px; }
.btn-icon { background: white; border: 1px solid #e2e8f0; cursor: pointer; font-size: 1.1rem; padding: 8px; border-radius: 6px; transition: 0.2s; }
.edit-icon:hover { background: #fef3c7; color: #d97706; border-color: #fcd34d; }
.delete-icon:hover { background: #fee2e2; color: #ef4444; border-color: #fca5a5; }
.checkbox-group { margin: 15px 0; font-weight: 600; color: #334155; display: flex; align-items: center; }
.checkbox-group input { margin-right: 10px; transform: scale(1.2); }
</style>