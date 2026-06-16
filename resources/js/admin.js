import { base_url } from './vue-assets';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import VueLazyload from 'vue3-lazyload';

import CreateColor from './components/admin/product/color/CreateColor.vue';
import ViewColor from './components/admin/product/color/ViewColor.vue';

import CreateSize from './components/admin/product/size/CreateSize.vue';
import ViewSize from './components/admin/product/size/ViewSize.vue';

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
app.component('create-color', CreateColor);
app.component('view-color', ViewColor);

app.component('create-size', CreateSize);
app.component('view-size', ViewSize);

app.component('create-admin', CreateAdmin);
app.component('view-admin', ViewAdmin);
app.component('create-category', CreateCategory);
app.component('view-category', ViewCategory);
app.component('create-brand', CreateBrand);
app.component('view-brand', ViewBrand);
app.component('create-subcategory', CreateSubCategory);
app.component('view-subcategory', ViewSubCategory);
app.component('create-subsubcategory', CreateSubSubCategory);
app.component('view-subsubcategory', ViewSubSubCategory);
// Inject plugins using the new app.use() chain syntax
app.use(VueLazyload, {
    loading: base_url + 'images/loading.gif',
    error: base_url + 'images/error.png',
});

// Mount to the DOM container element
app.mount('#wrapper');