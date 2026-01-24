<template>
  <div class="admin-layout">
    <aside class="sidebar">
      <h2>Admin Panel</h2>
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
      </nav>
      <a href="/" class="back-link">← Ver Web</a>
    </aside>

    <main class="content">
      
      <div v-if="currentTab === 'settings'" class="panel">
        <h3>Configuración General</h3>
        <div class="form-group">
          <label>Título de la Web</label>
          <input v-model="settings.title" type="text" class="input">
        </div>
        <div class="form-group">
          <label>Texto de Bienvenida</label>
          <textarea v-model="settings.hero_text" class="input"></textarea>
        </div>
        <button @click="saveSettings" class="btn btn-primary">Guardar Configuración</button>
      </div>

      <div v-if="currentTab === 'posts'" class="panel">
        <h3>Nueva Publicación</h3>
        
        <div class="create-post-form">
          <div class="row">
            <input v-model="newPost.title" placeholder="Título del artículo" class="input">
            <select v-model="newPost.category" class="input">
              <option value="" disabled>Seleccionar Categoría</option>
              <option value="eventos">Eventos</option>
              <option value="tecnologia">Tecnología</option>
              <option value="portafolio">Portafolio</option>
            </select>
          </div>
          
          <input v-model="newPost.excerpt" placeholder="Resumen corto (se ve en la tarjeta)" class="input">
          <input v-model="newPost.image" placeholder="URL de la imagen (ej: https://imgur.com/...)" class="input">
          <textarea v-model="newPost.content" placeholder="Escribe aquí todo el contenido..." class="input area-large"></textarea>
          
          <button @click="createPost" class="btn btn-success">Publicar Artículo</button>
        </div>

        <hr>

        <h3>Historial de Publicaciones</h3>
        <div class="posts-list">
          <div v-for="post in posts" :key="post.id" class="post-item">
            <div>
              <strong>{{ post.title }}</strong>
              <span class="badge">{{ post.category }}</span>
            </div>
            <button @click="deletePost(post.id)" class="btn-delete">Eliminar</button>
          </div>
        </div>
      </div>

    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

// Recibimos los datos que envía el controlador
const props = defineProps({
  initialData: Object // Aquí viene 'setting' y 'posts' juntos
});

// Datos Reactivos
const currentTab = ref('posts'); // Pestaña activa por defecto
const settings = ref(props.initialData.setting || { title: '', hero_text: '' });
const posts = ref(props.initialData.posts || []);

// Objeto vacío para el nuevo post
const newPost = ref({
  title: '',
  category: '',
  excerpt: '',
  content: '',
  image: ''
});

// --- FUNCIONES ---

// 1. Guardar Configuración
const saveSettings = async () => {
  try {
    await axios.post('/admin/settings', settings.value);
    alert('¡Configuración actualizada!');
  } catch (e) {
    alert('Error al guardar');
  }
};

// 2. Crear Post
const createPost = async () => {
  if (!newPost.value.title || !newPost.value.category) return alert('Faltan datos');

  try {
    const response = await axios.post('/admin/posts', newPost.value);
    // Agregamos el nuevo post a la lista visualmente (sin recargar)
    posts.value.unshift(response.data.post);
    // Limpiamos el formulario
    newPost.value = { title: '', category: '', excerpt: '', content: '', image: '' };
    alert('Publicado con éxito');
  } catch (e) {
    console.error(e);
    alert('Error al publicar');
  }
};

// 3. Eliminar Post
const deletePost = async (id) => {
  if (!confirm('¿Seguro que quieres borrar esto?')) return;

  try {
    await axios.delete(`/admin/posts/${id}`);
    // Quitamos el post de la lista visualmente
    posts.value = posts.value.filter(post => post.id !== id);
  } catch (e) {
    alert('Error al eliminar');
  }
};
</script>

<style scoped>
/* Estilos estilo Dashboard Moderno */
.admin-layout {
  display: flex;
  min-height: 100vh;
  font-family: 'Segoe UI', sans-serif;
  background: #f4f6f8;
}

/* Sidebar */
.sidebar {
  width: 250px;
  background: #1e293b;
  color: white;
  padding: 20px;
  display: flex;
  flex-direction: column;
}
.sidebar h2 { margin-bottom: 30px; font-size: 1.2rem; text-align: center;}
.sidebar nav button {
  width: 100%;
  text-align: left;
  padding: 12px;
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  font-size: 1rem;
  margin-bottom: 5px;
  border-radius: 5px;
  transition: 0.2s;
}
.sidebar nav button:hover, .sidebar nav button.active {
  background: #334155;
  color: white;
}
.back-link {
  margin-top: auto;
  color: #94a3b8;
  text-decoration: none;
  padding: 10px;
  text-align: center;
}

/* Content */
.content {
  flex: 1;
  padding: 40px;
}
.panel {
  background: white;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.05);
  max-width: 800px;
  margin: 0 auto;
}
.form-group { margin-bottom: 20px; }
.input {
  width: 100%;
  padding: 10px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  margin-bottom: 10px;
  font-size: 0.95rem;
}
.area-large { height: 150px; resize: vertical; }
.row { display: flex; gap: 10px; }

/* Botones */
.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}
.btn-primary { background: #3b82f6; color: white; }
.btn-success { background: #10b981; color: white; width: 100%; margin-top: 10px;}
.btn-delete {
  background: #ef4444;
  color: white;
  border: none;
  padding: 5px 10px;
  border-radius: 4px;
  cursor: pointer;
}

/* Lista */
.badge {
  background: #e2e8f0;
  padding: 2px 8px;
  border-radius: 10px;
  font-size: 0.8rem;
  margin-left: 10px;
  color: #475569;
  text-transform: uppercase;
}
.post-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 0;
  border-bottom: 1px solid #f1f5f9;
}
</style>