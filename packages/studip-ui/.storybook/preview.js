import { setup } from '@storybook/vue3'
import '../dist/studip-ui.css'
import StoryDescriptionWrapper from './StoryDescriptionWrapper.vue'
import './preview-styles.css'

setup((app) => {
    app.config.globalProperties.$gettext = (msg, vars) => {
        if (vars) {
            return msg.replace(/%\{\s*(\w+)\s*\}/g, (_, key) => vars[key] ?? '')
        }
        return msg
    }
})

/** @type { import('@storybook/vue3-vite').Preview } */
const preview = {
    parameters: {
        controls: {
            matchers: {
                color: /(background|color)$/i,
                date: /Date$/i,
            },
        },
        docs: {
            source: {
                language: 'html',
                type: 'code',
                transform: (code, storyContext) => {
                    const component = storyContext.component
                    const nameInPascalCase = component.name || component.__docgenInfo?.displayName

                    if (nameInPascalCase) {
                        const componentNameKebab = toKebabCase(nameInPascalCase)
                        return generateKebabCode(storyContext.args, componentNameKebab)
                    }

                    return generateKebabCode(storyContext.args, 'component')
                },
            },
        },
    },
}

export default preview

export const decorators = [
    (story, context) => {
        const storyDescription = context.parameters?.docs?.description?.story

        if (storyDescription && context.viewMode === 'story') {
            return {
                components: { StoryDescriptionWrapper, story },
                template: `<StoryDescriptionWrapper :description="storyDescription"><story /></StoryDescriptionWrapper>`,
                data: () => ({ storyDescription }),
            }
        }

        return story()
    },
]

const toKebabCase = (str) => {
    return str
        .replace(/([A-Z]+)([A-Z][a-z])/g, '$1-$2')
        .replace(/([a-z0-9])([A-Z])/g, '$1-$2')
        .toLowerCase()
}

export const generateKebabCode = (args, componentName) => {
    const props = Object.keys(args)
        .filter((key) => args[key] !== undefined && args[key] !== false)
        .map((key) => {
            const kebabKey = toKebabCase(key)
            let value = args[key]

            if (typeof value === 'boolean' && value === true) {
                return `${kebabKey}`
            }
            if (typeof value === 'string') {
                return `${kebabKey}="${value}"`
            }
            return `${kebabKey}="${value}"`
        })
        .filter((attr) => attr.length > 0)
        .join(' ')

    return `<${componentName} ${props} />`
}
