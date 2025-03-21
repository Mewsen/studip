<template>
    <div>
        <div v-if="!isSearching"
             class="studip-tree"
             :class="{'studip-tree-navigatable': showStructureAsNavigation}"
        >
            <studip-progress-indicator v-if="isLoading" :size="48"></studip-progress-indicator>

            <component :is="viewComponent"
                       :with-children="withChildren"
                       :visible-children-only="visibleChildrenOnly"
                       :with-courses="withCourses"
                       :node="startNode"
                       :breadcrumb-icon="breadcrumbIcon"
                       :editable="editable"
                       :edit-url="editUrl"
                       :create-url="createUrl"
                       :delete-url="deleteUrl"
                       :with-export="withExport"
                       :show-structure-as-navigation="showStructureAsNavigation"
                       :assignable="assignable"
                       :with-course-assign="withCourseAssign"
                       @change-current-node="changeCurrentNode"
            ></component>
        </div>
        <div v-else class="studip-tree">
            <tree-search-result :search-config="searchConfig"></tree-search-result>
        </div>
        <Teleport v-if="withSearch" to="#search-widget" name="sidebar-search">
            <search-widget v-if="currentNode" :min-length="3" ref="searchWidget"></search-widget>
        </Teleport>
        <Teleport v-if="!editable && !isSearching && !isLoading && currentNode"
                        to="#views-widget"
                        name="sidebar-views">
            <studip-tree-view-widget :config="viewConfig" />
        </Teleport>
    </div>
</template>

<script>
import axios from 'axios';
import { TreeMixin } from '../../mixins/TreeMixin';
import PageLayout from '../../../assets/javascripts/lib/page_layout';
import StudipProgressIndicator from '../StudipProgressIndicator.vue';
import SearchWidget from '../SearchWidget.vue';
import StudipTreeViewWidget from './StudipTreeViewWidget.vue';
import StudipTreeList from './StudipTreeList.vue';
import StudipTreeTable from './StudipTreeTable.vue';
import StudipTreeNode from './StudipTreeNode.vue';
import TreeSearchResult from './TreeSearchResult.vue';

export default {
    name: 'StudipTree',
    components: {
        TreeSearchResult,
        SearchWidget,
        StudipTreeViewWidget,
        StudipProgressIndicator,
        StudipTreeList,
        StudipTreeTable,
        StudipTreeNode
    },
    mixins: [ TreeMixin ],
    props: {
        treeId: {
            type: String,
            default: ''
        },
        startId: {
            type: String,
            required: true
        },
        title: {
            type: String,
            default: ''
        },
        openNodes: {
            type: Array,
            default: () => []
        },
        openLevels: {
            type: Number,
            default: 0
        },
        withChildren: {
            type: Boolean,
            default: true
        },
        withInfo: {
            type: Boolean,
            default: true
        },
        visibleChildrenOnly: {
            type: Boolean,
            default: true
        },
        withCourses: {
            type: Boolean,
            default: false
        },
        semester: {
            type: String,
            default: ''
        },
        semClass: {
            type: Number,
            default: 0
        },
        breadcrumbIcon: {
            type: String,
            default: 'literature'
        },
        itemIcon: {
            type: String,
            default: 'literature'
        },
        withSearch: {
            type: Boolean,
            default: false
        },
        withExport: {
            type: Boolean,
            default: false
        },
        withCourseAssign: {
            type: Boolean,
            default: false
        },
        editable: {
            type: Boolean,
            default: false
        },
        editUrl: {
            type: String,
            default: ''
        },
        createUrl: {
            type: String,
            default: ''
        },
        deleteUrl: {
            type: String,
            default: ''
        },
        showStructureAsNavigation: {
            type: Boolean,
            default: false
        },
        assignable: {
            type: Boolean,
            default: false
        },
        assignLeavesOnly: {
            type: Boolean,
            default: false
        },
        notAssignableNodes: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            nodeId: this.startId,
            startNode: null,
            currentNode: this.startNode,
            loaded: false,
            isLoading: false,
            showStructuralNavigation: false,
            searchConfig: {},
            isSearching: false,
            pageTitle: document.title
        }
    },
    computed: {
        viewComponent() {
            if (this.startNode && this.viewType === 'list') {
                return StudipTreeList;
            }

            if (this.startNode && this.viewType === 'table') {
                return StudipTreeTable;
            }

            return null;
        },
        viewConfig() {
            return {
                view: this.viewType,
                node: this.currentNode,
                semester: this.semester,
                semClass: this.semClass
            }
        }
    },
    methods: {
        changeCurrentNode(node) {
            this.currentNode = node;
            this.setPageTitle(this.currentNode.attributes.name);
            this.$nextTick(() => {
                document.getElementById('tree-breadcrumb-' + node.attributes.id)?.focus();
            });
        },
        exportUrl() {
            return STUDIP.URLHelper.getURL('dispatch.php/tree/export_csv');
        },
        injectSearchterm(targetId, searchterm) {
            const form = document.getElementById(targetId).querySelector('form');
            let input = form.querySelector('input[type="hidden"][name="search"]');
            if (!input) {
                input = document.createElement('input');
                input.setAttribute('id', `${targetId}-searchterm`);
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', 'search');
                form.appendChild(input);
            }
            input.setAttribute('value', searchterm);
        },
        setPageTitle(nodeTitle) {
            const title = this.pageTitle.split('-');
            PageLayout.title = title.slice(0, -1).join('-') + '/ ' + nodeTitle + ' -' + title[title.length - 1];
        }
    },
    mounted() {
        window.focus();

        const loadingIndicator = axios.interceptors.request.use(config => {
            setTimeout(() => {
                if (!this.loaded) {
                    this.isLoading = true;
                }
            }, this.showProgressIndicatorTimeout);
            return config;
        });

        this.fetchNode(this.startId).then(node => {
            this.startNode = node;
            this.currentNode = this.startNode;
            this.loaded = true;
            this.isLoading = false;
            this.setPageTitle(this.currentNode.attributes.name);
        });

        axios.interceptors.request.eject(loadingIndicator);

        this.globalOn('do-search', searchterm => {
            this.searchConfig = {
                searchterm,
                semester: this.semester,
                semclass: this.semClass,
                classname: this.startNode.attributes.classname,
                startId: this.currentNode.id,
            };
            this.injectSearchterm('semester-selector', searchterm);
            this.injectSearchterm('semclass-selector', searchterm);
            this.isSearching = true;
        });

        this.globalOn('cancel-search', () => {
            this.searchConfig = {};
            this.searchterm = '';
            document.getElementById('semester-selector-searchterm')?.remove();
            document.getElementById('semclass-selector-searchterm')?.remove();
            this.isSearching = false;
        });
    }
}
</script>
