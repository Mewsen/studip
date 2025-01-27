import globals from "globals";
import pluginJs from "@eslint/js";
import tseslint from "typescript-eslint";
import pluginVue from "eslint-plugin-vue";

/** @type {import('eslint').Linter.Config[]} */
export default [
    pluginJs.configs.recommended,
    ...tseslint.configs.recommended,
    ...pluginVue.configs["flat/essential"],
    {
        languageOptions: {
            globals: {
                ...globals.browser,
                ...globals.node,
                "STUDIP": "writable",
                "CKEDITOR": "writable",
                "$": "writable",
                "_": "writable",
                "jQuery": "writable"
            }
        },
        files: ["resources/**/*.{js,mjs,cjs,ts,vue}"],
        ignores: [
            'resources/assets/javascripts/jquery/autoresize.jquery.min.js',
            'resources/assets/javascripts/jquery/jstree/jquery.jstree.js',
            'resources/assets/javascripts/vendor'
        ]
    },
    {
        files: ["**/*.vue"],
        languageOptions: {
            parserOptions: {
                parser: tseslint.parser
            }
        }
    },
    {
        rules: {
            "@typescript-eslint/no-require-imports": "off",
            "vue/html-indent": "off",
            "vue/multi-word-component-names": "off",
            "vue/no-deprecated-destroyed-lifecycle": "off",
            "indent": "off"
        }
    }
];
