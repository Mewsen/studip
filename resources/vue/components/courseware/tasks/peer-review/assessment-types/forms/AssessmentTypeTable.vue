<template>
    <article>
        <form class="default studipform">
            <div class="formpart" v-for="(criterium, index) in criteria" :key="index">
                <fieldset>
                    <legend>
                        {{ criterium.text }}
                    </legend>
                    <section>
                        <div>
                            <label v-for="(text, rating) in ratingLevels" :key="text"
                                ><input
                                    :disabled="disabled"
                                    v-model="answers[index].rating"
                                    :name="`rating-${index}`"
                                    type="radio"
                                    :value="rating + 1"
                                    @change="changeAnswers"
                                />{{ text }}</label
                            >
                        </div>
                        <label :for="`assessment-type-table-${index}`">
                            {{ $gettext('Begründung') }}
                        </label>
                        <textarea
                            :id="`assessment-type-table-${index}`"
                            :disabled="disabled"
                            v-model="answers[index].text"
                            @change="changeAnswers"
                        />
                    </section>
                </fieldset>
            </div>
        </form>
    </article>
</template>
<script>
import { $gettext } from '../../../../../../../assets/javascripts/lib/gettext';

const emptyAssessment = (criteria) => {
    return {
        answers: criteria.map((_) => ({ text: '', rating: 0 })),
    };
};

export default {
    props: {
        disabled: {
            type: Boolean,
            default: false,
        },
        process: {
            type: Object,
            required: true,
        },
        review: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            answers: [],
        };
    },
    computed: {
        criteria() {
            const payload = this.process.attributes.configuration.payload;
            return payload.criteria ?? [];
        },
        ratingLevels() {
            return [$gettext('gut'), $gettext('ok'), $gettext('schwach')];
        },
    },
    methods: {
        changeAnswers() {
            this.$emit('answer', { answers: this.answers });
        },
    },
    beforeMount() {
        if (this.review.attributes.assessment && 'answers' in this.review.attributes.assessment) {
            this.answers = this.review.attributes.assessment.answers;
        } else {
            this.answers = emptyAssessment(this.criteria).answers;
        }
    },
};
</script>

<style scoped>
textarea {
    min-height: 5em;
    max-width: 48em;
    width: 100%;
}

.formpart + .formpart {
    margin-block-start: 2rem;
}

form.default .formpart section {
    padding-block-start: 0;
}

.formpart section div {
    display: flex;
    gap: 1em;
}

.formpart section label {
    display: inline-block;
    white-space: nowrap;
}
</style>
