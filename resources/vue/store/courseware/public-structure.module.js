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
    build({ commit, rootGetters }) {
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
        const httpClient = rootGetters['httpClient'];

        let response = await httpClient.get(
            `public/courseware/${context.id}/courseware-structural-elements/${context.rootId}`,
            {
                params: {
                    include: 'containers,containers.blocks',
                },
            }
        );

        const rootElement = response.data.data;
        const includedObjects = response.data.included || [];
        dispatch('courseware-structural-elements/storeRecord', rootElement, { root: true });
        for (const includedObject of includedObjects) {
            dispatch(`${includedObject.type}/storeRecord`, includedObject, { root: true });
        }

        response = await httpClient.get(
            `public/courseware/${context.id}/courseware-structural-elements/${context.rootId}/descendants`,
            {
                params: {
                    'page[offset]': 0,
                    'page[limit]': 10000,
                },
            }
        );

        const descendants = response.data.data;
        for (const descendant of descendants) {
            dispatch('courseware-structural-elements/storeRecord', descendant, { root: true });
        }
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
