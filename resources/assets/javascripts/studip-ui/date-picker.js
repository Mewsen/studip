// Setup Stud.IP's own datepicker extensions
export default {
    selector: '.has-date-picker,[data-date-picker]',
    // Initialize all datepickers that not yet been initialized (e.g. in dialogs)
    init: function () {
        $(this.selector).filter(function () {
            return $(this).data('date-picker-init') === undefined;
        }).each(function () {
            $(this).data('date-picker-init', true).datepicker();
        });
    },
    // Apply registered handlers. Take care: This happens upon before a
    // picker is shown as well as after a date has been selected.
    refresh: function () {
        $(this.selector).each(function () {
            var element = this,
                options = $(element).data().datePicker;
            if (options) {
                $.each(options, function (key, value) {
                    if (this.dataHandlers[key] !== undefined) {
                        this.dataHandlers[key].call(element, value);
                    }
                });
            }
        });
    },
    // Define handlers for any data-datepicker option
    dataHandlers: {
        // Ensure this date is not later (<=) than another date by setting
        // the maximum allowed date the other date.
        // This will also set this date to the maximum allowed date if it
        // currently later than the allowed maximum date.
        '<='(selector, offset) {
            var this_date = $(this).datepicker('getDate'),
                max_date = null,
                temp,
                adjustment = 0;

            if ($(this).data().datePicker.offset) {
                temp = $(this).data().datePicker.offset;
                adjustment = parseInt($(temp).val(), 10);
            }

            // Get max date by either actual dates or maxDate options on
            // all matching elements
            if (selector === 'today') {
                max_date = new Date();
            } else {
                $(selector).each(function () {
                    var date = $(this).datepicker('getDate') || $(this).datepicker('option', 'maxDate');
                    if (date && (!max_date || date < max_date)) {
                        max_date = new Date(date);
                    }
                });
            }

            // Set max date and adjust current date if neccessary
            if (max_date) {
                max_date.setTime(max_date.getTime() - (offset || 0) * 24 * 60 * 60 * 1000);

                temp = new Date(max_date);
                temp.setDate(temp.getDate() - adjustment);

                if (this_date && this_date > max_date) {
                    $(this).datepicker('setDate', temp);
                }

                $(this).datepicker('option', 'maxDate', max_date);
            } else {
                $(this).datepicker('option', 'maxDate', null);
            }
        },
        // Ensure this date is earlier (<) than another date by setting the
        // maximum allowed date to the other date - 1 day.
        // This will also set this date to the maximum allowed date - 1 day
        // if it is currently later than the allowed maximum date.
        '<'(selector) {
            this['<='].call(this, selector, 1);
        },
        // Ensure this date is not earlier (>=) than another date by setting
        // the minimum allowed date to the other date.
        // This will also set this date to the minimum allowed date if it is
        // currently earlier than the allowed minimum date.
        '>='(selector, offset) {
            var this_date = $(this).datepicker('getDate'),
                min_date = null,
                temp,
                adjustment = 0;

            if ($(this).data().datePicker.offset) {
                temp = $(this).data().datePicker.offset;
                adjustment = parseInt($(temp).val(), 10);
            }

            // Get min date by either actual dates or minDate options on
            // all matching elements
            if (selector === 'today') {
                min_date = new Date();
            } else {
                $(selector).each(function () {
                    var date = $(this).datepicker('getDate') || $(this).datepicker('option', 'minDate');
                    if (date && (!min_date || date > min_date)) {
                        min_date = new Date(date);
                    }
                });
            }

            // Set min date and adjust current date if neccessary
            if (min_date) {
                min_date.setTime(min_date.getTime() + (offset || 0) * 24 * 60 * 60 * 1000);

                temp = new Date(min_date);
                temp.setDate(temp.getDate() + adjustment);

                if (this_date && this_date < min_date) {
                    $(this).datepicker('setDate', temp);
                }

                $(this).datepicker('option', 'minDate', min_date);
            } else {
                $(this).datepicker('option', 'minDate', null);
            }
        },
        // Ensure this date is later (>) than another date by setting the
        // minimum allowed date to the other date + 1 day.
        // This will also set this date to the minimum allowed date + 1 day
        // if it is currently earlier than the allowed minimum date.
        '>'(selector) {
            this['>='].call(this, selector, 1);
        }
    }
};
