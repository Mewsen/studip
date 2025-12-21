<script setup>
import ResourceForm from "../../../components/lti/resources/ResourceForm.vue";

const props = defineProps({
    resource: {
        type: Object,
        required: true
    },
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
    <div class="lti" style="display: flex;">
        <div style="flex: 1;">
            <ResourceForm v-bind="{...props}" />
        </div>
    </div>
</template>
