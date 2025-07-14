<template>
    <StudipDialog
        title="$gettext('Theme löschen')"
        :closeText="$gettext('Schließen')"
        closeClass="cancel"
    >


    </StudipDialog>
</template>

<script setup>
defineEmits(['close', 'confirm']);
</script>
