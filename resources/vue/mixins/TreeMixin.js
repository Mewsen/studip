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
            currentNode: null,
            showProgressIndicatorTimeout: 500
        };
    },
    computed: {
        ...mapState('treestore', {
            semester: 'semesterId',
            semClass: 'semClass',
            limit: 'courseLimit',
            viewType: 'viewType',
        }),
        ...mapGetters('treestore', [
            'getNodeCourseInfo',
            'getNodeCoursesTotal',
        ]),

        totalCourseCount() {
            return this.getNodeCoursesTotal(this.currentNode?.id);
        }
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

        pushState(state, parameters = {}) {
            const url = new URL(location.href);
            Object.entries(parameters).forEach(([key, value]) => {
                if (value === null && url.searchParams.has(key)) {
                    url.searchParams.delete(key);
                } else {
                    url.searchParams.set(key, value);
                }
            });
            window.history.pushState(state, '', url);
        },

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
            }

            // Update browser history.
            if (pushState) {
                this.pushState(
                    {nodeId: node.id},
                    {node_id: node.id},
                );
            }

            // Update node_id for semester selector.
            const semesterSelector = document.querySelector('#semester-selector-node-id');
            semesterSelector.value = node.id;
        },
        nodeUrl(node_id, semester = null ) {
            return STUDIP.URLHelper.getURL('', { node_id, semester })
        },
        profileUrl(username) {
            return STUDIP.URLHelper.getURL('dispatch.php/profile', { username })
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
        async updateOffset(page) {
            this.courses = await this.fetchNodeCourses({
                id: this.currentNode.id,
                page
            });
        }
    }
}
