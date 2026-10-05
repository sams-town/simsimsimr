export default [
    // payment
    {
        path: '/radiology/list',
        name: 'RadiologyListView',
        component: () => import('@/views/pages/radiology/RadiologyListView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/radiology/list/process/:id',
        name: 'RadiologyListProcessView',
        component: () => import('@/views/pages/radiology/RadiologyListProcessView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/radiology/list/process/result/:id',
        name: 'RadiologyListProcessResultView',
        component: () => import('@/views/pages/radiology/RadiologyProcessResultView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/radiology/list/process/print-expertise/:order_id/:radiology_request_item_id/:image_id',
        name: 'RadiologyListProcessExpertiseView',
        component: () => import('@/views/pages/radiology/print/OrderListView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/radiology/list/process/print-result/:id',
        name: 'RadiologyPrintResultView',
        component: () => import('@/views/pages/radiology/print/RadiologyResultView.vue'),
        meta: {
            layout: 'full'
        }
    },
    // ------------------ inspection ------------------
    {
        path: '/radiology/inspection-report',
        name: 'InspectionReportView',
        component: () => import('@/views/pages/radiology/InspectionReportView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/radiology/dashboard',
        name: 'RadiologyDashboardView',
        component: () => import('@/views/pages/radiology/RadiologyDashboardView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/radiology/action',
        name: 'RadiologyActionView',
        component: () => import('@/views/pages/radiology/RadiologyActionView.vue'),
        meta: {
            layout: 'full'
        }
    },
    
]
