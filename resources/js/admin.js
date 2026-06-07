import { base_url } from './vue-assets';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import VueLazyload from 'vue3-lazyload';

import CreateAdmin from './components/admin/admin/CreateAdmin.vue';
import ViewAdmin from './components/admin/admin/ViewAdmin.vue';

// Initialize the Vue 3 Application instance
const app = createApp({});

// Initialize Pinia
app.use(createPinia());

// Register Global Components on the instance
app.component('create-admin', CreateAdmin);
app.component('view-admin', ViewAdmin);

// Inject plugins using the new app.use() chain syntax
app.use(VueLazyload, {
    loading: base_url + 'images/loading.gif',
    error: base_url + 'images/error.png',
});

// Mount to the DOM container element
app.mount('#wrapper');