export default {
    supportsNativeInput: (function () {
        let input = document.createElement('input');
        input.setAttribute('type', 'datetime-local');

        const invalid = 'not-a-valid-datetime';
        input.setAttribute('value', invalid);

        return input.value !== invalid;
    })(),
    selector: [
        '.has-datetime-picker',
        '[data-datetime-picker]',
        'input[type="datetime-local"]',
    ].join(','),
    // Initialize all datetimepickers that not yet been initialized (e.g. in dialogs)
    init() {
        Array.from(document.querySelectorAll(this.selector)).filter(node => {
            return node.dataset.datetimePickerInit === undefined;
        }).forEach(element => {
            element.dataset.datetimePickerInit = true;

            // Load and apply polyfill if necessary
            if (!this.supportsNativeInput) {
                import('time-input-polyfill').then(({default: TimePolyfill}) => {
                    new TimePolyfill(element);
                    element.classList.add('hasTimepicker');
                });
            }

            this.refresh(element);

            element.addEventListener('change', event => {
                this.refresh(event.target);
            });
        });
        //
        // $(this.selector).filter(function () {
        //     return $(this).data('datetime-picker-init') === undefined;
        // }).each(function () {
        //     $(this).data('datetime-picker-init', true).datetimepicker();
        // });
    },
    // Apply registered handlers. Take care: This happens upon before a
    // picker is shown as well as after a date has been selected.
    refresh(node) {
        const options = node.dataset.datetimePicker !== undefined
            ? JSON.parse(node.dataset.datetimePicker)
            : {};
        Object.entries(options).forEach(([key, value]) => {
            if (this.dataHandlers[key] !== undefined) {
                this.dataHandlers[key](node, value);
            }
        });
    },
    // Define handlers for any data-datepicker option
    dataHandlers: {
        // Ensure this date is not later (<=) than another date by setting
        // the maximum allowed date the other date.
        // This will also set this date to the maximum allowed date if it
        // currently later than the allowed maximum date.
        '<=': function (selector, offset) {
            var this_date = $(this).datetimepicker('getDate'),
                max_date = null,
                temp;

            if ((offset === undefined) && $(selector).data('offset')) {
                temp   = $(selector).data('offset');
                offset = parseInt($(temp).val(), 10);
            }

            // Get max date by either actual dates or maxDate options on
            // all matching elements
            if (selector === 'today') {
                max_date = new Date();
                max_date.setHours(0, 23, 59, 59);
            } else {
                $(selector).each(function () {
                    var date = $(this).datetimepicker('getDate') || $(this).datetimepicker('option', 'maxDate');
                    if (date && (!max_date || date < max_date)) {
                        max_date = new Date(date);
                    }
                });
            }

            // Set max date and adjust current date if neccessary
            if (max_date) {
                max_date.setTime(max_date.getTime() - (offset || 0) * 24 * 60 * 60 * 1000);

                if (this_date && this_date > max_date) {
                    $(this).datetimepicker('setDate', max_date);
                }

                $(this).datetimepicker('option', 'maxDate', max_date);
            } else {
                $(this).datetimepicker('option', 'maxDate', null);
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
            var this_date = $(this).datetimepicker('getDate'),
                min_date = null,
                temp;

            if ((offset === undefined) && $(selector).data('offset')) {
                temp   = $(selector).data('offset');
                offset = parseInt($(temp).val(), 10);
            }

            // Get min date by either actual dates or minDate options on
            // all matching elements
            if (selector === 'today') {
                min_date = new Date();
                min_date.setHours(0, 0, 0);
            } else {
                $(selector).each(function () {
                    var date = $(this).datetimepicker('getDate') || $(this).datetimepicker('option', 'minDate');
                    if (date && (!min_date || date > min_date)) {
                        min_date = new Date(date);
                    }
                });
            }

            // Set min date and adjust current date if neccessary
            if (min_date) {
                min_date.setTime(min_date.getTime() + (offset || 0) * 24 * 60 * 60 * 1000);

                if (this_date && this_date < min_date) {
                    $(this).datetimepicker('setDate', min_date);
                }

                $(this).datetimepicker('option', 'minDate', min_date);
            } else {
                $(this).datetimepicker('option', 'minDate', null);
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
