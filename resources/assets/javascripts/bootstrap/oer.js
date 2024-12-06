STUDIP.domReady(() => {
    if (jQuery(".oer_search").length) {
        STUDIP.OER.initSearch();
    }
    jQuery(".serversettings .index_server a").on("click", function () {
        const host_id = jQuery(this).closest("tr").data("host_id");
        const active = jQuery(this).is(".checked") ? 0 : 1;
        jQuery.ajax({
            url: STUDIP.ABSOLUTE_URI_STUDIP + "dispatch.php/oer/admin/toggle_index_server",
            data: {host_id, active},
            type: 'post',
            success(html) {
                jQuery(this).html(html);
                if (active) {
                    jQuery(this).addClass("checked").removeClass("unchecked");
                } else {
                    jQuery(this).addClass("unchecked").removeClass("checked");
                }
            }
        });
        return false;
    });
    jQuery(".serversettings .active a").on("click", function () {
        const host_id = jQuery(this).closest("tr").data("host_id");
        const active = jQuery(this).is(".checked") ? 0 : 1;
        jQuery.ajax({
            url: STUDIP.ABSOLUTE_URI_STUDIP + "dispatch.php/oer/admin/toggle_server_active",
            data: {
                'host_id': host_id,
                'active': active
            },
            type: "post",
            success(html) {
                jQuery(this).html(html);
                if (active) {
                    jQuery(this).addClass("checked").removeClass("unchecked");
                } else {
                    jQuery(this).addClass("unchecked").removeClass("checked");
                }
            }
        });
        return false;
    });

});

STUDIP.ready(() => {
    if ($('.oercampus_editmaterial').length) {

        STUDIP.Vue.load().then(({createApp}) => {
            const app = createApp({
                data() {
                    return {
                        name: $('.oercampus_editmaterial input.oername').val(),
                        logo_url: $('.oercampus_editmaterial .logo_file').data("oldurl") ?? null,
                        customlogo: $('.oercampus_editmaterial .logo_file').data("customlogo") == '1',
                        filename: $('.oercampus_editmaterial .file.drag-and-drop').data("filename"),
                        filesize: $('.oercampus_editmaterial .file.drag-and-drop').data("filesize"),
                        tags: $('.oercampus_editmaterial .oer_tags').data("defaulttags") ?? [],
                        minimumTags: 5
                    };
                },
                mounted: function () {
                    jQuery("#difficulty_slider_edit").slider({
                        range: true,
                        min: 1,
                        max: 12,
                        values: [jQuery("#difficulty_start").val(), jQuery("#difficulty_end").val()],
                        change: function (event, ui) {
                            jQuery("#difficulty_start").val(ui.values[0]);
                            jQuery("#difficulty_end").val(ui.values[1]);
                        }
                    });
                    jQuery('.oercampus_editmaterial').find(':focusable').first().focus();
                },
                methods: {
                    editName: function () {
                        this.name = $('.oername').val();
                    },
                    editImage: function (event) {
                        let reader = new FileReader();
                        reader.addEventListener("load", () => {
                            this.logo_url = reader.result;
                            this.customlogo = true;
                        }, false);
                        reader.readAsDataURL(
                            event.target.files.length > 0
                                ? event.target.files[0]
                                : event.dataTransfer.files[0]
                        );
                    },
                    dropImage: function (event) {
                        window.document.getElementById("oer_logo_uploader").files = event.dataTransfer.files;
                        this.editImage(event);
                    },
                    editFile: function (event) {
                        this.filename = event.target.files[0].name;
                        this.filesize = event.target.files[0].size;
                        if (!this.name) {
                            this.name = this.filename;
                            $('.oername').val(this.name);
                        }
                    },
                    dropFile: function (event) {
                        window.document.getElementById("oer_file").files = event.dataTransfer.files;
                        this.editFile(event);
                    },
                    addTag: function () {
                        if (this.minimumTags < this.tags.length) {
                            this.minimumTags = this.tags.length + 1;
                        } else {
                            this.minimumTags++;
                        }
                    },
                    removeTag: function (i) {
                        this.$delete(this.tags, i);
                        if ((this.minimumTags > this.tags.length) && (this.minimumTags > 5)) {
                            this.minimumTags--;
                        }
                    }
                },
                computed: {
                    displayTags () {
                        const result = this.tags.concat([]);
                        while (result.length < this.minimumTags) {
                            result.push('');
                        }
                        return result;
                    }
                },
            });
            app.mount('.oercampus_editmaterial');
            STUDIP.OER.EditApp = app;
        });
    }
});
