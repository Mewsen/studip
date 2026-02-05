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
    <FocusTrap>
        <LtiApp>
            <LtiDeploymentForm :deployment="deployment" :registration="registration" />
        </LtiApp>
    </FocusTrap>
</template>

<style>
.ui-dialog-content .lti {
    display: grid;
}
</style>
