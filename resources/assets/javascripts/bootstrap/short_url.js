STUDIP.ready(function() {
    jQuery(document).on('click', '#short_url_action', function(event) {
        event.preventDefault();
        //Get the current path:
        let path = jQuery(event.target).data('path');
        if (path.length < 1) {
            return;
        }

        //Send a request to create a short-URL:
        jQuery.ajax(
            {
                url: STUDIP.URLHelper.getURL('dispatch.php/u/create'),
                data: {
                    path: path
                },
                method: 'POST'
            }
        ).done(function (data) {
            //Copy the Short-URL into the clipboard:
            navigator.clipboard.writeText(data.full_short_url);
            //Open the dialog with the short-URL to allow setting an alias:
            STUDIP.Dialog.fromURL(STUDIP.URLHelper.getURL('dispatch.php/u/alias/' + data.url_id));
        });
    });
});
