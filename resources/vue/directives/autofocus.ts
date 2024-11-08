// Shamelessly copied from https://github.com/byteboomers/vue-autofocus-directive

function focusElement(el: HTMLElement, binding: any) : void {
    if (binding.value && !binding.value) {
        return;
    }

    el.focus()
}

export default {
    bind(el: HTMLElement, binding: any, vnode: any) {
        if (vnode.componentInstance?.focus instanceof Function) {
            // When the component itself has a focus method
            vnode.componentInstance.focus();
        } else {
            // When the component of the element gets activated
            vnode.context.$on('hook:activated', () => focusElement(el, binding));
        }

    },
    inserted: focusElement
}
