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
        <ul>
          <li v-if="isAdmin" @click="currentTab = 'posts'" :class="{ active: currentTab === 'posts' }">📝 Blog / Noticias</li>
          <li v-if="isAdmin" @click="currentTab = 'settings'" :class="{ active: currentTab === 'settings' }">⚙️ Configuración Web</li>
          <li v-if="isAdmin" @click="currentTab = 'create_invitation'; openInvForm();" :class="{ active: currentTab === 'create_invitation' }">✨ Crear Invitación</li>
          <li @click="currentTab = 'clients'" :class="{ active: currentTab === 'clients' }">💌 Invitaciones</li>
          <li class="logout-tab" @click="logout">🚪 Cerrar Sesión</li>
        </ul>
      </nav>
      <a :href="homeUrl" class="back-link">← Ver Web</a>
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

      <div v-if="currentTab === 'create_invitation' || currentTab === 'clients'" class="panel slide-in">
        
        <div v-if="currentTab === 'create_invitation' || isEditingInv" class="header-action">
          <h3>{{ isEditingInv ? '✏️ Editando Invitación' : '✨ Crear Nueva Invitación' }}</h3>
          <button v-if="isEditingInv" @click="currentTab = 'clients'; isEditingInv = false;" class="btn-cancel">Cancelar Edición</button>
        </div>
        
        <div v-if="currentTab === 'clients' && !isEditingInv" class="header-action" style="margin-top: 20px;">
          <h3>💌 Invitaciones Creadas (Privadas)</h3>
          <button v-if="isAdmin" @click="currentTab = 'create_invitation'; openInvForm();" class="btn-primary">+ Nueva Invitación</button>
        </div>

        <!-- FORM HEADER -->
        <div v-if="currentTab === 'create_invitation' || isEditingInv" class="inv-form-header">
          <div class="inv-form-icon">{{ isEditingInv ? '✏️' : '🎉' }}</div>
          <div>
            <h3 class="inv-form-title">{{ isEditingInv ? 'Editando Invitación' : 'Nueva Invitación' }}</h3>
            <p class="inv-form-sub">{{ isEditingInv ? 'Modifica los datos y guarda los cambios.' : 'Completa los campos para crear una invitación personalizada.' }}</p>
          </div>
          <button v-if="isEditingInv" @click="resetInvForm" class="inv-cancel-btn">✕ Cancelar</button>
        </div>

        <div v-if="currentTab === 'create_invitation' || isEditingInv" class="inv-form-body">

          <!-- SECCIÓN A: Información General -->
          <div class="form-section">
            <div class="form-section-head" style="--sec-color:#6366f1">
              <div class="sec-icon" style="background:#6366f1">🗂</div>
              <div>
                <div class="sec-label">Sección 1</div>
                <div class="sec-title">Información General</div>
              </div>
            </div>
            <div class="form-section-body">
              <div class="row">
                <div class="col">
                  <label class="flabel">Título de la Invitación</label>
                  <input v-model="newInv.title" placeholder="Ej: Boda Marcela & Alejandro" class="input">
                </div>
                <div class="col-small">
                  <label class="flabel">Plantilla Base</label>
                  <select v-model="newInv.template" class="input" :disabled="!isAdmin">
                    <option value="boda1">💍 Boda Especial (Boda 1)</option>
                    <option value="boda2">🌙 Boda Moderna (Boda 2)</option>
                    <option value="custom">✨ Personalizada</option>
                  </select>
                  <small v-if="!isAdmin" style="font-size:0.78rem;color:#94a3b8;">La plantilla es asignada por el administrador.</small>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col">
                  <label class="flabel">Nombre del Cliente (opcional)</label>
                  <input v-model="newInv.client_name" placeholder="Ej: Familia García" class="input">
                </div>
                <div class="col-small">
                  <label class="flabel">📅 Fecha del Evento</label>
                  <input v-model="newInv.event_date" type="date" class="input">
                </div>
                <div class="col">
                  <label class="flabel">🔗 URL Amigable (opcional)</label>
                  <input v-model="newInv.slug" placeholder="boda-ana-juan" class="input">
                </div>
              </div>
            </div>
          </div>

          <!-- SECCIÓN B: Constructor de Boda -->
          <div v-if="newInv.template === 'boda1' || newInv.template === 'boda2'" class="boda-builder">

            <!-- B1: Tema y Color -->
            <div class="form-section">
              <div class="form-section-head" style="--sec-color:#ec4899">
                <div class="sec-icon" style="background:#ec4899">🎨</div>
                <div>
                  <div class="sec-label">Sección 2</div>
                  <div class="sec-title">Color y Tema</div>
                </div>
              </div>
              <div class="form-section-body">
                <div class="row align-items-center">
                  <div class="col-small">
                    <label class="flabel">Color Principal</label>
                    <div class="color-pick-row">
                      <input type="color" v-model="newInv.data.theme_color" class="color-picker">
                      <span class="color-chip" :style="{ background: newInv.data.theme_color }"></span>
                    </div>
                  </div>
                  <div class="col">
                    <div class="color-preview-banner" :style="{ borderLeftColor: newInv.data.theme_color, color: newInv.data.theme_color }">
                      <strong>Vista previa:</strong> Este color se usará para títulos, botones y detalles decorativos de la invitación.
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- B2: Novios -->
            <div class="form-section">
              <div class="form-section-head" style="--sec-color:#f43f5e">
                <div class="sec-icon" style="background:#f43f5e">💑</div>
                <div>
                  <div class="sec-label">Sección 3</div>
                  <div class="sec-title">Nombres de los Novios</div>
                </div>
              </div>
              <div class="form-section-body">
                <div class="row">
                  <div class="col">
                    <label class="flabel">🤵 Novio</label>
                    <input v-model="newInv.data.groom" placeholder="Nombre del novio" class="input input-elegant">
                  </div>
                  <div class="names-amp">&</div>
                  <div class="col">
                    <label class="flabel">👰 Novia</label>
                    <input v-model="newInv.data.bride" placeholder="Nombre de la novia" class="input input-elegant">
                  </div>
                </div>
              </div>
            </div>

            <!-- B3: Lugares -->
            <div class="form-section">
              <div class="form-section-head" style="--sec-color:#0ea5e9">
                <div class="sec-icon" style="background:#0ea5e9">📍</div>
                <div>
                  <div class="sec-label">Sección 4</div>
                  <div class="sec-title">Lugares de Celebración</div>
                </div>
              </div>
              <div class="form-section-body">
                <div class="row">
                  <div class="col location-col">
                    <div class="location-col-header">⛪ Ceremonia</div>
                    <label class="flabel">🕔 Hora de inicio</label>
                    <input type="time" v-model="newInv.data.ceremony_time" class="input input-light mb-2">
                    <label class="flabel">Nombre del lugar</label>
                    <input v-model="newInv.data.ceremony_location" placeholder="Ej: Parroquia San Miguel" class="input input-light">
                    <label class="flabel mt-2">🗺 Ubicación</label>
                    <div class="map-dual-input">
                      <input v-model="newInv.data.ceremony_map" placeholder="Pegar link de Google Maps..." class="input input-light mb-0" style="flex:1">
                      <button type="button" @click="openMapPicker('ceremony')" class="btn-pick-map">🗺 Elegir</button>
                    </div>
                    <small class="map-hint" v-if="newInv.data.ceremony_map">✅ Ubicación guardada</small>
                  </div>

                  <div class="location-divider">↔</div>

                  <div class="col location-col">
                    <div class="location-col-header">🥂 Recepción</div>
                    <label class="flabel">🕔 Hora de inicio</label>
                    <input type="time" v-model="newInv.data.reception_time" class="input input-light mb-2">
                    <label class="flabel">Nombre del lugar</label>
                    <input v-model="newInv.data.reception_location" placeholder="Ej: Hacienda Los Ángeles" class="input input-light">
                    <label class="flabel mt-2">🗺 Ubicación</label>
                    <div class="map-dual-input">
                      <input v-model="newInv.data.reception_map" placeholder="Pegar link de Google Maps..." class="input input-light mb-0" style="flex:1">
                      <button type="button" @click="openMapPicker('reception')" class="btn-pick-map">🗺 Elegir</button>
                    </div>
                    <small class="map-hint" v-if="newInv.data.reception_map">✅ Ubicación guardada</small>
                  </div>
                </div>
              </div>
            </div>

            <!-- B4: Itinerario -->
            <div class="form-section">
              <div class="form-section-head" style="--sec-color:#f59e0b">
                <div class="sec-icon" style="background:#f59e0b">🗓</div>
                <div>
                  <div class="sec-label">Sección 5</div>
                  <div class="sec-title">Itinerario del Evento</div>
                </div>
                <button @click.prevent="addItineraryItem" class="sec-add-btn">+ Añadir</button>
              </div>
              <div class="form-section-body">
                <p class="sec-hint">Agrega los momentos clave de la celebración (Llegada, Brindis, Cena, etc.)</p>
                <div class="itinerary-list">
                  <transition-group name="list">
                    <div v-for="(item, index) in newInv.data.itinerary" :key="'iti'+index" class="itinerary-item">
                      <div class="iti-index">{{ index + 1 }}</div>
                      <div class="itinerary-grid">
                        <div class="iti-time">
                          <label class="time-label">🕔 Hora</label>
                          <input type="time" v-model="item.time" class="input input-light mb-0">
                        </div>
                        <div class="iti-details">
                          <input v-model="item.activity" placeholder="¿Qué sucederá? (Ej: Primer Baile)" class="input input-light font-bold mb-2">
                          <input v-model="item.description" placeholder="Descripción breve opcional..." class="input input-light text-small mb-0">
                        </div>
                        <div class="iti-action">
                          <button @click.prevent="removeItineraryItem(index)" class="btn-cancel btn-circle" title="Eliminar">×</button>
                        </div>
                      </div>
                    </div>
                  </transition-group>
                  <div v-if="!newInv.data.itinerary || newInv.data.itinerary.length === 0" class="iti-empty">
                    📋 Aún no hay actividades. Pulsa <strong>+ Añadir</strong> para comenzar.
                  </div>
                </div>
              </div>
            </div>

            <!-- B5: Padrinos -->
            <div class="form-section">
              <div class="form-section-head" style="--sec-color:#10b981">
                <div class="sec-icon" style="background:#10b981">⭐</div>
                <div>
                  <div class="sec-label">Sección 6</div>
                  <div class="sec-title">Padrinos / Familiares</div>
                </div>
                <button @click.prevent="addPadrinoItem" class="sec-add-btn">+ Añadir</button>
              </div>
              <div class="form-section-body">
                <p class="sec-hint">Añade los padrinos principales. Si lo dejas vacío, esta sección no aparecerá.</p>
                <div class="itinerary-list">
                  <transition-group name="list">
                    <div v-for="(pad, index) in newInv.data.padrinos" :key="'pad'+index" class="itinerary-item">
                      <div class="iti-index">{{ index + 1 }}</div>
                      <div class="itinerary-grid" style="grid-template-columns: 1fr 1fr auto;">
                        <div class="iti-details" style="padding-left:0">
                          <input v-model="pad.role" placeholder="Rol (Ej: Padrinos de Anillos)" class="input input-light mb-0">
                        </div>
                        <div class="iti-details" style="padding-left:10px">
                          <input v-model="pad.name" placeholder="Nombre(s)" class="input input-light font-bold mb-0">
                        </div>
                        <div class="iti-action">
                          <button @click.prevent="removePadrinoItem(index)" class="btn-cancel btn-circle" title="Eliminar">×</button>
                        </div>
                      </div>
                    </div>
                  </transition-group>
                  <div v-if="!newInv.data.padrinos || newInv.data.padrinos.length === 0" class="iti-empty">
                    ⭐ Sin padrinos. Pulsa <strong>+ Añadir</strong> para agregar uno.
                  </div>
                </div>
              </div>
            </div>

            <!-- B6: Extras -->
            <div class="form-section">
              <div class="form-section-head" style="--sec-color:#8b5cf6">
                <div class="sec-icon" style="background:#8b5cf6">⚙️</div>
                <div>
                  <div class="sec-label">Sección 7</div>
                  <div class="sec-title">Extras (RSVP y Música)</div>
                </div>
              </div>
              <div class="form-section-body">
                <div v-if="newInv.template !== 'boda1'">
                  <label class="flabel">📲 WhatsApp para confirmaciones (con código de país)</label>
                  <input v-model="newInv.data.whatsapp_rsvp" placeholder="Ej: 59178945612" class="input input-light">
                </div>

                <!-- Música solo disponible en Boda 2 -->
                <div v-if="newInv.template === 'boda2'" class="mt-3">
                  <label class="flabel">🎵 Música de Fondo (MP3)</label>
                  <div v-if="isEditingInv && newInv.music_url && !newInv.music_file" class="music-saved-state">
                    <div class="music-saved-info">
                      <span class="music-saved-icon">🎵</span>
                      <div>
                        <strong>Música guardada</strong>
                        <small class="d-block text-muted">{{ getMusicFileName(newInv.music_url) }}</small>
                      </div>
                    </div>
                    <div class="music-saved-actions">
                      <label class="btn-music-change">🔄 Cambiar<input type="file" @change="handleMusicUpload" class="file-input" accept="audio/mpeg,audio/wav"></label>
                      <button type="button" @click="removeMusic" class="btn-music-remove">🗑️ Eliminar</button>
                    </div>
                  </div>
                  <div v-else-if="newInv.music_file" class="music-new-state">
                    <div class="music-saved-info">
                      <span class="music-saved-icon">🎶</span>
                      <div><strong>Lista para subir</strong><small class="d-block text-muted">{{ newInv.music_file.name }}</small></div>
                    </div>
                    <button type="button" @click="newInv.music_file = null" class="btn-music-remove">× Cancelar</button>
                  </div>
                  <div v-else>
                    <label class="file-label mt-2" style="display:inline-flex;align-items:center;gap:8px;">
                      📂 Seleccionar MP3/WAV
                      <input type="file" @change="handleMusicUpload" class="file-input" accept="audio/mpeg,audio/wav">
                    </label>
                  </div>
                </div>
                <div v-else class="music-not-available">
                  🔇 La plantilla <strong>Boda 1</strong> no incluye reproductor de música.
                </div>
              </div>
            </div>

          </div><!-- /boda-builder -->

          <!-- SECCIÓN C: Mensaje y Galería -->
          <div class="form-section">
            <div class="form-section-head" style="--sec-color:#64748b">
              <div class="sec-icon" style="background:#64748b">📝</div>
              <div>
                <div class="sec-label">Sección Final</div>
                <div class="sec-title">Mensaje y Galería</div>
              </div>
            </div>
            <div class="form-section-body">
              <label class="flabel">Mensaje / Descripción Corta</label>
              <textarea v-model="newInv.description" placeholder="Mensaje de bienvenida para los invitados..." class="input area-small"></textarea>

              <label class="flabel mt-3">📸 Galería de Fotos (múltiple)</label>
              <label class="file-label file-multiple">
                📂 Elegir Fotos
                <input type="file" @change="handleInvFiles" class="file-input" accept="image/*" multiple>
              </label>
              <div v-if="newInv.image_files.length > 0" class="file-success mt-2">
                {{ newInv.image_files.length }} archivo(s) listo(s) para subir
              </div>
              <div v-if="isEditingInv && newInv.kept_images.length > 0" class="kept-images-preview mt-3">
                <small>Fotos actuales (clic para eliminar):</small>
                <div class="preview-grid">
                  <div v-for="(img, idx) in newInv.kept_images" :key="idx" class="preview-item">
                    <img :src="img" alt="preview">
                    <button @click.prevent="removeKeptImage(idx)" class="btn-remove-img">×</button>
                  </div>
                </div>
              </div>


            </div>
          </div>

          <!-- BOTÓN GUARDAR -->
          <button @click="saveInvitation" class="inv-save-btn" :class="isEditingInv ? 'inv-save-update' : 'inv-save-new'">
            <span>{{ isEditingInv ? '💾 Guardar Cambios' : '🎉 Crear Invitación' }}</span>
          </button>

        </div><!-- /inv-form-body -->

        <div v-if="statsView.show" class="stats-overlay slide-in">
          <!-- HEADER -->
          <div class="stats-top-header">
            <div>
              <p class="stats-subtitle">Panel de Estadísticas</p>
              <h3 class="stats-title">{{ statsView.invitation.title }}</h3>
            </div>
            <div style="display:flex;gap:8px;align-items:center;">
              <button @click="refreshStats" class="stats-refresh-btn" title="Actualizar datos">
                <span :class="{ spinning: statsView.loading }">&#8635;</span> Actualizar
              </button>
              <button @click="statsView.show = false" class="stats-close-btn">×</button>
            </div>
          </div>

          <!-- KPI CARDS -->
          <div class="stats-kpi-grid">
            <div class="kpi-card kpi-blue">
              <div class="kpi-icon">👁️</div>
              <div class="kpi-body">
                <span class="kpi-value">{{ statsView.data.visits || 0 }}</span>
                <span class="kpi-label">Visitas Totales</span>
              </div>
            </div>
            <div class="kpi-card kpi-green">
              <div class="kpi-icon">✅</div>
              <div class="kpi-body">
                <span class="kpi-value">{{ confirmedCount }}</span>
                <span class="kpi-label">Confirmaron Asistencia</span>
              </div>
            </div>
            <div class="kpi-card kpi-purple">
              <div class="kpi-icon">👥</div>
              <div class="kpi-body">
                <span class="kpi-value">{{ totalGuests }}</span>
                <span class="kpi-label">Invitados Totales</span>
              </div>
            </div>
            <div class="kpi-card kpi-orange">
              <div class="kpi-icon">📝</div>
              <div class="kpi-body">
                <span class="kpi-value">{{ statsView.data.responses?.length || 0 }}</span>
                <span class="kpi-label">Respuestas Recibidas</span>
              </div>
            </div>
          </div>

          <!-- BARRA DE ASISTENCIA -->
          <div v-if="statsView.data.responses?.length > 0" class="attendance-bar-wrapper">
            <div class="attendance-bar-header">
              <span>📊 Tasa de Confirmación</span>
              <strong>{{ Math.round((confirmedCount / statsView.data.responses.length) * 100) }}%</strong>
            </div>
            <div class="attendance-track">
              <div class="attendance-fill" :style="{ width: Math.round((confirmedCount / statsView.data.responses.length) * 100) + '%' }"></div>
            </div>
          </div>

          <!-- LISTA DE RESPUESTAS -->
          <div class="responses-section">
            <h4 class="responses-title">📬 Respuestas Detalladas
              <span class="responses-count">{{ statsView.data.responses?.length || 0 }}</span>
            </h4>

            <div v-if="!statsView.data.responses || statsView.data.responses.length === 0" class="empty-responses">
              <div class="empty-icon">📤</div>
              <p>Aún no hay respuestas para esta invitación.</p>
            </div>

            <div v-else class="response-cards">
              <div v-for="res in statsView.data.responses" :key="res.id"
                   class="response-card" :class="res.attending === 'si' ? 'rcard-yes' : 'rcard-no'">
                <div class="rcard-avatar" :class="res.attending === 'si' ? 'av-green' : 'av-red'">
                  {{ res.name?.charAt(0)?.toUpperCase() || '?' }}
                </div>
                <div class="rcard-info">
                  <strong class="rcard-name">{{ res.name }}</strong>
                  <span v-if="res.message" class="rcard-msg">"​{{ res.message }}​"</span>
                </div>
                <div class="rcard-right">
                  <span class="rcard-badge" :class="res.attending === 'si' ? 'badge-yes' : 'badge-no'">
                    {{ res.attending === 'si' ? '✅ Confirma' : '❌ No asiste' }}
                  </span>
                  <span v-if="res.attending === 'si'" class="rcard-guests">
                    👥 {{ res.guests }} persona{{ res.guests != 1 ? 's' : '' }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="currentTab === 'clients' && !isEditingInv">
          <div class="grid">
            <div v-for="inv in clientInvitations" :key="inv.id" class="post-item" :class="{ 'editing-item': inv.id === editingInvId }">
              <div class="post-info">
                <strong>{{ inv.client_name || 'Sin nombre' }} - {{ inv.title }}</strong>
                <div class="post-badges">
                  <span class="badge">{{ inv.event_date ? new Date(inv.event_date).toLocaleDateString() : 'N/A' }}</span>
                </div>
              </div>
              <div class="actions">
                <button v-if="isAdmin" @click="openUserModal(inv)" class="btn-icon stats-icon" title="Asignar Usuario/Login">👤</button>
                <button @click="openStats(inv)" class="btn-icon stats-icon" title="Ver Estadísticas">📊</button>
                <button @click="editInvitation(inv)" class="btn-icon edit-icon" title="Editar">✏️</button>
                <button v-if="isAdmin" @click="deleteInvitation(inv.id, true)" class="btn-icon delete-icon" title="Eliminar">🗑️</button>
                <button @click="copyLink(inv.slug)" class="btn-icon" title="Copiar Link Privado">🔗</button>
              </div>
            </div>
            <div v-if="clientInvitations.length === 0" class="text-muted">No has creado ninguna invitación aún.</div>
          </div>
        </div>
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

    <!-- MODAL USUARIO CLIENTE -->
    <div v-if="userModal.show" class="modal-overlay">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Crear Usuario de Acceso</h3>
          <button @click="userModal.show = false" class="close-btn">×</button>
        </div>
        <div class="modal-body">
          <p v-if="userModal.isExistingUser" class="user-assigned-banner mb-4">
            ✅ Esta invitación ya tiene un usuario asignado. Puedes ver o actualizar sus datos.
          </p>
          <p v-else class="text-muted mb-4">
            Crea una cuenta para que el cliente <strong>{{ userModal.invitation?.title }}</strong> pueda gestionar su invitación.
          </p>
          <div class="form-group mb-3">
            <label>Nombre del Cliente</label>
            <input v-model="userModal.form.name" type="text" class="input input-light" placeholder="Ej: Marcela y Alejandro">
          </div>
          <div class="form-group mb-3">
            <label>Usuario de Acceso <small style="color:#94a3b8">(puede ser correo o un nombre, ej: marcelayboda)</small></label>
            <input v-model="userModal.form.email" type="text" class="input input-light" placeholder="Ej: marcelayalejandro o cliente@correo.com">
          </div>
          <div class="form-group mb-3">
            <label>Contraseña {{ userModal.isExistingUser ? '(dejar vacío para no cambiar)' : 'Temporal' }}</label>
            <input v-model="userModal.form.password" type="text" class="input input-light" :placeholder="userModal.isExistingUser ? 'Dejar vacío para mantener la actual' : 'Mínimo 6 caracteres'">
          </div>
          <button @click="assignUser" class="btn btn-success w-full mt-3" :disabled="userModal.isSubmitting">
             {{ userModal.isSubmitting ? 'Guardando...' : (userModal.isExistingUser ? 'Actualizar Acceso' : 'Crear y Asignar Cuenta') }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ===== MAP PICKER MODAL ===== -->
  <div v-if="mapPicker.show" class="mappicker-overlay">
    <div class="mappicker-modal">
      <div class="mappicker-header">
        <div>
          <p class="mappicker-sub">Elige la ubicación</p>
          <h3 class="mappicker-title">{{ mapPicker.target === 'ceremony' ? '⛪ Ceremonia' : '🥂 Recepción' }}</h3>
        </div>
        <button @click="mapPicker.show = false" class="mappicker-close">×</button>
      </div>

      <!-- Search bar -->
      <div class="mappicker-search">
        <input
          v-model="mapPicker.searchQuery"
          @keyup.enter="searchLocation"
          placeholder="Busca un lugar... ej: Catedral de Santa Cruz"
          class="input"
          style="margin:0"
        >
        <button @click="searchLocation" class="btn-search-map" :disabled="mapPicker.searching">
          {{ mapPicker.searching ? '...' : '🔍' }}
        </button>
      </div>
      <p class="mappicker-tip">O haz clic directamente en el mapa para colocar el marcador.</p>

      <!-- Map container -->
      <div id="adminMapPicker" class="mappicker-map"></div>

      <!-- Selected place info -->
      <div v-if="mapPicker.selected" class="mappicker-selected">
        <span class="mappicker-pin">📍</span>
        <div>
          <strong>{{ mapPicker.selectedName || 'Ubicación seleccionada' }}</strong>
          <small>Lat: {{ mapPicker.lat?.toFixed(6) }}, Lng: {{ mapPicker.lng?.toFixed(6) }}</small>
        </div>
      </div>

      <div class="mappicker-footer">
        <button @click="mapPicker.show = false" class="btn btn-cancel">Cancelar</button>
        <button @click="confirmMapLocation" class="btn btn-success" :disabled="!mapPicker.selected">
          ✅ Confirmar Ubicación
        </button>
      </div>
    </div>
  </div>

</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import axios from 'axios';

const props = defineProps({
  initialData: Object 
});
const homeUrl = props.initialData.homeUrl || '/';
const baseUrl = props.initialData.adminBaseUrl || '';
const currentTab = ref(props.initialData.user?.role === 'client' ? 'clients' : 'posts');
const mediaType = ref('upload');
const settings = ref(props.initialData.setting || { title: '', hero_text: '' });
const posts = ref(props.initialData.posts || []);
const invitations = ref(props.initialData.invitations || []);
const currentUser = ref(props.initialData.user || { role: 'client' });
const isAdmin = computed(() => currentUser.value.role === 'admin');

// User Creation Modal
const userModal = ref({
    show: false,
    invitation: null,
    form: { name: '', email: '', password: '' },
    isSubmitting: false,
    isExistingUser: false
});

// Notificaciones
const notification = ref({ show: false, message: '', type: 'success' });

// Estado de Edición
const isEditing = ref(false);
const editingId = ref(null);
const isEditingInv = ref(false);
const editingInvId = ref(null);
const invSubTab = ref('demos');

// Estadísticas
const statsView = ref({ show: false, loading: false, invitation: {}, data: { visits: 0, responses: [] } });
const confirmedCount = computed(() => statsView.value.data.responses.filter(r => r.attending === 'si').length);
const totalGuests = computed(() => statsView.value.data.responses.reduce((acc, current) => acc + (current.attending === 'si' ? current.guests : 0), 0));

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

const newInv = ref({
  title: '',
  slug: '',
  is_demo: false, // Added is_demo to newInv state
  template: 'boda1',
  client_name: '',
  event_date: '',
  description: '',
  preview_url: '',
  image_files: [],
  kept_images: [],
  music_file: null,
  music_url: '',
  data: { itinerary: [], theme_color: '#ba966a' }
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
    await axios.post(`${baseUrl}/settings`, settings.value);
    showToast('Configuración actualizada correctamente');
  } catch (e) { showToast('Error al actualizar', 'error'); }
};

const handleMusicUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        newInv.value.music_file = file;
        showToast('Música seleccionada ✓');
    }
};

const getMusicFileName = (url) => {
  if (!url) return '';
  try {
    const parts = url.split('/');
    const filename = decodeURIComponent(parts[parts.length - 1]);
    // Quitar timestamp prefix si existe (ej: 1709000000_cancion.mp3)
    return filename.replace(/^\d+_/, '');
  } catch(e) { return url; }
};

const removeMusic = () => {
  newInv.value.music_url = '';
  newInv.value.music_file = null;
  showToast('Música eliminada. Guarda la invitación para aplicar los cambios.', 'success');
};;

const openStats = async (inv) => {
    statsView.value.invitation = inv;
    statsView.value.show = true;
    statsView.value.loading = true;
    try {
        const res = await axios.get(`${baseUrl}/invitations/${inv.id}/stats`);
        statsView.value.data = res.data;
    } catch (e) { showToast('Error al cargar estadísticas', 'error'); }
    finally { statsView.value.loading = false; }
};

const refreshStats = async () => {
    if (!statsView.value.invitation?.id) return;
    statsView.value.loading = true;
    try {
        const res = await axios.get(`${baseUrl}/invitations/${statsView.value.invitation.id}/stats`);
        statsView.value.data = res.data;
        showToast('Estadísticas actualizadas', 'success');
    } catch (e) { showToast('Error al actualizar estadísticas', 'error'); }
    finally { statsView.value.loading = false; }
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

// Funciones de Invitación
const demoInvitations = computed(() => {
  return invitations.value.filter(inv => inv.is_demo);
});

const clientInvitations = computed(() => {
  return invitations.value.filter(inv => !inv.is_demo);
});

const getTemplateName = (templateCode) => {
  if (templateCode === 'boda1') return 'Boda Especial (Boda 1)';
  if (templateCode === 'boda2') return 'Boda Moderna (Boda 2)';
  if (templateCode === 'custom') return 'Personalizada';
  return templateCode || 'Estándar';
};

const copyLink = (slug) => {
  if (!slug) return showToast('No hay URL configurada', 'error');
  const path = `${window.location.origin}/invitacion/${slug}`;
  navigator.clipboard.writeText(path).then(() => {
    showToast('Enlace de invitación copiado al portapapeles', 'success');
  });
};

const resetInvForm = () => {
  isEditingInv.value = false;
  editingInvId.value = null;
  newInv.value = {
    title: '', client_name: '', event_date: '', description: '', preview_url: '',
    slug: '', is_demo: false, template: 'boda1',
    image_files: [], kept_images: [], music_file: null,
    data: { 
      itinerary: [], 
      padrinos: [
        { role: 'Padrinos de Anillos', name: '' },
        { role: 'Padrinos de Civil', name: '' }
      ], 
      theme_color: '#ba966a' 
    }
  };
  previewImage.value = false;
  mediaType.value = 'upload';
};

const openInvForm = () => {
  resetInvForm();
  newInv.value.is_demo = (currentTab.value === 'templates');
};

const addItineraryItem = () => {
  newInv.value.data.itinerary.push({ time: '', activity: '', description: '' });
};

const removeItineraryItem = (index) => {
  newInv.value.data.itinerary.splice(index, 1);
};

const addPadrinoItem = () => {
  if (!newInv.value.data.padrinos) newInv.value.data.padrinos = [];
  newInv.value.data.padrinos.push({ role: '', name: '' });
};

const removePadrinoItem = (index) => {
  newInv.value.data.padrinos.splice(index, 1);
};

const handleInvFiles = (event) => {
  newInv.value.image_files = Array.from(event.target.files);
};

const removeKeptImage = (index) => {
  newInv.value.kept_images.splice(index, 1);
};

const saveInvitation = async () => {
  if (!newInv.value.title) return showToast('El título es obligatorio', 'error');

  let formData = new FormData();
  formData.append('title', newInv.value.title);
  if (newInv.value.slug) formData.append('slug', newInv.value.slug);
  formData.append('template', newInv.value.template);
  formData.append('is_demo', newInv.value.is_demo ? 1 : 0);
  
  if (newInv.value.client_name) formData.append('client_name', newInv.value.client_name);
  if (newInv.value.event_date) formData.append('event_date', newInv.value.event_date);
  if (newInv.value.description) formData.append('description', newInv.value.description);
  if (newInv.value.preview_url) formData.append('preview_url', newInv.value.preview_url);
  formData.append('data', JSON.stringify(newInv.value.data));
  
  if (newInv.value.music_file) {
      formData.append('music_file', newInv.value.music_file);
  } else if (isEditingInv.value && newInv.value.music_url === '') {
      // El usuario eliminó la música, notificar al backend
      formData.append('remove_music', '1');
  }

  if (newInv.value.kept_images.length > 0) formData.append('kept_images', JSON.stringify(newInv.value.kept_images));

  newInv.value.image_files.forEach((file, index) => {
    formData.append(`images[${index}]`, file);
  });

  try {
    let base = baseUrl || '/admin';
    if (!base.startsWith('/')) base = '/' + base;
    
    let url = `${base}/invitations`;
    let msg = '¡Invitación creada exitosamente!';

    if (isEditingInv.value) {
      url = `${base}/invitations/${editingInvId.value}`; 
      formData.append('_method', 'PUT'); 
      msg = '¡Invitación actualizada correctamente!';
    }

    await axios.post(url, formData, { headers: { 'Content-Type': 'multipart/form-data' }});
    showToast(msg, 'success');
    setTimeout(() => { window.location.reload(); }, 1500);

  } catch (e) {
    showToast('Error al guardar invitación', 'error');
  }
};

const deleteInvitation = async (id) => {
  if (!confirm('¿Seguro que deseas eliminar esta invitación?')) return;
  try {
    await axios.delete(`${baseUrl}/invitations/${id}`);
    invitations.value = invitations.value.filter(inv => inv.id !== id);
    if (editingInvId.value === id) resetInvForm();
    showToast('Invitación eliminada', 'success');
  } catch (e) {
    showToast('Error al eliminar invitación', 'error');
  }
};

const editInvitation = (inv) => {
  isEditingInv.value = true;
  editingInvId.value = inv.id;
  
  newInv.value = { 
    ...inv,
    event_date: inv.event_date ? inv.event_date.split('T')[0] : '',
    slug: inv.slug || '',
    is_demo: inv.is_demo ? true : false,
    template: inv.template || 'boda1',
    music_url: inv.data?.music_url || '',
    music_file: null,
    data: inv.data || { itinerary: [], padrinos: [], theme_color: '#ba966a' },
    image_files: [],
    kept_images: inv.gallery_images ? [...inv.gallery_images] : []
  };

  if (!newInv.value.data.theme_color) newInv.value.data.theme_color = '#ba966a';
  if (!newInv.value.data.itinerary) newInv.value.data.itinerary = [];
  if (!newInv.value.data.padrinos) newInv.value.data.padrinos = [];

  // Cambiar a la pestaña correcta para mostrar el formulario
  currentTab.value = inv.is_demo ? 'templates' : 'clients';

  window.scrollTo({ top: 0, behavior: 'smooth' });
  showToast('Modo edición de invitación activado', 'success');
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
    let url = `${baseUrl}/posts`;
    let msg = '¡Publicación creada exitosamente!';

    if (isEditing.value) {
      url = `${baseUrl}/posts/${editingId.value}`; 
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
    await axios.delete(`${baseUrl}/posts/${id}`);
    posts.value = posts.value.filter(post => post.id !== id);
    if (editingId.value === id) resetForm();
    showToast('Publicación eliminada', 'success');
  } catch (e) {
    showToast('Error al eliminar', 'error');
  }
};

const openUserModal = (inv) => {
    userModal.value.invitation = inv;

    // Si ya tiene un usuario asignado, mostramos sus datos reales
    if (inv.user) {
        userModal.value.form = {
            name: inv.user.name || inv.client_name || '',
            email: inv.user.email || '',
            password: '' // Se deja vacío; solo cambiar si se quiere actualizar
        };
        userModal.value.isExistingUser = true;
    } else {
        userModal.value.form = {
            name: inv.client_name || inv.title,
            email: '',
            password: Math.random().toString(36).slice(-8)
        };
        userModal.value.isExistingUser = false;
    }

    userModal.value.show = true;
};

const assignUser = async () => {
    if (!userModal.value.form.email || !userModal.value.form.password) {
        return showToast('El usuario y la contraseña son obligatorios', 'error');
    }
    userModal.value.isSubmitting = true;
    try {
        const res = await axios.post(`${baseUrl}/invitations/${userModal.value.invitation.id}/assign-user`, userModal.value.form);
        showToast(res.data.message, 'success');
        userModal.value.show = false;
        // Opcional: Recargar o actualizar localmente
        setTimeout(() => window.location.reload(), 1500);
    } catch (e) {
        const msg = e.response?.data?.message || 'Error al crear usuario';
        showToast(msg, 'error');
    } finally {
        userModal.value.isSubmitting = false;
    }
};

const logout = async () => {
  try {
    await axios.post('/logout');
    window.location.href = `${baseUrl}/login`;
  } catch (e) { 
    showToast('Error al cerrar sesión', 'error'); 
  }
};

// ===== MAP PICKER (Leaflet + OpenStreetMap - 100% Gratis) =====
const mapPicker = ref({
  show: false,
  target: 'ceremony', // 'ceremony' | 'reception'
  searchQuery: '',
  searching: false,
  selected: false,
  selectedName: '',
  lat: null,
  lng: null
});

let leafletMap = null;
let leafletMarker = null;

const loadLeaflet = () => new Promise((resolve, reject) => {
  if (window.L) { resolve(); return; }
  const link = document.createElement('link');
  link.rel = 'stylesheet';
  link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
  document.head.appendChild(link);
  const script = document.createElement('script');
  script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
  script.onload = resolve;
  script.onerror = reject;
  document.head.appendChild(script);
});

const initMap = (lat = -17.78, lng = -63.18) => {
  if (leafletMap) { leafletMap.remove(); leafletMap = null; leafletMarker = null; }
  const L = window.L;
  leafletMap = L.map('adminMapPicker').setView([lat, lng], 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  }).addTo(leafletMap);

  leafletMap.on('click', async (e) => {
    const { lat, lng } = e.latlng;
    placeMarker(lat, lng);
    // Reverse geocode with Nominatim to get place name
    try {
      const res = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`);
      const data = await res.json();
      mapPicker.value.selectedName = data.display_name?.split(',').slice(0,2).join(', ') || '';
    } catch(e) { mapPicker.value.selectedName = ''; }
  });
};

const placeMarker = (lat, lng) => {
  const L = window.L;
  if (leafletMarker) leafletMarker.remove();
  leafletMarker = L.marker([lat, lng], {
    icon: L.icon({
      iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
      shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
      iconSize: [25, 41], iconAnchor: [12, 41]
    })
  }).addTo(leafletMap);
  mapPicker.value.lat = lat;
  mapPicker.value.lng = lng;
  mapPicker.value.selected = true;
};

const openMapPicker = async (target) => {
  mapPicker.value.target = target;
  mapPicker.value.show = true;
  mapPicker.value.selected = false;
  mapPicker.value.selectedName = '';
  mapPicker.value.lat = null;
  mapPicker.value.lng = null;
  mapPicker.value.searchQuery = '';
  await loadLeaflet();
  await nextTick();
  setTimeout(() => initMap(), 100);
};

const searchLocation = async () => {
  if (!mapPicker.value.searchQuery.trim()) return;
  mapPicker.value.searching = true;
  try {
    const q = encodeURIComponent(mapPicker.value.searchQuery);
    const res = await fetch(`https://nominatim.openstreetmap.org/search?q=${q}&format=json&limit=1`);
    const data = await res.json();
    if (data.length > 0) {
      const { lat, lon, display_name } = data[0];
      const latF = parseFloat(lat), lngF = parseFloat(lon);
      leafletMap.setView([latF, lngF], 16);
      placeMarker(latF, lngF);
      mapPicker.value.selectedName = display_name?.split(',').slice(0,2).join(', ') || '';
    } else {
      showToast('No se encontró el lugar, intenta con otro nombre', 'error');
    }
  } catch(e) { showToast('Error al buscar ubicación', 'error'); }
  finally { mapPicker.value.searching = false; }
};

const confirmMapLocation = () => {
  if (!mapPicker.value.selected) return;
  const { lat, lng, target } = mapPicker.value;
  const gmapsUrl = `https://www.google.com/maps?q=${lat},${lng}`;
  if (target === 'ceremony') {
    newInv.value.data.ceremony_map = gmapsUrl;
    if (!newInv.value.data.ceremony_location && mapPicker.value.selectedName) {
      newInv.value.data.ceremony_location = mapPicker.value.selectedName;
    }
  } else {
    newInv.value.data.reception_map = gmapsUrl;
    if (!newInv.value.data.reception_location && mapPicker.value.selectedName) {
      newInv.value.data.reception_location = mapPicker.value.selectedName;
    }
  }
  if (leafletMap) { leafletMap.remove(); leafletMap = null; }
  mapPicker.value.show = false;
  showToast('✅ Ubicación guardada correctamente', 'success');
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
.sidebar nav ul { list-style: none; padding: 0; margin: 0; }
.sidebar nav li {
  padding: 14px 16px; color: #94a3b8; cursor: pointer; font-size: 1rem; margin-bottom: 8px; 
  border-radius: 8px; transition: all 0.2s; font-weight: 500; display: flex; align-items: center; gap: 10px;
}
.sidebar nav li:hover { background: rgba(255,255,255,0.05); color: white; }
.sidebar nav li.active { background: #3b82f6; color: white; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }

.logout-tab { margin-top: 40px !important; color: #ef4444 !important; }
.logout-tab:hover { background: rgba(239, 68, 68, 0.1) !important; }

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

/* IMAGES PREVIEW */
.preview-grid { display: flex; gap: 15px; flex-wrap: wrap; }
.preview-item { position: relative; width: 100px; height: 100px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
.preview-item img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; }
.btn-remove-img { position: absolute; top: -5px; right: -5px; background: #ef4444; color: white; border: none; border-radius: 50%; width: 22px; height: 22px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); transition: 0.2s;}
.btn-remove-img:hover { background: #dc2626; transform: scale(1.1); }

/* RESPONSIVE DESIGN */
@media (max-width: 768px) {
  .admin-layout { flex-direction: column; }
  .sidebar { width: 100%; padding: 15px; display: flex; flex-direction: row; flex-wrap: wrap; align-items: center; justify-content: space-between; }
  .sidebar-header h2 { margin-bottom: 0; font-size: 1.2rem; }
  .sidebar nav { display: flex; flex-wrap: wrap; gap: 5px; width: 100%; margin-top: 15px; }
  .sidebar nav button { width: auto; flex: 1 1 calc(50% - 5px); padding: 10px; font-size: 0.9rem; justify-content: center; margin-bottom: 0; }
  .logout-btn { margin-top: 0 !important; width: 100% !important; flex: 1 1 100% !important; justify-content: center; }
  .back-link { margin-top: 15px; width: 100%; }
  
  .content { padding: 20px 15px; }
  .panel { padding: 20px; border-radius: 12px; }
  .row { flex-direction: column; gap: 0; }
  .col-small { width: 100%; }
  .post-item { flex-direction: column; align-items: flex-start; gap: 15px; }
  .actions { width: 100%; justify-content: flex-end; }
}

/* MODAL OVERLAY */
.modal-overlay {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px);
  display: flex; align-items: center; justify-content: center; z-index: 2000;
}
.modal-content {
  background: white; width: 90%; max-width: 500px; padding: 30px;
  border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
  animation: modalIn 0.3s ease-out;
}
@keyframes modalIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; }
.close-btn { background: none; border: none; font-size: 1.8rem; color: #94a3b8; cursor: pointer; }

/* STATS PANEL - MODERN REDESIGN */
.stats-overlay {
  background: white;
  border-radius: 20px;
  padding: 30px;
  border: 1px solid #e2e8f0;
  margin-top: 20px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.08);
}
.stats-top-header {
  display: flex; justify-content: space-between; align-items: flex-start;
  margin-bottom: 28px; padding-bottom: 20px;
  border-bottom: 2px solid #f1f5f9;
}
.stats-subtitle { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 2px; color: #94a3b8; margin: 0 0 4px; font-weight: 700; }
.stats-title { font-size: 1.4rem; font-weight: 800; color: #0f172a; margin: 0; }
.stats-close-btn {
  background: #f1f5f9; border: none; border-radius: 50%;
  width: 36px; height: 36px; font-size: 1.4rem; color: #64748b;
  cursor: pointer; display: flex; align-items: center; justify-content: center;
  transition: 0.2s; flex-shrink: 0;
}
.stats-close-btn:hover { background: #fee2e2; color: #ef4444; }

.stats-refresh-btn {
  background: #f0fdf4; border: 1px solid #bbf7d0; color: #059669;
  border-radius: 10px; padding: 8px 16px; font-size: 0.88rem; font-weight: 700;
  cursor: pointer; display: flex; align-items: center; gap: 6px; transition: 0.2s;
}
.stats-refresh-btn:hover { background: #d1fae5; }
.spinning { display: inline-block; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* FORM - TIME & MAP FIELDS */
.time-label, .map-label {
  display: block; font-size: 0.8rem; font-weight: 700; color: #64748b;
  text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;
}
.map-input-group { margin-top: 8px; }
.map-hint {
  display: block; font-size: 0.78rem; color: #94a3b8;
  line-height: 1.5; margin-top: 4px;
}
.map-hint a { color: #3b82f6; text-decoration: underline; }

/* KPI CARDS */
.stats-kpi-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 16px; margin-bottom: 28px;
}
.kpi-card {
  border-radius: 16px; padding: 20px 18px;
  display: flex; align-items: center; gap: 14px;
  position: relative; overflow: hidden;
}
.kpi-card::after {
  content: ''; position: absolute; right: -15px; top: -15px;
  width: 80px; height: 80px; border-radius: 50%;
  background: rgba(255,255,255,0.15);
}
.kpi-blue  { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; }
.kpi-green { background: linear-gradient(135deg, #10b981, #059669); color: white; }
.kpi-purple{ background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: white; }
.kpi-orange{ background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
.kpi-icon { font-size: 1.6rem; flex-shrink: 0; }
.kpi-body { display: flex; flex-direction: column; }
.kpi-value { font-size: 2rem; font-weight: 900; line-height: 1; }
.kpi-label { font-size: 0.78rem; font-weight: 600; opacity: 0.85; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; }

/* ATTENDANCE BAR */
.attendance-bar-wrapper {
  background: #f8fafc; border-radius: 14px; padding: 18px 22px;
  margin-bottom: 28px; border: 1px solid #e2e8f0;
}
.attendance-bar-header {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 10px; font-size: 0.9rem; color: #475569; font-weight: 600;
}
.attendance-bar-header strong { font-size: 1.1rem; color: #10b981; }
.attendance-track {
  height: 10px; background: #e2e8f0; border-radius: 99px; overflow: hidden;
}
.attendance-fill {
  height: 100%; background: linear-gradient(90deg, #10b981, #34d399);
  border-radius: 99px; transition: width 0.8s cubic-bezier(0.4,0,0.2,1);
}

/* RESPONSE CARDS */
.responses-section { margin-top: 4px; }
.responses-title {
  font-size: 1rem; font-weight: 700; color: #1e293b;
  margin: 0 0 16px; display: flex; align-items: center; gap: 10px;
}
.responses-count {
  background: #e2e8f0; color: #475569;
  border-radius: 99px; padding: 2px 10px;
  font-size: 0.8rem; font-weight: 700;
}
.empty-responses {
  text-align: center; padding: 40px 20px;
  color: #94a3b8; background: #f8fafc; border-radius: 14px;
  border: 2px dashed #e2e8f0;
}
.empty-icon { font-size: 2.5rem; margin-bottom: 10px; }
.response-cards { display: flex; flex-direction: column; gap: 10px; }
.response-card {
  display: flex; align-items: center; gap: 14px;
  padding: 14px 18px; border-radius: 14px;
  border: 1px solid; transition: 0.2s;
}
.rcard-yes { background: #f0fdf4; border-color: #bbf7d0; }
.rcard-no  { background: #fff1f2; border-color: #fecdd3; }
.rcard-avatar {
  width: 42px; height: 42px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-weight: 800; font-size: 1.1rem; flex-shrink: 0;
}
.av-green { background: #d1fae5; color: #059669; }
.av-red   { background: #fee2e2; color: #ef4444; }
.rcard-info { flex: 1; min-width: 0; }
.rcard-name { display: block; font-weight: 700; font-size: 0.95rem; color: #1e293b; }
.rcard-msg  { display: block; font-size: 0.82rem; color: #64748b; font-style: italic; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 300px; }
.rcard-right { display: flex; flex-direction: column; align-items: flex-end; gap: 4px; flex-shrink: 0; }
.rcard-badge { font-size: 0.8rem; font-weight: 700; padding: 4px 12px; border-radius: 99px; }
.badge-yes { background: #d1fae5; color: #059669; }
.badge-no  { background: #fee2e2; color: #ef4444; }
.rcard-guests { font-size: 0.78rem; color: #64748b; font-weight: 600; }

.table-container { overflow-x: auto; margin-top: 15px; }
.modern-table { width: 100%; border-collapse: collapse; text-align: left; }
.modern-table th { padding: 12px; border-bottom: 2px solid #f1f5f9; color: #64748b; font-size: 0.85rem; }
.modern-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }
.video-badge-green { background: #d1fae5; color: #059669; }
.stats-icon:hover { background: #e0f2fe; color: #0284c7; border-color: #7dd3fc; }

.user-assigned-banner {
  background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7;
  border-radius: 10px; padding: 12px 16px; font-size: 0.9rem; font-weight: 600;
}

/* MAP DUAL INPUT */
.map-dual-input {
  display: flex; gap: 8px; align-items: stretch;
}
.btn-pick-map {
  background: #3b82f6; color: white; border: none;
  border-radius: 8px; padding: 10px 14px; font-size: 0.85rem;
  font-weight: 700; cursor: pointer; white-space: nowrap;
  transition: 0.2s; flex-shrink: 0;
}
.btn-pick-map:hover { background: #2563eb; transform: translateY(-1px); }

/* MAP PICKER MODAL */
.mappicker-overlay {
  position: fixed; inset: 0;
  background: rgba(15,23,42,0.75); backdrop-filter: blur(6px);
  z-index: 9999; display: flex; align-items: center; justify-content: center;
  padding: 20px;
}
.mappicker-modal {
  background: white; border-radius: 24px;
  width: 100%; max-width: 720px;
  display: flex; flex-direction: column;
  max-height: 90vh; overflow: hidden;
  box-shadow: 0 30px 80px rgba(0,0,0,0.4);
  animation: modalIn 0.3s ease-out;
}
.mappicker-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 20px 24px; border-bottom: 1px solid #f1f5f9; flex-shrink: 0;
}
.mappicker-sub { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 2px; color: #94a3b8; margin: 0 0 3px; font-weight: 700; }
.mappicker-title { font-size: 1.3rem; font-weight: 800; color: #0f172a; margin: 0; }
.mappicker-close {
  background: #f1f5f9; border: none; border-radius: 50%;
  width: 38px; height: 38px; font-size: 1.5rem; color: #64748b;
  cursor: pointer; display: flex; align-items: center; justify-content: center;
  transition: 0.2s;
}
.mappicker-close:hover { background: #fee2e2; color: #ef4444; }

.mappicker-search {
  display: flex; gap: 8px; padding: 16px 24px 8px; flex-shrink: 0;
}
.btn-search-map {
  background: #3b82f6; color: white; border: none;
  border-radius: 8px; padding: 0 18px; font-size: 1.1rem;
  cursor: pointer; flex-shrink: 0; transition: 0.2s;
}
.btn-search-map:hover:not(:disabled) { background: #2563eb; }
.btn-search-map:disabled { opacity: 0.5; }

.mappicker-tip {
  font-size: 0.78rem; color: #94a3b8; padding: 0 24px 8px;
  margin: 0; flex-shrink: 0;
}

.mappicker-map {
  flex: 1; min-height: 340px; background: #e2e8f0; flex-shrink: 0;
}

.mappicker-selected {
  display: flex; gap: 12px; align-items: center;
  padding: 12px 24px;
  background: #f0fdf4; border-top: 1px solid #bbf7d0;
  flex-shrink: 0;
}
.mappicker-pin { font-size: 1.4rem; }
.mappicker-selected strong { display: block; font-size: 0.9rem; color: #065f46; }
.mappicker-selected small { font-size: 0.75rem; color: #94a3b8; }

.mappicker-footer {
  display: flex; gap: 10px; justify-content: flex-end;
  padding: 14px 24px; border-top: 1px solid #f1f5f9; flex-shrink: 0;
}
.mappicker-footer .btn { width: auto; margin: 0; padding: 10px 22px; }

/* MUSIC SAVED STATE */
.music-saved-state, .music-new-state {
  display: flex; align-items: center; justify-content: space-between;
  gap: 12px; padding: 12px 16px;
  border-radius: 12px; margin-top: 8px;
}
.music-saved-state { background: #f0fdf4; border: 1px solid #bbf7d0; }
.music-new-state { background: #eff6ff; border: 1px solid #bfdbfe; }
.music-saved-info { display: flex; align-items: center; gap: 12px; }
.music-saved-icon { font-size: 1.5rem; }
.music-saved-info strong { display: block; font-size: 0.9rem; color: #1e293b; }
.music-saved-actions { display: flex; gap: 8px; flex-shrink: 0; }
.btn-music-change {
  background: #3b82f6; color: white; border: none;
  border-radius: 8px; padding: 8px 14px; font-size: 0.82rem;
  font-weight: 700; cursor: pointer; display: flex; align-items: center;
  transition: 0.2s; white-space: nowrap;
}
.btn-music-change:hover { background: #2563eb; }
.btn-music-remove {
  background: #fee2e2; color: #ef4444; border: 1px solid #fca5a5;
  border-radius: 8px; padding: 8px 14px; font-size: 0.82rem;
  font-weight: 700; cursor: pointer; white-space: nowrap; transition: 0.2s;
}
.btn-music-remove:hover { background: #fca5a5; }
.music-not-available {
  background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px;
  padding: 12px 16px; font-size: 0.88rem; color: #64748b; margin-top: 12px;
}
.d-block { display: block; }

/* RE-DISEÑO DEL FORMULARIO DE INVITACIÓN */
.inv-form-header {
  display: flex; gap: 16px; align-items: center; background: #f8fafc;
  padding: 20px 24px; border-radius: 16px; margin-bottom: 24px;
}
.inv-form-icon { font-size: 2.2rem; }
.inv-form-title { margin: 0; font-size: 1.25rem; color: #1e293b; font-weight: 800; }
.inv-form-sub { margin: 4px 0 0; color: #64748b; font-size: 0.9rem; }
.inv-cancel-btn { 
  margin-left: auto; background: white; border: 1px solid #cbd5e1;
  padding: 8px 16px; border-radius: 8px; cursor: pointer; color: #475569;
  font-weight: 600; transition: 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
.inv-cancel-btn:hover { background: #f1f5f9; border-color: #94a3b8; }

.form-section {
  background: white; border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -2px rgba(0,0,0,0.02);
  border: 1px solid #f1f5f9; margin-bottom: 24px; overflow: hidden;
}
.form-section-head {
  display: flex; gap: 14px; align-items: center; padding: 16px 20px;
  background: linear-gradient(to right, rgba(238,242,255,0.4), transparent);
  border-bottom: 1px solid #f1f5f9; position: relative;
}
.form-section-head::before {
  content: ''; position: absolute; left: 0; top: 0; bottom: 0;
  width: 4px; background: var(--sec-color, #e2e8f0);
}
.sec-icon { 
  width: 42px; height: 42px; border-radius: 12px; display: flex;
  align-items: center; justify-content: center; font-size: 1.2rem;
  color: white; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.sec-label { font-size: 0.75rem; font-weight: 700; color: var(--sec-color); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px; }
.sec-title { font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0; }
.sec-hint { margin: 0 0 16px; color: #64748b; font-size: 0.9rem; }

.form-section-body { padding: 24px; }

.flabel { display: block; font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 8px; }

.boda-builder { margin-top: 32px; }

.color-pick-row { display: flex; gap: 12px; align-items: center; }
.color-picker { width: 44px; height: 44px; padding: 0; border: none; border-radius: 8px; overflow: hidden; cursor: pointer; }
.color-picker::-webkit-color-swatch-wrapper { padding: 0; }
.color-picker::-webkit-color-swatch { border: none; border-radius: 8px; box-shadow: inset 0 0 0 1px rgba(0,0,0,0.1); }
.color-chip { width: 100%; height: 44px; border-radius: 8px; flex: 1; box-shadow: inset 0 0 0 1px rgba(0,0,0,0.1); }
.color-preview-banner {
  padding: 16px; background: #f8fafc; border-radius: 12px; border-left: 4px solid;
  font-size: 0.9rem; margin-top: 8px;
}

.names-amp { font-size: 2rem; color: #cbd5e1; font-weight: 300; font-family: serif; display: flex; align-items: center; justify-content: center; padding-top: 24px; }
.input-elegant { font-size: 1.1rem; padding: 12px 16px; font-weight: 500; font-family: 'Cormorant Garamond', serif; border-color: #cbd5e1; }
.input-elegant:focus { border-color: #94a3b8; }

.location-col { background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px dashed #cbd5e1; }
.location-col-header { font-size: 1.1rem; font-weight: 800; margin-bottom: 16px; color: #1e293b; display: flex; align-items: center; gap: 8px; }
.location-divider { display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #cbd5e1; width: 40px; }

.sec-add-btn {
  margin-left: auto; background: #f1f5f9; color: #0f172a; border: 1px solid #cbd5e1;
  padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 700;
  cursor: pointer; transition: 0.2s;
}
.sec-add-btn:hover { background: #e2e8f0; }

.iti-index { 
  width: 28px; height: 28px; background: #f1f5f9; color: #64748b;
  border-radius: 50%; display: flex; align-items: center; justify-content: center;
  font-size: 0.8rem; font-weight: 800; flex-shrink: 0;
}
.itinerary-grid { flex: 1; display: grid; grid-template-columns: 130px 1fr auto; gap: 16px; align-items: start; }
.iti-empty { text-align: center; padding: 32px 20px; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1; color: #64748b; font-size: 0.95rem; }

.inv-save-btn {
  width: 100%; padding: 18px; border: none; border-radius: 16px;
  font-size: 1.1rem; font-weight: 800; color: white; cursor: pointer;
  transition: all 0.2s; margin-top: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  display: flex; align-items: center; justify-content: center; gap: 8px;
}
.inv-save-new { background: linear-gradient(135deg, #3b82f6, #6366f1); }
.inv-save-new:hover { background: linear-gradient(135deg, #2563eb, #4f46e5); transform: translateY(-2px); box-shadow: 0 6px 16px rgba(59, 130, 246, 0.3); }
.inv-save-update { background: linear-gradient(135deg, #f59e0b, #d97706); }
.inv-save-update:hover { background: linear-gradient(135deg, #d97706, #b45309); transform: translateY(-2px); box-shadow: 0 6px 16px rgba(245, 158, 11, 0.3); }

/* Para responsiveness */
@media (max-width: 768px) {
  .itinerary-grid { grid-template-columns: 1fr; gap: 12px; }
  .location-divider { display: none; }
  .names-amp { padding-top: 0; padding-bottom: 12px; }
}
</style>