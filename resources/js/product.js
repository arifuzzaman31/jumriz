import { createApp } from 'vue';
import { createPinia } from 'pinia';

import CreateProduct from './components/admin/product/CreateProduct.vue';
import ViewProduct from './components/admin/product/ViewProduct.vue';

const app = createApp({});

app.use(createPinia());

app.component('create-product', CreateProduct);
app.component('view-product', ViewProduct);

app.mount('#product-app');

