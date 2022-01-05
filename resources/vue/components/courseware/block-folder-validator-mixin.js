import { mapActions, mapGetters } from 'vuex';
import CoursewareCompanionBox from './CoursewareCompanionBox.vue';

export const blockFolderValidatorMixin = {
    computed: {
        ...mapGetters({
            folderById: 'folders/byId',
        }),
        invalidFolderMessageText() {
            return this.$gettext('Der Zugriff auf ausgewählte Datei(en) könnte gesperrt sein!')
        }
    },
    components: {
        CoursewareCompanionBox,
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
            let valid = false;
            if (folderId) {
                try {
                    let id = folderId;
                    await this.loadFolder({ id });
                    let folder = this.folderById({ id });
                    if (folder && folder.relationships && folder.relationships.parent) {
                        let id = folder.relationships.parent.data.id;
                        await this.loadFolder({ id });
                        let parent = this.folderById({ id });
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
