import { fileURLToPath } from 'node:url'
import { mergeConfig, defineConfig, configDefaults } from 'vitest/config'
import viteConfig from './vite.config'

export default mergeConfig(
    viteConfig,
    defineConfig({
        test: {
            environment: 'jsdom',
            exclude: [...configDefaults.exclude, 'e2e/**', 'src/composables/**/*.{js,ts,mjs}'],
            root: fileURLToPath(new URL('./', import.meta.url)),
            coverage: {
                enabled: true,
                provider: 'v8',
                reporter: ['text', 'json', 'html'],
                statements: 90,
                branches: 80,
                functions: 90,
                lines: 90,
                include: ['src/components/**/*.vue'],
            },
        },
    }),
)
