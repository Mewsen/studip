<template>
    <ul class="widget-list widget-links cw-action-widget" v-if="structuralElement">
        <li class="cw-action-widget-show-toc">
            <button @click="toggleTOC">
                {{ tocText }}
            </button>
        </li>
        <li class="cw-action-widget-show-consume-mode">
            <button @click="showConsumeMode">
                <translate>Vollbild einschalten</translate>
            </button>
        </li>
        <li v-if="canEdit && !blockedByAnotherUser" class="cw-action-widget-edit">
            <button @click="editElement">
                <translate>Seite bearbeiten</translate>
            </button>
        </li>
        <li v-if="canEdit && blockedByAnotherUser && userIsTeacher" class="cw-action-widget-remove-lock">
            <button @click="removeElementLock">
                <translate>Sperre aufheben</translate>
            </button>
        </li>
        <li v-if="canEdit && !blockedByAnotherUser" class="cw-action-widget-sort">
            <button @click="sortContainers">
                <translate>Abschnitte sortieren</translate>
            </button>
        </li>
        <li v-if="canEdit" class="cw-action-widget-add">
            <button @click="addElement">
                <translate>Seite hinzufügen</translate>
            </button>
        </li>
        <li class="cw-action-widget-info">
            <button @click="showElementInfo">
                <translate>Informationen anzeigen</translate>
            </button>
        </li>
        <li class="cw-action-widget-star">
            <button @click="createBookmark">
                <translate>Lesezeichen setzen</translate>
            </button>
        </li>
        <li v-if="!isRoot && canEdit && !blockedByAnotherUser" class="cw-action-widget-trash">
            <button @click="deleteElement">
                <translate>Seite löschen</translate>
            </button>
        </li>
    </ul>
</template>

<script>
import StudipIcon from './../StudipIcon.vue';
import CoursewareExport from '@/vue/mixins/courseware/export.js';
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'courseware-action-widget',
    props: ['structuralElement', 'canVisit'],
    components: {
        StudipIcon,
    },
    mixins: [CoursewareExport],
    computed: {
        ...mapGetters({
            userId: 'userId',
            userIsTeacher: 'userIsTeacher',
            consumeMode: 'consumeMode',
            showToolbar: 'showToolbar',

            blocked: 'currentElementBlocked',
            blockerId: 'currentElementBlockerId',
            blockedByThisUser: 'currentElementBlockedByThisUser',
            blockedByAnotherUser: 'currentElementBlockedByAnotherUser',
        }),
        isRoot() {
            if (!this.structuralElement) {
                return true;
            }

            return this.structuralElement.relationships.parent.data === null;
        },
        canEdit() {
            if (!this.structuralElement) {
                return false;
            }
            return this.structuralElement.attributes['can-edit'];
        },
        currentId() {
            return this.structuralElement?.id;
        },
        tocText() {
            return this.showToolbar ? this.$gettext('Inhaltsverzeichnis ausblenden') : this.$gettext('Inhaltsverzeichnis anzeigen');
        },
        isTask() {
            return this.structuralElement?.relationships.task.data !== null;
        }
    },
    methods: {
        ...mapActions({
            showElementEditDialog: 'showElementEditDialog',
            showElementAddDialog: 'showElementAddDialog',
            showElementDeleteDialog: 'showElementDeleteDialog',
            showElementInfoDialog: 'showElementInfoDialog',
            showElementRemoveLockDialog: 'showElementRemoveLockDialog',
            setStructuralElementSortMode: 'setStructuralElementSortMode',
            companionInfo: 'companionInfo',
            addBookmark: 'addBookmark',
            lockObject: 'lockObject',
            setConsumeMode: 'coursewareConsumeMode',
            setViewMode: 'coursewareViewMode',
            setShowToolbar: 'coursewareShowToolbar',
            setSelectedToolbarItem: 'coursewareSelectedToolbarItem',
            loadStructuralElement: 'loadStructuralElement',
        }),
        async editElement() {
            await this.loadStructuralElement(this.currentId);
            if (this.blockedByAnotherUser) {
                this.companionInfo({ info: this.$gettext('Diese Seite wird bereits bearbeitet.') });

                return false;
            }
            try {
                await this.lockObject({ id: this.currentId, type: 'courseware-structural-elements' });
            } catch(error) {
                if (error.status === 409) {
                    this.companionInfo({ info: this.$gettext('Diese Seite wird bereits bearbeitet.') });
                } else {
                    console.log(error);
                }

                return false;
            }
            this.showElementEditDialog(true);
        },
        async removeElementLock() {
            this.showElementRemoveLockDialog(true);
        },
        async sortContainers() {
            await this.loadStructuralElement(this.currentId);
            if (this.blockedByAnotherUser) {
                this.companionInfo({ info: this.$gettext('Diese Seite wird bereits bearbeitet.') });

                return false;
            }
            try {
                await this.lockObject({ id: this.currentId, type: 'courseware-structural-elements' });
            } catch (error) {
                if (error.status === 409) {
                    this.companionInfo({ info: this.$gettext('Diese Seite wird bereits bearbeitet.') });
                } else {
                    console.log(error);
                }

                return false;
            }
            this.setStructuralElementSortMode(true);
        },
        async deleteElement() {
            await this.loadStructuralElement(this.currentId);
            if (this.blockedByAnotherUser) {
                this.companionInfo({ info: this.$gettextInterpolate('Löschen nicht möglich, da %{blockingUserName} die Seite bearbeitet.', {blockingUserName: this.blockingUserName}) });

                return false;
            }
            await this.lockObject({ id: this.currentId, type: 'courseware-structural-elements' });
            this.showElementDeleteDialog(true);
        },
        addElement() {
            this.showElementAddDialog(true);
        },
        showElementInfo() {
            this.showElementInfoDialog(true);
        },
        createBookmark() {
            this.addBookmark(this.structuralElement);
            this.companionInfo({ info: this.$gettext('Das Lesezeichen wurde gesetzt.') });
        },
        toggleTOC() {
            this.setShowToolbar(!this.showToolbar);
        },
        showConsumeMode() {
            this.setViewMode('read');
            this.setSelectedToolbarItem('contents');
            this.setConsumeMode(true);
        },
    },
};
</script>
