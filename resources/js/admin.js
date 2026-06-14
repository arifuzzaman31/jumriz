import { base_url } from './vue-assets';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import VueLazyload from 'vue3-lazyload';

import CreateAdmin from './components/admin/admin/CreateAdmin.vue';
import ViewAdmin from './components/admin/admin/ViewAdmin.vue';

import CreateCategory from './components/admin/category/CreateCategory.vue';
import ViewCategory from './components/admin/category/ViewCategory.vue';

import CreateBrand from './components/admin/brand/CreateBrand.vue';
import ViewBrand from './components/admin/brand/ViewBrand.vue';

import CreateSubCategory from './components/admin/subcategory/CreateSubCategory.vue';
import ViewSubCategory from './components/admin/subcategory/ViewSubCategory.vue';

import CreateSubSubCategory from './components/admin/subsubcategory/CreateSubSubCategory.vue';
import ViewSubSubCategory from './components/admin/subsubcategory/ViewSubSubCategory.vue';
// Initialize the Vue 3 Application instance
const app = createApp({});

// Initialize Pinia
app.use(createPinia());

// Register Global Components on the instance
app.component('create-admin', CreateAdmin);
app.component('view-admin', ViewAdmin);
app.component('create-category', CreateCategory);
app.component('view-category', ViewCategory);
app.component('create-brand', CreateBrand);
app.component('view-brand', ViewBrand);
app.component('create-sub-category', CreateSubCategory);
app.component('view-sub-category', ViewSubCategory);
app.component('create-subcategory', CreateSubSubCategory);
app.component('view-subcategory', ViewSubSubCategory);
// Inject plugins using the new app.use() chain syntax
app.use(VueLazyload, {
    loading: base_url + 'images/loading.gif',
    error: base_url + 'images/error.png',
});

// Mount to the DOM container element
app.mount('#wrapper');