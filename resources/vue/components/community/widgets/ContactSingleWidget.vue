<template>
    <WidgetWrapper :title="widgetTitle" :widget-data="props.widgetData" v-bind="$attrs" @update-config="handleConfigUpdate">
        <template #content>
            <div class="contact-card">
                <div class="contact-card-avatar">
                    <img v-bind:src="contact?.meta?.avatar?.medium" alt="User avatar" >
                </div>
                <div class="contact-card-info">
                    <dl>
                        <dt>{{ $gettext('Name') }}</dt>
                        <dd>{{ contact?.['formatted-name'] }}</dd>
                        <dt>{{ $gettext('E-Mail') }}</dt>
                        <dd>{{ contact?.email }}</dd>
                        <dt>{{ $gettext('Status') }}</dt>
                        <dd class="contact-card-info-status">{{ contact?.permission }}</dd>
                    </dl>
                </div>
            </div>
            <button @click="showContact">show the contact</button>
        </template>

        <template #settings>
            <h3 class="settings-header">Konfiguration: Einzelner Kontakt</h3>
            <p>{{ config }}</p>
        </template>
    </WidgetWrapper>
</template>

<script setup>
import { computed, ref, onMounted, capitalize } from 'vue';
import { storeToRefs } from 'pinia';
import WidgetWrapper from '@/vue/components/widget/WidgetWrapper.vue';
import { useCommunityOverviewStore } from '@/vue/store/pinia/community/community-overview.js';
import { useContactStore } from '@/vue/store/pinia/community/community-contacts.js';
import { useContext } from '../../../composables/context.js';

const props = defineProps({
    widgetData: {
        type: Object,
        required: true,
    },
});

const overviewStore = useCommunityOverviewStore();
const contactStore = useContactStore();
const widgetTitle = 'ContactSingle';

const formPayload = ref(JSON.parse(JSON.stringify(props.widgetData.payload)));

const { records } = storeToRefs(contactStore);

const userId = useContext().userId.value;


const config = computed(() => {
    return props.widgetData.config;
});

const contactId = computed(() => {
    return props.widgetData.payload['contact-id'];
});

const contact = computed(() => {
    return contactStore.byId(contactId.value);
});

function showContact() {
    overviewStore.openContactInDrawer(contactId.value);
}

onMounted(async () => {
    await contactStore.fetchAll(userId);
    console.log(records.value.get(contactId.value));
});
</script>
<style lang="scss" scoped>
.contact-card {
    display: inline-flex;
    align-items: flex-start;
    gap: 5px;
    margin-bottom: 10px;
}
.contact-card-avatar {
    flex-grow: 1;
    img {
        max-width: 150px;
    }
}
.contact-card-info {
    flex-grow: 2;
    dl {
       margin-top: 0px; 
    }
    dd.contact-card-info-status {
        text-transform: capitalize;
    }
}
</style>