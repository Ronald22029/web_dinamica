<template>
  <div class="main-wrapper">
    
    <nav class="navbar" :class="{ 'scrolled': isScrolled }">
      <div class="container nav-content">
        <a href="/" class="brand">
          <span>EL<span class="highlight">.EDEN</span></span>
        </a>
        <a href="/" class="btn-back">
          <span class="icon-arrow">←</span> <span>Volver</span>
        </a>
      </div>
    </nav>

    <div class="bg-decoration"></div>

    <article class="article-layout container">
      
      <!-- Breadcrumb para SEO -->
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="/">Inicio</a>
        <span class="separator">/</span>
        <a :href="'/categoria/' + post.category">{{ post.category.charAt(0).toUpperCase() + post.category.slice(1) }}</a>
        <span class="separator">/</span>
        <span class="current">{{ post.title }}</span>
      </nav>

      <header class="card header-card">
        <div class="meta-row">
          <a :href="'/categoria/' + post.category" class="category-pill">{{ post.category }}</a>
          <span class="date">{{ formatDate(post.created_at) }}</span>
        </div>
        
        <h1 class="title">{{ post.title }}</h1>
        <p class="excerpt">{{ post.excerpt }}</p>
      </header>

      <div class="multimedia-section video-wrapper" v-if="post.video_url" :class="{ 'video-vertical': isVertical(post.video_url) }">
        <iframe 
          :src="getEmbedUrl(post.video_url)" 
          frameborder="0" 
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
          allowfullscreen
          class="video-iframe">
        </iframe>
      </div>

      <div class="multimedia-section" v-else-if="post.image">
        <img 
          :src="post.image" 
          :alt="post.title" 
          class="hero-image"
        >
      </div>

      <div class="card content-card">
        <div class="prose" v-html="post.content"></div>

        <div class="share-section">
          <h2 class="share-title">¿Te gustó? ¡Compártelo!</h2>
          <div class="share-buttons">
            
            <button @click="share('whatsapp')" class="btn-share whatsapp" title="Compartir en WhatsApp">
              <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.232-.298.347-.497.115-.198.058-.372-.03-.545-.089-.174-.793-1.912-1.087-2.617-.29-.697-.588-.602-.806-.613-.205-.01-.44-.012-.675-.012-.236 0-.619.088-.943.435-.325.347-1.24 1.211-1.24 2.953 0 1.742 1.268 3.424 1.444 3.664.177.241 2.497 3.813 6.049 5.347 2.378 1.027 3.328 1.096 4.532 1.02 1.32-.083 2.712-.556 3.094-1.636.382-1.08.382-2.007.268-2.196-.114-.19-.412-.303-.709-.452zM12 21.603c-1.753 0-3.473-.464-5.003-1.346l-.358-.207-3.722.977.994-3.629-.234-.372C2.81 15.356 2.053 13.518 2.053 11.603 2.053 6.118 6.516 1.655 12 1.655s9.947 4.463 9.947 9.948c0 5.485-4.462 9.948-9.947 9.948z"/></svg>
            </button>

            <button @click="share('facebook')" class="btn-share facebook" title="Compartir en Facebook">
              <svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </button>

            <button @click="share('twitter')" class="btn-share twitter" title="Compartir en X">
              <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </button>

            <button @click="share('linkedin')" class="btn-share linkedin" title="Compartir en LinkedIn">
              <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
            </button>

            <button @click="copyLink" class="btn-share copy" title="Copiar Enlace">
              <span v-if="copied">¡Copiado!</span>
              <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
            </button>

          </div>
        </div>
      </div>

      <!-- Artículos Relacionados para SEO (enlaces internos) -->
      <div v-if="relatedPosts.length > 0" class="card related-section">
        <h2 class="related-title">Artículos relacionados</h2>
        <div class="related-grid">
          <a v-for="rp in relatedPosts" :key="rp.id" :href="'/post/' + rp.id" class="related-card">
            <img :src="getThumbnail(rp)" :alt="rp.title" class="related-img">
            <div class="related-info">
              <span class="related-category">{{ rp.category }}</span>
              <h3>{{ rp.title }}</h3>
            </div>
          </a>
        </div>
      </div>

      <div class="footer-actions">
        <a href="/" class="btn-outline">Ver más artículos</a>
      </div>

    </article>

    <footer-section></footer-section>

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  initialData: Object
});

const post = ref(props.initialData.post);
const relatedPosts = ref(props.initialData.related_posts || []);
const isScrolled = ref(false);
const copied = ref(false);

const isVertical = (url) => {
  if (!url) return false;
  return url.includes('tiktok.com') || url.includes('/shorts/') || url.includes('/reel/');
};

const getThumbnail = (p) => {
  if (p.image) return p.image;
  if (p.video_url) {
    const url = p.video_url;
    if (url.includes('youtube.com') || url.includes('youtu.be')) {
      let videoId = '';
      if (url.includes('/shorts/')) {
        videoId = url.split('/shorts/')[1].split('?')[0];
      } else {
        videoId = url.split('v=')[1] || url.split('/').pop();
      }
      const cleanId = videoId ? videoId.split('&')[0].split('?')[0] : null;
      if (cleanId) return `https://img.youtube.com/vi/${cleanId}/hqdefault.jpg`;
    }
    if (url.includes('dai.ly') || url.includes('dailymotion.com')) {
      const parts = url.split('/');
      const videoId = parts[parts.length - 1].split('?')[0];
      if (videoId) return `https://www.dailymotion.com/thumbnail/video/${videoId}`;
    }
    return 'https://ui-avatars.com/api/?name=Video&background=1e293b&color=fff&size=512';
  }
  return 'https://via.placeholder.com/400x250?text=Sin+Imagen';
};

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20;
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('es-ES', { 
    day: 'numeric', 
    month: 'long', 
    year: 'numeric' 
  }).format(date);
};

const getEmbedUrl = (url) => {
  if (!url) return '';
  if (url.includes('youtube.com') || url.includes('youtu.be')) {
    let videoId = '';
    if (url.includes('/shorts/')) {
       videoId = url.split('/shorts/')[1].split('?')[0];
    } else {
       videoId = url.split('v=')[1] || url.split('/').pop();
    }
    const cleanId = videoId ? videoId.split('&')[0].split('?')[0] : '';
    return `https://www.youtube.com/embed/${cleanId}`;
  }
  if (url.includes('dai.ly') || url.includes('dailymotion.com')) {
    const parts = url.split('/');
    const videoId = parts[parts.length - 1].split('?')[0];
    return `https://www.dailymotion.com/embed/video/${videoId}`;
  }
  if (url.includes('tiktok.com')) {
    if (url.includes('/video/')) {
       const videoId = url.split('/video/')[1].split('?')[0];
       return `https://www.tiktok.com/embed/v2/${videoId}`;
    }
    return url; 
  }
  if (url.includes('facebook.com') || url.includes('fb.watch')) {
      return `https://www.facebook.com/plugins/video.php?href=${encodeURIComponent(url)}&show_text=0&width=560`;
  }
  return url;
};

// --- FUNCIÓN DE COMPARTIR ---
const share = (platform) => {
  const url = encodeURIComponent(window.location.href);
  const text = encodeURIComponent(post.value.title);
  let shareUrl = '';

  switch (platform) {
    case 'whatsapp':
      shareUrl = `https://wa.me/?text=${text}%20${url}`;
      break;
    case 'facebook':
      shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
      break;
    case 'twitter':
      shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${text}`;
      break;
    case 'linkedin':
      shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
      break;
  }

  window.open(shareUrl, '_blank', 'width=600,height=400');
};

const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(window.location.href);
    copied.value = true;
    setTimeout(() => copied.value = false, 2000);
  } catch (err) {
    alert('No se pudo copiar el enlace');
  }
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&family=Merriweather:ital,wght@0,300;0,400;0,700;1,300&display=swap');

:host {
  font-family: 'Plus Jakarta Sans', sans-serif;
  color: #1e293b;
}

.main-wrapper {
  background-color: #f1f5f9;
  min-height: 100vh;
  position: relative;
  padding-bottom: 40px;
}

.bg-decoration {
  position: absolute;
  top: 0; left: 0; width: 100%; height: 500px;
  background: radial-gradient(circle at 50% 0%, rgba(59, 130, 246, 0.08), transparent 70%);
  z-index: 0; pointer-events: none;
}

.container { max-width: 900px; margin: 0 auto; padding: 0 20px; }

/* NAVBAR */
.navbar {
  position: fixed; top: 0; left: 0; width: 100%; padding: 15px 0; z-index: 100;
  transition: all 0.3s ease; background: rgba(241, 245, 249, 0.8); backdrop-filter: blur(10px);
}
.navbar.scrolled {
  background: rgba(255, 255, 255, 0.9); box-shadow: 0 4px 20px rgba(0,0,0,0.03);
}
.nav-content { display: flex; justify-content: space-between; align-items: center; }
.brand { font-weight: 800; font-size: 1.3rem; text-decoration: none; color: #0f172a; }
.highlight { color: #3b82f6; }
.btn-back { text-decoration: none; color: #64748b; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 6px; }
.btn-back:hover { color: #0f172a; }
.icon-arrow { font-size: 1.2rem; margin-top: -2px; }

/* LAYOUT */
.article-layout { position: relative; z-index: 1; padding-top: 100px; display: flex; flex-direction: column; gap: 30px; }
.card { background: #ffffff; border-radius: 24px; padding: 40px; box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04); border: 1px solid rgba(255, 255, 255, 0.5); }

/* HEADER */
.header-card { text-align: center; padding: 50px 40px; }
.meta-row { display: flex; justify-content: center; gap: 15px; margin-bottom: 24px; }
.category-pill { background: #eff6ff; color: #2563eb; padding: 6px 16px; border-radius: 100px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
.date { color: #94a3b8; font-size: 0.9rem; }
.title { font-size: 2.8rem; line-height: 1.15; font-weight: 800; color: #1e293b; margin-bottom: 20px; }
.excerpt { font-size: 1.25rem; line-height: 1.6; color: #64748b; max-width: 80%; margin: 0 auto; }

/* MULTIMEDIA */
.multimedia-section { width: 100%; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.08); margin-top: -10px; margin-bottom: -10px; z-index: 2; background: #000; }
.hero-image { width: 100%; height: auto; max-height: 500px; object-fit: cover; display: block; }
.video-wrapper { position: relative; padding-bottom: 56.25%; height: 0; }
.video-wrapper.video-vertical { padding-bottom: 177.77%; max-width: 400px; margin: 0 auto; }
.video-iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }

/* CONTENT & SHARE */
.content-card { padding: 50px 60px; }
.prose { font-family: 'Merriweather', serif; font-size: 1.15rem; line-height: 1.9; color: #334155; }
.prose :deep(p) { margin-bottom: 1.6em; }
.prose :deep(img) { border-radius: 12px; max-width: 100%; height: auto; }

/* ESTILOS DE COMPARTIR */
.share-section { margin-top: 60px; padding-top: 40px; border-top: 1px solid #f1f5f9; text-align: center; }
.share-title { font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-bottom: 20px; }
.share-buttons { display: flex; justify-content: center; gap: 15px; flex-wrap: wrap; }

.btn-share {
  width: 50px; height: 50px; border-radius: 50%; border: none; cursor: pointer;
  display: flex; align-items: center; justify-content: center; transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  color: white; font-size: 1.2rem;
}
.btn-share:hover { transform: translateY(-5px) scale(1.1); box-shadow: 0 10px 20px rgba(0,0,0,0.15); }
.btn-share svg { width: 22px; height: 22px; }

.whatsapp { background: #25D366; }
.facebook { background: #1877F2; }
.twitter { background: #000000; }
.linkedin { background: #0A66C2; }
.copy { background: #64748b; width: auto; padding: 0 20px; border-radius: 50px; gap: 8px; font-weight: 600; font-size: 0.9rem; }

/* FOOTER */
.footer-actions { text-align: center; margin-top: 20px; }
.btn-outline { display: inline-block; padding: 12px 32px; border: 2px solid #cbd5e1; border-radius: 50px; color: #475569; font-weight: 700; text-decoration: none; transition: all 0.2s; }
.btn-outline:hover { border-color: #0f172a; color: #0f172a; background: white; transform: translateY(-2px); }
.modern-footer { text-align: center; padding: 60px 0 20px; color: #94a3b8; font-size: 0.85rem; }

/* BREADCRUMB */
.breadcrumb {
  display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
  font-size: 0.85rem; font-weight: 500; margin-bottom: 10px;
}
.breadcrumb a { color: #3b82f6; text-decoration: none; }
.breadcrumb a:hover { text-decoration: underline; }
.breadcrumb .separator { color: #94a3b8; }
.breadcrumb .current { color: #94a3b8; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 300px; }
.category-pill { text-decoration: none; display: inline-block; }
.category-pill:hover { background: #dbeafe; }

/* RELATED POSTS */
.related-section { padding: 40px; }
.related-title {
  font-size: 1.3rem; font-weight: 800; color: #0f172a; margin-bottom: 24px;
  position: relative; display: inline-block;
}
.related-title::after {
  content: ''; position: absolute; bottom: -6px; left: 0;
  width: 40px; height: 3px; background: linear-gradient(90deg, #3b82f6, #8b5cf6); border-radius: 2px;
}
.related-grid {
  display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;
}
.related-card {
  display: flex; flex-direction: column; border-radius: 16px; overflow: hidden;
  background: #f8fafc; text-decoration: none; color: inherit;
  transition: transform 0.3s, box-shadow 0.3s; border: 1px solid #f1f5f9;
}
.related-card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.06); }
.related-img { width: 100%; height: 160px; object-fit: cover; }
.related-info { padding: 16px; }
.related-category {
  font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #2563eb;
  background: #eff6ff; padding: 3px 10px; border-radius: 100px;
}
.related-info h3 {
  font-size: 1rem; margin-top: 10px; font-weight: 700; color: #1e293b;
  line-height: 1.3;
}

@media (max-width: 768px) {
  .title { font-size: 2rem; }
  .content-card { padding: 30px 24px; }
  .related-grid { grid-template-columns: 1fr; }
}
</style>