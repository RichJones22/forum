let mix = require('laravel-mix');

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

if (process.env.NODE_ENV === 'production') {
    mix
        .options({
            uglify: {
                uglifyOptions: {
                    compress: {
                        drop_console: true, // suppress console.log() messages.
                    },
                    output: {
                        max_line_len: 50000, // suppress warning 'WARN: Output exceeds 32000 characters'
                    }
                },
            },
        });

}

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css');

// if we are in production babel compile our app.js and minify and version all .js and .css
if (process.env.NODE_ENV === 'production') {
    mix
        .version();
}