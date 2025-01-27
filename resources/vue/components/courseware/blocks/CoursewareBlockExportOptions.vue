<template>
    <section class="cw-block-export-options">
        <header>{{ $gettext('Export Optionen') }}</header>
        <div class="cw-block-features-content">
            <button class="button" @click="$emit('close')">{{ $gettext('Schließen') }}</button>
        </div>
    </section>
</template>

<script>
export default {
    name: 'courseware-block-export-options',
    emits: ['close'],
    props: {
        block: Object,
    },
};
</script>
