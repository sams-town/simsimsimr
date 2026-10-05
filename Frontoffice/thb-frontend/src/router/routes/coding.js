export default [
    // Master Tarif
    {
        path: '/coding/main',
        name: 'CodingDiagnosaView',
        component: () => import('@/views/pages/coding/CodingDiagnosaView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/coding/chronic',
        name: 'DrugChronicView',
        component: () => import('@/views/pages/coding/DrugChronicView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/coding/main/result',
        name: 'CodingResultView',
        component: () => import('@/views/pages/coding/CodingResultView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/medical-services/verification-bast-bpjs',
        name: 'MedicalServicesView',
        component: () => import('@/views/pages/coding/MedicalServicesView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/medical-services/resume',
        name: 'MedicalServicesResumeView',
        component: () => import('@/views/pages/coding/MedicalServicesResumeView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/medical-services/doctor',
        name: 'MedicalServicesDoctorView',
        component: () => import('@/views/pages/coding/MedicalServicesDoctorView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/margin',
        name: 'MarginView',
        component: () => import('@/views/pages/coding/MarginView.vue'),
        meta: {
            layout: 'full'
        }
    },

]
