import { base_url } from './vue-assets';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import VueLazyload from 'vue3-lazyload';

import CreateCurrency from './components/admin/setting/currency/CreateCurrency.vue';
import ViewCurrency from './components/admin/setting/currency/ViewCurrency.vue';

// Initialize the Vue 3 Application instance
const app = createApp({});

// Initialize Pinia
app.use(createPinia());

// Register Global Components on the instance
app.component('create-currency', CreateCurrency);
app.component('view-currency', ViewCurrency);

// Inject plugins
app.use(VueLazyload, {
    loading: base_url + 'images/loading.gif',
});

// Mount to the DOM container element
app.mount('#wrapper');
