export default [
    // ------------------ Ward List ------------------
    {
        path: '/sirs',
        name: 'SirsView',
        component: () => import('@/views/pages/sirs/SirsReportView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/sirsold',
        name: 'SirsOldView',
        component: () => import('@/views/pages/sirs/SirsReportOldView.vue'),
        meta: {
            layout: 'full'
        }
    },
]
