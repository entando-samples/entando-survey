const mix = require("laravel-mix");
const path = require("path");
require('mix-html-builder');


mix.alias({
    "@": path.join(__dirname, "resources/js"),
});

mix.setPublicPath('build')
    .js("resources/js/ent-survey.js", "static/js")
    .sass("resources/css/app.scss", "static/css")
    .html({
        output: '.',
        htmlRoot: 'resources/views/index.html',
        inject: true,
    })
    .vue({ version: 2, extractStyles: true })
    .extract()
    .version()
    .disableNotifications()
    .browserSync({
        server: "./build",
        port:9900
    });
