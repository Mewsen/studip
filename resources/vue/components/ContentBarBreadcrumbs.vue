<template>
    <ul>
        <li v-for="(breadcrumb, index) in breadcrumbs" class="cw-ribbon-breadcrumb-item" :key="index">
            <span v-if="breadcrumb.active">{{ breadcrumb.title }}</span>
            <a v-else :href="breadcrumb.url">{{ breadcrumb.title }}</a>
        </li>
    </ul>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';
import { TOCItem, traverse } from './table-of-contents';

interface Breadcrumb {
    title: string;
    url: string;
    active: boolean;
}

export default defineComponent({
    name: 'ContentBarBreadcrumbs',
    props: {
        // The table of contents tree for the page that is currently open.
        toc: {
            type: Object as PropType<TOCItem>,
            required: true,
        },
    },
    computed: {
        // Convert the nested TOCItem into a list of breadcrumbs we can iterate through
        // in the template.
        breadcrumbs(): Breadcrumb[] {
            // First, clone the toc and add parent references to it.
            // (The parent references are lost in serialization from PHP to JS.)
            const tocClone = JSON.parse(JSON.stringify(this.toc));
            this.addParentReferences(tocClone);

            // Then, find the TOCItem corresponding to the page that is currently open.
            const activeTocItem = this.findActiveTocItem(tocClone);
            if (!activeTocItem) {
                console.error('No TOCItem is marked as active. No breadcrumbs will be rendered.');
                return [];
            }

            // Finally, iterate upwards from the active TOC Item, through its parent, grandparent, ...
            // up to the root, generating a breadcrumb at each step of the way.
            const breadcrumbs = [{ title: activeTocItem.title, url: activeTocItem.url, active: true }];
            let current = activeTocItem;
            while (current.parent) {
                current = current.parent;
                breadcrumbs.push({ title: current.title, url: current.url, active: false });
            }
            return breadcrumbs.reverse();
        },
    },
    methods: {
        // Find the TOCItem, if any, that is marked as active in the given toc tree.
        findActiveTocItem(toc: TOCItem): TOCItem | undefined {
            let activeItem: TOCItem | undefined;
            traverse(toc, (item) => {
                if (item.active) {
                    activeItem = item;
                }
            });
            return activeItem;
        },
        // Augment each node in the given toc tree with a reference to its parent.
        addParentReferences(tocItem: TOCItem, parent?: TOCItem): void {
            if (parent) {
                tocItem.parent = parent;
            }
            for (let child of tocItem.children) {
                this.addParentReferences(child, tocItem);
            }
        },
    },
});
</script>
