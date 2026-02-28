import './bootstrap';
import { createApp } from 'vue';
import HomePage from './components/HomePage.vue';
import AdminPanel from './components/AdminPanel.vue';
import PostPage from './components/PostPage.vue';
import FooterSection from './components/FooterSection.vue';
import Boda1 from './components/invitations/Boda1.vue';
import Boda2 from './components/invitations/Boda2.vue';

const app = createApp({});

app.component('home-page', HomePage);
app.component('admin-panel', AdminPanel);
app.component('post-page', PostPage);
app.component('footer-section', FooterSection);
app.component('boda-1', Boda1);
app.component('boda-2', Boda2);

app.mount('#app');