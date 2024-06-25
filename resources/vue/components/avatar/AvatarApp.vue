<template>
    <div class="avatar">
        <section class="contentbox">
            <header>
                <h1>{{ avatarAltText }}</h1>
            </header>
            <section v-if="!editingImage" class="avatar-display">
                <img class="avatar-original" :src="avatarUrl" :alt="avatarAltText" />
                <div class="button-wrapper">
                    <button class="button edit" @click="changeImage">
                        <span v-if="isCustomized">{{ $gettext('Ändern') }}</span
                        ><span v-else>{{ $gettext('Bild wählen') }}</span>
                    </button>
                    <button v-if="isCustomized" class="button trash" @click="showRemoveDialog = true">
                        {{ $gettext('Löschen') }}
                    </button>
                </div>
            </section>
            <section v-show="editingImage" class="avatar-edit">
                <div class="cropper-container">
                    <img ref="newImage" src="" @zoom="checkImageSize" />
                </div>
                <studip-message-box v-if="invalid" type="warning" :hideClose="false">{{
                    $gettext(
                        'Bildauswahl zu klein. Bitte wählen Sie einen größeren Ausschnitt oder ein anderes Bild aus.'
                    )
                }}</studip-message-box>
                <div class="cropper-actions-wrapper">
                    <div class="cropper-actions-group">
                        <div class="labeled-range-input">
                            <label for="cropper-rotate" class="sr-only">{{ $gettext('Neigungswähler') }}</label>
                            <input
                                id="cropper-rotate"
                                type="range"
                                min="-45"
                                max="45"
                                steps="1"
                                v-model="cropperRotate"
                                @input="updateCropperRotate"
                            />
                            <div class="labeled-range-input-labels">
                                <span>-45°</span>
                                <span>0°</span>
                                <span>+45°</span>
                            </div>
                        </div>
                    </div>
                    <div class="cropper-actions-group">
                        <button
                            class="cropper-actions-button"
                            @click="updateCropperBaseRotation(90)"
                            :title="$gettext('nach rechts drehen')"
                        >
                            <StudipIcon shape="rotate-right" :size="24" />
                        </button>
                        <button
                            class="cropper-actions-button"
                            @click="updateCropperBaseRotation(-90)"
                            :title="$gettext('nach links drehen')"
                        >
                            <StudipIcon shape="rotate-left" :size="24" />
                        </button>
                        <button
                            class="cropper-actions-button"
                            @click="zoomCropper(+0.1)"
                            :title="$gettext('vergrößern')"
                        >
                            <StudipIcon shape="zoom-in2" :size="24" />
                        </button>
                        <button
                            class="cropper-actions-button"
                            @click="zoomCropper(-0.1)"
                            :title="$gettext('verkleinern')"
                        >
                            <StudipIcon shape="zoom-out2" :size="24" />
                        </button>
                        <button class="cropper-actions-button" @click="flip()" :title="$gettext('horizontal spiegeln')">
                            <StudipIcon shape="flip" :size="24" />
                        </button>
                        <button class="cropper-actions-button" @click="resetCropper" :title="$gettext('zurücksetzen')">
                            <StudipIcon shape="refresh" :size="24" />
                        </button>
                        <button class="cropper-actions-button" @click="changeImage" :title="$gettext('Bild auswählen')">
                            <StudipIcon shape="upload" :size="24" />
                        </button>
                    </div>
                    <div class="cropper-actions-group">
                        <button class="button accept" @click="storeAvatar" :disabled="invalid">
                            {{ $gettext('Speichern') }}
                        </button>
                        <button class="button cancel" @click="abortEditing">{{ $gettext('Abbrechen') }}</button>
                    </div>
                </div>
            </section>
        </section>
        <input
            id="avatar-upload"
            ref="uploadFile"
            type="file"
            accept="image/gif,image/png,image/jpeg,image/webp;capture=camera"
            @change="updateUploadImage"
        />
        <studip-dialog
            v-if="showRemoveDialog"
            :title="$gettext('Bild löschen')"
            :question="$gettext('Möchten Sie dieses Bild wirklich löschen')"
            height="180"
            width="360"
            @confirm="removeAvatar"
            @close="showRemoveDialog = false"
        ></studip-dialog>
        <studip-dialog
            v-if="showChangeImageDialog"
            :title="$gettext('Bildquelle auswählen')"
            :closeText="$gettext('Abbrechen')"
            closeClass="cancel"
            height="310"
            @close="showChangeImageDialog = false"
        >
            <template v-slot:dialogContent>
                <div class="square-button-panel">
                    <studip-square-button
                        icon="upload"
                        :title="$gettext('Bild hochladen')"
                        @click="selectUploadImage"
                    ></studip-square-button>
                    <studip-square-button
                        icon="block-gallery"
                        :title="$gettext('Aus Bilderpool auswählen')"
                        @click="selectStockImage"
                    ></studip-square-button>
                </div>
            </template>
        </studip-dialog>
        <StockImageSelector
            v-if="showStockImageSelector"
            @close="showStockImageSelector = false"
            @select="onSelectStockImage"
        />
    </div>
</template>

<script>
import axios from 'axios';
import StudipDialog from '../StudipDialog.vue';
import StudipMessageBox from '../StudipMessageBox.vue';
import StudipSquareButton from '../StudipSquareButton.vue';
import StockImageSelector from '../stock-images/SelectorDialog.vue';
import { mapGetters, mapActions } from 'vuex';

import Cropper from 'cropperjs';

export default {
    components: {
        StudipDialog,
        StudipMessageBox,
        StudipSquareButton,
        StockImageSelector,
    },
    data() {
        return {
            showRemoveDialog: false,
            showChangeImageDialog: false,
            showStockImageSelector: false,
            selectedStockImage: false,
            uploadImage: null,
            editingImage: false,
            base64Image: null,
            cropper: null,
            invalid: false,
            cropperRotate: 0,
            cropperBaseRotation: 0,
            fliped: false,
        };
    },
    computed: {
        ...mapGetters({
            context: 'context',
            currentAvatar: 'currentAvatar',
            isCourseAvatar: 'isCourseAvatar',
            isInstituteAvatar: 'isInstituteAvatar',
            isStudygroupAvatar: 'isStudygroupAvatar',
            isUserAvatar: 'isUserAvatar',
            isCustomized: 'isCustomized',
        }),
        avatarUrl() {
            return this.currentAvatar.meta.url.normal;
        },
        avatarAltText() {
            if (this.isUserAvatar) {
                return this.$gettext('Mein Profilbild');
            }
            if (this.isCourseAvatar) {
                return this.$gettext('Veranstaltungsbild');
            }
            if (this.isStudygroupAvatar) {
                return this.$gettext('Studiengruppenbild');
            }
            if (this.isInstituteAvatar) {
                return this.$gettext('Einrichtungsbild');
            }
            return '';
        },
    },
    methods: {
        changeImage() {
            if (this.isUserAvatar) {
                this.selectUploadImage();
            } else {
                this.showChangeImageDialog = true;
            }
        },
        selectUploadImage() {
            this.showChangeImageDialog = false;
            this.$refs.uploadFile.click();
        },
        selectStockImage() {
            this.showChangeImageDialog = false;
            this.showStockImageSelector = true;
        },
        onSelectStockImage(stockImage) {
            if (this.cropper) {
                this.resetCropper();
                this.cropper.destroy();
            }
            this.base64Image = null;
            this.uploadImage = null;
            this.selectedStockImage = stockImage;
            this.showStockImageSelector = false;
            this.loadStockImage();
        },
        updateUploadImage() {
            if (this.cropper) {
                this.resetCropper();
                this.cropper.destroy();
            }
            this.base64Image = null;
            this.createBase64Image(this.$refs.uploadFile.files[0]);
        },
        createBase64Image(FileObject) {
            const reader = new FileReader();
            reader.onload = (event) => {
                this.base64Image = event.target.result;
                this.enableCropper();
            };
            reader.readAsDataURL(FileObject);
        },
        enableCropper() {
            let image = this.$refs.newImage;
            image.src = this.base64Image;
            this.cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 0.9,
                dragMode: 'move',
                cropBoxMovable: false,
                cropBoxResizable: false,
                toggleDragModeOnDblclick: false,
            });
            this.editingImage = true;
        },
        checkImageSize() {
            const data = this.cropper.getData();
            if (data.width < 250 || data.height < 250) {
                this.invalid = true;
            } else {
                this.invalid = false;
            }
        },
        updateCropperBaseRotation(val) {
            this.cropperBaseRotation += val;
            if (this.cropperBaseRotation > 270) {
                this.cropperBaseRotation = 0;
            }
            this.cropperRotate = 0;
            this.cropper.rotateTo(this.cropperBaseRotation);
        },
        updateCropperRotate() {
            this.cropper.rotateTo(parseInt(this.cropperRotate) + this.cropperBaseRotation);
        },
        resetCropper() {
            this.cropper.reset();
            this.fliped = false;
            this.cropperBaseRotation = 0;
            this.cropperRotate = 0;
        },
        zoomCropper(val) {
            this.cropper.zoom(val);
        },
        flip() {
            this.fliped ? this.cropper.scale(1, 1) : this.cropper.scale(-1, 1);
            this.fliped = !this.fliped;
        },
        abortEditing() {
            this.resetCropper();
            this.cropper.destroy();
            this.selectedStockImage = null;
            this.$refs.uploadFile.value = '';
            this.base64Image = null;
            this.editingImage = false;
            this.fliped = false;
        },

        loadStockImage() {
            const url = this.selectedStockImage.attributes['download-urls'].original;
            fetch(url)
                .then((response) => response.blob())
                .then(
                    (blob) =>
                        new Promise((resolve, reject) => {
                            const reader = new FileReader();
                            reader.onloadend = () => resolve(reader.result);
                            reader.onerror = reject;
                            reader.readAsDataURL(blob);
                        })
                )
                .then((base64) => {
                    this.base64Image = base64;
                    this.enableCropper();
                });
        },

        storeAvatar() {
            if (this.invalid) {
                return false;
            }
            const croppedImage = this.cropper.getCroppedCanvas().toDataURL('image/webp');
            const data = {
                data: {
                    'range-id': this.context.id,
                    'range-type': this.context.type,
                    image: croppedImage,
                },
            };

            axios.post(STUDIP.URLHelper.getURL(`jsonapi.php/v1/${this.context.type}/${this.context.id}/avatar`, {}, true), data).then((response) => {
                location.reload();
            });
        },

        removeAvatar() {
            axios
                .delete(
                    STUDIP.URLHelper.getURL(`jsonapi.php/v1/${this.context.type}/${this.context.id}/avatar`, {}, true)
                )
                .then((response) => {
                    location.reload();
                });
        },
    },
};
</script>
<style scoped lang="scss">
.avatar {
    max-width: 520px;
}

.avatar-original {
    max-width: 500px;
}
#avatar-upload {
    display: none;
}
.cropper-container {
    width: 500px;
    height: 500px;
    margin-bottom: 1em;
}
.square-button-panel {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    width: 100%;
    justify-content: center;
}

.cropper-actions-wrapper {
    max-width: 500px;

    .cropper-actions-group {
        display: flex;
        justify-content: space-between;
        margin: 1em 0;

        .cropper-actions-button {
            background-color: transparent;
            border: none;
            cursor: pointer;
        }

        .labeled-range-input {
            width: 100%;
            input[type='range'] {
                width: 100%;
                -webkit-appearance: none;
                appearance: none;
                cursor: pointer;
                height: 2px;
                background: var(--content-color-40);

                &::-webkit-slider-thumb {
                    -webkit-appearance: none;
                    appearance: none;
                    height: 16px;
                    width: 16px;
                    background-color: var(--base-color);
                    border-radius: 50%;
                    border: none;
                }

                &::-moz-range-thumb {
                    height: 16px;
                    width: 16px;
                    background-color: var(--base-color);
                    border-radius: 50%;
                    border: none;
                }
            }

            .labeled-range-input-labels {
                display: flex;
                justify-content: space-between;
                span {
                    margin-left: 0.5em;
                }
            }
        }
    }
}
</style>
