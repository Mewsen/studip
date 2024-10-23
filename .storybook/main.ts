import type { StorybookConfig } from '@storybook/vue3-webpack5';
import path from 'path';

const config: StorybookConfig = {
    stories: ['../stories/**/*.mdx', '../stories/**/*.stories.@(js|jsx|mjs|ts|tsx)'],
    addons: [
        '@storybook/addon-webpack5-compiler-swc',
        '@storybook/addon-onboarding',
        '@storybook/addon-links',
        '@storybook/addon-essentials',
        '@chromatic-com/storybook',
        '@storybook/addon-interactions',
    ],
    framework: {
        name: '@storybook/vue3-webpack5',
        options: {},
    },
    staticDirs: ['../public'],
    webpackFinal: async (config) => {
        config!.resolve!.alias = {
          ...config!.resolve!.alias,
          '@components': path.resolve(__dirname, '../resources/vue/components'),
        };
    
        // Falls du SCSS unterstützen möchtest:
        config!.module!.rules!.push({
            test: /\.scss$/,
            use: ['style-loader', 'css-loader', 'sass-loader'],
            include: [
              path.resolve(__dirname, '../resources/assets/stylesheets'),
              path.resolve(__dirname, '../resources/vue/components')
            ],
        });
    
        return config;
      },
};
export default config;
