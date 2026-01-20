<template>
    <ul class="contact-list">
        <li
            v-for="contact in data"
            :key="contact.id"
            class="contact-list-item"
            :class="{ 'is-selected': isItemSelected(contact.id) }"
            @click="isSelectionMode ? toggleItem(contact.id) : null"
        >
            <div v-if="isSelectionMode" class="contact-list-item__checkbox">
                <input
                    type="checkbox"
                    :checked="isItemSelected(contact.id)"
                    @click.stop
                    @change="toggleItem(contact.id)"
                />
            </div>

            <div class="contact-list-item__avatar">
                <img :src="contact.meta.avatar.small" :alt="contact['formatted-name']" />
                <div class="status-indicator" :class="contact.meta['is-online'] ? 'is-online' : 'is-offline'"></div>
            </div>

            <div class="contact-list-item__main">
                <div class="identity-row">
                    <a :href="getProfileUrl(contact)" @click.stop class="contact-name">
                        {{ contact['formatted-name'] }}
                    </a>
                    <span class="contact-username">({{ contact.username }})</span>
                </div>

                <div class="meta-row">
                    <div class="meta-item" :title="contact.email">
                        <studip-icon shape="mail" :size="12" />
                        <span>{{ contact.email }}</span>
                    </div>
                    <div v-if="contact.cell || contact.phone" class="meta-item">
                        <studip-icon :shape="contact.cell ? 'cellphone' : 'phone'" :size="12" />
                        <span>{{ contact.cell || contact.phone }}</span>
                    </div>
                </div>
            </div>

            <div class="contact-list-item__actions">
                <a
                    data-dialog="width=720;height=760"
                    :href="getMessageUrl(contact)"
                    class="as-button icon-only"
                    :title="$gettext('Nachricht schreiben')"
                >
                    <studip-icon shape="mail" />
                </a>
                <a
                    data-dialog="width=900;height=700"
                    :href="getChatUrl(contact)"
                    class="as-button icon-only"
                    :title="$gettext('Chat starten')"
                >
                    <studip-icon shape="chat" role="clickable" />
                </a>
                <a
                    v-if="(contact.cell || contact.phone) && canCall"
                    :href="`tel:${contact.cell || contact.phone}`"
                    class="as-button icon-only"
                >
                    <studip-icon :shape="contact.cell ? 'cellphone' : 'phone'" />
                </a>
                <studip-action-menu
                    :items="getMenuItems(contact)"
                    :collapse-at="0"
                    @delete="openDeleteDialog(contact)"
                    @delete-from-group="openRemoveFromGroupDialog(contact)"
                />
            </div>
        </li>
    </ul>
    <studip-dialog
        v-if="isConfirmDialogOpen"
        :title="confirmConfig.title"
        :question="confirmConfig.question"
        :height="confirmConfig.height"
        :width="confirmConfig.width"
        @confirm="handleConfirmAction"
        @close="isConfirmDialogOpen = false"
    />
</template>

<script setup>
import { getCurrentInstance, inject } from 'vue';
import StudipActionMenu from '@/vue/components/StudipActionMenu.vue';
import { useContactActions } from '@/vue/composables/useContactActions';

const props = defineProps(['data']);

const { proxy } = getCurrentInstance();
const {
    isConfirmDialogOpen,
    confirmConfig,
    handleConfirmAction,
    openDeleteDialog,
    openRemoveFromGroupDialog,
    getProfileUrl,
    getMessageUrl,
    getChatUrl,
    canCall,
    getMenuItems,
} = useContactActions(proxy.$gettext);
const { isSelectionMode, selectedIds, toggleItem } = inject('selectionContext');

const isItemSelected = (id) => selectedIds.value.includes(id);
</script>

<style lang="scss">
.contact-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 0;
}

.contact-list-item {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    background: var(--color--global-background);
    gap: 15px;
    transition:
        transform 0.1s,
        box-shadow 0.1s;

    &:not(:last-child) {
        border-bottom: 1px solid var(--color--tile-border);
    }

    &.is-selected {
        background-color: var(--color--tile-background-selected);
        border-color: var(--color--tile-border-selected);
    }

    &__avatar {
        position: relative;
        width: 44px;
        height: 44px;
        flex-shrink: 0;

        img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .status-indicator {
            position: absolute;
            bottom: 1px;
            right: 1px;
            width: 11px;
            height: 11px;
            border-radius: 50%;
            border: 2px solid var(--color--global-background);
            &.is-online {
                background-color: var(--color--good);
            }
            &.is-offline {
                background-color: var(--color--inactive);
            }
        }
    }

    &__main {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 2px;

        .identity-row {
            display: flex;
            align-items: baseline;
            gap: 8px;

            .contact-name {
                font-size: 1.15em;
            }

            .contact-username {
                font-size: 0.75rem;
                color: var(--color--font-secondary);
            }
        }

        .meta-row {
            display: flex;
            flex-wrap: wrap;
            column-gap: 15px;
            row-gap: 2px;

            .meta-item {
                display: flex;
                align-items: center;
                gap: 5px;
                font-size: 0.75rem;
                color: var(--color--font-secondary);

                .studip-icon {
                    line-height: 0.75rem;
                }
            }
        }
    }

    &__actions {
        display: flex;
        justify-content: flex-end;
        gap: 4px;
        margin-top: 8px;

        a.icon-only,
        button.icon-only {
            padding: 4px;
            height: 34px;
            width: 34px;
            min-width: unset;
        }

        button.icon-only .studip-icon {
            vertical-align: bottom;
        }

        a.icon-only .studip-icon {
            vertical-align: text-top;
        }

        .action-menu:not(.is-open) {
            border: solid thin var(--color--highlight);
            border-radius: var(--border-radius-default);
            background-color: var(--color--global-background);
            padding: 5px;
        }
    }
}
</style>
