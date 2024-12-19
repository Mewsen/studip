<template>
    <div class="file-chooser">
        <button class="button" @click="openDialog">{{ buttonTitle }}</button><span>{{ selectedName }}</span>
        <file-chooser-dialog v-if="showDialog" v-bind="$props" @close="closeDialog" @selected="select" />
    </div>
</template>

<script>
import FileChooserDialog from './file-chooser/FileChooserDialog.vue';
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'studip-file-chooser',
    components: {
        FileChooserDialog,
    },
    emits: ['update:modelValue'],
    props: {
        selectable: {
            type: String,
            default: 'file',
            validator: (value) => {
                return ['file', 'folder'].includes(value);
            },
        },
        modelValue: {
            type: String,
            required: false,
        },
        courseId: {
            type: String,
            validator: (value) => {
                return value !== '';
            },
            required: false,
        },
        userId: {
            type: String,
            validator: (value) => {
                return value !== '';
            },
            required: false,
        },
        isImage: { type: Boolean, default: false },
        isVideo: { type: Boolean, default: false },
        isAudio: { type: Boolean, default: false },
        isDocument: { type: Boolean, default: false },
        excludedCourseFolderTypes: { type: Array, default: () => [] },
        excludedUserFolderTypes: { type: Array, default: () => [] },
    },
    data() {
        return {
            showDialog: false,
            selectedFile: null,
            selectedFolder: null,
        };
    },
    computed: {
        ...mapGetters({
            fileById: 'file-refs/byId',
            folderById: 'folders/byId',
        }),
        buttonTitle() {
            if (this.selectable === 'folder') {
                return this.$gettext('Ordner auswählen');
            }

            return this.$gettext('Datei auswählen');
        },
        selectedName() {
            if (this.selectable === 'folder') {
                if (this.modelValue === '') {
                    return this.$gettext('Kein Ordner ausgewählt');
                }
                return this.$gettext(
                    'Ordner "%{folderName}" ausgewählt'
                    ,
                    { folderName: this.folderById({ id: this.modelValue })?.attributes?.name ?? '-' }
                );
            }

            if (this.modelValue === '') {
                return this.$gettext('Keine Datei ausgewählt');
            }
            return this.$gettext(
                'Datei "%{fileName}" ausgewählt',
                { fileName: this.fileById({ id: this.modelValue })?.attributes?.name ?? '-' }
            );
        },
    },
    methods: {
        ...mapActions({
            loadFile: 'file-refs/loadById',
            loadFolder: 'folders/loadById',
        }),
        openDialog() {
            this.showDialog = true;
        },
        closeDialog() {
            this.showDialog = false;
        },
        select(id) {
            this.closeDialog();
            this.$emit('update:modelValue', id);
        },
        loadSelection() {
            if (this.selectable === 'folder') {
                if (this.modelValue !== '') {
                    this.loadFolder({ id: this.modelValue });
                }
            } else {
                if (this.modelValue !== '') {
                    this.loadFile({ id: this.modelValue });
                }
            }
        }
    },
    mounted() {
        this.loadSelection();
    },
};
</script>

<style lang="scss" scoped>
.file-chooser {
    text-indent: 0;
    max-width: 48em;
    button {
        margin: 0.5ex 0 0.5ex 0;
        padding: 5px 15px;
        width: 150px;
    }
    span {
        box-sizing: border-box;
        border: solid thin var(--content-color-40);
        border-left: none;
        display: inline-block;
        font-size: 14px;
        line-height: 130%;
        min-width: 100px;
        width: calc(100% - 150px);
        overflow: hidden;
        text-overflow: ellipsis;
        padding: 5px 15px;
        vertical-align: middle;
        white-space: nowrap;
    }
}
</style>
