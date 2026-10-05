export default [
    {
      path: "/bpjs/letter/:id/:no",
      name: "BpjsLetterView",
      component: () => import("@/views/pages/BPJS/BpjsLetterView.vue"),
      meta: {
        layout: "full",
      },
    },
    {
      path: "/bpjs/refer/:id/:no",
      name: "BpjsReferView",
      component: () => import("@/views/pages/BPJS/BpjsReferView.vue"),
      meta: {
        layout: "full",
      },
    },
    {
      path: "/bpjs/refer-special/:id/:no",
      name: "BpjsReferSpecialView",
      component: () => import("@/views/pages/BPJS/BpjsReferSpecialView.vue"),
      meta: {
        layout: "full",
      },
    },
    {
      path: "/bpjs/checkout/:id/:no",
      name: "BpjsCheckoutView",
      component: () => import("@/views/pages/BPJS/BpjsCheckoutView.vue"),
      meta: {
        layout: "full",
      },
    },
    {
      path: "/bpjs/refer-prb/:id/:no",
      name: "BpjsReferPrbView",
      component: () => import("@/views/pages/BPJS/BpjsReferPrbView.vue"),
      meta: {
        layout: "full",
      },
    },
    // CEK PESERTA BPJS
    {
        path: '/bpjs/member-check',
        name: 'BpjsMemberCheckView',
        component: () => import('@/views/pages/BPJS/BPJSMemberCheckView.vue'),
        meta: {
            layout: 'full'
        }
    },

    // DATA PESERTA BPJS
    {
        path: '/bpjs/member-data',
        name: 'BpjsMemberDataView',
        component: () => import('@/views/pages/BPJS/bpjs_member_data/BPJSMemberDataView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/bpjs/member-data/update/:id',
        name: 'BpjsMemberDataUpdateView',
        component: () => import('@/views/pages/BPJS/bpjs_member_data/BPJSMemberDataUpdateView.vue'),
        meta: {
            layout: 'full'
        }
    },

    // DATA PENGAJUAN
    {
        path: '/bpjs/data-submission',
        name: 'BpjsDataSubmissionView',
        component: () => import('@/views/pages/BPJS/DataSubmissionView.vue'),
        meta: {
            layout: 'full'
        }
    },

    // DATA PENGAJUAN KLAIM
    {
        path: '/bpjs/claim-submission-sheet',
        name: 'BpjsClaimSubmissionSheetView',
        component: () => import('@/views/pages/BPJS/ClaimSubmissionSheetView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/bpjs/claim-submission-sheet/create',
        name: 'BpjsCreateClaimView',
        component: () => import('@/views/pages/BPJS/CreateClaimView.vue'),
        meta: {
            layout: 'full'
        }
    },

    // MONITORING KUNJUNGAN
    {
        path: '/bpjs/visit-monitoring',
        name: 'BpjsVisitMonitoringView',
        component: () => import('@/views/pages/BPJS/VisitMonitoringView.vue'),
        meta: {
            layout: 'full'
        }
    },

    // DATA RUJUKAN PESERTA BPJS
    {
        path: '/bpjs/reference-data',
        name: 'BpjsReferenceDataView',
        component: () => import('@/views/pages/BPJS/BPJSReferenceDataView.vue'),
        meta: {
            layout: 'full'
        }
    },
]
