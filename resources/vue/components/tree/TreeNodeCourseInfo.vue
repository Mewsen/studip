<template>
    <div class="studip-tree-child-description">
        <studip-loading-skeleton v-if="isLoading" />
        <div v-else v-translate="{ count: courseCount }" :translate-n="courseCount"
             translate-plural="<strong>%{count}</strong> Veranstaltungen">
            <strong>Eine</strong> Veranstaltung
        </div>
    </div>
</template>

<script>
import { TreeMixin } from '../../mixins/TreeMixin';
import StudipLoadingSkeleton from '../StudipLoadingSkeleton.vue';

export default {
    name: 'TreeNodeCourseInfo',
    components: { StudipLoadingSkeleton },
    mixins: [ TreeMixin ],
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
    mounted() {
        this.loadNodeInfo(this.node);
    },
    watch: {
        node(newNode) {
            this.loadNodeInfo(newNode);
        }
    }
}
</script>
