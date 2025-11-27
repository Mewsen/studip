<template>
    <SidebarWidget :title="$gettext('Aktionen')">
        <template #content>
            <ul class="widget-list widget-links" :aria-label="$gettext('Aktionen')">
                <li>
                    <button @click="onUploadClick">
                        <studip-icon shape="upload" class="widget-action-icon" :inline="true" />
                        {{ $gettext('Bild hinzufügen') }}
                    </button>
                </li>
                <li>
                    <button @click="onZipUploadClick">
                        <studip-icon shape="import" class="widget-action-icon" :inline="true" />
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
