import {defineConfig} from 'vite';
import path from 'path'
import { nodeResolve } from '@rollup/plugin-node-resolve';
import vue from '@vitejs/plugin-vue2'

const assetsPath = path.resolve(__dirname, "resources/assets");
export default defineConfig({
    resolve: {
        alias: {
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
        nodeResolve(),
        vue(),
    ],
    dedupe: ['vue-resizable', '@node_modules/vrp-vue-resizable'],
    optimizeDeps: {
        exclude: ['components/vue-resizable']
    },
    build: {
        lib: {
            // Could also be a dictionary or array of multiple entry points
            entry: {
                "studip-base": assetsPath + "/javascripts/entry-base.js",
                "studip-admission": assetsPath + "/javascripts/entry-admission.js",
                "studip-statusgroups": assetsPath + "/javascripts/entry-statusgroups.js",
                "studip-wysiwyg": assetsPath + "/javascripts/entry-wysiwyg.js",
                "studip-installer": assetsPath + "/javascripts/entry-installer.js",
                "print": path.resolve(__dirname, "resources/assets/stylesheets") + "/print.less",
                "webservices": path.resolve(__dirname, "resources/assets/stylesheets") + "/webservices.scss",
                "accessibility": path.resolve(__dirname, "resources/assets/stylesheets") + "/highcontrast.scss"
            },
            name: 'MyLib',
            // the proper extensions will be added
            fileName: 'my-lib',
        },
        minify: true,
        outDir: 'public/assets',
        // don't inline anything for demo
        assetsInlineLimit: 0,
        emptyOutDir: true,
        rollupOptions: {
            external: [
                /^expose-loader.*/,
                'vue'
            ],
        }
    },
    define: {
        _global: ({})
    }
})
