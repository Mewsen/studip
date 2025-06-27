import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import rawPlugin from 'vite-plugin-raw';

export default defineConfig(({ mode }) => {
    return {
        plugins: [
            vue({
                template: {
                    compilerOptions: {
                        isCustomElement: tag => ['altcha-widget'].includes(tag)
                    }
                }
            }),
            rawPlugin({
                match: /ckeditor5-[^/\\]+[/\\]theme[/\\]icons[/\\][^/\\]+\.svg$/,
                exclude: undefined
            }),
        ],
        resolve: {
            alias: {
                '@': path.resolve(__dirname, 'resources'),
                'jquery-ui': 'jquery-ui/ui',
            }
        },
        build: {
            outDir: 'public/assets',
            rollupOptions: {
                input: {
                    'studip-base': path.resolve(__dirname, 'resources/assets/javascripts/entry-base.js'),
                    'studip-statusgroups': path.resolve(__dirname, 'resources/assets/javascripts/entry-statusgroups.js'),
                    'studip-wysiwyg': path.resolve(__dirname, 'resources/assets/javascripts/entry-wysiwyg.js'),
                    'studip-installer': path.resolve(__dirname, 'resources/assets/javascripts/entry-installer.js'),
                    'print': path.resolve(__dirname, 'resources/assets/stylesheets/print.scss'),
                    'accessibility': path.resolve(__dirname, 'resources/assets/stylesheets/highcontrast.scss'),
                },
                external: ['vue', 'vuex'],
                output: {
                    entryFileNames: 'javascripts/[name].js',
                    chunkFileNames: 'javascripts/[name].chunk.js?h=[hash]',
                    assetFileNames: assetInfo => {
                        if (assetInfo.name?.endsWith('.css')) {
                            return 'stylesheets/[name][extname]';
                        }
                        return '[name][extname]';
                    },
                }
            },
            sourcemap: mode !== 'production',
        },
        css: {
            postcss: './postcss.config.js',
            preprocessorOptions: {
                scss: {},
            }
        },
        define: {
            __VUE_OPTIONS_API__: true,
            __VUE_PROD_DEVTOOLS__: mode !== 'production',
            __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: mode !== 'production',
        }
    };
});
