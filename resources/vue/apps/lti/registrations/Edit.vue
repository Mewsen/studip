<script setup>
import LtiRegistrationForm from '../../../components/lti/registrations/LtiRegistrationForm.vue';
import StudipMessageBox from "../../../components/StudipMessageBox.vue";
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import LtiApp from "../../../components/lti/LtiApp.vue";

defineProps({
    registration: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <LtiApp>
        <div class="lti" style="display: flex;">
            <div style="flex: 1;">
                <StudipMessageBox type="warning">
                    {{ $gettext('Bitte beachten Sie das geltende europäische Datenschutzrecht (DSGVO)!') }}
                </StudipMessageBox>

                <LtiRegistrationForm :registration="registration" :role="registration.role" />
            </div>
        </div>
    </LtiApp>
</template>
