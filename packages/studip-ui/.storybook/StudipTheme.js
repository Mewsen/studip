import { create } from 'storybook/theming'

export default create({
    base: 'light',
    brandTitle: 'Stud.IP Storybook',
    brandUrl: 'https://studip.de',
    brandImage: 'https://develop.studip.de/studip/assets/images/logos/studip4-logo.svg',
    brandTarget: '_self',

    colorPrimary: '#28497c',
    colorSecondary: '#28497c',

    fontBase: '"Lato", sans-serif',
    fontCode: 'monospace',
})
