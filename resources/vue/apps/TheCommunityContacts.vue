<template>
    <h2 class="community-header">{{ title }}</h2>
    <studip-data-set-viewer
        v-model:selection-mode="bulkModeActive"
        :data="contacts"
        :available-views="['card', 'list']"
        :view-components="contactViews"
        @selection-change="currentSelection = $event"
    >
        <template #header-left="{ selectAll, countSelection }">
            <studip-context-menu :title="$gettext('Gruppe auswählen')" button-shape="group2">
                <template #content>
                    <studip-context-menu-entry>
                        <label>
                            <input type="radio" name="group" value="all" v-model="selectedGroup" />
                            {{ $gettext('Alle Kontakte') }}
                        </label>
                    </studip-context-menu-entry>
                    <studip-context-menu-entry v-for="group in contactGroups" :key="group.id">
                        <label>
                            <input type="radio" name="group" :value="group.id" v-model="selectedGroup" />
                            {{ group.name }}
                        </label>
                    </studip-context-menu-entry>
                </template>
            </studip-context-menu>
            <studip-button-group
                v-model="bulkModeActive"
                collapsible
                :toggle-label="$gettext('Mehrfachauswahl')"
                :active-label="$gettext('Auswahl abbrechen')"
            >
                <button class="button" @click="selectAll">{{ $gettext('Alles auswählen') }}</button>
                <a
                    class="as-button"
                    :class="{ disabled: !hasSelection }"
                    :data-dialog="hasSelection ? 'width=720;height=760' : null"
                    :href="hasSelection ? bulkMessageUrl : null"
                >
                    {{ $gettext('Nachricht senden') }}
                </a>
                <a
                    class="as-button"
                    :class="{ disabled: !hasSelection }"
                    :href="hasSelection ? bulkMailUrl : null"
                >
                    {{ $gettext('E-Mail senden') }}
                </a>
            </studip-button-group>
        </template>

        <template #header-right>
            <div class="header-actions">
                <studip-context-menu :title="$gettext('Filter')" button-shape="filter">
                    <template #content>
                        <studip-context-menu-entry>
                            <studip-switch :label="$gettext('Kontakt ist online')" />
                        </studip-context-menu-entry>
                        <studip-context-menu-entry>
                            <label>
                                {{ $gettext('Suche') }}
                                <input type="text" :placeholder="$gettext('Suchen...')" />
                            </label>
                        </studip-context-menu-entry>
                    </template>
                </studip-context-menu>
                <studip-context-menu
                    v-if="isSpecificGroupSelected"
                    :title="$gettext('Einstellungen')"
                    button-shape="settings"
                >
                    <template #content>
                        <studip-context-menu-entry
                            :label="$gettext('Gruppe bearbeiten')"
                            :description="$gettext('Ändere hier den Namen und andere Einstellungen')"
                            icon="edit"
                            is-clickable
                            @click="console.log('click edit contact group')"
                        />
                        <studip-context-menu-entry
                            :label="$gettext('Gruppe löschen')"
                            :description="$gettext('Lösche diese Gruppe, deine Kontakte bleiben erhalten.')"
                            icon="trash"
                            is-clickable
                            @click="console.log('click remove contact group')"
                        />
                    </template>
                </studip-context-menu>

                <studip-context-menu :title="$gettext('Hinzufügen')" button-shape="add">
                    <template #content>
                        <studip-context-menu-entry
                            :label="$gettext('Kontakt hinzufügen')"
                            :description="$gettext('Suche dir jemand nettes und füge ihn hinzu')"
                            icon="add"
                            is-clickable
                            @click="console.log('click add contact')"
                        />
                        <studip-context-menu-entry
                            :label="$gettext('Gruppe erstellen')"
                            :description="$gettext('Erstellt eine Gruppe ;)')"
                            icon="add"
                            is-clickable
                            @click="console.log('click add contact-group')"
                        />
                    </template>
                </studip-context-menu>
            </div>
        </template>
    </studip-data-set-viewer>
</template>

<script setup>
import { computed, getCurrentInstance, ref, onMounted } from 'vue';
import StudipButtonGroup from '@/vue/components/StudipButtonGroup.vue';
import StudipContextMenu from '@/vue/components/StudipContextMenu.vue';
import StudipContextMenuEntry from '@/vue/components/StudipContextMenuEntry.vue';
import StudipSwitch from '@/vue/components/StudipSwitch.vue';
import StudipDataSetViewer from '@/vue/components/data-set-viewer/StudipDataSetViewer.vue';
import { useContactStore } from '@/vue/store/pinia/contact/contacts';
import { useContactGroupStore } from '@/vue/store/pinia/contact/contact-groups';

import ContactCardView from '@/vue/components/community/contacts/ContactCardView.vue';
import ContactListView from '@/vue/components/community/contacts/ContactListView.vue';
import { forEach } from 'lodash';

const { proxy } = getCurrentInstance();

const contactViews = {
    card: ContactCardView,
    list: ContactListView,
};

const contactStore = useContactStore();
const contactGroupStore = useContactGroupStore();

const contacts = computed(() => {
    return contactStore.all ?? [];
});

const contactGroups = computed(() => {
    return contactGroupStore.all ?? [];
});

const selectedGroup = ref('all');

const bulkModeActive = ref(false);

const currentSelection = ref([]);

const hasSelection = computed(() => {
    return currentSelection.value.length > 0;
});

const isSpecificGroupSelected = computed(() => {
    return selectedGroup.value !== 'all';
});

const selectedContacts = computed(() => {
    return currentSelection.value.map((id) => contactStore.byId(id)).filter(Boolean);
});

const bulkMessageUrl = computed(() => {
    if (!bulkModeActive.value || selectedContacts.value.length === 0) {
        return '#';
    }
    const params = new URLSearchParams();

    selectedContacts.value.forEach((contact, index) => {
        params.append(`rec_uname[${index}]`, contact.username);
    });

    return `${STUDIP.URLHelper.base_url}dispatch.php/messages/write?${params.toString()}`;
});

const bulkMailUrl = computed(() => {
    if (!bulkModeActive.value || selectedContacts.value.length === 0) {
        return '#';
    }

    const emails = selectedContacts.value
        .map((c) => c.email)
        .filter(Boolean)
        .join(',');

    return `mailto:${emails}`;
});

const title = computed(() => {
    if (!isSpecificGroupSelected.value) {
        return proxy.$gettext('Alle Kontakte');
    } else {
        const group = contactGroupStore.byId(selectedGroup.value);

        return group.name;
    }
});

onMounted(async () => {
    const userId = STUDIP.USER_ID;

    await contactStore.fetchAll(userId);
    await contactGroupStore.fetchAll();
});
</script>
<style lang="scss">
.community-header {
    margin-top: 0;
}
</style>
