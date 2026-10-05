export default [
    // code blue
    {
        path: '/codeblue',
        name: 'CodeBlueView',
        component: () => import('@/views/pages/code_blue/CodeBlueView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/codeblue/notification',
        name: 'CodeBlueNotificationView',
        component: () => import('@/views/pages/code_blue/CodeBlueNotificationView.vue'),
        meta: {
            layout: 'full'
        }
    },
    // code blue for testing 
    {
        path: '/codeblue-test',
        name: 'CodeBlueTestView',
        component: () => import('@/views/pages/code_blue/code_blue_test/CodeBlueTestView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/codeblue/notification-test',
        name: 'CodeBlueTestNotificationView',
        component: () => import('@/views/pages/code_blue/code_blue_test/CodeBlueTestNotificationView.vue'),
        meta: {
            layout: 'full'
        }
    },
]
