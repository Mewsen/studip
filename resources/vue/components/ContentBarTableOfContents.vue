<template>
    <div class="contentbar-button-wrapper contentbar-toc-wrapper">
        <button v-if="!tocOpen"
                class="cw-ribbon-button cw-ribbon-button-menu"
                :title="$gettext('Inhaltsverzeichnis öffnen')"
                @click.prevent="showTOC(true)"></button>
        <transition name="cw-ribbon-slide" appear>
            <article v-if="tocOpen" id="toc">
                <header id="toc_header">
                    <h1 id="toc_h1">
                        {{ $gettextInterpolate('Inhalt (%{count} Elemente)', { count: tocItemsCount }) }}
                    </h1>
                    <button class="toc-hide-button"
                            :title="$gettext('Inhaltsverzeichnis schließen')"
                            @click.prevent="showTOC(false)"></button>
                </header>
                <section>
                    <ul class="toc">
                        <ContentBarTocItemList :toc="toc" />
                    </ul>
                </section>
            </article>
        </transition>
    </div>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';
import { TOCItem, traverse } from './table-of-contents';
import ContentBarTocItemList from './ContentBarTocItemList.vue';

export default defineComponent({
    name: 'ContentBarTableOfContents',
    components: { ContentBarTocItemList },
    props: {
        toc: {
            required: true,
            type: Object as PropType<TOCItem>,
        },
    },
    data() {
        return {
            tocOpen: false
        }
    },
    computed: {
        tocItemsCount(): number {
            if (!this.toc) {
                return 0;
            }
            // Count how many items are in the TOC tree.
            let count = 0;
            traverse(this.toc, (item) => count++);
            return count;
        },
    },
    methods: {
        showTOC(state = true) {
            this.tocOpen = state;
        }
    }
});
</script>
