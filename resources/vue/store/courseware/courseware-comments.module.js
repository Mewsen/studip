const getDefaultState = () => {
    return {
        context: null,
        httpClient: null,
        userId: null,
        userIsTeacher: false,
        typeFilter: 'all', // all, blocks, elements
        createdFilter: 'all', // all, oneDay, oneWeek
        unitFilter: 'all', // all or unit id
    };
};

const initialState = getDefaultState();

const getters = {
    context(state) {
        return state.context;
    },
    httpClient(state) {
        return state.httpClient;
    },
    userId(state) {
        return state.userId;
    },
    userIsTeacher(state) {
        return state.userIsTeacher;
    },
    typeFilter(state) {
        return state.typeFilter;
    },
    createdFilter(state) {
        return state.createdFilter;
    },
    unitFilter(state) {
        return state.unitFilter;
    }
};

export const state = { ...initialState };

export const actions = {
    // setters
    setContext({ commit }, context) {
        commit('setContext', context);
    },
    setHttpClient({ commit }, httpClient) {
        commit('setHttpClient', httpClient);
    },
    setUserId({ commit }, id) {
        commit('setUserId', id);
    },
    setTypeFilter({ commit }, type) {
        commit('setTypeFilter', type);
    },
    setCreatedFilter({ commit }, created) {
        commit('setCreatedFilter', created);
    },
    setUnitFilter({ commit }, id) {
        commit('setUnitFilter', id);
    },
    setUserIsTeacher({ commit }, isTeacher) {
        commit('setUserIsTeacher', isTeacher);
    }
};

export const mutations = {
    setContext(state, data) {
        state.context = data;
    },
    setHttpClient(state, data) {
        state.httpClient = data;
    },
    setUserId(state, data) {
        state.userId = data;
    },
    setTypeFilter(state, data) {
        state.typeFilter = data;
    },
    setCreatedFilter(state, data) {
        state.createdFilter = data;
    },
    setUnitFilter(state, data) {
        state.unitFilter = data;
    },
    setUserIsTeacher(state, isTeacher) {
        state.userIsTeacher = isTeacher;
    },
};

export default {
    state,
    actions,
    mutations,
    getters,
};
