import { mapActions, mapGetters } from 'vuex';

const containerMixin = {
    computed: {
        ...mapGetters({
            blockById: 'courseware-blocks/byId',
            containerById: 'courseware-containers/byId',
            pluginManager: 'pluginManager',
        }),
    },
    created: function () {
        this.pluginManager.registerComponentsLocally(this);
    },
    methods: {
        ...mapActions({
            updateBlock: 'updateBlock',
            updateContainer: 'updateContainer',
            loadContainer: 'courseware-containers/loadById',
            loadBlock: 'courseware-blocks/loadById',
            loadStructuralElement: 'loadStructuralElement',
            lockObject: 'lockObject',
            unlockObject: 'unlockObject',
            createBlock: 'createBlockInContainer',
            createContainer: 'createContainer',
            companionInfo: 'companionInfo',
            companionSuccess: 'companionSuccess',
            companionWarning: 'companionWarning',
            sortContainersInStructualElements: 'sortContainersInStructualElements',
            setAdderStorage: 'coursewareBlockAdder',
            setProcessing: 'setProcessing',
            containerUpdate: 'courseware-containers/update'
        }),
        dropBlock(e) {
            this.isDragging = false; // implemented bei echt container type
            let data = {};
            data.originContainerId = e.from.__vue__.$attrs.containerId;
            data.targetContainerId = e.to.__vue__.$attrs.containerId;
            if (data.originContainerId === data.targetContainerId) {
                this.storeSort(); // implemented bei echt container type
            } else {
                data.originSectionId = e.from.__vue__.$attrs.sectionId;
                data.originSectionBlockList = e.from.__vue__.$children.map(b => { return b.$attrs.blockId; });
                data.targetSectionId = e.to.__vue__.$attrs.sectionId;
                data.targetSectionBlockList = e.to.__vue__.$children.map(b => { return b.$attrs.blockId; });
                data.blockId = e.item._underlying_vm_.id;
                data.newPos = e.newIndex;
                const indexInBlockList = data.targetSectionBlockList.findIndex(b => b === data.blockId);
                data.targetSectionBlockList.splice(data.newPos, 0, data.targetSectionBlockList.splice(indexInBlockList,1));
                this.storeInAnotherContainer(data);
            }
        },
        async storeInAnotherContainer(data) {
            this.setProcessing(true);
            // update origin container
            if (data.originContainerId) {
                await this.lockObject({ id: data.originContainerId, type: 'courseware-containers' });
                await this.loadContainer({ id : data.originContainerId });
                let originContainer = this.containerById({ id: data.originContainerId});
                originContainer.attributes.payload.sections[data.originSectionId].blocks = data.originSectionBlockList;
                await this.containerUpdate(
                    originContainer,
                );
                await this.unlockObject({ id: data.originContainerId, type: 'courseware-containers' });
            }
            // update target container
            await this.lockObject({ id: data.targetContainerId, type: 'courseware-containers' });
            await this.loadContainer({ id : data.targetContainerId });
            let targetContainer = this.containerById({ id: data.targetContainerId});
            targetContainer.attributes.payload.sections[data.targetSectionId].blocks = data.targetSectionBlockList;
            await this.containerUpdate(
                targetContainer,
            );
            await this.unlockObject({ id: data.targetContainerId, type: 'courseware-containers' });
         
            // update block container id
            let block = this.blockById({id: data.blockId });
            block.relationships.container.data.id = data.targetContainerId;
            block.attributes.position = data.newPos;
            await this.lockObject({ id: block.id, type: 'courseware-blocks' });
            await this.updateBlock({
                block: block,
                containerId: data.targetContainerId,
            });
            await this.unlockObject({ id: block.id, type: 'courseware-blocks' });
            await this.loadBlock({ id: block.id });
            await this.loadContainer({ id : data.originContainerId });
            this.setProcessing(false);
        },
        checkSimpleArrayEquality(firstSet, secondSet) {
            return Array.isArray(firstSet) && Array.isArray(secondSet) &&
                firstSet.length === secondSet.length &&
                firstSet.every((val, index) => val === secondSet[index]);
        }
    }
};

export default containerMixin;
