<template>
    <article>
        <form class="default studipform">
            <div class="formpart" v-for="(criterium, index) in criteria" :key="index">
                <LabelRequired
                    :id="`assessment-type-form-${index}`"
                    :label="criterium.text"
                    />
                <p>{{ criterium.description }}</p>
                <textarea
                    :id="`assessment-type-form-${index}`"
                    required
                    aria-required="true"
                    :disabled="disabled"
                    v-model="answers[index]"
                    @change="changeAnswers" />
            </div>
        </form>
    </article>
</template>
<script>
import LabelRequired from '../../../../../forms/LabelRequired.vue';

export default {
    components: { LabelRequired },
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
            answers: this.review.attributes.assessment?.answers ?? [],
        };
    },
    computed: {
        criteria() {
            const payload = this.process.attributes.configuration.payload;
            return payload.criteria ?? [];
        },
    },
    methods: {
        changeAnswers() {
            const answers = this.criteria.map((_, index) => this.answers[index] ?? '');
            this.$emit('answer', { answers });
        },
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
    margin-block-start: 1rem;
}
</style>
