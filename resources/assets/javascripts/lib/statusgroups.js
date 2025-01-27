const Statusgroups = {
    ajax_endpoint: false,
    apply() {
        $('.movable tbody').sortable({
            axis: 'y',
            handle: '.drag-handle',
            helper(event, ui) {
                ui.children().each(function () {
                    $(this).width($(this).width());
                });
                return ui;
            },
            start() {
                $(this)
                    .closest('table')
                    .addClass('nohover');
            },
            stop(event, ui) {
                const table = $(this).closest('table');
                const group = table.attr('id');
                const user = ui.item.data('userid');
                const position = $(ui.item).prevAll().length;

                table.removeClass('nohover');

                $.ajax({
                    type: 'POST',
                    url: Statusgroups.ajax_endpoint,
                    dataType: 'html',
                    data: {group: group, user: user, pos: position},
                    async: false
                }).done(function (data) {
                    $('tbody', table).html(data);
                    Statusgroups.apply();
                });
            }
        });
    },

    initInputs() {
        $('input[name="numbering_type"]').on('click', () => {
            const type = $('input[name="numbering_type"]:checked').val();
            const disabled = parseInt(type, 10) === 2;

            $('input[name="startnumber"]')
                .prop('disabled', disabled)
                .toggle(!disabled);
        });
    }
};

export default Statusgroups;
