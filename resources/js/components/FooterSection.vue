<template>
  <footer class="footer-wrapper">
    <div class="footer-content container">
      
      <div class="footer-col brand-col">
        <a href="/sitio" class="brand-link">
          <div class="logo-box">
            <img :src="'/images/logo.jpg'" alt="Logo" class="footer-logo">
          </div>
          <span class="brand-text">EL<span class="highlight">.EDEN</span></span>
        </a>
        <p class="brand-desc">
          Transformando ideas en experiencias digitales. Tecnología, innovación y diseño en un solo lugar.
        </p>
      </div>

      <div class="footer-col links-col">
        <h4>Explorar</h4>
        <ul>
          <li><a href="/sitio">Inicio</a></li>
          <li><a href="/sitio/categoria/eventos">Eventos</a></li>
          <li><a href="/sitio/categoria/tecnologia">Tecnología</a></li>
          <li><a href="/sitio/categoria/portafolio">Portafolio</a></li>
        </ul>
      </div>

      <div class="footer-col contact-col">
        <h4>Contáctanos</h4>
        <ul class="contact-list">
          <li>
            <span class="icon">📞</span>
            <a href="https://wa.me/59174464344" target="_blank" class="phone-link">
              Whatsapp
            </a>
          </li>
          <li>
            <span class="icon">📧</span>
            <span>contacto@webeleden.com</span>
          </li>
          <li>
            <span class="icon">📍</span>
            <span>Latino</span>
          </li>
        </ul>
      </div>

      <div class="footer-col social-col">
        <h4>Síguenos</h4>
        <div class="social-icons">
          <a href="#" class="social-btn fb">FB</a>
          <a href="#" class="social-btn ig">IG</a>
          <a href="#" class="social-btn tk">TK</a>
        </div>
      </div>

    </div>

    <div class="copyright-bar">
      <div class="container">
        <p>&copy; 2026 ELEDEN. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap');

.footer-wrapper {
  background-color: #0f172a; /* Azul oscuro elegante */
  color: #94a3b8; /* Gris suave para texto */
  font-family: 'Plus Jakarta Sans', sans-serif;
  padding-top: 80px;
  position: relative;
  overflow: hidden;
}

/* CONTENEDOR CENTRADO */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
}

.footer-content {
  display: grid;
  grid-template-columns: 2fr 1fr 1.5fr 1fr; /* Diseño asimétrico moderno */
  gap: 40px;
  padding-bottom: 60px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

/* COLUMNA BRAND */
.brand-link {
  display: flex; align-items: center; gap: 12px;
  text-decoration: none; color: white; font-size: 1.5rem; font-weight: 800;
  margin-bottom: 20px;
}
.logo-box {
  width: 45px; height: 45px; border-radius: 10px; overflow: hidden;
  box-shadow: 0 0 15px rgba(59, 130, 246, 0.3);
}
.footer-logo { width: 100%; height: 100%; object-fit: cover; }
.highlight { color: #3b82f6; }
.brand-desc { line-height: 1.6; font-size: 0.95rem; max-width: 300px; }

/* TITULOS DE COLUMNA */
h4 {
  color: white; font-size: 1.1rem; font-weight: 700; margin-bottom: 24px;
  position: relative; display: inline-block;
}
h4::after {
  content: ''; position: absolute; left: 0; bottom: -8px;
  width: 30px; height: 3px; background: #3b82f6; border-radius: 2px;
}

/* ENLACES */
ul { list-style: none; padding: 0; margin: 0; }
li { margin-bottom: 12px; }
a { text-decoration: none; color: #94a3b8; transition: color 0.3s, transform 0.2s; display: inline-block; }
a:hover { color: white; transform: translateX(5px); }

/* CONTACTO */
.contact-list li { display: flex; align-items: center; gap: 12px; }
.icon { font-size: 1.2rem; }
.phone-link { color: #3b82f6; font-weight: 600; }

/* REDES SOCIALES */
.social-icons { display: flex; gap: 10px; }
.social-btn {
  width: 40px; height: 40px; border-radius: 8px;
  background: rgba(255, 255, 255, 0.05); color: white;
  display: flex; justify-content: center; align-items: center;
  font-weight: 800; font-size: 0.8rem; border: 1px solid rgba(255, 255, 255, 0.1);
}
.social-btn:hover { transform: translateY(-3px) !important; background: #3b82f6; border-color: #3b82f6; }

/* COPYRIGHT */
.copyright-bar {
  background: #020617; /* Más oscuro aún */
  padding: 20px 0; text-align: center; font-size: 0.85rem; margin-top: 0;
}

/* RESPONSIVE */
@media (max-width: 900px) {
  .footer-content { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 600px) {
  .footer-content { grid-template-columns: 1fr; text-align: center; }
  .brand-link, .contact-list li, .social-icons { justify-content: center; }
  h4::after { left: 50%; transform: translateX(-50%); }
}
</style>