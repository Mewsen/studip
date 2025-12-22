<script setup>
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import CopyAbleInput from "../../../components/lti/CopyAbleInput.vue";
import CopyAbleTextarea from "../../../components/lti/CopyAbleTextarea.vue";
import LtiApp from "../../../components/lti/LtiApp.vue";

defineProps({
    platformData: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <LtiApp>
        <form class="default" action="#">
            <dl>
                <dt>{{ $gettext('Plattform-ID') }}</dt>
                <dd>
                    <CopyAbleInput :content="platformData.audience" />
                </dd>

                <dt>{{ $gettext('OIDC authentication URL') }}</dt>
                <dd>
                    <CopyAbleInput :content="platformData.auth_login_url" />
                </dd>

                <dt>{{ $gettext('Token-URL') }}</dt>
                <dd>
                    <CopyAbleInput :content="platformData.token_url" />
                </dd>

                <dt>{{ $gettext('JWKS URL') }}</dt>
                <dd>
                    <CopyAbleInput :content="platformData.keyset_url" />
                </dd>

                <dt>{{ $gettext('Öffentlicher Schlüssel') }}</dt>
                <dd>
                    <CopyAbleTextarea :content="platformData.public_key" rows="12" />
                </dd>
            </dl>
        </form>
    </LtiApp>
</template>
