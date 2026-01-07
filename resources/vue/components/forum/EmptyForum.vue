<script setup>
import {getDiscussionCreateURL} from "./helpers/urls";
import {$gettext} from "../../../assets/javascripts/lib/gettext";
import StudipIcon from "@/vue/components/StudipIcon.vue";

const emptyForumIllustration = `${STUDIP.ASSETS_URL}images/forum/forum-keyvisual-positive.svg`;

const openTour = (id) => {
    STUDIP.Tour.init(id, 1);
}
</script>

<template>
    <div class="empty-forum">
        <div class="forum-illustration">
            <img :src="emptyForumIllustration" :alt="$gettext('Leeres Forum – Illustration')" />
        </div>
        <div>
            <h2>{{ $gettext('Starten Sie den Austausch') }}</h2>
            <p>
                {{ $gettext('Das Forum ist ein Ort für Kommunikation und Zusammenarbeit. Dieser entsteht bereits mit dem ersten Beitrag.') }}
            </p>
            <p>
                {{ $gettext('Noch ist es ziemlich ruhig hier. Warum starten Sie nicht einfach mit einer Frage zum Veranstaltungsthema, die Sie beschäftigt? Oder Sie teilen eine Idee, die noch Personen zum Mitmachen sucht?') }}
            </p>
            <p>
                {{ $gettext('Das Forum ist offen für alles, was weiterhilft, interessiert oder zum Nachdenken anregt. Manchmal braucht es nur einen kleinen Anstoß. Klicken Sie dafür auf „Eine Diskussion starten“ und erstellen den ersten Forumsbeitrag.') }}
            </p>

            <div class="buttons-container">
                <button type="button"
                        class="button button--icon-label"
                        @click.prevent="openTour('ea68d2f9d7b81d01d2d3ea38a105c734')">
                    <StudipIcon shape="lightbulb" :size="20" aria-hidden="true" />
                    {{ $gettext('Tour ansehen') }}
                </button>
                <a :href="getDiscussionCreateURL()" data-dialog="width=900;height=700" class="button button--icon-label">
                    <StudipIcon shape="add" :size="20" aria-hidden="true" />
                    {{ $gettext('Eine Diskussion starten') }}
                </a>
            </div>
        </div>
    </div>
</template>
