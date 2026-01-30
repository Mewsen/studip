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
        <ResourceForm v-bind="{...props}" />
    </LtiApp>
</template>

<style>
.ui-dialog-content .lti {
    display: grid;
}
</style>
