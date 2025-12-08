<script setup>
import LtiRegistrationForm from '../../../components/lti/registrations/LtiRegistrationForm.vue';
import StudipMessageBox from "../../../components/StudipMessageBox.vue";
import {$gettext} from "../../../../assets/javascripts/lib/gettext";

defineProps({
    role: {
        type: String,
        default: 'tool'
    }
});
</script>

<template>
    <div class="lti" style="display: flex;">
        <div style="flex: 1;">
            <StudipMessageBox type="warning">
                {{ $gettext('Bitte beachten Sie das geltende europäische Datenschutzrecht (DSGVO)!') }}
            </StudipMessageBox>
            <LtiRegistrationForm :role="role" />
        </div>
    </div>
</template>
