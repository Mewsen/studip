<script setup>
import LtiApp from "../../../components/lti/LtiApp.vue";
import LtiPublicationForm from "../../../components/lti/publications/LtiPublicationForm.vue";
import {FocusTrap} from "focus-trap-vue";

defineProps({
    publication: {
        type: Object,
        default: () => ({})
    }
});
</script>

<template>
    <FocusTrap>
        <LtiApp>
            <LtiPublicationForm :publication="publication" />
        </LtiApp>
    </FocusTrap>
</template>

<style>
.ui-dialog-content .lti {
    display: grid;
}
</style>
