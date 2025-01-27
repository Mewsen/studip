<template>
    <article>
        <section v-for="(criterium, index) in criteria" :key="index" class="criterium">
            <header>{{ criterium.text }}</header>

            <p class="criterium-description">{{ criterium.description }}</p>

            <p class="criterium-text">{{ answers[index] }}</p>
        </section>
    </article>
</template>

<script>
export default {
    props: {
        process: { type: Object, required: true },
        review: { type: Object, required: true },
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
};
</script>

<style scoped>
article {
    padding-inline: 1rem;
}

.criterium + .criterium {
    margin-block-start: 2rem;
}

.criterium header {
    font-weight: bold;
    margin-block: 1em;
}

.criterium-description {
    font-style: italic;
}

.criterium-text {
    line-height: 1.65;
}
</style>
