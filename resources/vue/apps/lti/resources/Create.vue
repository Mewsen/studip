<script setup>
import ResourceForm from "../../../components/lti/resources/ResourceForm.vue";
import LtiApp from "../../../components/lti/LtiApp.vue";

const props = defineProps({
    registrations: {
        type: Array,
        default: () => ([])
    },
    icons: {
        type: Array,
        default: () => ([])
    }
});
</script>

<template>
    <LtiApp>
        <div style="display: flex;">
            <div style="flex: 1;">
                <ResourceForm v-bind="{...props}" />
            </div>
        </div>
    </LtiApp>
</template>
