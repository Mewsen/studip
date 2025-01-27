<template>
    <form class="default">
        <section>
            <label>
                {{ $gettext('Nachricht bei fehlgeschlagener Anmeldung') }}
                <textarea name="message" rows="4" cols="50" v-model="messageText"></textarea>
            </label>
        </section>
        <section>
            <h3>
                {{ $gettext('Folgende Personen bei der Platzverteilung bevorzugen:') }}
            </h3>
            <div v-if="conditions.length > 0"
                 role="list">
                <div v-for="(filter, index) in conditions"
                     :key="index"
                     role="listitem">
                    <p v-if="conditions.length > 1 && index >= 1">
                        {{ $gettext('oder') }}
                    </p>
                    <p v-html="filter.attributes.text"></p>
                </div>
            </div>
            <p v-if="conditions.length === 0">
                {{ $gettext('Sie haben noch keine Auswahl festgelegt.') }}
            </p>
        </section>
        <section>
            <button class="button add"
                    @click.prevent="editFilter">
                {{ $gettext('Bedingung hinzufügen') }}
            </button>
        </section>
        <section>
            <label>
                <input type="checkbox"
                       v-model="favorSemester"
                       value="1">
                {{ $gettext('Höhere Fachsemester bevorzugen') }}
            </label>
        </section>
        <studip-user-filter v-if="showEditFilter"
                            @submit="confirmDialog"
                            @close="closeDialog"></studip-user-filter>
    </form>
</template>

<script>
import { AdmissionRuleMixin } from '../../mixins/AdmissionRuleMixin';
import StudipUserFilter from "../StudipUserFilter.vue";

export default {
    name: 'PreferentialAdmission',
    components: { StudipUserFilter },
    mixins: [AdmissionRuleMixin],
    data() {
        return {
            messageText: this.message || this.$gettext('Folgende Gruppen werden bei der Platzverteilung bevorzugt behandelt: %s'),
            conditions: [],
            favorSemester: false,
            showEditFilter: false,
            selectedFilters: []
        }
    },
    computed: {
        groupsAllowed() {
            return this.assignedRuleTypes.includes('ParticipantRestrictedAdmission')
        },
        payload() {
            return {
                type: 'PreferentialAdmission',
                payload: {
                    conditions: this.conditions,
                    'favor-semester': this.favorSemester,
                    message: this.messageText
                }
            }
        }
    },
    methods: {
        editFilter() {
            this.showEditFilter = true;
        },
        closeDialog() {
            this.showEditFilter = false;
        },
        confirmDialog(filter) {
            STUDIP.jsonapi.withPromises().post(
                'user-filters',
                {
                    data: {
                        data: {
                            attributes: {
                                filters: filter
                            }
                        }
                    }
                })
            .then(response => {
                this.conditions.push(response.data);
                this.showEditFilter = false;
            });
        },
        setRuleData(data) {
            this.conditions = data.attributes.payload['conditions'];
            this.favorSemester = data.attributes.payload['favor-semester'];
        },
        validate() {
            if (this.conditions.length === 0 && !this.favorSemester) {
                this.invalidData.push(
                    this.$gettext('Bitte geben Sie mindestens eine Auswahlbedingung an oder '
                        + 'bevorzugen Sie höhere Fachsemester.')
                );
            }

            return this.invalidData.length === 0;
        }
    }
}
</script>
