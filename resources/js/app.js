import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { useCartStore } from './store/index';

const app = createApp({});
const pinia = createPinia();
app.use(pinia);

// Standardized initial data fetch
const initStore = async () => {
    const cartStore = useCartStore();
    await Promise.allSettled([
        cartStore.getCart(),
        cartStore.getTrial(),
    ]);
};

initStore();

app.mount('#wrapper');
