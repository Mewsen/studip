<script setup>
import LtiApp from "../../../components/lti/LtiApp.vue";
import LtiPublicationForm from "../../../components/lti/publications/LtiPublicationForm.vue";
</script>

<template>
    <LtiApp>
        <LtiPublicationForm />
    </LtiApp>
</template>

<style>
.ui-dialog-content .lti {
    display: grid;
}
</style>
