<template>
    <div class="course-details">
        <div class="semester">
            ({{ course.attributes.semester }})
        </div>
        <div class="admission-state" v-if="course.attributes.admissionstate">
            <studip-icon :shape="course.attributes.admissionstate.icon"
                         :role="course.attributes.admissionstate.role"
                         :alt="course.attributes.admissionstate.info"></studip-icon>
        </div>
        <div class="course-lecturers">
            <span v-for="(lecturer, index) in course.attributes.lecturers" :key="index">
                <a :href="profileUrl(lecturer.username)"
                   :title="$gettext(
                       'Zum Profil von %{ user }',
                       { user: lecturer.name },
                       true
                   )"
                   tabindex="0">
                    {{ lecturer.name }}
                </a><template v-if="course.attributes.lecturers.length > 1 && index < course.attributes.lecturers.length - 1">, </template>
            </span>
        </div>
        <Teleport v-if="mounted" :to="'#course-dates-' + course.id" :append="true">
            <span v-html="course.attributes.dates"></span>
        </Teleport>
    </div>
</template>

<script>
import { TreeMixin } from '../../mixins/TreeMixin';

export default {
    name: 'TreeCourseDetails',
    mixins: [ TreeMixin ],
    props: {
        course: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            mounted: false
        };
    },
    mounted() {
        this.mounted = true;
    }
}
</script>
