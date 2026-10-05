export default [
    // payment
    {
        path: '/operatie-kamer',
        name: 'OperatieView',
        component: () => import('@/views/pages/operatie/OperatieKamerView.vue'),
        meta: {
            layout: 'full'
        }
    },

    {
        path: "/operatie-kamer/print-calendar",
        name: "PrintOperatieKamerCalendar",
        component: () => import("@/views/layouts/operatie/print/PrintOperatieKamerCalendar.vue"),
        meta: {
          layout: "full",
        },
    },
    
    {
        path: '/operatie-kamer/:ok_id',
        name: 'OperatieDetailView',
        component: () => import('@/views/pages/operatie/OperatieDetailView.vue'),
        meta: {
            layout: 'full'
        }
    },

    {
        path: "/operatie-kamer/print/:ok_id/:report_id",
        name: "PrintOperatieKamer",
        component: () => import("@/views/layouts/operatie/print/PrintOperatieKamer.vue"),
        meta: {
          layout: "full",
        },
    },
    {
        path: "/operatie-kamer/print-calendar",
        name: "PrintOperatieKamerCalendar",
        component: () => import("@/views/layouts/operatie/PrintOperatieKamerCalendar.vue"),
        meta: {
        layout: "full",
        },
    },
    
]
