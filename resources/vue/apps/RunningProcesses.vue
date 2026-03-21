<template>
    <div class="running_processes">
        <article class="studip">
            <section>
                <template v-if="sortedContexts.length === 0">
                    {{ $gettext('Es sind derzeit keine laufenden Prozesse vorhanden. Sobald für Sie relevante Aufgaben – zum Beispiel Fragebögen aus Ihren Veranstaltungen oder Einrichtungen – verfügbar sind, erscheinen diese hier.') }}
                </template>
                <ul class="clean" v-if="sortedContexts.length > 0">
                    <li v-for="context in sortedContexts" :key="context.id">
                        <a class="context" :href="context.url">
                            <span class="my-courses-avatar course-avatar-small"
                                  :style="'background-image: url(' + context.avatar + ')'"></span>
                            {{ context.name }}
                        </a>
                        <div class="processes">
                            <div v-for="process in getProcessesForContext(context)" :key="process.id" class="running_process">

                                <a :href="process.url"
                                   aria-hidden="true"
                                   tabindex="-1"
                                   :data-dialog="process.dialog ? 'size=auto' : null">
                                    <img :src="process.icon" aria-hidden="true">
                                </a>
                                <div class="process_right_side">
                                    <div class="process_text_info">
                                        <div>
                                            <a :href="process.url"
                                               :data-dialog="process.dialog ? 'size=auto' : null">
                                                {{ process.type }}
                                                {{ process.title }}
                                            </a>
                                            <span v-if="process.additionalShortInfo"
                                                  :title="process.additionalInfoTitleTag"
                                                  class="additionalShortInfo">
                                                {{ process.additionalShortInfo }}
                                            </span>
                                        </div>
                                        <div :title="getDatetimeInfo(process)" aria-live="off">
                                            {{ getRemainingTime(process) }}
                                        </div>
                                    </div>
                                    <div class="progressbar_container">
                                        <div class="progress_bar"
                                             role="progressbar"
                                             :aria-valuenow="getProcessPercentage(process)"
                                             aria-valuemax="100"
                                             aria-valuemin="0">
                                            <div :class="process.end - (currentTime / 1000) <= 86400 ? 'progress alerted' : 'progress'"
                                                 :style="'width: ' + getProcessPercentage(process) + '%;'"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </li>
                </ul>
            </section>
        </article>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { $ngettext } from "../../assets/javascripts/lib/gettext";
import { getRemainingTime as calculateRemainingTime } from "../utils/getRemainingTime";
import { datetime } from "../../assets/javascripts/lib/datetime";

const props = defineProps({
    contexts: {
        type: Object,
        required: true
    },
    processes: {
        type: Array,
        required: true
    }
});

const currentTime = ref(Date.now());
const intervalId = ref(null);

const sortedContexts = computed(() => {
    const contexts = Object.values(props.contexts);
    return contexts.sort((a, b) => {
        const a_short_end = getProcessesForContext(a)[0].end;
        const b_short_end = getProcessesForContext(b)[0].end;
        return a_short_end > b_short_end ? 1 : -1;
    });
});

function getProcessesForContext(context) {
    const processes = props.processes.filter(process => process.context_id === context.id);
    return processes.sort((a, b) => {
        return a.end > b.end ? 1 : -1;
    });
}

function getProcessPercentage(process) {
    const now = currentTime.value / 1000;
    if (now > process.end) {
        return 100;
    }
    if (now < process.begin) {
        return 0;
    }

    return Math.round((now - process.begin) / (process.end - process.begin) * 100);
}

function getRemainingTime(process) {
    const now = Math.floor(currentTime.value / 1000);

    if (now > process.end) {
        return this.$gettext('Beendet');
    }
    if (now < process.begin) {
        return this.$gettext('Noch nicht gestartet');
    }
    return calculateRemainingTime(process.end - now, $ngettext);
}

function getDatetimeInfo(process) {
    return datetime.getStudipDate(new Date(process.end * 1000));
}

onMounted(() => {
    intervalId.value = window.setInterval(() => {
        currentTime.value = Date.now();
    }, 1000);
});

onBeforeUnmount(() => {
    if (intervalId.value) {
        clearInterval(intervalId.value);
    }
});
</script>
