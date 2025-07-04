<script setup>
import {getTopicDeleteURL, getTopicEditURL, getTopicURL} from "../helpers/urls";
import {$gettext} from "@/assets/javascripts/lib/gettext";
import {useForumConfig} from "../../../store/pinia/forum/ForumConfig";
import StudipActionMenu from "@/vue/components/StudipActionMenu.vue";
import StudipIcon from "@/vue/components/StudipIcon.vue";
import StudipDateTime from "@/vue/components/StudipDateTime.vue";
import {computed} from "vue";

const forumConfig = useForumConfig();

const props = defineProps({
    topic: {
        type: Object,
        required: true,
    },
    renderType: {
        type: String,
        default: 'card'
    }
});

const topicActionMenus = computed(() => {
    if (forumConfig.isModerator) {
        return [
            { label: $gettext('Thema bearbeiten'),  icon: 'edit', emit: 'edit'},
            { label: $gettext('Thema löschen'),  icon: 'trash', emit: 'delete'}
        ];
    }

    return [];
});

const editTopic = () => STUDIP.Dialog.fromURL(getTopicEditURL(props.topic.id),{ width: '700' });

const deleteTopic = () => STUDIP.Dialog.confirm(
    $gettext('Wollen Sie dieses  "%{name}" Thema löschen? Dann werden auch alle Diskussionen gelöscht!', {name: props.topic.name}),
    () => window.location = getTopicDeleteURL(props.topic.id),
    STUDIP.Dialog.close()
);
</script>

<template>
    <tr v-if="renderType === 'tr'">
        <td>
            <div class="topic-overview">
                <div class="content">
                    <div class="title-with-actions">
                        <div class="title-with-actions__content">
                            <a class="title-with-actions__link" :href="getTopicURL(topic.id)" :title="$gettext('Zum Thema')">
                                <h3 class="line-clamp-2">{{ topic.name }}</h3>
                                <span
                                    v-if="topic.meta.postings_count > topic.meta.user_read_index"
                                    class="unread-items-badge"
                                    role="status"
                                    aria-live="polite"
                                    :aria-label="$gettext('Sie haben %{count} ungelesene Beiträge', {count: topic.meta.postings_count - topic.meta.user_read_index})"
                                    :title="$gettext('Sie haben %{count} ungelesene Beiträge', {count: topic.meta.postings_count - topic.meta.user_read_index})"
                                >
                                    {{ topic.meta.postings_count - topic.meta.user_read_index }}
                                </span>
                            </a>
                        </div>

                        <div class="title-with-actions__actions-xs">
                            <StudipActionMenu
                                :items="topicActionMenus"
                                @edit="editTopic"
                                @delete="deleteTopic"
                            />
                        </div>
                    </div>
                    <p>
                        <small class="line-clamp-3">{{ topic.description }}</small>
                    </p>
                </div>
            </div>

            <!--mobile display: start-->
            <div class="details-xs">
                <dl>
                    <dt>{{ $gettext('Anzahl der Teilnehmenden am Thema') }}</dt>
                    <dd class="inline-flex gap-5 items-center">
                        <StudipIcon shape="community2" role="info"  :size="15" aria-hidden="true"/>
                        {{ topic.meta.users_count }}
                    </dd>
                </dl>

                <dl>
                    <dt>{{ $gettext('Anzahl der Diskussionen') }}</dt>
                    <dd class="inline-flex gap-5 items-center">
                        <StudipIcon shape="forum" role="info"  :size="15" aria-hidden="true"/>
                        {{ topic.meta.discussions_count }}
                    </dd>

                    <dt>{{ $gettext('Anzahl der Beiträge') }}</dt>
                    <dd class="inline-flex gap-5 items-center">
                        <StudipIcon shape="reply" role="info"  :size="15" aria-hidden="true"/>
                        {{ topic.meta.postings_count }}
                    </dd>

                    <dt>{{ $gettext('Aktivitäten') }}</dt>
                    <dd class="inline-flex gap-5 items-center">
                        <StudipIcon shape="activity" role="info"  :size="15" aria-hidden="true"/>
                        <StudipDateTime v-if="topic.meta.recent_activity" :iso="topic.meta.recent_activity" :relative="true" />
                        <template v-else>{{ $gettext('Keine Aktivität') }}</template>
                    </dd>
                </dl>
            </div>
            <!--mobile display: end-->
        </td>
        <td class="nowrap" :title="$gettext('Anzahl der Diskussionen')" :aria-label="$gettext('Anzahl der Diskussionen')">
            {{ topic.meta.discussions_count }} {{ $gettext('Diskussionen') }}
        </td>
        <td>
            <span class="inline-flex gap-10 items-center" :title="$gettext('Anzahl der Teilnehmenden am Thema')" :aria-label="$gettext('Anzahl der Teilnehmenden am Thema')" role="group">
                <StudipIcon shape="community2" role="info"  :size="20" aria-hidden="true" />
                <span>{{ topic.meta.users_count }}</span>
            </span>
        </td>
        <td>
            <span class="inline-flex gap-10 items-center" :title="$gettext('Anzahl der Beiträge')" :aria-label="$gettext('Anzahl der Beiträge')" role="group">
                <StudipIcon shape="reply" role="info"  :size="20" aria-hidden="true" />
                <span>{{ topic.meta.postings_count }}</span>
            </span>
        </td>
        <td>
            <span class="inline-flex gap-10 items-center nowrap" :title="$gettext('Letzte Aktivität')" :aria-label="$gettext('Letzte Aktivität')" role="group">
                <StudipIcon shape="activity" role="info" :size="20" aria-hidden="true"/>
                <StudipDateTime v-if="topic.meta.recent_activity" :iso="topic.meta.recent_activity" :relative="true" />
                <template v-else>{{ $gettext('Keine Aktivität') }}</template>
            </span>
        </td>
        <td class="actions">
            <StudipActionMenu
                :items="topicActionMenus"
                @edit="editTopic"
                @delete="deleteTopic"
            />
        </td>
    </tr>
    <a
        v-else
        :href="getTopicURL(topic.id)"
        :title="$gettext('Zum Thema')"
        class="styleless"
    >
        <div class="topic-card">
            <div class="topic-card__content">
                <div class="topic-card__body">
                    <div class="flex space-between">
                        <div class="flex items-start gap-10">
                            <h3 class="topic-card__title line-clamp-2">
                                {{ topic.name }}
                            </h3>

                            <span
                                v-if="topic.meta.postings_count > topic.meta.user_read_index"
                                class="unread-items-badge"
                                role="status"
                                aria-live="polite"
                                :aria-label="$gettext('Sie haben %{count} ungelesene Beiträge', {count: topic.meta.postings_count - topic.meta.user_read_index})"
                                :title="$gettext('Sie haben %{count} ungelesene Beiträge', {count: topic.meta.postings_count - topic.meta.user_read_index})"
                            >
                                {{ topic.meta.postings_count - topic.meta.user_read_index }}
                            </span>
                        </div>

                        <div class="actions">
                            <StudipActionMenu
                                :items="topicActionMenus"
                                @edit="editTopic"
                                @delete="deleteTopic"
                            />
                        </div>
                    </div>
                    <p>
                        <small class="line-clamp-3">{{ topic.description }}</small>
                    </p>
                </div>
                <div class="topic-card__footer">
                    <span class="inline-flex gap-10 items-center" :title="$gettext('Anzahl der Teilnehmenden am Thema')" :aria-label="$gettext('Anzahl der Teilnehmenden am Thema')" role="group">
                        <StudipIcon shape="community2" role="info"  :size="15" aria-hidden="true" />
                        <small>{{ topic.meta.users_count }}</small>
                    </span>
                    <span class="inline-flex gap-10 items-center" :title="$gettext('Anzahl der Beiträge')" :aria-label="$gettext('Anzahl der Beiträge')" role="group">
                        <StudipIcon shape="reply" role="info"  :size="15" aria-hidden="true" />
                        <small>{{ topic.meta.postings_count }}</small>
                    </span>
                    <span class="inline-flex gap-10 items-center" :title="$gettext('Letzte Aktivität')" :aria-label="$gettext('Letzte Aktivität')" role="group">
                        <StudipIcon shape="activity" role="info"  :size="15" aria-hidden="true" />
                        <small v-if="topic.meta.recent_activity">
                            <StudipDateTime :iso="topic.meta.recent_activity" :relative="true" />
                        </small>
                        <small v-else>
                            {{ $gettext('Keine Aktivität') }}
                        </small>
                    </span>
                </div>
            </div>
        </div>
    </a>
</template>
