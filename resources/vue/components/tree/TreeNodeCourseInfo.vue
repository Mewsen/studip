<template>
    <div class="studip-tree-child-description">
        <studip-loading-skeleton v-if="courseCount === null" />
        <div v-else
             v-html="$ngettext(
                 '<strong>Eine</strong> Veranstaltung',
                 '<strong>%{count}</strong> Veranstaltungen',
                 courseCount,
                 { count: courseCount }
             )"
        ></div>
    </div>
</template>

<script>
import { TreeMixin } from '../../mixins/TreeMixin';
import {$ngettext} from "../../../assets/javascripts/lib/gettext";
import StudipLoadingSkeleton from "../StudipLoadingSkeleton.vue";

export default {
    name: 'TreeNodeCourseInfo',
    components: {StudipLoadingSkeleton},
    mixins: [ TreeMixin ],
    props: {
        node: {
            type: Object,
            required: true
        },
    },
    data() {
        return {
            courseCount: null,
        }
    },
    methods: {
        $ngettext,
    },
    watch: {
        node: {
            async handler(newNode) {
                this.courseCount = await this.fetchNodeCourseInfo(newNode.id);
            },
            immediate:true
        }
    }
}
</script>
