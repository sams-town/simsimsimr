export default [
    // ------------------------ Logistic Planning -------------------------
    {
        path: '/logistic/planning',
        name: 'LogisticPlanningView',
        component: () => import('@/views/pages/logistic/planning/PlanningView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/logistic/planning/print',
        name: 'LogisticPlanningPrintView',
        component: () => import('@/views/pages/logistic/planning/print/PlanningPrintView.vue'),
        meta: {
            layout: 'full'
        }
    },

    // ------------------------ Logistic Planning -------------------------
    {
        path: '/logistic/request',
        name: 'LogisticRequestView',
        component: () => import('@/views/pages/logistic/request/RequestView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/logistic/request/print',
        name: 'LogisticRequestPrintView',
        component: () => import('@/views/pages/logistic/request/print/RequestPrintView.vue'),
        meta: {
            layout: 'full'
        }
    },

    // ------------------------ Purchase Order -------------------------
    {
        path: '/logistic/purchase-order',
        name: 'LogisticPOView',
        component: () => import('@/views/pages/logistic/PO/PurchaseView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/logistic/purchase-order/print',
        name: 'LogisticPOPrintView',
        component: () => import('@/views/pages/logistic/PO/print/POPrintView.vue'),
        meta: {
            layout: 'full'
        }
    },

    // ------------------------ Penerimaan Logistik -------------------------
    {
        path: '/logistic/acceptance',
        name: 'LogisticAcceptanceView',
        component: () => import('@/views/pages/logistic/acceptance/AcceptanceView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/logistic/acceptance/print',
        name: 'LogisticAcceptancePrintView',
        component: () => import('@/views/pages/logistic/acceptance/print/AcceptancePrintView.vue'),
        meta: {
            layout: 'full'
        }
    },

    // ------------------------ Penerimaan Logistik -------------------------
    {
        path: '/logistic/stock',
        name: 'LogisticStockView',
        component: () => import('@/views/pages/logistic/stock/StockView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/logistic/stock/card-stock',
        name: 'LogisticStockCardView',
        component: () => import('@/views/pages/logistic/stock/print/CardStockView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/logistic/stock/logistic-stock',
        name: 'LogisticStocLogisticView',
        component: () => import('@/views/pages/logistic/stock/print/LogisticStockView.vue'),
        meta: {
            layout: 'full'
        }
    },

    // ------------------------ Penerimaan Logistik -------------------------
    {
        path: '/logistic/demand',
        name: 'LogisticUDemandView',
        component: () => import('@/views/pages/logistic/unit_demand/UnitDemandView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/logistic/demand/print',
        name: 'LogisticUDemandPrintView',
        component: () => import('@/views/pages/logistic/unit_demand/print/UnitDemandPrintView.vue'),
        meta: {
            layout: 'full'
        }
    },

     // ------------------------ Master Barang -------------------------
     {
        path: '/logistic/item-master',
        name: 'LogisticItemMasterView',
        component: () => import('@/views/pages/logistic/item_master/ItemMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },

     // ------------------------ Master Depo -------------------------
     {
        path: '/logistic/depo',
        name: 'LogisticDepoView',
        component: () => import('@/views/pages/logistic/depo/DepoView.vue'),
        meta: {
            layout: 'full'
        }
    },

     // ------------------------ Opname Stock -------------------------
     {
        path: '/logistic/opname-stock',
        name: 'LogisticOpnameView',
        component: () => import('@/views/pages/logistic/opname/OpnameStockView.vue'),
        meta: {
            layout: 'full'
        }
    },

     // ------------------------ Laporan -------------------------
     {
        path: '/logistic/report',
        name: 'LogisticReportView',
        component: () => import('@/views/pages/logistic/report/LogisticReportView.vue'),
        meta: {
            layout: 'full'
        }
    },
    
]
