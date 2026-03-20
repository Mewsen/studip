<script setup>
import {computed, reactive} from 'vue';
import {$gettext} from '../../../../assets/javascripts/lib/gettext';
import {storeDeploymentURL, updateDeploymentURL} from "../helpers/urls";

const CSRF = STUDIP.CSRF_TOKEN;

const props = defineProps({
    deployment: {
        type: Object,
        required: true
    },
    registration: {
        type: Object,
        default: () => ({
            role: 'tool'
        })
    },
    registrations: {
        type: Array,
        default: () => ([])
    }
});

const form = reactive({
    ...props.deployment
});

const formActionURL = computed(() => {
    if (props.deployment.id) {
        return updateDeploymentURL(props.deployment.id);
    }

    return storeDeploymentURL();
});
</script>

<template>
    <form
        class="default"
        :action="formActionURL"
        method="post"
        v-bind="$attrs"
    >
        <input type="hidden" :name="CSRF.name" :value="CSRF.value" />
        <input type="hidden" name="registration_id" :value="registration.id" />

        <label class="studiprequired m-0">
            <span class="textlabel">{{ $gettext('Name') }}</span>
            <span :title="$gettext('Name ist ein Pflichtfeld')" aria-hidden="true" class="asterisk">*</span>
            <input
                required
                type="text"
                name="name"
                v-model="form.name" />
        </label>

        <label class="studiprequired m-0">
            <span class="textlabel">{{ $gettext('Deployment-ID') }}</span>
            <span :title="$gettext('Deployment-ID ist ein Pflichtfeld')" aria-hidden="true" class="asterisk">*</span>
            <input
                required
                class="max-w-full"
                type="text"
                name="deployment_key"
                v-model="form.deployment_key" />
        </label>

        <label v-if="registration.role === 'platform'" class="studiprequired">
            <span class="textlabel">{{ $gettext('Client-ID') }}</span>
            <span :title="$gettext('Client-ID ist ein Pflichtfeld')" aria-hidden="true" class="asterisk">*</span>
            <input required type="text" name="client_id" v-model="form.client_id" />
        </label>

        <slot name="footer">
            <footer data-dialog-button>
                <button class="button accept">
                    {{ $gettext('Speichern') }}
                </button>
                <button class="button cancel" type="button" data-dialog-close>
                    {{ $gettext('Abbrechen') }}
                </button>
            </footer>
        </slot>
    </form>
</template>
