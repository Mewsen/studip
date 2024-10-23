import type { Preview  } from '@storybook/vue3';
import '../resources/assets/stylesheets/mixins/colors.scss';
import '../resources/assets/stylesheets/scss/variables.scss';

// Mock von window.STUDIP hinzufügen
if (!window.STUDIP) {
    window.STUDIP = {
        ASSETS_URL: 'assets/'
    };
}

const preview: Preview = {
    parameters: {
        controls: {
            matchers: {
                color: /(background|color)$/i,
                date: /Date$/i,
            },
        },
    },
};

export default preview;
