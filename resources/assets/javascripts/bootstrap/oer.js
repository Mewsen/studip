import Quicksearch from '../../../vue/components/Quicksearch.vue';

STUDIP.domReady(() => {
    jQuery(".serversettings .index_server a").on("click", function () {
        var host_id = jQuery(this).closest("tr").data("host_id");
        var active = jQuery(this).is(".checked") ? 0 : 1;
        var a = this;
        jQuery.ajax({
            "url": STUDIP.ABSOLUTE_URI_STUDIP + "dispatch.php/oer/admin/toggle_index_server",
            "data": {
                'host_id': host_id,
                'active': active
            },
            "type": "post",
            "success": function (html) {
                jQuery(a).html(html);
                if (active) {
                    jQuery(a).addClass("checked").removeClass("unchecked");
                } else {
                    jQuery(a).addClass("unchecked").removeClass("checked");
                }
            }
        });
        return false;
    });
    jQuery(".serversettings .active a").on("click", function () {
        var host_id = jQuery(this).closest("tr").data("host_id");
        var active = jQuery(this).is(".checked") ? 0 : 1;
        var a = this;
        jQuery.ajax({
            "url": STUDIP.ABSOLUTE_URI_STUDIP + "dispatch.php/oer/admin/toggle_server_active",
            "data": {
                'host_id': host_id,
                'active': active
            },
            "type": "post",
            "success": function (html) {
                jQuery(a).html(html);
                if (active) {
                    jQuery(a).addClass("checked").removeClass("unchecked");
                } else {
                    jQuery(a).addClass("unchecked").removeClass("checked");
                }
            }
        });
        return false;
    });

});
