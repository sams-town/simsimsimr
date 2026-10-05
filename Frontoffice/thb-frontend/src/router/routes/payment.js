export default [
    // payment
    {
        path: '/payment/patient-list',
        name: 'PatientPaymentListView',
        component: () => import('@/views/pages/payment/PaymentListView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/patient-list/detail',
        name: 'PatientPaymentListDetailView',
        component: () => import('@/views/pages/payment/PaymentListDetailView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/detail/:id',
        name: 'PatientPaymentListDetailView',
        component: () => import('@/views/pages/payment/PaymentListDetailView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/detail/add-dp/:id',
        name: 'PatientPaymentAddDPView',
        component: () => import('@/views/pages/payment/AddDownPaymentView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/invoice/:id',
        name: 'PaymentInvoiceView',
        component: () => import('@/views/pages/payment/PaymentInvoiceView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/invoice_free/:id',
        name: 'PaymentInvoiceView',
        component: () => import('@/views/pages/payment/PaymentInvoiceFreeView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/kwitansi/:id',
        name: 'PaymentKwitansiView',
        component: () => import('@/views/pages/payment/PaymentKwitansiView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/kwitansi/dp/:id',
        name: 'PaymentKwitansiDPView',
        component: () => import('@/views/pages/payment/PaymentKwitansiDPView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/patient-list/detail/invoice',
        name: 'PatientPaymentInvoiceView',
        component: () => import('@/views/pages/payment/PaymentInvoiceView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/patient-list/detail/eklaim',
        name: 'PatientPaymentEklaimView',
        component: () => import('@/views/pages/payment/print/EKlaimView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/detail/print/bill-preview',
        name: 'PatientPaymentBillPreviewView',
        component: () => import('@/views/pages/payment/print/BillPreviewView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/detail/print/bill-preview/dot',
        name: 'PatientPaymentBillPreviewDotView',
        component: () => import('@/views/pages/payment/print/BillPreviewDotMatrixView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/detail/print/kwitansi-preview',
        name: 'PatientPaymentKwitansiPreviewView',
        component: () => import('@/views/pages/payment/print/KwitansiPreviewView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/detail/print/kwitansi-preview/dot',
        name: 'PatientPaymentKwitansiPreviewDotView',
        component: () => import('@/views/pages/payment/print/KwitansiPreviewDotMatrixView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/repayment/:id',
        name: 'PatientPaymentRepaymentView',
        component: () => import('@/views/pages/payment/RepaymentView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/repaymentfree/:id',
        name: 'PatientPaymentRepaymentFreeView',
        component: () => import('@/views/pages/payment/RepaymentFreeView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/patient-list/detail/repayment-detail',
        name: 'PatientPaymentRepaymentDetailView',
        component: () => import('@/views/pages/payment/RepaymentDetailView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/patient-list/detail/repayment-detail/kwitansi',
        name: 'PatientPaymentKwitansiView',
        component: () => import('@/views/pages/payment/print/KwitansiView.vue'),
        meta: {
            layout: 'full'
        }
    },
    
    {
        path: '/payment/inpatient-list',
        name: 'InpatientPaymentListView',
        component: () => import('@/views/pages/payment/InpatientPaymentListView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/doctor-services',
        name: 'DoctorServicesView',
        component: () => import('@/views/pages/payment/DoctorServicesView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/doctor-services/report',
        name: 'DoctorServicesReportView',
        component: () => import('@/views/pages/payment/print/DoctorServicesReportView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/report',
        name: 'PaymentReportView',
        component: () => import('@/views/pages/payment/ReportView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/report/print',
        name: 'PaymentReportExportView',
        component: () => import('@/views/pages/payment/print/ReportExportView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/payment/free-patient',
        name: 'FreePatientView',
        component: () => import('@/views/pages/payment/FreePatientPaymentView.vue'),
        meta: {
            layout: 'full'
        }
    },
]
