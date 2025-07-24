<template>
    <div class="file-chooser-toolbar">
        <button v-if="showButtons" class="button" :disabled="!canAddFolder" @click="addFolder">
            {{ $gettext('Ordner hinzufügen') }}
        </button>
        <form v-if="showFolderAdder" class="default inline-form" @submit.prevent="">
            <label for="file-chooser-add-folder">{{ $gettext('Ordnername') }}
            <input
                id="file-chooser-add-folder"
                type="text"
                v-model="newFolderName"
                :placeholder="$gettext('Ordnername')"
            />
            </label>
            <div class="inline-button-group">
                <button :title="$gettext('Ordner anlegen')" @click="createFolder">
                    <studip-icon shape="accept" />
                </button>
                <button :title="$gettext('Abbrechen')" @click="closeAddFolder"><studip-icon shape="decline" /></button>
            </div>
        </form>
        <button v-if="showButtons && !isFolderChooser" class="button" @click="$refs.fileInput.click()">
            {{ $gettext('Datei hinzufügen') }}
        </button>
        <input v-show="false" type="file" ref="fileInput" :disabled="!canAddFile" @change="updateUpload" />

        <form v-if="showUpload" class="default inline-form" @submit.prevent="">
            <label>
                {{ $gettext('Datei') }}
                <input
                    :title="$gettext('Datei auswählen')"
                    type="text"
                    :value="uploadFileName"
                    readonly
                    @click="$refs.fileInput.click()"
                />
            </label>
            <label class="file-chooser-license">
                {{ $gettext('Lizenzauswahl') }}
                <studip-select
                    :options="termsOfUse"
                    label="name"
                    :reduce="(termsOfUse) => termsOfUse.id"
                    :clearable="false"
                    v-model="uploadFileLicense"
                >
                    <template #open-indicator="{ selectAttributes }">
                        <span v-bind="selectAttributes"><studip-icon shape="arr_1down" :size="10" /></span>
                    </template>
                    <template #no-options>
                        {{ $gettext('Es steht keine Auswahl zur Verfügung.') }}
                    </template>
                    <template #selected-option="option">
                        <studip-icon :shape="option.attributes.icon" />
                        <span>{{ option.attributes.name }}</span>
                    </template>
                    <template #option="option">
                        <studip-icon :shape="option.attributes.icon" />
                        <span>{{ option.attributes.name }}</span>
                    </template>
                </studip-select>
            </label>

            <div class="inline-button-group">
                <button :title="$gettext('Datei hochladen')" @click="createFile"><studip-icon shape="accept" /></button>
                <button :title="$gettext('Abbrechen')" @click="closeAddFile"><studip-icon shape="decline" /></button>
            </div>
        </form>
    </div>
</template>

<script>
import axios from 'axios';
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'file-chooser-toolbar',
    emits: ['fileAdded', 'folderAdded'],
    data() {
        return {
            showFolderAdder: false,
            newFolderName: '',
            showUpload: false,
            uploadFile: null,
            uploadFileLicense: null,
        };
    },
    computed: {
        ...mapGetters({
            activeFolderId: 'file-chooser/activeFolderId',
            activeFolder: 'file-chooser/activeFolder',
            activeFolderRangeType: 'file-chooser/activeFolderRangeType',
            courseId: 'file-chooser/courseId',
            isFolderChooser: 'file-chooser/isFolderChooser',
            userId: 'file-chooser/userId',
            termsOfUse: 'terms-of-use/all',
        }),
        showButtons() {
            return !this.showUpload && !this.showFolderAdder;
        },
        canAddFolder() {
            if (this.activeFolder) {
                return (
                    this.activeFolder.attributes['is-writable'] && this.activeFolder.attributes['is-subfolder-allowed']
                );
            }
            return false;
        },
        canAddFile() {
            if (this.activeFolder) {
                return this.activeFolder.attributes['is-writable'];
            }
            return false;
        },
        uploadFileName() {
            return this.uploadFile.name;
        },
    },
    async mounted() {
        await this.loadTermsOfUse();
        this.uploadFileLicense = this.getDefaultTerm();
    },
    methods: {
        ...mapActions({
            loadRangeFolders: 'file-chooser/loadRangeFolders',
            loadFolderFiles: 'file-chooser/loadFolderFiles',
            loadTermsOfUse: 'terms-of-use/loadAll',
        }),
        addFolder() {
            this.showFolderAdder = true;
        },
        closeAddFolder() {
            this.showFolderAdder = false;
            this.newFolderName = '';
        },
        async createFolder() {
            if (this.newFolderName === '') {
                this.closeAddFolder();
            }
            this.showFolderAdder = false;
            const httpClient = await this.getHttpClient();
            const newFolder = {
                data: {
                    type: 'folders',
                    attributes: {
                        name: this.newFolderName,
                        'folder-type': 'StandardFolder',
                    },
                    relationships: {
                        parent: {
                            data: {
                                id: this.activeFolderId,
                                type: 'folders',
                            },
                        },
                    },
                },
            };
            const context = {
                type: this.activeFolderRangeType,
                id: this.activeFolderRangeType === 'users' ? this.userId : this.courseId,
            };
            await httpClient.post(`${context.type}/${context.id}/folders`, newFolder);
            this.$emit('folderAdded');
            this.newFolderName = '';
            this.loadRangeFolders({ rangeType: context.type, rangeId: context.id });
        },
        getHttpClient() {
            return axios.create({
                baseURL: STUDIP.URLHelper.getURL(`jsonapi.php/v1`, {}, true),
                headers: {
                    'Content-Type': 'application/vnd.api+json',
                },
            });
        },
        updateUpload() {
            this.showUpload = true;
            this.uploadFile = this.$refs.fileInput.files[0];
        },
        closeAddFile() {
            this.showUpload = false;
            this.$refs.fileInput.value = null;
        },
        async createFile() {
            this.showUpload = false;
            const termId = this.uploadFileLicense || this.getDefaultTerm();
            const httpClient = await this.getHttpClient();
            const formData = new FormData();
            formData.append('file', this.uploadFile, this.uploadFileName);
            if (termId) {
                formData.append('term-id', termId);
            }
            const url = `folders/${this.activeFolderId}/file-refs`;
            let request = await httpClient.post(url, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });
            try {
                await httpClient.get(request.headers.location);
            } catch (e) {
                console.debug(e);
            }

            await this.loadFolderFiles({ folderId: this.activeFolderId });
            this.$emit('fileAdded');
            this.$refs.fileInput.value = null;
        },
        getDefaultTerm() {
            const defaultTerm = this.termsOfUse.filter((term) => term.attributes['is-default'])[0];
            if (defaultTerm) {
                return defaultTerm.id;
            }
            return null;
        },
    },
    watch: {
        activeFolderId() {
            this.closeAddFolder();
            this.closeAddFile();
        },
    },
};
</script>

<style lang="scss">
.file-chooser-toolbar {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    border-top: solid thin var(--color--action-menu-divider);

    &.with-table {
        border: none;
        margin-top: -16px;
    }

    .inline-form {
        display: flex;
        justify-content: space-between;
        gap: 5px;
        width: 100%;
        margin: 0.8em 0;

        label {
            flex-grow: 1;
            &.file-chooser-license {
                min-width: 50%;
            }
        }

        .inline-button-group {
            margin-top: 25px;
            button {
                border: solid thin var(--base-color);
                border-radius: var(--border-radius-default);
                background-color: transparent;
                height: 30px;
                width: 30px;
                cursor: pointer;

                svg {
                    vertical-align: middle;
                }
            }
        }
    }
}
</style>
