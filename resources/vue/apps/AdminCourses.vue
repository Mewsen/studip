<template>
    <div class="admin-courses-wrapper">
        <form method="post">
            <input type="hidden" :name="csrf.name" :value="csrf.value">

            <table class="default course-admin" ref="table">
                <caption ref="caption">
                    {{ $gettext('Veranstaltungen') }}
                    <span class="actions" v-if="isLoading">
                        <img :src="loadingIndicator" width="20" height="20" :title="$gettext('Daten werden geladen...')">
                    </span>
                    <span class="actions" v-else-if="coursesCount > 0">
                        {{ coursesCount + ' ' + $gettext('Veranstaltungen') }}
                    </span>
                </caption>
                <colgroup>
                    <col v-if="showComplete">
                    <col v-for="activeField in sortedActivatedFields"
                         :key="`col-${activeField}`"
                         :style="{width: activeField === 'contents' && contentWidth !== null ? `${contentWidth}px` : null}"
                    >
                    <col>
                </colgroup>
                <thead>
                    <tr class="sortable">
                        <th v-if="showComplete" :class="sort.by === 'completion' ? 'sort' + sort.direction.toLowerCase() : ''">
                            <a
                                @click.prevent="changeSort('completion')"
                                class="course-completion"
                                :title="$gettext('Bearbeitungsstatus')"
                            >
                                {{ $gettext('Bearbeitungsstatus') }}
                            </a>
                        </th>
                        <th v-for="activeField in sortedActivatedFields" :key="`field-${activeField}`" :class="sort.by === activeField ? 'sort' + sort.direction.toLowerCase() : ''">
                            <a href="#"
                               @click.prevent="changeSort(activeField)"
                               :title="sort.by === activeField && sort.direction === 'ASC' ? $gettext('Sortiert aufsteigend nach %{field}', {field: fields[activeField]}, true) : (sort.by === activeField && sort.direction === 'DESC' ? $gettext('Sortiert absteigend nach %{ field } ', { field: fields[activeField]}, true) : $gettext('Sortieren nach %{ field }', { field: fields[activeField]}, true))"
                               v-if="!unsortableFields.includes(activeField)"
                            >
                                {{ fields[activeField] }}
                            </a>
                            <template v-else>
                                {{ fields[activeField] }}
                            </template>
                        </th>
                        <th class="actions">
                            {{ $gettext('Aktion') }}
                            <studip-action-menu class="filter" :title="$gettext('Darstellungsfilter')" :items="availableFields" @toggleActiveField="toggleActiveField"></studip-action-menu>
                        </th>
                    </tr>
                    <tr v-if="buttons.top">
                        <th v-html="buttons.top" style="text-align: right" :colspan="colspan"></th>
                    </tr>
                </thead>
                <tbody :class="{ loading: isLoading }">
                    <tr v-for="course in sortedCourses"
                        :key="course.id"
                        :class="course.id === currentLine ? 'selected' : ''"
                        @click="currentLine = course.id">
                        <td v-if="showComplete">
                            <button class="course-completion undecorated"
                                    :data-course-completion="course.completion"
                                    :title="(course.completion > 0 ? (course.completion == 1 ? $gettext('Veranstaltung in Bearbeitung.') : $gettext('Veranstaltung komplett.')) : $gettext('Veranstaltung neu.')) + ' ' +  $gettext('Klicken zum Ändern des Status.')"
                                    @click.prevent="toggleCompletionState(course.id)">
                                {{ $gettext('Bearbeitungsstatus ändern') }}
                            </button>
                        </td>
                        <td v-for="active_field in sortedActivatedFields"
                            :key="active_field"
                            @click="event => actionForCourseAndField(course, active_field, event)"
                        >
                            <div v-html="course[active_field]"></div>
                            <button v-if="active_field === 'name' && getChildren(course).length > 0"
                                    @click.prevent="toggleOpenChildren(course.id)"
                            >
                                <studip-icon :shape="open_children.indexOf(course.id) === -1 ? 'add' : 'remove'" class="text-bottom"></studip-icon>
                                {{ $gettext(
                                    '%{ n } Unterveranstaltungen',
                                    { n: getChildren(course).length }
                                ) }}
                            </button>
                        </td>
                        <td class="actions" v-html="course.action">
                        </td>
                    </tr>
                    <tr v-if="coursesCount === 0 && coursesLoaded">
                        <td :colspan="colspan">
                            {{ $gettext('Keine Ergebnisse') }}
                        </td>
                    </tr>
                    <tr v-if="coursesCount > 0 && sortedCourses.length === 0">
                        <td :colspan="colspan">
                            {{
                                $gettext(
                                    '%{ n } Veranstaltungen entsprechen Ihrem Filter. Schränken Sie nach Möglichkeit die Filter weiter ein.',
                                    { n: coursesCount }
                                )
                            }}
                            <a href="" @click.prevent="loadCourses({withoutLimit: true});">
                                {{ $gettext('Alle anzeigen') }}
                            </a>
                        </td>
                    </tr>
                    <tr v-if="!coursesLoaded">
                        <td :colspan="colspan">
                            {{ $gettext('Daten werden geladen...') }}
                        </td>
                    </tr>
                </tbody>
                <tfoot v-if="buttons.bottom">
                    <tr>
                        <td v-html="buttons.bottom" style="text-align: right" :colspan="colspan"></td>
                    </tr>
                </tfoot>
            </table>
        </form>
        <transition name="slide">
            <div v-if="showSlider" class="slider">
                <nav>
                    <select v-model="showSlider.area">
                        <option v-for="area in filteredActionAreas"
                                :key="area.id"
                                :value="area.id"
                                :aria-label="$gettext('Aktionsbereich %{label} wählen', area)"
                        >
                            {{ area.label }}
                        </option>
                    </select>

                    <button @click.prevent="showSlider = false" class="as-link">
                        <studip-icon shape="decline"></studip-icon>
                    </button>
                </nav>
                <raw-html-mount :html="sliderContent"></raw-html-mount>
            </div>
        </transition>

    </div>

    <teleport to="#action-area-selector" defer>
        <sidebar-widget :title="$gettext('Aktionsbereichauswahl')">
            <template #content>
                <select class="sidebar-selectlist"
                        v-model="currentActionAreaId"
                >
                    <option v-for="area in actionAreas"
                            :key="area.id"
                            :value="area.id"
                    >
                        {{ area.label }}
                    </option>
                </select>
            </template>
        </sidebar-widget>
    </teleport>
</template>
<script>
import { mapActions, mapGetters, mapState } from 'vuex';
import RawHtmlMount from "../components/RawHtmlMount.vue";
import SidebarWidget from "../components/SidebarWidget.vue";

export default {
    name: 'AdminCourses',
    components: {SidebarWidget, RawHtmlMount},
    props: {
        maxCourses: Number,
        showComplete: {
            type: Boolean,
            default: false,
        },
        fields: Object,
        unsortableFields: Array,
        sortBy: String,
        sortFlag: String,
    },
    data() {
        return {
            sort: {
                by: this.sortBy,
                direction: this.sortFlag,
            },
            currentLine: null,
            open_children: [],
            contentWidth: null,
            showSlider: false,
            sliderContent: ''
        };
    },
    created() {
        this.loadCourses();

        this.globalOn('AdminCourses/changeActionArea', this.changeActionArea.bind(this));
        this.globalOn('AdminCourses/changeFilter', this.changeFilter.bind(this));
        this.globalOn('AdminCourses/loadCourse', this.loadCourse.bind(this));
    },
    updated() {
        const iconNavigations = this.$refs.table.querySelectorAll('tbody .my-courses-navigation');

        if (iconNavigations.length === 0) {
            this.contentWidth = null;
            return;
        }

        const iconCounts = Array.from(iconNavigations).map(node => {
            return node.querySelectorAll('.my-courses-navigation-item').length;
        });

        this.contentWidth = Math.max(...iconCounts) * 26;
    },
    unmounted() {
        this.globalOff('AdminCourses/changeActionArea', this.changeActionArea.bind(this));
        this.globalOff('AdminCourses/changeFilter', this.changeFilter.bind(this));
        this.globalOff('AdminCourses/loadCourse', this.loadCourse.bind(this));
    },
    computed: {
        ...mapState('admincourses', [
            'actionArea',
            'actionAreas',
            'activatedFields',
            'buttons',
            'courses',
            'coursesCount',
            'coursesLoaded',
            'filters',
        ]),
        ...mapGetters('admincourses', [
            'isLoading',
        ]),
        csrf() {
            return STUDIP.CSRF_TOKEN;
        },
        colspan () {
            let colspan = this.activatedFields.length + 1;
            if (this.showComplete) {
                colspan += 1;
            }
            return colspan;
        },
        sortedCourses() {
            let maincourses = this.courses.filter(c => !c.parent_course);
            maincourses = this.sortArray(maincourses);

            let sorted_courses = [];
            let children = [];
            for (let i in maincourses) {
                sorted_courses.push(maincourses[i]);
                if (this.open_children.indexOf(maincourses[i].id) !== -1) {
                    children = this.getChildren(maincourses[i]);
                    children = this.sortArray(children);
                    for (let k in children) {
                        sorted_courses.push(children[k]);
                    }
                }
            }
            return sorted_courses;
        },
        sortedActivatedFields() {
            return Object.keys(this.fields).filter(f => this.activatedFields.includes(f));
        },
        availableFields() {
            return Object.keys(this.fields).map(f => {
                return {
                    type: 'checkbox',
                    label: this.fields[f],
                    checked: this.activatedFields.includes(f),
                    name: 'activatedFields',
                    emit: 'toggleActiveField',
                    emitArguments: f,
                    disabled: f === 'name',
                }
            });
        },
        loadingIndicator() {
            return STUDIP.ASSETS_URL + 'images/loading-indicator.svg';
        },
        captionHeight() {
            console.log('caption height', this.$refs.caption);
            return `${this.$refs.caption.offsetHeight}px`;
        },
        filteredActionAreas() {
            return this.actionAreas.filter(area => !area.multimode);
        },
        currentActionAreaId: {
            get() {
                return this.actionArea;
            },
            set(value) {
                this.changeActionArea(value);
            }
        }
    },
    methods: {
        ...mapActions('admincourses', [
            'changeActionArea',
            'changeFilter',
            'loadCourses',
            'loadCourse',
            'toggleActiveField',
            'toggleCompletionState',
        ]),
        getChildren(course) {
            return this.courses.filter(c => c.parent_course === course.id);
        },
        toggleOpenChildren(course_id) {
            if (!this.open_children.includes(course_id)) {
                this.open_children.push(course_id);
            } else {
                this.open_children = this.open_children.filter(cid => cid !== course_id);
            }
        },
        changeSort(column) {
            if (this.sort.by === column) {
                this.sort.direction = this.sort.direction === 'ASC' ? 'DESC' : 'ASC';
            } else {
                this.currentLine = null;
                this.sort.direction = 'ASC';
            }
            this.sort.by = column;

            $.post(STUDIP.URLHelper.getURL('dispatch.php/admin/courses/sort'), {
                sortby: column,
                sortflag: this.sort.direction,
            });
        },
        sortArray (array) {
            const mappedFields = {
                last_activity: 'last_activity_raw',
                semester: 'semester_sort',
            };

            if (!array.length) {
                return [];
            }
            if (!this.activatedFields.includes(this.sort.by) && this.sort.by !== 'completion') {
                return array;
            }

            const striptags = function (text) {
                if (typeof text === 'string') {
                    return text.replace(/(<([^>]+)>)/gi, "");
                } else {
                    return text;
                }
            };

            let sortby = mappedFields[this.sort.by] ?? this.sort.by;

            // Define sort direction by this factor
            const directionFactor = this.sort.direction === 'ASC' ? 1 : -1;

            // Default sort function by string comparison of field
            const collator = new Intl.Collator(String.locale, {
                numeric: sortby !== 'number',
                sensitivity: 'base'
            });
            let sortFunction = function (a, b) {
                return collator.compare(striptags(a[sortby]), striptags(b[sortby]))
                    || collator.compare(striptags(a.number), striptags(b.number));
            };

            if (sortby === 'number') {
                sortFunction = (a, b) => {
                    return collator.compare(striptags(a.number), striptags(b.number))
                        || collator.compare(striptags(a.name), striptags(b.name));
                };
            } else {
                let is_numeric = !array.some(i => {
                    const value = striptags(i[sortby]);
                    return value && isNaN(parseInt(value, 10));
                });

                if (is_numeric) {
                    sortFunction = function (a, b) {
                        const aValue = (striptags(a[sortby]) ? parseInt(striptags(a[sortby]), 10) : 0);
                        const bValue = (striptags(b[sortby]) ? parseInt(striptags(b[sortby]), 10) : 0);

                        return aValue - bValue
                            || collator.compare(striptags(a.number), striptags(b.number));
                    };
                }
            }

            // Actual sort on copy of array
            return array.concat().sort((a, b) => directionFactor * sortFunction(a, b));
        },
        getURL(url, params = {}) {
            return STUDIP.URLHelper.getURL(url, params);
        },
        actionForCourseAndField(course, field, event) {
            if (
                field !== 'name'
                || this.actionAreas.find(area => area.id == this.currentActionAreaId).multimode
            ) {
                return;
            }
            event.preventDefault();

            if (!this.showSlider) {
                this.showSlider = {course, area: this.currentActionAreaId};
            } else if (this.showSlider.course.id !== course.id) {
                this.showSlider.course = course;
            } else {
                this.showSlider = false;
            }
        },
        changeSliderArea(area) {
            this.showSlider.area = area;
        }
    },
    watch: {
        showSlider: {
            async handler(data) {
                if (!data) {
                    this.sliderContent = '';
                    return;
                }


                this.sliderContent = '<div class="studip-loading-skeleton with-animation"></div>';

                const area = this.actionAreas.find(area => area.id == data.area);
                const url = STUDIP.URLHelper.getURL(area.url.replace('%s', data.course.id), {cid: data.course.id}, true);

                this.changeActionArea(area.id);

                this.sliderContent = await fetch(url, {
                    redirect: 'follow',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                }).then(response => response.text());
            },
            deep: true
        }
    },
};
</script>
<style lang="scss" scoped>
.admin-courses-wrapper {
    overflow-x: hidden;
    position: relative;

    .slider {
        box-sizing: border-box;
        border-left: 1px solid black;

        position: absolute;
        top: v-bind(captionHeight);
        width: 66%;
        bottom: 0;
        right: 0;
        overflow-x: hidden;
        overflow-y: auto;
        background: var(--white);

        z-index: 3;

        nav {
            background-color: var(--red);
            position: sticky;
            top: 0;
            z-index: 1;

            display: flex;
            flex-direction: row;
            justify-content: space-between;

            select {
                flex: 1 0 auto;
            }

            ul {
                display: flex;
                flex-direction: row;
                justify-content: stretch;

                margin: 0;
                padding: 0;

                li {
                    flex: 1;
                    list-style: none;

                    &.active {
                        flex: 0 1 auto;
                        background-color: var(--green);
                    }

                    button {
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                }
            }
        }
    }
}

.slide-enter-active,
.slide-leave-active {
    transition: all var(--transition-duration) ease-in-out;
}
.slide-enter-from,
.slide-leave-to {
    transform: translateX(50%);
    opacity: 0;
}
</style>
