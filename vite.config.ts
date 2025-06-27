import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import rawPlugin from 'vite-plugin-raw';
import { viteStaticCopy } from 'vite-plugin-static-copy';

export default defineConfig(({ mode }) => ({
    plugins: [
        vue({
            template: {
                compilerOptions: {
                    isCustomElement: tag => ['altcha-widget'].includes(tag)
                }
            }
        }),
        rawPlugin({
            match: /ckeditor5-[^/\\]+[\\/]+theme[\\/]+icons[\\/]+[^/\\]+\.svg$/,
            exclude: /.*/
        }),
        viteStaticCopy({
            targets: [
                {
                    src: 'node_modules/vue/dist/vue.global.prod.js',
                    dest: 'javascripts'
                },
                {
                    src: 'node_modules/vuex/dist/vuex.global.prod.js',
                    dest: 'javascripts'
                }
            ]
        })
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources'),
            'jquery-ui': 'jquery-ui'
        }
    },
    publicDir: false,
    build: {
        outDir: 'public/assets',
        emptyOutDir: false,
        sourcemap: mode !== 'production',
        rollupOptions: {
            input: {
                'studip-base': path.resolve(__dirname, 'resources/assets/javascripts/entry-base.js'),
                'studip-statusgroups': path.resolve(__dirname, 'resources/assets/javascripts/entry-statusgroups.js'),
                'studip-wysiwyg': path.resolve(__dirname, 'resources/assets/javascripts/entry-wysiwyg.js'),
                'studip-installer': path.resolve(__dirname, 'resources/assets/javascripts/entry-installer.js'),
                'print': path.resolve(__dirname, 'resources/assets/stylesheets/print.scss'),
                'accessibility': path.resolve(__dirname, 'resources/assets/stylesheets/highcontrast.scss')
            },
            external: ['vue', 'vuex'],
            output: {
                format: 'iife',
                entryFileNames: 'javascripts/[name].js',
                chunkFileNames: 'javascripts/[name].chunk.js?h=[hash]',
                assetFileNames: assetInfo => {
                    const name = assetInfo.name || '';
                    if (name.endsWith('.css')) {
                        return 'stylesheets/[name][extname]';
                    }
                    if (/\.(woff2?|ttf|eot)$/.test(name)) {
                        return 'fonts/[name][extname]';
                    }
                    if (/\.(png|jpe?g|svg|gif|webp)$/.test(name)) {
                        return 'images/[name][extname]';
                    }
                    return 'javascripts/[name][extname]';
                },
                globals: {
                    vue: 'Vue',
                    vuex: 'Vuex'
                }
            }
        }
    },
    css: {
        postcss: './postcss.config.js',
        preprocessorOptions: {
            scss: {}
        }
    },
    define: {
        __VUE_OPTIONS_API__: true,
        __VUE_PROD_DEVTOOLS__: mode !== 'production',
        __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: mode !== 'production'
    }
}));
