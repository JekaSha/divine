import { createApp } from 'vue';
import App from './App.vue';
import vuetify from './plugins/vuetify';
import { loadFonts } from './plugins/webfontloader';
import axios from './plugins/axios';

loadFonts();

const app = createApp(App);

// Поделитесь экземпляром Axios с компонентами через prototype
app.config.globalProperties.$axios = axios;

app
    .use(vuetify)
    .mount('#app');
