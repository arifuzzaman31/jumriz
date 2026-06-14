import { base_url } from './vue-assets';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import VueLazyload from 'vue3-lazyload';

import EmailSetting from './components/admin/setting/email/EmailSetting.vue';
import ViewEmail from './components/admin/email/ViewEmail.vue';

// Initialize the Vue 3 Application instance
const app = createApp({});

// Initialize Pinia
app.use(createPinia());

// Register Global Components on the instance
app.component('email-setting', EmailSetting);
app.component('view-email', ViewEmail);

// Inject plugins
app.use(VueLazyload, { loading: base_url + 'images/loading.gif' });

// Mount to the DOM container element
app.mount('#wrapper');