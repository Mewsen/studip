/* ------------------------------------------------------------------------
 * Anmeldeverfahren und -sets
 * ------------------------------------------------------------------------ */
import { $gettext } from './gettext';

const Admission = {

    /**
     * All registered rule types with their corresponding Vue components
     */
    availableRules: {
        ConditionalAdmission: 'ConditionalAdmission.vue',
        CourseMemberAdmission: 'CourseMemberAdmission.vue',
        LimitedAdmission: 'LimitedAdmission.vue',
        LockedAdmission: 'LockedAdmission.vue',
        ParticipantRestrictedAdmission: 'ParticipantRestrictedAdmission.vue',
        PasswordAdmission: 'PasswordAdmission.vue',
        PreferentialAdmission: 'PreferentialAdmission.vue',
        TermsAdmission: 'TermsAdmission.vue',
        TimedAdmission: 'TimedAdmission.vue'
    },

    getCourses: function(targetUrl) {
        var courseFilter = $('input[name="course_filter"]').val();
        if (courseFilter === '') {
            courseFilter = '%%%';
        }
        var data = {
            'courses[]': _.map($('#courselist input:checked'), 'id'),
            course_filter: courseFilter,
            semester: $('select[name="semester"]').val(),
            'institutes[]': $.merge(
                _.map($('input[name="institutes[]"]:hidden'), 'value'),
                _.map($('input[name="institutes[]"]:checked'), 'value')
            )
        };
        let loading = $gettext('Wird geladen');
        $('#instcourses').empty();
        $('<img/>', {
            src: STUDIP.ASSETS_URL + 'images/loading-indicator.svg',
            style: 'vertical-align: middle; width: 64px; height: 64px',
        }).appendTo('#instcourses');
        $('#instcourses').append(loading);
        $('#instcourses').load(targetUrl, data);
        return false;
    },

    updateInstitutes: function(elementId, instURL, courseURL, mode) {
        if (elementId !== '') {
            var query = '';
            $('.institute').each(function() {
                query += '&institutes[]=' + this.value;
            });
            switch (mode) {
                case 'delete':
                    $('#' + elementId).remove();
                    break;
                case 'add':
                    query += '&institutes[]=' + elementId;
                    $.post(instURL, query, function(data) {
                        $('#institutes').html(data);
                    });
                    break;
            }
            $('#instcourses :checked').each(function() {
                query += '&courses[]=' + this.value;
            });
            this.getCourses(courseURL);
            Admission.toggleNotSavedAlert();
        }
    },

    checkUncheckAll: function(inputName, mode) {
        switch (mode) {
            case 'check':
                $('input[name*="' + inputName + '"]').each(function() {
                    $(this).prop('checked', true);
                });
                break;
            case 'uncheck':
                $('input[name*="' + inputName + '"]').each(function() {
                    $(this).prop('checked', false);
                });
                break;
            case 'invert':
                $('input[name*="' + inputName + '"]').each(function() {
                    $(this).prop('checked', !$(this).prop('checked'));
                });
                break;
        }
        return false;
    },

    toggleNotSavedAlert: function() {
        $('.hidden-alert').show();
    }

};

export default Admission;
