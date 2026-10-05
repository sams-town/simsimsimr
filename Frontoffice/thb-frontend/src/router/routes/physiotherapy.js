export default [
    // Master pemeriksaan eksternal
    {
        path: '/physiotherapy/list',
        name: 'PhysiotherapyListView',
        component: () => import('@/views/pages/physiotherapy/PhysiotherapyListView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/physiotherapy/list/process/:id',
        name: 'PhysiotherapyListProcessView',
        component: () => import('@/views/pages/physiotherapy/PhysiotherapyListProcessView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/physiotherapy/external-checkup',
        name: 'PhysiotherapyExCheckupView',
        component: () => import('@/views/pages/physiotherapy/external_checkup/ExternalCheckupView.vue'),
        meta: {
            layout: 'full'
        }
    },

    // Permintaan pemeriksaan
    {
        path: '/physiotherapy/checkup-request',
        name: 'PhysiotherapyCheckupRequestView',
        component: () => import('@/views/pages/physiotherapy/checkup_request/CheckupRequestView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/physiotherapy/checkup-request/process',
        name: 'PhysiotherapyCheckupRequestProcessView',
        component: () => import('@/views/pages/physiotherapy/checkup_request/PhysiotherapyProcessView.vue'),
        meta: {
            layout: 'full'
        }
    },

    // Laporan Pemeriksaan
    {
        path: '/physiotherapy/checkup-report',
        name: 'PhysiotherapyCheckupReportView',
        component: () => import('@/views/pages/physiotherapy/checkup_report/ReportCheckupView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/physiotherapy/print/:id',
        name: 'PhysiotherapyListResultPrintView',
        component: () => import('@/views/pages/physiotherapy/print/ResultPrintView.vue'),
        meta: {
            layout: 'full'
        }
    },

]
