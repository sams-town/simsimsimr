export default [
    {
        path: '/master/room',
        name: 'MasterRoomView',
        component: () => import('@/views/pages/master/room/RoomMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/patient',
        name: 'MasterPatientView',
        component: () => import('@/views/pages/master/patient/PatientMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/patient/visit',
        name: 'MasterPatientVisitView',
        component: () => import('@/views/pages/master/patient/patient_visit/PatientVisitView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        // path: '/master/patient/detail/:norm',
        path: '/master/patient/detail/',
        name: 'MasterPatientDetailView',
        component: () => import('@/views/pages/master/patient/PatientMasterDetailView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/patient/medical-record',
        name: 'PatientMasterMedicalRecordsView',
        component: () => import('@/views/pages/master/patient/PatientMasterMedicalRecordsView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/icd10',
        name: 'MasterICD10View',
        component: () => import('@/views/pages/master/icd10/Icd10MasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/icd9cm',
        name: 'MasterICD9cmView',
        component: () => import('@/views/pages/master/icd9cm/Icd9cmMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/costing',
        name: 'MasterCostingView',
        component: () => import('@/views/pages/master/costing/CostingMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/position',
        name: 'MasterPositionView',
        component: () => import('@/views/pages/master/position/PositionMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/employee',
        name: 'MasterEmployeeView',
        component: () => import('@/views/pages/master/employee/EmployeeMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/department',
        name: 'MasterDepartmentView',
        component: () => import('@/views/pages/master/department/DepartmentMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/department/position/:id',
        name: 'MasterDepartmentPositionView',
        component: () => import('@/views/pages/master/department/DepartmentMasterPositionView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/department/employee/:idposition/:iddepartment',
        name: 'MasterDepartmentEmployeeView',
        component: () => import('@/views/pages/master/department/DepartmentMasterEmployeeView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/catalog',
        name: 'MasterCatalogView',
        component: () => import('@/views/pages/master/catalog/CatalogMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/depo',
        name: 'MasterDepoView',
        component: () => import('@/views/pages/master/depo/DepoMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/helper',
        name: 'MasterHelperView',
        component: () => import('@/views/pages/master/helper/HelperMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/health-facility',
        name: 'MasterHealthFacilityView',
        component: () => import('@/views/pages/master/health_facility/HealthFacilityMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/health-facility-item',
        name: 'MasterHealthFacilityItemView',
        component: () => import('@/views/pages/master/health_facility_item/HealthFacilityItemMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/company',
        name: 'MasterCompanyView',
        component: () => import('@/views/pages/master/company/CompanyMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/insurance',
        name: 'MasterInsuranceView',
        component: () => import('@/views/pages/master/company/InsuranceMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/faskes',
        name: 'MasterFaskesView',
        component: () => import('@/views/pages/master/company/FaskesMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/exit',
        name: 'MasterExitView',
        component: () => import('@/views/pages/master/exit/ExitMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/general',
        name: 'MasterGeneralView',
        component: () => import('@/views/pages/master/general/GeneralMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/payment_method',
        name: 'MasterPaymentMethodView',
        component: () => import('@/views/pages/master/payment_method/PaymentMethodMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/timestamp',
        name: 'MasterTimestampView',
        component: () => import('@/views/pages/master/timestamp/TimestampMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/service-class',
        name: 'MasterServiceClassView',
        component: () => import('@/views/pages/master/service_class/ServiceClassMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/how-to-enter',
        name: 'MasterHowToEnterView',
        component: () => import('@/views/pages/master/how_to_enter/HowToEnterMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/brand',
        name: 'MasterBrandView',
        component: () => import('@/views/pages/master/brand/BrandMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/package',
        name: 'MasterPackageView',
        component: () => import('@/views/pages/master/package/PackageMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/letter',
        name: 'MasterLetterView',
        component: () => import('@/views/pages/master/letter/MasterLetterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/ads',
        name: 'MasterAdsView',
        component: () => import('@/views/pages/master/ads/MasterAdsView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/medical-bundle',
        name: 'MasterBundleView',
        component: () => import('@/views/pages/master/medical_bundle/MedicalBundleMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/hemodialysis',
        name: 'MasterHemodialysisView',
        component: () => import('@/views/pages/master/hemodialysis/HemodialysisMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/diet',
        name: 'MasterDietView',
        component: () => import('@/views/pages/master/diet/DietMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/signatura',
        name: 'MasterSignaturaView',
        component: () => import('@/views/pages/master/signatura/SignaturaMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/master/catalog-instruction',
        name: 'MasterCatalogInstructionView',
        component: () => import('@/views/pages/master/catalog_instruction/CatalogInstructionMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },
]
