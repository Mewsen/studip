<template>
    <vSelect ref="select"
             v-bind="{...$props, ...selectAttrs}"
             :model-value="modelValue"
             @update:modelValue="value => updateValue(value)"
             :calculate-position="withPopper"
             class="studip-v-select"
             append-to-body
    >
        <template #open-indicator="{ attributes }">
            <span v-bind="attributes">
                <StudipIcon shape="arr_1down" :size="10" />
            </span>
        </template>
        <template v-for="(index, name) in $slots" v-slot:[name]="data">
            <slot :name="name" v-bind="data"></slot>
        </template>
    </vSelect>
</template>

<script>
import vSelect from 'vue-select';
import { createPopper } from '@popperjs/core'
import 'vue-select/dist/vue-select.css'
import StudipIcon from '@/vue/components/StudipIcon.vue';

export default {
    name: 'studip-select',
    emits: ['update:modelValue'],
    inheritAttrs: false,
    components: {
        StudipIcon,
        vSelect
    },
    props: {
        modelValue: [String, Number, Object, Array],
        maxHeight: {
            type: String,
            default: '12em'
        },
    },
    computed: {
        selectAttrs() {
            const attrs = {...this.$attrs};
            if (attrs.id !== undefined) {
                attrs.inputId = attrs.id;
                delete attrs.id;
            }
            return attrs;
        }
    },
    methods: {
        updateValue(val) {
            this.$emit('update:modelValue', val)
        },
        withPopper(dropdownList, component, { width }) {
            if (component.$el?.offsetParent.classList.contains('studip-dialog-content')) {
                dropdownList.classList.add('studip-v-select-ul-dialog');
            }
            dropdownList.style.width = width
            dropdownList.style.maxHeight = this.maxHeight;
            dropdownList.classList.add('studip-v-select-detachted-ul');
            let dropdownListHeight = parseFloat(this.getStyleValue(dropdownList, 'height')) +
                parseFloat(this.getStyleValue(dropdownList, 'paddingTop')) +
                parseFloat(this.getStyleValue(dropdownList, 'paddingBottom'));
            const popper = createPopper(component.$refs.toggle, dropdownList, {
                placement: this.calculatePlacement(dropdownListHeight),
                modifiers: [
                    {
                        name: 'offset',
                        options: {
                            offset: [0, -1],
                        },
                    },
                    {
                        name: 'toggleClass',
                        enabled: true,
                        phase: 'write',
                        fn({ state }) {
                            component.$refs.dropdownMenu.classList.toggle(
                                'studip-v-select-ul-drop-up',
                                state.placement === 'top'
                            )
                            component.$el.classList.toggle(
                                'studip-v-select-drop-up',
                                state.placement === 'top'
                            )
                        },
                    },
                ],
            })
            return () => popper.destroy()
        },
        calculatePlacement(dropdownListHeight) {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            let selectBottom = Math.ceil(
                this.$refs.select.$el.getBoundingClientRect().bottom + scrollTop
            );
            let totalExpandedList = selectBottom + dropdownListHeight;
            let totalDocHeight = Math.max(
                document.body.scrollHeight,
                document.body.offsetHeight,
                document.documentElement.clientHeight,
                document.documentElement.scrollHeight,
                document.documentElement.offsetHeight
            );
            let footerHeight = document.getElementById('main-footer').offsetHeight;
            let functionalAreaHeight = totalDocHeight - footerHeight;
            return totalExpandedList >= functionalAreaHeight ? 'top' : 'bottom';
        },
        getStyleValue(element, styleProp) {
            let result = '';
            if (window.getComputedStyle) {
                result = getComputedStyle(element)[styleProp];
            } else if (element.currentStyle) {
                result = element.currentStyle[styleProp];
            }
            return result;
        }
    }
};
</script>
<style>
.studip-v-select .vs__dropdown-toggle {
    max-height: fit-content;
}
</style>
