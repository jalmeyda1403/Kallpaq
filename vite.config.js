import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/css/custom.css',
                'node_modules/@fortawesome/fontawesome-free/css/all.min.css',
                'node_modules/overlayscrollbars/styles/overlayscrollbars.min.css',
                'node_modules/admin-lte/dist/css/adminlte.min.css',
                'node_modules/admin-lte/dist/js/adminlte.js',
            ],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '@': path.resolve(__dirname, 'resources/js'),
            ziggy: path.resolve(__dirname, 'vendor/tightenco/ziggy/dist/vue.es.js'),
            ziggyJs: path.resolve(__dirname, 'vendor/tightenco/ziggy/dist/index.es.js'),
            'primeicons': path.resolve(__dirname, 'node_modules/primeicons'),
            'vue-router': 'vue-router',
        },
    },
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
    },
});
