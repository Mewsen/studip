<script setup>
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import LtiApp from "../../../components/lti/LtiApp.vue";
import CopyableCodeBlock from "../../../components/CopyableCodeBlock.vue";

defineProps({
    platformData: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <LtiApp>
        <dl>
            <dt>{{ $gettext('Plattform-ID') }}</dt>
            <dd>
                <CopyableCodeBlock :content="platformData.audience" />
            </dd>

            <dt>{{ $gettext('OIDC authentication URL') }}</dt>
            <dd>
                <CopyableCodeBlock :content="platformData.auth_login_url" />
            </dd>

            <dt>{{ $gettext('Token-URL') }}</dt>
            <dd>
                <CopyableCodeBlock :content="platformData.token_url" />
            </dd>

            <dt>{{ $gettext('JWKS URL') }}</dt>
            <dd>
                <CopyableCodeBlock :content="platformData.keyset_url" />
            </dd>

            <dt>{{ $gettext('Öffentlicher Schlüssel') }}</dt>
            <dd>
                <CopyableCodeBlock :content="platformData.public_key" />
            </dd>
        </dl>
    </LtiApp>
</template>

