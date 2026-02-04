<script setup>
import ResourceForm from "../../../components/lti/resources/ResourceForm.vue";
import LtiApp from "../../../components/lti/LtiApp.vue";
import {FocusTrap} from "focus-trap-vue";

const props = defineProps({
    registrations: {
        type: Array,
        default: () => ([])
    },
    icons: {
        type: Array,
        default: () => ([])
    }
});
</script>

<template>
    <LtiApp>
        <FocusTrap>
            <ResourceForm v-bind="{...props}" />
        </FocusTrap>
    </LtiApp>
</template>

<style>
.ui-dialog-content .lti {
    display: grid;
}
</style>
