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
                    <div>
                        <p class="condition-text"
                           v-html="filter.attributes.text"
                        ></p>
                        <a @click.prevent="editFilter(index)"
                           :title="$gettext('Diese Bedingung bearbeiten')"
                        >
                            <studip-icon shape="edit"></studip-icon>
                        </a>
                        <a @click.prevent="deleteFilter(index)"
                           :title="$gettext('Diese Bedingung löschen')"
                        >
                            <studip-icon shape="trash"></studip-icon>
                        </a>
                    </div>
                </div>
            </div>
            <p v-if="conditions.length === 0">
                {{ $gettext('Sie haben noch keine Auswahl festgelegt.') }}
            </p>
        </section>
        <section>
            <button class="button add"
                    @click.prevent="editFilter(null)">
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
                            :filter="currentFilter"
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
            currentFilter: null,
            currentFilterIndex: null,
            selectedFilters: []
        }
    },
    computed: {
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
        editFilter(index) {
            this.showEditFilter = true;
            this.currentFilterIndex = index;
            this.currentFilter = index === null ? [] : this.conditions[index].attributes.fields;
        },
        deleteFilter(index) {
            this.conditions.splice(index, 1);
        },
        closeDialog() {
            this.showEditFilter = false;
            this.currentFilterIndex = null;
            this.currentFilter = null;
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
                if (this.currentFilterIndex !== null) {
                    this.conditions[this.currentFilterIndex] = response.data;
                } else {
                    this.conditions.push(response.data);
                }
                this.showEditFilter = false;
                this.currentFilterIndex = null;
                this.currentFilter = null;
            });
        },
        setRuleData(data) {
            this.messageText = data.attributes.payload['message'];
            this.conditions = data.attributes.payload['conditions'];
            this.favorSemester = data.attributes.payload['favor-semester'] === '1';
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

<style lang="scss" scoped>
.condition-text {
    display: inline-block;
}
</style>
