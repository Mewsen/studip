import Responsive from '../../assets/javascripts/lib/responsive.js';

import { mapState, mapActions, mapGetters } from 'vuex';
import Navigation from '../components/my-courses/Navigation.vue';
import { $gettext } from "../../assets/javascripts/lib/gettext";

function createMixin(minimal = false) {
    const result = {
        data () {
            return {
                responsiveDisplay: false,
            };
        },
        methods: {
            getCourseName(course) {
                let name = course.name;

                // Include sem number
                if (this.displaySemNumber) {
                    name = `${course.number} ${name}`;
                }

                // Show deputy info
                if (course.is_deputy) {
                    name = `${name} ${$gettext('[Vertretung]')}`;
                }

                return name.trim();
            },
            getCourseURL(course) {
                return this.urlFor('dispatch.php/course/go', {to: course.id}, true);
            },

            urlFor(url, parameters, ignore_params) {
                return STUDIP.URLHelper.getURL(url, parameters, ignore_params);
            },

            getCourses (ids) {
                return ids.map(id => this.courses[id]);
            },
        },
        computed: {
            csrf() {
                return STUDIP.CSRF_TOKEN;
            },
            displaySemNumber() {
                return this.config?.sem_number_always
                    || (
                        this.config?.sem_number
                        && !this.responsiveDisplay
                    );
            }
        },
        created() {
            this.responsiveDisplay = Responsive.isResponsive();
            Responsive.media_query.addEventListener('change', () => {
                this.responsiveDisplay = Responsive.isResponsive();
            })
        }
    };

    if (!minimal) {
        result.components = { Navigation };

        result.computed = {
            ...result.computed,

            ...mapState('mycourses', [
                'courses',
                'groups',
                'userid',
                'config',
            ]),
            ...mapGetters('mycourses', [
                'isGroupOpen',
                'getConfig',
            ]),

            numberOfNavElements () {
                return Math.max(
                    ...Object.values(this.courses).map(course => {
                        const navigation = this.getNavigationForCourse(course, true);
                        return Object.values(navigation).length;
                    })
                );
            }
        };

        result.methods = {
            ...result.methods,

            ...mapActions('mycourses', [
                'toggleOpenGroup',
                'updateConfigValue',
            ]),

            getActionMenuForCourse(course, withColorPicker = false) {
                let menu = [];

                if (!course.is_studygroup) {
                    menu.push({
                        url: this.urlFor(`dispatch.php/course/details/index/${course.id}`, {from: this.urlFor('dispatch.php/my_courses/index')}),
                        label: this.$gettext('Veranstaltungsdetails'),
                        icon: 'info-circle',
                        attributes: {
                            'data-dialog': '',
                        },
                    });
                }

                if (withColorPicker) {
                    // Color grouping
                    menu.push({
                        emit: 'show-color-picker',
                        emitArguments: [course],
                        label: this.$gettext('Farbgruppierung ändern'),
                        icon: 'group4'
                    });
                }

                // Extra navigation?
                if (!course.is_group) {
                    if (course.extra_navigation) {
                        menu.push(course.extra_navigation);
                    } else if (course.admission_binding) {
                        menu.push({
                            url: this.urlFor('dispatch.php/my_courses/decline_binding'),
                            label: this.$gettext('Aus der Veranstaltung austragen'),
                            icon: 'door-leave',
                            attributes: {
                                title: this.$gettext('Die Teilnahme ist bindend. Bitte wenden Sie sich an die Lehrenden.'),
                            },
                            disabled: true
                        });
                    } else {
                        menu.push({
                            url: this.urlFor(`dispatch.php/my_courses/decline/${course.id}`, {cmd: 'suppose_to_kill'}),
                            label: this.$gettext('Aus der Veranstaltung austragen'),
                            icon: 'door-leave'
                        });
                    }
                }

                return menu;
            },
            getHiddenTooltip(course) {
                let infotext = this.$gettext('Versteckte Veranstaltungen können über die Suchfunktionen nicht gefunden werden.');
                infotext += ' ';
                if (course.is_teacher && this.getConfig('allow_dozent_visibility')) {
                    infotext += this.$gettext('Um die Veranstaltung sichtbar zu machen, wählen Sie den Punkt "Sichtbarkeit" im Administrationsbereich der Veranstaltung.');
                } else {
                    infotext += this.$gettext('Um die Veranstaltung sichtbar zu machen, wenden Sie sich an Administrierende.');
                }
                return infotext;
            },
            getNavigationForCourse(course, gaps = false) {
                let navigation = {};

                Object.entries(course.navigation).forEach(([key, nav]) => {
                    if (!nav && !gaps) {
                        return;
                    }

                    if (this.getViewConfig('only_new') && !nav.important) {
                        return;
                    }

                    let result = nav ? Object.assign({}, nav) : false;
                    if (nav) {
                        if (nav.important) {
                            result.class = 'my-courses-navigation-important';
                            result.icon.role = 'attention';
                            result.icon.shape = result.icon.shape.replace(/^new\//, '');
                        } else {
                            result.class = false;
                            result.icon.role = 'clickable';
                        }

                        result.url = this.urlFor('dispatch.php/course/go', {
                            to: course.id,
                            redirect_to: result.url,
                        });
                    }

                    navigation[key] = result;
                });

                return navigation;
            },
            getViewConfig(key) {
                return this.getConfig(
                    'view_settings',
                    this.responsiveDisplay ? 'responsive' : 'regular',
                    key
                );
            },
            isChild (course) {
                return course.parent !== null && this.courses[course.parent] !== undefined;
            },
            isParent (course) {
                return course.children.length > 0 && course.children.every(childId => {
                    return this.courses[childId] !== undefined;
                });
            },
            updateViewConfig(key, value) {
                let config = this.getConfig('view_settings');
                config[this.responsiveDisplay ? 'responsive' : 'regular'][key] = value;
                return this.updateConfigValue({
                    key: 'view_settings',
                    value: config
                });
            },
        };
    }

    return result;
}

export default createMixin();

export { createMixin };
