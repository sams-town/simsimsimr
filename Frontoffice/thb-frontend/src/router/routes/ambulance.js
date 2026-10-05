export default [
    {
        path: '/ambulance/list',
        name: 'AmbulanceListView',
        component: () => import('@/views/pages/ambulance/AmbulanceMasterDataView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/ambulance/usage',
        name: 'AmbulanceUsageView',
        component: () => import('@/views/pages/ambulance/AmbulanceUsageView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/ambulance/used',
        name: 'AmbulanceUsedView',
        component: () => import('@/views/pages/ambulance/AmbulanceCurrentlyUsedView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/ambulance/ordered',
        name: 'AmbulanceOrderedView',
        component: () => import('@/views/pages/ambulance/AmbulanceOrderedView.vue'),
        meta: {
            layout: 'full'
        }
    },
    // ------------------ Ambulance Report ------------------
    {
        path: '/ambulance/report',
        name: 'AmbulanceReportView',
        component: () => import('@/views/pages/ambulance/AmbulanceReportView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/ambulance/report/print',
        name: 'AmbulanceReportPrintView',
        component: () => import('@/views/pages/ambulance/print/ReportPrintView.vue'),
        meta: {
            layout: 'full'
        }
    },
]
