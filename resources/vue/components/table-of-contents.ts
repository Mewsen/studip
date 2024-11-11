import { Icon } from '../../studip';

// Corresponds to the PHP class TOCItem
export interface TOCItem {
    title: string;
    url: string;
    parent?: TOCItem;
    children: TOCItem[];
    active: boolean;
    icon?: Icon;
}

// Depth-first traversal of a TOCItem hierachy starting at its root.
export function traverse(tocRoot: TOCItem, callback: (item: TOCItem) => void) {
    callback(tocRoot);
    for (let tocItem of tocRoot.children) {
        traverse(tocItem, callback);
    }
}
