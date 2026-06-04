import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { useCartStore } from './store/index';

const app = createApp({});
const pinia = createPinia();
app.use(pinia);

const cartStore = useCartStore();

document.addEventListener('DOMContentLoaded', async () => {
    await Promise.all([
        cartStore.getCart(),
        cartStore.getTrial(),
    ]);
});

app.mount('#front-wrapper');
