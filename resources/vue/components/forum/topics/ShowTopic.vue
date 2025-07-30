<script setup>
import StudipDialog from "../../StudipDialog.vue";
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import StudipDateTime from "../../StudipDateTime.vue";

defineEmits(['close']);

defineProps({
    topic: {
        type: Object,
        required: true,
    }
});

const isOpen = defineModel('isOpen');
</script>

<template>
    <StudipDialog
        v-if="isOpen"
        :title="$gettext('Informationen')"
        :closeText="$gettext('Schließen')"
        closeClass="cancel"
        height="700"
        width="600"
        @close="isOpen = false"
    >
        <template #dialogContent>
            <div class="forum">
                <dl class="use-utility-classes">
                    <dt>{{ $gettext('Title') }}</dt>
                    <dd>{{ topic.name }}</dd>

                    <dt>{{ $gettext('Beschreibung') }}</dt>
                    <dd class="break-word">
                        <p>{{ topic.description }}</p>
                    </dd>

                    <template v-if="topic.category">
                        <dt>{{ $gettext('Kategorie') }}</dt>
                        <dd>{{ topic.category.name }}</dd>
                    </template>

                    <dt>{{ $gettext('Anzahl der Diskussionen') }}</dt>
                    <dd>{{ topic.meta.discussions_count }}</dd>

                    <dt>{{ $gettext('Anzahl der Beiträge') }}</dt>
                    <dd>{{ topic.meta.postings_count }}</dd>

                    <dt>{{ $gettext('Anzahl der Teilnehmenden am Thema') }}</dt>
                    <dd>{{ topic.meta.users_count }}</dd>

                    <dt>{{ $gettext('Letzte Aktivität') }}</dt>
                    <dd>
                        <template v-if="topic.meta.recent_activity">
                            <StudipDateTime :iso="topic.meta.recent_activity" />
                        </template>
                        <template v-else>
                            {{ $gettext('Keine Aktivität') }}
                        </template>
                    </dd>

                    <dt>{{ $gettext('Erstellt am') }}</dt>
                    <dd>
                        <StudipDateTime :iso="topic.mkdate" />
                    </dd>
                </dl>
            </div>
        </template>
    </StudipDialog>
</template>
