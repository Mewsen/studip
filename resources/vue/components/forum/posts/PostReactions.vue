<script setup>
import {computed, ref, useTemplateRef} from "vue";
import {REACTION_ICONS} from "./reactions";
import {$gettext} from "@/assets/javascripts/lib/gettext";
import {numberFormatter} from "../../../../assets/javascripts/lib/number_formatter";
import useDetectOutsideClick from "../../../composables/useDetectOutsideClick";
import {useForumPost} from "../../../store/pinia/forum/ForumPost";
import {deserializeJSONAPIResponse} from "../../../../assets/javascripts/lib/jsonapiUtils";
import StudipIcon from "../../StudipIcon.vue";

const forumDiscussionPost = useForumPost();
const props = defineProps({
    posting_id: {
        type: String,
        required: true,
    },
    reactions: {
        type: Array,
        required: true
    }
});

const showReactions = ref(false);
const reactionStatusMessage = ref(null);

const groupedReactions = computed(() => Object.groupBy(props.reactions, ({ emoji }) => emoji));

const announceToScreenReader = message => reactionStatusMessage.value.textContent = message;

const getPostReactionJSONAPIObject = (emoji) => ({
    data: {
        type: 'forum-posting-reactions',
        attributes: {
            emoji: emoji
        },
        meta: {
            'emoji-icon': REACTION_ICONS[emoji].icon
        },
        relationships: {
            posting: {
                data: {
                    type: 'forum-postings',
                    id: props.posting_id
                }
            }
        }
    }
})

const storeReaction = async (emoji) => {
    try {
        const response = await STUDIP.jsonapi.withPromises().POST(
            'forum-posting-reactions?include=user&fields[users]=id',
            { data: getPostReactionJSONAPIObject(emoji) }
        );

        const reaction = await deserializeJSONAPIResponse(response);
        forumDiscussionPost.addPostReaction(reaction, props.posting_id);
        showReactions.value = false;
    } catch (error) {
        STUDIP.Report.error(error.statusText);
    }
}

const deleteReaction = async (reactionId) => {
    try {
        await STUDIP.jsonapi.withPromises().DELETE(`forum-posting-reactions/${reactionId}`);
        forumDiscussionPost.removePostReaction(reactionId, props.posting_id);
    } catch (error) {
        STUDIP.Report.error(error.statusText);
    }
}

const toggleReaction = async (emoji, reactions = props.reactions) => {
    const userReaction = findUserReaction(emoji, reactions);

    if (userReaction) {
        await deleteReaction(userReaction.id);
        announceToScreenReader($gettext('Reaktion wurde entfernt.'));
    } else {
        await storeReaction(emoji);
        announceToScreenReader($gettext('Reaktion wurde hinzugefügt.'));
    }
}

const findUserReaction = (emoji, reactions = props.reactions) => reactions.find(reaction => reaction.user.id === STUDIP.USER_ID && reaction.emoji === emoji);

const reactionCreate = useTemplateRef('reactionCreate');
useDetectOutsideClick(reactionCreate, () => showReactions.value = false);
</script>

<template>
    <div class="post-reactions-container">
        <div aria-live="polite" class="sr-only" role="status" ref="reactionStatusMessage"></div>

        <template v-if="reactions.length">
            <template v-for="(reaction, emoji) in groupedReactions" :key="emoji">
                <button
                    type="button"
                    class="post-reaction"
                    :class="{
                        '--active': findUserReaction(emoji, reaction)
                    }"
                    :title="findUserReaction(emoji, reaction)  ? $gettext('Reaktion zurücknehmen') : $gettext('Reaktion hinzufügen')"
                    :aria-label="findUserReaction(emoji, reaction) ? $gettext('Reaktion zurücknehmen') : $gettext('Reaktion hinzufügen')"
                    @click="toggleReaction(emoji, reaction)">
                    <span class="html-emoji" v-html="REACTION_ICONS[emoji].icon"></span>
                    <span>{{ numberFormatter(reaction.length, 1) }}</span>
                </button>
            </template>
        </template>
        <div ref="reactionCreate" class="post-reactions">
            <button
                class="post-reactions__create-button"
                type="button"
                :title="$gettext('Reagieren')"
                :aria-label="$gettext('Reagieren')"
                @click="showReactions = !showReactions">
                <StudipIcon shape="add-reaction" class="add-reaction-icon" :size="18" />
                <p>{{ numberFormatter(reactions.length, 1) }}</p>
            </button>
            <Transition name="fade">
                <div v-if="showReactions" class="post-reactions__container">
                    <template v-for="(emoji, index) in REACTION_ICONS" :key="index">
                        <button
                            type="button"
                            :class="{
                                '--active': findUserReaction(emoji.value)
                            }"
                            :title="$gettext('Auf diesen Beitrag reagieren')"
                            :aria-label="$gettext('Auf diesen Beitrag mit %{emojiName} reagieren', { emojiName: emoji.value })"
                            @click="toggleReaction(emoji.value)"
                        >
                            <span class="html-emoji" v-html="emoji.icon"></span>
                        </button>
                    </template>
                </div>
            </Transition>
        </div>
    </div>
</template>
