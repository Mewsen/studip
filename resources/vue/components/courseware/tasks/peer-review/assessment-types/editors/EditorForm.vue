<template>
    <CoursewareTabs>
        <CoursewareTab :name="$gettext('Editor')" :index="0" selected class="cw-peer-review-editor-form--editor">
            <form class="default studipform">
                <StudipArticle v-for="(criterium, index) in localCriteria" :key="index" collapsable>
                    <template v-slot:title="{ isOpen }">
                        <template v-if="isOpen">
                            {{
                                $gettextInterpolate($gettext('Kriterium %{ index }: "%{ text }"'), {
                                    index: index + 1,
                                    text: criterium.text,
                                })
                            }}
                        </template>
                        <template v-else>
                            {{ $gettextInterpolate($gettext('Kriterium %{ index }'), { index: index + 1 }) }}
                        </template>
                    </template>
                    <template #titleplus>
                        <StudipActionMenu :items="actionItems(index)" :collapseAt="2" @trash="removeLine" />
                    </template>
                    <template #body>
                        <div class="formpart criterium-text">
                            <LabelRequired :id="`editor-form-text-${index}`" :label="$gettext('Kriterium')" />
                            <input
                                :id="`editor-form-text-${index}`"
                                type="text"
                                v-model="criterium.text"
                                required
                                aria-required="true"
                            />
                        </div>
                        <div class="formpart criterium-description">
                            <LabelRequired :id="`editor-form-description-${index}`" :label="$gettext('Beschreibung')" />
                            <textarea
                                :id="`editor-form-description-${index}`"
                                v-model="criterium.description"
                                required
                                aria-required="true"
                            ></textarea>
                        </div>
                    </template>
                </StudipArticle>
                <div class="formpart">
                    <button class="button add" type="button" @click="addLine">
                        <span>{{ $gettext('Kriterium hinzufügen') }}</span>
                    </button>
                </div>
            </form>
        </CoursewareTab>
        <CoursewareTab :name="$gettext('Vorschau')" :index="1" class="cw-peer-review-editor-form--preview">
            <article>
                <section v-for="(criterium, index) in nonEmptyCriteria" :key="index">
                    <strong>{{ criterium.text }}</strong>
                    <p>{{ criterium.description }}</p>
                    <textarea disabled />
                </section>
            </article>
        </CoursewareTab>
    </CoursewareTabs>
</template>
<script lang="ts">
import Vue, { PropType } from 'vue';
import StudipActionMenu from '../../../../../StudipActionMenu.vue';
import StudipArticle from '../../../../../StudipArticle.vue';
import LabelRequired from '../../../../../forms/LabelRequired.vue';
import CoursewareTab from '../../../../layouts/CoursewareTab.vue';
import CoursewareTabs from '../../../../layouts/CoursewareTabs.vue';
import { EditorFormCriterium, FormAssessmentPayload } from '../../process-configuration';

export default Vue.extend({
    components: { CoursewareTab, CoursewareTabs, LabelRequired, StudipActionMenu, StudipArticle },
    props: {
        payload: {
            type: Object as PropType<FormAssessmentPayload>,
        },
    },
    model: {
        prop: 'payload',
        event: 'save',
    },
    data: () => ({ localCriteria: [] as EditorFormCriterium[] }),
    computed: {
        criteria() {
            return this.payload.criteria;
        },
        nonEmptyCriteria() {
            return this.localCriteria.filter(({ text }) => text.trim().length);
        },
    },
    methods: {
        actionItems(index: number) {
            return this.localCriteria.length > 1
                ? [
                      {
                          id: 1,
                          label: this.$gettext('Kriterium entfernen'),
                          icon: 'trash',
                          emit: 'trash',
                          emitArguments: [index],
                      },
                  ]
                : [];
        },
        addLine() {
            this.localCriteria.push({ text: '', description: '' });
        },
        removeLine(lineNumber: number) {
            this.localCriteria = this.localCriteria.filter((item, index) => index !== lineNumber);
        },
        resetLocalState() {
            this.localCriteria = this.criteria.map(({ text, description }) => ({ text, description }));
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
                this.$emit('save', { criteria: this.nonEmptyCriteria.map((c) => ({ ...c })) });
            },
            deep: true,
        },
    },
});
</script>

<style scoped>
.cw-peer-review-editor-form--editor form input {
    max-width: 48em;
}

textarea {
    min-height: 5em;
    max-width: 48em;
    width: 100%;
}

.cw-peer-review-editor-form--preview > article {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.cw-peer-review-editor-form--preview > article > * + * {
    border-top: 1px solid var(--light-gray-color-40);
    padding-block-start: 1rem;
}
</style>
