<template>
  <div class="boda-template" :style="cssVars" v-if="invitation">
    

    <!-- HEADER / HERO -->
    <header class="hero">
      <div class="hero-overlay"></div>
      <img v-if="coverImage" :src="coverImage" alt="Portada de la Boda" class="hero-bg">
      <div class="hero-content">
        <div class="hero-ornament">
          <svg width="100" height="20" viewBox="0 0 100 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 10H40" :stroke="themeColor" stroke-width="1.5"/>
            <circle cx="50" cy="10" r="3" :fill="themeColor"/>
            <path d="M60 10H100" :stroke="themeColor" stroke-width="1.5"/>
          </svg>
        </div>
        <h3 class="pre-title" :style="{ color: themeColor }">NOS CASAMOS</h3>
        <h1 class="names">{{ bride }}<br><span class="ampersand" :style="{ color: themeColor }">&</span><br>{{ groom }}</h1>
        <div class="hero-ornament">
          <svg width="100" height="20" viewBox="0 0 100 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 10H40" :stroke="themeColor" stroke-width="1.5"/>
            <path d="M45 5L50 15L55 5" :stroke="themeColor" stroke-width="1.5" fill="none"/>
            <path d="M60 10H100" :stroke="themeColor" stroke-width="1.5"/>
          </svg>
        </div>
        <div class="date-wrapper">
          <div class="date-line" :style="{ backgroundColor: themeColor }"></div>
          <p class="date">{{ formattedDate }}</p>
          <div class="date-line" :style="{ backgroundColor: themeColor }"></div>
        </div>
      </div>
      <div class="scroll-down">
        <span>Desliza hacia abajo</span>
        <div class="arrow">↓</div>
      </div>
    </header>



    <!-- INVITATION MESSAGE -->
    <section class="section message-section">
      <div class="container text-center">
        <div class="ornament-top" style="margin-bottom: 20px;">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" :stroke="themeColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
        </div>
        <h2 class="section-title">Estás Invitado</h2>
        <p class="message-text">
          {{ invitation.description || 'Tenemos el honor de invitarte a celebrar nuestra unión matrimonial. Tu presencia es el mejor regalo que podemos recibir.' }}
        </p>
      </div>
    </section>

    <!-- PADRINOS SECTION -->
    <section class="section padrinos-section bg-light-pattern" v-if="padrinos && padrinos.length">
      <div class="container text-center">
        <h2 class="section-title">Nuestros Padrinos</h2>
        <p class="text-muted" style="margin-bottom: 50px; font-style: italic; font-size: 1.2rem;">Agradecemos su guía y compañía en este día tan especial.</p>
        
        <div class="padrinos-grid">
          <div v-for="(pad, index) in padrinos" :key="index" class="padrino-card">
            <div class="padrino-icon" :style="{ backgroundColor: themeColor + '20', color: themeColor }">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
              </svg>
            </div>
            <h4 class="padrino-role" :style="{ color: themeColor }">{{ pad.role }}</h4>
            <div class="padrino-divider"></div>
            <h3 class="padrino-name">{{ pad.name }}</h3>
          </div>
        </div>
      </div>
    </section>

    <!-- LOCATIONS -->
    <section class="section locations-section bg-light">
      <div class="container">
        <h2 class="section-title text-center">¿Dónde nos encontramos?</h2>
        <div class="grid-2">
          
          <div class="location-card" v-if="ceremonyLocation">
            <div class="loc-card-top" :style="{ background: themeColor + '15', borderColor: themeColor + '40' }">
              <div class="loc-icon" :style="{ color: themeColor }">⛪</div>
              <div class="loc-details">
                <h3 :style="{ color: themeColor }">Ceremonia</h3>
                <p class="loc-time" v-if="ceremonyTime">🕔 {{ ceremonyTime }}</p>
                <p class="address">{{ ceremonyLocation }}</p>
              </div>
            </div>
            <div class="loc-card-actions">
              <button @click="openMap('ceremony')" class="btn-map-inline" :style="{ borderColor: themeColor, color: themeColor }">
                🗺 Ver Mapa
              </button>
              <a v-if="ceremonyMap && ceremonyMap !== '#'" :href="ceremonyMap" target="_blank" class="btn-how-to-get" :style="{ background: themeColor }">
                🚦 Cómo llegar
              </a>
            </div>
          </div>

          <div class="location-card" v-if="receptionLocation">
            <div class="loc-card-top" :style="{ background: themeColor + '15', borderColor: themeColor + '40' }">
              <div class="loc-icon" :style="{ color: themeColor }">🥂</div>
              <div class="loc-details">
                <h3 :style="{ color: themeColor }">Recepción</h3>
                <p class="loc-time" v-if="receptionTime">🕔 {{ receptionTime }}</p>
                <p class="address">{{ receptionLocation }}</p>
              </div>
            </div>
            <div class="loc-card-actions">
              <button @click="openMap('reception')" class="btn-map-inline" :style="{ borderColor: themeColor, color: themeColor }">
                🗺 Ver Mapa
              </button>
              <a v-if="receptionMap && receptionMap !== '#'" :href="receptionMap" target="_blank" class="btn-how-to-get" :style="{ background: themeColor }">
                🚦 Cómo llegar
              </a>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- MAP MODAL -->
    <teleport to="body">
      <transition name="map-fade">
        <div v-if="mapModal.show" class="map-modal-overlay" @click.self="mapModal.show = false">
          <div class="map-modal">
            <div class="map-modal-header">
              <div>
                <p class="map-modal-type">{{ mapModal.type === 'ceremony' ? '⛪ Ceremonia' : '🥂 Recepción' }}</p>
                <h3 class="map-modal-place">{{ mapModal.place }}</h3>
              </div>
              <button @click="mapModal.show = false" class="map-modal-close">×</button>
            </div>
            <div class="map-embed-container">
              <iframe
                :src="mapModal.embedUrl"
                width="100%" height="100%"
                frameborder="0" style="border:0"
                allowfullscreen loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
              ></iframe>
            </div>
            <div class="map-modal-footer">
              <a :href="mapModal.directUrl" target="_blank" class="btn-directions">
                🚦 Abrir en Google Maps para navegar
              </a>
            </div>
          </div>
        </div>
      </transition>
    </teleport>


    <!-- ITINERARY -->
    <section class="section itinerary-section" v-if="itinerary && itinerary.length">
      <div class="container">
        <h2 class="section-title text-center">Itinerario</h2>
        <div class="timeline" ref="timeline">
          
          <div v-for="(item, index) in itinerary" :key="index" class="timeline-item timeline-animate">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
              <h4>{{ item.time }}</h4>
              <h3>{{ item.activity }}</h3>
              <p>{{ item.description }}</p>
            </div>
          </div>
          
        </div>
      </div>
    </section>

    <!-- GALLERY -->
    <section class="section gallery-section" v-if="invitation.gallery_images && invitation.gallery_images.length">
      <div class="container">
        <h2 class="section-title text-center">Nuestros Momentos</h2>
        <div class="photo-grid">
          <div v-for="(img, index) in invitation.gallery_images" :key="index" class="photo-item">
            <img :src="img" alt="Foto de la galería">
          </div>
        </div>
      </div>
    </section>



    <footer class="footer" :style="{ backgroundColor: themeColor }">
      <p>Te esperamos para celebrar juntos el amor.</p>
      <p class="names-small">{{ bride }} & {{ groom }}</p>
    </footer>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  invitation: { type: Object, required: true }
});

const data = computed(() => props.invitation.data || {});

// Nombres
const bride = computed(() => data.value.bride || 'Novia');
const groom = computed(() => data.value.groom || 'Novio');

// Tema
const themeColor = computed(() => data.value.theme_color || '#ba966a');

const cssVars = computed(() => {
  return {
    '--theme-color': themeColor.value
  }
});

// Portada
const coverImage = computed(() => {
  return props.invitation.gallery_images && props.invitation.gallery_images.length > 0
    ? props.invitation.gallery_images[0]
    : 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80';
});

// Fechas y formateo
const formattedDate = computed(() => {
  if (!props.invitation.event_date) return '';
  const date = new Date(props.invitation.event_date);
  return new Intl.DateTimeFormat('es-ES', { 
    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
  }).format(date).toUpperCase();
});

// Cuenta regresiva
const countdown = ref({ days: '00', hours: '00', minutes: '00', seconds: '00' });
let timer = null;

const updateCountdown = () => {
  if (!props.invitation.event_date) return;
  const eventTime = new Date(props.invitation.event_date).getTime();
  const now = new Date().getTime();
  const distance = eventTime - now;

  if (distance < 0) {
    countdown.value = { days: '00', hours: '00', minutes: '00', seconds: '00' };
    return;
  }

  const d = Math.floor(distance / (1000 * 60 * 60 * 24));
  const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  const s = Math.floor((distance % (1000 * 60)) / 1000);

  countdown.value = {
    days: d.toString().padStart(2, '0'),
    hours: h.toString().padStart(2, '0'),
    minutes: m.toString().padStart(2, '0'),
    seconds: s.toString().padStart(2, '0')
  };
};

onMounted(() => {
  updateCountdown();
  timer = setInterval(updateCountdown, 1000);
});
onUnmounted(() => { if (timer) clearInterval(timer); });

// Ubicaciones
const ceremonyLocation = computed(() => data.value.ceremony_location || '');
const ceremonyTime = computed(() => {
  const t = data.value.ceremony_time || '';
  if (!t) return '';
  if (t.includes(':') && t.length <= 5) {
    const [h, m] = t.split(':').map(Number);
    const period = h >= 12 ? 'PM' : 'AM';
    const h12 = h % 12 || 12;
    return `${h12}:${String(m).padStart(2,'0')} ${period}`;
  }
  return t;
});
const ceremonyMap = computed(() => data.value.ceremony_map || '#');

const receptionLocation = computed(() => data.value.reception_location || '');
const receptionTime = computed(() => {
  const t = data.value.reception_time || '';
  if (!t) return '';
  if (t.includes(':') && t.length <= 5) {
    const [h, m] = t.split(':').map(Number);
    const period = h >= 12 ? 'PM' : 'AM';
    const h12 = h % 12 || 12;
    return `${h12}:${String(m).padStart(2,'0')} ${period}`;
  }
  return t;
});
const receptionMap = computed(() => data.value.reception_map || '#');

// -- MAP MODAL --
const mapModal = ref({ show: false, type: '', place: '', embedUrl: '', directUrl: '' });

const buildEmbedUrl = (mapUrl, placeName) => {
  // Si hay un link guardado, intentar extraer el parámetro ?q= (coordenadas o lugar)
  if (mapUrl && mapUrl !== '#') {
    try {
      const url = new URL(mapUrl);
      const q = url.searchParams.get('q');
      if (q) {
        // q puede ser "lat,lng" o un nombre — ambos funcionan en el embed de Google Maps
        return `https://maps.google.com/maps?q=${encodeURIComponent(q)}&output=embed`;
      }
    } catch(e) {
      // Si la URL no es parseable (ej: goo.gl short link), buscar por nombre
    }
    // Si el link entero es una URL válida de maps pero sin parámetro q, usarla de embed
    // Intentar pasar el link como query string de búsqueda
  }
  // Fallback: buscar por nombre del lugar
  const query = placeName || 'Lugar del evento';
  return `https://maps.google.com/maps?q=${encodeURIComponent(query)}&output=embed`;
};

const openMap = (type) => {
  const isCeremony = type === 'ceremony';
  const place = isCeremony ? ceremonyLocation.value : receptionLocation.value;
  const mapUrl = isCeremony ? ceremonyMap.value : receptionMap.value;
  mapModal.value = {
    show: true,
    type,
    place,
    embedUrl: buildEmbedUrl(mapUrl, place),
    directUrl: mapUrl && mapUrl !== '#' ? mapUrl : `https://maps.google.com/maps?q=${encodeURIComponent(place)}`
  };
};

// Itinerario y Padrinos
const itinerary = computed(() => data.value.itinerary || []);
const padrinos = computed(() => data.value.padrinos || []);

// RSVP
const rsvp = ref({ name: '', attending: '', guests: 1, message: '' });
const isSubmitting = ref(false);
const rsvpDeadline = computed(() => {
  if (!props.invitation.event_date) return 'próximamente';
  const date = new Date(props.invitation.event_date);
  date.setDate(date.getDate() - 15);
  return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'long' });
});

const sendRSVP = async () => {
  if (isSubmitting.value) return;
  
  // En modo demo sin ID, solo simulamos
  if (!props.invitation.id) {
      alert('¡Gracias! En una invitación real, esto se guardaría en la base de datos y te enviaría a WhatsApp.');
      return;
  }

  isSubmitting.value = true;
  try {
    // 1. Guardar en Base de Datos
    await axios.post(`/invitacion/${props.invitation.id}/rsvp`, rsvp.value);
    
    // 2. Abrir WhatsApp
    const phone = data.value.whatsapp_rsvp || '59178945612';
    const text = rsvp.value.attending === 'si' 
      ? `¡Hola! Confirmo mi asistencia: ${rsvp.value.name}. Seremos ${rsvp.value.guests} personas. ${rsvp.value.message ? 'Mensaje: ' + rsvp.value.message : ''}` 
      : `¡Hola! Lamentablemente no podré asistir: ${rsvp.value.name}. ¡Muchas felicidades!`;
    
    const url = `https://wa.me/${phone}?text=${encodeURIComponent(text)}`;
    window.open(url, '_blank');
  } catch (e) {
    console.error('Error saving RSVP', e);
    alert('Hubo un error al guardar tu confirmación, pero puedes enviarla por WhatsApp.');
  } finally {
    isSubmitting.value = false;
  }
};

// MÚSICA
const musicUrl = computed(() => data.value.music_url || '');
const audioRef = ref(null);
const isPlaying = ref(true);
const volume = ref(0.5);

const toggleAudio = () => {
  if (isPlaying.value) {
    audioRef.value.pause();
  } else {
    audioRef.value.play().catch(e => console.log('Autoplay blocked'));
  }
  isPlaying.value = !isPlaying.value;
};

const updateVolume = () => {
    if (audioRef.value) audioRef.value.volume = volume.value;
};

const tryAutoplay = () => {
  if (audioRef.value && musicUrl.value) {
    audioRef.value.play().then(() => {
      isPlaying.value = true;
    }).catch(e => {
        console.log('Autoplay blocked. Waiting for interaction.');
        isPlaying.value = false;
        
        // Add listeners for any interaction to trigger play
        const playOnInteraction = () => {
          if (audioRef.value) {
            audioRef.value.play().then(() => {
              isPlaying.value = true;
              removeListeners();
            });
          }
        };
        
        const removeListeners = () => {
          window.removeEventListener('click', playOnInteraction);
          window.removeEventListener('scroll', playOnInteraction);
          window.removeEventListener('touchstart', playOnInteraction);
        };

        window.addEventListener('click', playOnInteraction);
        window.addEventListener('scroll', playOnInteraction);
        window.addEventListener('touchstart', playOnInteraction);
    });
  }
};

const registerVisit = async () => {
    if (!props.invitation.id) return; // Saltamos en demos sin ID
    try {
        await axios.post(`/invitacion/${props.invitation.id}/visit`);
    } catch (e) { console.error('Error tracking visit', e); }
};

// Animación scroll itinerario
onMounted(() => {
  updateCountdown();
  timer = setInterval(updateCountdown, 1000);
  registerVisit();
  setTimeout(tryAutoplay, 1000);
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('in-view');
      }
    });
  }, { threshold: 0.1 });
  
  setTimeout(() => {
    document.querySelectorAll('.timeline-animate').forEach(el => observer.observe(el));
  }, 500);
});
onUnmounted(() => { if (timer) clearInterval(timer); });
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Montserrat:wght@300;400;600&display=swap');

.boda-template {
  font-family: 'Montserrat', sans-serif;
  color: #333;
  background: #fbf9f6;
  min-height: 100vh;
}

h1, h2, h3, .names {
  font-family: 'Cormorant Garamond', serif;
}

.text-center { text-align: center; }
.container { max-width: 1000px; margin: 0 auto; padding: 0 20px; }
/* PADRINOS */
.padrinos-grid {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 30px;
  max-width: 900px;
  margin: 0 auto;
}
.padrino-card {
  background: white;
  padding: 40px 30px;
  border-radius: 15px;
  min-width: 250px;
  flex: 1 1 250px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.04);
  position: relative;
  transition: transform 0.3s, box-shadow 0.3s;
  overflow: hidden;
}
.padrino-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; width: 100%; height: 4px;
  background: var(--theme-color);
}
.padrino-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 40px rgba(0,0,0,0.08);
}
.padrino-icon {
  width: 60px; height: 60px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 20px;
}
.padrino-role {
  font-family: 'Montserrat', sans-serif;
  font-size: 0.95rem;
  font-weight: 600;
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 1px;
}
.padrino-divider {
  width: 40px; height: 2px;
  background: #eee;
  margin: 15px auto;
}
.padrino-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.8rem;
  margin: 0;
  color: #333;
}

.section { padding: 100px 0; position: relative; }
.bg-light { background: #fdfdfd; }

.section-title {
  font-size: 3.5rem;
  color: var(--theme-color);
  margin-bottom: 50px;
  font-style: italic;
}

/* HERO */
.hero {
  position: relative;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  color: white;
  overflow: hidden;
}
.hero-bg {
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  object-fit: cover;
  z-index: 1;
}
.hero-overlay {
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.4) 70%, #fbf9f6 100%);
  z-index: 2;
}

/* AUDIO PLAYER FLOATING */
.audio-player-floating {
  position: fixed;
  bottom: 30px;
  left: 30px;
  z-index: 1000;
}
.audio-btn {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: white;
  border: none;
  box-shadow: 0 4px 15px rgba(0,0,0,0.15);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  transition: 0.3s;
}
.audio-btn:hover { transform: scale(1.1); }
.music-bars {
  display: flex;
  align-items: flex-end;
  gap: 3px;
  height: 15px;
}
.music-bars span {
  width: 3px;
  background: var(--theme-color);
  animation: music-bar 0.8s infinite ease-in-out;
}
.music-bars span:nth-child(2) { animation-delay: 0.2s; height: 100%; }
.music-bars span:nth-child(3) { animation-delay: 0.4s; height: 70%; }
@keyframes music-bar {
  0%, 100% { height: 5px; }
  50% { height: 15px; }
}

/* MODERN MUSIC BAR (Horizontal) */
.modern-music-bar {
    background: white;
    max-width: 400px;
    margin: 0 auto 50px;
    padding: 15px 25px;
    border-radius: 50px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    border: 1px solid #f0f0f0;
}
.music-info {
    display: flex;
    align-items: center;
    gap: 12px;
}
.music-icon { font-size: 1.2rem; }
.music-text {
    font-size: 0.9rem;
    font-weight: 600;
    color: #666;
    letter-spacing: 1px;
    text-transform: uppercase;
}
.music-controls {
    display: flex;
    align-items: center;
    gap: 15px;
}
.ctrl-btn {
    background: var(--theme-color);
    color: white;
    border: none;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.3s;
}
.ctrl-btn:hover { transform: scale(1.1); opacity: 0.9; }
.volume-slider {
    width: 80px;
    accent-color: var(--theme-color);
    cursor: pointer;
}

/* RSVP */
.rsvp-section { background: #fdfbf7; padding: 100px 0; }
.rsvp-form-card {
  max-width: 500px;
  margin: 0 auto;
  background: white;
  padding: 40px;
  border-radius: 20px;
  box-shadow: 0 15px 40px rgba(0,0,0,0.05);
}
.form-group {
  text-align: left;
  margin-bottom: 25px;
}
.form-group label {
  display: block;
  font-weight: 600;
  margin-bottom: 8px;
  color: #555;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 1px;
}
.modern-input {
  width: 100%;
  padding: 12px 15px;
  border: 1px solid #eee;
  border-radius: 10px;
  font-family: inherit;
  font-size: 1rem;
  outline: none;
  transition: 0.3s;
}
.modern-input:focus { border-color: var(--theme-color); }
.radio-group {
  display: flex;
  gap: 10px;
}
.radio-btn {
  flex: 1;
  padding: 12px;
  border: 1px solid #eee;
  background: transparent;
  border-radius: 10px;
  cursor: pointer;
  transition: 0.3s;
  font-family: inherit;
  font-weight: 600;
}
.radio-btn.active {
  background: var(--theme-color);
  color: white;
  border-color: var(--theme-color);
}
.btn-modern-primary {
  width: 100%;
  padding: 15px;
  background: #333;
  color: white;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 2px;
  cursor: pointer;
  transition: 0.3s;
}
.btn-modern-primary:hover:not(:disabled) { background: #000; letter-spacing: 3px; }
.btn-modern-primary:disabled { opacity: 0.5; cursor: not-allowed; }

.hero-content {
  position: relative;
  z-index: 3;
  padding: 0 20px;
}
.pre-title {
  font-family: 'Montserrat', sans-serif;
  letter-spacing: 6px;
  font-size: 1.2rem;
  font-weight: 500;
  margin-bottom: 20px;
}
.names {
  font-size: 7vw;
  line-height: 1.1;
  font-weight: 600;
  margin: 0;
  text-shadow: 0 4px 15px rgba(0,0,0,0.8);
}
.hero-ornament {
  margin: 15px 0;
}
.ampersand {
  font-family: 'Cormorant Garamond', serif;
  font-style: italic;
  font-size: 1.4em;
  display: inline-block;
  transform: scale(1.1);
}
.date-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 20px;
  margin-top: 40px;
  padding: 12px 30px;
  background: rgba(255,255,255,0.85);
  backdrop-filter: blur(10px);
  border-radius: 50px;
  display: inline-flex;
  max-width: 90%;
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}
.date-line {
  height: 1px;
  width: 40px;
  opacity: 0.5;
}
.date {
  font-size: 1.6rem;
  letter-spacing: 4px;
  font-family: 'Cormorant Garamond', serif;
  text-transform: uppercase;
  font-weight: 600;
  margin: 0;
  color: #222;
}
.scroll-down {
  position: absolute;
  bottom: 40px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 3;
  font-size: 0.8rem;
  letter-spacing: 2px;
  animation: bounce 2s infinite;
}

@keyframes bounce { 0%, 20%, 50%, 80%, 100% {transform: translateY(0) translateX(-50%);} 40% {transform: translateY(-20px) translateX(-50%);} 60% {transform: translateY(-10px) translateX(-50%);} }

/* COUNTDOWN */
.countdown {
  display: flex;
  justify-content: center;
  gap: 20px;
}
.time-box {
  background: white;
  min-width: 80px;
  padding: 20px 10px;
  border-radius: 10px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}
.time-box .num {
  display: block;
  font-size: 3.5rem;
  font-family: 'Cormorant Garamond', serif;
  color: var(--theme-color);
}
.time-box .label {
  font-size: 1rem;
  text-transform: uppercase;
  color: #888;
  letter-spacing: 2px;
}

/* MICRO ANIMATIONS */
.pulse-slow {
  animation: pulse-border 3s infinite ease-in-out;
}
@keyframes pulse-border {
  0% { box-shadow: 0 0 0 0 rgba(186, 150, 106, 0.4); }
  70% { box-shadow: 0 0 0 15px rgba(186, 150, 106, 0); }
  100% { box-shadow: 0 0 0 0 rgba(186, 150, 106, 0); }
}

/* MESSAGE */
.message-text {
  max-width: 700px;
  margin: 0 auto;
  font-size: 1.5rem;
  line-height: 2;
  color: #444;
  font-style: italic;
}

/* LOCATIONS */
.grid-2 {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 40px;
}
.location-card {
  text-align: center;
  background: white;
  padding: 50px 30px;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.03);
}
.location-card .icon {
  font-size: 3rem;
  margin-bottom: 20px;
}
.location-card h3 {
  font-size: 2.5rem;
  font-family: 'Cormorant Garamond', serif;
  margin-bottom: 15px;
}
.location-card .time {
  font-size: 1.2rem;
  font-weight: bold;
  color: #333;
  margin-bottom: 10px;
}
.location-card .address {
  font-size: 1rem;
  color: #888;
  line-height: 1.6;
}

/* LOCATION CARD - NUEVO DISEÑO */
.loc-card-top {
  display: flex;
  gap: 16px;
  align-items: flex-start;
  padding: 24px;
  border-radius: 16px 16px 0 0;
  border: 1px solid;
  border-bottom: none;
}
.loc-icon {
  font-size: 2rem;
  flex-shrink: 0;
  margin-top: 4px;
}
.loc-details h3 {
  font-size: 1.4rem;
  margin: 0 0 6px;
}
.loc-time {
  font-size: 1rem;
  font-weight: 700;
  color: #475569;
  margin: 0 0 6px;
  letter-spacing: 0.5px;
}
.loc-card-actions {
  display: flex;
  gap: 10px;
  padding: 14px 24px;
  background: white;
  border-radius: 0 0 16px 16px;
  border: 1px solid #e2e8f0;
  border-top: none;
}
.btn-map-inline {
  flex: 1;
  padding: 12px;
  background: transparent;
  border: 2px solid;
  border-radius: 10px;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  font-family: inherit;
  transition: 0.2s;
  letter-spacing: 0.5px;
}
.btn-map-inline:hover {
  opacity: 0.8;
  transform: translateY(-1px);
}
.btn-how-to-get {
  flex: 1;
  padding: 12px;
  color: white;
  border-radius: 10px;
  font-weight: 700;
  font-size: 1rem;
  text-decoration: none;
  text-align: center;
  transition: 0.2s;
  letter-spacing: 0.5px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.btn-how-to-get:hover { opacity: 0.85; transform: translateY(-1px); }

/* MAP MODAL */
.map-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.7);
  backdrop-filter: blur(6px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}
.map-modal {
  background: white;
  border-radius: 24px;
  width: 100%;
  max-width: 700px;
  overflow: hidden;
  box-shadow: 0 30px 70px rgba(0,0,0,0.4);
  display: flex;
  flex-direction: column;
  max-height: 90vh;
}
.map-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid #f1f5f9;
  flex-shrink: 0;
}
.map-modal-type {
  font-size: 0.82rem;
  text-transform: uppercase;
  letter-spacing: 2px;
  color: #94a3b8;
  font-weight: 700;
  margin: 0 0 4px;
}
.map-modal-place {
  font-size: 1.2rem;
  font-weight: 800;
  color: #1e293b;
  margin: 0;
  font-family: 'Cormorant Garamond', serif;
}
.map-modal-close {
  background: #f1f5f9;
  border: none;
  border-radius: 50%;
  width: 38px;
  height: 38px;
  font-size: 1.5rem;
  color: #64748b;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: 0.2s;
  flex-shrink: 0;
}
.map-modal-close:hover { background: #fee2e2; color: #ef4444; }
.map-embed-container {
  height: 380px;
  flex-shrink: 0;
  background: #f1f5f9;
  overflow: hidden;
}
.map-embed-container iframe {
  width: 100%;
  height: 100%;
  display: block;
}

.map-modal-footer {
  padding: 16px 24px;
  border-top: 1px solid #f1f5f9;
  flex-shrink: 0;
}
.btn-directions {
  display: block;
  text-align: center;
  padding: 14px;
  background: #1e293b;
  color: white;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 700;
  font-size: 1rem;
  letter-spacing: 0.5px;
  transition: 0.2s;
}
.btn-directions:hover { background: #0f172a; letter-spacing: 1px; }

/* MAP MODAL TRANSITION */
.map-fade-enter-active, .map-fade-leave-active { transition: all 0.3s ease; }
.map-fade-enter-from, .map-fade-leave-to { opacity: 0; transform: scale(0.95); }

/* ITINERARY */

.timeline {
  max-width: 700px;
  margin: 0 auto;
  position: relative;
}
.timeline::before {
  content: '';
  position: absolute;
  top: 0; left: 20px;
  height: 100%; width: 2px;
  background: #eee;
}
.timeline-item {
  position: relative;
  padding-left: 70px;
  margin-bottom: 50px;
}
.timeline-dot {
  position: absolute;
  left: 9px; top: 8px;
  width: 24px; height: 24px;
  background: var(--theme-color);
  border-radius: 50%;
  border: 5px solid #fbf9f6;
  box-shadow: 0 0 0 2px var(--theme-color);
}
.timeline-content h4 {
  font-family: 'Cormorant Garamond', serif;
  font-size: 2rem;
  color: var(--theme-color);
  margin: 0 0 10px;
}
.timeline-content h3 {
  font-size: 1.4rem;
  margin: 0 0 8px;
  font-weight: 600;
  font-family: 'Montserrat', sans-serif;
}
.timeline-content p {
  color: #666;
  font-size: 1.1rem;
  line-height: 1.6;
  margin: 0;
}

/* ITINERARY ANIMATION */
.timeline-animate {
  opacity: 0;
  transform: translateY(30px);
  transition: all 0.8s ease-out;
}
.timeline-animate.in-view {
  opacity: 1;
  transform: translateY(0);
}

/* PADRINOS */
.bg-light-pattern {
  background: #fdfdfd url('data:image/svg+xml;utf8,<svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"><path d="M20 20.5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z" fill="%23e0e0e0" fill-rule="evenodd"/></svg>');
}
.padrinos-grid {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 30px;
  max-width: 900px;
  margin: 0 auto;
}
.padrino-card {
  background: white;
  padding: 40px 30px;
  border-radius: 15px;
  min-width: 250px;
  flex: 1 1 250px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.04);
  position: relative;
  transition: transform 0.3s, box-shadow 0.3s;
  overflow: hidden;
}
.padrino-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; width: 100%; height: 4px;
  background: var(--theme-color);
}
.padrino-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 40px rgba(0,0,0,0.08);
}
.padrino-icon {
  width: 60px; height: 60px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 20px;
}
.padrino-role {
  font-family: 'Montserrat', sans-serif;
  font-size: 0.95rem;
  text-transform: uppercase;
  letter-spacing: 3px;
  margin: 0 0 15px;
  font-weight: 600;
}
.padrino-divider {
  height: 1px;
  width: 50px;
  background: #eee;
  margin: 0 auto 15px;
}
.padrino-name {
  font-size: 2rem;
  margin: 0;
  color: #222;
  font-family: 'Cormorant Garamond', serif;
}

/* GALLERY */
.photo-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 15px;
}
.photo-item {
  height: 300px;
  border-radius: 10px;
  overflow: hidden;
}
.photo-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: 0.5s;
}
.photo-item:hover img {
  transform: scale(1.05);
}

/* FOOTER */
.footer {
  text-align: center;
  padding: 80px 20px;
  color: white;
}
.footer p { margin: 0; letter-spacing: 2px; font-size: 1.1rem;}
.footer .names-small {
  font-family: 'Cormorant Garamond', serif;
  font-size: 3.5rem;
  margin-top: 25px;
  font-style: italic;
}

@media (max-width: 768px) {
  .names { font-size: 15vw; margin-bottom: 10px; }
  .date-wrapper { padding: 10px 20px; gap: 10px; margin-top: 25px; }
  .date-line { width: 20px; }
  .date { font-size: 1.2rem; letter-spacing: 2px; }
  .countdown { flex-wrap: wrap; }
  .time-box { min-width: 70px; padding: 15px 5px; }
  .section-title { font-size: 2.8rem; }
}
</style>
