import { ref } from 'vue';
import { $gettext } from '../../../assets/javascripts/lib/gettext';

const categories = ref([
    { title: $gettext('Texte'), type: 'text' },
    { title: $gettext('Multimedia'), type: 'multimedia' },
    { title: $gettext('Interaktion'), type: 'interaction' },
    { title: $gettext('Gestaltung'), type: 'layout' },
    { title: $gettext('Externe Inhalte'), type: 'external' },
    { title: $gettext('Biografie'), type: 'biography' },
]);

export const useBlockCategoryManager = () => {
    const addCategory = (title, type) => {
        categories.value = [...categories.value.filter((category) => category.type !== type), { title, type }];
    };

    return { categories, addCategory };
};
