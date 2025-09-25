<script setup>
import {computed, onMounted, reactive, useTemplateRef} from "vue";
import SelectTopicInput from "@/vue/components/forum/topics/SelectTopicInput.vue";
import SelectDiscussionType from "@/vue/components/forum/discussions/SelectDiscussionType.vue";
import SelectTagsInput from "@/vue/components/forum/SelectTagsInput.vue";
import StudipIcon from "../../../components/StudipIcon.vue";
import StudipWysiwyg from "../../../components/StudipWysiwyg.vue";
import StudipSwitch from "../../../components/StudipSwitch.vue";
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import {useForumConfig} from "../../../store/pinia/forum/ForumConfig";

const forumConfig = useForumConfig();
const CSRF = STUDIP.CSRF_TOKEN;

const props = defineProps({
    discussion: {
        type: Object,
    },
    topics: {
        type: Array,
        required: true
    },
    discussion_types: {
        type: Array,
        required: true
    },
    tags: {
        type: Array,
        required: true
    }
});

const discussionForm = reactive({
    ...props.discussion,
    closed_at: Boolean(props.discussion.closed_at),
    sticky: Boolean(props.discussion.sticky),
    topic: props.topics.find(({ topic_id }) => topic_id === props.discussion.topic_id),
    type: props.discussion_types.find(({ type_id }) => type_id === parseInt(props.discussion.type_id))
});

const formActionURL = computed(() => {
    if (props.discussion.discussion_id) {
        return STUDIP.URLHelper.getURL(`dispatch.php/course/forum/discussions/save/${props.discussion.discussion_id}`);
    }

    return STUDIP.URLHelper.getURL('dispatch.php/course/forum/discussions/save');
});

const availableTags = computed(() => {
    if (discussionForm.tags && discussionForm.tags.length > 0) {
        const selectedTagsId = discussionForm.tags.map(({ id }) => id);
        return props.tags.filter(({ id }) => selectedTagsId.indexOf(id) < 0);
    }

    return props.tags;
});

const titleInput = useTemplateRef('title-input');

onMounted(() => {
    titleInput.value.focus();
});
</script>

<template>
    <div class="forum" style="display: flex;">
        <form
            class="default use-utility-classes forum-form"
            :action="formActionURL"
            method="post"
        >
            <input type="hidden" :name="CSRF.name" :value="CSRF.value">
            <fieldset>
                <legend v-if="discussion.discussion_id" class="hide-in-dialog">
                    {{ $gettext('Diskussion bearbeiten') }}
                </legend>
                <legend v-else class="hide-in-dialog">
                    {{ $gettext('Neue Diskussion starten') }}
                </legend>

                <section>
                    <label class="studiprequired m-0">
                        <span class="textlabel">{{ $gettext('Diskussionstitel') }}</span>
                        <span :title="$gettext('Diskussionstitel ist ein Pflichtfeld')" aria-hidden="true" class="asterisk">*</span>
                        <input
                            required
                            type="text"
                            name="title"
                            ref="title-input"
                            v-model="discussionForm.title"
                            class="max-w-full" />
                    </label>

                    <div class="discussion-badges-container">
                        <div v-if="discussionForm.topic" class="badge">
                            <span :style="{ backgroundColor: discussionForm.topic.color ?? '#EDEDED', height: '12px', width: '12px'}"></span>
                            <span>{{ discussionForm.topic.name }}</span>
                            <button @click="discussionForm.topic = null" class="action">
                                <StudipIcon shape="decline" :size="15" />
                            </button>
                        </div>
                        <div v-if="discussionForm.type" class="badge">
                            <StudipIcon :shape="discussionForm.type.icon" :size="15" />
                            <span>{{ discussionForm.type.name }}</span>
                            <button @click="discussionForm.type = null" class="action">
                                <StudipIcon shape="decline" :size="15" />
                            </button>
                        </div>
                        <template v-for="tag in discussionForm.tags" :key="tag">
                            <div class="badge">
                                <span>{{ '#'+tag.name }}</span>
                                <button @click="discussionForm.tags = discussionForm.tags.filter(t => t.name !== tag.name)" class="action">
                                    <StudipIcon shape="decline" :size="15" />
                                </button>
                            </div>
                        </template>
                    </div>
                </section>

                <section class="inputs-container">
                    <div class="flex-1">
                        <label for="select-topic-input">
                            <span class="sr-only">{{ $gettext('Thema') }}</span>
                        </label>
                        <SelectTopicInput
                            id="select-topic-input"
                            :options="topics"
                            v-model="discussionForm.topic"
                            :taggable="true"
                            :required="true"
                        />
                        <input type="hidden" name="topic" :value="JSON.stringify(discussionForm.topic)">
                    </div>
                    <div class="flex-1">
                        <label for="select-discussion-type">
                            <span class="sr-only">{{ $gettext('Diskussionstyp') }}</span>
                        </label>
                        <SelectDiscussionType
                            id="select-discussion-type"
                            :options="discussion_types"
                            v-model="discussionForm.type"
                        />
                        <input v-if="discussionForm.type" type="hidden" name="type_id" :value="discussionForm.type.type_id">
                    </div>
                    <div class="flex-1">
                        <label for="select-tags-input">
                            <span class="sr-only">{{ $gettext('Schlagworte') }}</span>
                        </label>
                        <SelectTagsInput
                            id="select-tags-input"
                            :options="availableTags"
                            v-model="discussionForm.tags"
                            multiple
                            :taggable="true"
                        />
                        <input type="hidden" name="tags" :value="JSON.stringify(discussionForm.tags)">
                    </div>
                </section>

                <section class="mt-10" v-if="!discussion.discussion_id">
                    <input type="hidden" name="content" v-model="discussionForm.content">
                    <StudipWysiwyg :required="true" v-model="discussionForm.content" />
                </section>
                <section class="mt-10">
                    <StudipSwitch name="closed_at" v-model="discussionForm.closed_at" :label="$gettext('Diskussion schließen')" />
                </section>
                <section v-if="forumConfig.isModerator" class="mt-10">
                    <StudipSwitch name="sticky" v-model="discussionForm.sticky" :label="$gettext('Anpinnen')" />
                </section>
            </fieldset>
            <footer data-dialog-button>
                <button class="button accept">
                    {{ $gettext('Speichern') }}
                </button>
                <button class="button cancel" type="button" data-dialog-close>
                    {{ $gettext('Abbrechen') }}
                </button>
            </footer>
        </form>
    </div>
</template>

<style scoped>
.vs__dropdown-menu {
    z-index: 1020 !important;
}
</style>

