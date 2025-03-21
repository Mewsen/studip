// import ChunkedRequester from "../../assets/javascripts/lib/chunked-requester";
// import Cache from "../../assets/javascripts/lib/cache";
//
// const requester = new ChunkedRequester();
// const cache = Cache.getInstance('tree-info/');

const normalizeParameters = (parameters, defaults = {}, indexParameter = 'id') => {
    if (typeof parameters !== 'object') {
        parameters = {[indexParameter]: parameters};
    }
    return Object.assign({}, defaults, parameters);
}

class DataRequest
{
    static #promises = {};

    static #apiRequest(path, params = {}) {
        const apiUrl = `tree-node/${path}`;
        return STUDIP.jsonapi.withPromises().get(apiUrl, {
            data: Object.fromEntries(Object.entries(params).filter(([, value]) => value !== null))
        });
    }

    static async request(
        request,
        getter,
        handler
    ) {
        const data = getter();
        if (data !== null) {
            return data;
        }

        request = normalizeParameters(request, {}, 'path');

        const index = request.path + '/' + JSON.stringify(request.parameters);

        if (DataRequest.#promises[index] !== undefined) {
            return DataRequest.#promises[index];
        }

        const promise = DataRequest.#apiRequest(request.path, request.parameters);
        DataRequest.#promises[index] = promise;
        return promise.then(handler).finally(() => {
            delete DataRequest.#promises[index];
        });
    }
}

export default {
    namespaced: true,

    state: () => ({
        courses: new Map(),
        courseLimit: 50,
        nodes: new Map(),
        nodeChildren: new Map(),
        nodeCourseInfo: new Map(),
        nodeCourses: new Map(),
        isLoading: false,
        semesterId: 'all',
        semClass: 0,
        viewType: 'tree'
    }),
    getters: {
        getNode: (state) => (id) => {
            if (!state.nodes.has(id)) {
                return null;
            }
            return state.nodes.get(id);
        },
        getNodeChildren: (state) => (id) => {
            if (!state.nodeChildren.has(id)) {
                return null;
            }
            return state.nodeChildren.get(id).map(id => state.nodes.get(id));
        },
        getNodeCourseInfo: (state) => (id) => state.nodeCourseInfo.get(id) ?? null,
        getNodeCourses: (state) => (id, offset = 0) => {
            if (!state.nodeCourses.has(id)) {
                return null;
            }
            return state.nodeCourses.get(id)
                .slice(offset, state.courseLimit)
                .map(id => state.courses.get(id));
        }
    },
    mutations: {
        INITIALIZE_FROM_LOCAL_STORAGE(state) {
            state.nodes = new Map(JSON.parse(localStorage.treeNodes ?? '[]'));
            state.nodeChildren = new Map(JSON.parse(localStorage.treeNodeChildren ?? '[]'));
        },
        SET_COURSE(state, data) {
            state.courses.set(data.id, data);
        },
        SET_LOADING(state, loading) {
            state.isLoading = loading;
        },
        SET_NODE(state, data) {
            state.nodes.set(data.id, data);
            // localStorage.treeNodes = JSON.stringify(Array.from(state.nodes.entries()));
        },
        SET_NODE_CHILDREN(state, data) {
            state.nodeChildren.set(data.id, data.children);
            // localStorage.treeNodeChildren = JSON.stringify(Array.from(state.nodeChildren.entries()));
        },
        SET_NODE_COURSE_INFO(state, data) {
            state.nodeCourseInfo.set(data.id, data.courses);
        },
        SET_NODE_COURSES(state, data) {
            if (data.offset === undefined || data.reset === true) {
                state.nodeCourses.set(data.id, data.courses);
            } else {
                const courses = state.nodeCourses.get(data.id) ?? [];
                courses.splice(data.offset, 0, ...data.courses);
                state.nodeCourses.set(data.id, courses);
            }
        },
        SET_SEMESTER(state, semesterId) {
            state.nodeCourseInfo.clear();
            state.nodeCourses.clear();

            state.semesterId = semesterId;
        },
        SET_SEMCLASS(state, semClass) {
            state.nodeCourseInfo.clear();
            state.nodeCourses.clear();

            state.semClass = semClass;
        },
        SET_VIEW_TYPE(state, type) {
            state.viewType = type;
        }
    },
    actions: {
        async fetchNode({ commit, getters, state }, data) {
            data = normalizeParameters(data);

            return DataRequest.request(
                {
                    path: data.id,
                    parameters: {
                        include: 'children',
                        'filter[semester]': state.semesterId,
                    'filter[semclass]': state.semClass,
                    }
                },
                () => getters.getNode(data.id),
                (response => {
                    commit('SET_NODE', response.data);

                    commit('SET_NODE_COURSE_INFO', {
                        id: data.id,
                        courses: response.meta?.courses ?? 0
                    });

                    // Store included children
                    const children = [];
                    (response.included ?? [])
                        .filter(item => item.type === 'tree-node')
                        .forEach(item => {
                            commit('SET_NODE', item)
                            children.push(item.id);
                        });
                    commit('SET_NODE_CHILDREN', {
                        id: data.id,
                        children
                    });

                    return response.data;
                }),
            );
        },
        async fetchNodeChildren({ commit, getters }, data) {
            data = normalizeParameters(data);

            return DataRequest.request(
                `${data.id}/children`,
                () => getters.getNodeChildren(data.id),
                (response) => {
                    response.data.forEach(node => {
                        commit('SET_NODE', node);
                    })
                    commit('SET_NODE_CHILDREN', {
                        id: data.id,
                        children: response.data.map(n => n.id)
                    });

                    return response.data;
                }
            ).then(children => {
                return children.filter(child => !data.visibleChildrenOnly || child.attributes.visible)
            });
        },
        async fetchNodeCourseInfo({ commit, getters, state }, data) {
            data = normalizeParameters(data);

            return DataRequest.request(
                {
                    path: `${data.id}/courseinfo`,
                    parameters: {
                        'filter[semester]': state.semesterId,
                        'filter[semclass]': state.semClass,
                    }
                },
                () => getters.getNodeCourseInfo(data.id),
                (response) => {
                    commit('SET_NODE_COURSE_INFO', {
                        id: data.id,
                        courses: response.courses
                    });

                    return response.courses;
                }
            );
        },
        async fetchNodeCourses({ commit, getters, state }, data) {
            data = normalizeParameters(data, {page: 0});

            return DataRequest.request(
                {
                    path: `${data.id}/courses`,
                    parameters: {
                        'page[offset]': data.page * state.courseLimit,
                        'page[limit]': state.courseLimit,
                        'filter[semester]': state.semesterId,
                        'filter[semclass]': state.semClass,
                    }
                },
                () => getters.getNodeCourses(data.id, data.page * state.courseLimit),
                (response) => {
                    const courseIds = [];
                    response.data.forEach(course => {
                        commit('SET_COURSE', course);
                        courseIds.push(course.id);
                    })

                    commit('SET_NODE_COURSES', {
                        id: data.id,
                        courses: courseIds,
                        offset: data.page * state.courseLimit,
                    });

                    return response.data;
                }
            );
        }
    }
};
