STUDIP.ready(function() {
    jQuery(document).on('change', 'form.new-calendar-date-form input[name=begin]', function(event) {
        let begin_value = jQuery(event.target).val();
        let begin = STUDIP.Calendar.parseDateFromString(begin_value);
        if (!begin) {
            return;
        }
        let end_value = jQuery('form.new-calendar-date-form input[name=end]').val();
        let end = STUDIP.Calendar.parseDateFromString(end_value);
        if (end) {
            //Check if the date and time in end_value is past the date in begin_value.
            //If so, set the date or the time or both to a value after begin_value.
            if (end.getTime() <= begin.getTime()) {
                //Get the distance of the time (hours and minutes only) between begin and end:
                let diff = Math.abs(end.getHours() - begin.getHours()) * 3600
                    + Math.abs(end.getMinutes() - begin.getMinutes()) * 60;
                end = begin;
                end.setTime(end.getTime() + diff * 1000);
            }
        } else {
            //Clone begin and add one hour to end:
            end = begin;
            end.setTime(end.getTime() + 3600000);
        }

        //Display the new end:
        let end_string = STUDIP.DateTime.pad(end.getDate()) + '.' + STUDIP.DateTime.pad(end.getMonth() + 1) + '.' + end.getFullYear()
            + ' ' + STUDIP.DateTime.pad(end.getHours()) + ':' + STUDIP.DateTime.pad(end.getMinutes());
        jQuery('form.new-calendar-date-form input[name=end]').val(end_string);
    });
});
