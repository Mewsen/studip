const CssMinimizerPlugin = require('css-minimizer-webpack-plugin')
const TerserPlugin = require('terser-webpack-plugin')
const CopyPlugin = require('copy-webpack-plugin');
const common = require('./webpack.common.js')
const webpack = require('webpack')
const { merge } = require('webpack-merge')

module.exports = merge(common, {
    mode: 'production',
    stats: 'errors-only',
    devtool: 'source-map',
    optimization: {
        minimize: true,
        minimizer: [
            new webpack.DefinePlugin({
                __VUE_OPTIONS_API__: 'true',
                __VUE_PROD_DEVTOOLS__: 'false',
                __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: 'false'
            }),
            new TerserPlugin(
                {
                    extractComments: false
                }
            ),
            new CssMinimizerPlugin({
                minimizerOptions: {
                    preset: [ 'default', { discardComments: { removeAll: true } } ],
                },
            }),
        ]
    },
    plugins: [
        new CopyPlugin({
            patterns: [
                {
                    from: './node_modules/vue/dist/vue.global.prod.js',
                    to: './javascripts/vue.global.prod.js',
                },
                {
                    from: './node_modules/vuex/dist/vuex.global.prod.js',
                    to: './javascripts/vuex.global.prod.js',
                },
            ],
        }),
    ]
})
