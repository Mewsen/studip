import { $gettext } from './lib/gettext.js';
import Datepicker from './studip-ui/date-picker.js';
import DateTimepicker from './studip-ui/datetime-picker.js';
import Timepicker from './studip-ui/time-picker.js';

/**
 * This file contains extensions/adjustments for jQuery UI.
 */

(function ($, STUDIP) {

    $.widget( "ui.dialog", $.ui.dialog, {
        _allowInteraction: function( event ) {
            return hasParentWhich(isCKBodyWrapper)(event.target) ||  this._super( event );
        },
    });

    function hasParentWhich(predicate) {
        return function tryParent(element) {
            if (!element?.parentElement) {
                return false;
            }

            return predicate(element) || tryParent(element.parentElement);
        };
    }

    function isCKBodyWrapper(element) {
        return element?.classList?.contains('ck-body-wrapper');
    }

    /**
     * Setup and refine date picker, add automated handling for .has-date-picker
     * and [data-date-picker].
     * Note: [date-datepicker] would be a way better selector but unfortunately
     * jQuery UI's Datepicker itself stores vital data in the the "datepicker"
     * data() variable, so we cannot use it and need to use "date-picker"
     * instead.
     *
     * @author  Jan-Hendrik Willms <tleilax+studip@gmail.com>
     * @license GPL2 or any later version
     * @since   Stud.IP 3.4
     */

    'use strict';

    // Exit if datepicker is undefined (which it should never be)
    if (!Datepicker.supportsNativeInput && $.datepicker === undefined) {
        return;
    }

    if (!Datepicker.supportsNativeInput) {
        // Setup defaults and default locales
        var defaults = {},
            locale = {
                closeText: $gettext('Schließen'),
                prevText: $gettext('Zurück'),
                nextText: $gettext('Vor'),
                currentText: $gettext('Jetzt'),
                monthNames: [
                    $gettext('Januar'),
                    $gettext('Februar'),
                    $gettext('März'),
                    $gettext('April'),
                    $gettext('Mai'),
                    $gettext('Juni'),
                    $gettext('Juli'),
                    $gettext('August'),
                    $gettext('September'),
                    $gettext('Oktober'),
                    $gettext('November'),
                    $gettext('Dezember')
                ],
                monthNamesShort: [
                    $gettext('Jan'),
                    $gettext('Feb'),
                    $gettext('Mär'),
                    $gettext('Apr'),
                    $gettext('Mai'),
                    $gettext('Jun'),
                    $gettext('Jul'),
                    $gettext('Aug'),
                    $gettext('Sep'),
                    $gettext('Okt'),
                    $gettext('Nov'),
                    $gettext('Dez')
                ],
                dayNames: [
                    $gettext('Sonntag'),
                    $gettext('Montag'),
                    $gettext('Dienstag'),
                    $gettext('Mittwoch'),
                    $gettext('Donnerstag'),
                    $gettext('Freitag'),
                    $gettext('Samstag')
                ],
                dayNamesShort: [
                    $gettext('So'),
                    $gettext('Mo'),
                    $gettext('Di'),
                    $gettext('Mi'),
                    $gettext('Do'),
                    $gettext('Fr'),
                    $gettext('Sa')
                ],
                weekHeader: $gettext('Wo'),
                dateFormat: 'dd.mm.yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: '',
                changeMonth: true,
                changeYear: true,
            };
        // Set dayNamesMin to dayNamesShort since they are equal
        locale.dayNamesMin = locale.dayNamesShort;


        // Apply defaults including date picker handlers
        defaults = Object.assign({}, locale, {
            beforeShow (input) {
                Datepicker.refresh();

                if ($(input).parents('.ui-dialog').length > 0) {
                    return;
                }

                $(input).css({
                    'position': 'relative',
                    'z-index': 1002
                });
            },
            onSelect: function (value, instance) {
                if (value !== instance.lastVal) {
                    $(this).change();
                }
            }
        });

        $.datepicker.setDefaults(Object.assign({}, defaults, {
            beforeShow (input) {
                // Don't lose original behaviour
                defaults.beforeShow(input);

                if ($(input).parents('.ui-dialog').length > 0) {
                    $('.ui-dialog-content').bind('scroll.datepicker-scroll', _.debounce($.proxy(DpHideOnScroll, null, input), 100, {leading:true, trailing:false}));
                }
                $(window).bind('scroll.datepicker-scroll', _.debounce($.proxy(DpHideOnScroll, null, input), 100, {leading:true, trailing:false}));

                if ($(input).closest('#sidebar').length === 0) {
                    return;
                }

                const button = input.nextElementSibling;
                if (button && button.matches('input[type="submit"]')) {
                    button.style.position = 'relative';
                    button.style.zIndex = input.style.zIndex;
                }
            },
            onClose (date, inst) {
                $(this).one('click.picker', function () {
                    $(this).datepicker('show');
                }).on('blur', function () {
                    $(this).off('click.picker');
                });

                if ($(this).parents('.ui-dialog').length > 0) {
                    $('.ui-dialog-content').unbind('scroll.datepicker-scroll');
                } else {
                    $(window).unbind('scroll.datepicker-scroll');
                }
            }
        }));

        var DpHideOnScroll = function () {
            var input = arguments[0];
            $(input).blur();
            $(input).datepicker('hide');
        }
    }

    // Attach global focus handler on date picker elements
    $(document).on('focus', Datepicker.selector, () => {
        if (!$(event.target).is('input[type="date"]')) {
            $(event.target).attr('type', 'date');
        }

        Datepicker.init();
    });

    // Attach global focus handler on datetime picker elements
    $(document).on('focus', DateTimepicker.selector, () => {
        if (!$(event.target).is('input[type="datetime-local"]')) {
            $(event.target).attr('type', 'datetime-local');
        }
        DateTimepicker.init();
    });

    // Attach global focus handler on time picker elements
    $(document).on('focus', Timepicker.selector, event => {
        if (!$(event.target).is('input[type="time"]')) {
            $(event.target).attr('type', 'time');
        }

        Timepicker.init();
    });

    STUDIP.UI = STUDIP.UI ?? {};
    STUDIP.UI = {
        Datepicker,
        DateTimepicker,
        Timepicker,
    };

}(jQuery, STUDIP));
