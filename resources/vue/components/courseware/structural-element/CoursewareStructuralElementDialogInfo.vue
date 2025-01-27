<template>
    <studip-dialog
        :title="$gettext('Informationen zur Seite')"
        :closeText="$gettext('Schließen')"
        @close="showElementInfoDialog(false)"
    >
        <template v-slot:dialogContent>
            <table class="cw-structural-element-info">
                <tbody>
                    <tr>
                        <td>{{ $gettext('Titel') }}:</td>
                        <td>{{ structuralElement.attributes.title }}</td>
                    </tr>
                    <tr>
                        <td>{{ $gettext('Beschreibung') }}:</td>
                        <td>{{ structuralElement.attributes.payload.description }}</td>
                    </tr>
                    <tr>
                        <td>{{ $gettext('Seite wurde erstellt von') }}:</td>
                        <td>{{ ownerName }}</td>
                    </tr>
                    <tr>
                        <td>{{ $gettext('Seite wurde erstellt am') }}:</td>
                        <td><iso-date :date="structuralElement.attributes.mkdate" /></td>
                    </tr>
                    <tr>
                        <td>{{ $gettext('Zuletzt bearbeitet von') }}:</td>
                        <td>{{ editorName }}</td>
                    </tr>
                    <tr>
                        <td>{{ $gettext('Zuletzt bearbeitet am') }}:</td>
                        <td><iso-date :date="structuralElement.attributes.chdate" /></td>
                    </tr>
                </tbody>
            </table>
        </template>
    </studip-dialog>
</template>
<script>
import IsoDate from '../layouts/IsoDate.vue';
import { mapActions, mapGetters } from 'vuex';
export default {
    name: 'courseware-structural-element-dialog-info',
    components: {
        IsoDate,
    },
    props: {
        structuralElement: Object,
        ownerName: String,
    },
    computed: {
        ...mapGetters({
            relatedUsers: 'users/related',
        }),
        editor() {
            const editor = this.relatedUsers({
                parent: this.structuralElement,
                relationship: 'editor',
            });

            return editor ?? null;
        },

        editorName() {
            return this.editor?.attributes['formatted-name'] ?? '?';
        },
    },
    methods: {
        ...mapActions({
            showElementInfoDialog: 'showElementInfoDialog',
        }),
    },
};
</script>
