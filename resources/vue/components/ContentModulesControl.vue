<template>
    <div class="controls" v-if="isVisible">
        <div>
            <label>
                <input type="checkbox" :checked="module.active" @click="toggleModuleActivation(module)" :ref="'checkbox_' + module.id">
                {{ module.active ? $gettext('Werkzeug ist aktiv') : $gettext('Werkzeug ist inaktiv') }}
            </label>
        </div>
        <div>
            <a href="#"
               class="toggle_visibility"
               role="checkbox"
               v-if="module.active"
               :aria-checked="module.visibility !== 'tutor' ? 'true' : 'false'"
               @click.prevent="toggleModuleVisibility(module)">
                <studip-icon :shape="module.visibility !== 'tutor' ? 'visibility-visible' : 'visibility-invisible'"
                             class="text-bottom"
                             :title="$gettext('Inhaltsmodul %{ name } für Teilnehmende unsichtbar bzw. sichtbar schalten', { name: module.displayname}, true)"></studip-icon>
            </a>
        </div>
    </div>
</template>
<script>
import ContentModulesMixin from '../mixins/ContentModulesMixin.js';

export default {
    name: 'ContentModulesControl',
    props: {
        module_id: {
            type: String,
            required: true
        }
    },
    mixins: [ContentModulesMixin],
    computed: {
        isVisible() {
            return !this.module.mandatory;
        },
        module () {
            return this.modules.find(m => m.id === this.module_id) ?? null;
        }
    }
};
</script>
<style lang="scss">
.contentmodule_info {
    display: flex;
    .main_part {
        flex-grow: 1;

        .header {
            display: flex;
            align-items: center;
            .image {
                width: 200px;
                height: 150px;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .text {
                display: flex;
                flex-direction: column;
            }

        }
        [data-vue-app] {
            min-height: unset;
        }
        .controls {
            background-color: var(--color--fieldset-header);
            padding: 5px;
            display: flex;
            justify-content: space-between;
        }
        .keywords {
            margin-top: 10px;
            margin-bottom: 10px;
            padding-left: 25px;
        }
        .description {
            margin-top: 10px;
            padding: 0 1em;
        }
    }
    .screenshots {
        margin-left: 10px;
        max-width: 270px;
        li {
            margin-top: 20px;
            margin-bottom: 20px;
            img {
                display: block;
                width: 100%;
                border: solid thin var(--color--tile-border);
            }
        }

    }
}
</style>
