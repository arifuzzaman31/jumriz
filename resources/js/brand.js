import { base_url } from './vue-assets';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import VueLazyload from 'vue3-lazyload';

import CreateBrand from './components/admin/brand/CreateBrand.vue';
import ViewBrand from './components/admin/brand/ViewBrand.vue';

// Initialize the Vue 3 Application instance
const app = createApp({});

// Initialize Pinia
app.use(createPinia());

// Register Global Components on the instance
app.component('create-brand', CreateBrand);
app.component('view-brand', ViewBrand);

// Inject plugins
app.use(VueLazyload, {
    loading: base_url + 'images/loading.gif',
});

// Mount to the DOM container element
app.mount('#wrapper');
