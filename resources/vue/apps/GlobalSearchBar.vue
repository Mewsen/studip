<template>
    <div id="globalsearch-searchbar"
         role="search"
         :aria-label="$gettext('Globale Suche')"
         :class="{'is-visible': isVisible}"
         @keyup="keyHandler"
    >
        <input class="hidden-small-down"
               type="text"
               v-model.trim="needle"
               ref="input"
               name="globalsearchterm"
               id="globalsearch-input"
               :placeholder="$gettext('Was suchen Sie?')"
               role="combobox"
               aria-haspopup="listbox"
               aria-expanded="false"
               aria-controls="globalsearch-list"
               :aria-label="$gettext('Suche nach Objekten und Personen in Stud.IP')"
               @keyup.enter.prevent="doSearch()"
        >
        <studip-icon v-if="needle.length > 0"
                     shape="decline"
                     tabindex="0"
                     name="reset-search"
                     id="globalsearch-clear"
                     class="hidden-small-down"
                     @click="resetSearch()"
                     :alt="$gettext('Suche zurücksetzen')"
        ></studip-icon>
        <studip-icon shape="search"
                     role="info_alt"
                     tabindex="0"
                     name="start-search"
                     id="globalsearch-icon"
                     @click.prevent="doSearch()"
                     :alt="$gettext('Suche starten')"
        ></studip-icon>

        <div id="globalsearch-list"
             role="listbox">
            <button class="as-link"
                    id="globalsearch-togglehints"
                    tabindex="0"
                    :class="{open: showHints}"
                    @click.prevent="showHints = !showHints"
            >
                {{ showHints ? $gettext('Tipps ausblenden') : $gettext('Tipps einblenden') }}
            </button>

            <global-search-bar-hints v-if="showHints"></global-search-bar-hints>

            <div v-if="isSearching"
                 id="globalsearch-searching"
                 aria-live="polite"
            >
                {{ $gettext('Suche...') }}
            </div>
            <div v-if="results !== null"
                 id="globalsearch-results"
                 aria-live="polite"
                 role="list"
            >
                <span v-if="Object.keys(results).length === 0">
                    {{ $gettext('Keine Ergebnisse gefunden.') }}
                </span>
                <template v-else>
                    <article v-for="(value, category) in displayedResults"
                             :key="category"
                             :id="`globalsearch-${category}`"
                    >
                        <header class="globalsearch-category" :data-category="category">
                            <a href="#" @click.prevent="toggleCategory(category)">
                                {{ value.name }}
                            </a>
                            <div v-if="value.more && value.fullsearch !== ''"
                                 class="globalsearch-more-results"
                            >
                                <a :href="value.fullsearch">
                                    {{ $gettext('alle anzeigen') }}
                                </a>
                            </div>
                        </header>
                        <a v-for="(result, index) in value.content"
                           :key="`result-${category}-${index}`"
                           :href="result.url"
                           role="listitem"
                           :data-dialog="dataDialogValue(category)"
                           ref="searchresults"
                        >
                            <div v-if="result.img"
                                 class="globalsearch-result-img"
                            >
                                <img :src="result.img"
                                     alt="">
                            </div>
                            <div class="globalsearch-result-data">
                                <div class="globalsearch-result-title" v-html="result.name" />
                                <div class="globalsearch-result-details">
                                    <div v-if="result.description"
                                         class="globalsearch-result-description"
                                         v-html="result.description" />
                                    <div v-if="result.additional" v-html="result.additional" />
                                </div>
                            </div>
                            <div v-if="result.date" class="globalsearch-result-time" v-html="result.date" />

                            <div v-if="result.expand && result.expand !== value.fullsearch && value.more"
                                 class="globalsearch-result-expand">
                                <a :href="result.expand" :title="result.expandtext"></a>
                            </div>
                            <slot v-else name="expand" :item="{...result, category}"></slot>
                        </a>
                    </article>
                </template>
            </div>
        </div>
    </div>
</template>
<script>
// TODO: Responsive mode

import GlobalSearchBarHints from '../components/GlobalSearchBarHints.vue';

export default {
    name: 'global-search-bar',
    components: {GlobalSearchBarHints},
    props: {
        maxResultsPerType: {
            type: Number,
            required: true,
        },
        minNeedleLength: {
            type: Number,
            default: 3,
        },
        searchDelay: {
            type: Number,
            default: 750,
        },
        sourceUrl: {
            type: String,
            required: true,
        }
    },
    data() {
        return {
            abortController: new AbortController(),
            isSearching: false,
            isVisible: false,
            needle: '',
            results: null,
            searchResultIndex: null,
            selectedCategory: null,
            showHints: false,
            timeout: null,
        }
    },
    computed: {
        displayedResults() {
            let results = {};
            Object.keys(this.results).forEach((key) => {
                if (this.selectedCategory !== null && this.selectedCategory !== key) {
                    return;
                }

                results[key] = {...this.results[key]};

                if (this.selectedCategory === null) {
                    results[key].content = results[key].content.concat().slice(0, this.maxResultsPerType);
                }
            });

            return results;
        }
    },

    methods: {
        close() {
            this.resetSearch(false);
            this.$refs.input.blur();
            this.isVisible = false;
        },
        doSearch(with_delay = false) {
            clearTimeout(this.timeout);

            if (this.isSearching) {
                this.abortController.abort('Initiated new search');
            }

            if (this.needle.length < this.minNeedleLength) {
                return;
            }

            (new Promise(resolve => {
                if (with_delay) {
                    this.timeout = setTimeout(resolve, this.searchDelay);
                } else {
                    resolve();
                }
            })).then(() => {
                this.isSearching = true;

                const url = new URL(this.sourceUrl);
                url.searchParams.set('limit', (this.maxResultsPerType * 3).toString(10));
                url.searchParams.set('search', this.needle);
                url.searchParams.set('filters', JSON.stringify({
                    category: 'show_all_categories',
                    semester: 'future',
                }));

                return fetch(url, { signal: this.abortController.signal });
            })
            .then((response) => response.json())
            .then(response => {
                this.results = response;
                this.isSearching = false;
            });
        },
        resetSearch(focus = true) {
            this.needle = '';
            this.results = null;
            this.searchResultIndex = null;

            if (focus) {
                this.$refs.input.focus();
            }
        },

        dataDialogValue(category) {
            return ['GlobalSearchFiles', 'GlobalSearchMessages'].includes(category) ? '' : null;
        },

        toggleCategory(category) {
            if (this.selectedCategory === category) {
                this.selectedCategory = null;
            } else {
                this.selectedCategory = category;
            }
        },

        // Event handlers
        clickHandler(event) {
            if (!this.$el.contains(event.target) && this.isVisible) {
                this.close();
            }
        },
        focusHandler(event) {
            this.isVisible = this.$el.contains(event.target);
        },
        globalKeyHandler(event) {
            // Don't do anything if a dialog is open
            if (STUDIP.Dialog.stack.length > 0) {
                return;
            }

            // ctrl + space
            if (
                event.key === ' '
                && event.ctrlKey
                && !event.altKey
                && !event.metaKey
                && !event.shiftKey
            ) {
                event.preventDefault();

                if (this.isVisible) {
                    this.close();
                } else {
                    this.$refs.input.focus();
                }
            }

        },
        keyHandler(event) {
            if (!['ArrowDown', 'ArrowUp', 'Escape'].includes(event.key)) {
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();

            if (event.key === 'Escape') {
                this.close();
            } else if (this.$refs.searchresults.length > 0) {
                if (event.key === 'ArrowDown') {
                    this.searchResultIndex = (this.searchResultIndex ?? - 1) + 1;
                } else if (event.key === 'ArrowUp') {
                    this.searchResultIndex = (this.searchResultIndex ?? this.$refs.searchresults.length) - 1;
                }

                if (this.searchResultIndex < 0) {
                    this.searchResultIndex = 0;
                } else if (this.searchResultIndex > this.$refs.searchresults.length - 1) {
                    this.searchResultIndex = this.$refs.searchresults.length - 1;
                }

                this.$refs.searchresults[this.searchResultIndex].focus();
            }
        },
    },
    watch: {
        isVisible(current, previous) {
            if (current && !previous) {
                this.$refs.input.focus();
                this.searchResultIndex = null;
            }
        },
        needle(current) {
            if (current.length < this.minNeedleLength) {
                return;
            }

            this.doSearch(true);
        }
    },
    mounted() {
        document.addEventListener('click', this.clickHandler);
        document.addEventListener('focusin', this.focusHandler);
        document.addEventListener('keydown', this.globalKeyHandler)
    },
    beforeUnmount() {
        document.removeEventListener('keydown', this.globalKeyHandler)
        document.removeEventListener('focusin', this.focusHandler);
        document.removeEventListener('click', this.clickHandler);
    }
}
</script>
