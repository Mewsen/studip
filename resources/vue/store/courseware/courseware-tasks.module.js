import { ASSESSMENT_TYPES } from '../../components/courseware/tasks/peer-review/process-configuration';

const getDefaultState = () => {
    return {
        showTaskGroupsAddSolversDialog: false,
        showTaskGroupsDeleteDialog: false,
        showTaskGroupsModifyDeadlineDialog: false,
        showTasksDistributeDialog: false,
    };
};

const initialState = getDefaultState();

const getters = {
    showTaskGroupsAddSolversDialog(state) {
        return state.showTaskGroupsAddSolversDialog;
    },
    showTaskGroupsDeleteDialog(state) {
        return state.showTaskGroupsDeleteDialog;
    },
    showTaskGroupsModifyDeadlineDialog(state) {
        return state.showTaskGroupsModifyDeadlineDialog;
    },
    showTasksDistributeDialog(state) {
        return state.showTasksDistributeDialog;
    },
    taskGroupsByCid(state, getters, rootState, rootGetters) {
        return (cid) => {
            return rootGetters['courseware-task-groups/all'].filter(
                (taskGroup) => taskGroup.relationships.course.data.id === cid
            );
        };
    },
    tasksByCid(state, getters, rootState, rootGetters) {
        return (cid) => {
            const taskGroupIds = getters.taskGroupsByCid(cid).map(({ id }) => id);

            return rootGetters['courseware-tasks/all'].filter((task) =>
                taskGroupIds.includes(task.relationships['task-group'].data.id)
            );
        };
    },
};

export const state = { ...initialState };

export const actions = {
    // setters
    setShowTaskGroupsAddSolversDialog({ commit }, context) {
        commit('setShowTaskGroupsAddSolversDialog', context);
    },
    setShowTaskGroupsDeleteDialog({ commit }, context) {
        commit('setShowTaskGroupsDeleteDialog', context);
    },
    setShowTaskGroupsModifyDeadlineDialog({ commit }, context) {
        commit('setShowTaskGroupsModifyDeadlineDialog', context);
    },
    setShowTasksDistributeDialog({ commit }, context) {
        commit('setShowTasksDistributeDialog', context);
    },

    // other actions
    loadTasksOfCourse({ dispatch }, { cid }) {
        const options = {
            'filter[cid]': cid,
            include:
                'solver, structural-element, task-feedback, task-group, task-group.lecturer, task-group.peer-review-processes',
        };
        return dispatch('courseware-tasks/loadAll', { options }, { root: true });
    },

    loadTaskGroup({ dispatch }, { id }) {
        const options = {
            include: 'lecturer, peer-review-processes',
        };
        return dispatch('courseware-task-groups/loadById', { id, options }, { root: true });
    },

    modifyDeadlineOfTaskGroup({ dispatch }, { taskGroup, endDate }) {
        taskGroup.attributes['end-date'] = endDate.toISOString();

        return dispatch('courseware-task-groups/update', taskGroup, { root: true });
    },

    addSolversToTaskGroup({ dispatch, rootGetters }, { taskGroup, solvers }) {
        return rootGetters.httpClient.post(`courseware-task-groups/${+taskGroup.id}/relationships/solvers`, {
            data: solvers,
        });
    },

    createPeerReviewProcess({ dispatch }, { taskGroup, options }) {
        const { anonymous, duration, automaticPairing, type, payload } = options;
        const startDate = new Date(taskGroup.attributes['end-date']);
        startDate.setSeconds(startDate.getSeconds() + 1);
        const endDate = new Date(startDate);
        endDate.setDate(endDate.getDate() + duration);

        const data = {
            attributes: {
                configuration: { anonymous, duration, automaticPairing, type, payload },
                'review-start': startDate.toISOString(),
                'review-end': endDate.toISOString(),
            },
            relationships: {
                'task-group': {
                    data: {
                        type: taskGroup.type,
                        id: taskGroup.id,
                    },
                },
            },
        };

        return dispatch('courseware-peer-review-processes/create', data, { root: true });
    },

    replacePairings({ dispatch, rootGetters }, { process, pairings }) {
        const reviews = rootGetters['courseware-peer-reviews/related']({
            parent: process,
            relationship: 'peer-reviews',
        });
        const relation = ({ id, type }) => ({ data: { id, type } });
        const deleteReview = (review) => dispatch('courseware-peer-reviews/delete', review, { root: true });
        const createReview = (pairing) =>
            dispatch(
                'courseware-peer-reviews/create',
                {
                    type: 'courseware-peer-reviews',
                    attributes: {},
                    relationships: {
                        process: relation(process),
                        submitter: relation(pairing.submitter),
                        reviewer: relation(pairing.reviewer),
                    },
                },
                { root: true }
            );

        return Promise.all(reviews.map(deleteReview)).then(() => Promise.all(pairings.map(createReview)));
    },

    updatePeerReviewProcess({ dispatch, rootGetters }, { process, configuration }) {
        const taskGroup = rootGetters['courseware-task-groups/related']({
            parent: process,
            relationship: 'task-group',
        });

        const startDate = new Date(taskGroup.attributes['end-date']);
        const endDate = new Date(startDate);
        endDate.setDate(endDate.getDate() + configuration.duration);

        if (_.isEmpty(configuration.payload)) {
            configuration.payload = ASSESSMENT_TYPES[configuration.type].defaultPayload;
        }

        process.attributes.configuration = configuration;
        process.attributes['review-start'] = startDate.toISOString();
        process.attributes['review-end'] = endDate.toISOString();

        return dispatch('courseware-peer-review-processes/update', process, { root: true });
    },

    storeAssessment({ dispatch, rootGetters }, { review, assessment }) {
        review.attributes.assessment = assessment;
        return dispatch('courseware-peer-reviews/update', review, { root: true });
    },
};

export const mutations = {
    setShowTaskGroupsAddSolversDialog(state, data) {
        state.showTaskGroupsAddSolversDialog = data;
    },
    setShowTasksDistributeDialog(state, data) {
        state.showTasksDistributeDialog = data;
    },
    setShowTaskGroupsDeleteDialog(state, data) {
        state.showTaskGroupsDeleteDialog = data;
    },
    setShowTaskGroupsModifyDeadlineDialog(state, data) {
        state.showTaskGroupsModifyDeadlineDialog = data;
    },
};

export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters,
};
