STUDIP.domReady(function() {
    if ($('.sem-tree-assigned-root > ul > li').length == 0) {
        $('.sem-tree-assigned-root').addClass('hidden-js');
    }
});

STUDIP.ready(function() {
    $('.course-wizard-step-0 *:input:not(input[type=submit])').each(function () {
        $(this).attr(
            'tabindex',
            $(this).closest('section,footer').css('order')
        );
    });
    $('#wizard-coursetype').on('change', function() {
        let semtype = $(this).val();
        let mandatory_types = $('#wizard-maxmember').parent('section').data('mandatory');
        if (mandatory_types.includes(semtype)) {
            $('#wizard-maxmember').parent('section').show();
        } else {
            $('#wizard-maxmember').parent('section').hide();
        }
    });
});
