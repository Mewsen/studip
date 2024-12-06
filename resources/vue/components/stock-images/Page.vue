<template>
    <div class="stock-images-page">
        <studip-message-box v-if="showZipUploadMessage" :type="zipUploadMessageType">
            {{ zipUploadMessage }}
        </studip-message-box>
        <ImagesPagination
            v-show="!showUploadIndicator"
            :per-page="perPage"
            :stock-images="filteredStockImages"
            v-model:page="page"
        >
            <ImagesList
                :checked-images="checkedImages"
                :page="page"
                :per-page="perPage"
                :stock-images="filteredStockImages"
                @checked="onCheckboxChange"
                @open-page="(newPage) => (page = newPage)"
                @search="onSearch"
                @select="onSelectImage"
            />
        </ImagesPagination>
        <studip-progress-indicator
            v-show="showUploadIndicator"
            class="image-upload-indicator"
            :description="$gettext('Bilder werden hochgeladen...')"
        >
        </studip-progress-indicator>
        <Teleport to="#stock-images-widget" name="sidebar-stock-images">
            <SearchWidget :query="query" @search="onSearch" />
            <OrientationFilterWidget v-model:filters="filters" />
            <ColorFilterWidget v-model:filters="filters" />
            <ActionsWidget @initiateUpload="onUploadDialogShow" @initiateZipUpload="onZipUploadDialogShow" />
        </Teleport>
        <EditDialog
            :stock-image="selectedImage"
            :suggested-tags="suggestedTags"
            @confirm="onEditDialogConfirm"
            @cancel="selectedImage = null"
        />
        <UploadDialog
            :show="showUpload"
            :suggested-tags="suggestedTags"
            @confirm="onUploadDialogConfirm"
            @cancel="showUpload = false"
        />
        <ZipUploadDialog
            :show="showZipUpload"
            @confirm="onZipUploadDialogConfirm"
            @cancel="showZipUpload = false"
        ></ZipUploadDialog>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import ActionsWidget from './ActionsWidget.vue';
import ColorFilterWidget from './ColorFilterWidget.vue';
import EditDialog from './EditDialog.vue';
import ImagesList from './ImagesList.vue';
import ImagesPagination from './ImagesPagination.vue';
import OrientationFilterWidget from './OrientationFilterWidget.vue';
import SearchWidget from './SearchWidget.vue';
import UploadDialog from './UploadDialog.vue';
import ZipUploadDialog from './ZipUploadDialog.vue';
import StudipMessageBox from '../StudipMessageBox.vue';
import StudipProgressIndicator from '../StudipProgressIndicator.vue';
import { searchFilterAndSortImages } from './filters.js';

export default {
    components: {
        ActionsWidget,
        ColorFilterWidget,
        EditDialog,
        ImagesList,
        ImagesPagination,
        OrientationFilterWidget,
        SearchWidget,
        UploadDialog,
        ZipUploadDialog,
        StudipMessageBox,
        StudipProgressIndicator,
    },
    data: () => ({
        checkedImages: [],
        filters: {
            orientation: 'any',
            colors: [],
        },
        page: 1,
        perPage: 10,
        query: '',
        selectedImage: null,
        showUpload: false,
        showZipUpload: false,
        showZipUploadMessage: false,
        zipUploadMessage: '',
        zipUploadMessageType: 'success',
        showUploadIndicator: false,
    }),
    computed: {
        ...mapGetters({
            stockImages: 'stock-images/all',
            stockImagesMeta: 'stock-images/lastMeta',
            suggestedTags: 'studip/stockImages/allTags',
        }),
        filteredStockImages() {
            return searchFilterAndSortImages(this.stockImages, this.query, this.filters);
        },
    },
    methods: {
        ...mapActions({
            createStockImage: 'studip/stockImages/create',
            createStockImagesFromZip: 'studip/stockImages/createFromZip',
            loadStockImages: 'stock-images/loadWhere',
            updateStockImage: 'studip/stockImages/update',
        }),
        onCheckboxChange(image) {
            if (!this.checkedImages.includes(image.id)) {
                this.checkedImages.push(image.id);
            } else {
                this.checkedImages = this.checkedImages.filter((id) => id !== image.id);
            }
        },
        onEditDialogConfirm(attributes) {
            this.updateStockImage({ stockImage: this.selectedImage, attributes });
            this.selectedImage = null;
        },
        onSearch(query) {
            this.query = query;
        },
        onSelectImage(image) {
            this.selectedImage = image;
        },
        onUploadDialogConfirm({ file, metadata }) {
            this.createStockImage([file, metadata])
                .then(() => {
                    this.showUpload = false;
                })
                .catch((error) => {
                    console.error('Could not create stock image', error);
                });
        },
        onZipUploadDialogConfirm({ file }) {
            this.showZipUpload = false;
            this.showUploadIndicator = true;
            this.createStockImagesFromZip([file])
                .then((resp) => {
                    this.showUploadIndicator = false;
                    this.showZipUploadMessage = true;
                    this.zipUploadMessageType = 'success';
                    this.zipUploadMessage = this.$ngettext(
                        '%{length} Bild wurde hinzugefügt',
                        '%{length} Bilder wurden hinzugefügt',
                        resp.data['image-count'],
                        { length: resp.data['image-count'] }
                    );
                    this.$nextTick(() => {
                        this.fetchStockImages();
                    });
                })
                .catch(() => {
                    this.showUploadIndicator = false;
                    this.showZipUploadMessage = true;
                    this.zipUploadMessageType = 'error';
                    this.zipUploadMessage = this.$gettext('Beim importieren der Bilder ist ein Fehler aufgetreten.');
                    this.fetchStockImages();
                });
        },
        onUploadDialogShow() {
            this.showUpload = true;
        },
        onZipUploadDialogShow() {
            this.showZipUpload = true;
            this.showZipUploadMessage = false;
            this.zipUploadMessage = '';
        },
        async fetchStockImages() {
            const loadLimit = 30;
            await this.loadPage(0, loadLimit);
            const total = this.stockImagesMeta.page.total;

            const pages = [];
            for (let page = 1; page * loadLimit < total; page++) {
                pages.push(this.loadPage(page * loadLimit, loadLimit));
            }

            return Promise.all(pages);
        },
        loadPage(offset, limit) {
            return this.loadStockImages({
                filter: {},
                options: {
                    'page[offset]': offset,
                    'page[limit]': limit,
                },
            });
        },
    },
    created() {
        this.fetchStockImages();
    },
    watch: {
        query(newQuery, oldQuery) {
            if (newQuery !== oldQuery && this.page !== 1) {
                this.page = 1;
            }
        },
        filters(newFilters, oldFilters) {
            if (!_.isEqual(newFilters, oldFilters) && this.page !== 1) {
                this.page = 1;
            }
        },
    },
};
</script>
<style lang="scss">
.stock-images-page {
    height: 100%;

    .image-upload-indicator {
        top: 40%;
        position: relative;
    }
}
</style>
