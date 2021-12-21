import { mapActions, mapGetters } from 'vuex';

export const blockFolderValidatorMixin = {
    computed: {
        ...mapGetters({
            folderById: 'folders/byId',
        }),
        invalidFolderMessageText() {
            return this.$gettext('Der Zugriff auf ausgewählte Datei(en) könnte gesperrt sein!')
        }
    },
    data() {
        return {
            showInvalidFolderMessage: false
        }
    },
    methods: {
        ...mapActions({
            loadFolder: 'folders/loadById',
        }),
        async validateFolderAccessibility(folderId) {
            var valid = false;
            if (folderId) {
                try {
                    var id = folderId;
                    await this.loadFolder({ id });
                    var folder = this.folderById({ id });
                    if (folder && folder.relationships && folder.relationships.parent) {
                        var id = folder.relationships.parent.data.id;
                        await this.loadFolder({ id });
                        var parent = this.folderById({ id });
                        if (parent && parent.attributes['folder-type'] == 'HiddenFolder') {
                            valid = false;
                        } else if (parent.relationships && parent.relationships.parent) {
                            this.validateFolderAccessibility(parent.id);
                        } else {
                            valid = true;
                        }
                    }
                } catch (err) {
                    valid = false;
                }
            }
            this.showInvalidFolderMessage = this.isTeacher ? !valid : false;
        },
    },
};
