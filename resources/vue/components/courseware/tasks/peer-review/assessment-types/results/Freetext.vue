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
article {
    padding-inline: 1em;
}

.criterium {
    margin-block-end: 2em;
}

.criterium header {
    font-weight: bold;
    margin-block: 1em;
}

.criterium-text {
    line-height: 1.65;
}
</style>
