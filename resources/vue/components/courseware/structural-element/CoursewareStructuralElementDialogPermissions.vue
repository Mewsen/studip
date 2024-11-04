<template>
    <studip-dialog
        :title="dialogTitle"
        :confirm-text="$gettext('Speichern')"
        confirm-class="accept"
        :close-text="$gettext('Schließen')"
        close-class="cancel"
        :height="height"
        :width="width"
        @close="$emit('close')"
        @confirm="storePermissions"
    >
        <template v-slot:dialogContent>
            <div class="cw-permissions-form-wrapper">
                <form class="default cw-permissions-form-radioset" @submit.prevent="">
                    <div class="cw-radioset-wrapper" role="group" aria-labelledby="permission-type">
                        <p class="sr-only" id="permission-type">{{ $gettext('Typ') }}</p>
                        <div class="cw-radioset">
                            <div class="cw-radioset-box" :class="[permissionType === 'all' ? 'selected' : '']">
                                <input
                                    type="radio"
                                    id="permission-type-all"
                                    value="all"
                                    v-model="permissionType"
                                    @change="updatePermissionType"
                                />
                                <label for="permission-type-all">
                                    <div
                                        class="label-icon all"
                                        :class="[permissionType === 'all' ? 'selected' : '']"
                                    ></div>
                                    <div class="label-text">
                                        <span>{{ $gettext('alle Studierenden') }}</span>
                                    </div>
                                </label>
                            </div>
                            <div class="cw-radioset-box" :class="[permissionType === 'users' ? 'selected' : '']">
                                <input
                                    type="radio"
                                    id="permission-type-users"
                                    value="users"
                                    v-model="permissionType"
                                    @change="updatePermissionType"
                                />
                                <label for="permission-type-users">
                                    <div
                                        class="label-icon users"
                                        :class="[permissionType === 'users' ? 'selected' : '']"
                                    ></div>
                                    <div class="label-text">
                                        <span>{{ $gettext('ausgewählte Studierende') }}</span>
                                    </div>
                                </label>
                            </div>
                            <div class="cw-radioset-box" :class="[permissionType === 'groups' ? 'selected' : '']">
                                <input
                                    type="radio"
                                    id="permission-type-groups"
                                    value="groups"
                                    v-model="permissionType"
                                    @change="updatePermissionType"
                                />
                                <label for="permission-type-groups">
                                    <div
                                        class="label-icon groups"
                                        :class="[permissionType === 'groups' ? 'selected' : '']"
                                    ></div>
                                    <div class="label-text">
                                        <span>{{ $gettext('Gruppen') }}</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </form>

                <form class="default cw-form-selects" @submit.prevent="">
                    <div class="cw-form-selects-row">
                        <label>
                            {{ $gettext('Sichtbar') }}
                            <select v-model="visible" @change="updateVisibile">
                                <option value="always">{{ $gettext('Immer') }}</option>
                                <option value="period">{{ $gettext('Zeitraum') }}</option>
                                <option v-if="permissionType === 'all'" value="never">{{ $gettext('Nie') }}</option>
                            </select>
                        </label>
                        <template v-if="visible === 'period'">
                            <label>
                                {{ $gettext('von') }}
                                <datepicker v-model="visibleStartDate" :placeholder="$gettext('unbegrenzt')" />
                            </label>
                            <label>
                                {{ $gettext('bis') }}
                                <datepicker v-model="visibleEndDate" :placeholder="$gettext('unbegrenzt')" />
                            </label>
                        </template>
                    </div>
                    <div class="cw-form-selects-row">
                        <label
                            >{{ $gettext('Bearbeitbar') }}
                            <select v-model="writable" @change="updateWritable">
                                <option v-if="permissionType === 'all'" value="never">{{ $gettext('Nie') }}</option>
                                <option value="always">{{ $gettext('Immer') }}</option>
                                <option value="period">{{ $gettext('Zeitraum') }}</option>
                            </select>
                        </label>
                        <template v-if="writable === 'period'">
                            <div>
                                <label>
                                    {{ $gettext('von') }}
                                    <datepicker v-model="writableStartDate" :placeholder="$gettext('unbegrenzt')" />
                                </label>
                            </div>
                            <div>
                                <label>
                                    {{ $gettext('bis') }}
                                    <datepicker v-model="writableEndDate" :placeholder="$gettext('unbegrenzt')" />
                                </label>
                            </div>
                        </template>
                    </div>
                </form>
            </div>
            <div v-if="permissionType === 'all'" class="cw-contents-overview-teaser">
                <div class="cw-contents-overview-teaser-content">
                    <header>{{ $gettext('Rechte und Sichtbarkeit') }}</header>
                    <p>
                        {{
                            $gettext(
                                'Hier stellen Sie für diese Seite Ihres Lernmaterials ein, welche Teilnehmenden aus Ihrer Veranstaltung sie sehen bzw. bearbeiten können. Falls Sie eine Einstellung für das gesamte Lernmaterial suchen, können Sie diese Einstellung in der „Übersicht“ über alle Lernmaterialien in Ihrer Veranstaltung vornehmen.'
                            )
                        }}
                    </p>
                    <p>
                        {{
                            $gettext(
                                'Entscheiden Sie sich zunächst ob „alle Studierende“ die gleichen Rechte erhalten sollen, oder ob „einzelne Studierende“ oder zuvor erstellte „Gruppen“ unterschiedliche Rechte benötigen. Die Einstellung „einzelne Studierende“ oder „Gruppen“ bietet sich beispielsweise dann an, wenn Sie eine Coursewareseite von einer Kleingruppe bearbeiten lassen wollen. Anschließend können Sie einstellen, in welchem Zeitraum diese Rechte gelten.'
                            )
                        }}
                    </p>
                </div>
            </div>

            <table v-if="permissionType === 'users'" class="default permission-table">
                <caption>
                    {{ $gettext('Studierende') }}
                </caption>
                <thead>
                    <tr>
                        <th>{{ $gettext('Name') }}</th>
                        <th>
                            {{ $gettext('Sichtbar') }}
                            <input type="checkbox" v-model="visibleAll" @change="updatewritableAll" />
                        </th>
                        <th>
                            {{ $gettext('Bearbeitbar') }}
                            <input type="checkbox" v-model="writableAll" @change="updateVisibleAll" />
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="autorMembers.length === 0">
                        <td colspan="3">{{ $gettext('Es wurden keine Einträge gefunden.') }}</td>
                    </tr>
                    <tr v-for="autor in autorMembers" :key="autor.id">
                        <td>{{ autor.formattedname }}</td>
                        <td>
                            <input
                                v-if="!visibleAll"
                                type="checkbox"
                                :value="autor.id"
                                v-model="visibleApprovalUsers"
                                @change="updateUserVisible(autor)"
                            />
                            <studip-icon v-else shape="accept" role="info" :size="14" />
                        </td>
                        <td>
                            <input
                                v-if="!writableAll"
                                type="checkbox"
                                :value="autor.id"
                                v-model="writableApprovalUsers"
                                @change="updateUserWritable(autor)"
                            />
                            <studip-icon v-else shape="accept" role="info" :size="14" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <template v-if="permissionType === 'groups'">
                <table v-if="groups.length > 0" class="default">
                    <caption>
                        {{ $gettext('Gruppen') }}
                    </caption>
                    <thead>
                        <tr>
                            <th>{{ $gettext('Name') }}</th>
                            <th>
                                {{ $gettext('Sichtbar') }}
                                <input type="checkbox" v-model="visibleAll" :disabled="writableAll" />
                            </th>
                            <th>
                                {{ $gettext('Bearbeitbar') }}
                                <input type="checkbox" v-model="writableAll" @change="updateVisibleAll" />
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="groups.length === 0">
                            <td colspan="3">{{ $gettext('Es wurden keine Einträge gefunden.') }}</td>
                        </tr>
                        <tr v-for="group in groups" :key="group.id">
                            <td>{{ group.name }}</td>
                            <td>
                                <input
                                    v-if="!visibleAll"
                                    type="checkbox"
                                    :value="group.id"
                                    v-model="visibleApprovalGroups"
                                    @change="updateGroupVisible(group)"
                                />
                                <studip-icon v-else shape="accept" role="info" :size="14" />
                            </td>
                            <td>
                                <input
                                    v-if="!writableAll"
                                    type="checkbox"
                                    :value="group.id"
                                    v-model="writableApprovalGroups"
                                    @change="updateGroupWritable(group)"
                                />
                                <studip-icon v-else shape="accept" role="info" :size="14" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <courseware-companion-box
                    v-else
                    :msgCompanion="
                        $gettext(
                            'Sie haben noch keine Gruppen erstellt. Mit Gruppen können Sie die Sichtbarkeits- und Bearbeitungsrechte anschließend besonders unkompliziert an Arbeitsgruppen vergeben.'
                        )
                    "
                    mood="pointing"
                >
                    <template #companionActions>
                        <a :href="statusGroupsUrl"
                            ><button class="button">{{ $gettext('Zu den Gruppen der Veranstaltung') }}</button></a
                        >
                    </template>
                </courseware-companion-box>
            </template>
        </template>
    </studip-dialog>
</template>

<script>
import CoursewareCompanionBox from '../layouts/CoursewareCompanionBox.vue';
import Datepicker from './../../Datepicker.vue';
import axios from 'axios';
import { mapActions, mapGetters } from 'vuex';
export default {
    name: 'courseware-structural-element-dialog-permissions',
    components: {
        CoursewareCompanionBox,
        Datepicker,
    },
    props: {
        structuralElement: Object,
    },
    data() {
        return {
            permissionType: 'all',
            visible: 'always',
            visibleAll: false,
            visibleApprovalUsers: [],
            visibleApprovalGroups: [],
            visibleStartDate: null,
            visibleEndDate: null,
            writable: 'never',
            writableAll: false,
            writableStartDate: null,
            writableEndDate: null,
            writableApprovalUsers: [],
            writableApprovalGroups: [],
            height: '680',
            width: '870',
            currentSemester: null,
        };
    },
    computed: {
        ...mapGetters({
            blocked: 'currentElementBlocked',
            blockerId: 'currentElementBlockerId',
            blockedByAnotherUser: 'currentElementBlockedByAnotherUser',
            context: 'context',
            relatedCourseMemberships: 'course-memberships/related',
            relatedCourseStatusGroups: 'status-groups/related',
            relatedUser: 'users/related',
            userById: 'users/byId',
        }),
        blockingUser() {
            if (this.blockedByAnotherUser) {
                return this.userById({ id: this.blockerId });
            }

            return null;
        },
        blockingUserName() {
            return this.blockingUser ? this.blockingUser.attributes['formatted-name'] : '';
        },
        users() {
            const parent = { type: 'courses', id: this.context.id };
            const relationship = 'memberships';
            const memberships = this.relatedCourseMemberships({ parent, relationship });

            return (
                memberships?.map((membership) => {
                    const parent = { type: membership.type, id: membership.id };
                    const member = this.relatedUser({ parent, relationship: 'user' });

                    return {
                        id: member.id,
                        formattedname: member.attributes['formatted-name'],
                        username: member.attributes['username'],
                        perm: membership.attributes['permission'],
                    };
                }) ?? []
            );
        },
        statusGroupsUrl() {
            return STUDIP.URLHelper.getURL('dispatch.php/course/statusgroups');
        },
        autorMembers() {
            if (Object.keys(this.users).length === 0 && this.users.constructor === Object) {
                return [];
            }

            const members = this.users.filter(function (user) {
                return user.perm === 'autor';
            }) ?? [];

            return members;
        },
        groups() {
            const parent = { type: 'courses', id: this.context.id };
            const relationship = 'status-groups';
            const statusGroups = this.relatedCourseStatusGroups({ parent, relationship });

            return (
                statusGroups?.map((statusGroup) => {
                    return {
                        id: statusGroup.id,
                        name: statusGroup.attributes['name'],
                    };
                }) ?? []
            );
        },
        periodsValid() {
            if (this.writable !== 'period' || this.visible !== 'period') {
                return true;
            }
            return this.visibleStartDate <= this.writableStartDate
                && (
                    this.visibleEndDate === null
                    || this.visibleEndDate >= this.writableEndDate
                );
        },
        semesterDates() {
            const date = Date.now() / 1000;
            let startDate = date;
            let endDate = date;
            if (this.currentSemester) {
                startDate = new Date(this.currentSemester.attributes.start).getTime() / 1000;
                endDate = new Date(this.currentSemester.attributes.end).getTime() / 1000;
            }

            return { start: startDate, end: endDate };
        },
        dialogTitle() {
            return this.$gettext('Rechte und Sichtbarkeit') + ': ' + this.structuralElement.attributes.title;
        }
    },
    methods: {
        ...mapActions({
            loadCourseMemberships: 'course-memberships/loadRelated',
            loadCourseStatusGroups: 'status-groups/loadRelated',
            updateStructuralElement: 'updateStructuralElement',
            loadStructuralElement: 'loadStructuralElement',
            companionWarning: 'companionWarning',
            unlockObject: 'unlockObject',
            showStructuralElementPermissionsDialog: 'showStructuralElementPermissionsDialog',
        }),
        setDimensions() {
            this.height = Math.min((window.innerHeight * 0.8).toFixed(0), 680).toString();
            this.width = Math.min((window.innerWidth * 0.8).toFixed(0), 870)
                .toFixed(0)
                .toString();
        },
        initData() {
            this.permissionType = this.structuralElement.attributes['permission-type'];
            this.visible = this.structuralElement.attributes['visible'];
            this.visibleAll = this.structuralElement.attributes['visible-all'];
            this.visibleStartDate = this.structuralElement.attributes['visible-start-date']
                ? new Date(this.structuralElement.attributes['visible-start-date']).getTime() / 1000
                : null;
            this.visibleEndDate = this.structuralElement.attributes['visible-end-date']
                ? new Date(this.structuralElement.attributes['visible-end-date']).getTime() / 1000
                : null;
            this.writable = this.structuralElement.attributes['writable'];
            this.writableAll = this.structuralElement.attributes['writable-all'];
            this.writableStartDate = this.structuralElement.attributes['writable-start-date']
                ? new Date(this.structuralElement.attributes['writable-start-date']).getTime() / 1000
                : null;
            this.writableEndDate = this.structuralElement.attributes['writable-end-date']
                ? new Date(this.structuralElement.attributes['writable-end-date']).getTime() / 1000
                : null;
            if (this.permissionType === 'users') {
                this.visibleApprovalUsers = this.structuralElement.attributes['visible-approval'];
                this.writableApprovalUsers = this.structuralElement.attributes['writable-approval'];
            }
            if (this.permissionType === 'groups') {
                this.visibleApprovalGroups = this.structuralElement.attributes['visible-approval'];
                this.writableApprovalGroups = this.structuralElement.attributes['writable-approval'];
            }

            axios
                .get(STUDIP.URLHelper.getURL('jsonapi.php/v1/semesters', { 'filter[current]': true }, true))
                .then((response) => {
                    this.currentSemester = response.data.data[0];
                })
                .catch((error) => {
                    this.currentSemester = null;
                });
        },
        async storePermissions() {
            await this.loadStructuralElement(this.structuralElement.id);
            if (this.blockedByAnotherUser) {
                this.companionWarning({
                    info: this.$gettextInterpolate(
                        this.$gettext(
                            'Ihre Änderungen konnten nicht gespeichert werden, da %{blockingUserName} die Bearbeitung übernommen hat.'
                        ),
                        { blockingUserName: this.blockingUserName }
                    ),
                });
                this.$emit('close');
                return false;
            }
            if (!this.blocked) {
                await this.lockObject({ id: this.structuralElement.id, type: 'courseware-structural-elements' });
            }

            let visibleApproval = [];
            let writableApproval = [];
            if (this.permissionType === 'users') {
                visibleApproval = this.visibleApprovalUsers;
                writableApproval = this.writableApprovalUsers;
            }
            if (this.permissionType === 'groups') {
                visibleApproval = this.visibleApprovalGroups;
                writableApproval = this.writableApprovalGroups;
            }

            if (this.visible === 'period' && this.visibleStartDate === null && this.visibleEndDate === null) {
                this.visible = 'always';
            }

            if (this.writable === 'period' && this.writableStartDate === null && this.writableEndDate === null) {
                this.visible = 'always';
            }

            if (
                this.visible === 'period' &&
                this.visibleStartDate !== null &&
                this.visibleEndDate !== null &&
                this.visibleStartDate > this.visibleEndDate
            ) {
                this.companionWarning({
                    info: this.$gettext(
                        'Das Enddatum des Sichtbarkeitszeitraums darf nicht vor dem Startdatum liegen.'
                    ),
                });
                return false;
            }

            if (
                this.writable === 'period' &&
                this.writableStartDate !== null &&
                this.writableEndDate !== null &&
                this.writableStartDate > this.writableEndDate
            ) {
                this.companionWarning({
                    info: this.$gettext('Das Enddatum des Bearbeitungszeitraums darf nicht vor dem Startdatum liegen.'),
                });
                return false;
            }

            if (!this.periodsValid) {
                this.companionWarning({
                    info: this.$gettext('Der Bearbeitungszeitraum muss innerhalb des Sichtbarkeitszeitraums liegen.'),
                });
                return false;
            }

            const structuralElement = {
                id: this.structuralElement.id,
                type: 'courseware-structural-elements',
                attributes: {
                    'permission-type': this.permissionType,
                    visible: this.visible,
                    'visible-all': this.visibleAll && this.permissionType !== 'all' ? 1 : 0,
                    'visible-start-date':
                        this.visible === 'period' ? new Date(this.visibleStartDate * 1000).toISOString() : null,
                    'visible-end-date':
                        this.visible === 'period' ? new Date(this.visibleEndDate * 1000).toISOString() : null,
                    'visible-approval': JSON.stringify(visibleApproval),
                    writable: this.writable,
                    'writable-all': this.writableAll && this.permissionType !== 'all' ? 1 : 0,
                    'writable-start-date':
                        this.writable === 'period' ? new Date(this.writableStartDate * 1000).toISOString() : null,
                    'writable-end-date':
                        this.writable === 'period' ? new Date(this.writableEndDate * 1000).toISOString() : null,
                    'writable-approval': JSON.stringify(writableApproval),
                },
            };
            this.showStructuralElementPermissionsDialog(false);
            await this.updateStructuralElement({ element: structuralElement, id: this.structuralElement.id });
            await this.unlockObject({ id: this.structuralElement.id, type: 'courseware-structural-elements' });
            this.$emit('store');
        },
        updatePermissionType() {
            if (this.permissionType !== 'all') {
                if (this.visible === 'never') {
                    this.visible = 'always';
                }
                if (this.writable === 'never') {
                    this.writable = 'always';
                }
            } else {
                if (this.writable === 'always') {
                    this.writable = 'never';
                }
            }
        },
        updateVisibile() {
            if (this.visible === 'never' && this.permissionType === 'all') {
                this.writable = 'never';
            }
            if (this.visible === 'period') {
                if (this.writable === 'always') {
                    this.writable = 'period';
                    this.writableStartDate = this.writableStartDate ?? this.semesterDates.start;
                    this.writableEndDate = this.writableEndDate ?? this.semesterDates.end;
                }

                this.visibleStartDate = this.visibleStartDate ?? this.semesterDates.start;
                this.visibleEndDate = this.visibleEndDate ?? this.semesterDates.end;
            }
        },
        updateWritable() {
            if (this.writable === 'always') {
                this.visible = 'always';
            }
            if (this.writable === 'period' && this.permissionType === 'all' && this.visible !== 'always') {
                this.visible = 'period';
                this.visibleStartDate = this.visibleStartDate ?? this.semesterDates.start;
                this.visibleEndDate = this.visibleEndDate ?? this.semesterDates.end;
            }
            if (this.writable === 'period') {
                this.writableStartDate = this.writableStartDate ?? this.semesterDates.start;
                this.writableEndDate = this.writableEndDate ?? this.semesterDates.end;
            }
        },
        updateUserWritable(user) {
            if (this.writableApprovalUsers.includes(user.id) && !this.visibleApprovalUsers.includes(user.id)) {
                this.visibleApprovalUsers.push(user.id);
            }
        },
        updateUserVisible(user) {
            if (this.writableApprovalUsers.includes(user.id) && !this.visibleApprovalUsers.includes(user.id)) {
                this.writableApprovalUsers = this.writableApprovalUsers.filter((id) => id !== user.id);
            }
        },

        updateGroupWritable(group) {
            if (this.writableApprovalGroups.includes(group.id) && !this.visibleApprovalGroups.includes(group.id)) {
                this.visibleApprovalGroups.push(group.id);
            }
        },
        updateGroupVisible(group) {
            if (this.writableApprovalGroups.includes(group.id) && !this.visibleApprovalGroups.includes(group.id)) {
                this.writableApprovalGroups = this.writableApprovalGroups.filter((id) => id !== group.id);
            }
        },
        updateVisibleAll() {
            if (this.writableAll) {
                this.visibleAll = true;
            }
        },
        updatewritableAll() {
            if (!this.visibleAll) {
                this.writableAll = false;
            }
        },
    },
    mounted() {
        this.setDimensions();
        this.initData();
        const parent = { type: 'courses', id: this.context.id };
        let options = {
            include: 'user',
            'page[limit]': 10000,
        };
        this.loadCourseMemberships({ parent, relationship: 'memberships', options: options });
        this.loadCourseStatusGroups({ parent, relationship: 'status-groups' });
    },
};
</script>
