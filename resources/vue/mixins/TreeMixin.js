import axios from 'axios';
import {mapActions, mapGetters, mapMutations, mapState} from "vuex";

export const TreeMixin = {
    props: {
        visibleChildrenOnly: {
            type: Boolean,
            default: true
        },
    },
    data() {
        return {
            showProgressIndicatorTimeout: 500,
            totalCourseCount: 0,
            offset: 0
        };
    },
    computed: {
        ...mapState('treestore', {
            limit: 'courseLimit',
            viewType: 'viewType',
        }),
        ...mapGetters('treestore', [
            'getNodeCourseInfo',
        ]),
    },
    methods: {
        ...mapActions('treestore', [
            'fetchNode',
            'fetchNodeChildren',
            'fetchNodeCourseInfo',
            'fetchNodeCourses',
        ]),
        ...mapMutations('treestore', {
            initializeFromLocalStorage: 'INITIALIZE_FROM_LOCAL_STORAGE',
        }),

        async openNode(node, pushState = true) {
            this.currentNode = node;
            this.$emit('change-current-node', node);

            if (this.withChildren) {
                this.children = await this.fetchNodeChildren({
                    id: node.id,
                    visibleChildrenOnly: this.visibleChildrenOnly,
                });
            }

            if (this.withCourses) {
                this.courses = await this.fetchNodeCourses(node);
                    // .then(response => {
                    //     this.totalCourseCount = response.data.meta.page.total;
                    //     this.offset = Math.ceil(response.data.meta.page.offset / this.limit);
                    //     this.courses = response.data.data;
                    // });
            }

            // Update browser history.
            if (pushState) {
                const url = new URL(location.href);
                url.searchParams.set('node_id', node.id);
                window.history.pushState({nodeId: node.id}, '', url);
            }

            // Update node_id for semester selector.
            const semesterSelector = document.querySelector('#semester-selector-node-id');
            semesterSelector.value = node.id;
        },
        nodeUrl(node_id, semester = null ) {
            return STUDIP.URLHelper.getURL('', { node_id, semester })
        },
        courseUrl(courseId) {
            return STUDIP.URLHelper.getURL('dispatch.php/course/details/index/' + courseId)
        },
        profileUrl(username) {
            return STUDIP.URLHelper.getURL('dispatch.php/profile', { username })
        },
        exportUrl() {
            return STUDIP.URLHelper.getURL('dispatch.php/tree/export_csv');
        },
        editNode(editUrl, id) {
            STUDIP.Dialog.fromURL(
                editUrl + '/' + id,
                {
                    size: 'medium'
                }
            );
        },
        updateSorting(parentId, children) {
            let data = {};

            for (let i = 0 ; i < children.length ; i++) {
                data[children[i].attributes.id] = i;
            }

            const fd = new FormData();
            fd.append('sorting', JSON.stringify(data));
            axios.post(
                STUDIP.URLHelper.getURL('dispatch.php/admin/tree/sort/' + parentId),
                fd,
                { headers: { 'Content-Type': 'multipart/form-data' }}
            )
            .then(() => {
                STUDIP.Report.success(this.$gettext('Die Sortierung wurde geändert.'));
            })
            .catch(error => {
                STUDIP.Report.error(this.$gettext('Die Sortierung konnte nicht geändert werden.'), error);
            });
            STUDIP.Vue.emit('sort-tree-children', { parent: parentId, children: children });
        },
        updateOffset(newOffset) {
            this.getNodeCourses(this.currentNode, newOffset, this.semester, this.semClass, '', this.showingAllCourses)
                .then(courses => {
                    this.courseCount = courses.data.meta.page.total;
                    this.currentOffset = courses.data.meta.page.offset;
                    this.offset = newOffset;
                    this.courses = courses.data.data;
                });
        }
    },
    created() {
//        this.initializeFromLocalStorage();
    }
}
