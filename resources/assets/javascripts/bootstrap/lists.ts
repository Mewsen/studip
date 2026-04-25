import CollapsibleList from "../lib/collapsible-list";
import {ready} from "../lib/ready";

ready(() => {
    document.querySelectorAll('.collapsible-list')
        .forEach((node: Element) => CollapsibleList.fromNode(node as HTMLElement));
});
