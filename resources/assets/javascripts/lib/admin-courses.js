const AdminCourses = {
    App: {
        loadCourse(courseId) {
            STUDIP.Vue.emit('AdminCourses/loadCourse', courseId);
        },
        changeFilter(filters) {
            STUDIP.Vue.emit('AdminCourses/changeFilter', filters);
        },
        changeActionArea(area) {
            STUDIP.Vue.emit('AdminCourses/changeActionArea', area);
        }
    },
    changeFiltersDependendOnInstitute(institut_id) {
        AdminCourses.App.changeFilter({ institut_id });

        //change Studiengangteil filter
        $.get(
            STUDIP.URLHelper.getURL('dispatch.php/admin/courses/get_stdgangteil_selector/' + institut_id)
        ).done((widget) => {
            $('select[name=stgteil_select]').closest('label').replaceWith(widget);
        });

        //change Dozenten-Filter
        $.get(
            STUDIP.URLHelper.getURL('dispatch.php/admin/courses/get_teacher_selector/' + institut_id)
        ).done((widget) => {
            $('select[name=teacher_filter]').closest('label').replaceWith(widget);
        });
    }
};
export default AdminCourses;
