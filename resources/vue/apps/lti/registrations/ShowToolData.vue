<script setup>
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import CopyableCodeBlock from "../../../components/CopyableCodeBlock.vue";
import LtiApp from "../../../components/lti/LtiApp.vue";

defineProps({
    toolData: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <LtiApp>
        <dl>
            <dt>{{ $gettext('Launch URL') }}</dt>
            <dd>
                <CopyableCodeBlock :content="toolData.launch_url" />
            </dd>

            <dt>{{ $gettext('Initiate login URL') }}</dt>
            <dd>
                <CopyableCodeBlock :content="toolData.auth_init_url" />
            </dd>

            <dt>{{ $gettext('Deep-linking URL') }}</dt>
            <dd>
                <CopyableCodeBlock :content="toolData.deep_linking_url" />
            </dd>

            <dt>{{ $gettext('JWKS URL') }}</dt>
            <dd>
                <CopyableCodeBlock :content="toolData.keyset_url" />
            </dd>

            <dt>{{ $gettext('Öffentlicher Schlüssel') }}</dt>
            <dd>
                <CopyableCodeBlock :content="toolData.public_key" />
            </dd>
        </dl>
    </LtiApp>
</template>

