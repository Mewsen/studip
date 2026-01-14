import { $gettext } from '../lib/gettext';

STUDIP.Dialog.handlers.header['X-Raumzeit-Update-Times'] = function(json) {
    var info = $.parseJSON(json);
    $('.course-admin #course-' + info.course_id + ' .raumzeit').html(info.html);
};

$(document).on('change', '.datesBulkActions', function() {
    var $button = $(this).next('button');
    if ($(this).val() === 'delete') {
        $button.attr('data-confirm', $gettext('Wollen Sie die gewünschten Termine wirklich löschen?'));
    } else {
        if ($button.attr('data-confirm')) {
            $button.removeAttr('data-confirm');
        }
    }
});

$(document).on('change', '#edit-cycle', function() {
    var start = $('input[name=start_time]', this)[0],
        end = $('input[name=end_time]', this)[0],
        changed =
            start.defaultValue &&
            end.defaultValue &&
            (start.value !== start.defaultValue || end.value !== end.defaultValue);
    // check if new time exceeds the current one and add security question if necessary
    if (changed && (start.value < start.defaultValue || end.value > end.defaultValue)) {
        $(this).attr(
            'data-confirm',
            $gettext('Wenn Sie die regelmäßige Zeit ändern, verlieren Sie die Raumbuchungen für alle in der Zukunft liegenden Termine! Sind Sie sicher, dass Sie die regelmäßige Zeit ändern möchten?')
        );
    } else {
        // remove security question - not necessary (any more)
        $(this).attr('data-confirm', null);
    }
});
