const mix = require("laravel-mix");
const chokidar = require("chokidar");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

// https://stackoverflow.com/a/60313397/7759523
// https://github.com/laravel-mix/laravel-mix/issues/2719
mix.options({
    hmrOptions: {
        host: "localhost",
        port: "8000",
    },
});

mix.webpackConfig({
    // https://github.com/webpack/webpack-dev-server/issues/2792#issuecomment-725534821
    // optimization: {
    //     runtimeChunk: true,
    // },
    devServer: {
        host: "0.0.0.0",
        port: "8000",
        // https://stackoverflow.com/a/68888812/7759523
        onBeforeSetupMiddleware(server) {
            chokidar
                .watch(["./resources/views/**/*.blade.php"])
                .on("all", function () {
                    for (const ws of server.webSocketServer.clients) {
                        ws.send('{"type": "static-changed"}');
                    }
                });
        },
    },
});

mix.postCss("resources/css/app.css", "public/css", [
    require("postcss-import"),
    require("tailwindcss"),
    require("autoprefixer"),
]);

mix.ts("resources/js/app.js", "public/js").react();
