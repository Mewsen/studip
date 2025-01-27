function vips_post_render(element) {
    $(element).find('.rh_list').sortable({
        tolerance: 'pointer',
        connectWith: '.rh_list',
        update(event, ui) {
            if (ui.sender) {
                ui.item.find('input').val($(this).data('group'));
            }
        },
        over() {
            $(this).addClass('hover');
        },
        out() {
            $(this).removeClass('hover');
        },
        receive(event, ui) {
            const sortable = $(this).not('.multiple');
            const container = sortable.closest('.rh_table').find('.answer_container');

            // default answer container can have more items
            if (sortable.children().length > 1 && !sortable.is(container)) {
                sortable.find('.rh_item').each(function () {
                    if (!ui.item.is(this)) {
                        $(this).find('input').val(-1);
                        $(this).detach().appendTo(container)
                               .css('opacity', 0).animate({opacity: 1});
                    }
                });
            }
        },
    });

    $(element).find('.rh_item').on('keydown', function (event) {
        const sortable = $(this).parent();
        const container = sortable.closest('.rh_table').find('.answer_container');
        let target = $();

        if (sortable.is('.mc_list')) {
            if (event.key === 'ArrowUp') {
                $(this).prev().before(this);
                $(this).focus();
                event.preventDefault();
            } else if (event.key === 'ArrowDown') {
                $(this).next().after(this);
                $(this).focus();
                event.preventDefault();
            }
        } else if (sortable.is(container)) {
            if (event.key === 'ArrowLeft') {
                target = sortable.parent().find('.rh_list').first();
            }
        } else {
            if (event.key === 'ArrowRight') {
                target = container;
            } else if (event.key === 'ArrowUp') {
                target = sortable.parent().prev().find('.rh_list').first();
            } else if (event.key === 'ArrowDown') {
                target = sortable.parent().next().find('.rh_list').first();
            }
        }

        if (target.length) {
            $(this).find('input').val(target.data('group'));
            $(this).appendTo(target).focus();
            event.preventDefault();
        }
    });

    $(element).find('.cloze_select').filter(':contains("\\\\(")').each(function () {
        STUDIP.loadChunk('mathjax').then(({ Hub }) => {
            Hub.Queue(['Typeset', Hub, this]);
        });
    }).select2({
        minimumResultsForSearch: -1,
        templateResult(data) {
            if ($(data.element).children('.MathJax').length) {
                return $(data.element).children('.MathJax').clone();
            } else {
                return data.text;
            }
        },
        templateSelection(data) {
            if ($(data.element).children('.MathJax').length) {
                return $(data.element).children('.MathJax').clone();
            } else {
                return data.text;
            }
        }
    });

    $(element).find('.cloze_item').draggable({
        revert: 'invalid'
    });

    $(element).find('.cloze_drop').droppable({
        accept: '.cloze_item',
        tolerance: 'pointer',
        classes: {
            'ui-droppable-hover': 'hover'
        },
        drop(event, ui) {
            const container = $(this).closest('fieldset').find('.cloze_items');

            if (!$(this).is(container)) {
                $(this).find('.cloze_item').detach().appendTo(container)
                       .css('opacity', 0).animate({opacity: 1})
            }

            ui.draggable.closest('.cloze_drop').find('input').val('');
            ui.draggable.detach().css({top: 0, left: 0}).appendTo(this);
            $(this).find('input').val(ui.draggable.attr('data-value'));
        }
    });

    $(element).find('.vips_tabs').each(function () {
        $(this).tabs({
            active: $(this).hasClass('edit-hidden') ? 1 : 0
        });
    })
}

export { vips_post_render };
