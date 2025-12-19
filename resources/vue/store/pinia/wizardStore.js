import {defineStore} from 'pinia';
import {ref} from 'vue';

export const useWizardStore = defineStore('wizard', () => {
    let data = ref({});

    function initialize() {
        data.value = {};
    }

    function getData(index) {
        return data[index];
    }

    function setData(index, value) {
        data.value[index] = value;
    }

    return {
        initialize,
        getData,
        setData
    };
});
