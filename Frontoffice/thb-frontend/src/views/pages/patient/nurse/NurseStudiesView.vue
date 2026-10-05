<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Perawat' },
                { text: this.regData.name }
            ]"
        >
            <section class="">
                <!-- <patientMenu selectedMenu="menu5" /> -->
                <PatientBanner />
                <div class="d-flex w-100 justify-content-start flex-wrap">
                    <template v-if="live_nurse.obgyn">
                        <b-button @click="triggerNurseObgyn({ regData: regData, gate: 'main_gate', nurseData: null})" variant="primary" size="sm" class="mr-2 mt-8">
                            <feather-icon icon="PlusCircleIcon" class="mr-1" />
                            <span class="fs-12">ASESMEN PERAWAT</span>
                        </b-button>
                    </template>
                    <NurseObgynView ref="nurseObgynRef" @refreshNurse="getNurseList"/>

                    <!-- <template v-if="regData.health_facility_id == hfi_outpatient"> -->
                    <template v-if="live_nurse.outpatient">
                        <template v-if="regData.registration_insurance[0].insurance_id === 2 && regData.registration_insurance[0].company_id === '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8'">
                            <template v-if="regData.registration_insurance[0].sep">
                                <b-button @click="triggerNurseOutpatient({ regData: regData, gate: 'main_gate', nurseData: null})" variant="primary" size="sm" class="mr-2 mt-8">
                                    <feather-icon icon="PlusCircleIcon" class="mr-1" />
                                    <span class="fs-12">ASESMEN PERAWAT</span>
                                </b-button>
                            </template>
                        </template>
                        <template v-else>
                            <b-button @click="triggerNurseOutpatient({ regData: regData, gate: 'main_gate', nurseData: null})" variant="primary" size="sm" class="mr-2 mt-8">
                                <feather-icon icon="PlusCircleIcon" class="mr-1" />
                                <span class="fs-12">ASESMEN PERAWAT</span>
                            </b-button>
                        </template>
                    </template>
                    <NurseOutpatientView
                        ref="nurseOutpatientRef"
                        @refreshNurse="getNurseList"
                        :lab_list="supportPackage.lab_package_list"
                        :rad_list="supportPackage.rad_package_list"
                        :phy_list="itemsPhysiotherapy"
                    />

                    <!-- <template v-if="regData.health_facility_id == hfi_emergency"> -->
                    <template v-if="live_nurse.emergency">
                        <b-button @click="triggerNurseEmergency({ regData: regData, gate: 'main_gate', nurseData: null})" variant="primary" size="sm" class="mr-2 mt-8">
                            <feather-icon icon="PlusCircleIcon" class="mr-1" />
                            <span class="fs-12">ASESMEN PERAWAT</span>
                        </b-button>
                    </template>
                    <NurseEmergencyView ref="nurseEmergencyRef" @refreshNurse="getNurseList"/>

                    <template v-if="regData.health_facility_id == hfi_emergency">
                        <router-link :to="{ path: '/patient/triage/'+ $route.params.id}">
                            <b-button size="sm" variant="orange-500" class="mr-1 mt-8">
                                <feather-icon icon="PlusCircleIcon" class="mr-1" />
                                <span class="fs-12">TRIASE</span>
                            </b-button>
                        </router-link>
                    </template>

                    <template v-if="live_nurse.inpatient">
                        <b-button @click="triggerNurseInpatient({ regData: regData, gate: 'main_gate', nurseData: null})" variant="primary" size="sm" class="mr-2 mt-8">
                            <feather-icon icon="PlusCircleIcon" class="mr-1" />
                            <span class="fs-12">ASESMEN PERAWAT</span>
                        </b-button>
                    </template>
                    <NurseInpatientView ref="nurseInpatientRef" @refreshNurse="getNurseList"/>

                    <template v-if="regData.health_facility_id == hfi_inpatient">
                        <b-button @click="triggerNutrition({ regData: regData, nutritionData: null, options: optionsDiet, gate: 'add_gate'})" variant="ax-primary-400" size="sm" class="mr-2 mt-8">
                            <feather-icon icon="PlusCircleIcon" class="mr-1" />
                            <span class="fs-12">ORDER DIET</span>
                        </b-button>
                        <OrderDietView ref="nutritionRef" @childTrigger="getNutritionList"/>
                    </template>

                    <b-button @click="triggerProcedure({ regData: regData, procedureData: null, optDoctor: doctorList, optTyCosting: TypeCostingOptions, gate: 'add_gate'})" variant="ax-primary-400" size="sm" class="mr-2 mt-8">
                        <feather-icon icon="PlusCircleIcon" class="mr-1" />
                        <span class="fs-12">PROSEDUR</span>
                    </b-button>
                    <OrderProcedureView ref="procedureRef" @childProcedureTrigger="getProcedureList"/>

                    <b-button @click="triggerBhp({ regData: regData, bhpData: null, optCatalog: OptionsCatalog, optPackage: PackageOptions, gate: 'add_gate'})" variant="ax-primary-400" size="sm" class="mr-2 mt-8">
                        <feather-icon icon="PlusCircleIcon" class="mr-1" />
                        <span class="fs-12">ORDER BHP</span>
                    </b-button>
                    <OrderBhpView ref="bhpRef" @childBhpTrigger="getBhpList"/>

                    <b-button @click="triggerTool({ regData: regData, toolData: null, gate: 'add_gate'})" variant="ax-primary-400" size="sm" class="mr-2 mt-8">
                        <feather-icon icon="PlusCircleIcon" class="mr-1" />
                        <span class="fs-12">PEMAKAIAN ALAT</span>
                    </b-button>
                    <OrderToolView ref="toolRef" @childToolTrigger="getToolList"/>

                    <b-button @click="triggerBlood({ regData: regData, bloodData: null, gate: 'add_gate'})" variant="ax-primary-400" size="sm" class="mr-2 mt-8">
                        <feather-icon icon="PlusCircleIcon" class="mr-1" />
                        <span class="fs-12">TRANSFUSI DARAH</span>
                    </b-button>
                    <OrderBloodView ref="bloodRef" @childBloodTrigger="getBloodList"/>

                    <b-button @click="triggerOperatieModal({gate: 'main_gate', rowData: null})" variant="ax-primary-400" size="sm" class="mr-2 mt-8">
                        <feather-icon icon="FilePlusIcon" class="mr-1" />
                        <span class="fs-12">ORDER OK</span>
                    </b-button>
                    <OrderOk ref="childOperatieRef" @refreshOk="getOperatingList" />

                    <!-- <template v-if="regData.health_facility_id == hfi_outpatient">
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
                            class="mr-2">
                            <feather-icon icon="PlusCircleIcon" class="mr-1" />
                            <span class="fs-12">BOOKING PENUNJANG</span>
                        </b-button>
                    </template>
                    <SupportBookingView ref="supportBooking"/> -->

                    <b-button @click="triggerSupportHistoryModal({
                        alias_name: regData.alias_name,
                        name: regData.name,
                        norm: regData.norm,
                        birth_date: regData.birth_date,
                    })"
                    variant="orange-500"
                    size="sm"
                    class="mr-2 mt-8">
                        <feather-icon icon="ClockIcon" class="mr-1" />
                        <span class="fs-12">RIWAYAT PENUNJANG</span>
                    </b-button>
                    <SupportHistoryView ref="supportHistory" />

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

                    <!-- <template v-if="regData?.health_facility_id == hfi_inpatient">
                        <b-button @click="triggerResumeModal(regData)"
                        variant="w3green"
                        size="sm"
                        class="mr-2 mt-8">
                            <feather-icon icon="FilePlusIcon" class="mr-1" />
                            <span class="fs-12">RESUME</span>
                        </b-button>
                        <AddResumeView ref="childResumeRef" />
                    </template> -->

                <router-link :to="{ path: '/patient/nurse/signature/'+ this.regData.registration_destination_id}" target="_blank">
                    <b-button variant="w3green" size="sm" class="mr-2 mt-8">
                        <feather-icon icon="FilePlusIcon" class="mr-1" />
                        <span class="fs-12">TTD PASIEN</span>
                    </b-button>
                </router-link>
                <template v-if="regData?.health_facility_id == hfi_inpatient">
                    <b-button
                    :to="`/patient/resume/print/${regData.registration_destination_id}/${resume_list[0]?.id}`"
                    :disabled="resume_list.length==0"
                    target="_blank"
                    size="sm" variant="w3green" class="mr-2 mt-8">
                        <feather-icon icon="PrinterIcon" class="mr-1" /> CETAK RESUME
                    </b-button>
                </template>
                <template v-if="regData?.health_facility_id == hfi_inpatient">
                    <b-button @click="triggerFormCheckout({ regData: regData, optCatalog: OptionsCatalog, optPackage: PackageOptions, gate: 'add_gate'})" variant="ax-primary-400" size="sm" class="mr-2 mt-8">
                        <feather-icon icon="PlusCircleIcon" class="mr-1" />
                        <span class="fs-12">FORMULIR PASIEN PULANG</span>
                    </b-button>
                    <!-- <FormCheckoutView ref="formCheckoutRef" @childFormCheckoutTrigger="getFormCheckoutList"/> -->
                    <FormCheckoutView ref="formCheckoutRef"/>

                    <b-button @click="triggerConsultModal()"
                    variant="ax-primary-400"
                    size="sm"
                    class="mr-2 mt-8">
                        <feather-icon icon="FilePlusIcon" class="mr-1" />
                        <span class="fs-12">KONSUL/RABER</span>
                    </b-button>

                    <ConsultationView ref="childConsultRef" @refreshRegistration="getRegDetail" @openCPPT="triggerCpptModal" />
                </template>
                <template v-if="regData.health_facility_id == hfi_inpatient">
                    <b-button @click="triggerKardex" variant="ax-primary-400" size="sm" class="mr-2 mt-8">
                        <feather-icon icon="EyeIcon" class="mr-1" />
                        <span class="fs-12">KARDEKS</span>
                    </b-button>
                    <KardexHistory ref="kardexRef" />
                </template>
                <template v-if="regData?.health_facility_id == hfi_inpatient">
                    <b-button @click="triggerHandover" variant="warning-400" size="sm" class="mr-2 mt-8">
                        <feather-icon icon="EyeIcon" class="mr-1" />
                        <span class="fs-12"> HANDOVER </span>
                    </b-button>
                    <HandoverView ref="handoverRef" :cppt_list="[latestCppt]" />
                </template>
                <template v-if="isPews && (isRanap || isIGD)">
                    <b-button @click="triggerPEWS" variant="ax-primary-400" size="sm" class="mr-2 mt-8">
                        <feather-icon icon="EyeIcon" class="mr-1" />
                        <span class="fs-12"> PEWS </span>
                    </b-button>
                </template>
            </div>

            <section class="d-flex align-items-center mt-12 bg-info-100 p-12">
                <div class="fs-14 font-weight-bold mr-12">FILTER:</div>
                <b-form-checkbox size="sm" :value="true" :unchecked-value="false" class="mr-2" v-model="active.showingList.nurse">
                    <span class="fs-12">ASESMEN PERAWAT</span>
                </b-form-checkbox>
                <b-form-checkbox size="sm" :value="true" :unchecked-value="false" class="mr-2" v-model="active.showingList.doctor">
                    <span class="fs-12">ASESMEN DOKTER</span>
                </b-form-checkbox>
                <template v-if="regData.health_facility_id == hfi_inpatient">
                    <b-form-checkbox size="sm" :value="true" :unchecked-value="false" class="mr-2" v-model="active.showingList.diet">
                        <span class="fs-12">DIET</span>
                    </b-form-checkbox>
                </template>
                <b-form-checkbox size="sm" :value="true" :unchecked-value="false" class="mr-2" v-model="active.showingList.procedure">
                    <span class="fs-12">PROSEDUR</span>
                </b-form-checkbox>
                <b-form-checkbox size="sm" :value="true" :unchecked-value="false" class="mr-2" v-model="active.showingList.bhp">
                    <span class="fs-12">BHP</span>
                </b-form-checkbox>
                <b-form-checkbox size="sm" :value="true" :unchecked-value="false" class="mr-2" v-model="active.showingList.tools">
                    <span class="fs-12">PEMAKAIAN ALAT</span>
                </b-form-checkbox>
                <b-form-checkbox size="sm" :value="true" :unchecked-value="false" class="mr-2" v-model="active.showingList.bloods">
                    <span class="fs-12">TRANSFUSI DARAH</span>
                </b-form-checkbox>
            </section>

                <section class="mt-12" v-if="active.showingList.nurse">
                    <ModernLabel label="ASESMEN PERAWAT" />
                    <template v-if="nurseAsessmentList?.length > 0">
                        <template v-for="(result, index) in nurseAsessmentList" >
                            <section class="card mt-12" :style="isSimilar(result.registration_destination_id)">
                                <div class="card-body shadow-sm">
                                    <div class="d-flex align-items-center align-items-stretch" style="background-color: #edf2f6; ">
                                        <ModernLabel :label="getTypeNurseName(result.type_nurse_id)" :small="`Ditambahkan pada ${localDateTime(result.date_added)} Oleh ${result.employee_nurse_name}`" />
                                        <div class="d-flex align-items-center" style="margin-left:auto;">
                                            <b-button class="mr-2" @click="removeAssesmentNurse(result)" size="sm" variant="outline-danger-700">
                                                <feather-icon icon="TrashIcon" />
                                            </b-button>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-12">
                                        <router-link :to="{ path: '/patient/nurse/print/'+result.id}" target="_blank">
                                            <b-button pill size="sm" variant="w3green" class="px-16 mr-2">
                                                <b-icon icon="printer" /> <span>Cetak kajian</span>
                                            </b-button>
                                        </router-link>
                                        <b-button pill size="sm" variant="primary" class="px-16 mr-2" @click="editNurse(result)">
                                            <i><img src="@/@core/assets/icons/nurse/file-eye.svg" alt=""></i> <span>Lihat kajian</span>
                                        </b-button>
                                        <template v-if="result.registration_destination_id == regData.registration_destination_id">
                                            <!-- <b-button @click="onAddNsProcedure(result.registration_destination_id, result.id)" pill variant="outline-success" size="sm" class="px-16 mr-2"> -->
                                            <b-button @click="onAddNsProcedure(result)" pill variant="outline-success" size="sm" class="px-16 mr-2">
                                                <b-icon icon="plus" /> <span>Tindakan Perawat</span>
                                            </b-button>
                                        </template>
                                        <template v-if="result.type_nurse_id == 12">
                                            <router-link :to="{ path: '/prints/surat-keterangan-lahir/'+result.id}" target="_blank">
                                                <b-button pill size="sm" variant="w3green" class="px-16 mr-2">
                                                    <b-icon icon="printer" /> <span>Cetak Surat Keterangan Lahir</span>
                                                </b-button>
                                            </router-link>
                                        </template>
                                    </div>
                                    <div class="row mt-16">
                                        <div class="col-lg-4">
                                            <div>
                                                <div class="text-uppercase text-bold-14 text-primary-500">TTV :</div>
                                                <div class="mt-8" style="font-size: 13px;">
                                                    Systolic = {{ result?.systolic_blood_pressure || '-' }},
                                                    Diastolic = {{ result?.diastolic_blood_pressure || '-' }},
                                                    Frekuensi Nadi = {{ result?.pulse_frequency || '-' }},
                                                    Frekuensi Nafas = {{ result?.breath_frequency || '-' }},
                                                    Saturasi Oksigen = {{ result?.oximetry_frequency || '-' }},
                                                    Suhu = {{ result?.temperature || '-' }},
                                                    Oksigen = {{ getOxygenName(result?.oxygen_type_id || '-') }},
                                                    Kesadaran = {{ getAwareName(result?.aware) }},
                                                    GCS Mata = {{ getGcsEyeName(result?.eye_score || '-') }},
                                                    GCS Verbal = {{ getGcsVerbalName(result?.verbal_score || '-') }},
                                                    GCS Motorik = {{ getGcsMotoricName(result?.motor_score || '-') }},
                                                    Warna Kulit = {{ getColorSkinName(result?.skin_color || '-') }},
                                                    Perilaku = {{ getBehaviorName(result?.behavior || '-') }},
                                                </div>
                                            </div>
                                            <div class="mt-16">
                                                <div class="text-uppercase text-bold-14 text-primary-500">Antropometri :</div>
                                                <div class="mt-8" style="font-size: 13px;">
                                                    Berat Badan = {{ result?.weight || '-' }},
                                                    Tinggi Badan = {{ result?.height || '-' }},
                                                    IMT = {{ result?.imt || '-' }},
                                                    Lingkar Kepala = {{ result?.head_circumference || '-' }},
                                                    Lingkar Dada = {{ result?.chest_circumference || '-' }},
                                                    Lingkar Lengan Atas = {{ result?.arm_circumference || '-' }},
                                                    Lingkar Perut = {{ result?.stomach_circumference || '-' }},
                                                    Lingkar Lutut = {{ result?.knee_circumference || '-' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div>
                                                <div class="text-uppercase text-bold-14 text-primary-500">Anamnesa :</div>
                                                <div class="mt-8" style="font-size: 13px;">
                                                    <table class="w-100">
                                                        <tr>
                                                            <td class="align-top" style="width: 200px">Anamnesa</td>
                                                            <td clas="align-top px-3">:</td>
                                                            <td class="align-top">{{ result?.anamesa_type_id == 2 ? 'Allo Anamnesa' : 'Auto Anamnesa' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="align-top">Keluarga</td>
                                                            <td clas="align-top px-3">:</td>
                                                            <td class="align-top">{{ result?.family_name || '-' }} {{ getFamilyRelation(result?.family_relation_id || '-') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="align-top">Keluhan Utama</td>
                                                            <td clas="align-top px-3">:</td>
                                                            <td class="align-top">{{ result?.main_complaint || '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="align-top">Riwayat Penyakit Sekarang</td>
                                                            <td clas="align-top px-3">:</td>
                                                            <td class="align-top">
                                                                <div v-html="parsedBody(result?.history_disease_now || '-')"></div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="align-top">Riwayat Penyakit Dahulu</td>
                                                            <td clas="align-top px-3">:</td>
                                                            <td class="align-top">
                                                                <div v-html="parsedBody(result?.history_disease_past || '-')"></div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="result?.transaction_nurse?.length > 0" class="mt-16 mt-lg-28" style="font-size: 13px">
                                        <b-table-simple bordered fixed small>
                                            <b-thead>
                                                <b-tr>
                                                    <!-- <b-th class="text-center" style="width: 100px">Aksi</b-th> -->
                                                    <b-th v-if="result.registration_destination_id == regData.registration_destination_id" class="text-center" style="width: 100px">Aksi</b-th>
                                                    <b-th style="width: 150px">Tanggal & Jam</b-th>
                                                    <b-th class="text-center" style="width: 100px">Jumlah</b-th>
                                                    <b-th>Tindakan</b-th>
                                                    <b-th>Keterangan</b-th>
                                                </b-tr>
                                            </b-thead>
                                            <b-tbody>
                                                <template v-for="(itemsResult,i) in result.transaction_nurse" >
                                                    <b-tr v-if="itemsResult.nurse_id === result.id">
                                                        <!-- <b-td class="text-center"> -->
                                                        <b-td v-if="result.registration_destination_id == regData.registration_destination_id" class="text-center">
                                                            <!-- <b-button @click="onEditNsProcedure(itemsResult, result.registration_destination_id)" size="sm" variant="warning-500" class="mr-1"> -->
                                                            <b-button @click="onEditNsProcedure(itemsResult, result)" size="sm" variant="warning-500" class="mr-1">
                                                                <feather-icon icon="EditIcon" />
                                                            </b-button>
                                                            <b-button @click="removeNsProcedure(itemsResult)" size="sm" variant="danger-700">
                                                                <feather-icon icon="TrashIcon" />
                                                            </b-button>
                                                        </b-td>
                                                        <b-td>{{ dateTimeFullNumber(itemsResult.date_added) }}</b-td>
                                                        <b-td class="text-center">{{ itemsResult.var_1 }}</b-td>
                                                        <b-td>{{ itemsResult.costing_name }}</b-td>
                                                        <b-td>{{ itemsResult.notes }}</b-td>
                                                    </b-tr>
                                                </template>
                                            </b-tbody>
                                        </b-table-simple>
                                    </div>
                                </div>
                            </section>
                        </template>
                    </template>
                    <template v-if="load_nurse.limit_display <= load_nurse.count">
                        <div class="d-flex justify-content-center align-items-center">
                            <div @click="loadNurseMore" class="font-weight-bold pointer fs-15 text-center mt-16 py-1 load-text">
                                <a class="mr-2">Load more</a>
                                <feather-icon icon="ArrowDownIcon" size="17"/>
                            </div>
                        </div>
                    </template>
                </section>
                <div v-else class="text-center fs-13 mt-1" style="font-style: italic;">Tidak ada asesmen perawat yang dibuat pada pasien ini</div>

                <section class="mt-32" v-if="active.showingList.doctor">
                    <ModernLabel label="PERIODE PERAWATAN SAAT INI" variant="w3green" />
                    <template v-if="soapList.byRegistration?.length > 0">
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
                                            <!-- <template v-if="result.health_facility_id == hfi_outpatient">
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
                                            </template> -->
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
                                                    <template v-if="result?.laboratory_request_new?.length > 0">
                                                        <template v-for="(il, i) in result.laboratory_request_new">
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
                                                    <template v-if="result?.radiology_request_new?.length > 0">
                                                        <template v-for="(ir, i) in result.radiology_request_new">
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

                            <section v-else class="card mt-16 shadow-sm" :style="isSimilar(result.registration_destination_id)">
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

                                        <!-- CONDITIONAL BUTTONs -->

                                        <template v-if="result.health_facility_id == '444b3134-5b75-4b67-af72-a558d46716d7'">
                                            <router-link :to="{ path: '/prints/doctor-emergency-print/'+ result.id}" target="_blank">
                                                <b-button size="sm" variant="outline-success-500" class="px-16 mr-1">
                                                    <feather-icon icon="PrinterIcon" size="12" class="mr-1" />
                                                    <span class="fs-12">ASESMEN IGD</span>
                                                </b-button>
                                            </router-link>
                                        </template>

                                    <b-dropdown v-if="result.health_facility_item_id == hfii_rehabilitation && result.is_medical_rehabilitation == 1" size="sm" id="dropdown-aria" text="UJI FUNGSI" variant="success-500" class="mr-1 fs-12" menu-class="w-100">
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

                                    <b-dropdown v-if="result.health_facility_item_id == hfii_hemodialysis && result.is_hemodialysis == 1" size="sm" id="dropdown-aria" text="HEMODIALISA" variant="outline-success-500" class="mr-1 fs-12" menu-class="w-100">
                                         <router-link :to="{ path: '/prints/doctor-hemodialysis-print/'+ result.id}" target="_blank">
                                            <b-dropdown-item-button> Cetak Hasil </b-dropdown-item-button>
                                        </router-link>
                                         <router-link :to="{ path: '/prints/doctor-daily-hemodialysis-print/'+ result.id}" target="_blank">
                                            <b-dropdown-item-button> Cetak Harian </b-dropdown-item-button>
                                        </router-link>
                                         <router-link :to="{ path: '/prints/doctor-intradialytic-print/'+ result.id}" target="_blank">
                                            <b-dropdown-item-button> Cetak Intradialitik </b-dropdown-item-button>
                                        </router-link>
                                         <router-link :to="{ path: '/prints/doctor-tools-gauze-print/'+ result.id}" target="_blank">
                                            <b-dropdown-item-button> Cetak Pemakaian </b-dropdown-item-button>
                                        </router-link>
                                         <router-link :to="{ path: '/prints/doctor-discharge-planner-print/'+ result.id}" target="_blank">
                                            <b-dropdown-item-button> Cetak Discharge </b-dropdown-item-button>
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
                                                                        <tr :class="getMeaningVariant(result.neck_jvp)">
                                                                            <td class="align-top">JVP</td>
                                                                            <td class="align-top px-1">:</td>
                                                                            <td>{{ getNeckJVP(result.neck_jvp || 1) }}</td>
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
                                                                        <tr :class="getMeaningVariant(result.abdomen_bowel_sounds)">
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
                                    <b-button @click="triggerCpptModal({
                                        gate: 'main_gate',
                                        registration_destination_id: result.registration_destination_id
                                    })"
                                    variant="w3green"
                                    size="sm">
                                        <feather-icon icon="PlusCircleIcon" class="mr-1" />
                                        <span class="fs-12">TAMBAH CPPT</span>
                                    </b-button>
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
                </template>
                <div v-else class="text-center fs-13 mt-1" style="font-style: italic;">Tidak ada asesmen dokter yang dibuat pada periode ini</div>
            </section>

                <section class="mt-32" v-if="active.showingList.doctor">
                    <ModernLabel label="RIWAYAT BEROBAT"/>
                    <template v-if="soapList.exceptRegistration?.length > 0">
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
                                            <!-- <template v-if="result.health_facility_id == hfi_outpatient">
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
                                            </template> -->
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
                                                    <template v-if="result?.laboratory_request_new?.length > 0">
                                                        <template v-for="(il, i) in result.laboratory_request_new">
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
                                                    <template v-if="result?.radiology_request_new?.length > 0">
                                                        <template v-for="(ir, i) in result.radiology_request_new">
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

                                    <template v-if="result.health_facility_id == '444b3134-5b75-4b67-af72-a558d46716d7'">
                                        <router-link :to="{ path: '/prints/doctor-emergency-print/'+ result.id}" target="_blank">
                                            <b-button size="sm" variant="outline-success-500" class="px-16 mr-1">
                                                <feather-icon icon="PrinterIcon" size="12" class="mr-1" />
                                                <span class="fs-12">ASESMEN IGD</span>
                                            </b-button>
                                        </router-link>
                                    </template>

                                    <b-dropdown v-if="result.health_facility_item_id == hfii_rehabilitation && result.is_medical_rehabilitation == 1" size="sm" id="dropdown-aria" text="UJI FUNGSI" variant="success-500" class="mr-1 fs-12" menu-class="w-100">
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

                                    <b-dropdown v-if="result.health_facility_item_id == hfii_hemodialysis && result.is_hemodialysis == 1" size="sm" id="dropdown-aria" text="HEMODIALISA" variant="outline-success-500" class="mr-1 fs-12" menu-class="w-100">
                                         <router-link :to="{ path: '/prints/doctor-hemodialysis-print/'+ result.id}" target="_blank">
                                            <b-dropdown-item-button> Cetak Hasil </b-dropdown-item-button>
                                        </router-link>
                                         <router-link :to="{ path: '/prints/doctor-daily-hemodialysis-print/'+ result.id}" target="_blank">
                                            <b-dropdown-item-button> Cetak Harian </b-dropdown-item-button>
                                        </router-link>
                                         <router-link :to="{ path: '/prints/doctor-intradialytic-print/'+ result.id}" target="_blank">
                                            <b-dropdown-item-button> Cetak Intradialitik </b-dropdown-item-button>
                                        </router-link>
                                         <router-link :to="{ path: '/prints/doctor-tools-gauze-print/'+ result.id}" target="_blank">
                                            <b-dropdown-item-button> Cetak Pemakaian </b-dropdown-item-button>
                                        </router-link>
                                         <router-link :to="{ path: '/prints/doctor-discharge-planner-print/'+ result.id}" target="_blank">
                                            <b-dropdown-item-button> Cetak Discharge </b-dropdown-item-button>
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
                                                                        <tr :class="getMeaningVariant(result.abdomen_bowel_sounds)">
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

                                                <template v-if="result.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e'">
                                                    <div class="col-12 px-8 fs-12 mt-20">
                                                        <div class="fs-14 font-weight-bold bg-ax-primary-500 py-1 px-8 text-white">HEMODIALISA</div>
                                                        <table class="w-100 mt-8">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="align-top" style="width: 120px;">Diagnosis Utama</td>
                                                                    <td class="align-top px-1 text-center" style="width: 30px;">:</td>
                                                                    <td class="align-top">{{ result.main_diagnosis ? result.main_diagnosis : '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">Etiologi</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td class="align-top">{{ result.etiology ? result.etiology : '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">Penyakit Penyerta</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td class="align-top">{{ result.comorbidities ? result.comorbidities : '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">Frekuensi HD</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td class="align-top">{{ result.frequency_hd ? result.frequency_hd : '-' }} x/minggu</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="align-top">Akses Vaskular</td>
                                                                    <td class="align-top px-1 text-center">:</td>
                                                                    <td class="align-top">{{ result.vascular_access ? result.vascular_access : '-' }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </template>

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
                    <template v-if="load.limit_display <= load.count">
                        <div class="d-flex justify-content-center align-items-center">
                            <div @click="loadMore" class="font-weight-bold pointer fs-15 text-center mt-16 py-1 load-text">
                                <a class="mr-2">Load more</a>
                                <feather-icon icon="ArrowDownIcon" size="17"/>
                            </div>
                        </div>
                    </template>
                </template>
                <div v-else class="text-center fs-13 mt-1" style="font-style: italic;">Tidak ada riwayat berobat</div>
            </section>

                <section class="mt-32" v-if="active.showingList.diet && regData.health_facility_id == hfi_inpatient">
                    <div class="d-flex align-items-center align-items-stretch" style="background-color: #edf2f6; ">
                        <ModernLabel label="ORDER DIET PASIEN" variant="ax-primary-400" class="col px-0"/>
                        <div v-if="nutritionItems?.length > 0" class="d-flex align-items-center" style="width: 100px;">
                            <b-form-select id="per-page-select" v-model="nutritionTable.perPage" size="sm" :options="nutritionTable.pageOptions" />
                        </div>
                    </div>
                    <section v-if="nutritionItems?.length > 0" class="mt-12">
                        <b-table small fixed bordered head-variant="light" :items="nutritionItems" :fields="nutritionTable.fields" :per-page="0" :current-page="nutritionTable.currentPage" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                            <template #cell(time_type_id)="row">{{ getTimeName(row.value) }}</template>
                            <template #cell(action)="row">
                                <div v-if="row.item.status == 0">
                                    <!-- <b-button @click="triggerNutrition({ regData: null, nutritionData: row.item, options: optionsDiet, gate: 'edit_gate'})"  size="sm" variant="warning-500" class="mr-1"> -->
                                    <b-button @click="editNutrition(row.item)" size="sm" variant="warning-500" class="mr-1">
                                        <feather-icon icon="EditIcon" />
                                    </b-button>
                                    <b-button @click="triggerNutrition({ regData: null, nutritionData: row.item, options: null, gate: 'delete_gate'})" size="sm" variant="danger-500">
                                        <feather-icon icon="TrashIcon" />
                                    </b-button>
                                </div>
                            </template>
                        </b-table>
                        <div class="d-flex justify-content-between w-100 mt-12">
                            <b-pagination
                                v-model="nutritionTable.currentPage"
                                :total-rows="nutritionRows"
                                :per-page="nutritionTable.perPage"
                                align="fill" size="sm" class="my-0">
                            </b-pagination>
                            <span class="text-regular-14">Menampilkan {{ nutritionTable.firstRows }} hingga <template v-if="nutritionTable.lastRows < nutritionRows">{{ nutritionTable.lastRows }}</template> <template v-else>{{ nutritionRows }}</template> dari {{ nutritionRows }} entri</span>
                        </div>
                    </section>
                    <div v-else class="text-center fs-13 mt-1" style="font-style: italic;">Tidak ada order diet yang dibuat pada pasien ini</div>
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
                                        <template v-if="row.item?.operativeReport && Object.keys(row.item.operativeReport).length > 0">
                                            <b-button
                                            :to="`/operatie-kamer/print/${row.item.id}/${row.item.operativeReport.id}`"
                                            target="_blank"
                                            size="sm" variant="w3green" class="mr-1">
                                                <feather-icon icon="PrinterIcon" />
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

                <section class="mt-32" v-if="active.showingList.procedure">
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

                <section class="mt-32" v-if="active.showingList.bhp">
                    <div class="d-flex align-items-center align-items-stretch" style="background-color: #edf2f6; ">
                        <ModernLabel label="ORDER BHP PASIEN" variant="ax-primary-400" class="col px-0"/>
                        <div v-if="bhpItems?.length > 0" class="d-flex align-items-center" style="width: 100px;">
                            <b-form-select id="per-page-select" v-model="bhpTable.perPage" size="sm" :options="bhpTable.pageOptions" />
                        </div>
                    </div>
                    <section v-if="bhpItems?.length > 0" class="mt-12">
                        <b-table small fixed bordered head-variant="light" :items="bhpItems" :fields="bhpTable.fields" :per-page="0" :current-page="bhpTable.currentPage" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                            <template #cell(date_added)="row">{{ localDateTime(row.value) }}</template>
                            <template #cell(action)="row">
                                <b-button @click="editBhp(row.item)" size="sm" variant="warning-500" class="mr-1">
                                    <feather-icon icon="EditIcon" />
                                </b-button>
                            </template>
                            <template #cell(form_prescription)="row">
                                <template v-if="row.value?.length > 0">
                                    <li v-for="list in row.value" class="text-left">{{ list.pharmacy_catalog_name }}, {{ list.qty }} {{ list.package_name }}</li>
                                </template>
                                <div v-else>-</div>
                            </template>
                        </b-table>
                        <div class="d-flex justify-content-between w-100 mt-12">
                            <b-pagination
                                v-model="bhpTable.currentPage"
                                :total-rows="bhpRows"
                                :per-page="bhpTable.perPage"
                                align="fill" size="sm" class="my-0">
                            </b-pagination>
                            <span class="text-regular-14">Menampilkan {{ bhpTable.firstRows }} hingga <template v-if="bhpTable.lastRows < bhpRows">{{ bhpTable.lastRows }}</template> <template v-else>{{ bhpRows }}</template> dari {{ bhpRows }} entri</span>
                        </div>
                    </section>
                    <div v-else class="text-center fs-13 mt-1" style="font-style: italic;">Tidak ada order BHP yang dibuat pada pasien ini</div>
                </section>

                <section class="mt-32" v-if="active.showingList.tools">
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

                <section class="mt-32" v-if="active.showingList.bloods">
                    <div class="d-flex align-items-center align-items-stretch" style="background-color: #edf2f6; ">
                        <ModernLabel label="TRANFUSI DARAH" variant="ax-primary-400" class="col px-0"/>
                        <div v-if="bloodItems?.length > 0" class="d-flex align-items-center" style="width: 100px;">
                            <b-form-select id="per-page-select" v-model="bloodTable.perPage" size="sm" :options="bloodTable.pageOptions" />
                        </div>
                    </div>
                    <section v-if="bloodItems?.length > 0" class="mt-12">
                        <b-table small fixed bordered head-variant="light" :items="bloodItems" :fields="bloodTable.fields" :per-page="0" :current-page="bloodTable.currentPage" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                            <template #cell(date_added)="row">{{ localDateTime(row.value) }}</template>
                            <template #cell(action)="row">
                                <b-button @click="editBlood(row.item)" size="sm" variant="warning-500" class="mr-1">
                                    <feather-icon icon="EditIcon" />
                                </b-button>
                                <b-button @click="triggerBlood({ regData: null, bloodData: row.item, gate: 'delete_gate'})" size="sm" variant="danger-500">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </template>
                        </b-table>
                        <div class="d-flex justify-content-between w-100 mt-12">
                            <b-pagination
                                v-model="bloodTable.currentPage"
                                :total-rows="bloodRows"
                                :per-page="bloodTable.perPage"
                                align="fill" size="sm" class="my-0">
                            </b-pagination>
                            <span class="text-regular-14">Menampilkan {{ bloodTable.firstRows }} hingga <template v-if="bloodTable.lastRows < bloodRows">{{ bloodTable.lastRows }}</template> <template v-else>{{ bloodRows }}</template> dari {{ bloodRows }} entri</span>
                        </div>
                    </section>
                    <div v-else class="text-center fs-13 mt-1" style="font-style: italic;">Tidak ada pemakaian alat yang dibuat pada pasien ini</div>
                </section>

            </section>
        </Layout>

        <b-modal v-model="active.modal.nurse_transaction" scrollable no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    <!-- <div>{{ getTypeNurseName(transaction_header.type_nurse_id) }} - {{ transaction_header.date_added }}</div> -->
                    ANDA SEDANG MENGISI TINDAKAN PERAWAT -
                    <span class="text-primary">{{ transaction_header.alias_name }}. {{ transaction_header.name }} | {{ transaction_header.norm }} | {{localDate(transaction_header.birth_date)}} ({{ parsedAge(transaction_header.birth_date) }}) | {{ transaction_header.health_facility_item_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <section>
                <div>
                    <label class="fs-13 mb-0">Tindakan Perawat <span class="text-danger">*</span></label>
                    <v-select v-model="form_transaction.costing_id" :options="OptionsCosting" :reduce="item => item" label="title" class="select-size-sm" placeholder="pilih tindakan"></v-select>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Perawat</label>
                    <v-select v-model="form_transaction.user_nurse_id" :options="nurseList" :reduce="item => item.value" label="title" class="select-size-sm" placeholder="pilih perawat"></v-select>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Tanggal</label>
                    <b-form-input v-model="form_transaction.date_added" type="datetime-local" size="sm" />
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Jumlah <span class="text-danger">*</span></label>
                    <b-form-input v-model="form_transaction.var_1" type="number" size="sm" min="1" />
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Keterangan</label>
                    <textarea v-model.lazy="form_transaction.notes" rows="3" class="form-control form-control-sm"></textarea>
                </div>
            </section>
            <!-- <pre>{{ form_transaction }}</pre> -->
            <template #modal-footer>
                <div class="d-flex justify-content-end m-0">
                    <b-button @click="validationNurseTransaction" :disabled="active.disabled.transaction" size="sm" variant="primary" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <!-- ==== PEWS ===  -->
        <b-modal v-model="active.modal.nurse_pews" size="lg" hide-footer no-close-on-backdrop>
        <template #modal-header="{ close }">
            <div class="fs-13 font-weight-bold text-uppercase">
            PEWS (Pediatric Early Warning Score)
            </div>
            <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
        </template>
        <b-table responsive small bordered head-variant="light" :fields="PEWSfields" :items="PEWSdata" :busy="isPEWSLoading" show-empty empty-text="Tidak ada Data PEWS" class="text-center" style="font-size: 14px;" >
            <template #table-busy>
                <div class="text-center text-secondary my-2">
                    <b-spinner class="align-middle mr-2"></b-spinner>
                    <strong>Memuat data...</strong>
                </div>
            </template>
            <template #cell(no)="row">{{ row.index+1 }}</template>
            <template #cell(observation_date)="row">
                <div class="text-center">{{ localDateTime(row.value) }}</div>
            </template>
            <template #cell(total_score)="row">
                <div class="text-center">{{ row.value }}</div>
            </template>
        </b-table>
        <ModernLabel label="Form PEWS" class="mb-12" />
        <section class="row mb-12">
            <div class="col-lg-4">
            <label class="fs-13 mb-0">Tanggal</label>
                <b-form-input v-model="form_pews.date" type="date" size="sm" />
            </div>
            <div class="col-lg-4">
            <label class="fs-13 mb-0">Jam</label>
                <b-form-input v-model="form_pews.time" type="time" size="sm" />
            </div>
        </section>
        <div class="table-responsive">
            <table class="table table-bordered text-center" style="font-size: 12px;">
            <thead>
                <tr>
                <th style="width: 130px;">Parameter</th>
                <th class="bg-success text-white">
                    Hijau<br><small>0</small>
                </th>
                <th class="bg-warning text-dark">
                    Kuning<br><small>1</small>
                </th>
                <th style="background-color: #fd7e14; color: white;">
                    Oren<br><small>2</small>
                </th>
                <th class="bg-danger text-white">
                    Merah<br><small>3</small>
                </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <td class="text-left font-weight-bold align-middle">Skor Perilaku</td>
                <td>
                    <b-form-radio v-model="form_pews.behaviour_score" :value="0" class="d-flex justify-content-center">
                    <span>Sadar/Bermain</span>
                    </b-form-radio>
                </td>
                <td>
                    <b-form-radio v-model="form_pews.behaviour_score" :value="1" class="d-flex justify-content-center">
                    <span>Tidur/Perubahan Perilaku</span>
                    </b-form-radio>
                </td>
                <td>
                    <b-form-radio v-model="form_pews.behaviour_score" :value="2" class="d-flex justify-content-center">
                    <span>Gelisah</span>
                    </b-form-radio>
                </td>
                <td>
                    <b-form-radio v-model="form_pews.behaviour_score" :value="3" class="d-flex justify-content-center">
                    <span>Tidak merespon Terhadap Nyeri Penurunan Kesadaran</span>
                    </b-form-radio>
                </td>
                </tr>

                <tr>
                <td class="text-left font-weight-bold align-middle">Skor CRT/Warna Kulit</td>
                <td>
                    <b-form-radio v-model="form_pews.crt_score" :value="0" class="d-flex justify-content-center">
                    <span>1-2 dtk/Pink</span>
                    </b-form-radio>
                </td>
                <td>
                    <b-form-radio v-model="form_pews.crt_score" :value="1" class="d-flex justify-content-center">
                    <span>3 dtk/Pucat</span>
                    </b-form-radio>
                </td>
                <td>
                    <b-form-radio v-model="form_pews.crt_score" :value="2" class="d-flex justify-content-center">
                    <span>4 dtk/sianosis</span>
                    </b-form-radio>
                </td>
                <td>
                    <b-form-radio v-model="form_pews.crt_score" :value="3" class="d-flex justify-content-center">
                    <span>>= dtk/Mottle</span>
                    </b-form-radio>
                </td>
                </tr>

                <tr>
                <td class="text-left font-weight-bold align-middle">Skor Perespirasi</td>
                <td>
                    <b-form-radio v-model="form_pews.perespiration_score" :value="0" class="d-flex justify-content-center">
                    <span>Tidak ada retraksi</span>
                    </b-form-radio>
                </td>
                <td>
                    <b-form-radio v-model="form_pews.perespiration_score" :value="1" class="d-flex justify-content-center">
                    <span>Cuping hidung/O2 1-3 Lpm</span>
                    </b-form-radio>
                </td>
                <td>
                    <b-form-radio v-model="form_pews.perespiration_score" :value="2" class="d-flex justify-content-center">
                    <span>Retraksi dada/O2 4-6 Lpm</span>
                    </b-form-radio>
                </td>
                <td>
                    <b-form-radio v-model="form_pews.perespiration_score" :value="3" class="d-flex justify-content-center">
                    <span>Stridor/O2 7-8 Lpm</span>
                    </b-form-radio>
                </td>
                </tr>
            </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-12">
            <b-button variant="primary" size="sm" :disabled="isPEWSLoading || active.disabled.save_pews" @click="savePews">
            <feather-icon icon="SaveIcon" class="mr-50" />
            Simpan PEWS
            </b-button>
        </div>
        </b-modal>
        <SupportExaminationView ref="supportExamRef"/>
        <LabResultModalView ref="labModalResult"/>
        <RadResultModalView ref="radModalResult"/>
        <AddingCpptView ref="childCpptRef" @refreshAssessment="soapRegistration" @prescChildTemplate="fillPrescTemplate"/>
        <AddingSbarView ref="childSbarRef" @refreshAssessment="soapRegistration" @prescChildTemplate="fillPrescTemplate" @anamnChildTemplate="fillAnamnTemplate" />
    </div>
</template>
<script>
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue';
import moment from 'moment';
import { mapState } from 'vuex';
import LabResultModalView from '../medical_support/LabResultModalView.vue';
import RadResultModalView from '../medical_support/RadResultModalView.vue';

import dateMixin from "@/mixins/dateMixin";
import optionMixin from '@/mixins/optionMixin';

import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import OrderBhpView from '@/views/layouts/patient/BHP/OrderBhpView.vue';
import administration from '@/views/layouts/patient/doctor/administration.vue';
import AddingCpptView from '@/views/pages/patient/cppt/AddingCpptView.vue';
import ListCpptView from '@/views/pages/patient/cppt/ListCpptView.vue';
import AddingSbarView from '@/views/pages/patient/sbar/AddingSbarView.vue';
import ListSBARView from '@/views/pages/patient/sbar/ListSBARView.vue';
import OrderBloodView from '../blood_gourd/OrderBloodGourdView.vue';
import SupportExaminationView from '../medical_support/SupportExaminationView.vue';
import SupportHistoryView from '../medical_support/SupportHistoryView.vue';
import OrderDietView from '../nutrition/OrderDietView.vue';
import OrderProcedureView from '../procedure/OrderProcedureView.vue';
import OrderToolView from '../tool/OrderToolView.vue';
import NurseEmergencyView from './NurseEmergencyView.vue';
import NurseInpatientView from './NurseInpatientView.vue';
import NurseObgynView from './NurseObgynView.vue';
import NurseOutpatientView from './NurseOutpatientView.vue';
// import SupportBookingView from '../medical_support/SupportBookingView.vue';
import OrderOk from '@/views/pages/patient/OK/OrderOk.vue';
import FormCheckoutView from '@/views/pages/patient/checkout/FormCheckoutView.vue';
import ConsultationView from '@/views/pages/patient/consultation/ConsultationView.vue';
import KardexHistory from '@/views/pages/patient/kardex/KardexHistoryView.vue';
import AddResumeView from '../resume/AddResumeView.vue';
import HandoverView from '@/views/pages/patient/handover/HandoverView.vue';

export default {
    mixins: [dateMixin, optionMixin],
    name: 'DoctorStudies',
    components: {
        SupportHistoryView,
        administration,
        patientMenu,
        NurseInpatientView,
        NurseOutpatientView,
        NurseEmergencyView,
        NurseObgynView,
        OrderDietView,
        OrderProcedureView,
        OrderToolView,
        OrderBloodView,
        OrderBhpView,
        SupportExaminationView,
        RadResultModalView,
        LabResultModalView,
        AddingCpptView,
        ListCpptView,
        AddingSbarView,
        ListSBARView,
        AddResumeView,
        OrderOk,
        FormCheckoutView,
        // SupportBookingView,
        ConsultationView,
        KardexHistory,
        HandoverView,
    },
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            hfi_outpatient: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
            hfi_emergency: '444b3134-5b75-4b67-af72-a558d46716d7',
            hfii_hemodialysis: 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e',
            hfii_rehabilitation: '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a',
            hfii_mcu: 'd46e2eb6-85a9-4bd4-b515-a0858cc50c8f',
            hfii_physiotherapy: 'd350e55d-6f8a-4c42-b269-cfda717fbf8b',
            dr_position_obgyn: '146b0883-efff-4bf1-a40b-8beae6e2c1bd',
            live_nurse: {
                obgyn: false,
                outpatient: false,
                inpatient: false,
                emergency: false,
            },

            regData: {},
            soapList: {
                byRegistration: [],
                exceptRegistration: [],
            },
            load: {
                count: 0,
                limit_display: 4,
                add_display: 4,
            },

            load_nurse: {
                count: 0,
                limit_display: 2,
                add_display: 2,
            },

            nurseAsessmentList:[],

            resume_list:[],

            active: {
                modal: {
                    nurse_transaction: false,
                    nurse_pews: false,
                },
                showingList: {
                    nurse: true,
                    diet: true,
                    bhp: true,
                    procedure: true,
                    tools: true,
                    bloods: true,
                    doctor: true,
                },
                disabled: {
                    transaction: false,
                    save_pews: false,
                },
            },

            form_transaction: {
                id: '',
                costing_id: '',
                nurse_id: '',
                user_nurse_id: '',
                notes: '',
                var_1: 1,
                var_2: '',
                var_3: '',
                date_added: moment(new Date()).format('YYYY-MM-DD HH:mm:ss'),
            },
            isPews: false,
            isRanap: false,
            isIGD: false,
            form_pews: {
                // nurse_trx_id: '',
                // registration_destination_id: '',
                // employee_nurse_id: '',
                // type_nurse_id: '',
                date: new Date().toISOString().split('T')[0],
                time: new Date().toTimeString().slice(0, 5),
                // observation_date: new Date().toISOString(),
                behaviour_score: '',
                crt_score: '',
                perespiration_score: '',
            },
            transaction_header: {
                alias_name: '',
                name: '',
                norm: '',
                birth_date: '',
                health_facility_item_name: '',
                type_nurse_id: '',
                date_added: '',
            },

            // ============================
            nutritionParams: {
                offset: 0,
                limit: 10,
                registration_id: ''
            },

            nutritionTable: {
                pageOptions: [10, 20, 50, 100],
                currentPage: 1,
                perPage: 10,
                firstRows: 1,
                lastRows: 10,
                fields: [
                    { key: 'action', label: 'Aksi', thStyle: {width: '100px'} },
                    { key: 'date', label: 'Tanggal Order' },
                    { key: 'time_type_id', label: 'Waktu' },
                    { key: 'diet_name', label: 'Diet' },
                    { key: 'notes', label: 'Catatan' },
                ],
            },

            // ============================
            nurseList: [],
            doctorList: [],
            procedureParams: {
                offset: 0,
                limit: 10,
                lastRows: 10,
                registration_id: '',
            },

            procedureTable: {
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
                    { key: 'doctor_in_charge_name', label: 'Dokter'},
                    { key: 'notes', label: 'Keterangan'},
                ],
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

            // ============================
            bloodParams: {
                offset: 0,
                limit: 10,
                lastRows: 10,
                registration_id: ''
            },

            bloodTable: {
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


            // ============================
            bhpParams: {
                offset: 0,
                limit: 10,
                lastRows: 10,
                registration_id: '',
            },

            bhpTable: {
                pageOptions: [10, 20, 50, 100],
                currentPage: 1,
                perPage: 10,
                firstRows: 1,
                lastRows: 10,
                fields: [
                    { key: 'action', label: 'Aksi', thStyle: { width: '100px'} },
                    { key: 'date_added', label: 'Tanggal & Jam', thStyle: { width: '150px'} },
                    { key: 'pharmacy_depo_name', label: 'Depo', thStyle: { width: '150px'}},
                    { key: 'no_order', label: 'No Order', thStyle: { width: '150px'}},
                    { key: 'form_prescription', label: 'Item BHP', thStyle: { textAlign: 'left'}},
                ],
            },

            // ============================
            supportPackage: {
                lab_package_list: [],
                rad_package_list: [],
            },

            // ============================ templating
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
                // registration_id:'',
                norm: '',
            },
            mergedOperatingData:[],
            PEWS_nurse_trx_id: '',
            PEWSdata: [],
            isPEWSLoading: false,
            PEWSfields: [
                { key: 'no', label: 'No', thStyle: { minWidth: '35px' }},
                { key: 'observation_date', label: 'Tanggal dan Jam'},
                { key: 'total_score', label: 'Skor'},
            ],

            //Baby NewBorn
            // is_babyBorn: false,

            // ========================
            signature_time: [],
            signature_dose: [],
            catalog_instruction: [],
            signature_units: [],
        }
    },
    watch: {
        // "form_transaction.costing_id": function (value){
        //     console.log('form_transaction.costing_id: ',value);
        // },

        // Nutrition
        "nutritionTable.currentPage": {
            handler: function(value) {
                this.nutritionTable.firstRows = this.nutritionTable.perPage*(value-1)+1
                this.nutritionTable.lastRows = this.nutritionTable.perPage*(value)
                this.nutritionParams.offset = this.nutritionTable.perPage*(value-1)
                this.nutritionParams.limit = this.nutritionTable.perPage
                this.getNutritionList()
            }
        },
        "nutritionTable.perPage": {
            handler: function(value) {
                this.nutritionParams.limit = this.nutritionTable.perPage
                this.getNutritionList()
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
                this.procedureParams.lastRows = this.procedureParams.perPage
                this.getProcedureList()
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

        // blood-transaction
        "bloodTable.currentPage": {
            handler: function(value) {
                this.bloodTable.firstRows = this.bloodTable.perPage*(value-1)+1
                this.bloodTable.lastRows = this.bloodTable.perPage*(value)
                this.bloodParams.offset = this.bloodTable.perPage*(value-1)
                this.bloodParams.limit = this.bloodTable.perPage
                this.getBloodList()
            }
        },
        "bloodTable.perPage": {
            handler: function(value) {
                this.bloodParams.limit = this.bloodTable.perPage
                this.bloodParams.lastRows = this.bloodTable.perPage
                this.getBloodList()
            }
        },

        // bhp-transaction
        "bhpTable.currentPage": {
            handler: function(value) {
                this.bhpTable.firstRows = this.bhpTable.perPage*(value-1)+1
                this.bhpTable.lastRows = this.bhpTable.perPage*(value)
                this.bhpParams.offset = this.bhpTable.perPage*(value-1)
                this.bhpParams.limit = this.bhpTable.perPage
                this.getBhpList()
            }
        },
        "bhpTable.perPage": {
            handler: function(value) {
                this.bhpParams.limit = this.bhpTable.perPage
                this.bhpParams.lastRows = this.procedureParams.perPage
                this.getBhpList()
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
    },

    filters:{
        number(value) {
            return Number(value)
        },
    },

    computed: {
        ...mapState({
            // nurse list
            nurseItems: state => state.nurse.nurseItems,
            // nurse-procedure options
            OptionsCosting: state => state.costing.options.newCosting,
            // OptionsDoctor: state => state.employee.options.employee,
            // nutrition
            nutritionItems: state => state.nutrition.nutrition.items,
            nutritionRows: state => state.nutrition.nutrition.totalRows,
            optionsDiet: state => state.nutrition.optionsDiet,
            // doctor-procedure-transaction
            procedureItems: state => state.soap.procedure.items,
            procedureRows: state => state.soap.procedure.totalRows,
            TypeCostingOptions: state => state.helper.options.typecostingc,
            // tool-transaction
            toolItems: state => state.tool.tool.items,
            toolRows: state => state.tool.tool.totalRows,
            // blood-transaction
            bloodItems: state => state.blood.bloodTrans.items,
            bloodRows: state => state.blood.bloodTrans.totalRows,
            // bloodItems: state => state.blood.blood.items,
            // bloodRows: state => state.blood.blood.totalRows,
            // bhp-transaction
            bhpItems: state => state.soap.bhp.items,
            bhpRows: state => state.soap.bhp.totalRows,
            OptionsCatalog: state => state.catalog.options.catalog,
            PackageOptions: state => state.packages.options.packages,

            // booking
            itemsPhysiotherapy: state => state.physiotherapy.package.items,

            // vital option
            OptionsOxygen: state => state.soap.vitals.OptionsOxygen,
            OptionsAware: state => state.soap.vitals.OptionsAware,
            OptionGcsEye: state => state.soap.vitals.OptionGcsEye,
            OptionGcsVerbal: state => state.soap.vitals.OptionGcsVerbal,
            OptionGcsMotoric: state => state.soap.vitals.OptionGcsMotoric,

            // generalis option
            OptionsHeadCircumference: state => state.soap.generalis.OptionsHeadCircumference,
            OptionsHeadHairColor: state => state.soap.generalis.OptionsHeadHairColor,
            OptionsHeadHairCondition: state => state.soap.generalis.OptionsHeadHairCondition,
            OptionsEyeConjunctiva: state => state.soap.generalis.OptionsEyeConjunctiva,
            OptionsEyeSclera: state => state.soap.generalis.OptionsEyeSclera,
            OptionsEyePupil: state => state.soap.generalis.OptionsEyePupil,
            OptionsEyeReflect: state => state.soap.generalis.OptionsEyeReflect,
            OptionsEyeReflect: state => state.soap.generalis.OptionsEyeReflect,
            OptionsMouthMukosa: state => state.soap.generalis.OptionsMouthMukosa,
            OptionsMouthHiperemis: state => state.soap.generalis.OptionsMouthHiperemis,
            OptionsMouthTongue: state => state.soap.generalis.OptionsMouthTongue,
            OptionsMouthKaries: state => state.soap.generalis.OptionsMouthKaries,
            OptionsNeckKGB: state => state.soap.generalis.OptionsNeckKGB,
            OptionsNeckTrakea: state => state.soap.generalis.OptionsNeckTrakea,
            OptionsNeckMassa: state => state.soap.generalis.OptionsNeckMassa,
            OptionsInspectionChest: state => state.soap.generalis.OptionsInspectionChest,
            OptionsInspectionRetraction: state => state.soap.generalis.OptionsInspectionRetraction,
            OptionsPalpationChest: state => state.soap.generalis.OptionsPalpationChest,
            OptionsPalpationFremitus: state => state.soap.generalis.OptionsPalpationFremitus,
            OptionsPalpationKrepitasi: state => state.soap.generalis.OptionsPalpationKrepitasi,
            OptionsAuskultationPulmoVasikuler: state => state.soap.generalis.OptionsAuskultationPulmoVasikuler,
            OptionsAuskultationPulmoRhonki: state => state.soap.generalis.OptionsAuskultationPulmoRhonki,
            OptionsAuskultationPulmoWheezing: state => state.soap.generalis.OptionsAuskultationPulmoWheezing,
            OptionsAuskultationCORBJ: state => state.soap.generalis.OptionsAuskultationCORBJ,
            OptionsAuskultationCORMurmur: state => state.soap.generalis.OptionsAuskultationCORMurmur,
            OptionsAuskultationCORGallop: state => state.soap.generalis.OptionsAuskultationCORGallop,
            OptionsAbdomenSupel: state => state.soap.generalis.OptionsAbdomenSupel,
            OptionsAbdomenBising: state => state.soap.generalis.OptionsAbdomenBising,
            OptionsAbdomenPress: state => state.soap.generalis.OptionsAbdomenPress,
            OptionsAbdomenHepar: state => state.soap.generalis.OptionsAbdomenHepar,
            OptionsEkstremitasAkral: state => state.soap.generalis.OptionsEkstremitasAkral,
            OptionsEkstremitasCRT: state => state.soap.generalis.OptionsEkstremitasCRT,
            OptionsEkstremitasEdema: state => state.soap.generalis.OptionsEkstremitasEdema,
            OptionsOftalVisus: state => state.soap.generalis.OptionsOftalVisus,
            OptionsOftalTIO: state => state.soap.generalis.OptionsOftalTIO,
            OptionsOftalEyePosition: state => state.soap.generalis.OptionsOftalEyePosition,
            OptionsOftalMovement: state => state.soap.generalis.OptionsOftalMovement,
            OptionsOftalSuperSilia: state => state.soap.generalis.OptionsOftalSuperSilia,
            OptionsOftalPalpebraEdema: state => state.soap.generalis.OptionsOftalPalpebraEdema,
            OptionsOftalPalpebraPtosis: state => state.soap.generalis.OptionsOftalPalpebraPtosis,
            OptionsOftalPalpebraLagophtalmus: state => state.soap.generalis.OptionsOftalPalpebraLagophtalmus,
            OptionsOftalPalpebraTumor: state => state.soap.generalis.OptionsOftalPalpebraTumor,
            OptionsOftalApparetus: state => state.soap.generalis.OptionsOftalApparetus,
            OptionsOftalSilia: state => state.soap.generalis.OptionsOftalSilia,
            OptionsOftalConjHiperemis: state => state.soap.generalis.OptionsOftalConjHiperemis,
            OptionsOftalConjBleed: state => state.soap.generalis.OptionsOftalConjBleed,
            OptionsOftalKornea: state => state.soap.generalis.OptionsOftalKornea,
            OptionsOftalAnterior: state => state.soap.generalis.OptionsOftalAnterior,
            OptionsOftalIrisColor: state => state.soap.generalis.OptionsOftalIrisColor,
            OptionsOftalIrisKripte: state => state.soap.generalis.OptionsOftalIrisKripte,
            OptionsOftalPupilForm: state => state.soap.generalis.OptionsOftalPupilForm,
            OptionsOftalPupilDiameter: state => state.soap.generalis.OptionsOftalPupilDiameter,
            OptionsOftalPupilDirectLight: state => state.soap.generalis.OptionsOftalPupilDirectLight,
            OptionsOftalPupilIndirectLight: state => state.soap.generalis.OptionsOftalPupilIndirectLight,
            OptionsOftalLens: state => state.soap.generalis.OptionsOftalLens,
            OptionsOftalGlandula: state => state.soap.generalis.OptionsOftalGlandula,
            OptionsOftalLapang: state => state.soap.generalis.OptionsOftalLapang,
            OptionsOftalShadow: state => state.soap.generalis.OptionsOftalShadow,
        }),
        pewsTotal() {
            const { behavior, skin_color, respiration } = this.form_pews
            if (behavior === null || skin_color === null || respiration === null) return '-'
            return behavior + skin_color + respiration
            },
            pewsTotalClass() {
            const total = this.pewsTotal
            if (total === '-') return ''
            if (total <= 2) return 'bg-success text-white'
            if (total <= 4) return 'bg-warning text-dark'
            if (total <= 6) return 'bg-orange text-white'
            return 'bg-danger text-white'
            },
            pewsIsComplete() {
            const { behavior, skin_color, respiration, date, time } = this.form_pews
            return behavior !== null && skin_color !== null && respiration !== null && date && time
        },
        latestCpptItem() {
            const list = this.soapList.byRegistration
            if (!list || !list.length) return null

            const withCppt = list.filter(item => item.cppt && item.cppt.length > 0)
            if (!withCppt.length) return null

            return withCppt.reduce((latest, item) => {
            const latestDate = new Date(item.cppt[0].date_added)
            const currentDate = new Date(latest.cppt[0].date_added)
            return latestDate > currentDate ? item : latest
            })
        },

        latestCppt() {
            return this.latestCpptItem?.cppt?.[0] || null
        }
    },

    methods: {
        triggerKardex() {
            const message = {
                regData: {
                    registration_destination_id: this.regData.id,
                    name: this.regData.name,
                    alias_name: this.regData.alias_name,
                    birth_date: this.regData.birth_date,
                    norm: this.regData.norm,
                }
            }
            this.$refs.kardexRef.parentTrigger(message)
        },

        triggerHandover() {
            const message = {
                regData: {
                    registration_destination_id: this.regData.id,
                    name: this.regData.name,
                    alias_name: this.regData.alias_name,
                    birth_date: this.regData.birth_date,
                    norm: this.regData.norm,
                }
            }
            this.$refs.handoverRef.parentTrigger(message)
        },

        async fetchPEWSData() {
            this.isPEWSLoading = true
            try {
                const response = await this.$store.dispatch('nurse/getAll', this.regData.id)
                const pews = response?.data?.[0]?.pews

                if (Array.isArray(pews)) {
                    this.PEWSdata = pews
                    this.PEWS_nurse_trx_id = pews[0]?.nurse_id ?? ''
                }
            } catch (error) {
                console.error('Error while fetching PEWS data: ', error)
            } finally {
                this.isPEWSLoading = false
            }
        },

        async triggerPEWS() {
            this.active.modal.nurse_pews = true
            await this.fetchPEWSData()
        },

        async savePews() {
            this.active.disabled.save_pews = true;
            try {
                const observation_date = moment(`${this.form_pews.date} ${this.form_pews.time}`, 'YYYY-MM-DD HH:mm').format('YYYY-MM-DD HH:mm:ss')

                const payload = {
                    ...this.form_pews,
                    observation_date,
                    nurse_trx_id: this.PEWS_nurse_trx_id || '',
                    registration_destination_id: this.regData.id || '',
                    employee_nurse_id: this.userData.employee_id || '',
                    type_nurse_id: '',
                }
                await this.$store.dispatch('ews/savePEWS', payload)
                await this.fetchPEWSData()
                this.notification('Success', 'CheckIcon', 'success', 'Berhasil menyimpan PEWS')
                this.active.disabled.save_pews = false;
            } catch (error) {
                this.notification('Error', 'CloseIcon', 'danger', 'Gagal menyimpan PEWS')
            }
        },
        // newBornCheck(obj) {
        //     const birthDate = moment(obj.birth_date, 'YYYY-MM-DD');
        //     const now = moment();
        //     // const years = now.diff(birthDate, 'years');
        //     const days = now.diff(birthDate, 'days');

        //     this.is_babyBorn = days <= 28
        // },
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
                    this.operatingItems = response?.rows || []
                    this.okTable.totalRows = response?.iTotal || 0
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

        triggerOperatieModal(obj){
            const message = {
                gate: obj?.gate,
                rowData: obj?.rowData,
                regData: this.regData,
                doctor_list: this.doctorList,
            }
            this.$refs.childOperatieRef.parentTrigger(message);
        },
        // triggerResumeModal(obj){
        //     const message = obj
        //     this.$refs.childResumeRef.parentTrigger(message, 'add_gate');
        // },
        editNurse(result){
            if(result.registration_destination_id == this.regData.registration_destination_id) {
                if (this.live_nurse.obgyn) {
                    this.triggerNurseObgyn({ regData: this.regData, gate: 'edit_gate', nurseData: result})

                } else if (this.live_nurse.outpatient) {
                    this.triggerNurseOutpatient({ regData: this.regData, gate: 'edit_gate', nurseData: result})

                } else if (this.live_nurse.emergency) {
                    this.triggerNurseEmergency({ regData: this.regData, gate: 'edit_gate', nurseData: result})

                } else if (this.live_nurse.inpatient) {
                    this.triggerNurseInpatient({ regData: this.regData, gate: 'edit_gate', nurseData: result})
                }
            } else {
                this.$store.dispatch('registration/getById', result.registration_destination_id)
                .then(response => {
                    let data = response[0]

                    if (data.health_facility_id != this.hfi_emergency && data.doctor_in_charge_position_id == this.dr_position_obgyn) {
                        this.triggerNurseObgyn({ regData: data, gate: 'edit_gate', nurseData: result})

                    } else if (data.health_facility_id == this.hfi_outpatient) {
                        this.triggerNurseOutpatient({ regData: data, gate: 'edit_gate', nurseData: result})

                    } else if (data.health_facility_id == this.hfi_emergency) {
                        this.triggerNurseEmergency({ regData: data, gate: 'edit_gate', nurseData: result})

                    } else if (data.health_facility_id == this.hfi_inpatient) {
                        this.triggerNurseInpatient({ regData: data, gate: 'edit_gate', nurseData: result})
                    }
                })
            }
        },

        triggerNurseObgyn(param) {
            const message = param
            this.$refs.nurseObgynRef.parentTrigger(message);
        },

        triggerNurseOutpatient(param) {
            const message = param
            this.$refs.nurseOutpatientRef.parentTrigger(message);
        },

        triggerNurseEmergency(param) {
            const message = param
            this.$refs.nurseEmergencyRef.parentTrigger(message);
        },

        triggerNurseInpatient(param) {
            const message = param
            this.$refs.nurseInpatientRef.parentTrigger(message);
        },

        triggerSupportHistoryModal(obj) {
            const message = obj
            this.$refs.supportHistory.parentTrigger(message);
        },

        triggerAdministrationModal(obj) {
            const message = obj
            this.$refs.administrationRef.parentTrigger(message);
        },

        removeAssesmentNurse(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${this.getTypeNurseName(obj.type_nurse_id)} Ditambahkan pada ${this.localDateTime(obj.date_added)} Oleh ${obj.employee_nurse_name} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-600', okTitle: 'Ya Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    if(obj?.transaction_nurse.length == 0){
                        this.$store.dispatch('nurse/removeNurse', obj.id)
                        .then(
                            res => {
                                this.getNurseList()
                                this.notification('Success','CheckIcon','success','You have successfully')
                            },
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } else {
                        this.notification('Warning','CloseIcon','warning','Gagal Menghapus Asesmen, Masih ada Tindakan Perawat yang belum dihapus')
                    }
                }
            })
        },

        async clearNurseTransaction() {
            this.form_transaction.id = ''
            this.form_transaction.costing_id = ''
            this.form_transaction.nurse_id = ''
            this.form_transaction.user_nurse_id = ''
            this.form_transaction.notes = ''
            this.form_transaction.var_1 = 1
            this.form_transaction.var_2 = ''
            this.form_transaction.var_3 = ''
            this.form_transaction.date_added = moment(new Date()).format('YYYY-MM-DD HH:mm:ss')
        },

        getTransactionOptions(data) {
            const company_bpjs = '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8'
            const company_bpjstk = 'bb0d633f-99e1-4990-901b-0417a14e3cb6'
            const company_mi_mc = '315fe232-e034-4b45-bf0d-e8cbb1f04c1a'
            const company_mi_im = '8a322cf3-e135-4909-bc25-48e31adadb0e'
            const company_umum = 'e5a1e978-af77-4901-9ef8-4ebaeee77dfa'
            const inpatient_item_id = "d158a64e-6246-444a-97b7-8d0a2d90e53a"
            const non_class = 'e7ce65ba-3d51-41aa-add7-df8f6a40e2b4'
            const active_company = data.registration_insurance[0].company_id
            const active_service_class = data.registration_insurance[0].service_class_id

            let param = {
                type_costing_id: 3,
                company_insurance_id: '',
                service_class_id: '',
                health_facility_item_id: '',
            }

            param.company_insurance_id = active_company == company_bpjs  || active_company == company_bpjstk || active_company == company_mi_mc || active_company == company_mi_im ? active_company : company_umum
            param.service_class_id = data.health_facility_id == this.hfi_inpatient ? active_service_class : non_class
            param.health_facility_item_id = data.health_facility_id == inpatient_item_id ? '' : ''

            this.$store.dispatch('costing/getNewParamCosting', param)
        },

        async onAddNsProcedure(nurse_obj) {
            await this.clearNurseTransaction()
            const reg_des_id = nurse_obj.registration_destination_id
            this.form_transaction.nurse_id = nurse_obj.id

            // const getTempReg = async (args) => {
            //     const response = await this.$store.dispatch('registration/getById', args)
            //     return response.length > 0 ? response[0] : null
            // }
            const temp_regData = reg_des_id == this.regData.registration_destination_id
                ? this.regData
                : await this.getTempReg(reg_des_id)
            await this.openNsTransModal(temp_regData, nurse_obj)
        },

        async onEditNsProcedure(trans_obj, nurse_obj) {
            await this.clearNurseTransaction()
            const reg_des_id = nurse_obj.registration_destination_id

            const temp_regData = reg_des_id == this.regData.registration_destination_id
                ? this.regData
                : await this.getTempReg(reg_des_id)

            this.form_transaction.id = trans_obj?.id
            this.form_transaction.costing_id = {
                value: trans_obj?.costing_service_class_id,
                title: trans_obj?.costing_name,
                price: trans_obj?.var_2
            }
            this.form_transaction.user_nurse_id = trans_obj?.user_nurse_id
            this.form_transaction.notes = trans_obj?.notes
            this.form_transaction.var_1 = trans_obj?.var_1
            this.form_transaction.date_added = trans_obj?.date_added
            this.form_transaction.nurse_id = nurse_obj?.id

            await this.openNsTransModal(temp_regData, nurse_obj)
        },

        async openNsTransModal(regData, nurse_obj) {
            await this.clearTransactionHeader()
            this.getTransactionOptions(regData)
            this.transaction_header.alias_name = regData.alias_name
            this.transaction_header.name = regData.name
            this.transaction_header.norm = regData.norm
            this.transaction_header.birth_date = regData.birth_date
            this.transaction_header.health_facility_item_name = regData.health_facility_item_name
            this.transaction_header.type_nurse_id = nurse_obj.type_nurse_id
            this.transaction_header.date_added = nurse_obj.date_added

            this.active.modal.nurse_transaction = true
        },

        async clearTransactionHeader() {
            for (let property in this.transaction_header) {
                this.transaction_header[property] = ''
                // console.log(`${property}: ${this.transaction_header[property]}`);
            }
        },

        async getTempReg(reg_des) {
            const response = await this.$store.dispatch('registration/getById', reg_des)
            return response.length > 0 ? response[0] : null
        },





        async validationNurseTransaction() {
            try {
                this.active.disabled.transaction = true
                // console.log('this.form_transaction: ', this.form_transaction);
                let formData = {}
                if (this.form_transaction.costing_id == '' || this.form_transaction.var_1 == '' && this.form_transaction.user_nurse_id == '') {
                    this.notification('Warning','CloseIcon','warning','Tindakan perawat, nama perawat, dan jumlah wajib terisi')
                    return
                }

                formData = {
                    id: this.form_transaction.id,
                    nurse_id: this.form_transaction.nurse_id,
                    costing_id: this.form_transaction.costing_id.value,
                    user_nurse_id: this.form_transaction.user_nurse_id,
                    var_1: this.form_transaction.var_1,
                    var_2: this.form_transaction.costing_id.price,
                    var_3: this.form_transaction.costing_id.price*this.form_transaction.var_1,
                    notes: this.form_transaction.notes,
                    date_added: this.form_transaction.date_added
                }

                //dulu bgt BE minta strukturnya gini ga ngirim nurse_id kl edit
                if (this.form_transaction.id) delete formData.nurse_id

                const response = await this.$store.dispatch('nurse/formPostPutTransaction', formData)
                this.notification('Success','CheckIcon','success','You have successfully')
                this.active.modal.nurse_transaction = false
                await this.getNurseList()

            } catch (error) {
                const { message, debug, response } = error
                this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                console.error('Error catch validationNurseTransaction at: ', error);
            } finally {
                this.active.disabled.transaction = false
            }
        },

        removeNsProcedure(nurse_obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus tindakan ${nurse_obj.costing_name} tanggal ${nurse_obj.date_added} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-600', okTitle: 'Ya Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('nurse/removeTransaction', nurse_obj.id)
                    .then(
                        async res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            await this.getNurseList()
                        },
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        console.error('error removeNsProcedure at: ', response)
                    })
                }
            })
        },

        // onAddNsProcedure(reg_des_id, nurse_id) {
        //     this.clearNurseTransaction()
        //     this.active.modal.nurse_transaction = true

        //     if (reg_des_id == this.regData.registration_destination_id) {
        //         this.getTransactionOptions(this.regData)
        //     } else {
        //         this.$store.dispatch('registration/getById', reg_des_id)
        //         .then(response => {
        //             let data = response[0]
        //             this.getTransactionOptions(data)
        //         })
        //     }

        //     this.form_transaction.view = 'add'
        //     this.form_transaction.nurse_id = nurse_id
        //     // console.log('this.form_transaction', this.form_transaction);
        // },

        // onEditNsProcedure(obj, reg_des_id) {
        //     // console.log('obj', obj);
        //     this.clearNurseTransaction()
        //     this.active.modal.nurse_transaction = true

        //     if (reg_des_id == this.regData.registration_destination_id) {
        //         this.getTransactionOptions(this.regData)
        //     } else {
        //         this.$store.dispatch('registration/getById', reg_des_id)
        //         .then(response => {
        //             let data = response[0]
        //             this.getTransactionOptions(data)
        //         })
        //     }

        //     // this.form_transaction.view = 'edit'
        //     this.form_transaction.id = obj.id
        //     this.form_transaction.costing_id = {
        //         value: obj.costing_service_class_id,
        //         title: obj.costing_name,
        //         qty: obj.var_1,
        //         costing: obj.var_2
        //     }
        //     this.form_transaction.user_nurse_id = obj.user_nurse_id
        //     this.form_transaction.notes = obj.notes
        //     this.form_transaction.var_1 = obj.var_1
        //     this.form_transaction.date_added = obj.date_added
        // },

        // async validationNurseTransaction() {
        //     let formData = {}

        //     if (this.form_transaction.costing_id == '' || this.form_transaction.var_1 == '' && this.form_transaction.user_nurse_id == '') {
        //         this.notification('Warning','CloseIcon','warning','Tindakan perawat, nama perawat, dan Jumlah wajib terisi')

        //     } else{
        //         if (this.form_transaction.view == 'add' && this.form_transaction.id == '') {
        //             formData = {
        //                 id: this.form_transaction.id,
        //                 nurse_id: this.form_transaction.nurse_id,
        //                 costing_id: this.form_transaction.costing_id.value,
        //                 user_nurse_id: this.form_transaction.user_nurse_id,
        //                 var_1: this.form_transaction.var_1,
        //                 var_2: this.form_transaction.costing_id.price,
        //                 var_3: this.form_transaction.costing_id.price*this.form_transaction.var_1,
        //                 notes: this.form_transaction.notes,
        //                 date_added: this.form_transaction.date_added

        //             }
        //         } else if (this.form_transaction.view == 'edit' && this.form_transaction.id != '') {
        //             formData = {
        //                 id: this.form_transaction.id,
        //                 costing_id: this.form_transaction.costing_id.value,
        //                 user_nurse_id: this.form_transaction.user_nurse_id,
        //                 var_1: this.form_transaction.var_1,
        //                 var_2: this.form_transaction.costing_id.price,
        //                 var_3: this.form_transaction.costing_id.price*this.form_transaction.var_1,
        //                 notes: this.form_transaction.notes,
        //                 date_added: this.form_transaction.date_added
        //             }
        //         }

        //         // console.log('formData', formData);
        //         try {
        //             this.active.disabled.transaction = true
        //             await this.$store.dispatch('nurse/formPostPutTransaction', formData)
        //             .then(
        //                 res => {
        //                     this.notification('Success','CheckIcon','success','You have successfully')
        //                     this.getNurseList()
        //                     this.active.modal.nurse_transaction = false
        //                 }
        //             )
        //             .catch(({message, debug, response}) => {
        //                 this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
        //             })

        //         } catch (error) {
        //             this.notification('Failed','CloseIcon','danger','You have unsuccessful')
        //             console.error('Error catch at: ', error)

        //         } finally {
        //             this.active.disabled.transaction = false
        //         }
        //     }
        // },

        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },

        getFamilyRelation(code) {
            const marrieds = {
                '1': 'Ayah',
                '2': 'Ibu',
                '3': 'Kakak',
                '4': 'Adik',
            };
            return marrieds[code] || '-';
        },

        getTypeNurseName(type_nurse_id){
            switch (type_nurse_id) {
                case 1:
                    return 'Perawat: Asesmen Awal Poliklinik Dewasa'
                case 2:
                    return 'Perawat: Asesmen Awal Anak'
                case 3:
                    return 'Perawat: Asesmen Awal Obgyn'
                case 4:
                    return 'Perawat: Asesmen Awal IGD'
                case 5:
                    return 'Triase'
                case 6:
                    return 'Perawat: Asesmen Awal Rawat Inap Anak'
                case 7:
                    return 'Perawat: Asesmen Awal Rawat Inap Dewasa'
                case 8:
                    return 'Perawat: Asesmen Awal Rawat Inap Lansia'
                case 9:
                    return 'Perawat: Asesmen Awal Fisioterapi'
                case 10:
                    return 'Perawat: Asesmen SOAP'
                case 11:
                    return 'SDKI SLKI SIKI'
                case 12:
                    return 'Perawat: Asesmen Awal Rawat Inap Neonatus'
                case 13:
                    return 'Perawat: Asesmen Awal Hemodialisa'
                default:
                    return 'Asesmen Perawat'
            }
        },

        async getResumeList(regDesId) {
            try {
                const response = await this.$store.dispatch('resume/getAllList', { registration_destination_id: regDesId });
                // console.log('resume list response: ', response);

                if (response?.rows?.length > 0) {
                    this.resume_list = response.rows;
                } else {
                    this.resume_list = [];
                }
            } catch (error) {
                console.error('Error fetching resume list:', error);
                this.resume_list = [];
            }
        },

        isSimilar(reg_des_id) {
            if(this.regData.registration_destination_id == reg_des_id) {
                return 'border: 2px solid #03a972;'
            }
            return ''
        },

        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        async getNurseList() {
            let nurse_list = {
                norm: this.regData.norm,
                limit: this.load_nurse.limit_display,
                offset: 0,
            }

            try {
                const response = await this.$store.dispatch('nurse/getNurseList', nurse_list)
                this.nurseAsessmentList = (response && response.rows) || []
                this.load_nurse.count = (response && response.iTotal) || 0
                // console.log('nurseAsessmentLits', this.nurseAsessmentList)
            } catch (error) {
                console.error('Error catch while GET nurse/getNurseList at: ', error);
            }
        },

        loadNurseMore() {
            this.load_nurse.limit_display += this.load_nurse.add_display
            this.getNurseList()
            // console.log('limit:' + this.load.limit_display + ' ---- album.length ' + this.load.count);
        },

        getData (id) {
            const emergency_id = "444b3134-5b75-4b67-af72-a558d46716d7"
            const inpatient_id = "40f06534-07ec-4221-ad9d-dea08196079f"

			this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]
                // console.log(this.regData,'this.regdata')
                // console.log('this.regData.health_facility_id', this.regData.health_facility_id);
                this.isPews = this.pewsCriteria(this.regData?.birth_date)
                this.getNurseList()

                // this.newBornCheck(this.regData)

                this.liveNurse(this.regData)

                // doctor procedure-transaction
                this.getProcedureList()

                // doctor tool-transaction
                this.getToolList()

                // doctor blood-transaction
                this.getBloodList()

                // bhp-transaction
                this.getBhpList()

                // list-ok
                this.getOperatingList()

                // nutrition
                if (this.regData.health_facility_id == this.hfi_inpatient) {
                    this.getNutritionList()
                }
                // pews
                if (this.regData.health_facility_id == this.hfi_emergency || this.hfi_outpatient) {
                    this.isRanap = true
                    this.isIGD = true
                }

                // doctor-assessment
                this.soapRegistration()
                this.soapNotRegistration()
                // this.getDoctorAssessment()

                //booking penunjang
                this.getSupportPackage()

                // resume
                this.getResumeList(this.regData.registration_destination_id)

                this.pewsCriteria(this.regData?.birth_date)
            })
        },

        liveNurse(obj) {
            if (obj.health_facility_id != this.hfi_emergency && obj.doctor_in_charge_position_id == this.dr_position_obgyn) {
                this.live_nurse.obgyn = true
            } else if (obj.health_facility_id == this.hfi_outpatient) {
                this.live_nurse.outpatient = true
            } else if (obj.health_facility_id == this.hfi_inpatient) {
                this.live_nurse.inpatient = true
            } else if (obj.health_facility_id == this.hfi_emergency) {
                this.live_nurse.emergency = true
            }
        },

        // =====================================================================================================
        getNutritionList() {
            // this.nutritionParams.registration_destination_id = this.regData.registration_destination_id
            this.nutritionParams.registration_id = this.regData.registration_id
            this.$store.dispatch('nutrition/getNutritionStateList', this.nutritionParams)
        },

        getTimeName(time_id) {
            if (time_id == 1) return 'Pagi'
            if (time_id == 2) return 'Siang'
            if (time_id == 3) return 'Sore'
            return '-'
        },

        triggerNutrition(param) {
            const message = param
            this.$refs.nutritionRef.parentTrigger(message);
        },

        editNutrition(obj){
            if (obj.registration_destination_id == this.regData.registration_destination_id) {
                this.triggerNutrition({ regData: this.regData, nutritionData: obj, options: this.optionsDiet, gate: 'edit_gate'})
            } else {
                this.$store.dispatch('registration/getById', obj.registration_destination_id)
                .then(response => {
                    let data = response[0]
                    this.triggerNutrition({ regData: data, nutritionData: obj, options: this.optionsDiet, gate: 'edit_gate'})
                })
            }
        },

        // =====================================================================================================
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

        getAllNurseList() {
            try {
                this.$store.dispatch('employee/getNurseList').then((response) => {
                    this.nurseList = response
                }, error => {
                    console.error('Error while GET employee/getNurseList general at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET employee/getNurseList general at: ', error);
            }
        },

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


        // =====================================================================================================
        getBloodList() {
            this.bloodParams.registration_id = this.regData.registration_id
            this.$store.dispatch('blood/getBloodTransactionList', this.bloodParams)
        },

        triggerBlood(param) {
            const message = param
            this.$refs.bloodRef.parentTrigger(message);
        },

        editBlood(obj){
            if (obj.registration_destination_id == this.regData.registration_destination_id) {
                this.triggerBlood({ regData: this.regData, bloodData: obj, gate: 'edit_gate'})
            } else {
                this.$store.dispatch('registration/getById', obj.registration_destination_id)
                .then(response => {
                    let data = response[0]
                    this.triggerBlood({ regData: data, bloodData: obj, gate: 'edit_gate'})
                })
            }
        },

    // =====================================================================================================
        getBhpList() {
            this.bhpParams.registration_id = this.regData.registration_id
            this.$store.dispatch('soap/getBhpStateList', this.bhpParams)
        },

        triggerBhp(param) {
            const message = param
            this.$refs.bhpRef.parentTrigger(message);
        },

        editBhp(obj){
            if (obj.registration_destination_id == this.regData.registration_destination_id) {
                this.triggerBhp({ regData: this.regData, bhpData: obj, optCatalog: this.OptionsCatalog, optPackage: this.PackageOptions, gate: 'edit_gate'})
            } else {
                this.$store.dispatch('registration/getById', obj.registration_destination_id)
                .then(response => {
                    let data = response[0]
                    this.triggerBhp({ regData: data, bhpData: obj, optCatalog: this.OptionsCatalog, optPackage: this.PackageOptions, gate: 'edit_gate'})
                })
            }
        },

        // =====================================================================================================
        // triggerSupportBookingModal(obj) {
        //     const message = obj
        //     this.$refs.supportBooking.parentTrigger(message);
        // },

        // triggerCancelSupportBooking(obj) {
        //     const message = obj
        //     this.$refs.supportBooking.cancelTrigger(message);
        // },

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
                })

                this.$store.dispatch('radiology/getRadiologyPackageV2', param).then((response) => {
                    this.supportPackage.rad_package_list = response
                })

                this.$store.dispatch('physiotherapy/getPackageAll', param)

            } catch (error) {
                console.error('Error while GET data support_package at: ', error)
            }
        },

        // =====================================================================================================
        // getDoctorAssessment() {
        //     this.$store.dispatch('soap/getByRM', this.regData.norm)
        // },

        triggerCpptModal(obj) {
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
                cppt_id: null,
                triggerFrom: null,
            }
            this.$refs.childCpptRef.parentTrigger(message);
        },
        triggerSbarModal(obj) {
            const message = {
                // isSameReg: true,
                gate: obj.gate,
                registration_destination_id: obj.registration_destination_id,
                regData: this.regData,
                costing_list: this.OptionsCosting,
                lab_list: this.supportPackage?.lab_package_list,
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

        soapRegistration() {
            let only_reg = {
                registration_id: this.regData.registration_id
            }

            try {
                this.$store.dispatch('soap/getSoapList', only_reg).then((response) => {
                    this.soapList.byRegistration = (response && response.rows) || []
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
                    this.soapList.exceptRegistration = (response && response.rows) || []
                    this.load.count = (response && response.iTotal) || 0
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

        getSoapLabel(unit, hfin, initial, booking) {
            if (unit == this.hfi_outpatient && booking == 1) {
                return 'BOOKING PENUNJANG.'
            } else if (unit == this.hfi_inpatient && initial != 1) {
                return 'CPPT DOKTER RAWAT INAP'
            } else {
                return hfin
            }
        },

        getAssesToggleName(hfi) {
            if (hfi == this.hfi_inpatient) return 'ASESMEN AWAL RANAP'
            return 'ASESMEN'
        },

        getMeaningVariant(value) {
            if (value && value != 1) return 'text-danger-500'
            return ''
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
            // console.log('masuk ke ns-studies: ', data);
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

        // =============================

        async getOptSignTime() {
            try {
                await this.$store.dispatch('kardex/getOptSignaturaTime')
                .then(response => {
                    this.signature_time = response
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
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('Error GET kardex/getOptUnit at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET kardex/getOptUnit at: ', error);
            }
        },

        // ========================================
        //FORM CHECKOUT
        triggerFormCheckout(message) {
            this.$refs.formCheckoutRef.parentTrigger(message);
        },

        triggerConsultModal(){
            const message = {
                regData: this.regData,
                doctor_list: this.doctorSpecialistList
            }
            this.$refs.childConsultRef.parentTrigger(message);
        },

        getRegDetail(regDesId) {
            // console.log('regDesId: ', regDesId);
            this.$store.dispatch('registration/getById', regDesId).then(data => {
                this.regData = data[0]
            })
        },
    },
    created () {
        this.$store.dispatch('nutrition/getParamDiet')
        this.$store.dispatch('catalog/getParam')
        this.$store.dispatch('packages/getParam')
        this.$store.dispatch('helper/getParam', 'costing_c_one')
        // this.$store.dispatch('employee/getParamAll')

        this.getAllDoctorList()
        this.getAllNurseList()

        this.getAnamTemplate()
        this.getPrescTemplate()

        this.getPrescCatalog()
        this.getOptSignDose()
        this.getOptSignTime()
        this.getOptCatInstruction()
        this.getSignaturaUnits()

        if (this.$route.params.id) {
            this.getData(this.$route.params.id)
        }
    },
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

    .pointer {
        cursor: pointer;
    }
</style>