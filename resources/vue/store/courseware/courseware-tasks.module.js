const getDefaultState = () => {
    return {
        showTasksDistributeDialog: false,
    };
};

const initialState = getDefaultState();

const getters = {
    showTasksDistributeDialog(state) {
        return state.showTasksDistributeDialog;
    },
};

export const state = { ...initialState };

export const actions = {
    // setters
    setShowTasksDistributeDialog({ commit }, context) {
        commit('setShowTasksDistributeDialog', context);
    },

    // other actions
    loadTasksOfCourse({ dispatch }, { cid }) {
        const options = {
            'filter[cid]': cid,
            include: 'solver, structural-element, task-feedback, task-group, task-group.lecturer',
        };
        return dispatch('courseware-tasks/loadAll', { options }, { root: true });
    },
};

export const mutations = {
    setShowTasksDistributeDialog(state, data){
        state.showTasksDistributeDialog = data;
    },
};

export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters,
};
