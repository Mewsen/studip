<template>
    <div class="cw-block cw-blubber-chart">
        <courseware-default-block
            :block="block"
            :canEdit="canEdit"
            :isTeacher="isTeacher"
            :preview="true"
            @storeEdit="storeBlock"
            @closeEdit="initCurrentData"
        >
            <template #content>
                <div v-if="currentTitle" class="cw-block-title">
                    {{ currentTitle }}
                </div>
                <div v-if="currentThreadId" class="cw-block-blubber-content" >
                    <courseware-blubber-thread
                        :thread-id="currentThreadId"
                        @threadContent="setTitle"
                    />
                </div>
                <courseware-companion-box
                    v-else
                    :msgCompanion="$gettext('Es wurde noch keine Blubber-Konversation angelegt.')"
                    mood="unsure"
                />
            </template>
            <template v-if="canEdit" #edit>
                <form v-if="isTeacher && context.type === 'courses'" class="default" @submit.prevent="">
                    <label>
                        <translate>Blubber Konversation</translate>
                        <select v-model="currentThreadId">
                            <option value="">
                                <translate>neue Konversation</translate>
                            </option>
                            <option
                                v-for="thread in availableThreads"
                                :key="thread.id"
                                :value="thread.id"
                            >
                            {{ thread.attributes.content }}
                            </option>
                        </select>
                    </label>
                    <label>
                        <translate>Titel</translate>
                        <input type="text" v-model="currentTitle" required/>
                    </label>
                </form>
                <courseware-companion-box
                    v-if="!isTeacher"
                    :msgCompanion="onlyTeachersInfo"
                    mood="pointing"
                />
                <courseware-companion-box
                    v-if="context.type !== 'courses'"
                    :msgCompanion="notInCourseInfo"
                    mood="pointing"
                />
            </template>
            <template #info>
                <p><translate>Informationen zum Blubber-Block</translate></p>
            </template>
        </courseware-default-block>
    </div>
</template>

<script>
import CoursewareBlubberThread from './CoursewareBlubberThread.vue';
import CoursewareCompanionBox from './CoursewareCompanionBox.vue';
import CoursewareDefaultBlock from './CoursewareDefaultBlock.vue';
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'courseware-blubber-block',
    components: {
        CoursewareBlubberThread,
        CoursewareCompanionBox,
        CoursewareDefaultBlock,
    },
    props: {
        block: Object,
        canEdit: Boolean,
        isTeacher: Boolean,
    },
    data() {
        return {
            currentTitle: '',
            currentThreadId: '',
            availableThreads: [],
        }
    },
    computed: {
        ...mapGetters({
            context: 'context',
        }),
        notInCourseInfo() {
            return this.$gettext('Blubber-Konversationen für Courseware Blöcke können nur in Veranstaltungen anlegen werden.');
        },
        onlyTeachersInfo() {
            return this.$gettext('Nur Lehrende dürfen Blubber-Konversationen anlegen und ändern.')
        }
    },
    methods:{
        ...mapActions({
            updateBlock: 'updateBlockInContainer',
            loadCourseBlubberThreads: 'loadCourseBlubberThreads',
            createBlubberThread: 'createBlubberThread',
            updateBlubberThread: 'updateBlubberThread',
            companionWarning: 'companionWarning'
        }),
        async initCurrentData() {
            this.currentThreadId = this.block?.attributes?.payload?.thread_id;
            this.availableThreads = await this.loadCourseBlubberThreads({cid: this.context.id});
            this.availableThreads = this.availableThreads.filter(thread => thread.attributes.content !== null && thread.attributes.content !== '');
        },
        setTitle(e) {
            this.currentTitle = e;
        },
        async storeBlock() {
            if (this.context.type !== 'courses') {
                this.companionWarning({
                    info: this.notInCourseInfo
                });
                return;
            }
            if (!this.isTeacher) {
                this.companionWarning({
                    info: onlyTeachersInfo
                });
                return;
            }
            let attributes = {};
            attributes.payload = {};

            if (this.currentThreadId !== '' && this.currentTitle !== '') {
                await this.updateBlubberThread({ 
                    content: this.currentTitle,
                    threadId: this.currentThreadId
                });
            }

            if (this.currentTitle === '') {
                this.companionWarning({
                    info: this.$gettext('Bitte vergeben Sie einen Titel.')
                });
                return;
            }

            if (this.currentThreadId === '' && this.context.type === 'courses') {
                await this.createBlubberThread({
                    attributes: {
                        'context-type': 'course',
                        'context-id': this.context.id,
                        'content': this.currentTitle
                    }
                });
                const newThread = this.$store.getters['blubber-threads/lastCreated'];
                this.currentThreadId = newThread.id;
            }

            attributes.payload.thread_id = this.currentThreadId;

            this.updateBlock({
                attributes: attributes,
                blockId: this.block.id,
                containerId: this.block.relationships.container.data.id,
            });
        },
    },
    mounted() {
        this.initCurrentData();
    },
    watch: {
        currentThreadId(newId) {
            if (newId === '') {
                this.currentTitle = '';
            }
        }
    }
}
</script> 
