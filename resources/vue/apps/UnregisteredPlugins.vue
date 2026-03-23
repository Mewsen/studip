<template>

    <table class="default">
        <caption>
            {{ $gettext('Im Pluginverzeichnis vorhandene Plugins registrieren') }}
        </caption>
        <thead>
        <tr class="sortable">
            <th :class="getSortClasses('name')">
                <button class="as-link" @click.prevent="changeOrder('name')">
                    {{ $gettext('Name') }}
                </button>
            </th>
            <th :class="getSortClasses('classname')">
                <button class="as-link" @click.prevent="changeOrder('classname')">
                    {{ $gettext('Pluginklasse') }}
                </button>
            </th>
            <th :class="getSortClasses('version')">
                <button class="as-link" @click.prevent="changeOrder('version')">
                    {{ $gettext('Version') }}
                </button>
            </th>
            <th :class="getSortClasses('origin')">
                <button class="as-link" @click.prevent="changeOrder('origin')">
                    {{ $gettext('Ursprung') }}
                </button>
            </th>
            <th class="actions">{{ $gettext('Aktionen') }}</th>
        </tr>
        </thead>
        <tbody v-if="plugins.length">
            <tr v-for="(plugin, id) in sortedPlugins"
                :key="id">
                <td>{{ plugin.pluginname }}</td>
                <td>{{ plugin.pluginclassname }}</td>
                <td>{{ plugin.version }}</td>
                <td>{{ plugin.origin }}</td>
                <td>
                    <form class="default" method="post" :action="registerUrl(id)">
                        <input type="hidden" :name="csrf.name" :value="csrf.value" />
                        <button class="undecorated">
                        <StudipIcon
                            name=""
                            shape="install"
                            :title="$gettext('Plugin registrieren')"
                        />
                        </button>
                    </form>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr><td colspan="5">{{ $gettext('Es sind keine nicht registrierten Plugins vorhanden') }}</td></tr>
        </tbody>
    </table>

</template>
<script>

import StudipIcon from "../components/StudipIcon.vue";

export default {
    name: 'UnregisteredPlugins',
    components: {StudipIcon},
    props: {
        plugins: {
            type: Array,
            required: true
        },
    },
    data() {
        return {
            sort: {
                by: 'name',
                dir: 'asc'
            }
        }
    },
    computed: {
        csrf() {
            return STUDIP.CSRF_TOKEN;
        },
        sortedPlugins() {
            let sortedPlugins = [...this.plugins ?? []];
            return sortedPlugins.sort((a, b) => {
                const sortFactor = this.sort.dir === 'asc' ? 1 : -1;
                let sortValue = 0;

                switch (this.sort.by) {
                    case 'name':
                        sortValue = a.pluginname.localeCompare(b.pluginname);
                        break;

                    case 'classname':
                        sortValue = a.pluginclassname.localeCompare(b.pluginclassname);
                        break;

                    case 'version':
                        sortValue = a.version < b.version;
                        break;

                    case 'origin':
                        sortValue = a.origin.localeCompare(b.origin);
                        break;
                }

                return sortValue * sortFactor;

            });
        },
    },
    methods: {
        changeOrder(by) {
            if (this.sort.by === by) {
                this.sort.dir = this.sort.dir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sort.by = by;
                this.sort.dir = 'asc';
            }
        },
        getSortClasses(by) {
            return {
                'sortasc': this.sort.by === by && this.sort.dir === 'asc',
                'sortdesc': this.sort.by === by && this.sort.dir === 'desc'
            }
        },
        registerUrl(plugin_id) {
            return STUDIP.URLHelper.getURL(`dispatch.php/admin/plugin/register/${plugin_id}`);
        }

    }
}

</script>

<style lang="scss" scoped>
    table.default td:last-child {
        text-align: right;
    }
</style>
