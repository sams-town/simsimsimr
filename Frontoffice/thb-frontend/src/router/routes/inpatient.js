export default [
    // ------------------ Ward List ------------------
    {
        path: '/inpatient/ward',
        name: 'InpatientWardView',
        component: () => import('@/views/pages/inpatient/ward_list/WardListView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/inpatient/ward/:health_facility_item_id',
        name: 'InpatientWardDetailView',
        component: () => import('@/views/pages/inpatient/ward_list/WardDetailView.vue'),
        meta: {
            layout: 'full'
        }
    },
    // ------------------ Ward Monitoring ------------------
    {
        path: '/inpatient/monitoring',
        name: 'InpatientMonitoringView',
        component: () => import('@/views/layouts/inpatient/ward_monitoring/WardMonitoring.vue'),
        // component: () => import('@/views/pages/inpatient/ward_monitoring/WardMonitoringView.vue'),
        meta: {
            layout: 'full'
        }
    },
    // ------------------ Inpatient List ------------------
    {
        path: '/inpatient/list',
        name: 'InpatientListView',
        component: () => import('@/views/pages/inpatient/inpatient_list/InpatientListView.vue'),
        meta: {
            layout: 'full'
        }
    },
]
