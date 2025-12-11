<script setup>
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import CopyAbleInput from "../../../components/lti/CopyAbleInput.vue";
import CopyAbleTextarea from "../../../components/lti/CopyAbleTextarea.vue";

defineProps({
    toolData: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <div class="lti">
        <form class="default" action="#">
            <dl>
                <dt>{{ $gettext('Lunch URL') }}</dt>
                <dd>
                    <CopyAbleInput :content="toolData.launch_url" />
                </dd>

                <dt>{{ $gettext('Initiate login URL') }}</dt>
                <dd>
                    <CopyAbleInput :content="toolData.auth_init_url" />
                </dd>

                <dt>{{ $gettext('Deep-linking URL') }}</dt>
                <dd>
                    <CopyAbleInput :content="toolData.deep_linking_url" />
                </dd>

                <dt>{{ $gettext('JWKS URL') }}</dt>
                <dd>
                    <CopyAbleInput :content="toolData.keyset_url" />
                </dd>

                <dt>{{ $gettext('Öffentlicher Schlüssel') }}</dt>
                <dd>
                    <CopyAbleTextarea :content="toolData.public_key" rows="12" />
                </dd>
            </dl>
        </form>
    </div>
</template>
