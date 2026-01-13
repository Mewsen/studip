const { RsdoctorWebpackPlugin } = require('@rsdoctor/webpack-plugin');
const CopyPlugin = require('copy-webpack-plugin');
const ESLintPlugin = require('eslint-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const path = require('path');
const { VueLoaderPlugin } = require('vue-loader');

const assetsPath = path.resolve(__dirname, 'resources/assets/javascripts');

module.exports = {
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
        clean: {
            keep: /^(fonts|images|javascripts\/mathjax|sounds|stylesheets\/\.gitkeep)/,
        },
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
                        loader: MiniCssExtractPlugin.loader,
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
            },
            {
                test: /\.scss$/,
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader,
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
                    },
                ],
            },
            {
                test: /\.ts$/,
                loader: 'ts-loader',
                exclude: /node_modules/,
                options: {
                    appendTsSuffixTo: [/\.vue$/],
                },
            },
            {
                test: /\.js$/,
                exclude: /node_modules|ckeditor/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        cacheDirectory: true,
                    },
                },
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: {
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
        process.env.RSDOCTOR && new RsdoctorWebpackPlugin({}),
        new CopyPlugin({
            patterns: [
                {
                    from: './node_modules/@studip/pdfjs-studip',
                    to: './javascripts/pdfjs'
                },
            ],
        }),
        new VueLoaderPlugin(),
        new MiniCssExtractPlugin({
            filename: 'stylesheets/[name].css',
            chunkFilename: 'stylesheets/[name].css?h=[chunkhash]',
            ignoreOrder: true,
        }),
        new ESLintPlugin({
            configType: 'flat',
            eslintPath: 'eslint/use-at-your-own-risk',
            exclude: [
                'node_modules',
                'resources/assets/javascripts/jquery/autoresize.jquery.min.js',
                'resources/assets/javascripts/jquery/jstree/jquery.jstree.js',
                'resources/assets/javascripts/vendor',
            ],
        }),
    ].filter(Boolean),
    resolve: {
        alias: {
            'jquery-ui/data': 'jquery-ui/ui/data',
            'jquery-ui/disable-selection': 'jquery-ui/ui/disable-selection',
            'jquery-ui/focusable': 'jquery-ui/ui/focusable',
            'jquery-ui/form': 'jquery-ui/ui/form',
            'jquery-ui/ie': 'jquery-ui/ui/ie',
            'jquery-ui/keycode': 'jquery-ui/ui/keycode',
            'jquery-ui/labels': 'jquery-ui/ui/labels',
            'jquery-ui/jquery-1-7': 'jquery-ui/ui/jquery-1-7',
            'jquery-ui/plugin': 'jquery-ui/ui/plugin',
            'jquery-ui/safe-active-element': 'jquery-ui/ui/safe-active-element',
            'jquery-ui/safe-blur': 'jquery-ui/ui/safe-blur',
            'jquery-ui/scroll-parent': 'jquery-ui/ui/scroll-parent',
            'jquery-ui/tabbable': 'jquery-ui/ui/tabbable',
            'jquery-ui/unique-id': 'jquery-ui/ui/unique-id',
            'jquery-ui/version': 'jquery-ui/ui/version',
            'jquery-ui/widget': 'jquery-ui/ui/widget',
            'jquery-ui/widgets/mouse': 'jquery-ui/ui/widgets/mouse',
            'jquery-ui/widgets/draggable': 'jquery-ui/ui/widgets/draggable',
            'jquery-ui/widgets/droppable': 'jquery-ui/ui/widgets/droppable',
            'jquery-ui/widgets/resizable': 'jquery-ui/ui/widgets/resizable',
            '@': path.resolve(__dirname, 'resources'),
            '@studip-ui': path.resolve(__dirname, 'packages/studip-ui/src'),
        },
        extensions: ['.ts', '.vue', '.js'],
        fallback: {
            stream: require.resolve('stream-browserify'),
            buffer: require.resolve('buffer/'),
        },
    },
    externals: {
        vue: 'Vue',
        vuex: 'Vuex',
    },
    externalsType: 'global',
};
