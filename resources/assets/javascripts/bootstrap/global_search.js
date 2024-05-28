STUDIP.domReady(() => {
    // Clear search term
    $('#globalsearch-clear').on('click', function() {
        var before = $('#globalsearch-input').val();
        STUDIP.GlobalSearch.resetSearch();

        if ($('html').is('.responsive-display') && before.length === 0) {
            STUDIP.GlobalSearch.toggleSearchBar(false);
        }

        return false;
    });

    // Bind icon click to performing search.
    $('#globalsearch-icon').on('click', function() {
        STUDIP.GlobalSearch.doSearch();

        if ($('html').hasClass('responsified')) {
            var input = $('#globalsearch-input');
            input.toggleClass('hidden-small-down', false);
            input.focus();
        }

        return false;
    });

    // Enlarge search input on focus and show hints.
    $('#globalsearch-input').on('focus', function() {
        STUDIP.GlobalSearch.toggleSearchBar(true, false);
    });

    // Start search on Enter
    $('#globalsearch-input').on('keypress', function(e) {
        if (e.which === 13) {
            STUDIP.GlobalSearch.doSearch();
            return false;
        }
    });
    $('#globalsearch-input').on('keypress', function(e) {
        if (e.which === 13) {
            STUDIP.GlobalSearch.doSearch();
            return false;
        }
    });
    $('#globalsearch-searchbar').on('keydown', function(e) {
        if (e.originalEvent.code === 'ArrowDown') {
            if ($('#globalsearch-list [role=listitem]:focus').length === 0) {
                $('#globalsearch-list [role=listitem]:visible').first().focus();
            } else {
                let n = $('#globalsearch-list [role=listitem]:focus').next();
                if (n.length > 0 && n.is('[role=listitem]:visible')) {
                    n.focus();
                } else {
                    n = $('#globalsearch-list [role=listitem]:focus').parent().next().find('[role=listitem]:visible').first();
                    if (n.length > 0) {
                        n.focus();
                    } else {
                        $('#globalsearch-list [role=listitem]:visible').first().focus();
                    }
                }
            }
            return false;
        }
        if (e.originalEvent.code === 'ArrowUp') {
            if ($('#globalsearch-list [role=listitem]:focus').length === 0) {
                $('#globalsearch-list [role=listitem]:visible').last().focus();
            } else {
                let n = $('#globalsearch-list [role=listitem]:focus').prev();
                if (n.length > 0 && n.is('[role=listitem]:visible')) {
                    n.focus();
                } else {
                    n = $('#globalsearch-list [role=listitem]:focus').parent().prev().find('[role=listitem]:visible').last();
                    if (n.length > 0) {
                        n.focus();
                    } else {
                        $('#globalsearch-list [role=listitem]:visible').last().focus();
                    }
                }
            }
            return false;
        }
    });


    // Close search on click on page.
    $('div#flex-header, div#layout_page, #layout_footer').on('click', function() {
        if (!$('#globalsearch-input').hasClass('hidden-js')) {
            STUDIP.GlobalSearch.toggleSearchBar(false, false);
        }
    });

    // Show/hide hints on click.
    $('#globalsearch-togglehints').on('click', function() {
        var toggle = $('#globalsearch-togglehints'),
            currentText = toggle.text();

        toggle.text(toggle.data('toggle-text').trim());
        toggle.data('toggle-text', currentText);

        toggle.toggleClass('open');
    });

    // Delegate events on result container so we don't have to bind them
    // one by one
    $('#globalsearch-results').on('click', '.globalsearch-category a', function() {
        var category = $(this)
            .closest('.globalsearch-category')
            .data('category');
        STUDIP.GlobalSearch.expandCategory(category);
        return false;
    });

    // Key bindings.
    $(document).keydown(function(e) {
        // Don't do anything if a dialog is open
        if (STUDIP.Dialog.stack.length > 0) {
            return;
        }

        // ctrl + space
        if (e.which === 32 && e.ctrlKey && !e.altKey && !e.metaKey && !e.shiftKey) {
            e.preventDefault();
            if ($('#globalsearch-searchbar').hasClass('is-visible')) {
                STUDIP.GlobalSearch.toggleSearchBar(false, false);
                $('#globalsearch-input').blur();
            } else {
                $('#globalsearch-input').focus();
            }
            // escape
        } else if (e.which === 27 && !e.ctrlKey && !e.altKey && !e.metaKey && !e.shiftKey) {
            e.preventDefault();
            STUDIP.GlobalSearch.toggleSearchBar(false, true);
        }
    });

    // Start searching 750 ms after user stopped typing or content was added
    // by pasting text via right-click.
    $('#globalsearch-input').on('keyup paste',
        _.debounce(function() {
            STUDIP.GlobalSearch.doSearch();
        }, 750)
    );
});
