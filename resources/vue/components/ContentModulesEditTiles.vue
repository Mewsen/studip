<template>
    <div class="content-modules-wrapper">
        <draggable v-model="activeModules"
                   handle=".dragarea"
                   :component-data="{
                        name:'admin_contentmodules',
                        type: 'transition-group',
                        tag: 'div',
                   }"
                   item-key="id"
                   class="admin_contentmodules studip-grid"
                   role="listbox"
        >
            <template #item="{element}">
                <div
                    role="option"
                    class="studip-grid-element"
                    :class="getModuleCSSClasses(element, activated[element.id])"
                    v-cloak
                >
                    <div>
                        <a class="upper_part dragarea" :href="getDescriptionURL(element)" data-dialog>
                            <div>
                                <StudipIcon v-if="element.icon" :shape="element.icon.shape" :size="40" />
                            </div>
                            <div>
                                <h3>{{ element.displayname }}</h3>
                                {{ element.summary }}
                            </div>
                        </a>
                        <div class="down_part">
                            <div>
                                <a
                                    class="dragarea"
                                    tabindex="0"
                                    :aria-label="
                                        $gettext(
                                            'Sortierelement für Werkzeug %{module}. Drücken Sie die Tasten Pfeil-nach-oben oder Pfeil-nach-unten, um dieses Element in der Liste zu verschieben.',
                                            { module: element.displayname },
                                            true
                                        )
                                    "
                                    @keydown="keyboardHandler($event, element)"
                                    v-if="filterCategory === null"
                                    :ref="`draghandle-${element.id}`"
                                >
                                    <span class="drag-handle"></span>
                                </a>
                                <label v-if="!element.mandatory">
                                    <input
                                        type="checkbox"
                                        :checked="activated[element.id]"
                                        @click="toggleModule(element)"
                                        :ref="'checkbox_' + element.id"
                                    />
                                    {{ $gettext('Werkzeug ist aktiv') }}
                                </label>
                            </div>

                            <div class="icons_right">
                                <a
                                    href="#"
                                    class="toggle_visibility"
                                    role="checkbox"
                                    v-if="showVisibilityToggle(element)"
                                    :aria-checked="element.visibility !== 'tutor' ? 'true' : 'false'"
                                    @click.prevent="toggleModuleVisibility(element)"
                                >
                                    <studip-icon
                                        :shape="
                                            element.visibility !== 'tutor'
                                                ? 'visibility-visible'
                                                : 'visibility-invisible'
                                        "
                                        class="text-bottom"
                                        :title="
                                            $gettext(
                                                'Inhaltsmodul %{ name } für Teilnehmende unsichtbar bzw. sichtbar schalten',
                                                { name: element.displayname },
                                                true
                                            )
                                        "
                                    ></studip-icon>
                                </a>
                                <a :href="getRenameURL(element)" data-dialog="size=medium">
                                    <studip-icon
                                        shape="edit"
                                        class="text-bottom"
                                        :title="
                                            $gettext(
                                                'Umbenennen des Inhaltsmoduls %{ name }',
                                                { name: element.displayname },
                                                true
                                            )
                                        "
                                    ></studip-icon>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </draggable>
        <transition-group
            name="admin_contentmodules"
            class="admin_contentmodules studip-grid inactive-modules"
            tag="div"
            role="listbox"
        >
            <div
                v-for="module in inactiveModules"
                :key="module.id"
                role="option"
                class="studip-grid-element"
                :class="getModuleCSSClasses(module, activated[module.id])"
                v-cloak
            >
                <div>
                    <a class="upper_part" :href="getDescriptionURL(module)" data-dialog>
                        <div>
                            <StudipIcon v-if="module.icon" :shape="module.icon.shape" :size="40" />
                        </div>
                        <div>
                            <h3>{{ module.displayname }}</h3>
                            {{ module.summary }}
                        </div>
                    </a>
                    <div class="down_part">
                        <div>
                            <label v-if="!module.mandatory">
                                <input
                                    type="checkbox"
                                    :checked="activated[module.id]"
                                    @click="toggleModule(module)"
                                    :ref="'checkbox_' + module.id"
                                />
                                {{ $gettext('Werkzeug ist inaktiv') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </transition-group>
    </div>
</template>

<script>
import { mapState } from 'vuex';
import ContentModulesMixin from '../mixins/ContentModulesMixin.js';

export default {
    name: 'ContentModules',
    mixins: [ContentModulesMixin],
    data: () => ({
        activated: {},
        timeouts: {},
    }),
    computed: {
        ...mapState('contentmodules', ['modules']),
    },
    methods: {
        toggleModule(module) {
            this.activated[module.id] = !this.activated[module.id];
            this.toggleModuleActivation(module);
        },
    },
    watch: {
        modules: {
            immediate: true,
            deep: true,
            handler(current) {
                current.forEach((module) => this.activated[module.id] = module.active);
            },
        },
    },
};
</script>

<style lang="scss" scoped>
.content-modules-wrapper {
    max-width: 1410px;
}
.inactive-modules {
    margin-top: 1em;
    border-top: solid thin var(--color--tile-border);
    padding-top: 1em;
}
.studip-grid-element {
    display: flex;
    flex-direction: row;
    background-color: var(--white);
    border-left: 1px solid var(--color--tile-marker-inactive);

    &.visibility-visible {
        border-left-color: var(--color--tile-marker-active);
        > div {
            border-left-color: var(--color--tile-marker-active);
        }
    }
    &.visibility-invisible {
        border-left-color: var(--yellow);
        > div {
            border-left-color: var(--color--tile-marker-attention);
        }
    }

    &.sortable-ghost {
        border: dashed 2px var(--color--tile-border);
        margin: 0;
        * {
            opacity: 0;
        }
    }
    &.pulse:not(.sortable-ghost) {
        box-shadow: 0 0 0 0 rgb(255, 189, 51, 1);
        animation: pulse calc(2 * var(--transition-duration-slow));
        animation-iteration-count: 1;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 189, 51, 1);
        }
        25% {
            box-shadow: 0 0 0 5px rgba(255, 189, 51, 0.8);
        }
        50% {
            box-shadow: 0 0 0 5px rgba(255, 189, 51, 0.6);
        }
        75% {
            box-shadow: 0 0 0 5px rgba(255, 189, 51, 0.4);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(255, 189, 51, 0);
        }
    }

    > div {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all var(--transition-duration) ease;
        border-left: 10px solid var(--color--tile-marker-inactive);
        min-height: 150px;
        width: 100%;

        > a.upper_part {
            display: flex;
            color: var(--color--highlight);
            height: 100%;

            > :first-child {
                padding: 10px 5px 10px 15px;
            }
            > :last-child {
                padding: 10px 10px 20px;

                h3 {
                    margin-top: 0;
                    color: var(--color--highlight);
                }
            }
            &:hover {
                color: var(--color--highlight-hover);
                text-decoration: none;
                h3 {
                    color: var(--color--highlight-hover);
                }
            }
        }
        > .down_part {
            background-color: var(--color--tile-title-background);
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 30px;
            padding-left: 5px;
            > div {
                display: flex;
                align-items: center;
            }
            .icons_right > a {
                margin-right: 8px;
            }
        }
    }
}
</style>
