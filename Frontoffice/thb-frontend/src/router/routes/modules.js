export default [
    {
        path: '/system/module',
        name: 'ModuleView',
        component: () => import('@/views/pages/system/module/ModuleView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/system/module-item',
        name: 'ModuleItemView',
        component: () => import('@/views/pages/system/module_item/ModuleItemView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/system/user',
        name: 'UserView',
        component: () => import('@/views/pages/system/user/UserView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/system/group',
        name: 'GroupView',
        component: () => import('@/views/pages/system/group/GroupView.vue'),
        meta: {
            layout: 'full'
        }
    },
]