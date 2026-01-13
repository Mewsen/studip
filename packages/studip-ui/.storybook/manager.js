import './manager.css'
import { addons } from 'storybook/manager-api'
import studipTheme from './StudipTheme'
import { defaultConfig } from 'storybook-addon-tag-badges/manager-helpers'

addons.setConfig({
    theme: studipTheme,
    tagBadges: [
        {
            tags: { prefix: 'since' },
            badge: ({ getTagSuffix, tag }) => {
                const version = getTagSuffix(tag)
                return {
                    text: `since: ${version}`,
                    style: 'blue',
                }
            },
            display: {
                sidebar: [],
                toolbar: true,
                mdx: true,
            },
        },
        {
            tags: { prefix: 'changed' },
            badge: ({ getTagSuffix, tag }) => {
                const version = getTagSuffix(tag)
                return {
                    text: `changed: ${version}`,
                    style: 'turquoise',
                }
            },
            display: {
                sidebar: [],
                toolbar: true,
                mdx: true,
            },
        },

        ...defaultConfig,
    ],
})
