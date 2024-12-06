import {$gettext} from '../lib/gettext';

const OER = {
    periodicalPushData: function () {
        if (jQuery(".comments").length) {
            return {
                'review_id': jQuery("[name=comment]").data("review_id")
            };
        }
    },
    update: function (output) {
        if (output.comments) {
            for (var i = 0; i < output.comments.length; i++) {
                if (jQuery("#comment_" + output.comments[i].comment_id).length === 0) {
                    jQuery(".comments").append(output.comments[i].html).find(":last-child").hide().fadeIn(300);
                }
            }
        }
    },
    requestFullscreen: function (selector) {
        var player = jQuery(selector)[0];
        if (!player) {
            window.alert($gettext('Kein passendes Element für Vollbildmodus.'));
            return;
        }
        if (player.requestFullscreen) {
            player.requestFullscreen();
        } else if (player.msRequestFullscreen) {
            player.msRequestFullscreen();
        } else if (player.mozRequestFullScreen) {
            player.mozRequestFullScreen();
        } else if (player.webkitRequestFullscreen) {
            player.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
        }
    },
    initSearch: function () {
        STUDIP.Vue.load().then(({createApp}) => {
            STUDIP.OER.Search = createApp({
                el: ".oer_search",
                data() {
                    return {
                        browseMode: false,
                        tags: $(".oer_search").data("tags"),
                        tagHistory: [],
                        searchtext: "",
                        activeFilterPanel: false,
                        difficulty: [1, 12],
                        category: null,
                        results: false,
                        material_select_url_template: $(".oer_search").data("material_select_url_template")
                    };
                },
                methods: {
                    sync_search_text: function () {
                        this.searchtext = $(".oer_search input[name=search]").val();
                    },
                    triggerFilterPanel: function () {
                        this.activeFilterPanel = !this.activeFilterPanel;
                    },
                    showFilterPanel: function () {
                        this.activeFilterPanel = true;
                    },
                    hideFilterPanel: function () {
                        this.activeFilterPanel = false;
                    },
                    clearAllFilters: function (keep_results) {
                        this.clearCategory();
                        this.clearDifficulty();
                        if (this.searchtext.length !== 0) {
                            this.searchtext = '';
                        }
                        $(".oer_search input[name=search]").val('');
                        if (keep_results !== true) {
                            this.results = false;
                        }
                    },
                    clearDifficulty: function () {
                        this.difficulty[0] = 1;
                        this.difficulty[1] = 12;
                        jQuery('#difficulty_slider').slider('values', this.difficulty);
                    },
                    clearCategory() {
                        if (this.category != null) {
                            this.category = null;
                        }
                    },
                    getIconShape(result) {
                        if (result.category === "video") {
                            return "video";
                        }
                        if (result.category === "audio") {
                            return "file-audio";
                        }
                        if (result.category === "presentation") {
                            return "file-pdf";
                        }
                        if (result.category === "elearning") {
                            return "learnmodule";
                        }
                        if (result.content_type === "application/zip") {
                            return "archive3";
                        }
                        return "file";
                    },
                    search() {
                        this.browseMode = false;
                        $.ajax({
                            url: STUDIP.URLHelper.getURL("dispatch.php/oer/market/search"),
                            data: {
                                type: this.category,
                                difficulty: this.difficulty.join(","),
                                search: this.searchtext
                            },
                            dataType: "json",
                            success: (output) => {
                                $("#new_ones").hide();
                                this.results = output.materials;
                                this.activeFilterPanel = false;
                                $(".material_navigation").toggle(this.results.length === 0);
                                $(".mainlist").toggle(this.results.length === 0);
                                $(".new_ones").toggle(this.results.length === 0);
                            }
                        });
                        return false;
                    },
                    browseTag(tag_hash, name) {
                        this.clearAllFilters(true);
                        let tags = [];
                        for (let i in this.tagHistory) {
                            tags.push(this.tagHistory[i].tag_hash);
                        }
                        if (tag_hash && (tags.indexOf(tag_hash) === -1)) {
                            tags.push(tag_hash);
                        }
                        return new Promise((resolve, reject) => {
                            $.ajax({
                                url: STUDIP.URLHelper.getURL("dispatch.php/oer/market/get_tags"),
                                data: {
                                    tags: tags
                                },
                                dataType: "json",
                                success(output) {
                                    this.results = output.results.materials;
                                    this.tags = output.tags;
                                    if (tag_hash) {
                                        this.tagHistory.push({
                                            tag_hash: tag_hash,
                                            name: name
                                        });
                                    }
                                    if (this.tagHistory.length > 0) {
                                        $("#new_ones").hide();
                                    }
                                    resolve();
                                },
                                error(jqXHR, textStatus, errorThrown) {
                                    reject(new Error(errorThrown));
                                }
                            });
                        });
                    },
                    backInCloud() {
                        if (this.tagHistory.length === 0) {
                            this.browseMode = false;
                            return;
                        }
                        this.tagHistory.pop();
                        let tag_hash = null;
                        let tag_name = null;
                        if (this.tagHistory.length > 0) {
                            tag_hash = this.tagHistory[this.tagHistory.length - 1].tag_hash;
                            tag_name = this.tagHistory[this.tagHistory.length - 1].name;
                        }
                        this.tagHistory.pop();
                        this.browseTag(tag_hash, tag_name).then(() => {
                            if (this.tagHistory.length === 0) {
                                $("#new_ones").show();
                            }
                        });

                    },
                    getTagStyle() {
                        return "position: relative; top: " + Math.floor(Math.random() * 15 - 15) + "px";
                    },
                    capitalizeFirstLetter(string) {
                        return string.charAt(0).toUpperCase() + string.slice(1);
                    },
                    getMaterialURL(material_id) {
                        return this.material_select_url_template.replace("__material_id__", material_id);
                    },
                    shortenName(name) {
                        if (name.length > 55) {
                            return name.substring(0, 50) + ' ...';
                        } else {
                            return name;
                        }
                    }
                },
                mounted() {
                    this.results = $(this.$el).data('searchresults');
                    if (this.results !== false) {
                        $("#new_ones").hide();
                    }
                    if ($(this.$el).data('filteredcategory')) {
                        this.category = $(this.$el).data('filteredcategory');
                    }

                    document.addEventListener('click', (event) => {
                        if (event.target.closest('.searchform') === null) {
                            this.hideFilterPanel();
                        }
                    })
                },
                updated() {
                    this.$nextTick(function () {
                        if (!jQuery("#difficulty_slider.ui-slider").length) { //to prevent an endless loop
                            jQuery("#difficulty_slider").slider({
                                range: true,
                                min: 1,
                                max: 12,
                                values: [this.difficulty[0], this.difficulty[1]],
                                change(event, ui) {
                                    this.difficulty = ui.values;
                                }
                            });
                        }
                    });
                }
            });
        });
    }
};


export default OER;
