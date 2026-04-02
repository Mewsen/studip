<template>
    <section class="studip-tiles" v-if="isLoaded">
        <a v-for="studygroup in studygroups"
           :key="studygroup.id"
           :href="createStudygroupURL(studygroup)"
        >
            <div>
                <img :src="studygroup.avatar"
                     class="course-avatar-medium"
                     alt=""
                >
                <div>
                    <strong>{{ studygroup.title }}</strong>
                    <div>
                        {{ $ngettext(
                            '%{count} Mitglied',
                            '%{count} Mitglieder',
                            studygroup.members,
                            { count: studygroup.members }
                        ) }}
                    </div>
                </div>
            </div>
            <div v-if="studygroup.tags.length > 0">
                <template v-for="tag in studygroup.tags">
                    #{{ tag }}
                </template>
            </div>
        </a>
    </section>
    <template v-else>
        {{ $gettext('Vorschläge werden geladen. Bitte warten.') }}
    </template>
</template>
<script setup>
import {computed, ref} from "vue";
import {jsonapi} from "@/assets/javascripts/lib/jsonapi";

const CACHE_KEY = 'studygroup-proposals';

const studygroups = ref(null);

const createStudygroupURL = (studygroup) => STUDIP.URLHelper.getURL(
    `dispatch.php/course/studygroup/details/${studygroup.id}`
);
const isLoaded = computed(() => studygroups.value !== null);

const cache = STUDIP.Cache.getInstance();

if (cache.has(CACHE_KEY)) {
    studygroups.value = cache.get(CACHE_KEY);
} else {
    jsonapi.withPromises().get('studygroup-proposals', {
        data: {include: 'tags'}
    }).then(response => {
        return response.data.map(studygroup => ({
            id: studygroup.id,
            title: studygroup.attributes.title,
            avatar: studygroup.meta.avatar.medium,
            members: studygroup.meta['members-count'],
            tags: studygroup.relationships.tags.data?.map(tag => {
                const includedTag = response.included
                    .find(item => item.type === 'tags' && item.id === tag.id);
                return includedTag.attributes.name
            }) ?? [],
        }));
    }).then(proposals => {
        studygroups.value = proposals;
        cache.set(CACHE_KEY, proposals, 15 * 60);
    });
}
</script>
