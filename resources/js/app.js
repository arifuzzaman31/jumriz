import { createApp } from 'vue';
import { createPinia, storeToRefs } from 'pinia';
import { useCartStore } from './store/index';

const app = createApp({});

const pinia = createPinia();
app.use(pinia);

const cartStore = useCartStore();
const { cart_items, trial_items } = storeToRefs(cartStore);

document.addEventListener('DOMContentLoaded', async () => {
    await Promise.all([
        cartStore.getCart(),
        cartStore.getTrial(),
    ]);
});

app.mount('#wrapper');
