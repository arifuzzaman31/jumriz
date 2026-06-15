import { base_url } from './vue-assets';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import VueLazyload from 'vue3-lazyload';

import CreateSubCategory from './components/admin/subcategory/CreateSubCategory.vue';
import ViewSubCategory from './components/admin/subcategory/ViewSubCategory.vue';

// Initialize the Vue 3 Application instance
const app = createApp({});

// Initialize Pinia
app.use(createPinia());

// Register Global Components on the instance
app.component('create-subcategory', CreateSubCategory);
app.component('view-subcategory', ViewSubCategory);

// Inject plugins
app.use(VueLazyload, {
    loading: base_url + 'images/loading.gif',
});

// Mount to the DOM container element
app.mount('#wrapper');