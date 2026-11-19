const mix = require('laravel-mix');
const fs = require('fs');

// Ensure the per-installation sass overrides file exists
if (!fs.existsSync('resources/sass/_custom.scss')) {
    fs.writeFileSync('resources/sass/_custom.scss', '// Per-installation overrides\n');
}

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .vue()
    .sass('resources/sass/app.scss', 'public/css')
    .sourceMaps()
    .version();
