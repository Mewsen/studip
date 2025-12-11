<script setup>
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import StudipIcon from "../../../components/StudipIcon.vue";
import DeploymentIndex from "../../../components/lti/deployments/DeploymentIndex.vue";

const props = defineProps({
    deployments: {
        type: Array,
        default: () => ([])
    },
    registration: {
        type: Object,
        required: true
    }
});

const addDeployment = () => STUDIP.Dialog.fromURL(STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/deployments/create?registration_id=${props.registration.id}`), { width: '500', height: '400'});

const getRegistrationShowURL = id => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/registrations/show/${id}`);
</script>


<template>
    <div class="lti">
        <header class="header">
            <div class="header__content header__content--with-actions">
                <ul class="breadcrumb">
                    <li>
                        <a :href="getRegistrationShowURL(registration.id)" :title="$gettext('Zur Registrierung')">
                            {{ registration.name }}
                        </a>
                    </li>
                    <li>
                        {{ $gettext('LTI-Deploymetns') }}
                    </li>
                </ul>

                <div class="actions">
                    <button
                        type="button"
                        class="button button--icon-only"
                        @click="addDeployment"
                        :title="$gettext('Neues Deployment anlegen')">
                        <StudipIcon shape="add" aria-hidden="true" />
                    </button>
                </div>
            </div>
        </header>
        <DeploymentIndex :deployments="deployments" :registration="registration" />
    </div>
</template>
