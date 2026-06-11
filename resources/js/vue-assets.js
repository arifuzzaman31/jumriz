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

// 4. Event Bus for Vue 3
// Vue 3 removed the Vue 2 instance event methods ($on/$emit/$off/$once).
// This wrapper keeps the existing project-wide EventBus API working.
import mitt from 'mitt';
export const emitter = mitt();

export const EventBus = {
    $on: emitter.on.bind(emitter),
    $off: emitter.off.bind(emitter),
    $once(type, handler) {
        const wrappedHandler = (event) => {
            this.$off(type, wrappedHandler);
            handler(event);
        };
        this.$on(type, wrappedHandler);
    },
    $emit: emitter.emit.bind(emitter),
};