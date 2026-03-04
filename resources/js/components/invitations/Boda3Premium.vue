<template>
  <div class="boda-premium-container" v-if="invitation">
    <div id="music-bar" :class="{ visible: showMusicBar }">
      <div class="player-top">
        <button class="play-btn" @click="togglePlay">
          <svg v-if="!isPlaying" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg>
          <svg v-else viewBox="0 0 24 24"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
        </button>
        <div class="player-meta">
          <div class="player-title">{{ bride }} & {{ groom }} · {{ formattedShortDate }}</div>
          <div class="player-subtitle">Nuestra canción</div>
        </div>
        <div class="player-time">{{ currentTimeFormatted }}</div>
      </div>
      <div id="visualizer">
        <div class="vbar" v-for="(val, index) in visualizerBars" :key="index" :style="{ height: val + 'px', background: val > 12 ? '#ffffff' : '#b0b0b0' }"></div>
      </div>
    </div>

    <!-- AUDIO ELEMENT -->
    <audio ref="audioRef" loop preload="none" @timeupdate="onTimeUpdate" :src="musicUrl || '/audio/boda.mp3'"></audio>
    <div id="hearts" ref="heartsContainer"></div>

    <!-- INTRO -->
    <div id="intro" :style="{ opacity: introOpacity, visibility: introVisible ? 'visible' : 'hidden', display: introDisplay }">
      <div id="intro-bg" :style="{ backgroundImage: `url(${coverImage})` }"></div>
      <canvas id="intro-canvas" ref="canvasRef"></canvas>
      <div class="intro-box">
        <span class="intro-label">Con amor te invitamos a compartir</span>
        <div class="intro-title">Nuestra Boda</div>
        <div class="intro-names">{{ bride }} & {{ groom }}</div>
        <div class="intro-divider"></div>
        <div class="intro-date">{{ formattedDate }}</div>
        <button class="intro-cta" @click="enterSite">
          <span>Abrir Invitación ✦</span>
        </button>
      </div>
    </div>

    <!-- MAIN -->
    <div id="main" :class="{ visible: mainVisible }">
      <!-- HERO -->
      <div class="hero rt" id="hero" :class="{ 'in-view': heroInView }" ref="heroRef">
        <div class="hero-bg" :style="{ backgroundImage: `url(${coverImage})`, transform: `scale(1.08) translateY(${scrollY * 0.09}px)` }"></div>
        <div class="hero-content">
          <div class="hero-eyebrow">Nos casamos</div>
          <div class="hero-names">{{ bride }}<br><span class="hero-amp">♡</span><br>{{ groom }}</div>
          <div class="hero-date-row">
            <div class="h-line l"></div>
            <div class="hero-date-txt">{{ formattedDateClean }}</div>
            <div class="h-line r"></div>
          </div>
          <p class="hero-sub">{{ data.hero_subtitle || 'Un día que recordaremos para siempre' }}</p>
        </div>
        <div class="scroll-cue">
          <span>Descubrir</span>
          <div class="chev"></div><div class="chev"></div><div class="chev"></div>
        </div>
      </div>

      <!-- COUPLE -->
      <div class="ibg-section rt" ref="coupleRef">
        <div class="ibg-photo" :style="{ backgroundImage: `url('https://images.unsplash.com/photo-1583939003579-730e3918a45a?w=900&q=80')`, transform: `scale(1.2) translateY(${-coupleOffset * 0.09}px)` }"></div>
        <div class="ibg-overlay"></div>
        <div class="ibg-inner">
          <span class="s-tag">Los Novios</span>
          <h2 class="s-title">Dos almas, un destino</h2>
          <div class="g-line"></div>
          <p class="s-body">{{ data.couple_text || 'Cada historia de amor es única. La nuestra comenzó con una mirada y hoy, con el corazón lleno de gratitud, queremos compartir este momento tan especial con las personas que más amamos.' }}</p>
          <div class="couple-row">
            <div class="c-person">
              <div class="c-avatar"><div class="c-avatar-inner">👰</div></div>
              <div class="c-name">{{ bride }}</div>
              <div class="c-role">La Novia</div>
            </div>
            <div class="c-heart">♡</div>
            <div class="c-person">
              <div class="c-avatar"><div class="c-avatar-inner">🤵</div></div>
              <div class="c-name">{{ groom }}</div>
              <div class="c-role">El Novio</div>
            </div>
          </div>
        </div>
      </div>

      <!-- DETAILS -->
      <div class="plain-section rt">
        <div class="section-inner">
          <span class="s-tag">La Celebración</span>
          <h2 class="s-title">Detalles del Gran Día</h2>
          <div class="g-line"></div>
          <div class="date-prominent">
            <span class="dc-icon" style="animation:none; margin:0 0 0.5rem 0;">📅</span>
            <span class="dc-label">Fecha del Evento</span>
            <div class="dc-val" style="font-size: clamp(1.1rem, 5vw, 1.4rem);">{{ formattedDateBreak }}</div>
          </div>
          <h3 class="loc-subtitle">¿Dónde nos encontramos?</h3>
          <div class="cards-grid">
            <div class="dcard">
              <div class="dc-corner tl"></div><div class="dc-corner br"></div>
              <span class="dc-icon" style="font-size:2.5rem;">⛪</span>
              <span class="dc-label">Ceremonia</span>
              <div class="dc-val">{{ data.ceremony_location || 'Iglesia Principal' }}<br><span v-if="data.ceremony_time">{{ data.ceremony_time }}</span></div>
              <div class="loc-card-actions" v-if="data.ceremony_map">
                <button class="btn-map" @click="openModalMap('ceremony')">🗺 Ver Mapa</button>
                <a :href="data.ceremony_map" target="_blank" class="btn-map btn-how-to">🚦 Cómo llegar</a>
              </div>
            </div>
            <div class="dcard">
              <div class="dc-corner tl"></div><div class="dc-corner br"></div>
              <span class="dc-icon" style="font-size:2.5rem;">🥂</span>
              <span class="dc-label">Recepción</span>
              <div class="dc-val">{{ data.reception_location || 'Salón de Eventos' }}<br><span v-if="data.reception_time">{{ data.reception_time }}</span></div>
              <div class="loc-card-actions" v-if="data.reception_map">
                <button class="btn-map" @click="openModalMap('reception')">🗺 Ver Mapa</button>
                <a :href="data.reception_map" target="_blank" class="btn-map btn-how-to">🚦 Cómo llegar</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- TIMELINE -->
      <div class="ibg-section rt" ref="timelineRef" v-if="itinerary && itinerary.length">
        <div class="ibg-photo" :style="{ backgroundImage: `url('https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=900&q=80')`, transform: `scale(1.2) translateY(${-timelineOffset * 0.09}px)` }"></div>
        <div class="ibg-overlay"></div>
        <div class="ibg-inner">
          <span class="s-tag">Programa</span>
          <h2 class="s-title">El Orden del Día</h2>
          <div class="g-line"></div>
          <div class="timeline">
            <div class="tl-item" v-for="(item, index) in itinerary" :key="index">
              <div class="tl-dot"></div>
              <span class="tl-time">{{ item.time }}</span>
              <div class="tl-event">{{ item.activity }}</div>
              <div class="tl-sub" v-if="item.description">{{ item.description }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- COUNTDOWN -->
      <div class="plain-section rt" style="background: radial-gradient(ellipse at center, #160a14 0%, var(--dark) 70%);">
        <div class="section-inner">
          <span class="s-tag">Cuenta Regresiva</span>
          <h2 class="s-title">Faltan...</h2>
          <div class="g-line"></div>
          <div class="cd-grid">
            <div class="cd-item"><span class="cd-num">{{ countdown.days }}</span><span class="cd-label">Días</span></div>
            <div class="cd-item"><span class="cd-num">{{ countdown.hours }}</span><span class="cd-label">Horas</span></div>
            <div class="cd-item"><span class="cd-num">{{ countdown.minutes }}</span><span class="cd-label">Mins</span></div>
            <div class="cd-item"><span class="cd-num">{{ countdown.seconds }}</span><span class="cd-label">Segs</span></div>
          </div>
        </div>
      </div>

      <!-- GALLERY CAROUSEL -->
      <div class="plain-section rt" v-if="invitation.gallery_images && invitation.gallery_images.length > 0">
        <div class="section-inner" style="max-width: 1000px;">
          <span class="s-tag">Momentos</span>
          <h2 class="s-title">Nuestra Historia</h2>
          <div class="g-line"></div>
          
          <div class="gallery-wrapper">
            <div class="gallery-slider" ref="gallerySlider">
              <div 
                class="gallery-item" 
                v-for="(img, idx) in invitation.gallery_images" 
                :key="idx"
                @click="openLightbox(idx)"
              >
                <img :src="img" alt="Foto de galería" loading="lazy">
                <div class="g-overlay"><span>🔍 Ampliar</span></div>
              </div>
            </div>
            <button class="g-nav g-prev" @click="scrollGallery(-1)">❮</button>
            <button class="g-nav g-next" @click="scrollGallery(1)">❯</button>
          </div>
        </div>
      </div>

      <!-- RSVP -->
      <div class="ibg-section rt" ref="rsvpRef">
        <div class="ibg-photo" :style="{ backgroundImage: `url('https://images.unsplash.com/photo-1522673607200-164d1b6ce486?w=900&q=80')`, transform: `scale(1.2) translateY(${-rsvpOffset * 0.09}px)` }"></div>
        <div class="ibg-overlay"></div>
        <div class="ibg-inner">
          <span class="s-tag">Confirmación</span>
          <h2 class="s-title">Acompáñanos</h2>
          <div class="g-line"></div>
          <p class="s-body">Tu presencia es el mejor regalo.<br>Confirma antes del {{ rsvpDeadline }}.</p>
          <div class="rsvp-container">
            <button class="intro-cta" @click="openRsvp" style="opacity:1; animation:none; background:rgba(0,0,0,0.5);">
              <span>Confirmar Asistencia ✦</span>
            </button>
          </div>
        </div>
      </div>

      <footer>
        <div class="foot-names">{{ bride }} & {{ groom }}</div>
        <div class="foot-sub">{{ formattedDate }} · Para siempre</div>
        <div class="foot-hearts">♡ ♡ ♡</div>
      </footer>
    </div>

    <!-- RSVP MODAL -->
    <div class="modal-overlay" :class="{ open: isRsvpOpen }" @click.self="closeRsvp">
      <div class="modal-box">
        <button class="close-modal" @click="closeRsvp">×</button>
        <h3 style="font-family:'Playfair Display',serif; color:#fff; font-size:1.8rem; margin-bottom:1.5rem; text-align:center;">Confirmar</h3>
        <form @submit.prevent="submitRsvp">
          <div class="form-group">
            <label class="form-label">Nombre Completo</label>
            <input type="text" v-model="rsvpForm.name" class="form-input" placeholder="Tu nombre" required>
          </div>
          <div class="form-group">
            <label class="form-label">¿Podrás asistir?</label>
            <div class="qty-selector">
              <div class="qty-btn" :class="{ active: rsvpForm.attending === 'si' }" @click="rsvpForm.attending = 'si'">Sí, asistiré</div>
              <div class="qty-btn" :class="{ active: rsvpForm.attending === 'no' }" @click="rsvpForm.attending = 'no'">No podré</div>
            </div>
          </div>
          <div class="form-group" v-if="rsvpForm.attending === 'si'">
            <label class="form-label">Cantidad de Asistentes</label>
            <div class="qty-selector">
              <div class="qty-btn" :class="{ active: rsvpForm.guests === 1 }" @click="rsvpForm.guests = 1">1</div>
              <div class="qty-btn" :class="{ active: rsvpForm.guests === 2 }" @click="rsvpForm.guests = 2">2</div>
              <div class="qty-btn" :class="{ active: rsvpForm.guests === 3 }" @click="rsvpForm.guests = 3">3</div>
              <div class="qty-btn" :class="{ active: isManualQty }" @click="isManualQty = true">Otro</div>
            </div>
            <div id="manualQtyGroup" v-show="isManualQty" style="margin-top:0.8rem;">
              <input type="number" v-model="rsvpForm.guests" class="form-input" placeholder="Escribe la cantidad" min="1">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Mensaje (Opcional)</label>
            <input type="text" v-model="rsvpForm.message" class="form-input" placeholder="Algún deseo para los novios...">
          </div>
          <button type="submit" class="submit-btn" :disabled="isSubmitting || !rsvpForm.attending">{{ isSubmitting ? 'Enviando...' : 'Enviar por WhatsApp' }}</button>
        </form>
      </div>
    </div>

    <!-- LIGHTBOX MODAL -->
    <div class="lightbox-overlay" :class="{ open: lightbox.isOpen }" @click.self="closeLightbox">
      <button class="close-lightbox" @click="closeLightbox">×</button>
      <button class="lb-nav lb-prev" @click.stop="navigateLightbox(-1)" v-if="invitation.gallery_images && invitation.gallery_images.length > 1">❮</button>
      <img :src="lightbox.currentImage" alt="Vista ampliada" v-if="lightbox.isOpen" class="lb-img">
      <button class="lb-nav lb-next" @click.stop="navigateLightbox(1)" v-if="invitation.gallery_images && invitation.gallery_images.length > 1">❯</button>
      <div class="lb-counter" v-if="invitation.gallery_images && invitation.gallery_images.length > 0">
        {{ lightbox.currentIndex + 1 }} / {{ invitation.gallery_images.length }}
      </div>
    </div>
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
            <div class="map-embed-wrapper">
              <iframe
                :src="mapModal.embedUrl"
                width="100%" height="100%"
                frameborder="0" style="border:0;display:block"
                allowfullscreen loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
              ></iframe>
            </div>
            <div class="map-modal-footer">
              <a :href="mapModal.directUrl" target="_blank" class="btn-directions">
                🚦 Abrir en Google Maps
              </a>
            </div>
          </div>
        </div>
      </transition>
    </teleport>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';

const props = defineProps({
  invitation: { type: Object, required: true }
});

const data = computed(() => props.invitation.data || {});
const bride = computed(() => data.value.bride || 'Novia');
const groom = computed(() => data.value.groom || 'Novio');
const musicUrl = computed(() => data.value.music_url || '');

const coverImage = computed(() => {
  return props.invitation.gallery_images && props.invitation.gallery_images.length > 0
    ? props.invitation.gallery_images[0]
    : 'https://images.unsplash.com/photo-1519741497674-611481863552?w=800&q=80';
});

const mapDateToClean = (dateStr) => {
  if (!dateStr) return '00 / 00 / 0000';
  const d = new Date(dateStr);
  return `${String(d.getDate()).padStart(2, '0')} · ${String(d.getMonth() + 1).padStart(2, '0')} · ${d.getFullYear()}`;
};

const formattedDateClean = computed(() => mapDateToClean(props.invitation.event_date));
const formattedDate = computed(() => {
  if (!props.invitation.event_date) return '';
  const d = new Date(props.invitation.event_date);
  return d.toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' });
});
const formattedShortDate = computed(() => {
  if (!props.invitation.event_date) return '';
  const d = new Date(props.invitation.event_date);
  return d.toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' });
});
const formattedDateBreak = computed(() => {
  if (!props.invitation.event_date) return '';
  const d = new Date(props.invitation.event_date);
  return `${d.toLocaleDateString('es-ES', { weekday: 'long' })}\n${d.getDate()} de ${d.toLocaleDateString('es-ES', { month: 'long' })}, ${d.getFullYear()}`.toUpperCase();
});

const itinerary = computed(() => data.value.itinerary || []);

// --- STATE ---
const introOpacity = ref('1');
const introVisible = ref(true);
const introDisplay = ref('flex');
const mainVisible = ref(false);
const showMusicBar = ref(false);
const heroInView = ref(false);

const audioRef = ref(null);
const isPlaying = ref(false);
const currentTimeFormatted = ref('0:00');
const visualizerBars = ref(Array(150).fill(2));
let audioCtx, analyser, dataArray, rafId;

// --- DOM REFS FOR PARALLAX ---
const heroRef = ref(null);
const coupleRef = ref(null);
const timelineRef = ref(null);
const rsvpRef = ref(null);
const scrollY = ref(0);
const coupleOffset = ref(0);
const timelineOffset = ref(0);
const rsvpOffset = ref(0);

const handleScroll = () => {
  scrollY.value = window.scrollY;
  if (coupleRef.value) coupleOffset.value = coupleRef.value.getBoundingClientRect().top;
  if (timelineRef.value) timelineOffset.value = timelineRef.value.getBoundingClientRect().top;
  if (rsvpRef.value) rsvpOffset.value = rsvpRef.value.getBoundingClientRect().top;
};

// --- MAP MODAL ---
const mapModal = ref({ show: false, type: '', place: '', embedUrl: '', directUrl: '' });

const buildEmbedUrl = (mapUrl, placeName) => {
  if (mapUrl && mapUrl !== '#') {
    try {
      const url = new URL(mapUrl);
      const q = url.searchParams.get('q');
      if (q) return `https://maps.google.com/maps?q=${encodeURIComponent(q)}&output=embed`;
    } catch(e) {}
  }
  const query = placeName || 'Lugar del evento';
  return `https://maps.google.com/maps?q=${encodeURIComponent(query)}&output=embed`;
};

const openModalMap = (type) => {
  const isCeremony = type === 'ceremony';
  const place = isCeremony ? (data.value.ceremony_location || 'Ceremonia') : (data.value.reception_location || 'Recepción');
  const mapUrl = isCeremony ? data.value.ceremony_map : data.value.reception_map;
  
  mapModal.value = {
    show: true,
    type,
    place,
    embedUrl: buildEmbedUrl(mapUrl, place),
    directUrl: mapUrl && mapUrl !== '#' ? mapUrl : `https://maps.google.com/maps?q=${encodeURIComponent(place)}`
  };
};

// --- ENTER SITE ---
let canvasInterval, heartInterval;
const enterSite = () => {
  if (!audioRef.value.src) audioRef.value.src = musicUrl.value || '/audio/boda.mp3';
  audioRef.value.play().then(() => { isPlaying.value = true; setupAudioVisualizer(); }).catch(() => {});
  
  introOpacity.value = '0';
  setTimeout(() => {
    introVisible.value = false;
    introDisplay.value = 'none';
    clearInterval(heartInterval);
    mainVisible.value = true;
    heroInView.value = true;
    startHearts();
    showMusicBar.value = true;
  }, 1500);
};

// --- AUDIO VISUALIZER ---
const togglePlay = () => {
  if (!audioRef.value.src) audioRef.value.src = musicUrl.value || '/audio/boda.mp3';
  if (isPlaying.value) {
    audioRef.value.pause(); isPlaying.value = false; cancelAnimationFrame(rafId);
  } else {
    setupAudioVisualizer();
    audioRef.value.play(); isPlaying.value = true; drawVisualizer();
  }
};

const setupAudioVisualizer = () => {
  if (!audioCtx) {
    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    analyser = audioCtx.createAnalyser();
    analyser.fftSize = 256;
    const src = audioCtx.createMediaElementSource(audioRef.value);
    src.connect(analyser); analyser.connect(audioCtx.destination);
    dataArray = new Uint8Array(analyser.frequencyBinCount);
  }
  if (audioCtx.state === 'suspended') audioCtx.resume();
  drawVisualizer();
};

const drawVisualizer = () => {
  if (!isPlaying.value) return;
  analyser.getByteFrequencyData(dataArray);
  
  // Solo la primera mitad de las frecuencias tienen energía significativa en una pista normal.
  // Mapearemos los primeros 65 bins sobre nuestras 150 barras para que se muevan en TODA la pantalla.
  const usableBins = Math.min(65, dataArray.length);
  const step = usableBins / 150;
  
  visualizerBars.value = visualizerBars.value.map((_, i) => {
    const val = dataArray[Math.floor(i * step)] || 0;
    const height = 2 + (val / 255) * 20;
    
    // Al tener 150 barras, ajustamos la probabilidad para no sobrecargar de partículas
    if (val > 60 && Math.random() > 0.85) {
      emitParticle(i, val);
    }
    
    return height;
  });
  rafId = requestAnimationFrame(drawVisualizer);
};

const emitParticle = (barIndex, intensity) => {
  const container = document.querySelector('.boda-premium-container');
  if (!container) return;
  
  const el = document.createElement('div');
  el.className = 'v-particle';
  
  // Posición horizontal cubriendo el 100% del ancho de la pantalla,
  // y no solo el centro. barIndex va de 0 a 149.
  const leftPct = (barIndex / 149) * 100;
  el.style.left = `${leftPct}vw`;
  
  // Condición: intensidad alta (blanca/chica), intensidad media (ploma/grande)
  const isHighInt = intensity > 150;
  const color = isHighInt ? '#ffffff' : '#b0b0b0';
  el.style.background = color;
  el.style.boxShadow = `0 0 6px ${color}`;
  
  // El plomo (plata) debe ser más grande, las blancas más chicas
  const size = isHighInt ? '2px' : '3.5px';
  el.style.width = size;
  el.style.height = size;
  
  // Variar la caída
  el.style.animationDuration = (2.5 + Math.random() * 2) + 's';
  
  // Agregar al DOM de la plantilla para respetar estilos "scoped"
  container.appendChild(el);
  
  // Limpiar la partícula
  setTimeout(() => { if (el.parentNode) el.remove(); }, 5000);
};

const onTimeUpdate = () => {
  if (audioRef.value) {
    const m = Math.floor(audioRef.value.currentTime / 60);
    const s = Math.floor(audioRef.value.currentTime % 60).toString().padStart(2, '0');
    currentTimeFormatted.value = `${m}:${s}`;
  }
};

// --- COUNTDOWN ---
const countdown = ref({ days: '000', hours: '00', minutes: '00', seconds: '00' });
let cdTimer = null;
const updateCountdown = () => {
  if (!props.invitation.event_date) return;
  const eventTime = new Date(props.invitation.event_date).getTime();
  const distance = eventTime - new Date().getTime();
  if (distance < 0) return;
  countdown.value = {
    days: String(Math.floor(distance / 86400000)).padStart(3, '0'),
    hours: String(Math.floor((distance % 86400000) / 3600000)).padStart(2, '0'),
    minutes: String(Math.floor((distance % 3600000) / 60000)).padStart(2, '0'),
    seconds: String(Math.floor((distance % 60000) / 1000)).padStart(2, '0')
  };
};

// --- RSVP ---
const isRsvpOpen = ref(false);
const isManualQty = ref(false);
const isSubmitting = ref(false);
const rsvpForm = ref({ name: '', attending: 'si', guests: 1, message: '' });

const rsvpDeadline = computed(() => {
  if (!props.invitation.event_date) return 'próximamente';
  const d = new Date(props.invitation.event_date);
  d.setDate(d.getDate() - 15);
  return d.toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' });
});

const openRsvp = () => { isRsvpOpen.value = true; };
const closeRsvp = () => { isRsvpOpen.value = false; };

const submitRsvp = async () => {
  if (isSubmitting.value) return;
  if (!props.invitation.id) {
    alert('Modo demo. Redirigiendo a wp...');
    closeRsvp(); return;
  }
  isSubmitting.value = true;
  try {
    await axios.post(`/invitacion/${props.invitation.id}/rsvp`, rsvpForm.value);
    const phone = data.value.whatsapp_rsvp || '59178945612';
    const text = rsvpForm.value.attending === 'si'
      ? `¡Hola! Confirmo asistencia: ${rsvpForm.value.name}. Seremos ${rsvpForm.value.guests} personas. ${rsvpForm.value.message}`
      : `¡Hola! No podré asistir: ${rsvpForm.value.name}. ¡Felicidades!`;
    window.open(`https://wa.me/${phone}?text=${encodeURIComponent(text)}`, '_blank');
    for (let i = 0; i < 15; i++) spawnSparks(window.innerWidth / 2, window.innerHeight / 2);
    closeRsvp();
  } catch (e) {
    alert('Error al guardar, puedes enviar el mensaje por WhatsApp de todas formas.');
  } finally {
    isSubmitting.value = false;
  }
};

// --- EFFECTS ---
const heartsContainer = ref(null);
const startHearts = () => {
  const syms = ['♡', '♥', '💕', '💗', '🌸'];
  const cols = ['#c0647a', '#e8a0b0', '#c9a84c', 'rgba(255,255,255,0.5)'];
  setInterval(() => {
    if (!heartsContainer.value) return;
    const el = document.createElement('div');
    el.className = 'fheart'; el.textContent = syms[Math.floor(Math.random() * syms.length)];
    el.style.left = (Math.random() * 100) + '%'; el.style.fontSize = (0.7 + Math.random() * 1.4) + 'rem';
    el.style.animationDuration = (9 + Math.random() * 11) + 's'; el.style.animationDelay = (Math.random() * 1.5) + 's';
    el.style.color = cols[Math.floor(Math.random() * cols.length)];
    heartsContainer.value.appendChild(el);
    setTimeout(() => el.remove(), 24000);
  }, 900);
};

const spawnSparks = (x, y) => {
  const s = ['✦', '✧', '♡', '✨', '🌸', '⭐'];
  for (let i = 0; i < 5; i++) setTimeout(() => {
    const el = document.createElement('div');
    el.className = 'sparkle'; el.textContent = s[Math.floor(Math.random() * s.length)];
    el.style.left = (x + (Math.random() - 0.5) * 52) + 'px'; el.style.top = (y + (Math.random() - 0.5) * 52) + 'px';
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 800);
  }, i * 55);
};

const onClickSparks = (e) => spawnSparks(e.clientX, e.clientY);

const setupIntersectionObserver = () => {
  const obs = new IntersectionObserver(e => {
    e.forEach(x => { if (x.isIntersecting) x.target.classList.add('revealed'); });
  }, { threshold: 0.1 });
  document.querySelectorAll('.rt').forEach(el => obs.observe(el));
};

// --- GALLERY & LIGHTBOX ---
const gallerySlider = ref(null);
const scrollGallery = (dir) => {
  if (!gallerySlider.value) return;
  const amount = window.innerWidth > 600 ? 350 : 260;
  gallerySlider.value.scrollBy({ left: amount * dir, behavior: 'smooth' });
};

const lightbox = ref({
  isOpen: false,
  currentIndex: 0,
  currentImage: ''
});

const openLightbox = (index) => {
  if (!props.invitation.gallery_images) return;
  lightbox.value.currentIndex = index;
  lightbox.value.currentImage = props.invitation.gallery_images[index];
  lightbox.value.isOpen = true;
  document.body.style.overflow = 'hidden';
};

const closeLightbox = () => {
  lightbox.value.isOpen = false;
  document.body.style.overflow = '';
};

const navigateLightbox = (dir) => {
  if (!props.invitation.gallery_images) return;
  const total = props.invitation.gallery_images.length;
  let nextIdx = lightbox.value.currentIndex + dir;
  if (nextIdx < 0) nextIdx = total - 1;
  if (nextIdx >= total) nextIdx = 0;
  lightbox.value.currentIndex = nextIdx;
  lightbox.value.currentImage = props.invitation.gallery_images[nextIdx];
};

const registerVisit = async () => {
  if (!props.invitation.id) return;
  try { await axios.post(`/invitacion/${props.invitation.id}/visit`); } catch (e) {}
};

onMounted(() => {
  registerVisit();
  window.addEventListener('scroll', handleScroll);
  document.addEventListener('click', onClickSparks);
  updateCountdown(); cdTimer = setInterval(updateCountdown, 1000);
  nextTick(() => { setupIntersectionObserver(); });
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
  document.removeEventListener('click', onClickSparks);
  if (cdTimer) clearInterval(cdTimer);
  if (rafId) cancelAnimationFrame(rafId);
  if (audioCtx && audioCtx.state !== 'closed') audioCtx.close();
});
</script>
<style scoped>
  * { box-sizing: border-box; }
  .boda-premium-container { background: #08050a; font-family: 'Cormorant Garamond', serif; overflow-x: hidden; color: #fff; min-height: 100vh; }

  /* ========== MUSIC PLAYER BAR ========== */
  #music-bar {
    position: fixed; top: 0; left: 0; right: 0; z-index: 10000;
    height: 64px; display: flex; flex-direction: column;
    background: rgba(5, 3, 7, 0.92); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);
    border-bottom: 1px solid rgba(201,168,76,0.15); box-shadow: 0 4px 40px rgba(0,0,0,0.6);
    opacity: 0; transform: translateY(-100%); transition: opacity 0.8s ease, transform 0.8s ease; pointer-events: none;
  }
  #music-bar.visible { opacity: 1; transform: translateY(0); pointer-events: all; }
  .player-top { display: flex; align-items: center; gap: 0.85rem; padding: 0 1rem; flex: 1; }
  .play-btn {
    width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #d4a84c 0%, #8c6020 100%);
    border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; flex-shrink: 0;
    box-shadow: 0 0 18px rgba(201,168,76,0.55); transition: transform 0.15s, box-shadow 0.15s; -webkit-tap-highlight-color: transparent;
  }
  .play-btn:active { transform: scale(0.88); }
  .play-btn svg { width: 13px; height: 13px; fill: #08050a; pointer-events: none; }
  .player-meta { flex: 1; min-width: 0; }
  .player-title { font-family: 'Cinzel', serif; font-size: 0.56rem; color: #c9a84c; letter-spacing: 0.28em; text-transform: uppercase; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .player-subtitle { font-family: 'Cormorant Garamond', serif; font-style: italic; font-size: 0.7rem; color: rgba(255,255,255,0.32); margin-top: 1px; }
  .player-time { font-family: 'Cinzel', serif; font-size: 0.5rem; color: rgba(255,255,255,0.35); letter-spacing: 0.08em; flex-shrink: 0; }
  #visualizer { width: 100%; height: 22px; display: flex; align-items: flex-start; gap: 0; overflow: hidden; background: rgba(0,0,0,0.3); position: relative; }
  .vbar { flex: 1; height: 2px; background: #c9a84c; border-radius: 0 0 1px 1px; transition: height 0.04s linear; }

  /* Visualizer Particles */
  :deep(.v-particle) {
    position: fixed; top: 64px; /* Empieza debajo de la barra musical */
    width: 2px; height: 2px; border-radius: 50%;
    pointer-events: none; z-index: 9998;
    animation: fallParticle linear forwards;
    opacity: 0.8;
  }
  @keyframes fallParticle {
    0% { transform: translateY(0) scale(1); opacity: 0.9; }
    50% { opacity: 1; transform: translateY(7vh) scale(1.1); }
    100% { transform: translateY(15vh) scale(0); opacity: 0; }
  }

  /* ========== INTRO ========== */
  #intro { position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; flex-direction: column; overflow: hidden; background: #08050a; transition: opacity 1.5s ease, visibility 1.5s; }
  #intro-bg { position: absolute; inset: 0; background-size: cover; background-position: center; filter: blur(8px) brightness(0.2) saturate(0.7); transform: scale(1.1); z-index: 0; }
  #intro-canvas { position: absolute; inset: 0; z-index: 1; }
  .intro-box { position: relative; z-index: 5; text-align: center; padding: 2rem 1.6rem; display: flex; flex-direction: column; align-items: center; }
  .intro-label { font-family: 'Cinzel', serif; color: #c9a84c; font-size: 0.57rem; letter-spacing: 0.55em; text-transform: uppercase; opacity: 0; animation: fadeUp 0.8s ease 0.5s forwards; }
  .intro-title { font-family: 'Playfair Display', serif; font-size: clamp(3rem, 15vw, 6rem); color: #fff; line-height: 0.9; margin: 0.4rem 0; text-shadow: 0 0 60px rgba(201,168,76,0.55), 0 0 120px rgba(192,100,122,0.25); opacity: 0; animation: titleBlast 1.3s cubic-bezier(0.16,1,0.3,1) 1s forwards; font-style: italic; }
  .intro-names { font-family: 'Cinzel', serif; font-size: clamp(1rem, 5vw, 2rem); color: #f0d080; font-weight: 600; letter-spacing: 0.2em; opacity: 0; animation: fadeUp 1s ease 2.1s forwards; margin-top: 0.5rem; }
  .intro-divider { width: 0; height: 1px; background: linear-gradient(90deg, transparent, #c9a84c, transparent); margin: 1.2rem auto; animation: expandLine 0.8s ease 2.7s forwards; }
  .intro-date { font-family: 'Cormorant Garamond', serif; font-style: italic; color: #e8a0b0; font-size: clamp(0.95rem, 4vw, 1.35rem); opacity: 0; animation: fadeUp 0.8s ease 3.1s forwards; letter-spacing: 0.1em; }
  .intro-cta { margin-top: 2.8rem; padding: 0.9rem 2.8rem; border: 1px solid #c9a84c; background: transparent; color: #c9a84c; font-family: 'Cinzel', serif; font-size: 0.63rem; letter-spacing: 0.38em; text-transform: uppercase; cursor: pointer; position: relative; overflow: hidden; opacity: 0; animation: fadeUp 0.8s ease 3.7s forwards; transition: color 0.4s; -webkit-tap-highlight-color: transparent; }
  .intro-cta::before { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, #c9a84c 0%, #c0647a 100%); transform: scaleX(0); transform-origin: left; transition: transform 0.4s; }
  .intro-cta:active::before, .intro-cta:hover::before { transform: scaleX(1); }
  .intro-cta:active, .intro-cta:hover { color: #fff; }
  .intro-cta span { position: relative; z-index: 1; }

  @keyframes fadeUp { from { opacity:0; transform:translateY(28px); } to { opacity:1; transform:translateY(0); } }
  @keyframes titleBlast { 0% { opacity:0; transform:scale(0.4) translateY(50px); filter:blur(24px); } 60% { opacity:1; transform:scale(1.06) translateY(-8px); filter:blur(0); } 100% { opacity:1; transform:scale(1) translateY(0); } }
  @keyframes expandLine { from { width:0; } to { width:min(260px,70vw); } }

  /* ========== MAIN ========== */
  #main { opacity: 0; transition: opacity 1.4s ease; padding-top: 0; display: none; }
  #main.visible { opacity: 1; display: block; padding-top: 64px; }

  /* ========== HERO ========== */
  .hero { min-height: 100svh; position: relative; display: flex; align-items: center; justify-content: center; overflow: hidden; }
  .hero-bg { position: absolute; inset: 0; background-position: center 30%; background-size: cover; filter: blur(3px) brightness(0.25) saturate(0.65); transform: scale(1.08); will-change: transform; z-index: 0; }
  .hero::after { content: ''; position: absolute; inset: 0; z-index: 1; background: linear-gradient(180deg, rgba(8,5,10,0.55) 0%, rgba(14,8,12,0.25) 45%, rgba(14,8,12,0.25) 55%, rgba(8,5,10,0.9) 100%); }
  .hero-content { position: relative; z-index: 3; text-align: center; padding: 2rem 1.4rem; }
  .hero-eyebrow { font-family: 'Cinzel', serif; color: #c9a84c; font-size: 0.58rem; letter-spacing: 0.7em; text-transform: uppercase; opacity: 0; transform: translateY(20px); transition: all 0.9s ease 0.1s; }
  .hero-names { font-family: 'Playfair Display', serif; font-size: clamp(3rem, 15vw, 6.5rem); font-style: italic; color: #fff; line-height: 0.95; text-shadow: 0 0 60px rgba(201,168,76,0.4), 0 0 130px rgba(192,100,122,0.15); opacity: 0; transform: translateY(30px); transition: all 1.1s ease 0.35s; margin: 0.6rem 0; font-weight: 600; }
  .hero-amp { color: #e8a0b0; font-size: 0.5em; font-family: 'Cormorant Garamond', serif; }
  .hero-date-row { display: flex; align-items: center; justify-content: center; gap: 1rem; opacity: 0; transform: translateY(18px); transition: all 0.9s ease 0.75s; margin-top: 1rem; }
  .h-line { height: 1px; width: 45px; }
  .h-line.l { background: linear-gradient(90deg, transparent, #c9a84c); }
  .h-line.r { background: linear-gradient(90deg, #c9a84c, transparent); }
  .hero-date-txt { font-family: 'Cinzel', serif; color: #f0d080; font-size: clamp(0.68rem, 3vw, 0.95rem); letter-spacing: 0.28em; }
  .hero-sub { font-family: 'Cormorant Garamond', serif; font-style: italic; color: rgba(245,213,221,0.8); font-size: clamp(1rem, 4.5vw, 1.4rem); margin-top: 0.9rem; opacity: 0; transform: translateY(18px); transition: all 0.9s ease 0.95s; }
  .hero.in-view .hero-eyebrow, .hero.in-view .hero-names, .hero.in-view .hero-date-row, .hero.in-view .hero-sub { opacity: 1; transform: none; }
  
  .scroll-cue { position: absolute; bottom: 2rem; left: 50%; transform: translateX(-50%); z-index: 4; display: flex; flex-direction: column; align-items: center; gap: 4px; }
  .scroll-cue span { font-family: 'Cinzel', serif; color: #c9a84c; font-size: 0.48rem; letter-spacing: 0.5em; text-transform: uppercase; opacity: 0.55; }
  .chev { width: 13px; height: 13px; border-right: 1.5px solid #c9a84c; border-bottom: 1.5px solid #c9a84c; transform: rotate(45deg); opacity: 0; animation: chevFall 1.8s ease infinite; }
  .chev:nth-child(3) { animation-delay: 0.2s; }
  .chev:nth-child(4) { animation-delay: 0.4s; }
  @keyframes chevFall { 0% { opacity:0; transform:rotate(45deg) translate(-3px,-3px); } 50% { opacity:0.8; } 100% { opacity:0; transform:rotate(45deg) translate(3px,3px); } }

  /* ========== SECTIONS ========== */
  .ibg-section { position: relative; overflow: hidden; padding: 6rem 1.4rem; }
  .ibg-photo { position: absolute; inset: -20%; background-size: cover; background-position: center; filter: blur(9px) brightness(0.17) saturate(0.6); will-change: transform; transition: transform 0.1s linear; }
  .ibg-overlay { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(8,5,10,0.78) 0%, rgba(12,7,11,0.45) 50%, rgba(8,5,10,0.78) 100%); }
  .ibg-inner { position: relative; z-index: 2; max-width: 860px; margin: 0 auto; text-align: center; }
  .plain-section { padding: 6rem 1.4rem; background: #08050a; }
  .section-inner { max-width: 860px; margin: 0 auto; text-align: center; }
  .s-tag { font-family: 'Cinzel', serif; color: #c9a84c; font-size: 0.57rem; letter-spacing: 0.62em; text-transform: uppercase; display: block; margin-bottom: 1rem; opacity: 0; transform: translateY(16px); transition: all 0.7s ease; }
  .s-title { font-family: 'Playfair Display', serif; font-size: clamp(1.75rem, 7vw, 3.4rem); color: #fff; font-style: italic; font-weight: 400; margin-bottom: 1rem; opacity: 0; transform: translateY(20px); transition: all 0.8s ease 0.12s; }
  .s-body { font-size: clamp(0.92rem, 3.8vw, 1.2rem); color: rgba(255,255,255,0.62); line-height: 1.95; opacity: 0; transform: translateY(20px); transition: all 0.8s ease 0.24s; }
  .g-line { width: 0; height: 1px; background: linear-gradient(90deg, transparent, #c9a84c, transparent); margin: 1.5rem auto; transition: width 1s ease 0.32s; }
  .revealed .s-tag, .revealed .s-title, .revealed .s-body { opacity: 1; transform: none; }
  .revealed .g-line { width: min(200px, 60vw); }

  /* ========== COUPLE ========== */
  .couple-row { display: flex; align-items: center; justify-content: center; gap: 1.6rem; flex-wrap: wrap; margin-top: 3rem; }
  .c-person { text-align: center; opacity: 0; transform: translateX(-22px); transition: all 0.85s ease; }
  .c-person:last-child { transform: translateX(22px); }
  .revealed .c-person { opacity: 1; transform: none; }
  .revealed .c-person:last-child { opacity: 1; transform: none; transition-delay: 0.18s; }
  .c-avatar { width: clamp(95px,26vw,125px); height: clamp(95px,26vw,125px); border-radius: 50%; margin: 0 auto 1rem; position: relative; display: flex; align-items: center; justify-content: center; }
  .c-avatar::before { content: ''; position: absolute; inset: -2px; border-radius: 50%; background: conic-gradient(from 0deg, transparent, #c9a84c 30%, transparent 55%, #c0647a 80%, transparent); animation: spinBorder 4s linear infinite; z-index: 0; }
  .c-avatar-inner { position: relative; z-index: 1; width: calc(100% - 4px); height: calc(100% - 4px); border-radius: 50%; display: flex; align-items: center; justify-content: center; background: radial-gradient(circle at 35% 35%, #2d1a28, #0f0810); font-size: clamp(2.4rem, 10vw, 3.6rem); border: 2px solid rgba(201,168,76,0.25); }
  @keyframes spinBorder { to { transform: rotate(360deg); } }
  .c-name { font-family: 'Sacramento', cursive; font-size: clamp(2.2rem, 9vw, 3rem); color: #fff; text-shadow: 0 0 20px rgba(201,168,76,0.3); }
  .c-role { font-family: 'Cinzel', serif; font-size: 0.53rem; letter-spacing: 0.45em; color: #c9a84c; text-transform: uppercase; margin-top: 0.3rem; }
  .c-heart { font-size: clamp(2.5rem,10vw,3.5rem); color: #c0647a; animation: hbeat 1.6s ease-in-out infinite; text-shadow: 0 0 25px rgba(192,100,122,0.7); opacity: 0; transition: opacity 0.8s ease 0.4s; flex-shrink: 0; }
  .revealed .c-heart { opacity: 1; }
  @keyframes hbeat { 0%,100% { transform: scale(1); } 14% { transform: scale(1.2); } 28% { transform: scale(1); } 42% { transform: scale(1.1); } }

  /* ========== CARDS & TIMELINE ========== */
  .cards-grid { display: grid; grid-template-columns: 1fr; gap: 1.2rem; margin-top: 2rem; max-width: 800px; margin-inline: auto; }
  @media(min-width:650px) { .cards-grid { grid-template-columns: repeat(2,1fr); } }
  .dcard { border: 1px solid rgba(201,168,76,0.17); padding: 2rem 1.2rem; background: rgba(255,255,255,0.025); position: relative; opacity: 0; transform: translateY(26px); transition: all 0.7s ease; }
  .dcard:active, .dcard:hover { border-color: rgba(201,168,76,0.45); }
  .revealed .dcard:nth-child(1) { opacity:1; transform:none; transition-delay:0.05s; }
  .revealed .dcard:nth-child(2) { opacity:1; transform:none; transition-delay:0.2s; }
  .revealed .dcard:nth-child(3) { opacity:1; transform:none; transition-delay:0.35s; }
  .dc-corner { position: absolute; width: 15px; height: 15px; }
  .dc-corner.tl { top:0; left:0; border-top:1px solid #c9a84c; border-left:1px solid #c9a84c; }
  .dc-corner.br { bottom:0; right:0; border-bottom:1px solid #c9a84c; border-right:1px solid #c9a84c; }
  .dc-icon { font-size: 2rem; display: block; margin-bottom: 0.9rem; filter: drop-shadow(0 0 10px rgba(201,168,76,0.5)); animation: floatI 4s ease-in-out infinite; }
  .dc-label { font-family: 'Cinzel', serif; font-size: 0.53rem; letter-spacing: 0.45em; color: #c9a84c; text-transform: uppercase; display: block; margin-bottom: 0.6rem; }
  .dc-val { font-family: 'Playfair Display', serif; font-size: clamp(0.98rem,4vw,1.22rem); color: #fff; font-style: italic; line-height: 1.45; }
  
  .date-prominent { margin-bottom: 1.5rem; padding: 2rem 1rem; border: 1px solid rgba(201,168,76,0.3); background: rgba(201,168,76,0.03); display: inline-block; min-width: min(100%, 350px); opacity: 0; transform: translateY(20px); transition: all 0.8s ease; }
  .revealed .date-prominent { opacity: 1; transform: none; }
  .loc-subtitle { font-family: 'Playfair Display', serif; font-size: clamp(1.5rem, 5vw, 2.2rem); color: rgba(255,255,255,0.85); font-style: italic; margin: 1rem 0 0.5rem; opacity: 0; transform: translateY(20px); transition: all 0.8s ease 0.1s; }
  .revealed .loc-subtitle { opacity: 1; transform: none; }
  
  .loc-card-actions { display: flex; gap: 8px; justify-content: center; margin-top: 1.5rem; flex-wrap: wrap; }
  .btn-map { flex: 1; padding: 0.8rem 0.5rem; background: transparent; border: 1px solid #c9a84c; color: #c9a84c; font-family: 'Cinzel', serif; font-size: 0.55rem; letter-spacing: 0.15em; cursor: pointer; transition: all 0.3s; text-transform: uppercase; border-radius: 6px; font-weight: 700; min-width: 110px; text-decoration: none; display: flex; align-items: center; justify-content: center; text-align: center; }
  .btn-map:hover { background: #c9a84c; color: #08050a; }
  .btn-how-to { background: rgba(201,168,76,0.1); border-color: rgba(201,168,76,0.5); }

  /* ========== MAP MODAL ========== */
  .map-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(6px); z-index: 10005; display: flex; align-items: center; justify-content: center; padding: 20px; }
  .map-modal { background: #0f0a0e; border: 1px solid #c9a84c; width: 100%; max-width: 600px; box-shadow: 0 10px 40px rgba(0,0,0,0.6); position: relative; }
  .map-modal-header { display: flex; justify-content: space-between; align-items: center; padding: 1.2rem 1.5rem; border-bottom: 1px solid rgba(201,168,76,0.2); }
  .map-modal-type { font-family: 'Cinzel', serif; font-size: 0.5rem; letter-spacing: 0.25em; color: #c9a84c; text-transform: uppercase; margin: 0 0 0.3rem; }
  .map-modal-place { font-family: 'Playfair Display', serif; font-size: 1.3rem; color: #fff; font-style: italic; margin: 0; }
  .map-modal-close { background: transparent; border: none; color: #c9a84c; font-size: 2rem; cursor: pointer; transition: 0.3s; line-height: 1; }
  .map-modal-close:hover { color: #fff; transform: scale(1.1); }
  .map-embed-wrapper { height: 350px; background: rgba(255,255,255,0.02); }
  .map-modal-footer { padding: 1rem 1.5rem; border-top: 1px solid rgba(201,168,76,0.2); display: flex; justify-content: center; }
  .btn-directions { width: 100%; padding: 0.9rem; background: linear-gradient(135deg, #c9a84c, #8c6020); color: #08050a; font-family: 'Cinzel', serif; font-size: 0.6rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; text-align: center; text-decoration: none; border-radius: 4px; transition: transform 0.2s; }
  .btn-directions:hover { transform: scale(0.98); }
  .map-fade-enter-active, .map-fade-leave-active { transition: opacity 0.3s ease; }
  .map-fade-enter-from, .map-fade-leave-to { opacity: 0; }

  .timeline { position: relative; max-width: 500px; margin: 3rem auto 0; padding-left: 2rem; text-align: left;}
  .timeline::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 1px; background: linear-gradient(180deg, transparent, #c9a84c 15%, #c9a84c 85%, transparent); }
  .tl-item { position: relative; padding-bottom: 2.2rem; opacity: 0; transform: translateX(-18px); transition: all 0.7s ease; }
  .revealed .tl-item:nth-child(1) { opacity:1; transform:none; transition-delay:0.05s; }
  .revealed .tl-item:nth-child(2) { opacity:1; transform:none; transition-delay:0.2s; }
  .revealed .tl-item:nth-child(3) { opacity:1; transform:none; transition-delay:0.35s; }
  .revealed .tl-item:nth-child(4) { opacity:1; transform:none; transition-delay:0.5s; }
  .tl-dot { width: 9px; height: 9px; background: #c9a84c; border-radius: 50%; position: absolute; left: -2.5rem; top: 0.4rem; box-shadow: 0 0 10px rgba(201,168,76,0.7); }
  .tl-time { font-family: 'Cinzel', serif; font-size: 0.53rem; color: #c9a84c; letter-spacing: 0.38em; text-transform: uppercase; display: block; margin-bottom: 0.25rem; }
  .tl-event { font-family: 'Playfair Display', serif; font-size: clamp(1rem,4vw,1.18rem); color: #fff; font-style: italic; }
  .tl-sub { font-size: 0.8rem; color: rgba(255,255,255,0.36); margin-top: 0.22rem; }

  /* ========== COUNTDOWN ========== */
  .cd-grid { display: flex; gap: clamp(0.7rem,3vw,1.2rem); justify-content: center; flex-wrap: nowrap; margin-top: 2.5rem; }
  .cd-item { text-align: center; opacity:0; transform:scale(0.8); transition:all 0.6s ease; }
  .revealed .cd-item:nth-child(1){opacity:1;transform:none;transition-delay:0.05s;}
  .revealed .cd-item:nth-child(2){opacity:1;transform:none;transition-delay:0.15s;}
  .revealed .cd-item:nth-child(3){opacity:1;transform:none;transition-delay:0.25s;}
  .revealed .cd-item:nth-child(4){opacity:1;transform:none;transition-delay:0.35s;}
  .cd-num { font-family: 'Cinzel', serif; font-size: clamp(1.7rem,7.5vw,3.8rem); color: #f0d080; font-weight: 900; text-shadow: 0 0 22px rgba(201,168,76,0.42); display: block; border: 1px solid rgba(201,168,76,0.22); padding: clamp(0.6rem,2vw,0.9rem) clamp(0.5rem,2vw,0.8rem); min-width: clamp(58px,16vw,95px); background: rgba(201,168,76,0.04); }
  .cd-label { font-family: 'Cinzel', serif; font-size: 0.46rem; color: #c9a84c; letter-spacing: 0.38em; text-transform: uppercase; display: block; margin-top: 0.55rem; }

  /* ========== RSVP MODAL ========== */
  .rsvp-container { text-align: center; margin-top: 2rem; }
  .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 10001; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: opacity 0.4s ease; backdrop-filter: blur(5px); }
  .modal-overlay.open { opacity: 1; pointer-events: all; }
  .modal-box { background: #0f0a0e; border: 1px solid #c9a84c; width: 90%; max-width: 450px; padding: 2rem; transform: translateY(30px); transition: transform 0.4s ease; position: relative; overflow-y: auto; max-height: 90vh; }
  .modal-overlay.open .modal-box { transform: translateY(0); }
  .close-modal { position: absolute; top: 1rem; right: 1rem; font-size: 1.5rem; color: #c9a84c; cursor: pointer; background: none; border: none; }
  .form-group { margin-bottom: 1.2rem; text-align: left; }
  .form-label { display: block; font-family: 'Cinzel', serif; font-size: 0.6rem; color: #c9a84c; letter-spacing: 0.2em; margin-bottom: 0.5rem; text-transform: uppercase; }
  .form-input { width: 100%; padding: 0.8rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(201,168,76,0.3); color: #fff; font-family: 'Cormorant Garamond', serif; font-size: 1.1rem; outline: none; transition: border 0.3s; }
  .form-input:focus { border-color: #c9a84c; }
  .qty-selector { display: flex; gap: 0.5rem; margin-top: 0.5rem; }
  .qty-btn { flex: 1; padding: 0.7rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(201,168,76,0.3); color: #aaa; cursor: pointer; font-family: 'Cinzel', serif; font-size: 0.9rem; text-align: center; }
  .qty-btn.active { background: #c9a84c; color: #000; border-color: #c9a84c; font-weight: bold; }
  .submit-btn { width: 100%; padding: 1rem; margin-top: 1rem; background: linear-gradient(135deg, #c9a84c, #8c6020); border: none; color: #1a0c16; font-family: 'Cinzel', serif; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; cursor: pointer; transition: transform 0.2s; }
  .submit-btn:disabled { opacity: 0.6; cursor: not-allowed; }
  .submit-btn:active { transform: scale(0.97); }

  /* ========== GALLERY CAROUSEL ========== */
  .gallery-wrapper { position: relative; margin-top: 2rem; }
  .gallery-slider { display: flex; gap: 1rem; overflow-x: auto; scroll-snap-type: x mandatory; scrollbar-width: none; -ms-overflow-style: none; padding-bottom: 1rem; }
  .gallery-slider::-webkit-scrollbar { display: none; }
  .gallery-item { flex: 0 0 clamp(260px, 70vw, 350px); height: clamp(350px, 90vw, 450px); border-radius: 4px; overflow: hidden; position: relative; scroll-snap-align: center; cursor: pointer; border: 1px solid rgba(201,168,76,0.3); opacity:0; transform:scale(0.9); transition:all 0.6s ease; }
  .revealed .gallery-item { opacity: 1; transform: none; }
  .gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
  .gallery-item:hover img { transform: scale(1.05); }
  .g-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.5); opacity: 0; display: flex; align-items: center; justify-content: center; transition: opacity 0.3s ease; }
  .gallery-item:hover .g-overlay { opacity: 1; }
  .g-overlay span { color: #fff; font-family: 'Cinzel', serif; font-size: 0.8rem; letter-spacing: 0.2em; border: 1px solid #fff; padding: 0.5rem 1rem; }
  .g-nav { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(8,5,10,0.8); border: 1px solid #c9a84c; color: #c9a84c; width: 44px; height: 44px; border-radius: 50%; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 2; transition: all 0.3s; }
  .g-nav:hover { background: #c9a84c; color: #08050a; }
  .g-prev { left: -22px; }
  .g-next { right: -22px; }
  @media(max-width: 600px) { .g-nav { display: none; } }

  /* ========== LIGHTBOX MODAL ========== */
  .lightbox-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.95); z-index: 10005; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: opacity 0.4s ease; backdrop-filter: blur(8px); }
  .lightbox-overlay.open { opacity: 1; pointer-events: all; }
  .lb-img { max-width: 90vw; max-height: 85vh; object-fit: contain; border: 1px solid rgba(201,168,76,0.5); box-shadow: 0 0 40px rgba(0,0,0,0.8); transform: scale(0.95); transition: transform 0.4s ease; }
  .lightbox-overlay.open .lb-img { transform: scale(1); }
  .close-lightbox { position: absolute; top: 1.5rem; right: 1.5rem; color: #fff; font-size: 2.5rem; background: none; border: none; cursor: pointer; text-shadow: 0 0 10px #000; z-index: 2; }
  .lb-nav { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.1); color: #fff; border: none; font-size: 2rem; padding: 1rem; cursor: pointer; transition: background 0.3s; z-index: 2; }
  .lb-nav:hover { background: rgba(255,255,255,0.2); }
  .lb-prev { left: 1rem; }
  .lb-next { right: 1rem; }
  .lb-counter { position: absolute; bottom: 1.5rem; color: #fff; font-family: 'Cormorant Garamond', serif; font-size: 1.2rem; letter-spacing: 0.1em; }

  /* Footer & Effects */
  footer { padding: 4.5rem 1.4rem 3rem; text-align: center; background: linear-gradient(180deg, #08050a, #040208); border-top: 1px solid rgba(201,168,76,0.1); }
  .foot-names { font-family: 'Sacramento', cursive; font-size: clamp(3rem,14vw,6rem); color: #fff; text-shadow: 0 0 40px rgba(201,168,76,0.3); }
  .foot-sub { font-family: 'Cormorant Garamond', serif; font-style: italic; color: rgba(255,255,255,0.32); font-size: 0.88rem; margin-top: 0.7rem; letter-spacing: 0.1em; }
  .foot-hearts { margin-top: 1.5rem; font-size: 1.1rem; color: #c0647a; letter-spacing: 0.5rem; animation: pulse 3s ease-in-out infinite; }
  @keyframes pulse { 0%,100%{opacity:0.5;transform:scale(1);} 50%{opacity:0.9;transform:scale(1.08);} }
  
  :deep(.fheart) { position: fixed; bottom: -60px; pointer-events: none; z-index: 9997; opacity: 0; animation: floatH linear infinite; }
  @keyframes floatH { 0% { opacity:0; transform:translateY(0) rotate(0deg) scale(0.5); } 10% { opacity:0.65; } 80% { opacity:0.3; } 100% { opacity:0; transform:translateY(-110vh) rotate(360deg) scale(1.2); } }
  :deep(.sparkle) { position: fixed; pointer-events: none; z-index: 9999; animation: sparkAnim 0.75s ease forwards; color:#c9a84c; }
  @keyframes sparkAnim { 0% { opacity:1; transform:translate(-50%,-50%) scale(0) rotate(0deg); } 55% { opacity:1; transform:translate(-50%,-50%) scale(1.1) rotate(180deg); } 100% { opacity:0; transform:translate(-50%,-50%) scale(0.3) rotate(360deg); } }
</style>
