<script setup>
import {getCategoryDeleteURL, getCategoryEditURL, getCategoryURL} from "../helpers/urls";
import StudipIcon from "@/vue/components/StudipIcon.vue";
import StudipDateTime from "@/vue/components/StudipDateTime.vue";
import StudipActionMenu from "@/vue/components/StudipActionMenu.vue";
import {useForumConfig} from "../../../store/pinia/forum/ForumConfig";
import {$gettext} from "@/assets/javascripts/lib/gettext";
import {computed} from "vue";

const forumConfig = useForumConfig();

const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
    renderType: {
        type: String,
        default: 'card'
    }
});

const categoryActionMenus = computed(() => {
    if (forumConfig.isModerator) {
        return [
            { label: $gettext('Kategorie bearbeiten'),  icon: 'edit', emit: 'edit'},
            { label: $gettext('Kategorie löschen'),  icon: 'trash', emit: 'delete'}
        ];
    }

    return [];
});

const editCategory = () => STUDIP.Dialog.fromURL(getCategoryEditURL(props.category.id), { width: '700' });

const deleteCategory = () => STUDIP.Dialog.confirm(
    $gettext('Wollen Sie diese  "%{name}" Kategorie löschen?', {name: props.category.name}),
    () => window.location = getCategoryDeleteURL(props.category.id),
    STUDIP.Dialog.close()
);
</script>

<template>
    <tr v-if="renderType === 'tr'">
        <td>
            <div class="topic-overview">
                <div class="flag" v-if="category.color" :style="{ backgroundColor: category.color}"></div>
                <div class="content">
                    <div class="title-with-actions">
                        <div class="title-with-actions__content">
                            <a
                                class="title-with-actions__link"
                                :href="getCategoryURL(category.id)"
                                :title="$gettext('Zur Kategorie')">
                                <h3 class="line-clamp-2">{{ category.name }}</h3>
                                <span
                                    v-if="category.meta.postings_count > category.meta.user_read_index"
                                    class="unread-items-badge"
                                    role="status"
                                    aria-live="polite"
                                    :aria-label="$gettext('Sie haben %{count} ungelesene Beiträge', {count: category.meta.postings_count - category.meta.user_read_index})"
                                    :title="$gettext('Sie haben %{count} ungelesene Beiträge', {count: category.meta.postings_count - category.meta.user_read_index})"
                                >
                                    {{ category.meta.postings_count - category.meta.user_read_index }}
                                </span>
                            </a>
                        </div>

                        <div class="title-with-actions__actions-xs">
                            <StudipActionMenu
                                :items="categoryActionMenus"
                                @edit="editCategory"
                                @delete="deleteCategory"
                            />
                        </div>
                    </div>
                    <p>
                        <small class="line-clamp-3">{{ category.description }}</small>
                    </p>
                </div>
            </div>

            <!--mobile display: start-->
            <div class="details-xs">
                <dl>
                    <dt>{{ $gettext('Anzahl der Teilnehmenden in der Kategorie') }}</dt>
                    <dd class="inline-flex gap-5 items-center">
                        <StudipIcon shape="community2" role="info" :size="15" />
                        {{ category.meta.users_count }}
                    </dd>
                </dl>

                <dl>
                    <dt>{{ $gettext('Anzahl der Diskussionen') }}</dt>
                    <dd class="inline-flex gap-5 items-center">
                        <StudipIcon shape="forum" role="info" :size="15" />
                        {{ category.meta.discussions_count }}
                    </dd>

                    <dt>{{ $gettext('Anzahl der Beiträge') }}</dt>
                    <dd class="inline-flex gap-5 items-center">
                        <StudipIcon shape="reply" role="info" :size="15" />
                        {{ category.meta.postings_count }}
                    </dd>

                    <dt>{{ $gettext('Aktivitäten') }}</dt>
                    <dd class="inline-flex gap-5 items-center">
                        <StudipIcon shape="activity" role="info" :size="15" />
                        <StudipDateTime v-if="category.meta.recent_activity" :iso="category.meta.recent_activity" :relative="true" />
                        <template v-else>{{ $gettext('Keine Aktivität') }}</template>
                    </dd>
                </dl>
            </div>
            <!--mobile display: end-->
        </td>
        <td class="nowrap" :title="$gettext('Anzahl der Diskussionen')" :aria-label="$gettext('Anzahl der Diskussionen')">
            {{ category.meta.discussions_count }} {{ $gettext('Diskussion') }}
        </td>
        <td>
            <span class="inline-flex gap-10 items-center" :title="$gettext('Anzahl der Teilnehmenden in der Kategorie')" :aria-label="$gettext('Anzahl der Teilnehmenden in der Kategorie')" role="group">
                <StudipIcon shape="community2" role="info" :size="20" aria-hidden="true" />
                <span>{{ category.meta.users_count }}</span>
            </span>
        </td>
        <td>
            <span class="inline-flex gap-10 items-center" :title="$gettext('Anzahl der Beiträge')" :aria-label="$gettext('Anzahl der Beiträge')" role="group">
                <StudipIcon shape="reply" role="info" :size="20" aria-hidden="true" />
                <span>{{ category.meta.postings_count }}</span>
            </span>
        </td>
        <td>
            <span class="inline-flex gap-10 items-center nowrap" :title="$gettext('Letzte Aktivität')" :aria-label="$gettext('Letzte Aktivität')" role="group">
                <StudipIcon shape="activity" role="info" :size="20" aria-hidden="true"/>
                <StudipDateTime v-if="category.meta.recent_activity" :iso="category.meta.recent_activity" :relative="true" />
                <template v-else>{{ $gettext('Keine Aktivität') }}</template>
            </span>
        </td>
        <td class="actions">
            <StudipActionMenu
                :items="categoryActionMenus"
                @edit="editCategory"
                @delete="deleteCategory"
            />
        </td>
    </tr>
    <a
        v-else
        :href="getCategoryURL(category.id)"
        :title="$gettext('Zur Kategorie')"
        class="styleless">
        <div
            class="topic-card"
            :class="{
                '--with-hover-style': category.color
            }"
            :style="{
                '--forum-topic-card-hover-border-color': category.color
            }"
        >
            <div v-if="category.color" class="topic-card__flag" :style="{ backgroundColor: category.color}"></div>
            <div class="topic-card__content">
                <div class="topic-card__body">
                    <div class="flex space-between">
                        <div class="flex items-start gap-10">
                            <h3 class="topic-card__title line-clamp-2">
                                {{ category.name }}
                            </h3>

                            <span
                                v-if="category.meta.postings_count > category.meta.user_read_index"
                                class="unread-items-badge"
                                role="status"
                                aria-live="polite"
                                :aria-label="$gettext('Sie haben %{count} ungelesene Beiträge', {count: category.meta.postings_count - category.meta.user_read_index})"
                                :title="$gettext('Sie haben %{count} ungelesene Beiträge', {count: category.meta.postings_count - category.meta.user_read_index})"
                            >
                                {{ category.meta.postings_count - category.meta.user_read_index }}
                            </span>
                        </div>
                        <div class="actions">
                            <StudipActionMenu
                                :items="categoryActionMenus"
                                @edit="editCategory"
                                @delete="deleteCategory"
                            />
                        </div>
                    </div>
                    <p>
                        <small class="line-clamp-3">{{ category.description }}</small>
                    </p>
                </div>
                <div class="topic-card__footer">
                    <span class="inline-flex gap-10 items-center" :title="$gettext('Anzahl der Teilnehmenden in der Kategorie')" :aria-label="$gettext('Anzahl der Teilnehmenden in der Kategorie')" role="group">
                        <StudipIcon shape="community2" role="info" :size="15" aria-hidden="true"/>
                        <small>{{ category.meta.users_count }}</small>
                    </span>
                    <span class="inline-flex gap-10 items-center" :title="$gettext('Anzahl der Beiträge')" :aria-label="$gettext('Anzahl der Beiträge')" role="group">
                        <StudipIcon shape="reply" role="info" :size="15" aria-hidden="true"/>
                        <small>{{ category.meta.postings_count }}</small>
                    </span>
                    <span class="inline-flex gap-10 items-center" :title="$gettext('Letzte Aktivität')" :aria-label="$gettext('Letzte Aktivität')" role="group">
                        <StudipIcon shape="activity" role="info" :size="15" aria-hidden="true"/>
                        <small v-if="category.meta.recent_activity">
                            <StudipDateTime :iso="category.meta.recent_activity" :relative="true" />
                        </small>
                        <small v-else>{{ $gettext('Keine Aktivität') }}</small>
                    </span>
                </div>
            </div>
        </div>
    </a>
</template>
