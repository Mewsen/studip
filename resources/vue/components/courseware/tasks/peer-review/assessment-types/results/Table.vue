<template>
    <article>
        <section v-for="(criterium, index) in criteria" :key="index" class="criterium">
            <header>{{ criterium.text }}</header>

            <div class="criterium-rating">
                <div>{{ $gettext('Bewertung') }}</div>
                <p>{{ ratingLevels[answers[index].rating - 1] }}</p>
            </div>

            <p class="criterium-text">{{ answers[index].text }}</p>
        </section>
    </article>
</template>

<script>
const emptyAssessment = (criteria) => ({
    answers: criteria.map((_) => ({ text: '', rating: 0 })),
});

export default {
    props: {
        process: { type: Object, required: true },
        review: { type: Object, required: true },
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
            return [this.$gettext('gut'), this.$gettext('ok'), this.$gettext('schwach')];
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
.criterium + .criterium {
    margin-block-start: 1rem;
}

.criterium header {
    font-weight: bold;
    margin-block: 1em;
}

.criterium-rating > div {
    font-weight: bold;
}

.criterium-text {
}
</style>
