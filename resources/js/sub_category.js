import { base_url } from './vue-assets';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import VueLazyload from 'vue3-lazyload';

import CreateSubCategory from './components/admin/category/CreateSubCategory.vue';
import ViewSubCategory from './components/admin/category/ViewSubCategory.vue';

// Initialize the Vue 3 Application instance
const app = createApp({});

// Initialize Pinia
app.use(createPinia());

// Register Global Components on the instance
app.component('create-sub-category', CreateSubCategory);
app.component('view-sub-category', ViewSubCategory);

// Inject plugins
app.use(VueLazyload, {
    loading: base_url + 'images/loading.gif',
});

// Mount to the DOM container element
app.mount('#wrapper');