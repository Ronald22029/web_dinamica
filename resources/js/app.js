import './bootstrap';
import { createApp } from 'vue';
import HomePage from './components/HomePage.vue';
import AdminPanel from './components/AdminPanel.vue'; // <--- Importar
import PostPage from './components/PostPage.vue';
import FooterSection from './components/FooterSection.vue';

const app = createApp({});

app.component('home-page', HomePage);
app.component('admin-panel', AdminPanel); // <--- Registrar
app.component('post-page', PostPage);
app.component('footer-section', FooterSection);

app.mount('#app');