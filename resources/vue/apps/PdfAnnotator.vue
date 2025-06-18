<template>
    <div class="pdf-annotator">
        <pdf-js-viewer ref="pdfJsViewer"
                       :file_id="fileRef.id"
                       :user-fullname="userFullname"/>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import PdfJsViewer from '../components/PdfJsViewer.vue';
import { mapActions, mapGetters } from 'vuex';
import { httpClient } from "../../assets/javascripts/chunks/vue";

export default defineComponent({
    name: 'PdfAnnotator',
    components: { PdfJsViewer },
    props: {
        fileRef: {
            type: Object,
            required: true
        },
        userFullname: {
            type: String,
            required: true
        }
    },
    created() {
        // This event is fired by the 'save' button in the annotate_pdf view.
        window.STUDIP.eventBus.on('files:save-annotated-pdf', this.savePdf);
    },
    beforeUnmount() {
        window.STUDIP.eventBus.off('files:save-annotated-pdf');
    },
    computed: {
        ...mapGetters({
            fileRefById: 'file-refs/byId',
            folderById: 'folders/byId',
        }),
    },
    methods: {
        ...mapActions({
            loadFolder: 'folders/loadById',
        }),
        /**
         * Make a request to Stud.IP to save the annotated file.
         */
        async savePdf() {
            const blob = await (this.$refs.pdfJsViewer as any).savePdf();
            const formData = new FormData();
            const filename = this.$gettext('%{originalFilename} (korrigiert)', {
                originalFilename: this.fileRef.name,
            });
            formData.append('file', blob, filename);
            const url = `file-refs/${this.fileRef.id}/annotations`;
            const res = await httpClient.post(url, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });
            window.location.reload();
            return res;
        },
    },
});
</script>

<style>
.pdf-annotator {
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow-y: clip;
}
.annotate-pdf-root {
    flex-grow: 1;
}
</style>
