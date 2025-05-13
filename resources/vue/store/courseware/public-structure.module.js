const getDefaultState = () => {
    return {
        children: [],
        ordered: [],
    };
};

const initialState = getDefaultState();
const state = { ...initialState };

const getters = {
    children(state) {
        return (id) => state.children[id] ?? [];
    },
    ordered(state) {
        return state.ordered;
    },
};

export const mutations = {
    reset(state) {
        Object.assign(state, getDefaultState());
    },
    setChildren(state, children) {
        state.children = children;
    },
    setOrdered(state, ordered) {
        state.ordered = ordered;
    },
};

const actions = {
    build({commit, rootGetters }) {
        const context = rootGetters['context'];
        const structuralElements = rootGetters['courseware-structural-elements/all'];
        const children = structuralElements.reduce((memo, element) => {
            const parent = element.relationships.parent?.data?.id ?? null;
            if (parent) {
                if (!memo[parent]) {
                    memo[parent] = [];
                }
                memo[parent].push([element.id, element.attributes.position]);
            }

            return memo;
        }, {});
        for (const key of Object.keys(children)) {
            children[key].sort((childA, childB) => childA[1] - childB[1]);
            children[key] = children[key].map(([id]) => id);
        }

        commit('setChildren', children);

        const ordered = [...visitTree(children, context.rootId)];

        commit('setOrdered', ordered);
    },
    async load({ dispatch, rootGetters }) {
        const context = rootGetters['context'];

        await dispatch('courseware-structural-elements/loadById', {
            id: context.rootId,
            options: {
                include: 'containers,containers.blocks',
            },
        }, { root: true });
        const root = rootGetters['courseware-structural-elements/byId']({id: context.rootId});
        await dispatch('loadDescendants', { root });
    },
    loadDescendants({ dispatch }, { root }) {
        const parent = { id: root.id, type: root.type };
        const relationship = 'descendants';
        const options = {
            'page[offset]': 0,
            'page[limit]': 10000,
        };

        return dispatch(
            'courseware-structural-elements/loadRelated',
            { parent, relationship, options },
            { root: true }
        );
    },
};

function* visitTree(tree, current) {
    if (current) {
        yield current;

        const children = tree[current];
        if (children) {
            for (let index = 0; index < children.length; index++) {
                yield* visitTree(tree, children[index]);
            }
        }
    }
}

export default {
    namespaced: true,
    actions,
    getters,
    mutations,
    state,
};
