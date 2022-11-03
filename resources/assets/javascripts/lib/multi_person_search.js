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
        this.name = name;

        $('#' + name + '_selectbox').select2({
            ajax: {
                url: STUDIP.URLHelper.getURL('dispatch.php/multipersonsearch/ajax_search/' + name),
                dataType: 'json',
                delay: 250
            },
            //TODO: alle auswählen und alle abwählen
        });

        $('#' + this.name + '_selectbox').change(function() {
            MultiPersonSearch.count();
        });

        $('#' + this.name + ' .quickfilter').click(function(event) {
            event.preventDefault();
            MultiPersonSearch.loadQuickfilter($(this).data('quickfilter'));
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
        if ($('#' + this.name + '_selectbox option[value="' + user_id + '"]').length > 0) {
            return true;
        } else {
            return false;
        }
    },

    selectAll: function() {
        $('#' + this.name + '_selectbox option').attr('selected', 'selected');
        this.count();
    },

    unselectAll: function() {
        $('#' + this.name + '_selectbox option').attr('selected', 'false');
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
        if ($('#' + this.name + '_selectbox option[value=' + value + ']').length == 0) {
            let new_option = new Option(text, value, true, true);
            $('#' + this.name + '_selectbox').append(new_option).trigger('change');
        }
    },

    refresh: function() {
        MultiPersonSearch.count();
    },

    count: function() {
        $('#' + this.name + '_count').text($('#' + this.name + '_selectbox option:enabled:selected').length);
    }
};

export default MultiPersonSearch;
