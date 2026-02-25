<template>
  <div class="main-wrapper">
    
    <nav class="navbar" :class="{ 'scrolled': isScrolled }">
      <div class="container nav-content">
        
        <a href="/" class="brand">
          <div class="logo-container">
            <img :src="'/images/logo.jpg'" alt="Logo Web Viral" class="nav-logo">
          </div>
          <span>EL<span class="highlight">.EDEN</span></span>
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

      <!-- H1 siempre presente para SEO -->
      <h1 v-if="pageData.carousel_posts && pageData.carousel_posts.length > 0" class="sr-only">{{ pageData.hero_title || 'ELEDEN – Tecnología, Eventos y Portafolio' }}</h1>
      
      <div v-if="pageData.carousel_posts && pageData.carousel_posts.length > 0" class="carousel-container">
        <div class="carousel-track" :style="{ transform: `translateX(-${currentSlide * 100}%)` }">
          <div v-for="slide in pageData.carousel_posts" :key="slide.id" class="carousel-slide">
            
            <a :href="'/post/' + slide.id" class="slide-link">
              
              <img :src="getThumbnail(slide)" :alt="slide.title" class="slide-bg">
              
              <div class="slide-content glass-panel">
                <span class="slide-badge">{{ slide.category }}</span>
                <h2>{{ slide.title }}</h2>
                <p>{{ slide.excerpt }}</p>
                <span class="read-more-btn">Leer artículo →</span>
              </div>

            </a>

          </div>
        </div>

        <button @click="prevSlide" class="nav-btn prev" aria-label="Slide anterior">❮</button>
        <button @click="nextSlide" class="nav-btn next" aria-label="Slide siguiente">❯</button>
        
        <div class="indicators">
          <span v-for="(slide, index) in pageData.carousel_posts" 
                :key="index" 
                :class="{ active: currentSlide === index }"
                @click="currentSlide = index"
                :aria-label="'Ir al slide ' + (index + 1)"></span>
        </div>
      </div>

      <div v-else class="container hero-content">
        <nav class="breadcrumb" v-if="pageData.current_category !== 'home'" aria-label="Breadcrumb">
          <a href="/">Inicio</a>
          <span class="separator">/</span>
          <span class="current">{{ pageData.current_category.charAt(0).toUpperCase() + pageData.current_category.slice(1) }}</span>
        </nav>
        <h1 class="hero-title">{{ pageData.hero_title }}</h1>
        <p class="hero-subtitle">{{ pageData.hero_text }}</p>
        
        <div class="hero-actions" v-if="pageData.current_category === 'home'">
          <a href="/categoria/portafolio" class="btn-primary">Ver Proyectos</a>
        </div>
      </div>
    </header>

    <section id="content" class="container content-section">
      <h2 class="section-heading">{{ pageData.current_category === 'home' ? 'Últimas publicaciones' : 'Artículos de ' + pageData.current_category.charAt(0).toUpperCase() + pageData.current_category.slice(1) }}</h2>

      <div v-if="pageData.posts.length === 0" class="empty-state">
        <div class="empty-icon">📭</div>
        <p>No hay publicaciones recientes en esta sección.</p>
      </div>

      <div v-else class="grid">
        <article v-for="(post, index) in pageData.posts" :key="post.id" 
                 class="card" :style="{ animationDelay: `${index * 100}ms` }">
          
          <div class="card-media">
            
            <div v-if="post.video_url && playingVideoId === post.id" class="video-container">
               <iframe 
                  :src="getEmbedUrl(post.video_url)" 
                  :title="post.title"
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                  allowfullscreen>
               </iframe>
            </div>

            <div v-else class="media-wrapper" 
                 @click="post.video_url ? playVideo(post.id) : null"
                 :class="{ 'is-video': post.video_url }">
              
              <img :src="getThumbnail(post)" :alt="post.title" :class="{ 'video-thumbnail': post.video_url }">
              
              <div class="card-overlay">
                <a :href="'/categoria/' + post.category" class="category-tag">{{ post.category }}</a>
              </div>

              <div v-if="post.video_url" class="play-icon-overlay">▶</div>
            </div>

          </div>

          <div class="card-content">
            <h3><a :href="'/post/' + post.id" class="card-title-link">{{ post.title }}</a></h3>
            <p>{{ post.excerpt }}</p>
            <div class="card-footer">
              <a :href="'/categoria/' + post.category" class="category-link">{{ post.category }}</a>
              <a :href="'/post/' + post.id" class="link-arrow">Leer más <span class="arrow">→</span></a>
            </div>
          </div>
        </article>
      </div>
    </section>

    <footer-section></footer-section>

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  initialData: Object
});

const pageData = ref(props.initialData);
const isScrolled = ref(false);
const currentSlide = ref(0);
const playingVideoId = ref(null);
let interval = null;

const playVideo = (id) => { playingVideoId.value = id; };

const getThumbnail = (post) => {
  if (post.image) return post.image;
  if (post.video_url) {
    if (post.video_url.includes('youtube.com') || post.video_url.includes('youtu.be')) {
      const videoId = post.video_url.split('v=')[1] || post.video_url.split('/').pop();
      const cleanId = videoId ? videoId.split('&')[0].split('?')[0] : null;
      if (cleanId) return `https://img.youtube.com/vi/${cleanId}/hqdefault.jpg`;
    }
    return 'https://ui-avatars.com/api/?name=Play+Video&background=1e293b&color=fff&size=512&font-size=0.33';
  }
  return 'https://via.placeholder.com/600x400?text=Sin+Imagen';
};

const getEmbedUrl = (url) => {
  if (!url) return '';
  if (url.includes('youtube.com') || url.includes('youtu.be')) {
    const videoId = url.split('v=')[1] || url.split('/').pop();
    const cleanId = videoId ? videoId.split('&')[0].split('?')[0] : '';
    return `https://www.youtube.com/embed/${cleanId}?autoplay=1`;
  }
  if (url.includes('facebook.com')) {
      return `https://www.facebook.com/plugins/video.php?href=${encodeURIComponent(url)}&show_text=0&width=560&autoplay=1`;
  }
  return url;
};

const handleScroll = () => { isScrolled.value = window.scrollY > 20; };

const nextSlide = () => {
  if (!pageData.value.carousel_posts || pageData.value.carousel_posts.length === 0) return;
  currentSlide.value = (currentSlide.value + 1) % pageData.value.carousel_posts.length;
};

const prevSlide = () => {
  if (!pageData.value.carousel_posts || pageData.value.carousel_posts.length === 0) return;
  currentSlide.value = (currentSlide.value - 1 + pageData.value.carousel_posts.length) % pageData.value.carousel_posts.length;
};

onMounted(() => {
  document.title = pageData.value.meta_title || 'ELEDEN';
  window.addEventListener('scroll', handleScroll);
  if (pageData.value.carousel_posts && pageData.value.carousel_posts.length > 0) {
    interval = setInterval(nextSlide, 5000);
  }
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
  if (interval) clearInterval(interval);
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;500;700;800&display=swap');

.main-wrapper {
  font-family: 'Plus Jakarta Sans', sans-serif;
  color: #1e293b;
  background-color: #f8fafc;
  min-height: 100vh;
  width: 100%;
  overflow-x: hidden; 
  position: relative;
}

.container {
  max-width: 1200px;
  width: 100%;
  margin: 0 auto;
  padding: 0 24px;
  box-sizing: border-box;
}

.navbar {
  position: fixed;
  top: 0; left: 0; width: 100%;
  z-index: 1000;
  padding: 20px 0;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.navbar.scrolled {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(12px);
  padding: 12px 0;
  border-bottom: 1px solid rgba(0,0,0,0.05);
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
}

.nav-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
}

.brand {
  display: flex; align-items: center; gap: 12px;
  font-size: 1.5rem; font-weight: 800; text-decoration: none; color: #0f172a;
}
.logo-container {
  overflow: hidden; border-radius: 12px;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2); transition: transform 0.3s ease;
}
.nav-logo {
  height: 45px; width: auto; display: block; object-fit: cover;
}
.highlight { color: #3b82f6; }

/* LINKS */
.nav-links { display: flex; gap: 32px; list-style: none; padding: 0; margin: 0; }
.nav-links a {
  text-decoration: none; color: #64748b; font-weight: 600; font-size: 0.95rem;
  transition: color 0.3s; position: relative;
}
.nav-links a:hover, .nav-links a.active { color: #0f172a; }
.nav-links a::after {
  content: ''; position: absolute; bottom: -4px; left: 0; width: 0%; height: 2px;
  background: #3b82f6; transition: width 0.3s ease;
}
.nav-links a.active::after { width: 100%; }

/* HERO */
.hero-section {
  position: relative; padding-top: 140px; padding-bottom: 80px; text-align: center;
}
.aurora-bg {
  position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
  background: radial-gradient(circle at 50% 50%, rgba(59, 130, 246, 0.15), transparent 50%),
              radial-gradient(circle at 80% 20%, rgba(139, 92, 246, 0.15), transparent 40%);
  animation: auroraMove 15s infinite alternate linear;
  z-index: 0; filter: blur(60px);
}
@keyframes auroraMove {
  0% { transform: translate(0, 0) rotate(0deg); }
  100% { transform: translate(-10%, 10%) rotate(5deg); }
}

/* CARRUSEL Y ENLACE DE SLIDE */
.carousel-container {
  position: relative; width: 95%; max-width: 1100px; height: 500px; margin: 0 auto;
  border-radius: 24px; overflow: hidden;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); z-index: 1;
}
.carousel-track { display: flex; height: 100%; transition: transform 0.7s cubic-bezier(0.25, 1, 0.5, 1); }
.carousel-slide { min-width: 100%; position: relative; }

/* NUEVO: ESTILO PARA EL LINK DEL CARRUSEL */
.slide-link {
  display: block; width: 100%; height: 100%; text-decoration: none; color: inherit;
  cursor: pointer;
}

.slide-bg { width: 100%; height: 100%; object-fit: cover; filter: brightness(0.75); transition: transform 1s ease; }
.slide-link:hover .slide-bg { transform: scale(1.05); filter: brightness(0.65); }

.glass-panel {
  position: absolute; bottom: 50px; left: 50px; max-width: 500px; padding: 32px;
  border-radius: 20px; color: white; background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.2);
  text-align: left; animation: slideUp 0.8s ease-out;
  transition: transform 0.3s;
}
.slide-link:hover .glass-panel { transform: translateY(-5px); background: rgba(255,255,255,0.15); }

.slide-badge { background: #3b82f6; padding: 6px 12px; border-radius: 6px; font-weight: bold; font-size: 0.8rem; text-transform: uppercase; }
.glass-panel h2 { margin: 15px 0 10px; font-size: 2.2rem; font-weight: 800; line-height: 1.1; }
.read-more-btn { font-weight: 700; color: #93c5fd; font-size: 0.9rem; margin-top: 10px; display: inline-block; }

.nav-btn {
  position: absolute; top: 50%; transform: translateY(-50%);
  background: rgba(255,255,255,0.2); color: white; border: none;
  width: 50px; height: 50px; border-radius: 50%; font-size: 1.5rem; cursor: pointer; transition: 0.3s; z-index: 2;
}
.nav-btn:hover { background: rgba(255,255,255,0.4); }
.prev { left: 20px; } .next { right: 20px; }
.indicators { position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; z-index: 2; }
.indicators span { width: 8px; height: 8px; background: rgba(255,255,255,0.4); border-radius: 50%; cursor: pointer; }
.indicators span.active { background: white; transform: scale(1.4); }

/* CARDS */
.hero-title { font-size: 4rem; margin-bottom: 20px; font-weight: 800; background: linear-gradient(135deg, #0f172a 0%, #334155 100%); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
.hero-subtitle { font-size: 1.25rem; color: #64748b; margin-bottom: 30px; }
.badge-pill { background: rgba(59, 130, 246, 0.1); color: #2563eb; padding: 8px 16px; border-radius: 100px; font-weight: 700; margin-bottom: 20px; display: inline-block; }
.btn-primary { background: #0f172a; color: white; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: 600; }

.content-section { padding-bottom: 120px; margin-top: 60px; }
.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 40px; }
.card { background: white; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); transition: all 0.4s ease; animation: fadeIn 0.8s ease-out backwards; }
.card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px -5px rgba(0,0,0,0.08); }

.card-media { height: 240px; position: relative; overflow: hidden; background: #000; }
.media-wrapper { width: 100%; height: 100%; position: relative; }
.media-wrapper.is-video { cursor: pointer; }
.media-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease; }
.card:hover .media-wrapper img { transform: scale(1.08); }

.video-container { width: 100%; height: 100%; }
.video-container iframe { width: 100%; height: 100%; border: none; }
.video-thumbnail { opacity: 0.9; }

.play-icon-overlay {
  position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
  width: 50px; height: 50px; background: rgba(0,0,0,0.6);
  border-radius: 50%; display: flex; justify-content: center; align-items: center;
  color: white; font-size: 1.5rem; border: 2px solid white; pointer-events: none;
  backdrop-filter: blur(2px); transition: transform 0.3s;
}
.media-wrapper:hover .play-icon-overlay { transform: translate(-50%, -50%) scale(1.1); background: rgba(255, 0, 0, 0.8); border-color: red; }

.category-tag { position: absolute; bottom: 16px; left: 16px; background: rgba(255,255,255,0.9); padding: 6px 14px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
.card-content { padding: 32px; }
.card-content h3 { font-size: 1.35rem; margin-bottom: 12px; font-weight: 700; }
.link-arrow { text-decoration: none; color: #3b82f6; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }

.modern-footer { text-align: center; padding: 60px 0; color: #94a3b8; font-size: 0.9rem; border-top: 1px solid #f1f5f9; }
.dim { opacity: 0.6; }
.empty-state { text-align: center; color: #64748b; margin-top: 50px; }

/* SR-ONLY para H1 invisible pero accesible */
.sr-only {
  position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
  overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;
}

/* SECTION HEADING */
.section-heading {
  font-size: 1.8rem; font-weight: 800; color: #0f172a; margin-bottom: 40px;
  position: relative; display: inline-block;
}
.section-heading::after {
  content: ''; position: absolute; bottom: -8px; left: 0;
  width: 50px; height: 4px; background: linear-gradient(90deg, #3b82f6, #8b5cf6); border-radius: 2px;
}

/* BREADCRUMB */
.breadcrumb {
  display: flex; align-items: center; gap: 8px; margin-bottom: 20px;
  font-size: 0.9rem; font-weight: 500;
}
.breadcrumb a { color: #3b82f6; text-decoration: none; }
.breadcrumb a:hover { text-decoration: underline; }
.breadcrumb .separator { color: #94a3b8; }
.breadcrumb .current { color: #64748b; }

/* CARD FOOTER & CATEGORY LINK */
.card-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 12px; }
.category-link {
  background: #eff6ff; color: #2563eb; padding: 4px 12px; border-radius: 100px;
  font-size: 0.75rem; font-weight: 700; text-transform: uppercase; text-decoration: none;
  transition: background 0.2s;
}
.category-link:hover { background: #dbeafe; }
.card-title-link { text-decoration: none; color: inherit; }
.card-title-link:hover { color: #3b82f6; }
.category-tag { text-decoration: none; color: inherit; }

@keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
@keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }

@media (max-width: 768px) {
  .hero-title { font-size: 2.5rem; }
  .grid { grid-template-columns: 1fr; }
  .nav-links { display: none; }
}
</style>