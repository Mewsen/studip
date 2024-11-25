<template>
    <div>
        <label class="col-3">
            {{ $gettext('Feld für Serienmail einfügen') }}
            <select v-model="selectedMarker">
                <option value="">
                    -- {{ $gettext('Feld zum Einfügen auswählen') }} --
                </option>
                <option v-for="(marker, index) in markers"
                        :key="index"
                        :value="marker.marker"
                        :data-description="marker.description">
                    {{ marker.name }}
                </option>
            </select>
        </label>
        <button class="button col-3 insert-marker-button"
                :title="$gettext('Feld einfügen')"
                :disabled="selectedMarker === ''"
                @click.prevent="insertMarker">
            {{ $gettext('In den Text einfügen') }}
        </button>
        <p v-if="selectedMarker !== ''">
            {{ description }}
        </p>
    </div>
</template>

<script>
export default {
    name: 'SerialTextMarkers',
    props: {
        markers: {
            type: Array,
            required: true
        },
        editor: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            editorInstance: null,
            selectedMarker: ''
        }
    },
    computed: {
        description() {
            return this.markers.find((m) => { return m.marker === this.selectedMarker; }).description;
        }
    },
    methods: {
        insertMarker() {
            this.editorInstance.model.change(writer => {
                writer.insertText(
                    ' {{' + this.selectedMarker + '}}',
                    this.editorInstance.model.document.selection.getFirstPosition()
                );
            });
        }
    },
    mounted() {
        STUDIP.eventBus.on('editor-loaded', editor => {
            if (document.getElementById(this.editor) === editor.sourceElement) {
                this.editorInstance = editor;
            }
        });
    },
    destroyed() {
        STUDIP.eventBus.off('editor-loaded');
    }
}
</script>

<style scoped>
button {
    vertical-align: bottom;
}
</style>
