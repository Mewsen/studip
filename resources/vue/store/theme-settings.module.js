const getDefaultState = () => {
    return {
        httpClient: null,
        userId: null,

        showThemeAddDialog: false,
        showThemeAddImportDialog: false,
        showThemeAddCopyDialog: false,
    };
};

const initialState = getDefaultState();

const getters = {

    httpClient(state) {
        return state.httpClient;
    },
    userId(state) {
        return state.userId;
    },
    showThemeAddDialog(state) {
        return state.showThemeAddDialog;
    },
    showThemeAddImportDialog(state) {
        return state.showThemeAddImportDialog;
    },
    showThemeAddCopyDialog(state) {
        return state.showThemeAddCopyDialog;
    },
};

export const state = { ...initialState };


export const actions = {
    // setters
    setHttpClient({ commit }, httpClient) {
        commit('setHttpClient', httpClient);
    },
    setUserId({ commit }, userId) {
        commit('setUserId', userId);
    },

    setShowThemeAddDialog({ commit }, show) {
        commit('setShowThemeAddDialog', show);
    },
    setShowThemeAddImportDialog({ commit }, show) {
        commit('setShowThemeAddImportDialog', show);
    },
    setShowThemeAddCopyDialog({ commit }, show) {
        commit('setShowThemeAddCopyDialog', show);
    },

    // actions
    async updateTheme({ dispatch }, { theme }) {
        await dispatch('studip-themes/update', theme, { root: true });

        return dispatch(
            'studip-themes/loadById',
            { id: theme.id },
            { root: true }
        );
    },

    async activateTheme({ dispatch }, { theme }) {
        const activeTheme = {
            id: theme.id,
            attributes: {
                active: true,
            }
        };
        await dispatch('studip-themes/update', activeTheme, { root: true });

        return true;
    },

    async addTheme({ dispatch, rootGetters }) {
        await dispatch('studip-themes/create', {}, { root: true });
        const created = rootGetters['studip-themes/lastCreated'];

        await dispatch(
            'studip-themes/loadById',
            { id: created.id },
            { root: true }
        );

        return created;
    },

    createThemeFromData( { dispatch }, { theme }) {
        dispatch('studip-themes/create', theme, { root: true });
    },

    deleteTheme({ dispatch }, data) {
        return dispatch('studip-themes/delete', data, { root: true });
    },
}
export const mutations = {
    setHttpClient(state, httpClient) {
        state.httpClient = httpClient;
    },
    setUserId(state, data) {
        state.userId = data;
    },

    setShowThemeAddDialog(state, show) {
        state.showThemeAddDialog = show;
    },
    setShowThemeAddImportDialog(state, show) {
        state.showThemeAddImportDialog = show;
    },
    setShowThemeAddCopyDialog(state, show) {
        state.showThemeAddCopyDialog = show;
    },
};

export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters,
};
