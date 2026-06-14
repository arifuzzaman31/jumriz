import { createApp } from 'vue';
import { createPinia } from 'pinia';

const app = createApp({});
app.use(createPinia());
app.mount('#wrapper');
