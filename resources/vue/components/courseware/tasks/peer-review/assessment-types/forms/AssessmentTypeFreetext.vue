<template>
    <article>
        <form class="default studipform">
            <div class="formpart">
                <LabelRequired
                    id="assessment-type-freetext"
                    :label="$gettext('Bewertung')"
                    />
                <textarea
                    id="assessment-type-freetext"
                    required
                    aria-required="true"
                    :disabled="disabled"
                    v-model="answer"
                    @change="changeAnswer" />
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
            answer: this.review.attributes.assessment?.answer ?? "",
        };
    },
    methods: {
        changeAnswer() {
            const answer = this.answer ?? '';
            this.$emit('answer', { answer });
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
