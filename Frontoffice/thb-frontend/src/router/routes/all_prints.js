export default [
    // GENERAL
    {
        path: '/prints/label/:id',
        name: 'PrintsLabelView',
        component: () => import('@/views/pages/all_prints/LabelView.vue'),
        // component: () => import('@/views/pages/all_prints/PrintPage.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/label/lab/:id',
        name: 'PrintsLabelLabView',
        component: () => import('@/views/pages/all_prints/LabelLabView.vue'),
        // component: () => import('@/views/pages/all_prints/PrintPage.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/label/radiology/:id',
        name: 'PrintsLabelRadiologyView',
        component: () => import('@/views/pages/all_prints/LabelRadiologyView.vue'),
        // component: () => import('@/views/pages/all_prints/PrintPage.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/antrean/:id',
        name: 'PrintsAntreanView',
        component: () => import('@/views/pages/all_prints/AntreanView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/struk/:id',
        name: 'PrintsStrukView',
        component: () => import('@/views/pages/all_prints/StrukView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/resume/inpatient/:soap_id',
        name: 'ResumesPrintView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorResumeInpatientPrint.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/card/:norm',
        name: 'PrintsCardView',
        component: () => import('@/views/pages/all_prints/CardView.vue'),
        meta: {
            layout: 'full'
        }
    },

    // BPJS
    {
        path: '/prints/bpjs-member/:id',
        name: 'PrintsBpjsMemberView',
        component: () => import('@/views/pages/all_prints/bpjs/BPJSMemberView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/sep/:id/:no',
        name: 'PrintsBpjsSepView',
        component: () => import('@/views/pages/all_prints/bpjs/BPJSSepView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/rujukan/:id/:no',
        name: 'PrintsBpjsRujukanView',
        component: () => import('@/views/pages/all_prints/bpjs/BPJSRujukanView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/control/:id/:no',
        name: 'PrintsBpjsControlView',
        component: () => import('@/views/pages/all_prints/bpjs/BPJSControlView.vue'),
        meta: {
            layout: 'full'
        }
    },

    //print dokumen surat-surat
    {
        path:'/prints/surat-hak-kewajiban-pasien-keluarga/:id/:letter_id',
        name: 'SuratHakKewajibanPasienKeluarga',
        component:()=>import('@/views/pages/all_prints/SuratHakKewajibanPasienKeluarga.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path:'/prints/surat-pernyataan-bpjs-kesehatan/:id/:letter_id',
        name: 'SuratPernyataanBPJSKesehatan',
        component:()=>import('@/views/pages/all_prints/SuratPernyataanBpjsKesehatan.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path:'/prints/surat-kematian/:reg_des_id/:letter_id',
        name: 'SuratKematian',
        component:()=>import('@/views/pages/all_prints/SuratKematian.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path:'/prints/surat-kronologi-kejadian/:id/:letter_id',
        name: 'SuratKronologiKejadian',
        component:()=>import('@/views/pages/all_prints/SuratKronologiKejadian.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path:'/prints/surat-permintaan-naik-kelas-rawat-perawatan/:id/:letter_id',
        name: 'SuratPermintaanNaikKelasRawatPerawatan',
        component:()=>import('@/views/pages/all_prints/SuratPermintaanNaikKelasPerawatan.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path:'/prints/surat-keterangan-lahir/:nurse_id',
        name: 'SuratKeteranganLahir',
        component:()=>import('@/views/pages/all_prints/SuratKeteranganLahir.vue'),
        meta: {
            layout: 'full'
        }
    },
        {
        path:'/prints/perkiraan-biaya-op-tindakan/:id/:letter_id',
        name: 'PerkiraanBiayaOperasiatauTindakan',
        component:()=>import('@/views/pages/all_prints/PerkiraanBiayaOperasiTindakan.vue'),
        meta: {
            layout: 'full'
        }
    },

    // Medical Certificate
    {
        path: '/prints/medical-certificate/:id/:letter_id',
        name: 'MedicalCertificateView',
        component: () => import('@/views/pages/all_prints/MedicalCertificateView.vue'),
        meta: {
            layout: 'full'
        }
    },
        // Surat Keterangan Sehat
    {
        path: '/prints/surat-keterangan-sehat/:id/:letter_id',
        name: 'HealthCertificateView',
        component: () => import('@/views/pages/all_prints/HealthCertificateView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/surat-keterangan-lhair/:id/:letter_id',
        name: 'HealthCertificateView',
        component: () => import('@/views/pages/all_prints/HealthCertificateView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/spri/:id',
        name: 'SPRIView',
        component: () => import('@/views/pages/all_prints/SPRIView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/skri/:id/:letter_id',
        name: 'SKRIView',
        component: () => import('@/views/pages/all_prints/SKRIView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/flight-certificate/:reg_des_id/:letter_id',
        name: 'FlightCertificateView',
        component: () => import('@/views/pages/all_prints/FlightCertificateView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/guarantee-letter/:reg_des_id/:letter_id',
        name: 'GuaranteeLetterView',
        component: () => import('@/views/pages/all_prints/GuaranteeLetterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/inform-endoscopy-letter/:reg_des_id/:letter_id',
        name: 'EndoscopyLetterView',
        component: () => import('@/views/pages/all_prints/InformConsentEndoscopy.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/inform-kolonoskopi-letter/:reg_des_id/:letter_id',
        name: 'KolonoskopiLetterView',
        component: () => import('@/views/pages/all_prints/InformConsentKolonoskopi.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/inform-ligasi-letter/:reg_des_id/:letter_id',
        name: 'LigasiLetterView',
        component: () => import('@/views/pages/all_prints/InformConsentLigasi.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/death-certificate/:reg_des_id/:letter_id',
        name: 'DeathCertificateView',
        component: () => import('@/views/pages/all_prints/DeathCertificateView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/health-certificate/:reg_des_id',
        name: 'HealthCertificateView',
        component: () => import('@/views/pages/all_prints/HealthCertificateView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/treatment-certificate/:reg_des_id',
        name: 'TreatmentCertificateView',
        component: () => import('@/views/pages/all_prints/TreatmentCertificateView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/outgoing-referral-letter/:reg_des_id',
        name: 'OutReferralLetterView',
        component: () => import('@/views/pages/all_prints/OutReferralLetterView.vue'),
        meta: {
            layout: 'full'
        }
    },

    {
        path: '/prints/surat-perjanjian-kunjungan/:reg_des_id',
        name: 'SuratPerjanjianKunjunganPrintView',
        component: () => import('@/views/pages/all_prints/SuratPerjanjianKunjungan.vue'),
        meta: {
            layout: 'full'
        }
    },

    // {
    //     path: '/prints/surat-perjanjian-kunjungan/:reg_des_id/:resume_id',
    //     name: 'SuratPerjanjianKunjunganPrintView',
    //     component: () => import('@/views/pages/all_prints/SuratPerjanjianKunjungan.vue'),
    //     meta: {
    //         layout: 'full'
    //     }
    // },

    // Doctor Studies
    {
        path: '/prints/doctor-studies-print/:soap_id',
        name: 'DoctorStudiesPrintView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorStudiesPrint.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/mcu/:soap_id',
        name: 'DoctorStudiesPrintView',
        component: () => import('@/views/pages/all_prints/patient/doctor/MCUPrint.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/doctor-sbpk-print/:soap_id',
        name: 'DoctorSbpkPrintView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorSbpkPrint.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/mcu-result/:soap_id',
        name: 'McuResultView',
        component: () => import('@/views/pages/all_prints/patient/doctor/McuResult.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/orthopedi-result/:soap_id',
        name: 'OrthopediResultView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorOrthopediPrint.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/eye-result/:soap_id',
        name: 'McuResultView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorEyePrint.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/pulmo-result/:soap_id',
        name: 'PulmoResultView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorPulmoPrint.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/mcu-drug/:soap_id',
        name: 'McuDrugView',
        component: () => import('@/views/pages/all_prints/patient/doctor/McuDrug.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/resume/:soap_id',
        name: 'ResumesPrintView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorResumePrint.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/resumechronic/:soap_id',
        name: 'ResumesDrChronicPrintView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorResumeChronicPrint.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/doctor-emergency-print/v1/:soap_id',
        name: 'DoctorEmergencyPrintV1View',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorEmergencyPrintV1.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/doctor-emergency-print/:soap_id',
        name: 'DoctorEmergencyPrintView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorEmergencyPrint.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/booking-laboratory-print/:soap_id',
        name: 'DoctorBookingSupportLaboratoryPrintView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorSupportLaboratoryPrint.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/booking-radiology-print/:soap_id',
        name: 'DoctorBookingSupportRadiologyPrintView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorSupportRadiologyPrint.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/doctor-hemodialysis-print/:soap_id',
        name: 'DoctorHemodialysisPrintView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorHemodialysisPrint.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/doctor-daily-hemodialysis-print/:soap_id',
        name: 'DoctorDailyHemodialysisPrintView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorDailyHemodialysisPrint.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/doctor-intradialytic-print/:soap_id',
        name: 'DoctorIntradialyticPrintView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorIntradialyticPrint.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/doctor-tools-gauze-print/:soap_id',
        name: 'DoctorToolsGauzePrintView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorToolsGauzePrint.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/doctor-discharge-planner-print/:soap_id',
        name: 'DoctorDischargePlannerPrintView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorDischargePlanner.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/doctor-rehabilitation-print/:soap_id',
        name: 'DoctorRehabilitationPrintView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorRehabilitationPrint.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/doctor-rehabilitation-result-print/:soap_id',
        name: 'DoctorRehabilitationResultPrintView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorRehabilitationResultPrint.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/doctor-rehabilitation-reassessment-print/:soap_id',
        name: 'DoctorRehabilitationReassessmentPrintView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorRehabilitationReassessmentPrint.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/doctor-therapy-program-print/:soap_id',
        name: 'DoctorTherapyProgramPrintView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorTherapyProgramPrint.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/doctor-certificate-print/:soap_id',
        name: 'DoctorCertificatePrintView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorCertificatePrint.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/prints/doctor-rehabilitation-schedule-print/:soap_id',
        name: 'DoctorRehabilitationSchedulePrintView',
        component: () => import('@/views/pages/all_prints/patient/doctor/DoctorRehabilitationSchedulePrint.vue'),
        meta: {
            layout: 'full'
        }
    },
]
