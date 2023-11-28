<template>
    <div v-if="shouldCollapse" class="action-menu">
        <a class="action-menu-icon" :title="$gettext('Aktionen')" aria-expanded="false" :aria-label="$gettext('Aktionsmenü')" href="#">
            <div></div>
            <div></div>
            <div></div>
        </a>
        <div class="action-menu-content">
            <div class="action-menu-title">
                {{ $gettext('Aktionen') }}
            </div>
            <ul class="action-menu-list">
                <li v-for="item in navigationItems" :key="item.id"
                    class="action-menu-item"
                    :class="{'action-menu-item-disabled': item.disabled}"
                >
                    <label v-if="item.disabled" aria-disabled="true" v-bind="item.attributes">
                        <studip-icon v-if="item.icon"
                                     :shape="item.icon"
                                     role="inactive"
                                     class="action-menu-item-icon"
                        />
                        <span v-else class="action-menu-no-icon"></span>

                        {{ item.label }}
                    </label>
                    <a v-else-if="item.type === 'link'" v-bind="item.attributes" v-on="linkEvents(item)">
                        <studip-icon v-if="item.icon"
                                     :shape="item.icon"
                                     class="action-menu-item-icon"
                        />
                        <span v-else class="action-menu-no-icon"></span>

                        {{ item.label }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div v-else>
        <template v-for="item in navigationItems">
            <label v-if="item.disabled" :key="item.id" aria-disabled="true" v-bind="item.attributes">
                <studip-icon :shape="item.icon"
                             :title="item.label"
                             role="inactive"
                             class="action-menu-item-icon"
                />
            </label>
            <a v-else :key="item.id" v-bind="item.attributes" v-on="linkEvents(item)">
                <studip-icon :shape="item.icon"
                             :title="item.label"
                             class="action-menu-item-icon"
                ></studip-icon>
            </a>
        </template>
    </div>
</template>

<script>
export default {
    name: 'studip-action-menu',
    props: {
        items: Array,
        collapseAt: {
            default: true,
        }
    },
    data () {
        return {
            open: false
        };
    },
    methods: {
        linkEvents (item) {
            let events = {};
            if (item.emit) {
                events.click = (e) => {
                    e.preventDefault();
                    this.$emit.apply(this, [item.emit].concat(item.emitArguments ?? []));
                    this.close();
                };
            }
            return events;
        },
        close () {
            STUDIP.ActionMenu.closeAll();
        }
    },
    computed: {
        navigationItems () {
            return this.items.map((item) => {
                item.type = item.type ?? 'link';
                item.attributes = item.attributes ?? {};

                if (item.type === 'link') {
                    item.attributes.href = item.url ?? '#';
                }

                return item;
            });
        },
        shouldCollapse () {
            if (this.collapseAt === false) {
                return false;
            }
            if (this.collapseAt === true) {
                return true;
            }
            return Number.parseInt(this.collapseAt) <= this.items.length;
        }
    }
}
</script>
