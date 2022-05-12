import {fileURLToPath, URL} from 'url'

import {defineConfig} from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [vue()],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url))
        }
    },
    base: '/entando-de-app/cmsresources/ent-prj-tmpl-vue-bundle/',
    build: {
        outDir: 'build',
        assetsDir: 'static/assets',
        rollupOptions: {
            output: {
                entryFileNames: "static/js/[name]-[hash].js",
                assetFileNames: function (assetInfo) {
                    const assetExt = assetInfo.name.substring(assetInfo.name.lastIndexOf(".") + 1)
                    if (assetExt === 'css') {
                        return `static/css/[name]-[hash][extname]`
                    }
                    return `static/[name]-[hash][extname]`
                }

            }
        }
    },
    server: {
        proxy: {
            '^/entando-vue-template-api/api': {
                target: 'http://localhost:8081',
                rewrite: (path) => path.replace(/^\/entando-vue-template-api\/api/, '/api'),
                changeOrigin: true
            }
        }
    }

})
