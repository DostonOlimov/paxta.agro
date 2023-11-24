const mix = require('laravel-mix');

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
const { VueLoaderPlugin } = require('vue-loader')

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .vue(3)
    .webpackConfig({
        module: {
            rules: [
                {
                    test: /\.vue$/,
                    loader: 'vue-loader'
                },
                {
                    test: /\.html$/,
                    loader: 'html-loader'
                }
            ]
        },
        plugins: [
            new VueLoaderPlugin()
        ]
    });
// =======
//     .vue()
//     .sass('resources/sass/app.scss', 'public/css');
// >>>>>>> 8f28ff9df2e4cd2b229f015852f1c2a615e56fd4
