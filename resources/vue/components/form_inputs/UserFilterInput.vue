<template>
    <div class="formpart">
        <section v-if="filters.length > 0" class="default userfilter-list">
            <header>
                <h2>
                    {{ $gettext('Mindestens ein Filter muss zutreffen') }}
                </h2>
            </header>
            <table class="default">
                <tbody>
                    <tr v-for="(filter, index) in filters"
                         :key="index"
                         class="userfilter">
                        <td v-html="filter.attributes.text"></td>
                        <td class="actions">
                            <a class="undecorated"
                               @click.prevent="editFilter(index)"
                               :title="$gettext('Diesen Filter bearbeiten')"
                               tabindex="0"
                            >
                                <studip-icon shape="edit"></studip-icon>
                            </a>
                            <a class="undecorated"
                               @click.prevent="deleteFilter(index)"
                               :title="$gettext('Diesen Filter löschen')"
                               tabindex="0">
                                <studip-icon shape="trash"></studip-icon>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
        <button class="button"
                type="button"
                @click.prevent="editFilter(null)">
            {{ $gettext('Filter hinzufügen') }}
        </button>
        <studip-user-filter v-if="currentFilter !== null"
                            :filter="currentFilterData"
                            :context="context"
                            :target="target"
                            @submit="submitFilter"
                            @close="closeFilter"></studip-user-filter>
    </div>
</template>

<script>
import StudipUserFilter from '../StudipUserFilter.vue';

export default {
    name: 'UserFilterInput',
    components: {StudipUserFilter},
    emits: ['update:modelValue'],
    props: {
        name: {
            type: String,
            required: true
        },
        modelValue: String,
        context: {
            type: String,
            default: ''
        },
        target: {
            type: String,
            default: 'all'
        }
    },
    data() {
        return {
            key: 0,
            currentFilter: null,
            currentFilterData: [],
            filters: [],
            stringified: ''
        }
    },
    methods: {
        editFilter(index) {
            this.currentFilter = index !== null ? index : -1;
            if (this.currentFilter !== -1) {
                let data = [];

                for (const field of Object.entries(this.filters[index].attributes.fields)) {
                    data.push(field[1]);
                }

                this.currentFilterData = data;
            } else {
                this.currentFilterData = [];
            }
        },
        submitFilter(filter) {
            STUDIP.jsonapi.withPromises().post(
                'user-filters',
                {
                    data: {
                        data: {
                            attributes: {
                                filters: filter
                            }
                        }
                    }
                })
                .then(response => {
                    if (this.currentFilter !== -1) {
                        this.filters[this.currentFilter] = response.data;
                    } else {
                        this.filters.push(response.data);
                    }
                    this.currentFilter = null;
                    this.currentFilterData = [];
                    this.changed();
                })
                .catch(error => {
                    STUDIP.Report.error(this.$gettext('Es ist ein Fehler aufgetreten.'), error);
                });
        },
        closeFilter() {
            this.currentFilter = null;
        },
        deleteFilter(index) {
            this.filters.splice(index, 1);
            this.changed();
        },
        actionMenuItems(index) {
            return [
                {
                    id: 'edit',
                    label: this.$gettext('Bearbeiten'),
                    icon: 'edit',
                    emit: 'edit',
                    emitArguments: index
                },
                {
                    id: 'delete',
                    label: this.$gettext('Löschen'),
                    icon: 'trash',
                    emit: 'delete',
                    emitArguments: index
                }
            ];
        },
        changed() {
            this.stringified = JSON.stringify(this.filters);
            this.$emit('update:modelValue', this.stringified);
        }
    },
    watch: {
        target() {
            this.filters = [];
        }
    },
    mounted() {
        if (this.modelValue) {
            this.filters = JSON.parse(this.modelValue);
        }
    }
}
</script>

<style lang="scss" scoped>
table.default {
    margin-bottom: unset;
    width: 50%;

    .actions {
        text-align: right;
    }
}
</style>
