export default [
    // Master Tarif
    {
        path: '/cssd',
        name: 'CssdView',
        component: () => import('@/views/pages/CSSD/CSSDView.vue'),
        meta: {
            layout: 'full'
        }
    },
]
