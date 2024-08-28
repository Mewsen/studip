<template>
    <div>
        <form :action="saveUrl" method="post" class="default" v-show="isEditing">
            <input type="hidden" :name="csrf.name" :value="csrf.value">

            <textarea class="wiki-editor size-l"
                      ref="wiki_editor"
                      data-editor="extraPlugins=WikiLink"
                      name="content"
                      v-model="content"
            ></textarea>

            <div></div>
            <label>
                <input type="checkbox" v-model="autosave">
                {{ $gettext('Automatisches Speichern aktivieren.') }}
            </label>
            <div>
                {{ $gettext('Zuletzt gespeichert') }}:
                <studip-date-time :timestamp="Math.floor(lastSaveDate / 1000)"
                                  :relative="true"
                ></studip-date-time>
            </div>

            <div data-dialog-button="">
                <button class="button" :title="isChanged ? $gettext('Den aktuellen Stand speichern.') : $gettext('Der aktuelle Stand wurde bereits gespeichert.')">
                    {{ $gettext('Speichern') }}
                </button>
                <a :href="cancelUrl" class="button">
                    {{ $gettext('Verlassen') }}
                </a>
                <button v-for="user in requestingUsers"
                        :key="user.user_id"
                        @click.prevent="delegateEditMode(user.user_id)"
                        class="button"
                >
                    {{ $gettextInterpolate($gettext('Schreibmodus an %{name} übergeben'), { name: user.fullname }, true) }}
                </button>
            </div>
        </form>

        <div v-if="!isEditing">
            <div v-html="content"></div>
            <div data-dialog-button>
                <button class="button"
                        v-if="!editingWasRequested"
                        :title="$gettext('Beantragen Sie, dass Sie den Text jetzt bearbeiten wollen.')"
                        @click.prevent="applyEditing()"
                >
                    {{ $gettext('Bearbeiten beantragen') }}
                </button>
                <button class="cancel button"
                        v-else
                        :title="$gettext('Klicken Sie, um die Anfrage zum Bearbeiten abzubrechen')"
                        @click.prevent="cancelApplyEditing()"
                >
                    {{ $gettext('Bearbeiten beantragt') }}
                </button>
                <a :href="cancelUrl" class="button">
                    {{ $gettext('Verlassen') }}
                </a>
            </div>
        </div>

        <wiki-editor-online-users :users="onlineUsers"></wiki-editor-online-users>

        <mounting-portal :mount-to="`.wiki-last-edited-${pageId}`">
            <studip-date-time :timestamp="Math.floor(lastSaveDate / 1000)"
                              :relative="true"
            ></studip-date-time>
        </mounting-portal>
    </div>
</template>
<script>
import WikiEditorOnlineUsers from "./WikiEditorOnlineUsers.vue";
import StudipDateTime from "./StudipDateTime.vue";
import JSUpdater from "@/assets/javascripts/lib/jsupdater";

export default {
    name: 'wiki-editor',
    components: {StudipDateTime, WikiEditorOnlineUsers },
    props: {
        cancelUrl: {
            type: String,
            required: true,
        },
        chdate: {
            type: String,
            required: true,
        },
        editing: {
            type: Boolean,
            default: true
        },
        offlineThreshold: {
            type: Number,
            default: 60 * 1000
        },
        pageContent: {
            type: String,
            default: ''
        },
        pageId: {
            type: Number,
            required: true
        },
        saveUrl: {
            type: String,
            required: true
        },
        users: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            autosave: false,
            content: this.pageContent,
            editor: null,
            isChanged: false,
            isEditing: this.editing,
            lastFocussedDate: null,
            lastSaveDate: new Date(this.chdate),
            onlineUsers: this.users,
        };
    },
    computed: {
        csrf() {
            return STUDIP.CSRF_TOKEN;
        },
        editingWasRequested() {
            return this.onlineUsers
                .filter(u => u.user_id === STUDIP.USER_ID)
                .some(u => u.editing_request);
        },
        isOnlineAndEditing() {
            return this.isEditing
                && new Date() - this.lastFocussedDate < this.offlineThreshold;
        },
        requestingUsers() {
            return this.onlineUsers
                .filter(u => u.editing_request)
                .sort((a, b) => a.fullname.localeCompare(b.fullname));
        }
    },
    methods: {
        applyEditing() {
            const url = STUDIP.URLHelper.getURL(`dispatch.php/course/wiki/apply_editing/${this.pageId}`)
            $.post(url).done(output => {
                if (output.me_online.editing > 0) {
                    this.isEditing = true;
                    this.focusEditor();
                }
                this.onlineUsers = output.users;
            });
        },
        cancelApplyEditing() {
            const url = STUDIP.URLHelper.getURL(`dispatch.php/course/wiki/cancel_apply_editing/${this.pageId}`)
            $.post(url).done(output => {
                this.onlineUsers = output.users;
            });
        },
        delegateEditMode(user_id) {
            const url = STUDIP.URLHelper.getURL(`dispatch.php/course/wiki/delegate_edit_mode/${this.pageId}/${user_id}`);
            $.post(url).done(() => {
                this.isEditing = false;
            });
        },
        focusEditor() {
            this.$nextTick(() => {
                this.editor.editing.view.focus();
            });
        },
        getUpdaterData() {
            if (this.editor.editing.view.document.isFocused) {
                this.lastFocussedDate = new Date();
            }

            const data = {
                id: this.pageId,
                online: this.isOnlineAndEditing
            };

            if (this.autosave && this.isChanged) {
                data.content = this.editor.getData();
                this.isChanged = false;
            }

            return data;
        },
        securityHandler(event) {
            event.preventDefault();

            event.returnValue = true;
        }
    },
    mounted() {
        const textarea = this.$refs['wiki_editor'];

        STUDIP.wysiwyg.replace(textarea).then((editor) => {
            editor.model.document.on('change:data', () => {
                this.isChanged = editor.getData() !== this.content;
            });

            if (this.isEditing) {
                this.focusEditor();
            }

            this.editor = editor;
        });

        JSUpdater.register(
            'wiki_editor_status',
            (content) => {
                this.onlineUsers = content.users;
                this.isEditing = content.editing;

                if ('chdate' in content) {
                    this.lastSaveDate = new Date(content.chdate);
                }

                if ('content' in content) {
                    this.content = content.content;
                }

                if (!this.isEditing && 'wysiwyg' in content) {
                    this.editor.setData(content.wysiwyg);
                }
            },
            () => this.getUpdaterData()
        )
    },
    watch: {
        isChanged(current) {
            if (current) {
                window.addEventListener('beforeunload', this.securityHandler);
            } else {
                window.removeEventListener('beforeunload', this.securityHandler);
            }
        }
    }
}
</script>
