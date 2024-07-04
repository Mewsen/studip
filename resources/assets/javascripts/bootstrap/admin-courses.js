STUDIP.domReady(() => {
    $('.admin-courses-options').find('.options-radio, .options-checkbox').on('click', function () {
        $(this).toggleClass(['options-checked', 'options-unchecked']);
        $(this).attr('aria-checked', $(this).is('.options-checked') ? 'true' : 'false');

        if ($(this).is('.options-radio')) {
            const filterName = $(this).data('filter-name');
            $(`button[data-filter-name="${filterName}"]`)
                .not(this)
                .removeClass('options-checked')
                .addClass('options-unchecked')
                .attr('aria-checked', 'false');
        }
    });
});
