<template>
    <StudipLoadingSkeleton v-if="!updateInfosLoaded">
        <studipAssetImg file="ajax-indicator-black.svg" width="50"/>
    </StudipLoadingSkeleton>
    <StudipMessageBox v-else-if="Object.keys(updateInfos).length > 0"
                      :hideClose="true"
    >
        {{ $gettext('Es ist ein Update für ein Plugin verfügbar') }}<br>
        <form :action="updateURL" method="post">
            <input type="hidden" :name="csrf.name" :value="csrf.value">

            <div style="margin: 1ex;">
                <div v-for="(info, id) in updateInfos" :key="id">
                    <label>
                        <input type="checkbox" name="update[]" :value="id" :checked="getPluginById(id).enabled">
                        {{ $gettext('%{plugin}: Version %{version} installieren', {
                            plugin: getPluginById(id).name,
                            version: info.version
                        }) }}
                    </label>
                </div>
            </div>

            <button type="submit" class="accept button" name="doUpdate">
                {{ $gettext('Starten') }}
            </button>
        </form>
    </StudipMessageBox>

    <form :action="storeURL" method="post" class="default">
        <input type="hidden" :name="csrf.name" :value="csrf.value">
        <table class="default" id="plugin-administration-table">
            <caption>
                {{ $gettext('Verwaltung von Plugins') }}
                <template v-if="sortedPlugins.length < plugins.length">
                    ({{ $gettext('%{filtered} von %{total} Plugins', {
                        filtered: sortedPlugins.length,
                        total: plugins.length,
                    }) }})
                </template>
            </caption>
            <thead>
                <tr class="sortable">
                    <th :class="getSortClasses('enabled')"
                        :aria-sort="getAriaSort('enabled')"
                        scope="col"
                    >
                        <button class="as-link" type="button" @click.prevent="changeOrder('enabled')">
                            {{ $gettext('Aktiv') }}
                        </button>
                    </th>
                    <th :class="getSortClasses('name')"
                        :aria-sort="getAriaSort('name')"
                        scope="col"
                    >
                        <button class="as-link" type="button" @click.prevent="changeOrder('name')">
                            {{ $gettext('Name') }}
                        </button>
                    </th>
                    <th :class="getSortClasses('core')"
                        :aria-sort="getAriaSort('core')"
                        scope="col"
                    >
                        <button class="as-link" type="button" @click.prevent="changeOrder('core')">
                            {{ $gettext('Kernplugin') }}
                        </button>
                    </th>
                    <th :class="getSortClasses('origin')"
                        :aria-sort="getAriaSort('origin')"
                        scope="col"
                    >
                        <button class="as-link" type="button" @click.prevent="changeOrder('origin')">
                            {{ $gettext('Origin') }}
                        </button>
                    </th>
                    <th scope="col">{{ $gettext('Plugintypen') }}</th>
                    <th scope="col">{{ $gettext('Version') }}</th>
                    <th scope="col">{{ $gettext('Schema') }}</th>
                    <th :class="getSortClasses('position')"
                        :aria-sort="getAriaSort('position')"
                        scope="col"
                    >
                        <button class="as-link" type="button" @click.prevent="changeOrder('position')">
                            {{ $gettext('Position') }}
                        </button>
                    </th>
                    <th class="actions"
                        scope="col"
                    >{{ $gettext('Aktionen') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="plugin in sortedPlugins"
                    :key="plugin.id"
                    :class="{ 'not-installed': !plugin.installed }"
                >
                    <td>
                        <input type="checkbox"
                               :name="`enabled_${plugin.id}`"
                               v-model="plugin.enabled"
                               value="1"
                               :aria-label="getActivationAriaLabel(plugin)"
                        >
                    </td>
                    <td>
                        <button class="as-link" @click.prevent="selectedPlugin = plugin">
                            {{ plugin.name }}
                        </button>
                    </td>
                    <td>{{ plugin.core ? $gettext('ja') : $gettext('nein') }}</td>
                    <td>{{ plugin.manifest.origin ?? '' }}</td>
                    <td>{{ plugin.type.join(', ') }}</td>
                    <td>{{ plugin.manifest.version ?? '' }}</td>
                    <td>
                        {{ plugin.migration_info.schema_version ?? '' }}
                        <template v-if="plugin.migration_info.pending_migrations > 0">
                            <a :href="migratePluginURL(plugin)"
                               :title="getMigrationLabel(plugin)"
                               :aria-label="getMigrationLabel(plugin)"
                            >
                                <StudipIcon shape="plugin"
                                            role="status-red"
                                            aria-hidden="true"
                                />
                            </a>
                        </template>
                    </td>
                    <td>
                        <input type="number"
                               min="0"
                               :name="`position_${plugin.id}`"
                               v-model.number="plugin.position"
                               size="2"
                               :aria-label="$gettext('Position des Plugins %{name}', plugin)"
                        >
                    </td>
                    <td class="actions">
                        <StudipActionMenu :items="getActionMenuForPlugin(plugin)"
                                          :context="$gettext('Plugin %{name}', plugin)"
                        />
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="9">
                        <button type="submit" class="accept button">
                            {{ $gettext('Speichern') }}
                        </button>
                    </td>
                </tr>
            </tfoot>
        </table>
    </form>

    <Teleport to="#sidebar">
        <SidebarWidget :title="$gettext('Filter')">
            <template #content>
                <select v-model="filter.type"
                        class="sidebar-selectlist"
                        name="filter[type]"
                        :aria-label="$gettext('Plugin-Typen')"
                >
                    <option value="">
                        {{ $gettext('Alle Plugin-Typen anzeigen') }}
                    </option>
                    <option v-for="type in types" :key="type">
                        {{ type }}
                    </option>
                </select>

                <select v-model="filter.origin"
                        class="sidebar-selectlist"
                        name="filter[origin]"
                        :aria-label="$gettext('Herkunft der Plugins')"
                >
                    <option value="">
                        {{ $gettext('Nach Origin filtern') }}
                    </option>
                    <option v-for="origin in origins" :key="origin">
                        {{ origin }}
                    </option>
                </select>

                <div role="radiogroup"
                     :aria-label="$gettext('Anzeige von Kern-Plugins')"
                >
                    <div>
                        <label>
                            <input type="radio"
                                   name="filter[core]"
                                   value="yes"
                                   v-model="filter.corePlugins"
                            >
                            {{ $gettext('Alle Plugins anzeigen') }}
                        </label>
                    </div>
                    <div>
                        <label>
                            <input type="radio"
                                   name="filter[core]"
                                   value="no"
                                   v-model="filter.corePlugins"
                            >
                            {{ $gettext('Kern-Plugins ausblenden') }}
                        </label>
                    </div>
                    <div>
                        <label>
                            <input type="radio"
                                   name="filter[core]"
                                   value="only"
                                   v-model="filter.corePlugins"
                            >
                            {{ $gettext('Nur Kern-Plugins anzeigen') }}
                        </label>
                    </div>
                </div>
            </template>
        </SidebarWidget>
    </Teleport>

    <StudipDialog v-if="selectedPlugin"
                  :title="selectedPlugin.name"
                  :height="400"
                  :width="600"
                  :close-text="$gettext('Schließen')"
                  @close="selectedPlugin = null"
    >
        <template #dialogContent>
            <dl>
                <dt>{{ $gettext('Name') }}</dt>
                <dd>{{ selectedPlugin.name }}</dd>
                <dt>{{ $gettext('Klassenname') }}</dt>
                <dd>{{ selectedPlugin.class }}</dd>
                <dt>{{ $gettext('Typ') }}</dt>
                <dd>{{ selectedPlugin.type.join(', ') }}</dd>
                <dt>{{ $gettext('Herkunft') }}</dt>
                <dd>{{ selectedPlugin.manifest.origin ?? '-' }}</dd>
                <dt>{{ $gettext('Version')}}</dt>
                <dd>{{ selectedPlugin.manifest.version ?? '-' }}</dd>
                <template v-if="selectedPlugin.manifest.description">
                    <dt>{{ $gettext('Beschreibung') }}</dt>
                    <dd>{{ selectedPlugin.manifest.description}}</dd>
                </template>
            </dl>
        </template>
    </StudipDialog>
</template>
<script>
import {mapState} from "pinia";
import {usePluginStore} from "../store/pinia/Plugin";
import {$gettext, $ngettext} from "../../assets/javascripts/lib/gettext";
import StudipMessageBox from "../components/StudipMessageBox.vue";
import StudipLoadingSkeleton from "../components/StudipLoadingSkeleton.vue";

const pluginStore = usePluginStore();

export default {
    name: 'PluginAdministration',
    components: {StudipLoadingSkeleton, StudipMessageBox},
    props: {
        configuration: Object,
    },
    data() {
        return {
            filter: {
                corePlugins: this.configuration.core_filter,
                origin: '',
                type: this.configuration.plugin_filter ?? ''
            },
            selectedPlugin: null,
            sort: {
                by: 'name',
                dir: 'asc'
            },
            updateInfosLoaded: false
        }
    },
    computed: {
        ...mapState(usePluginStore, ['getPluginById', 'origins', 'plugins', 'types', 'updateInfos']),
        csrf() {
            return STUDIP.CSRF_TOKEN;
        },
        sortedPlugins() {
            let plugins = [...this.plugins ?? []];
            plugins = plugins.filter(plugin => this.filter.type === '' || plugin.type.includes(this.filter.type));
            plugins = plugins.filter(plugin => this.filter.origin === '' || plugin.manifest.origin?.includes(this.filter.origin));
            plugins = plugins.filter(plugin => this.filter.corePlugins === 'yes' || (this.filter.corePlugins === 'no' && !plugin.core) || (this.filter.corePlugins === 'only' && plugin.core));
            return plugins.sort((a, b) => {
                const sortFactor = this.sort.dir === 'asc' ? 1 : -1;
                let sortValue = 0;

                switch (this.sort.by) {
                    case 'name':
                        sortValue = a.name.localeCompare(b.name);
                        break;
                    case 'enabled':
                        sortValue = a.enabled === b.enabled ? a.name.localeCompare(b.name) : (b.enabled ? 1 : -1);
                        break;
                    case 'core':
                        sortValue = a.core === b.core ? a.name.localeCompare(b.name) : (b.core ? 1 : -1);
                        break;
                    case 'origin':
                        sortValue = a.manifest.origin?.localeCompare(b.manifest.origin ?? '') || a.name.localeCompare(b.name);
                        break;
                    case 'position':
                        sortValue = a.position - b.position || a.name.localeCompare(b.name);
                        break;
                }

                return sortValue * sortFactor;
            });
        },
        storeURL() {
            return STUDIP.URLHelper.getURL('dispatch.php/admin/plugin/save');
        },
        updateURL() {
            return STUDIP.URLHelper.getURL('dispatch.php/admin/plugin/install_updates');
        },
    },
    methods: {
        $gettext,
        $ngettext,
        changeOrder(by) {
            if (this.sort.by === by) {
                this.sort.dir = this.sort.dir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sort.by = by;
                this.sort.dir = 'asc';
            }
        },
        getActionMenuForPlugin(plugin) {
            const actions = [];
            actions.push({
                id: 1,
                label: this.$gettext('Zugriffsrechte bearbeiten'),
                type: 'link',
                url: STUDIP.URLHelper.getURL(`dispatch.php/admin/role/assign_plugin_role/${plugin.id}`),
                icon: 'edit',
            });

            if (plugin.type.includes('StudipModule')) {
                actions.push({
                    id: 2,
                    label: this.$gettext('Beschreibung und Hervorhebung'),
                    type: 'link',
                    url: STUDIP.URLHelper.getURL(`dispatch.php/admin/plugin/edit_description/${plugin.id}`),
                    attributes: {'data-dialog': 'size=big'},
                    icon: 'infopage',
                });
            }

            if (!plugin.core) {
                actions.push(
                    {
                        id: 3,
                        label: this.$gettext('Automatisches Update verwalten'),
                        type: 'link',
                        url: STUDIP.URLHelper.getURL(`dispatch.php/admin/plugin/edit_automaticupdate/${plugin.id}`),
                        icon: 'install',
                    },
                    {
                        id: 4,
                        label: this.$gettext('Herunterladen'),
                        type: 'link',
                        url: STUDIP.URLHelper.getURL(`dispatch.php/admin/plugin/download/${plugin.id}`),
                        icon: 'download',
                    },
                    {
                        id: 5,
                        label: this.$gettext('Plugin löschen'),
                        type: 'link',
                        url: STUDIP.URLHelper.getURL(`dispatch.php/admin/plugin/ask_delete/${plugin.id}`),
                        icon: 'trash',
                    },
                );
            }

            return actions;
        },
        getSortClasses(by) {
            return {
                'sortasc': this.sort.by === by && this.sort.dir === 'asc',
                'sortdesc': this.sort.by === by && this.sort.dir === 'desc'
            }
        },
        getAriaSort(by) {
            if (this.sort.by !== by) {
                return 'none';
            }
            return this.sort.dir === 'asc' ? 'ascending' : 'descending';
        },
        migratePluginURL(plugin) {
            return STUDIP.URLHelper.getURL(`dispatch.php/plugin/migrate/${plugin.id}`);
        },
        getActivationAriaLabel(plugin) {
            return plugin.enabled
                 ? $gettext('Plugin %{name}, aktiviert. Abwählen zum Deaktivieren.', plugin)
                 : $gettext('Plugin %{name}, deaktiviert. Auswählen zum Aktivieren.', plugin);
        },
        getMigrationLabel(plugin) {
            return $ngettext(
                `Eine ausstehende Migration für Plugin %{name}`,
                '%{amount} ausstehende Migrationen für Plugin %{name}',
                plugin.migration_info.pending_migrations,
                {
                    amount: plugin.migration_info.pending_migrations,
                    name: plugin.name
                }
            );
        }
    },
    created() {
        Promise.allSettled([
            pluginStore.loadPlugins(),
            pluginStore.loadUpdateInfos(),
        ]).then(() => {
            this.updateInfosLoaded = true;
        });
    },
    watch: {
        'filter.corePlugins': {
            handler(current) {
                const config = {
                    ...this.configuration,
                    core_filter: current,
                }

                pluginStore.changeConfig(
                    'PLUGINADMIN_DISPLAY_SETTINGS',
                    config
                );
            }
        },
        'filter.type': {
            handler(current) {
                const config = {
                    ...this.configuration,
                    plugin_filter: current || null,
                }

                pluginStore.changeConfig(
                    'PLUGINADMIN_DISPLAY_SETTINGS',
                    config
                );
            }
        }
    }
}
</script>
<style lang="scss">
table#plugin-administration-table tr.not-installed td {
    background-color: var(--light-gray-color-20);
    text-decoration: line-through;
}
</style>
