<template>
    <iframe ref="iframe" :src="iframeUrl" class="pdfjs-viewer-iframe" @load="onIframeLoad"/>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { getLocale } from '../../assets/javascripts/lib/gettext';

interface PdfJsIframe {
    contentWindow: {
        setStudipUser(formattedName: string): void;
        PDFViewerApplication: {
            pdfDocument: {
                saveDocument(): Promise<BlobPart>;
            };
        };
        PDFViewerApplicationOptions: {
            set(field: string, value: unknown): void;
            setAll(options: Record<string, unknown>): void;
        };
    };
}

export default defineComponent({
    props: {
        file_id: {
            type: String,
            required: true,
        },
        userFullname: {
            type: String,
            required: true
        }
    },
    computed: {
        iframeUrl(): string {
            return window.STUDIP.URLHelper.getURL('assets/javascripts/pdfjs/web/viewer.html', {
                file: window.STUDIP.URLHelper.getURL('sendfile.php', { file_id: this.file_id }),
            });
        },
    },
    mounted() {
        // This is an event dispatched by PDF.js from within the iframe which
        // grants us an opportunity to set configuration values before PDF.js
        // is initialized.
        // See webViewerLoad() in pdfjs/web/viewer.js.
        window.document.addEventListener('webviewerloaded', this.onWebViewerLoaded);
        // Add an event listener for dialog closing so that we can prompt the user
        // before potentially losing data.
        $('.studip-dialog').on('dialogbeforeclose', this.beforeDialogClose);
    },
    unmounted() {
        window.document.removeEventListener('webviewerloaded', this.onWebViewerLoaded);
    },
    methods: {
        async savePdf(): Promise<Blob> {
            const data = await (
                this.$refs.iframe as unknown as PdfJsIframe
            ).contentWindow.PDFViewerApplication.pdfDocument.saveDocument();
            return new Blob([data], { type: 'application/pdf' });
        },
        beforeDialogClose(evt: Event) {
            if (!window.confirm(this.$gettext('Ihre Änderungen wurden noch nicht gespeichert.'))) {
                evt.preventDefault();
                evt.stopPropagation();
                return false;
            }

            return true;
        },
        onWebViewerLoaded(evt: Event) {
            const iframe = this.$refs.iframe as unknown as PdfJsIframe;
            // Verify that the event is from our iframe, because there could be
            // multiple PdfJsViewer components on the same page.
            if ((evt as CustomEvent).detail.source === iframe.contentWindow) {
                const locale = getLocale()
                    // Stud.IP uses '_' and PDF.js uses '-' (e.g. 'de_DE', 'de-DE').
                    .replace('_', '-');
                const options = {
                    'localeProperties': {
                        lang: locale,
                    },
                    // Disable 'User Preferences' in PDF.js so they will not
                    // conflict with Stud.IP-based configuration.
                    'disablePreferences': true,
                    'viewerCssTheme': 1
                };
                iframe.contentWindow.PDFViewerApplicationOptions.setAll(options);
            }
        },
        // Get the name of the current user and set it in PDF.js's options so that
        // it can be applied to the annotations they create and edit.
        async onIframeLoad(event: Event): Promise<void> {
            (this.$refs.iframe as unknown as PdfJsIframe).contentWindow.setStudipUser(
                this.userFullname
            );
        },
    },
});
</script>

<style scoped>
.pdfjs-viewer-iframe {
    width: 100%;
    height: 100vh;
}
</style>
