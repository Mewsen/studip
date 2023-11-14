<template>
    <ul>
        <li v-if="options.anonymous">{{ $gettext('Anonymes Review') }}</li>
        <li v-else>{{ $gettext('Offenes Review') }}</li>

        <li>
            {{
                $gettextInterpolate($gettext('%{n} Tage Zeit für das Review'), {
                    n: options.duration,
                })
            }}
        </li>

        <li>
            {{ reviewTypes[options.type].long }}
        </li>

        <li v-if="options.automaticPairing">
            {{ $gettext('Zusammenstellung der Review-Paarungen durch das Programm') }}
        </li>
        <li v-else>{{ $gettext('Zusammenstellung der Review-Paarungen durch die Lehrenden') }}</li>
    </ul>
</template>

<script lang="ts">
import Vue, { PropType } from 'vue';
import { ProcessConfiguration, ASSESSMENT_TYPES } from './process-configuration';

export default Vue.extend({
    props: {
        options: {
            required: true,
            type: Object as PropType<ProcessConfiguration>,
        },
    },
    computed: {
        reviewTypes: () => ASSESSMENT_TYPES,
    },
});
</script>
