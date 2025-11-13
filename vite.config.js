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
                'node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
                'node_modules/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css',
                'node_modules/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css',
                'node_modules/dropzone/dist/dropzone.css',
                'node_modules/select2/dist/css/select2.min.css',
                'node_modules/select2-bootstrap4-theme/dist/select2-bootstrap4.min.css',
                'node_modules/summernote/dist/summernote-bs4.min.css',
                'node_modules/lightgallery/css/lightgallery.css',
                'node_modules/daterangepicker/daterangepicker.css',
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
        },
    },
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
    },
});
