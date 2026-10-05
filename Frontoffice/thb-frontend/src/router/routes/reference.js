export default [
    {
        path: '/reference-doctor',
        name: 'DoctorReferenceView',
        component: () => import('@/views/pages/reference/DoctorReferenceView.vue'),
        meta: {
            layout: 'full'
        }
    },
]
