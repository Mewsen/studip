<template>
    <table class="admin_contentmodules table default">
        <colgroup>
            <col style="width: 20px" v-if="filterCategory === null" />
            <col style="width: 20px" />
            <col />
            <col style="width: 24px" />
        </colgroup>
        <thead>
            <tr>
                <th v-if="filterCategory === null"></th>
                <th></th>
                <th>{{ $gettext('Name') }}</th>
                <th class="actions">{{ $gettext('Aktionen') }}</th>
            </tr>
        </thead>
        <draggable v-model="activeModules"
                   handle=".dragarea"
                   tag="tbody"
                   item-key="id"
        >
            <template #item="{element}">
                <tr :class="getModuleCSSClasses(element)" v-cloak>
                    <td v-if="filterCategory === null">
                        <a
                            class="dragarea"
                            tabindex="0"
                            :aria-label="
                                $gettext(
                                    'Sortierelement für Module %{module}. Drücken Sie die Tasten Pfeil-nach-oben oder Pfeil-nach-unten, um dieses Element in der Liste zu verschieben.',
                                    { module: element.displayname },
                                    true
                                )
                            "
                            @keydown="keyboardHandler($event, element)"
                            v-if="element.active"
                            :ref="`draghandle-${element.id}`"
                        >
                            <span class="drag-handle"></span>
                        </a>
                    </td>
                    <td>
                        <input
                            type="checkbox"
                            v-model="element.active"
                            @click="toggleModuleActivation(element)"
                            v-if="!element.mandatory"
                            :ref="'checkbox_' + element.id"
                        />
                    </td>
                    <td>
                        <a
                            class="upper_part"
                            :class="{ dragrea: element.active }"
                            :href="getDescriptionURL(element)"
                            data-dialog
                        >
                            <img :src="element.icon" width="20" height="20" v-if="element.icon" class="text-bottom" />
                            {{ element.displayname }}
                        </a>
                    </td>
                    <td class="actions">
                        <a
                            href="#"
                            v-if="showVisibilityToggle(element)"
                            role="checkbox"
                            :aria-checked="element.visibility !== 'tutor' ? 'true' : 'false'"
                            @click.prevent="toggleModuleVisibility(element)"
                        >
                            <studip-icon
                                :shape="element.visibility !== 'tutor' ? 'visibility-visible' : 'visibility-invisible'"
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
                        <a :href="getRenameURL(element)" data-dialog="size=auto" v-if="element.active">
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
                    </td>
                </tr>
            </template>
        </draggable>
        <tbody>
            <tr v-for="module in inactiveModules" :key="module.id" :class="getModuleCSSClasses(module)" v-cloak>
                <td v-if="filterCategory === null"></td>
                <td>
                    <input
                        type="checkbox"
                        v-model="module.active"
                        @click="toggleModuleActivation(module)"
                        v-if="!module.mandatory"
                        :ref="'checkbox_' + module.id"
                    />
                </td>
                <td>
                    <a class="upper_part" :href="getDescriptionURL(module)" data-dialog>
                        <img :src="module.icon" width="20" height="20" v-if="module.icon" class="text-bottom" />
                        {{ module.displayname }}
                    </a>
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>
</template>

<script>
import ContentModulesMixin from '../mixins/ContentModulesMixin.js';

export default {
    name: 'contentmodules-edit-table',
    mixins: [ContentModulesMixin],
};
</script>
<style lang="scss">
@use '../../assets/stylesheets/mixins/colors.scss';

table.admin_contentmodules > tbody > tr {
    &.sortable-ghost {
        * {
            opacity: 0;
        }
    }
    > td:first-child {
        background-image: linear-gradient(var(--dark-gray-color-60), var(--dark-gray-color-60));
        background-repeat: no-repeat;
        background-position: left;
        background-size: 10px auto;
        padding-left: 15px;
    }
    &.visibility-visible > td:first-child {
        background-image: linear-gradient(var(--green), var(--green));
    }
    &.visibility-invisible > td:first-child {
        background-image: linear-gradient(var(--yellow), var(--yellow));
    }
    > td {
        height: 31px; //to make all rows equally high
    }
}
</style>
