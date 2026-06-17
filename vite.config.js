import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/admin.js',
                // 'resources/js/product.js',
                'resources/js/front.js'
            ],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            'vue': 'vue/dist/vue.esm-bundler.js', // Keeps runtime compilation enabled
        },
    },
    server: {
        host: '0.0.0.0',        // Listen on all interfaces for Docker
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',   // Your local machine hostname
            port: 5173,
            protocol: 'ws',
        },
        watch: {
            usePolling: true,    // Important for Docker filesystem
            interval: 1000,      // Poll every second
        },
        cors: true,
    },
});