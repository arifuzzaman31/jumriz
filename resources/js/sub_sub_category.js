import { base_url } from './vue-assets';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import VueLazyload from 'vue3-lazyload';

import CreateSubSubCategory from './components/admin/subsubcategory/CreateSubSubCategory.vue';
import ViewSubSubCategory from './components/admin/subsubcategory/ViewSubSubCategory.vue';

// Initialize the Vue 3 Application instance
const app = createApp({});

// Initialize Pinia
app.use(createPinia());

// Register Global Components on the instance
app.component('create-subsubcategory', CreateSubSubCategory);
app.component('view-subsubcategory', ViewSubSubCategory);

// Inject plugins
app.use(VueLazyload, {
    loading: base_url + 'images/loading.gif',
});

// Mount to the DOM container element
app.mount('#wrapper');
