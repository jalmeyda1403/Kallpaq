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
                'resources/css/custom-select2.css',
                'node_modules/admin-lte/dist/plugins/fontawesome-free/css/all.min.css',
                'node_modules/admin-lte/dist/plugins/overlayScrollbars/css/OverlayScrollbars.min.css',
                'node_modules/admin-lte/dist/css/adminlte.min.css',
                'node_modules/admin-lte/dist/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css',
                'node_modules/admin-lte/dist/plugins/datatables-buttons/css/buttons.bootstrap4.min.css',
                'node_modules/admin-lte/dist/plugins/datatables-responsive/css/responsive.bootstrap4.min.css',
                'node_modules/admin-lte/dist/plugins/dropzone/min/dropzone.min.css',
                'node_modules/admin-lte/dist/plugins/select2/css/select2.min.css',
                'node_modules/admin-lte/dist/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css',
                'node_modules/admin-lte/dist/plugins/summernote/summernote-bs4.min.css',
                'node_modules/admin-lte/dist/plugins/lightgallery/css/lightgallery.min.css',
                'node_modules/admin-lte/dist/plugins/chart.js/chart.min.css',
                'node_modules/admin-lte/dist/plugins/daterangepicker/daterangepicker.css',
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
        },
    },
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
    },
});
