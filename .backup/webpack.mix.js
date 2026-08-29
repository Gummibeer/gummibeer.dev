const mix = require('laravel-mix');
const glob = require('glob');
const path = require('path');

Mix.listen('configReady', webpackConfig => {
    webpackConfig.module.rules.forEach(rule => {
        if (Array.isArray(rule.use)) {
            rule.use.forEach(ruleUse => {
                if (ruleUse.loader === 'resolve-url-loader') {
                    ruleUse.options.engine = 'postcss';
                }
            });
        }
    });
});

mix
    .sass('resources/scss/app.scss', 'public/css')
    .js('resources/js/app.js', 'public/js')
    .options({
        processCssUrls: true,
        postCss: [
            require('tailwindcss')('./tailwind.config.js'),
            require('postcss-discard-comments')({
                removeAll: true,
            }),
        ],
    })
    .version()
;

glob.sync(path.resolve(__dirname, 'resources', 'images') + '/**/*.@(png|jpg|gif|ico)').forEach(img => {
    mix.copy(img, img.replace(path.resolve(__dirname, 'resources', 'images'), 'public/images'));
});

mix.copyDirectory('node_modules/twemoji-emojis/vendor/svg', 'public/vendor/twemoji/svg');