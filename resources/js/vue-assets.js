// 1. Change 'require' to standard ES module imports for Axios
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Setting up CSRF-token in JS 
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Use Vite's built-in BASE_URL. 
// Note: This usually returns '/' and always includes the trailing slash.
export const base_url = import.meta.env.BASE_URL;

// 2. Sweet Alert Import (Stays the same, standard ES syntax)
import Swal from 'sweetalert2';
window.Swal = Swal;

// 3. REMOVED: window.Vue = require('vue')
// In Vue 3, Vue is no longer exposed globally like this because 
// everything mounts to isolated `createApp()` instances.

// 4. FIXING THE EVENT BUS FOR VUE 3
// Vue 3 completely removed '$on', '$off', and '$once' methods from the core instance.
// To keep an Event Bus working, you should use a lightweight library like 'mitt'.
import mitt from 'mitt';
export const emitter = mitt();