<script setup>
import ResourceForm from "../../../components/lti/resources/ResourceForm.vue";

defineProps({
    resource: {
        type: Object,
        required: true
    },
    registrations: {
        type: Array,
        default: () => ([])
    }
});
</script>

<template>
    <div class="lti" style="display: flex;">
        <div style="flex: 1;">
            <ResourceForm :resource="resource" :registrations="registrations" />
        </div>
    </div>
</template>
