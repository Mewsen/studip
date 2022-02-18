const Autoinsert = {
    init: function() {
        jQuery('input[name="autoinsert_type"][type="radio"]').on('change', function(event) {
            const selected = event.target.value
            jQuery('.autoinsert-selection').addClass('hidden-js')
            jQuery('#autoinsert-' + selected).removeClass('hidden-js')
        })
    }
}

export default Autoinsert
