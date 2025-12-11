<script setup>
import LtiDeploymentForm from "../../../components/lti/deployments/LtiDeploymentForm.vue";
import {$gettext} from "../../../../assets/javascripts/lib/gettext";

defineProps({
    deployment: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <div class="lti" style="display: flex;">
        <div style="flex: 1;">
            <LtiDeploymentForm :deployment="deployment">
                <footer data-dialog-button>
                    <button class="button accept">
                        {{ $gettext('Speichern') }}
                    </button>
                    <button class="button cancel" type="button" data-dialog-close>
                        {{ $gettext('Abbrechen') }}
                    </button>
                </footer>
            </LtiDeploymentForm>
        </div>
    </div>
</template>
