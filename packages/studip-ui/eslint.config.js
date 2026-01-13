import { defineConfig, globalIgnores } from 'eslint/config'
import globals from 'globals'
import js from '@eslint/js'
import pluginVue from 'eslint-plugin-vue'
import pluginVitest from '@vitest/eslint-plugin'
import skipFormatting from '@vue/eslint-config-prettier/skip-formatting'

export default defineConfig([
    js.configs.recommended,

    ...pluginVue.configs['flat/essential'],

    {
        name: 'app/files-to-lint',
        files: ['**/*.{js,mjs,jsx,vue}'],
        rules: {
            'vue/component-name-in-template-casing': [
                'error',
                'PascalCase',
                {
                    registeredComponentsOnly: false,
                    ignores: ['/-.+/'],
                },
            ],
            'vue/no-mutating-props': 'error',
            'vue/block-order': [
                'error',
                {
                    order: ['template', 'script', 'style'],
                },
            ],
        },
    },

    globalIgnores(['**/dist/**', '**/dist-ssr/**', '**/coverage/**']),

    {
        languageOptions: {
            globals: {
                ...globals.browser,
            },
        },
    },

    {
        ...pluginVitest.configs.recommended,
        files: ['src/**/__tests__/*'],
    },

    skipFormatting,
])
