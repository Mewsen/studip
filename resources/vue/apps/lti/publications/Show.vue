<script setup>
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import LtiApp from "../../../components/lti/LtiApp.vue";
import StudipDateTime from "../../../components/StudipDateTime.vue";
import {showRangeURL, userProfileURL} from "../../../components/lti/helpers/urls";
import CopyableCodeBlock from "../../../components/CopyableCodeBlock.vue";
import StudipTooltipIcon from "../../../components/StudipTooltipIcon.vue";
import UserAvatarDropdown from "../../../components/avatar/UserAvatarDropdown.vue";

defineProps({
    publication: {
        type: Object,
        required: true
    }
});

</script>

<template>
    <LtiApp>
        <dl class="use-utility-classes">
            <dt>{{ $gettext('Name') }}</dt>
            <dd>{{ publication.name }}</dd>

            <dt>{{ $gettext('Version') }}</dt>
            <dd>
                {{ publication.version }}
            </dd>

            <dt>{{ $gettext('Bereich') }}</dt>
            <dd>
                <a :href="showRangeURL(publication.range_id)" :title="$gettext('Zur Veranstaltung')">
                    {{ publication.range_name }}
                </a>
            </dd>

            <dt>{{ $gettext('Status') }}</dt>
            <dd>
                {{ publication.status.label }}
            </dd>

            <dt>{{ $gettext('Anzahl der Teilnehmenden') }}</dt>
            <dd>
                {{ publication.members.length }}
            </dd>

            <dt>{{ $gettext('Custom-Parameter') }}</dt>
            <dd>
                <CopyableCodeBlock :content="publication.custom_parameter" />
            </dd>

            <dt>{{ $gettext('Erstellt von') }}</dt>
            <dd>
                <div class="user-avatar-container">
                    <UserAvatarDropdown :user="publication.user" />
                    <a :href="userProfileURL(publication.user.username)" :title="$gettext('Zum Benutzerprofil')">
                        {{ publication.user.name }}
                    </a>
                </div>
            </dd>

            <dt>{{ $gettext('Erstellt am') }}</dt>
            <dd>
                <StudipDateTime :iso="publication.mkdate" />
            </dd>
        </dl>

        <article class="studip">
            <header>
                <h1>
                    {{ $gettext('Konfiguration') }}
                </h1>
            </header>
            <dl>
                <dt>{{ $gettext('Anmeldefrist') }}</dt>
                <dd>
                    <StudipDateTime v-if="publication.enrollment_deadline" :iso="publication.enrollment_deadline" />
                    <template v-else>
                        {{ $gettext('Unbefristet') }}
                    </template>
                </dd>

                <dt>{{ $gettext('Startdatum') }}</dt>
                <dd>
                    <StudipDateTime v-if="publication.start_date" :iso="publication.start_date" />
                    <template v-else>
                        {{ $gettext('Unbefristet') }}
                    </template>
                </dd>

                <dt>{{ $gettext('Enddatum') }}</dt>
                <dd>
                    <StudipDateTime v-if="publication.end_date" :iso="publication.end_date" />
                    <template v-else>
                        {{ $gettext('Unbefristet') }}
                    </template>
                </dd>

                <dt>
                    {{ $gettext('Maximale Anzahl eingeschriebener Benutzer') }}
                    <StudipTooltipIcon
                        :text="$gettext('Die maximale Anzahl an externe Nutzer:innen, die auf diese Veranstaltung zugreifen können. Wenn das Feld leer oder auf null gesetzt ist, gibt es keine Begrenzung.')"
                    />
                </dt>
                <dd>
                    {{ publication.maximum_enrolled_users }}
                </dd>

                <dt>{{ $gettext('Rolle der Lehrende') }}</dt>
                <dd>
                    {{ publication.dozent_role }}
                </dd>

                <dt>{{ $gettext('Rolle der Studierende') }}</dt>
                <dd>
                    {{ publication.autor_role }}
                </dd>
            </dl>
        </article>
    </LtiApp>
</template>
