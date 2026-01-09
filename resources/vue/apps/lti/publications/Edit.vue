<script setup>
import LtiApp from "../../../components/lti/LtiApp.vue";
import LtiPublicationForm from "../../../components/lti/publications/LtiPublicationForm.vue";

defineProps({
    publication: {
        type: Object,
        default: () => ({})
    }
});
</script>

<template>
    <LtiApp>
        <LtiPublicationForm :publication="publication" />
    </LtiApp>
</template>

<style>
.ui-dialog-content .lti {
    display: grid;
}
</style>
