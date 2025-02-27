<template>
    <div>
        <studip-progress-indicator v-if="loading"
                                   :size="32"
        />
        <table v-else-if="messages.data?.length > 0" class="default">
            <colgroup>
                <col>
                <col>
                <col>
                <col>
                <col style="width: 200px">
                <col style="width: 20px">
            </colgroup>
            <thead>
                <tr>
                    <th>{{ $gettext('Betreff') }}</th>
                    <th>{{ $gettext('Nachricht') }}</th>
                    <th>{{ $gettext('Erstellt von') }}</th>
                    <th>{{ $gettext('Zielgruppe') }}</th>
                    <th>{{ $gettext('Letzte Änderung') }}</th>
                    <th>{{ $gettext('Aktionen') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(message, index) in messages.data"
                    :key="index"
                >
                    <td>{{ message.attributes.subject }}</td>
                    <td v-html="message.attributes.message"></td>
                    <td>{{ getAuthor(message.relationships.author.data.id).attributes['formatted-name'] }}</td>
                    <td>{{ message.attributes.target }}</td>
                    <td>{{ message.attributes.chdate }}</td>
                    <td>
                        <studip-action-menu :items="actionMenuItems"
                                            @edit="editMessage(message.id)"
                                            @delete="deleteMessage(message.id)"></studip-action-menu>
                    </td>
                </tr>
            </tbody>
        </table>
        <studip-message-box v-else
                            type="info">
            {{ $gettext('Es wurden keine Nachrichten gefunden.') }}
        </studip-message-box>
        <teleport to="#message-views">
            <sidebar-widget id="views-widget" class="sidebar-widget" :title="$gettext('Ansichten')">
                <template #content>
                    <ul class="widget-list widget-links sidebar-views"
                        :aria-label="$gettext('Ansichten')">
                        <li id="index" :class="{ active: 'unsent' === currentView}">
                            <a :href="url('dispatch.php/massmail/overview')"
                               @click.prevent="setCurrentView('unsent')">
                                {{ $gettext('Zum Versand anstehend') }}
                            </a>
                        </li>
                        <li id="index" :class="{ active: 'templates' === currentView}">
                            <a :href="url('dispatch.php/massmail/overview')"
                               @click.prevent="setCurrentView('templates')">
                                {{ $gettext('Meine Vorlagen') }}
                            </a>
                        </li>
                        <li id="index" :class="{ active: 'protected' === currentView}">
                            <a :href="url('dispatch.php/massmail/overview')"
                               @click.prevent="setCurrentView('protected')">
                                {{ $gettext('Geschützt') }}
                            </a>
                        </li>
                    </ul>
                </template>
            </sidebar-widget>
        </teleport>
    </div>
</template>

<script>
import StudipProgressIndicator from '../StudipProgressIndicator.vue';
import StudipActionMenu from '../StudipActionMenu.vue';
import SidebarWidget from '../SidebarWidget.vue';

export default {
    name: 'MassMailMessagesList',
    components: { SidebarWidget, StudipActionMenu, StudipProgressIndicator },
    data() {
        return {
            loading: false,
            messages: {},
            currentView: 'unsent'
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
        getMessages() {
            this.loading = true;

            const data = { include: 'author'};

            switch (this.currentView) {
                case 'templates':
                    data.filter = {templates: 1};
                    break;
                case 'protected':
                    data.filter = {protected: 1};
                    break;
            }

            STUDIP.jsonapi.withPromises().get('mass-mails/messages', {data: data})
                .then(response => {
                    this.messages = response;
                    this.loading = false;
                })
                .catch(error => {
                    this.messages = [];
                    STUDIP.Report.error(this.$gettext('Es ist ein Fehler aufgetreten'), error);
                    this.loading = false;
                });
        },
        getAuthor(id) {
            const result = this.messages.included.filter(entry => entry.id === id);
            return result?.length > 0 ? result[0] : null;
        },
        editMessage(id) {
            window.location = STUDIP.URLHelper.getURL('dispatch.php/massmail/message/index/' + id);
        },
        deleteMessage(id) {
            if (STUDIP.Dialog.confirm(
                this.$gettext('Soll diese Nachricht wirklich gelöscht werden?'),
                () => {
                    window.location = STUDIP.URLHelper.getURL('dispatch.php/massmail/message/delete/' + id);
                },
                STUDIP.Dialog.close())
            );
        },
        url(target) {
            return STUDIP.URLHelper.getURL(target);
        },
        setCurrentView(view) {
            this.currentView = view;
            this.getMessages();
        }
    },
    created() {
        this.getMessages();
    }
}
</script>
