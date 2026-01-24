import './bootstrap';
import { createApp } from 'vue';
import HomePage from './components/HomePage.vue';
import AdminPanel from './components/AdminPanel.vue'; // <--- Importar

const app = createApp({});

app.component('home-page', HomePage);
app.component('admin-panel', AdminPanel); // <--- Registrar

app.mount('#app');