export default [
    // ------------------------ Laboratory list -------------------------
    {
        path: '/laboratory/list',
        name: 'LaboratoryListView',
        component: () => import('@/views/pages/laboratory/LaboratoryListView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/laboratory/list/process/:id',
        name: 'LaboratoryListProcessView',
        component: () => import('@/views/pages/laboratory/LaboratoryListProcessView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/laboratory/list/process/print-order',
        name: 'LaboratoryListProcessOrderView',
        component: () => import('@/views/pages/laboratory/print/OrderListView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/laboratory/list/process/result/:id/:item',
        name: 'LaboratoryListProcessResultView',
        component: () => import('@/views/pages/laboratory/ProcessResultView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/laboratory/list/process/pa/:id/:item',
        name: 'LaboratoryListPAResultView',
        component: () => import('@/views/pages/laboratory/ProcessResultPAView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/laboratory/print/:id',
        name: 'LaboratoryListResultPrintView',
        component: () => import('@/views/pages/laboratory/print/ResultPrintView.vue'),
        meta: {
            layout: 'full'
        }
    },

    
    // ------------------------ Laboratory inspection -------------------------
    {
        path: '/laboratory/inspection',
        name: 'LaboratoryInspectionView',
        component: () => import('@/views/pages/laboratory/InspectionView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/laboratory/inspection-report',
        name: 'LaboratoryInspectionReportView',
        component: () => import('@/views/pages/laboratory/InspectionReportView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/laboratory/parameter',
        name: 'LaboratoryParameterView',
        component: () => import('@/views/pages/laboratory/LaboratoryParameterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/laboratory/collation',
        name: 'LaboratoryCollationView',
        component: () => import('@/views/pages/laboratory/LaboratoryCollationView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/laboratory/action',
        name: 'LaboratoryActionView',
        component: () => import('@/views/pages/laboratory/LaboratoryActionView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/laboratory/package',
        name: 'LaboratoryPackageView',
        component: () => import('@/views/pages/laboratory/LaboratoryPackageView.vue'),
        meta: {
            layout: 'full'
        }
    },
    // {
    //     path: '/laboratory/package/:id',
    //     name: 'LaboratoryPackageView',
    //     component: () => import('@/views/pages/laboratory/LaboratoryPackageDetail.vue'),
    //     meta: {
    //         layout: 'full'
    //     }
    // },
    {
        path: '/laboratory/dashboard',
        name: 'LaboratoryDashboardView',
        component: () => import('@/views/pages/laboratory/LaboratoryDashboardView.vue'),
        meta: {
            layout: 'full'
        }
    },
]
