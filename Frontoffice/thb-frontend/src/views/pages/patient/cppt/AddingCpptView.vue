<template>
    <div>
        <b-modal v-model="active.modal.main" scrollable size="xl" hide-footer no-close-on-esc no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI CPPT
                    <span class="text-w3green">{{ temp_header.alias_name }}. {{ temp_header.name }} | {{ temp_header.norm }} | {{localDate(temp_header.birth_date)}} ({{ parsedAge(temp_header.birth_date) }}) | BB: {{temp_header.weight | number}} | TB: {{ temp_header.height | number}}</span>
                </div>
                <feather-icon icon="XIcon" @click="onCloseMainModal" size="18" class="pointer" />
            </template>

            <div class="row position-relative">
                <div class="col-4">
                    <div class="row">
                        <section class="col-8 position-sticky px-1" :style="{top: '0', height: `${dynamicHeight}`, overflowY: 'auto'}">
                            <ModernLabel variant="w3green" label="REFERENSI ASESMEN PASIEN" class="mb-2"/>
                            <b-tabs small content-class="mt-3 text-regular-12" nav-wrapper-class="fs-13" nav-class="custom-tab-style">
                                <b-tab title="DOKTER" active>
                                    <div v-for="(item, index) in history.soap" class="accordion" role="tablist">
                                        <b-card no-body class="mb-1">
                                            <b-card-header block v-b-toggle="'accordion-soap-'+item.id" header-tag="header" class="pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#03a972'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                                <div>
                                                    <div class="font-weight-bold">ASESMEN {{ localDateTime(item.date_added) }}</div>
                                                    <div>{{ item.doctor_in_charge_name }} | {{ item.health_facility_item_name }}</div>
                                                </div>
                                                <feather-icon icon="ChevronDownIcon" size="18" />
                                            </b-card-header>
                                            <b-collapse :id="`accordion-soap-`+item.id" accordion="soap-accordion" role="tabpanel">
                                                <b-card-body class="p-12 bg-info-300">
                                                    <section class="card">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">ANAMNESA</div>
                                                                <!-- <b-button size="sm" @click="copySelected($event, soapAnamnesa, item, 'ANAMNESA')" variant="w3green" style="font-size: 12px; line-height:120%">COPY</b-button> -->
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div><b>Anamnesa: </b>{{ item.anamesa_type_id == '1' ? 'Auto Anamnesa' : 'Allo Anamnesa' }}</div>
                                                            <div><b>Keluhan Utama: </b>{{ item?.main_complaint || '-' }}</div>
                                                            <div><b>Nama Keluarga: </b>{{ item?.family_name || '-' }}</div>
                                                            <div><b>Hubungan Keluarga: </b>{{ getFamilyName(item?.family_relation_id || 0) }}</div>
                                                            <div><b>R.Penyakit Sekarang: </b>{{ item?.history_disease_now || '-' }}</div>
                                                            <div><b>R.Penyakit Dahulu: </b>{{ item?.history_disease_past || '-' }}</div>
                                                            <div><b>R.Obat: </b>{{ item?.history_drugs || '-' }}</div>
                                                            <div>
                                                                <b>Alergi: </b>
                                                                <template v-if="item?.allergies?.length > 0">
                                                                    <li v-for="list in item?.allergies">{{ list.name }}</li>
                                                                </template>
                                                                <span v-else>-</span>
                                                            </div>
                                                        </div>
                                                    </section>
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">TANDA VITAL</div>
                                                                <template v-if="allowedToCopySoap('VITAL')">
                                                                    <b-button @click="copySelected($event, soapVitalSign, item, 'VITAL')" size="sm" variant="w3green" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div class="">
                                                                <b>Systolic: </b>{{ item.systolic_blood_pressure }},
                                                                <b>Diastolic: </b>{{ item.diastolic_blood_pressure }},
                                                                <b>Nadi: </b>{{ item.pulse_frequency }},
                                                                <b>Nafas: </b>{{ item.breath_frequency }},
                                                                <b>Saturasi: </b>{{ item.oximetry_frequency }},
                                                                <b>Suhu: </b>{{ item.temperature }},
                                                                <b>GD: </b>{{ item.blood_sugar }},
                                                                <b>Oksigenasi: </b>{{ getOxygenName(item?.oxygen_type_id || '-') }},
                                                                <b>Kesadaran:</b> {{ getAwareName(item?.aware) }},
                                                                <b>GCS Mata: </b>{{ getGcsEyeName(item?.eye_score || '-') }},
                                                                <b>GCS Verbal: </b>{{ getGcsVerbalName(item?.verbal_score || '-') }},
                                                                <b>GCS Motorik: </b>{{ getGcsMotoricName(item?.motor_score || '-') }},
                                                                <b>Warna Kulit: </b>{{ getColorSkinName(item?.skin_color || '-') }},
                                                                <b>Perilaku: </b>{{ getBehaviorName(item?.behavior || '-') }},
                                                            </div>
                                                            <div class="mt-8">
                                                                <b>BB: </b>{{ item.weight }},
                                                                <b>TB: </b>{{ item.height }},
                                                                <b>IMT: </b>{{ item.imt }},
                                                                <b>L.Kepala: </b>{{ item.head_circumference }},
                                                                <b>L.Dada: </b>{{ item.chest_circumference }},
                                                                <b>L.Lengan Atas: </b>{{ item.arm_circumference }},
                                                                <b>L.Perut: </b>{{ item.stomach_circumference }},
                                                                <b>L.Lutut: </b>{{ item.knee_circumference }},
                                                            </div>
                                                        </div>
                                                    </section>
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">ST GENERALIS</div>
                                                                <template v-if="allowedToCopySoap('GENERALIS')">
                                                                    <b-button size="sm" @click="copySelected($event, soapLocalis, item, 'GENERALIS')" variant="w3green" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div><b>Status Lokalis: </b>{{ item?.localist_status || '-' }}</div>
                                                        </div>
                                                    </section>
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">DIAGNOSA</div>
                                                                <template v-if="allowedToCopySoap('DIAGNOSIS')">
                                                                    <b-button @click="copySelected($event, soapDiagnosis, item, 'DIAGNOSIS')" size="sm" variant="w3green" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div><b>Catatan ICD10 : </b>{{ item?.icd10_notes || '-' }}</div>
                                                            <div>
                                                                <div><b>ICD10: </b><span v-if="!item.icd10 || item.icd10.length == 0">-</span></div>
                                                                <template v-if="item.icd10?.length > 0">
                                                                    <li v-for="icd10 in item.icd10">{{ icd10.code }} {{ icd10.name }}</li>
                                                                </template>
                                                            </div>
                                                            <div class="mt-8">
                                                                <div><b>Non-ICD10: </b><span v-if="!item.non_icd10 || item.non_icd10?.length == 0">-</span></div>
                                                                <template v-if="item.non_icd10?.length > 0">
                                                                    <li v-for="list in item.non_icd10">{{ list }}</li>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </section>
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">PROSEDUR</div>
                                                                <template v-if="allowedToCopySoap('PROCEDURE')">
                                                                    <b-button @click="copySelected($event, soapProcedure, item, 'PROCEDURE')" size="sm" variant="w3green" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div>
                                                                <div>
                                                                    <b>PEMERIKSAAN: </b>
                                                                    <span v-if="!item.transaction_doctor || item.transaction_doctor.length == 0">-</span>
                                                                </div>
                                                                <template v-if="item.transaction_doctor?.length > 0">
                                                                    <li v-for="list in item.transaction_doctor">{{ list.costing_name }}</li>
                                                                </template>
                                                            </div>
                                                            <div>
                                                                <template v-if="item.soap_p?.length > 0">
                                                                    <div v-for="(list, index_i) in item.soap_p" :class="index_i == 0 ? 'mt-8' : 'mt-1'">
                                                                        <div>
                                                                            <div>
                                                                                <b>ICD9CM: </b>
                                                                                <span v-if="!list.icd9cm || list.icd9cm.length == 0">-</span>
                                                                            </div>
                                                                            <template v-if="list.icd9cm?.length > 0">
                                                                                <li v-for="unit in list.icd9cm">{{ unit.code }} {{ unit.name }}</li>
                                                                            </template>
                                                                        </div>
                                                                        <div><b>Catatan ICD9CM : </b>{{ list?.therapy_plan || '-' }}</div>
                                                                    </div>
                                                                </template>
                                                                <div v-else class="mt-8">
                                                                    <div><b>ICD9CM: -</b></div>
                                                                    <div><b>CATATAN ICD9CM: -</b></div>
                                                                </div>
                                                            </div>
                                                            <div><b>Advice: </b>{{item?.advice || '-'}}</div>
                                                        </div>
                                                    </section>
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">LABORATORIUM</div>
                                                                <template v-if="allowedToCopySoap('LABORATORY') && lab_status == 1">
                                                                    <b-button @click="copySelected($event, soapLaboratory, item, 'LABORATORY')" size="sm" variant="w3green" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div>
                                                                <b>LABORATORIUM: </b>
                                                                <span v-if="!item.laboratory_request || item.laboratory_request.length == 0">-</span>
                                                            </div>
                                                            <div v-if="item?.laboratory_request?.length > 0" class="mt-2">
                                                                <template v-for="list in item.laboratory_request">
                                                                    <div>{{ list?.no_request }}</div>
                                                                    <li v-for="unit in list.laboratory_request_items">
                                                                        <template v-if="unit.result?.length > 0">
                                                                            <feather-icon icon="EyeIcon" @click="triggerLabResult({ alias_name: regData.alias_name, name: regData.name, result: unit.result, costing_name: unit.laboratory_package_name, laboratory_request_id: unit.laboratory_request_id })" color="blue"/>
                                                                        </template>
                                                                        {{ unit.laboratory_package_name }}
                                                                    </li>
                                                                    <div class="mt-2" v-if="list?.clinical_indications">
                                                                        <b>Catatan:</b>
                                                                        <div class="bg-neutral-100 p-2 rounded">{{ list.clinical_indications }}</div>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </section>
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">RADIOLOGI</div>
                                                                <template v-if="allowedToCopySoap('RADIOLOGY') && rad_status == 1">
                                                                    <b-button @click="copySelected($event, soapRadiology, item, 'RADIOLOGY')" size="sm" variant="w3green" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div>
                                                                <b>RADIOLOGI: </b>
                                                                <span v-if="!item.radiology_request || item.radiology_request.length == 0">-</span>
                                                            </div>
                                                            <div v-if="item?.radiology_request?.length > 0">
                                                                <template v-for="list in item.radiology_request">
                                                                    <div class="mt-2">{{ list?.no_request }}</div>
                                                                    <li v-for="unit in list.radiology_request_items">
                                                                        <template v-if="unit.url?.length > 0">
                                                                            <feather-icon icon="EyeIcon" @click="triggerRadResult({ alias_name: regData.alias_name, name: regData.name, url: unit.url, costing_name: unit.radiology_package_name })" color="blue"/>
                                                                        </template>
                                                                        {{ unit.radiology_package_name }}
                                                                    </li>
                                                                    <div class="mt-2" v-if="list?.clinical_indications">
                                                                        <b>Catatan Klinis:</b>
                                                                        <div class="bg-neutral-100 p-2 rounded">{{ list.clinical_indications }}</div>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </section>
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">FISIOTERAPI</div>
                                                                <template v-if="allowedToCopySoap('PHYSIOTHERAPY')">
                                                                    <b-button @click="copySelected($event, soapPhysiotherapy, item, 'PHYSIOTHERAPY')" size="sm" variant="w3green" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div>
                                                                <b>FISIOTERAPI: </b>
                                                                <span v-if="!item.physiotherapy_request || item.physiotherapy_request.length == 0">-</span>
                                                            </div>
                                                            <div v-if="item?.physiotherapy_request?.length > 0">
                                                                <template v-for="list in item.physiotherapy_request">
                                                                    <div class="mt-2">{{ list?.no_request }}</div>
                                                                    <li v-for="unit in list.physiotherapy_request_items">{{ unit.physiotherapy_package_name }}</li>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </section>
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">RESEP</div>
                                                                <template v-if="allowedToCopySoap('PRESC')">
                                                                    <b-button @click="copySelected($event, soapPrescription, item, 'PRESC')" size="sm" variant="w3green" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div v-if="item?.prescription" class="mt-1">
                                                                <template v-for="(list, presc_i) in item.prescription">
                                                                    <div :class="presc_i > 0 ? 'mt-2' : ''">{{ list?.no_order }}</div>
                                                                    <li v-for="unit in list.form_prescription">
                                                                        {{ unit?.pharmacy_catalog_name }}
                                                                        <span v-if="unit.qty">, qty: {{ unit?.qty }} {{ unit?.package_name }}</span>
                                                                        <span v-if="unit.used"> {{ unit?.used }}</span>
                                                                    </li>
                                                                </template>
                                                            </div>
                                                            <template v-else>-</template>
                                                        </div>
                                                    </section>
                                                </b-card-body>
                                            </b-collapse>
                                        </b-card>
                                    </div>
                                    <template v-if="soap_load.limit_display < soap_load.count">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <div @click="loadMoreSoap" class="font-weight-bold pointer fs-13 text-center py-1 load-text">
                                                <a class="mr-2">Load more</a>
                                                <feather-icon icon="ArrowDownIcon" size="14"/>
                                            </div>
                                        </div>
                                    </template>
                                </b-tab>
                                <b-tab title="PERAWAT">
                                    <div v-for="(item, index) in history.nurse" class="accordion" role="tablist">
                                        <b-card no-body class="mb-1">
                                            <b-card-header block v-b-toggle="'accordion-ns-'+item.id" header-tag="header" class="pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#03a972'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                                <div>
                                                    <div class="font-weight-bold">PERAWAT {{ localDateTime(item.date_added) }}</div>
                                                    <div>{{ item.employee_nurse_name }} | {{ item.health_facility_item_name }}</div>
                                                </div>
                                                <feather-icon icon="ChevronDownIcon" size="18" />
                                            </b-card-header>
                                            <b-collapse :id="`accordion-ns-`+item.id" accordion="ns-accordion" role="tabpanel">
                                                <b-card-body class="p-12 bg-info-300">
                                                    <section class="card">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">ANAMNESA</div>
                                                                <!-- <b-button @click="copySelected($event, nurseAnamnesa, item, 'ANAMNESA')" size="sm" variant="w3green" style="font-size: 12px; line-height:120%">COPY</b-button> -->
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div><b>Anamnesa: </b>{{ item.anamesa_type_id == '1' ? 'Auto Anamnesa' : 'Allo Anamnesa' }}</div>
                                                            <div><b>Keluhan Utama: </b>{{ item?.main_complaint || '-' }}</div>
                                                            <div><b>Nama Keluarga: </b>{{ item?.family_name || '-' }}</div>
                                                            <div><b>Hubungan Keluarga: </b>{{ getFamilyName(item?.family_relation_id || 0) }}</div>
                                                            <div><b>R.Penyakit Sekarang: </b>{{ item?.history_disease_now || '-' }}</div>
                                                            <div><b>R.Penyakit Dahulu: </b>{{ item?.history_disease_past || '-' }}</div>
                                                        </div>
                                                    </section>
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">TANDA VITAL</div>
                                                                <b-button @click="copySelected($event, nurseVitalSign, item, 'VITAL')" size="sm" variant="w3green" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div class="">
                                                                <b>Systolic: </b>{{ item.systolic_blood_pressure }},
                                                                <b>Diastolic: </b>{{ item.diastolic_blood_pressure }},
                                                                <b>Nadi: </b>{{ item.pulse_frequency }},
                                                                <b>Nafas: </b>{{ item.breath_frequency }},
                                                                <b>Saturasi: </b>{{ item.oximetry_frequency }},
                                                                <b>Suhu: </b>{{ item.temperature }},
                                                                <b>GD: </b>{{ item.blood_sugar }},
                                                                <b>Oksigenasi: </b>{{ getOxygenName(item?.oxygen_type_id || '-') }},
                                                                <b>Kesadaran:</b> {{ getAwareName(item?.aware) }},
                                                                <b>GCS Mata: </b>{{ getGcsEyeName(item?.eye_score || '-') }},
                                                                <b>GCS Verbal: </b>{{ getGcsVerbalName(item?.verbal_score || '-') }},
                                                                <b>GCS Motorik: </b>{{ getGcsMotoricName(item?.motor_score || '-') }},
                                                                <b>Warna Kulit: </b>{{ getColorSkinName(item?.skin_color || '-') }},
                                                                <b>Perilaku: </b>{{ getBehaviorName(item?.behavior || '-') }},
                                                            </div>
                                                            <div class="mt-8">
                                                                <b>BB: </b>{{ item.weight }},
                                                                <b>TB: </b>{{ item.height }},
                                                                <b>IMT: </b>{{ item.imt }},
                                                                <b>L.Kepala: </b>{{ item.head_circumference }},
                                                                <b>L.Dada: </b>{{ item.chest_circumference }},
                                                                <b>L.Lengan Atas: </b>{{ item.arm_circumference }},
                                                                <b>L.Perut: </b>{{ item.stomach_circumference }},
                                                                <b>L.Lutut: </b>{{ item.knee_circumference }},
                                                            </div>
                                                        </div>
                                                    </section>
                                                </b-card-body>
                                            </b-collapse>
                                        </b-card>
                                    </div>
                                    <template v-if="nurse_load.limit_display < nurse_load.count">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <div @click="loadMoreNurse" class="font-weight-bold pointer fs-13 text-center py-1 load-text">
                                                <a class="mr-2">Load more</a>
                                                <feather-icon icon="ArrowDownIcon" size="14"/>
                                            </div>
                                        </div>
                                    </template>
                                </b-tab>
                                <b-tab title="CPPT" active>
                                    <div v-for="(item, index) in history.cppt" :key="'cppt-'+item.id" class="accordion" role="tablist">
                                        <b-card no-body class="mb-1">
                                            <b-card-header block v-b-toggle="`accordion-cppt-${item.id}`" header-tag="header" class="pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                                <div>
                                                    <div class="font-weight-bold">CPPT {{ localDateTime(item.date_added) }}</div>
                                                    <div>{{ item.user_added_name }} | {{ item.health_facility_item_name }}</div>
                                                </div>
                                                <feather-icon icon="ChevronDownIcon" size="18" />
                                            </b-card-header>
                                            <b-collapse :id="`accordion-cppt-${item.id}`" accordion="accordion-cppt" role="tabpanel">
                                                <b-card-body class="p-12 bg-info-300">
                                                    <template v-if="item.group_name == 'Doctor' || item.group_name == 'Nurse' || item.group_name == 'Super User'">
                                                        <section class="card">
                                                            <div class="card-header px-12 py-8">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div class="text-bold-14">SUBJECTIVE</div>
                                                                    <template v-if="allowedToCopyCppt('ANAMNESA')">
                                                                        <b-button size="sm" @click="copySelected($event, cpptSubjective, item, 'ANAMNESA')" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="card-body p-12">
                                                                <div><b>Subjective: </b>{{ item?.subjective || '-'}}</div>
                                                                <div v-if="item.group_name == 'Nurse'"><b>Objective: </b>{{ item?.nursing_objective || '-'}}</div>
                                                            </div>
                                                        </section>
                                                        <section class="card mt-16">
                                                            <div class="card-header px-12 py-8">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div class="text-bold-14">TANDA VITAL</div>
                                                                    <template v-if="allowedToCopyCppt('VITAL')">
                                                                        <b-button @click="copySelected($event, cpptVitalSign, item, 'VITAL')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="card-body p-12">
                                                                <div class="">
                                                                    <b>Systolic: </b>{{ item.systolic_blood_pressure }},
                                                                    <b>Diastolic: </b>{{ item.diastolic_blood_pressure }},
                                                                    <b>Nadi: </b>{{ item.pulse_frequency }},
                                                                    <b>Nafas: </b>{{ item.breath_frequency }},
                                                                    <b>Saturasi: </b>{{ item.oximetry_frequency }},
                                                                    <b>Suhu: </b>{{ item.temperature }},
                                                                    <b>GD: </b>{{ item.blood_sugar }},
                                                                    <b>Oksigenasi: </b>{{ getOxygenName(item?.oxygen_type_id || '-') }},
                                                                    <b>Kesadaran:</b> {{ getAwareName(item?.aware) }},
                                                                    <b>GCS Mata: </b>{{ getGcsEyeName(item?.eye_score || '-') }},
                                                                    <b>GCS Verbal: </b>{{ getGcsVerbalName(item?.verbal_score || '-') }},
                                                                    <b>GCS Motorik: </b>{{ getGcsMotoricName(item?.motor_score || '-') }},
                                                                    <b>Warna Kulit: </b>{{ getColorSkinName(item?.skin_color || '-') }},
                                                                    <b>Perilaku: </b>{{ getBehaviorName(item?.behavior || '-') }},
                                                                    <b>BB: </b>{{ item.weight }},
                                                                    <b>TB: </b>{{ item.height }},
                                                                    <b>IMT: </b>{{ item.imt }},
                                                                </div>
                                                            </div>
                                                        </section>
                                                    </template>

                                                    <template v-if="item.group_name == 'Doctor' || item.group_name == 'Super User'">
                                                        <section class="card mt-16">
                                                            <div class="card-header px-12 py-8">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div class="text-bold-14">ST GENERALIS</div>
                                                                    <template v-if="allowedToCopyCppt('GENERALIS')">
                                                                        <b-button size="sm" @click="copySelected($event, cpptLocalis, item, 'GENERALIS')" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="card-body p-12">
                                                                <div><b>Status Lokalis: </b>{{ item?.localist_status || '-' }}</div>
                                                            </div>
                                                        </section>
                                                        <section class="card mt-16">
                                                            <div class="card-header px-12 py-8">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div class="text-bold-14">DIAGNOSA</div>
                                                                    <template v-if="allowedToCopyCppt('DIAGNOSIS')">
                                                                        <b-button @click="copySelected($event, cpptDiagnosis, item, 'DIAGNOSIS')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="card-body p-12">
                                                                <div><b>Catatan ICD10 : </b>{{ item?.icd10_notes || '-' }}</div>
                                                                <div>
                                                                    <div><b>ICD10: </b><span v-if="!item.icd10 || item.icd10.length == 0">-</span></div>
                                                                    <template v-if="item.icd10?.length > 0">
                                                                        <li v-for="icd10 in item.icd10">{{ icd10.icd10_code }} {{ icd10.icd10_name }}</li>
                                                                    </template>
                                                                </div>
                                                                <div class="mt-8">
                                                                    <div><b>Non-ICD10: </b><span v-if="!item.non_icd10 || item.non_icd10?.length == 0">-</span></div>
                                                                    <template v-if="item.non_icd10?.length > 0">
                                                                        <li v-for="list in item.non_icd10">{{ list }}</li>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </section>
                                                        <section class="card mt-16">
                                                            <div class="card-header px-12 py-8">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div class="text-bold-14">PROSEDUR</div>
                                                                    <template v-if="allowedToCopyCppt('PROCEDURE')">
                                                                        <b-button @click="copySelected($event, cpptProcedure, item, 'PROCEDURE')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="card-body p-12">
                                                                <div>
                                                                    <div>
                                                                        <b>PEMERIKSAAN: </b>
                                                                        <span v-if="!item.transaction_doctor || item.transaction_doctor.length == 0">-</span>
                                                                    </div>
                                                                    <template v-if="item.transaction_doctor?.length > 0">
                                                                        <li v-for="list in item.transaction_doctor">{{ list.costing_name }}</li>
                                                                    </template>
                                                                </div>
                                                                <div class="mt-1">
                                                                    <template v-if="item.icd9cm?.length > 0">
                                                                        <div><b>ICD9CM: </b></div>
                                                                        <li v-for="unit in item.icd9cm">{{ unit.icd9cm_code }} {{ unit.icd9cm_name }}</li>
                                                                    </template>
                                                                    <div v-else><b>ICD9CM: </b>-</div>
                                                                </div>
                                                                <div class="mt-1"><b>CATATAN ICD9CM: </b>{{ item?.therapy_plan || '-' }}</div>
                                                            </div>
                                                        </section>
                                                        <section class="card mt-16">
                                                            <div class="card-header px-12 py-8">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div class="text-bold-14">PLAN</div>
                                                                    <template v-if="allowedToCopyCppt('PLAN')">
                                                                        <b-button size="sm" @click="copySelected($event, cpptPlan, item, 'PLAN')" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="card-body p-12">
                                                                <div><b>Plan Non-Farmakologis: </b>{{ item?.plan || '-' }}</div>
                                                            </div>
                                                            <div class="card-body p-12">
                                                                <div><b>Plan Farmakologis: </b>{{ item?.pharmacological_plan || '-' }}</div>
                                                            </div>
                                                        </section>
                                                        <section class="card mt-16">
                                                            <div class="card-header px-12 py-8">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div class="text-bold-14">LABORATORIUM</div>
                                                                    <template v-if="allowedToCopyCppt('LABORATORY') && lab_status == 1">
                                                                        <b-button @click="copySelected($event, cpptLaboratory, item, 'LABORATORY')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="card-body p-12">
                                                                <div>
                                                                    <b>LABORATORIUM: </b>
                                                                    <span v-if="!item.laboratory_request || item.laboratory_request.length == 0">-</span>
                                                                </div>
                                                                <div v-if="item?.laboratory_request?.length > 0" class="mt-2">
                                                                    <template v-for="list in item.laboratory_request">
                                                                        <div>{{ list?.no_request }}</div>
                                                                        <li v-for="unit in list.laboratory_request_items">
                                                                            <template v-if="unit.result?.length > 0">
                                                                                <feather-icon icon="EyeIcon" @click="triggerLabResult({ alias_name: regData.alias_name, name: regData.name, result: unit.result, costing_name: unit.laboratory_package_name, laboratory_request_id: unit.laboratory_request_id })" color="blue"/>
                                                                            </template>
                                                                            {{ unit.laboratory_package_name }}
                                                                        </li>
                                                                        <div class="mt-2" v-if="list?.clinical_indications">
                                                                            <b>Catatan:</b>
                                                                            <div class="bg-neutral-100 p-2 rounded">{{ list.clinical_indications }}</div>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </section>
                                                        <section class="card mt-16">
                                                            <div class="card-header px-12 py-8">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div class="text-bold-14">RADIOLOGI</div>
                                                                    <template v-if="allowedToCopyCppt('RADIOLOGY') && rad_status == 1">
                                                                        <b-button @click="copySelected($event, cpptRadiology, item, 'RADIOLOGY')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="card-body p-12">
                                                                <div>
                                                                    <b>RADIOLOGI: </b>
                                                                    <span v-if="!item.radiology_request || item.radiology_request.length == 0">-</span>
                                                                </div>
                                                                <div v-if="item?.radiology_request?.length > 0">
                                                                    <template v-for="list in item.radiology_request">
                                                                        <div class="mt-2">{{ list?.no_request }}</div>
                                                                        <li v-for="unit in list.radiology_request_items">
                                                                            <template v-if="unit.url?.length > 0">
                                                                                <feather-icon icon="EyeIcon" @click="triggerRadResult({ alias_name: regData.alias_name, name: regData.name, url: unit.url, costing_name: unit.radiology_package_name })" color="blue"/>
                                                                            </template>
                                                                            {{ unit.radiology_package_name }}
                                                                        </li>
                                                                        <div class="mt-2" v-if="list?.clinical_indications">
                                                                            <b>Catatan Klinis:</b>
                                                                            <div class="bg-neutral-100 p-2 rounded">{{ list.clinical_indications }}</div>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </section>
                                                        <section class="card mt-16">
                                                            <div class="card-header px-12 py-8">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div class="text-bold-14">FISIOTERAPI</div>
                                                                    <template v-if="allowedToCopyCppt('PHYSIOTHERAPY')">
                                                                        <b-button @click="copySelected($event, cpptPhysiotherapy, item, 'PHYSIOTHERAPY')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="card-body p-12">
                                                                <div>
                                                                    <b>FISIOTERAPI: </b>
                                                                    <span v-if="!item.physiotherapy_request || item.physiotherapy_request.length == 0">-</span>
                                                                </div>
                                                                <div v-if="item?.physiotherapy_request?.length > 0">
                                                                    <template v-for="list in item.physiotherapy_request">
                                                                        <div class="mt-2">{{ list?.no_request }}</div>
                                                                        <li v-for="unit in list.physiotherapy_request_items">{{ unit.physiotherapy_package_name }}</li>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </section>
                                                        <section class="card mt-16">
                                                            <div class="card-header px-12 py-8">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div class="text-bold-14">RESEP</div>
                                                                    <template v-if="allowedToCopyCppt('PRESC')">
                                                                        <b-button @click="copySelected($event, soapPrescription, item, 'PRESC')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="card-body p-12">
                                                                <div v-if="item?.prescription" class="mt-1">
                                                                    <template v-for="(list, presc_i) in item.prescription">
                                                                        <div :class="presc_i > 0 ? 'mt-2' : ''">{{ list?.no_order }}</div>
                                                                        <li v-for="unit in list.form_prescription">
                                                                            {{ unit?.pharmacy_catalog_name }}
                                                                            <span v-if="unit.qty">, qty: {{ unit?.qty }} {{ unit?.package_name }}</span>
                                                                            <span v-if="unit.used"> {{ unit?.used }}</span>
                                                                        </li>
                                                                    </template>
                                                                </div>
                                                                <template v-else>-</template>
                                                            </div>
                                                        </section>
                                                    </template>

                                                    <template v-else-if="item.group_name == 'Nurse'">
                                                        <section class="card mt-16">
                                                            <div class="card-header px-12 py-8">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div class="text-bold-14">DIAGNOSA</div>
                                                                    <template v-if="allowedToCopyCppt('NSDIAGNOSIS')">
                                                                        <b-button size="sm" @click="copySelected($event, cpptNsDiagnosis, item, 'NSDIAGNOSIS')" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="card-body p-12">
                                                                <div><b>Diagnosa: </b>{{ item?.nursing_diagnose || '-'}}</div>
                                                            </div>
                                                        </section>
                                                        <section class="card mt-16">
                                                            <div class="card-header px-12 py-8">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div class="text-bold-14">PLAN</div>
                                                                    <template v-if="allowedToCopyCppt('NSPLAN')">
                                                                        <b-button size="sm" @click="copySelected($event, cpptNsPlan, item, 'NSPLAN')" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="card-body p-12">
                                                                <div><b>Plan: </b>{{ item?.nursing_plan || '-'}}</div>
                                                            </div>
                                                        </section>
                                                    </template>

                                                    <template v-else-if="item.group_name == 'Gizi'">
                                                        <section class="card mt-16">
                                                            <div class="card-header px-12 py-8">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div class="text-bold-14">GIZI</div>
                                                                    <template v-if="allowedToCopyCppt('NUTRITION')">
                                                                        <b-button size="sm" @click="copySelected($event, cpptNutrition, item, 'NUTRITION')" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="card-body p-12">
                                                                <div><b>Asesmen: </b>{{ item?.nutrition_assessment || '-'}}</div>
                                                                <div><b>Diagnosis: </b>{{ item?.nutrition_diagnosis || '-'}}</div>
                                                                <div><b>Intervensi: </b>{{ item?.nutrition_intervention || '-'}}</div>
                                                                <div><b>Monitoring & Evaluasi: </b>{{ item?.nutrition_monitoring_evaluation || '-'}}</div>
                                                            </div>
                                                        </section>
                                                    </template>
                                                </b-card-body>
                                            </b-collapse>
                                        </b-card>
                                    </div>
                                    <template v-if="cppt_load.limit_display < cppt_load.count">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <div @click="loadMoreCppt" class="font-weight-bold pointer fs-13 text-center py-1 load-text">
                                                <a class="mr-2">Load more</a>
                                                <feather-icon icon="ArrowDownIcon" size="14"/>
                                            </div>
                                        </div>
                                    </template>
                                </b-tab>
                                <b-tab title="RESUME">
                                    <div v-for="(item, index) in history.resume" class="accordion" role="tablist">
                                        <b-card no-body class="mb-1">
                                            <b-card-header block v-b-toggle="'accordion-resume-'+item.id" header-tag="header" class="pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                                <div>
                                                    <div class="font-weight-bold">RESUME {{ localDateTime(item.date_added) }}</div>
                                                    <div>{{ item.doctor_in_charge_name }} | {{ item.health_facility_item_name }}</div>
                                                </div>
                                                <feather-icon icon="ChevronDownIcon" size="18" />
                                            </b-card-header>
                                            <b-collapse :id="`accordion-resume-`+item.id" accordion="resume-accordion" role="tabpanel">
                                                <b-card-body class="p-12 bg-info-200">
                                                    <div>
                                                        <div class="font-weight-bold">Indikasi Rawat Inap</div>
                                                        <div>{{ item?.indication_for_hospitalization }}</div>
                                                    </div>
                                                    <div class="mt-12">
                                                        <div class="font-weight-bold">Riwayat Ringkasan Penyakit</div>
                                                        <div>{{ item?.summary_medical_history }}</div>
                                                    </div>
                                                    <div class="mt-12">
                                                        <div class="font-weight-bold">Hasil Pemeriksaan Fisik Penting dan Temuan Lain</div>
                                                        <div>{{ item?.physical_check }}</div>
                                                    </div>
                                                    <div class="mt-12">
                                                        <div class="font-weight-bold">Diagnosis ICD10</div>
                                                        <template v-for="unit in item?.icd10">
                                                            <li>{{ unit.icd10_code }} {{ unit.icd10_name }}</li>
                                                        </template>
                                                    </div>
                                                    <div class="mt-12">
                                                        <div class="font-weight-bold">Tindakan ICD9CM</div>
                                                        <template v-for="unit in item?.icd9cm">
                                                            <li>{{ unit.icd9cm_code }} {{ unit.icd9cm_name }}</li>
                                                        </template>
                                                    </div>
                                                    <div class="mt-12">
                                                        <div class="font-weight-bold">Laboratorium</div>
                                                        <div v-for="list in item.laboratory_request_items">
                                                            <li>
                                                                <template v-if="list?.result?.length > 0">
                                                                    <feather-icon icon="EyeIcon" @click="triggerLabResult({ alias_name: regData.alias_name, name: regData.name, result: list.result, url: list.url,costing_name: list.costing_name, laboratory_request_id: list.laboratory_request_id })" color="blue" class="pointer"/>
                                                                </template>
                                                                {{ list.costing_name }}
                                                            </li>
                                                        </div>
                                                    </div>
                                                    <div class="mt-12">
                                                        <div class="font-weight-bold">Radiologi</div>
                                                        <template v-if="item.radiology_request_items">
                                                            <li v-for="list in item.radiology_request_items">
                                                                <template v-if="list.url?.length > 0">
                                                                    <feather-icon icon="EyeIcon" @click="triggerRadResult({ alias_name: regData.alias_name, name: regData.name, url: list.url, costing_name: list.costing_name })" color="blue" class="pointer"/>
                                                                </template>
                                                                {{ list.costing_name }}
                                                            </li>
                                                        </template>
                                                    </div>
                                                    <div class="mt-12">
                                                        <div class="font-weight-bold">Obat-obatan selama perawatan</div>
                                                        <div>{{ item?.significant_medications }}</div>
                                                    </div>
                                                    <div class="mt-12">
                                                        <div class="font-weight-bold">Obat yang dibawa pulang</div>
                                                        <div>{{ item?.take_home_medications }}</div>
                                                    </div>
                                                    <div class="mt-12">
                                                        <div class="font-weight-bold">Cara pulang</div>
                                                        <div>
                                                            <span>{{ getResExit(item?.exit_id) }}</span>
                                                            <span v-if="item?.exit_id == 'adb7ce0b-ede3-4fee-b273-ce159d286732'"> : {{ item?.exit_anythingelse }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="mt-12">
                                                        <div class="font-weight-bold">Kondisi saat pulang</div>
                                                        <div>
                                                            <span>{{ getResExitConditionName(item?.exit_condition_type_id) }}</span>
                                                            <span v-if="item?.exit_condition_type_id == 4"> : {{ item?.exit_condition_anythingelse }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="mt-12">
                                                        <div class="font-weight-bold">Kontrol Pasca Rawat Inap</div>
                                                        <div>
                                                            <span>{{ getResControlInstructionName(item?.care_instruction_control) }}</span>
                                                            <span v-if="item?.care_instruction_control == 1">, tanggal {{ localDate(item?.care_instruction_control_date) }} di {{ item?.health_facility_item_name_control }}</span>
                                                            <span v-if="item?.care_instruction_control == 4"> : {{ item?.care_instruction_anythingelse }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="mt-12">
                                                        <div class="font-weight-bold">Edukasi selama di rumah</div>
                                                        <div>{{ item?.home_education }}</div>
                                                    </div>
                                                </b-card-body>
                                            </b-collapse>
                                        </b-card>
                                    </div>
                                    <template v-if="rsm_load.limit_display < rsm_load.count">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <div @click="loadMoreResume" class="font-weight-bold pointer fs-13 text-center py-1 load-text">
                                                <a class="mr-2">Load more</a>
                                                <feather-icon icon="ArrowDownIcon" size="14"/>
                                            </div>
                                        </div>
                                    </template>
                                </b-tab>
                                <b-tab title="LABORATORIUM" >
                                    <template v-if="history.laboratory?.length > 0">
                                        <template v-for="(item, index) in history.laboratory">
                                            <section class="card" :class="{ 'mt-16': index > 0 }">
                                                <div class="card-header px-12 py-8">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="font-weight-bold">{{ item?.date_request }}</div>
                                                            <div>{{ item?.doctor_in_charge_name }} | {{ item.health_facility_item_name }}</div>
                                                        </div>
                                                        <template v-if="allowedToCopySupport() && lab_status == 1">
                                                            <b-button @click="copySelected($event, copyLaboratory, item, 'LABORATORY')" size="sm" variant="primary" class="fs-12" style="line-height:120%">COPY</b-button>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="card-body p-12">
                                                    <template v-if="item.laboratory_request_items">
                                                        <li v-for="unit in item.laboratory_request_items">
                                                            <template v-if="unit.result?.length > 0">
                                                                <feather-icon icon="EyeIcon" @click="triggerLabResult({ alias_name: regData.alias_name, name: regData.name, result: unit.result, costing_name: unit.laboratory_package_name, laboratory_request_id: unit.laboratory_request_id })" color="blue" class="pointer"/>
                                                            </template>
                                                            {{ unit.laboratory_package_name }}
                                                        </li>
                                                    </template>
                                                    <div class="mt-2" v-if="item.clinical_indications">
                                                        <b>Catatan:</b>
                                                        <div class="bg-neutral-100 p-2 rounded">{{ item.clinical_indications }}</div>
                                                    </div>
                                                </div>
                                            </section>
                                        </template>
                                        <template v-if="lab_load.limit_display < lab_load.count">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <div @click="loadMoreLaboratory" class="font-weight-bold pointer fs-13 text-center py-1 load-text">
                                                    <a class="mr-2">Load more</a>
                                                    <feather-icon icon="ArrowDownIcon" size="14"/>
                                                </div>
                                            </div>
                                        </template>
                                    </template>
                                    <div v-else class="text-center" style="font-style:italic">Tidak ditemukan riwayat laboratorium</div>
                                </b-tab>
                                <b-tab title="RADIOLOGI">
                                    <template v-if="history.radiology?.length > 0">
                                        <template v-for="(item, index) in history.radiology">
                                            <section class="card" :class="{ 'mt-16': index > 0 }">
                                                <div class="card-header px-12 py-8">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="font-weight-bold">{{ item?.date_request }}</div>
                                                            <div>{{ item?.doctor_in_charge_name }} | {{ item.health_facility_item_name }}</div>
                                                        </div>
                                                        <template v-if="allowedToCopySupport() && rad_status == 1">
                                                            <b-button @click="copySelected($event, copyRadiology, item, 'RADIOLOGY')" size="sm" variant="primary" class="fs-12" style="line-height:120%">COPY</b-button>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="card-body p-12">
                                                    <template v-if="item.radiology_request_items">
                                                        <li v-for="unit in item.radiology_request_items">
                                                            <template v-if="unit.url?.length > 0">
                                                                <feather-icon icon="EyeIcon" @click="triggerRadResult({ alias_name: regData.alias_name, name: regData.name, url: unit.url, costing_name: unit.radiology_package_name })" color="blue" class="pointer"/>
                                                            </template>
                                                            {{ unit.radiology_package_name }}
                                                        </li>
                                                    </template>
                                                    <div class="mt-2" v-if="item.clinical_indications">
                                                        <b>Catatan Klinis:</b>
                                                        <div class="bg-neutral-100 p-2 rounded">{{ item.clinical_indications }}</div>
                                                    </div>
                                                </div>
                                            </section>
                                        </template>
                                        <template v-if="rad_load.limit_display < rad_load.count">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <div @click="loadMoreRadiology" class="font-weight-bold pointer fs-13 text-center py-1 load-text">
                                                    <a class="mr-2">Load more</a>
                                                    <feather-icon icon="ArrowDownIcon" size="14"/>
                                                </div>
                                            </div>
                                        </template>
                                    </template>
                                    <div v-else class="text-center" style="font-style:italic">Tidak ditemukan riwayat radiologi</div>
                                </b-tab>
                                <b-tab title="FISIOTERAPI">
                                    <template v-if="history.physiotherapy?.length > 0">
                                        <template v-for="(item, index) in history.physiotherapy">
                                            <section class="card" :class="{ 'mt-16': index > 0 }">
                                                <div class="card-header px-12 py-8">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="font-weight-bold">{{ item?.date_request }}</div>
                                                            <div>{{ item?.doctor_in_charge_name }} | {{ item.health_facility_item_name }}</div>
                                                        </div>
                                                        <template v-if="allowedToCopySupport()">
                                                            <b-button @click="copySelected($event, copyPhysiotherapy, item, 'PHYSIOTHERAPY')" size="sm" variant="primary" class="fs-12" style="line-height:120%">COPY</b-button>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="card-body p-12">
                                                    <template v-if="item.physiotherapy_request_items">
                                                        <li v-for="unit in item.physiotherapy_request_items">{{ unit.physiotherapy_package_name }}</li>
                                                    </template>
                                                </div>
                                            </section>
                                        </template>
                                        <template v-if="phy_load.limit_display < phy_load.count">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <div @click="loadMorePhysiotherapy" class="font-weight-bold pointer fs-13 text-center py-1 load-text">
                                                    <a class="mr-2">Load more</a>
                                                    <feather-icon icon="ArrowDownIcon" size="14"/>
                                                </div>
                                            </div>
                                        </template>
                                    </template>
                                    <div v-else class="text-center" style="font-style:italic">Tidak ditemukan riwayat fisioterapi</div>
                                </b-tab>
                                <b-tab title="PENUNJANG LAINNYA">
                                    <template v-if="history.examination?.length > 0">
                                        <template v-for="(item, index) in history.examination">
                                            <section class="card" :class="{ 'mt-16': index > 0 }">
                                                <div class="card-header px-12 py-8 font-weight-bold">{{ item.category_examination_name }}</div>
                                                <div class="card-body p-12">
                                                    <template v-if="item?.data?.files?.length > 0">
                                                        <li v-for="list in item.data.files" class="mb-2">
                                                            {{ list.examination_date }}
                                                            <div>
                                                                <b>Expertise: </b>
                                                                <span>{{ list.expertise || '-' }}</span>
                                                            </div>
                                                            <template v-if="isPdfFile(list.filename)">
                                                                <div>
                                                                    <span>Browser tidak mendukung preview PDF, </span>
                                                                    <a :href="urlAsset+list.url" download>Download</a>
                                                                </div>
                                                            </template>
                                                            <div v-else class="w-100" style="position: relative">
                                                                <b-img rounded :src="urlbe+list.url" class="w-100" />
                                                                <b-button @click="expandImage(list.url)" v-b-popover.hover.top="'Expand Gambar'" size="sm" variant="w3green" style="position: absolute; right: 0; top: 0;">
                                                                    <feather-icon icon="ExternalLinkIcon" />
                                                                </b-button>
                                                            </div>
                                                        </li>
                                                    </template>
                                                </div>
                                            </section>
                                        </template>
                                        <template v-if="exa_load.limit_display < exa_load.count">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <div @click="loadMoreExamination" class="font-weight-bold pointer fs-13 text-center py-1 load-text">
                                                    <a class="mr-2">Load more</a>
                                                    <feather-icon icon="ArrowDownIcon" size="14"/>
                                                </div>
                                            </div>
                                        </template>
                                    </template>
                                    <div v-else class="text-center" style="font-style:italic">Tidak ditemukan riwayat penunjang lainnya</div>
                                </b-tab>
                                <b-tab title="TEMPLATE">
                                    <b-card no-body class="mb-1">
                                        <b-card-header block @click="active.advance.template_anam = !active.advance.template_anam" header-tag="header" class="pointer d-flex justify-content-between align-items-center" :class="active.advance.template_anam ? null : 'collapsed'" :aria-expanded="active.advance.template_anam ? 'true' : 'false'" aria-controls="collapse-temp-anamn" role="tab" onMouseOver="this.style.backgroundColor='#03a972'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                            <div>
                                                <div class="font-weight-bold">TEMPLATE ANAMNESA</div>
                                            </div>
                                            <feather-icon icon="ChevronDownIcon" size="18" />
                                        </b-card-header>
                                        <b-collapse id="collapse-temp-anamn" v-model="active.advance.template_anam" accordion="tmp-accordion" role="tabpanel">
                                            <b-card-body class="p-12 bg-info-300">
                                                <b-form-input v-model="anamn_search" @input="filterItemAnamn" size="sm" placeholder="Cari berdasarkan nama template..." />

                                                <template v-if="anamn_filtered?.length > 0">
                                                    <section v-for="(item, index) in anamn_filtered" class="card mt-10">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">{{ item.name }}</div>
                                                                <!-- <b-button @click="copySelected($event, templateAnamnesa, item, 'ANAMNESA')" size="sm" variant="w3green" style="font-size: 12px; line-height:120%">COPY</b-button> -->
                                                            </div>
                                                        </div>
                                                        <div class="card-body py-8 px-12">
                                                            <div><b>Anamnesa: </b>{{ getAnamnesaName(item.anamesa_type_id) }}</div>
                                                            <div><b>Keluhan Utama: </b>{{ item?.main_complaint || '-' }}</div>
                                                            <div><b>Nama Keluarga: </b>{{ item?.family_name || '-' }}</div>
                                                            <div><b>Hubungan Keluarga: </b>{{ getFamilyName(item?.family_relation_id) }}</div>
                                                            <div><b>R.Penyakit Sekarang: </b>{{ item?.history_disease_now || '-' }}</div>
                                                            <div><b>R.Penyakit Dahulu: </b>{{ item?.history_disease_past || '-' }}</div>
                                                        </div>
                                                    </section>
                                                </template>
                                                <div v-else class="text-center mt-32">Tidak ditemukan template anamnesa yang tersimpan</div>
                                            </b-card-body>
                                        </b-collapse>
                                    </b-card>
                                    <b-card no-body class="mb-1">
                                        <b-card-header block @click="active.advance.template_presc = !active.advance.template_presc" header-tag="header" class="pointer d-flex justify-content-between align-items-center" :class="active.advance.template_presc ? null : 'collapsed'" :aria-expanded="active.advance.template_presc ? 'true' : 'false'" aria-controls="collapse-temp-presc" role="tab" onMouseOver="this.style.backgroundColor='#03a972'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                            <div>
                                                <div class="font-weight-bold">TEMPLATE RESEP</div>
                                            </div>
                                            <feather-icon icon="ChevronDownIcon" size="18" />
                                        </b-card-header>
                                        <b-collapse id="collapse-temp-presc" v-model="active.advance.template_presc" accordion="tmp-accordion" role="tabpanel">
                                            <b-card-body class="p-12 bg-info-300">
                                                <template v-if="presc_filtered?.length > 0">
                                                    <b-form-input v-model="presc_search" @input="filterItemPresc" size="sm" placeholder="Cari berdasarkan nama template..." />
                                                    <section v-for="(item, index) in presc_filtered" class="card mt-10">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">{{ item.name }}</div>
                                                                <b-button @click="copySelected($event, templatePrescription, item, 'PRESC')" size="sm" variant="w3green" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                            </div>
                                                        </div>
                                                        <div class="card-body pb-12 pt-0 px-0">
                                                            <template v-if="item?.form_prescription_parent">
                                                                <div v-for="(list, idx_p) in item.form_prescription_parent" :class="idx_p > 0 ? 'mt-12' : ''">
                                                                    <div :class="getPrescParentVariant(list.id)" class="px-12 py-1">
                                                                        <div class="font-weight-bold">{{ getPrescParentName(list.id) }}</div>
                                                                        <div v-if="list.id != 1" class="fs-12 text-italic" style="font-weight: 400">
                                                                            <span>Jumlah: {{ list?.value }}, </span>
                                                                            <span>Kemasan: {{ getParentPackageName(list.package) }}, </span>
                                                                            <span>Aturan: {{ list.used }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div v-if="item?.form_prescription" class="px-12 mt-1">
                                                                        <li v-for="unit in item.form_prescription" v-if="list.id == unit.prescription_item_type_id">{{ unit?.pharmacy_catalog_name }}</li>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                            <template v-else>-</template>
                                                        </div>
                                                    </section>
                                                </template>
                                                <div v-else class="text-center mt-32">Tidak ditemukan template resep yang tersimpan</div>
                                            </b-card-body>
                                        </b-collapse>
                                    </b-card>
                                </b-tab>
                                <b-tab title="AI">
                                    <div class="text-center" style="font-style:italic">under maintenance</div>
                                </b-tab>
                            </b-tabs>
                        </section>
                        <div class="col-4 bg-w3green rounded py-16 px-2 position-sticky" :style="{top: '0', height: `${dynamicHeight}`, overflowY: 'auto'}">

                            <template v-if="active.access == 'doctor' || active.access == 'general_doctor' || active.access == 'nurse'">
                                <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-0" @click="changeTab('ANAMNESA')" :variant="getMenusVariant('ANAMNESA')">ANAMNESA</b-button>
                                <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('VITAL')" :variant="getMenusVariant('VITAL')">TANDA VITAL</b-button>
                            </template>
                            <template v-if="active.access == 'doctor' || active.access == 'general_doctor'">
                                <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('GENERALIS')" :variant="getMenusVariant('GENERALIS')">ST GENERALIS</b-button>
                                <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('DIAGNOSIS')" :variant="getMenusVariant('DIAGNOSIS')">DIAGNOSA</b-button>
                                <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('PROCEDURE')" :variant="getMenusVariant('PROCEDURE')">PROSEDUR</b-button>
                                <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('PLAN')" :variant="getMenusVariant('PLAN')">PLAN</b-button>
                            </template>
                            <template v-if="active.access == 'doctor'">
                                <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('LABORATORY')" :variant="getMenusVariant('LABORATORY')">LABORATORIUM</b-button>
                                <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('RADIOLOGY')" :variant="getMenusVariant('RADIOLOGY')">RADIOLOGI</b-button>
                                <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('PHYSIOTHERAPY')" :variant="getMenusVariant('PHYSIOTHERAPY')">FISIOTERAPI</b-button>
                                <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('PRESC')" :variant="getMenusVariant('PRESC')">RESEP</b-button>
                            </template>

                            <template v-if="active.access == 'nurse'">
                                <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('NSDIAGNOSIS')" :variant="getMenusVariant('NSDIAGNOSIS')">DIAGNOSA</b-button>
                                <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('NSPLAN')" :variant="getMenusVariant('NSPLAN')">PLAN</b-button>
                            </template>

                            <template v-if="active.access == 'nutritionist'">
                                <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('NUTRITION')" :variant="getMenusVariant('NUTRITION')">GIZI</b-button>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="col-8 px-1">
                    <section v-if="active.section == 'ANAMNESA'" class="card">
                        <div class="card-header bg-w3green text-white fs-14 font-weight-bold py-10">ANAMNESA</div>
                        <div class="card-body px-20 py-8" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <div class="row">
                                <div class="col-12 mt-8">
                                        <label class="fs-13 mb-0">Subjective</label>
                                        <textarea v-model.lazy="form_anamnesa.subjective" rows="3" class="form-control form-control-sm" autocomplete="off" />
                                    </div>
                                <div class="col-12 mt-8" v-if="active.access == 'nurse'">
                                    <label class="fs-13 mb-0">Objective</label>
                                        <textarea v-model.lazy="form_anamnesa.nursing_objective" rows="3" class="form-control form-control-sm" autocomplete="off" />
                                    </div>
                                </div>
                            <!-- <template v-if="input_prescription?.type_prescription_id == 2">
                                <b-button @click="triggerToResume()" variant="ax-primary-400">trigger resume</b-button>
                            </template> -->
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-between align-items-center" style="width: 100% !important;">
                            <div class="d-flex align-items-center">
                                <b-button-group size="sm">
                                    <b-button @click="changeSection(active.section, 'VITAL')" :disabled="active.disabled.section" variant="w3green">
                                        Simpan & lanjutkan
                                        <feather-icon icon="ChevronRightIcon" />
                                    </b-button>
                                </b-button-group>
                                <!-- <div class="ml-2 d-flex align-items-center">
                                    <label class="mb-0 mr-2 text-muted" style="font-size: 12px; white-space: nowrap;">Terakhir diperbarui:</label>
                                        <b-form-input
                                        v-model="last_updated"
                                        type="date"
                                        size="sm"
                                        style="width: 160px;"
                                        />
                                </div> -->
                            </div>
                        </div>
                        </section>

                    <section v-if="active.section == 'VITAL'" class="card">
                        <div class="card-header bg-w3green text-white fs-14 font-weight-bold py-10">VITAL</div>
                        <div class="card-body px-20 py-8" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <div class="">
                                <div v-if="active.access == 'doctor'" class="fs-13 text-muted mt-3 mb-4" style="font-style: italic;">
                                    Data Tanda Vital diisi secara otomatis berdasarkan hasil monitoring TTV perawat / dokter sebelumnya. Lakukan perubahan jika diperlukan.
                                </div>
                                <ModernLabel variant="w3green" label="Tanda Vital" />
                                <div class="row mt-8">
                                    <div class="col-lg-3">
                                        <label class="fs-13 mb-0">Systolic <span class="text-danger">*</span></label>
                                        <b-input-group size="sm" append="mmHg">
                                            <b-form-input
                                                type="number"
                                                autocomplete="off"
                                                v-model="form_vital.systolic_blood_pressure"
                                                :required="active.access == 'nurse'"
                                            ></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Diastolic <span class="text-danger">*</span></label>
                                        <b-input-group size="sm" append="mmHg">
                                            <b-form-input
                                                type="number"
                                                autocomplete="off"
                                                v-model="form_vital.diastolic_blood_pressure"
                                                :required="active.access == 'nurse'"
                                            ></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Frekuensi Nadi <span class="text-danger">*</span></label>
                                        <b-input-group size="sm" append="x/mnt">
                                            <b-form-input
                                                type="number"
                                                autocomplete="off"
                                                v-model="form_vital.pulse_frequency"
                                                :required="active.access == 'nurse'"
                                            ></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Frekuensi Nafas <span class="text-danger">*</span></label>
                                        <b-input-group size="sm" append="x/mnt">
                                            <b-form-input type="number" autocomplete="off" v-model="form_vital.breath_frequency" :required="active.access == 'nurse'"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Saturasi Oksigen <span class="text-danger">*</span></label>
                                        <b-input-group size="sm" append="%">
                                            <b-form-input type="number" autocomplete="off" v-model="form_vital.oximetry_frequency" :required="active.access == 'nurse'"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Suhu <span class="text-danger">*</span></label>
                                        <b-input-group size="sm" append="ᵒC">
                                            <b-form-input type="number" autocomplete="off" v-model="form_vital.temperature" :required="active.access == 'nurse'"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Oksigenasi</label>
                                        <v-select v-model="form_vital.oxygen_type_id" :options="OptionsOxygen" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm"></v-select>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Kesadaran</label>
                                        <v-select v-model="form_vital.aware" :options="OptionsAware" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm"></v-select>
                                    </div>
                                    <div v-if="active.access == 'nurse'" class="col-12">
                                        <div class="fs-13 text-muted mt-3 mb-1" style="font-style: italic;">
                                            Data kesadaran dan antropometri diisi secara otomatis berdasarkan data TTV sebelumnya. Lakukan perubahan jika diperlukan.
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">GCS Mata</label>
                                        <v-select v-model="form_vital.eye_score" :options="OptionGcsEye" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm"></v-select>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">GCS verbal</label>
                                        <v-select v-model="form_vital.verbal_score" :options="OptionGcsVerbal" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm"></v-select>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">GCS Motorik</label>
                                        <v-select v-model="form_vital.motor_score" :options="OptionGcsMotoric" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm"></v-select>
                                    </div>
                                    <template v-if="isPews">
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Warna Kulit</label>
                                            <v-select v-model="form_vital.skin_color" :options="optSkinColor" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Perilaku</label>
                                            <v-select v-model="form_vital.behavior" :options="optBehavior" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div>
                                <ModernLabel variant="w3green" label="Antropometri" class="mt-4" />
                                <div class="row mt-8" :class="active.advance.antopometri ? '' : 'mb-8'">
                                    <div class="col-lg-3">
                                        <label class="fs-13 mb-0">Berat Badan</label>
                                        <b-input-group size="sm" append="kg">
                                            <b-form-input type="number" autocomplete="off" @input="imtChanged($event)" v-model="form_vital.weight"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Tinggi Badan</label>
                                        <b-input-group size="sm" append="cm">
                                            <b-form-input type="number" autocomplete="off" @input="imtChanged($event)" v-model="form_vital.height"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">IMT</label>
                                        <b-input-group size="sm" append="kg/m²">
                                            <b-form-input type="number" autocomplete="off" v-model="form_vital.imt" :disabled="true"></b-form-input>
                                        </b-input-group>
                                    </div>
                                </div>
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Objective</label>
                                    <textarea
                                        v-model="form_vital.objective"
                                        rows="3"
                                        class="form-control form-control-sm"
                                        autocomplete="off"
                                        placeholder="Tuliskan objective pemeriksaan..."
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <div class="d-flex">
                                <b-button
                                    @click="changeTab('ANAMNESA')"
                                    :disabled="active.disabled.section"
                                    variant="outline-neutral-700"
                                    class="rounded"
                                    size="sm"
                                >
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button
                                    @click="changeSection(active.section, getFirstNextSection())"
                                    :disabled="active.disabled.section"
                                    variant="w3green"
                                    class="ml-2 rounded"
                                    size="sm"
                                >
                                    Simpan &amp; lanjutkan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                            </div>
                        </div>
                    </section>

                    <section v-if="active.section == 'GENERALIS'" class="card">
                        <div class="card-header bg-w3green text-white fs-14 font-weight-bold py-10">STATUS GENERALIS</div>
                        <div class="card-body px-20 py-8" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <div>
                                <div>
                                    <ModernLabel variant="w3green" label="Kepala" />
                                    <div class="row">
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Kepala</label>
                                            <v-select v-model="form_generalis.head" :options="OptionsHeadCircumference" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Warna Rambut</label>
                                            <v-select v-model="form_generalis.head_hair_color" :options="OptionsHeadHairColor" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Kondisi Rambut</label>
                                            <v-select v-model="form_generalis.head_hair_condition" :options="OptionsHeadHairCondition" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-20">
                                    <ModernLabel variant="w3green" label="Mata" />
                                    <div class="row">
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Conjunctiva</label>
                                            <v-select v-model="form_generalis.eye_conjunctive" :options="OptionsEyeConjunctiva" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Sklera</label>
                                            <v-select v-model="form_generalis.eye_sclera" :options="OptionsEyeSclera" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Pupil</label>
                                            <v-select v-model="form_generalis.eye_pupil" :options="OptionsEyePupil" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Reflek Cahaya</label>
                                            <v-select v-model="form_generalis.eye_light_reflex" :options="OptionsEyeReflect" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                        </div>
                                    </div>
                                </div>
                                    <!-- <b-collapse id="collapse-general" v-model="active.advance.generalis"> -->
                                    <div class="mt-20">
                                        <ModernLabel variant="w3green" label="Mulut" />
                                        <div class="row">
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Mukosa</label>
                                                <v-select v-model="form_generalis.mouth_mucosa" :options="OptionsMouthMukosa" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Perubahan Warna</label>
                                                <v-select v-model="form_generalis.mouth_discoloration" :options="OptionsMouthHiperemis" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Lidah</label>
                                                <v-select v-model="form_generalis.mouth_tongue" :options="OptionsMouthTongue" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Karies</label>
                                                <v-select v-model="form_generalis.mouth_caries" :options="OptionsMouthKaries" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-20">
                                        <ModernLabel variant="w3green" label="Leher" />
                                        <div class="row">
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">KGB</label>
                                                <v-select v-model="form_generalis.neck_kgb" :options="OptionsNeckKGB" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Posisi Trakea</label>
                                                <v-select v-model="form_generalis.neck_trachea_position" :options="OptionsNeckTrakea" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Massa</label>
                                                <v-select v-model="form_generalis.neck_massa" :options="OptionsNeckMassa" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">JVP</label>
                                                <v-select
                                                    v-model="form_generalis.neck_jvp"
                                                    :options="[
                                                        { value: 1, title: 'Peningkatan JVP (-)' },
                                                        { value: 2, title: 'Peningkatan JVP (+)' }
                                                    ]"
                                                    :reduce="item => item.value"
                                                    label="title"
                                                    placeholder="-- Pilih --"
                                                    class="select-size-sm"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-20">
                                        <ModernLabel variant="w3green" label="Thoraks" />
                                        <div class="mt-12">
                                            <div class="font-weight-bold" style="font-size: 14px">INSPEKSI</div>
                                            <div class="row">
                                                <div class="col-lg-3 mt-8 mt-lg-0">
                                                    <label class="fs-13 mb-0">Dada</label>
                                                    <v-select v-model="form_generalis.thorax_insp_chest" :options="OptionsInspectionChest" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                                <div class="col-lg-3 mt-8 mt-lg-0">
                                                    <label class="fs-13 mb-0">Retraksi</label>
                                                    <v-select v-model="form_generalis.thorax_insp_retraction" :options="OptionsInspectionRetraction" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-16">
                                            <div class="font-weight-bold" style="font-size: 14px">PALPASI</div>
                                            <div class="row">
                                                <div class="col-lg-3 mt-8 mt-lg-0">
                                                    <label class="fs-13 mb-0">Dada</label>
                                                    <v-select v-model="form_generalis.thorax_palp_chest" :options="OptionsPalpationChest" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                                <div class="col-lg-3 mt-8 mt-lg-0">
                                                    <label class="fs-13 mb-0">Fremitus</label>
                                                    <v-select v-model="form_generalis.thorax_palp_fremitus" :options="OptionsPalpationFremitus" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                                <div class="col-lg-3 mt-8 mt-lg-0">
                                                    <label class="fs-13 mb-0">Krepitasi</label>
                                                    <v-select v-model="form_generalis.thorax_palp_crepitation" :options="OptionsPalpationKrepitasi" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-16">
                                            <div class="font-weight-bold" style="font-size: 14px">AUSKULTASI</div>
                                            <div class="row">
                                                <div class="col-lg-3 mt-8 mt-lg-0">
                                                    <label class="fs-13 mb-0">Pulmo Vasikuler</label>
                                                    <v-select v-model="form_generalis.thorax_ausk_pul_vasiculer" :options="OptionsAuskultationPulmoVasikuler" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                                <div class="col-lg-3 mt-8 mt-lg-0">
                                                    <label class="fs-13 mb-0">Pulmo Rhonki</label>
                                                    <v-select v-model="form_generalis.thorax_ausk_pul_rhonki" :options="OptionsAuskultationPulmoRhonki" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                                <div class="col-lg-3 mt-8 mt-lg-0">
                                                    <label class="fs-13 mb-0">Pulmo Wheezing</label>
                                                    <v-select v-model="form_generalis.thorax_ausk_pul_wheezing" :options="OptionsAuskultationPulmoWheezing" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-3 mt-8">
                                                    <label class="fs-13 mb-0">COR Bunyi Jantung</label>
                                                    <v-select v-model="form_generalis.thorax_ausk_cor_heart_sound" :options="OptionsAuskultationCORBJ" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                                <div class="col-lg-3 mt-8">
                                                    <label class="fs-13 mb-0">COR Murmur</label>
                                                    <v-select v-model="form_generalis.thorax_ausk_cor_murmur" :options="OptionsAuskultationCORMurmur" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                                <div class="col-lg-3 mt-8">
                                                    <label class="fs-13 mb-0">COR Gallop</label>
                                                    <v-select v-model="form_generalis.thorax_ausk_cor_gallop" :options="OptionsAuskultationCORGallop" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-20">
                                        <ModernLabel variant="w3green" label="Abdomen" />
                                        <div class="row">
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Supel</label>
                                                <v-select v-model="form_generalis.abdomen_supel" :options="OptionsAbdomenSupel" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Bising Usus</label>
                                                <v-select v-model="form_generalis.abdomen_bowel_sounds" :options="OptionsAbdomenBising" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Nyeri Tekan</label>
                                                <v-select v-model="form_generalis.abdomen_tenderness" :options="OptionsAbdomenPress" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Hepar & Lien</label>
                                                <v-select v-model="form_generalis.abdomen_hepar_lien" :options="OptionsAbdomenHepar" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                            </div>
                                            <div class="col-lg mt-8">
                                                <label class="fs-13 mb-0">Pemeriksaan Abdomen Lainnya</label>
                                                <textarea v-model.lazy="form_generalis.abdomen_other_physical_check" class="form-control form-control-sm" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-20">
                                        <ModernLabel variant="w3green" label="Ekstremitas" />
                                        <div class="row">
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Akral</label>
                                                <v-select v-model="form_generalis.extremity_acral" :options="OptionsEkstremitasAkral" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">CRT</label>
                                                <v-select v-model="form_generalis.extremity_crt" :options="OptionsEkstremitasCRT" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Edema</label>
                                                <v-select v-model="form_generalis.extremity_edema" :options="OptionsEkstremitasEdema" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-20">
                                        <ModernLabel variant="w3green" label="Genitalia" />
                                        <div class="mt-8 mb-8">
                                            <textarea v-model.lazy="form_generalis.genitalia" class="form-control form-control-sm" autocomplete="off" placeholder="(Tuliskan pemeriksaan genitalia)" />
                                        </div>
                                    </div>
                                    <div class="mt-20">
                                        <ModernLabel variant="w3green" label="Status Lokalis" />
                                        <div class="mt-8 mb-8">
                                            <textarea v-model.lazy="form_generalis.localist_status" class="form-control form-control-sm" autocomplete="off" placeholder="(Tuliskan status lokalis di kolom ini secara lengkap)" />
                                        </div>
                                    </div>
                                    <div class="mt-20">
                                        <ModernLabel variant="w3green" label="Status Neurologis" />
                                        <div class="mt-8 mb-8">
                                            <textarea v-model.lazy="form_generalis.neurologist_status" class="form-control form-control-sm" autocomplete="off" />
                                        </div>
                                    </div>
                                    <div class="mt-20">
                                        <ModernLabel variant="w3green" label="Status oftalmologis" />
                                        <div class="mt-8 mb-8">
                                            <textarea v-model.lazy="form_generalis.ophthal_status" class="form-control form-control-sm" autocomplete="off" />
                                        </div>
                                    </div>
                                    <!-- </b-collapse> -->

                                    <!-- <small @click="active.advance.generalis = !active.advance.generalis" class="font-weight-bold text-primary pointer fs-11 mt-12" :class="active.advance.generalis ? null : 'collapsed'" :aria-expanded="active.advance.generalis ? 'true' : 'false'" aria-controls="collapse-general">
                                        Advance status generalis
                                        <b-icon :icon="active.advance.generalis == true ? 'chevron-up' : 'chevron-down'" />
                                    </small> -->
                                </div>
                            </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <div class="d-flex">
                                <b-button
                                    @click="changeTab('VITAL')"
                                    :disabled="active.disabled.section"
                                    variant="outline-neutral-700"
                                    class="rounded"
                                    size="sm"
                                >
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button
                                    @click="changeSection(active.section, 'DIAGNOSIS')"
                                    :disabled="active.disabled.section"
                                    variant="w3green"
                                    class="ml-2 rounded"
                                    size="sm"
                                >
                                    Simpan &amp; lanjutkan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                            </div>
                        </div>
                        </section>

                    <section v-if="active.section == 'DIAGNOSIS'" class="card">
                        <div class="card-header bg-w3green text-white fs-14 font-weight-bold py-10">DIAGNOSA</div>
                        <div class="card-body px-20 py-8" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <div class="">
                                <div v-if="active.access == 'doctor'" class="fs-13 text-muted mt-3 mb-4" style="font-style: italic;">
                                        Data Diagnosis diisi secara otomatis berdasarkan diagnosa Anda di CPPT sebelumnya. Konfirmasi atau lakukan perubahan sesuai kondisi pasien.
                                    </div>
                                    <ModernLabel variant="w3green" label="Diagnosa (ICD10)" />
                                    <template v-if="form_diagnosis.icd10.length > 0">
                                        <!-- <b-table responsive bordered small :fields="icd10Fields" :items="form_diagnosis.icd10" class="mt-12 text-center" style="font-size: 14px;" >
                                            <template #cell(no)="row">{{ row.index+1 }}</template>
                                            <template #cell(title)="row">
                                                <div class="text-left">{{ row.value }}</div>
                                            </template>
                                            <template #cell(action)="row">
                                                <b-button variant="danger-600" size="sm" @click="delete10(row.index)" class="mr-2">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </template>
                                        </b-table> -->
                                        <b-table-simple responsive bordered small class="mt-12 text-center" style="font-size: 14px;">
                                            <b-thead>
                                                <b-tr>
                                                    <b-th style="width: 60px;">No</b-th>
                                                    <b-th>ICD10</b-th>
                                                    <b-th style="width: 60px;">Aksi</b-th>
                                                </b-tr>
                                            </b-thead>
                                            <draggable
                                                v-model="form_diagnosis.icd10"
                                                tag="tbody"
                                                handle=".drag-handle"
                                                :animation="200"
                                            >
                                                <b-tr v-for="(row, index) in form_diagnosis.icd10" :key="row.icd10_id">
                                                    <b-td>
                                                        <span class="drag-handle" style="cursor:move;">&#9776;</span> {{ index + 1 }}
                                                    </b-td>
                                                    <b-td class="text-left">{{ row.title }}</b-td>
                                                    <b-td>
                                                        <b-button variant="danger-600" size="sm" @click="delete10(index)" class="mr-2">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </b-td>
                                                </b-tr>
                                            </draggable>
                                        </b-table-simple>
                                    </template>
                                    <v-select v-model="icd10_id" @input="onAdd10($event)" aria-readonly="true" @search="onSearchIcd10" :options="OptionsIcd10" :reduce="item => item" label="title" placeholder="-- Diagnosa --" class="select-size-sm mt-12" />
                                    <textarea v-model.lazy="form_diagnosis.icd10_notes" autocomplete="off" placeholder="Catatan ICD10" class="form-control form-control-sm mt-12" />
                                </div>

                                <div class="mt-32" >
                                    <ModernLabel variant="w3green" label="Diagnosis Non-ICD10" />
                                    <template v-if="form_diagnosis.non_icd10.length > 0">
                                        <b-table-simple bordered responsive small class="mt-12" style="font-size: 14px">
                                            <b-tr>
<b-th style="width: 60px;" class="text-center">Aksi</b-th>
                                                <b-th style="width: 60px;" class="text-center">No</b-th>
                                                <b-th>Non-ICD10</b-th>
                                                                                            </b-tr>
                                            <template v-for="(item,index) in form_diagnosis.non_icd10">
                                            <b-tr class="item">
                                                    <b-td class="text-center">
                                                        <b-button variant="danger-600" size="sm" @click="removeOther10(index)">
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                    </b-td>
<b-td class="text-center">{{ form_diagnosis.icd10.length+index+1 }}</b-td>
                                                    <b-td>
                                                        <div>{{ item }}</div>
                                                    </b-td>
                                                                                                    </b-tr>
                                            </template>
                                        </b-table-simple>
                                    </template>
                                    <div class="mt-10">
                                        <b-form-input v-model="input_other10" size="sm" placeholder="Non-ICD10" />
                                        <b-button @click="addOther10" size="sm" variant="outline-primary" class="mt-12">Tambah</b-button>
                                    </div>
                                </div>
                            </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <div class="d-flex">
                                <b-button
                                    @click="changeTab('GENERALIS')"
                                    :disabled="active.disabled.section"
                                    variant="outline-neutral-700"
                                    class="rounded"
                                    size="sm"
                                >
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button
                                    @click="changeSection(active.section, 'PROCEDURE')"
                                    :disabled="active.disabled.section"
                                    variant="w3green"
                                    class="ml-2 rounded"
                                    size="sm"
                                >
                                    Simpan &amp; lanjutkan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                            </div>
                        </div>
                        </section>

                    <section v-if="active.section == 'PROCEDURE'" class="card">
                        <div class="card-header bg-w3green text-white fs-14 font-weight-bold py-10">PROSEDUR</div>
                        <div class="card-body px-20 py-8" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <section class="w-100">
                                <ModernLabel variant="w3green" label="Tindakan Dokter" />
                                <template v-if="form_procedure.transaction_doctor.length > 0">
                                    <b-table small responsive bordered :fields="transactionFields" :items="form_procedure.transaction_doctor" class="mt-12 text-center" thead-tr-class="fs-13" tbody-tr-class="fs-12" style="font-size: 14px;" >
                                        <template #cell(no)="row">{{ row.index+1 }}</template>
                                        <template #cell(title)="row">
                                            <div class="text-left">{{ row.item.costing_name }}</div>
                                            <v-select v-if="row.item.is_ref===1" v-model="row.item.doctor_ref_id" aria-readonly="true" :options="OptionsDoctorRef" :reduce="item => item.value" label="title" placeholder="-- Pilih Dokter --"></v-select>
                                        </template>
                                        <template #cell(var_2)="row">
                                            <b-form-input type="number" size="sm" v-model="row.item.var_2" aria-readonly="true" />
                                        </template>
                                        <template #cell(action)="row">
                                            <b-button variant="danger-600" size="sm" @click="deleteSC(row.index)" class="mr-2">
                                                <feather-icon icon="TrashIcon" />
                                            </b-button>
                                        </template>
                                    </b-table>
                                </template>
                                <v-select v-model="costing_id" @input="onAddSC($event)" aria-readonly="true" :options="getCosting" :reduce="item => item" label="title" placeholder="-- Pemeriksaan & Tindakan --" class="select-size-sm mt-12" />
                            </section>

                            <section class="mt-32" >
                                <ModernLabel variant="w3green" label="Tindakan (ICD9CM)" />
                                <textarea v-model.lazy="form_procedure.therapy_plan" autocomplete="off" placeholder="Catatan ICD9CM" class="form-control form-control-sm mt-12" />
                                <template v-if="form_procedure.icd9cm.length > 0">
                                    <b-table small responsive bordered :fields="icd9cmFields" :items="form_procedure.icd9cm" class="mt-12 text-center" thead-tr-class="fs-13" tbody-tr-class="fs-12" style="font-size: 14px;" >
                                        <template #cell(no)="row">{{ row.index+1 }}</template>
                                        <template #cell(title)="row">
                                            <div class="text-left">{{ row.value }}</div>
                                        </template>
                                        <template #cell(action)="row">
                                            <b-button variant="danger-600" size="sm" @click="delete9cm(row.index)" class="mr-2">
                                                <feather-icon icon="TrashIcon" />
                                            </b-button>
                                        </template>
                                    </b-table>
                                </template>
                                <v-select v-model="icd9cm_id" @input="onAdd9cm($event)" aria-readonly="true" @search="onSearchIcd9cm" :options="OptionsIcd9cm" :reduce="OptionsIcd9cm => OptionsIcd9cm" label="title" placeholder="-- ICD 9 --" class="select-size-sm mt-12" />
                            </section>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <div class="d-flex">
                                <b-button
                                    @click="changeTab('DIAGNOSIS')"
                                    :disabled="active.disabled.section"
                                    variant="outline-neutral-700"
                                    class="rounded"
                                    size="sm"
                                >
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button
                                    @click="changeSection(active.section, 'PLAN')"
                                    :disabled="active.disabled.section"
                                    variant="w3green"
                                    class="ml-2 rounded"
                                    size="sm"
                                >
                                    Simpan &amp; lanjutkan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                            </div>
                        </div>
                    </section>

                    <section v-if="active.section == 'PLAN'" class="card">
                        <div class="card-header bg-w3green text-white fs-14 font-weight-bold py-10">PLAN</div>
                        <div class="card-body px-20 py-8" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <div class="row">
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Plan Non-Farmakologis</label>
                                    <textarea v-model.lazy="form_plan.plan" rows="3" class="form-control form-control-sm" autocomplete="off" />
                                </div>
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Plan Farmakologis</label>
                                    <textarea v-model.lazy="form_plan.pharmacological_plan" rows="4" class="form-control form-control-sm" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <div class="d-flex">
                                <b-button
                                    @click="changeTab('PROCEDURE')"
                                    :disabled="active.disabled.section"
                                    variant="outline-neutral-700"
                                    class="rounded"
                                    size="sm"
                                >
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button
                                    @click="changeSection(active.section, getSecondNextSection())"
                                    :disabled="active.disabled.section"
                                    variant="w3green"
                                    class="ml-2 rounded"
                                    size="sm"
                                >
                                    Simpan &amp; lanjutkan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                            </div>
                        </div>
                    </section>

                    <section v-if="active.section == 'LABORATORY'" class="card">
                        <div class="card-header bg-w3green text-white fs-14 font-weight-bold py-10">LABORATORIUM</div>
                        <div class="card-body px-20 py-8" :class="lab_status != 1 ? 'bg-warning-100': ''" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <div v-if="lab_status != 1" class="card-body bg-warning mb-16">
                                <div class="d-flex align-items-center">
                                    <b-icon-exclamation-triangle-fill variant="neutral-800" style="width: 24px; height: 24px;"/>
                                    <div class="ml-3 font-italic">Form penunjang laboratorium tidak dapat diubah karena telah diproses oleh laboratorium</div>
                                </div>
                            </div>

                            <div :class="lab_status != 1 ? 'mt-12': ''">
                                <div class="w-100">
                                    <div class="fs-14 font-weight-bold">PEMERIKSAAN TERPILIH:</div>
                                    <b-form-checkbox-group size="sm" v-model="form_laboratory.laboratory_request_items">
                                        <b-form-checkbox
                                        v-for="(item, index) in form_laboratory.laboratory_request_items"
                                        :key="index"
                                        :value="item"
                                        :disabled="lab_status != 1"
                                        >
                                        {{ item.costing_name }}
                                        </b-form-checkbox>
                                    </b-form-checkbox-group>
                                </div>
                                <b-form-input v-model="searchlab" :disabled="lab_status != 1" placeholder="Pencarian" size="sm" class="mt-8" />
                                <div class="mt-8 py-2 bg-neutral-100" style="height: 350px; overflow-y: scroll;">
                                    <b-form-checkbox-group size="sm" class="col-lg-12" v-model="form_laboratory.laboratory_request_items" :disabled="lab_status != 1">
                                        <div v-for="(item, index) in filteredListLab" :key="index" :class="index != 0 ? 'mt-10' : 'mt-0'">
                                            <div class="fs-14 font-weight-bold text-uppercase">{{ item.category_name }}</div>
                                            <div class="row mx-0">
                                                <b-form-checkbox v-for="(list, packIndex) in item.category_items" v-bind:key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'laboratory_package_id':list.id, 'costing_name':list.costing_name}" class="mr-0 col-lg-4">{{list.costing_name }}</b-form-checkbox>
                                            </div>
                                        </div>
                                    </b-form-checkbox-group>
                                </div>
                            </div>

                            <div class="mt-12">
                                <label class="fs-13 mb-0">Catatan</label>
                                <b-form-textarea
                                    v-model="form_laboratory.clinical_indications"
                                    rows="4"
                                    class="form-control form-control-sm"
                                    autocomplete="off"
                                />
                            </div>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <div class="d-flex">
                                <b-button
                                    @click="changeTab('PLAN')"
                                    :disabled="active.disabled.section"
                                    variant="outline-neutral-700"
                                    class="rounded"
                                    size="sm"
                                >
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button
                                    @click="changeSection(active.section, 'RADIOLOGY')"
                                    :disabled="active.disabled.section"
                                    variant="w3green"
                                    class="ml-2 rounded"
                                    size="sm"
                                >
                                    Simpan &amp; lanjutkan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                            </div>
                        </div>
                    </section>

                    <section v-if="active.section == 'RADIOLOGY'" class="card">
                        <div class="card-header bg-w3green text-white fs-14 font-weight-bold py-10">RADIOLOGI</div>
                        <div class="card-body px-20 py-8" :class="rad_status != 1 ? 'bg-warning-100': ''" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <div v-if="rad_status != 1" class="card-body bg-warning mb-16">
                                <div class="d-flex align-items-center">
                                    <b-icon-exclamation-triangle-fill variant="neutral-800" style="width: 24px; height: 24px;"/>
                                    <div class="ml-3 font-italic">Form penunjang radiologi tidak dapat diubah karena telah diproses oleh radiologi</div>
                                </div>
                            </div>
                            <div :class="rad_status != 1 ? 'mt-12': ''">
                                <div class="w-100">
                                    <div class="fs-14 font-weight-bold">PEMERIKSAAN TERPILIH:</div>
                                    <b-form-checkbox-group size="sm" v-model="form_radiology.radiology_request_items">
                                        <b-form-checkbox
                                        v-for="(item, index) in form_radiology.radiology_request_items"
                                        :key="index"
                                        :value="item"
                                        :disabled="rad_status != 1"
                                        >
                                        {{ item.costing_name }}
                                        </b-form-checkbox>
                                    </b-form-checkbox-group>
                                </div>
                                <b-form-input type="text" v-model="searchrad" :disabled="rad_status != 1" placeholder="Pencarian" size="sm" class="mt-8" />
                                <div class="mt-8 py-2 bg-neutral-100" style="height: 350px; overflow-y: scroll;">
                                    <b-form-checkbox-group size="sm" class="col-lg-12" v-model="form_radiology.radiology_request_items" :disabled="rad_status != 1">
                                        <div v-for="(item, index) in filteredListRad" :key="index" :class="index != 0 ? 'mt-10' : 'mt-0'">
                                            <div class="fs-14 font-weight-bold text-uppercase">{{ item.category_name }}</div>
                                            <div class="row mx-0">
                                                <b-form-checkbox v-for="(list, packIndex) in item.category_items" v-bind:key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'radiology_package_id':list.id, 'costing_name':list.costing_name}" class="mr-0 col-lg-4">{{list.costing_name }}</b-form-checkbox>
                                            </div>
                                        </div>
                                    </b-form-checkbox-group>
                                </div>
                            </div>
                            <div class="mt-12">
                                <label class="fs-13 mb-0">Catatan Klinis</label>
                                <b-form-textarea
                                    v-model="form_radiology.clinical_indications"
                                    rows="4"
                                    class="form-control form-control-sm"
                                    autocomplete="off"
                                />
                            </div>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <div class="d-flex">
                                <b-button
                                    @click="changeTab('LABORATORY')"
                                    :disabled="active.disabled.section"
                                    variant="outline-neutral-700"
                                    class="rounded"
                                    size="sm"
                                >
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button
                                    @click="changeSection(active.section, 'PHYSIOTHERAPY')"
                                    :disabled="active.disabled.section"
                                    variant="w3green"
                                    class="ml-2 rounded"
                                    size="sm"
                                >
                                    Simpan &amp; lanjutkan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                            </div>
                        </div>
                    </section>

                    <section v-if="active.section == 'PHYSIOTHERAPY'" class="card">
                        <div class="card-header bg-w3green text-white fs-14 font-weight-bold py-10">FISIOTERAPI</div>
                        <div class="card-body px-20 py-8" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <div class="w-100">
                                <div class="fs-14 font-weight-bold">PEMERIKSAAN TERPILIH:</div>
                                <b-form-checkbox-group size="sm" v-model="form_physiotherapy.physiotherapy_request_items">
                                    <b-form-checkbox
                                    v-for="(item, index) in form_physiotherapy.physiotherapy_request_items"
                                    :key="index"
                                    :value="item"
                                    >
                                    {{ item.costing_name }}
                                    </b-form-checkbox>
                                </b-form-checkbox-group>
                            </div>
                            <b-form-input type="text" v-model="searchphy" placeholder="Pencarian" size="sm" class="mt-8" />
                            <div class="mt-8 py-2 bg-neutral-100" style="height: 350px; overflow-y: scroll;">
                                <b-form-checkbox-group size="sm" class="col-lg-12" v-model="form_physiotherapy.physiotherapy_request_items">
                                    <b-form-checkbox v-for="(ip, index) in filteredListPhy" v-bind:key="index" :value="{'service_class_id': ip.service_class_id,'physiotherapy_package_id':ip.id, 'costing_name': ip.costing_name}" class="mr-0 col-lg-4">{{ip.costing_name }}</b-form-checkbox>
                                </b-form-checkbox-group>
                            </div>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <div class="d-flex">
                                <b-button
                                    @click="changeTab('RADIOLOGY')"
                                    :disabled="active.disabled.section"
                                    variant="outline-neutral-700"
                                    class="rounded"
                                    size="sm"
                                >
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button
                                    @click="changeSection(active.section, 'PRESC')"
                                    :disabled="active.disabled.section"
                                    variant="w3green"
                                    class="ml-2 rounded"
                                    size="sm"
                                >
                                    Simpan &amp; lanjutkan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                            </div>
                        </div>
                    </section>

                    <section v-if="active.section == 'PRESC'">
                        <b-card no-body>
                            <b-tabs card nav-wrapper-class="bg-w3green pt-0 pl-8" active-nav-item-class="bg-orange-400">
                                <b-tab no-body active>
                                    <template #title>
                                        <span class="fs-14 font-weight-bold text-white">RESEP AKTIF</span>
                                    </template>

                                    <div class="card-body px-16 py-8" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                                        <div class="row" style="padding-left: 0; padding-right: 0;">
                                            <div class="col-lg-3 pr-0">
                                                <label class="fs-13 mb-0">Sediaan</label>
                                                <v-select v-model="kxStock" @input="openPreParent($event)" :options="optParentStock" :reduce="item => item" label="title" class="select-size-sm" placeholder="sediaan" />
                                            </div>
                                            <div class="col-lg">
                                                <label class="fs-13 mb-0">Tambah Katalog</label>
                                                <v-select v-model="kxCatalog" @input="openPreItem($event)" @search="onSearchCatalog" :options="OptionsCatalog" :reduce="item => item" :filterable="false" label="title" class="select-size-sm" placeholder="pilih obat"></v-select>
                                            </div>
                                        </div>

                                        <b-table-simple fixed small class="mt-12">
                                            <b-thead class="text-center fs-12">
                                                <b-tr>
                                                    <th class="align-middle fs-10" style="width: 40px;">AKSI</th>
                                                    <th class="align-middle" style="width: 220px;">NAMA OBAT</th>
                                                    <th class="align-middle fs-10" style="width: 100px;">DITAMBAHKAN OLEH</th>
                                                    <th class="align-middle" style="width: 135px;">STATUS</th>
                                                    <!-- <th class="align-middle fs-10" style="width: 100px;">KEMASAN / SATUAN</th> -->
                                                    <th class="align-middle" style="width: 80px;">DOSIS</th>
                                                    <th class="align-middle fs-10" style="width: 100px;">SATUAN DOSIS / KEMASAN</th>
                                                    <th class="align-middle" style="width: 150px;">WAKTU</th>
                                                    <th class="align-middle" style="width: 220px;">ADMINISTER</th>
                                                </b-tr>
                                            </b-thead>
                                            <template v-for="(item_p, index_p) in input_prescription.form_prescription_parent">
                                                <b-tr class="fs-11" :class="getPrescParentVariant(item_p.id)">
                                                    <b-td :colspan="item_p.id == 1 ? 8 : 2" class="font-weight-bold align-middle" :class="item_p.id == 1? 'py-12' : ''">{{ getPrescParentName(item_p.id || '-') }}</b-td>
                                                    <template v-if="item_p.id != 1">
                                                        <td></td>
                                                        <td>
                                                            <v-select v-model="item_p.status" :options="optKardexItemStatus" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                                        </td>
                                                        <b-td>
                                                            <b-form-input v-model="item_p.value" disabled type="number" min="1" size="sm" autocomplete="off" placeholder="jumlah"/>
                                                        </b-td>
                                                        <b-td>
                                                            <v-select v-model="item_p.package" :options="optParentPackage" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                                        </b-td>
                                                        <b-td>
                                                            <b-input-group size="sm" prepend="TIAP">
                                                                <b-form-select v-model="item_p.signatura_times_id" :options="optSignTime" text-field="title" size="sm" />
                                                            </b-input-group>
                                                        </b-td>
                                                        <b-td>
                                                            <v-select @input="updateAdminister(index_p, $event, 'parent')" v-model="item_p.administer_id" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                                                            <b-form-input v-if="item_p.administer_id == other_administer" v-model="item_p.desc" size="sm" autocomplete="off" class="mt-1"/>
                                                        </b-td>
                                                    </template>
                                                </b-tr>
                                                <template v-for="(item, index) in input_prescription.form_prescription" v-if="item_p.id == item.prescription_type_id">
                                                    <tr class="fs-11" :class="{'bg-warning-300': item.is_exist == 1 && item.is_change == 1}">
                                                        <td class="text-center">
                                                            <template v-if="item.is_exist == 0">
                                                                <b-button @click="deletePRES(index)" variant="danger-600" size="sm">
                                                                    <feather-icon icon="TrashIcon" />
                                                                </b-button>
                                                            </template>
                                                            <template v-if="item.is_exist == 1">
                                                                <feather-icon icon="InfoIcon" :id="`popover-target-${index}`" color="grey" class="pointer"/>
                                                                <b-popover :target="`popover-target-${index}`" triggers="hover" placement="right">
                                                                    <template #title>
                                                                        <div class="fs-10 text-uppercase font-weight-bold">{{ item.name }}</div>
                                                                    </template>
                                                                    <div class="fst-italic fs-10">
                                                                        <li>ditambahkan oleh {{ item.doctor_added_name }}</li>
                                                                        <li>digunakan selama {{ differenceDate(item.exist_status == 4 ? item.stop_date : getCurrentFullDate(), item.catalog_date) }}</li>
                                                                    </div>
                                                                </b-popover>
                                                            </template>
                                                        </td>
                                                        <td>{{ item.name }}</td>
                                                        <td class="text-center">{{ item.doctor_nickname }}</td>
                                                        <td>
                                                            <v-select v-if="item.prescription_type_id == 1" v-model="item.status" :options="optKardexItemStatus" :reduce="item => item.value" :disabled="kxDisabledCriteria(item.is_exist, item.doctor_added)" label="title" placeholder="pilih" class="select-size-sm" />
                                                        </td>
                                                        <!-- <td>
                                                            <v-select v-model="item.package_id" :options="PackageOptions" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                                        </td> -->
                                                        <td>
                                                            <b-form-input v-model="item.dose" :disabled="kxDisabledCriteria(item.is_exist, item.doctor_added)" type="number" min="1" size="sm" autocomplete="off" placeholder="dosis" class="kx-input"/>
                                                        </td>
                                                        <b-td>
                                                            <v-select v-model="item.dose_unit_id" :options="item.dose_unit_options" :reduce="unit => unit.value" :disabled="kxDisabledCriteria(item.is_exist, item.doctor_added)" label="title" placeholder="pilih" class="select-size-sm"/>
                                                        </b-td>
                                                        <b-td>
                                                            <b-input-group v-if="item.prescription_type_id == 1" size="sm" prepend="TIAP">
                                                                <b-form-select v-model="item.signatura_times_id" :options="optSignTime" :disabled="kxDisabledCriteria(item.is_exist, item.doctor_added)" text-field="title" size="sm"/>
                                                            </b-input-group>
                                                        </b-td>
                                                        <td>
                                                            <template v-if="item.prescription_type_id == 1">
                                                                <v-select @input="updateAdminister(index, $event, null)" v-model="item.administer_id" :options="optAdminister" :reduce="item => item.value" :disabled="kxDisabledCriteria(item.is_exist, item.doctor_added)" label="title" placeholder="pilih" class="select-size-sm"/>
                                                                <b-form-input v-if="item.administer_id == other_administer" v-model="item.desc" :disabled="kxDisabledCriteria(item.is_exist, item.doctor_added)" size="sm" autocomplete="off" class="mt-1"/>
                                                            </template>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </template>
                                        </b-table-simple>
                                        <!-- <pre>{{ input_prescription }}</pre> -->

                                        <!-- <b-button @click="triggerKardexNotif('8a08ae66-2e6c-40ab-85aa-a2ebbbd0c958')" size="sm" >
                                            Cobain
                                        </b-button> -->

                                        <section class="mt-24" style="display: none;">
                                            <ModernLabel label="Non Farmakologi" />
                                            <template v-if="input_prescription.non_pharmacological?.length > 0">
                                                <b-table small bordered :fields="nonPharmaFields" :items="input_prescription.non_pharmacological" class="mt-8 fs-13 text-center">
                                                    <template #cell(action)="row">
                                                        <b-button variant="danger-600" size="sm" @click="removeNonPharma(row.index)">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </template>
                                                    <template #cell(no)="row">{{ row.index+1 }}</template>
                                                    <template #cell(name)="row">
                                                        <!-- <div class="text-left">{{ parsedBody(row.item.name) }}</div> -->
                                                        <!-- <div class="text-left">{{ parsedBody(row.item.name) }}</div> -->
                                                         <b-textarea v-model="row.item.name" rows="4" readonly></b-textarea>
                                                    </template>
                                                </b-table>
                                            </template>
                                            <div class="mt-12">
                                                <textarea v-model.lazy="kxNonPharma" @keyup.enter="addNonPharma" rows="3" class="form-control form-control-sm" autocomplete="off" />
                                                <!-- <b-form-input @keyup.enter="addNonPharma" v-model="kxNonPharma" size="sm" placeholder="tekan enter untuk menambahkan"/> -->
                                                <b-button @click="addNonPharma" size="sm" variant="outline-primary" class="mt-1">Tambah</b-button>
                                            </div>
                                            <!-- <pre>{{input_prescription.non_pharmacological}}</pre> -->
                                        </section>
                                    </div>

                                    <b-card-footer class="py-1 px-2">
                                        <div class="d-flex">
                                            <b-button
                                                @click="changeTab('PHYSIOTHERAPY')"
                                                :disabled="active.disabled.section"
                                                variant="outline-neutral-700"
                                                class="rounded"
                                                size="sm"
                                            >
                                                <feather-icon icon="ChevronLeftIcon" />
                                                Kembali
                                            </b-button>
                                            <b-button
                                                @click="changeSection(active.section, 'PRESC')"
                                                :disabled="active.disabled.section"
                                                variant="w3green"
                                                class="ml-2 rounded"
                                                size="sm"
                                            >
                                                Simpan Kardeks
                                            </b-button>
                                        </div>
                                    </b-card-footer>
                                </b-tab>

                                <b-tab no-body>
                                    <template #title>
                                        <span class="fs-14 font-weight-bold text-white">RIWAYAT KARDEKS</span>
                                    </template>

                                    <div class="card-body p-0" :style="{top: '0', height: `calc(${dynamicHeight} - 41px)`, overflowY: 'auto'}">
                                        <template v-if="full_kardex?.length > 0">
                                            <!-- <div class="px-24 pt-12">
                                                <div class="row">
                                                    <div class="col-lg-3">
                                                        <b-form-input v-model="params_kardex.date_added" @change="getFullKardex" size="sm" type="date" />
                                                    </div>
                                                </div>
                                            </div> -->
                                            <div v-for="(list, listIndex) in full_kardex">
                                                <div class="p-24" :class="{'bg-diagonal': list.pharmacy_prescription_id != null}">
                                                    <ModernLabel :label="`Ditambahkan pada ${localDateTime(list.date_added)}`" variant="w3green" />

                                                    <b-table-simple responsive small class="fs-12 mt-10 bg-white">
                                                        <b-tr>
                                                            <b-th>NAMA</b-th>
                                                            <b-th style="width: 80px;">KEMASAN</b-th>
                                                            <b-th style="width: 140px;">SIGNATURA</b-th>
                                                            <b-th style="width: 220px;">ADMINISTER</b-th>
                                                        </b-tr>
                                                        <template v-for="(category, catIndex) in list.kardex_parent" v-if="hasItemsInCategory(list.kardex_item, category.id)">
                                                            <b-tr>
                                                                <b-th colspan="5" :class="getPrescParentVariant(category.id)">
                                                                    <div class="text-uppercase ">{{ getPrescParentName(category.id) }}</div>
                                                                    <div v-if="category.id != 1" class="fs-12 text-italic" style="font-weight: 400">
                                                                        <!-- <span>Jumlah: {{ category.value }}, </span> -->
                                                                        <span>Kemasan: {{ getParentPackageName(category?.package) }}, </span>
                                                                        <span>
                                                                            {{ category?.value ?? '-' }}
                                                                            TIAP {{ getDynamicOptName(category?.signatura_times_id, optSignTime)}}
                                                                            {{ getDynamicOptName(category?.administer_id, optAdminister) }}
                                                                            <span v-if="category.administer_id == other_administer">: {{ category.desc }}</span>
                                                                        </span>
                                                                    </div>
                                                                </b-th>
                                                            </b-tr>
                                                            <template v-for="(item, itemIndex) in list.kardex_item">
                                                                <b-tr :key="itemIndex" v-if="item.prescription_item_type_id == category.id" :class="getKardexItemVariant(item, list.suggestion_status, list.is_approve)">
                                                                    <b-td>
                                                                        <div :class="{'font-weight-bold': item.pharmacy_catalog_ref_name}">{{ item.pharmacy_catalog_name }}</div>
                                                                        <div v-if="item.pharmacy_catalog_ref_name" class=" d-flex fs-10">
                                                                            <feather-icon icon="CornerLeftUpIcon" class="mr-1"/>
                                                                            <div>
                                                                                <div>{{item.pharmacy_catalog_ref_name}}</div>
                                                                                <div class="font-italic">
                                                                                    <span style="font-weight: 600;">Alasan subtitusi: </span>
                                                                                    {{item.notes}}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="d-flex">
                                                                            <div>
                                                                                <b-badge v-if="item?.status" :variant="getKardexItemStatusVariant(item?.status)" class="text-uppercase mr-1">{{ getKardexItemStatusName(item?.status) }}</b-badge>
                                                                            </div>
                                                                            <small class="font-italic" :class="{'text-neutral-500': !item.is_change == 1}">
                                                                                <div>ditambahkan pada {{ localDateTime(item.catalog_date) }} oleh {{ item?.doctor_added_name || '-' }}</div>
                                                                                <div v-if="item.status == 4">stop pada {{ localDateTime(item.stop_date) }} (digunakan selama {{ differenceDate(item.stop_date, item.catalog_date) }})</div>
                                                                            </small>
                                                                        </div>
                                                                    </b-td>
                                                                    <b-td>{{ item.package_name }}</b-td>
                                                                    <b-td>
                                                                        {{ item.dose }}
                                                                        {{ getDynamicOptName(item.dose_unit_id, optSignUnits)}} /
                                                                        {{ getDynamicOptName(item.signatura_times_id, optSignTime)}}
                                                                    </b-td>
                                                                    <b-td>
                                                                        <span>{{ getDynamicOptName(item.administer_id, optAdminister) }}</span>
                                                                        <span v-if="item.administer_id == other_administer">: {{ item.desc }}</span>
                                                                    </b-td>
                                                                </b-tr>
                                                            </template>
                                                        </template>
                                                    </b-table-simple>

                                                    <div class="fs-14 bg-white p-12">
                                                        <b class="text-uppercase">Non-Farmakologi : </b>
                                                        <li v-for="unit in list?.kardex_non_pharmacological">
                                                            <span>{{ unit.name }} </span>
                                                            <small class="fs-11 font-italic text-neutral-500">(ditambahkan oleh {{ unit.doctor_added_name || '-' }})</small>
                                                        </li>
                                                    </div>
                                                </div>
                                                <hr class="m-0">
                                            </div>
                                        </template>
                                        <div v-else class="p-16 fs-14 text-center d-flex justify-content-center align-items-center h-100">
                                            <i>Tidak ada riwayat resep kardeks sebelumnya yang tercatat pada perawatan ini</i>
                                        </div>
                                    </div>
                                </b-tab>

                                <b-tab no-body>
                                    <template #title>
                                        <span class="fs-14 font-weight-bold text-white">STOK KARDEKS</span>
                                    </template>

                                    <div class="card-body px-16 py-8" :style="{top: '0', height: `calc(${dynamicHeight} - 41px)`, overflowY: 'auto'}">
                                        <template v-if="kardex_stock?.length > 0">
                                            <b-table-simple small fixed bordered class="text-center">
                                                <b-tr class="fs-14">
                                                    <b-th style="width: 50px">NO</b-th>
                                                    <b-th style="width: 400px" class="text-left">NAMA OBAT</b-th>
                                                    <b-th>DOSIS</b-th>
                                                    <b-th>STOCK</b-th>
                                                    <b-th>JUMLAH FISIK</b-th>
                                                </b-tr>
                                                <template v-for="(item, index) in kardex_stock">
                                                    <template v-if="item.kardex_concoction.length > 0">
                                                        <b-tr class="fs-12 bg-warning-200">
                                                            <b-td :rowspan="item.kardex_concoction.length+1">{{index+1}}</b-td>
                                                            <b-td class="text-left">
                                                                <div>{{ item.catalog_name }}</div>
                                                                <b-badge :variant="getKardexItemStatusVariant(item?.status)" class="text-uppercase mr-1">{{ getKardexItemStatusName(item?.status) }}</b-badge>
                                                            </b-td>
                                                            <b-td>{{ item.dose }} {{ item.dose_unit_name }} TIAP {{ item.signatura_times_name }}</b-td>
                                                            <b-td>{{ item.current_amount }} {{ item.current_amount_unit_name }}</b-td>
                                                            <b-td>{{ item.current_physical_quantity }} {{ item.package_name }}</b-td>
                                                        </b-tr>
                                                        <b-tr v-for="list in item.kardex_concoction" class="fs-12" >
                                                            <b-td class="text-left">
                                                                {{ list.pharmacy_catalog_name }}
                                                                <feather-icon icon="InfoIcon" :id="`popover-conc-${list.id}`" :color="list.is_incomplete_master == 1 ? 'red' : 'grey'" class="pointer"/>
                                                                <b-popover :target="`popover-conc-${list.id}`" triggers="hover" placement="right">
                                                                    <template #title>
                                                                        <div class="fs-10 text-uppercase font-weight-bold">{{ list.catalog_name }}</div>
                                                                    </template>
                                                                    <div class="fst-italic fs-10">
                                                                        <li>Kekuatan: {{ list.catalog_dose }} {{ list.catalog_dose_unit_name}}</li>
                                                                        <li>Kemasan: {{ list.catalog_packaging_value }} {{ list.catalog_packaging_unit_name}}</li>
                                                                    </div>
                                                                </b-popover>
                                                            </b-td>
                                                            <b-td>{{ list.dose }} {{ list.dose_unit_name }}</b-td>
                                                            <b-td>-</b-td>
                                                            <b-td>-</b-td>
                                                        </b-tr>
                                                    </template>
                                                    <b-tr v-else class="fs-12">
                                                        <b-td>{{index+1}}</b-td>
                                                        <b-td class="text-left">
                                                            <div>
                                                                {{ item.catalog_name }}
                                                                <feather-icon icon="InfoIcon" :id="`popover-stock-${item.id}`" :color="item.is_incomplete_master == 1 ? 'red' : 'grey'" class="pointer"/>
                                                            </div>
                                                            <b-badge :variant="getKardexItemStatusVariant(item?.status)" class="text-uppercase mr-1">{{ getKardexItemStatusName(item?.status) }}</b-badge>
                                                            <b-popover :target="`popover-stock-${item.id}`" triggers="hover" placement="right">
                                                                <template #title>
                                                                    <div class="fs-10 text-uppercase font-weight-bold">{{ item.catalog_name }}</div>
                                                                </template>
                                                                <div class="fst-italic fs-10">
                                                                    <li>Kekuatan: {{ item.catalog_dose }} {{ item.catalog_dose_unit_name}}</li>
                                                                    <li>Kemasan: {{ item.catalog_packaging_value }} {{ item.catalog_packaging_unit_name}}</li>
                                                                </div>
                                                            </b-popover>
                                                        </b-td>
                                                        <b-td>{{ item.dose }} {{ item.dose_unit_name }} TIAP {{ item.signatura_times_name }}</b-td>
                                                        <b-td>{{ item.current_amount }} {{ item.current_amount_unit_name }}</b-td>
                                                        <b-td>{{ item.current_physical_quantity }} {{ item.package_name }}</b-td>
                                                    </b-tr>
                                                </template>
                                            </b-table-simple>
                                        </template>
                                        <div v-else class="p-16 fs-14 text-center d-flex justify-content-center align-items-center h-100">
                                            <i>Tidak ada stok kardeks yang tercatat pada perawatan ini</i>
                                        </div>
                                        <!-- <pre>{{ kardex_stock }}</pre> -->
                                    </div>
                                </b-tab>

                                <b-tab no-body>
                                    <template #title>
                                        <span class="fs-14 font-weight-bold text-white">RIWAYAT ORDER</span>
                                    </template>

                                    <div class="card-body p-0" :style="{top: '0', height: `calc(${dynamicHeight} - 41px)`, overflowY: 'auto'}">
                                        <template v-if="kardex_do?.length > 0">
                                            <div v-for="(list, listIndex) in kardex_do">
                                                <div class="px-16 py-8">
                                                    <ModernLabel :label="`${list.no_order}`" :small="`Ditambahkan pada ${localDateTime(list.date_added)}`" variant="w3green" />
                                                    <b-table-simple responsive small class="fs-12 mt-10 bg-white mb-0">
                                                        <b-tr>
                                                            <b-th>NAMA</b-th>
                                                            <b-th style="width: 120px;">JUMLAH</b-th>
                                                            <b-th style="width: 140px;">SIGNATURA</b-th>
                                                            <b-th style="width: 220px;">ADMINISTER</b-th>
                                                        </b-tr>
                                                        <template v-for="(category, catIndex) in list.form_prescription_parent" v-if="hasItemsInCategory(list.form_prescription, category.id)">
                                                            <b-tr>
                                                                <b-th colspan="5" :class="getPrescParentVariant(category.id)">
                                                                    <div class="text-uppercase ">{{ getPrescParentName(category.id) }}</div>
                                                                    <div v-if="category.id != 1" class="fs-12 text-italic" style="font-weight: 400">
                                                                        <span>Kemasan: {{ getParentPackageName(category?.package) }}, </span>
                                                                        <span>
                                                                            {{ category?.value ?? '-' }}
                                                                            TIAP {{ getDynamicOptName(category?.signatura_times_id, optSignTime)}}
                                                                            {{ getDynamicOptName(category?.administer_id, optAdminister) }}
                                                                            <span v-if="category.administer_id == other_administer">: {{ category.desc }}</span>
                                                                        </span>
                                                                    </div>
                                                                </b-th>
                                                            </b-tr>
                                                            <template v-for="(item, itemIndex) in list.form_prescription">
                                                                <b-tr :key="itemIndex" v-if="item.prescription_item_type_id == category.id">
                                                                    <b-td>{{ item.pharmacy_catalog_name }}</b-td>
                                                                    <b-td>{{ item.qty }} {{ item.package_name }}</b-td>
                                                                    <b-td>
                                                                        {{ item.dose }}
                                                                        {{ getDynamicOptName(item.dose_unit_id, optSignUnits)}} /
                                                                        {{ getDynamicOptName(item.signatura_times_id, optSignTime)}}
                                                                    </b-td>
                                                                    <b-td>
                                                                        <span>{{ getDynamicOptName(item.administer_id, optAdminister) }}</span>
                                                                        <span v-if="item.administer_id == other_administer">: {{ item.desc }}</span>
                                                                    </b-td>
                                                                </b-tr>
                                                            </template>
                                                        </template>
                                                    </b-table-simple>
                                                </div>
                                                <hr class="m-0">
                                            </div>
                                        </template>
                                        <div v-else class="p-16 fs-14 text-center d-flex justify-content-center align-items-center h-100">
                                            <i>Tidak ada riwayat order sebelumnya yang tercatat pada perawatan ini</i>
                                        </div>
                                        <!-- <pre>{{ kardex_do }}</pre> -->
                                    </div>
                                </b-tab>

                                <b-tab no-body>
                                    <template #title>
                                        <span class="fs-14 font-weight-bold text-white">RESEP PULANG</span>
                                    </template>

                                    <div class="card-body px-12 py-8" :class="presc_status == 1 ? 'bg-warning-100': ''" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                                        <div v-if="presc_status == 1" class="card-body bg-warning mb-16">
                                            <div class="d-flex align-items-center">
                                                <b-icon-exclamation-triangle-fill variant="neutral-800" style="width: 24px; height: 24px;"/>
                                                <div class="ml-3 font-italic">Form resep tidak dapat diubah karena telah diproses oleh farmasi</div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!-- <div class="col-lg-3 pr-0">
                                                <label class="fs-13 mb-0">Tipe Resep</label>
                                                <v-select v-model="home_recipe.type_prescription_id" :options="OptionsTypePresc" :reduce="item => item.value" :disabled="presc_status == 1" label="title" class="select-size-sm" placeholder="tipe resep" />
                                            </div> -->

                                            <template v-if="intensiveCriteria()">
                                                <div class="col-lg-3">
                                                    <label class="fs-13 mb-0">Tujuan Order</label>
                                                    <v-select v-model="home_recipe.pharmacy_depo_id" :options="OptionsDepo" :reduce="item => item.value" :disabled="presc_status == 1" label="title" class="select-size-sm" placeholder="depo" />
                                                </div>
                                            </template>
                                        </div>
                                        <div class="row mt-8">
                                            <div class="col-lg-3 pr-0">
                                                <label class="fs-13 mb-0">Sediaan</label>
                                                <v-select v-model="hrStock" @input="openHRPreParent($event)" :options="optParentStock" :reduce="item => item" :disabled="presc_status == 1" label="title" class="select-size-sm" placeholder="sediaan" />
                                            </div>
                                            <div class="col-lg">
                                                <label class="fs-13 mb-0">Tambah Katalog</label>
                                                <v-select v-model="hrCatalog" @input="openHRPreItem($event)" @search="onSearchHRCatalog" :options="optHrCatalog" :reduce="item => item" :disabled="presc_status == 1" :filterable="false" label="title" class="select-size-sm" placeholder="pilih obat"></v-select>
                                            </div>
                                        </div>
                                        <b-table-simple fixed small class="mt-12">
                                            <b-thead class="text-center fs-12">
                                                <b-tr>
                                                    <th style="width: 60px;">AKSI</th>
                                                    <th style="width: 270px;">NAMA OBAT</th>
                                                    <th class="align-middle">
                                                        SIGNATURA & ATURAN PAKAI
                                                        <feather-icon id="`pop-trgt-prs-sgntr`" icon="InfoIcon" size="12" color="blue" style="cursor: pointer;" />
                                                        <b-popover target="`pop-trgt-prs-sgntr`" triggers="hover" placement="bottom">
                                                            <template #title>
                                                                <div class="fs-10 text-uppercase font-weight-bold">Pengisian Kolom Signatura & Aturan Pakai</div>
                                                            </template>
                                                            <PrescInstruction ins_section="signatura" />
                                                        </b-popover>
                                                    </th>
                                                    <th style="width: 80px;" class="align-middle">JUMLAH</th>
                                                    <th class="align-middle">KEMASAN / SATUAN</th>
                                                    <th style="width: 240px;" class="align-middle">
                                                        KETERANGAN
                                                        <feather-icon id="`pop-trgt-prs-nts`" icon="InfoIcon" size="12" color="blue" style="cursor: pointer;" />
                                                        <b-popover target="`pop-trgt-prs-nts`" triggers="hover" placement="bottom">
                                                            <template #title>
                                                                <div class="fs-10 text-uppercase font-weight-bold">Pengisian Kolom Keterangan</div>
                                                            </template>
                                                            <PrescInstruction ins_section="notes" />
                                                        </b-popover>
                                                    </th>
                                                </b-tr>
                                            </b-thead>
                                            <template v-for="(item_p, index_p) in home_recipe.form_prescription_parent">
                                                <b-tr class="fs-11" :class="getPrescParentVariant(item_p.id)">
                                                    <b-td colspan="2" class="font-weight-bold align-middle" :class="item_p.id == 1? 'py-12' : ''">{{ item_p.prescription }}</b-td>
                                                    <template v-if="item_p.id != 1">
                                                        <td>
                                                            <b-form-input size="sm" v-model="item_p.used" :disabled="presc_status == 1" type="text" placeholder="signatura, waktu penggunaan, & aturan pakai"/>
                                                        </td>
                                                        <td>
                                                            <b-form-input size="sm" v-model="item_p.value" :disabled="presc_status == 1" type="number" min="1" placeholder="jumlah" />
                                                        </td>
                                                        <b-td>
                                                            <v-select v-model="item_p.package" :options="optParentPackage" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                                        </b-td>
                                                        <b-td></b-td>
                                                    </template>
                                                    <template v-else>
                                                        <b-td colspan="4"></b-td>
                                                    </template>
                                                </b-tr>
                                                <tr class="item fs-11" v-for="(item, index) in home_recipe.form_prescription" v-if="item_p.id == item.prescription_type_id">
                                                    <td class="text-center">
                                                        <b-button @click="removeHRItem(index)" :disabled="presc_status == 1" variant="danger-600" size="sm" class="mr-2">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </td>
                                                    <td>{{ item.name }}</td>
                                                    <td>
                                                        <b-form-input v-model="item.used" :disabled="presc_status == 1" size="sm" type="text" v-if="item.prescription_type_id == 1"/>
                                                        <div v-else class="text-center">-</div>
                                                    </td>
                                                    <td>
                                                        <b-form-input v-model="item.value_package" :disabled="presc_status == 1" size="sm" type="number" min="1" />
                                                    </td>
                                                    <td>
                                                        <v-select v-model="item.package_id" :options="PackageOptions" :reduce="item => item.value" :disabled="presc_status == 1" label="title" placeholder="pilih" class="select-size-sm" />
                                                    </td>
                                                    <td>
                                                        <b-form-input v-model="item.desc" :disabled="presc_status == 1" size="sm" type="text" />
                                                    </td>
                                                </tr>
                                            </template>
                                        </b-table-simple>
                                    </div>

                                    <b-card-footer class="py-1 px-2">
                                        <div class="d-flex">
                                            <b-button
                                                @click="changeTab('PHYSIOTHERAPY')"
                                                :disabled="active.disabled.section"
                                                variant="outline-neutral-700"
                                                class="rounded"
                                                size="sm"
                                            >
                                                <feather-icon icon="ChevronLeftIcon" />
                                                Kembali
                                            </b-button>
                                            <b-button
                                                v-if="presc_status == 0"
                                                @click="savePresc(active.section, 'PRESC')"
                                                :disabled="active.disabled.section"
                                                variant="w3green"
                                                class="ml-2 rounded"
                                                size="sm"
                                            >
                                                Simpan Resep Pulang
                                            </b-button>
                                        </div>
                                    </b-card-footer>
                                </b-tab>
                            </b-tabs>
                        </b-card>
                    </section>

                    <section v-if="active.section == 'NSDIAGNOSIS'" class="card">
                        <div class="card-header bg-w3green text-white fs-14 font-weight-bold py-10">PLAN</div>
                        <div class="card-body px-20 py-8" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <div class="row">
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Diagnosa</label>
                                    <textarea v-model.lazy="form_diagnosis.nursing_diagnose" rows="3" class="form-control form-control-sm" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <div class="d-flex">
                                <b-button
                                    @click="changeTab('VITAL')"
                                    :disabled="active.disabled.section"
                                    variant="outline-neutral-700"
                                    class="rounded"
                                    size="sm"
                                >
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button
                                    @click="changeSection(active.section, 'NSPLAN')"
                                    :disabled="active.disabled.section"
                                    variant="w3green"
                                    class="ml-2 rounded"
                                    size="sm"
                                >
                                    Simpan &amp; lanjutkan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                            </div>
                        </div>
                        </section>

                    <section v-if="active.section == 'NSPLAN'" class="card">
                        <div class="card-header bg-w3green text-white fs-14 font-weight-bold py-10">PLAN</div>
                        <div class="card-body px-20 py-8" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <div class="row">
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Plan</label>
                                    <textarea v-model.lazy="form_plan.nursing_plan" rows="3" class="form-control form-control-sm" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <div class="d-flex">
                                <b-button
                                    @click="changeTab('NSDIAGNOSIS')"
                                    :disabled="active.disabled.section"
                                    variant="outline-neutral-700"
                                    class="rounded"
                                    size="sm"
                                >
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button
                                    @click="changeSection(active.section, 'NSPLAN')"
                                    :disabled="active.disabled.section"
                                    variant="w3green"
                                    class="ml-2 rounded"
                                    size="sm"
                                >
                                    Simpan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                                </div>
                            </div>
                        </section>

                    <section v-if="active.section == 'NUTRITION'" class="card">
                        <div class="card-header bg-w3green text-white fs-14 font-weight-bold"> GIZI</div>
                        <div class="card-body px-20 py-8 position-sticky" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <div class="row">
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Asesmen</label>
                                    <textarea v-model.lazy="form_nutrition.nutrition_assessment" rows="3" class="form-control form-control-sm" autocomplete="off" />
                                </div>
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Diagnosis</label>
                                    <textarea v-model.lazy="form_nutrition.nutrition_diagnosis" rows="3" class="form-control form-control-sm" autocomplete="off" />
                                </div>
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Intervensi</label>
                                    <textarea v-model.lazy="form_nutrition.nutrition_intervention" rows="3" class="form-control form-control-sm" autocomplete="off" />
                                </div>
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Monitoring & Evaluasi</label>
                                    <textarea v-model.lazy="form_nutrition.nutrition_monitoring_evaluation" rows="3" class="form-control form-control-sm" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <div class="d-flex">
                                <b-button
                                    @click="changeSection(active.section, 'NUTRITION')"
                                    :disabled="active.disabled.section"
                                    variant="w3green"
                                    class="ml-2 rounded"
                                    size="sm"
                                >
                                    Simpan
                                </b-button>
                            </div>
                        </div>

                    </section>

               </div>
            </div>

            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <div class="col-lg-2">
                        <b-button @click="closeMainModal" :disabled="isDisabled" size="sm" variant="orange-500" class="btn-block" :class="active.section == 'PRESC' ? 'visible' : 'invisible'">
                    <feather-icon icon="SaveIcon" class="mr-1" />
                    Simpan Semua
                </b-button>
            </div>
                </div>
                <!-- <div class="ml-2 d-flex align-items-center">
                    <label class="mb-0 mr-2 text-muted" style="font-size: 12px; white-space: nowrap;">Terakhir diperbarui:</label>
                        <b-form-input
                        v-model="last_updated"
                        type="date"
                        size="sm"
                        style="width: 160px;"
                        />
                </div> -->
            </template>
        </b-modal>

        <b-modal v-model="active.modal.presc_parent" size="sm" no-close-on-esc no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA AKAN MENAMBAHKAN SEDIAAN
                    <span class="text-primary">{{ temp_presc_parent.parent_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="closePreParent" size="18" class="pointer" />
            </template>
            <section>
                <div>
                    <label class="fs-13 mb-0">Kemasan</label>
                    <v-select v-model="temp_presc_parent.package" :options="optParentPackage" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Waktu konsumsi / pemakaian</label>
                    <b-input-group size="sm" prepend="TIAP">
                        <b-form-select v-model="temp_presc_parent.signatura_time" :options="optSignTime" text-field="title" size="sm" />
                    </b-input-group>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Administer</label>
                    <v-select v-model="temp_presc_parent.administer" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                </div>
                <div v-if="temp_presc_parent.administer == other_administer" class="mt-10">
                    <label class="fs-13 mb-0">Administer Lainnya</label>
                    <b-form-input v-model="temp_presc_parent.desc" size="sm" autocomplete="off"/>
                </div>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="addParent" size="sm" variant="primary">TAMBAHKAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal.presc_items" size="sm" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA AKAN MENAMBAHKAN
                    <span class="text-primary">{{ temp_presc_items.parent_name }} - {{ temp_presc_items.catalog_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <section>
                <div>
                    <label class="fs-13 mb-0">Signatura</label>
                    <div class="row">
                        <div class="col-lg-4 pr-lg-0">
                            <b-form-input v-model="temp_presc_items.signatura_dose" type="number" size="sm" autocomplete="off" placeholder="dosis"/>
                        </div>
                        <v-select v-model="temp_presc_items.signatura_unit" :options="optItemSignUnits" :reduce="item => item.value" label="title" placeholder="satuan dosis" class="select-size-sm col-lg-4 mt-8 mt-lg-0"/>
                        <b-input-group v-if="temp_presc_items.parent_id == 1" size="sm" prepend="TIAP" class="col-lg-4 pl-lg-0 mt-8 mt-lg-0">
                            <b-form-select v-model="temp_presc_items.signatura_time" :options="optSignTime" text-field="title" size="sm" />
                        </b-input-group>
                    </div>
                </div>
                <template v-if="temp_presc_items.parent_id == 1">
                    <div class="mt-10">
                        <label class="fs-13 mb-0">Administer</label>
                        <v-select v-model="temp_presc_items.administer" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                    </div>
                    <div v-if="temp_presc_items.administer == other_administer" class="mt-10">
                        <label class="fs-13 mb-0">Administer Lainnya</label>
                        <b-form-input v-model="temp_presc_items.desc" size="sm" autocomplete="off"/>
                    </div>
                </template>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <b-button @click="addPrescItem" size="sm" variant="primary">TAMBAHKAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal ref="confirmModal" size="sm">
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase text-center w-100">PERUBAHAN BELUM DISIMPAN</div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="text-center fs-13">
                <p>Perubahan yang anda lakukan pada <b>{{ getSectionName(this.active.section) }}</b> belum tersimpan. Apakah anda ingin menyimpan perubahan?</p>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-center w-100">
                    <b-button size="sm" variant="outline-danger-500" @click="discardChanges">Buang Perubahan</b-button>
                    <b-button size="sm" variant="w3green" class="ml-1" @click="saveChanges" :disabled="active.disabled.section">Simpan & lanjutkan</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal ref="confirmKardexModal" size="ms" no-close-on-backdrop no-close-on-esc>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase text-center w-100">KONFIRMASI PERUBAHAN PADA KARDEKS AKTIF</div>
                <feather-icon icon="XIcon" @click="closePrescModal()" size="18" class="pointer" />
            </template>
            <div class="fs-13">
                <p>{{ getNarativeKrxchanges }}</p>

                <template v-if="kardexChanges?.form_prescription?.length > 0 || kardexChanges?.form_prescription_parent?.length > 0">
                    <b-table-simple small fixed bordered class="fs-11 text-center">
                        <b-tr>
                            <b-th style="width: 40px">NO</b-th>
                            <b-th style="width: 250px" class="text-left">NAMA OBAT</b-th>
                            <b-th>KEMASAN / SATUAN</b-th>
                            <b-th>DOSIS</b-th>
                            <b-th>WAKTU</b-th>
                            <b-th>ADMINISTER</b-th>
                        </b-tr>

                        <template v-if="kardexChanges?.form_prescription?.length > 0">
                            <b-tr v-for="(item, index) in kardexChanges.form_prescription">
                                <b-td>{{ index+1 }}</b-td>
                                <b-td class="text-left">
                                    <div>{{ item.name }}</div>
                                    <b-badge v-if="item.prescription_type_id == 1" :variant="getKardexItemStatusVariant(item.status)" class="text-uppercase">{{ getKardexItemStatusName(item.status) }}</b-badge>
                                </b-td>
                                <b-td>{{ item.package_name }}</b-td>
                                <b-td>{{ item.dose }} {{ getDynamicOptName(item.dose_unit_id, optSignUnits)}}</b-td>
                                <b-td>{{ item.prescription_type_id == 1 ? `TIAP ${getDynamicOptName(item.signatura_times_id, optSignTime)}` : '-' }}</b-td>
                                <b-td>
                                    <template v-if="item.prescription_type_id == 1">
                                        <span>{{ getDynamicOptName(item.administer_id, optAdminister) }}</span>
                                        <span v-if="item.administer_id == other_administer">: {{ item.desc }}</span>
                                    </template>
                                    <div v-else>-</div>
                                </b-td>
                            </b-tr>
                        </template>

                        <template v-if="kardexChanges?.form_prescription_parent?.length > 0">
                            <b-tr v-for="(item, index) in kardexChanges.form_prescription_parent">
                                <b-td>{{ kardexChanges?.form_prescription?.length ? kardexChanges?.form_prescription?.length+index+1 : index+1 }}</b-td>
                                <b-td class="text-left">
                                    <div>{{ getPrescParentName(item.id || '-') }}</div>
                                    <b-badge :variant="getKardexItemStatusVariant(item.status)" class="text-uppercase">{{ getKardexItemStatusName(item.status) }}</b-badge>
                                </b-td>
                                <b-td>{{ getParentPackageName(item.package) }}</b-td>
                                <b-td>{{ item.value }}</b-td>
                                <b-td>TIAP {{ getDynamicOptName(item.signatura_times_id, optSignTime) }}</b-td>
                                <b-td>
                                    <span>{{ getDynamicOptName(item.administer_id, optAdminister) }}</span>
                                    <span v-if="item.administer_id == other_administer">: {{ item.desc }}</span>
                                </b-td>
                            </b-tr>
                        </template>
                    </b-table-simple>
                </template>

                <div v-if="kardexChanges?.non_pharmacological?.length > 0">
                    <p class="mb-0 font-weight-bold">NON-FARMAKOLOGI: </p>
                    <li v-for="item in kardexChanges.non_pharmacological">
                        <span>{{ item.name }} </span>
                        <small class="fs-11 font-italic text-neutral-500">(ditambahkan oleh {{ item.doctor_added_name || '-' }})</small>
                    </li>
                </div>
                <!-- <pre>{{ kardexChanges }}</pre> -->
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-center w-100">
                    <b-button @click="closePrescModal()" size="sm" variant="outline-danger-500" class="px-32">Batal</b-button>
                    <b-button @click="proceedToKardex(active.destination)" :disabled="active.disabled.changes_presc" size="sm" variant="w3green" class="ml-1">Simpan & lanjutkan</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal ref="exitWarningModal" size="sm">
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase text-center w-100">PERUBAHAN BELUM DISIMPAN</div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="text-center fs-13">
                <p>Perubahan yang anda lakukan pada <b>{{ getSectionName(this.active.section) }}</b> belum tersimpan. Apakah anda ingin menyimpan perubahan sebelum keluar?</p>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-center w-100">
                    <b-button @click="discardAndExit" size="sm" variant="outline-danger-500">Buang Perubahan</b-button>
                    <b-button @click="saveAndExit" :disabled="disabled_exit" size="sm" variant="w3green" class="ml-1">Simpan & keluar</b-button>
                </div>
            </template>
        </b-modal>

        <!-- <b-modal v-model="active.modal.mdl_presc_template" size="sm">
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase text-center w-100">ANDA AKAN MENAMBAHKAN RESEP INI SEBAGAI TEMPLATE</div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <section>
                <div>
                    <label class="fs-13 mb-0">Nama Template Resep</label>
                    <b-form-input v-model="presc_inp_templating.name" autocomplete="off" size="sm" placeholder="tuliskan nama template resep" />
                </div>
                <div>
                    <template v-if="presc_inp_templating.form_prescription_parent">
                        <div v-for="(list, idx_p) in presc_inp_templating.form_prescription_parent" class="mt-12">
                            <div :class="getPrescParentVariant(list.id)" class="px-12 py-1">
                                <div class="fs-14 font-weight-bold">{{ getPrescParentName(list.id) }}</div>
                                <div v-if="list.id != 1" class="fs-12 text-italic" style="font-weight: 400">
                                    <span>Jumlah: {{ list?.value }}, </span>
                                    <span>Kemasan: {{ getParentPackageName(list.package) }}, </span>
                                    <span>Aturan: {{ list.used }}</span>
                                </div>
                            </div>
                            <div v-if="presc_inp_templating?.form_prescription" class="fs-13 px-12 mt-1">
                                <li v-for="unit in presc_inp_templating.form_prescription" v-if="list.id == unit.prescription_type_id">{{ unit?.name || '-' }}</li>
                            </div>
                        </div>
                    </template>
                    <template v-else>-</template>
                </div>
            </section>
            <template #modal-footer>
                <b-button @click="savePrescTemplate" :disabled="active.disabled.dsb_presc_template" size="sm" variant="w3green" class="m-0 px-24">SIMPAN</b-button>
            </template>
        </b-modal> -->

        <b-modal v-model="active.modal.hr_parent" size="sm" no-close-on-esc no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA AKAN MENAMBAHKAN SEDIAAN
                    <span class="text-primary">{{ temp_hrParent.parent_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="closeHRPreParent" size="18" class="pointer" />
                <!-- <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" /> -->
            </template>
            <section>
                <div>
                    <label class="fs-13 mb-0">Signatura</label>
                    <v-select v-model="temp_hrParent.signatura" :options="OptionsSignatura" :reduce="item => item.value" label="title" placeholder="signatura" class="select-size-sm"/>
                </div>
                <div v-if="temp_hrParent.signatura == 'other'" class="mt-10">
                    <label class="fs-13 mb-0">Signatura lain</label>
                    <b-form-input v-model="temp_hrParent.other_signatura" size="sm" autocomplete="off"/>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Waktu Penggunaan</label>
                    <v-select v-model="temp_hrParent.consume_time" :options="OptionsConsumeTime" :reduce="item => item.value" label="title" placeholder="waktu penggunaan" class="select-size-sm"/>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Aturan Pakai</label>
                    <v-select v-model="temp_hrParent.utilization" :options="OptionsUtilization" :reduce="item => item.value" label="title" placeholder="aturan pakai" class="select-size-sm"/>
                </div>
                <div v-if="temp_hrParent.utilization == 'other'" class="mt-10">
                    <label class="fs-13 mb-0">Aturan Pakai lain</label>
                    <b-form-input v-model="temp_hrParent.other_utilization" size="sm" autocomplete="off"/>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Kemasan</label>
                    <v-select v-model="temp_hrParent.package" :options="optParentPackage" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                </div>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="addHRParent" size="sm" variant="primary">TAMBAHKAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal.hr_items" size="sm" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA AKAN MENAMBAHKAN OBAT
                    <span class="text-primary">{{ temp_hrItems.parent_name }}-{{ temp_hrItems.catalog_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <section>
                <div>
                    <label class="fs-13 mb-0">Signatura</label>
                    <v-select v-model="temp_hrItems.signatura" :options="OptionsSignatura" :reduce="item => item.value" label="title" placeholder="signatura" class="select-size-sm"/>
                </div>
                <div v-if="temp_hrItems.signatura == 'other'" class="mt-10">
                    <label class="fs-13 mb-0">Signatura lain</label>
                    <b-form-input v-model="temp_hrItems.other_signatura" size="sm" autocomplete="off"/>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Waktu Penggunaan</label>
                    <v-select v-model="temp_hrItems.consume_time" :options="OptionsConsumeTime" :reduce="item => item.value" label="title" placeholder="waktu penggunaan" class="select-size-sm"/>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Aturan Pakai</label>
                    <v-select v-model="temp_hrItems.utilization" :options="OptionsUtilization" :reduce="item => item.value" label="title" placeholder="aturan pakai" class="select-size-sm"/>
                </div>
                <div v-if="temp_hrItems.utilization == 'other'" class="mt-10">
                    <label class="fs-13 mb-0">Aturan Pakai lain</label>
                    <b-form-input v-model="temp_hrItems.other_utilization" size="sm" autocomplete="off"/>
                </div>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="addHRPrescItem" size="sm" variant="primary">TAMBAHKAN</b-button>
                </div>
            </template>
        </b-modal>

        <LabResultModalView ref="labModalResult"/>
        <RadResultModalView ref="radModalResult"/>
        <ExternalResumeView ref="extResumeRef"/>
    </div>
</template>

<script>
import { mapState } from 'vuex'
import moment from "moment"
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import LabResultModalView from '@/views/pages/patient/medical_support/LabResultModalView.vue';
import RadResultModalView from '@/views/pages/patient/medical_support/RadResultModalView.vue';
import ExternalResumeView from '@/views/pages/patient/resume/ExternalResumeView.vue';
import dateMixin from "@/mixins/dateMixin";
import optionMixin from '@/mixins/optionMixin';
import draggable from 'vuedraggable';
import io from 'socket.io-client';

export default {
    components: { LabResultModalView, RadResultModalView, ExternalResumeView, draggable},
    mixins: [dateMixin, optionMixin],
    data() {
        return {
            notif_socket: io(process.env.VUE_APP_SOCKET_IO_NOTIF, { path: '/socket.io', transports: ['websocket', 'polling', 'flashsocket'], withCredentials: true}),
            urlbe:process.env.VUE_APP_IMAGE,
            urlAsset:process.env.VUE_APP_ASSETS_BE,
            dynamicHeight: "80vh",
            differenceHeight: "85px",
            userData: JSON.parse(localStorage.getItem('userData')),

            admin_id: 'd77142c4-4777-47d8-bd0c-fd7dfd134af4',
            admin_group_id: 'ddad321c-a9d8-430c-95f0-de38d20834a1',
            doctor_group_id: 'efb3fdff-fbe3-4954-a391-3729e544b62a',
            general_dr_position: 'd93bd445-df1e-451a-bf95-5fc10546c133',
            anesthesia_dr_position: 'fc9f95b0-79c4-4edd-811f-0f09eac8aac3',
            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            hfi_outpatient: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
            hfi_emergency: '444b3134-5b75-4b67-af72-a558d46716d7',
            hfii_hcu: '1843843c-af62-4452-b19f-beddd1acd0ec',
            hfii_picu: 'e66e6ed4-4ef4-4375-a6d0-0445e3ec3039',
            hfii_iccu: '7f2a9f80-c30d-4cbd-9f0d-364cda2ec84c',
            hfii_icu: '6c7d7e53-ff24-4246-85b2-07ea77ac801f',
            hfii_icu_isolation: 'ff1f252e-daab-4bdb-b308-e9f4cb11a47d',
            hfii_nicu: '5515af72-6dcb-415d-89e4-2433e7b0149d',
            hfii_nicu_isolation: 'ed40f235-1c7b-4954-aebf-b3dc70a674b7',
            hfii_perina:'05079cd9-07c6-4cf9-beb0-271eb301b7c1',

            central_depo: '297c44f0-12ef-40e6-b9a7-c831065b3372',
            nutritionist_group_id: '4677762a-5fd0-4d14-bb96-6ec2f39f1097',
            other_administer: 'ea3c2573-b9f5-428d-ada4-0ce6ea67c72f',

            reg_des_id: '',
            regData: {},
            global_cppt_id: '',
            global_gate: '',
            global_from: '',
            cppt_user_added: '',
            last_updated: '',
            consult_type3: false,
            consult_intensive: false,
            // isDisabled: false,
            // blankObjects: [],

            active: {
                modal: {
                    main: false,
                    allergy: false,
                    presc_parent: false,
                    presc_items: false,
                    mdl_presc_template: false,
                    hr_parent: false,
                    hr_items: false,
                },
                advance: {
                    antopometri: false,
                    generalis: false,
                    oftalmologis: false,
                    template_presc: false,
                    template_anam: false,
                },
                disabled: {
                    changes_presc: false,
                    section: false,
                    dsb_presc_template: false,
                    dsb_anamn_template: false,
                },
                section: 'ANAMNESA',
                destination: '',
                access: '',
            },
            temp_header: {
                alias_name: '',
                name: '',
                norm: '',
                birth_date: '',
                weight: '',
                height: '',
            },
            transactionFields: [
                { key: 'action', label: 'Aksi', thStyle: 'width: 60px' },
                { key: 'no', label: 'No', thStyle: 'width: 60px' },
                { key: 'var_2', label: 'Jumlah', thStyle: 'width: 100px' },
                { key: 'title', label: 'Nama Prosedur', thClass: 'text-left' },
            ],
            icd10Fields: [
                { key: 'action', label: 'Aksi', thStyle: 'width: 60px' },
                { key: 'no', label: 'No', thStyle: 'width: 60px' },
                { key: 'title', label: 'ICD10', thClass: 'text-left' },
            ],
            icd9cmFields: [
                { key: 'action', label: 'Aksi', thStyle: 'width: 60px' },
                { key: 'no', label: 'No', thStyle: 'width: 60px' },
                { key: 'title', label: 'ICD9CM', thClass: 'text-left' },
            ],
            nonPharmaFields: [
                { key: 'action', label: 'AKSI', thStyle: { width: '50px' } },
                { key: 'no', label: 'NO', thStyle: { width: '40px' } },
                { key: 'name', label: 'NON FARMAKOLOGI', thClass: 'text-left' },
                { key: 'doctor_added_name', label: 'DITAMBAHKAN OLEH', thStyle: { width: '200px' } },
            ],

            isPews: false,
            nextTab: null,

            original_anamnesa: {},
            original_vital: {},
            original_generalis: {},
            original_diagnosis: {},
            original_procedure: {},
            original_plan: {},
            original_laboratory: {},
            original_radiology: {},
            original_physiotherapy: {},
            original_prescription: {},

            form_anamnesa: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                subjective: '',
                nursing_objective: '',
            },

            form_vital: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                systolic_blood_pressure: '',
                diastolic_blood_pressure: '',
                pulse_frequency: '',
                breath_frequency: '',
                oximetry_frequency: '',
                temperature: '',
                aware: '',
                oxygen_type_id: '',
                eye_score: '',
                verbal_score: '',
                motor_score: '',
                skin_color: '',
                behavior: '',
                weight: '',
                height: '',
                imt: '',
                objective: '',
            },

            form_generalis: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                is_initial_inpatient: 1,
                is_hemodialysis: 0,
                is_medical_rehabilitation: 0,
                head: 1,
                head_hair_color: 1,
                head_hair_condition: 1,
                eye_conjunctive: 1,
                eye_sclera: 1,
                eye_pupil: 1,
                eye_light_reflex: 1,
                mouth_mucosa: 1,
                mouth_discoloration: 1,
                mouth_tongue: 1,
                mouth_caries: 1,
                neck_kgb: 1,
                neck_trachea_position: 1,
                neck_massa: 1,
                neck_jvp: 1,
                thorax_insp_chest: 1,
                thorax_insp_retraction: 1,
                thorax_palp_chest: 1,
                thorax_palp_fremitus: 1,
                thorax_palp_crepitation: 1,
                thorax_ausk_pul_vasiculer: 1,
                thorax_ausk_pul_rhonki: 1,
                thorax_ausk_pul_wheezing: 1,
                thorax_ausk_cor_heart_sound: 1,
                thorax_ausk_cor_murmur: 1,
                thorax_ausk_cor_gallop: 1,
                abdomen_supel: 1,
                abdomen_bowel_sounds: 1,
                abdomen_tenderness: 1,
                abdomen_hepar_lien: 1,
                abdomen_other_physical_check: '',
                extremity_acral: 1,
                extremity_crt: 1,
                extremity_edema: 1,
                genitalia: '',
                localist_status: '',
                neurologist_status: '',
                ophthal_status: '',
            },

            form_diagnosis: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                icd10: [],
                icd10_notes: '',
                non_icd10: [],
                nursing_diagnose: '',
            },

            form_procedure: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                icd9cm: [],
                therapy_plan: '',
                transaction_doctor: [],
            },

            form_plan: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                nursing_plan: '',
                plan: '',
                pharmacological_plan: '',
            },

            form_laboratory: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                laboratory_request_items: [],
                clinical_indications: '',
            },

            form_radiology: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                radiology_request_items: [],
                clinical_indications: '',
            },

            form_physiotherapy: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                physiotherapy_request_items: [],
            },

            input_prescription: {
                id: '',
                doctor_in_charge_id: '',
                registration_destination_id: '',
                health_facility_item_id: '',
                form_prescription_parent: [{
                    id: 1,
                }],
                form_prescription: [],
                non_pharmacological: [],
                soap_id: '',
                cppt_id: '',
                sbar_id: '',
            },

            form_nutrition: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                nutrition_assessment: '',
                nutrition_diagnosis: '',
                nutrition_intervention: '',
                nutrition_monitoring_evaluation: '',
            },

            input_other10: '',
            costing_id:'',
            icd10_id:'',
            OptionsIcd10:[],
            icd9cm_id:'',
            OptionsIcd9cm:[],

            supportPackage: {
                lab_package_list: [],
                rad_package_list: [],
                phy_package_list: [],
            },
            searchlab:'',
            lab_status: 1,
            searchrad:'',
            rad_status: 1,
            searchphy:'',

            PackageOptions: [],
            OptionsCatalog: [],
            catalog_list: [],
            kxStock: { value: 1, title: 'OBAT SATUAN' },
            kxCatalog: '',
            kxNonPharma: '',

            temp_presc_parent: {
                parent_id: '',
                parent_name: '',

                package: '',
                signatura_time: '',
                administer: '',
                desc: ''
            },
            temp_presc_items: {
                parent_id: '',
                parent_name: '',
                catalog_name: '',
                data_catalog: {},

                signatura_dose: '',
                signatura_unit: '',
                signatura_time: '',
                administer: '',
                desc: ''
            },
            // non_pharma_list: [],
            // optNonPharma: [],

            getCosting: [],

            presc_search: '',
            presc_filtered: [],
            presc_list_templating: [],
            presc_inp_templating: {
                id: '',
                name: '',
                form_prescription_parent: [],
                form_prescription: []
            },

            anamn_search: '',
            anamn_list_templating: [],
            anamn_filtered: [],
            anamn_form_templating: {
                id: '',
                name: '',
                anamesa_type_id: 1,
                family_name: '',
                family_relation_id: '',
                main_complaint: '',
                history_disease_now: '',
                history_disease_past: '',
                history_drugs: '',
            },

            presc_status: 0,
            home_recipe: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                pharmacy_depo_id: '',
                type_prescription_id: '',
                form_prescription_parent: [],
                form_prescription: [],
            },
            hrStock: '',
            hrCatalog: '',
            optHrCatalog: [],
            temp_hrParent: {
                parent_id: '',
                parent_name: '',
                signatura: '',
                other_signatura: '',
                consume_time: '',
                utilization: '',
                other_utilization: '',
                package: '',
            },
            temp_hrItems: {
                parent_id: '',
                parent_name: '',
                catalog_name: '',
                signatura: '',
                other_signatura: '',
                consume_time: '',
                utilization: '',
                other_utilization: '',
                data_catalog: {},
            },

            history: {
                cppt: [],
                cppt_doctor: [],
                nurse: [],
                soap: [],
                laboratory: [],
                radiology: [],
                physiotherapy: [],
                resume: [],
                examination: [],
            },

            cppt_load: {
                count: 0,
                limit_display: 3,
                add_display: 3,
            },

            nurse_load: {
                count: 0,
                limit_display: 3,
                add_display: 3,
            },

            soap_load: {
                count: 0,
                limit_display: 3,
                add_display: 3,
            },

            lab_load: {
                count: 0,
                limit_display: 3,
                add_display: 3,
            },

            rad_load: {
                count: 0,
                limit_display: 3,
                add_display: 3,
            },

            phy_load: {
                count: 0,
                limit_display: 3,
                add_display: 3,
            },

            rsm_load: {
                count: 0,
                limit_display: 3,
                add_display: 3,
            },

            exa_load: {
                count: 0,
                limit_display: 3,
                add_display: 3,
            },

            optSignTime: [],
            optSignDose: [],
            optSignUnits: [],
            optAdminister: [],
            optItemSignUnits: [],

            full_kardex: [],
            // params_kardex: {
            //     registration_destination_id: '',
            //     date_added: moment(new Date()).format('YYYY-MM-DD'),
            // },
            combine_empty_found: [],
            kardex_stock: [],
            kardex_do: [],
            kardexChanges: [],
            needToTrigerNotif: true,

            disabled_exit: false,
        }
    },
    computed: {
        ...mapState({
            itemsPhysiotherapy: state => state.physiotherapy.package.items,

            OptionsDoctorRef: state => state.employee.options.employee,
            OptionsCosting: state => state.costing.options.costing,

            // vital option
            OptionsOxygen: state => state.soap.vitals.OptionsOxygen,
            OptionsAware: state => state.soap.vitals.OptionsAware,
            OptionGcsEye: state => state.soap.vitals.OptionGcsEye,
            OptionGcsVerbal: state => state.soap.vitals.OptionGcsVerbal,
            OptionGcsMotoric: state => state.soap.vitals.OptionGcsMotoric,
            optSkinColor: state => state.frontoptions.general.vital_sign.skin_color,
            optBehavior: state => state.frontoptions.general.vital_sign.behavior,

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

            // prescription options
            optParentPackage: state => state.frontoptions.doctor.assessment.prescription.parent_package,
            optParentStock: state => state.frontoptions.doctor.assessment.prescription.parent_stock,
            OptionsDepo: state => state.frontoptions.doctor.assessment.prescription.depo,
            optKardexItemStatus: state => state.frontoptions.doctor.assessment.prescription.kardex_item_status,
            OptionsSignatura: state => state.frontoptions.doctor.assessment.prescription.signatura,
            OptionsConsumeTime: state => state.frontoptions.doctor.assessment.prescription.consume_time,
            OptionsUtilization: state => state.frontoptions.doctor.assessment.prescription.utilization,
            // OptionsTypePresc: state => state.frontoptions.doctor.assessment.prescription.prescription_type,
        }),
        filteredListLab() {
            return this.supportPackage.lab_package_list.map((lab) => {
                const filteredCategoryItems = lab.category_items.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchlab.toLowerCase())
                );

                return {
                    ...lab,
                    category_items: filteredCategoryItems,
                };
            }).filter(lab => lab.category_items.length > 0);
        },
        filteredListRad() {
            return this.supportPackage?.rad_package_list.map((rad) => {
                const filteredCategoryItems = rad?.category_items?.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchrad.toLowerCase())
                );

                return {
                    ...rad,
                    category_items: filteredCategoryItems,
                };
            }).filter(rad => rad?.category_items?.length > 0);
        },
        filteredListPhy() {
            return this.supportPackage.phy_package_list.filter((phy) => {
                const computedPhyObj = { ...phy}
                return Object.keys(computedPhyObj)
                    .some(key => ('' + computedPhyObj[key]).toLowerCase().includes(this.searchphy.toLowerCase()))
            })
        },

        getNarativeKrxchanges() {
            const p = this.kardexChanges?.form_prescription?.length > 0 ||
                    this.kardexChanges?.form_prescription_parent?.length > 0;
            const n = this.kardexChanges?.non_pharmacological?.length > 0;

            const types = [];
            if (p) types.push('Resep');
            if (n) types.push('Non-farmakologi');

            return types.length ? `${types.join(' dan ')} telah diubah, harap periksa kembali sebelum disimpan.` : '';
        },
    },
    filters:{
        number(value) {
            return Number(value)
        },
    },

    methods: {
        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },
        updateAdminister(index, value, target) {
            const arrayKey = target ? `form_prescription_${target}` : 'form_prescription'
            const targetArray = this.input_prescription[arrayKey]
            if(value != this.other_administer) targetArray[index].desc = ''
        },

        getKardexItemVariant(obj, suggestion_status, is_approve) {
            if ((obj?.is_suggestion == 1 && suggestion_status == 2 && is_approve == 0) || (obj.is_reject == 1 && is_approve == 1)) return 'bg-ax-danger-300 text-white'
            if (obj?.is_change == 1) return 'bg-warning-400'
        },

        kxDisabledCriteria(is_exist, user_added) {
            if (this.userData.employee_id == this.admin_id || this.userData.position.id == this.general_dr_position || (this.userData.employee_id == this.regData.doctor_in_charge_id && this.userData.position.id == this.anesthesia_dr_position) || is_exist == 0) return false
            return this.userData.employee_id !== user_added;
        },

        intensiveCriteria() {
            if(
                this.regData.health_facility_id == this.hfi_inpatient && (
                    this.regData.health_facility_item_id == this.hfii_hcu
                    || this.regData.health_facility_item_id == this.hfii_picu
                    || this.regData.health_facility_item_id == this.hfii_iccu
                    || this.regData.health_facility_item_id == this.hfii_icu
                    || this.regData.health_facility_item_id == this.hfii_icu_isolation
                    || this.regData.health_facility_item_id == this.hfii_nicu
                    || this.regData.health_facility_item_id == this.hfii_nicu_isolation
                    || this.regData.health_facility_item_id == this.hfii_perina
                )) return true
            return false
        },

        triggerToResume() {
            const message = {
                regData: this.regData,
                cppt_id: this.global_cppt_id,
            }
            this.$refs.extResumeRef.createResume(message);
        },

        getSectionName(sectionKey){
            if (sectionKey == 'ANAMNESA') return 'Anamnesa'
            if (sectionKey == 'VITAL') return 'Tanda Vital'
            if (sectionKey == 'GENERALIS') return 'Status Generalis'
            if (sectionKey == 'DIAGNOSIS') return 'Diagnosa'
            if (sectionKey == 'PROCEDURE') return 'Prosedur'
            if (sectionKey == 'PLAN') return 'Plan'
            if (sectionKey == 'LABORATORY') return 'Laboratorium'
            if (sectionKey == 'RADIOLOGY') return 'Radiologi'
            if (sectionKey == 'PHYSIOTHERAPY') return 'Fisioterapi'
            if (sectionKey == 'PRESC') return 'Resep Kardeks Aktif'

            if (sectionKey == 'NSDIAGNOSIS') return 'Diagnosa'
            if (sectionKey == 'NSPLAN') return 'Plan'

            if (sectionKey == 'NUTRITION') return 'Gizi'
        },

        async cloneAllState() {
            this.original_anamnesa = JSON.parse(JSON.stringify(this.form_anamnesa))
            this.original_vital = JSON.parse(JSON.stringify(this.form_vital))
            this.original_generalis = JSON.parse(JSON.stringify(this.form_generalis))
            this.original_diagnosis = JSON.parse(JSON.stringify(this.form_diagnosis))
            this.original_procedure = JSON.parse(JSON.stringify(this.form_procedure))
            this.original_plan = JSON.parse(JSON.stringify(this.form_plan))
            this.original_laboratory = JSON.parse(JSON.stringify(this.form_laboratory))
            this.original_radiology = JSON.parse(JSON.stringify(this.form_radiology))
            this.original_physiotherapy = JSON.parse(JSON.stringify(this.form_physiotherapy))
            this.original_prescription = JSON.parse(JSON.stringify(this.input_prescription))
            this.original_nutrition = JSON.parse(JSON.stringify(this.form_nutrition));
        },

        onCloseMainModal() {
            if (this.hasChanges(this.active.section)) {
                this.$refs.exitWarningModal.show();
            } else {
                this.active.modal.main = false;
            }
        },
        discardAndExit() {
            // Discard changes and close modal
            const formMapping = {
                ANAMNESA: ["form_anamnesa", "original_anamnesa"],
                VITAL: ["form_vital", "original_vital"],
                GENERALIS: ["form_generalis", "original_generalis"],
                DIAGNOSIS: ["form_diagnosis", "original_diagnosis"],
                PROCEDURE: ["form_procedure", "original_procedure"],
                PLAN: ["form_plan", "original_plan"],
                LABORATORY: ["form_laboratory", "original_laboratory"],
                RADIOLOGY: ["form_radiology", "original_radiology"],
                PHYSIOTHERAPY: ["form_physiotherapy", "original_physiotherapy"],
                PRESC: ["input_prescription", "original_prescription"],
                NSDIAGNOSIS: ["form_diagnosis", "original_diagnosis"],
                NSPLAN: ["form_plan", "original_plan"],
                NUTRITION: ["form_nutrition", "original_nutrition"],
            };
            if (formMapping[this.active.section]) {
                const [form, original] = formMapping[this.active.section];
                const id = this[form].id
                this[form] = JSON.parse(JSON.stringify(this[original]))
                this[form].id = id
            }
            this.$refs.exitWarningModal.hide();
            this.active.modal.main = false;
        },
        async saveAndExit() {
            // Save changes, then close modal
            try {
                this.disabled_exit = true
                await this.changeSection(this.active.section, this.active.section);
                this.$refs.exitWarningModal.hide();
                this.active.modal.main = false;
            } catch (error) {
                console.error('Error save & exit at: ', error);
            } finally {
                this.disabled_exit = false
            }
        },

        changeTab(destination) {
            this.resetPrescSelect()
            const changes = this.hasChanges(this.active.section)
            if (changes) {
                this.nextTab = destination
                this.$refs.confirmModal.show()
            } else {
                this.active.section = destination
                if (destination === 'DIAGNOSIS' && this.global_gate === 'main_gate') {
                    this.prefillDiagnosisFromHistory();
                }
            }
        },

        resetPrescSelect() {
            this.kxStock = { value: 1, title: 'OBAT SATUAN' }
            this.kxCatalog = null

            this.hrStock = null
            this.hrCatalog = null
        },

        hasChanges(exist) {
            const formMapping = {
                ANAMNESA: ["form_anamnesa", "original_anamnesa"],
                VITAL: ["form_vital", "original_vital"],
                GENERALIS: ["form_generalis", "original_generalis"],
                DIAGNOSIS: ["form_diagnosis", "original_diagnosis"],
                PROCEDURE: ["form_procedure", "original_procedure"],
                PLAN: ["form_plan", "original_plan"],
                LABORATORY: ["form_laboratory", "original_laboratory"],
                RADIOLOGY: ["form_radiology", "original_radiology"],
                PHYSIOTHERAPY: ["form_physiotherapy", "original_physiotherapy"],
                PRESC: ["input_prescription", "original_prescription"],
                NSDIAGNOSIS: ["form_diagnosis", "original_diagnosis"],
                NSPLAN: ["form_plan", "original_plan"],
                NUTRITION: ["form_nutrition", "original_nutrition"],
            };

            if (!formMapping[exist]) return false;

            const [form, original] = formMapping[exist]
            const formData = { ...this[form] }
            const originalData = { ...this[original] }

            // ngehapus properti yang gak perlu dibandingin
            const excludedProps = ['id']
            excludedProps.forEach(prop => {
                delete formData[prop]
                delete originalData[prop]
            });

            // Deep comparison - rekursif
            const isDifferent = (a, b) => {
                if (typeof a !== typeof b) return true;

                // Handle array (termasuk perubahan di dalam object array)
                if (Array.isArray(a) && Array.isArray(b)) {
                    if (a.length !== b.length) return true;
                    return a.some((item, index) => isDifferent(item, b[index]))
                }

                // Handle object
                if (typeof a === 'object' && a !== null && b !== null) {
                    const keysA = Object.keys(a)
                    const keysB = Object.keys(b)
                    if (keysA.length !== keysB.length) return true
                    return keysA.some(key => isDifferent(a[key], b[key]))
                }

                // Handle primitives - sengaja pake nullish
                return String(a ?? '') !== String(b ?? '')
            };

            // Cari perubahan
            const changes = {};
            Object.keys(formData).forEach(key => {
                if (isDifferent(formData[key], originalData[key])) {
                    changes[key] = {
                        before: originalData[key],
                        after: formData[key]
                    };
                }
            });

            // console.log("Perubahan yang signifikan:", changes);
            return Object.keys(changes).length > 0 ? changes : false
        },

        discardChanges() {
            const formMapping = {
                ANAMNESA: ["form_anamnesa", "original_anamnesa"],
                VITAL: ["form_vital", "original_vital"],
                GENERALIS: ["form_generalis", "original_generalis"],
                DIAGNOSIS: ["form_diagnosis", "original_diagnosis"],
                PROCEDURE: ["form_procedure", "original_procedure"],
                PLAN: ["form_plan", "original_plan"],
                LABORATORY: ["form_laboratory", "original_laboratory"],
                RADIOLOGY: ["form_radiology", "original_radiology"],
                PHYSIOTHERAPY: ["form_physiotherapy", "original_physiotherapy"],
                PRESC: ["input_prescription", "original_prescription"],
                NSDIAGNOSIS: ["form_diagnosis", "original_diagnosis"],
                NSPLAN: ["form_plan", "original_plan"],
                NUTRITION: ["form_nutrition", "original_nutrition"],
            };

            if (formMapping[this.active.section]) {
                const [form, original] = formMapping[this.active.section];

                const id = this[form].id
                this[form] = JSON.parse(JSON.stringify(this[original]))
                this[form].id = id
            }

            this.active.section = this.nextTab
            this.$refs.confirmModal.hide()
        },

        async saveChanges() {
            this.active.disabled.section = true
            await this.changeSection(this.active.section, this.nextTab)
            this.$refs.confirmModal.hide()
        },

        // ==================================================

        getMenusVariant(value) {
            if (value == this.active.section) {
                return 'frOrange'
            }
            return 'outline-frOrange'
        },

        async changeSection(exist, destination) {
            this.active.disabled.section = true
            this.active.destination = destination
            this.resetPrescSelect()

            const saveFunctions = {
                ANAMNESA: this.saveAnamnesa,
                VITAL: this.saveVital,
                GENERALIS: this.saveGeneralis,
                DIAGNOSIS: this.saveDiagnosis,
                PROCEDURE: this.saveProcedure,
                PLAN: this.savePlan,
                LABORATORY: this.saveLaboratory,
                RADIOLOGY: this.saveRadiology,
                PHYSIOTHERAPY: this.savePhysiotherapy,
                PRESC: this.saveKardex,
                NSDIAGNOSIS: this.saveDiagnosis,
                NSPLAN: this.savePlan,
                NUTRITION: this.saveNutrition,
            }

            const nextFunc = saveFunctions[exist]
            if (!nextFunc) return

            // Handle skip cases (LABORATORY & RADIOLOGY)
            if ((exist === 'LABORATORY' && this.lab_status != 1) ||
                (exist === 'RADIOLOGY' && this.rad_status != 1)) {
                this.active.section = destination
                return
            }

            // Handle special case (ANAMNESA)
            const params = exist === 'ANAMNESA' ? [exist, destination, 'main_section'] : [exist, destination]
            await nextFunc(...params)
        },

        getFirstNextSection(){
            if (this.active.access == 'doctor' || this.active.access == 'general_doctor') return 'GENERALIS'
            if (this.active.access == 'nurse') return 'NSDIAGNOSIS'
            return 'NUTRITION'
        },

        getSecondNextSection(){
            if (this.active.access == 'doctor') return 'LABORATORY'
            return 'PLAN'
        },

        async getMyCppt(cppt_id) {
            try {
                this.getAssessment()
                const response = await this.$store.dispatch('cppt/getByCpptId', cppt_id);
                if (response?.length > 0) {
                    await this.fillCpptForm(response[0])
                    this.getCpptHistory(this.regData.norm)
                    this.getDiagnosisCPPTHistory(this.regData.norm)
                }

                // console.log(`getMyCppt, gate: ${this.global_gate} from: ${this.global_from}`);
                if (this.global_from == 'consultation') {
                    this.$emit("refreshConsult", 'initial-message');
                }
            } catch (error) {
                const { message, debug, response } = error;
                console.error('Error details getMyCppt:', { message, debug, response })
            }
        },

        prefillDiagnosisFromHistory() {
            const latest = this.history.cppt_doctor[0];
            if (latest) {
                if (latest.icd10) {
                    this.form_diagnosis.icd10 = latest.icd10.map(item => ({
                        icd10_id: item.icd10_id,
                        value: item.icd10_id,
                        title: `${item.icd10_code} ${item.icd10_name}`,
                        action: "action"
                    }));
                }
                this.form_diagnosis.icd10_notes = latest.icd10_notes || '';
                this.form_diagnosis.non_icd10 = Array.isArray(latest.non_icd10) ? [...latest.non_icd10] : [];
            }
        },

        async getVitalList(reg_id) {
            try {
                const response = await this.$store.dispatch('vitalsign/getListVitalSign', { registration_id: reg_id, limit: 1 });
                this.vital_list = response.rows;
                if (this.vital_list && this.vital_list.length > 0) {
                    const latest = this.vital_list[0];
                    if (this.active.access == 'doctor') {
                        this.form_vital.systolic_blood_pressure = latest.systolic ?? '';
                        this.form_vital.diastolic_blood_pressure = latest.diastolic ?? '';
                        this.form_vital.pulse_frequency = latest.pulse ?? '';
                        this.form_vital.breath_frequency = latest.respiratory_rate ?? '';
                        this.form_vital.oximetry_frequency = latest.oxygen_saturation ?? '';
                        this.form_vital.temperature = latest.temperature ?? '';
                    }
                    this.form_vital.oxygen_type_id = latest.oxygen_type_id ?? '';
                    this.form_vital.aware = latest.aware ?? '';
                    this.form_vital.eye_score = latest.eye_score ?? '';
                    this.form_vital.verbal_score = latest.verbal_score ?? '';
                    this.form_vital.motor_score = latest.motor_score ?? '';
                    this.form_vital.skin_color = latest.skin_color ?? '';
                    this.form_vital.behavior = latest.behavior ?? '';
                    this.form_vital.weight = latest.weight ?? '';
                    this.form_vital.height = latest.height ?? '';
                    this.form_vital.imt = latest.imt ?? parseFloat((this.form_vital.weight / (this.form_vital.height * this.form_vital.height / 10000)).toFixed(2));
                }
            } catch (error) {
                console.error('Error while GET vitalsign/getListVitalSign at: ', error);
            }
        },

        async saveAnamnesa(exist, destination, save_from) {
            // if (save_from != 'main_section') this.active.disabled.section = true
            this.form_anamnesa.doctor_in_charge_id ||= this.regData.doctor_in_charge_id;
            this.form_anamnesa.registration_destination_id ||= this.regData.registration_destination_id

            let fixed_data = {
                exist: exist,
                data: this.form_anamnesa
            }

            try {
                const res = await this.$store.dispatch('cppt/putSections', fixed_data)
                this.global_cppt_id = res?.data?.id

                if(save_from == 'main_section') {
                    this.notification('Success','CheckIcon','success','Berhasil menyimpan anamnesa')
                    localStorage.setItem(`actvcpt`, JSON.stringify(res?.data?.id))
                    this.active.section = destination
                }
                await this.getMyCppt(res?.data?.id)

            } catch (error) {
                const { message, debug, response } = error
                this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
            } finally {
                if(save_from == 'main_section') {
                    this.active.disabled.section = false
                }
            }
        },

        async saveVital(exist, destination) {
            let requiredFields = [
                !this.form_vital.pulse_frequency,
                !this.form_vital.breath_frequency,
                !this.form_vital.oximetry_frequency,
                !this.form_vital.temperature
            ]
            if (!this.isPews) { // require systolc n diastolic if not a child (not pews)
                requiredFields.unshift(
                    !this.form_vital.systolic_blood_pressure,
                    !this.form_vital.diastolic_blood_pressure
                )
            }
            if (this.active.access === 'nurse' && requiredFields.some(Boolean)) {
                this.notification('Perhatian', 'AlertTriangleIcon', 'warning', 'Tanda vital dengan bintang merah wajib diisi');
                return
            }

            this.form_vital.doctor_in_charge_id ||= this.regData.doctor_in_charge_id
            this.form_vital.registration_destination_id ||= this.regData.registration_destination_id

            let fixed_data = {
                exist: exist,
                data: this.form_vital
            }

            try {
                const res = await this.$store.dispatch('cppt/putSections', fixed_data)
                this.notification('Success','CheckIcon','success','Berhasil menyimpan tanda vital')
                localStorage.setItem(`actvcpt`, JSON.stringify(res?.data?.id))
                await this.getMyCppt(res?.data?.id)
                this.active.section = destination
            } catch (error) {
                const { message, debug, response } = error
                this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
            } finally {
                this.active.disabled.section = false
            }
        },

        async saveGeneralis(exist, destination) {
            this.form_generalis.doctor_in_charge_id ||= this.regData.doctor_in_charge_id
            this.form_generalis.registration_destination_id ||= this.regData.registration_destination_id

            let fixed_data = {
                exist: exist,
                data: this.form_generalis
            }

            try {
                const res = await this.$store.dispatch('cppt/putSections', fixed_data)
                this.notification('Success','CheckIcon','success','Berhasil menyimpan status generalis')
                localStorage.setItem(`actvcpt`, JSON.stringify(res?.data?.id))
                await this.getMyCppt(res?.data?.id)
                this.active.section = destination
            } catch (error) {
                const { message, debug, response } = error
                this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
            } finally {
                this.active.disabled.section = false
            }
        },

        async saveDiagnosis(exist, destination) {
            this.form_diagnosis.doctor_in_charge_id ||= this.regData.doctor_in_charge_id
            this.form_diagnosis.registration_destination_id ||= this.regData.registration_destination_id

            let fixed_data = {
                exist: exist,
                data: this.form_diagnosis
            }

            try {
                const res = await this.$store.dispatch('cppt/putSections', fixed_data)
                this.notification('Success','CheckIcon','success','Berhasil menyimpan diagnosa')
                localStorage.setItem(`actvcpt`, JSON.stringify(res?.data?.id))
                await this.getMyCppt(res?.data?.id)
                this.active.section = destination
            } catch (error) {
                const { message, debug, response } = error
                this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
            } finally {
                this.active.disabled.section = false
            }
        },

        async saveProcedure(exist, destination) {
            this.form_procedure.doctor_in_charge_id ||= this.regData.doctor_in_charge_id
            this.form_procedure.registration_destination_id ||= this.regData.registration_destination_id

            let fixed_data = {
                exist: exist,
                data: this.form_procedure
            }

            try {
                const res = await this.$store.dispatch('cppt/putSections', fixed_data)
                this.notification('Success','CheckIcon','success','Berhasil menyimpan prosedur')
                localStorage.setItem(`actvcpt`, JSON.stringify(res?.data?.id))
                await this.getMyCppt(res?.data?.id)
                this.active.section = destination
            } catch (error) {
                const { message, debug, response } = error
                this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
            } finally {
                this.active.disabled.section = false
            }
        },

        async savePlan(exist, destination) {
            this.form_plan.doctor_in_charge_id ||= this.regData.doctor_in_charge_id
            this.form_plan.registration_destination_id ||= this.regData.registration_destination_id

            let fixed_data = {
                exist: exist,
                data: this.form_plan
            }

            try {
                const res = await this.$store.dispatch('cppt/putSections', fixed_data)
                this.notification('Success','CheckIcon','success','Berhasil menyimpan plan')
                localStorage.setItem(`actvcpt`, JSON.stringify(res?.data?.id))
                await this.getMyCppt(res?.data?.id)
                this.active.section = destination
            } catch (error) {
                const { message, debug, response } = error
                this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
            } finally {
                this.active.disabled.section = false
            }
        },

        async saveLaboratory(exist, destination) {
            this.form_laboratory.doctor_in_charge_id ||= this.regData.doctor_in_charge_id
            this.form_laboratory.registration_destination_id ||= this.regData.registration_destination_id

            let fixed_data = {
                exist: exist,
                data: this.form_laboratory
            }

            try {
                const res = await this.$store.dispatch('cppt/putSections', fixed_data)
                this.notification('Success','CheckIcon','success','Berhasil menyimpan laboratorium')
                localStorage.setItem(`actvcpt`, JSON.stringify(res?.data?.id))
                await this.getMyCppt(res?.data?.id)
                this.active.section = destination
            } catch (error) {
                const { message, debug, response } = error
                this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
            } finally {
                this.active.disabled.section = false
            }
        },

        async saveRadiology(exist, destination) {
            this.form_radiology.doctor_in_charge_id ||= this.regData.doctor_in_charge_id
            this.form_radiology.registration_destination_id ||= this.regData.registration_destination_id

            let fixed_data = {
                exist: exist,
                data: this.form_radiology
            }

            try {
                const res = await this.$store.dispatch('cppt/putSections', fixed_data)
                this.notification('Success','CheckIcon','success','Berhasil menyimpan radiologi')
                localStorage.setItem(`actvcpt`, JSON.stringify(res?.data?.id))
                await this.getMyCppt(res?.data?.id)
                this.active.section = destination
            } catch (error) {
                const { message, debug, response } = error
                this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
            } finally {
                this.active.disabled.section = false
            }
        },

        async savePhysiotherapy(exist, destination) {
            this.form_physiotherapy.doctor_in_charge_id ||= this.regData.doctor_in_charge_id;
            this.form_physiotherapy.registration_destination_id ||= this.regData.registration_destination_id

            let fixed_data = {
                exist: exist,
                data: this.form_physiotherapy
            }

            try {
                const res = await this.$store.dispatch('cppt/putSections', fixed_data)
                this.notification('Success','CheckIcon','success','Berhasil menyimpan fisioterapi')
                localStorage.setItem(`actvcpt`, JSON.stringify(res?.data?.id))
                await this.getMyCppt(res?.data?.id)
                this.active.section = destination
            } catch (error) {
                const { message, debug, response } = error
                this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
            } finally {
                this.active.disabled.section = false
            }
        },

        async savePresc(exist, destination) {
            this.home_recipe.doctor_in_charge_id ||= this.regData.doctor_in_charge_id;
            this.home_recipe.registration_destination_id ||= this.regData.registration_destination_id
            this.home_recipe.pharmacy_depo_id ||= this.central_depo
            this.home_recipe.type_prescription_id ||= 2

            let fixed_data = {
                exist: exist,
                data: this.home_recipe
            }

            let inp_presc = this.home_recipe?.form_prescription
            let inp_parent = this.home_recipe?.form_prescription_parent
            let item_null_val = []
            let parent_null_val = []

            if (inp_parent?.length > 0 && inp_presc?.length > 0) {
                for (let idx_i = 0; idx_i < inp_presc.length; idx_i++) {
                    if (inp_presc[idx_i].value_package == "" || inp_presc[idx_i].value_package == 0) {
                        item_null_val.push(inp_presc[idx_i].name)
                    }
                }

                for (let idx_p = 0; idx_p < inp_parent.length; idx_p++) {
                    if ((inp_parent[idx_p].value == "" || inp_parent[idx_p].value == 0) && inp_parent[idx_p].id != 1) {
                        parent_null_val.push(inp_parent[idx_p].prescription)
                    }
                }

                if(parent_null_val.length == 0 && item_null_val.length == 0) {
                    try {
                        this.active.disabled.section = true
                        await this.$store.dispatch('cppt/putSections', fixed_data)
                        .then(
                            async res => {
                                this.notification('Success','CheckIcon','success','Berhasil menyimpan resep')
                                localStorage.setItem(`actvcpt`, JSON.stringify(res?.data?.id))
                                await this.getMyCppt(res?.data?.id)
                                this.active.section = destination

                                if (this.home_recipe.type_prescription_id == 2) {
                                    this.$bvModal
                                        .msgBoxConfirm(`Apakah anda ingin sekaligus membuat resume untuk ${this.regData.alias_name}.${this.regData.name}?`, {
                                        title: 'Konfirmasi membuat resume', size: 'sm', okVariant: 'primary-500', okTitle: 'Ya, Lanjutkan', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
                                    })
                                    .then(value => {
                                        if (value === true){
                                            this.triggerToResume()
                                        }
                                    })
                                }
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at savePresc: ', error)
                    } finally {
                        this.active.disabled.section = false
                    }
                } else if(parent_null_val.length > 0 && item_null_val.length > 0) {
                    alert(`Jumlah sediaan ${parent_null_val} & katalog ${item_null_val} minimal diisi 1`)
                } else if(parent_null_val.length > 0) {
                    alert(`Jumlah sediaan ${parent_null_val} minimal diisi 1`)
                } else if(item_null_val.length > 0) {
                    alert(`Jumlah obat pada katalog ${item_null_val} minimal diisi 1`)
                }
            }
        },

        async saveNutrition(exist, destination) {
            this.form_nutrition.doctor_in_charge_id ||= this.regData.doctor_in_charge_id;
            this.form_nutrition.registration_destination_id ||= this.regData.registration_destination_id;

            let fixed_data = {
                exist: exist,
                data: this.form_nutrition
            }

            try {
                const res = await this.$store.dispatch('cppt/putSections', fixed_data)
                this.notification('Success','CheckIcon','success','Berhasil menyimpan asesmen gizi');
                localStorage.setItem(`actvcpt`, JSON.stringify(res?.data?.id))
                this.getMyCppt(res?.data?.id)
                this.active.section = destination
            } catch (error) {
                const { message, debug, response } = error;
                this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
            } finally {
                this.active.disabled.section = false
            }
        },

        async saveKardex(exist, destination) {
            try {
                let inp_items = this.input_prescription?.form_prescription
                let inp_parent = this.input_prescription?.form_prescription_parent

                if (inp_parent?.length > 0 && inp_items?.length > 0) {
                    if(this.emptyChecking({
                        parents: inp_parent,
                        items: inp_items
                    })) {
                        const message = "SILAKAN LENGKAPI:\n\n" + this.combine_empty_found.map(item => `- ${item}`).join('\n');
                        alert(message);
                        this.active.disabled.section = false
                    } else {
                        for (let i = 0; i < inp_parent.length; i++) {
                            const el = inp_parent[i];
                            if (el.is_exist == 0 && el.id != 1 && el.status == 1) {
                                this.input_prescription.form_prescription_parent[i].status = 2
                            }
                        }

                        let changes = this.kardexSpesificChanges()
                        if (changes) {
                            this.kardexChanges = changes
                            this.$refs.confirmKardexModal.show()
                        } else {
                            this.proceedToKardex(destination)
                        }
                    }
                }
            } catch (error) {
                console.error('Error catch at saveKardex: ', error)
            }
        },

        emptyChecking(obj) {
            const naming = {
                package: "Kemasan/satuan",
                // package_id: "Kemasan/satuan",
                value: "Jumlah",
                dose: "Dosis",
                dose_unit_id: "Satuan Dosis",
                signatura_times_id: "Waktu",
                administer_id: "Administer",
                status: "Status"
            };

            const parent_properties = ['status', 'value', 'package', 'signatura_times_id', 'administer_id']
            const item_unit_properties = ['status', 'dose', 'dose_unit_id', 'signatura_times_id', 'administer_id']
            const item_conc_properties = ['dose', 'dose_unit_id']

            let parent_criteria = []
            let parent_part = []
            let item_part = []

            let null_parents = []
            let null_items = []

            let data_items = obj.items || []
            let data_parents = []
            parent_criteria = [...new Set(data_items.map(item => item.prescription_type_id))] // buat dapetin parent mana aja yang punya items
            let compounded_medicine = obj.parents.filter(item => item.id != 1) // ambil yang racikan aja, krn parent obat satuan ga ada inputan dosis dll

            // tambahin `criteria` buat nentuin selanjutnya perlu di cek atau gak
            for (let idx_c = 0; idx_c < compounded_medicine.length; idx_c++) {
                const el = compounded_medicine[idx_c];
                data_parents.push({
                    ...el,
                    criteria: parent_criteria.includes(el.id)
                })
            }

            if (data_parents) {
                for (let idx_p = 0; idx_p < data_parents.length; idx_p++) {
                    parent_part = []
                    const el = data_parents[idx_p]

                    if(el.criteria === true) {
                        for (let i = 0; i < parent_properties.length; i++) {
                            const pp = parent_properties[i];
                            const value = el[pp];
                            const isInvalid = pp === 'value'
                                ? !value || value <= 0 // null, undefined, '', atau <= 0
                                : !value || value == 0 // null, undefined, '', atau == 0

                            if (isInvalid) {
                                parent_part.push(naming[pp]);
                                // console.log('empty property found: ', pp, value);
                            }
                        }

                        // console.log('All empty property found: ', parent_part);
                        if (parent_part.length > 0) {
                            let narrative_parent = `${this.getPrescParentName(el.id)}: ${parent_part.join(', ')}`
                            null_parents.push(narrative_parent)
                        }
                    }
                }
                // console.log('null_parents: ', null_parents);
            }

            if (data_items) {
                for (let idx_i = 0; idx_i < data_items.length; idx_i++) {
                    item_part = []
                    const el = data_items[idx_i]
                    let properties = el.prescription_type_id == 1 ? item_unit_properties : item_conc_properties

                    for (let i = 0; i < properties.length; i++) {
                        const ip = properties[i];
                        const value = el[ip];
                        const isInvalid = ip === 'dose'
                            ? !value || value <= 0 // null, undefined, '', atau <= 0
                            : !value || value == 0 // null, undefined, '', atau == 0

                        if (isInvalid) {
                            item_part.push(naming[ip]);
                            // console.log('empty property found: ', ip, value);
                        }
                    }

                    // console.log('All empty property found: ', parent_part);
                    if (item_part.length > 0) {
                        let narrative_item = `${el.name}: ${item_part.join(', ')}`
                        null_items.push(narrative_item)
                    }
                }
                // console.log('null_items: ', null_items);
            }

            this.combine_empty_found = [...null_parents, ...null_items]
            return this.combine_empty_found.length > 0 ? true : false
        },

        kardexSpesificChanges() {
            const formData = { ...this.input_prescription }
            const originalData = { ...this.original_prescription }

            // ngehapus properti yang gak perlu dibandingin
            const excludedProps = ['id', 'doctor_in_charge_id', 'registration_destination_id', 'health_facility_item_id', 'soap_id', 'cppt_id', 'sbar_id']
            excludedProps.forEach(prop => {
                delete formData[prop]
                delete originalData[prop]
            });

            // Fungsi buat dapetin object lengkap yang berubah
            const getChangedObjects = (before, after) => {
                // Handle array
                if (Array.isArray(before) && Array.isArray(after)) {
                    const changedItems = [];

                    // Cek item yang diubah atau ditambahin
                    after.forEach((afterItem, index) => {
                        if (index < before.length) {
                            // Item udah ada di before, cek apakah berubah
                            const isChanged = isDifferent(before[index], afterItem);
                            if (isChanged) {
                                changedItems.push(afterItem); // Return object lengkap dari after (formData)
                            }
                        } else {
                            // Item baru yang ditambahkan di after
                            changedItems.push(afterItem);
                        }
                    });

                    // Cek item yang dihapus dari before
                    // tp ini harusnya ga akan terjadi karena yg is_exist == 1 itu ga bisa dihapus
                    // jadi kl ada Null nih belom ke handle dari FE, next dah
                    if (before.length > after.length) {
                        for (let i = after.length; i < before.length; i++) {
                            changedItems.push(null); // sementara, kl ada item yang dihapus, push Null dulu
                        }
                    }

                    return changedItems.length > 0 ? changedItems : null;
                }

                // Handle object - return object lengkap dari after kalo ada perubahan
                if (typeof before === 'object' && before !== null && after !== null) {
                    const isChanged = isDifferent(before, after);
                    return isChanged ? after : null;
                }

                // Handle primitive values - return value dari after kalo berbeda
                return String(before ?? '') !== String(after ?? '') ? after : null;
            };

            // Fungsi deep comparison
            const isDifferent = (a, b) => {
                if (typeof a !== typeof b) return true;

                if (Array.isArray(a) && Array.isArray(b)) {
                    if (a.length !== b.length) return true;
                    return a.some((item, index) => isDifferent(item, b[index]))
                }

                if (typeof a === 'object' && a !== null && b !== null) {
                    const keysA = Object.keys(a)
                    const keysB = Object.keys(b)
                    if (keysA.length !== keysB.length) return true
                    return keysA.some(key => isDifferent(a[key], b[key]))
                }

                return String(a ?? '') !== String(b ?? '')
            };

            // Cari perubahan
            const changes = {};
            Object.keys(formData).forEach(key => {
                if (isDifferent(originalData[key], formData[key])) {
                    const changedObjects = getChangedObjects(originalData[key], formData[key]);

                    changes[key] = changedObjects
                    // changes[key] = {
                    //     before: originalData[key],
                    //     after: formData[key],
                    //     changes: changedObjects // Object lengkap dari formData yang berubah
                    // };
                }
            });

            // console.log("Perubahan kardeks berubah:", changes);
            return Object.keys(changes).length > 0 ? changes : false;
        },

        async proceedToKardex(destination) {
            this.active.disabled.changes_presc = true
            if (!this.global_cppt_id) {
                await this.saveAnamnesa('ANAMNESA', 'PRESC', 'kardex')
            }

            this.input_prescription.doctor_in_charge_id ||= this.regData.doctor_in_charge_id;
            this.input_prescription.registration_destination_id ||= this.regData.registration_destination_id
            this.input_prescription.health_facility_item_id = this.regData.health_facility_item_id
            this.input_prescription.soap_id = null
            this.input_prescription.cppt_id = this.global_cppt_id
            this.input_prescription.sbar_id = null

            if (this.consult_type3 || this.consult_intensive) {
                this.$emit("refreshConsult", 'initial-message');
                this.needToTrigerNotif = await this.triggerKardexNotif(this.input_prescription.id)
            }

            try {
                const res = await this.$store.dispatch('kardex/putKardex', this.input_prescription);
                if (this.needToTrigerNotif == false) {
                    this.notif_socket.emit('SEND_SBAR', {
                        type: 'CONSULTATION_KARDEX',
                        doctor_in_charge_id: this.regData.doctor_in_charge_id,
                        position_id: this.general_dr_position,
                    })
                }

                if (this.consult_type3 || this.consult_intensive) {
                    this.notification('Success','CheckIcon','success','Resep kardeks berhasil di simpan')
                    await this.getKardexList()
                    this.$refs.confirmKardexModal.hide()
                    await this.getFullKardex()
                    this.getAssessment()
                    // this.getNonPharma()
                    this.getKardexStock(this.regData.registration_destination_id)
                    this.getKardexDo(this.regData.registration_destination_id)
                    this.active.section = destination
                } else {
                    await this.orderKardex(destination, res.data.id)
                }
            } catch (error) {
                const { message, debug, response } = error;
                this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
            } finally {
                this.active.disabled.section = false
                this.active.disabled.changes_presc = false
            }
        },

        async orderKardex(destination, kardex_id) {
            try {
                let form_order = {
                    kardex_id: kardex_id
                }
                const res = await this.$store.dispatch('kardex/putOrderKardex', form_order);
                this.notification('Success','CheckIcon','success','Resep kardeks berhasil di order')
                await this.getKardexList()
                this.$refs.confirmKardexModal.hide()
                await this.getFullKardex()
                this.getAssessment()
                // this.getNonPharma()
                this.getKardexStock(this.regData.registration_destination_id)
                this.getKardexDo(this.regData.registration_destination_id)
                this.active.section = destination
            } catch (error) {
                const { message, debug, response } = error;
                this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
            }
        },

        closePrescModal() {
            this.active.disabled.section = false
            this.$refs.confirmKardexModal.hide()
        },

        async parentTrigger(message) {
            this.global_cppt_id = ''
            // presc-template
            this.presc_list_templating = message.presc_template
            this.presc_filtered = message.presc_template

            // anamn-template
            this.anamn_list_templating = message.anamn_template
            this.anamn_filtered = message.anamn_template

            // prescription
            this.catalog_list = message.catalog_list
            this.PackageOptions = message.stock_list
            this.optSignTime = message.signature_time
            this.optSignDose = message.signature_dose
            this.optSignUnits = message.signature_units
            this.optAdminister = message.catalog_instruction
            // this.getNonPharma()

            this.global_gate = message.gate

            if (message.gate == 'main_gate') {
                if (message.registration_destination_id == this.reg_des_id) {
                    this.regData = message.regData
                    this.getCosting = message.costing_list
                    this.supportPackage.lab_package_list = message.lab_list
                    this.supportPackage.rad_package_list = message.rad_list
                    this.supportPackage.phy_package_list = message.phy_list
                    this.assignTempHeader()

                    this.global_from = message?.triggerFrom || await this.isConsultant(this.global_gate)
                    // console.log('this.global_from', this.global_from);

                    this.pewsAndHistory(this.regData.birth_date, this.regData.norm)
                    this.getKardexStock(this.regData.registration_destination_id)
                    this.getKardexDo(this.regData.registration_destination_id)
                } else {
                    // console.log('beda regdes')
                    await this.getData(message.registration_destination_id)
                }

                await this.determineAccess({
                    gate: message.gate,
                    cppt: null,
                });

                // await this.getVitalList(this.regData.registration_id);

                await this.openMainModal(message.gate, null)

            } else if (message.gate == 'edit_gate') {
                this.global_cppt_id = message.cppt_id
                await this.getData(message.registration_destination_id)
                // console.log('message.gate, message.cppt_id', message.gate, message.cppt_id);
                await this.openMainModal(message.gate, message.cppt_id)
            }
        },

        async isConsultant(gate) {
            const user_id = gate == 'main_gate' ? this.userData.employee_id : this.cppt_user_added
            const employee = await this.$store.dispatch('employee/getDetailById', user_id)
            let is_specialist = employee?.length > 0 ? employee[0]?.is_specialist : 0
            let doctor_ids = []

            if (is_specialist) {
                for (let index = 2; index <= 11; index++) {
                    const el = this.regData[`doctor_in_charge_id_${index}`]
                    doctor_ids.push(el)
                }
                let result = doctor_ids.some(val => val == user_id) ? 'consultation' : null
                if (result == 'consultation') {
                    const response = await this.$store.dispatch('consultation/getListConsultation', {registration_destination_id: this.regData?.registration_destination_id})
                    let consultation_rows = response?.rows || []
                    this.consult_type3 = consultation_rows.some(item => item.consultant_id == user_id && item.consultation_type_id == 3 && item.status == 0)

                    const intensive = [this.hfii_icu, this.hfii_iccu, this.hfii_icu_isolation]
                    this.consult_intensive = intensive.includes(this.regData.health_facility_item_id)
                }
                return result
            }
            return null
        },

        async getData (reg_des_id) {
			await this.$store.dispatch('registration/getById', reg_des_id).then(data => {
                this.regData = data[0]

                this.assignTempHeader()
                this.getTransactionOptions()
                this.getSupportPackage()
                this.pewsAndHistory(this.regData.birth_date, this.regData.norm)
                this.getKardexStock(this.regData.registration_destination_id)
                this.getKardexDo(this.regData.registration_destination_id)
            })
        },

        async determineAccess(obj) {
            const { gate, cppt } = obj
            const { user_group, position, employee_id } = this.userData

            const rules = {
                main_gate: {
                    doctor: (user_group.id == this.doctor_group_id && position.id != this.general_dr_position) || user_group.id == this.admin_group_id,
                    general_doctor: user_group.id == this.doctor_group_id && position.id == this.general_dr_position,
                    nutritionist: user_group.id == this.nutritionist_group_id
                },
                edit_gate: {
                    doctor: (cppt?.user_added_role == 'Doctor' && cppt?.user_added_position_id != this.general_dr_position) || cppt?.user_added_role == 'Super User',
                    general_doctor: cppt?.user_added_role == 'Doctor' && cppt?.user_added_position_id == this.general_dr_position,
                    nutritionist: cppt?.user_added_role == 'Gizi'
                }
            }

            this.active.access = Object.keys(rules[gate] || {}).find(
                key => rules[gate][key]
            ) || 'nurse'
            // console.log('this.active.access: ', this.active.access);
        },

        assignTempHeader() {
            this.temp_header.alias_name = this.regData?.alias_name
            this.temp_header.name = this.regData?.name
            this.temp_header.norm = this.regData?.norm
            this.temp_header.birth_date = this.regData?.birth_date
            this.temp_header.weight = this.regData?.ttv?.weight
            this.temp_header.height = this.regData?.ttv?.height
        },

        getTransactionOptions() {
            const company_bpjs = '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8'
            const company_bpjstk = 'bb0d633f-99e1-4990-901b-0417a14e3cb6'
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

            param.company_insurance_id = active_company == company_bpjs || active_company == company_bpjstk ? active_company : company_umum
            param.service_class_id = this.regData.health_facility_id == this.hfi_inpatient ? active_service_class : ''
            param.health_facility_item_id = this.regData.health_facility_id == this.hfi_inpatient ? inpatient_item_id : this.regData.health_facility_item_id

            this.$store.dispatch('costing/getParam', param)
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
                    // console.log('this.supportPackage.lab_package_list', this.supportPackage.lab_package_list);
                })

                this.$store.dispatch('radiology/getRadiologyPackageV2', param).then((response) => {
                    this.supportPackage.rad_package_list = response
                    // console.log('this.supportPackage.rad_package_list', this.supportPackage.rad_package_list);
                })

                this.$store.dispatch('physiotherapy/getPackageAll', param)
            } catch (error) {
                console.error('Error while GET data support_package at: ', error)
            }
        },

        pewsAndHistory(birth_date, norm) {
            // pews
            this.isPews = this.pewsCriteria(birth_date)

            // history
            this.history.cppt = []
            this.cppt_load.limit_display = 3
            this.getCpptHistory(norm)

            this.history.cppt_doctor = []
            this.getDiagnosisCPPTHistory(norm)

            this.history.nurse = []
            this.nurse_load.limit_display = 3
            this.getNurseHistory(norm)

            this.history.soap = []
            this.soap_load.limit_display = 3
            this.getSoapHistory(norm)

            this.history.laboratory = []
            this.lab_load.limit_display = 3
            this.getLaboratoryHistory(norm)

            this.history.radiology = []
            this.rad_load.limit_display = 3
            this.getRadiologyHistory(norm)

            this.history.physiotherapy = []
            this.phy_load.limit_display = 3
            this.getPhysiotherapyHistory(norm)

            this.history.resume = []
            this.rsm_load.limit_display = 3
            this.getResumeList(norm)

            this.history.examination = []
            this.exa_load.limit_display = 3
            this.getExaminationList(norm)
        },

        filterItemPresc() {
            if (!this.presc_search) return this.presc_filtered = [...this.presc_list_templating]

            const query = this.presc_search.toLowerCase()
            this.presc_filtered = this.presc_list_templating.filter(item => item.name.toLowerCase().includes(query))
        },

        filterItemAnamn() {
            if (!this.anamn_search) return this.anamn_filtered = [...this.anamn_list_templating]

            const query = this.anamn_search.toLowerCase()
            this.anamn_filtered = this.anamn_list_templating.filter(item => item.name.toLowerCase().includes(query))
        },

        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        imtChanged($event){
            let brt_badan = 0
            if (this.form_vital.weight) {
                brt_badan = parseFloat(this.form_vital.weight);
            } else {
                brt_badan = 0
            }

            let tng_badan = 0
            if (this.form_vital.height) {
                tng_badan = parseFloat(this.form_vital.height);
            } else {
                tng_badan = 0
            }

            let imt = 0.0;

            if (brt_badan && tng_badan) {
                imt = tng_badan * tng_badan;
                imt = (brt_badan/imt)*100*100;
                this.form_vital.imt = imt.toFixed(1);
            }
        },

        async openMainModal(gate, cppt_id) {
            if (gate == 'main_gate') {
                await this.resetCpptForm()
                await this.getVitalList(this.regData.registration_id)
                this.prefillDiagnosisFromHistory();
                await this.cloneAllState()
            } else if (gate == 'edit_gate') {
                await this.getMyCppt(cppt_id)
            }

            await this.$nextTick(() => {
                if (this.active.access === 'nutritionist') {
                    this.active.section = 'NUTRITION';
                } else {
                    this.active.section = 'ANAMNESA';
                }
            })

            await this.getKardexList()
            await this.getFullKardex()
            this.active.modal.main = true
        },

        async getKardexList() {
            const param = {
                registration_destination_id: this.reg_des_id,
                is_active: 1
            }
            try {
                const response = await this.$store.dispatch('kardex/getKardexList', param);
                await this.resetKardex()
                let active_kardex = response.rows || []
                await this.fillKardex(active_kardex[0])
            } catch (error) {
                console.error('Error GET kardex/getKardexList at: ', error);
            }
        },

        async triggerKardexNotif(kardex_id) {
            const notif_param = {
                // employee_id: 'e483d663-9707-4cb4-b980-334405197131',
                employee_id: this.cppt_user_added,
                status: 'delivered'
            }

            let delivered_notif = await this.$store.dispatch('notification/getNotificationAll', notif_param)
            let kardex_notif = delivered_notif.some(item => item.type == 'kardex' && item.data?.kardex_id == kardex_id)
            // console.log('kardex_notif', kardex_notif);
            return kardex_notif
        },

        async getFullKardex() {
            try {
                await this.$store.dispatch('kardex/getKardexList', { registration_destination_id: this.reg_des_id })
                .then(
                    async response => {
                        this.full_kardex = response.rows || []
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('Error GET kardex/getKardexList at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET kardex/getKardexList at: ', error);
            }
        },

        async fillKardex(obj) {
            if (obj) {
                this.input_prescription.id = obj.id
                this.input_prescription.doctor_in_charge_id = obj.doctor_in_charge_id
                this.input_prescription.registration_destination_id = obj.registration_destination_id

                let var_presc = obj.kardex_item || []
                let var_parent = obj.kardex_parent || []
                let var_non = obj.kardex_non_pharmacological || []

                if (var_non.length > 0) {
                    const mappedNon = var_non.map(item => {
                        return {
                            id: item.non_pharmacological_id,
                            name: item.name,
                            doctor_added: item.doctor_added ?? '',
                            doctor_added_name: item.doctor_added_name ?? '-',
                        };
                    });
                    this.input_prescription.non_pharmacological = mappedNon;
                } else {
                    this.input_prescription.non_pharmacological = []
                }

                if (var_parent.length > 0) {
                    const mappedPrescParent = var_parent.map(item => {
                        if (item.id != 1) {
                            return {
                                id: item.id,
                                status: item?.status || 3,
                                package: Number(item.package),
                                value: Number(item.value ?? 0),
                                signatura_times_id: item.signatura_times_id ?? '',
                                administer_id: item.administer_id ?? '',
                                desc: item.administer_id == this.other_administer ? item?.desc : '',
                                is_exist: 1,
                            };
                        } else {
                            return {
                                id: item.id,
                                is_exist: 1,
                            };
                        }
                    });
                    mappedPrescParent.sort((a, b) => a.id - b.id);
                    this.input_prescription.form_prescription_parent = mappedPrescParent;
                } else {
                    this.input_prescription.form_prescription_parent = [{
                        id: 1,
                    }]
                }

                if (var_presc) {
                    const mappedPresc = var_presc.map(item => {
                        let temp_sign = []
                        if (item?.dose_unit_options.length > 0) {
                            const mappedSign = item.dose_unit_options.map(item => {
                                return {
                                    value: item.id,
                                    title: item.name,
                                };
                            });
                            temp_sign = mappedSign
                        }

                        return {
                            pharmacy_catalog_id: item.pharmacy_catalog_id,
                            package_id: item.package_id,
                            prescription_type_id: item.prescription_item_type_id,
                            dose: item.dose,
                            dose_unit_id: item.dose_unit_id,
                            signatura_times_id: item.signatura_times_id,
                            administer_id: item.administer_id,
                            desc: item.administer_id == this.other_administer ? item?.desc : '',
                            sort: item.sort,
                            status: item.status,

                            is_exist: 1,
                            name: item.pharmacy_catalog_name,
                            package_name: item.package_name,
                            catalog_date: item.catalog_date,
                            stop_date: item.stop_date,
                            is_change: item.is_change,
                            exist_status: item.status,
                            doctor_added: item.doctor_added,
                            doctor_added_name: item.doctor_added_name,
                            doctor_nickname: item?.doctor_nickname || '-',
                            id: item.id,
                            dose_unit_options: temp_sign,
                        };
                    });
                    // mappedPresc.sort((a, b) => a.sort - b.sort);
                    this.input_prescription.form_prescription = mappedPresc;
                } else {
                    this.input_prescription.form_prescription = [{
                        id: 1,
                    }]
                }
                await this.cloneAllState()
            }
        },

        async resetKardex() {
            this.input_prescription.id = ''
            this.input_prescription.doctor_in_charge_id = ''
            this.input_prescription.registration_destination_id = ''
            this.input_prescription.health_facility_item_id = ''
            this.input_prescription.form_prescription_parent = [{
                id: 1,
            }]
            this.input_prescription.form_prescription = []
            this.input_prescription.non_pharmacological = []
            this.input_prescription.soap_id = ''
            this.input_prescription.cppt_id = ''
            this.input_prescription.sbar_id = ''
        },

        async getKardexStock(reg_des_id) {
            const param = {
                registration_destination_id: reg_des_id,
            }
            try {
                await this.$store.dispatch('kardex/getKardexStock', param)
                .then(
                    async response => {
                        this.kardex_stock = response?.sort((a, b) => a.catalog_name - b.catalog_name)
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('Error GET kardex/getKardexStock at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET kardex/getKardexStock at: ', error);
            }
        },

        async getKardexDo(reg_des_id) {
            const param = {
                registration_destination_id: reg_des_id,
                list_type: 'kardex'
            }
            try {
                await this.$store.dispatch('prescription/getPrescriptionListV2', param)
                .then(
                    async response => {
                        this.kardex_do = response?.rows || []
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('Error GET prescription/getPrescriptionListV2 at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET prescription/getPrescriptionListV2 at: ', error);
            }
        },

        async fillCpptForm(obj) {
            this.global_gate = 'edit_gate'
            await this.determineAccess({
                gate: this.global_gate,
                cppt: obj,
            })
            await this.resetCpptForm()
            await this.getVitalList(this.regData.registration_id)

            if (obj) {
                // console.log('fillCppt', obj)
                this.$nextTick(async () => {
                    this.global_cppt_id = obj?.id || ''
                    this.cppt_user_added = obj?.user_added_employee_id
                    // this.last_updated = obj?.date_updated || '2026-02-11 13:51:37'
                    const dateSource = obj?.date_updated || obj?.date_added
                    this.last_updated = dateSource
                        ? moment(dateSource).format('YYYY-MM-DD')
                        : ''

                    // =============================================================================================
                    this.form_anamnesa.id = obj?.id || ''
                    this.form_anamnesa.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_anamnesa.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id
                    this.form_anamnesa.subjective = obj?.subjective || ''
                    this.form_anamnesa.nursing_objective = obj?.nursing_objective || ''


                    // =============================================================================================
                    this.form_vital.id = obj?.id || ''
                    this.form_vital.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_vital.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id
                    if (!this.form_vital.systolic_blood_pressure) this.form_vital.systolic_blood_pressure = obj?.systolic_blood_pressure;
                    if (!this.form_vital.diastolic_blood_pressure) this.form_vital.diastolic_blood_pressure = obj?.diastolic_blood_pressure;
                    if (!this.form_vital.pulse_frequency) this.form_vital.pulse_frequency = obj?.pulse_frequency;
                    if (!this.form_vital.breath_frequency) this.form_vital.breath_frequency = obj?.breath_frequency;
                    if (!this.form_vital.oximetry_frequency) this.form_vital.oximetry_frequency = obj?.oximetry_frequency;
                    if (!this.form_vital.temperature) this.form_vital.temperature = obj?.temperature;
                    if (!this.form_vital.oxygen_type_id) this.form_vital.oxygen_type_id = obj?.oxygen_type_id || '';
                    if (!this.form_vital.aware) this.form_vital.aware = obj?.aware;
                    if (!this.form_vital.eye_score) this.form_vital.eye_score = obj?.eye_score || '';
                    if (!this.form_vital.verbal_score) this.form_vital.verbal_score = obj?.verbal_score || '';
                    if (!this.form_vital.motor_score) this.form_vital.motor_score = obj?.motor_score || '';
                    if (this.isPews) {
                        if (!this.form_vital.skin_color) this.form_vital.skin_color = obj?.skin_color || '';
                        if (!this.form_vital.behavior) this.form_vital.behavior = obj?.behavior || '';
                    }
                    if (!this.form_vital.weight) this.form_vital.weight = obj?.weight;
                    if (!this.form_vital.height) this.form_vital.height = obj?.height;
                    if (!this.form_vital.imt) this.form_vital.imt = obj?.imt;
                    this.form_vital.objective = obj?.objective || ''

                    // =============================================================================================
                    this.form_generalis.id = obj?.id || ''
                    this.form_generalis.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_generalis.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id
                    this.form_generalis.head = obj?.head || 1
                    this.form_generalis.head_hair_color = obj?.head_hair_color || 1
                    this.form_generalis.head_hair_condition = obj?.head_hair_condition || 1
                    this.form_generalis.eye_conjunctive = obj?.eye_conjunctive || 1
                    this.form_generalis.eye_sclera = obj?.eye_sclera || 1
                    this.form_generalis.eye_pupil = obj?.eye_pupil || 1
                    this.form_generalis.eye_light_reflex = obj?.eye_light_reflex || 1
                    this.form_generalis.mouth_mucosa = obj?.mouth_mucosa || 1
                    this.form_generalis.mouth_discoloration = obj?.mouth_discoloration || 1
                    this.form_generalis.mouth_tongue = obj?.mouth_tongue || 1
                    this.form_generalis.mouth_caries = obj?.mouth_caries || 1
                    this.form_generalis.neck_kgb = obj?.neck_kgb || 1
                    this.form_generalis.neck_trachea_position = obj?.neck_trachea_position || 1
                    this.form_generalis.neck_massa = obj?.neck_massa || 1
                    this.form_generalis.neck_jvp = obj?.neck_jvp || 1
                    this.form_generalis.thorax_insp_chest = obj?.thorax_insp_chest || 1
                    this.form_generalis.thorax_insp_retraction = obj?.thorax_insp_retraction || 1
                    this.form_generalis.thorax_palp_chest = obj?.thorax_palp_chest || 1
                    this.form_generalis.thorax_palp_fremitus = obj?.thorax_palp_fremitus || 1
                    this.form_generalis.thorax_palp_crepitation = obj?.thorax_palp_crepitation || 1
                    this.form_generalis.thorax_ausk_pul_vasiculer = obj?.thorax_ausk_pul_vasiculer || 1
                    this.form_generalis.thorax_ausk_pul_rhonki = obj?.thorax_ausk_pul_rhonki || 1
                    this.form_generalis.thorax_ausk_pul_wheezing = obj?.thorax_ausk_pul_wheezing || 1
                    this.form_generalis.thorax_ausk_cor_heart_sound = obj?.thorax_ausk_cor_heart_sound || 1
                    this.form_generalis.thorax_ausk_cor_murmur = obj?.thorax_ausk_cor_murmur || 1
                    this.form_generalis.thorax_ausk_cor_gallop = obj?.thorax_ausk_cor_gallop || 1
                    this.form_generalis.abdomen_supel = obj?.abdomen_supel || 1
                    this.form_generalis.abdomen_bowel_sounds = obj?.abdomen_bowel_sounds || 1
                    this.form_generalis.abdomen_tenderness = obj?.abdomen_tenderness || 1
                    this.form_generalis.abdomen_hepar_lien = obj?.abdomen_hepar_lien || 1
                    this.form_generalis.abdomen_other_physical_check = obj?.abdomen_other_physical_check || ''
                    this.form_generalis.extremity_acral = obj?.extremity_acral || 1
                    this.form_generalis.extremity_crt = obj?.extremity_crt || 1
                    this.form_generalis.extremity_edema = obj?.extremity_edema || 1
                    this.form_generalis.genitalia = obj?.genitalia || ''
                    this.form_generalis.localist_status = obj?.localist_status || ''
                    this.form_generalis.neurologist_status = obj?.neurologist_status || ''
                    this.form_generalis.ophthal_status = obj?.ophthal_status || ''

                    // =============================================================================================
                    this.form_diagnosis.id = obj?.id || ''
                    this.form_diagnosis.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_diagnosis.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id
                    if (obj.icd10) {
                        const mappedIcd10 = obj.icd10.map(item => {
                            return {
                                icd10_id: item.icd10_id,
                                value: item.icd10_id,
                                title: `${item.icd10_code} ${item.icd10_name}`,
                                action: "action"
                            };
                        });
                        this.form_diagnosis.icd10 = mappedIcd10;
                    } else {
                        this.form_diagnosis.icd10 = []
                    }
                    this.form_diagnosis.icd10_notes = obj?.icd10_notes || ''
                    this.form_diagnosis.non_icd10 = obj?.non_icd10 || []
                    this.form_diagnosis.nursing_diagnose = obj?.nursing_diagnose || ''

                    // =============================================================================================
                    this.form_procedure.id = obj?.id || ''
                    this.form_procedure.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_procedure.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id
                    this.form_procedure.transaction_doctor = obj.transaction_doctor ? obj.transaction_doctor : []
                    this.form_procedure.therapy_plan = obj.therapy_plan ? obj.therapy_plan : ''
                    if (obj.icd9cm) {
                        const mappedIcd9cm = obj.icd9cm.map(item => {
                            return {
                                icd9cm_id: item.icd9cm_id,
                                value: item.icd9cm_id,
                                title: `${item.icd9cm_code} ${item.icd9cm_name}`,
                                action: "action"
                            };
                        });
                        this.form_procedure.icd9cm = mappedIcd9cm;
                    } else {
                        this.form_procedure.icd9cm = []
                    }

                    // =============================================================================================
                    this.form_plan.id = obj?.id || ''
                    this.form_plan.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_plan.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id
                    this.form_plan.nursing_plan = obj?.nursing_plan || ''
                    this.form_plan.plan = obj?.plan || ''
                    this.form_plan.pharmacological_plan = obj?.pharmacological_plan || ''

                    // =============================================================================================
                    this.form_laboratory.id = obj?.id || ''
                    this.form_laboratory.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_laboratory.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id

                    if (obj?.laboratory_request?.length > 0) {
                        let first_lab = obj.laboratory_request[0]
                        this.lab_status = first_lab?.status || 1

                        if (first_lab?.laboratory_request_items) {
                            const mappedLab = first_lab.laboratory_request_items.map(item => {
                                return {
                                    costing_name: item.laboratory_package_name,
                                    laboratory_package_id: item.laboratory_package_id,
                                    service_class_id: item.service_class_id
                                };
                            });
                            this.form_laboratory.laboratory_request_items = mappedLab;
                        } else {
                            this.form_laboratory.laboratory_request_items = []
                        }

                        if (obj.laboratory_request.clinical_indications) {
                            this.form_laboratory.clinical_indications = obj.laboratory_request.clinical_indications;
                        } else {
                            this.form_laboratory.clinical_indications = obj?.clinical_indications || '';
                        }
                    } else {
                        this.lab_status = 1
                    }

                    // =============================================================================================
                    this.form_radiology.id = obj?.id || ''
                    this.form_radiology.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_radiology.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id

                    if (obj?.radiology_request?.length > 0) {
                        let first_rad = obj.radiology_request[0]
                        this.rad_status = first_rad?.status || 1

                        if (first_rad?.radiology_request_items) {
                            const mappedRad = first_rad.radiology_request_items.map(item => {
                                return {
                                    costing_name: item.radiology_package_name,
                                    radiology_package_id: item.radiology_package_id,
                                    service_class_id: item.service_class_id
                                };
                            });
                            this.form_radiology.radiology_request_items = mappedRad;
                        } else {
                            this.form_radiology.radiology_request_items = []
                        }

                        if (obj.radiology_request.clinical_indications) {
                            this.form_radiology.clinical_indications = obj.radiology_request.clinical_indications;
                        } else {
                            this.form_radiology.clinical_indications = obj?.clinical_indications || '';
                        }
                    } else {
                        this.rad_status = 1
                    }

                    // =============================================================================================
                    this.form_physiotherapy.id = obj?.id || ''
                    this.form_physiotherapy.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_physiotherapy.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id

                    if (obj?.physiotherapy_request?.length > 0) {
                        let first_phy = obj.physiotherapy_request[0]
                        const mappedPhy = first_phy.physiotherapy_request_items.map(item => {
                            return {
                                costing_name: item.physiotherapy_package_name,
                                physiotherapy_package_id: item.physiotherapy_package_id,
                                service_class_id: item.service_class_id
                            };
                        });
                        this.form_physiotherapy.physiotherapy_request_items = mappedPhy;
                    } else {
                        this.form_physiotherapy.physiotherapy_request_items = []
                    }

                     // =============================================================================================
                    this.home_recipe.id = obj?.id || ''
                    this.home_recipe.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id
                    this.home_recipe.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id

                    if (obj?.prescription?.length > 0) {
                        const fix_presc = obj.prescription.find(item => item?.type_prescription_id == 2) || {}
                        let var_presc = fix_presc?.form_prescription || []
                        let var_parent = fix_presc?.form_prescription_parent || []

                        this.presc_status = fix_presc.flag || 0
                        this.home_recipe.pharmacy_depo_id = fix_presc.pharmacy_depo_id || this.central_depo
                        this.home_recipe.type_prescription_id = fix_presc?.type_prescription_id || 2

                        if (var_parent) {
                            const mappedPrescParent = var_parent.map(item => {
                                let name = item.id ? this.getPrescParentName(item.id) : ''
                                return {
                                    id: item.id,
                                    used: item.used,
                                    value: item.value,
                                    prescription: name,
                                    package: item.package,
                                };
                            });
                            this.home_recipe.form_prescription_parent = mappedPrescParent;
                        } else {
                            this.home_recipe.form_prescription_parent = []
                        }

                        if (var_presc) {
                            const mappedPresc = var_presc.map(item => {
                                let name = item.id ? this.getPrescParentName(item.id) : ''
                                return {
                                    pharmacy_catalog_id: item.pharmacy_catalog_id,
                                    prescription_type_id: item.prescription_item_type_id,
                                    package_id: item.package_id,
                                    package_name: item.package_name,
                                    dose: item.dose,
                                    sum_dose: item.dose+' '+ item.dose_name,
                                    value_package: item.qty,
                                    sort: item.sort,
                                    used: item.used,
                                    desc: item.desc,

                                    code: item.pharmacy_catalog_code,
                                    name: item.pharmacy_catalog_name,
                                    value_doctor: item.qty_doctor,
                                    prescription_type_name: name,
                                };
                            });
                            this.home_recipe.form_prescription = mappedPresc;
                        } else {
                            this.home_recipe.form_prescription = [];
                        }
                    } else {
                        this.presc_status = 0
                    }
                    // =============================================================================================
                    this.form_nutrition.id = obj?.id || ''
                    this.form_nutrition.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_nutrition.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id
                    this.form_nutrition.nutrition_assessment = obj?.nutrition_assessment || ''
                    this.form_nutrition.nutrition_diagnosis = obj?.nutrition_diagnosis || ''
                    this.form_nutrition.nutrition_intervention = obj?.nutrition_intervention || ''
                    this.form_nutrition.nutrition_monitoring_evaluation = obj?.nutrition_monitoring_evaluation || ''

                    // =============================================================================================
                    await this.cloneAllState()
                });
            } else {
                await this.cloneAllState()
            }

            await this.$nextTick(() => {
                if (this.active.access === 'nutritionist') {
                    this.active.section = 'NUTRITION';
                }
            })
            this.global_from = await this.isConsultant(this.global_gate)
        },

        getAssessment() {
            let message = 'initial dulu'
            this.$emit("refreshAssessment", message);
        },

        // DIAGNOSIS ICD10
        onSearchIcd10(search, loading) {
            if(search.length >= 3) {
                loading(true);
                this.$store.dispatch('icd10/getParam', search).then(data => {
                    this.OptionsIcd10 = []
                    for (let i = 0; i < data.length; i++) {
                        const option = []
                        for (const key in data[i]) {
                            if (key === 'id') { option['value'] = data[i][key] } else if (key === 'name') { option['title'] = data[i]['code']+' '+data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                        }
                        this.OptionsIcd10.push(Object.assign({}, option))
                    }
                    loading(false)
                })
            }
        },

        onAdd10 (event) {
            this.icd10_id = ''

            if (this.form_diagnosis.icd10.some(item => item.icd10_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD10 yang sama')
            } else {
                this.form_diagnosis.icd10.push({ icd10_id:event.value, title:event.title, value:event.value, action: 'action'})
            }

        },
        delete10 (index) {
            this.form_diagnosis.icd10.splice(index, 1)
        },

        addOther10() {
            let input = ''
            if (this.input_other10) {
                input = this.input_other10
                this.form_diagnosis.non_icd10.push(input)
                this.input_other10 = ''
            }
        },

        removeOther10 (index) {
            this.form_diagnosis.non_icd10.splice(index, 1)
        },

        // TRANSAKSI DOKTER
        onAddSC (event) {
            this.costing_id = ''

            this.form_procedure.transaction_doctor.push(
                {
                    costing_id: event.costing_id,
                    costing_name: event.title,
                    is_ref: event.data.is_ref,
                    value: event.value,
                    var_1: event.costing,
                    var_2: 1,
                    doctor_ref_id: '',
                }
            )
        },

        deleteSC (index) {
            this.form_procedure.transaction_doctor.splice(index, 1)
        },

        // TINDAKAN ICD9CM
        onSearchIcd9cm(search, loading) {
            if(search.length >= 2) {
                loading(true);
                this.$store.dispatch('icd9cm/getParam', search).then(data => {
                    this.OptionsIcd9cm = []
                    for (let i = 0; i < data.length; i++) {
                        const option = []
                        for (const key in data[i]) {
                            if (key === 'id') { option['value'] = data[i][key] } else if (key === 'name') { option['title'] = data[i]['code']+' '+data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                        }
                        this.OptionsIcd9cm.push(Object.assign({}, option))
                    }
                    loading(false)
                })
            }
        },

        onAdd9cm (event) {
            this.icd9cm_id = ''

            if (this.form_procedure.icd9cm.some(item => item.icd9cm_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD9 yang sama')
            } else {
                this.form_procedure.icd9cm.push({ icd9cm_id:event.value, title:event.title, value:event.value, action: 'action'})
            }
        },

        delete9cm (index) {
            this.form_procedure.icd9cm.splice(index, 1)
        },

        // PRESCRIPTION
        openPreParent(event) {
            if (event == null) return

            let index = this.input_prescription.form_prescription_parent.findIndex((item) => item.id == event.value);
            if (index !== -1) return;
            // if (index !== -1) return console.log("Object already exists");

            // kl bukan obat satuan, alias racik
            if (event.value != 1) {
                this.temp_presc_parent.parent_id = event.value
                this.temp_presc_parent.parent_name = event.title
                this.temp_presc_parent.package = ''
                this.temp_presc_parent.signatura_time = ''
                this.temp_presc_parent.administer = ''
                this.temp_presc_parent.desc = ''
                this.active.modal.presc_parent = true
                return
            }

            // obat satuan
            this.input_prescription.form_prescription_parent.push({
                id: event.value,
                is_exist: 0,
            })
        },

        closePreParent() {
            this.active.modal.presc_parent = false
            this.kxStock = ''
            this.kxCatalog = ''
        },

        addParent() {
            this.input_prescription.form_prescription_parent.push(
                {
                    id: this.temp_presc_parent.parent_id,
                    status: 3,
                    value: 1,
                    package: this.temp_presc_parent.package,
                    signatura_times_id: this.temp_presc_parent.signatura_time,
                    administer_id: this.temp_presc_parent.administer,
                    desc: this.temp_presc_parent.desc,
                    is_exist: 0,
                }
            )
            this.active.modal.presc_parent = false
        },

        onSearchCatalog(search, loading) {
            if (search.length >= 3) {
                loading(true)
                const searchTerm = search.toLowerCase();
                this.OptionsCatalog = this.catalog_list.filter(item => {
                    return item?.data_catalog?.composition.toLowerCase().includes(searchTerm) ||
                    item?.title.toLowerCase().includes(searchTerm)
                });
                loading(false)
            }
        },

        async openPreItem(event) {
            this.kxCatalog = ''
            if (this.kxStock == '' || this.kxStock == null) return alert("Silakan Pilih Sediaan Terlebih Dahulu")

            this.temp_presc_items.parent_id = this.kxStock?.value
            this.temp_presc_items.parent_name = this.kxStock?.title
            this.temp_presc_items.catalog_name = event?.data_catalog?.name
            this.temp_presc_items.data_catalog = event?.data_catalog
            this.temp_presc_items.signatura_dose = event?.data_catalog?.signatura_dose
            this.temp_presc_items.signatura_unit = event?.data_catalog?.signatura_unit_id
            this.temp_presc_items.signatura_time = event?.data_catalog?.signatura_times_id
            this.temp_presc_items.administer = event?.data_catalog?.instruction_id || ''
            this.temp_presc_items.desc = ''
            await this.getSignaturaUnits(event?.data_catalog?.package_id)

            this.active.modal.presc_items = true
        },

        addPrescItem() {
            this.input_prescription.form_prescription.push({
                pharmacy_catalog_id: this.temp_presc_items?.data_catalog?.id,
                package_id: this.temp_presc_items?.data_catalog?.package_id,
                prescription_type_id: this.temp_presc_items.parent_id,
                dose: this.temp_presc_items.signatura_dose,
                dose_unit_id: this.temp_presc_items.signatura_unit,
                signatura_times_id: this.temp_presc_items.signatura_time,
                administer_id: this.temp_presc_items.administer,
                desc: this.temp_presc_items.desc,
                sort: this.input_prescription.form_prescription.length,
                status: this.temp_presc_items.parent_id == 1 ? 2 : null,

                is_exist: 0,
                name: this.temp_presc_items?.data_catalog?.name,
                package_name: this.temp_presc_items?.data_catalog?.package_name,
                catalog_date: '',
                stop_date: '',
                is_change: '',
                exist_status: '',
                doctor_added: this.userData.employee_id,
                doctor_added_name: this.userData.employee_name,
                doctor_nickname: this.userData.employee_name,
                id: '',
                dose_unit_options: this.optItemSignUnits
            })
            this.active.modal.presc_items = false
        },

        deletePRES (index) {
            this.input_prescription.form_prescription.splice(index, 1)
        },

        async resetCpptForm() {
            this.form_anamnesa.id = ''
            this.form_anamnesa.registration_destination_id = ''
            this.form_anamnesa.doctor_in_charge_id = ''
            this.form_anamnesa.subjective = ''
            this.form_anamnesa.nursing_objective = ''

            this.form_vital.id = ''
            this.form_vital.registration_destination_id = ''
            this.form_vital.doctor_in_charge_id = ''
            this.form_vital.systolic_blood_pressure = ''
            this.form_vital.diastolic_blood_pressure = ''
            this.form_vital.pulse_frequency = ''
            this.form_vital.breath_frequency = ''
            this.form_vital.oximetry_frequency = ''
            this.form_vital.temperature = ''
            this.form_vital.aware = ''
            this.form_vital.oxygen_type_id = ''
            this.form_vital.eye_score = ''
            this.form_vital.verbal_score = ''
            this.form_vital.motor_score = ''
            this.form_vital.skin_color = ''
            this.form_vital.behavior = ''
            this.form_vital.weight = ''
            this.form_vital.height = ''
            this.form_vital.imt = ''
            this.form_vital.objective = ''

            this.form_generalis.id = ''
            this.form_generalis.doctor_in_charge_id = ''
            this.form_generalis.registration_destination_id = ''
            this.form_generalis.head = 1
            this.form_generalis.head_hair_color = 1
            this.form_generalis.head_hair_condition = 1
            this.form_generalis.eye_conjunctive = 1
            this.form_generalis.eye_sclera = 1
            this.form_generalis.eye_pupil = 1
            this.form_generalis.eye_light_reflex = 1
            this.form_generalis.mouth_mucosa = 1
            this.form_generalis.mouth_discoloration = 1
            this.form_generalis.mouth_tongue = 1
            this.form_generalis.mouth_caries = 1
            this.form_generalis.neck_kgb = 1
            this.form_generalis.neck_trachea_position = 1
            this.form_generalis.neck_massa = 1
            this.form_generalis.neck_jvp = 1
            this.form_generalis.thorax_insp_chest = 1
            this.form_generalis.thorax_insp_retraction = 1
            this.form_generalis.thorax_palp_chest = 1
            this.form_generalis.thorax_palp_fremitus = 1
            this.form_generalis.thorax_palp_crepitation = 1
            this.form_generalis.thorax_ausk_pul_vasiculer = 1
            this.form_generalis.thorax_ausk_pul_rhonki = 1
            this.form_generalis.thorax_ausk_pul_wheezing = 1
            this.form_generalis.thorax_ausk_cor_heart_sound = 1
            this.form_generalis.thorax_ausk_cor_murmur = 1
            this.form_generalis.thorax_ausk_cor_gallop = 1
            this.form_generalis.abdomen_supel = 1
            this.form_generalis.abdomen_bowel_sounds = 1
            this.form_generalis.abdomen_tenderness = 1
            this.form_generalis.abdomen_hepar_lien = 1
            this.form_generalis.abdomen_other_physical_check = ''
            this.form_generalis.extremity_acral = 1
            this.form_generalis.extremity_crt = 1
            this.form_generalis.extremity_edema = 1
            this.form_generalis.genitalia = ''
            this.form_generalis.localist_status = ''
            this.form_generalis.neurologist_status = ''
            this.form_generalis.ophthal_status = ''

            this.form_diagnosis.id = ''
            this.form_diagnosis.registration_destination_id = ''
            this.form_diagnosis.doctor_in_charge_id = ''
            this.form_diagnosis.icd10 = []
            this.form_diagnosis.icd10_notes = ''
            this.form_diagnosis.non_icd10 = []
            this.form_diagnosis.nursing_diagnose = ''

            this.form_procedure.id = ''
            this.form_procedure.registration_destination_id = ''
            this.form_procedure.doctor_in_charge_id = ''
            this.form_procedure.icd9cm = []
            this.form_procedure.therapy_plan = ''
            this.form_procedure.transaction_doctor = []

            this.form_plan.id = ''
            this.form_plan.registration_destination_id = ''
            this.form_plan.doctor_in_charge_id = ''
            this.form_plan.nursing_plan = ''
            this.form_plan.plan = ''
            this.form_plan.pharmacological_plan = ''

            this.form_laboratory.id = ''
            this.form_laboratory.registration_destination_id = ''
            this.form_laboratory.doctor_in_charge_id = ''
            this.form_laboratory.laboratory_request_items = []
            this.form_laboratory.clinical_indications = ''

            this.form_radiology.id = ''
            this.form_radiology.registration_destination_id = ''
            this.form_radiology.doctor_in_charge_id = ''
            this.form_radiology.radiology_request_items = []
            this.form_radiology.clinical_indications = ''

            this.form_physiotherapy.id = ''
            this.form_physiotherapy.registration_destination_id = ''
            this.form_physiotherapy.doctor_in_charge_id = ''
            this.form_physiotherapy.physiotherapy_request_items = []

            this.home_recipe.id = ''
            this.home_recipe.registration_destination_id = ''
            this.home_recipe.doctor_in_charge_id = ''
            this.home_recipe.pharmacy_depo_id = ''
            this.home_recipe.form_prescription_parent = []
            this.home_recipe.form_prescription = []
            this.home_recipe.type_prescription_id = ''

            this.form_nutrition.id = ''
            this.form_nutrition.registration_destination_id = ''
            this.form_nutrition.doctor_in_charge_id = ''
            this.form_nutrition.nutrition_assessment = ''
            this.form_nutrition.nutrition_diagnosis = ''
            this.form_nutrition.nutrition_intervention = ''
            this.form_nutrition.nutrition_monitoring_evaluation = ''
        },

        hasItemsInCategory(items, categoryId) {
            if (items) {
                return items.some(item => item.prescription_item_type_id === categoryId);
            }
        },

        allowedToCopySoap(section) {
            const rules = {
                doctor: ['VITAL', 'GENERALIS', 'DIAGNOSIS', 'PROCEDURE', 'LABORATORY', 'RADIOLOGY', 'PHYSITHERAPY', 'PRESC'],
                general_doctor: ['VITAL', 'GENERALIS', 'DIAGNOSIS', 'PROCEDURE'],
                nurse: ['VITAL'],
            }

            return rules[this.active.access]?.includes(section) ?? false
        },

        allowedToCopyCppt(section) {
            const rules = {
                doctor: ['ANAMNESA', 'VITAL', 'GENERALIS', 'DIAGNOSIS', 'PROCEDURE', 'PLAN', 'LABORATORY', 'RADIOLOGY', 'PHYSITHERAPY', 'PRESC'],
                general_doctor: ['ANAMNESA', 'VITAL', 'GENERALIS', 'DIAGNOSIS', 'PROCEDURE', 'PLAN'],
                nurse: ['ANAMNESA', 'VITAL', 'NSDIAGNOSIS', 'NSPLAN'],
                nutritionist: ['NUTRITION'],
            }

            return rules[this.active.access]?.includes(section) ?? false
        },

        allowedToCopySupport() {
            if (this.active.access == 'doctor') return true
            return false
        },

        copySelected(event, func, obj, section) {
            if(section == this.active.section) {
                const button = event.target;
                button.innerText = 'COPIED';

                func(obj);

                setTimeout(() => {
                    button.innerText = 'COPY';
                }, 1000);
            } else {
                this.notification('Perhatian','AlertCircleIcon','warning','Pastikan untuk menyalin dalam tab yang sama')
            }
        },

        copyLaboratory(obj) {
            if (obj.laboratory_request_items) {
                const mappedLab = obj.laboratory_request_items.map(item => {
                    return {
                        costing_name: item.laboratory_package_name,
                        laboratory_package_id: item.laboratory_package_id,
                        service_class_id: item.service_class_id
                    };
                });
                this.form_laboratory.laboratory_request_items = mappedLab;
            }

            if (obj.clinical_indications) {
                this.form_laboratory.clinical_indications = obj.clinical_indications
            } else {
                this.form_laboratory.clinical_indications = '';
            }
        },

        copyRadiology(obj) {
            if (obj.radiology_request_items) {
                const mappedRad = obj.radiology_request_items.map(item => {
                    return {
                        costing_name: item.radiology_package_name,
                        radiology_package_id: item.radiology_package_id,
                        service_class_id: item.service_class_id
                    };
                });
                this.form_radiology.radiology_request_items = mappedRad;
            }

            if (obj.clinical_indications) {
                this.form_radiology.clinical_indications = obj.clinical_indications
            } else {
                this.form_radiology.clinical_indications = '';
            }

        },

        copyPhysiotherapy(obj) {
            if (obj.physiotherapy_request_items) {
                const mappedPhy = obj.physiotherapy_request_items.map(item => {
                    return {
                        costing_name: item.physiotherapy_package_name,
                        physiotherapy_package_id: item.physiotherapy_package_id,
                        service_class_id: item.service_class_id
                    };
                });
                this.form_physiotherapy.physiotherapy_request_items = mappedPhy;
            }
        },

        // soapAnamnesa(obj) {
        //     this.form_anamnesa.subjective = obj.subjective
        // },

        soapVitalSign(obj) {
            this.form_vital.systolic_blood_pressure = obj?.systolic_blood_pressure
            this.form_vital.diastolic_blood_pressure = obj?.diastolic_blood_pressure
            this.form_vital.pulse_frequency = obj?.pulse_frequency
            this.form_vital.breath_frequency = obj?.breath_frequency
            this.form_vital.oximetry_frequency = obj?.oximetry_frequency
            this.form_vital.temperature = obj?.temperature
            this.form_vital.oxygen_type_id = obj?.oxygen_type_id
            this.form_vital.aware = obj?.aware
            this.form_vital.eye_score = obj?.eye_score
            this.form_vital.verbal_score = obj?.verbal_score
            this.form_vital.motor_score = obj?.motor_score
            if (this.isPews) {
                this.form_vital.skin_color = obj?.skin_color
                this.form_vital.behavior = obj?.behavior
            }
            this.form_vital.weight = obj?.weight
            this.form_vital.height = obj?.height
            this.form_vital.imt = obj?.imt
            this.form_vital.objective = obj?.objective || ''
        },

        soapLocalis(obj) {
            this.form_generalis.localist_status = obj.localist_status
        },

        soapDiagnosis(obj) {
            if (obj.icd10) {
                const mappedIcd10 = obj.icd10.map(item => {
                    return {
                        icd10_id: item.icd10_id,
                        value: item.icd10_id,
                        title: `${item.code} ${item.name}`,
                        action: "action"
                    };
                });
                this.form_diagnosis.icd10 = mappedIcd10;
            }
            this.form_diagnosis.icd10_notes = obj.icd10_notes;
            // tambahin non-icd10
            if (obj.non_icd10) {
                for (let i = 0; i < obj.non_icd10.length; i++) {
                    const el = obj.non_icd10[i];
                    this.form_diagnosis.non_icd10.push(el)
                }
            }
        },
        soapProcedure(obj) {
            if (obj.soap_p?.length > 0) {
                let seenIds = new Set();

                this.form_procedure.icd9cm = obj.soap_p.reduce((acc, req) => {
                    req.icd9cm.forEach(item => {
                        if (!seenIds.has(item.icd9cm_id)) {
                            seenIds.add(item.icd9cm_id);
                            acc.push({
                                icd9cm_id: item.icd9cm_id,
                                value: item.icd9cm_id,
                                title: `${item.code} ${item.name}`,
                                action: "action"
                            });
                        }
                    });
                    return acc;
                }, []);
                this.form_procedure.therapy_plan = obj.soap_p[0].therapy_plan;
            }

            if (obj.transaction_doctor) {
                let list = [];
                this.form_procedure.transaction_doctor = []

                for (let i = 0; i < obj.transaction_doctor.length; i++) {
                    const el = obj.transaction_doctor[i];
                    let costingIndex = '';

                    costingIndex = this.getCosting.findIndex(item => item.costing_id === el.costing_id);
                    if (costingIndex !== -1) {
                        let data = {
                            costing_id: this.getCosting[costingIndex].costing_id,
                            costing_name: this.getCosting[costingIndex].title,
                            is_ref: this.getCosting[costingIndex].data.is_ref,
                            value: this.getCosting[costingIndex].value,
                            var_1: this.getCosting[costingIndex].costing,
                            var_2: 1,
                            doctor_ref_id:'',
                            action: 'action',
                        }
                        list.push(data);
                    }
                }
                this.form_procedure.transaction_doctor = list;
            }
        },

        soapLaboratory(obj) {
            if (obj.laboratory_request) {
                let seenLabIds = new Set();

                this.form_laboratory.laboratory_request_items = obj.laboratory_request.reduce((acc, req) => {
                    req.laboratory_request_items.forEach(item => {
                        if (!seenLabIds.has(item.laboratory_package_id)) {
                            seenLabIds.add(item.laboratory_package_id);
                            acc.push({
                                costing_name: item.laboratory_package_name,
                                laboratory_package_id: item.laboratory_package_id,
                                service_class_id: item.service_class_id,
                            });
                        }
                    });
                    return acc;
                }, []);

                const notesArr = obj.laboratory_request
                    .map(r => r.clinical_indications)
                    .filter(note => note && note.trim() !== '');
                this.form_laboratory.clinical_indications = notesArr?.join('\n');
            }
        },

        soapRadiology(obj) {
            if (obj.radiology_request) {
                let seenRadIds = new Set();

                this.form_radiology.radiology_request_items = obj.radiology_request.reduce((acc, req) => {
                    req.radiology_request_items.forEach(item => {
                        if (!seenRadIds.has(item.radiology_package_id)) {
                            seenRadIds.add(item.radiology_package_id);
                            acc.push({
                                costing_name: item.radiology_package_name,
                                radiology_package_id: item.radiology_package_id,
                                service_class_id: item.service_class_id,
                            });
                        }
                    });
                    return acc;
                }, []);

                const notesArr = obj.radiology_request
                    .map(r => r.clinical_indications)
                    .filter(note => note && note.trim() !== '');
                this.form_radiology.clinical_indications = notesArr?.join('\n');
            }
        },

        soapPhysiotherapy(obj) {
            if (obj.physiotherapy_request) {
                let seenRadIds = new Set();

                this.form_physiotherapy.physiotherapy_request_items = obj.physiotherapy_request.reduce((acc, req) => {
                    req.physiotherapy_request_items.forEach(item => {
                        if (!seenRadIds.has(item.physiotherapy_package_id)) {
                            seenRadIds.add(item.physiotherapy_package_id);
                            acc.push({
                                costing_name: item.physiotherapy_package_name,
                                physiotherapy_package_id: item.physiotherapy_package_id,
                                service_class_id: item.service_class_id,
                            });
                        }
                    });
                    return acc;
                }, []);
            }
        },

        soapPrescription(obj) {
            const prescriptions = obj?.prescription || []
            if (prescriptions.length === 0) return

            const exsParents = this.input_prescription.form_prescription_parent.map(item => Number(item.id))
            const exsItems = this.input_prescription.form_prescription.map(item => ({
                prescription_type_id: item.prescription_type_id,
                pharmacy_catalog_id: item.pharmacy_catalog_id
            }))

            // Process parents
            prescriptions.flatMap(p => p.form_prescription_parent)
                .filter(parent => !exsParents.includes(parent.id))
                .forEach(el => {
                    this.input_prescription.form_prescription_parent.push(
                        el.id == 1
                        ? { id: el.id, is_exist: 0, }
                        : {
                            id: el.id,
                            status: 3,
                            value: Number(el.value ?? 0),
                            package: Number(el.package),
                            signatura_times_id: el.signatura_times_id ?? '',
                            administer_id: el.administer_id ?? '',
                            desc: el.administer_id == this.other_administer ? el.desc : '',
                            is_exist: 0,
                        }
                    )
                })

            // Process items
            prescriptions.flatMap(p => p.form_prescription)
                .filter(item => !exsItems.some(exs =>
                    exs.pharmacy_catalog_id == item.pharmacy_catalog_id &&
                    exs.prescription_type_id == item.prescription_item_type_id
                ))
                .forEach(el => {
                    this.input_prescription.form_prescription.push({
                        pharmacy_catalog_id: el.pharmacy_catalog_id,
                        package_id: el.package_id,
                        prescription_type_id: el.prescription_item_type_id,
                        dose: Number(el.dose),
                        dose_unit_id: el.dose_unit_id,
                        signatura_times_id: el.signatura_times_id,
                        administer_id: el.administer_id,
                        desc: el.administer_id == this.other_administer ? el.desc : '',
                        sort: el.sort,
                        status: el.prescription_item_type_id == 1 ? 2 : null,

                        is_exist: 0,
                        name: el.pharmacy_catalog_name,
                        package_name: el.package_name,
                        catalog_date: '',
                        stop_date: '',
                        is_change: '',
                        exist_status: '',
                        doctor_added: this.userData.employee_id,
                        doctor_added_name: this.userData.employee_name,
                        doctor_nickname: this.userData.employee_name,
                        id: '',
                        dose_unit_options: el?.dose_unit_options?.map(opt => ({
                            value: opt.id,
                            title: opt.name
                        })) || []
                    })
                })
        },

        // nurseAnamnesa(obj) {
        //     this.form_anamnesa.subjective = obj.subjective
        // },

        nurseVitalSign(obj) {
            this.form_vital.systolic_blood_pressure = obj?.systolic_blood_pressure
            this.form_vital.diastolic_blood_pressure = obj?.diastolic_blood_pressure
            this.form_vital.pulse_frequency = obj?.pulse_frequency
            this.form_vital.breath_frequency = obj?.breath_frequency
            this.form_vital.oximetry_frequency = obj?.oximetry_frequency
            this.form_vital.temperature = obj?.temperature
            this.form_vital.oxygen_type_id = obj?.oxygen_type_id
            this.form_vital.aware = obj?.aware
            this.form_vital.eye_score = obj?.eye_score
            this.form_vital.verbal_score = obj?.verbal_score
            this.form_vital.motor_score = obj?.motor_score
            if (this.isPews) {
                this.form_vital.skin_color = obj?.skin_color
                this.form_vital.behavior = obj?.behavior
            }
            this.form_vital.weight = obj?.weight
            this.form_vital.height = obj?.height
            this.form_vital.imt = obj?.imt
            this.form_vital.objective = obj?.objective || ''
        },

        cpptNsDiagnosis(obj) {
            this.form_diagnosis.nursing_diagnose = obj?.nursing_diagnose
        },

        cpptNsPlan(obj) {
            this.form_plan.nursing_plan = obj?.nursing_plan
        },

        cpptNutrition(obj) {
            this.form_nutrition.nutrition_assessment = obj?.nutrition_assessment
            this.form_nutrition.nutrition_diagnosis = obj?.nutrition_diagnosis
            this.form_nutrition.nutrition_intervention = obj?.nutrition_intervention
            this.form_nutrition.nutrition_monitoring_evaluation = obj?.nutrition_monitoring_evaluation
        },

        cpptSubjective(obj) {
            this.form_anamnesa.subjective = obj?.subjective
            if (this.active.access == 'nurse') this.form_anamnesa.nursing_objective = obj?.nursing_objective
        },

        cpptVitalSign(obj) {
            this.form_vital.systolic_blood_pressure = obj?.systolic_blood_pressure
            this.form_vital.diastolic_blood_pressure = obj?.diastolic_blood_pressure
            this.form_vital.pulse_frequency = obj?.pulse_frequency
            this.form_vital.breath_frequency = obj?.breath_frequency
            this.form_vital.oximetry_frequency = obj?.oximetry_frequency
            this.form_vital.temperature = obj?.temperature
            this.form_vital.oxygen_type_id = obj?.oxygen_type_id
            this.form_vital.aware = obj?.aware
            this.form_vital.eye_score = obj?.eye_score
            this.form_vital.verbal_score = obj?.verbal_score
            this.form_vital.motor_score = obj?.motor_score
            if (this.isPews) {
                this.form_vital.skin_color = obj?.skin_color
                this.form_vital.behavior = obj?.behavior
            }
            this.form_vital.weight = obj?.weight
            this.form_vital.height = obj?.height
            this.form_vital.imt = obj?.imt
            this.form_vital.objective = obj?.objective || ''
        },

        cpptLocalis(obj) {
            this.form_generalis.localist_status = obj.localist_status
        },

        cpptDiagnosis(obj) {
            if (obj.icd10) {
                const mappedIcd10 = obj.icd10.map(item => {
                    return {
                        icd10_id: item.icd10_id,
                        value: item.icd10_id,
                        title: `${item.icd10_code} ${item.icd10_name}`,
                        action: "action"
                    };
                });
                this.form_diagnosis.icd10 = mappedIcd10;
            }
            this.form_diagnosis.icd10_notes = obj.icd10_notes;
            // tambahin non-icd10
            this.form_diagnosis.non_icd10 = Array.isArray(obj.non_icd10) ? [...obj.non_icd10] : [];
                // for (let i = 0; i < obj.non_icd10.length; i++) {
                //     const el = obj.non_icd10[i];
                //     this.form_diagnosis.non_icd10.push(el)
                // }
        },

        cpptProcedure(obj) {
            this.form_procedure.icd9cm = []
            let temp9 = []
            if (obj.icd9cm?.length > 0) {
                for (let i = 0; i < obj.icd9cm.length; i++) {
                    const el = obj.icd9cm[i];

                    temp9.push({
                        icd9cm_id: el.icd9cm_id,
                        value: el.icd9cm_id,
                        title: `${el.icd9cm_code} ${el.icd9cm_name}`,
                        action: "action"
                    });
                }
                this.form_procedure.icd9cm = temp9
            }
            this.form_procedure.therapy_plan = obj?.therapy_plan;

            if (obj.transaction_doctor) {
                let list = [];
                this.form_procedure.transaction_doctor = []

                for (let i = 0; i < obj.transaction_doctor.length; i++) {
                    const el = obj.transaction_doctor[i];
                    let costingIndex = '';

                    costingIndex = this.getCosting.findIndex(item => item.costing_id === el.costing_id);
                    if (costingIndex !== -1) {
                        let data = {
                            costing_id: this.getCosting[costingIndex].costing_id,
                            costing_name: this.getCosting[costingIndex].title,
                            is_ref: this.getCosting[costingIndex].data.is_ref,
                            value: this.getCosting[costingIndex].value,
                            var_1: this.getCosting[costingIndex].costing,
                            var_2: 1,
                            doctor_ref_id:'',
                            action: 'action',
                        }
                        list.push(data);
                    }
                }
                this.form_procedure.transaction_doctor = list;
            }
        },

        cpptPlan(obj) {
            this.form_plan.plan = obj?.plan
            this.form_plan.pharmacological_plan = obj?.pharmacological_plan
        },

        cpptLaboratory(obj) {
            if (obj.laboratory_request) {
                let seenLabIds = new Set();

                this.form_laboratory.laboratory_request_items = obj.laboratory_request.reduce((acc, req) => {
                    req.laboratory_request_items.forEach(item => {
                        if (!seenLabIds.has(item.laboratory_package_id)) {
                            seenLabIds.add(item.laboratory_package_id);
                            acc.push({
                                costing_name: item.laboratory_package_name,
                                laboratory_package_id: item.laboratory_package_id,
                                service_class_id: item.service_class_id,
                            });
                        }
                    });
                    return acc;
                }, []);

                const notesArr = obj.laboratory_request
                    .map(r => r.clinical_indications)
                    .filter(note => note && note.trim() !== '');
                this.form_laboratory.clinical_indications = notesArr?.join('\n');
            }
        },

        cpptRadiology(obj) {
            if (obj.radiology_request) {
                let seenRadIds = new Set();

                this.form_radiology.radiology_request_items = obj.radiology_request.reduce((acc, req) => {
                    req.radiology_request_items.forEach(item => {
                        if (!seenRadIds.has(item.radiology_package_id)) {
                            seenRadIds.add(item.radiology_package_id);
                            acc.push({
                                costing_name: item.radiology_package_name,
                                radiology_package_id: item.radiology_package_id,
                                service_class_id: item.service_class_id,
                            });
                        }
                    });
                    return acc;
                }, []);

                const notesArr = obj.radiology_request
                    .map(r => r.clinical_indications)
                    .filter(note => note && note.trim() !== '');
                this.form_radiology.clinical_indications = notesArr?.join('\n');
            }
        },

        cpptPhysiotherapy(obj) {
            if (obj.physiotherapy_request) {
                let seenRadIds = new Set();

                this.form_physiotherapy.physiotherapy_request_items = obj.physiotherapy_request.reduce((acc, req) => {
                    req.physiotherapy_request_items.forEach(item => {
                        if (!seenRadIds.has(item.physiotherapy_package_id)) {
                            seenRadIds.add(item.physiotherapy_package_id);
                            acc.push({
                                costing_name: item.physiotherapy_package_name,
                                physiotherapy_package_id: item.physiotherapy_package_id,
                                service_class_id: item.service_class_id,
                            });
                        }
                    });
                    return acc;
                }, []);
            }
        },

        // cpptPrescription(obj) {
        //     if (obj.prescription) {
        //         let seenParentIds = new Set();
        //         this.input_prescription.form_prescription_parent = obj.prescription.reduce((acc, req) => {
        //             req.form_prescription_parent.forEach(item => {
        //                 if (!seenParentIds.has(item.id)) {
        //                     seenParentIds.add(item.id);
        //                     acc.push({
        //                         id: item.id,
        //                         used: item.used,
        //                         desc: item.desc,
        //                         // value: Number(item.value),
        //                         prescription: this.getPrescParentName(item.id),
        //                         package: item.package,
        //                     });
        //                 }
        //             });
        //             return acc;
        //         }, []);

        //         this.input_prescription.form_prescription = obj.prescription.reduce((acc, req) => {
        //             req.form_prescription.forEach(item => {
        //                 acc.push({
        //                     pharmacy_catalog_id: item.pharmacy_catalog_id,
        //                     package_id: item.package_id,
        //                     dose: item.dose,
        //                     prescription_type_id: item.prescription_item_type_id,
        //                     // value_package: item.qty,
        //                     used: item.used,
        //                     desc: item.desc,
        //                     sort: item.sort,
        //                     status: 1,

        //                     name: item.pharmacy_catalog_name,
        //                     package_name: item.package_name,
        //                     prescription_type_name: this.getPrescParentName(item.prescription_item_type_id),
        //                     // sum_dose: item.dose+' '+ item.dose_name,
        //                     // code: item.pharmacy_catalog_code,
        //                     // value_doctor: item.qty_doctor,
        //                 });
        //             });
        //             return acc;
        //         }, []);
        //     }
        // },

        templatePrescription(obj) {
            if (!obj) return

            const exsParents = this.input_prescription.form_prescription_parent.map(item => Number(item.id))
            const exsItems = this.input_prescription.form_prescription.map(item => ({
                prescription_type_id: item.prescription_type_id,
                pharmacy_catalog_id: item.pharmacy_catalog_id
            }))

            let var_parent = obj.form_prescription_parent || []
            let var_presc = obj.form_prescription || []

            if (var_parent) {
                const filteredParent = var_parent.filter(p => !exsParents.includes(p.id))
                const newParents = filteredParent.map(item =>
                    item.id == 1
                        ? { id: item.id, is_exist: 0, }
                        : {
                            id: item.id,
                            status: 3,
                            value: Number(item.value ?? 0),
                            package: Number(item.package),
                            signatura_times_id: item.signatura_times_id ?? '',
                            administer_id: item.administer_id ?? '',
                            desc: item.administer_id == this.other_administer ? item.desc : '',
                            is_exist: 0,
                        }
                )
                this.input_prescription.form_prescription_parent.push(...newParents)
            }

            if (var_presc) {
                const filtereditem = var_presc.filter(item => !exsItems.some(exs =>
                    exs.pharmacy_catalog_id == item.pharmacy_catalog_id &&
                    exs.prescription_type_id == item.prescription_item_type_id
                ))

                const newItems = filtereditem.map(item => {
                    return {
                        pharmacy_catalog_id: item.pharmacy_catalog_id,
                        package_id: item.package_id,
                        prescription_type_id: item.prescription_item_type_id,
                        dose: Number(item.dose),
                        dose_unit_id: item.dose_unit_id,
                        signatura_times_id: item.signatura_times_id,
                        administer_id: item.administer_id,
                        desc: item.administer_id == this.other_administer ? item.desc : '',
                        sort: item.sort,
                        status: item.prescription_item_type_id == 1 ? 2 : null,

                        is_exist: 0,
                        name: item.pharmacy_catalog_name,
                        package_name: item.package_name,
                        catalog_date: '',
                        stop_date: '',
                        is_change: '',
                        exist_status: '',
                        doctor_added: this.userData.employee_id,
                        doctor_added_name: this.userData.employee_name,
                        doctor_nickname: this.userData.employee_name,
                        id: '',
                        dose_unit_options: item?.dose_unit_options?.map(opt => ({
                            value: opt.id,
                            title: opt.name
                        })) || []
                    }
                })
                this.input_prescription.form_prescription.push(...newItems)
            }
        },

        // templateAnamnesa(obj) {
        //     this.form_anamnesa.anamesa_type_id = obj?.anamesa_type_id
        //     this.form_anamnesa.family_name = obj?.family_name
        //     this.form_anamnesa.family_relation_id = obj?.family_relation_id
        //     this.form_anamnesa.main_complaint = obj?.main_complaint
        //     this.form_anamnesa.history_disease_now = obj?.history_disease_now
        //     this.form_anamnesa.history_disease_past = obj?.history_disease_past
        //     this.form_anamnesa.history_drugs = obj?.history_drugs
        // },

        triggerRadResult(message) {
            this.$refs.radModalResult.seeRadiologyResult(message);
        },

        triggerLabResult(message) {
            this.$refs.labModalResult.seeLaboratoryResult(message);
        },

        // openAsPrescTemplate(obj) {
        //     this.resetPrescTemplate()
        //     if (obj.parent) {
        //         const mappedPrescParent = obj.parent.map(item => {
        //             return {
        //                 id: item.id,
        //                 used: item.used,
        //                 value: Number(item.value),
        //                 prescription: this.getPrescParentName(item?.id || ''),
        //                 package: item.package,
        //             };
        //         });
        //         this.presc_inp_templating.form_prescription_parent = mappedPrescParent;
        //     } else {
        //         this.presc_inp_templating.form_prescription_parent = []
        //     }

        //     if (obj.item) {
        //         const mappedPresc = obj.item.map(unit => {
        //             return {
        //                 code: unit?.code || unit?.pharmacy_catalog_code,
        //                 name: unit?.name || unit?.pharmacy_catalog_name,
        //                 pharmacy_catalog_id: unit.pharmacy_catalog_id,
        //                 package_id: unit?.package_id,
        //                 dose: unit?.dose,
        //                 sum_dose: `${unit?.dose} ${unit?.dose_name || ''}`,
        //                 package_name: unit?.package_name,
        //                 prescription_type_id: unit?.prescription_type_id || unit?.prescription_item_type_id,
        //                 prescription_type_name: this.getPrescParentName(unit?.prescription_type_id || unit?.prescription_item_type_id || ''),
        //                 value_doctor: unit.qty_doctor || 0,
        //                 value_package: unit?.qty || unit?.value_package,
        //                 used: unit?.used,
        //                 desc: unit?.desc,
        //                 sort: unit?.sort,
        //             };
        //         });
        //         this.presc_inp_templating.form_prescription = mappedPresc;
        //     } else {
        //         this.presc_inp_templating.form_prescription = [];
        //     }

        //     // console.log('this.presc_inp_templating:', this.presc_inp_templating)
        //     this.active.modal.mdl_presc_template = true
        // },

        // async savePrescTemplate() {
        //     try {
        //         this.active.disabled.dsb_presc_template = true
        //         await this.$store.dispatch('templating/postPutPrescTemplate', this.presc_inp_templating)
        //         .then(
        //             res => {
        //                 this.notification('Success','CheckIcon','success','You have successfully')
        //                 this.active.modal.mdl_presc_template = false
        //                 this.resetPrescTemplate()
        //                 this.getPrescTemplate()
        //             }
        //         )
        //         .catch(({message, debug, response}) => {
        //             this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
        //         })
        //     } catch (error) {
        //         this.notification('Failed','CloseIcon','danger','You have unsuccessful')
        //         console.error('Error catch at: ', error)
        //     } finally {
        //         this.active.disabled.dsb_presc_template = false
        //     }
        // },

        // resetPrescTemplate() {
        //     this.presc_inp_templating.id = ''
        //     this.presc_inp_templating.name = ''
        //     this.presc_inp_templating.form_prescription_parent = []
        //     this.presc_inp_templating.form_prescription = []
        // },

        // getPrescTemplate() {
        //     try {
        //         this.$store.dispatch('templating/getPrescTemplate').then((response) => {
        //             this.presc_list_templating = response
        //             this.presc_filtered = response
        //             // trigger refresh pake emit ke dr-stds / List CPPT
        //             this.$emit("prescChildTemplate", response);
        //         }, error => {
        //             console.error('Error while GET templating/getPrescTemplate at: ', error);
        //         })
        //     } catch (error) {
        //         console.error('Error catch while GET templating/getPrescTemplate at: ', error);
        //     }
        // },

        // getNonPharma() {
        //     try {
        //         this.$store.dispatch('kardex/getOptNonPharma')
        //         .then(
        //             response => {
        //                 this.non_pharma_list = response
        //             }
        //         )
        //         .catch(({message, debug, response}) => {
        //             console.error('Error GET kardex/getOptNonPharma at: ', response);
        //         })
        //     } catch (error) {
        //         console.error('Error catch while GET kardex/getOptNonPharma at: ', error);
        //     }
        // },

        addNonPharma() {
            if (this.kxNonPharma) {
                this.input_prescription.non_pharmacological.push({
                    id: '',
                    name: this.kxNonPharma,
                    doctor_added: this.userData.employee_id,
                    doctor_added_name: this.userData.employee_name,
                })
                this.kxNonPharma = ''
            }
        },

        removeNonPharma(index) {
            this.input_prescription.non_pharmacological.splice(index, 1)
        },

        async getCpptHistory(norm) {
            const param = {
                norm: norm || this.regData.norm,
                limit: this.cppt_load.limit_display
            }
            try {
                await this.$store.dispatch('cppt/getHistoryCPPT', param)
                .then(
                    response => {
                        this.history.cppt = response.rows
                        this.cppt_load.count = response.iTotal
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('Error GET cppt/getHistoryCPPT at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET cppt/getHistoryCPPT at: ', error);
            }
    },

        async getDiagnosisCPPTHistory(norm) {
            const param = {
                norm: norm,
                limit: this.cppt_load.limit_display,
                reg_des_id: this.reg_des_id
            }
            try {
                await this.$store.dispatch('cppt/getDiagnosisCPPTHistory', param)
                .then(
                    response => {
                        this.history.cppt_doctor = Array.isArray(response) ? response : [];
                        this.cppt_load.count = response.iTotal
                        // console.log('getDiagnosisCPPTHistory response: ', response);
                        // console.log('this.history.cppt_doctor: ', this.history.cppt_doctor);
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('Error GET cppt/getDoctorHistoryCPPT at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET cppt/getDoctorHistoryCPPT at: ', error);
            }
        },

        loadMoreCppt() {
            this.cppt_load.limit_display += this.cppt_load.add_display
            this.getCpptHistory(this.regData.norm)
        },

        async getNurseHistory(norm) {
            const param = {
                norm: norm,
                limit: this.nurse_load.limit_display
            }
            try {
                await this.$store.dispatch('nurse/getHistoryNurse', param)
                .then(
                    response => {
                        this.history.nurse = response.rows
                        this.nurse_load.count = response.iTotal
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('Error GET nurse/getHistoryNurse at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET nurse/getHistoryNurse at: ', error);
            }
        },

        loadMoreNurse() {
            this.nurse_load.limit_display += this.nurse_load.add_display
            this.getNurseHistory(this.regData.norm)
        },

        async getSoapHistory(norm) {
            const param = {
                norm: norm,
                limit: this.soap_load.limit_display
            }
            try {
                await this.$store.dispatch('soap/getHistorySOAP', param)
                .then(
                    response => {
                        this.history.soap = response.rows
                        this.soap_load.count = response.iTotal
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('Error GET soap/getHistorySOAP at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET soap/getHistorySOAP at: ', error);
            }
        },

        loadMoreSoap() {
            this.soap_load.limit_display += this.soap_load.add_display
            this.getSoapHistory(this.regData.norm)
        },

        async getLaboratoryHistory(norm) {
            const param = {
                norm: norm,
                limit: this.lab_load.limit_display
            }
            try {
                await this.$store.dispatch('laboratory/getLaboratoryHistoryV2', param)
                .then(response => {
                    this.history.laboratory = response.rows
                    this.lab_load.count = response.iTotal
                })
                .catch(({message, debug, response}) => {
                    console.error('Error GET laboratory/getLaboratoryHistoryV2 at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET laboratory/getLaboratoryHistoryV2 at: ', error)
            }
        },

        loadMoreLaboratory() {
            this.lab_load.limit_display += this.lab_load.add_display
            this.getLaboratoryHistory(this.regData.norm)
        },

        async getRadiologyHistory(norm) {
            const param = {
                norm: norm,
                limit: this.rad_load.limit_display
            }
            try {
                await this.$store.dispatch('radiology/getRadiologyHistoryV2', param)
                .then(response => {
                    this.history.radiology = response.rows
                    this.rad_load.count = response.iTotal
                })
                .catch(({message, debug, response}) => {
                    console.error('Error GET radiology/getRadiologyHistoryV2 at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET radiology/getRadiologyHistoryV2 at: ', error)
            }
        },

        loadMoreRadiology() {
            this.rad_load.limit_display += this.rad_load.add_display
            this.getRadiologyHistory(this.regData.norm)
        },

        async getPhysiotherapyHistory(norm) {
            const param = {
                norm: norm,
                limit: this.phy_load.limit_display
            }
            try {
                await this.$store.dispatch('physiotherapy/getPhysiotherapyHistoryV2', param)
                .then(response => {
                    this.history.physiotherapy = response.rows
                    this.phy_load.count = response.iTotal
                })
                .catch(({message, debug, response}) => {
                    console.error('Error GET physiotherapy/getPhysiotherapyHistoryV2 at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET physiotherapy/getPhysiotherapyHistoryV2 at: ', error)
            }
        },

        loadMorePhysiotherapy() {
            this.phy_load.limit_display += this.phy_load.add_display
            this.getPhysiotherapyHistory(this.regData.norm)
        },

        async getResumeList(norm) {
            const param = {
                norm: norm,
                limit: this.rsm_load.limit_display,
            }

            try {
                await this.$store.dispatch('resume/getAllList', param)
                .then(response => {
                    this.history.resume = response.rows
                    this.rsm_load.count = response.iTotal
                })
                .catch(({message, debug, response}) => {
                    console.error('Error GET resume/getAllList at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET resume/getAllList at: ', error)
            }
        },

        loadMoreResume() {
            this.rsm_load.limit_display += this.rsm_load.add_display
            this.getResumeList(this.regData.norm)
        },

        async getExaminationList(norm) {
            const param = {
                norm: norm,
                limit: this.exa_load.limit_display,
            }

            try {
                await this.$store.dispatch('soap/getExaminationList', param)
                .then(response => {
                    this.history.examination = response.rows
                    this.exa_load.count = response.iTotal
                })
                .catch(({message, debug, response}) => {
                    console.error('Error GET soap/getExaminationList at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET soap/getExaminationList at: ', error)
            }
        },

        loadMoreExamination() {
            this.exa_load.limit_display += this.exa_load.add_display
            this.getExaminationList(this.regData.norm)
        },

        isPdfFile(filename) {
            if (!filename || typeof filename !== 'string') return false;
            const last4Chars = filename.slice(-4).toLowerCase();
            return last4Chars === '.pdf';
        },

        expandImage(url){
            const param = this.urlbe + url;
            const newWindow = window.open('', '_blank');

            newWindow.document.write(`
                <html>
                    <head>
                        <title>Image Preview</title>
                        <style>
                            body, html {
                                margin: 0;
                                padding: 0;
                                height: 100%;
                                width: 100%;
                            }
                            img {
                                width: 100%;
                                height: auto;
                            }
                        </style>
                    </head>
                    <body>
                        <img src="${param}" alt="Image Preview">
                    </body>
                </html>
            `);
            newWindow.document.close();
        },

        async getOptSignItemDose(value) {
            let otherObj = { value: 'other', title: 'Lainnya' }
            this.temp_presc_items.signatura_dose = ''

            if (value) {
                try {
                    await this.$store.dispatch('kardex/getOptSignaturaDose', { package_id: value } )
                    .then(response => {
                        this.optSignItemDose = response
                        if (this.optSignItemDose?.length == 1) this.temp_presc_items.signatura_dose = response[0]
                    })
                    .catch(({message, debug, response}) => {
                        console.error('Error at getOptSignItemDose: ', response);
                    })
                } catch (error) {
                    console.error('Error catch at getOptSignItemDose: ', error)
                }
            } else {
                this.optSignItemDose = [otherObj]
                this.temp_presc_items.signatura_dose = otherObj
            }
        },

        //  ===================================

        openHRPreParent(event) {
            if (event == null) return

            // this.prescription_type = event
            let index = this.home_recipe.form_prescription_parent.findIndex((item) => item.id == event.value);
            if (index !== -1) return;
            // if (index !== -1) return console.log("Object already exists");

            if (event.value != 1) {
                this.temp_hrParent.parent_id = event.value
                this.temp_hrParent.parent_name = event.title
                this.temp_hrParent.signatura = ''
                this.temp_hrParent.other_signatura = ''
                this.temp_hrParent.consume_time = ''
                this.temp_hrParent.utilization = ''
                this.temp_hrParent.other_utilization = ''
                this.temp_hrParent.package = ''
                this.active.modal.hr_parent = true
                return
            }

            this.home_recipe.form_prescription_parent.push({
                id: event.value,
                prescription: event.title,
                used: '',
                value: event.value == 1 ? 0 : 1,
                package: '',
            })
        },

        closeHRPreParent() {
            this.active.modal.hr_parent = false
            this.hrStock = ''
            this.hrCatalog = ''
        },

        addHRParent() {
            let con_signatura = this.temp_hrParent.signatura == 'other' ? this.temp_hrParent.other_signatura : this.temp_hrParent.signatura
            let con_utilization = this.temp_hrParent.utilization == 'other' ? this.temp_hrParent.other_utilization : this.temp_hrParent.utilization

            this.home_recipe.form_prescription_parent.push(
                {
                    id: this.temp_hrParent.parent_id,
                    prescription: this.temp_hrParent.parent_name,
                    used: `${con_signatura} ${this.temp_hrParent.consume_time} ${con_utilization}`,
                    value: 1,
                    package: this.temp_hrParent.package
                }
            )
            this.active.modal.hr_parent = false
        },

        onSearchHRCatalog(search, loading) {
            if (search.length >= 3) {
                loading(true)
                const searchTerm = search.toLowerCase();
                this.optHrCatalog = this.catalog_list.filter(item => {
                    return item?.data_catalog?.composition.toLowerCase().includes(searchTerm) ||
                    item?.title.toLowerCase().includes(searchTerm)
                });
                loading(false)
            }
        },

        getConsumeTime(obj) {
            if (obj.morning === 1 && obj.afternoon === 1 && obj.evening == 0) return '';

            const times = [
                obj.morning ? 'Pagi' : '',
                obj.afternoon ? 'Siang' : '',
                obj.evening ? 'Malam' : ''
            ].filter(Boolean);

            return times.length ? times.join('-') : '';
        },

        openHRPreItem (event) {
            this.hrCatalog = ''
            if (this.hrStock == '' || this.hrStock == null) return alert("Silakan Pilih Sediaan Terlebih Dahulu")

            if (this.hrStock.value != 1) {
                this.home_recipe.form_prescription.push({
                    code: event?.data_catalog?.code,
                    name: event?.data_catalog?.name,
                    pharmacy_catalog_id: event?.data_catalog?.id,
                    package_id: event?.data_catalog?.package_id,
                    dose: event?.data_catalog?.dose,
                    sum_dose: event?.data_catalog?.dose+' '+event?.data_catalog?.dose_name,
                    package_name: event?.data_catalog?.package_name,
                    prescription_type_id: this.hrStock.value,
                    prescription_type_name: this.hrStock.title,
                    value_doctor: 0,
                    value_package: 1,
                    used: '',
                    desc: '',
                    sort: this.home_recipe.form_prescription.length,
                })
                return
            }

            this.temp_hrItems.parent_id = ''
            this.temp_hrItems.parent_name = ''
            this.temp_hrItems.catalog_name = ''
            this.temp_hrItems.signatura = ''
            this.temp_hrItems.other_signatura = ''
            this.temp_hrItems.consume_time = ''
            this.temp_hrItems.utilization = ''
            this.temp_hrItems.other_utilization = ''
            this.temp_hrItems.data_catalog = {}

            let temp_pharmacy_catalog_id = event?.data_catalog?.id
            try {
                this.$store.dispatch('catalog/getCatalogReference', temp_pharmacy_catalog_id).then((response) => {
                    if (response?.length > 0) {
                        this.temp_hrItems.parent_id = this.hrStock.value
                        this.temp_hrItems.parent_name = this.hrStock.title
                        this.temp_hrItems.data_catalog = event?.data_catalog
                        this.temp_hrItems.catalog_name = event?.data_catalog?.name
                        this.temp_hrItems.consume_time = this.getConsumeTime({
                            morning: response[0]?.morning,
                            afternoon: response[0]?.afternoon,
                            evening: response[0]?.evening,
                        })

                        let res_signatura = response[0]?.signatura
                        if (res_signatura) {
                            const is_inOption = this.OptionsSignatura.find((item) => item.value == res_signatura);
                            this.temp_hrItems.signatura = is_inOption ? res_signatura : 'other'
                            this.temp_hrItems.other_signatura = is_inOption ? '' : res_signatura
                        } else {
                            this.temp_hrItems.signatura = ''
                        }

                    } else {
                        this.temp_hrItems.parent_id = this.hrStock.value
                        this.temp_hrItems.parent_name = this.hrStock.title
                        this.temp_hrItems.data_catalog = event?.data_catalog
                        this.temp_hrItems.catalog_name = event?.data_catalog?.name
                    }

                    this.active.modal.hr_items = true
                    // console.log('this.temp_hrItems: ', this.temp_hrItems);
                }, error => {
                    console.error('Error while GET catalog/getCatalogReference at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET catalog/getCatalogReference at: ', error);
            }
        },

        addHRPrescItem() {
            let con_signatura = this.temp_hrItems.signatura == 'other' ? this.temp_hrItems.other_signatura : this.temp_hrItems.signatura
            let con_utilization = this.temp_hrItems.utilization == 'other' ? this.temp_hrItems.other_utilization : this.temp_hrItems.utilization

            this.home_recipe.form_prescription.push({
                code: this.temp_hrItems?.data_catalog?.code,
                name: this.temp_hrItems?.data_catalog?.name,
                pharmacy_catalog_id: this.temp_hrItems?.data_catalog?.id,
                package_id: this.temp_hrItems?.data_catalog?.package_id,
                dose: this.temp_hrItems?.data_catalog?.dose,
                sum_dose: this.temp_hrItems?.data_catalog?.dose+' '+this.temp_hrItems?.data_catalog?.dose_name,
                package_name: this.temp_hrItems?.data_catalog?.package_name,
                prescription_type_id: this.temp_hrItems.parent_id,
                prescription_type_name: this.temp_hrItems.parent_name,
                value_doctor: 0,
                value_package: 1,
                used: `${con_signatura || ''}`,
                desc: `${this.temp_hrItems.consume_time} ${con_utilization}`,
                sort: this.home_recipe.form_prescription.length,
            })
            this.active.modal.hr_items = false
        },

        removeHRItem(index) {
            this.home_recipe.form_prescription.splice(index, 1)
        },

        async getSignaturaUnits(package_id) {
            this.optItemSignUnits = []
            let param = {
                package_id: package_id
            }
            try {
                await this.$store.dispatch('kardex/getOptUnit', param)
                .then(
                    async response => {
                        this.optItemSignUnits = response
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
    watch: {
        'itemsPhysiotherapy': function(val){
            if (val) {
                this.supportPackage.phy_package_list = val
            }
        },

        'OptionsCosting': function(val){
            if (val) {
                this.getCosting = val
            }
        },
        "temp_hrParent.signatura"(newValue) {
            if (newValue != 'other') {
                this.temp_hrParent.other_signatura = ''
            }
        },
        "temp_hrParent.utilization"(newValue) {
            if (newValue != 'other') {
                this.temp_hrParent.other_utilization = ''
            }
        },
        "temp_hrItems.signatura"(newValue) {
            if (newValue != 'other') {
                this.temp_hrItems.other_signatura = ''
            }
        },
        "temp_hrItems.utilization"(newValue) {
            if (newValue != 'other') {
                this.temp_hrItems.other_utilization = ''
            }
        },
        "temp_presc_parent.administer"(newValue) {
            if (newValue != this.other_administer) {
                this.temp_presc_parent.desc = ''
            }
        },
        "temp_presc_items.administer"(newValue) {
            if (newValue != this.other_administer) {
                this.temp_presc_items.desc = ''
            }
        },
    },
    created () {
        if (this.$route.params.id) {
            this.reg_des_id = this.$route.params.id
        }

        this.$store.dispatch('employee/getParamSpecialist')
        }

}
</script>
<style scoped>
    ::v-deep(.custom-tab-style .nav-link) {
        padding: 8px 10px 6px !important;
    }

    .load-text{
        color: blue;
    }

    .load-text:hover{
        color: darkblue;
        text-decoration: underline;
        background-color: antiquewhite;
        width: 100%;
    }

    .bg-diagonal {
        background-color: #ffffff;
        opacity: 1;
        background-size: 8px 8px;
        background-image: repeating-linear-gradient(45deg, #d4d4ff 0, #d4d4ff 0.8px, #ffffff 0, #ffffff 50%);
    }

    input.kx-input::-webkit-outer-spin-button,
    input.kx-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .drag-handle {
        cursor: move;
        font-size: 18px;
        margin-right: 8px;
    }
</style>