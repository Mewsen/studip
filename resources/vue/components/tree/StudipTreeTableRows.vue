<template>
    <tr v-bind="$attrs" class="studip-tree-child">
        <td>
            <a v-if="editable && children.length > 1" class="drag-link" role="option"
               tabindex="0"
               :title="$gettextInterpolate($gettext('Sortierelement für Element %{node}. Drücken Sie die Tasten Pfeil-nach-oben oder Pfeil-nach-unten, um dieses Element in der Liste zu verschieben.'), {node: element.attributes.name}, true)"
               @keydown="keyHandler($event, index)"
               :ref="'draghandle-' + index">
                <span class="drag-handle"></span>
            </a>
        </td>
        <td>
            <a :href="nodeUrl(element.id, semester !== 'all' ? semester : null)" tabindex="0"
               @click.prevent="$emit('open:node', element)"
               :title="$gettextInterpolate($gettext('Unterebene %{ node } öffnen'),
                                    { node: node.attributes.name }, true)">
                <studip-icon :shape="element.attributes['has-children'] ? 'folder-full' : 'folder-empty'"
                             :size="26"></studip-icon>
            </a>
        </td>
        <td>
            <a :href="nodeUrl(element.id, semester !== 'all' ? semester : null)" tabindex="0"
               @click.prevent="$emit('open:node', element)"
               :title="$gettextInterpolate($gettext('Unterebene %{ node } öffnen'),
                                    { node: node.attributes.name }, true)">
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
    <tr v-for="(course) in courses" :key="course.id" class="studip-tree-child studip-tree-course">
        <td></td>
        <td>
            <studip-icon shape="seminar" :size="26"></studip-icon>
        </td>
        <td>
            <a :href="courseUrl(course.id)" tabindex="0"
               :title="$gettextInterpolate(
                                   $gettext('Zur Veranstaltung %{ title }'),
                                   { title: course.attributes.title },
                                   true
                               )">
                <template v-if="course.attributes['course-number']">
                    {{ course.attributes['course-number'] }}
                </template>
                {{ course.attributes.title }}
            </a>
            <div :id="'course-dates-' + course.id" class="course-dates"></div>
        </td>
        <td :colspan="editable ? 2 : null">
            <tree-course-details :course="course.id"></tree-course-details>
        </td>
    </tr>
</template>
<script>
import TreeCourseDetails from "./TreeCourseDetails.vue";
import StudipIcon from "../StudipIcon.vue";
import TreeNodeCourseInfo from "./TreeNodeCourseInfo.vue";
import {TreeMixin} from "../../mixins/TreeMixin";

export default {
    name: 'studip-tree-table-rows',
    components: {TreeCourseDetails, StudipIcon, TreeNodeCourseInfo},
    emits: ['open:node'],
    mixins: [ TreeMixin ],
    props: {
        children: Array,
        courses: Array,
        editable: Boolean,
        element: Object,
        index: Number,
        semester: String,
        node: Object,
    }
};
</script>
