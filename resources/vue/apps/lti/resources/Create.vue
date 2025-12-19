<script setup>
import ResourceForm from "../../../components/lti/resources/ResourceForm.vue";

defineProps({
    registrations: {
        type: Array,
        default: () => ([])
    }
});
</script>

<template>
    <div class="lti" style="display: flex;">
        <div style="flex: 1;">
            <ResourceForm :registrations="registrations" />
        </div>
    </div>
</template>
