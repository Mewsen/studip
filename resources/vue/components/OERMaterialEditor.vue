<template>
    <form :action="storeUrl"
          method="post"
          class="default oercampus_editmaterial"
          onsubmit="$(window).off('beforeunload')"
          data-secure
          enctype="multipart/form-data"
    >
        <input type="hidden" :name="csrf.name" :value="csrf.value">
        <input v-if="template?.redirect_url"
               type="hidden"
               name="redirect_url"
               :value="template.redirect_url">
        <input v-if="template?.module_id"
               type="hidden"
               name="module_id"
               :value="template.module_id">

        <fieldset>
            <legend>{{ $gettext('Grunddaten') }}</legend>

            <label>
                <span class="required">{{ $gettext('Name') }}</span>
                <input type="text"
                       name="data[name]"
                       class="oername"
                       required
                       v-model.trim="name"
                       maxlength="64">
            </label>

            <div>{{ $gettext('Vorschau') }}</div>

            <div class="hgroup" @drop.prevent="dropImage">
                <label for="oer_logo_uploader">
                    <article class="contentbox" :title="name">
                        <header>
                            <h1>
                                <studip-icon shape="file"
                                             :size="20"
                                             class="text-bottom"></studip-icon>
                                <div class="title">{{ name }}</div>
                            </h1>
                        </header>
                        <div class="image"
                             :style="{
                                backgroundImage: `url(${logoUrl})`,
                                backgroundSize: customLogo ? null : '60% auto'
                             }"
                        ></div>
                    </article>
                </label>

                <div>
                    <label class="file-upload logo_file">
                    {{ $gettext('Vorschau-Bilddatei (optional)') }}
                    <input type="file"
                           name="image"
                           id="oer_logo_uploader"
                           accept="image/*"
                           @change="editImage()">
                    </label>

                    <label v-if="material.front_image_content_type">
                        <input type="checkbox" name="delete_front_image" value="1">
                        {{ $gettext('Vorschaubild löschen') }}
                    </label>
                </div>
            </div>

            <label v-if="!template?.tmp_name"
                   class="file drag-and-drop"
                   @drop.prevent="dropFile">
                {{ $gettext('Datei (gerne auch eine ZIP-Datei) auswählen') }}
                <input type="file" name="file" id="oer_file" @change="editFile()">
                <div v-if="material.filename">
                    <span>{{ material.filename }}</span>
                    <span>{{ material.filesize }}</span>
                </div>
            </label>

            <label>
                {{ $gettext('Beschreibung') }}
                <textarea name="data[description]" v-model="description"></textarea>
            </label>

            <label>
                <input type="hidden" name="data[draft]" value="0">
                <input type="checkbox" name="data[draft]" value="1" :checked="material.draft">
                {{ $gettext('Entwurf (nicht veröffentlicht)') }}
            </label>

            <label>
                {{ $gettext('Kategorie') }}
                <select name="data[category]" v-model="category">
                    <option v-for="category in categories"
                            :key="`category-${category.key}`"
                            :value="category.value"
                            :title="category.title ?? null"
                    >
                        {{ category.label }}
                    </option>
                </select>
            </label>

            <label>
                {{ $gettext('Vorschau-URL (optional)') }}
                <input type="url" name="data[player_url]" pattern="^https?://.*"
                       :value="(material.player_url || template?.player_url) ?? ''">
            </label>

            <div v-if="material.id">
                <h4>{{ $gettext('Autoren') }}</h4>
                <ul class="clean autoren" :class="{multiple: material.users.length > 1}">
                    <li v-for="user in material.users" :key="`material-user-${user.id}`">
                        <label>
                            <input v-if="material.users.length > 1"
                                   type="checkbox"
                                   name="remove_users"
                                   :value="`${user.external ? 1 : 0}_${user.id}`"
                            >
                            <div>
                                <span class="avatar" :style="{backgroundImage: `url(${user.avatar})`}"></span>
                                <span class="author_name">{{ user.name }}</span>
                                <studip-icon v-if="material.users.length > 1"
                                             shape="trash"
                                             class="text-bottom"
                                             :title="$gettext('Person als Autor entfernen')"
                                ></studip-icon>
                            </div>
                        </label>
                    </li>
                </ul>
            </div>

            <div class="oer_tags_container">
                <h4>{{ $gettext('Themen (am besten mindestens 5)') }}</h4>
                <ul class="clean oer_tags">
                    <li v-for="(tag, index) in tags" :key="index">
                        #
                        <quicksearch name="tags[]"
                                     :searchtype="tagSearch"
                                     v-model="tags[index]"
                                     :autocomplete="true"
                                     v-focus-on-create
                        ></quicksearch>
                        <button class="as-link" @click.prevent="removeTag(index)"
                           :title="$gettext('Thema aus der Liste streichen')">
                            <studip-icon shape="trash" :size="20" class="text-bottom"></studip-icon>
                        </button>

                    </li>
                </ul>
                <button class="as-link" @click.prevent="addTag()">
                    <studip-icon shape="add" :size="20" class="text-bottom"></studip-icon>
                    {{ $gettext('Thema hinzufügen') }}
                </button>
            </div>

            <div class="level_filter" style="margin-top: 13px; max-width: 682px;">
                <h4>{{ $gettext('Niveau') }}</h4>

                <input type="hidden" name="data[difficulty_start]" :value="difficultyStart">
                <input type="hidden" name="data[difficulty_end]" :value="difficultyEnd">

                <studip-level-slider :lower-value.sync="difficultyStart"
                                     :upper-value.sync="difficultyEnd"
                ></studip-level-slider>
            </div>

            <label v-if="enableTwillo"
                   style="margin-top: 20px;"
            >
                <input type="checkbox"
                       name="publish_on_twillo"
                       value="1"
                       :checked="material.published_id_on_twillo">
                {{ $gettext('Auf twillo.de veröffentlichen') }}
            </label>
        </fieldset>

        <fieldset v-if="licensesEnabled">
            <legend>{{ $gettext('Lizenz') }}</legend>
            {{ $gettext('Ich erkläre mich bereit, dass meine Lernmaterialien unter der angegebenen Lizenz an alle Nutzenden freigegeben werden. Ich bestätige zudem, dass ich das Recht habe, diese Dateien frei zu veröffentlichen, weil entweder ich selbst sie angefertigt habe, oder sie von anderen Quellen mit kompatibler Lizenz stammen.') }}

            <div>
                <select class="licenses_selector" name="data[license_identifier]">
                    <option v-for="license in licenses"
                            :key="`license-${license.id}`"
                            :value="license.id"
                            :selected="license.id === material.license_identifier">
                        {{ license.name }}
                    </option>
                </select>
            </div>
        </fieldset>

        <footer data-dialog-button>
            <button type="submit" name="save" class="button">
                {{ material.id ? $gettext('Speichern') : $gettext('Hochladen') }}
            </button>
            <a :href="URLHelper.getURL('dispatch.php/oer/mymaterial')" class="button cancel">
                {{ $gettext('Abbrechen') }}
            </a>
        </footer>
    </form>
</template>
<script>
import Quicksearch from "./Quicksearch.vue";
import StudipIcon from "./StudipIcon.vue";
import StudipLevelSlider from "./StudipLevelSlider.vue";
import Vue from "vue";

let mounted = false;

export default {
    name: "OERMaterialEditor",
    components: {StudipLevelSlider, StudipIcon, Quicksearch },
    directives: {
        ['focus-on-create']: {
            bind(el) {
                if (mounted) {
                    Vue.nextTick(() => el.querySelector('input')?.focus());
                }
            }
        }
    },
    mounted () {
        mounted = true;
    },
    props: {
        material: {
            type: Object,
            required: true,
        },
        storeUrl: {
            type: String,
            required: true
        },
        template: Object,
        tagSearch: {
            type: String,
            required: true
        },
        licensesEnabled: Boolean,
        licenses: Array,
        enableTwillo: Boolean,

        minimumTags: {
            type: Number,
            default: 5
        }
    },
    data() {
        const tags = this.material.tags.concat(
            this.template?.tags ?? [],
            Array(this.minimumTags).fill('')
        ).slice(0, this.minimumTags);

        return {
            name: this.material.name.trim() || this.template?.name.trim() || '',
            category: this.material.category || (this.material.id ? '' : null),
            description: this.material.description.trim() || this.template?.description.trim() || '',
            difficultyEnd: this.material.difficulty_end,
            difficultyStart: this.material.difficulty_start,
            tags: tags,
        };
    },
    computed: {
        categories() {
            const categories = [
                {
                    key: 'audio',
                    label: this.$gettext('Audio')
                },
                {
                    key: 'video',
                    label: this.$gettext('Video')
                },
                {
                    key: 'presentation',
                    label: this.$gettext('Folien')
                },
                {
                    key: 'elearning',
                    label: this.$gettext('Lernmodule')
                },
                {
                    key: '',
                    label: this.$gettext('Ohne Kategorie'),
                    title: this.$gettext('Fehlt eine Kategorie? Kein Problem, arbeiten Sie stattdessen mit Schlagwörtern. Die sind viel flexibler.')
                }
            ];

            if (!this.material.id) {
                categories.unshift({key: 'auto', label: this.$gettext('Automatisch erkennen')});
            }

            return categories;
        },
        csrf() {
            return STUDIP.CSRF_TOKEN;
        },
        customLogo() {
            return this.template?.image_tmp_name?.length > 0
                || this.material.front_image_content_type?.length > 0;
        },
        logoUrl() {
            return this.template?.image_tmp_name ? STUDIP.URLHelper.getURL('dispatch.php/oer/mymaterial/show_tmp_image') : this.material.logoUrl;
        },
        URLHelper() {
            return STUDIP.URLHelper;
        }
    },
    methods: {
        editImage(event) {
            let reader = new FileReader();
            let vue = this;
            reader.addEventListener("load", function () {
                vue.logo_url = reader.result;
                vue.customlogo = true;
            }, false);
            reader.readAsDataURL(
                event.target.files.length > 0
                    ? event.target.files[0]
                    : event.dataTransfer.files[0]
            );
        },
        dropImage(event) {
            window.document.getElementById("oer_logo_uploader").files = event.dataTransfer.files;
            this.editImage(event);
        },
        editFile(event) {
            this.filename = event.target.files[0].name;
            this.filesize = event.target.files[0].size;

            if (!this.name) {
                this.name = this.filename;
            }
        },
        dropFile(event) {
            window.document.getElementById("oer_file").files = event.dataTransfer.files;
            this.editFile(event);
        },
        addTag() {
            this.tags.push('');
        },
        removeTag(i) {
            this.tags = this.tags.filter((element, index) => index !== i);
        }
    },
    watch: {
        tags(current) {
            if (current.length === 0) {
                this.tags.push('');
            }
        }
    }
}
</script>
<style lang="scss" scoped>
.oercampus_editmaterial {
    .drag-and-drop {
        width: 260px;
        margin-left: 0;
        height: 60px;
        background-position: center 40px;
        padding-top: 100px;
    }

    .autoren {
        &.multiple label {
            cursor: pointer;
        }
        input[type=checkbox] {
            display: none;
        }
        input[type=checkbox]:checked + div {
            text-decoration: line-through;
        }
        .avatar {
            display: inline-block;
            background-position: center center;
            background-repeat: no-repeat;
            background-size: 100% 100%;
            width: 20px;
            min-width: 20px;
            height: 20px;
            margin-right: 5px;
            position: relative;
            top: 5px;
        }
    }
    .oer_tags_container {
        margin-top: 10px;
    }
}
</style>
