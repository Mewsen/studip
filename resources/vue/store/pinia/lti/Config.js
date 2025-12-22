import {defineStore} from 'pinia';
import {ref} from 'vue';

export const useLtiConfig = defineStore(
    'lti_config',
    () => {
        const isAdmin  = ref(false);
        const isModerator  = ref(false);


        return {
            isAdmin,
            isModerator
        }
    }
)
