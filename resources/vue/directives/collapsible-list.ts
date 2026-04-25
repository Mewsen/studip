import {DirectiveBinding} from "vue";
import CollapsibleList from "../../assets/javascripts/lib/collapsible-list";

const queryAll = (context: HTMLElement, strict: boolean): Array<CollapsibleList> => {
    const lists = Array.from(context.querySelectorAll('ul,ol'));
    if (context.matches('ul,ol')) {
        lists.push(context);
    }

    return lists
        .filter(el => !strict || el.matches('.collapsible-list'))
        .map((el: Element): CollapsibleList => CollapsibleList.fromNode(el as HTMLElement));
}

export default {
    mounted(el: HTMLElement, binding: DirectiveBinding) {
        queryAll(el, binding.modifiers.strict ?? false);
    },
    updated(el: HTMLElement, binding: DirectiveBinding) {
        queryAll(el, binding.modifiers.strict ?? false);
    },
    unmounted(el: HTMLElement, binding: DirectiveBinding) {
        queryAll(el, binding.modifiers.strict ?? false).forEach(list => list.destroy());
    }
}
