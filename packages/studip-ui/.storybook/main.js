/** @type { import('@storybook/vue3-vite').StorybookConfig } */
const config = {
    stories: ['../src/**/*.stories.@(js|jsx|mjs|ts|tsx)'],
    addons: [
        '@storybook/addon-a11y',
        '@storybook/addon-docs',
        '@storybook/addon-designs',
        'storybook-addon-tag-badges'
    ],
    framework: {
        name: '@storybook/vue3-vite',
        options: {},
    },
    docs: {
        defaultName: 'Dokumentation',
    },
    core: {
        disableTelemetry: true,
    },
    staticDirs: [
        { from: '../dist/assets/images/icons', to: 'assets/images/icons' },
        { from: '../public/fonts', to: 'fonts' },
    ],
}
export default config
