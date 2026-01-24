<template>
  <div class="main-wrapper">
    
    <nav class="navbar" :class="{ 'scrolled': isScrolled }">
      <div class="container nav-content">
        <a href="/" class="brand">
          Ronald<span class="highlight">.Dev</span>
        </a>
        <ul class="nav-links">
          <li><a href="/" :class="{ active: pageData.current_category === 'home' }">Inicio</a></li>
          <li><a href="/categoria/eventos" :class="{ active: pageData.current_category === 'eventos' }">Eventos</a></li>
          <li><a href="/categoria/tecnologia" :class="{ active: pageData.current_category === 'tecnologia' }">Tecnología</a></li>
          <li><a href="/categoria/portafolio" :class="{ active: pageData.current_category === 'portafolio' }">Portafolio</a></li>
        </ul>
      </div>
    </nav>

    <header class="hero-section">
      <div class="aurora-bg"></div>
      <div class="container hero-content">
        <span class="badge-pill" v-if="pageData.current_category !== 'home'">
          Explorando {{ pageData.current_category }}
        </span>
        
        <h1 class="hero-title">
          {{ pageData.hero_title }}
        </h1>
        
        <p class="hero-subtitle">{{ pageData.hero_text }}</p>
        
        <div class="hero-actions" v-if="pageData.current_category === 'home'">
          <a href="/categoria/portafolio" class="btn-primary">Ver Proyectos</a>
          <a href="#content" class="btn-secondary">Leer Novedades</a>
        </div>
      </div>
    </header>

    <section id="content" class="container content-section">
      <div v-if="pageData.posts.length === 0" class="empty-state">
        <div class="empty-icon">📭</div>
        <p>No hay publicaciones en esta categoría aún.</p>
      </div>

      <div v-else class="grid">
        <article v-for="(post, index) in pageData.posts" :key="post.id" 
                 class="card" :style="{ animationDelay: `${index * 100}ms` }">
          
          <div class="card-media">
            <img :src="post.image || 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=800&q=80'" alt="Post">
            <div class="card-overlay">
              <span class="category-tag">{{ post.category }}</span>
            </div>
          </div>

          <div class="card-content">
            <h3>{{ post.title }}</h3>
            <p>{{ post.excerpt }}</p>
            <a href="#" class="link-arrow">Leer artículo <span class="arrow">→</span></a>
          </div>
        </article>
      </div>
    </section>

    <footer class="modern-footer">
      <p>&copy; 2026 Ronald Dev. <span class="dim">Innovación y Diseño.</span></p>
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

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20;
};

onMounted(() => {
  document.title = pageData.value.meta_title;
  window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
/* --- IMPORTAR FUENTE --- */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;500;700;800&display=swap');

:host {
  font-family: 'Plus Jakarta Sans', sans-serif;
  color: #1e293b;
  overflow-x: hidden;
}

.main-wrapper {
  background-color: #f8fafc;
  min-height: 100vh;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
}

/* --- NAVBAR GLASS --- */
.navbar {
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 100;
  padding: 20px 0;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.navbar.scrolled {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(12px);
  padding: 12px 0;
  border-bottom: 1px solid rgba(0,0,0,0.05);
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
}

.nav-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.brand {
  font-size: 1.5rem;
  font-weight: 800;
  text-decoration: none;
  color: #0f172a;
  letter-spacing: -0.5px;
}

.highlight { color: #3b82f6; }

.nav-links {
  display: flex;
  gap: 32px;
  list-style: none;
  padding: 0;
}

.nav-links a {
  text-decoration: none;
  color: #64748b;
  font-weight: 600;
  font-size: 0.95rem;
  transition: color 0.3s;
  position: relative;
}

.nav-links a:hover, .nav-links a.active {
  color: #0f172a;
}

.nav-links a::after {
  content: '';
  position: absolute;
  bottom: -4px;
  left: 0;
  width: 0%;
  height: 2px;
  background: #3b82f6;
  transition: width 0.3s ease;
}

.nav-links a.active::after { width: 100%; }

/* --- HERO AURORA --- */
.hero-section {
  position: relative;
  padding-top: 180px;
  padding-bottom: 100px;
  overflow: hidden;
  text-align: center;
}

/* Efecto de fondo animado */
.aurora-bg {
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle at 50% 50%, rgba(59, 130, 246, 0.15), transparent 50%),
              radial-gradient(circle at 80% 20%, rgba(139, 92, 246, 0.15), transparent 40%);
  animation: auroraMove 15s infinite alternate linear;
  z-index: 0;
  filter: blur(60px);
}

@keyframes auroraMove {
  0% { transform: translate(0, 0) rotate(0deg); }
  100% { transform: translate(-10%, 10%) rotate(5deg); }
}

.hero-content {
  position: relative;
  z-index: 1;
}

.badge-pill {
  background: rgba(59, 130, 246, 0.1);
  color: #2563eb;
  padding: 8px 16px;
  border-radius: 100px;
  font-size: 0.85rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  display: inline-block;
  margin-bottom: 24px;
  backdrop-filter: blur(5px);
}

.hero-title {
  font-size: 4.5rem;
  line-height: 1.1;
  font-weight: 800;
  letter-spacing: -2px;
  margin-bottom: 24px;
  background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: slideUp 0.8s ease-out;
}

.hero-subtitle {
  font-size: 1.25rem;
  color: #64748b;
  max-width: 600px;
  margin: 0 auto 40px;
  line-height: 1.7;
  animation: slideUp 0.8s ease-out 0.2s backwards;
}

.hero-actions {
  display: flex;
  justify-content: center;
  gap: 16px;
  animation: slideUp 0.8s ease-out 0.4s backwards;
}

.btn-primary {
  background: #0f172a;
  color: white;
  padding: 14px 32px;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 600;
  transition: transform 0.2s, box-shadow 0.2s;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.3);
}

.btn-secondary {
  background: white;
  color: #0f172a;
  padding: 14px 32px;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 600;
  border: 1px solid #e2e8f0;
  transition: all 0.2s;
}

.btn-secondary:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
}

/* --- CARDS MODERNAS --- */
.content-section {
  padding-bottom: 120px;
}

.grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
  gap: 40px;
}

.card {
  background: white;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid rgba(255,255,255,0.5);
  animation: fadeIn 0.8s ease-out backwards;
}

.card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.08);
}

.card-media {
  height: 240px;
  position: relative;
  overflow: hidden;
}

.card-media img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s ease;
}

.card:hover .card-media img {
  transform: scale(1.08);
}

.category-tag {
  position: absolute;
  bottom: 16px;
  left: 16px;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(4px);
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  color: #0f172a;
  box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}

.card-content {
  padding: 32px;
}

.card-content h3 {
  font-size: 1.35rem;
  margin-bottom: 12px;
  font-weight: 700;
  color: #0f172a;
  line-height: 1.3;
}

.card-content p {
  color: #64748b;
  line-height: 1.6;
  margin-bottom: 24px;
  font-size: 0.95rem;
}

.link-arrow {
  text-decoration: none;
  color: #3b82f6;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: gap 0.2s;
}

.link-arrow:hover {
  gap: 10px;
}

.modern-footer {
  text-align: center;
  padding: 60px 0;
  color: #94a3b8;
  font-size: 0.9rem;
  border-top: 1px solid #f1f5f9;
}

.dim { opacity: 0.6; }

/* --- ANIMACIONES --- */
@keyframes slideUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}

/* Responsive */
@media (max-width: 768px) {
  .hero-title { font-size: 3rem; }
  .grid { grid-template-columns: 1fr; }
  .nav-links { display: none; } /* Idealmente aquí agregarías un menú móvil */
}
</style>