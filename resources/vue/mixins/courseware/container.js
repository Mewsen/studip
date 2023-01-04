import { mapGetters } from 'vuex';

const containerMixin = {
    computed: {
        ...mapGetters(['pluginManager']),
    },
    created: function () {
        this.pluginManager.registerComponentsLocally(this);
    },
    methods: {
        checkSimpleArrayEquality(firstSet, secondSet) {
            return Array.isArray(firstSet) && Array.isArray(secondSet) &&
                firstSet.length === secondSet.length &&
                firstSet.every((val, index) => val === secondSet[index]);
        }
    }
};

export default containerMixin;
