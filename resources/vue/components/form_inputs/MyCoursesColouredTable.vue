<template>
    <div class="formpart">
        <label>
            {{ $gettext('Semester') }}
            <select v-if="with_semester_selector" :name="name + '_semester_id'"
                    @change="showSemesterCourses" v-model="semester_id">
                <option v-for="semester in available_semesters" :key="semester.id"
                        :value="semester.id">
                    {{ semester.name }}
                </option>
            </select>
        </label>
        <table class="default mycourses" :key="semester_id"
               v-for="(courses, semester_id) of visible_semester_courses">
            <caption>{{ available_semesters[semester_id].name }}</caption>
            <colgroup>
                <col style="width: 7px">
                <col style="width: 25px">
                <col style="width: 70px">
                <col>
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th>{{ $gettext('Nummer') }}</th>
                    <th>{{ $gettext('Name') }}</th>
                    <th class="actions">{{ $gettext('Auswahl') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="course of courses" :key="course.id">
                    <td :class="'gruppe' + course.course_type"></td>
                    <td>
                        <img :src="course.avatar_url" alt="" class="my-courses-avatar course-avatar-small">
                    </td>
                    <td>{{ course.number }}</td>
                    <td>{{ course.name }}</td>
                    <td class="actions">
                        <input type="hidden" :name="name + '_course_ids[' + course.id + ']'" value="0">
                        <input type="checkbox" :name="name + '_course_ids[' + course.id + ']'"
                               value="1" :checked="selected_course_id_list.includes(course.id)"
                               :title="$gettextInterpolate($gettext('%{course} auswählen'), {course: course.name})">
                    </td>
                </tr>
                <tr v-if="courses.length === 0">
                    <td colspan="4">
                        <studip-message-box>{{ $gettext('Im gewählten Semester stehen keine Veranstaltungen zur Auswahl zur Verfügung.') }}</studip-message-box>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import StudipMessageBox from "../StudipMessageBox.vue";

export default {
    name: 'my-courses-coloured-table',
    components: {StudipMessageBox},
    props: {
        default_semester_id: {
            type: String,
            required: true,
        },
        selected_course_ids: {
            type: Array,
            required: false,
            default: () => [],
        },
        name: {
            type: String,
            required: false,
            default: 'selected_course_ids',
        },
        semester_data: {
            type: Object,
            required: false,
            default: () => {},
        }
    },
    data() {
        //Retrieve all semesters, if the semester selector is present:
        let semester_data = this.semester_data;
        return {
            available_semesters: semester_data,
            semester_id: this.default_semester_id,
            semester_courses: {},
            visible_semester_courses: {},
            selected_course_id_list: [...this.selected_course_ids],
            with_semester_selector: (Object.keys(semester_data).length > 0)
        };
    },
    mounted() {
        if (this.semester_id) {
            this.showSemesterCourses(this.semester_id);
        }
    },
    methods: {
        showSemesterCourses(semester_id = undefined) {
            if (semester_id instanceof Object) {
                semester_id = semester_id.target.value;
            }
            if (!semester_id) {
                semester_id = this.semester_id.toString();
            }
            if (!semester_id) {
                return;
            }
            this.visible_semester_courses = {};
            if (this.semester_courses[semester_id] === undefined) {
                //The courses have not yet been retrieved.
                STUDIP.jsonapi.request(
                    'users/' + STUDIP.USER_ID + '/courses',
                    {
                        data: {
                            'fields[courses]': 'id,course-number,title,course-type',
                            'filter[semester]': semester_id
                        }
                    }
                ).done((response) => {
                    this.semester_courses[semester_id] = [];
                    let unsorted_courses = [];
                    for (let course_data of response.data) {
                        if (course_data.type !== 'courses') {
                            continue;
                        }
                        unsorted_courses.push({
                            id: course_data.id,
                            name: course_data.attributes.title,
                            number: course_data.attributes['course-number'],
                            course_type: course_data.attributes['course-type'],
                            avatar_url: course_data.meta.avatar.small
                        });
                    }
                    unsorted_courses.sort(function (a,b) {
                        if (a.name < b.name) {
                            return -1;
                        } else if (a.name > b.name) {
                            return 1;
                        } else {
                            if (a.number < b.number) {
                                return -1;
                            } else if (a.number > b.number) {
                                return 1;
                            } else {
                                return 0;
                            }
                        }
                    });
                    this.semester_courses[semester_id] = unsorted_courses;
                    this.visible_semester_courses[semester_id] = this.semester_courses[semester_id];
                    this.$forceUpdate();
                });
            } else {
                this.visible_semester_courses[semester_id] = this.semester_courses[semester_id];
            }
        }
    }
}
</script>
