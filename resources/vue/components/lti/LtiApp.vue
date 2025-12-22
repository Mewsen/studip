<script setup>
import {onMounted} from 'vue';
import {useLtiConfig} from '../../store/pinia/lti/Config';

const ltiConfig = useLtiConfig();
const fetchConfigs = async () => {
    try {
        const response = await STUDIP.jsonapi.withPromises().GET(`courses/${STUDIP.URLHelper.parameters.cid}/lti-configs`);

        ltiConfig.$patch({
            isModerator: response.meta['is-moderator'],
            isAdmin: response.meta['is-admin']
        });
    } catch (error) {
        STUDIP.Report.error(error);
    }
}

onMounted(async () => await fetchConfigs());
</script>

<template>
    <div class="lti">
        <div class="lti__container use-utility-classes">
            <div>
                <slot />
            </div>
        </div>
    </div>
</template>
