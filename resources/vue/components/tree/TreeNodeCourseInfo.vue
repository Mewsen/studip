<template>
    <div class="studip-tree-child-description">
        <studip-loading-skeleton v-if="isLoading" />
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
        return {
            courseCount: this.getCachedNodeCourseInfo(this.node, this.semester, this.semClass),
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
