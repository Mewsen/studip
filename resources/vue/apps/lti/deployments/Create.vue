<script setup>
import LtiDeploymentForm from "../../../components/lti/deployments/LtiDeploymentForm.vue";
import LtiApp from "../../../components/lti/LtiApp.vue";
import {FocusTrap} from "focus-trap-vue";

defineProps({
    deployment: {
        type: Object,
        default: () => ({})
    },
    registration: {
        type: Object,
        default: () => ({
           role: 'tool'
        })
    }
});
</script>

<template>
    <LtiApp>
        <FocusTrap>
            <LtiDeploymentForm :deployment="deployment" :registration="registration" />
        </FocusTrap>
    </LtiApp>
</template>

<style>
.ui-dialog-content .lti {
    display: grid;
}
</style>
