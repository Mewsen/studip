<template>
    <div class="studip-tree-child-description">
        <studip-loading-skeleton v-if="isLoading" />
        <div v-else>
             <div v-html="$ngettext(
                 '<strong>%{count}</strong> Veranstaltung auf dieser Ebene',
                 '<strong>%{count}</strong> Veranstaltungen auf dieser Ebene',
                 courseCount,
                 { count: courseCount }
             )"></div>
        </div>
    </div>
</template>

<script>
import { TreeMixin } from '../../mixins/TreeMixin';
import StudipLoadingSkeleton from '../StudipLoadingSkeleton.vue';
import {$ngettext} from "../../../assets/javascripts/lib/gettext";

export default {
    name: 'TreeNodeCourseInfo',
    components: { StudipLoadingSkeleton },
    mixins: [ TreeMixin ],
    emits: ['showAllCourses'],
    props: {
        node: {
            type: Object,
            required: true
        },
        semester: {
            type: String,
            default: 'all'
        },
        semClass: {
            type: Number,
            default: 0
        }
    },
    data() {
        const cachedCourseInfo = this.getCachedNodeCourseInfo(this.node.id, this.semester, this.semClass);

        return {
            allCourseCount: cachedCourseInfo.allCourses,
            courseCount: cachedCourseInfo.courses,
            showingAllCourses: false
        }
    },
    computed: {
        isLoading() {
            return this.courseCount === null;
        }
    },
    methods: {
        $ngettext,
        showAllCourses(state) {
            this.showingAllCourses = state;
            this.$emit('showAllCourses', state);
        },
        loadNodeInfo(node) {
            this.getNodeCourseInfo(node, this.semester, this.semClass)
                .then(info => {
                    this.courseCount = info?.data.courses ?? 0;
                });
        }
    },
    watch: {
        node: {
            handler(newNode) {
                this.loadNodeInfo(newNode);
            },
            immediate: true
        }
    }
}
</script>
