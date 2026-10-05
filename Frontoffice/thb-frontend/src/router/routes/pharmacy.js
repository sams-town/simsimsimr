export default [
    // ------------------------ Planning transaction -------------------------
    {
        path: '/pharmacy/planning',
        name: 'PharmacyPlanningView',
        component: () => import('@/views/pages/pharmacy/planning/PlanningTransactionView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/planning/procurement',
        name: 'PharmacyPlanningProcurementView',
        component: () => import('@/views/pages/pharmacy/planning/print/ProcurementView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/planning/details',
        name: 'PharmacyPlanningDetailsView',
        component: () => import('@/views/pages/pharmacy/planning/PlanningDetailsView.vue'),
        meta: {
            layout: 'full'
        }
    },
    // ------------------------ Purchase Order -------------------------
    {
        path: '/pharmacy/purchase',
        name: 'PharmacyPurchaseView',
        component: () => import('@/views/pages/pharmacy/purchase/PurchaseOrderView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/purchase/print/:id',
        name: 'PharmacyPurchasePrintView',
        component: () => import('@/views/pages/pharmacy/purchase/print/PrintPurchaseView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/purchase/print/prekusor/:id',
        name: 'PharmacyPurchasePrintPrekusorView',
        component: () => import('@/views/pages/pharmacy/purchase/print/PrintPurchasePrekusorView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/purchase/print/oot/:id',
        name: 'PharmacyPurchasePrintOOTView',
        component: () => import('@/views/pages/pharmacy/purchase/print/PrintPurchaseOOTView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/purchase/print/psikotropika/:id',
        name: 'PharmacyPurchasePrintPsikotropikaView',
        component: () => import('@/views/pages/pharmacy/purchase/print/PrintPurchasePsikotropikaView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/purchase/details',
        name: 'PharmacyPurchaseDetailsView',
        component: () => import('@/views/pages/pharmacy/purchase/PurchaseDetailsView.vue'),
        meta: {
            layout: 'full'
        }
    },
    // ------------------------ Receive -------------------------
    {
        path: '/pharmacy/receive',
        name: 'PharmacyReceiveView',
        component: () => import('@/views/pages/pharmacy/receive/PharmacyReceiveView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/receive/print/:id',
        name: 'PharmacyReceivePrintView',
        component: () => import('@/views/pages/pharmacy/receive/print/PrintReceiveView.vue'),
        meta: {
            layout: 'full'
        }
    },
    // ------------------------ Card Stock -------------------------
    {
        path: '/pharmacy/stock',
        name: 'PharmacyStockView',
        component: () => import('@/views/pages/pharmacy/stock/CardStockView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/stock/available',
        name: 'PharmacyStockAvailableView',
        component: () => import('@/views/pages/pharmacy/stock/print/AvailabilityCardView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/stock/supplies',
        name: 'PharmacySuppliesView',
        component: () => import('@/views/pages/pharmacy/stock/print/SuppliesView.vue'),
        meta: {
            layout: 'full'
        }
    },
    // ------------------------ mutation of medical devices -------------------------
    {
        path: '/pharmacy/mutation',
        name: 'PharmacyMutationView',
        component: () => import('@/views/pages/pharmacy/mutation/MutationView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/mutation/unit',
        name: 'PharmacyMutationUnitView',
        component: () => import('@/views/pages/pharmacy/mutation/MutationView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/mutation/print/:id',
        name: 'PharmacyMutationPrintView',
        component: () => import('@/views/pages/pharmacy/mutation/print/PrintMutationView.vue'),
        meta: {
            layout: 'full'
        }
    },
    // ------------------------ Recipe -------------------------
    {
        path: '/pharmacy/recipe/outpatient',
        name: 'PharmacyRecipeOutpatientView',
        component: () => import('@/views/pages/pharmacy/recipe/outpatient/OutpatientRecipeView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/recipe/free',
        name: 'PharmacyRecipeFreeView',
        component: () => import('@/views/pages/pharmacy/recipe/outpatient/RecipeFreeView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/recipe/inpatient',
        name: 'PharmacyRecipeInpatientView',
        component: () => import('@/views/pages/pharmacy/recipe/outpatient/InpatientRecipeView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/recipe/emergency',
        name: 'PharmacyRecipeEmergencyView',
        component: () => import('@/views/pages/pharmacy/recipe/outpatient/EmergencyRecipeView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/recipe/add',
        name: 'PharmacyRecipeAddView',
        component: () => import('@/views/pages/pharmacy/recipe/outpatient/PharmacyRecipeAddView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/recipe/history',
        name: 'PharmacyRecipeHistoryView',
        component: () => import('@/views/pages/pharmacy/recipe/outpatient/PharmacyRecipeHistoryView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/recipe/ok',
        name: 'PharmacyRecipeOKView',
        component: () => import('@/views/pages/pharmacy/recipe/outpatient/PharmacyRecipeOKView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/recipe/outpatient/detail/:id',
        name: 'PharmacyRecipeOutpatientDetailView',
        component: () => import('@/views/pages/pharmacy/recipe/outpatient/OutpatientRecipeDetailView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/recipe/outpatient/payment/:id',
        name: 'PharmacyRecipeOutpatientPaymentView',
        component: () => import('@/views/pages/pharmacy/recipe/outpatient/OutpatientRecipePaymentView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/recipe/outpatient/retur/:id',
        name: 'PharmacyRecipeOutpatientReturView',
        component: () => import('@/views/pages/pharmacy/recipe/outpatient/OutpatientRecipeReturView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/recipe/substitution/:id/:pharmacy_catalog_id',
        name: 'PharmacyRecipeSubstitutionView',
        component: () => import('@/views/layouts/pharmacy/recipe/outpatient/Substitution.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/recipe/verification/:id',
        name: 'PharmacyRecipeVerificationView',
        component: () => import('@/views/layouts/pharmacy/recipe/outpatient/Verification.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/recipe/eticket/:id',
        name: 'PharmacyRecipeEticketView',
        component: () => import('@/views/layouts/pharmacy/recipe/outpatient/Eticket.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/recipe/print-recipe/:id',
        name: 'PharmacyRecipePrintView',
        component: () => import('@/views/pages/pharmacy/recipe/PrintRecipe.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/recipe/print-recipe-check/:id',
        name: 'PharmacyRecipeCheckPrintView',
        component: () => import('@/views/pages/pharmacy/recipe/PrintRecipeCheck.vue'),
        meta: {
            layout: 'full'
        }
    },
    // ------------------------ Warehouse Stock Reduction -------------------------
    {
        path: '/pharmacy/reduction',
        name: 'PharmacyReductionView',
        component: () => import('@/views/pages/pharmacy/reduction/ReductionStockView.vue'),
        meta: {
            layout: 'full'
        }
    },
    // ------------------------ Consultation -------------------------
    {
        path: '/pharmacy/consultation',
        name: 'PharmacyConsultationView',
        component: () => import('@/views/pages/pharmacy/consultation/PharmacyConsultationView.vue'),
        meta: {
            layout: 'full'
        }
    },
    // ------------------------ Selling Price -------------------------
    {
        path: '/pharmacy/selling',
        name: 'PharmacySellingView',
        component: () => import('@/views/pages/pharmacy/selling/SellingPriceView.vue'),
        meta: {
            layout: 'full'
        }
    },
    // ------------------------ Selling Price Review -------------------------
    {
        path: '/pharmacy/review',
        name: 'PharmacySPReviewView',
        component: () => import('@/views/pages/pharmacy/SPReview/SPReviewView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/review/update',
        name: 'PharmacySPReviewUpdateView',
        component: () => import('@/views/pages/pharmacy/SPReview/SPUpdateView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/review/price-list',
        name: 'PharmacySPReviewPriceListView',
        component: () => import('@/views/pages/pharmacy/SPReview/print/PriceListView.vue'),
        meta: {
            layout: 'full'
        }
    },
    // ------------------------ Catalog -------------------------
    {
        path: '/pharmacy/catalog',
        name: 'PharmacyCatalogMasterView',
        component: () => import('@/views/pages/pharmacy/catalog/CatalogMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/catalog/warehouse-stock-print',
        name: 'PharmacyWarehouseStockPrintView',
        component: () => import('@/views/pages/pharmacy/catalog/print/WarehouseStockView.vue'),
        meta: {
            layout: 'full'
        }
    },
    // ------------------------ Opname -------------------------
    {
        path: '/pharmacy/opname',
        name: 'PharmacyOpnameView',
        component: () => import('@/views/pages/pharmacy/opname/OpnameView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/opname/print',
        name: 'PharmacyOpnamePrintView',
        component: () => import('@/views/pages/pharmacy/opname/print/OpnameStockView.vue'),
        meta: {
            layout: 'full'
        }
    },
    // ------------------------ Reports -------------------------
    {
        path: '/pharmacy/report',
        name: 'PharmacyReportView',
        component: () => import('@/views/pages/pharmacy/report/ReportView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/report/acceptance',
        name: 'PharmacyReportAcceptanceView',
        component: () => import('@/views/pages/pharmacy/report/acceptance/AcceptanceReportView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/report/order',
        name: 'PharmacyReportOrderView',
        component: () => import('@/views/pages/pharmacy/report/order/OrderReportView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/report/opname',
        name: 'PharmacyReportOpnameView',
        component: () => import('@/views/pages/pharmacy/report/opname/OpnameReportView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/report/acceptance/print',
        name: 'PharmacyReportAcceptancePrintView',
        component: () => import('@/views/pages/pharmacy/report/acceptance/print/AcceptancePrintView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/pharmacy/report/sales',
        name: 'PharmacyReportSalesView',
        component: () => import('@/views/pages/pharmacy/report/sales/SalesReportView.vue'),
        meta: {
            layout: 'full'
        }
    },

]
