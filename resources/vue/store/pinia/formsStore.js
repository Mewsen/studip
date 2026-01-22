import {defineStore} from 'pinia';
import {ref} from 'vue';

export const useFormsStore = defineStore('forms', () => {
    let data = ref({});

    function initialize() {
        data.value = {};
    }

    function getData(id) {
        return data.value[id];
    }

    function setData(id, value) {
        console.log('setData', id, value);
        data.value[id] = value;
    }

    return {
        initialize,
        getData,
        setData
    };
});
