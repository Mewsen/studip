<template>
    <div class="contact-card-grid">
        <div
            v-for="contact in data"
            :key="contact.id"
            class="contact-card"
            :class="{ 'is-selected': isItemSelected(contact.id) }"
            @click="isSelectionMode ? toggleItem(contact.id) : null"
        >
            <div v-if="isSelectionMode" class="contact-card__checkbox">
                <input
                    type="checkbox"
                    :checked="isItemSelected(contact.id)"
                    @click.stop
                    @change="toggleItem(contact.id)"
                />
            </div>
            <div class="contact-card__menu">
                <studip-action-menu
                    :items="getMenuItems(contact)"
                    :collapse-at="0"
                    @delete="openDeleteDialog(contact)"
                    @delete-from-group="openRemoveFromGroupDialog(contact)"
                />
            </div>
            <div class="contact-card__body">
                <div class="contact-card__avatar">
                    <img :src="contact.meta.avatar.medium" :alt="contact['formatted-name']" />
                    <div
                        class="status-indicator"
                        :class="contact.meta['is-online'] ? 'is-online' : 'is-offline'"
                        :title="contact.meta['is-online'] ? $gettext('Online') : $gettext('Offline')"
                    ></div>
                </div>

                <div class="contact-card__content">
                    <div class="contact-card__header">
                        <a :href="getProfileUrl(contact)">
                            <h3 class="contact-name">{{ contact['formatted-name'] }}</h3>
                        </a>
                        <span class="contact-username">{{ contact.username }}</span>
                    </div>

                    <div class="contact-card__meta">
                        <div class="meta-item" :title="contact.email">
                            <studip-icon shape="mail" :size="14" />
                            <span>{{ contact.email }}</span>
                        </div>
                        <div v-if="contact.phone" class="meta-item">
                            <studip-icon shape="phone" :size="14" />
                            <span>{{ contact.phone }}</span>
                        </div>
                        <div v-if="contact.cell" class="meta-item">
                            <studip-icon shape="cellphone" :size="14" />
                            <span>{{ contact.cell }}</span>
                        </div>
                    </div>

                    <div class="contact-card__actions">
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
                            <studip-icon shape="chat" />
                        </a>
                        <a
                            v-if="(contact.cell || contact.phone) && canCall"
                            :href="`tel:${contact.cell || contact.phone}`"
                            class="as-button icon-only"
                        >
                            <studip-icon :shape="contact.cell ? 'cellphone' : 'phone'" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

defineProps(['data']);

const { proxy } = getCurrentInstance();
const {
    isConfirmDialogOpen,
    isProcessing,
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

const isItemSelected = (id) => {
    return selectedIds.value.includes(id);
};
</script>

<style scoped lang="scss">
.contact-card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 16px;
    padding: 16px 0;
}

.contact-card {
    position: relative;
    border: 1px solid var(--color--tile-border);
    background: var(--color--global-background);
    transition: all 0.2s ease-in-out;
    cursor: default;

    &:hover {
        border-color: var(--color--tile-border-focus);
    }

    &.is-selected {
        border-color: var(--color--tile-border-selected);
        background-color: var(--color--tile-background-selected);
    }

    &__menu {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 11;

        :deep(.action-menu) {
            display: inline-block;
        }
    }

    &__body {
        display: flex;
        padding: 12px;
        gap: 16px;
    }

    &__avatar {
        position: relative;
        height: 64px;
        flex-shrink: 0;

        img {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid var(--color--gray-lighter);
        }

        .status-indicator {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 14px;
            height: 14px;
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

    &__content {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    &__header {
        margin-bottom: 8px;
        padding-right: 24px;

        .contact-name {
            margin: 0;
            font-size: 1.1em;
            color: var(--color--base);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .contact-username {
            display: block;
            font-size: 0.85em;
            color: var(--color--gray);
        }
    }

    &__meta {
        flex-grow: 1;
        min-height: 3rem;

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.9em;
            color: var(--color--base-light);
            margin-bottom: 4px;

            span {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
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
    }

    &__checkbox {
        position: absolute;
        top: 8px;
        left: 8px;
        z-index: 10;

        input {
            cursor: pointer;
            width: 16px;
            height: 16px;
        }
    }
}
</style>
