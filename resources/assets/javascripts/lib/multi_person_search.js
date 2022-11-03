import { $gettext } from './gettext.js';

const MultiPersonSearch = {
    init: function() {
        $('.multi_person_search_link').each(function() {
            // init js form
            $(this).attr('href', $(this).data('js-form'));
            // init form if it is loaded via ajax
            $(this).on('dialog-open', function(event, parameters) {
                MultiPersonSearch.dialog(
                    $(parameters.dialog)
                        .find('.mpscontainer')
                        .data('dialogname')
                );
            });
        });
    },

    dialog: function(name) {
        var count_template = _.template($gettext('Sie haben <%= count %> Personen ausgewählt'));

        this.name = name;

        $('#' + name + '_selectbox').select2({
            ajax: {
                url: STUDIP.URLHelper.getURL('dispatch.php/multipersonsearch/ajax_search/' + name),
                dataType: 'json',
                delay: 250
            },
            //TODO: alle auswählen und alle abwählen
        });

        $('#' + this.name).on('keyup keypress', function(e) {
            var code = e.keyCode || e.which;
            if (code == 13) {
                e.preventDefault();
                MultiPersonSearch.search();
                return false;
            }
        });

        $('#' + this.name + '_selectbox').change(function() {
            MultiPersonSearch.count();
        });

        $('#' + this.name + ' .quickfilter').click(function(event) {
            event.preventDefault();
            MultiPersonSearch.loadQuickfilter($(this).data('quickfilter'));
            return false;
        });
    },

    loadQuickfilter: function(title) {
        MultiPersonSearch.removeAllNotSelected();

        let count = 0;
        $('#' + this.name + ' .quickfilter-value[data-quickfilter_id="' + title + '"]').each(function() {
            count += MultiPersonSearch.append(
                $(this).data('value'),
                $(this).text(),
                MultiPersonSearch.isAlreadyMember($(this).data('value'))
            );
        });
        MultiPersonSearch.refresh();
    },

    isAlreadyMember: function(user_id) {
        if ($('#' + this.name + '_selectbox_default option[value="' + user_id + '"]').length > 0) {
            return true;
        } else {
            return false;
        }
    },

    search: function() {
        var searchterm = $('#' + this.name + '_searchinput').val(),
            name = this.name,
            not_found_template = _.template(
                $gettext('Es wurden keine neuen Ergebnisse für "<%= needle %>" gefunden.')
            );
        $.getJSON(
            STUDIP.URLHelper.getURL('dispatch.php/multipersonsearch/ajax_search/' + this.name, { s: searchterm }),
            function(data) {
                MultiPersonSearch.removeAllNotSelected();
                var searchcount = 0;
                $.each(data, function(i, item) {
                    searchcount += MultiPersonSearch.append(
                        item.user_id,
                        item.avatar + ' -- ' + item.text,
                        item.member
                    );
                });
                MultiPersonSearch.refresh();

                if (searchcount == 0) {
                    MultiPersonSearch.append('--', not_found_template({ needle: searchterm }), true);
                    MultiPersonSearch.refresh();
                }
            }
        );
        return false;
    },

    selectAll: function() {
        $('#' + this.name + '_selectbox').select2();
        this.count();
    },

    unselectAll: function() {
        $('#' + this.name + '_selectbox').select2('deselect_all');
        this.count();
    },

    removeAll: function() {
        $('#' + this.name + '_selectbox option').remove();
        this.refresh();
    },

    removeAllNotSelected: function() {
        $('#' + this.name + '_selectbox option:not(:selected)').remove();
        this.refresh();
    },

    resetSearch: function() {
        $('#' + this.name + '_searchinput').val('');
        MultiPersonSearch.removeAllNotSelected();
    },

    append: function(value, text) {
        console.debug('append');
        if ($('#' + this.name + '_selectbox option[value=' + value + ']').length == 0) {
            console.debug('new option');
            let new_option = new Option(text, value, true, true);
            $('#' + this.name + '_selectbox').append(new_option).trigger('change');
            return 1;
        }
        return 0;
    },

    refresh: function() {
        //$('#' + this.name + '_selectbox').select2();
        MultiPersonSearch.count();
    },

    count: function() {
        $('#' + this.name + '_count').text($('#' + this.name + '_selectbox option:enabled:selected').length);
    }
};

export default MultiPersonSearch;
