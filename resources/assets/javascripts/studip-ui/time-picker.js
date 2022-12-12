export default {
    supportsNativeInput: (function () {
        let input = document.createElement('input');
        input.setAttribute('type', 'time');

        const invalid = 'not-a-time';
        input.setAttribute('value', invalid);

        return input.value !== invalid;
    })(),
    selector: [
        '.has-time-picker',
        '[data-time-picker]',
        'input[type="time"]',
    ].join(','),
    // Initialize all datetimepickers that not yet been initialized (e.g. in dialogs)
    init() {
        const elements = Array.from(document.querySelectorAll(this.selector)).filter(node => {
            return node.dataset.timePickerInit === undefined;
        });

        elements.forEach(element => {
            element.dataset.timePickerInit = true;
            element.addEventListener('change', event => {
                this.refresh(event.target);
            });

            // Load and apply polyfill if necessary
            if (!this.supportsNativeInput) {
                import('time-input-polyfill').then(({default: TimePolyfill}) => {
                    new TimePolyfill(element);
                    element.classList.add('hasTimepicker');
                });
            }
        });
    },
    // Apply registered handlers. Take care: This happens upon before a
    // picker is shown as well as after a date has been selected.
    refresh(node) {
        const options = node.dataset.timePicker !== undefined
            ? JSON.parse(node.dataset.timePicker)
            : {};
        Object.entries(options).forEach(([key, value]) => {
            if (this.dataHandlers[key] !== undefined) {
                this.dataHandlers[key](node, value);
            }
        });
    },
    parseTime(time) {
        const split = time.split(':');
        return {
            hour: parseInt(split[0], 10),
            minute: parseInt(split[1], 10)
        };
    },
    createTime(hours, minutes, minute_offset = 0) {
        // Adjust minutes if offset is given
        minutes = minutes + minute_offset;
        if (minutes >= 60) {
            hours += 1;
            minutes -= 60;
        } else if (minutes < 0) {
            hours -= 1;
            minutes += 60;
        }

        // Sanitize hours
        hours = Math.min(23, Math.max(0, hours));

        return ('0' + hours).slice(-2) + ':' + ('0' + minutes).slice(-2);
    },
    // Define handlers for any data-time-picker option
    dataHandlers: {
        // Ensure this time is not later (<=) than another time by setting
        // the maximum allowed time on the other time.
        // This will also set this time to the maximum allowed time if it is
        // currently later than the allowed maximum time.
        '<='(node, selector, offset) {
            const this_time = node.value;
            let max_time = null;

            offset = offset ?? node.dataset.offset ?? 0;

            document.querySelectorAll(selector).forEach(n => {
                const time = n.value;
                if (time && (!max_time || time < max_time)) {
                    max_time = time;
                }
            });

            // Set max time and adjust current time if neccessary
            if (max_time) {
                const parsed = Timepicker.parseTime(max_time);
                max_time = Timepicker.createTime(
                    parsed.hour,
                    parsed.minute,
                    -offset
                );

                if (this_time && this_time > max_time) {
                    node.value = max_time;
                }

                node.setAttribute('max', max_time);
            } else {
                node.removeAttribute('max');
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
        '>='(node, selector, offset) {
            const this_time = node.value;
            let min_time = null;

            offset = offset ?? node.dataset.offset ?? 0;

            document.querySelectorAll(selector).forEach(n => {
                const time = n.value;
                if (time && (!min_time || time < min_time)) {
                    min_time = time;
                }
            });

            // Set min time and adjust current time if neccessary
            if (min_time) {
                const parsed = Timepicker.parseTime(min_time);
                min_time = Timepicker.createTime(
                    parsed.hour,
                    parsed.minute,
                    offset
                );

                if (this_time && this_time < min_time) {
                    node.value = min_time;
                }

                node.setAttribute('min', min_time);
            } else {
                node.removeAttribute('min');
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
