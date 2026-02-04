<script setup>
import LtiApp from "../../../components/lti/LtiApp.vue";
import LtiPublicationForm from "../../../components/lti/publications/LtiPublicationForm.vue";
import {FocusTrap} from "focus-trap-vue";
</script>

<template>
    <LtiApp>
        <FocusTrap>
            <LtiPublicationForm />
        </FocusTrap>
    </LtiApp>
</template>

<style>
.ui-dialog-content .lti {
    display: grid;
}
</style>
