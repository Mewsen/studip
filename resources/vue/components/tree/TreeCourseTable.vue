<template>
    <table v-if="courses.length > 0"
           class="default studip-tree-courses-table"
    >
        <colgroup>
            <col>
            <col>
        </colgroup>
        <thead>
            <tr v-if="$slots.pagination">
                <td colspan="3">
                    <slot name="pagination"></slot>
                </td>
            </tr>
            <tr>
                <th>{{ $gettext('Name') }}</th>
                <th>{{ $gettext('Information') }}</th>
            </tr>
        </thead>
        <tbody role="listbox">
            <tr v-for="(course) in courses" :key="course.id" class="studip-tree-child studip-tree-course">
                <td>
                    <a :href="courseUrl(course.id)" tabindex="0"
                       :title="$gettext(
                               'Zur Veranstaltung %{ title }',
                               { title: course.attributes.title },
                               true
                           )"
                    >
                        <studip-icon shape="seminar" :size="26"></studip-icon>

                        <template v-if="course.attributes['course-number']">
                            {{ course.attributes['course-number'] }}
                        </template>
                        {{ course.attributes.title }}
                    </a>
                    <div :id="'course-dates-' + course.id" class="course-dates"></div>
                </td>
                <td>
                    <tree-course-details :course="course"></tree-course-details>
                </td>
            </tr>
        </tbody>
        <tfoot v-if="$slots.pagination">
            <tr>
                <td colspan="3">
                    <slot name="pagination"></slot>
                </td>
            </tr>
        </tfoot>
    </table>
</template>
<script>
import StudipIcon from "../StudipIcon.vue";
import TreeCourseDetails from "./TreeCourseDetails.vue";

export default {
    name: 'TreeCourseTable',
    components: {TreeCourseDetails, StudipIcon},
    props: {
        courses: Array,
    },
    methods: {
        courseUrl(courseId) {
            return STUDIP.URLHelper.getURL('dispatch.php/course/details/index/' + courseId)
        }
    }
}
</script>
