import { $gettext } from './gettext';

const Overlapping = {

    /**
     * Initialize Select2 select boxes.
     * @returns {undefined}
     */
    init: function () {
        let base_selection = $('#base-version-select');
        base_selection.select2({
            placeholder: $gettext('Studiengangteil suchen'),
            minimumInputLength: 3,
            ajax: {
                url: STUDIP.URLHelper.getURL('dispatch.php/admin/overlapping/base_version'),
                dataType: 'json'
            }
        });

        $('#comp-versions-select').select2({
            placeholder: $gettext('Optional weitere Studiengangteile (max. 5)'),
            minimumInputLength: 3,
            ajax: {
                url: STUDIP.URLHelper.getURL('dispatch.php/admin/overlapping/comp_versions'),
                dataType: 'json'
            }
        });

        $('#fachsem-select').select2({
            placeholder: $gettext('Fachsemester auswählen (optional)')
        });
        $('#semtype-select').select2({
            placeholder: $gettext('Veranstaltungstyp auswählen (optional)')
        });
        base_selection.on('select2:select', function () {
            $('#comp-versions-select').val(null).trigger('change');
            $.ajax({
                url: STUDIP.URLHelper.getURL('dispatch.php/admin/overlapping/comp_versions'),
                dataType: 'json',
                data: {
                    version_id: $('#base-version-select').select2('data')[0].id
                },
                success: function(data) {
                    if (data.results.length) {
                        let inputlength = 3;
                        if (data.results.length < 4) {
                            inputlength = 0;
                        }
                        $('#comp-versions-select').select2({
                            placeholder: $gettext('Optional weitere Studiengangteile (max. 5)'),
                            minimumInputLength: inputlength,
                            ajax: {
                                url: STUDIP.URLHelper.getURL('dispatch.php/admin/overlapping/comp_versions',
                                    {'version_id': $('#base-version-select').select2('data')[0].id}),
                                dataType: 'json'
                            }
                        });
                    } else {
                        base_selection.select2({
                            placeholder: $gettext('Keine weitere Auswahl möglich')
                        });
                        base_selection.prop('disabled', true).trigger('change');
                    }
                }
            });
        });

        $('span.mvv-overlapping-exclude').on('click', function () {
            const conflict_id = $(this).data('mvv-ovl-conflict');
            $.ajax({
                method: 'get',
                url: STUDIP.URLHelper.getURL('dispatch.php/admin/overlapping/exclude'),
                data: {
                    'conflict_id': conflict_id
                },
                success() {
                    $('.mvv-overlapping-exclude').each(function () {
                        if ($(this).data('mvv-ovl-conflict') === conflict_id) {
                            $(this).toggleClass('mvv-overlapping-invisible');
                        }
                        $(this).attr('title', $gettext('Veranstaltung berücksichtigen'));
                    });
                    $('.mvv-overlapping-invisible').attr('title', $gettext('Veranstaltung nicht berücksichtigen'));

                }
            })
            return false;
        });
    }
};

export default Overlapping;
