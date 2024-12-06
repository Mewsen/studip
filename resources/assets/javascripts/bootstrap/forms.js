import { $gettext } from '../lib/gettext';
import Report from '../lib/report.ts';
import Dialog from "../lib/dialog";

// Allow fieldsets to collapse
$(document).on(
    'click',
    'form.default fieldset.collapsable legend,form.default.collapsable fieldset legend',
    function() {
        $(this)
            .closest('fieldset')
            .toggleClass('collapsed');
    }
);

// Display a visible hint that indicates how many characters the user may
// input if the element has a maxlength restriction.

$(document).on('focus', 'form.default [maxlength]:not(.no-hint)', function() {
    if (!$(this).is('textarea,input') || $(this).data('length-hint') || $(this).is('[readonly],[disabled]')) {
        return;
    }

    var width = $(this).outerWidth(true),
        hint = $('<div class="length-hint">').hide(),
        wrap = $('<div class="length-hint-wrapper">').width(width),
        timeout = null;

    $(this).wrap(wrap);

    hint.text($gettext('Zeichen verbleibend: '));

    hint.append('<span class="length-hint-counter">');
    hint.insertBefore(this);

    $(this)
        .focus(function() {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                hint.finish().show('slide', { direction: 'down' }, 300);
            }, 200);
        })
        .blur(function() {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                hint.finish().hide('slide', { direction: 'down' }, 300);
            }, 200);
        })
        .on('focus propertychange change keyup', function() {
            var count = $(this).val().length,
                max = parseInt($(this).attr('maxlength'), 10);

            hint.find('.length-hint-counter').text(max - count);
        });

    $(this).data('length-hint', true);

    setTimeout(
        function() {
            $(this).focus();
        }.bind(this),
        0
    );
});

// Automatic form submission handler when a select has changed it's value.
// Due to accessibility issues, an intuitive select[onchange=form.submit()]
// leads to terrible behaviour when invoked not by mouse. The form is
// submitted upon _every_ change, including key strokes.
// Thus, we need to overwrite this behaviour. Breakdown of this solution:
//
// - Only submit when the value has actually changed
// - Always submit when pressing enter (keycode 13)
// - Always check for change on blur event
//
// - Store whether the element was activated by click event
// - If so, submit upon next change event
// - Otherwise submit when enter has been pressed
//
// Be aware: All select[onchange*="submit()"] will be rewritten to
// select.submit-upon-select and have the onchange attribute removed.
// This might lead to unexpected behaviour.

// Ensure, every .submit-upon-select has an defaultSelected option.
$(document)
    .on('focus', 'select[onchange*="submit()"]', function() {
        $(this)
            .removeAttr('onchange')
            .addClass('submit-upon-select');
    })
    .on('click mousedown', 'select.submit-upon-select', function() {
        // Firefox and Chrome handle click events on selects differently,
        // thus we need the mousedown event and the click event is needed for
        // select2 elements. Please do not change!

        $(this).data('wasClicked', true);
    })
    .on('change', 'select.submit-upon-select', function() {
        // Trigger blur event if element was clicked in the beginning

        if ($(this).data('wasClicked')) {
            $(this).trigger('blur');
        }
    })
    .on('focusout keyup keypress keydown select', 'select.submit-upon-select', function(event) {
        var shouldSubmit = event.type === 'keyup' ? event.which === 13 : $(this).data('wasClicked'),
            is_default = $('option:selected', this).prop('defaultSelected');

        // Submit only if value has changed and either enter was pressed or
        // select was opened by click
        if (!is_default && shouldSubmit) {
            if ($(this).data('formaction')) {
                $(this.form).attr('action', $(this).data('formaction'));
            }
            $(this.form).submit();
            $('option', this).prop('defaultSelected', false).filter(':selected').prop('defaultSelected', true);
            return false;
        }
    });

STUDIP.ready((event) => {
    $('.submit-upon-select', event.target).each(function() {
        var has_default_selected =
            $('option', this).filter(function() {
                return this.defaultSelected;
            }).length > 0;
        if (!has_default_selected) {
            $('option', this)
                .first()
                .prop('defaultSelected', true);
        }
    });
});


// Use select2 for crossbrowser compliant select styling and
// handling
$.fn.select2.amd.define('select2/i18n/de', [], function() {
    return {
        inputTooLong: function(e) {
            var t = e.input.length - e.maximum;
            return $gettext('Bitte %u Zeichen weniger eingeben').replace('%u', t);
        },
        inputTooShort: function(e) {
            var t = e.minimum - e.input.length;
            return $gettext('Bitte %u Zeichen mehr eingeben').replace('%u', t);
        },
        loadingMore: function() {
            return $gettext('Lade mehr Ergebnisse...');
        },
        maximumSelected: function(e) {
            var t = [
                $gettext('Sie können nur %u Eintrag auswählen'),
                $gettext('Sie können nur %u Einträge auswählen')
            ];
            return t[e.maximum === 1 ? 0 : 1].replace('%u', e.maximum);
        },
        noResults: function() {
            return $gettext('Keine Übereinstimmungen gefunden');
        },
        searching: function() {
            return $gettext('Suche...');
        }
    };
});
$.fn.select2.defaults.set('language', 'de');

function createSelect2(element) {
    if ($(element).data('select2')) {
        return;
    }

    let select_classes = $(element)
            .removeClass('select2-awaiting')
            .attr('class'),
        option = $('<option>'),
        width = $(element).outerWidth(true),
        cloned = $(element)
            .clone()
            .css('opacity', 0)
            .appendTo('body'),
        wrapper = $('<div class="select2-wrapper">').css('display', cloned.css('display')),
        placeholder,
        dropdownAutoWidth = $(element).data('dropdown-auto-width')
    ;

    cloned.remove();
    $(wrapper)
        .add(element)
        .css('width', width);

    if ($('.is-placeholder', element).length > 0) {
        placeholder = $('.is-placeholder', element)
            .text()
            .trim();

        option.attr('selected', $(element).val() === '');
        $('.is-placeholder', element).replaceWith(option);
    }

    $(element).select2({
        adaptDropdownCssClass: function() {
            return select_classes;
        },
        allowClear: placeholder !== undefined,
        minimumResultsForSearch: $(element).closest('#sidebar').length > 0 ? 15 : 10,
        placeholder: placeholder,
        dropdownAutoWidth: dropdownAutoWidth,
        dropdownParent: $(element).closest('.ui-dialog,#sidebar,body'),
        templateResult: function(data, container) {
            if (data.element) {
                let option_classes = $(data.element).attr('class'),
                    element_data = $(data.element).data();
                $(container).addClass(option_classes);

                // Allow text color changes (calendar needs this)
                if (element_data.textColor) {
                    $(container).css('color', element_data.textColor);
                }
            }
            return data.text;
        },
        templateSelection: function(data) {
            let result = $('<span class="select2-selection__content">').text(data.text),
                element_data = $(data.element).data();
            if (element_data && element_data.textColor) {
                result.css('color', element_data.textColor);
            }

            if (element_data && element_data.colorClass) {
                result.addClass(element_data.colorClass);
            }

            return result;
        },
        width: 'style'
    });

    $(element)
        .next()
        .addBack()
        .wrapAll(wrapper);
}

STUDIP.ready(function () {
    let forms = window.document.querySelectorAll('.studipform:not(.vueified)');
    if (forms.length > 0) {
        STUDIP.Vue.load().then(({createApp}) => {
            forms.forEach(f => {
                f.classList.add('vueified');

                const app = createApp({
                    data() {
                        let params = JSON.parse(f.dataset.inputs);
                        params.STUDIPFORM_REQUIRED = f.dataset.required ? JSON.parse(f.dataset.required) : [];
                        params.STUDIPFORM_SERVERVALIDATION = f.dataset.server_validation > 0;
                        params.STUDIPFORM_DISPLAYVALIDATION = false;
                        params.STUDIPFORM_VALIDATIONNOTES = [];
                        params.STUDIPFORM_AUTOSAVEURL = f.dataset.autosave;
                        params.STUDIPFORM_VALIDATION_URL = f.dataset.validation_url;
                        params.STUDIPFORM_VALIDATED = false;
                        params.STUDIPFORM_REDIRECTURL = f.dataset.url;
                        params.STUDIPFORM_INPUTS_ORDER = [];
                        params.STUDIPFORM_SELECTEDLANGUAGES = {};
                        for (let i in JSON.parse(f.dataset.inputs)) {
                            params.STUDIPFORM_INPUTS_ORDER.push(i);
                        }
                        return params;
                    },
                    methods: {
                        submit: function (e) {
                            if (this.STUDIPFORM_VALIDATED) {
                                return;
                            }
                            this.STUDIPFORM_VALIDATIONNOTES = [];
                            this.STUDIPFORM_DISPLAYVALIDATION = true;

                            //validation:
                            let validation_promise = this.validate();
                            validation_promise.then(validated => {
                                if (!validated) {
                                    this.$el.scrollIntoView({
                                        behavior: 'smooth'
                                    });
                                    return;
                                }

                                if (this.STUDIPFORM_AUTOSAVEURL) {
                                    let params = this.getFormValues();
                                    params.STUDIPFORM_AUTOSTORE = 1;

                                    $.ajax({
                                        url: this.STUDIPFORM_AUTOSAVEURL,
                                        data: params,
                                        type: 'post',
                                        success(output) {
                                            if (output === 'STUDIPFORM_STORE_SUCCESS' && this.STUDIPFORM_REDIRECTURL) {
                                                //The form has been stored successfully:
                                                window.location.href = this.STUDIPFORM_REDIRECTURL;
                                            } else if (output !== 'STUDIPFORM_STORE_SUCCESS') {
                                                Report.error($gettext('Es ist ein Fehler aufgetreten'), output);
                                            }
                                        }
                                    });
                                } else {
                                    this.STUDIPFORM_VALIDATED = true;
                                    this.$el.submit();
                                }
                            });
                            e.preventDefault();
                        },
                        getFormValues() {
                            let params = {
                                security_token: this.$refs.securityToken.value
                            };
                            Object.keys(this.$data).forEach(i => {
                                if (!i.startsWith('STUDIPFORM_')) {
                                    if (typeof this.$data[i] === 'boolean') {
                                        params[i] = this.$data[i] ? 1 : 0;
                                    } else {
                                        params[i] = this.$data[i];
                                    }
                                }
                            });
                            return params;
                        },
                        validate() {
                            this.STUDIPFORM_VALIDATIONNOTES = [];

                            return new Promise((resolve) => {
                                let validated = this.$el.checkValidity();

                                $(this.$el).find('input, select, textarea').each(function () {
                                    if (!this.validity.valid) {
                                        let note = {
                                            name: this.name,
                                            label: $(this.labels[0]).find('.textlabel').text(),
                                            description: $gettext('Fehler!'),
                                            describedby: this.id
                                        };
                                        if ($(this).data('validation_requirement')) {
                                            note.description = $(this).data('validation_requirement');
                                        }
                                        if (this.validity.tooShort) {
                                            note.description = $gettext(
                                                'Geben Sie mindestens %{min} Zeichen ein.',
                                                { min: this.minLength }
                                            );
                                        }
                                        if (this.validity.valueMissing) {
                                            if (this.type === 'checkbox') {
                                                note.description = $gettext('Dieses Feld muss ausgewählt sein.');
                                            } else {
                                                if (this.minLength > 0) {
                                                    note.description = $gettext(
                                                        'Hier muss ein Wert mit mindestens %{min} Zeichen eingetragen werden.',
                                                        { min: this.minLength }
                                                    );
                                                } else {
                                                    note.description = $gettext('Hier muss ein Wert eingetragen werden.');
                                                }

                                            }
                                        }
                                        this.STUDIPFORM_VALIDATIONNOTES.push(note);
                                    }
                                });

                                if (this.STUDIPFORM_SERVERVALIDATION) {
                                    let params = this.getFormValues();
                                    if (this.STUDIPFORM_AUTOSAVEURL) {
                                        params.STUDIPFORM_AUTOSTORE = 1;
                                    }
                                    params.STUDIPFORM_SERVERVALIDATION = 1;

                                    $.post(this.STUDIPFORM_VALIDATION_URL, params).done((output) => {
                                        for (let i in output) {
                                            this.STUDIPFORM_VALIDATIONNOTES.push({
                                                name: output[i].name,
                                                label: output[i].label,
                                                description: output[i].error,
                                                describedby: null
                                            });
                                        }
                                        validated = this.STUDIPFORM_VALIDATIONNOTES.length < 1;
                                        resolve(validated);
                                    });
                                } else {
                                    resolve(validated);
                                }
                            });
                        },
                        setInputs(inputs) {
                            for (const [key, value] of Object.entries(inputs)) {
                                if (this[key] !== undefined) {
                                    this[key] = value;
                                }
                            }
                        },
                        selectLanguage(input_name, language_id) {
                            let languages = {
                                ...this.STUDIPFORM_SELECTEDLANGUAGES
                            };
                            languages[input_name] = language_id;
                            this.STUDIPFORM_SELECTEDLANGUAGES = languages;
                        }
                    },
                    computed: {
                        ordererValidationNotes: function () {
                            let orderedNotes = [];
                            for (let i in this.STUDIPFORM_INPUTS_ORDER) {
                                for (let k in this.STUDIPFORM_VALIDATIONNOTES) {
                                    if (this.STUDIPFORM_VALIDATIONNOTES[k].name === this.STUDIPFORM_INPUTS_ORDER[i]) {
                                        orderedNotes.push(this.STUDIPFORM_VALIDATIONNOTES[k]);
                                    }
                                }
                            }
                            return orderedNotes;
                        }
                    },
                    mounted() {
                        if (this.$el.closest('.ui-dialog')) {
                            const cancelButton = this.$el.querySelector('footer .button.cancel:last-of-type');
                            if (cancelButton) {
                                cancelButton.addEventListener('click', (e) => {
                                    Dialog.close();
                                    e.preventDefault();
                                })
                            }
                        }
                    }
                });
                app.mount(f);
            });
        });
    }

    /*
     * Form elements with the "simplevue" class are meant for forms that just need some vue components
     * to do something fancy inside the form but which do not need the full functionality of the form builder.
     */
    let simple_vue_items = document.querySelectorAll('form .simplevue:not(.vueified)');
    if (simple_vue_items.length > 0) {
        STUDIP.Vue.load().then(({createApp}) => {
            simple_vue_items.forEach(f => {
                f.classList.add('vueified');
                createApp().mount(f);
            });
        });
    }

    // Well, this is really nasty: Select2 can't determine the select
    // element's width if it is hidden (by itself or by its parent).
    // This is due to the fact that elements are not rendered when hidden
    // (which seems pretty obvious when you think about it) but elements
    // only have a width when they are rendered (pretty obvious as well).
    //
    // Thus, we need to handle the visible elements first and apply
    // select2 directly.
    $('select.nested-select:visible').each(function() {
        createSelect2(this);
    });

    // The hidden need a little more love. The only, almost sane-ish
    // solution seems to be to attach a mutation observer to the closest
    // visible element from the requested select element and observe style,
    // class and attribute changes in order to detect when the select
    // element itself will become visible. Pretty straight forward, huh?
    $('select.nested-select:hidden:not(.select2-awaiting)').each(function() {
        var observer = new window.MutationObserver(onDomChange);
        observer.observe($(this).closest(':visible')[0], {
            attributeOldValue: true,
            attributes: true,
            attributeFilter: ['style', 'class', 'hidden'],
            characterData: false,
            childList: true,
            subtree: true
        });

        $(this).addClass('select2-awaiting');
    });

    function onDomChange(mutations, observer) {
        mutations.forEach(function(mutation) {
            let targets = Array.from(mutation.target.querySelectorAll('select.select2-awaiting'));
            if (mutation.target.matches('select.select2-awaiting')) {
                targets.push(mutation.target);
            }
            targets = $(targets).filter(':visible');

            if (targets.length > 0) {
                targets.removeClass('select2-awaiting').each(function() {
                    createSelect2(this);
                });
                observer.disconnect();
            }
        });
    }

    // Unfortunately, this code needs to be duplicated because jQuery
    // namespacing kind of sucks. If the below change handler is namespaced
    // and we trigger that namespaced event here, still all change handlers
    // will execute (which is bad due to $(select).change(form.submit())).
    $('select:not([multiple])').each(function() {
        $(this).toggleClass('has-no-value', this.value === '');
    });
});

$(document)
    .on('change', 'select:not([multiple])', function() {
        $(this).toggleClass('has-no-value', this.value === '');
    })
    .on('dialog-close', function(event, data) {
        $('select.nested-select', data.dialog).each(function() {
            if (!$(this).data('select2')) {
                return;
            }
            $(this).select2('close');
        });
    })
    .on('select2:open', 'select', function() {
        $(this).click();
    });
