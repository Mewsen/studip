import { $gettext } from "../lib/gettext";

$(function() {
    if ($('#exam_timer').length > 0) {
        const exam_timer = $('#exam_timer');
        const user_end_time = exam_timer.data('time') + Math.floor(Date.now() / 1000);
        const timer_id = setInterval(() => {
            const remaining_time = user_end_time - Math.floor(Date.now() / 1000);

            // update timer
            exam_timer.children('.time').text(Math.round(remaining_time / 60));

            if (remaining_time < 180 && !exam_timer.hasClass('alert')) {
                exam_timer.addClass('alert');
            }

            if (remaining_time < 0) {
                if (document.jsfrm) {
                    clearInterval(timer_id);
                    document.jsfrm.removeAttribute('data-secure');
                    document.jsfrm.forced.value = 1;
                    document.jsfrm.submit();
                } else {
                    location.reload();
                }
            }
        }, 1000);

        exam_timer.draggable();
    }

    if ($('#list').length > 0) {
        const assignment = $('#list').data('assignment');

        $('#list').sortable({
            axis: 'y',
            containment: 'parent',
            handle: '.drag-handle',
            helper(event, element) {
                element.children().width((index, width) => width);

                return element;
            },
            tolerance: 'pointer',
            update() {
                $.post(
                    STUDIP.URLHelper.getURL('dispatch.php/vips/sheets/move_exercise', { assignment_id: assignment }),
                    $('#list').sortable('serialize')
                );
            }
        });

        $('#list > tr').on('keydown', function (event) {
            if (event.key === 'ArrowUp' && event.target === this) {
                $(this).prev().before(this);
            } else if (event.key === 'ArrowDown' && event.target === this) {
                $(this).next().after(this);
            } else {
                return;
            }

            $(this).focus();
            $('#list').sortable('option').update();
            event.preventDefault();
        });
    }

    $(document).on('click', '.add_ip_range', function (event) {
        const input = $(this).closest('fieldset').find('input[name=ip_range]');

        input.val(input.val() + ' ' + $(this).attr('data-value'));
        event.preventDefault();
    });

    $(document).on('input', '.validate_ip_range', function () {
        const ip_ranges = $(this).val().split(/[ ,]+/);
        let message = '';

        for (const ip_range of ip_ranges) {
            if (
                ip_range.length > 0
                && ip_range.charAt(0) !== '#'
                && !ip_range.match(/^[\d.]+(\/\d+|-[\d.]+)?$/)
                && !ip_range.match(/^[\da-fA-F:]+(\/\d+|-[\da-fA-F:]+)?$/)
            ) {
                message = $gettext('Der IP-Zugriffsbereich ist ungültig.');
            }
        }

        this.setCustomValidity(message);
    });

    $(document).on('click', '.vips_file_upload', function (event) {
        $(this).closest('form').find('.file_upload').click();
        event.preventDefault();
    });

    $(document).on('change', '.file_upload.attach', function () {
        const button = $(this).closest('form').find('.vips_file_upload');

        if (this.files && this.files.length > 1) {
            button.text(button.data('label').replace('%d', this.files.length));
            button.next('.file_upload_hint').show();
        } else if (this.files) {
            button.text(this.files[0].name);
            button.next('.file_upload_hint').show();
        }
    });

    $(document).on('change', '.file_upload.inline', function (event) {
        const textarea = $(this).closest('form').find('.download');
        const reader = new FileReader();

        if (this.files && this.files.length > 0) {
            reader.onload = function () {
                textarea.val(reader.result);
            };
            reader.onerror = function () {
                STUDIP.Dialog.show(reader.error.message, {
                    title: $gettext('Fehler beim Hochladen'),
                    size: 'fit',
                    wikilink: false,
                    dialogClass: 'studip-confirmation'
                });
            }
            reader.readAsText(this.files[0]);
        }
        event.preventDefault();
    });

    $(document).on('click', '.vips_file_download', function (event) {
        const text = $(this).closest('form').find('.download').val();
        const link = $(this).closest('form').find('a[download]');
        const blob = new Blob([text], {type: 'text/plain; charset=UTF-8'});

        link.attr('href', URL.createObjectURL(blob));
        link[0].click();
        event.preventDefault();
    });

    $('.sortable_list').sortable({
        axis: 'y',
        containment: 'parent',
        items: '> .sortable_item',
        tolerance: 'pointer'
    });

    $(document).on('keydown', '.sortable_item', function (event) {
        if (event.key === 'ArrowUp' && event.target === this) {
            $(this).prev('.sortable_item:visible').before(this);
        } else if (event.key === 'ArrowDown' && event.target === this) {
            $(this).next('.sortable_item:visible').after(this);
        } else {
            return;
        }

        $(this).focus();
        event.preventDefault();
    });

    $(document).on('click', '.textarea_toggle', function (event) {
        const toggle = $(this).closest('.size_toggle');
        const items = toggle.find('.character_input');

        const name = items[0].name;
        items[0].name = items[1].name;
        items[1].name = name;

        const value = items[0].value;
        items[0].value = items[1].value;
        items[1].value = value;

        if (STUDIP.wysiwyg.getEditor && STUDIP.wysiwyg.getEditor(items[1])) {
            STUDIP.wysiwyg.getEditor(items[1]).setData(value);
        }

        toggle.toggleClass('size_large').toggleClass('size_small');
        event.preventDefault();
    });

    $(document).on('change', '.tb_layout', function () {
        const toggle = $(this).closest('fieldset').find('.size_toggle');

        toggle.find('.small_input').toggleClass('monospace', $(this).val() === 'code');

        if (
            $(this).val() === '' && toggle.hasClass('size_large')
            || $(this).val() === 'code' && toggle.hasClass('size_large')
            || $(this).val() === 'markup' && toggle.hasClass('size_small')
        ) {
            toggle.find('.textarea_toggle').click();
        }
    });

    $(document).on('click', '.choice_list .add_dynamic_row', function () {
        $(this).closest('fieldset').find('.choice_select').each(function () {
            const template = $(this).children('.template').last();
            const clone = template.clone(true).removeClass('template');
            const index = template.data('index');

            template.data('index', index + 1);
            clone.insertBefore(template);
            clone.find('input[data-value]').each(function () {
                $(this).attr('value', index);
                $(this).removeAttr('data-value');
            });
        });
    });

    $(document).on('change', '.choice_list input', function () {
        const index = $(this).closest('.dynamic_row').data('index');
        const items = $(this).closest('fieldset').find('.choice_select');

        items.children().filter(function () {
            return $(this).data('index') === index;
        }).children('span').text($(this).val());
    });

    $(document).on('click', '.choice_list .delete_dynamic_row', function () {
        const index = $(this).closest('.dynamic_row').data('index');
        const items = $(this).closest('fieldset').find('.choice_select');

        items.children().filter(function () {
            return $(this).data('index') === index;
        }).remove();
    });

    $('.dynamic_list').each(function () {
        $(this).children('.dynamic_row').each(function (i) {
            $(this).data('index', i);
        });
    });

    $(document).on('click', '.add_dynamic_row', function (event) {
        const container = $(this).closest('.dynamic_list');
        const template = container.children('.template').last();
        const clone = template.clone(true).removeClass('template');
        const index = template.data('index');

        template.data('index', index + 1);
        clone.insertBefore(template);
        clone.find('input[data-name], select[data-name], textarea[data-name]').each(function () {
            if ($(this).data('name').indexOf(':') === 0) {
                $(this).data('name', $(this).data('name').substr(1) + '[' + index + ']');
            } else {
                $(this).attr('name', $(this).data('name') + '[' + index + ']');
                $(this).removeAttr('data-name');
            }
        });
        clone.find('input[data-value], select[data-value], textarea[data-value]').each(function () {
            if ($(this).data('value').indexOf(':') === 0) {
                $(this).data('value', $(this).data('value').substr(1));
            } else {
                $(this).attr('value', index);
                $(this).removeAttr('data-value');
            }
        });
        clone.find('.wysiwyg-hidden:not(.template *)').toggleClass('wysiwyg wysiwyg-hidden');
        clone.find('.add_dynamic_row:visible').click();
        event.preventDefault();
    });

    $(document).on('click', '.delete_dynamic_row', function (event) {
        $(this).closest('.dynamic_row').remove();
        event.preventDefault();
    });

    $(document).on('click', '.solution-toggle', function (event) {
        if ($(this).closest('.solution').length > 0) {
            $(this).closest('.solution').toggleClass('solution-closed');
        } else if ($('.arrow_all').first().css('display') !== 'none') {
            $('.arrow_all').toggle();
            $('.solution').removeClass('solution-closed');
        } else {
            $('.arrow_all').toggle();
            $('.solution').addClass('solution-closed');
        }

        $(document.body).trigger('sticky_kit:recalc');
        event.preventDefault();
    });

    $(document).on('click', '.edit_solution', function (event) {
        const tabs = $(this).closest('.vips_tabs');

        tabs.removeClass('edit-hidden');
        tabs.find('.wysiwyg').attr('name', 'commented_solution');
        tabs.tabs('option', 'active', 0);
        event.preventDefault();
    });

    // add select2 to modal dialog including selects with optgroups
    $(document).on('dialog-open', function (event, parameters) {
        $('.vips_nested_select').select2({
            minimumResultsForSearch: 12,
            dropdownParent: $(parameters.dialog).closest('.ui-dialog, body'),
            matcher(params, data) {
                const originalMatcher = $.fn.select2.defaults.defaults.matcher;
                const result = originalMatcher(params, data);

                if (result && result.children && data.children && data.children.length) {
                    if (data.children.length !== result.children.length &&
                        data.text.toLowerCase().includes(params.term.toLowerCase())) {
                        result.children = data.children;
                    }
                }

                return result;
            }
        });
    });

    $('.assignment_type').change(function () {
        $('#assignment').attr('class', $(this).val());

        if ($(this).val() === 'exam') {
            $('#exam_length input').attr('disabled', null);
        } else {
            $('#exam_length input').attr('disabled', 'disabled');
        }

        if ($(this).val() === 'selftest') {
            $('#end_date input').attr('required', null);
            $('#end_date span').removeClass('required');
        } else {
            $('#end_date input').attr('required', 'required');
            $('#end_date span').addClass('required');
        }
    });

    $('.rh_select_type').change(function () {
        $(this).parent().next('table').toggleClass('rh_single');
    });

    STUDIP.Vips.vips_post_render(document);
});
