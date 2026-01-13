import { resolve } from 'node:path'
import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

const srcDir = fileURLToPath(new URL('./src', import.meta.url))
const componentsDir = fileURLToPath(new URL('./src/components', import.meta.url))

export default defineConfig({
    build: {
        sourcemap: true,
        target: 'es2020',
        lib: {
            entry: {
                'studip-ui': resolve('src/main.js'),
                'components/index': resolve('src/components/index.js'),
                'composables/index': resolve('src/composables/index.js'),
            },
            name: 'StudipUi',
        },
        rollupOptions: {
            external: ['vue'],
            output: {
                globals: {
                    vue: 'Vue',
                },
                manualChunks(id) {
                    if (id.startsWith(componentsDir) && !id.includes('index.js')) {
                        return 'components/index'
                    }
                    return null
                },
            },
        },
    },
    plugins: [vue()],
    resolve: {
        alias: {
            '@studip-ui': srcDir,
        },
    },
})
