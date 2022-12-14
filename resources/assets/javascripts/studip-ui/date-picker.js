// Setup Stud.IP's own datepicker extensions
function getValue(element) {
    if (Datepicker.supportsNativeInput) {
        return element.value;
    }

    return $(element).datepicker('getDate');
}

function setValue(element, value) {
    if (Datepicker.supportsNativeInput) {
        element.value = value;
        return;
    }

    $(element).datepicker('setDate', value);
}

function getOption(element, option) {
    if (Datepicker.supportsNativeInput) {
        return element.getAttribute(option) ?? null;
    }

    const mapping = {
        min: 'minDate',
        max: 'maxDate',
    };

    return $(element).datepicker('option', mapping[option] ?? option);
}

function setOption(element, option, value) {
    if (Datepicker.supportsNativeInput) {
        element.setAttribute(option, value);
        return;
    }

    const mapping = {
        min: 'minDate',
        max: 'maxDate',
    };

    $(element).datepicker('option', mapping[option] ?? option, value);
}

const Datepicker = {
    supportsNativeInput: (function () {
        let input = document.createElement('input');
        input.setAttribute('type', 'date');

        const invalid = 'not-a-valid-date';
        input.setAttribute('value', invalid);

        return input.value !== invalid;
    })(),
    selector: [
        '.has-date-picker',
        '[data-date-picker]',
        'input[type="date"]',
    ].join(','),
    // Initialize all datepickers that not yet been initialized (e.g. in dialogs)
    init() {
        Array.from(document.querySelectorAll(this.selector)).filter(node => {
            return node.dataset.datePickerInit === undefined;
        }).forEach(element => {
            element.dataset.datePickerInit = true;

            if (!this.supportsNativeInput) {
                $(element).datepicker();
            }

            this.refresh(element);

            element.addEventListener('change', event => {
                this.refresh(event.target);
            });
        });
    },
    // Apply registered handlers. Take care: This happens upon before a
    // picker is shown as well as after a date has been selected.
    refresh(node) {
        const options = node.dataset.datePicker !== undefined
            ? JSON.parse(node.dataset.datePicker)
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
        '<='(node, selector, offset = null) {
            let this_date = getValue(node);
            let max_date = null;

            offset = offset ?? node.dataset.datePicker?.offset ?? 0;

            // Get max date by either actual dates or maxDate options on
            // all matching elements
            if (selector === 'today') {
                max_date = new Date();
            } else {
                $(selector).each(function () {
                    var date = getValue(this) ?? getOption(this, 'max');
                    if (date && (!max_date || date < max_date)) {
                        max_date = new Date(date);
                    }
                });
            }

            // Set max date and adjust current date if neccessary
            if (max_date) {
                max_date.setTime(max_date.getTime() - offset * 24 * 60 * 60 * 1000);

                if (this_date && this_date > max_date) {
                    setValue(node, max_date);
                }

                setOption(node, 'max', max_date);
            } else {
                setOption(node, 'max', null);
            }
        },
        // Ensure this date is earlier (<) than another date by setting the
        // maximum allowed date to the other date - 1 day.
        // This will also set this date to the maximum allowed date - 1 day
        // if it is currently later than the allowed maximum date.
        '<'(node, selector) {
            this['<='](node, selector, 1);
        },
        // Ensure this date is not earlier (>=) than another date by setting
        // the minimum allowed date to the other date.
        // This will also set this date to the minimum allowed date if it is
        // currently earlier than the allowed minimum date.
        '>='(node, selector, offset = null) {
            let this_date = getValue(node);
            let min_date;

            offset = offset ?? node.dataset.datePicker?.offset ?? 0;

            // Get min date by either actual dates or minDate options on
            // all matching elements
            if (selector === 'today') {
                min_date = new Date();
            } else {
                document.querySelectorAll(selector).forEach(n => {
                    var date = getValue(n) ?? getOption(n, 'min');
                    if (date && (!min_date || date > min_date)) {
                        min_date = new Date(date);
                    }
                });
            }

            // Set min date and adjust current date if neccessary
            if (min_date) {
                min_date.setTime(min_date.getTime() + offset * 24 * 60 * 60 * 1000);

                if (this_date && this_date < min_date) {
                    setValue(node, min_date);
                }

                setOption(node, 'min', min_date);
            } else {
                setOption(node, 'min', null);
            }
        },
        // Ensure this date is later (>) than another date by setting the
        // minimum allowed date to the other date + 1 day.
        // This will also set this date to the minimum allowed date + 1 day
        // if it is currently earlier than the allowed minimum date.
        '>'(node, selector) {
            this['>='](node, selector, 1);
        }
    }
};

export default Datepicker;
