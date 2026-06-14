import { base_url } from './vue-assets';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import VueLazyload from 'vue3-lazyload';

import ShippingSetting from './components/admin/setting/shipping/shippingSetting.vue';
import ShippingArea from './components/admin/setting/shipping/ShippingArea.vue';
import CreateArea from './components/admin/setting/shipping/CreateArea.vue';

// Initialize the Vue 3 Application instance
const app = createApp({});

// Initialize Pinia
app.use(createPinia());

// Register Global Components on the instance
app.component('shipping-setting', ShippingSetting);
app.component('shipping-area', ShippingArea);
app.component('create-area', CreateArea);

// Inject plugins
app.use(VueLazyload, { loading: base_url + 'images/loading.gif' });

// Mount to the DOM container element
app.mount('#wrapper');