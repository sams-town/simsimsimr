export default [
  {
    path: '/bridge-machine',
    name: 'BridgeMachineView',
    component: () => import('@/views/pages/queue/bridgemachine/BridgeMachineView.vue'),
    meta: {
      layout: 'full'
    }
  },
  {
    path: '/bridge-machine-new',
    name: 'BridgeMachineNewView',
    component: () => import('@/views/layouts/queue/bridgemachine/BridgeMachineNew.vue'),
    meta: {
      layout: 'full'
    }
  },
  {
    path: '/jkn-checkin',
    name: 'JknCheckinView',
    component: () => import('@/views/pages/queue/bridgemachine/JknCheckinView.vue'),
    meta: {
      layout: 'full'
    }
  },
  {
    path: '/display-master',
    name: 'DisplayPlasmaView',
    component: () => import('@/views/pages/queue/bridgemachine/DisplayPlasmaView.vue'),
    meta: {
      layout: 'full'
    }
  },
  {
    path: '/ok',
    name: 'OKView',
    component: () => import('@/views/pages/queue/bridgemachine/OKView.vue'),
    meta: {
      layout: 'full'
    }
  },
  {
    path: '/display-admisi',
    name: 'BridgeMachineView',
    component: () => import('@/views/pages/queue/bridgemachine/DisplayMachineView.vue'),
    meta: {
      layout: 'full'
    }
  },
  {
    path: '/queue/admision',
    name: 'QueueAdmisionView',
    component: () => import('@/views/pages/queue/bridgemachine/QueueAdmisionView.vue'),
    meta: {
      layout: 'full'
    }
  },
  {
    path: '/bridge-dashboard',
    name: 'BridgeDashboardView',
    component: () => import('@/views/pages/queue/bridgemachine/BridgeDashboardView.vue'),
    meta: {
      layout: 'full'
    }
  },

  // REGISTRATION 

  // List pendaftaran pasien
  {
    path: '/registration/patient-list',
    name: 'PatientListView',
    component: () => import('@/views/pages/registration/patient_registration_list/PatientRegistrationListView.vue'),
    meta: {
      layout: 'full'
    }
  },

  // List pendaftaran eksternal
  {
    path: '/registration/external-list',
    name: 'ExternalListView',
    component: () => import('@/views/pages/registration/external_registration_list/ExternalRegistrationListView.vue'),
    meta: {
      layout: 'full'
    }
  },

  // List pendaftaran batal
  {
    path: '/registration/cancel-list',
    name: 'CancelListView',
    component: () => import('@/views/pages/registration/cancel_registration_list/CancelRegistrationListView.vue'),
    meta: {
      layout: 'full'
    }
  },

  // List sensus rawat inap
  {
    path: '/registration/sensus-list',
    name: 'SensusListView',
    component: () => import('@/views/pages/registration/inpatient_census_list/InpatientCensusListView.vue'),
    meta: {
      layout: 'full'
    }
  },

  // Jadwal praktek dokter
  {
    path: '/registration/schedule-doctor',
    name: 'ScheduleDoctorView',
    component: () => import('@/views/pages/registration/doctor_practice_schedule/DoctorPracticeScheduleView.vue'),
    meta: {
      layout: 'full'
    }
  },

  // laporan kunjungan rawat jalan
  {
    path: '/registration/outpatient-visit-report',
    name: 'OutpatientVisitReportView',
    component: () => import('@/views/pages/registration/outpatient_visit_report/OutpatientVisitReportView.vue'),
    meta: {
      layout: 'full'
    }
  },

  // Pendaftaran
  {
    path: '/registration/regist-patient',
    name: 'RegisMasterView',
    component: () => import('@/views/pages/registration/registerPatient.vue'),
    meta: {
      layout: 'full'
    }
  },

  // Jadwal kontrol pasien
  {
    path: '/registration/patient-control-schedule',
    name: 'SchedulePatientView',
    component: () => import('@/views/pages/registration/patient_control_list/PatientControlListView.vue'),
    meta: {
      layout: 'full'
    }
  },
  {
    path: '/bpjs/refer-check',
    name: 'BPJSReferCheckView',
    component: () => import('@/views/pages/BPJS/BpjsReferCheckView.vue'),
    meta: {
      layout: 'full'
    }
  },
  {
    path: '/monitoring',
    name: 'MonitoringView',
    component: () => import('@/views/layouts/queue/nurse/Monitoring.vue'),
    meta: {
      layout: 'full'
    }
  },
  {
    path: '/registration/regist-patient-new/:id',
    name: 'RegisMasteNewrView',
    component: () => import('@/views/pages/registration/registerPatientNew.vue'),
    meta: {
      layout: 'full'
    }
  },
  {
    path: '/registration/regist-patient-new',
    name: 'RegisMasteNewrView',
    component: () => import('@/views/pages/registration/registerPatientNew.vue'),
    meta: {
      layout: 'full'
    }
  },

  // Pendaftaran eksternal
  {
    path: '/registration/external',
    name: 'ExternalRegView',
    component: () => import('@/views/pages/registration/ExternalRegView.vue'),
    meta: {
      layout: 'full'
    }
  },
  
  // Laporan
  {
    path: '/registration/report',
    name: 'ReportView',
    component: () => import('@/views/pages/registration/Report.vue'),
    meta: {
      layout: 'full'
    }
  },
  
  // Dashboard
  //  #

  // History rekam medis
  {
    path: '/registration/medical-history',
    name: 'MedicalHistoryView',
    component: () => import('@/views/pages/registration/MedicalHistory.vue'),
    meta: {
      layout: 'full'
    }
  },

  // Merge rekam medis
  {
    path: '/registration/medical-record',
    name: 'MedicalRecordView',
    component: () => import('@/views/pages/registration/medical_record/MedicalRecordView.vue'),
    meta: {
      layout: 'full'
    }
  },

  // Laporan rekapitulasi kegiatan pelayanan
  {
    path: '/registration/service-report',
    name: 'ServiceReportView',
    component: () => import('@/views/pages/registration/service_report/ServiceReportView.vue'),
    meta: {
      layout: 'full'
    }
  },

  // CANVASING
  
  {
    path: '/canvasing',
    name: 'CanvasView',
    component: () => import('@/views/pages/canvasing/CanvasView.vue'),
    meta: {
      layout: 'full'
    }
  },

  // PATIENT STUDIES
  {
    path: "/patient/detail/:id",
    name: "PatientDetailView",
    component: () => import("@/views/pages/patient/detail/PatientDetailView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/insurance/:id/:no",
    name: "PatientInsuranceView",
    component: () => import("@/views/pages/patient/insurance/PatientDetailView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/cppt/:id",
    name: "PatientCPPTView",
    component: () => import("@/views/pages/patient/cppt/CPPTView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/cppt/print/:cppt_id",
    name: "PatientPrintCPPTView",
    component: () => import("@/views/layouts/patient/cppt/print/CPPTPrint.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/sbar/print/:sbar_id",
    name: "PatientPrintSBARView",
    component: () => import("@/views/layouts/patient/sbar/print/SBARPrint.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/nurse/:id",
    name: "NurseStudiesView",
    component: () => import("@/views/pages/patient/nurse/NurseStudiesView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: '/patient/nurse/print/:nurse_id',
    name: 'PrintsMrNurseView',
    component: () => import('@/views/pages/all_prints/medical_record/NurseMedicalRecord.vue'),
    meta: {
      layout: 'full'
    }
  },
  {
    path: "/patient/nurse/signature/:reg_des_id",
    name: "NurseSignatureView",
    component: () => import("@/views/layouts/patient/nurse/patientSignature.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/doctor/:id",
    name: "DoctorStudiesView",
    component: () => import("@/views/pages/patient/doctor/DoctorStudiesView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/doctor/letter/:id",
    name: "PrintPatientDoctorLetterView",
    component: () => import("@/views/pages/all_prints/patient/doctor/LetterPrint.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/transfer-patient/:id",
    name: "TransferPatient",
    component: () => import("@/views/pages/all_prints/patient/TransferPatient.vue"),
    meta: {
      layout: "full",
    },
  },
  // {
  //   path: "/patient/doctor/death-certified",
  //   name: "DeathCertificateView",
  //   component: () => import("@/views/pages/patient/doctor/DeathCertificateView.vue"),
  //   meta: {
  //     layout: "full",
  //   },
  // },
  // {
  //   path: "/patient/doctor/health-certified",
  //   name: "HealthCertificateView",
  //   component: () => import("@/views/pages/patient/doctor/HealthCertificateView.vue"),
  //   meta: {
  //     layout: "full",
  //   },
  // },
  // {
  //   path: "/patient/doctor/sick-certified",
  //   name: "SickCertificateView",
  //   component: () => import("@/views/pages/patient/doctor/SickCertificateView.vue"),
  //   meta: {
  //     layout: "full",
  //   },
  // },
  {
    path: "/patient/attribute",
    name: "PatientAttributeView",
    component: () => import("@/views/pages/patient/print_attribute/AttributeView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/laboratory/:id",
    name: "PatientLaboratoryView",
    component: () => import("@/views/pages/patient/laboratory/LaboratoryView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/laboratory/information-certificate",
    name: "PatientLaboratoryInfoCertificateView",
    component: () => import("@/views/pages/patient/laboratory/InformationCertificateView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/laboratory/order-certificate",
    name: "PatientLaboratoryOrderCertificateView",
    component: () => import("@/views/pages/patient/laboratory/OrderCertificateView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/radiology/:id",
    name: "PatientRadiologyView",
    component: () => import("@/views/pages/patient/radiology/RadiologyView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/radiology/order-certificate",
    name: "PatientRadiologyOrderCertificateView",
    component: () => import("@/views/pages/patient/radiology/OrderCertificateView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/physiotherapy/:id",
    name: "PatientPhysiotherapyView",
    component: () => import("@/views/pages/patient/physiotherapy/PhysiotherapyView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/procedure/:id",
    name: "PatientProcedureView",
    component: () => import("@/views/pages/patient/procedure/PatientProcedureView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/tool-use/:id",
    name: "PatientToolView",
    component: () => import("@/views/pages/patient/tool/ToolUseView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/blood-gourd/:id",
    name: "PatientBloodGourdView",
    component: () => import("@/views/pages/patient/blood_gourd/BloodGourdView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/bhp/:id",
    name: "PatientBHPView",
    component: () => import("@/views/pages/patient/BHP/BHPView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/OK/:id",
    name: "PatientOKView",
    component: () => import("@/views/pages/patient/OK/OKView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/triage/:id",
    name: "PatientTriageView",
    component: () => import("@/views/pages/patient/triage/PatientTriageView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/triage/v1/:id",
    name: "PatientTriageV1View",
    component: () => import("@/views/pages/patient/triage/PatientTriageV1View.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/resume/:id",
    name: "PatientResumeView",
    component: () => import("@/views/pages/patient/resume/ResumeView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/resume/print/:id/:resume_id",
    name: "PatientResumeprintView",
    component: () => import("@/views/layouts/patient/resume/print/PrintResume.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/MCU/:id",
    name: "PatientMCUView",
    component: () => import("@/views/pages/patient/MCU/MCUView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/nutrition/:id",
    name: "PatientNutritionView",
    component: () => import("@/views/pages/patient/nutrition/PatientNutritionView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/room/:id",
    name: "PatientRoomView",
    component: () => import("@/views/pages/patient/room/PatientRoomView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/alkes/:id",
    name: "PatientAlkesView",
    component: () => import("@/views/pages/patient/alkes/PatientAlkesView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/prescription/:id",
    name: "PatientPrescriptionView",
    component: () => import("@/views/pages/patient/prescription/PatientPrescriptionView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/jkn/:reg_des_id/:soap_id",
    name: "PatientJknView",
    component: () => import("@/views/pages/patient/jkn/JKNView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/inpatient/:id",
    name: "PatientInpatientView",
    component: () => import("@/views/pages/patient/inpatient/InpatientView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/inpatient/ttv-monitoring/:id",
    name: "PatientInpatientTTVView",
    component: () => import("@/views/pages/patient/inpatient/TTV/TtvMonitoringView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/inpatient/heart-monitoring/:id",
    name: "PatientInpatientHeartView",
    component: () => import("@/views/pages/patient/inpatient/fetal_heart/FetalHeartView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/inpatient/sliding-scale/:id",
    name: "PatientSlidingScaleView",
    component: () => import("@/views/pages/patient/inpatient/sliding_scale/SlidingScaleView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/inpatient/blood-transfusion/:id",
    name: "PatientBloodTransfusionView",
    component: () => import("@/views/pages/patient/inpatient/blood_transfusion/BloodTransfusionView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/inpatient/ews/:id",
    name: "PatientEWSView",
    component: () => import("@/views/pages/patient/inpatient/EWS/EWSView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/inpatient/temperature-chart/:id",
    name: "PatientTemperatureChartView",
    component: () => import("@/views/pages/patient/inpatient/temperature_chart/TemperatureChartView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/inpatient/fluid-balance/:id",
    name: "PatientFluidBalanceView",
    component: () => import("@/views/pages/patient/inpatient/fluid_balance/FluidBalanceView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/inpatient/pain-observation/:id",
    name: "PatientPainObservationView",
    component: () => import("@/views/pages/patient/inpatient/pain_observation/PainObservationView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/inpatient/partograph/:id",
    name: "PatientPartographView",
    component: () => import("@/views/pages/patient/inpatient/partograph/PartographView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: '/patient/inpatient/partograph/print/:id',
    name: 'PrintPartograph',
    component: () => import('@/views/layouts/patient/inpatient/partograph/print/PrintPartograph.vue'),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/medical-rehabilitation/:id",
    name: "PatientMedicalRehabilitationView",
    component: () => import("@/views/pages/patient/medical_rehabilitation/MedicalRehabilitationView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/patient/hemodialysis/:id",
    name: "PatientHemodialysisView",
    component: () => import("@/views/pages/patient/hemodialysis/HemodialysisView.vue"),
    meta: {
      layout: "full",
    },
  },
]
