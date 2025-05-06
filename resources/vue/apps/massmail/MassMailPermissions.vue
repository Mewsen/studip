<template>
    <div>
        <table v-if="!loading && permissions?.data.length > 0"
               class="default">
            <colgroup>
                <col>
                <col width="20%">
                <col width="30%">
                <col width="24">
            </colgroup>
            <thead>
            <tr>
                <th>{{ $gettext('Einrichtung') }}</th>
                <th>{{ $gettext('Benötigte Rechte') }}</th>
                <th>{{ $gettext('Erlaubte Zielgruppen') }}</th>
                <th>{{ $gettext('Aktionen') }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(permission) in permissions.data" :key="permission.id">
                <td>
                    {{ getInstitute(permission).attributes.name }}
                </td>
                <td>
                    {{ permission.attributes['min-perm']}}
                </td>
                <td>
                    <div v-if="permission.meta['allowed-degrees-count'] > 0">
                        {{ $gettext('%{degrees} Abschlüsse', { degrees: permission.meta['allowed-degrees-count']}) }}
                    </div>
                    <div v-if="permission.meta['allowed-subjects-count'] > 0">
                        {{ $gettext('%{subjects} Fächer', { subjects: permission.meta['allowed-subjects-count']}) }}
                    </div>
                    <div v-if="permission.meta['allowed-institutes-count'] > 0">
                        {{ $gettext('%{institutes} Einrichtungen', { institutes: permission.meta['allowed-institutes-count']}) }}
                    </div>
                </td>
                <td>
                    <studip-action-menu :items="actionMenuItems"
                                        @edit="editPermission(permission.id)"
                                        @delete="deletePermission(permission.id)"></studip-action-menu>
                </td>
            </tr>
            </tbody>
        </table>
        <studip-message-box v-if="!loading && permissions.data.length === 0" type="info">
            {{ $gettext('Es sind keine Berechtigungen für Personen ohne Root-Rechte konfiguriert.') }}
        </studip-message-box>
        <studip-progress-indicator v-if="loading"></studip-progress-indicator>
    </div>
</template>

<script>
import StudipProgressIndicator from "@/vue/components/StudipProgressIndicator.vue";
import StudipActionMenu from "@/vue/components/StudipActionMenu.vue";

export default {
    name: 'MassMailPermissions',
    components: {StudipActionMenu, StudipProgressIndicator},
    data() {
        return {
            loading: true,
            permissions: []
        }
    },
    computed: {
        actionMenuItems() {
            return [
                { label: this.$gettext('Bearbeiten'), icon: 'edit', emit: 'edit'},
                { label: this.$gettext('Löschen'), icon: 'trash', emit: 'delete'}
            ];
        }
    },
    methods: {
        getInstitute(permission) {
            const institute = this.permissions.included.filter(entry => {
                return entry.id === permission.relationships.institute.data.id;
            });
            return institute.at(0);
        },
        editPermission(id) {
            STUDIP.Dialog.fromURL(
                STUDIP.URLHelper.getURL('dispatch.php/massmail/permissions/edit/' + id)
            );
        },
        deletePermission(id) {
            if (STUDIP.Dialog.confirm(
                this.$gettext('Soll diese Berechtigung wirklich gelöscht werden?'),
                () => {
                    window.location = STUDIP.URLHelper.getURL('dispatch.php/massmail/permissions/delete/' + id);
                    location.reload();
                },
                STUDIP.Dialog.close())
            );
        }
    },
    created() {
        STUDIP.jsonapi.GET('mass-mails/permissions', { data: { include: 'institute'}})
            .then(response => {
                this.permissions = response;
                this.loading = false;
            })
            .fail(error => {
                STUDIP.Report.error(error);
            });
    }
}
</script>
