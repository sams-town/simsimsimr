export default [
    // backoffice
    {
        path: '/backoffice/coa',
        name: 'BackofficeCoaView',
        component: () => import('@/views/pages/backoffice/coa/CoaBackofficeView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/backoffice/classification',
        name: 'BackofficeClassificationView',
        component: () => import('@/views/pages/backoffice/coa/ClassificationBackofficeView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/backoffice/classification/header',
        name: 'BackofficeClassificationHeaderView',
        component: () => import('@/views/pages/backoffice/coa/ClassificationHeaderBackofficeView.vue'),
        meta: {
            layout: 'full'
        }
    }
]
