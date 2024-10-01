<template>
    <SidebarWidget :title="$gettext('Aktionen')">
        <template #content>
            <ul class="widget-list widget-links">
                <li>
                    <studip-icon shape="upload" class="widget-action-icon" />
                    <button @click="onUploadClick">{{ $gettext('Bild hinzufügen') }}</button>
                </li>
                <li>
                    <studip-icon shape="import" class="widget-action-icon" />
                    <button @click="onZipUploadClick">{{ $gettext('Bildersammlung importieren') }}</button>
                </li>
            </ul>
        </template>
    </SidebarWidget>
</template>
<script>
import SidebarWidget from '../SidebarWidget.vue';

export default {
    components: {
        SidebarWidget,
    },
    methods: {
        onUploadClick() {
            this.$emit('initiateUpload');
        },
        onZipUploadClick() {
            this.$emit('initiateZipUpload');
        }
    },
};
</script>
<style scoped>
.widget-list li {
    position: relative;
}
.widget-action-icon {
    position: absolute;
    left: 0;
}
</style>
