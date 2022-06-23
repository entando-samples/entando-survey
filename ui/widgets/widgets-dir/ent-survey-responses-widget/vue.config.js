const {defineConfig} = require('@vue/cli-service')
const appPackageJson = require('./package.json');
const webpack = require('webpack');
const path = require("path");



module.exports = defineConfig({
    transpileDependencies: true,
    assetsDir: 'static',
    outputDir: 'build',
    //publicPath: '/entando-de-app/cmsresources/ent-prj-tmpl-vue-bundle/',
    configureWebpack: config => {
        const filename = `${appPackageJson.name}-${appPackageJson.version}`;
        config.plugins.push(new webpack.optimize.LimitChunkCountPlugin({
            maxChunks: 1
        }));

        return {
            output:{
                filename: `static/js/${filename}.js`,
            },
            resolve: {
                alias: {
                    "@": path.join(__dirname, "src/js"),
                }
            }
        }
    }

})
