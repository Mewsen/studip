<template>
    <tr v-bind="$attrs" class="studip-tree-child">
        <td>
            <a v-if="editable && children.length > 1" class="drag-link" role="option"
               tabindex="0"
               :title="$gettext('Sortierelement für Element %{node}. Drücken Sie die Tasten Pfeil-nach-oben oder Pfeil-nach-unten, um dieses Element in der Liste zu verschieben.', {node: element.attributes.name}, true)"
               @keydown="keyHandler($event, index)"
               :ref="'draghandle-' + index">
                <span class="drag-handle"></span>
            </a>
        </td>
        <td>
            <a :href="nodeUrl(element.id, semester !== 'all' ? semester : null)" tabindex="0"
               @click.prevent="$emit('open:node', element)"
               :title="$gettext(
                   'Unterebene %{ node } öffnen',
                   { node: node.attributes.name },
                   true
               )"
            >
                <studip-icon :shape="element.attributes['has-children'] ? 'folder-full' : 'folder-empty'"
                             :size="26"></studip-icon>
            </a>
        </td>
        <td>
            <a :href="nodeUrl(element.id, semester !== 'all' ? semester : null)" tabindex="0"
               @click.prevent="$emit('open:node', element)"
               :title="$gettext(
                   'Unterebene %{ node } öffnen',
                   { node: node.attributes.name },
                   true
               )"
            >
                {{ element.attributes.name }}
            </a>
        </td>
        <td>
            <tree-node-course-info v-if="node.attributes.ancestors.length > 2"
                                   :node="element"
                                   :semester="semester"
                                   :sem-class="semClass"
            ></tree-node-course-info>
        </td>
    </tr>
</template>
<script>
import StudipIcon from "../StudipIcon.vue";
import TreeNodeCourseInfo from "./TreeNodeCourseInfo.vue";
import {TreeMixin} from "../../mixins/TreeMixin";

export default {
    name: 'studip-tree-table-rows',
    components: { StudipIcon, TreeNodeCourseInfo},
    emits: ['open:node'],
    mixins: [ TreeMixin ],
    props: {
        children: Array,
        editable: Boolean,
        element: Object,
        index: Number,
        semester: String,
        semClass: Number,
        node: Object,
    }
};
</script>
