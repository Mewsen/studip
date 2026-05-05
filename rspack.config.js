const path = require('node:path');
const { VueLoaderPlugin } = require('vue-loader');
const ESLintPlugin = require('eslint-rspack-plugin');
const { rspack } = require('@rspack/core');
const { RsdoctorRspackPlugin } = require('@rsdoctor/rspack-plugin');

const assetsPath = path.resolve(__dirname, 'resources/assets/javascripts');
const isProduction = process.env.NODE_ENV === 'production';

module.exports = {
    stats: isProduction ? 'errors-only' : true,
    devtool: isProduction ? 'source-map' : 'eval',
    entry: {
        'studip-base': assetsPath + '/entry-base.js',
        'studip-statusgroups': assetsPath + '/entry-statusgroups.js',
        'studip-wysiwyg': assetsPath + '/entry-wysiwyg.js',
        'studip-installer': assetsPath + '/entry-installer.js',
        print: path.resolve(__dirname, 'resources/assets/stylesheets') + '/print.scss',
        accessibility: path.resolve(__dirname, 'resources/assets/stylesheets') + '/highcontrast.scss',
    },
    output: {
        path: path.resolve(__dirname, 'public/assets'),
        chunkFilename: 'javascripts/[id].chunk.js?h=[chunkhash]',
        filename: 'javascripts/[name].js',
    },
    module: {
        rules: [
            {
                test: /ckeditor5-[^/\\]+[/\\]theme[/\\]icons[/\\][^/\\]+\.svg$/,
                use: ['raw-loader'],
            },
            {
                test: /\.css$/,
                use: [
                    {
                        loader: rspack.CssExtractRspackPlugin.loader,
                    },
                    {
                        loader: 'css-loader',
                        options: {
                            url: false,
                            importLoaders: 1,
                        },
                    },
                    {
                        loader: 'postcss-loader',
                    },
                ],
                type: 'javascript/auto',
            },
            {
                test: /\.scss$/,
                use: [
                    {
                        loader: rspack.CssExtractRspackPlugin.loader,
                    },
                    {
                        loader: 'css-loader',
                        options: {
                            url: false,
                            importLoaders: 2,
                        },
                    },
                    {
                        loader: 'postcss-loader',
                    },
                    {
                        loader: 'sass-loader',
                        options: {
                            sassOptions: {
                                quietDeps: true,
                                silenceDeprecations: ['color-functions', 'global-builtin', 'import', 'mixed-decls'],
                            },
                        },
                    },
                ],
                type: 'javascript/auto',
            },
            {
                test: /\.(j|t)s$/,
                exclude: [/[\\/]node_modules[\\/]/],
                loader: 'builtin:swc-loader',
                options: {
                    jsc: {
                        parser: {
                            syntax: 'typescript',
                        },
                        externalHelpers: true,
                    },
                    env: {
                        targets: 'Chrome >= 48',
                    },
                },
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: {
                    experimentalInlineMatchResource: true,
                    compilerOptions: {
                        whitespace: 'preserve',
                        isCustomElement(tag) {
                            return ['altcha-widget'].includes(tag);
                        },
                    },
                },
            },
        ],
    },
    plugins: [
        process.env.RSDOCTOR && new RsdoctorRspackPlugin({}),
        new rspack.DefinePlugin({
            __VUE_OPTIONS_API__: 'true',
            __VUE_PROD_DEVTOOLS__: isProduction ? 'false' : 'true',
            __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: isProduction ? 'false' : 'true',
        }),
        new VueLoaderPlugin(),
        new rspack.CssExtractRspackPlugin({
            filename: 'stylesheets/[name].css',
            chunkFilename: 'stylesheets/[name].css?h=[chunkhash]',
            ignoreOrder: true,
        }),
        new rspack.CopyRspackPlugin({
            patterns: [
                ...[
                    './node_modules/jquery/dist/jquery.min.js',
                    './node_modules/jquery-ui/dist/jquery-ui.min.js',
                    './node_modules/select2/dist/js/select2.full.min.js',
                    './node_modules/tablesorter/dist/js/jquery.tablesorter.combined.min.js',
                    './node_modules/jquery-ui-timepicker-addon/dist/jquery-ui-timepicker-addon.min.js',
                    './node_modules/jquery.scrollto/jquery.scrollTo.min.js',
                    './node_modules/jquery.qrcode/jquery.qrcode.min.js',
                    './node_modules/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js',
                    './node_modules/lodash/lodash.min.js',
                    './node_modules/vue/dist/vue.global.prod.js',
                    './node_modules/vuex/dist/vuex.global.prod.js',
                ].map((from) => ({ from, to: 'javascripts/' })),
                {
                    from: './node_modules/@studip/pdfjs-studip',
                    to: './javascripts/pdfjs'
                },
                {
                    from: './node_modules/jquery-ui-timepicker-addon/dist/jquery-ui-timepicker-addon.min.css',
                    to: 'stylesheets/',
                },
            ],
        }),
        process.env.ESLINT &&
            new ESLintPlugin({
                configType: 'flat',
                eslintPath: 'eslint/use-at-your-own-risk',
                exclude: [
                    'node_modules',
                    'public/assets/javascripts/ckeditor/ckeditor.js',
                    'resources/assets/javascripts/jquery/autoresize.jquery.min.js',
                    'resources/assets/javascripts/jquery/jstree/jquery.jstree.js',
                    'resources/assets/javascripts/vendor',
                ],
            }),
        // new CKEditorTranslationsPlugin({
        //     addMainLanguageTranslationsToAllAssets: true,
        //     language: 'de',
        // }),
    ].filter(Boolean),
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources'),
            '@studip-ui': path.resolve(__dirname, 'packages/studip-ui/src'),
        },
        extensions: ['.ts', '.vue', '.js'],
        fallback: {
            stream: require.resolve('stream-browserify'),
            buffer: require.resolve('buffer/'),
        },
    },
    watchOptions: {
        ignored: [/[\\/](?:\.git|node_modules)[\\/]/, /\.d\.[cm]ts$/],
    },
    externals: {
        vue: 'Vue',
        vuex: 'Vuex',
    },
    externalsType: 'global',
};
