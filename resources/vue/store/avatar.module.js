const getDefaultState = () => {
    return {
        context: null,
        httpClient: null,
        userId: null,
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

    currentAvatar(state, getters, rootState, rootGetters) {
        if (getters.context === null) {
            return null;
        }
        const parent = {
            type: getters.context.type,
            id: getters.context.id,
        };

        const relationship = 'avatar';

        return rootGetters['avatar/related']({ parent, relationship });
    },
    currentUser(state, getters, rootState, rootGetters) {
        const id = getters.userId;
        return rootGetters['users/byId']({ id });
    },
    isCourseAvatar(state, getters) {
        return getters.context?.type === 'courses';
    },
    isInstituteAvatar(state, getters) {
        return getters.context?.type === 'institutes';
    },
    isStudygroupAvatar(state, getters) {
        return getters.context?.type === 'studygroups';
    },
    isUserAvatar(state, getters) {
        return getters.context?.type === 'users';
    },
    isCustomized(state, getters) {
        return getters.currentAvatar.attributes.customized;
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
    setUserId({ commit }, userId) {
        commit('setUserId', userId);
    },

    // other actions
    loadAvatar({ dispatch, getters, rootGetters }) {
        const parent = {
            type: getters.context.type,
            id: getters.context.id,
        };

        const relationship = 'avatar';

        return dispatch('avatar/loadRelated', { parent, relationship }, { root: true }).then(() => {
            rootGetters['avatar/related']({ parent, relationship });
        });
    },
};

export const mutations = {
    setContext(state, context) {
        state.context = context;
    },
    setHttpClient(state, httpClient) {
        state.httpClient = httpClient;
    },
    setUserId(state, data) {
        state.userId = data;
    },
};

export default {
    state,
    actions,
    mutations,
    getters,
};
