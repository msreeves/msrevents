const mix = require('laravel-mix');

mix
//     .options({
//        processCssUrls: false,
//  })
   .js('src/js/app.js', 'dist')
   .sass('src/scss/app.scss', 'dist');