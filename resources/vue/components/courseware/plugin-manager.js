import { useBlockCategoryManager } from '../../composables/courseware/useBlockCategoryManager.js';

const { addCategory } = useBlockCategoryManager();

class PluginManager {
    constructor() {
        this.blocks = [];
        this.containers = [];
    }

    addBlock(name, block) {
        this.blocks[name] = block;
    }

    addCategory(title, type) {
        addCategory(title, type);
    }

    addContainer(name, container) {
        this.containers[name] = container;
    }

    registerComponentsLocally(component) {
        for (const [name, block] of Object.entries(this.blocks)) {
            if (!component.$options.components) {
                component.$options.components = {};
            }
            component.$options.components[name] = block;
        }
        for (const [name, container] of Object.entries(this.containers)) {
            if (!component.$options.components) {
                component.$options.components = {};
            }
            component.$options.components[name] = container;
        }
    }
}

export default PluginManager;
