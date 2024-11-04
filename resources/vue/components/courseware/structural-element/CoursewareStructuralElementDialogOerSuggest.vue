<template>
    <studip-dialog
        height="600"
        width="600"
        :title="$gettext('Seite für OER Campus vorschlagen')"
        :confirmText="$gettext('Vorschlagen')"
        confirmClass="accept"
        :closeText="$gettext('Abbrechen')"
        closeClass="cancel"
        @close="updateShowSuggestOerDialog(false)"
        @confirm="sendOerSuggestion"
    >
        <template v-slot:dialogContent>
            <p>
                {{
                    $gettextInterpolate(
                        $gettext(
                            'Der folgende Lerninhalt wird %{ ownerName } zur Veröffentlichung im OER Campus vorgeschlagen:'
                        ),
                        { ownerName: ownerName }
                    )
                }}
            </p>
            <table class="cw-structural-element-info">
                <tr>
                    <td>{{ $gettext('Titel') }}:</td>
                    <td>{{ structuralElement.attributes.title }}</td>
                </tr>
                <tr>
                    <td>{{ $gettext('Beschreibung') }}:</td>
                    <td>{{ structuralElement.attributes.payload.description }}</td>
                </tr>
            </table>
            <form class="default" @submit.prevent="">
                <label>
                    {{
                        $gettext(
                            'Ihr Vorschlag wird anonym versendet. Falls gewünscht, können Sie zusätzlich eine Nachricht verfassen'
                        )
                    }}
                    <textarea v-model="additionalText" class="cw-structural-element-description"></textarea>
                </label>
            </form>
        </template>
    </studip-dialog>
</template>
<script>
import CoursewareOerMessage from '@/vue/mixins/courseware/oermessage.js';
import { mapActions } from 'vuex';
export default {
    name: 'courseware-structural-element-dialog-oer-suggest',
    mixins: [CoursewareOerMessage],
    props: {
        structuralElement: Object,
        ownerName: String
    },
    data() {
        return {
            additionalText: '',
        }
    },
    methods: {
        ...mapActions({
            updateShowSuggestOerDialog: 'updateShowSuggestOerDialog',
        }),
        sendOerSuggestion() {
            this.suggestViaAction(this.structuralElement, this.additionalText);
            this.updateShowSuggestOerDialog(false);
        },
    },
};
</script>
