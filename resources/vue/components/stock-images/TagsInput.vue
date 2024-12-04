<template>
    <div>
        <span class="sr-only">{{
            $gettext('Um einen Tag zu erstellen, schließen Sie Ihre Eingabe mit der Eingabetaste ab.')
        }}</span>
        <VueTagsInput
            v-model="tag"
            :add-on-key="[13, ';']"
            :autocomplete-items="filteredItems"
            :maxlength="1000"
            :save-on-key="[13, ';']"
            :separators="[';']"
            :tags="formattedTags"
            @tags-changed="onTagsChanged"
            :placeholder="$gettext('Tag hinzufügen')"
        >
            <template #tag-actions="{ index, edit, performDelete }">
                <i
                    tabindex="0"
                    v-show="edit"
                    class="ti-icon-undo"
                    @keyup.enter="performcancelEdit(index)"
                    @click="performcancelEdit(index)"
                />
                <i
                    tabindex="0"
                    v-show="!edit"
                    class="ti-icon-close"
                    @keyup.enter="performDelete(index)"
                    @click="performDelete(index)"
                    :title="$gettext('Tag entfernen')"
                />
            </template>
        </VueTagsInput>
    </div>
</template>
<script>
import { VueTagsInput } from '@vojtechlanka/vue-tags-input';

const fromSimpleTags = (array) => array.map((text) => ({ text }));
const toSimpleTags = (tags) => tags.map(({ text }) => text);

export default {
    emits: ['update:tags'],
    props: {
        tags: {
            type: Array,
            default: () => [],
        },
        suggestions: {
            type: Array,
            default: () => [],
        },
    },

    components: { VueTagsInput },

    data: () => ({
        tag: '',
        formattedTags: [],
    }),

    computed: {
        filteredItems() {
            return fromSimpleTags(this.suggestions).filter(
                (i) => i.text.toLowerCase().includes(this.tag.toLowerCase())
            );
        },
    },

    mounted() {
        this.formattedTags = fromSimpleTags(this.tags);
    },

    methods: {
        onTagsChanged(newTags) {
            this.$emit('update:tags', toSimpleTags(newTags));
        },
    },

    watch: {
        tags(newTags) {
            this.formattedTags = fromSimpleTags(newTags);
        },
    },
};
</script>
