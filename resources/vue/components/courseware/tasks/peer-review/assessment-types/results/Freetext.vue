<template>
    <article>
        <section class="criterium">
            <header>{{ $gettext('Bewertung') }}</header>

            <p class="criterium-text">{{ answer }}</p>
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
            answer: this.review.attributes.assessment?.answer ?? '',
        };
    },
};
</script>

<style scoped>
.criterium header {
    font-weight: bold;
    margin-block: 1em;
}

.criterium-text {
}
</style>
