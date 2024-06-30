const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .copy('node_modules/admin-lte/dist/css/adminlte.css', 'public/css')
    .copy('node_modules/admin-lte/dist/js/adminlte.js', 'public/js')
    .copy('node_modules/@fortawesome/fontawesome-free/css/all.min.css', 'public/css')
    .copy('node_modules/@fortawesome/fontawesome-free/webfonts', 'public/webfonts')
    .sourceMaps(); // Generar sourcemaps para facilitar la depuración

if (mix.inProduction()) {
    mix.version(); // Generar versión para la caché de assets en producción
}