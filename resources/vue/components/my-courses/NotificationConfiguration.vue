<template>
    <form :action="storeUrl" method="post" @submit="secured = false">
        <input type="hidden" :name="csrf.name" :value="csrf.value">
        <input v-for="course in courses"
               :key="`hidden-input-${course.id}`"
               type="hidden"
               name="course_ids[]"
               :value="course.id">

        <table class="default">
            <caption>{{ $gettext('Benachrichtigung über neue Inhalte anpassen') }}</caption>
            <colgroup>
                <col style="width: 1px">
                <col style="width: 100%">
                <col v-for="module in modules" style="width: 20px" :key="`module-col-${module.id}`">
                <col style="width: 20px">
            </colgroup>
            <thead>
                <tr>
                    <th colspan="2">{{ $gettext('Veranstaltung') }}</th>
                    <th v-for="module in modules" :key="`module-header-${module.id}`">
                        <studip-icon :shape="module.icon.shape"
                                     role="info"
                                     :title="module.name"
                        ></studip-icon>
                    </th>
                    <th>{{ $gettext('Alle') }}</th>
                </tr>
                <tr>
                    <td colspan="2">
                        {{ $gettext('Benachrichtigungen für die folgenden Veranstaltungen:') }}
                    </td>
                    <td v-for="module in modules" :key="`module-icon-${module.id}`">
                        <input type="checkbox"
                               v-bind.prop="checkedAllColumns(module)"
                               @click="toggleColumn(module)">
                    </td>
                    <td>
                        <input type="checkbox"
                               v-bind.prop="checkedAll()"
                               @click="toggleAll()"
                        >
                    </td>
                </tr>
            </thead>
            <tbody v-for="group in groups"
                   :key="`group-${group.id}`"
            >
                <tr>
                    <th :colspan="3 + modules.length">{{ group.name }}</th>
                </tr>
                <tr v-for="course in getCoursesForGroup(group)" :key="`course-${course.id}`">
                    <td :class="`gruppe${course.group}`"></td>
                    <td>
                        <a :href="getCourseURL(course)">
                            {{ getCourseName(course) }}
                        </a>
                    </td>
                    <td v-for="module in modules" :key="`course-${course.id}-module-${module.id}`">
                        <input type="checkbox"
                               :name="`notifications[${course.id}][]`"
                               :value="module.id"
                               v-model="courseModules[course.id]">
                    </td>
                    <td>
                        <input type="checkbox"
                               :value="course.id"
                               v-bind.prop="checkedAllRows(course)"
                               @click="toggleRow(course)">
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td :colspan="3 + modules.length">
                        <button type="submit" class="button accept" :title="$gettext('Änderungen übernehmen')">
                            {{ $gettext('Speichern') }}
                        </button>
                        <button type="reset" class="button" @click.prevent="reset()">
                            {{ $gettext('Zurücksetzen') }}
                        </button>
                    </td>
                </tr>
            </tfoot>
        </table>
    </form>
</template>
<script>
import {mapState} from "vuex";
import { createMixin } from "../../mixins/MyCoursesMixin";

export default {
    name: 'MyCoursesNotificationConfiguration',
    mixins: [
        createMixin()
    ],
    props: {
        storeUrl: String,
        modules: Object,
        notifications: Object,
    },
    data() {
        return {
            courseModules: {},
            secured: true,
        }
    },
    computed: {
        ...mapState('mycourses', [
            'courses',
            'groups',
            'config',
        ]),

        isChanged() {
            return Object.entries(this.courseModules).some(([id, notifications]) => {
                const original = this.notifications[id].toSorted();
                const current = notifications.toSorted();
                return JSON.stringify(original) !== JSON.stringify(current);
            });
        }
    },
    methods: {
        checkedAll() {
            const allCount = this.modules.length;
            const checkedCount = this.modules.filter(module => this.checkedAllColumns(module).checked).length;
            const indeterminateCount = this.modules.filter(module => this.checkedAllColumns(module).indeterminate).length;
            return {
                checked: checkedCount === allCount,
                indeterminate: checkedCount !== allCount && indeterminateCount > 0,
            };
        },
        checkedAllColumns(module) {
            const allCount = Object.keys(this.courseModules).length;
            const checkedCount = Object.values(this.courseModules).filter(modules => modules.includes(module.id)).length;
            return {
                checked: checkedCount === allCount,
                indeterminate: checkedCount > 0 && checkedCount < allCount,
            };
        },
        checkedAllRows(course) {
            const allCount = this.modules.length;
            const checkedCount = this.courseModules[course.id].length;
            return {
                checked: checkedCount === allCount,
                indeterminate: checkedCount > 0 && checkedCount < allCount,
            };
        },
        getCoursesForGroup(group) {
            const ids = group.data.map(item => item.ids).flat();
            return this.getCourses(ids);
        },
        reset() {
            // We need to copy the values instead we would work on the original
            // data and could never detect any changes
            this.courseModules = Object.keys(this.courses).reduce(
                (carry, id) => {
                    carry[id] = this.notifications[id]?.concat() ?? [];
                    return carry;
                },
                {}
            );
        },
        securityHandler(event) {
            if (!this.isChanged || !this.secured) {
                return;
            }

            event.preventDefault();
        },
        toggleAll() {
            const value = this.checkedAll().checked ? [] : this.modules.map(m => m.id);
            Object.keys(this.courses).forEach(courseId => {
                this.courseModules[courseId] = value;
            });
        },
        toggleColumn(module) {
            const disable = this.checkedAllColumns(module).checked;
            Object.entries(this.courseModules).forEach(([courseId, modules]) => {
                if (disable && this.courseModules[courseId].includes(module.id)) {
                    this.courseModules[courseId].splice(
                        this.courseModules[courseId].indexOf(module.id),
                        1
                    );
                } else if (!disable && !modules.includes(module.id)) {
                    this.courseModules[courseId].push(module.id);
                }
            });
        },
        toggleRow(course) {
            if (this.checkedAllRows(course).checked) {
                this.courseModules[course.id] = [];
            } else {
                this.courseModules[course.id] = Object.values(this.modules).map(m => m.id);
            }
        }
    },
    created() {
        this.reset();

        window.addEventListener('beforeunload', this.securityHandler);
    },
    beforeUnmount() {
        window.removeEventListener('beforeunload', this.securityHandler);
    }
};
</script>
<style lang="scss" scoped>
table.default {
    tbody td:first-child {
        padding-left: 0;
    }
    thead td:last-child,
    tbody td:last-child {
        border-left: 1px solid var(--color--table-border);
    }
}
</style>
