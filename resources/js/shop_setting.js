import { base_url } from './vue-assets';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import VueLazyload from 'vue3-lazyload';

import SeoSetting from './components/admin/setting/seo/SeoSetting.vue';
import ShopSetting from './components/admin/setting/shop/ShopSetting.vue';
import TrialSetting from './components/admin/setting/trial/TrialSetting.vue';
import PwaSetting from './components/admin/setting/pwa/PwaSetting.vue';
import DeliveryDateSlot from './components/admin/setting/slot/DeliveryDateSlot.vue';
import CreateTimeSlot from './components/admin/setting/slot/CreateTimeSlot.vue';
import ViewTimeSlot from './components/admin/setting/slot/ViewTimeSlot.vue';

// Initialize the Vue 3 Application instance
const app = createApp({});

// Initialize Pinia
app.use(createPinia());

// Register Global Components on the instance
app.component('seo-setting', SeoSetting);
app.component('shop-setting', ShopSetting);
app.component('trial-setting', TrialSetting);
app.component('pwa-setting', PwaSetting);
app.component('date-slot-setting', DeliveryDateSlot);
app.component('create-time-slot', CreateTimeSlot);
app.component('view-time-slot', ViewTimeSlot);

// Inject plugins
app.use(VueLazyload, { loading: base_url + 'images/loading.gif' });

// Mount to the DOM container element
app.mount('#wrapper');
