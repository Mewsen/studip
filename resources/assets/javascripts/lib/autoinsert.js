const Autoinsert = {
    init: function() {
        $('input[name="autoinsert_type"][type="checkbox"]').on('change', function(event) {
            if (this.checked) {
                $('#autoinsert-' + this.value).removeClass('hidden-js')
            } else {
                $('#autoinsert-' + this.value).addClass('hidden-js')
            }
        })
    }
}

export default Autoinsert
