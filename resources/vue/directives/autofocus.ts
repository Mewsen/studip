// Shamelessly copied from https://github.com/byteboomers/vue-autofocus-directive

import {DirectiveBinding} from "vue";

function focusElement(el: HTMLElement, binding: DirectiveBinding) : void {
    if (binding.value !== undefined && !binding.value) {
        return;
    }

    el.focus()
}

export default {
    mounted(el: HTMLElement, binding: DirectiveBinding) {
        // When the component of the element gets activated
        focusElement(el, binding);
    },
    inserted: focusElement
}
