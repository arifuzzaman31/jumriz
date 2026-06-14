import { base_url } from './vue-assets';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import VueLazyload from 'vue3-lazyload';

import CreatePage from './components/admin/setting/pages/CreatePage.vue';
import ViewPage from './components/admin/setting/pages/ViewPage.vue';

// Initialize the Vue 3 Application instance
const app = createApp({});

// Initialize Pinia
app.use(createPinia());

// Register Global Components on the instance
app.component('create-page', CreatePage);
app.component('view-page', ViewPage);

// Inject plugins
app.use(VueLazyload, { loading: base_url + 'images/loading.gif' });

// Mount to the DOM container element
app.mount('#wrapper');