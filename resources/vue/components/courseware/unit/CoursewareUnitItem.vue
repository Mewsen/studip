<template>
    <li class="courseware-unit-item">
        <courseware-tile
            tag="div"
            :color="color"
            :title="title"
            :descriptionLink="url"
            :descriptionTitle="$gettext('Lernmaterial öffnen')"
            :displayProgress="inCourseContext"
            :progress="progress"
            :imageUrl="imageUrl"
            :handle="handle"
            :handleId="'unit-handle-' + unit.id"
            @handle-keydown="$emit('unit-keydown', $event)"
        >
            <template #image-overlay-with-action-menu>
                <studip-action-menu
                    class="cw-unit-action-menu"
                    :items="menuItems"
                    :context="title"
                    :collapseAt="0"
                    @showDelete="openDeleteDialog"
                    @showExport="openExportDialog"
                    @showProgress="openProgressDialog"
                    @showSettings="openSettingsDialog"
                    @showLayout="openLayoutDialog"
                    @showPermissions="openPermissionsDialog"
                    @duplicateUnit="duplicate"
                    @showFeedbackCreate="openFeedbackCreateDialog"
                    @showFeedback="openFeedbackDialog"
                />
            </template>
            <template #image-overlay-bottom v-if="hasFeedbackElement">
                <studip-five-stars
                    v-if="hasFeedbackEntries"
                    :amount="feedbackAverage"
                    :size="16"
                    :title="$gettext(
                        'Lernmaterial wurde mit %{avg} Sternen bewertet',
                        { avg: feedbackAverage }
                    )"
                />
                <studip-five-stars
                    v-else
                    :amount="5"
                    :size="16"
                    role="inactive"
                    :title="$gettext('Lernmaterial wurde noch nicht bewertet')"
                />
            </template>
            <template #description>
                {{ description }}
            </template>
            <template #footer>
                <template v-if="hasPermissionSettings">
                    <p v-if="visiblePermissionInfo" :title="visiblePermissionInfo?.title">
                        <studip-icon :shape="visiblePermissionInfo.icon" role="info_alt" :size="16" />
                        {{ visiblePermissionInfo.text }}
                    </p>
                    <p v-if="writablePermissionInfo" :title="writablePermissionInfo?.title">
                        <studip-icon :shape="writablePermissionInfo.icon" role="info_alt" :size="16" />
                        {{ writablePermissionInfo.text }}
                    </p>
                </template>
                <template v-if="certificate">
                    <p>
                        <studip-icon shape="medal" :size="16" role="info_alt" />
                        {{ $gettext('Zertifikat') }}
                    </p>
                </template>
            </template>
        </courseware-tile>
        <studip-dialog
            v-if="showDeleteDialog"
            :title="$gettext('Lernmaterial löschen')"
            :question="$gettext(
                'Möchten Sie das Lernmaterial %{ unitTitle } wirklich löschen?',
                 { unitTitle: title },
                 true
            )"
            height="200"
            @confirm="executeDelete"
            @close="closeDeleteDialog"
        ></studip-dialog>

        <studip-dialog
            v-if="showProgressDialog"
            :title="userIsTeacher ? $gettext('Fortschritt aller Teilnehmenden') : $gettext('Mein Fortschritt')"
            :closeText="$gettext('Schließen')"
            closeClass="cancel"
            width="800"
            height="600"
            @close="closeProgressDialog"
        >
            <template v-slot:dialogContent>
                <courseware-unit-progress
                    :progressData="progresses"
                    :unitId="unit.id"
                    :rootId="parseInt(unitElement.id)"
                />
            </template>
        </studip-dialog>

        <courseware-unit-item-dialog-export v-if="showExportDialog" :unit="unit" @close="showExportDialog = false" />
        <courseware-unit-item-dialog-settings v-if="showSettingsDialog" :unit="unit" @close="closeSettingsDialog" />
        <courseware-unit-item-dialog-layout
            v-if="showLayoutDialog"
            :unit="unit"
            :unitElement="unitElement"
            @close="closeLayoutDialog"
        />
        <courseware-unit-item-dialog-permission-scope
            v-if="showPermissionScopeDialog"
            :unit="unit"
            @close="closePermissionsDialog"
            @switch="switchPermissionScope"
        />
        <courseware-unit-item-dialog-permissions
            v-if="showPermissionSettingsDialog"
            :unit="unit"
            :unit-name="title"
            @close="closePermissionsDialog"
        />
        <feedback-dialog
            v-if="showFeedbackDialog"
            :feedbackElementId="parseInt(feedbackElementId)"
            :currentUser="currentUser"
            @deleted="loadUnit({ id: unit.id })"
            @close="closeFeedbackDialog"
        />
        <feedback-create-dialog
            v-if="showFeedbackCreateDialog"
            :defaultQuestion="$gettext('Bewerten Sie das Lernmaterial')"
            rangeType="courseware-units"
            :rangeId="unit.id"
            @created="loadUnit({ id: unit.id })"
            @close="closeFeedbackCreateDialog"
        />
    </li>
</template>

<script>
import CoursewareTile from '../layouts/CoursewareTile.vue';
import CoursewareUnitItemDialogExport from './CoursewareUnitItemDialogExport.vue';
import CoursewareUnitItemDialogSettings from './CoursewareUnitItemDialogSettings.vue';
import CoursewareUnitItemDialogLayout from './CoursewareUnitItemDialogLayout.vue';
import CoursewareUnitItemDialogPermissions from './CoursewareUnitItemDialogPermissions.vue';
import CoursewareUnitItemDialogPermissionScope from './CoursewareUnitItemDialogPermissionScope.vue';
import CoursewareUnitProgress from './CoursewareUnitProgress.vue';
import FeedbackDialog from '../../feedback/FeedbackDialog.vue';
import FeedbackCreateDialog from '../../feedback/FeedbackCreateDialog.vue';
import StudipFiveStars from '../../feedback/StudipFiveStars.vue';
import axios from 'axios';

import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'courseware-unit-item',
    emits: ['unit-keydown'],
    components: {
        CoursewareTile,
        CoursewareUnitItemDialogExport,
        CoursewareUnitItemDialogLayout,
        CoursewareUnitItemDialogSettings,
        CoursewareUnitItemDialogPermissions,
        CoursewareUnitItemDialogPermissionScope,
        CoursewareUnitProgress,
        FeedbackDialog,
        FeedbackCreateDialog,
        StudipFiveStars,
    },
    props: {
        unit: Object,
        handle: {
            type: Boolean,
            default: true,
        },
    },
    data() {
        return {
            showDeleteDialog: false,
            showExportDialog: false,
            showSettingsDialog: false,
            showProgressDialog: false,
            showLayoutDialog: false,
            showPermissionsDialog: false,
            progresses: null,
            certificate: null,
            showFeedbackDialog: false,
            showFeedbackCreateDialog: false,

            showPermissionScopeDialog: false,
            showPermissionSettingsDialog: false,
        };
    },
    computed: {
        ...mapGetters({
            context: 'context',
            structuralElementById: 'courseware-structural-elements/byId',
            userIsTeacher: 'userIsTeacher',
            canCreateFeedbackElement: 'canCreateFeedbackElement',
            isFeedbackActivated: 'isFeedbackActivated',
            feedbackElementById: 'feedback-elements/byId',
            currentUser: 'currentUser',
        }),
        menuItems() {
            let menu = [];
            if (this.inCourseContext) {
                menu.push({ id: 1, label: this.$gettext('Fortschritt'), icon: 'progress', emit: 'showProgress' });
                if (this.userIsTeacher) {
                    menu.push({ id: 2, label: this.$gettext('Einstellungen'), icon: 'settings', emit: 'showSettings' });
                }
                if (this.isFeedbackActivated) {
                    if (this.canCreateFeedbackElement && !this.hasFeedbackElement) {
                        menu.push({
                            id: 7,
                            label: this.$gettext('Feedback aktivieren'),
                            icon: 'feedback',
                            emit: 'showFeedbackCreate',
                        });
                    }
                    if (this.hasFeedbackElement) {
                        menu.push({
                            id: 7,
                            label: this.$gettext('Feedback anzeigen'),
                            icon: 'feedback',
                            emit: 'showFeedback',
                        });
                    }
                }
                if (this.certificate) {
                    menu.push({
                        id: 3,
                        label: this.$gettext('Zertifikat'),
                        icon: 'medal',
                        url: STUDIP.URLHelper.getURL('sendfile.php', {
                            type: 0,
                            file_id: this.certificate,
                            file_name: this.$gettext('Zertifikat') + '.pdf',
                        }),
                    });
                }
            }

            if (this.userIsTeacher || !this.inCourseContext) {
                menu.push({ id: 4, label: this.$gettext('Darstellung'), icon: 'colorpicker', emit: 'showLayout' });
                menu.push({ id: 6, label: this.$gettext('Duplizieren'), icon: 'copy', emit: 'duplicateUnit' });
                menu.push({ id: 8, label: this.$gettext('Exportieren'), icon: 'export', emit: 'showExport' });
                menu.push({ id: 9, label: this.$gettext('Löschen'), icon: 'trash', emit: 'showDelete' });
            }

            if (this.userIsTeacher && this.inCourseContext) {
                menu.push({
                    id: 5,
                    label: this.$gettext('Rechte und Sichtbarkeit'),
                    icon: 'lock-unlocked',
                    emit: 'showPermissions',
                });
            }

            menu.sort((a, b) => {
                return a.id - b.id;
            });
            return menu;
        },
        unitElement() {
            return this.structuralElementById({ id: this.unit.relationships['structural-element'].data.id }) ?? null;
        },
        feedbackElementId() {
            return this.unit.relationships['feedback-element']?.data?.id;
        },
        hasFeedbackElement() {
            return this.feedbackElementId !== undefined;
        },
        hasFeedbackEntries() {
            return this.feedbackElement?.attributes?.['has-entries'] ?? false;
        },
        feedbackAverage() {
            return this.feedbackElement?.attributes?.['average-rating'] ?? 0;
        },
        feedbackElement() {
            return this.feedbackElementById({ id: this.feedbackElementId });
        },
        color() {
            return this.unitElement?.attributes?.payload?.color ?? 'studip-blue';
        },
        title() {
            return this.unitElement?.attributes?.title ?? '';
        },
        description() {
            return this.unitElement?.attributes?.payload?.description ?? '';
        },
        imageUrl() {
            return this.unitElement?.relationships?.image?.meta?.['download-url'] ?? '';
        },
        url() {
            if (this.inCourseContext) {
                return STUDIP.URLHelper.getURL('dispatch.php/course/courseware/courseware/' + this.unit.id, {
                    cid: this.context.id,
                });
            } else {
                return STUDIP.URLHelper.getURL('dispatch.php/contents/courseware/courseware/' + this.unit.id);
            }
        },
        progress() {
            if (this.unitElement) {
                return this.progresses?.[this.unitElement.id]?.progress?.cumulative ?? 0;
            }
            return 0;
        },
        inCourseContext() {
            return this.context.type === 'courses';
        },
        hasPermissionSettings() {
            return this.unit.attributes['permission-scope'] === 'unit';
        },
        visiblePermissionInfo() {
            if (!this.hasPermissionSettings) {
                return false;
            }
            let info = { icon: '', text: '', title: '' };
            if (!this.userIsTeacher) {
                if (this.unit.attributes.visible === 'period') {
                    info.icon = 'date';
                    info.text = this.$gettext(
                        'Sichtbar bis zum %{end}',
                        { end: this.permissionVisibleEndDate }
                    );

                    return info;
                }
                return false;
            }

            switch (this.unit.attributes.visible) {
                case 'always':
                    info.icon = 'visibility-visible';
                    switch (this.unit.attributes['permission-type']) {
                        case 'all':
                            info.text = this.$gettext('Sichtbar für alle');
                            break;
                        case 'users': {
                            if (this.unit.attributes['visible-all']) {
                                info.text = this.$gettext('Sichtbar für alle');
                            } else {
                                const users = this.unit.attributes['visible-approval'].length;
                                info.text = this.$ngettext(
                                    'Sichtbar für einen Studierenden',
                                    'Sichtbar für %{count} Studierende',
                                    users,
                                    { count: users }
                                );
                                if (users === 0) {
                                    info.icon = 'lock-locked';
                                    info.text = this.$gettext('Nur sichtbar für Lehrende');
                                }
                            }
                            break;
                        }
                        case 'groups': {
                            if (this.unit.attributes['visible-all']) {
                                info.text = this.$gettext('Sichtbar für alle');
                            } else {
                                const groups = this.unit.attributes['visible-approval'].length;
                                info.text = this.$ngettext(
                                    'Sichtbar für eine Gruppe',
                                    'Sichtbar für %{count} Gruppen',
                                    groups,
                                    { count: groups }
                                );
                                if (groups === 0) {
                                    info.icon = 'lock-locked';
                                    info.text = this.$gettext('Nur sichtbar für Lehrende');
                                }
                            }
                            break;
                        }
                    }
                    break;
                case 'never':
                    info.icon = 'lock-locked';
                    info.text = this.$gettext('Nur sichtbar für Lehrende');
                    break;
                case 'period': {
                    info.icon = 'date';
                    info.title = this.$gettext(
                        'Für %{persons} sichtbar vom %{start} bis zum %{end}',
                        {
                            start: this.permissionVisibleStartDate,
                            end: this.permissionVisibleEndDate,
                            persons: this.getPermissionPersons('visible-approval'),
                        }
                    );
                    info.text = this.$gettext('Zeitlich begrenzt sichtbar');
                    if (
                        this.unit.attributes['permission-type'] !== 'all' &&
                        this.unit.attributes['visible-approval'].length === 0
                    ) {
                        info.icon = 'lock-locked';
                        info.title = '';
                        info.text = this.$gettext('Nur sichtbar für Lehrende');
                    }
                    break;
                }
            }

            return info;
        },
        permissionVisibleStartDate() {
            return STUDIP.DateTime.getStudipDate(new Date(this.unit.attributes?.['visible-start-date']), false, true);
        },
        permissionVisibleEndDate() {
            return STUDIP.DateTime.getStudipDate(new Date(this.unit.attributes?.['visible-end-date']), false, true);
        },
        permissionWritableStartDate() {
            return STUDIP.DateTime.getStudipDate(new Date(this.unit.attributes?.['writable-start-date']), false, true);
        },
        permissionWritableEndDate() {
            return STUDIP.DateTime.getStudipDate(new Date(this.unit.attributes?.['writable-end-date']), false, true);
        },
        writablePermissionInfo() {
            if (this.unit.attributes['permission-scope'] !== 'unit') {
                return false;
            }

            let info = { icon: '', text: '', title: '' };

            if (!this.userIsTeacher) {
                if (this.unit.attributes['can-edit-content']) {
                    info.icon = 'edit';
                    if (this.unit.attributes.writable === 'period') {
                        info.text = this.$gettext(
                            'Bearbeitbar bis zum %{end}',
                            { end: this.permissionWritableEndDate }
                        );
                    } else {
                        info.text = this.$gettext('Bearbeitbar');
                    }

                    return info;
                }

                return false;
            }

            if (['always', 'period'].includes(this.unit.attributes.writable)) {
                if (
                    this.unit.attributes['permission-type'] !== 'all' &&
                    this.unit.attributes['writable-approval'].length === 0 &&
                    !this.unit.attributes['writable-all']
                ) {
                    return false;
                }
                info.icon = 'edit';
                if (this.unit.attributes.writable === 'always') {
                    info.text = this.$gettext(
                        'Bearbeitbar für %{persons}',
                        { persons: this.getPermissionPersons('writable-approval') }
                    );
                }
                if (this.unit.attributes.writable === 'period') {
                    info.title = this.$gettext(
                        'Für %{persons} bearbeitbar vom %{start} bis zum %{end}',
                        {
                            start: this.permissionWritableStartDate,
                            end: this.permissionWritableEndDate,
                            persons: this.getPermissionPersons('writable-approval'),
                        }
                    );
                    info.text = this.$gettext('Zeitlich begrenzt bearbeitbar');
                }

                return info;
            }

            return false;
        },
    },
    async mounted() {
        if (this.inCourseContext) {
            this.progresses = await this.loadUnitProgresses({ unitId: this.unit.id });
            this.checkCertificate();
        }
    },
    methods: {
        ...mapActions({
            deleteUnit: 'deleteUnit',
            loadUnitProgresses: 'loadUnitProgresses',
            loadUnit: 'courseware-units/loadById',
            copyUnit: 'copyUnit',
            companionSuccess: 'companionSuccess',
            createFeedback: 'feedback-elements/create',
            loadFeedbackElement: 'feedback-elements/loadById',
        }),
        checkCertificate() {
            if (this.getStudipConfig('COURSEWARE_CERTIFICATES_ENABLE') && this.unit.attributes.config.certificate) {
                axios
                    .get(
                        STUDIP.URLHelper.getURL(
                            'jsonapi.php/v1/courseware-units/' + this.unit.id + '/certificate/' + STUDIP.USER_ID
                        )
                    )
                    .then((response) => {
                        this.certificate = response.data;
                    })
                    .catch(() => {});
            }
        },
        executeDelete() {
            this.deleteUnit({ id: this.unit.id });
        },
        openDeleteDialog() {
            this.showDeleteDialog = true;
        },
        closeDeleteDialog() {
            this.showDeleteDialog = false;
        },
        openExportDialog() {
            this.showExportDialog = true;
        },
        async openProgressDialog() {
            this.showProgressDialog = true;
            this.progresses = await this.loadUnitProgresses({ unitId: this.unit.id });
        },
        closeProgressDialog() {
            this.showProgressDialog = false;
        },
        openSettingsDialog() {
            this.showSettingsDialog = true;
        },
        closeSettingsDialog() {
            this.showSettingsDialog = false;
        },
        openLayoutDialog() {
            this.showLayoutDialog = true;
        },
        closeLayoutDialog() {
            this.showLayoutDialog = false;
        },
        openPermissionsDialog() {
            this.showPermissionsDialog = true;
        },
        closePermissionsDialog() {
            this.showPermissionsDialog = false;
            this.showPermissionScopeDialog = false;
            this.showPermissionSettingsDialog = false;
        },
        openFeedbackCreateDialog() {
            this.showFeedbackCreateDialog = true;
        },
        closeFeedbackCreateDialog() {
            this.showFeedbackCreateDialog = false;
        },
        openFeedbackDialog() {
            if (this.feedbackElementId) {
                this.showFeedbackDialog = true;
            }
        },
        closeFeedbackDialog() {
            this.showFeedbackDialog = false;
            this.loadFeedbackElement({ id: this.feedbackElementId });
        },
        async duplicate() {
            await this.copyUnit({ unitId: this.unit.id, modified: null, duplicate: true });
            this.companionSuccess({ info: this.$gettext('Lernmaterial kopiert.') });
        },
        async switchPermissionScope() {
            await this.loadUnit({ id: this.unit.id });
            this.showPermissionScopeDialog = false;
            this.showPermissionSettingsDialog = true;
        },
        getPermissionPersons(type) {
            switch (this.unit.attributes['permission-type']) {
                case 'all':
                    return this.$gettext('alle');
                case 'users': {
                    if (this.unit.attributes['writable-all']) {
                        return this.$gettext('alle');
                    } else {
                        const users = this.unit.attributes[type].length;
                        return this.$ngettext(
                            'einen Studierenden',
                            '%{count} Studierende',
                            users,
                            { count: users }
                        );
                    }
                }
                case 'groups': {
                    if (this.unit.attributes['writable-all']) {
                        return this.$gettext('alle');
                    } else {
                        const groups = this.unit.attributes[type].length;
                        return this.$ngettext(
                            'eine Gruppe',
                            '%{count} Gruppen',
                            groups,
                            { count: groups }
                        );
                    }
                }
            }

            return '-';
        },
    },
    watch: {
        showPermissionsDialog(newVal) {
            if (newVal) {
                if (this.unit.attributes['permission-scope'] !== 'unit') {
                    this.showPermissionScopeDialog = true;
                    this.showPermissionSettingsDialog = false;
                } else {
                    this.showPermissionScopeDialog = false;
                    this.showPermissionSettingsDialog = true;
                }
            }
        },
    },
};
</script>
