export default [
    {
        path: '/linen/handover',
        name: 'LinenHandoverView',
        component: () => import('@/views/pages/linen/LinenView.vue'),
        meta: {
            layout: 'full'
        }
    },
]
