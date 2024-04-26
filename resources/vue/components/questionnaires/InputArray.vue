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
            <Draggable v-model="options" handle=".dragarea" tag="tbody" class="statements">
                <tr v-for="(option, index) in options" :key="index">
                    <td class="dragcolumn">
                        <a class="dragarea"
                           tabindex="0"
                           :title="$gettextInterpolate($gettext(`Sortierelement für %{label} %{option}. Drücken Sie die Tasten Pfeil-nach-oben oder Pfeil-nach-unten, um dieses Element in der Liste zu verschieben.`), {option, label})"
                           @keydown="keyHandler($event, index)"
                           ref="draghandle">
                            <span class="drag-handle"></span>
                        </a>
                    </td>
                    <td>
                        <input type="text"
                               ref="inputs"
                               :placeholder="label"
                               @paste="(ev) => onPaste(ev, index)"
                               v-model="options[index]">
                    </td>
                    <slot name="body-cells" />
                    <td class="actions">
                        <StudipIcon name="delete"
                                     shape="trash"
                                     :size="20"
                                     @click.prevent="deleteOption(index)"
                                     :title="$gettextInterpolate($gettext('%{label} löschen'), {label})"
                        />
                    </td>
                </tr>
            </Draggable>
            <tfoot>
                <tr>
                    <td :colspan="3 + additionalColspan">
                        <button class="as-link"
                                :title="$gettextInterpolate($gettext('%{label} hinzufügen'),  {label})"
                                @click.prevent="addOption()">
                            <StudipIcon shape="add" :size="20" alt="" />
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
        value: Array,
    },
    data() {
        return {
            options: [],
            assistiveLive: '',
        };
    },
    methods: {
        addOption(val = '', position = this.options.length) {
            this.$set(this.options, position, val.trim());

            this.$nextTick(() => {
                this.$refs.inputs[position].focus();
            });
        },
        deleteOption(index) {
            const question = this.options[index] ? this.$gettext('Wirklich löschen?') : true;
            STUDIP.Dialog.confirm(question).done(() => {
                this.$delete(this.options, index);
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
                this.assistiveLive = this.$gettextInterpolate(
                    this.$gettext('Aktuelle Position in der Liste: %{pos} von %{listLength}.'),
                    {pos: newIndex + 1, listLength: this.options.length}
                );

                this.$nextTick(() => {
                    this.$refs['draghandle'][newIndex].focus();
                });
            })
        },
        moveElement(index, direction) {
            if (this.options[index + direction] === undefined) {
                return Promise.resolve(index);
            }

            const indices = [index, index + direction].sort();

            this.options.splice(
                Math.min(...indices),
                2,
                ...indices.reverse().map(idx => this.options[idx])
            );

            return Promise.resolve(index + direction);
        }
    },
    watch: {
        options: {
            handler(current) {
                this.$emit('input', current);
            },
            deep: true
        },
        value: {
            handler(current) {
                this.options = current;
            },
            immediate: true
        }
    }
}
</script>
<style scoped>
.input-array input[type="text"] {
    max-width: unset;
}
</style>
