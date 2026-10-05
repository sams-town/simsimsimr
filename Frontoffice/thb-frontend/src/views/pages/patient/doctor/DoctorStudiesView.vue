<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: this.regData.health_facility_item_name },
                { text: this.regData.name }
            ]"
        >
            <section class="">
                <!-- <patientMenu selectedMenu="menu6" /> -->
                <PatientBanner />
                <section class="d-flex w-100 justify-content-start flex-wrap">
                    <template v-if="regData?.health_facility_id == hfi_emergency">
                        <b-button @click="triggerEmergencyModal('main_gate')" variant="info-600" size="sm" class="mr-2 mt-8">
                            <feather-icon icon="PlusCircleIcon" class="mr-1" />
                            <span class="fs-12">ASESMEN IGD</span>
                        </b-button>
                    </template>
                    <EmergencyStudies ref="childEmeRef" @refreshAssessment="soapRegistration"/>
                    <template v-if="regData?.health_facility_id == hfi_inpatient && isReady()">
                        <b-button @click="triggerInpatientModal('main_gate')" variant="info-600" size="sm" class="mr-2 mt-8">
                            <feather-icon icon="PlusCircleIcon" class="mr-1" />
                            <span class="fs-12">ASESMEN AWAL RANAP</span>
                        </b-button>
                    </template>
                    <InpatientStudies ref="childInRef" @refreshAssessment="soapRegistration" @prescChildTemplate="fillPrescTemplate" @anamnChildTemplate="fillAnamnTemplate"/>

                    <template v-if="regData?.health_facility_id == hfi_outpatient && isReady()">
                        <b-button @click="triggerOutpatientModal('main_gate')" variant="info-600" size="sm" class="mr-2 mt-8">
                            <feather-icon icon="PlusCircleIcon" class="mr-1" />
                            <span class="fs-12">ASESMEN DOKTER</span>
                        </b-button>
                        <b-button @click="triggerSupportBookingModal({
                            regData: regData,
                            lab_list: supportPackage.lab_package_list,
                            rad_list: supportPackage.rad_package_list,
                            phy_list: itemsPhysiotherapy,
                            view: 'ADD',
                            data: null
                        })"
                        variant="orange-500"
                        size="sm"
                        class="mr-2 mt-8">
                            <feather-icon icon="ClockIcon" class="mr-1" />
                            <span class="fs-12">BOOKING PENUNJANG</span>
                        </b-button>
                    </template>
                    <OutpatientStudies ref="childOutRef" @refreshAssessment="soapRegistration" @prescChildTemplate="fillPrescTemplate" @anamnChildTemplate="fillAnamnTemplate" @childTrigger="handleCheckoutTrigger"/>
                    <SupportBookingView ref="supportBooking" @refreshAssessment="soapRegistration"/>

                    <template v-if="regData.health_facility_id == hfi_emergency">
                        <router-link :to="{ path: '/patient/triage/'+ reg_des_id}">
                            <b-button size="sm" variant="orange-500" class="mr-2 mt-8">
                                <feather-icon icon="PlusCircleIcon" class="mr-1" />
                                <span class="fs-12">TRIASE</span>
                            </b-button>
                        </router-link>
                    </template>

                    <b-button @click="triggerSupportHistoryModal({
                        alias_name: regData.alias_name,
                        name: regData.name,
                        norm: regData.norm,
                        birth_date: regData.birth_date,
                    })"
                    variant="orange-500"
                    size="sm"
                    class="mr-2 mt-8">
                        <feather-icon icon="ClockIcon" class="mr-2" />
                        <span class="fs-12">RIWAYAT PENUNJANG</span>
                    </b-button>
                    <SupportHistoryView ref="supportHistory" />

                    <template v-if="regData?.health_facility_id == hfi_emergency || regData?.health_facility_id == hfi_outpatient">
                        <b-button @click="openModalSPRI()" variant="primary" size="sm" class="mr-2 mt-8">
                            <feather-icon :icon="form_spri.id? 'RefreshCwIcon' : 'PlusCircleIcon'" class="mr-1" />
                            <span class="fs-12">{{ form_spri.id ? 'LIHAT SPRI' : 'SPRI'}}</span>
                        </b-button>
                    </template>

                    <!-- jangan dihapus, ini checkout-dokter yang is_checkoutnya tetep 0 buat nyelesein pelayanan -->
                    <!-- <b-button @click="active.modal_checkout = !active.modal_checkout" variant="orange-500" size="sm" class="mr-1">
                        <feather-icon icon="LogOutIcon" />
                        <span class="fs-12">CHECKOUT</span>
                    </b-button> -->

                    <template v-if="regData?.health_facility_id == hfi_emergency">
                        <b-button @click="active.modal_v2checkout = !active.modal_v2checkout" variant="orange-500" size="sm" class="mr-2 mt-8">
                            <feather-icon icon="LogOutIcon" />
                            <span class="fs-12">CHECKOUT</span>
                        </b-button>
                    </template>

                    <template v-if="regData.health_facility_id == hfi_inpatient || regData.health_facility_id == hfi_outpatient">
                        <b-button @click="triggerProcedure({ regData: regData, procedureData: null, optDoctor: doctorList, optTyCosting: TypeCostingOptions, gate: 'add_gate'})" variant="ax-primary-400" size="sm" class="mr-2 mt-8">
                            <feather-icon icon="PlusCircleIcon" class="mr-1" />
                            <span class="fs-12">PROSEDUR</span>
                        </b-button>
                        <OrderProcedureView ref="procedureRef" @childProcedureTrigger="getProcedureList"/>
                    </template>

                    <b-button @click="triggerAdministrationModal({
                        alias_name: regData.alias_name,
                        name: regData.name,
                        norm: regData.norm,
                        birth_date: regData.birth_date,
                    })"
                    variant="w3green"
                    size="sm"
                    class="mr-2 mt-8">
                        <feather-icon icon="FilePlusIcon" class="mr-1" />
                        <span class="fs-12">ADMINISTRASI SURAT</span>
                    </b-button>
                    <administration ref="administrationRef" />

                    <template v-if="this.regData.registration_insurance">
                        <template v-for="list in this.regData.registration_insurance">
                            <b-button
                                v-if="list.is_primary === 1 && list.company_id === '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8'"
                                @click="openIcare(list.number_insurance)"
                                size="sm" variant="w3green"
                                class="mr-2 mt-8"
                            >
                                I-CARE
                            </b-button>
                        </template>
                    </template>

                    <template v-if="regData?.health_facility_id == hfi_inpatient">
                        <b-button @click="triggerResumeModal(regData)"
                        variant="w3green"
                        size="sm"
                        class="mr-2 mt-8">
                            <feather-icon icon="FilePlusIcon" class="mr-1" />
                            <span class="fs-12">RESUME</span>
                        </b-button>
                        <AddResumeView ref="childResumeRef" />

                        <b-button @click="triggerConsultModal()"
                        variant="ax-primary-400"
                        size="sm"
                        class="mr-2 mt-8">
                            <feather-icon icon="FilePlusIcon" class="mr-1" />
                            <span class="fs-12">KONSUL/RABER</span>
                        </b-button>

                        <ConsultationView ref="childConsultRef" @refreshRegistration="getRegDetail" @openCPPT="triggerCpptModal" />
                    </template>

                    <b-button @click="triggerOperatieModal({gate: 'main_gate', rowData: null})" variant="ax-primary-400" size="sm" class="mr-2 mt-8">
                        <feather-icon icon="FilePlusIcon" class="mr-1" />
                        <span class="fs-12">ORDER OK</span>
                    </b-button>
                    <OrderOk ref="childOperatieRef" @refreshOk="getOperatingList" />

                    <b-button @click="triggerTool({ regData: regData, toolData: null, gate: 'add_gate'})" variant="ax-primary-400" size="sm" class="mr-2 mt-8">
                        <feather-icon icon="PlusCircleIcon" class="mr-1" />
                        <span class="fs-12">PEMAKAIAN ALAT</span>
                    </b-button>
                    <OrderToolView ref="toolRef" @childToolTrigger="getToolList"/>
                </section>

                <section class="mt-16">
                    <ModernLabel label="PERIODE PERAWATAN SAAT INI"/>
                    <template v-for="(result,i) in soapList.byRegistration" >
                        <section v-if="result.health_facility_id == hfi_outpatient && result.is_booking == 1" class="card mt-16 shadow-sm" :style="isSimilar(result.registration_destination_id)">
                            <div class="card-header p-12 bg-white">
                                <ModernLabel label="BOOKING PENUNJANG" :small="`Ditambahkan pada ${localFullDay(result.date_added)} Oleh ${result.doctor_in_charge_name}`" />
                                <section class="mt-8">
                                    <b-button v-b-toggle="'accordion-ppsi'+result.id" size="sm" variant="primary" class="mr-1">
                                        <feather-icon icon="EyeIcon" size="12" class="mr-1" />
                                        <span class="fs-12">ASESMEN</span>
                                    </b-button>
                                    <b-dropdown size="sm" id="dropdown-aria" text="CETAK & EDIT" variant="success-500">
                                        <template v-if="regData?.health_facility_id == hfi_outpatient">
                                            <b-dropdown-item-button @click="triggerSupportBookingModal({
                                                    regData: regData,
                                                    lab_list: supportPackage.lab_package_list,
                                                    rad_list: supportPackage.rad_package_list,
                                                    phy_list: itemsPhysiotherapy,
                                                    view: 'EDIT',
                                                    data: result
                                                })">Edit Booking
                                            </b-dropdown-item-button>
                                            <b-dropdown-divider></b-dropdown-divider>
                                        </template>
                                        <template v-if="result.health_facility_id == hfi_outpatient && result.is_booking == 1 && result.laboratory_request">
                                            <b-dropdown-header id="dropdown-header-1">LABORATORIUM</b-dropdown-header>
                                            <router-link :to="{ path: '/prints/booking-laboratory-print/'+ result.id}" target="_blank">
                                                <b-dropdown-item-button aria-describedby="dropdown-header-1">Cetak</b-dropdown-item-button>
                                            </router-link>
                                            <b-dropdown-item-button @click="triggerCancelSupportBooking({ request: result.laboratory_request, norm: regData.norm, gate: 'laboratory_gate'})" :disabled="result.laboratory_request.status == 2 || result.laboratory_request.status == 3" aria-describedby="dropdown-header-1">Batalkan Order</b-dropdown-item-button>
                                        </template>
                                        <template v-if="result.health_facility_id == hfi_outpatient && result.is_booking == 1 && result.radiology_request">
                                            <b-dropdown-header id="dropdown-header-2">RADIOLOGI</b-dropdown-header>
                                            <router-link :to="{ path: '/prints/booking-radiology-print/'+ result.id}" target="_blank">
                                                <b-dropdown-item-button aria-describedby="dropdown-header-2">Cetak</b-dropdown-item-button>
                                            </router-link>
                                            <b-dropdown-item-button @click="triggerCancelSupportBooking({ request: result.radiology_request, norm: regData.norm, gate: 'radiology_gate'})" :disabled="result.radiology_request.status == 2 || result.radiology_request.status == 3" aria-describedby="dropdown-header-2">Batalkan Order</b-dropdown-item-button>
                                        </template>
                                        <template v-if="result.health_facility_id == hfi_outpatient && result.is_booking == 1 && result.physiotherapy_request">
                                            <b-dropdown-header id="dropdown-header-2">FISIOTERAPI</b-dropdown-header>
                                            <router-link :to="{ path: '/prints/doctor-rehabilitation-schedule-print/'+ result.id}" target="_blank">
                                                <b-dropdown-item-button>Cetak Jadwal</b-dropdown-item-button>
                                            </router-link>
                                        </template>
                                    </b-dropdown>
                                </section>
                            </div>
                            <b-collapse accordion="my-accordion" :id="`accordion-ppsi`+result.id">
                                <div class="card-body px-0 py-8">
                                    <div class="row mx-0">
                                        <section class="col-lg-3 px-8 fs-12">
                                            <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">LABORATORIUM</div>
                                            <section class="mt-8">
                                                <template v-if="result?.laboratory_request?.length > 0">
                                                    <template v-for="(il, i) in result.laboratory_request">
                                                        <div class="mb-12">
                                                            <!-- <div class="font-weight-bold">{{il.no_request}} [{{ il.queue }}]</div> -->
                                                            <div class="font-weight-bold">{{localDateTime(il.date_request)}}</div>
                                                            <li v-for="(list, j) in il.laboratory_request_items">
                                                                <template v-if="list.result?.length > 0">
                                                                    <feather-icon icon="EyeIcon" @click="triggerLabResult({ alias_name: regData.alias_name, name: regData.name, result: list.result, url: list.url,costing_name: list.costing_name, laboratory_request_id: list.laboratory_request_id })" color="blue" v-b-tooltip.hover.left="`Lihat hasil ${list.costing_name}`"/>
                                                                </template>
                                                                <template v-else>
                                                                    <img src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`${list.costing_name} telah di order, belum terbit hasil`">
                                                                </template>
                                                                {{ list.costing_name }}
                                                            </li>
                                                        </div>
                                                    </template>
                                                </template>
                                                <div v-else>-</div>
                                            </section>
                                        </section>
                                        <section class="col-lg-3 px-8 fs-12">
                                            <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">RADIOLOGI</div>
                                            <section class="mt-8">
                                                <template v-if="result?.radiology_request?.length > 0">
                                                    <template v-for="(ir, i) in result.radiology_request">
                                                        <div class="mb-12">
                                                            <!-- <div class="font-weight-bold">{{ir.no_request}} [{{ ir.queue }}]</div> -->
                                                            <div class="font-weight-bold">{{localDateTime(ir.date_request)}}</div>
                                                            <li v-for="(list, j) in ir.radiology_request_items">
                                                                <template v-if="list?.url?.length > 0">
                                                                    <feather-icon icon="EyeIcon" @click="triggerRadResult({ alias_name: regData.alias_name, name: regData.name, url: list.url, costing_name: list.costing_name })" color="blue" v-b-tooltip.hover.left="`Lihat hasil ${list.costing_name}`"/>
                                                                </template>
                                                                <template v-else>
                                                                    <img src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`${list.costing_name} telah di order, belum terbit hasil`">
                                                                </template>
                                                                {{ list.costing_name }}
                                                            </li>
                                                            <div v-if="ir.clinical_indications" class="mt-2">
                                                                <b>Catatan Klinis:</b>
                                                                <div class="bg-neutral-100 p-2 rounded">{{ ir.clinical_indications }}</div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </template>
                                                <div v-else class="font-weight-bold">-</div>
                                            </section>
                                        </section>
                                        <section class="col-lg-3 px-8 fs-12">
                                            <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">FISIOTERAPI</div>
                                            <section class="mt-8">
                                                <div v-if="result?.physiotherapy_request?.length > 0">
                                                    <template v-for="(phy, i) in result.physiotherapy_request">
                                                        <div class="mb-12">
                                                            <div class="font-weight-bold">{{localDateTime(phy.date_request)}}</div>
                                                            <li v-for="(list, j) in phy.physiotherapy_request_items">{{ list.costing_name }} </li>
                                                        </div>
                                                    </template>
                                                </div>
                                                <div v-else>-</div>
                                            </section>
                                        </section>
                                    </div>
                                </div>
                            </b-collapse>
                        </section>

                        <section v-else-if="!(result.health_facility_id == hfi_inpatient && result.is_initial_inpatient == 0)" class="card mt-16 shadow-sm" :style="isSimilar(result.registration_destination_id)">
                            <div class="card-header p-12 bg-white">
                                <ModernLabel :label="getSoapLabel(result.health_facility_id, result.health_facility_item_name, result.is_initial_inpatient, result.is_booking)" :small="`Ditambahkan pada ${localFullDay(result.date_added)} Oleh ${result.doctor_in_charge_name}`" />
                                <section class="mt-8">
                                    <template v-if="userData.user_group.id == 'a4c04d8d-12d8-4c54-afca-69de6bd5f62d' || userData.employee_id == 'd77142c4-4777-47d8-bd0c-fd7dfd134af4'">
                                        <router-link :to="{ path: '/patient/jkn/'+ result.registration_destination_id + '/' + result.id}" target="_blank">
                                            <b-button size="sm" variant="orange-500" class="text-white mr-1">
                                                <feather-icon icon="ZapIcon" size="12" class="mr-1" />
                                                <span class="fs-12">JKN ACCESS</span>
                                            </b-button>
                                        </router-link>
                                    </template>

                                    <b-button v-b-toggle="'accordion-ppsi'+result.id" size="sm" variant="primary" class="mr-1">
                                        <feather-icon icon="EyeIcon" size="12" class="mr-1" />
                                        <span class="fs-12">{{ getAssesToggleName(result.health_facility_id) }}</span>
                                    </b-button>

                                    <template v-if="result.health_facility_id == hfi_inpatient">
                                        <b-button v-b-toggle="'accordion-cppt-'+result.id" size="sm" variant="w3green" class="mr-1">
                                            <feather-icon icon="EyeIcon" size="12" class="mr-1" />
                                            <span class="fs-12">CPPT</span>
                                        </b-button>

                                        <b-button v-b-toggle="'accordion-sbar-'+result.id" size="sm" class="mr-1 btn-sbar">
                                            <feather-icon icon="EyeIcon" size="12" class="mr-1" />
                                            <span class="fs-12">SBAR</span>
                                        </b-button>

                                        <!-- <b-button @click="triggerKardex" size="sm" variant="ax-primary-400" class="mr-1">
                                            <feather-icon icon="BookIcon" size="12" class="mr-1" />
                                            <span class="fs-12">KARDEKS</span>
                                        </b-button> -->
                                    </template>

                                    <template v-if="examinationCondition(result)">
                                        <b-button @click="triggerExamination({ examData: result })" size="sm" variant="w3green" class="mr-1">
                                            <feather-icon icon="UploadIcon" size="12" class="mr-1" />
                                            <span class="fs-12">PENUNJANG LAINNYA</span>
                                        </b-button>
                                    </template>

                                    <router-link :to="{ path: '/prints/doctor-sbpk-print/'+ result.id}" target="_blank">
                                        <b-button size="sm" variant="outline-primary-500" class="px-16 mr-1">
                                            <feather-icon icon="PrinterIcon" size="12" class="mr-1" />
                                            <span class="fs-12">SBPK</span>
                                        </b-button>
                                    </router-link>

                                    <router-link :to="{ path: '/prints/doctor-studies-print/'+ result.id}" target="_blank">
                                        <b-button size="sm" variant="outline-success-500" class="px-16 mr-1">
                                            <feather-icon icon="PrinterIcon" size="12" class="mr-1" />
                                            <span class="fs-12">ASESMEN</span>
                                        </b-button>
                                    </router-link>

                                    <template v-if="result.health_facility_item_id == hfii_mcu">
                                        <router-link :to="{ path: '/prints/mcu-result/'+ result.id}" target="_blank">
                                            <b-button size="sm" variant="outline-success-500" class="px-16 mr-1">
                                                <feather-icon icon="PrinterIcon" size="12" class="mr-1" />
                                                <span class="fs-12">ASESMEN MCU</span>
                                            </b-button>
                                        </router-link>
                                    </template>

                                    <template v-if="result.health_facility_item_id == hfii_orthopedi">
                                        <router-link :to="{ path: '/prints/orthopedi-result/'+ result.id}" target="_blank">
                                            <b-button size="sm" variant="outline-success-500" class="px-16 mr-1">
                                                <feather-icon icon="PrinterIcon" size="12" class="mr-1" />
                                                <span class="fs-12">ASESMEN ORTHOPEDI</span>
                                            </b-button>
                                        </router-link>
                                    </template>

                                    <template v-if="result.health_facility_item_id == hfii_pulmo">
                                        <router-link :to="{ path: '/prints/pulmo-result/'+ result.id}" target="_blank">
                                            <b-button size="sm" variant="outline-success-500" class="px-16 mr-1">
                                                <feather-icon icon="PrinterIcon" size="12" class="mr-1" />
                                                <span class="fs-12">ASESMEN PARU</span>
                                            </b-button>
                                        </router-link>
                                    </template>

                                    <template v-if="result.health_facility_item_id == hfii_eye">
                                        <router-link :to="{ path: '/prints/eye-result/'+ result.id}" target="_blank">
                                            <b-button size="sm" variant="outline-success-500" class="px-16 mr-1">
                                                <feather-icon icon="PrinterIcon" size="12" class="mr-1" />
                                                <span class="fs-12">ASESMEN MATA</span>
                                            </b-button>
                                        </router-link>
                                    </template>

                                    <!-- CONDITIONAL BUTTONs -->

                                    <template v-if="result.health_facility_id == hfi_emergency">
                                        <router-link :to="{ path: '/prints/doctor-emergency-print/'+ result.id}" target="_blank">
                                            <b-button size="sm" variant="outline-success-500" class="px-16 mr-1">
                                                <feather-icon icon="PrinterIcon" size="12" class="mr-1" />
                                                <span class="fs-12">ASESMEN IGD</span>
                                            </b-button>
                                        </router-link>
                                    </template>

                                    <b-dropdown v-if="result.health_facility_item_id == hfii_rehabilitation && result.is_medical_rehabilitation == 1" size="sm" id="dropdown-aria" text="UJI FUNGSI" variant="outline-success-500" class="mr-1 fs-12" menu-class="w-100">
                                        <router-link :to="{ path: '/prints/doctor-rehabilitation-print/'+ result.id}" target="_blank">
                                            <b-dropdown-item-button>Cetak Formulir</b-dropdown-item-button>
                                        </router-link>
                                        <router-link :to="{ path: '/prints/doctor-rehabilitation-result-print/'+ result.id}" target="_blank">
                                            <b-dropdown-item-button>Cetak Hasil</b-dropdown-item-button>
                                        </router-link>
                                        <router-link :to="{ path: '/prints/doctor-therapy-program-print/'+ result.id}" target="_blank">
                                            <b-dropdown-item-button>Cetak Program</b-dropdown-item-button>
                                        </router-link>
                                        <router-link :to="{ path: '/prints/doctor-rehabilitation-schedule-print/'+ result.id}" target="_blank">
                                            <b-dropdown-item-button>Cetak Jadwal</b-dropdown-item-button>
                                        </router-link>
                                        <router-link :to="{ path: '/prints/doctor-certificate-print/'+ result.id}" target="_blank">
                                            <b-dropdown-item-button>Cetak Keterangan</b-dropdown-item-button>
                                        </router-link>
                                        <router-link :to="{ path: '/prints/doctor-rehabilitation-reassessment-print/'+ result.id}" target="_blank">
                                            <b-dropdown-item-button>Cetak Re-asesmen</b-dropdown-item-button>
                                        </router-link>
                                    </b-dropdown>

                                    <template v-if="result.health_facility_item_id == hfii_hemodialysis && result.is_hemodialysis == 1">
                                        <router-link :to="{ path: '/prints/doctor-hemodialysis-print/'+ result.id}" target="_blank">
                                            <b-button size="sm" variant="outline-success-500" class="px-16 mr-1">
                                                <feather-icon icon="PrinterIcon" size="12" class="mr-1" />
                                                <span class="fs-12">HEMODIALISA</span>
                                            </b-button>
                                        </router-link>
                                    </template>

                                    <!-- JANGAN DIHAPUS -->

                                    <!-- <template v-if="getOutpatientCondition(result.doctor_in_charge_id, result.health_facility_id, result.is_resume_exit, result.is_booking)">
                                        <b-button @click="triggerOutpatientModal(result)" pill size="sm" variant="primary-600" class="px-16 mr-1">
                                            <i><img src="@/@core/assets/icons/nurse/file-eye.svg" alt=""></i> <span>Buka Kajian</span>
                                        </b-button>
                                    </template> -->

                                    <!-- <template v-if="getInpatientCondition(result.doctor_in_charge_id, result.health_facility_id, result.is_initial_inpatient)">
                                        <b-button @click="triggerInpatientModal(result)" pill size="sm" variant="primary-600" class="px-16 mr-1">
                                            <i><img src="@/@core/assets/icons/nurse/file-eye.svg" alt=""></i> <span>Buka Kajian</span>
                                        </b-button>
                                    </template> -->
                                </section>
                            </div>

                            <b-collapse accordion="my-accordion" :id="`accordion-ppsi`+result.id">
                                <div class="card-body px-0 py-8">
                                    <div class="row mx-0">
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <section class="col-lg-4 px-8 fs-12">
                                                    <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">SUBJECTIVE</div>
                                                    <section class="mt-8">
                                                        <div class="font-weight-bold text-ax-primary-500">KELUHAN UTAMA:</div>
                                                        <div>{{result?.main_complaint || '-' }}</div>
                                                    </section>
                                                    <section class="mt-12">
                                                        <div class="font-weight-bold text-ax-primary-500">RIWAYAT PENYAKIT SEKARANG:</div>
                                                        <div v-html="parsedBody(result?.history_disease_now || '-')"></div>
                                                    </section>
                                                    <section class="mt-12">
                                                        <div class="font-weight-bold text-ax-primary-500">RIWAYAT PENYAKIT DAHULU:</div>
                                                        <div v-html="parsedBody(result?.history_disease_past || '-')"></div>
                                                    </section>
                                                    <section class="mt-12">
                                                        <div class="font-weight-bold text-ax-primary-500">RIWAYAT OBAT:</div>
                                                        <div v-html="parsedBody(result?.history_drugs || '-')"></div>
                                                    </section>
                                                    <section class="mt-12">
                                                        <div class="font-weight-bold text-ax-primary-500">ALERGI:</div>
                                                        <template v-if="result.allergies?.length > 0">
                                                            <li v-for="list in result.allergies">{{ list.name }}</li>
                                                        </template>
                                                        <div v-else>-</div>
                                                    </section>
                                                </section>

                                                <section class="col-lg-4 px-8 fs-12">
                                                    <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">OBJECTIVE</div>
                                                    <section class="mt-8">
                                                        <div class="font-weight-bold text-ax-primary-500">TANDA VITAL:</div>
                                                        <div>
                                                            Systolic = {{ result?.systolic_blood_pressure || '-' }},
                                                            Diastolic = {{ result?.diastolic_blood_pressure || '-' }},
                                                            Frekuensi Nadi = {{ result?.pulse_frequency || '-' }},
                                                            Frekuensi Nafas = {{ result?.breath_frequency || '-' }},
                                                            Saturasi Oksigen = {{ result?.oximetry_frequency || '-' }},
                                                            Suhu = {{ result?.temperature || '-' }},
                                                            Oksigenasi = {{ getOxygenName(result?.oxygen_type_id || '-') }},
                                                            Kesadaran = {{ getAwareName(result?.aware) }},
                                                            GCS Mata = {{ getGcsEyeName(result?.eye_score || '-') }},
                                                            GCS Verbal = {{ getGcsVerbalName(result?.verbal_score || '-') }},
                                                            GCS Motorik = {{ getGcsMotoricName(result?.motor_score || '-') }},
                                                            Warna Kulit = {{ getColorSkinName(result?.skin_color || '-') }},
                                                            Perilaku = {{ getBehaviorName(result?.behavior || '-') }},
                                                        </div>
                                                    </section>
                                                    <section class="mt-12">
                                                        <div class="font-weight-bold text-ax-primary-500">ANTROPOMETRI:</div>
                                                        <div>
                                                            BB = {{ result?.weight || '-' }},
                                                            TB = {{ result?.height || '-' }},
                                                            IMT = {{ result?.imt || '-' }},
                                                            L.Kepala = {{ result?.head_circumference || '-' }},
                                                            L.Dada = {{ result?.chest_circumference || '-' }},
                                                            L.Lengan Atas = {{ result?.arm_circumference || '-' }},
                                                            L.Perut = {{ result?.stomach_circumference || '-' }},
                                                            L.Lutut = {{ result?.knee_circumference || '-' }}
                                                        </div>
                                                    </section>
                                                </section>

                                                <section class="col-lg-4 px-8 fs-12">
                                                    <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">ASSESSMENT</div>
                                                    <section class="mt-8">
                                                        <div class="font-weight-bold text-ax-primary-500">DIAGNOSIS:</div>
                                                        <template v-if="result?.icd10?.length > 0">
                                                            <li v-for="(list, index) in result.icd10">{{ list.icd10_code }} {{ list.icd10_name }}</li>
                                                        </template>
                                                        <div v-else class="text-italic">-</div>
                                                    </section>
                                                    <section class="mt-12">
                                                        <div class="font-weight-bold text-ax-primary-500">CATATAN ICD10:</div>
                                                        <div v-html="parsedBody(result?.icd10_notes || '-')"></div>
                                                    </section>
                                                    <section class="mt-12">
                                                        <div class="font-weight-bold text-ax-primary-500">NON-ICD10:</div>
                                                        <template v-if="result?.non_icd10?.length > 0">
                                                            <li v-for="(list, index) in result.non_icd10">{{ list }}</li>
                                                        </template>
                                                        <div v-else class="text-italic">-</div>
                                                    </section>
                                                </section>

                                                <section class="col-12 px-8 fs-12 mt-20">
                                                    <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">STATUS GENERALIS</div>
                                                    <div class="mt-8">
                                                        <i class="text-danger-500">*teks merah untuk menandai pemeriksaan fisik bermakna</i>
                                                        <div class="row fs-11 mt-1">
                                                            <div class="col-lg-4">
                                                                <table class="w-100">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">KEPALA</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.head)">
                                                                            <td style="width: 100px;">Kepala</td>
                                                                            <td style="width: 15px;" class="px-1">:</td>
                                                                            <td>{{ getHead(result.head || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.head_hair_color)">
                                                                            <td class="align-top">Warna Rambut</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getHairColor(result.head_hair_color || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.head_hair_condition)">
                                                                            <td class="align-top">Kondisi Rambut</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getHairCondition(result.head_hair_condition || 1) }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">MATA</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.eye_conjunctive)">
                                                                            <td class="align-top" >Konjungtiva</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getEyeConjunctive(result.eye_conjunctive || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.eye_sclera)">
                                                                            <td class="align-top">Sklera</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getEyeSclera(result.eye_sclera || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.eye_pupil)">
                                                                            <td class="align-top">Pupil</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getEyePupil(result.eye_pupil || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.eye_light_reflex)">
                                                                            <td class="align-top">Reflek Cahaya</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getEyeReflect(result.eye_light_reflex || 1) }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">MULUT</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.mouth_mucosa)">
                                                                            <td class="align-top">Mukosa</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getMouthMucosa(result.mouth_mucosa || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.mouth_discoloration)">
                                                                            <td class="align-top">Perubahan Warna</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getMouthDiscoloration(result.mouth_discoloration || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.mouth_tongue)">
                                                                            <td class="align-top">Lidah</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getMouthTongue(result.mouth_tongue || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.mouth_caries)">
                                                                            <td class="align-top">Karies</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getMouthCaries(result.mouth_caries || 1) }}</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <table class="w-100">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">LEHER</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.neck_kgb)">
                                                                            <td class="align-top" style="width: 100px;">KGB</td>
                                                                            <td style="width: 15px;" class="align-top px-1">:</td>
                                                                            <td>{{ getNeckKGB(result.neck_kgb || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.neck_trachea_position)">
                                                                            <td class="align-top">Posisi Trakea</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getNeckTracheaPosition(result.neck_trachea_position || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.neck_massa)">
                                                                            <td class="align-top">Massa</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getNeckMassa(result.neck_massa || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result?.neck_jvp)">
                                                                            <td class="align-top">JVP</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getNeckJVP(result?.neck_jvp || 1) }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">EKSTREMITAS</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.extremity_acral)">
                                                                            <td class="align-top">Akral</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getEkstremitasAkral(result.extremity_acral || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.extremity_crt)">
                                                                            <td class="align-top">CRT</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getEkstremitasCRT(result.extremity_crt || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.extremity_edema)">
                                                                            <td class="align-top">Edema</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getEkstremitasEdema(result.extremity_edema || 1) }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">ABDOMEN</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.abdomen_supel)">
                                                                            <td class="align-top">Supel</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getAbdomenSupel(result.abdomen_supel || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.abdomen_bowel_sounds)">
                                                                            <td class="align-top">Bising Usus</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getAbdomenBising(result.abdomen_bowel_sounds || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.abdomen_tenderness)">
                                                                            <td class="align-top">Nyeri Tekan</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getAbdomenPress(result.abdomen_tenderness || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.abdomen_hepar_lien)">
                                                                            <td class="align-top">Hepar & Lien</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getAbdomenHepar(result.abdomen_hepar_lien || 1) }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="align-top">Pemeriksaan Abdomen Lainnya</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td class="align-top">
                                                                                <div v-html="parsedBody(result?.abdomen_other_physical_check || '-')"></div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <table class="w-100">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">THORAKS</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="3" class="font-weight-bold">INSPEKSI</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.thorax_insp_chest)">
                                                                            <td class="align-top" style="width: 120px;">Dada</td>
                                                                            <td class="align-top px-1" style="width: 15px;">:</td>
                                                                            <td>{{ getInspectionChest(result.thorax_insp_chest || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.thorax_insp_retraction)">
                                                                            <td class="align-top">Retraksi</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getInspectionRetractiont(result.thorax_insp_retraction || 1) }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="3" class="font-weight-bold">PALPASI</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.thorax_insp_chest || 1)">
                                                                            <td class="align-top">Dada</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getPalpationChest(result.thorax_insp_chest || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.thorax_palp_fremitus)">
                                                                            <td class="align-top">Fremitus</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getPalpationFremitus(result.thorax_palp_fremitus || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.thorax_palp_crepitation)">
                                                                            <td class="align-top">Krepitasi</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getPalpationKrepitasi(result.thorax_palp_crepitation || 1) }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="3" class="font-weight-bold">AUSKULTASI</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.thorax_ausk_pul_vasiculer)">
                                                                            <td class="align-top">Pulmo Vasikuler</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getAuskultationPulmoVasikuler(result.thorax_ausk_pul_vasiculer || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.thorax_ausk_pul_rhonki)">
                                                                            <td class="align-top">Pulmo Rhonki</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getAuskultationPulmoRhonki(result.thorax_ausk_pul_rhonki || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.thorax_ausk_pul_wheezing)">
                                                                            <td class="align-top">Pulmo Wheezing</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getAuskultationPulmoWheezing(result.thorax_ausk_pul_wheezing || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.thorax_ausk_cor_heart_sound)">
                                                                            <td class="align-top">COR Bunyi Jantung</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getAuskultationCORBJ(result.thorax_ausk_cor_heart_sound || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.thorax_ausk_cor_murmur)">
                                                                            <td class="align-top">COR Murmur</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getAuskultationCORMurmur(result.thorax_ausk_cor_murmur || 1) }}</td>
                                                                        </tr>
                                                                        <tr :class="getMeaningVariant(result.thorax_ausk_cor_gallop)">
                                                                            <td class="align-top">COR Gallop</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getAuskultationCORGallop(result.thorax_ausk_cor_gallop || 1) }}</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <table class="w-100 mt-1">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="align-top" style="width: 100px;">Genitalia</td>
                                                                    <td class="align-top px-1" style="width: 15px;">:</td>
                                                                    <td>
                                                                        <div v-html="parsedBody(result?.genitalia || '-')"></div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">Status Lokalis</td>
                                                                    <td class="align-top px-1">:</td>
                                                                    <td>
                                                                        <div v-html="parsedBody(result?.localist_status || '-')"></div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">Status Neurologis</td>
                                                                    <td class="align-top px-1">:</td>
                                                                    <td>
                                                                        <div v-html="parsedBody(result?.neurologist_status || '-')"></div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">Status Oftalmologis</td>
                                                                    <td class="align-top px-1">:</td>
                                                                    <td>
                                                                        <div v-html="parsedBody(result?.ophthal_status || '-')"></div>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </section>

                                                <template v-if="result.health_facility_item_id == hfii_hemodialysis">
                                                    <div class="col-12 px-8 fs-12 mt-20">
                                                        <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">HEMODIALISA</div>
                                                        <table class="w-100 mt-8">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="align-top" style="width: 120px;">Diagnosis Utama</td>
                                                                    <td class="align-top px-1 text-center" style="width: 30px;">:</td>
                                                                    <td class="align-top">{{ result?.main_diagnosis || '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">Etiologi</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td class="align-top">{{ result?.etiology || '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">Penyakit Penyerta</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td class="align-top">{{ result?.comorbidities || '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">Frekuensi HD</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td class="align-top">{{ result?.frequency_hd || '-' }} x/minggu</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">Akses Vaskular</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td class="align-top">
                                                                        <div v-html="parsedBody(result?.vascular_access || '-')"></div>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </template>

                                            <template v-if="result.health_facility_item_id == hfii_rehabilitation">
                                                <div class="col-12 px-8 fs-12 mt-20">
                                                    <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">UJI FUNGSI / PROSEDUR KFR</div>
                                                    <div class="row fs-12">
                                                        <div class="col-lg-4">
                                                            <section class="mt-8">
                                                                <div class="font-weight-bold text-ax-primary-500">SUSPEK PENYAKIT AKIBAT:</div>
                                                                <div>{{ result?.suspected_disease_type_id == '1' ?  `Ya, ${result.suspected_disease}` : 'Tidak' }}</div>
                                                            </section>
                                                            <section class="mt-12">
                                                                <div class="font-weight-bold text-ax-primary-500">PEMERIKSAAN & UJI FUNGSI:</div>
                                                                <div v-html="parsedBody(result?.functional_testing || '-')"></div>
                                                            </section>
                                                            <section class="mt-12">
                                                                <div class="font-weight-bold text-ax-primary-500">DIAGNOSA FUNGSIONAL:</div>
                                                                <div v-html="parsedBody(result?.functional_diagnosis || '-')"></div>
                                                            </section>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <section class="mt-8">
                                                                <div class="font-weight-bold text-ax-primary-500">DIAGNOSA MEDIS:</div>
                                                                <div v-html="parsedBody(result?.medical_diagnosis || '-')"></div>
                                                            </section>
                                                            <section class="mt-12">
                                                                <div class="font-weight-bold text-ax-primary-500">HASIL YANG DIDAPAT:</div>
                                                                <div v-html="parsedBody(result?.result || '-')"></div>
                                                            </section>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <section class="mt-8">
                                                                <div class="font-weight-bold text-ax-primary-500">KESIMPULAN:</div>
                                                                <div v-html="parsedBody(result?.conclusion || '-')"></div>
                                                            </section>
                                                            <section class="mt-12">
                                                                <div class="font-weight-bold text-ax-primary-500">REKOMENDASI:</div>
                                                                <div v-html="parsedBody(result?.recommendation || '-')"></div>
                                                            </section>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>

                                                <template v-if="result.health_facility_item_id == hfii_mcu">
                                                    <div class="col-12 px-8 fs-12 mt-20">
                                                        <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">MEDICAL CHECK UP</div>
                                                        <table class="w-100 mt-8">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="align-top" style="width: 150px;">Kesimpulan</td>
                                                                    <td class="align-top px-1 text-center" style="width: 30px;">:</td>
                                                                    <td class="align-top">
                                                                        <div v-html="parsedBody(result?.fit_to_work || '-')"></div>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <section class="col-lg-3 px-8 fs-12">
                                            <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">PLANNING</div>
                                            <section class="mt-8">
                                                <div class="font-weight-bold text-ax-primary-500">PROSEDUR:</div>
                                                <template v-if="result?.transaction_doctor?.length > 0">
                                                    <li v-for="list in result.transaction_doctor">{{ list.costing_name }}</li>
                                                </template>
                                                <div v-else>-</div>
                                            </section>
                                            <section class="mt-12">
                                                <div class="font-weight-bold text-ax-primary-500">ICD9CM:</div>
                                                <div>
                                                    <div v-if="result?.icd9cm?.length > 0">
                                                        <li v-for="list in result.icd9cm">{{ list.icd9cm_code }} {{ list.icd9cm_name }}</li>
                                                    </div>
                                                    <div v-else>-</div>
                                                </div>
                                                <div v-html="parsedBody(result?.therapy_plan || '-')" class="mt-1"></div>
                                            </section>
                                            <section class="mt-12">
                                                <div class="font-weight-bold text-ax-primary-500">LABORATORIUM:</div>
                                                <template v-if="result?.laboratory_request?.length > 0">
                                                    <template v-for="(il, i) in result.laboratory_request">
                                                        <div class="mb-12">
                                                            <div class="font-weight-bold">{{localDateTime(il.date_request)}}</div>
                                                            <li v-for="(list, j) in il.laboratory_request_items">
                                                                <template v-if="list.result?.length > 0">
                                                                    <feather-icon icon="EyeIcon" @click="triggerLabResult({ alias_name: regData.alias_name, name: regData.name, result: list.result, url: list.url,costing_name: list.costing_name, laboratory_request_id: list.laboratory_request_id })" color="blue" v-b-tooltip.hover.left="`Lihat hasil ${list.costing_name}`"/>
                                                                </template>
                                                                <template v-else>
                                                                    <img src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`${list.costing_name} telah di order, belum terbit hasil`">
                                                                </template>
                                                                {{ list.costing_name }}
                                                            </li>
                                                        </div>
                                                    </template>
                                                </template>
                                                <div v-else>-</div>
                                            </section>
                                            <section class="mt-12">
                                                <div class="font-weight-bold text-ax-primary-500">RADIOLOGI:</div>
                                                <template v-if="result?.radiology_request?.length > 0">
                                                    <template v-for="(ir, i) in result.radiology_request">
                                                        <div class="mb-12">
                                                            <div class="font-weight-bold">{{localDateTime(ir.date_request)}}</div>
                                                            <li v-for="(list, j) in ir.radiology_request_items">
                                                                <template v-if="list.url.length > 0">
                                                                    <feather-icon icon="EyeIcon" @click="triggerRadResult({ alias_name: regData.alias_name, name: regData.name, url: list.url, costing_name: list.costing_name })" color="blue" v-b-tooltip.hover.left="`Lihat hasil ${list.costing_name}`"/>
                                                                </template>
                                                                <template v-else>
                                                                    <img src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`${list.costing_name} telah di order, belum terbit hasil`">
                                                                </template>
                                                                {{ list.costing_name }}
                                                            </li>
                                                            <div v-if="ir.clinical_indications" class="mt-2">
                                                                <b>Catatan Klinis:</b>
                                                                <div class="bg-neutral-100 p-2 rounded">{{ ir.clinical_indications }}</div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </template>
                                                <div v-else class="font-weight-bold">-</div>
                                            </section>
                                            <section class="mt-12">
                                                <div class="font-weight-bold text-ax-primary-500">FISIOTERAPI:</div>
                                                <div v-if="result?.physiotherapy_request?.length > 0">
                                                    <template v-for="(phy, i) in result.physiotherapy_request">
                                                        <div class="mb-12">
                                                            <div class="font-weight-bold">{{localDateTime(phy.date_request)}}</div>
                                                            <li v-for="(list, j) in phy.physiotherapy_request_items">{{ list.costing_name }} </li>
                                                        </div>
                                                    </template>
                                                </div>
                                                <div v-else>-</div>
                                            </section>
                                            <section class="mt-12">
                                                <div class="font-weight-bold text-ax-primary-500">RESEP:</div>
                                                <template v-if="result?.prescription?.length > 0">
                                                    <div v-for="(ipre, i) in result.prescription" class="mt-8">
                                                        <div class="font-weight-bold">
                                                            <template>
                                                                <feather-icon v-if="ipre?.flag == 1" icon="CheckSquareIcon" color="green" v-b-tooltip.hover.left="`Diproses oleh farmasi`"/>
                                                                <img v-else-if="ipre?.flag == 0" src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`Berhasil diorder`">
                                                            </template>
                                                            {{localDateTime(ipre.date_added)}}
                                                        </div>
                                                        <div v-for="(list, idx_p) in ipre.form_prescription_parent" class="mt-1">
                                                            <div :class="getPrescParentVariant(list.id)" class="px-12 py-1">
                                                                <div class="font-weight-bold">{{ getPrescParentName(list.id) }}</div>
                                                                <div v-if="list.id != 1" class="fs-12 text-italic" style="font-weight: 400">
                                                                    <span>Kemasan: {{ getParentPackageName(list?.package) }}, </span>
                                                                    <span>
                                                                        {{ list?.value | number }}
                                                                        TIAP {{ getDynamicOptName(list?.signatura_times_id, signature_time)}}
                                                                        {{ getDynamicOptName(list?.administer_id, catalog_instruction) }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div v-if="ipre?.form_prescription" class="mt-1">
                                                                <li v-for="unit in ipre.form_prescription" v-if="list.id == unit.prescription_item_type_id">
                                                                    {{ unit?.pharmacy_catalog_name }}, {{ unit?.dose }} {{ getDynamicOptName(unit?.dose_unit_id, signature_units) }}
                                                                    <span v-if="unit?.prescription_item_type_id == 1">
                                                                        TIAP {{ getDynamicOptName(unit?.signatura_times_id, signature_time)}}
                                                                        {{ getDynamicOptName(unit?.administer_id, catalog_instruction) }}
                                                                    </span>
                                                                </li>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                                <div v-else>-</div>
                                            </section>
                                            <section class="mt-12">
                                                <div class="font-weight-bold text-ax-primary-500">ADVICE:</div>
                                                <div v-html="parsedBody(result?.advice || '-')"></div>
                                            </section>
                                        </section>
                                    </div>
                                </div>
                            </b-collapse>

                            <b-collapse accordion="my-accordion" :id="`accordion-cppt-`+result.id">
                                <div class="card-body py-8 px-12">
                                    <template v-if="isAllowedAddCppt(result.registration_destination_id)">
                                        <b-button @click="triggerCpptModal({
                                            gate: 'main_gate',
                                            registration_destination_id: result.registration_destination_id,
                                            cppt_id: null,
                                            triggerFrom: null,
                                        })"
                                        variant="w3green"
                                        size="sm">
                                            <feather-icon icon="PlusCircleIcon" class="mr-1" />
                                            <span class="fs-12">TAMBAH CPPT</span>
                                        </b-button>
                                    </template>
                                    <template v-if="result?.cppt?.length > 0">
                                        <ListCpptView
                                            :cppt_list="result.cppt"
                                            :catalog_list="catalog_list"
                                            :stock_list="PackageOptions"
                                            :presc_template="presc_template_list"
                                            :anamn_template="anamn_template_list"
                                            :patient="{
                                                name: regData.name,
                                                alias_name: regData.alias_name,
                                                registration_destination_id: regData.registration_destination_id,
                                                soap_registration_destination_id: result.registration_destination_id
                                            }"
                                            :signature_time="signature_time"
                                            :signature_dose="signature_dose"
                                            :signature_units="signature_units"
                                            :catalog_instruction="catalog_instruction"

                                            @refreshAssessment="soapRegistration"
                                            @prescChildTemplate="fillPrescTemplate"
                                        />
                                    </template>
                                </div>
                            </b-collapse>

                            <b-collapse accordion="my-accordion" :id="`accordion-sbar-`+result.id">
                                <div class="card-body px-0 py-8">
                                    <div class="px-8 fs-12">
                                        <b-button  @click="triggerSbarModal({
                                        gate: 'main_gate',
                                        registration_destination_id: result.registration_destination_id
                                        })"
                                        class="btn-sbar"
                                        size="sm">
                                            <feather-icon icon="PlusCircleIcon" class="mr-1" />
                                            <span class="fs-12">SBAR</span>
                                        </b-button>
                                        <template v-if="result?.sbar?.length > 0">
                                            <ListSBARView
                                                :sbar_list="result.sbar"
                                                :catalog_list="catalog_list"
                                                :stock_list="PackageOptions"
                                                :presc_template="presc_template_list"
                                                :anamn_template="anamn_template_list"
                                                :patient="{
                                                    name: regData.name,
                                                    alias_name: regData.alias_name,
                                                    registration_destination_id: regData.registration_destination_id,
                                                    soap_registration_destination_id: result.registration_destination_id
                                                }"
                                                :signature_time="signature_time"
                                                :signature_dose="signature_dose"
                                                :signature_units="signature_units"
                                                :catalog_instruction="catalog_instruction"

                                                @refreshAssessment="soapRegistration"
                                            />
                                        </template>
                                    </div>
                                </div>
                            </b-collapse>
                        </section>
                    </template>
                </section>

                <section class="mt-32" v-if="regData.health_facility_id == hfi_inpatient">
                    <div class="d-flex align-items-center align-items-stretch" style="background-color: #edf2f6; ">
                        <ModernLabel label="HISTORY TRANSFER" variant="ax-primary-400" class="col px-0"/>
                        <div v-if="RoomHistoryData?.length > 0" class="d-flex align-items-center" style="width: 100px;">
                            <b-form-select id="per-page-select" v-model="roomHistoryTable.perPage" size="sm" :options="roomHistoryTable.pageOptions" />
                        </div>
                    </div>
                    <section v-if="RoomHistoryData?.length > 0" class="mt-12">
                        <b-table small bordered responsive :fields="roomHistoryfields" :items="RoomHistoryData" head-variant="light" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                            <template #cell(name)="row">
                                {{ row.item?.alias_name }}.{{ row.item?.patient_name }}
                            </template>

                            <template #cell(operation_type_id)="row">
                                <div class="w-100 text-center">
                                    {{ getOperationTypeName(row.value)}}
                                </div>
                            </template>

                            <template #cell(doctor_in_charge_id)="row">
                                {{ row.item.doctor_in_charge_name }} {{ row.item.doctor_in_charge_name_2 }} {{ row.item.doctor_in_charge_name_3 }} {{ row.item.doctor_in_charge_name_4 }}
                            </template>

                            <template #cell(operation_approved_date)="row">
                                <div>{{ row?.value || '-'}}</div>
                            </template>

                            <template #cell(approved)="row">
                                <template v-if="row.item?.employee_approved_name || row.item?.employee_approved_date ">
                                    {{ row.item.employee_approved_name }} {{ row.item.employee_approved_date }}
                                </template>
                                <div v-else>-</div>
                            </template>

                            <template #cell(action)="row">
                                <div class="d-flex justify-content-center">
                                    <b-button @click="triggerOperatieModal({gate: 'info_gate', rowData: row.item})" size="sm" variant="w3green" class="mr-1">
                                        <feather-icon icon="SearchIcon" />
                                    </b-button>
                                    <template v-if="row.item.status == 1">
                                        <b-button @click="triggerOperatieModal({gate: 'edit_gate', rowData: row.item})" size="sm" variant="warning" class="mr-1">
                                            <feather-icon icon="EditIcon" />
                                        </b-button>
                                        <b-button @click="triggerOperatieModal({gate: 'cancel_gate', rowData: row.item})" size="sm" variant="danger">
                                            <feather-icon icon="XSquareIcon" />
                                        </b-button>
                                    </template>
                                    <template v-if="row.item.status == 2 || row.item.status == 3">
                                        <!-- <router-link :to="{ path: '/operatie-kamer/'+ row.item.id}" target="_blank">
                                            <b-button size="sm" variant="primary" class="mr-2">
                                                <feather-icon icon="AirplayIcon" />
                                            </b-button>
                                        </router-link> -->
                                        <b-button
                                            size="sm"
                                            variant="primary"
                                            class="mr-1"
                                            @click="openOKInNewTab(row.item.id)"
                                        >
                                            <feather-icon icon="AirplayIcon" />
                                        </b-button>
                                        <template v-if="row.item?.operativeReport && Object.keys(row.item.operativeReport).length > 0">
                                            <b-button
                                            :to="`/operatie-kamer/print/${row.item.id}/${row.item.operativeReport.id}`"
                                            target="_blank"
                                            size="sm" variant="w3green" class="mr-1">
                                                <feather-icon icon="PrinterIcon" />
                                                <!-- <b-icon icon="printer" /> -->
                                            </b-button>
                                        </template>
                                    </template>
                                </div>
                            </template>
                        </b-table>
                        <div class="d-flex justify-content-between w-100 mt-12">
                            <b-pagination
                                v-model="roomHistoryTable.currentPage"
                                :total-rows="roomHistoryTable.totalRows"
                                :per-page="roomHistoryTable.perPage"
                                align="fill" size="sm" class="my-0">
                            </b-pagination>
                            <span class="text-regular-14">Menampilkan {{ roomHistoryTable.firstRows }} hingga <template v-if="roomHistoryTable.lastRows < roomHistoryTable.totalRows">{{ roomHistoryTable.lastRows }}</template> <template v-else>{{ roomHistoryTable.totalRows }}</template> dari {{ roomHistoryTable.totalRows }} entri</span>
                        </div>
                    </section>
                    <div v-else class="text-center fs-13 mt-1" style="font-style: italic;">Tidak ada order operasi yang dibuat pada pasien ini</div>
                </section>

                <section class="mt-32" v-if="regData.health_facility_id == hfi_inpatient || regData.health_facility_id == hfi_outpatient">
                    <div class="d-flex align-items-center align-items-stretch" style="background-color: #edf2f6; ">
                        <ModernLabel label="PROSEDUR" variant="ax-primary-400" class="col px-0"/>
                        <div v-if="procedureItems?.length > 0" class="d-flex align-items-center" style="width: 100px;">
                            <b-form-select id="per-page-select" v-model="procedureTable.perPage" size="sm" :options="procedureTable.pageOptions" />
                        </div>
                    </div>
                    <section v-if="procedureItems?.length > 0" class="mt-12">
                        <b-table small fixed bordered head-variant="light" :items="procedureItems" :fields="procedureTable.fields" :per-page="0" :current-page="procedureTable.currentPage" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                            <template #cell(date_added)="row">{{ localDateTime(row.value) }}</template>
                            <template #cell(action)="row">
                                <b-button @click="editProcedure(row.item)" size="sm" variant="warning-500" class="mr-1">
                                    <feather-icon icon="EditIcon" />
                                </b-button>
                                <b-button @click="triggerProcedure({ regData: null, procedureData: row.item, optDoctor: null, optTyCosting: null, gate: 'delete_gate'})" size="sm" variant="danger-500">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </template>
                        </b-table>
                        <div class="d-flex justify-content-between w-100 mt-12">
                            <b-pagination
                                v-model="procedureTable.currentPage"
                                :total-rows="procedureRows"
                                :per-page="procedureTable.perPage"
                                align="fill" size="sm" class="my-0">
                            </b-pagination>
                            <span class="text-regular-14">Menampilkan {{ procedureTable.firstRows }} hingga <template v-if="procedureTable.lastRows < procedureRows">{{ procedureTable.lastRows }}</template> <template v-else>{{ procedureRows }}</template> dari {{ procedureRows }} entri</span>
                        </div>
                    </section>
                    <div v-else class="text-center fs-13 mt-1" style="font-style: italic;">Tidak ada order prosedur yang dibuat pada pasien ini</div>
                </section>

                <section class="mt-32" >
                    <div class="d-flex align-items-center align-items-stretch" style="background-color: #edf2f6; ">
                        <ModernLabel label="ORDER OPERASI" variant="ax-primary-400" class="col px-0"/>
                        <div v-if="mergedOperatingData?.length > 0" class="d-flex align-items-center" style="width: 100px;">
                            <b-form-select id="per-page-select" v-model="okTable.perPage" size="sm" :options="okTable.pageOptions" />
                        </div>
                    </div>
                    <section v-if="mergedOperatingData?.length > 0" class="mt-12">
                        <b-table small bordered responsive :fields="OKfields" :items="mergedOperatingData" head-variant="light" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                            <template #cell(name)="row">
                                {{ row.item?.alias_name }}.{{ row.item?.patient_name }}
                            </template>

                                <template #cell(operation_type_id)="row">
                                    <div class="w-100 text-center">
                                        {{ getOperationTypeName(row.value)}}
                                    </div>
                                </template>

                                <template #cell(room)="row">
                                    <template v-if="row.item?.room_name || row.item?.bed_name ">
                                        {{ row.item.room_name }} {{ row.item.bed_name }}
                                    </template>
                                    <div v-else>-</div>
                                </template>

                                <template #cell(operation_approved_date)="row">
                                    <div>{{ row?.value || '-'}}</div>
                                </template>

                                <template #cell(approved)="row">
                                    <template v-if="row.item?.employee_approved_name || row.item?.employee_approved_date ">
                                        {{ row.item.employee_approved_name }} {{ row.item.employee_approved_date }}
                                    </template>
                                    <div v-else>-</div>
                                </template>

                            <template #cell(action)="row">
                                <div class="d-flex justify-content-center">
                                    <b-button @click="triggerOperatieModal({gate: 'info_gate', rowData: row.item})" size="sm" variant="w3green" class="mr-1">
                                        <feather-icon icon="SearchIcon" />
                                    </b-button>
                                    <template v-if="row.item.status == 1">
                                        <b-button @click="triggerOperatieModal({gate: 'edit_gate', rowData: row.item})" size="sm" variant="warning" class="mr-1">
                                            <feather-icon icon="EditIcon" />
                                        </b-button>
                                        <b-button @click="triggerOperatieModal({gate: 'cancel_gate', rowData: row.item})" size="sm" variant="danger">
                                            <feather-icon icon="XSquareIcon" />
                                        </b-button>
                                    </template>
                                    <template v-if="row.item.status == 2 || row.item.status == 3">
                                        <!-- <router-link :to="{ path: '/operatie-kamer/'+ row.item.id}" target="_blank">
                                            <b-button size="sm" variant="primary" class="mr-2">
                                                <feather-icon icon="AirplayIcon" />
                                            </b-button>
                                        </router-link> -->
                                        <b-button
                                            size="sm"
                                            variant="primary"
                                            class="mr-1"
                                            @click="openOKInNewTab(row.item.id)"
                                        >
                                            <feather-icon icon="AirplayIcon" />
                                        </b-button>
                                        <template v-if="row.item?.operativeReport && Object.keys(row.item.operativeReport).length > 0">
                                            <b-button
                                            :to="`/operatie-kamer/print/${row.item.id}/${row.item.operativeReport.id}`"
                                            target="_blank"
                                            size="sm" variant="w3green" class="mr-1">
                                                <feather-icon icon="PrinterIcon" />
                                                <!-- <b-icon icon="printer" /> -->
                                            </b-button>
                                        </template>
                                    </template>
                                </div>
                            </template>
                        </b-table>
                        <div class="d-flex justify-content-between w-100 mt-12">
                            <b-pagination
                                v-model="okTable.currentPage"
                                :total-rows="okTable.totalRows"
                                :per-page="okTable.perPage"
                                align="fill" size="sm" class="my-0">
                            </b-pagination>
                            <span class="text-regular-14">Menampilkan {{ okTable.firstRows }} hingga <template v-if="okTable.lastRows < okTable.totalRows">{{ okTable.lastRows }}</template> <template v-else>{{ okTable.totalRows }}</template> dari {{ okTable.totalRows }} entri</span>
                        </div>
                    </section>
                    <div v-else class="text-center fs-13 mt-1" style="font-style: italic;">Tidak ada order operasi yang dibuat pada pasien ini</div>
                </section>

                <section class="mt-32">
                    <div class="d-flex align-items-center align-items-stretch" style="background-color: #edf2f6; ">
                        <ModernLabel label="PEMAKAIAN ALAT" variant="ax-primary-400" class="col px-0"/>
                        <div v-if="toolItems?.length > 0" class="d-flex align-items-center" style="width: 100px;">
                            <b-form-select id="per-page-select" v-model="toolTable.perPage" size="sm" :options="toolTable.pageOptions" />
                        </div>
                    </div>
                    <section v-if="toolItems?.length > 0" class="mt-12">
                        <b-table small fixed bordered head-variant="light" :items="toolItems" :fields="toolTable.fields" :per-page="0" :current-page="toolTable.currentPage" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                            <template #cell(date_added)="row">{{ localDateTime(row.value) }}</template>
                            <template #cell(action)="row">
                                <b-button @click="editTool(row.item)" size="sm" variant="warning-500" class="mr-1">
                                    <feather-icon icon="EditIcon" />
                                </b-button>
                                <b-button @click="triggerTool({ regData: null, toolData: row.item, gate: 'delete_gate'})" size="sm" variant="danger-500">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </template>
                        </b-table>
                        <div class="d-flex justify-content-between w-100 mt-12">
                            <b-pagination
                                v-model="toolTable.currentPage"
                                :total-rows="toolRows"
                                :per-page="toolTable.perPage"
                                align="fill" size="sm" class="my-0">
                            </b-pagination>
                            <span class="text-regular-14">Menampilkan {{ toolTable.firstRows }} hingga <template v-if="toolTable.lastRows < toolRows">{{ toolTable.lastRows }}</template> <template v-else>{{ toolRows }}</template> dari {{ toolRows }} entri</span>
                        </div>
                    </section>
                    <div v-else class="text-center fs-13 mt-1" style="font-style: italic;">Tidak ada pemakaian alat yang dibuat pada pasien ini</div>
                </section>

                <section class="mt-16">
                    <ModernLabel label="RIWAYAT BEROBAT"/>
                    <template v-for="(result,i) in soapList.exceptRegistration" >
                        <section v-if="result.health_facility_id == hfi_outpatient && result.is_booking == 1" class="card mt-16 shadow-sm" :style="isSimilar(result.registration_destination_id)">
                            <div class="card-header p-12 bg-white">
                                <ModernLabel label="BOOKING PENUNJANG" :small="`Ditambahkan pada ${localFullDay(result.date_added)} Oleh ${result.doctor_in_charge_name}`" />
                                <section class="mt-8">
                                    <b-button v-b-toggle="'accordion-rb'+result.id" size="sm" variant="primary" class="mr-1">
                                        <feather-icon icon="EyeIcon" size="12" class="mr-1" />
                                        <span class="fs-12">ASESMEN</span>
                                    </b-button>
                                    <b-dropdown size="sm" id="dropdown-aria" text="CETAK & EDIT" variant="success-500">
                                        <template v-if="regData?.health_facility_id == hfi_outpatient">
                                            <b-dropdown-item-button @click="triggerSupportBookingModal({
                                                    regData: regData,
                                                    lab_list: supportPackage.lab_package_list,
                                                    rad_list: supportPackage.rad_package_list,
                                                    phy_list: itemsPhysiotherapy,
                                                    view: 'EDIT',
                                                    data: result
                                                })">Edit Booking
                                            </b-dropdown-item-button>
                                            <b-dropdown-divider></b-dropdown-divider>
                                        </template>
                                        <template v-if="result.health_facility_id == hfi_outpatient && result.is_booking == 1 && result.laboratory_request">
                                            <b-dropdown-header id="dropdown-header-1">LABORATORIUM</b-dropdown-header>
                                            <router-link :to="{ path: '/prints/booking-laboratory-print/'+ result.id}" target="_blank">
                                                <b-dropdown-item-button aria-describedby="dropdown-header-1">Cetak</b-dropdown-item-button>
                                            </router-link>
                                            <b-dropdown-item-button @click="triggerCancelSupportBooking({ request: result.laboratory_request, norm: regData.norm, gate: 'laboratory_gate'})" :disabled="result.laboratory_request.status == 2 || result.laboratory_request.status == 3" aria-describedby="dropdown-header-1">Batalkan Order</b-dropdown-item-button>
                                        </template>
                                        <template v-if="result.health_facility_id == hfi_outpatient && result.is_booking == 1 && result.radiology_request">
                                            <b-dropdown-header id="dropdown-header-2">RADIOLOGI</b-dropdown-header>
                                            <router-link :to="{ path: '/prints/booking-radiology-print/'+ result.id}" target="_blank">
                                                <b-dropdown-item-button aria-describedby="dropdown-header-2">Cetak</b-dropdown-item-button>
                                            </router-link>
                                            <b-dropdown-item-button @click="triggerCancelSupportBooking({ request: result.radiology_request, norm: regData.norm, gate: 'radiology_gate'})" :disabled="result.radiology_request.status == 2 || result.radiology_request.status == 3" aria-describedby="dropdown-header-2">Batalkan Order</b-dropdown-item-button>
                                        </template>
                                        <template v-if="result.health_facility_id == hfi_outpatient && result.is_booking == 1 && result.physiotherapy_request">
                                            <b-dropdown-header id="dropdown-header-2">FISIOTERAPI</b-dropdown-header>
                                            <router-link :to="{ path: '/prints/doctor-rehabilitation-schedule-print/'+ result.id}" target="_blank">
                                                <b-dropdown-item-button>Cetak Jadwal</b-dropdown-item-button>
                                            </router-link>
                                        </template>
                                    </b-dropdown>
                                </section>
                            </div>
                            <b-collapse :id="`accordion-rb`+result.id">
                                <div class="card-body px-0 py-8">
                                    <div class="row mx-0">
                                        <section class="col-lg-3 px-8 fs-12">
                                            <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">LABORATORIUM</div>
                                            <section class="mt-8">
                                                <template v-if="result?.laboratory_request?.length > 0">
                                                    <template v-for="(il, i) in result.laboratory_request">
                                                        <div class="mb-12">
                                                            <div class="font-weight-bold">{{localDateTime(il.date_request)}}</div>
                                                            <li v-for="(list, j) in il.laboratory_request_items">
                                                                <template v-if="list.result?.length > 0">
                                                                    <feather-icon icon="EyeIcon" @click="triggerLabResult({ alias_name: regData.alias_name, name: regData.name, result: list.result, url: list.url,costing_name: list.costing_name, laboratory_request_id: list.laboratory_request_id })" color="blue" v-b-tooltip.hover.left="`Lihat hasil ${list.costing_name}`"/>
                                                                </template>
                                                                <template v-else>
                                                                    <img src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`${list.costing_name} telah di order, belum terbit hasil`">
                                                                </template>
                                                                {{ list.costing_name }}
                                                            </li>
                                                        </div>
                                                    </template>
                                                </template>
                                                <div v-else>-</div>
                                            </section>
                                        </section>
                                        <section class="col-lg-3 px-8 fs-12">
                                            <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">RADIOLOGI</div>
                                            <section class="mt-8">
                                                <template v-if="result?.radiology_request?.length > 0">
                                                    <template v-for="(ir, i) in result.radiology_request">
                                                        <div class="mb-12">
                                                            <div class="font-weight-bold">{{localDateTime(ir.date_request)}}</div>
                                                            <li v-for="(list, j) in ir.radiology_request_items">
                                                                <template v-if="list?.url?.length > 0">
                                                                    <feather-icon icon="EyeIcon" @click="triggerRadResult({ alias_name: regData.alias_name, name: regData.name, url: list.url, costing_name: list.costing_name })" color="blue" v-b-tooltip.hover.left="`Lihat hasil ${list.costing_name}`"/>
                                                                </template>
                                                                <template v-else>
                                                                    <img src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`${list.costing_name} telah di order, belum terbit hasil`">
                                                                </template>
                                                                {{ list.costing_name }}
                                                            </li>
                                                            <div v-if="ir.clinical_indications" class="mt-2">
                                                                <b>Catatan Klinis:</b>
                                                                <div class="bg-neutral-100 p-2 rounded">{{ ir.clinical_indications }}</div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </template>
                                                <div v-else class="font-weight-bold">-</div>
                                            </section>
                                        </section>
                                        <section class="col-lg-3 px-8 fs-12">
                                            <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">FISIOTERAPI</div>
                                            <section class="mt-8">
                                                <div v-if="result?.physiotherapy_request?.length > 0">
                                                    <template v-for="(phy, i) in result.physiotherapy_request">
                                                        <div class="mb-12">
                                                            <div class="font-weight-bold">{{localDateTime(phy.date_request)}}</div>
                                                            <li v-for="(list, j) in phy.physiotherapy_request_items">{{ list.costing_name }} </li>
                                                        </div>
                                                    </template>
                                                </div>
                                                <div v-else>-</div>
                                            </section>
                                        </section>
                                    </div>
                                </div>
                            </b-collapse>
                        </section>

                        <section v-else-if="!(result.health_facility_id == hfi_inpatient && result.is_initial_inpatient == 0)" class="card mt-16 shadow-sm" :style="isSimilar(result.registration_destination_id)">
                            <div class="card-header p-12 bg-white">
                                <ModernLabel :label="getSoapLabel(result.health_facility_id, result.health_facility_item_name, result.is_initial_inpatient, result.is_booking)" :small="`Ditambahkan pada ${localFullDay(result.date_added)} Oleh ${result.doctor_in_charge_name}`" />
                                <section class="mt-8">
                                    <template v-if="userData.user_group.id == 'a4c04d8d-12d8-4c54-afca-69de6bd5f62d' || userData.employee_id == 'd77142c4-4777-47d8-bd0c-fd7dfd134af4'">
                                        <router-link :to="{ path: '/patient/jkn/'+ result.registration_destination_id + '/' + result.id}" target="_blank">
                                            <b-button size="sm" variant="orange-500" class="text-white mr-1">
                                                <feather-icon icon="ZapIcon" size="12" class="mr-1" />
                                                <span class="fs-12">JKN ACCESS</span>
                                            </b-button>
                                        </router-link>
                                    </template>

                                    <b-button v-b-toggle="'accordion-rb'+result.id" size="sm" variant="primary" class="mr-1">
                                        <feather-icon icon="EyeIcon" size="12" class="mr-1" />
                                        <span class="fs-12">{{ getAssesToggleName(result.health_facility_id) }}</span>
                                    </b-button>

                                    <template v-if="examinationCondition(result)">
                                        <b-button @click="triggerExamination({ examData: result })" size="sm" variant="w3green" class="mr-1">
                                            <feather-icon icon="UploadIcon" size="12" class="mr-1" />
                                            <span class="fs-12">PENUNJANG LAINNYA</span>
                                        </b-button>
                                    </template>

                                        <router-link :to="{ path: '/prints/doctor-sbpk-print/'+ result.id}" target="_blank">
                                            <b-button size="sm" variant="outline-primary-500" class="px-16 mr-1">
                                                <feather-icon icon="PrinterIcon" size="12" class="mr-1" />
                                                <span class="fs-12">SBPK</span>
                                            </b-button>
                                        </router-link>

                                        <router-link :to="{ path: '/prints/doctor-studies-print/'+ result.id}" target="_blank">
                                            <b-button size="sm" variant="outline-success-500" class="px-16 mr-1">
                                                <feather-icon icon="PrinterIcon" size="12" class="mr-1" />
                                                <span class="fs-12">ASESMEN</span>
                                            </b-button>
                                        </router-link>

                                        <!-- CONDITIONAL BUTTONs -->

                                        <template v-if="result.health_facility_id == hfi_emergency">
                                            <router-link :to="{ path: '/prints/doctor-emergency-print/'+ result.id}" target="_blank">
                                                <b-button size="sm" variant="outline-success-500" class="px-16 mr-1">
                                                    <feather-icon icon="PrinterIcon" size="12" class="mr-1" />
                                                    <span class="fs-12">ASESMEN IGD</span>
                                                </b-button>
                                            </router-link>
                                        </template>

                                    <b-dropdown v-if="result.health_facility_item_id == hfii_rehabilitation && result.is_medical_rehabilitation == 1" size="sm" id="dropdown-aria" text="UJI FUNGSI" variant="outline-success-500" class="mr-1 fs-12" menu-class="w-100">
                                        <router-link :to="{ path: '/prints/doctor-rehabilitation-print/'+ result.id}" target="_blank">
                                            <b-dropdown-item-button>Cetak Formulir</b-dropdown-item-button>
                                        </router-link>
                                        <router-link :to="{ path: '/prints/doctor-rehabilitation-result-print/'+ result.id}" target="_blank">
                                            <b-dropdown-item-button>Cetak Hasil</b-dropdown-item-button>
                                        </router-link>
                                    </b-dropdown>

                                    <template v-if="result.health_facility_item_id == hfii_hemodialysis && result.is_hemodialysis == 1">
                                        <router-link :to="{ path: '/prints/doctor-hemodialysis-print/'+ result.id}" target="_blank">
                                            <b-button size="sm" variant="outline-success-500" class="px-16 mr-1">
                                                <feather-icon icon="PrinterIcon" size="12" class="mr-1" />
                                                <span class="fs-12">HEMODIALISA</span>
                                            </b-button>
                                        </router-link>
                                    </template>

                                    <b-dropdown v-if="result.health_facility_item_id == hfii_physiotherapy" size="sm" id="dropdown-aria" text="UJI FUNGSI" variant="success-500" class="mr-1 fs-12" menu-class="w-100">
                                        <router-link :to="{ path: '/prints/doctor-rehabilitation-print/'+ result.id}" target="_blank">
                                            <b-dropdown-item-button>Cetak Formulir</b-dropdown-item-button>
                                        </router-link>
                                        <router-link :to="{ path: '/prints/doctor-rehabilitation-result-print/'+ result.id}" target="_blank">
                                            <b-dropdown-item-button>Cetak Hasil</b-dropdown-item-button>
                                        </router-link>
                                        <router-link :to="{ path: '/prints/doctor-therapy-program-print/'+ result.id}" target="_blank">
                                        <b-dropdown-item-button>Cetak Program</b-dropdown-item-button>
                                        </router-link>
                                        <router-link :to="{ path: '/prints/doctor-rehabilitation-schedule-print/'+ result.id}" target="_blank">
                                           <b-dropdown-item-button>Cetak Jadwal</b-dropdown-item-button>
                                        </router-link>
                                        <router-link :to="{ path: '/prints/doctor-certificate-print/'+ result.id}" target="_blank">
                                           <b-dropdown-item-button>Cetak Keterangan</b-dropdown-item-button>
                                        </router-link>
                                    </b-dropdown>

                                    <!-- JANGAN DIHAPUS -->

                                    <!-- <template v-if="getOutpatientCondition(result.doctor_in_charge_id, result.health_facility_id, result.is_resume_exit, result.is_booking)">
                                        <b-button @click="triggerOutpatientModal(result)" pill size="sm" variant="primary-600" class="px-16 mr-1">
                                            <i><img src="@/@core/assets/icons/nurse/file-eye.svg" alt=""></i> <span>Buka Kajian</span>
                                        </b-button>
                                    </template> -->

                                    <!-- <template v-if="getInpatientCondition(result.doctor_in_charge_id, result.health_facility_id, result.is_initial_inpatient)">
                                        <b-button @click="triggerInpatientModal(result)" pill size="sm" variant="primary-600" class="px-16 mr-1">
                                            <i><img src="@/@core/assets/icons/nurse/file-eye.svg" alt=""></i> <span>Buka Kajian</span>
                                        </b-button>
                                    </template> -->
                                </section>
                            </div>

                            <b-collapse :id="`accordion-rb`+result.id">
                                <div class="card-body px-0 py-8">
                                    <div class="row mx-0">
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <section class="col-lg-4 px-8 fs-12">
                                                    <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">SUBJECTIVE</div>
                                                    <section class="mt-8">
                                                        <div class="font-weight-bold text-ax-primary-500">KELUHAN UTAMA:</div>
                                                        <div>{{result?.main_complaint || '-' }}</div>
                                                    </section>
                                                    <section class="mt-12">
                                                        <div class="font-weight-bold text-ax-primary-500">RIWAYAT PENYAKIT SEKARANG:</div>
                                                        <div v-html="parsedBody(result?.history_disease_now || '-')"></div>
                                                    </section>
                                                    <section class="mt-12">
                                                        <div class="font-weight-bold text-ax-primary-500">RIWAYAT PENYAKIT DAHULU:</div>
                                                        <div v-html="parsedBody(result?.history_disease_past || '-')"></div>
                                                    </section>
                                                    <section class="mt-12">
                                                        <div class="font-weight-bold text-ax-primary-500">RIWAYAT OBAT:</div>
                                                        <div v-html="parsedBody(result?.history_drugs || '-')"></div>
                                                    </section>
                                                    <section class="mt-12">
                                                        <div class="font-weight-bold text-ax-primary-500">ALERGI:</div>
                                                        <template v-if="result.allergies?.length > 0">
                                                            <li v-for="list in result.allergies">{{ list.name }}</li>
                                                        </template>
                                                        <div v-else>-</div>
                                                    </section>
                                                </section>

                                                <section class="col-lg-4 px-8 fs-12">
                                                    <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">OBJECTIVE</div>
                                                    <section class="mt-8">
                                                        <div class="font-weight-bold text-ax-primary-500">TANDA VITAL:</div>
                                                        <div>
                                                            Systolic = {{ result?.systolic_blood_pressure || '-' }},
                                                            Diastolic = {{ result?.diastolic_blood_pressure || '-' }},
                                                            Frekuensi Nadi = {{ result?.pulse_frequency || '-' }},
                                                            Frekuensi Nafas = {{ result?.breath_frequency || '-' }},
                                                            Saturasi Oksigen = {{ result?.oximetry_frequency || '-' }},
                                                            Suhu = {{ result?.temperature || '-' }},
                                                            Oksigenasi = {{ getOxygenName(result?.oxygen_type_id || '-') }},
                                                            Kesadaran = {{ getAwareName(result?.aware) }},
                                                            GCS Mata = {{ getGcsEyeName(result?.eye_score || '-') }},
                                                            GCS Verbal = {{ getGcsVerbalName(result?.verbal_score || '-') }},
                                                            GCS Motorik = {{ getGcsMotoricName(result?.motor_score || '-') }},
                                                            Warna Kulit = {{ getColorSkinName(result?.skin_color || '-') }},
                                                            Perilaku = {{ getBehaviorName(result?.behavior || '-') }},
                                                        </div>
                                                    </section>
                                                    <section class="mt-12">
                                                        <div class="font-weight-bold text-ax-primary-500">ANTROPOMETRI:</div>
                                                        <div>
                                                            BB = {{ result?.weight || '-' }},
                                                            TB = {{ result?.height || '-' }},
                                                            IMT = {{ result?.imt || '-' }},
                                                            L.Kepala = {{ result?.head_circumference || '-' }},
                                                            L.Dada = {{ result?.chest_circumference || '-' }},
                                                            L.Lengan Atas = {{ result?.arm_circumference || '-' }},
                                                            L.Perut = {{ result?.stomach_circumference || '-' }},
                                                            L.Lutut = {{ result?.knee_circumference || '-' }}
                                                        </div>
                                                    </section>
                                                </section>

                                                <section class="col-lg-4 px-8 fs-12">
                                                    <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">ASSESSMENT</div>
                                                    <section class="mt-8">
                                                        <div class="font-weight-bold text-ax-primary-500">DIAGNOSIS:</div>
                                                        <template v-if="result?.icd10?.length > 0">
                                                            <li v-for="(list, index) in result.icd10">{{ list.icd10_code }} {{ list.icd10_name }}</li>
                                                        </template>
                                                        <div v-else class="text-italic">-</div>
                                                    </section>
                                                    <section class="mt-12">
                                                        <div class="font-weight-bold text-ax-primary-500">CATATAN ICD10:</div>
                                                        <div v-html="parsedBody(result?.icd10_notes || '-')"></div>
                                                    </section>
                                                    <section class="mt-12">
                                                        <div class="font-weight-bold text-ax-primary-500">NON-ICD10:</div>
                                                        <template v-if="result?.non_icd10?.length > 0">
                                                            <li v-for="(list, index) in result.non_icd10">{{ list }}</li>
                                                        </template>
                                                        <div v-else class="text-italic">-</div>
                                                    </section>
                                                </section>

                                                    <section class="col-12 px-8 fs-12 mt-20">
                                                        <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">STATUS GENERALIS</div>
                                                        <div class="mt-8">
                                                            <i class="text-danger-500">*teks merah untuk menandai pemeriksaan fisik bermakna</i>
                                                            <div class="row fs-11 mt-1">
                                                                <div class="col-lg-4">
                                                                    <table class="w-100">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">KEPALA</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.head)">
                                                                                <td style="width: 100px;">Kepala</td>
                                                                                <td style="width: 15px;" class="px-1">:</td>
                                                                                <td>{{ getHead(result.head ) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.head_hair_color)">
                                                                                <td class="align-top">Warna Rambut</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getHairColor(result.head_hair_color) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.head_hair_condition)">
                                                                                <td class="align-top">Kondisi Rambut</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getHairCondition(result.head_hair_condition) }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">MATA</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.eye_conjunctive)">
                                                                                <td class="align-top" >Konjungtiva</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getEyeConjunctive(result.eye_conjunctive) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.eye_sclera)">
                                                                                <td class="align-top">Sklera</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getEyeSclera(result.eye_sclera) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.eye_pupil)">
                                                                                <td class="align-top">Pupil</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getEyePupil(result.eye_pupil) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.eye_light_reflex)">
                                                                                <td class="align-top">Reflek Cahaya</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getEyeReflect(result.eye_light_reflex) }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">MULUT</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.mouth_mucosa)">
                                                                                <td class="align-top">Mukosa</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getMouthMucosa(result.mouth_mucosa) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.mouth_discoloration)">
                                                                                <td class="align-top">Perubahan Warna</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getMouthDiscoloration(result.mouth_discoloration) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.mouth_tongue)">
                                                                                <td class="align-top">Lidah</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getMouthTongue(result.mouth_tongue) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.mouth_caries)">
                                                                                <td class="align-top">Karies</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getMouthCaries(result.mouth_caries) }}</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <table class="w-100">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">LEHER</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.neck_kgb)">
                                                                                <td class="align-top" style="width: 100px;">KGB</td>
                                                                                <td style="width: 15px;" class="align-top px-1">:</td>
                                                                                <td>{{ getNeckKGB(result.neck_kgb) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.neck_trachea_position)">
                                                                                <td class="align-top">Posisi Trakea</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getNeckTracheaPosition(result.neck_trachea_position) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.neck_massa)">
                                                                                <td class="align-top">Massa</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getNeckMassa(result.neck_massa) }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">EKSTREMITAS</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.extremity_acral)">
                                                                                <td class="align-top">Akral</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getEkstremitasAkral(result.extremity_acral) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.extremity_crt)">
                                                                                <td class="align-top">CRT</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getEkstremitasCRT(result.extremity_crt) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.extremity_edema)">
                                                                                <td class="align-top">Edema</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getEkstremitasEdema(result.extremity_edema) }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">ABDOMEN</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.abdomen_supel)">
                                                                                <td class="align-top">Supel</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getAbdomenSupel(result.abdomen_supel) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.abdomen_bowel_sounds)">
                                                                                <td class="align-top">Bising Usus</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getAbdomenBising(result.abdomen_bowel_sounds) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.abdomen_bowel_sounds)">
                                                                                <td class="align-top">Nyeri Tekan</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getAbdomenPress(result.abdomen_tenderness) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.abdomen_hepar_lien)">
                                                                                <td class="align-top">Hepar & Lien</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getAbdomenHepar(result.abdomen_hepar_lien) }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="align-top">Pemeriksaan Abdomen Lainnya</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td class="align-top">
                                                                                    <div v-html="parsedBody((result.abdomen_other_physical_check) ? (result.abdomen_other_physical_check) : '-')"></div>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <table class="w-100">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">THORAKS</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="3" class="font-weight-bold">INSPEKSI</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.thorax_insp_chest)">
                                                                                <td class="align-top" style="width: 120px;">Dada</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getInspectionChest(result.thorax_insp_chest) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.thorax_insp_retraction)">
                                                                                <td class="align-top">Retraksi</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getInspectionRetractiont(result.thorax_insp_retraction) }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="3" class="font-weight-bold">PALPASI</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.thorax_insp_chest)">
                                                                                <td class="align-top">Dada</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getPalpationChest(result.thorax_insp_chest) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.thorax_palp_fremitus)">
                                                                                <td class="align-top">Fremitus</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getPalpationFremitus(result.thorax_palp_fremitus) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.thorax_palp_crepitation)">
                                                                                <td class="align-top">Krepitasi</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getPalpationKrepitasi(result.thorax_palp_crepitation) }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="3" class="font-weight-bold">AUSKULTASI</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.thorax_ausk_pul_vasiculer)">
                                                                                <td class="align-top">Pulmo Vasikuler</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getAuskultationPulmoVasikuler(result.thorax_ausk_pul_vasiculer) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.thorax_ausk_pul_rhonki)">
                                                                                <td class="align-top">Pulmo Rhonki</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getAuskultationPulmoRhonki(result.thorax_ausk_pul_rhonki) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.thorax_ausk_pul_wheezing)">
                                                                                <td class="align-top">Pulmo Wheezing</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getAuskultationPulmoWheezing(result.thorax_ausk_pul_wheezing) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.thorax_ausk_cor_heart_sound)">
                                                                                <td class="align-top">COR Bunyi Jantung</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getAuskultationCORBJ(result.thorax_ausk_cor_heart_sound) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.thorax_ausk_cor_murmur)">
                                                                                <td class="align-top">COR Murmur</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getAuskultationCORMurmur(result.thorax_ausk_cor_murmur) }}</td>
                                                                            </tr>
                                                                            <tr :class="getMeaningVariant(result.thorax_ausk_cor_gallop)">
                                                                                <td class="align-top">COR Gallop</td>
                                                                                <td class="align-top px-1">:</td>
                                                                                <td>{{ getAuskultationCORGallop(result.thorax_ausk_cor_gallop) }}</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <table class="w-100 mt-1">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="align-top" style="width: 100px;">Genitalia</td>
                                                                        <td class="align-top px-1" style="width: 15px;">:</td>
                                                                        <td>
                                                                            <div v-html="parsedBody(result?.genitalia || '-')"></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="align-top">Status Lokalis</td>
                                                                        <td class="align-top px-1">:</td>
                                                                        <td>
                                                                            <div v-html="parsedBody(result?.localist_status || '-')"></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="align-top">Status Neurologis</td>
                                                                        <td class="align-top px-1">:</td>
                                                                        <td>
                                                                            <div v-html="parsedBody(result?.neurologist_status || '-')"></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="align-top">Status Oftalmologis</td>
                                                                        <td class="align-top px-1">:</td>
                                                                        <td>
                                                                            <div v-html="parsedBody(result?.ophthal_status || '-')"></div>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </section>

                                            <template v-if="result.health_facility_item_id == hfii_hemodialysis">
                                                <div class="col-12 px-8 fs-12 mt-20">
                                                    <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">HEMODIALISA</div>
                                                    <table class="w-100 mt-8">
                                                        <tbody>
                                                            <tr>
                                                                <td class="align-top" style="width: 120px;">Diagnosis Utama</td>
                                                                <td class="align-top px-1 text-center" style="width: 30px;">:</td>
                                                                <td class="align-top">{{ result?.main_diagnosis || '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Etiologi</td>
                                                                <td class="align-top px-1 text-center">:</td>
                                                                <td class="align-top">{{ result?.etiology || '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Penyakit Penyerta</td>
                                                                <td class="align-top px-1 text-center">:</td>
                                                                <td class="align-top">{{ result?.comorbidities || '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Frekuensi HD</td>
                                                                <td class="align-top px-1 text-center">:</td>
                                                                <td class="align-top">{{ result?.frequency_hd || '-' }} x/minggu</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-top">Akses Vaskular</td>
                                                                <td class="align-top px-1 text-center">:</td>
                                                                <td class="align-top">
                                                                    <div v-html="parsedBody(result?.vascular_access || '-')"></div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </template>

                                            <template v-if="result.health_facility_item_id == hfii_rehabilitation">
                                                <div class="col-12 px-8 fs-12 mt-20">
                                                    <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">UJI FUNGSI / PROSEDUR KFR</div>
                                                    <div class="row fs-12">
                                                        <div class="col-lg-4">
                                                            <section class="mt-8">
                                                                <div class="font-weight-bold text-ax-primary-500">SUSPEK PENYAKIT AKIBAT:</div>
                                                                <div>{{ result?.suspected_disease_type_id == '1' ?  `Ya, ${result.suspected_disease}` : 'Tidak' }}</div>
                                                            </section>
                                                            <section class="mt-12">
                                                                <div class="font-weight-bold text-ax-primary-500">PEMERIKSAAN & UJI FUNGSI:</div>
                                                                <div v-html="parsedBody(result?.functional_testing || '-')"></div>
                                                            </section>
                                                            <section class="mt-12">
                                                                <div class="font-weight-bold text-ax-primary-500">DIAGNOSA FUNGSIONAL:</div>
                                                                <div v-html="parsedBody(result?.functional_diagnosis || '-')"></div>
                                                            </section>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <section class="mt-8">
                                                                <div class="font-weight-bold text-ax-primary-500">DIAGNOSA MEDIS:</div>
                                                                <div v-html="parsedBody(result?.medical_diagnosis || '-')"></div>
                                                            </section>
                                                            <section class="mt-12">
                                                                <div class="font-weight-bold text-ax-primary-500">HASIL YANG DIDAPAT:</div>
                                                                <div v-html="parsedBody(result?.result || '-')"></div>
                                                            </section>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <section class="mt-8">
                                                                <div class="font-weight-bold text-ax-primary-500">KESIMPULAN:</div>
                                                                <div v-html="parsedBody(result?.conclusion || '-')"></div>
                                                            </section>
                                                            <section class="mt-12">
                                                                <div class="font-weight-bold text-ax-primary-500">REKOMENDASI:</div>
                                                                <div v-html="parsedBody(result?.recommendation || '-')"></div>
                                                            </section>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>

                                                <template v-if="result.health_facility_item_id == hfii_mcu">
                                                    <div class="col-12 px-8 fs-12 mt-20">
                                                        <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">MEDICAL CHECK UP</div>
                                                        <table class="w-100 mt-8">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="align-top" style="width: 150px;">Kesimpulan</td>
                                                                    <td class="align-top px-1 text-center" style="width: 30px;">:</td>
                                                                    <td class="align-top">
                                                                    <div v-html="parsedBody(result?.fit_to_work || '-')"></div>
                                                                </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <section class="col-lg-3 px-8 fs-12">
                                            <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">PLANNING</div>
                                            <section class="mt-8">
                                                <div class="font-weight-bold text-ax-primary-500">PROSEDUR:</div>
                                                <template v-if="result?.transaction_doctor?.length > 0">
                                                    <li v-for="list in result.transaction_doctor">{{ list.costing_name }}</li>
                                                </template>
                                                <div v-else>-</div>
                                            </section>
                                            <section class="mt-12">
                                                <div class="font-weight-bold text-ax-primary-500">ICD9CM:</div>
                                                <div>
                                                    <div v-if="result?.icd9cm?.length > 0">
                                                        <li v-for="list in result.icd9cm">{{ list.icd9cm_code }} {{ list.icd9cm_name }}</li>
                                                    </div>
                                                    <div v-else>-</div>
                                                </div>
                                                <div v-html="parsedBody(result?.therapy_plan || '-')" class="mt-1"></div>
                                            </section>
                                            <section class="mt-12">
                                                <div class="font-weight-bold text-ax-primary-500">LABORATORIUM:</div>
                                                <template v-if="result?.laboratory_request?.length > 0">
                                                    <template v-for="(il, i) in result.laboratory_request">
                                                        <div class="mb-12">
                                                            <div class="font-weight-bold">{{localDateTime(il.date_request)}}</div>
                                                            <li v-for="(list, j) in il.laboratory_request_items">
                                                                <template v-if="list.result?.length > 0">
                                                                    <feather-icon icon="EyeIcon" @click="triggerLabResult({ alias_name: regData.alias_name, name: regData.name, result: list.result, url: list.url,costing_name: list.costing_name, laboratory_request_id: list.laboratory_request_id })" color="blue" v-b-tooltip.hover.left="`Lihat hasil ${list.costing_name}`"/>
                                                                </template>
                                                                <template v-else>
                                                                    <img src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`${list.costing_name} telah di order, belum terbit hasil`">
                                                                </template>
                                                                {{ list.costing_name }}
                                                            </li>
                                                        </div>
                                                    </template>
                                                </template>
                                                <div v-else>-</div>
                                            </section>
                                            <section class="mt-12">
                                                <div class="font-weight-bold text-ax-primary-500">RADIOLOGI:</div>
                                                <template v-if="result?.radiology_request?.length > 0">
                                                    <template v-for="(ir, i) in result.radiology_request">
                                                        <div class="mb-12">
                                                            <div class="font-weight-bold">{{localDateTime(ir.date_request)}}</div>
                                                            <li v-for="(list, j) in ir.radiology_request_items">
                                                                <template v-if="list.url.length > 0">
                                                                    <feather-icon icon="EyeIcon" @click="triggerRadResult({ alias_name: regData.alias_name, name: regData.name, url: list.url, costing_name: list.costing_name })" color="blue" v-b-tooltip.hover.left="`Lihat hasil ${list.costing_name}`"/>
                                                                </template>
                                                                <template v-else>
                                                                    <img src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`${list.costing_name} telah di order, belum terbit hasil`">
                                                                </template>
                                                                {{ list.costing_name }}
                                                            </li>
                                                            <div v-if="ir.clinical_indications" class="mt-2">
                                                                <b>Catatan Klinis:</b>
                                                                <div class="bg-neutral-100 p-2 rounded">{{ ir.clinical_indications }}</div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </template>
                                                <div v-else class="font-weight-bold">-</div>
                                            </section>
                                            <section class="mt-12">
                                                <div class="font-weight-bold text-ax-primary-500">FISIOTERAPI:</div>
                                                <div v-if="result?.physiotherapy_request?.length > 0">
                                                    <template v-for="(phy, i) in result.physiotherapy_request">
                                                        <div class="mb-12">
                                                            <div class="font-weight-bold">{{localDateTime(phy.date_request)}}</div>
                                                            <li v-for="(list, j) in phy.physiotherapy_request_items">{{ list.costing_name }} </li>
                                                        </div>
                                                    </template>
                                                </div>
                                                <div v-else>-</div>
                                            </section>
                                            <section class="mt-12">
                                                <div class="font-weight-bold text-ax-primary-500">RESEP:</div>
                                                <template v-if="result?.prescription?.length > 0">
                                                    <div v-for="(ipre, i) in result.prescription" class="mt-8">
                                                        <div class="font-weight-bold">
                                                            <template>
                                                                <feather-icon v-if="ipre?.flag == 1" icon="CheckSquareIcon" color="green" v-b-tooltip.hover.left="`Diproses oleh farmasi`"/>
                                                                <img v-else-if="ipre?.flag == 0" src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`Berhasil diorder`">
                                                            </template>
                                                            {{localDateTime(ipre.date_added)}}
                                                        </div>
                                                        <div v-for="(list, idx_p) in ipre.form_prescription_parent" class="mt-1">
                                                            <div :class="getPrescParentVariant(list.id)" class="px-12 py-1">
                                                                <div class="font-weight-bold">{{ getPrescParentName(list.id) }}</div>
                                                                <div v-if="list.id != 1" class="fs-12 text-italic" style="font-weight: 400">
                                                                    <span>Kemasan: {{ getParentPackageName(list?.package) }}, </span>
                                                                    <span>
                                                                        {{ list?.value | number }}
                                                                        TIAP {{ getDynamicOptName(list?.signatura_times_id, signature_time)}}
                                                                        {{ getDynamicOptName(list?.administer_id, catalog_instruction) }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div v-if="ipre?.form_prescription" class="mt-1">
                                                                <li v-for="unit in ipre.form_prescription" v-if="list.id == unit.prescription_item_type_id">
                                                                    {{ unit?.pharmacy_catalog_name }}, {{ unit?.dose }} {{ getDynamicOptName(unit?.dose_unit_id, signature_units) }}
                                                                    <span v-if="unit?.prescription_item_type_id == 1">
                                                                        TIAP {{ getDynamicOptName(unit?.signatura_times_id, signature_time)}}
                                                                        {{ getDynamicOptName(unit?.administer_id, catalog_instruction) }}
                                                                    </span>
                                                                </li>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                                <div v-else>-</div>
                                            </section>
                                            <section class="mt-12">
                                                <div class="font-weight-bold text-ax-primary-500">ADVICE:</div>
                                                <div v-html="parsedBody(result?.advice || '-')"></div>
                                            </section>
                                        </section>
                                    </div>
                                </div>
                            </b-collapse>
                        </section>
                    </template>
                    <template v-if="load.limit_display < load.count">
                        <div class="d-flex justify-content-center align-items-center">
                            <div @click="loadMore" class="font-weight-bold pointer fs-15 text-center mt-16 py-1 load-text">
                                <a class="mr-2">Load more</a>
                                <feather-icon icon="ArrowDownIcon" size="17"/>
                            </div>
                        </div>
                    </template>
                </section>

                    <b-modal v-model="active_act.modal_SPRI" size="sm" no-close-on-backdrop>
                        <template #modal-header="{ close }">
                            <div class="d-flex justify-content-between w-100">
                                <div class="text-uppercase font-weight-bold fs-13 pr-16">Anda akan membuat SPRI untuk <span class="text-primary-500">{{ regData.alias_name }} {{ regData.name }}</span></div>
                                <feather-icon icon="XIcon" @click="close()" class="pointer"/>
                            </div>
                        </template>
                        <section>
                            <div>
                                <template v-if="form_spri.icd10?.length > 0">
                                    <b-table-simple bordered small>
                                        <b-tr class="fs-13">
                                            <b-th class="text-center" style="width: 40px;">No</b-th>
                                            <b-th>Diagnosis ICD10</b-th>
                                        </b-tr>
                                        <template v-for="(item,index) in form_spri.icd10">
                                            <b-tr class="fs-12">
                                                <b-td class="text-center">{{ index+1 }}</b-td>
                                                <b-td>{{ item.title }}</b-td>
                                            </b-tr>
                                        </template>
                                    </b-table-simple>
                                </template>
                                <div v-else class="fs-12 text-center bg-warning-200 text-neutral-900 p-2 w-100" style="font-style:italic">Belum ada diagnosis ICD10 yang ditambahkan pada asesmen dokter</div>
                            </div>

                            <div class="mt-8">
                                <label class="fs-13 mb-0">Indikasi Rawat Inap <span class="text-danger-500">*</span></label>
                                <v-select v-model="form_spri.indication" :options="OptionsIndication" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="pilih indikasi rawat inap" />
                            </div>
                            <div class="mt-8">
                                <label class="fs-13 mb-0">Rencana Tindakan <span class="text-danger-500">*</span></label>
                                <b-textarea v-model="form_spri.plan" size="sm" autocomplete="off"></b-textarea>
                            </div>
                            <div class="mt-8">
                                <label class="fs-13 mb-0">Keadaan Umum Pasien <span class="text-danger-500">*</span></label>
                                <v-select v-model="form_spri.condition" :options="OptionsCondition" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="pilih keadaan umum pasien" />
                            </div>
                            <div class="mt-8">
                                <label class="fs-13 mb-0">DPJP Utama <span class="text-danger-500">*</span></label>
                                <v-select v-model="form_spri.doctor_in_charge_id" :options="OptionsSpecialistDoctor" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="dpjp utama" />
                            </div>
                        </section>
                        <template #modal-footer>
                            <div class="d-flex justify-content-end">
                                <b-button :disabled="isDisabledSPRI" @click="validationSPRI" variant="primary" size="sm" class="px-24">Simpan</b-button>
                            </div>
                        </template>
                    </b-modal>

                    <b-modal v-model="active.modal_checkout" size="sm" no-close-on-backdrop>
                        <template #modal-header="{ close }">
                            <div class="d-flex justify-content-between w-100">
                                <div class="text-uppercase font-weight-bold fs-13 pr-16">Konfirmasi Selesai Pelayanan untuk <span class="text-primary-500">{{ regData.alias_name }} {{ regData.name }}</span></div>
                                <feather-icon icon="XIcon" @click="close()" class="pointer"/>
                            </div>
                        </template>
                        <div>
                            <label class="fs-13 mb-0">Kriteria Pasien <span class="text-danger">*</span></label>
                            <v-select v-model="form_checkout.type_emergency_id" aria-readonly="true" :options="OptionsTypeEmergency" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                        </div>
                        <div class="mt-8">
                            <label class="fs-13 mb-0">Jenis Kunjungan <span class="text-danger">*</span></label>
                            <v-select v-model="form_checkout.type_visit_id" aria-readonly="true" :options="OptionsTypeVisit" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                        </div>
                        <template v-if="form_checkout.type_visit_id == 2">
                            <div class="mt-8">
                                <label class="fs-13 mb-0">Tanggal Kontrol</label>
                                <b-form-input v-model="form_checkout.date_control" size="sm" type="date" />
                            </div>
                        </template>

                        <div class="mt-8">
                            <label class="fs-13 mb-0">Checkout <span class="text-danger">*</span></label>
                            <v-select v-model="form_checkout.exit_id" aria-readonly="true" :options="OptionsExit" :reduce="OptionsExit => OptionsExit.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                        </div>

                        <template #modal-footer>
                            <div class="d-flex justify-content-end">
                                <b-button @click="validationExit" :disabled="active.disabled_checkout" size="sm" variant="primary">Selesaikan Pelayanan</b-button>
                            </div>
                        </template>
                    </b-modal>

                    <b-modal v-model="active.modal_v2checkout" size="sm" no-close-on-backdrop>
                        <template #modal-header="{ close }">
                            <div class="d-flex justify-content-between w-100">
                                <div class="text-uppercase font-weight-bold fs-13 pr-16">Anda akan melakukan checkout untuk <span class="text-primary-500">{{ regData.alias_name }} {{ regData.name }}</span></div>
                                <feather-icon icon="XIcon" @click="close()" class="pointer"/>
                            </div>
                        </template>
                        <div>
                            <label class="fs-13 mb-0">Kriteria Pasien <span class="text-danger">*</span></label>
                            <v-select v-model="form_v2checkout.type_emergency_id" aria-readonly="true" :options="OptionsTypeEmergency" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                        </div>
                        <div class="mt-8">
                            <label class="fs-13 mb-0">Jenis Kunjungan <span class="text-danger">*</span></label>
                            <v-select v-model="form_v2checkout.type_visit_id" aria-readonly="true" :options="OptionsTypeVisit" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                        </div>
                        <div class="mt-8">
                            <label class="fs-13 mb-0">Checkout <span class="text-danger">*</span></label>
                            <v-select v-model="form_v2checkout.exit_id" aria-readonly="true" :options="OptionsExit" :reduce="OptionsExit => OptionsExit.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                        </div>

                        <template #modal-footer>
                            <div class="d-flex justify-content-end">
                                <b-button @click="validationIgdCheckout" :disabled="active.disabled_v2checkout" size="sm" variant="primary">CHECKOUT SEKARANG</b-button>
                            </div>
                        </template>
                    </b-modal>


                <!-- <KardexHistory ref="kardexRef" /> -->
                <SupportExaminationView ref="supportExamRef"/>
                <LabResultModalView ref="labModalResult"/>
                <RadResultModalView ref="radModalResult"/>
                <AddingCpptView ref="childCpptRef" @refreshAssessment="soapRegistration" @prescChildTemplate="fillPrescTemplate" @refreshConsult="refreshFuncConsult"/>
                <AddingSbarView ref="childSbarRef" @refreshAssessment="soapRegistration" @prescChildTemplate="fillPrescTemplate" />
            </section>
        </Layout>
    </div>
</template>

<script>
import { mapState } from 'vuex'
import dateMixin from "@/mixins/dateMixin";
import optionMixin from '@/mixins/optionMixin';
// import moment from 'moment'

// import io from 'socket.io-client';

import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import RadResultModalView from '../medical_support/RadResultModalView.vue';
import LabResultModalView from '../medical_support/LabResultModalView.vue';

import OutpatientStudies from '@/views/layouts/patient/doctor/OutpatientStudies.vue';
import EmergencyStudies from '@/views/layouts/patient/doctor/EmergencyStudies.vue';
import InpatientStudies from '@/views/layouts/patient/doctor/InpatientStudies.vue';
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import SupportHistoryView from '../medical_support/SupportHistoryView.vue';
import SupportBookingView from '../medical_support/SupportBookingView.vue';
import administration from '@/views/layouts/patient/doctor/administration.vue';
import SupportExaminationView from '../medical_support/SupportExaminationView.vue';
import AddingCpptView from '@/views/pages/patient/cppt/AddingCpptView.vue';
import OrderProcedureView from '../procedure/OrderProcedureView.vue';
import ListCpptView from '../cppt/ListCpptView.vue';
import AddingSbarView from '@/views/pages/patient/sbar/AddingSbarView.vue';
import ListSBARView from '../sbar/ListSBARView.vue';
import AddResumeView from '../resume/AddResumeView.vue';
import ConsultationView from '@/views/pages/patient/consultation/ConsultationView.vue'
import OrderOk from '@/views/pages/patient/OK/OrderOk.vue'
import OrderToolView from '../tool/OrderToolView.vue';
// import KardexHistory from '@/views/pages/patient/kardex/KardexHistoryView.vue'

export default {
    mixins: [dateMixin, optionMixin],
    name: 'DoctorStudies',
    components: {
        OutpatientStudies,
        EmergencyStudies,
        InpatientStudies,
        patientMenu,
        SupportHistoryView,
        SupportBookingView,
        administration,
        SupportExaminationView,
        RadResultModalView,
        LabResultModalView,
        AddingCpptView,
        ListCpptView,
        AddingSbarView,
        OrderProcedureView,
        ListSBARView,
        AddResumeView,
        ConsultationView,
        OrderOk,
        OrderToolView,
        // KardexHistory,
    },
    data() {
        return {
            // socket : io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"],withCredentials: true }),
            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            hfi_outpatient: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
            hfi_emergency: '444b3134-5b75-4b67-af72-a558d46716d7',
            hfii_hemodialysis: 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e',
            hfii_rehabilitation: '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a',
            hfii_physiotherapy: 'd350e55d-6f8a-4c42-b269-cfda717fbf8b',
            hfii_mcu: 'd46e2eb6-85a9-4bd4-b515-a0858cc50c8f',
            hfii_orthopedi: '820af6c1-0c58-45ae-afb2-990360f9e132',
            hfii_pulmo: '6292975d-65c5-4d7d-b7ca-1ef85b30c286',
            hfii_eye: 'bbdf4d7d-3255-4753-8292-70b836e019f4',

            userData: JSON.parse(localStorage.getItem('userData')),
            isDisabledSPRI: false,

            soapList: {
                byRegistration: [],
                exceptRegistration: [],
            },

            load: {
                count: 0,
                limit_display: 4,
                add_display: 4,
            },

            supportPackage: {
                lab_package_list: [],
                rad_package_list: [],
            },

            doctorList: [],
            doctorSpecialistList: [],
            procedureParams: {
                offset: 0,
                limit: 20,
                registration_id: '',
                norm: ''
            },

            procedureTable: {
                pageOptions: [20, 50, 100],
                currentPage: 1,
                perPage: 20,
                firstRows: 1,
                lastRows: 20,
                fields: [
                    { key: 'action', label: 'Aksi', thStyle: { width: '100px' } },
                    { key: 'date_added', label: 'Tanggal & Jam' },
                    { key: 'costing_name', label: 'Tindakan'},
                    { key: 'var_2', label: 'Jumlah'},
                    { key: 'doctor_in_charge_name', label: 'Dokter'},
                    { key: 'notes', label: 'Keterangan'},
                ],
            },

            reg_des_id: '',
            form_spri: {
                id:'',
                soap_id:'',
                doctor_in_charge_id:'',
                condition: 1,
                indication:'',
                icd10:[],
                registration_destination_id: '',
                plan:'',
            },
            active: {
                booking_view: 'ADD',
                editAccess: {
                    laboratory: true,
                    radiology: true,
                    // physiotherapy: true,
                },
                modal_checkout: false,
                disabled_checkout: false,
                disabled_booking_date: false,
                advance: {
                    assessment: false,
                },
                ready_status: {
                    registration: false,
                    sPast: false,
                    sNow: false,
                    laboratory: false,
                    radiology: false,
                    signTime: false,
                    signDose: false,
                    catInstruction: false,
                    signUnits: false,
                    // hCppt: false,
                    // hNurse: false,
                    // hSoap: false,
                    // physiotherapy: false,
                    // costing: false,
                },
                modal_v2checkout: false,
                disabled_v2checkout: false,
            },
            urlbe:process.env.VUE_APP_IMAGE,
            active_act: {
                modal_booking: false,
                modal_SPRI: false,
                modal_IGD: false,
                modal_procedure: false,
                modal_lab_igd: false,
                modal_rad_igd: false,
                modal_presc_igd: false,
            },
            paramsInsuranceServiceClassDoctor:{},
            regData: {},
            searchlab: '',
            searchrad: '',
            searchphy: '',
            OptionsIcd10:[],
            icd10_id: '',
            isDisable: false,
            OptionsAnamesaType:[
                { value: 1, title: 'Auto Anamnesa' },
                { value: 2, title: 'Allo Anamnesa' }
            ],
            OptionsFamilyRelation:[
                { value: 1, title: 'Ayah' },
                { value: 2, title: 'Ibu' },
                { value: 3, title: 'Kakak' },
                { value: 4, title: 'Adik' },
            ],

            // Checkout
            form_checkout: {
                type_emergency_id: '',
                type_visit_id: 2,
                exit_id: '807ca005-5827-4e19-9c97-0bc3d4e924ca',
                date_control: null
            },
            form_v2checkout: {
                type_emergency_id: '',
                type_visit_id: 2,
                exit_id: '807ca005-5827-4e19-9c97-0bc3d4e924ca',
            },
            OptionsExit:[
                { value: '807ca005-5827-4e19-9c97-0bc3d4e924ca', title: 'Atas Persetujuan Dokter' },
                { value: '4a6d28c4-e020-425f-91e1-d39699cf679b', title: 'Meninggal' },
                { value: 'a1631acc-6a94-414d-95de-a9faea0f1c2f', title: 'Dirujuk' },
                { value: 'ae0aabfa-1600-443f-8a60-62f9e76b1608', title: 'Permintaan Sendiri' },
                { value: 'dfe2b9c6-5934-4748-aad9-6d19c957ecfd', title: 'Kabur' },
                { value: '03e783b8-92ef-40c2-a867-d5895ffbfc83', title: 'Poliklinik' },
                { value: 'f359a4b5-4406-49d7-9a81-49bf34b4c21a', title: 'Sembuh' },
                { value: '2542a5f7-570a-449c-9eee-525569c0ce1e', title: 'Rawat Inap' },
            ],

            // templating
            catalog_list: [],
            presc_template_list: [],
            anamn_template_list: [],

            // OK
            operatingItems: [],
            OKfields: [
                { key: 'action', label: 'Aksi', thStyle: { minWidth: '140px' } },
                { key: 'no_order', label: 'Kode Pendaftaran', thStyle: { minWidth: '140px' } },
                { key: 'name', label: 'Nama', thStyle: { minWidth: '140px' } },
                { key: 'norm', label: 'No RM', thStyle: { minWidth: '100px' } },
                { key: 'operation_type_id', label: 'Jenis Operasi', thStyle: { minWidth: '140px' } },
                { key: 'operation_date', label: 'Tanggal Request', thStyle: { minWidth: '140px' } },
                { key: 'operation_approved_date', label: 'Rencana Operasi', thStyle: { minWidth: '140px' } },
                { key: 'room', label: 'Ruang Operasi', thStyle: { minWidth: '140px' } },
                { key: 'approved', label: 'Disetujui Oleh', thStyle: { minWidth: '140px' } },
            ],
            okTable: {
                pageOptions: [10, 20, 50, 100],
                currentPage: 1,
                perPage: 10,
                totalRows: 0,
                firstRows: 1,
                lastRows: 10,
            },
            okParams: {
                limit: 10,
                offset: 0,
                // registration_destination_id: '',
                // registration_id: '',
                norm: '',
            },

            // RoomHistory
            operatingItems: [],
            roomHistoryfields: [
                { key: 'room_name', label: 'Ruang', thStyle: { minWidth: '140px' } },
                { key: 'doctor_in_charge_id', label: 'DPJP', thStyle: { minWidth: '140px' } },
            ],
            roomHistoryTable: {
                pageOptions: [10, 20, 50, 100],
                currentPage: 1,
                perPage: 10,
                totalRows: 0,
                firstRows: 1,
                lastRows: 10,
            },
            roomHistoryParams: {
                limit: 10,
                offset: 0,
                registration_destination_id: '',
                // registration_id: '',
                norm: '',
            },

            // ============================
            toolParams: {
                offset: 0,
                limit: 10,
                lastRows: 10,
                registration_id: ''
            },

            toolTable: {
                pageOptions: [10, 20, 50, 100],
                currentPage: 1,
                perPage: 10,
                firstRows: 1,
                lastRows: 10,
                fields: [
                    { key: 'action', label: 'Aksi', thStyle: { width: '100px' } },
                    { key: 'date_added', label: 'Tanggal & Jam' },
                    { key: 'costing_name', label: 'Tindakan'},
                    { key: 'var_2', label: 'Jumlah'},
                    { key: 'notes', label: 'Keterangan'},
                ],
            },

            mergedOperatingData: [],
            RoomHistoryData: [],
            // ============================

            signature_time: [],
            signature_dose: [],
            catalog_instruction: [],
            signature_units: [],
        }
    },

    watch: {
        "form_checkout.type_visit_id"(newValue) {
            if (newValue != 2) {
                this.form_checkout.date_control = ''
            }
        },

        // doctor-procedure-transaction
        "procedureTable.currentPage": {
            handler: function(value) {
                this.procedureTable.firstRows = this.procedureTable.perPage*(value-1)+1
                this.procedureTable.lastRows = this.procedureTable.perPage*(value)
                this.procedureParams.offset = this.procedureTable.perPage*(value-1)
                this.procedureParams.limit = this.procedureTable.perPage
                this.getProcedureList()
            }
        },
        "procedureTable.perPage": {
            handler: function(value) {
                this.procedureParams.limit = this.procedureTable.perPage
                this.procedureParams.lastRows = this.procedureTable.perPage
                this.getProcedureList()
            }
        },

        // ok
        "okTable.currentPage": {
            handler: function(value) {
                this.okTable.firstRows = this.okTable.perPage*(value-1)+1
                this.okTable.lastRows = this.okTable.perPage*(value)
                this.okParams.offset = this.okTable.perPage*(value-1)
                this.okParams.limit = this.okTable.perPage
                this.getOperatingList()
            }
        },
        "okTable.perPage": {
            handler: function(value) {
                this.okParams.limit = this.okTable.perPage
                this.okParams.lastRows = this.okTable.perPage
                this.getOperatingList()
            }
        },

        // room history
        "roomHistoryTable.currentPage": {
            handler: function(value) {
                this.roomHistoryTable.firstRows = this.roomHistoryTable.perPage*(value-1)+1
                this.roomHistoryTable.lastRows = this.roomHistoryTable.perPage*(value)
                this.roomHistoryParams.offset = this.roomHistoryTable.perPage*(value-1)
                this.roomHistoryParams.limit = this.roomHistoryTable.perPage
                this.getOperatingList()
            }
        },
        "roomHistoryTable.perPage": {
            handler: function(value) {
                this.roomHistoryParams.limit = this.roomHistoryTable.perPage
                this.roomHistoryParams.lastRows = this.roomHistoryTable.perPage
                this.getOperatingList()
            }
        },

        // tool-transaction
        "toolTable.currentPage": {
            handler: function(value) {
                this.toolTable.firstRows = this.toolTable.perPage*(value-1)+1
                this.toolTable.lastRows = this.toolTable.perPage*(value)
                this.toolParams.offset = this.toolTable.perPage*(value-1)
                this.toolParams.limit = this.toolTable.perPage
                this.getToolList()
            }
        },
        "toolTable.perPage": {
            handler: function(value) {
                this.toolParams.limit = this.toolTable.perPage
                this.toolParams.lastRows = this.toolTable.perPage
                this.getToolList()
            }
        },

    },

    filters:{
        number(value) {
            return Number(value)
        },
    },

    computed: {
        ...mapState({
            //SPRI
            OptionsIndication: state => state.frontoptions.general.spri.indication,
            OptionsCondition: state => state.frontoptions.general.spri.condition,

            // Checkout
            OptionsTypeVisit: state => state.frontoptions.general.checkout.type_visit,
            OptionsTypeEmergency: state => state.helper.options.typeEmergency,

            // doctor-procedure-transaction
            procedureItems: state => state.soap.procedure.items,
            procedureRows: state => state.soap.procedure.totalRows,
            TypeCostingOptions: state => state.helper.options.typecostingc,

            // tool-transaction
            toolItems: state => state.tool.tool.items,
            toolRows: state => state.tool.tool.totalRows,

            items: state => state.soap.items,
            OptionsSpecialistDoctor: state => state.employee.options.specialist,
            itemsPhysiotherapy: state => state.physiotherapy.package.items,
            OptionsCosting: state => state.costing.options.costing,
            PackageOptions: state => state.packages.options.packages,
        }),
    },

    methods: {
        async mergeOperatingWithReport() {
            this.mergedOperatingData = [];

            try {
                for (let i = 0; i < this.operatingItems.length; i++) {
                    const operatingItem = this.operatingItems[i];
                    const okId = operatingItem.id;


                    const reportResponse = await this.$store.dispatch(
                        'operatie/getDetailOperativeReportById',
                        { ok_id: okId }
                    );

                    const operativeReport = Array.isArray(reportResponse) && reportResponse.length > 0
                        ? reportResponse[0]
                        : null;

                    const mergedItem = {
                        ...operatingItem,
                        operativeReport: operativeReport
                    };

                    this.mergedOperatingData.push(mergedItem);
                }

                // console.log('Merged Operating Data:', this.mergedOperatingData);

            } catch (error) {
                console.error('Error merging data:', error);
            }
        },
        getOperatingList() {
            // this.okParams.registration_destination_id = this.regData.registration_destination_id
            // this.okParams.registration_id = this.regData.registration_id
            this.okParams.norm = this.regData.norm
            // console.log('this.okParams', this.okParams);
            try {
                this.$store.dispatch('operatie/getAllList', this.okParams).then(async response => {
                    this.operatingItems = response?.rows
                    this.okTable.totalRows = response?.iTotal
                    // console.log('this.operatingItems:', this.operatingItems);
                    // console.log('this.okTable:', this.okTable);

                    await this.mergeOperatingWithReport();
                }, error => {
                    console.error('Error while operatie/getAllList at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET operatie/getAllList at: ', error);
            }
        },
        
        getRoomHistoryList() {
            this.roomHistoryParams.registration_destination_id = this.regData.registration_destination_id
            try {
                this.$store.dispatch('room/getRoomHistory', this.roomHistoryParams).then(async response => {
                    this.RoomHistoryData = response?.rows
                    this.roomHistoryTable.totalRows = response?.iTotal
                    // console.log('this.operatingItems:', this.operatingItems);
                    // console.log('this.okTable:', this.okTable);

                }, error => {
                    console.error('Error while operatie/getAllList at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET operatie/getAllList at: ', error);
            }
        },

        isReady() {
            const { ready_status } = this.active;
            return ready_status.registration &&
                    ready_status.sPast &&
                    ready_status.sNow &&
                    ready_status.laboratory &&
                    ready_status.radiology &&
                    ready_status.signTime &&
                    ready_status.signDose &&
                    ready_status.catInstruction &&
                    ready_status.signUnits
        },

        getMeaningVariant(value) {
            if (value && value != 1) return 'text-danger-500'
            return ''
        },

        getAssesToggleName(hfi) {
            if (hfi == this.hfi_inpatient) return 'ASESMEN AWAL RANAP'
            return 'ASESMEN'
        },

        handleCheckoutTrigger(data) {
            // console.log("Data dari child:", data);
            this.active.modal_checkout = !this.active.modal_checkout
        },
        isSimilar(reg_des_id) {
            if(this.regData.registration_destination_id == reg_des_id) {
                return 'border: 2px solid #03a972;'
            }
            return ''
        },
        triggerAdministrationModal(obj) {
            const message = obj
            this.$refs.administrationRef.parentTrigger(message);
        },
        triggerSupportBookingModal(obj) {
            const message = obj
            this.$refs.supportBooking.parentTrigger(message);
        },
        triggerCancelSupportBooking(obj) {
            const message = obj
            this.$refs.supportBooking.cancelTrigger(message);
        },
        triggerSupportHistoryModal(obj) {
            const message = obj
            this.$refs.supportHistory.parentTrigger(message);
        },

        triggerOutpatientModal(gate) {
            const message = {
                gate: gate,
                presc_template: this.presc_template_list,
                anamn_template: this.anamn_template_list,
                regData: this.regData,
                lab_list: this.supportPackage?.lab_package_list,
                rad_list: this.supportPackage?.rad_package_list,
                phy_list: this.itemsPhysiotherapy,
                costing_list: this.OptionsCosting,
                catalog_list: this.catalog_list,
                stock_list: this.PackageOptions,
                signature_time: this.signature_time,
                signature_dose: this.signature_dose,
                signature_units: this.signature_units,
                catalog_instruction: this.catalog_instruction,
            }
            this.$refs.childOutRef.parentTrigger(message);
        },
        triggerInpatientModal(gate) {
            const message = {
                gate: gate,
                presc_template: this.presc_template_list,
                anamn_template: this.anamn_template_list,
                regData: this.regData,
                lab_list: this.supportPackage?.lab_package_list,
                rad_list: this.supportPackage?.rad_package_list,
                phy_list: this.itemsPhysiotherapy,
                costing_list: this.OptionsCosting,
                catalog_list: this.catalog_list,
                stock_list: this.PackageOptions,
                signature_time: this.signature_time,
                signature_dose: this.signature_dose,
                signature_units: this.signature_units,
                catalog_instruction: this.catalog_instruction,
            }
            this.$refs.childInRef.parentTrigger(message);
        },

        triggerEmergencyModal() {
            const message = {
                gate: 'main_gate',
                regData: this.regData,
                lab_list: this.supportPackage?.lab_package_list,
                rad_list: this.supportPackage?.rad_package_list,
                costing_list: this.OptionsCosting,
                catalog_list: this.catalog_list,
                stock_list: this.PackageOptions,
                doctor_list: this.doctorSpecialistList,
                signature_time: this.signature_time,
                signature_dose: this.signature_dose,
                signature_units: this.signature_units,
                catalog_instruction: this.catalog_instruction,
            }
            this.$refs.childEmeRef.parentTrigger(message);
        },


        triggerCpptModal(obj) {
            // console.log('masuk sini');
            const message = {
                gate: obj.gate,
                registration_destination_id: obj.registration_destination_id,
                regData: this.regData,
                costing_list: this.OptionsCosting,
                lab_list: this.supportPackage?.lab_package_list,
                rad_list: this.supportPackage?.rad_package_list,
                phy_list: this.itemsPhysiotherapy,
                catalog_list: this.catalog_list,
                stock_list: this.PackageOptions,
                presc_template: this.presc_template_list,
                anamn_template: this.anamn_template_list,
                signature_time: this.signature_time,
                signature_dose: this.signature_dose,
                signature_units: this.signature_units,
                catalog_instruction: this.catalog_instruction,
                cppt_id: obj?.cppt_id || null,
                triggerFrom: obj?.triggerFrom || null,
                // triggerFrom: obj?.triggerFrom ?? this.isConsultant(),
            }
            // console.log('triger cppt di dr-stds: ', message);
            this.$refs.childCpptRef.parentTrigger(message);
        },

        async isAllowedAddCppt(reg_des_id) {
            this.$nextTick(async () => {
                if (reg_des_id != this.regData.registration_destination_id) return false

                let general_dr_position = 'd93bd445-df1e-451a-bf95-5fc10546c133'
                let dr_group_id = 'efb3fdff-fbe3-4954-a391-3729e544b62a'
                let is_specialist = this.userData.user_group.id == dr_group_id && this.userData.position_id !== general_dr_position
                if (!is_specialist) return true

                const user_id = this.userData.employee_id
                const response = await this.$store.dispatch('consultation/getListConsultation', {registration_destination_id: this.regData?.registration_destination_id})
                let consultation_rows = response?.rows || []

                const consult_type3 = consultation_rows.some(item => item.consultant_id == user_id && item.consultation_type_id == 3 && item.status == 0 && item.cppt_id != null)
                return !consult_type3
            })
        },

        // isConsultant() {
        //     const user_id = this.userData.employee_id
        //     let general_dr_position = 'd93bd445-df1e-451a-bf95-5fc10546c133'
        //     let dr_group_id = 'efb3fdff-fbe3-4954-a391-3729e544b62a'
        //     let is_specialist = this.userData.user_group.id == dr_group_id && this.userData.position_id !== general_dr_position
        //     let doctor_ids = []

        //     if (is_specialist) {
        //         for (let index = 2; index <= 8; index++) {
        //             const el = this.regData[`doctor_in_charge_id_${index}`]
        //             doctor_ids.push(el)
        //             // console.log(`el ${index}: `, el);
        //         }
        //         return doctor_ids.some(val => val == user_id) ? 'consultation' : null
        //     }
        //     return null
        // },

        triggerSbarModal(obj) {
            const message = {
                gate: obj.gate,
                registration_destination_id: obj.registration_destination_id,
                regData: this.regData,
                lab_list: this.supportPackage?.lab_package_list,
                rad_list: this.supportPackage?.rad_package_list,
                phy_list: this.itemsPhysiotherapy,
                catalog_list: this.catalog_list,
                stock_list: this.PackageOptions,
                presc_template: this.presc_template_list,
                anamn_template: this.anamn_template_list,
                signature_time: this.signature_time,
                signature_dose: this.signature_dose,
                signature_units: this.signature_units,
                catalog_instruction: this.catalog_instruction,
            }
            this.$refs.childSbarRef.parentTrigger(message);
        },
        triggerResumeModal(obj){
            const message = obj
            this.$refs.childResumeRef.parentTrigger(message, 'add_gate');
        },

        triggerConsultModal(){
            const message = {
                regData: this.regData,
                doctor_list: this.doctorSpecialistList
            }
            this.$refs.childConsultRef.parentTrigger(message);
        },

        refreshFuncConsult(message) {
            // console.log('masuk ke refreshFuncConsult di drstds');
            this.$refs.childConsultRef.getConsultation();
        },

        triggerOperatieModal(obj){
            const message = {
                gate: obj?.gate,
                rowData: obj?.rowData,
                regData: this.regData,
                doctor_list: this.doctorList,
            }
            this.$refs.childOperatieRef.parentTrigger(message);
        },

        // getOutpatientCondition(doctor_in_charge_id, health_facility_id, is_resume_exit, is_booking) {
        //     if ((doctor_in_charge_id == this.userData.employee_id || this.userData.employee_id == 'd77142c4-4777-47d8-bd0c-fd7dfd134af4') && health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6' && is_resume_exit != 1 && is_booking != 1) {
        //         return true
        //     }
        //     return false
        // },
        // getInpatientCondition(doctor_in_charge_id, health_facility_id, is_initial_inpatient) {
        //     if ((doctor_in_charge_id == this.userData.employee_id || this.userData.employee_id == 'd77142c4-4777-47d8-bd0c-fd7dfd134af4') && health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f' && is_initial_inpatient == 1) {
        //         return true
        //     }
        //     return false
        // },
        validationExit() {
            let params = {
                checkout: {
                    exit_id: this.form_checkout.exit_id,
                    type_visit_id: this.form_checkout.type_visit_id,
                    type_emergency_id: this.form_checkout.type_emergency_id,
                    date_control: this.form_checkout.date_control
                },
                registration_destination_id: this.reg_des_id,
            }

            try {
                this.active.disabled_checkout = true
                this.$store.dispatch('registration/checkoutDoctor', params).then(response => {
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.getRegDetail(this.regData.registration_destination_id)
                    this.active.modal_checkout = false

                    this.updateBpjsStatus()
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error("Error at: ", error)
            } finally {
                this.active.disabled_checkout = false
            }
        },

        validationIgdCheckout() {
            let params = {
                checkout: {
                    exit_id: this.form_v2checkout.exit_id,
                    type_visit_id: this.form_v2checkout.type_visit_id,
                    type_emergency_id: this.form_v2checkout.type_emergency_id,
                },
                registration_destination_id: this.reg_des_id,
            }

            try {
                this.active.disabled_v2checkout = true
                this.$store.dispatch('registration/checkoutV2', params).then(response => {
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.getRegDetail(this.regData.registration_destination_id)
                    this.active.modal_v2checkout = false
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error("Error at: ", error)
            } finally {
                this.active.disabled_v2checkout = false
            }
        },

        updateBpjsStatus() {
            let param = {
                registration_destination_id: this.regData.registration_destination_id
            }

            try {
                this.$store.dispatch('soap/putBpjsStatus', param)
                .then(
                    res => {
                        this.getRegDetail(this.regData.registration_destination_id)
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                console.error('Error catch at: ', error)
            }
        },

        getSoapLabel(unit, hfin, initial, booking) {
            if (unit == this.hfi_outpatient && booking == 1) {
                return 'BOOKING PENUNJANG.'
            } else if (unit == this.hfi_inpatient && initial != 1) {
                return 'CPPT DOKTER RAWAT INAP'
            } else {
                return hfin
            }
        },

        getSupportPackage() {
            const company_bpjs = '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8'
            const company_bpjstk = 'bb0d633f-99e1-4990-901b-0417a14e3cb6'
            const company_umum = 'e5a1e978-af77-4901-9ef8-4ebaeee77dfa'
            const service_non_class = 'e7ce65ba-3d51-41aa-add7-df8f6a40e2b4'
            const active_company = this.regData.registration_insurance[0].company_id
            const active_service_class = this.regData.registration_insurance[0].service_class_id

            let param = {
                service_class_id: '',
                company_insurance_id: '',
            }

            param.company_insurance_id = active_company == company_bpjs || active_company == company_bpjstk ? active_company : company_umum
            param.service_class_id = this.regData.health_facility_id == this.hfi_inpatient ? active_service_class : service_non_class

            try {
                this.$store.dispatch('laboratory/getLaboratoryPackageV2', param).then((response) => {
                    this.supportPackage.lab_package_list = response
                    this.active.ready_status.laboratory = true
                })

                this.$store.dispatch('radiology/getRadiologyPackageV2', param).then((response) => {
                    this.supportPackage.rad_package_list = response
                    this.active.ready_status.radiology = true
                })

                this.$store.dispatch('physiotherapy/getPackageAll', param)

            } catch (error) {
                console.error('Error while GET data support_package at: ', error)
            }
        },

        async getData(id) {
			await this.$store.dispatch('registration/getById', id).then(async data => {
                this.regData = data[0]
                this.active.ready_status.registration = true

                this.form_checkout.type_emergency_id = this.regData.type_emergency_id || 5
                this.form_checkout.type_visit_id = this.regData.type_visit_id || 2
                this.form_checkout.exit_id = this.regData.exit_id || '807ca005-5827-4e19-9c97-0bc3d4e924ca'

                this.getAllAssesment()

                // doctor procedure-transaction
                this.getProcedureList()

                 // doctor tool-transaction
                this.getToolList()

                // list-ok
                this.getOperatingList()

                // list-room-history
                this.getRoomHistoryList()

                let reg_ins = this.regData.registration_insurance
                if (this.regData.icare_status != 1 && reg_ins) {
                    for (let i = 0; i < reg_ins.length; i++) {
                        const el = reg_ins[i];
                        if (el.is_primary == 1 && el.company_id == '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8') {
                            this.openIcare(el.number_insurance)
                        }
                    }
                }

                this.getSupportPackage()
                this.getTransactionOptions()
            })
        },

        getAllAssesment() {
            this.soapRegistration()
            this.soapNotRegistration()
        },

        soapRegistration() {
            let only_reg = {
                registration_id: this.regData.registration_id,
                limit: 3,
                offset: 0,
            }

            try {
                this.$store.dispatch('soap/getSoapList', only_reg).then((response) => {
                    this.soapList.byRegistration = response.rows
                    this.active.ready_status.sNow = true
                    // console.log('this.soapList.byRegistration: ', this.soapList.byRegistration);
                }, error => {
                    console.error('Error while soap/getSoapList at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET soap/getSoapList at: ', error);
            }
        },

        soapNotRegistration() {
            let except_reg = {
                norm: this.regData.norm,
                not_registration_id: this.regData.registration_id,
                limit: this.load.limit_display,
                offset: 0,
            }
            // console.log('except_reg:', except_reg);
            try {
                this.$store.dispatch('soap/getSoapList', except_reg).then((response) => {
                    this.soapList.exceptRegistration = response.rows
                    this.load.count = response.iTotal
                    this.active.ready_status.sPast = true
                    // console.log('this.soapList.exceptRegistration: ', this.soapList.exceptRegistration);
                    // console.log('this.load: ', this.load);
                }, error => {
                    console.error('Error while soap/getSoapList at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET soap/getSoapList at: ', error);
            }
        },

        loadMore() {
            this.load.limit_display += this.load.add_display
            this.soapNotRegistration()
            // console.log('limit:' + this.load.limit_display + ' ---- album.length ' + this.load.count);
        },

        openIcare(number_insurance) {
            let param = {
                number_insurance: !number_insurance || number_insurance != '-' ? number_insurance : this.regData.idcard,
                bride_bpjs: this.regData.bride_bpjs
            }

            this.$store.dispatch('patient/getIcare', param)
            .then(res => {
                // console.log('res', res);

                if (res?.metaData?.code == 200) {
                    const url = res?.response?.url

                    const width = 600;
                    const height = 400;
                    const left = (screen.width - width) / 2;
                    const top = (screen.height - height) / 2;

                    const windowFeatures = `width=${width},height=${height},left=${left},top=${top},resizable=yes,scrollbars=yes`;
                    window.open(url, '_blank', windowFeatures);

                    if (this.regData.icare_status != 1) {
                        this.updateIcareStatus()
                    }
                }
            })
        },

        updateIcareStatus() {
            try {
                this.$store.dispatch('patient/putIcareStatus', this.regData.registration_destination_id)
                .then(
                    res => {
                        this.getRegDetail(this.regData.registration_destination_id)
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('Error response: ', message)
                })
            } catch (error) {
                console.error('Error catch at: ', error)
            }
        },

        getRegDetail(regDesId) {
            // console.log('regDesId: ', regDesId);
            this.$store.dispatch('registration/getById', regDesId).then(data => {
                this.regData = data[0]
            })
        },

        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        openModalSPRI(){
            this.active_act.modal_SPRI = true
            this.handlingSPRI()
        },

        handlingSPRI() {
            let regDesId = this.reg_des_id

            const params = {
                registration_destination_id: regDesId,
                is_booking: 0
            }
            // this.$store.dispatch('soap/getByRegId', regDesId).then((response) => {
            this.$store.dispatch('soap/getDetail', params).then((response) => {
                const obj = response[0]

                if (response.length > 0) {
                    this.$store.dispatch('soap/getInpatientByRegId', regDesId).then((response_inpatient)=>{
                        let res = response_inpatient[0]
                        if (response_inpatient.length > 0) {
                            this.form_spri.indication = res.indication
                            this.form_spri.condition = res.condition || 1
                            this.form_spri.plan = res.plan
                            this.form_spri.doctor_in_charge_id = res.doctor_in_charge_id
                            this.form_spri.id = res.id
                            this.form_spri.registration_destination_id = res.registration_destination_id
                        }
                    })

                    this.form_spri.soap_id = obj.id
                    if (obj.icd10) {
                        const mappedIcd10 = obj.icd10.map(item => {
                            return {
                                icd10_id: item.icd10_id,
                                value: item.icd10_id,
                                title: `${item.code} ${item.name}`,
                                action: "action"
                            };
                        });
                        this.form_spri.icd10 = mappedIcd10;
                    }
                    this.form_spri.doctor_in_charge_id = this.regData.doctor_in_charge_id
                }
            })
        },

        async validationSPRI() {
            const fields = {
                indication: 'Indikasi rawat inap',
                plan: 'Rencana tindakan',
                condition: 'Keadaan Umum Pasien',
                doctor_in_charge_id: 'DPJP Utama',
            }

            let isEmpty = false;
            for (const [key, message] of Object.entries(fields)) {
                if (this.form_spri[key] === '' || this.form_spri[key] === null) {
                    this.notification('Warning','AlertTriangleIcon','warning', `${message} wajib diisi`)
                    isEmpty = true;
                }
            }

            if (!isEmpty) {
                this.form_spri.registration_destination_id ||= this.regData.registration_destination_id
                try {
                    this.isDisabledSPRI = true
                    await this.$store.dispatch('soap/formPostInPatient', this.form_spri)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.active_act.modal_SPRI = false
                            this.resetSPRI()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    console.error('Error at: ', error)
                } finally {
                    this.isDisabledSPRI = false
                }
            }
        },

        resetSPRI() {
            this.form_spri.id = ''
            this.form_spri.soap_id = ''
            this.form_spri.doctor_in_charge_id = ''
            this.form_spri.indication = ''
            this.form_spri.condition = 1
            this.form_spri.registration_destination_id = ''
            this.form_spri.plan = ''
        },

        getTransactionOptions() {
            const company_bpjs = '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8'
            const company_bpjstk = 'bb0d633f-99e1-4990-901b-0417a14e3cb6'
            const company_mi_mc = '315fe232-e034-4b45-bf0d-e8cbb1f04c1a'
            const company_mi_im = '8a322cf3-e135-4909-bc25-48e31adadb0e'
            const company_umum = 'e5a1e978-af77-4901-9ef8-4ebaeee77dfa'
            const inpatient_item_id = "d158a64e-6246-444a-97b7-8d0a2d90e53a"
            const active_company = this.regData.registration_insurance[0].company_id
            const active_service_class = this.regData.registration_insurance[0].service_class_id

            let param = {
                type_costing_id: 3,
                company_insurance_id: '',
                service_class_id: '',
                health_facility_item_id: '',
            }

            param.company_insurance_id = active_company == company_bpjs  || active_company == company_bpjstk || active_company == company_mi_mc || active_company == company_mi_im ? active_company : company_umum
            param.service_class_id = this.regData.health_facility_id == this.hfi_inpatient ? active_service_class : ''
            param.health_facility_item_id = this.regData.health_facility_id == this.hfi_inpatient ? inpatient_item_id : this.regData.health_facility_item_id

            this.$store.dispatch('costing/getParam', param)
        },

        getPrescCatalog() {
            try {
                this.$store.dispatch('catalog/getOptionCatalog').then((response) => {
                    this.catalog_list = response
                }, error => {
                    console.error('Error while GET catalog/getOptionCatalog at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET catalog/getOptionCatalog at: ', error);
            }
        },

        // ========================================
        triggerExamination(param) {
            const message = param
            this.$refs.supportExamRef.parentTrigger(message);
        },

        examinationCondition(obj) {
            if (obj?.health_facility_id == this.hfi_outpatient && obj?.is_booking != 1) return true
            if (obj?.health_facility_id == this.hfi_inpatient && obj?.is_initial_inpatient == 1) return true
            if (obj?.health_facility_id == this.hfi_emergency) return true
            return false
        },

        // ========================================
        triggerRadResult(message) {
            this.$refs.radModalResult.seeRadiologyResult(message);
        },

        triggerLabResult(message) {
            this.$refs.labModalResult.seeLaboratoryResult(message);
        },

        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },
        // ========================================
        getPrescTemplate() {
            try {
                this.$store.dispatch('templating/getPrescTemplate').then((response) => {
                    this.presc_template_list = response
                }, error => {
                    console.error('Error while GET templating/getPrescTemplate at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET templating/getPrescTemplate at: ', error);
            }
        },

        fillPrescTemplate(data){
            // console.log('masuk ke drstudies: ', data);
            this.presc_template_list = data
        },

        // ========================================
        getAnamTemplate() {
            try {
                this.$store.dispatch('templating/getAnamTemplate').then((response) => {
                    this.anamn_template_list = response
                }, error => {
                    console.error('Error while GET templating/getAnamTemplate at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET templating/getAnamTemplate at: ', error);
            }
        },

        fillAnamnTemplate(data){
            this.anamn_template_list = data
        },

        // ========================================
        getProcedureList() {
            this.procedureParams.registration_id = this.regData.registration_id
            this.$store.dispatch('soap/getDoctorProcedureStateList', this.procedureParams)
        },

        triggerProcedure(param) {
            const message = param
            this.$refs.procedureRef.parentTrigger(message);
        },

        editProcedure(obj){
            if (obj.registration_destination_id == this.regData.registration_destination_id) {
                this.triggerProcedure({ regData: this.regData, procedureData: obj, optDoctor: this.doctorList, optTyCosting: this.TypeCostingOptions, gate: 'edit_gate'})
            } else {
                this.$store.dispatch('registration/getById', obj.registration_destination_id)
                .then(response => {
                    let data = response[0]
                    this.triggerProcedure({ regData: data, procedureData: obj, optDoctor: this.doctorList, optTyCosting: this.TypeCostingOptions, gate: 'edit_gate'})
                })
            }
        },

        // =====================================================================================================
        getToolList() {
            this.toolParams.registration_id = this.regData.registration_id
            this.$store.dispatch('tool/getToolTransactionStateList', this.toolParams)
        },

        triggerTool(param) {
            const message = param
            this.$refs.toolRef.parentTrigger(message);
        },

        editTool(obj){
            if (obj.registration_destination_id == this.regData.registration_destination_id) {
                this.triggerTool({ regData: this.regData, toolData: obj, gate: 'edit_gate'})
            } else {
                this.$store.dispatch('registration/getById', obj.registration_destination_id)
                .then(response => {
                    let data = response[0]
                    this.triggerTool({ regData: data, toolData: obj, gate: 'edit_gate'})
                })
            }
        },

        // ========================================
        getAllDoctorList() {
            try {
                this.$store.dispatch('employee/getDoctorList', { is_specialist: 'all' }).then((response) => {
                    this.doctorList = response
                }, error => {
                    console.error('Error while GET employee/getDoctorList general at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET employee/getDoctorList general at: ', error);
            }
        },

        getSpecialistDoctorList() {
            try {
                this.$store.dispatch('employee/getDoctorList', { is_specialist: '1' }).then((response) => {
                    this.doctorSpecialistList = response
                }, error => {
                    console.error('Error while GET employee/getDoctorList general at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET employee/getDoctorList general at: ', error);
            }
        },
        openOKInNewTab(id) {
            const url = `/operatie-kamer/${id}`
            const fromPath = this.$route.fullPath;

            if (fromPath.startsWith('/patient/doctor/')) {
                const basePath = fromPath.split('/').slice(0, 3).join('/');
                localStorage.setItem('fromPath', basePath);
            }
            window.open(url, '_blank')
        },

        // =============================
        // triggerKardex() {
        //     const message = {
        //         regData: this.regData
        //     }
        //     this.$refs.kardexRef.parentTrigger(message);
        // },

        // =============================

        async getOptSignTime() {
            try {
                await this.$store.dispatch('kardex/getOptSignaturaTime')
                .then(response => {
                    this.signature_time = response
                    this.active.ready_status.signTime = true
                    // console.log('response: ', response);
                })
                .catch(({message, debug, response}) => {
                    console.error('Error at: ', response);
                })
            } catch (error) {
                console.error('Error catch at: ', error)
            }
        },

        async getOptSignDose() {
            try {
                await this.$store.dispatch('kardex/getOptSignaturaDose', {})
                .then(response => {
                    this.signature_dose = response
                    this.active.ready_status.signDose = true
                    // console.log('response: ', response);
                })
                .catch(({message, debug, response}) => {
                    console.error('Error at: ', response);
                })
            } catch (error) {
                console.error('Error catch at: ', error)
            }
        },

        async getOptCatInstruction() {
            try {
                await this.$store.dispatch('catalog/getOptCatalogInstruction')
                .then(response => {
                    this.catalog_instruction = response
                    this.active.ready_status.catInstruction = true
                    // console.log('response: ', response);
                })
                .catch(({message, debug, response}) => {
                    console.error('Error at: ', response);
                })
            } catch (error) {
                console.error('Error catch at: ', error)
            }
        },

        async getSignaturaUnits() {
            try {
                await this.$store.dispatch('kardex/getOptUnit', {})
                .then(
                    async response => {
                        this.signature_units = response
                        this.active.ready_status.signUnits = true
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('Error GET kardex/getOptUnit at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET kardex/getOptUnit at: ', error);
            }
        },
    },
    created () {
        this.$store.dispatch('helper/getParam', 'type_room')
        this.$store.dispatch('employee/getParamAllSpecialist')
        this.$store.dispatch('employee/getParam', '')
        this.$store.dispatch('packages/getParam')
        this.$store.dispatch('helper/getParam', 'costing_c_one')
        this.getPrescTemplate()
        this.getPrescCatalog()
        this.getAnamTemplate()
        this.getAllDoctorList()
        this.getSpecialistDoctorList()
        this.getOptSignTime()
        this.getOptSignDose()
        this.getOptCatInstruction()
        this.getSignaturaUnits()
        // this.$store.dispatch('laboratory/getPackageAll', { type_costing_id: 4 })
        // this.$store.dispatch('radiology/getPackageAll')

        if (this.$route.params.id) {
            this.reg_des_id = this.$route.params.id
            this.regData = {}
            this.soapList.byRegistration = []
            this.soapList.exceptRegistration = []
            this.supportPackage.lab_package_list = []
            this.supportPackage.rad_package_list = []
            // this.OptionsCosting = []
            setTimeout(() => {
                this.getData(this.$route.params.id)
            }, 300);
        }
    }
}
</script>
<style scoped>
    .btn-sbar{
        color: #fff;
        background-color: #a58e7c;
        border-color: #a58e7c;
    }

    .load-text{
        color: blue;
    }

    .load-text:hover{
        color: darkblue;
        text-decoration: underline;
    }
</style>
