// useSlotFilter.js
import { isVNode, Comment } from 'vue';

export function useSlotFilter(slots) {
  const getValidChildren = (componentName) => {
    const rawNodes = slots.default ? slots.default() : [];

    return rawNodes.filter(vnode => {
      if (!isVNode(vnode) || vnode.type === Comment) return false;
      
      const isCorrectComponent = vnode.type?.name === componentName || 
                                  vnode.type?.__name === componentName;

      if (!isCorrectComponent) {
        if (vnode.type !== Symbol.for('v-fgt')) {
          console.warn(`[UI Kit] ButtonGroup: Element vom Typ "${vnode.type?.name || vnode.type}" wurde entfernt.`);
        }
        return false;
      }
      return true;
    });
  };

  return { getValidChildren };
}