<template>
    <label class="studiprequired" :for="id">
        <span class="textlabel">{{ label }}</span>
        <span class="asterisk" :title="$gettext('Dies ist ein Pflichtfeld')" aria-hidden="true">*</span>
        <slot></slot>
    </label>
</template>

<script>
export default {
    props: {
        id: {
            type: String,
            required: true,
        },
        label: {
            type: String,
            required: true,
        },
    },
};
</script>
