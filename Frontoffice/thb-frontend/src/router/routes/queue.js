export default [
    {
        path: '/queue/poli/:id',
        name: 'QueuePoliView',
        component: () => import('@/views/pages/queue/poli/QueuePoliView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/queue/laboratory/:id',
        name: 'QueueLaboratoryView',
        component: () => import('@/views/pages/queue/hf/QueueLaboratoryView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/queue/radiology/:id',
        name: 'QueueRadiologyView',
        component: () => import('@/views/pages/queue/hf/QueueRadiologyView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/queue/pharmacy/:id',
        name: 'QueuePharmacyView',
        component: () => import('@/views/pages/queue/hf/QueuePharmacyView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/queue/nurse',
        name: 'QueueNurseView',
        component: () => import('@/views/pages/queue/nurse/QueueNurseView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/queue/nurse/list',
        name: 'QueueNurseListView',
        component: () => import('@/views/pages/queue/nurse/WorkOrderView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/outpatient',
        name: 'QueueNurseRJView',
        component: () => import('@/views/pages/queue/nurse/QueueNurseRJView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/checkout',
        name: 'QueueNurseCHKView',
        component: () => import('@/views/pages/queue/nurse/QueueNurseCHKView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/igd',
        name: 'QueueNurseIGDView',
        component: () => import('@/views/pages/queue/nurse/QueueNurseIGDView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/inpatient',
        name: 'QueueNurseINView',
        component: () => import('@/views/pages/queue/nurse/QueueNurseINView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/queue/doctor',
        name: 'QueueDoctorView',
        component: () => import('@/views/pages/queue/doctor/QueueDoctorView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/queue/doctor/list',
        name: 'QueueDoctorListView',
        component: () => import('@/views/pages/queue/nurse/QueueNurseRJView.vue'),
        // component: () => import('@/views/pages/queue/doctor/WorkOrderView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/queue/doctor/checkout',
        name: 'QueueDoctorCheckoutView',
        component: () => import('@/views/pages/queue/doctor/WorkOrderCheckoutView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/loket',
        name: 'MasterLoketView',
        component: () => import('@/views/pages/master/loket/MasterLoketView.vue'),
        meta: {
            layout: 'full'
        }
    },
]
