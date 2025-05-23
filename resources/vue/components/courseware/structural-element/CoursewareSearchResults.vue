<template>
    <div role="region" id="search" aria-live="polite">
        <ContentBar>
            <template #buttons-left>
                <studip-icon shape="search" :size="24" />
            </template>
            <template #breadcrumb-list>
                {{ $gettext('Suchergebnisse') }}
            </template>
            <template #menu>
                <button :title="$gettext('Suchergebnisse schließen')" @click="closeResults">
                    <studip-icon shape="decline" :size="24"/>
                </button>
            </template>
        </ContentBar>
        <div id="search-results">
            <article v-if="currentUnitSearchResults.length > 0" class="studip padding-less">
                <header>
                    <h1 class="search-results-header">
                        {{ $gettext('Suchergebnisse in diesem Lernmaterial') }}
                    </h1>
                </header>
                <section v-for="result in currentUnitSearchResults" :key="result['structural-element-id']">
                    <router-link
                        :to="'/structural_element/' + result['structural-element-id']"
                        @click="closeResults"
                    >
                        <div v-show="result.img !== null" class="search-result-img hidden-tiny-down">
                            <img :src="result.img" />
                        </div>
                        <div class="search-result-data">
                            <div class="search-result-title" v-html="result.name"></div>
                            <div class="search-result-details">
                                <div class="search-result-description" v-html="result.description"></div>
                            </div>
                        </div>
                        <div class="search-result-information">
                            <div class="search-result-time" v-html="result.date"></div>
                        </div>
                    </router-link>
                </section>
            </article>
            <article v-if="otherUnitsSearchResults.length > 0" class="studip padding-less">
                <header>
                    <h1 class="search-results-header">
                        {{ $gettext('Suchergebnisse in anderen Lernmaterialien') }}
                    </h1>
                </header>
                <section v-for="result in otherUnitsSearchResults" :key="result['unit-id'] + '-' + result['structural-element-id']">
                    <a :href="result['url']">
                        <div v-show="result.img !== null" class="search-result-img hidden-tiny-down">
                                <img :src="result.img" />
                        </div>
                        <div class="search-result-data">
                            <div class="search-result-title" v-html="result.name"></div>
                            <div class="search-result-details">
                                <div class="search-result-description" v-html="result.description"></div>
                            </div>
                        </div>
                        <div class="search-result-information">
                            <div class="search-result-time" v-html="result.date"></div>
                        </div>
                    </a>
                </section>
            </article>
            <courseware-companion-box
                v-if="noResults"
                :msgCompanion="$gettext('Es wurden keine Suchergebnisse gefunden.')"
                mood="sad"
            />
        </div>
    </div>
</template>

<script>
import CoursewareCompanionBox from '../layouts/CoursewareCompanionBox.vue';
import StudipIcon from '../../StudipIcon.vue';
import { mapActions, mapGetters } from 'vuex';
import ContentBar from "../../ContentBar.vue";

export default {
    name: 'courseware-search-results',
    components: {
        ContentBar,
        CoursewareCompanionBox,
        StudipIcon
    },
    computed: {
        ...mapGetters({
            searchResults: 'searchResults',
            context: 'context'
        }),
        currentUnitId() {
            return +this.context.unit;
        },
        currentUnitSearchResults() {
            return this.searchResults.filter(result => {
                return result['unit-id'] === this.currentUnitId;
            });
        },
        otherUnitsSearchResults() {
            return this.searchResults.filter(result => {
                return result['unit-id'] !== this.currentUnitId;
            });
        },
        noResults() {
            return this.currentUnitSearchResults.length === 0 && this.otherUnitsSearchResults.length === 0;
        }
    },
    methods: {
        ...mapActions({
            setShowSearchResults: 'setShowSearchResults',
            setSearchResults: 'setSearchResults',
        }),
        closeResults() {
            this.setShowSearchResults(false);
            this.setSearchResults([]);
        },
    }
}
</script>
