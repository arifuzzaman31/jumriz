import { base_url } from './vue-assets';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import VueLazyload from 'vue3-lazyload';

import CraeteProduct from './components/admin/product/CreateProduct.vue';
import ViewProduct from './components/admin/product/ViewProduct.vue';

import CreateColor from './components/admin/product/color/CreateColor.vue';
import ViewColor from './components/admin/product/color/ViewColor.vue';

import CreateSize from './components/admin/product/size/CreateSize.vue';
import ViewSize from './components/admin/product/size/ViewSize.vue';

// Initialize the Vue 3 Application instance
const app = createApp({});

// Initialize Pinia
app.use(createPinia());

// Register Global Components on the instance
app.component('create-product', CraeteProduct);
app.component('view-product', ViewProduct);

app.component('create-color', CreateColor);
app.component('view-color', ViewColor);

app.component('create-size', CreateSize);
app.component('view-size', ViewSize);

// Inject plugins

app.use(VueLazyload, {
    loading: base_url + 'images/loading.gif',
    error: base_url + 'images/error.png',
});

// Mount to the DOM container element
app.mount('#wrapper');

