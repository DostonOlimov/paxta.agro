const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .vue() // Add this to enable Vue support
    .postCss('resources/css/app.css', 'public/css', [
        require('tailwindcss'),
        require('autoprefixer'),
    ]);

mix.disableNotifications();
