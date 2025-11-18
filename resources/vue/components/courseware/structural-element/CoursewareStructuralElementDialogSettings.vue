<template>
    <studip-dialog
        :title="$gettext('Seiteneinstellungen')"
        :confirmText="$gettext('Speichern')"
        confirmClass="accept"
        :closeText="$gettext('Schließen')"
        closeClass="cancel"
        height="560"
        :width="inContent ? '720' : '500'"
        class="studip-dialog-with-tab"
        @close="closeSettingsDialog"
        @confirm="storeCurrentElement"
    >
        <template v-slot:dialogContent>
            <courseware-tabs class="cw-tab-in-dialog">
                <courseware-tab :name="$gettext('Grunddaten')" :selected="true" :index="0">
                    <form class="default" @submit.prevent="">
                        <label>
                            {{ $gettext('Titel') }}
                            <input type="text" v-model="currentElement.attributes.title" :disabled="isTask"/>
                        </label>
                        <label>
                            {{ $gettext('Beschreibung') }}
                            <textarea
                                v-model="currentElement.attributes.payload.description"
                                class="cw-structural-element-description"
                            ></textarea>
                        </label>
                    </form>
                </courseware-tab>
                <courseware-tab :name="$gettext('Metadaten')" :index="1">
                    <form class="default" @submit.prevent="">
                        <label for="current-payload-color">
                            {{ $gettext('Farbe') }}
                        </label>
                        <StudipSelect
                            id="current-payload-color"
                            v-model="currentElement.attributes.payload.color"
                            :options="colors"
                            :reduce="(color) => color.class"
                            label="name"
                            class="cw-vs-select"
                            :clearable="false"
                        >
                            <template #no-options>
                                {{ $gettext('Es steht keine Auswahl zur Verfügung.') }}.
                            </template>
                            <template #selected-option="option">
                                    <span class="vs__option-color" :style="{ 'background-color': option.hex }"></span
                                    ><span>{{ option.name }}</span>
                            </template>
                            <template #option="option">
                                    <span class="vs__option-color" :style="{ 'background-color': option.hex }"></span
                                    ><span>{{ option.name }}</span>
                            </template>
                        </StudipSelect>
                        <label v-if="!isTask">
                            {{ $gettext('Art des Lernmaterials') }}
                            <select v-model="currentElement.attributes.purpose">
                                <option value="content">{{ $gettext('Inhalt') }}</option>
                                <option v-if="!inCourse" value="template">
                                    {{ $gettext('Aufgabenvorlage') }}
                                </option>
                                <option value="oer">{{ $gettext('OER-Material') }}</option>
                                <option value="portfolio">{{ $gettext('ePortfolio') }}</option>
                                <option value="draft">{{ $gettext('Entwurf') }}</option>
                                <option value="other">{{ $gettext('Sonstiges') }}</option>
                            </select>
                        </label>
                        <template v-if="currentElement.attributes.purpose === 'oer'">
                            <label>
                                {{ $gettext('Lizenztyp') }}
                                <select v-model="currentElement.attributes.payload.license_type">
                                    <option v-for="license in licenses" :key="license.id" :value="license.id">
                                        {{ license.name }}
                                    </option>
                                </select>
                            </label>
                            <label>
                                {{ $gettext('Geschätzter zeitlicher Aufwand') }}
                                <input type="text" v-model="currentElement.attributes.payload.required_time" />
                            </label>
                            <label>
                                {{ $gettext('Niveau') }}<br />
                                {{ $gettext('von') }}
                                <select v-model="currentElement.attributes.payload.difficulty_start">
                                    <option
                                        v-for="difficulty_start in 12"
                                        :key="difficulty_start"
                                        :value="difficulty_start"
                                    >
                                        {{ difficulty_start }}
                                    </option>
                                </select>
                                {{ $gettext('bis') }}
                                <select v-model="currentElement.attributes.payload.difficulty_end">
                                    <option v-for="difficulty_end in 12" :key="difficulty_end" :value="difficulty_end">
                                        {{ difficulty_end }}
                                    </option>
                                </select>
                            </label>
                        </template>
                    </form>
                </courseware-tab>
                <courseware-tab :name="$gettext('Bild')" :index="2">
                    <form class="default" @submit.prevent="">
                        <template v-if="hasImage">
                            <img
                                :src="image"
                                class="cw-structural-element-image-preview"
                                :alt="$gettext('Vorschaubild')"
                            />
                            <label>
                                <button class="button" @click="deleteImage">
                                    {{ $gettext('Bild löschen') }}
                                </button>
                            </label>
                        </template>

                        <div v-else class="cw-structural-element-image-preview-placeholder"></div>

                        <div v-if="uploadFileError" class="messagebox messagebox_error">
                            {{ uploadFileError }}
                        </div>

                        <div v-show="!hasImage">
                            <label>
                                {{ $gettext('Bild hochladen') }}
                                <input
                                    class="cw-file-input"
                                    ref="upload_image"
                                    type="file"
                                    accept="image/*"
                                    @change="checkUploadFile"
                                />
                            </label>
                            {{ $gettext('oder') }}
                            <br />
                            <button class="button" type="button" @click="showStockImageSelector = true">
                                {{ $gettext('Aus dem Bilderpool auswählen') }}
                            </button>
                            <StockImageSelector
                                v-if="showStockImageSelector"
                                @close="showStockImageSelector = false"
                                @select="onSelectStockImage"
                            />
                        </div>
                    </form>
                </courseware-tab>
                <courseware-tab v-if="inContent" :name="$gettext('Rechte')" :index="3">
                    <courseware-content-permissions
                        :element="currentElement"
                        @updateContentApproval="updateContentApproval"
                    />
                </courseware-tab>
            </courseware-tabs>
        </template>
    </studip-dialog>
</template>

<script>
import CoursewareContentPermissions from '../CoursewareContentPermissions.vue';
import CoursewareTabs from '../layouts/CoursewareTabs.vue';
import CoursewareTab from '../layouts/CoursewareTab.vue';
import wizardMixin from '@/vue/mixins/courseware/wizard.js';
import colorMixin from '@/vue/mixins/courseware/colors.js';
import StockImageSelector from '../../stock-images/SelectorDialog.vue';
import { mapActions, mapGetters } from 'vuex';
export default {
    name: 'courseware-structural-element-dialog-settings',
    emits: ['close', 'store'],
    mixins: [colorMixin, wizardMixin],
    components: {
        CoursewareContentPermissions,
        CoursewareTabs,
        CoursewareTab,
        StockImageSelector,
    },
    props: {
        structuralElement: Object,
    },
    data() {
        return {
            currentElement: _.cloneDeep(this.structuralElement),
            showStockImageSelector: false,
            selectedStockImage: null,
            uploadFileError: '',
            uploadImageURL: null,
            deletingPreviewImage: false,
        };
    },
    computed: {
        ...mapGetters({
            blocked: 'currentElementBlocked',
            blockerId: 'currentElementBlockerId',
            blockedByThisUser: 'currentElementBlockedByThisUser',
            blockedByAnotherUser: 'currentElementBlockedByAnotherUser',
            context: 'context',
            userId: 'userId',
            userById: 'users/byId',
            userIsTeacher: 'userIsTeacher',
        }),
        inCourse() {
            return this.context.type === 'courses';
        },
        inContent() {
            return this.context.type === 'users' && this.userId === this.structuralElement?.relationships.user.data.id;
        },
        isTask() {
            return this.structuralElement?.relationships.task.data !== null;
        },
        currentId() {
            return this.structuralElement?.id;
        },
        colors() {
            return this.mixinColors.filter(color => color.darkmode);
        },
        blockingUser() {
            if (this.blockedByAnotherUser) {
                return this.userById({ id: this.blockerId });
            }

            return null;
        },
        blockingUserName() {
            return this.blockingUser ? this.blockingUser.attributes['formatted-name'] : '';
        },
        hasImage() {
            return (this.image || this.selectedStockImage) && this.deletingPreviewImage === false;
        },
        image() {
            if (this.selectedStockImage) {
                return this.selectedStockImage.attributes['download-urls'].small;
            }
            if (this.uploadImageURL) {
                return this.uploadImageURL;
            }
            return this.structuralElement.relationships?.image?.meta?.['download-url'] ?? null;
        },

        imageType() {
            return this.structuralElement.relationships?.image?.data?.type ?? null;
        },
    },
    methods: {
        ...mapActions({
            deleteImageForStructuralElement: 'deleteImageForStructuralElement',
            loadStructuralElement: 'loadStructuralElement',
            lockObject: 'lockObject',
            unlockObject: 'unlockObject',
            setStockImageForStructuralElement: 'setStockImageForStructuralElement',
            showElementEditDialog: 'showElementEditDialog',
            updateStructuralElement: 'updateStructuralElement',
            uploadImageForStructuralElement: 'uploadImageForStructuralElement',
        }),
        checkUploadFile() {
            const file = this.$refs?.upload_image?.files[0];
            this.uploadImageURL = null;
            this.uploadFileError = this.checkUploadImageFile(this.$refs?.upload_image?.files[0]);
            if (this.uploadFileError === '') {
                this.deletingPreviewImage = false;
                this.uploadImageURL = window.URL.createObjectURL(file);
            }
        },
        deleteImage() {
            if (!this.deletingPreviewImage) {
                this.deletingPreviewImage = true;
            }
        },
        onSelectStockImage(stockImage) {
            if (this.$refs?.upload_image) {
                this.$refs.upload_image.value = null;
            }
            this.selectedStockImage = stockImage;
            this.showStockImageSelector = false;
            this.deletingPreviewImage = false;
        },
        updateContentApproval(approval) {
            this.currentElement.attributes['content-approval'] = approval;
        },
        async closeSettingsDialog() {
            this.showElementEditDialog(false);
            await this.unlockObject({ id: this.currentId, type: 'courseware-structural-elements' });
            this.$emit('close')
        },
        async storeCurrentElement() {
            await this.loadStructuralElement(this.currentElement.id);
            if (this.blockedByAnotherUser) {
                this.companionWarning({
                    info: this.$gettext(
                        'Ihre Änderungen konnten nicht gespeichert werden, da %{blockingUserName} die Bearbeitung übernommen hat.',
                        { blockingUserName: this.blockingUserName }
                    ),
                });
                this.showElementEditDialog(false);
                return false;
            }
            if (!this.blocked) {
                await this.lockObject({ id: this.currentId, type: 'courseware-structural-elements' });
            }

            const file = this.$refs?.upload_image?.files[0];
            try {
                this.uploadFileError = '';
                if (file) {
                    await this.uploadImageForStructuralElement({
                        structuralElement: this.currentElement,
                        file,
                    });
                } else if (this.selectedStockImage) {
                    await this.setStockImageForStructuralElement({
                        structuralElement: this.currentElement,
                        stockImage: this.selectedStockImage,
                    });
                } else if (this.deletingPreviewImage) {
                    await this.deleteImageForStructuralElement(this.currentElement);
                }

                this.loadStructuralElement(this.currentElement.id);
            } catch (error) {
                console.error(error);
                this.uploadFileError = this.$gettext(
                    'Das Bild für das neue Lernmaterial konnte nicht gespeichert werden.'
                );
            }

            this.showElementEditDialog(false);

            const element = {
                id: this.currentElement.id,
                type: this.currentElement.type,
                attributes: this.currentElement.attributes,
            };

            await this.updateStructuralElement({ element, id: this.currentId });
            await this.unlockObject({ id: this.currentId, type: 'courseware-structural-elements' });
            this.$emit('store');
        },
    },
    mounted() {
        // this.currentElement = _.cloneDeep(this.structuralElement);
        this.uploadFileError = '';
        this.deletingPreviewImage = false;
        this.uploadImageURL = null;
    },
};
</script>
