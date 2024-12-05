<template>
    <div class="cw-element-permissions">
        <studip-message-box v-if="message != false"
            :type="message.type ? message.type : 'info'"
            :details="message.details ? message.details : []"
            :hideClose="false">
            {{ $gettext(message.text) }}
        </studip-message-box>
        <table class="default">
            <caption>
                {{ $gettext('Personen') }}
            </caption>
            <colgroup>
                <col style="width:35%">
                <col style="width:15%">
                <col style="width:25%">
                <col style="width:15%">
                <col style="width:10%">
            </colgroup>
            <thead>
                <tr>
                    <th>{{ $gettext('Name') }}</th>
                    <th>{{ $gettext('Leserechte') }}</th>
                    <th>{{ $gettext('Lese- und Schreibrechte') }}</th>
                    <th>{{ $gettext('Ablaufdatum') }}</th>
                    <th class="actions">{{ $gettext('Aktion') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="listEmpty" class="empty">
                    <td colspan="5">
                        {{ $gettext('Es wurden noch keine Freigaben erteilt') }}
                    </td>
                </tr>
                <tr v-for="(user_perm, index) of userPermsList" :key="index">
                    <td>
                        <label>
                            {{ user_perm['formatted-name'] }}
                            <i>{{ user_perm.username }}</i>
                        </label>
                    </td>
                    <td class="perm">
                        <input
                            class="right"
                            :title="$gettext('Leserechte für %{ userName }', { userName: user_perm.username }, true)"
                            type="radio"
                            :name="`${user_perm.id}_right`"
                            value="read"
                            :checked="userPermsList[index]['read'] && !userPermsList[index]['write']"
                            @change="updateReadWritePerm(index, $event.target.value)"
                        />
                    </td>
                    <td class="perm">
                        <input
                            class="right"
                            :title="$gettext('Lese- und Schreibrechte für %{ userName }', { userName: user_perm.username }, true)"
                            type="radio"
                            :name="`${user_perm.id}_right`"
                            value="write"
                            :checked="userPermsList[index]['read'] && userPermsList[index]['write']"
                            @change="updateReadWritePerm(index, $event.target.value)"
                        />
                    </td>
                    <td>
                        <input
                            style="cursor: pointer !important;"
                            :title="getExpiryTitle(user_perm.username, userPermsList[index]['expiry'])"
                            type="date"
                            :min="minDate"
                            :id="`${user_perm.id}_expiry`"
                            v-model="userPermsList[index]['expiry']"
                            @change="refreshContentApproval"
                        />
                    </td>
                    <td class="actions">
                        <button
                            class="cw-permission-delete"
                            :title="$gettext('Entfernen der Rechte von %{ userName }', { userName: user_perm.username }, true)"
                            @click.prevent="confirmDeleteUserPerm(index)"
                        >
                        </button>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">
                        <span class="multibuttons">
                            <button class="button add cw-add-persons" @click.prevent="showAddMultiPersonDialog = true">
                                {{ $gettext('Personen hinzufügen') }}
                            </button>
                            <button
                                class="button"
                                :class="{disabled: listEmpty}"
                                :disabled="listEmpty"
                                @click.prevent="setAllPerms('read')"
                            >
                                {{ $gettext('Allen Leserechte geben') }}
                            </button>
                            <button
                                class="button"
                                :class="{disabled: listEmpty}"
                                :disabled="listEmpty"
                                @click.prevent="setAllPerms('write')"
                            >
                                {{ $gettext('Allen Lese- und Schreibrechte geben') }}
                            </button>
                        </span>
                    </td>
                </tr>
            </tfoot>
        </table>
        <studip-dialog
            v-if="showAddMultiPersonDialog"
            :title="$gettext('Personen hinzufügen')"
            :confirmText="$gettext('Speichern')"
            confirmClass="accept"
            :closeText="$gettext('Schließen')"
            closeClass="cancel"
            @close="clearSelectedUsers"
            @confirm="getSelectedUsers"
            height="500"
            width="750"
        >
            <template v-slot:dialogContent>
                <studip-multi-person-search v-model="selectedUsers" name="content-persons"/>
            </template>
        </studip-dialog>
        <studip-dialog
            v-if="showDeleteDialog"
            :title="$gettext('Personen löschen')"
            :question="$gettext('Möchten Sie diese Person wirklich löschen?')"
            height="180"
            @confirm="performDeleteUserPerm"
            @close="clearDeleteUserPerm"
        ></studip-dialog>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'courseware-content-permissions',
    emits: ['updateContentApproval'],
    props: {
        element: Object,
    },
    data() {
        return {
            showAddMultiPersonDialog: null,
            userPermsList: [],
            selectedUsers:[],
            contentApprovalUsers: [],
            message: false,
            showDeleteDialog: false,
            deleteUserPermIndex: -1
        };
    },

    mounted() {
        this.initUserPermsList();
    },

    computed: {
        ...mapGetters({
            userById: 'users/byId',
        }),

        listEmpty() {
            return this.userPermsList.length === 0;
        },

        contentApproval() {
            return {
                users: this.contentApprovalUsers,
            };
        },

        minDate() {
            let today = new Date();
            return today.toISOString().split('T')[0];
        }
    },

    methods: {
        ...mapActions({
            loadUser: 'users/loadById',
        }),

        getExpiryTitle(userName, date) {
            if (date) {
                return this.$gettext(
                    'Die Berechtigungen für %{ userName } laufen am folgendem Datum ab: %{ dateStr }',
                    { userName: userName, dateStr: new Date(date).toLocaleDateString() }
                );
            } else {
                return this.$gettext(
                    'Das Ablaufdatum der Berechtigungen für %{ userName }',
                    { userName: userName }
                );
            }
        },

        async getUser(userId) {
            await this.loadUser({id: userId});
            const user = this.userById({id: userId});
            return user;
        },

        async initUserPermsList() {

            if (this.element.attributes['content-approval'].users !== undefined) {
                this.contentApprovalUsers = this.element.attributes['content-approval'].users;
            }

            /* eslint-disable no-await-in-loop */
            for (const user_perm_obj of this.contentApprovalUsers) {
                let userObj = await this.getUser(user_perm_obj.id);
                this.userPermsList.push({
                    'id' : user_perm_obj.id,
                    'read': user_perm_obj.read,
                    'write': user_perm_obj.write,
                    'expiry': user_perm_obj.expiry ? new Date(user_perm_obj.expiry).toISOString().split('T')[0] : '',
                    'formatted-name': userObj.attributes['formatted-name'],
                    'username': userObj.attributes['username'],
                });
            }
        },

        async getSelectedUsers() {
            this.message = false;
            let duplicatedUsers = [];
            if (this.selectedUsers.length) {
                for (const selected_user of this.selectedUsers) {
                    let exists = this.userPermsList.some(user => {
                        return user.id === selected_user.id;
                    });
                    if (!exists) {
                        let newUserPerm = {
                            'id': selected_user.id,
                            'read': true,
                            'write': false,
                            'expiry': '',
                            'formatted-name': selected_user['formatted-name'],
                            'username': selected_user.username,
                        };
                        this.userPermsList.push(newUserPerm);
                        this.refreshContentApproval();
                    } else {
                        duplicatedUsers.push(selected_user);
                    }
                }
                this.selectedUsers = [];
            }
            this.showAddMultiPersonDialog = false;

            if (duplicatedUsers.length > 0) {
                this.message = {};
                this.message.text = this.$gettext('Die folgenden ausgewählten Personen existierten bereits:');
                this.message.type = 'info';
                this.message.details = [];
                for (const duplicated of duplicatedUsers) {
                    this.message.details.push(duplicated['formatted-name']);
                }
            }
        },

        clearSelectedUsers() {
            this.selectedUsers = [];
            this.showAddMultiPersonDialog = false;
        },

        confirmDeleteUserPerm(index) {
            this.deleteUserPermIndex = index;
            this.showDeleteDialog = true;
        },

        performDeleteUserPerm() {
            if (this.deleteUserPermIndex !== -1) {
                this.userPermsList.splice(this.deleteUserPermIndex, 1);
                this.refreshContentApproval();
            }
            this.clearDeleteUserPerm();
        },

        clearDeleteUserPerm() {
            this.deleteUserPermIndex = -1;
            this.showDeleteDialog = false;
        },

        updateReadWritePerm(index, value) {
            let read = false;
            let write = false;

            if (value === 'read') {
                read = true;
            } else if (value === 'write') {
                read = true;
                write = true;
            }

            this.userPermsList[index]['read'] = read;
            this.userPermsList[index]['write'] = write;
            this.refreshContentApproval();
        },

        setAllPerms(permtype) {
            if (this.listEmpty) {
                return false;
            }
            let read = true;
            let write = permtype === 'write';
            this.userPermsList.every(item => {
                item['read'] = read;
                item['write'] = write;

                return true;
            });

            this.refreshContentApproval();
        },

        refreshContentApproval() {
            this.contentApprovalUsers = [];
            for (const user_perm_obj of this.userPermsList) {
                let readRight = user_perm_obj.write ? true : user_perm_obj.read;
                this.contentApprovalUsers.push({
                    'id': user_perm_obj.id,
                    'read': readRight,
                    'write': user_perm_obj.write,
                    'expiry': user_perm_obj.expiry ? new Date(user_perm_obj.expiry).toISOString() : ''
                });
            }
            this.$emit('updateContentApproval', this.contentApproval);
        },
    },
};
</script>
