<script setup>
import LtiDeploymentForm from "../../../components/lti/deployments/LtiDeploymentForm.vue";
import {$gettext} from "../../../../assets/javascripts/lib/gettext";

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
    <div class="lti" style="display: flex;">
        <div style="flex: 1;">
            <LtiDeploymentForm :deployment="deployment" :registration="registration" />
        </div>
    </div>
</template>
