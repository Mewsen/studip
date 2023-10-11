import {defineConfig } from 'vite';
import path from 'path'
import {processAssetFileNames, entryFileNames, chunkFileNames, assetDir} from "./configAssets";
import vue from '@vitejs/plugin-vue2'
import requireTransform from 'vite-plugin-require-transform';

const fullAssetsDir = path.resolve(__dirname, "resources/assets");
export default defineConfig({
    resolve: {
        alias: {
            '@img': path.resolve(__dirname, 'public/assets/images'),
            '@fonts': path.resolve(__dirname, 'public/assets/fonts'),
            '@vue$': 'vue/dist/vue.esm.js',
            '@jquery-ui/data': 'jquery-ui/ui/data',
            '@jquery-ui/disable-selection': 'jquery-ui/ui/disable-selection',
            '@jquery-ui/focusable': 'jquery-ui/ui/focusable',
            '@jquery-ui/form': 'jquery-ui/ui/form',
            '@jquery-ui/ie': 'jquery-ui/ui/ie',
            '@jquery-ui/keycode': 'jquery-ui/ui/keycode',
            '@jquery-ui/labels': 'jquery-ui/ui/labels',
            '@jquery-ui/jquery-1-7': 'jquery-ui/ui/jquery-1-7',
            '@jquery-ui/plugin': 'jquery-ui/ui/plugin',
            '@jquery-ui/safe-active-element': 'jquery-ui/ui/safe-active-element',
            '@jquery-ui/safe-blur': 'jquery-ui/ui/safe-blur',
            '@jquery-ui/scroll-parent': 'jquery-ui/ui/scroll-parent',
            '@jquery-ui/tabbable': 'jquery-ui/ui/tabbable',
            '@jquery-ui/unique-id': 'jquery-ui/ui/unique-id',
            '@jquery-ui/version': 'jquery-ui/ui/version',
            '@jquery-ui/widget': 'jquery-ui/ui/widget',
            '@jquery-ui/widgets/mouse': 'jquery-ui/ui/widgets/mouse',
            '@jquery-ui/widgets/draggable': 'jquery-ui/ui/widgets/draggable',
            '@jquery-ui/widgets/droppable': 'jquery-ui/ui/widgets/droppable',
            '@jquery-ui/widgets/resizable': 'jquery-ui/ui/widgets/resizable',
            './components/vue-resizable': 'node_modules/vrp-vue-resizable/src/components/vue-resizable.vue',
            '@': path.resolve(__dirname, 'resources'),
            "~@": path.resolve(__dirname, "/resources"),
        }
    },
    plugins: [
        vue(),
        requireTransform({}),
    ],
    build: {
        lib: {
            // Could also be a dictionary or array of multiple entry points
            entry: {
                "studip-base": fullAssetsDir + "/javascripts/entry-base.js",
                "studip-admission": fullAssetsDir + "/javascripts/entry-admission.js",
                "studip-statusgroups": fullAssetsDir + "/javascripts/entry-statusgroups.js",
                "studip-wysiwyg": fullAssetsDir + "/javascripts/entry-wysiwyg.js",
                "studip-installer": fullAssetsDir + "/javascripts/entry-installer.js",
                // "studip-less": fullAssetsDir + "/stylesheets/studip.less",
                // "studip-scss": fullAssetsDir + "/stylesheets/studip.scss",
                // "studip-jquery-ui": fullAssetsDir + "/stylesheets/studip-jquery-ui.less",
                "print": fullAssetsDir + "/stylesheets/print.less",
                "webservices": fullAssetsDir + "/stylesheets/webservices.scss",
                "accessibility": fullAssetsDir + "/stylesheets/highcontrast.scss"
            },
        },
        cssCodeSplit: true,
        assetsDir: assetDir,
        minify: true,
        modulePreload: false,
        outDir: './',
        emptyOutDir: false,
        copyPublicDir: false,
        rollupOptions: {
            external: [
                'vue'
            ],
            output: {
                entryFileNames: entryFileNames,
                assetFileNames: processAssetFileNames,
                chunkFileNames: chunkFileNames,
            }
        }
    }
})
