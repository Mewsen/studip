<template>
    <div class="input-array">
        <span aria-live="assertive" class="sr-only">{{ assistiveLive }}</span>

        <table class="default nohover">
            <colgroup>
                <col style="width: 16px">
                <col>
                <col v-for="i in additionalColspan" :key="`colspan-${i}`">
                <col style="width: 24px">
            </colgroup>
            <thead>
                <tr>
                    <th class="dragcolumn"></th>
                    <th>{{ labelPlural }}</th>
                    <slot name="header-cells" />
                    <th class="actions"></th>
                </tr>
            </thead>
            <Draggable v-model="options"
                       item-key="index"
                       handle=".dragarea"
                       tag="tbody"
                       class="statements"
            >
                <template #item="{element, index}">
                    <tr>
                        <td class="dragcolumn">
                            <a class="dragarea"
                               tabindex="0"
                               :title="$gettext(`Sortierelement für %{label} %{option}. Drücken Sie die Tasten Pfeil-nach-oben oder Pfeil-nach-unten, um dieses Element in der Liste zu verschieben.`, {option: element.value, label}, true)"
                               @keydown="keyHandler($event, index)"
                               :ref="`draghandle${index}`">
                                <span class="drag-handle"></span>
                            </a>
                        </td>
                        <td>
                            <input type="text"
                                   :ref="`inputs-${index}`"
                                   :placeholder="label"
                                   @paste="(ev) => onPaste(ev, index)"
                                   v-model="element.value">
                        </td>
                        <slot name="body-cells" />
                        <td class="actions">
                            <StudipIcon name="delete"
                                        shape="trash"
                                        @click.prevent="deleteOption(index)"
                                        :title="$gettext('%{label} löschen', {label: element.value}, true)"
                            />
                        </td>
                    </tr>
                </template>
            </Draggable>
            <tfoot>
                <tr>
                    <td :colspan="3 + additionalColspan">
                        <button class="as-link"
                                :title="$gettext('%{label} hinzufügen',  {label}, true)"
                                @click.prevent="addOption()">
                            <StudipIcon shape="add" alt="" />
                        </button>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</template>

<script>
import Draggable from 'vuedraggable';
import { $gettext } from '../../../assets/javascripts/lib/gettext';

export default {
    name: 'input-array',
    emits: ['update:modelValue'],
    components: { Draggable },
    props: {
        additionalColspan: {
            type: Number,
            default: 0,
        },
        label: {
            type: String,
            default: $gettext('Option'),
        },
        labelPlural: {
            type: String,
            default: $gettext('Optionen'),
        },
        modelValue: Array,
    },
    data() {
        return {
            assistiveLive: '',
            options: this.modelValue.map((element, index) => ({
                value: element,
                index: index,
            })),
        };
    },
    methods: {
        addOption(val = '', position = this.options.length) {
            this.options.splice(position, 0, {
                value: val.trim(),
                index: position,
            });

            this.$nextTick(() => {
                this.$refs[`inputs-${position}`].focus();
            });
        },
        deleteOption(index) {
            const question = this.options[index] ? this.$gettext('Wirklich löschen?') : true;
            STUDIP.Dialog.confirm(question).done(() => {
                this.options.splice(index, 1);
            });
        },
        onPaste(ev, position) {
            ev.clipboardData
                .getData('text')
                .split("\n")
                .filter(str => str.trim().length > 0)
                .forEach((value, index) => this.addOption(value, position + index));
            ev.preventDefault();
        },
        keyHandler(e, index) {
            if (e.keyCode !== 38 && e.keyCode !== 40) {
                return;
            }

            e.preventDefault();

            const moveUp = e.keyCode === 38;

            this.moveElement(index, moveUp ? -1 : 1).then((newIndex) => {
                if (newIndex === false) {
                    return;
                }

                this.assistiveLive = this.$gettext(
                    'Aktuelle Position in der Liste: %{pos} von %{listLength}.',
                    {pos: newIndex + 1, listLength: this.options.length}
                );

                this.$nextTick(() => {
                    this.$refs[`draghandle${newIndex}`].focus();
                });
            })
        },
        moveElement(index, direction) {
            if (this.options[index + direction] === undefined) {
                return Promise.resolve(false);
            }

            const indices = [index, index + direction].sort();

            [this.options[indices[0]], this.options[indices[1]]] = [this.options[indices[1]], this.options[indices[0]]];
            this.options = [...this.options];

            return Promise.resolve(index + direction);
        },
    },
    watch: {
        options: {
            handler(current) {
                this.$emit('update:model-value', current.map(element => element.value));
            },
            deep: true,
        }
    }
}
</script>
<style scoped>
.input-array input[type="text"] {
    max-width: unset;
}
</style>
