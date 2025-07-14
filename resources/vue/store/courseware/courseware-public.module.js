const getDefaultState = () => {
    return {
        blockAdder: {},
        blockTypes: [],
        containerTypes: [],
        containerAdder: false,
        context: null,
        courseware: {},
        httpClient: null,
        isAuthenticated: false,
        password: null,
        pluginManager: null,
        selectedToolbarItem: 'contents',
        showToolbar: false,
        userId: null,
        viewMode: 'read',
    };
};

const initialState = getDefaultState();

const getters = {
    blockAdder(state) {
        return state.blockAdder;
    },

    blockTypes(state) {
        return state.blockTypes;
    },

    containerTypes(state) {
        return state.containerTypes;
    },

    containerAdder(state) {
        return state.containerAdder;
    },

    context(state) {
        return state.context;
    },

    courseware(state) {
        return state.courseware;
    },

    httpClient(state) {
        return state.httpClient;
    },

    isAuthenticated(state) {
        return state.isAuthenticated;
    },

    password(state) {
        return state.password;
    },

    pluginManager(state) {
        return state.pluginManager;
    },

    selectedToolbarItem(state) {
        return state.selectedToolbarItem;
    },

    showToolbar(state) {
        return state.showToolbar;
    },

    userId(state) {
        return state.userId;
    },

    userIsTeacher() {
        return false;
    },

    viewMode(state) {
        return state.viewMode;
    },
};

export const state = { ...initialState };

export const actions = {
    // setters
    setBlockTypes({ commit }, blockTypes) {
        commit('setBlockTypes', blockTypes);
    },
    setContainerTypes({ commit }, containerTypes) {
        commit('setContainerTypes', containerTypes);
    },

    coursewareContainerAdder(context, adder) {
        context.commit('setContainerAdder', adder);
    },

    coursewareShowToolbar(context, toolbar) {
        context.commit('setShowToolbar', toolbar);
    },

    coursewareViewMode(context, view) {
        context.commit('setViewMode', view);
    },

    setContext({ commit }, context) {
        commit('setContext', context);
    },

    setPluginManager({ commit }, pluginManager) {
        commit('setPluginManager', pluginManager);
    },

    setIsAuthenticated({ commit }, isAuthenticated) {
        commit('setIsAuthenticated', isAuthenticated);
    },

    setPassword({ commit }, password) {
        commit('setPassword', password);
    },

    setHttpClient({ commit }, httpClient) {
        commit('setHttpClient', httpClient);
    },

    // other actions
    async loadStructuralElement({ dispatch, rootGetters }, structuralElementId) {
        const context = rootGetters['context'];
        const httpClient = rootGetters['httpClient'];

        let response = await httpClient.get(
            `public/courseware/${context.id}/courseware-structural-elements/${structuralElementId}`,
            {
                params: {
                    include: 'containers,containers.blocks',
                },
            }
        );

        const element = response.data.data;
        const includedObjects = response.data.included ?? [];
        dispatch('courseware-structural-elements/storeRecord', element, { root: true });
        for (const includedObject of includedObjects) {
            dispatch(`${includedObject.type}/storeRecord`, includedObject, { root: true });
        }

        return element;
    },

    validatePassword({ getters, dispatch }, password) {
        if (password === getters.password) {
            dispatch('setIsAuthenticated', true);

            return true;
        }

        return false;
    },
};

export const mutations = {

    coursewareSet(state, data) {
        state.courseware = data;
    },

    setBlockTypes(state, blockTypes) {
        state.blockTypes = blockTypes;
    },

    setContainerTypes(state, containerTypes) {
        state.containerTypes = containerTypes;
    },

    setContainerAdder(state, containerAdder) {
        state.containerAdder = containerAdder;
    },

    setContext(state, context) {
        state.context = context;
    },

    setIsAuthenticated(state, isAuthenticated) {
        state.isAuthenticated = isAuthenticated;
    },

    setPassword(state, password) {
        state.password = password;
    },

    setPluginManager(state, pluginManager) {
        state.pluginManager = pluginManager;
    },

    setShowToolbar(state, showToolbar) {
        state.showToolbar = showToolbar;
    },

    setViewMode(state, data) {
        state.viewMode = data;
    },

    setHttpClient(state, httpClient) {
        state.httpClient = httpClient;
    },
};

export default {
    state,
    actions,
    mutations,
    getters,
};
