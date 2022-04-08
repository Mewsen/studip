const Autoinsert = {
    init: function() {
        $('input[name="autoinsert_type"][type="radio"]').on('change', function(event) {
            const selected = event.target.value
            $('.autoinsert-selection').addClass('hidden-js')
            $('#autoinsert-' + selected).removeClass('hidden-js')
        })
    }
}

export default Autoinsert
