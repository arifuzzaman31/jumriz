// Change 'require' to modern ES Import syntax
import './vue-assets'; 
import { createApp } from 'vue';
import VueLazyLoad from 'vue3-lazyload'

// Import your components directly
import DashboardSummary from './components/admin/dashboard/dashboard.vue';
import ChartSummary from './components/admin/dashboard/chart.vue';
// import DashCheck from './components/admin/dashboard/CheckDash.vue';

// Initialize the Vue 3 Application instance
const app = createApp({});

// Register Global Components on the instance
app.component('dashboard-summary', DashboardSummary);
app.component('chart-summary', ChartSummary);
// app.component('dash-check', DashCheck);

// Inject plugins using the new app.use() chain syntax
app.use(VueLazyLoad, {
    loading: base_url + 'images/loading.gif',
});

// Mount to the DOM container element (replaces 'el: #wrapper')
app.mount('#wrapper');