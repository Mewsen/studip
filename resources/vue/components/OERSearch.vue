<template>
    <form class="oer_search" :action="url">
        <div class="searchform">
            <div class="oneliner">
                <div class="frame">
                    <span v-if="category != null"
                          class="category activefilter" :title="$gettext('Aktiver Filter der Kategorie')">
                        <span>{{ category }}</span>
                        <a href="#"
                           @click.prevent="clearCategory()"
                           class="erasefilter"
                           :title="$gettext('Filter der Kategorie entfernen')">
                            <studip-icon shape="decline" class="text-bottom"></studip-icon>
                        </a>
                    </span>

                    <span v-if="difficulty[0] != 1 || difficulty[1] != 12"
                          class="niveau activefilter"
                          title="$gettext('Aktiver Filter für das Niveau')"
                    >
                        {{ $gettext('Niveau') }}:
                        <span>{{ difficulty[0] }}</span>
                        -
                        <span>{{ difficulty[1] }}</span>
                        <a href="#"
                           @click.prevent="clearDifficulty()"
                           class="erasefilter"
                           :title="$gettext('Filter des Niveaus entfernen')">
                            <studip-icon shape="decline" class="text-bottom"></studip-icon>
                        </a>
                    </span>

                    <input type="text"
                           name="search"
                           v-model.trim="searchtext"
                           @focus="toggleFilterPanel(true)"
                           @keydown.enter.prevent="search()">

                    <button v-if="difficulty[0] != 1 || difficulty[1] != 12 || (category != null) || (searchtext.length > 0)"
                            class="erase"
                            type="button"
                            :title="$gettext('Suchformular zurücksetzen')"
                            @click="clearAllFilters">
                        <studip-icon shape="decline"></studip-icon>
                    </button>

                    <button @click="toggleFilterPanel()"
                            type="button"
                            :title="$gettext('Suchfilter einstellen')"
                            :class="activeFilterPanel ? 'active' : ''">
                        <studip-icon shape="filter" :role="activeFilterPanel ? 'info_alt' : 'clickable'"></studip-icon>
                    </button>

                    <div v-if="activeFilterPanel" class="filterpanel_shadow"></div>

                    <div v-if="activeFilterPanel" class="filterpanel">
                        <div>
                            <h3>{{ $gettext('Kategorien') }}</h3>
                            <ul class="clean">
                                <li>
                                    <button class="as-link" @click.prevent="category = 'audio'">
                                        <studip-icon :shape="category === 'audio' ? 'radiobutton-checked' : 'radiobutton-unchecked'" class="text-bottom"></studip-icon>
                                        {{ $gettext('Audio') }}
                                    </button>
                                </li>
                                <li>
                                    <button class="as-link" @click.prevent="category = 'video'">
                                        <studip-icon :shape="category === 'video' ? 'radiobutton-checked' : 'radiobutton-unchecked'" class="text-bottom"></studip-icon>
                                        {{ $gettext('Video') }}
                                    </button>
                                </li>
                                <li>
                                    <button class="as-link" @click.prevent="category = 'presentation'">
                                        <studip-icon :shape="category === 'presentation' ? 'radiobutton-checked' : 'radiobutton-unchecked'" class="text-bottom"></studip-icon>
                                        {{ $gettext('Folien') }}
                                    </button>
                                </li>
                                <li>
                                    <button class="as-link" @click.prevent="category = 'elearning'">
                                        <studip-icon :shape="category === 'elearning' ? 'radiobutton-checked' : 'radiobutton-unchecked'" class="text-bottom"></studip-icon>
                                        {{ $gettext('Lernmodule') }}
                                    </button>
                                </li>
                                <li>
                                    <button class="as-link" @click.prevent="category = null">
                                        <studip-icon shape="link-intern" class="text-bottom"></studip-icon>
                                        {{ $gettext('Alles') }}
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="level_filter">
                            <h3>{{ $gettext('Niveau') }}</h3>
                            <div class="level_labels">
                                <div>{{ $gettext('Leicht') }}</div>
                                <div>{{ $gettext('Schwer') }}</div>
                            </div>
                            <div class="level_numbers">
                                <div v-for="i in 12" :key="i" style="text-align: right">
                                    {{ i }}
                                </div>
                            </div>
                            <div id="difficulty_slider"></div>

                            <input type="hidden" id="difficulty" name="difficulty" value="">
                        </div>
                    </div>


                    <button :title="$gettext('Suche starten')"
                            @click.prevent="search()"
                            @focus="toggleFilterPanel(false)"
                            type="button"
                    >
                        <studip-icon shape="search"></studip-icon>
                    </button>
                </div>

            </div>


        </div>

        <div class="browser">

            <div v-if="browseMode === false" class="intro">
                <StudipAssetImg file="oer-keyvisual-negative.svg" class="illustration responsive-hidden"></StudipAssetImg>
                <div>
                    <h3>{{ $gettext('Wertvolle Lernmaterialien entdecken!') }}</h3>
                    <div class="responsive-hidden">
                        {{ $gettext('Neue und spannende Lernmaterialien zu finden, ist ganz einfach. Mit dem Entdeckermodus können Sie nach Schlagwörtern stöbern und durch Themengebiete surfen.') }}
                    </div>

                    <div>
                        <button class="button" @click.prevent="browseMode = true">
                            {{ $gettext('Zum Entdeckermodus') }}
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="browseMode === true" class="tagcloud">
                <div>
                    <h3>{{ $gettext('Wertvolle Materialien entdecken!') }}</h3>
                    {{ $gettext('Klicken Sie auf die Schlagwörter und entdecken Sie Lernmaterialien zum Thema.') }}
                </div>
                <a href="" @click.prevent="backInCloud" class="back-button">
                    <studip-icon shape="arr_1left" :size="50"></studip-icon>
                </a>
                <ul class="tags clean">
                    <li v-for="tag in tagCloud" :key="tag">
                        <a href="#"
                           class="button"
                           :style="getTagStyle(tag.tag_hash)"
                           :title="tag.name"
                           @click.prevent="browseTag(tag.tag_hash, tag.name)"
                        >
                            #{{ tag.name }}
                        </a>
                    </li>
                </ul>
            </div>

        </div>

        <div v-if="browseMode && results && results.length === 0" class="oer_no_results">
            <StudipMessageBox type="info" :hide-close="true">
                {{ $gettext('Keine Ergebnisse gefunden.') }}
            </StudipMessageBox>
        </div>

        <ul class="results oer_material_overview" v-if="results && results.length > 0">
            <li v-for="result in results" :key="result.material_id">
                <article class="contentbox" :title="result.name">
                    <a :href="getMaterialURL(result.material_id)" target="_blank">
                        <header>
                            <h1>
                                <studip-icon :shape="getIconShape(result)"
                                             class="text-bottom"></studip-icon>
                                {{ shortenName(result.name) }}
                            </h1>
                        </header>
                        <div class="image" :style="`background-image: url(${result.logo_url});${!result.front_image_content_type ? ' background-size: 60% auto;' : ''}`"></div>
                    </a>
                </article>
            </li>
        </ul>

    </form>
</template>
<script>
import StudipMessageBox from './StudipMessageBox.vue';
import StudipAssetImg from './StudipAssetImg.vue';

export default {
    name: 'OERSearch',
    components: {StudipAssetImg, StudipMessageBox},
    props: {
        searchResults: [Array, Boolean],
        filteredTag: String,
        filteredCategory: String,
        tags: Array,
        materialSelectUrlTemplate: String,
        toPlugin: String,
        toFolderId: String,
        url: {
            type: String,
            required: true,
        }
    },
    data() {
        return {
            browseMode: false,
            tagHistory: [],
            searchtext: '',
            activeFilterPanel: false,
            difficulty: [1, 12],
            category: null,
            results: this.searchResults || false,
        };
    },
    computed: {
        tagCloud() {
            const history = this.tagHistory.map(tag => tag.tag_hash);
            return this.tags.filter(tag => !history.includes(tag.tag_hash));
        }
    },
    methods: {
        toggleFilterPanel(state = null) {
            this.activeFilterPanel = state ?? !this.activeFilterPanel;
        },
        clearAllFilters(keep_results) {
            this.clearCategory();
            this.clearDifficulty();
            this.searchtext = '';
            if (!keep_results) {
                this.results = false;
            }
        },
        clearDifficulty() {
            if (this.difficulty[0] != 1 && this.difficulty[1] != 12) {
                this.difficulty = [1, 12];
            }
            jQuery("#difficulty_slider").slider("values", this.difficulty);
        },
        clearCategory() {
            if (this.category != null) {
                this.category = null;
            }
        },
        getIconShape(result) {
            if (result.category === 'video') {
                return 'video';
            }
            if (result.category === 'audio') {
                return 'file-audio';
            }
            if (result.category === 'presentation') {
                return 'file-pdf';
            }
            if (result.category === 'elearning') {
                return 'learnmodule';
            }
            if (result.content_type === 'application/zip') {
                return 'archive3';
            }
            return 'file';
        },
        search() {
            this.browseMode = false;
            $.getJSON(STUDIP.URLHelper.getURL('dispatch.php/oer/market/search', {
                type: this.category,
                difficulty: this.difficulty.join(','),
                search: this.searchtext
            })).done(output => {
                this.results = output.materials.length;
                this.activeFilterPanel = false;

                this.toggleElements(
                    this.results.length === 0,
                    '.material_navigation',
                    '.mainlist',
                    '#new_ones'
                );
            });
        },
        browseTag(tag_hash, name) {
            this.clearAllFilters(true);
            let tags = this.tagHistory.map(i => i.tag_hash);
            if (tag_hash && !tags.includes(tag_hash)) {
                tags.push(tag_hash);
            }
            return $.getJSON(STUDIP.URLHelper.getURL('dispatch.php/oer/market/get_tags', { tags })).done(output => {
                this.results = output.results.materials;

                this.$emit('update:tags', output.tags);

                if (tag_hash) {
                    this.tagHistory.push({ tag_hash, name });
                }
                if (this.tagHistory.length > 0) {
                    this.toggleElements(false, '#new_ones');
                }
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
                tag_hash = this.tagHistory.at(-1).tag_hash;
                tag_name = this.tagHistory.at(-1).name;
            }

            this.tagHistory.pop();
            this.browseTag(tag_hash, tag_name).done(() =>  {
                if (this.tagHistory.length === 0) {
                    this.toggleElements(true, '#new_ones');
                }
            });

        },
        getTagStyle(tag_hash) {
            return {
                position: 'relative',
                top: Math.floor(Math.random() * 15 - 15) + 'px'
            };
        },
        capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        },
        getMaterialURL(material_id) {
            return STUDIP.URLHelper.getURL(
                this.materialSelectUrlTemplate.replace('__material_id__', material_id),
                {
                    to_plugin: this.toPlugin,
                    to_folder_id: this.toFolderId
                }
            );
        },
        shortenName(name, maxLength = 55) {
            if (name.length > maxLength) {
                return name.substring(0, maxLength - 3) + ' ...';
            }

            return name;
        },
        hideFilterPanelListener(event) {
            if (!event.target.closest('.oer_search .searchform')) {
                this.toggleFilterPanel(false);
            }
        },
        toggleElements(state, ...selectors) {
            document.querySelectorAll(selectors.join(',')).forEach(node => {
                node.style.display = state ? '' : 'none';
            });
        }
    },
    mounted() {
        this.toggleElements(this.results === false, '#new_ones');

        document.body.addEventListener('click', this.hideFilterPanelListener);
    },
    beforeDestroy() {
        document.body.removeEventListener('click', this.hideFilterPanelListener)
    },
    updated() {
        this.$nextTick(() => {
            jQuery("#difficulty_slider:not(.ui-slider)").slider({
                range: true,
                min: 1,
                max: 12,
                values: [this.difficulty[0], this.difficulty[1]],
                change: (event, ui) => {
                    this.difficulty = ui.values;
                }
            });
        });
    }
};
</script>
