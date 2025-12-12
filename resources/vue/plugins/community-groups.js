import { createRouter, createWebHashHistory } from 'vue-router';
import CommunityGroupsOverview from '@/vue/components/community/groups/Overview.vue';
import GroupDetail from '@/vue/components/community/groups/Detail.vue';
import GroupMembers from '@/vue/components/community/groups/Members.vue';
import GroupChat from '@/vue/components/community/groups/Chat.vue';
import GroupPinboard from '@/vue/components/community/groups/Pinboard.vue';

const BASE_URL = window.location.pathname;

export const CommunityGroupsPlugin = {
    install(Vue) {
        const routes = [
            {
                path: '/',
                name: 'GroupsOverview',
                component: CommunityGroupsOverview,
            },
            {
                path: '/:id',
                name: 'GroupDetail',
                component: GroupDetail,
                props: true,
                children: [
                    {
                        path: '',
                        name: 'GroupMembers',
                        component: GroupMembers,
                    },
                    {
                        path: 'chat',
                        name: 'GroupChat',
                        component: GroupChat,
                    },
                    {
                        path: 'pinboard',
                        name: 'GroupPinboard',
                        component: GroupPinboard,
                    },
                ],
            },
        ];
        const router = createRouter({
            history: createWebHashHistory(BASE_URL),
            routes: routes,
        });
        Vue.use(router);
    },
};
