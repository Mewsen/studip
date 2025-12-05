<template>
    <SidebarWidget :title="$gettext('Aktionen')">
        <template #content>
            <ul class="widget-list widget-links">
                <li>
                    <button type="button" @click="onUploadClick" class="as-link">
                        <studip-icon shape="upload" />
                        {{ $gettext('Bild hinzufügen') }}
                    </button>
                </li>
                <li>
                    <button type="button" @click="onZipUploadClick" class="as-link">
                        <studip-icon shape="import" />
                        {{ $gettext('Bildersammlung importieren') }}
                    </button>
                </li>
            </ul>
        </template>
    </SidebarWidget>
</template>
<script>
import SidebarWidget from '../SidebarWidget.vue';

export default {
    emits: ['initiateUpload', 'initiateZipUpload'],
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
