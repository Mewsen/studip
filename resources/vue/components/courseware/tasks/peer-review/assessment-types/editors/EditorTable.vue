<template>
    <CoursewareTabs>
        <CoursewareTab :name="$gettext('Editor')" :index="0" selected class="cw-peer-review-editor-table-editor">
            <form class="studip studipform">
                <div class="formpart" v-for="(criterium, index) in localCriteria" :key="index">
                    <LabelRequired :id="`editor-table-text-${index}`" :label="$gettext('Kriterium')" class="sr-only" />
                    <input
                        :id="`editor-table-text-${index}`"
                        type="text"
                        v-model="criterium.text"
                        required
                        aria-required="true"
                    />
                    <StudipIcon
                        :disabled="criteria.length === 1"
                        name="delete"
                        shape="trash"
                        :size="20"
                        :title="$gettext('Kriterium entfernen')"
                        @click.prevent="removeLine(index)"
                    />
                </div>
                <div class="formpart">
                    <button class="button add" type="button" @click="addLine">
                        <span>{{ $gettext('Kriterium hinzufügen') }}</span>
                    </button>
                </div>
            </form>
        </CoursewareTab>
        <CoursewareTab :name="$gettext('Vorschau')" :index="1" class="cw-peer-review-editor-table--preview">
            <table class="default">
                <thead>
                    <tr>
                        <th>{{ $gettext('Kriterien') }}</th>
                        <th>{{ $gettext('Bewertung') }}</th>
                        <th>{{ $gettext('Kommentar') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(criterium, index) in nonEmptyCriteria" :key="index">
                        <td>{{ criterium.text }}</td>
                        <td>
                            <label v-for="text in [$gettext('gut'), $gettext('ok'), $gettext('schwach')]" :key="text">
                                <input name="rating" type="radio" disabled />
                                {{ text }}
                            </label>
                        </td>
                        <td>
                            <textarea disabled />
                        </td>
                    </tr>
                </tbody>
            </table>
        </CoursewareTab>
    </CoursewareTabs>
</template>
<script>
import LabelRequired from '../../../../../forms/LabelRequired.vue';
import CoursewareTab from '../../../../layouts/CoursewareTab.vue';
import CoursewareTabs from '../../../../layouts/CoursewareTabs.vue';
import { EditorTableCriterium, TableAssessmentPayload } from '../../process-configuration';

export default {
    components: { CoursewareTab, CoursewareTabs, LabelRequired },
    props: {
        payload: {
            type: Object,
        },
    },
    emits: ['update:payload'],
    data: () => ({ localCriteria: [] }),
    computed: {
        criteria() {
            return this.payload.criteria;
        },
        nonEmptyCriteria() {
            return this.localCriteria.filter(({ text }) => text.trim().length);
        },
    },
    methods: {
        addLine() {
            this.localCriteria.push({ text: '' });
        },
        removeLine(lineNumber) {
            this.localCriteria = this.localCriteria.filter((item, index) => index !== lineNumber);
        },
        resetLocalState() {
            this.localCriteria = this.criteria.map(({ text }) => ({ text }));
        },
    },
    mounted() {
        this.resetLocalState();
    },
    watch: {
        payload() {
            this.resetLocalState();
        },
        localCriteria: {
            handler() {
                this.$emit('update:payload', { criteria: this.nonEmptyCriteria.map((c) => ({ ...c })) });
            },
            deep: true,
        },
    },
};
</script>

<style scoped>
form button.trash {
    min-width: 2em;
    width: 2em;
}
form input {
    flex-grow: 1;
    height: 1.7em;
    max-width: 48em;
}

form .formpart {
    display: flex;
    align-items: center;
    gap: 1em;
}

.formpart {
    margin-block-start: 0.75em;
}

.cw-peer-review-editor-table--preview label {
    display: block;
    white-space: nowrap;
}

.cw-peer-review-editor-table-editor input[name="delete"] {
    flex-grow: 0;
    padding-inline: 0.7em;
}
</style>
