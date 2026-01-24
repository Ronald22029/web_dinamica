<template>
  <div class="layout">
    
    <nav class="navbar" :class="{ 'scrolled': isScrolled }">
      <div class="container nav-content">
        <a href="/" class="logo">Ronald<span class="dot">.</span>Dev</a>
        <ul class="nav-links">
          <li><a href="/" :class="{ active: pageData.current_category === 'home' }">Inicio</a></li>
          <li><a href="/categoria/eventos" :class="{ active: pageData.current_category === 'eventos' }">Eventos</a></li>
          <li><a href="/categoria/tecnologia" :class="{ active: pageData.current_category === 'tecnologia' }">Tecnología</a></li>
          <li><a href="/categoria/portafolio" :class="{ active: pageData.current_category === 'portafolio' }">Portafolio</a></li>
        </ul>
      </div>
    </nav>

    <header class="hero">
      <div class="container hero-content">
        <span class="pill" v-if="pageData.current_category !== 'home'">Explorando</span>
        <h1 class="fade-in">{{ pageData.hero_title }}</h1>
        <p class="fade-in-delay">{{ pageData.hero_text }}</p>
      </div>
    </header>

    <section class="container content-section">
      
      <div v-if="pageData.posts.length === 0" class="empty-state">
        <p>Aún no hay publicaciones en esta sección.</p>
      </div>

      <div v-else class="grid">
        <article v-for="post in pageData.posts" :key="post.id" class="card">
          <div class="card-image">
            <img :src="post.image || 'https://via.placeholder.com/600x400/eee/ccc?text=Cover'" alt="Post Image">
            <span class="category-tag">{{ post.category }}</span>
          </div>
          <div class="card-body">
            <h3>{{ post.title }}</h3>
            <p>{{ post.excerpt }}</p>
            <a href="#" class="read-more">Leer más →</a>
          </div>
        </article>
      </div>
    </section>

    <footer class="footer">
      <p>&copy; 2026 Ronald Dev. Diseño Minimalista.</p>
    </footer>

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  initialData: Object
});

const pageData = ref(props.initialData);
const isScrolled = ref(false);

// SEO: Actualizar título del navegador
onMounted(() => {
  document.title = pageData.value.meta_title;
  window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});

// Efecto para el menú al hacer scroll
const handleScroll = () => {
  isScrolled.value = window.scrollY > 50;
};
</script>

<style scoped>
/* --- VARIABLES Y RESET --- */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap');

:host {
  font-family: 'Inter', sans-serif;
  color: #1a1a1a;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

/* --- NAVBAR --- */
.navbar {
  position: fixed;
  top: 0;
  width: 100%;
  padding: 20px 0;
  background: rgba(255, 255, 255, 0.95);
  transition: all 0.3s ease;
  z-index: 1000;
  border-bottom: 1px solid transparent;
}

.navbar.scrolled {
  padding: 15px 0;
  box-shadow: 0 2px 20px rgba(0,0,0,0.05);
  border-bottom: 1px solid #eee;
}

.nav-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo {
  font-weight: 800;
  font-size: 1.5rem;
  text-decoration: none;
  color: #000;
  letter-spacing: -1px;
}
.logo .dot { color: #3b82f6; }

.nav-links {
  display: flex;
  gap: 30px;
  list-style: none;
}

.nav-links a {
  text-decoration: none;
  color: #666;
  font-weight: 500;
  font-size: 0.95rem;
  transition: color 0.3s;
}

.nav-links a:hover, .nav-links a.active {
  color: #000;
}

/* --- HERO SECTION --- */
.hero {
  padding-top: 180px;
  padding-bottom: 80px;
  text-align: left;
  background: #fff;
}

.pill {
  background: #f3f4f6;
  padding: 5px 15px;
  border-radius: 50px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: #666;
}

.hero h1 {
  font-size: 4rem;
  line-height: 1.1;
  margin-top: 20px;
  margin-bottom: 20px;
  font-weight: 800;
  letter-spacing: -2px;
}

.hero p {
  font-size: 1.25rem;
  color: #666;
  max-width: 600px;
  line-height: 1.6;
}

/* --- GRID & CARDS --- */
.content-section { padding-bottom: 100px; }

.grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 40px;
}

.card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(0,0,0,0.08);
}

.card-image {
  height: 220px;
  width: 100%;
  position: relative;
  overflow: hidden;
}

.card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.card:hover .card-image img { transform: scale(1.05); }

.category-tag {
  position: absolute;
  top: 15px;
  right: 15px;
  background: white;
  padding: 5px 12px;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  color: #000;
}

.card-body { padding: 25px 0; }

.card-body h3 {
  font-size: 1.5rem;
  margin-bottom: 10px;
  font-weight: 700;
  letter-spacing: -0.5px;
}

.card-body p {
  color: #666;
  line-height: 1.6;
  margin-bottom: 20px;
}

.read-more {
  text-decoration: none;
  color: #000;
  font-weight: 600;
  font-size: 0.9rem;
  border-bottom: 2px solid #eee;
  padding-bottom: 2px;
  transition: border-color 0.3s;
}

.read-more:hover { border-color: #000; }

.footer {
  text-align: center;
  padding: 40px;
  border-top: 1px solid #eee;
  color: #999;
  font-size: 0.9rem;
}

/* --- ANIMACIONES --- */
.fade-in { animation: fadeIn 0.8s ease forwards; opacity: 0; transform: translateY(20px); }
.fade-in-delay { animation: fadeIn 0.8s ease 0.2s forwards; opacity: 0; transform: translateY(20px); }

@keyframes fadeIn {
  to { opacity: 1; transform: translateY(0); }
}

/* Responsive */
@media (max-width: 768px) {
  .hero h1 { font-size: 2.5rem; }
  .nav-links { display: none; /* Aquí podrías añadir un menú hamburguesa */ }
  .grid { grid-template-columns: 1fr; }
}
</style>