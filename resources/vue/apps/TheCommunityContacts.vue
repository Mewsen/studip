<template>
    <template v-if="!contactsLoaded">
        <studip-progress-indicator v-show="showLoading" :description="$gettext('Kontakte werden geladen…')" />
    </template>
    <template v-else>
        <h2 class="community-header">{{ title }}</h2>
        <studip-data-set-viewer
            v-model:selection-mode="bulkModeActive"
            :data="filteredContacts"
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
                    <a class="as-button" :class="{ disabled: !hasSelection }" :href="hasSelection ? bulkMailUrl : null">
                        {{ $gettext('E-Mail senden') }}
                    </a>
                </studip-button-group>
            </template>

            <template #header-right>
                <div class="header-actions">
                    <studip-context-menu :title="$gettext('Filter')" button-shape="filter">
                        <template #content>
                            <studip-context-menu-entry>
                                <studip-switch :label="$gettext('Kontakt ist online')" v-model="filters.onlyOnline" />
                            </studip-context-menu-entry>
                            <studip-context-menu-entry>
                                <label>
                                    <span class="sr-only">{{ $gettext('Suche') }}</span>
                                    <studip-search-input
                                        v-model="filters.search"
                                        :placeholder="$gettext('Name oder Username...')"
                                    />
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
                                @click="openDialog('editGroup', contactGroupStore.selectedGroup.name)"
                            />
                            <studip-context-menu-entry
                                :label="$gettext('Gruppe löschen')"
                                :description="$gettext('Lösche diese Gruppe, deine Kontakte bleiben erhalten.')"
                                icon="trash"
                                is-clickable
                                @click="openDeleteGroupDialog"
                            />
                        </template>
                    </studip-context-menu>

                    <studip-context-menu :title="$gettext('Hinzufügen')" button-shape="add">
                        <template #content>
                            <studip-context-menu-entry
                                :label="$gettext('Kontakt hinzufügen')"
                                :description="$gettext('Erstellt aus einem Stud.IP Nutzer einen Kontakt')"
                                is-clickable
                                @click="openDialog('addContact')"
                            />
                            <studip-context-menu-entry
                                :label="$gettext('Gruppe erstellen')"
                                :description="$gettext('Mit Gruppen können Kontakte organisiert werden')"
                                is-clickable
                                @click="openDialog('addGroup')"
                            />
                            <studip-context-menu-entry
                                v-if="isSpecificGroupSelected"
                                :label="$gettext('Kontakt zu Gruppe hinzufügen')"
                                :description="$gettext('Fügt der ausgewählten Gruppe einen Kontakt hinzu')"
                                is-clickable
                                @click="openDialog('addToGroup')"
                            />
                        </template>
                    </studip-context-menu>
                </div>
            </template>
            <template #empty-state>
                <div class="empty-state-container">
                    <studip-icon
                        :shape="filters.search ? 'search' : filters.onlyOnline ? 'spaceship' : 'ufo'"
                        :size="60"
                    />

                    <h3 v-if="filters.search">
                        {{ $gettext('Keine Treffer für Ihre Suche') }}
                    </h3>
                    <h3 v-else-if="filters.onlyOnline">
                        {{ $gettext('Aktuell ist niemand aus dieser Auswahl online') }}
                    </h3>
                    <h3 v-else-if="isSpecificGroupSelected">
                        {{ $gettext('Diese Gruppe ist noch leer') }}
                    </h3>
                    <h3 v-else>
                        {{ $gettext('Sie haben noch keine Kontakte') }}
                    </h3>

                    <div class="empty-state-actions">
                        <button
                            v-if="filters.onlyOnline && !filters.search"
                            class="button"
                            @click="filters.onlyOnline = false"
                        >
                            {{ $gettext('Online-Filter aufheben') }}
                        </button>

                        <button v-if="filters.search" class="button" @click="filters.search = ''">
                            {{ $gettext('Suche zurücksetzen') }}
                        </button>

                        <button class="button add" @click="console.log('add contact')">
                            {{ $gettext('Kontakt hinzufügen') }}
                        </button>
                    </div>
                </div>
            </template>
        </studip-data-set-viewer>
        <studip-dialog
            v-if="isConfirmDialogOpen"
            :title="confirmConfig.title"
            :question="confirmConfig.question"
            :height="confirmConfig.height"
            :width="confirmConfig.width"
            @confirm="handleConfirmAction"
            @close="isConfirmDialogOpen = false"
        />
        <studip-dialog v-if="activeDialog" v-bind="currentConfig" @close="closeDialog" @confirm="handleConfirm">
            <template #dialogContent>
                <template v-if="activeDialog === 'addContact'">
                    <studip-multi-person-search
                        v-model="selectedUsers"
                        :exclude="excludedIds"
                        search-context="contacts"
                    />
                </template>

                <form v-if="['addGroup', 'editGroup'].includes(activeDialog)" class="default">
                    <label>
                        <span class="required">{{ $gettext('Gruppenname') }}</span>
                        <input type="text" v-model="groupNameModel" required />
                    </label>
                </form>

                <studip-dual-list-box
                    v-if="activeDialog === 'addToGroup'"
                    v-model="addToGroupIds"
                    :available-items="availableContacts"
                    label-key="username"
                />
            </template>
        </studip-dialog>
    </template>
</template>

<script setup>
import { computed, getCurrentInstance, ref, onMounted, watch } from 'vue';

import StudipButtonGroup from '@/vue/components/StudipButtonGroup.vue';
import StudipContextMenu from '@/vue/components/StudipContextMenu.vue';
import StudipContextMenuEntry from '@/vue/components/StudipContextMenuEntry.vue';
import StudipDataSetViewer from '@/vue/components/data-set-viewer/StudipDataSetViewer.vue';
import StudipDialog from '@/vue/components/StudipDialog.vue';
import StudipDualListBox from '@/vue/components/StudipDualListBox.vue';
import StudipMultiPersonSearch from '@/vue/components/StudipMultiPersonSearch.vue';
import StudipProgressIndicator from '@/vue/components/StudipProgressIndicator.vue';
import StudipSearchInput from '@/vue/components/StudipSearchInput.vue';
import StudipSwitch from '@/vue/components/StudipSwitch.vue';

import { useContactStore } from '@/vue/store/pinia/contact/contacts';
import { useContactGroupStore } from '@/vue/store/pinia/contact/contact-groups';

import { useLoadingBuffer } from '@/vue/composables/useLoadingBuffer.js';
import { useContactDialogActions } from '@/vue/composables/useContactDialogActions.js';
import { useContactActions } from '@/vue/composables/useContactActions.js';

import ContactCardView from '@/vue/components/community/contacts/ContactCardView.vue';
import ContactListView from '@/vue/components/community/contacts/ContactListView.vue';

const { proxy } = getCurrentInstance();

const contactStore = useContactStore();
const contactGroupStore = useContactGroupStore();

const { showLoading, runWithLoading } = useLoadingBuffer();

const {
    activeDialog,
    groupNameModel,
    selectedUsers,
    addToGroupIds,
    currentConfig,
    openDialog,
    closeDialog,
    handleConfirm,
} = useContactDialogActions(proxy.$gettext);
const { isConfirmDialogOpen, confirmConfig, handleConfirmAction, openDeleteGroupDialog } = useContactActions(
    proxy.$gettext,
);

const contactViews = {
    card: ContactCardView,
    list: ContactListView,
};

const bulkModeActive = ref(false);
const contactsLoaded = ref(false);
const currentSelection = ref([]);
const filters = ref({
    onlyOnline: false,
    search: '',
});

const availableContacts = computed(() => {
    return contactStore.all.filter((contact) => {
        return !contact.group_ids || !contact.group_ids.has(contactGroupStore.selectedGroupId);
    });
});

const userId = computed(() => {
    return STUDIP.USER_ID;
});

const filteredContacts = computed(() => {
    let result = contactStore.all ?? [];

    if (selectedGroup.value !== 'all') {
        result = result.filter((c) => c.group_ids?.has(String(selectedGroup.value)));
    }

    if (filters.value.onlyOnline) {
        result = result.filter((c) => c.meta?.['is-online'] === true);
    }

    if (filters.value.search.trim() !== '') {
        const query = filters.value.search.toLowerCase();
        result = result.filter((c) => {
            return (
                c['formatted-name']?.toLowerCase().includes(query) ||
                c.username?.toLowerCase().includes(query) ||
                c.email?.toLowerCase().includes(query)
            );
        });
    }

    return result;
});

const contactGroups = computed(() => {
    return contactGroupStore.all ?? [];
});

const selectedGroup = computed({
    get: () => contactGroupStore.selectedGroupId,
    set: (val) => contactGroupStore.selectGroup(val),
});

const hasSelection = computed(() => {
    return currentSelection.value.length > 0;
});

const isSpecificGroupSelected = computed(() => {
    return contactGroupStore.selectedGroupId !== 'all';
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
    return contactGroupStore.selectedGroup?.name || proxy.$gettext('Alle Kontakte');
});

const excludedIds = computed(() => {
    const ids = contactStore.all.map((c) => c.id);
    ids.push(STUDIP.USER_ID);
    return ids;
});

watch(
    selectedGroup,
    async (newGroupId) => {
        if (newGroupId === 'all') return;

        const group = contactGroupStore.byId(newGroupId);
        if (group && !group.members_loaded) {
            await contactGroupStore.fetchGroupMembers(newGroupId);
        }
    },
    { immediate: true },
);

onMounted(async () => {
    runWithLoading(async () => {
        await contactStore.fetchAll(userId.value);
        await contactGroupStore.fetchAll();

        contactsLoaded.value = true;
    });
});
</script>
<style lang="scss">
.community-header {
    margin-top: 0;
}
</style>
