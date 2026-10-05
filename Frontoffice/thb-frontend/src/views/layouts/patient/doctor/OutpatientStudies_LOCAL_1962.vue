<template>
    <div>
        <b-modal v-model="active.modal.main" scrollable size="xl" no-close-on-esc no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ASESMEN POLIKLINIK
                    <span class="text-primary"> {{ temp_header.alias_name }}. {{ temp_header.name }} | {{ temp_header.norm }} | {{localDate(temp_header.birth_date)}} ({{ parsedAge(temp_header.birth_date) }}) | BB: {{temp_header.weight | number}} KG | TB: {{ temp_header.height | number}} CM</span>
                </div>
                <feather-icon icon="XIcon" @click="closeMainModal('close')" size="18" class="pointer" />
            </template>

            <div class="row position-relative">
                <div class="col-4">
                    <div class="row">
                        <section class="col-8 position-sticky px-1" :style="{top: '0', height: `${dynamicHeight}`, overflowY: 'auto'}">
                            <ModernLabel label="REFERENSI ASESMEN PASIEN" class="mb-2"/>
                            <b-tabs small content-class="mt-3 text-regular-12" nav-wrapper-class="fs-13" nav-class="custom-tab-style">
                                <b-tab title="DOKTER" active>
                                    <div v-for="(item, index) in soap_history" class="accordion" role="tablist">
                                        <b-card no-body class="mb-1">
                                            <b-card-header block v-b-toggle="'accordion-'+index" header-tag="header" class="pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                                <div>
                                                    <div class="font-weight-bold">ASESMEN {{ localDateTime(item.date_added) }}</div>
                                                    <div>{{ item.doctor_in_charge_name }} | {{ item.health_facility_item_name }}</div>
                                                </div>
                                                <feather-icon icon="ChevronDownIcon" size="18" />
                                            </b-card-header>
                                            <b-collapse :id="`accordion-`+index" accordion="my-accordion" role="tabpanel">
                                                <b-card-body class="p-12">
                                                    <!-- <b-button @click="copyAllSoap($event, item)" size="sm" variant="primary" class="btn-block">COPY ALL</b-button> -->
                                                    <section class="card">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">ANAMNESA</div>
                                                                <b-button size="sm" @click="copySelected($event, soapAnamnesa, item, 'ANAMNESA')" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div><b>Anamnesa: </b>{{ item.anamesa_type_id == '1' ? 'Auto Anamnesa' : 'Allo Anamnesa' }}</div>
                                                            <div><b>Keluhan Utama: </b>{{ item?.main_complaint || '-' }}</div>
                                                            <div><b>Nama Keluarga: </b>{{ item?.family_name || '-' }}</div>
                                                            <div><b>Hubungan Keluarga: </b>{{ getRelation(item?.family_relation_id || 0) }}</div>
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
                                                                <b-button @click="copySelected($event, soapVitalSign, item, 'VITAL')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                                    <section class="card">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">ST GENERALIS</div>
                                                                <b-button size="sm" @click="copySelected($event, soapLocalis, item, 'GENERALIS')" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                                                <b-button @click="copySelected($event, soapDiagnosis, item, 'DIAGNOSIS')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                                                <b-button @click="copySelected($event, soapProcedure, item, 'PROCEDURE')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                                                <b-button @click="copySelected($event, soapLaboratory, item, 'LABORATORY')" v-if="lab_status == 1" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                                                        <!-- <template v-if="unit.result?.length > 0">
                                                                            <table class="table table-sm" style="font-size: 10px;">
                                                                                <tr><th>Flag</th><th>Nilai</th><th>Rujukan</th><th>Satuan</th></tr>
                                                                                <tr v-for="results in unit.result">
                                                                                    <td>{{results.flag}}</td>
                                                                                    <td>{{results.hasil}}</td>
                                                                                    <td>{{results.n_rujukan}}</td>
                                                                                    <td>{{results.satuan}}</td>
                                                                                </tr>                                                                    
                                                                            </table>                                                                    
                                                                        </template> -->
                                                                    </li>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </section>
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">RADIOLOGI</div>
                                                                <b-button @click="copySelected($event, soapRadiology, item, 'RADIOLOGY')" v-if="rad_status == 1" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </section>
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">FISIOTERAPI</div>
                                                                <b-button @click="copySelected($event, soapPhysiotherapy, item, 'PHYSIOTHERAPY')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                                                <b-button @click="copySelected($event, soapPrescription, item, 'PRESC')" v-if="presc_status == 0" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div v-if="item?.prescription" class="mt-1">
                                                                <template v-for="(list, presc_i) in item.prescription">
                                                                    <div :class="presc_i > 0 ? 'mt-2' : ''">{{ list?.no_order }}</div>
                                                                    <li v-for="unit in list.form_prescription">{{ unit?.pharmacy_catalog_name }}</li>
                                                                </template>
                                                            </div>
                                                            <template v-else>-</template>
                                                        </div>
                                                    </section>
                                                </b-card-body>
                                            </b-collapse>
                                        </b-card>
                                    </div>
                                </b-tab>
                                <b-tab title="PERAWAT">
                                    <div v-for="(item, index) in nurse_history" class="accordion" role="tablist">
                                        <b-card no-body class="mb-1">
                                            <b-card-header block v-b-toggle="'accordion-'+index" header-tag="header" class="pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                                <div>
                                                    <div class="font-weight-bold">PERAWAT {{ localDateTime(item.date_added) }}</div>
                                                    <div>{{ item.employee_nurse_name }} | {{ item.health_facility_item_name }}</div>
                                                </div>
                                                <feather-icon icon="ChevronDownIcon" size="18" />
                                            </b-card-header>
                                            <b-collapse :id="`accordion-`+index" accordion="my-accordion" role="tabpanel">
                                                <b-card-body class="p-12">
                                                    <!-- <b-button @click="copyAllNurse($event, item)" size="sm" variant="primary" class="btn-block">COPY ALL</b-button> -->
                                                    <section class="card">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">ANAMNESA</div>
                                                                <b-button @click="copySelected($event, nurseAnamnesa, item, 'ANAMNESA')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div><b>Anamnesa: </b>{{ item.anamesa_type_id == '1' ? 'Auto Anamnesa' : 'Allo Anamnesa' }}</div>
                                                            <div><b>Keluhan Utama: </b>{{ item?.main_complaint || '-' }}</div>
                                                            <div><b>Nama Keluarga: </b>{{ item?.family_name || '-' }}</div>
                                                            <div><b>Hubungan Keluarga: </b>{{ getRelation(item?.family_relation_id || 0) }}</div>
                                                            <div><b>R.Penyakit Sekarang: </b>{{ item?.history_disease_now || '-' }}</div>
                                                            <div><b>R.Penyakit Dahulu: </b>{{ item?.history_disease_past || '-' }}</div>
                                                        </div>
                                                    </section>
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">TANDA VITAL</div>
                                                                <b-button @click="copySelected($event, nurseVitalSign, item, 'VITAL')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                </b-tab>
                                <b-tab title="CPPT">
                                    <div v-for="(item, index) in cppt_history" class="accordion" role="tablist">
                                        <b-card no-body class="mb-1">
                                            <b-card-header block v-b-toggle="'accordion-'+index" header-tag="header" class="pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                                <div>
                                                    <div class="font-weight-bold">CPPT {{ localDateTime(item.date_added) }}</div>
                                                    <div>{{ item.user_added_name }} | {{ item.user_added_role }}</div>
                                                </div>
                                                <feather-icon icon="ChevronDownIcon" size="18" />
                                            </b-card-header>
                                            <b-collapse :id="`accordion-`+index" accordion="my-accordion" role="tabpanel">
                                                <b-card-body class="p-12">
                                                    <!-- <b-button @click="copyAllCppt($event, item)" size="sm" variant="primary" class="btn-block">COPY ALL</b-button> -->
                                                    <section class="card">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">SUBJECTIVE</div>
                                                                <!-- <b-button size="sm" @click="copySelected($event, cpptAnamnesa, item, 'ANAMNESA')" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button> -->
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div><b>Subjective: </b>{{ item?.subjective }}</div>
                                                        </div>
                                                    </section>
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">TANDA VITAL</div>
                                                                <b-button @click="copySelected($event, cpptVitalSign, item, 'VITAL')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                                                <b>BB: </b>{{ item.weight }},
                                                                <b>TB: </b>{{ item.height }},
                                                                <b>IMT: </b>{{ item.imt }},
                                                            </div>
                                                        </div>
                                                    </section>
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">ST GENERALIS</div>
                                                                <b-button size="sm" @click="copySelected($event, cpptLocalis, item, 'GENERALIS')" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                                                <b-button @click="copySelected($event, cpptDiagnosis, item, 'DIAGNOSIS')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                                                <b-button @click="copySelected($event, cpptProcedure, item, 'PROCEDURE')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div><b>Plan Non-Farmakologis: </b>{{ item?.plan || '-' }}</div>
                                                        </div>
                                                    </section>
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">LABORATORIUM</div>
                                                                <b-button @click="copySelected($event, cpptLaboratory, item, 'LABORATORY')" v-if="lab_status == 1" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </section>
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">RADIOLOGI</div>
                                                                <b-button @click="copySelected($event, cpptRadiology, item, 'RADIOLOGY')" v-if="rad_status == 1" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </section>
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">FISIOTERAPI</div>
                                                                <b-button @click="copySelected($event, cpptPhysiotherapy, item, 'PHYSIOTHERAPY')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                                                <b-button @click="copySelected($event, cpptPrescription, item, 'PRESC')" v-if="presc_status == 0" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div v-if="item?.prescription" class="mt-1">
                                                                <template v-for="(list, presc_i) in item.prescription">
                                                                    <div :class="presc_i > 0 ? 'mt-2' : ''">{{ list?.no_order }}</div>
                                                                    <li v-for="unit in list.form_prescription">{{ unit?.pharmacy_catalog_name }}</li>
                                                                </template>
                                                            </div>
                                                            <template v-else>-</template>
                                                        </div>
                                                    </section>
                                                </b-card-body>
                                            </b-collapse>
                                        </b-card>
                                    </div>
                                </b-tab>
                                <b-tab title="TEMPLATE">
                                    <b-card no-body class="mb-1">
                                        <b-card-header block @click="active.advance.template_anam = !active.advance.template_anam" header-tag="header" class="pointer d-flex justify-content-between align-items-center" :class="active.advance.template_anam ? null : 'collapsed'" :aria-expanded="active.advance.template_anam ? 'true' : 'false'" aria-controls="collapse-temp-anamn" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                            <div>
                                                <div class="font-weight-bold">TEMPLATE ANAMNESA</div>
                                            </div>
                                            <feather-icon icon="ChevronDownIcon" size="18" />
                                        </b-card-header>
                                        <b-collapse id="collapse-temp-anamn" v-model="active.advance.template_anam" accordion="my-accordion" role="tabpanel">
                                            <b-card-body class="p-12">
                                                <b-form-input v-model="anamn_search" @input="filterItemAnamn" size="sm" placeholder="Cari berdasarkan nama template..." />
                                                
                                                <template v-if="anamn_filtered?.length > 0">
                                                    <section v-for="(item, index) in anamn_filtered" class="card mt-10">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">{{ item.name }}</div>
                                                                <b-button @click="copySelected($event, templateAnamnesa, item, 'ANAMNESA')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                        <b-card-header block @click="active.advance.template_presc = !active.advance.template_presc" header-tag="header" class="pointer d-flex justify-content-between align-items-center" :class="active.advance.template_presc ? null : 'collapsed'" :aria-expanded="active.advance.template_presc ? 'true' : 'false'" aria-controls="collapse-temp-presc" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                            <div>
                                                <div class="font-weight-bold">TEMPLATE RESEP</div>
                                            </div>
                                            <feather-icon icon="ChevronDownIcon" size="18" />
                                        </b-card-header>
                                        <b-collapse id="collapse-temp-presc" v-model="active.advance.template_presc" accordion="my-accordion" role="tabpanel">
                                            <b-card-body class="p-12">
                                                <template v-if="presc_list_templating?.length > 0">
                                                    <b-form-input v-model="presc_search" @input="filterItemPresc" size="sm" placeholder="Cari berdasarkan nama template..." />
                                                    <!-- <section v-for="(item, index) in presc_list_templating" class="card" :class="index > 0 ? 'mt-10' : ''"> -->
                                                    <section v-for="(item, index) in presc_filtered" class="card mt-10">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">{{ item.name }}</div>
                                                                <b-button @click="copySelected($event, templatePrescription, item, 'PRESC')" v-if="presc_status == 0" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                            </div>
                                                        </div>
                                                        <div class="card-body pb-12 pt-0 px-0">                                                                    
                                                            <template v-if="item?.form_prescription_parent">
                                                                <div v-for="(list, idx_p) in item.form_prescription_parent" :class="idx_p > 0 ? 'mt-12' : ''">
                                                                    <div :class="getParentVariant(list.id)" class="px-12 py-1">
                                                                        <div class="font-weight-bold">{{ getParentName(list.id) }}</div>
                                                                        <div v-if="list.id != 1" class="fs-12 text-italic" style="font-weight: 400">  
                                                                            <span>Jumlah: {{ list.value }}, </span>
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
                        <div class="col-4 bg-primary rounded py-16 px-2 position-sticky" :style="{top: '0', height: `${dynamicHeight}`, overflowY: 'auto'}">
                            <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-0" @click="changeTab('ANAMNESA')" :variant="getMenusVariant('ANAMNESA')">ANAMNESA</b-button>
                            <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('VITAL')" :variant="getMenusVariant('VITAL')">TANDA VITAL</b-button>
                            <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('GENERALIS')" :variant="getMenusVariant('GENERALIS')">ST GENERALIS</b-button>
                            <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('DIAGNOSIS')" :variant="getMenusVariant('DIAGNOSIS')">DIAGNOSA</b-button>
                            <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('PROCEDURE')" :variant="getMenusVariant('PROCEDURE')">PROSEDUR</b-button>
                            <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('LABORATORY')" :variant="getMenusVariant('LABORATORY')">LABORATORIUM</b-button>
                            <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('RADIOLOGY')" :variant="getMenusVariant('RADIOLOGY')">RADIOLOGI</b-button>
                            <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('PHYSIOTHERAPY')" :variant="getMenusVariant('PHYSIOTHERAPY')">FISIOTERAPI</b-button>
                            <template v-if="regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e'">
                                <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-2" @click="changeTab('HEMODIALYSIS')" :variant="getMenusVariant('HEMODIALYSIS')">HEMODIALISA</b-button>
                            </template>
                            <template v-if="regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a'">
                                <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-2" @click="changeTab('REHABILITATION')" :variant="getMenusVariant('REHABILITATION')">UJI FUNGSI</b-button>
                            </template>
                            <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('PRESC')" :variant="getMenusVariant('PRESC')">RESEP</b-button>
                        </div>
                    </div>
                </div>
                <div class="col-8 px-1">
                    <section v-if="active.section == 'ANAMNESA'" class="card">
                        <div class="card-header bg-primary d-flex justify-content-between">
                            <span class="text-white fs-14 font-weight-bold">ANAMNESA</span>
                            <b-button @click="openAsAnamnTemplate()" size="sm" variant="orange-500" v-b-popover.hover.bottom="'Simpan Sebagai Template'">
                                <feather-icon icon="ArchiveIcon" />
                            </b-button>
                        </div>
                        <div class="card-body px-20 py-8 position-sticky" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <div class="row">
                                <div class="col-lg-3">
                                    <label class="fs-13 mb-0">Anamnesa</label>
                                    <v-select class="select-size-sm" v-model="form_anamnesa.anamesa_type_id" :options="OptionsAnamesaType" :reduce="item => item.value" label="title" placeholder="-- Pilih Anamnesa --"></v-select>
                                </div>
                                <template v-if="form_anamnesa.anamesa_type_id == 2">
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Nama Keluarga</label>
                                        <b-form-input type="text" size="sm" v-model="form_anamnesa.family_name" autocomplete="off"></b-form-input>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Hubungan Keluarga</label>
                                        <v-select v-model="form_anamnesa.family_relation_id" class="select-size-sm" :options="OptionsFamilyRelation" :reduce="OptionsFamilyRelation => OptionsFamilyRelation.value" label="title" placeholder="-- Pilih --"></v-select>
                                    </div>
                                </template>
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Keluhan Utama</label>
                                    <b-form-input size="sm" v-model="form_anamnesa.main_complaint" autocomplete="off"></b-form-input>
                                </div>
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Riwayat Penyakit Sekarang</label>
                                    <textarea v-model.lazy="form_anamnesa.history_disease_now" rows="3" class="form-control form-control-sm" autocomplete="off" />
                                </div>
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Riwayat Penyakit Dahulu</label>
                                    <textarea v-model.lazy="form_anamnesa.history_disease_past" rows="3" class="form-control form-control-sm" autocomplete="off" />
                                </div>
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Riwayat Obat</label>
                                    <textarea v-model.lazy="form_anamnesa.history_drugs" rows="3" class="form-control form-control-sm" autocomplete="off" />
                                </div>
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Riwayat Alergi</label>
                                    <b-table-simple bordered small fixed class="text-center">
                                        <b-tr class="fs-13 ">
                                            <b-th class="align-middle" style="width: 40px;">
                                                <b-button @click="addAllergy" variant="primary" size="sm">
                                                    <feather-icon icon="PlusIcon" />
                                                </b-button>
                                            </b-th>
                                            <b-th class="align-middle" style="width: 40px;">No</b-th>
                                            <b-th class="align-middle text-left">Alergi</b-th>
                                        </b-tr>
                                        <b-tr v-for="(item, index) in form_anamnesa.allergies" class="fs-12">
                                            <b-td>
                                                <b-button @click="removeAllergy(index)" variant="outline-danger-500" size="sm">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </b-td>
                                            <b-td class="align-middle">{{ index+1 }}</b-td>
                                            <b-td>
                                                <b-form-input size="sm" v-model="item.name" autocomplete="off" />                                                
                                            </b-td>
                                        </b-tr>
                                    </b-table-simple>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <b-button-group size="sm">
                                <b-button @click="changeSection(active.section, 'VITAL')" :disabled="active.disabled.section" variant="w3green">
                                    Simpan & lanjutkan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                            </b-button-group>
                        </div>
                    </section>

                    <section v-if="active.section == 'VITAL'" class="card">
                        <div class="card-header bg-primary text-white fs-14 font-weight-bold">VITAL</div>
                        <div class="card-body px-20 py-8 position-sticky" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <div class="">
                                <ModernLabel label="Tanda Vital" />
                                <div class="row mt-8">
                                    <div class="col-lg-3">
                                        <label class="fs-13 mb-0">Systolic <span class="text-danger">*</span></label>
                                        <b-input-group size="sm" append="mmHg">
                                            <b-form-input type="number" autocomplete="off" v-model="form_vital.systolic_blood_pressure"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Diastolic <span class="text-danger">*</span></label>
                                        <b-input-group size="sm" append="mmHg">
                                            <b-form-input type="number" autocomplete="off" v-model="form_vital.diastolic_blood_pressure"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Frekuensi Nadi <span class="text-danger">*</span></label>
                                        <b-input-group size="sm" append="x/mnt">
                                            <b-form-input type="number" autocomplete="off" v-model="form_vital.pulse_frequency"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Frekuensi Nafas <span class="text-danger">*</span></label>
                                        <b-input-group size="sm" append="x/mnt">
                                            <b-form-input type="number" autocomplete="off" v-model="form_vital.breath_frequency"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Saturasi Oksigen <span class="text-danger">*</span></label>
                                        <b-input-group size="sm" append="%">
                                            <b-form-input type="number" autocomplete="off" v-model="form_vital.oximetry_frequency"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Suhu <span class="text-danger">*</span></label>
                                        <b-input-group size="sm" append="ᵒC">
                                            <b-form-input type="number" autocomplete="off" v-model="form_vital.temperature"></b-form-input>
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
                                </div>
                            </div>

                            <div class="mt-32">
                                <ModernLabel label="Antropometri" />
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
                                            <b-form-input type="number" autocomplete="off" v-model="form_vital.imt"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Lingkar Kepala</label>
                                        <b-input-group size="sm" append="cm">
                                            <b-form-input type="number" autocomplete="off" v-model="form_vital.head_circumference"></b-form-input>
                                        </b-input-group>
                                    </div>
                                </div>
                                <b-collapse id="collapse-anto" v-model="active.advance.antopometri" class="mb-8">
                                    <div class="row">
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Lingkar Dada</label>
                                            <b-input-group size="sm" append="cm">
                                                <b-form-input type="number" autocomplete="off" v-model="form_vital.chest_circumference"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Lingkar Lengan Atas</label>
                                            <b-input-group size="sm" append="cm">
                                                <b-form-input type="number" autocomplete="off" v-model="form_vital.arm_circumference"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Lingkar Perut</label>
                                            <b-input-group size="sm" append="cm">
                                                <b-form-input type="number" autocomplete="off" v-model="form_vital.stomach_circumference"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Lingkar Lutut</label>
                                            <b-input-group size="sm" append="cm">
                                                <b-form-input type="number" autocomplete="off" v-model="form_vital.knee_circumference"></b-form-input>
                                            </b-input-group>
                                        </div>
                                    </div>
                                </b-collapse>
                                <small @click="active.advance.antopometri = !active.advance.antopometri" class="font-weight-bold text-primary pointer fs-11" :class="active.advance.antopometri ? null : 'collapsed'" :aria-expanded="active.advance.antopometri ? 'true' : 'false'" aria-controls="collapse-anto">
                                    Advance Antopometri 
                                    <b-icon :icon="active.advance.antopometri == true ? 'chevron-up' : 'chevron-down'" />
                                </small>
                            </div>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <b-button-group size="sm">
                                <b-button @click="changeTab('ANAMNESA')" :disabled="active.disabled.section" variant="outline-neutral-700">
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button @click="changeSection(active.section, 'GENERALIS')" :disabled="active.disabled.section" variant="w3green">
                                    Simpan & lanjutkan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                            </b-button-group>
                        </div>
                    </section>

                    <section v-if="active.section == 'GENERALIS'" class="card">
                        <div class="card-header bg-primary text-white fs-14 font-weight-bold">STATUS GENERALIS</div>
                        <div class="card-body px-20 py-8 position-sticky" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <div>
                                <div>
                                    <ModernLabel label="Kepala" />
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
                                    <ModernLabel label="Mata" />
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
                                    <ModernLabel label="Mulut" />
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
                                    <ModernLabel label="Leher" />
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
                                    </div>
                                </div>
                                <div class="mt-20">
                                    <ModernLabel label="Thoraks" />
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
                                    <ModernLabel label="Abdomen" />
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
                                    <ModernLabel label="Ekstrimitas" />
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
                                    <ModernLabel label="Genitalia" />
                                    <div class="mt-8 mb-8">
                                        <textarea v-model.lazy="form_generalis.genitalia" class="form-control form-control-sm" autocomplete="off" placeholder="(Tuliskan pemeriksaan genitalia)" />
                                    </div>
                                </div>
                                <div class="mt-20">
                                    <ModernLabel label="Status Lokalis" />
                                    <div class="mt-8 mb-8">
                                        <textarea v-model.lazy="form_generalis.localist_status" class="form-control form-control-sm" autocomplete="off" placeholder="(Tuliskan status lokalis di kolom ini secara lengkap)" />
                                    </div>                                                    
                                </div>
                                <div class="mt-20">
                                    <ModernLabel label="Status oftalmologis" />
                                    <div class="mt-8 mb-8">
                                        <textarea v-model.lazy="form_generalis.ophthal_status" class="form-control form-control-sm" autocomplete="off" />
                                    </div>
                                </div>
                                <!-- </b-collapse> -->

                                <!-- <small @click="active.advance.generalis = !active.advance.generalis" class="font-weight-bold text-primary pointer fs-11 mt-12" :class="active.advance.generalis ? null : 'collapsed'" :aria-expanded="active.advance.generalis ? 'true' : 'false'" aria-controls="collapse-general">
                                    Advance status generalis
                                    <b-icon :icon="active.advance.generalis == true ? 'chevron-up' : 'chevron-down'" />
                                </small> -->
                                <b-collapse id="collapse-ofta" v-model="active.advance.oftalmologis" class="mb-8">
                                    <b-table-simple small bordered fixed>
                                        <b-thead class="fs-13">
                                            <b-tr class="text-center">
                                                <b-th class="bg-warning-200">OD</b-th>
                                                <b-th class="bg-primary-100">Pemeriksaan</b-th>
                                                <b-th class="bg-warning-200">OS</b-th>
                                            </b-tr>
                                        </b-thead>
                                        <b-tbody class="fs-12">
                                            <b-tr>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_od.visus" :options="OptionsOftalVisus" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                                <b-th class="text-center bg-primary-100">Visus</b-th>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_os.visus" :options="OptionsOftalVisus" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <div>
                                                        <label class="fs-12 mb-0">Digital</label>
                                                        <v-select v-model="form_generalis.ophthal_od.tio_digital" :options="OptionsOftalTIO" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="fs-12 mb-0">Tonometri Sciotz</label>
                                                        <textarea v-model.lazy="form_generalis.ophthal_od.tio_tonometri_sciots" class="form-control form-control-sm" autocomplete="off" />
                                                    </div>
                                                </b-td>
                                                <b-th class="text-center bg-primary-100">TIO</b-th>
                                                <b-td>
                                                    <div>
                                                        <label class="fs-12 mb-0">Digital</label>
                                                        <v-select v-model="form_generalis.ophthal_os.tio_digital" :options="OptionsOftalTIO" label="title" :reduce="item => item.value" class="select-size-sm" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="fs-12 mb-0">Tonometri Sciotz</label>
                                                        <textarea v-model.lazy="form_generalis.ophthal_os.tio_tonometri_sciots" class="form-control form-control-sm" autocomplete="off" />
                                                    </div>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_od.eye_position" :options="OptionsOftalEyePosition" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                                <b-th class="text-center bg-primary-100">Posisi Mata</b-th>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_os.eye_position" :options="OptionsOftalEyePosition" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_od.eye_movement" :options="OptionsOftalMovement" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                                <b-th class="text-center bg-primary-100">Pergerakan Mata</b-th>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_os.eye_movement" :options="OptionsOftalMovement" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_od.super_silia" :options="OptionsOftalSuperSilia" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                                <b-th class="text-center bg-primary-100">Super Silia</b-th>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_os.super_silia" :options="OptionsOftalSuperSilia" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <div>
                                                        <label class="fs-12 mb-0">Edema</label>
                                                        <v-select v-model="form_generalis.ophthal_od.palpebra_edema" :options="OptionsOftalPalpebraEdema" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="fs-12 mb-0">Ptosis</label>
                                                        <v-select v-model="form_generalis.ophthal_od.palpebra_ptosis" :options="OptionsOftalPalpebraPtosis" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="fs-12 mb-0">Lagophtalmus</label>
                                                        <v-select v-model="form_generalis.ophthal_od.palpebra_lagophtalmus" :options="OptionsOftalPalpebraLagophtalmus" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="fs-12 mb-0">Tumor</label>
                                                        <v-select v-model="form_generalis.ophthal_od.palpebra_tumor" :options="OptionsOftalPalpebraTumor" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                </b-td>
                                                <b-th class="text-center bg-primary-100">Palpebra</b-th>
                                                <b-td>
                                                    <div>
                                                        <label class="fs-12 mb-0">Edema</label>
                                                        <v-select v-model="form_generalis.ophthal_os.palpebra_edema" :options="OptionsOftalPalpebraEdema" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="fs-12 mb-0">Ptosis</label>
                                                        <v-select v-model="form_generalis.ophthal_os.palpebra_ptosis" :options="OptionsOftalPalpebraPtosis" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="fs-12 mb-0">Lagophtalmus</label>
                                                        <v-select v-model="form_generalis.ophthal_os.palpebra_lagophtalmus" :options="OptionsOftalPalpebraLagophtalmus" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="fs-12 mb-0">Tumor</label>
                                                        <v-select v-model="form_generalis.ophthal_os.palpebra_tumor" :options="OptionsOftalPalpebraTumor" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_od.apparatus_lakrimalis" :options="OptionsOftalApparetus" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                                <b-th class="text-center bg-primary-100">Apparatus Lakrimalis</b-th>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_os.apparatus_lakrimalis" :options="OptionsOftalApparetus" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_od.silia" :options="OptionsOftalSilia" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                                <b-th class="text-center bg-primary-100">Silia</b-th>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_os.silia" :options="OptionsOftalSilia" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>                                                                    
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <div>
                                                        <label class="fs-12 mb-0">Hiperemis</label>
                                                        <v-select v-model="form_generalis.ophthal_od.conjunctive_hiperemis" :options="OptionsOftalConjHiperemis" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="fs-12 mb-0">Pendarahan</label>
                                                        <v-select v-model="form_generalis.ophthal_od.conjunctive_bleed" :options="OptionsOftalConjBleed" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                </b-td>
                                                <b-th class="text-center bg-primary-100">Konjungtiva</b-th>
                                                <b-td>
                                                    <div>
                                                        <label class="fs-12 mb-0">Hiperemis</label>
                                                        <v-select v-model="form_generalis.ophthal_os.conjunctive_hiperemis" :options="OptionsOftalConjHiperemis" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="fs-12 mb-0">Pendarahan</label>
                                                        <v-select v-model="form_generalis.ophthal_os.conjunctive_bleed" :options="OptionsOftalConjBleed" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_od.cornea" :options="OptionsOftalKornea" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                                <b-th class="text-center bg-primary-100">Kornea</b-th>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_os.cornea" :options="OptionsOftalKornea" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_od.camera_oculi" :options="OptionsOftalAnterior" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                                <b-th class="text-center bg-primary-100">Camera Oculi Anterior</b-th>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_os.camera_oculi" :options="OptionsOftalAnterior" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <div>
                                                        <label class="fs-12 mb-0">Warna Iris</label>
                                                        <v-select v-model="form_generalis.ophthal_od.iris_color" :options="OptionsOftalIrisColor" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="fs-12 mb-0">Kripte</label>
                                                        <v-select v-model="form_generalis.ophthal_od.iris_cripte" :options="OptionsOftalIrisKripte" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                </b-td>
                                                <b-th class="text-center bg-primary-100">Iris</b-th>
                                                <b-td>
                                                    <div>
                                                        <label class="fs-12 mb-0">Warna Iris</label>
                                                        <v-select v-model="form_generalis.ophthal_os.iris_color" :options="OptionsOftalIrisColor" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="fs-12 mb-0">Kripte</label>
                                                        <v-select v-model="form_generalis.ophthal_os.iris_cripte" :options="OptionsOftalIrisKripte" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <div>
                                                        <label class="fs-12 mb-0">Bentuk Pupil</label>
                                                        <v-select v-model="form_generalis.ophthal_od.pupil_form" :options="OptionsOftalPupilForm" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="fs-12 mb-0">Diameter Pupil</label>
                                                        <v-select v-model="form_generalis.ophthal_od.pupil_diameter" :options="OptionsOftalPupilDiameter" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="fs-12 mb-0">Refleksi Cahaya Langsung</label>
                                                        <v-select v-model="form_generalis.ophthal_od.pupil_direct_light" :options="OptionsOftalPupilDirectLight" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="fs-12 mb-0">Refleksi Cahaya Tak Langsung</label>
                                                        <v-select v-model="form_generalis.ophthal_od.pupil_indirect_light" :options="OptionsOftalPupilIndirectLight" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                </b-td>
                                                <b-th class="text-center bg-primary-100">Pupil</b-th>
                                                <b-td>
                                                    <div>
                                                        <label class="fs-12 mb-0">Bentuk Pupil</label>
                                                        <v-select v-model="form_generalis.ophthal_os.pupil_form" :options="OptionsOftalPupilForm" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="fs-12 mb-0">Diameter Pupil</label>
                                                        <v-select v-model="form_generalis.ophthal_os.pupil_diameter" :options="OptionsOftalPupilDiameter" :reduce="item => item.value" class="select-size-sm"label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="fs-12 mb-0">Refleksi Cahaya Langsung</label>
                                                        <v-select v-model="form_generalis.ophthal_os.pupil_direct_light" :options="OptionsOftalPupilDirectLight" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="mt-12">
                                                        <label class="fs-12 mb-0">Refleksi Cahaya Tak Langsung</label>
                                                        <v-select v-model="form_generalis.ophthal_os.pupil_indirect_light" :options="OptionsOftalPupilIndirectLight" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_od.lens" :options="OptionsOftalLens" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                                <b-th class="text-center bg-primary-100">Lensa</b-th>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_os.lens" :options="OptionsOftalLens" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_od.shadow_test" :options="OptionsOftalShadow" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                                <b-th class="text-center bg-primary-100">Shadow Test</b-th>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_os.shadow_test" :options="OptionsOftalShadow" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_od.glandula" :options="OptionsOftalGlandula" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                                <b-th class="text-center bg-primary-100">Glandula pre-aurikuler</b-th>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_os.glandula" :options="OptionsOftalGlandula" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_od.field_view" :options="OptionsOftalLapang" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                                <b-th class="text-center bg-primary-100">Lapang Pandang</b-th>
                                                <b-td>
                                                    <v-select v-model="form_generalis.ophthal_os.field_view" :options="OptionsOftalLapang" :reduce="item => item.value" class="select-size-sm" label="title" placeholder="-- Pilih --"></v-select>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>
                                                    <textarea v-model.lazy="form_generalis.ophthal_od.fundus" class="form-control form-control-sm" autocomplete="off" />
                                                </b-td>
                                                <b-th class="text-center bg-primary-100">Fundus</b-th>
                                                <b-td>
                                                    <textarea v-model.lazy="form_generalis.ophthal_os.fundus" class="form-control form-control-sm" autocomplete="off" />                                                                    
                                                </b-td>
                                            </b-tr>
                                        </b-tbody>
                                    </b-table-simple>
                                </b-collapse>

                                <small @click="active.advance.oftalmologis = !active.advance.oftalmologis" class="font-weight-bold text-primary pointer fs-11 mt-12" :class="active.advance.oftalmologis ? null : 'collapsed'" :aria-expanded="active.advance.oftalmologis ? 'true' : 'false'" aria-controls="collapse-ofta">
                                    Advance status oftalmologis 
                                    <b-icon :icon="active.advance.oftalmologis == true ? 'chevron-up' : 'chevron-down'" />
                                </small>
                            </div>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <b-button-group size="sm">
                                <b-button @click="changeTab('VITAL')" :disabled="active.disabled.section" variant="outline-neutral-700">
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button @click="changeSection(active.section, 'DIAGNOSIS')" :disabled="active.disabled.section" variant="w3green">
                                    Simpan & lanjutkan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                            </b-button-group>
                        </div>
                    </section>

                    <section v-if="active.section == 'DIAGNOSIS'" class="card">
                        <div class="card-header bg-primary text-white fs-14 font-weight-bold">DIAGNOSA</div>
                        <div class="card-body px-20 py-8 position-sticky" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <div class="">
                                <ModernLabel label="Diagnosa (ICD10)" />
                                <template v-if="form_diagnosis.icd10.length > 0">
                                    <b-table responsive bordered small :fields="icd10Fields" :items="form_diagnosis.icd10" class="mt-12 text-center" style="font-size: 14px;" >
                                        <template #cell(no)="row">{{ row.index+1 }}</template>
                                        <template #cell(title)="row">
                                            <div class="text-left">{{ row.value }}</div>
                                        </template>
                                        <template #cell(action)="row">
                                            <b-button variant="danger-600" size="sm" @click="delete10(row.index)" class="mr-2">
                                                <feather-icon icon="TrashIcon" />
                                            </b-button>
                                        </template>
                                    </b-table>
                                </template>                                    
                                <v-select v-model="icd10_id" @input="onAdd10($event)" aria-readonly="true" @search="onSearchIcd10" :options="OptionsIcd10" :reduce="item => item" label="title" placeholder="-- Diagnosa --" class="select-size-sm mt-12" />
                                <textarea v-model.lazy="form_diagnosis.icd10_notes" autocomplete="off" placeholder="Catatan ICD10" class="form-control form-control-sm mt-12" />
                            </div>

                            <div class="mt-32" >
                                <ModernLabel label="Diagnosis Non-ICD10" />
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
                            <b-button-group size="sm">
                                <b-button @click="changeTab('GENERALIS')" :disabled="active.disabled.section" variant="outline-neutral-700">
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button @click="changeSection(active.section, 'PROCEDURE')" :disabled="active.disabled.section" variant="w3green">
                                    Simpan & lanjutkan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                            </b-button-group>
                        </div>
                    </section>

                    <section v-if="active.section == 'PROCEDURE'" class="card">
                        <div class="card-header bg-primary text-white fs-14 font-weight-bold">PROSEDUR</div>
                        <div class="card-body px-20 py-8 position-sticky" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <section class="w-100">
                                <ModernLabel label="Tindakan Dokter" />
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
                                <ModernLabel label="Tindakan (ICD9CM)" />
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

                            <div class="mt-12">
                                <label class="fs-13 mb-0">Advice</label>
                                <textarea v-model.lazy="form_procedure.advice" rows="3" class="form-control form-control-sm" autocomplete="off" />
                            </div>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <b-button-group size="sm">
                                <b-button @click="changeTab('DIAGNOSIS')" :disabled="active.disabled.section" variant="outline-neutral-700">
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button @click="changeSection(active.section, 'LABORATORY')" :disabled="active.disabled.section" variant="w3green">
                                    Simpan & lanjutkan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                            </b-button-group>
                        </div>
                    </section>
                    
                    <section v-if="active.section == 'LABORATORY'" class="card">
                        <div class="card-header bg-primary text-white fs-14 font-weight-bold">LABORATORIUM</div>
                        <div class="card-body px-20 py-8 position-sticky" :class="lab_status != 1 ? 'bg-warning-100': ''" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
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
                                <b-form-input v-model="searchlab" placeholder="Pencarian" size="sm" class="mt-8" />
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
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <b-button-group size="sm">
                                <b-button @click="changeTab('PROCEDURE')" :disabled="active.disabled.section" variant="outline-neutral-700">
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button @click="changeSection(active.section, 'RADIOLOGY')" :disabled="active.disabled.section" variant="w3green">
                                    Simpan & lanjutkan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                            </b-button-group>
                        </div>
                    </section>

                    <section v-if="active.section == 'RADIOLOGY'" class="card">
                        <div class="card-header bg-primary text-white fs-14 font-weight-bold">RADIOLOGI</div>
                        <div class="card-body px-20 py-8 position-sticky" :class="rad_status != 1 ? 'bg-warning-100': ''" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
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
                                <b-form-input type="text" v-model="searchrad" placeholder="Pencarian" size="sm" class="mt-8" />
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
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <b-button-group size="sm">
                                <b-button @click="changeTab('LABORATORY')" :disabled="active.disabled.section" variant="outline-neutral-700">
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button @click="changeSection(active.section, 'PHYSIOTHERAPY')" :disabled="active.disabled.section" variant="w3green">
                                    Simpan & lanjutkan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                            </b-button-group>
                        </div>
                    </section>

                    <section v-if="active.section == 'PHYSIOTHERAPY'" class="card">
                        <div class="card-header bg-primary text-white fs-14 font-weight-bold">FISIOTERAPI</div>
                        <div class="card-body px-20 py-8 position-sticky" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
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
                            <b-button-group size="sm">
                                <b-button @click="changeTab('RADIOLOGY')" :disabled="active.disabled.section" variant="outline-neutral-700">
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button @click="changeSection(active.section, getNextSection())" :disabled="active.disabled.section" variant="w3green">
                                    Simpan & lanjutkan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                            </b-button-group>
                        </div>
                    </section>

                    <section v-if="active.section == 'HEMODIALYSIS'" class="card">
                        <div class="card-header bg-primary text-white fs-14 font-weight-bold">HEMODIALISA</div>
                        <div class="card-body px-20 py-8 position-sticky" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <div class="row">
                                <div class="col-lg-4">
                                    <label class="fs-13 mb-0">Diagnosis Utama</label>
                                    <v-select v-model="form_hemodialysis.main_diagnosis" :options="OptionsDiagnose" :reduce="item => item.value" label="title" class="select-size-sm" placeholder="-- Pilih --"></v-select>
                                </div>
                                <div class="col-lg-4 mt-8 mt-lg-0">
                                    <label class="fs-13 mb-0">Etiologi</label>
                                    <v-select v-model="form_hemodialysis.etiology" :options="OptionsEtiology" :reduce="item => item.value" label="title" class="select-size-sm" placeholder="-- Pilih --"></v-select>
                                </div>
                                <div class="col-lg-4 mt-8 mt-lg-0">
                                    <label class="fs-13 mb-0">Penyakit Penyerta</label>
                                    <v-select v-model="form_hemodialysis.comorbidities" :options="OptionsComorbidities" :reduce="item => item.value" label="title" class="select-size-sm" placeholder="-- Pilih --"></v-select>
                                </div>
                                <div class="col-lg-4 mt-8">
                                    <label class="fs-13 mb-0">Frekuensi HD</label>
                                    <b-input-group size="sm" append="x/minggu">
                                        <b-form-input v-model="form_hemodialysis.frequency_hd" type="number" />
                                    </b-input-group>
                                </div>
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Akses Vaskular</label>
                                    <textarea v-model.lazy="form_hemodialysis.vascular_access" rows="4" class="form-control form-control-sm" />
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <b-button-group size="sm">
                                <b-button @click="changeTab('PHYSIOTHERAPY')" :disabled="active.disabled.section" variant="outline-neutral-700">
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button @click="changeSection(active.section, 'PRESC')" :disabled="active.disabled.section" variant="w3green">
                                    Simpan & lanjutkan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                            </b-button-group>
                        </div>
                    </section>
                    <section v-if="active.section == 'REHABILITATION'" class="card">
                        <div class="card-header bg-primary text-white fs-14 font-weight-bold">UJI FUNGSI/PROSEDUR KFR</div>
                        <div class="card-body px-20 py-8 position-sticky" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <div class="row">
                                <div class="col-lg-6">
                                    <label class="fs-13 mb-0">Suspek Penyakit Akibat</label>
                                    <div class="row">
                                        <v-select v-model="form_rehabilitation.suspected_disease_type_id" :options="OptionsSuspected" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm col-lg-6"></v-select>
                                        <div class="col-lg-6 mt-10 mt-lg-0">
                                            <b-form-input v-model="form_rehabilitation.suspected_disease" size="sm" placeholder="Suspek Penyakit Akibat" v-if="form_rehabilitation.suspected_disease_type_id == '1'" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Pemeriksaan Fisik & Uji Fungsi</label>
                                    <textarea v-model.lazy="form_rehabilitation.functional_testing" class="form-control form-control-sm"/>
                                </div>
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Diagnosa Fungsional</label>
                                    <textarea v-model.lazy="form_rehabilitation.functional_diagnosis" class="form-control form-control-sm"/>
                                </div>
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Diagnosa Medis</label>
                                    <textarea v-model.lazy="form_rehabilitation.medical_diagnosis" class="form-control form-control-sm"/>
                                </div>
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Hasil Yang Didapat</label>
                                    <textarea v-model.lazy="form_rehabilitation.result" class="form-control form-control-sm"/>
                                </div>
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Kesimpulan</label>
                                    <textarea v-model.lazy="form_rehabilitation.conclusion" class="form-control form-control-sm"/>
                                </div>
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Rekomendasi</label>
                                    <textarea v-model.lazy="form_rehabilitation.recommendation" class="form-control form-control-sm"/>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <b-button-group size="sm">
                                <b-button @click="changeTab('PHYSIOTHERAPY')" :disabled="active.disabled.section" variant="outline-neutral-700">
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button @click="changeSection(active.section, 'PRESC')" :disabled="active.disabled.section" variant="w3green">
                                    Simpan & lanjutkan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                            </b-button-group>
                        </div>
                    </section>

                    <section v-if="active.section == 'PRESC'" class="card">
                        <div class="card-header bg-primary d-flex justify-content-between">
                            <span class="text-white fs-14 font-weight-bold">RESEP</span>
                            <template v-if="presc_status == 0">
                                <b-button @click="openAsPrescTemplate({ item: input_prescription.form_prescription, parent: input_prescription.form_prescription_parent })" size="sm" variant="orange-500" v-b-popover.hover.bottom="'Simpan Sebagai Template'">
                                    <feather-icon icon="ArchiveIcon" />
                                </b-button>
                            </template>
                        </div>
                        <div class="card-body px-12 py-8 position-sticky" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <template v-if="presc_status == 0">
                                <div class="row">
                                    <div class="col-lg-3 pr-0">
                                        <label class="fs-13 mb-0">Sediaan</label>
                                        <v-select v-model="prescription" @input="openPreParent($event)" :options="PrescriptionOptions" :reduce="item => item" label="title" class="select-size-sm" placeholder="sediaan"></v-select>
                                    </div>
                                    <div class="col-lg">
                                        <label class="fs-13 mb-0">Tambah Katalog</label>
                                        <v-select v-model="catalog" @input="openPreItem($event)" @search="onSearchCatalog" :options="OptionsCatalog" :reduce="item => item" label="title" class="select-size-sm" placeholder="pilih obat"></v-select>
                                    </div>
                                </div>
                                <b-table-simple fixed small class="mt-12">
                                    <b-thead class="text-center fs-12">
                                        <b-tr>
                                            <th style="width: 60px;">AKSI</th>
                                            <th style="width: 270px;">NAMA OBAT</th>
                                            <th>ATURAN PAKAI</th>
                                            <th style="width: 80px;">JUMLAH</th>
                                            <th>KEMASAN / SATUAN</th>
                                            <th style="width: 240px;">KETERANGAN</th>
                                        </b-tr>
                                    </b-thead>
                                    <template v-for="(item_p, index_p) in input_prescription.form_prescription_parent">
                                        <b-tr class="fs-11" :class="getParentVariant(item_p.id)">
                                            <b-td colspan="2" class="font-weight-bold align-middle" :class="item_p.id == 1? 'py-12' : ''">{{ item_p.prescription }}</b-td>
                                            <template v-if="item_p.id != 1">
                                                <td>
                                                    <b-form-input size="sm" v-model="item_p.used" type="text" />
                                                </td>
                                                <td>
                                                    <b-form-input size="sm" v-model="item_p.value" type="number" min="1" placeholder="jumlah" />
                                                </td>
                                                <b-td colspan="2"></b-td>
                                            </template>
                                            <template v-else>
                                                <b-td colspan="4"></b-td>
                                            </template>
                                        </b-tr>
                                        <tr class="item fs-11" v-for="(item, index) in input_prescription.form_prescription" v-if="item_p.id == item.prescription_type_id">
                                            <td class="text-center">
                                                <b-button @click="deletePRES(index)" variant="danger-600" size="sm" class="mr-2">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </td>
                                            <td>{{ item.name }}</td>
                                            <td>
                                                <b-form-input v-model="item.used" size="sm" type="text" v-if="item.prescription_type_id == 1"/>
                                                <div v-else class="text-center">-</div>
                                            </td>
                                            <td>
                                                <b-form-input size="sm" type="number" min="1" v-model="item.value_package" />
                                            </td>
                                            <td>
                                                <v-select v-model="item.package_id" :options="PackageOptions" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                            </td>
                                            <td>
                                                <b-form-input v-model="item.desc" size="sm" type="text" />
                                            </td>
                                        </tr>
                                    </template>
                                </b-table-simple>
                            </template>

                            <template v-if="presc_status == 1">
                                <b-button @click="openModalExcPresc" variant="outline-primary" size="sm">
                                    <feather-icon icon="PlusCircleIcon" class="mr-1" />
                                    <span class="fs-12">TAMBAH RESEP</span>
                                </b-button>

                                <b-table :fields="precriptionFields" :items="exclusive_presc_list" small bordered responsive class="mt-12" thead-tr-class="fs-13 text-center" :tbody-tr-class="rowIGDPrescription">
                                    <template #cell(no)="row">
                                        <div class="text-center fs-12">{{ row.index+1 }}</div>
                                    </template>
                                    <template #cell(no_order)="row">
                                        <div class="text-center fs-12">{{ row.value }}</div>
                                    </template>
                                    <template #cell(form_prescription)="row">
                                        <div v-for="(category, index) in row.item.form_prescription_parent" :key="index" class="mt-8" v-if="hasItemsInCategory(row.value, category.id)">
                                            <b-table-simple responsive small>
                                                <b-thead class="fs-13">
                                                    <b-tr>
                                                        <b-th colspan="5" :class="getParentVariant(category.id)">
                                                            <div class="text-uppercase ">{{ getParentName(category.id) }}</div>
                                                            <div v-if="category.id != 1" class="fs-12 text-italic" style="font-weight: 400">  
                                                                <span>Jumlah: {{ category.value }}, </span>
                                                                <span>Aturan pakai: {{ category.used }}</span>
                                                            </div>
                                                        </b-th>
                                                    </b-tr>
                                                    <b-tr>
                                                        <b-th>Nama</b-th>
                                                        <b-th style="width: 90px;">Jumlah</b-th>
                                                        <b-th style="width: 200px;">Aturan Pakai</b-th>
                                                        <b-th style="width: 120px;">Keterangan</b-th>
                                                    </b-tr>
                                                </b-thead>
                                                <b-tbody class="fs-12">
                                                    <template v-for="(item, itemIndex) in row.value">
                                                        <b-tr :key="itemIndex" v-if="item.prescription_item_type_id == category.id">
                                                            <b-td>{{ item.pharmacy_catalog_name }}</b-td>
                                                            <b-td>{{ item.qty }} {{ item.package_name }}</b-td>
                                                            <b-td>{{ item.used }}</b-td>
                                                            <b-td>{{ item.desc }}</b-td>
                                                        </b-tr>
                                                    </template>
                                                </b-tbody>
                                            </b-table-simple>
                                        </div>
                                    </template>
                                    
                                    <template #cell(action)="row">
                                        <div class="text-center">
                                            <div v-if="row.item.flag == 0" class="d-flex justify-content-center">
                                                <b-button @click="removeExcPresc(row.item)" size="sm" variant="danger-700">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                                <b-button @click="onEditPresc(row.item)" size="sm" variant="warning-500" class="ml-1">
                                                    <feather-icon icon="EditIcon" />
                                                </b-button>
                                            </div>
                                            <div v-else class="text-center fs-12">Resep telah diproses</div>
                                            <b-button @click="openAsPrescTemplate({ item: row.item.form_prescription, parent: row.item.form_prescription_parent })" size="sm" variant="orange-500" class="mt-8" v-b-popover.hover.bottom="'Simpan Sebagai Template'">
                                                <feather-icon icon="ArchiveIcon" />
                                            </b-button>
                                        </div>
                                    </template>
                                </b-table>
                            </template> 
                        </div>
                        <div v-if="presc_status == 0" class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <b-button-group size="sm">
                                <b-button @click="changeTab(getPrevSection())" :disabled="active.disabled.section" variant="outline-neutral-700">
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button @click="changeSection(active.section, 'PRESC')" :disabled="active.disabled.section" variant="w3green">
                                    <feather-icon icon="SaveIcon" />
                                    Simpan
                                </b-button>
                            </b-button-group>
                        </div>
                        <div v-if="presc_status == 1" class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <b-button-group size="sm">
                                <b-button @click="active.section = getPrevSection()" :disabled="active.disabled.section" variant="outline-neutral-700">
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                            </b-button-group>
                        </div>
                    </section>
                </div>
            </div>

            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <div class="col-lg-2">
                        <b-button @click="closeMainModal('save_button')" :disabled="isDisabled" size="sm" variant="orange-500" class="btn-block" :class="active.section == 'PRESC' ? 'visible' : 'invisible'">
                            <feather-icon icon="SaveIcon" class="mr-1" />
                            <span>Checkout</span>
                        </b-button>
                    </div>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal.presc_parent" size="sm" no-close-on-esc no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA AKAN MENAMBAHKAN SEDIAAN
                    <span class="text-primary">{{ temp_presc_parent.parent_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="closePreParent()" size="18" class="pointer" />
                <!-- <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" /> -->
            </template>
            <section>
                <div>
                    <label class="fs-13 mb-0">Signatura</label>
                    <v-select v-model="temp_presc_parent.signatura" :options="OptionsSignatura" :reduce="item => item.value" label="title" placeholder="signatura" class="select-size-sm"/>
                </div>
                <div v-if="temp_presc_parent.signatura == 'other'" class="mt-10">
                    <label class="fs-13 mb-0">Signatura lain</label>
                    <b-form-input v-model="temp_presc_parent.other_signatura" size="sm" autocomplete="off"/>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Waktu Penggunaan</label>
                    <v-select v-model="temp_presc_parent.consume_time" :options="OptionsConsumeTime" :reduce="item => item.value" label="title" placeholder="waktu penggunaan" class="select-size-sm"/>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Aturan Pakai</label>
                    <v-select v-model="temp_presc_parent.utilization" :options="OptionsUtilization" :reduce="item => item.value" label="title" placeholder="aturan pakai" class="select-size-sm"/>
                </div>
                <div v-if="temp_presc_parent.utilization == 'other'" class="mt-10">
                    <label class="fs-13 mb-0">Aturan Pakai lain</label>
                    <b-form-input v-model="temp_presc_parent.other_utilization" size="sm" autocomplete="off"/>
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
                    ANDA AKAN MENAMBAHKAN OBAT
                    <span class="text-primary">{{ temp_presc_items.parent_name }}-{{ temp_presc_items.catalog_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <section>
                <div>
                    <label class="fs-13 mb-0">Signatura</label>
                    <v-select v-model="temp_presc_items.signatura" :options="OptionsSignatura" :reduce="item => item.value" label="title" placeholder="signatura" class="select-size-sm"/>
                </div>
                <div v-if="temp_presc_items.signatura == 'other'" class="mt-10">
                    <label class="fs-13 mb-0">Signatura lain</label>
                    <b-form-input v-model="temp_presc_items.other_signatura" size="sm" autocomplete="off"/>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Waktu Penggunaan</label>
                    <v-select v-model="temp_presc_items.consume_time" :options="OptionsConsumeTime" :reduce="item => item.value" label="title" placeholder="waktu penggunaan" class="select-size-sm"/>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Aturan Pakai</label>
                    <v-select v-model="temp_presc_items.utilization" :options="OptionsUtilization" :reduce="item => item.value" label="title" placeholder="aturan pakai" class="select-size-sm"/>
                </div>
                <div v-if="temp_presc_items.utilization == 'other'" class="mt-10">
                    <label class="fs-13 mb-0">Aturan Pakai lain</label>
                    <b-form-input v-model="temp_presc_items.other_utilization" size="sm" autocomplete="off"/>
                </div>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="addPrescItem" size="sm" variant="primary">TAMBAHKAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal.exc_presc" size="lg" title="Order Resep" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENAMBAHKAN RESEP BARU UNTUK
                    <span class="text-primary">{{ temp_header.alias_name }}. {{ temp_header.name }} | {{ temp_header.norm }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Sediaan</label>
                    <v-select v-model="exc_prescription" @input="openEXCPreParent($event)" :options="PrescriptionOptions" :reduce="item => item" label="title" class="select-size-sm" placeholder="sediaan"></v-select>
                </div>
                <div class="col-lg">
                    <label class="fs-13 mb-0">Tambah Obat</label>
                    <v-select v-model="exc_catalog" @input="openEXCPreItem($event)" @search="onSearchEXCCatalog" :options="ExcOptionsCatalog" :reduce="item => item" label="title" class="select-size-sm" placeholder="pilih obat"></v-select>
                </div>
            </div>
            <b-table-simple fixed small class="mt-12">
                <b-thead class="text-center fs-12">
                    <b-tr>
                        <th style="width: 60px;">AKSI</th>
                        <th style="width: 270px;">NAMA OBAT</th>
                        <th>ATURAN PAKAI</th>
                        <th style="width: 100px;">JUMLAH</th>
                        <th>KEMASAN / SATUAN</th>
                        <th>KETERANGAN</th>
                    </b-tr>
                </b-thead>
                <template v-for="(item_p, index_p) in form_exclusive_presc.form_prescription_parent">
                    <b-tr class="fs-11" :class="getParentVariant(item_p.id)">
                        <b-td colspan="2" class="font-weight-bold align-middle" :class="item_p.id == 1? 'py-12' : ''">{{ item_p.prescription }}</b-td>
                        <template v-if="item_p.id != 1">
                            <td>
                                <b-form-input size="sm" v-model="item_p.used" type="text" />
                            </td>
                            <td>
                                <b-form-input size="sm" v-model="item_p.value" type="number" min="1" placeholder="jumlah" />
                            </td>
                            <b-td colspan="2"></b-td>
                        </template>
                        <template v-else>
                            <b-td colspan="4"></b-td>
                        </template>
                    </b-tr>
                    <tr class="item fs-11" v-for="(item, index) in form_exclusive_presc.form_prescription" v-if="item_p.id == item.prescription_type_id">
                        <td class="text-center">
                            <b-button @click="deleteEXCPRES(index)" variant="danger-600" size="sm" class="mr-2">
                                <feather-icon icon="TrashIcon" />
                            </b-button>
                        </td>
                        <td>{{ item.name }}</td>
                        <td class="align-middle">
                            <b-form-input v-model="item.used" size="sm" type="text" v-if="item.prescription_type_id == 1"/>
                            <div v-else class="text-center">-</div>
                        </td>
                        <td>
                            <b-form-input size="sm" type="number" min="1" v-model="item.value_package" />
                        </td>
                        <td>
                            <v-select v-model="item.package_id" :options="PackageOptions" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                        </td>
                        <td class="align-middle">
                            <b-form-input v-model="item.desc" size="sm" type="text" />
                        </td>
                    </tr>
                </template>
            </b-table-simple>
            <template #modal-footer>
                <div class="w-100 d-flex justify-content-end">
                    <b-button @click="validationExcPrescription" size="sm" :disabled="active.disabled.ecx_presc" variant="primary">SIMPAN RESEP</b-button>
                </div>
            </template>
        </b-modal>
        <b-modal v-model="active.modal.presc_exc_parent" size="sm" no-close-on-esc no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA AKAN MENAMBAHKAN SEDIAAN
                    <span class="text-primary">{{ temp_exc_parent.parent_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="closeEXCPreParent()" size="18" class="pointer" />
                <!-- <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" /> -->
            </template>
            <section>
                <div>
                    <label class="fs-13 mb-0">Signatura</label>
                    <v-select v-model="temp_exc_parent.signatura" :options="OptionsSignatura" :reduce="item => item.value" label="title" placeholder="signatura" class="select-size-sm"/>
                </div>
                <div v-if="temp_exc_parent.signatura == 'other'" class="mt-10">
                    <label class="fs-13 mb-0">Signatura lain</label>
                    <b-form-input v-model="temp_exc_parent.other_signatura" size="sm" autocomplete="off"/>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Waktu Penggunaan</label>
                    <v-select v-model="temp_exc_parent.consume_time" :options="OptionsConsumeTime" :reduce="item => item.value" label="title" placeholder="waktu penggunaan" class="select-size-sm"/>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Aturan Pakai</label>
                    <v-select v-model="temp_exc_parent.utilization" :options="OptionsUtilization" :reduce="item => item.value" label="title" placeholder="aturan pakai" class="select-size-sm"/>
                </div>
                <div v-if="temp_exc_parent.utilization == 'other'" class="mt-10">
                    <label class="fs-13 mb-0">Aturan Pakai lain</label>
                    <b-form-input v-model="temp_exc_parent.other_utilization" size="sm" autocomplete="off"/>
                </div>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="addEXCParent" size="sm" variant="primary">TAMBAHKAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal.presc_exc_items" size="sm" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA AKAN MENAMBAHKAN OBAT
                    <span class="text-primary">{{ temp_exc_items.parent_name }}-{{ temp_exc_items.catalog_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <section>
                <div>
                    <label class="fs-13 mb-0">Signatura</label>
                    <v-select v-model="temp_exc_items.signatura" :options="OptionsSignatura" :reduce="item => item.value" label="title" placeholder="signatura" class="select-size-sm"/>
                </div>
                <div v-if="temp_exc_items.signatura == 'other'" class="mt-10">
                    <label class="fs-13 mb-0">Signatura lain</label>
                    <b-form-input v-model="temp_exc_items.other_signatura" size="sm" autocomplete="off"/>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Waktu Penggunaan</label>
                    <v-select v-model="temp_exc_items.consume_time" :options="OptionsConsumeTime" :reduce="item => item.value" label="title" placeholder="waktu penggunaan" class="select-size-sm"/>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Aturan Pakai</label>
                    <v-select v-model="temp_exc_items.utilization" :options="OptionsUtilization" :reduce="item => item.value" label="title" placeholder="aturan pakai" class="select-size-sm"/>
                </div>
                <div v-if="temp_exc_items.utilization == 'other'" class="mt-10">
                    <label class="fs-13 mb-0">Aturan Pakai lain</label>
                    <b-form-input v-model="temp_exc_items.other_utilization" size="sm" autocomplete="off"/>
                </div>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="addEXCPrescItem" size="sm" variant="primary">TAMBAHKAN</b-button>
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
                    <b-button size="sm" variant="w3green" class="ml-1" @click="saveChanges">Simpan & lanjutkan</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal ref="confirmEmptyModal" size="sm">
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase text-center w-100">FORM BELUM TERISI</div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="text-center fs-13">
                <div>
                    Terdapat form yang belum terisi pada tab 
                    <span class="text-danger-800 font-weight-bold">{{ blankObjects }}</span>
                </div>
                <p class="mt-8">Apakah anda ingin mengisinya terlebih dahulu?</p>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-center w-100">
                    <b-button size="sm" variant="outline-danger-500" @click="leaveBlank">Biarkan Kosong</b-button>
                    <b-button size="sm" variant="w3green" class="ml-1" @click="fillInNow">Isi Sekarang</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal.mdl_presc_template" size="sm">
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
                            <div :class="getParentVariant(list.id)" class="px-12 py-1">
                                <div class="fs-14 font-weight-bold">{{ getParentName(list.id) }}</div>
                                <div v-if="list.id != 1" class="fs-12 text-italic" style="font-weight: 400">  
                                    <span>Jumlah: {{ list.value }}, </span>
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
                <b-button size="sm" variant="w3green" class="m-0 px-24" @click="savePrescTemplate">SIMPAN</b-button>
            </template>
        </b-modal>

        <b-modal v-model="active.modal.mdl_anamn_template" size="sm">
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase text-center w-100">ANDA AKAN MENAMBAHKAN ANAMNESA INI SEBAGAI TEMPLATE</div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <section>
                <div>
                    <label class="fs-13 mb-0">Nama Template Resep</label>
                    <b-form-input v-model="anamn_form_templating.name" autocomplete="off" size="sm" placeholder="tuliskan nama template anamnesa" /> 
                </div>
                <div class="bg-info-100 mt-16 p-8 fs-12">
                    <div><b>Anamnesa: </b>{{ getAnamnesaName(anamn_form_templating.anamesa_type_id) }}</div>
                    <div><b>Keluhan Utama: </b>{{ anamn_form_templating?.main_complaint || '-' }}</div>
                    <div><b>Nama Keluarga: </b>{{ anamn_form_templating?.family_name || '-' }}</div>
                    <div><b>Hub. Keluarga: </b>{{ getFamilyName(anamn_form_templating?.family_relation_id) }}</div>
                    <div><b>R.Penyakit Sekarang: </b>{{ anamn_form_templating?.history_disease_now || '-' }}</div>
                    <div><b>R.Penyakit Dahulu: </b>{{ anamn_form_templating?.history_disease_past || '-' }}</div>
                </div>
            </section>
            <template #modal-footer>
                <b-button size="sm" variant="w3green" class="m-0 px-24" @click="saveAnamnTemplate">SIMPAN</b-button>
            </template>
        </b-modal>

        <LabResultModalView ref="labModalResult"/>
        <RadResultModalView ref="radModalResult"/>
    </div>
</template>

<script>
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import LabResultModalView from '@/views/pages/patient/medical_support/LabResultModalView.vue';
import RadResultModalView from '@/views/pages/patient/medical_support/RadResultModalView.vue';
import dateMixin from "@/mixins/dateMixin";
import optionMixin from '@/mixins/optionMixin';
// import io from 'socket.io-client';

export default {
    components: {LabResultModalView, RadResultModalView},
    mixins: [dateMixin, optionMixin],
    data() {
        return {
            // socket : io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"],withCredentials: true }),
            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            hfi_outpatient: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
            hfi_emergency: '444b3134-5b75-4b67-af72-a558d46716d7',
            urlbe:process.env.VUE_APP_IMAGE,
            dynamicHeight: "73vh",
            differenceHeight: "85px",
            userData: JSON.parse(localStorage.getItem('userData')),
            reg_des_id: '',
            regData: {},
            soap_list: [],
            cppt_history: [],
            nurse_history: [],
            soap_history: [],
            global_soap_id: '',
            isDisabled: false,
            active: {
                modal: {
                    main: false,
                    allergy: false,
                    exc_presc: false,
                    presc_parent: false,
                    presc_items: false,
                    presc_exc_parent: false,
                    presc_exc_items: false,
                    mdl_presc_template: false,
                    mdl_anamn_template: false,
                },
                advance: {
                    antopometri: false,
                    generalis: false,
                    oftalmologis: false,
                    template_presc: false,
                    template_anam: false,
                },
                disabled: {
                    ecx_presc: false,
                    section: false,
                    dsb_presc_template: false,
                    dsb_anamn_template: false,
                },
                section: 'ANAMNESA'
            },
            temp_header: {
                alias_name: '',
                name: '',
                norm: '',
                birth_date: '',
                weight: '',
                height: '',
            },
            allergyFields: [
                { key: 'no', label: 'No' },
                { key: 'name', label: 'Alergi' },
                { key: 'type_allergy_id', label: 'Jenis Alergi' },
                { key: 'reaction', label: 'Reaksi' },
                { key: 'severity', label: 'Tingkat Keparahan' },
                { key: 'side_effect', label: 'Efek Samping' },
                { key: 'date_tragedy', label: 'Tgl Kejadian' },
                { key: 'Aksi' },
            ],
            allergyInput: {
                type_allergy_id: 0,
                name: '',
                reaction: '',
                severity: '',
                side_effect: '',
                date_tragedy: '',
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
            precriptionFields: [
                { key: 'no', label: 'No', thStyle: 'width: 40px' },
                { key: 'no_order', label: 'Nomor Order', thStyle: 'width: 150px' },
                { key: 'form_prescription', label: 'Resep' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px' },
            ],
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
            PrescriptionOptions:[
                { value: 1, title: 'OBAT SATUAN' },
                { value: 2, title: 'RACIK 1' },
                { value: 3, title: 'RACIK 2' },
                { value: 4, title: 'RACIK 3' },
                { value: 5, title: 'RACIK 4' }
            ],

            OptionsSuspected:[
                { value: 0, title: 'Tidak' },
                { value: 1, title: 'Ya' }
            ],
            OptionsDiagnose: [],
            OptionsEtiology: [],
            OptionsComorbidities: [],

            nextTab: null,

            original_anamnesa: {},
            original_vital: {},
            original_generalis: {},
            original_diagnosis: {},
            original_procedure: {},
            original_laboratory: {},
            original_radiology: {},
            original_physiotherapy: {},
            original_prescription: {},
            original_hemodialysis: {},
            original_rehabilitation: {},

            form_anamnesa: {
                soap_id: '',
                doctor_in_charge_id: '',
                registration_destination_id: '',
                is_initial_inpatient: 0,
                is_hemodialysis: 0,
                is_medical_rehabilitation: 0,
                anamesa_type_id: '',
                family_relation_id: '',
                family_name: '',
                main_complaint: '',
                history_disease_now: '',
                history_disease_past: '',
                history_drugs: '',
                allergies: []
            },

            form_vital: {
                soap_id: '',
                doctor_in_charge_id: '',
                registration_destination_id: '',
                is_initial_inpatient: 0,
                is_hemodialysis: 0,
                is_medical_rehabilitation: 0,
                systolic_blood_pressure: '',
                diastolic_blood_pressure: '',
                pulse_frequency: '',
                breath_frequency: '',
                oximetry_frequency: '',
                temperature: '',
                oxygen_type_id: '',
                aware: '',
                eye_score: '',
                verbal_score: '',
                motor_score: '',
                weight: '',
                height: '',
                imt: '',
                head_circumference: '',
                chest_circumference: '',
                arm_circumference: '',
                stomach_circumference: '',
                knee_circumference: ''
            },

            form_generalis: {
                soap_id: '',
                doctor_in_charge_id: '',
                registration_destination_id: '',
                is_initial_inpatient: 0,
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
                ophthal_status: '',
                ophthal_od: {
                    visus: 12,
                    tio_digital: 1,
                    tio_tonometri_sciots: '',
                    eye_position: 1,
                    eye_movement: 1,
                    super_silia: 1,
                    palpebra_edema: 1,
                    palpebra_ptosis: 1,
                    palpebra_lagophtalmus: 1,
                    palpebra_tumor: 1,
                    apparatus_lakrimalis: 1,
                    silia: 1,
                    conjunctive_hiperemis: 1,
                    conjunctive_bleed: 1,
                    cornea: 1,
                    camera_oculi: 1,
                    iris_color: 2,
                    iris_cripte: 1,
                    pupil_form: 1,
                    pupil_diameter: 4,
                    pupil_direct_light: 1,
                    pupil_indirect_light: 1,
                    lens: 1,
                    shadow_test: 1,
                    glandula: 1,
                    field_view: 1,
                    fundus: '',
                },
                ophthal_os: {
                    visus: 12,
                    tio_digital: 1,
                    tio_tonometri_sciots: '',
                    eye_position: 1,
                    eye_movement: 1,
                    super_silia: 1,
                    palpebra_edema: 1,
                    palpebra_ptosis: 1,
                    palpebra_lagophtalmus: 1,
                    palpebra_tumor: 1,
                    apparatus_lakrimalis: 1,
                    silia: 1,
                    conjunctive_hiperemis: 1,
                    conjunctive_bleed: 1,
                    cornea: 1,
                    camera_oculi: 1,
                    iris_color: 2,
                    iris_cripte: 1,
                    pupil_form: 1,
                    pupil_diameter: 4,
                    pupil_direct_light: 1,
                    pupil_indirect_light: 1,
                    lens: 1,
                    shadow_test: 1,
                    glandula: 1,
                    field_view: 1,
                    fundus: '',
                },
            },

            form_diagnosis: {
                soap_id: '',
                doctor_in_charge_id: '',
                registration_destination_id: '',
                is_initial_inpatient: 0,
                is_hemodialysis: 0,
                is_medical_rehabilitation: 0,
                icd10: [],
                icd10_notes: '',
                non_icd10: []
            },

            form_procedure: {
                soap_id: '',
                doctor_in_charge_id: '',
                registration_destination_id: '',
                is_initial_inpatient: 0,
                is_hemodialysis: 0,
                is_medical_rehabilitation: 0,
                icd9cm: [],
                therapy_plan: '',
                transaction_doctor: [],
                advice: ''
            },

            form_laboratory: {
                soap_id: '',
                doctor_in_charge_id: '',
                registration_destination_id: '',
                laboratory_request_items: [],
            },

            form_radiology: {
                soap_id: '',
                doctor_in_charge_id: '',
                registration_destination_id: '',
                radiology_request_items: [],
            },
            
            form_physiotherapy: {
                soap_id: '',
                doctor_in_charge_id: '',
                registration_destination_id: '',
                physiotherapy_request_items: [],
            },
            
            input_prescription: {
                soap_id: '',
                doctor_in_charge_id: '',
                registration_destination_id: '',
                pharmacy_depo_id: '',
                form_prescription_parent: [],
                form_prescription: []
            },
            
            form_hemodialysis: {
                soap_id: '',
                doctor_in_charge_id: '',
                registration_destination_id: '',
                is_initial_inpatient: 0,
                is_hemodialysis: 1,
                is_medical_rehabilitation: 0,
                main_diagnosis: '',
                etiology: '',
                comorbidities: '',
                frequency_hd: '',
                vascular_access: ''
            },

            form_rehabilitation: {
                soap_id: '',
                doctor_in_charge_id: '',
                registration_destination_id: '',
                is_initial_inpatient: 0,
                is_hemodialysis: 0,
                is_medical_rehabilitation: 1,
                functional_testing: '',
                suspected_disease_type_id: 0,
                suspected_disease: '',
                result: '',
                conclusion: '',
                recommendation: '',
                functional_diagnosis: '',
                medical_diagnosis: '',
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
            catalog: '',
            prescription: '',
            prescription_type: {},
            presc_status: 0,
            temp_presc_parent: {
                parent_id: '',
                parent_name: '',
                signatura: '',
                other_signatura: '',
                consume_time: '',
                utilization: '',
                other_utilization: '',
            },
            temp_presc_items: {
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

            exclusive_presc_list: [],
            form_exclusive_presc: {
                id: '',
                soap_id: '',
                pharmacy_depo_id: '',
                soap_p_id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                form_prescription_parent: [],
                form_prescription: []
            },
            
            ExcOptionsCatalog: [],
            exc_catalog: '',
            exc_prescription: '',
            exc_prescription_type: {},
            temp_exc_parent: {
                parent_id: '',
                parent_name: '',
                signatura: '',
                other_signatura: '',
                consume_time: '',
                utilization: '',
                other_utilization: '',
            },
            temp_exc_items: {
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

            prescription_name:'OBAT SATUAN',
            getCosting: [],

            radiologyResultField: [
                { key: 'no', label: 'No', thStyle: { width: '40px', textAlign: 'center' }  },
                { key: 'expertise', label: 'Expertise', thStyle:'width: 400px' },
                { key: 'url', label: 'Gambar' },
                { key: 'action', label: 'Aksi', thStyle: { width: '100px' }  },
            ],
            temp_rad_result: {
                arryData: '',
                costing_name: '',
            },
            blankObjects: [],
            catalog_list: [],
            
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
        }
    },
    computed: {
        ...mapState({ 
            itemsAllergy: state => state.patient.allergy.items, 
            OptionsDoctorRef: state => state.employee.options.employee,
            // PackageOptions: state => state.packages.options.packages,
            // OptionsCosting: state => state.costing.options.costing,
            // itemsPhysiotherapy: state => state.physiotherapy.package.items,
            
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

            // prescription options
            OptionsUtilization: state => state.soap.prescriptions.OptionsUtilization,
            OptionsConsumeTime: state => state.soap.prescriptions.OptionsConsumeTime,
            OptionsSignatura: state => state.soap.prescriptions.OptionsSignatura,
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
            return this.supportPackage.rad_package_list.map((rad) => {
                const filteredCategoryItems = rad.category_items.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchrad.toLowerCase())
                );

                return {
                    ...rad,
                    category_items: filteredCategoryItems,
                };
            }).filter(rad => rad.category_items.length > 0); 
        },
        filteredListPhy() {
            return this.supportPackage.phy_package_list.filter((phy) => {
                const computedPhyObj = { ...phy}
                return Object.keys(computedPhyObj)
                    .some(key => ('' + computedPhyObj[key]).toLowerCase().includes(this.searchphy.toLowerCase()))
            })
        },        
    },
    filters:{
        number(value) {
            return Number(value)
        },
        // numberToDecimal(value) {
        //     let val = (value/1).toFixed(0).replace('.', ',')
        //     return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        // },
        // numberToDecimalCurr(value) {
        //     let val = (value/1).toFixed(2).replace('.', ',')
        //     return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        // },
        // decimalToNumber(value) {
        //     const val = value
        //         .replace(/(kr|\$|£|€)/g, '')
        //         .trim()
        //         .replace(/(.+)[.,](\d+)$/g, "$1x$2")
        //         .replace(/[.,]/g, '')
        //         .replace('x', '.');
        //     return parseFloat(val);
        // }
    },
    methods: {
        getSectionName(sectionKey){
            if (sectionKey == 'ANAMNESA') return 'Anamnesa'
            if (sectionKey == 'VITAL') return 'Tanda Vital'
            if (sectionKey == 'GENERALIS') return 'Status Generalis'
            if (sectionKey == 'DIAGNOSIS') return 'Diagnosa'
            if (sectionKey == 'PROCEDURE') return 'Prosedur'
            if (sectionKey == 'LABORATORY') return 'Laboratorium'
            if (sectionKey == 'RADIOLOGY') return 'Radiologi'
            if (sectionKey == 'PHYSIOTHERAPY') return 'Fisioterapi'
            if (sectionKey == 'PRESC') return 'Resep'
            if (sectionKey == 'HEMODIALYSIS') return 'Hemodialisa'
            if (sectionKey == 'REHABILITATION') return 'Uji Fungsi'
        },

        async cloneAllState() {            
            this.original_anamnesa = JSON.parse(JSON.stringify(this.form_anamnesa))
            this.original_vital = JSON.parse(JSON.stringify(this.form_vital))
            this.original_generalis = JSON.parse(JSON.stringify(this.form_generalis))
            this.original_diagnosis = JSON.parse(JSON.stringify(this.form_diagnosis))
            this.original_procedure = JSON.parse(JSON.stringify(this.form_procedure))
            this.original_laboratory = JSON.parse(JSON.stringify(this.form_laboratory))
            this.original_radiology = JSON.parse(JSON.stringify(this.form_radiology))
            this.original_physiotherapy = JSON.parse(JSON.stringify(this.form_physiotherapy))
            this.original_prescription = JSON.parse(JSON.stringify(this.input_prescription))
            this.original_hemodialysis = JSON.parse(JSON.stringify(this.form_hemodialysis))
            this.original_rehabilitation = JSON.parse(JSON.stringify(this.form_rehabilitation))
        },

        changeTab(destination) {
            this.prescription = ''
            this.catalog = ''

            const changes = this.hasChanges(this.active.section)
            if (changes) {
                this.nextTab = destination
                this.$refs.confirmModal.show()
            } else {
                this.active.section = destination
            }
        },

        hasChanges(exist) {
            const formMapping = {
                ANAMNESA: ["form_anamnesa", "original_anamnesa"],
                VITAL: ["form_vital", "original_vital"],
                GENERALIS: ["form_generalis", "original_generalis"],
                DIAGNOSIS: ["form_diagnosis", "original_diagnosis"],
                PROCEDURE: ["form_procedure", "original_procedure"],
                LABORATORY: ["form_laboratory", "original_laboratory"],
                RADIOLOGY: ["form_radiology", "original_radiology"],
                PHYSIOTHERAPY: ["form_physiotherapy", "original_physiotherapy"],
                PRESC: ["input_prescription", "original_prescription"],
                HEMODIALYSIS: ["form_hemodialysis", "original_hemodialysis"],
                REHABILITATION: ["form_rehabilitation", "original_rehabilitation"],
            };

            if (!formMapping[exist]) return false;

            const [form, original] = formMapping[exist];

            const formWithoutSoapId = { ...this[form] };
            const originalWithoutSoapId = { ...this[original] };
            delete formWithoutSoapId.soap_id;
            delete formWithoutSoapId.is_hemodialysis;
            delete formWithoutSoapId.is_initial_inpatient;
            delete formWithoutSoapId.is_medical_rehabilitation;
            
            delete originalWithoutSoapId.soap_id;
            delete originalWithoutSoapId.is_hemodialysis;
            delete originalWithoutSoapId.is_initial_inpatient;
            delete originalWithoutSoapId.is_medical_rehabilitation;

            let changes = {};

            Object.keys(formWithoutSoapId).forEach(key => {
                let before = originalWithoutSoapId[key];
                let after = formWithoutSoapId[key];

                // Ubah data null atau string kosong biar jadi sting kosong 
                const beforeStr = before === null || before === undefined ? "" : String(before);
                const afterStr = after === null || after === undefined ? "" : String(after);

                // kalo value yang dibandingin sama (kasus tipe datanya beda), di abaikan
                if (beforeStr !== afterStr) {
                    changes[key] = { before, after };
                }
            });

            // console.log("Perubahan yang signifikan:", changes);
            return Object.keys(changes).length > 0 ? changes : false;
        },

        discardChanges() {
            const formMapping = {
                ANAMNESA: ["form_anamnesa", "original_anamnesa"],
                VITAL: ["form_vital", "original_vital"],
                GENERALIS: ["form_generalis", "original_generalis"],
                DIAGNOSIS: ["form_diagnosis", "original_diagnosis"],
                PROCEDURE: ["form_procedure", "original_procedure"],
                LABORATORY: ["form_laboratory", "original_laboratory"],
                RADIOLOGY: ["form_radiology", "original_radiology"],
                PHYSIOTHERAPY: ["form_physiotherapy", "original_physiotherapy"],
                PRESC: ["input_prescription", "original_prescription"],
                HEMODIALYSIS: ["form_hemodialysis", "original_hemodialysis"],
                REHABILITATION: ["form_rehabilitation", "original_rehabilitation"],
            };

            if (formMapping[this.active.section]) {
                const [form, original] = formMapping[this.active.section];

                const soap_id = this[form].soap_id
                const is_hemodialysis = this[form].is_hemodialysis
                const is_initial_inpatient = this[form].is_initial_inpatient
                const is_medical_rehabilitation = this[form].is_medical_rehabilitation
                this[form] = JSON.parse(JSON.stringify(this[original]))
                this[form].soap_id = soap_id
                this[form].is_hemodialysis = is_hemodialysis
                this[form].is_initial_inpatient = is_initial_inpatient
                this[form].is_medical_rehabilitation = is_medical_rehabilitation
            }

            this.active.section = this.nextTab
            this.$refs.confirmModal.hide()
        },
        saveChanges() {
            const formMapping = {
                ANAMNESA: ["form_anamnesa", "original_anamnesa"],
                VITAL: ["form_vital", "original_vital"],
                GENERALIS: ["form_generalis", "original_generalis"],
                DIAGNOSIS: ["form_diagnosis", "original_diagnosis"],
                PROCEDURE: ["form_procedure", "original_procedure"],
                LABORATORY: ["form_laboratory", "original_laboratory"],
                RADIOLOGY: ["form_radiology", "original_radiology"],
                PHYSIOTHERAPY: ["form_physiotherapy", "original_physiotherapy"],
                PRESC: ["input_prescription", "original_prescription"],
                HEMODIALYSIS: ["form_hemodialysis", "original_hemodialysis"],
                REHABILITATION: ["form_rehabilitation", "original_rehabilitation"],
            };

            if (formMapping[this.active.section]) {
                const [form, original] = formMapping[this.active.section];
                const soap_id = this[form].soap_id;
                const is_hemodialysis = this[form].is_hemodialysis;
                const is_initial_inpatient = this[form].is_initial_inpatient;
                const is_medical_rehabilitation = this[form].is_medical_rehabilitation;

                this[original] = JSON.parse(JSON.stringify(this[form]));
                this[original].soap_id = soap_id;
                this[original].is_hemodialysis = is_hemodialysis;
                this[original].is_initial_inpatient = is_initial_inpatient;
                this[original].is_medical_rehabilitation = is_medical_rehabilitation;
                this.changeSection(this.active.section, this.nextTab)
            }
            
            this.$refs.confirmModal.hide();
        },

        leaveBlank() {
            this.$refs.confirmEmptyModal.hide()

            // cek ada yang berubah atau ga di section yg lagi aktif, kl ada langsung disimpen
            if (this.hasChanges(this.active.section)) {
                this.changeSection(this.active.section, this.active.section)
            }
            let haveSoap = this.global_soap_id ? true : false
            let notCheckedOut = this.regData.is_checkout == 0 ? true : false
            this.active.modal.main = false

            if (haveSoap == true && notCheckedOut == true){
                const data = { message: "bisa triger checkout, barangkali tar mau kirim data" };
                this.$emit("childTrigger", data);
            }
        },
        
        fillInNow() {
            this.$refs.confirmEmptyModal.hide()
        },

        // ==================================================

        getMenusVariant(value) {
            if (value == this.active.section) {
                return 'frOrange'
            }
            return 'outline-frOrange'
        },

        changeSection(exist, destination) {
            this.prescription = ''
            this.catalog = ''

            const saveFunctions = {
                ANAMNESA: this.saveAnamnesa,
                VITAL: this.saveVital,
                GENERALIS: this.saveGeneralis,
                DIAGNOSIS: this.saveDiagnosis,
                PROCEDURE: this.saveProcedure,
                LABORATORY: this.saveLaboratory,
                RADIOLOGY: this.saveRadiology,
                PHYSIOTHERAPY: this.savePhysiotherapy,
                PRESC: this.savePresc,
                HEMODIALYSIS: this.saveHemodialysis,
                REHABILITATION: this.saveRehabilitation,
            };

            if (!saveFunctions[exist]) return;
            
            if (exist == 'VITAL') {
                saveFunctions[exist](exist, destination, 'main_section');
            } else if (exist == 'PRESC') {
                this.presc_status == 1 ? this.active.section = destination : saveFunctions[exist](exist, destination)
            } else if (exist == 'LABORATORY') {
                this.lab_status != 1 ? this.active.section = destination : saveFunctions[exist](exist, destination)
            } else if (exist == 'RADIOLOGY') {
                this.rad_status != 1 ? this.active.section = destination : saveFunctions[exist](exist, destination)
            } else {
                saveFunctions[exist](exist, destination);
            }
        },

        getPrevSection(){
            if (this.regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e') return 'HEMODIALYSIS'
            if (this.regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a') return 'REHABILITATION'
            return 'PHYSIOTHERAPY'
        },

        getNextSection(){
            if (this.regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e') return 'HEMODIALYSIS'
            if (this.regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a') return 'REHABILITATION'
            return 'PRESC'
        },

        async getMySoap(soap_id){
            try {                
                this.$store.dispatch('soap/getBySoapId', soap_id).then(async (response) => {
                    if (response?.length > 0) {
                        await this.fillSoapForm(response[0])
                    }
                }, error => {
                    console.error('Error while GET soap/getBySoapId at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET soap/getBySoapId at: ', error);
            }
        },
        
        async saveAnamnesa(exist, destination) {
            this.form_anamnesa.doctor_in_charge_id ||= this.regData.doctor_in_charge_id;
            this.form_anamnesa.registration_destination_id ||= this.regData.registration_destination_id
            this.form_anamnesa.is_hemodialysis = this.regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e' ? 1 : 0
            this.form_anamnesa.is_medical_rehabilitation = this.regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a' ? 1 : 0

            let fixed_data = {
                exist: exist,
                data: this.form_anamnesa   
            }

            try {
                this.active.disabled.section = true
                this.$store.dispatch('soap/putSections', fixed_data)
                .then(
                    async (res) => {
                        this.notification('Success','CheckIcon','success','Berhasil menyimpan anamnesa')
                        await this.getMySoap(res?.data?.id)
                        this.active.section = destination
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.section = false
            }
        },
        
        async saveVital(exist, destination, save_from) {
            this.form_vital.doctor_in_charge_id ||= this.regData.doctor_in_charge_id;
            this.form_vital.registration_destination_id ||= this.regData.registration_destination_id
            this.form_vital.is_hemodialysis = this.regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e' ? 1 : 0
            this.form_vital.is_medical_rehabilitation = this.regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a' ? 1 : 0
            
            let fixed_data = {
                exist: exist,
                data: this.form_vital   
            }

            try {
                this.active.disabled.section = true
                this.$store.dispatch('soap/putSections', fixed_data)
                .then(
                    async (res) => {
                        if(save_from == 'main_section') {
                            this.notification('Success','CheckIcon','success','Berhasil menyimpan tanda vital')
                            this.active.section = destination
                        }
                        await this.getMySoap(res?.data?.id)
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.section = false
            }
        },
        
        async saveGeneralis(exist, destination, save_from) {
            this.form_generalis.doctor_in_charge_id ||= this.regData.doctor_in_charge_id;
            this.form_generalis.registration_destination_id ||= this.regData.registration_destination_id
            this.form_generalis.is_hemodialysis = this.regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e' ? 1 : 0
            this.form_generalis.is_medical_rehabilitation = this.regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a' ? 1 : 0

            let fixed_data = {
                exist: exist,
                data: this.form_generalis   
            }

            try {
                this.active.disabled.section = true
                this.$store.dispatch('soap/putSections', fixed_data)
                .then(
                    async (res) => {
                        this.notification('Success','CheckIcon','success','Berhasil menyimpan status generalis')
                        await this.getMySoap(res?.data?.id)
                        this.active.section = destination
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.section = false
            }
        },
        
        async saveDiagnosis(exist, destination) {
            this.form_diagnosis.doctor_in_charge_id ||= this.regData.doctor_in_charge_id;
            this.form_diagnosis.registration_destination_id ||= this.regData.registration_destination_id
            this.form_diagnosis.is_hemodialysis = this.regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e' ? 1 : 0
            this.form_diagnosis.is_medical_rehabilitation = this.regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a' ? 1 : 0

            let fixed_data = {
                exist: exist,
                data: this.form_diagnosis   
            }

            try {
                this.active.disabled.section = true
                this.$store.dispatch('soap/putSections', fixed_data)
                .then(
                    async (res) => {
                        this.notification('Success','CheckIcon','success','Berhasil menyimpan diagnosa')
                        await this.getMySoap(res?.data?.id)
                        this.active.section = destination
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.section = false
            }
        },
        
        async saveProcedure(exist, destination) {
            this.form_procedure.doctor_in_charge_id ||= this.regData.doctor_in_charge_id;
            this.form_procedure.registration_destination_id ||= this.regData.registration_destination_id
            this.form_procedure.is_hemodialysis = this.regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e' ? 1 : 0
            this.form_procedure.is_medical_rehabilitation = this.regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a' ? 1 : 0

            let fixed_data = {
                exist: exist,
                data: this.form_procedure   
            }

            try {
                this.active.disabled.section = true
                this.$store.dispatch('soap/putSections', fixed_data)
                .then(
                    async (res) => {
                        this.notification('Success','CheckIcon','success','Berhasil menyimpan prosedur')
                        await this.getMySoap(res?.data?.id)
                        this.active.section = destination
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.section = false
            }
        },
        
        async saveLaboratory(exist, destination) {
            this.form_laboratory.doctor_in_charge_id ||= this.regData.doctor_in_charge_id;
            this.form_laboratory.registration_destination_id ||= this.regData.registration_destination_id

            let fixed_data = {
                exist: exist,
                data: this.form_laboratory   
            }

            try {
                this.active.disabled.section = true
                this.$store.dispatch('soap/putSections', fixed_data)
                .then(
                    async (res) => {
                        this.notification('Success','CheckIcon','success','Berhasil menyimpan laboratorium')
                        await this.getMySoap(res?.data?.id)
                        this.active.section = destination
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.section = false
            }
        },
        
        async saveRadiology(exist, destination) {
            this.form_radiology.doctor_in_charge_id ||= this.regData.doctor_in_charge_id;
            this.form_radiology.registration_destination_id ||= this.regData.registration_destination_id
            
            let fixed_data = {
                exist: exist,
                data: this.form_radiology   
            }

            try {
                this.active.disabled.section = true
                this.$store.dispatch('soap/putSections', fixed_data)
                .then(
                    async (res) => {
                        this.notification('Success','CheckIcon','success','Berhasil menyimpan radiologi')
                        await this.getMySoap(res?.data?.id)
                        this.active.section = destination
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
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
                this.active.disabled.section = true
                this.$store.dispatch('soap/putSections', fixed_data)
                .then(
                    async (res) => {
                        this.notification('Success','CheckIcon','success','Berhasil menyimpan fisioterapi')
                        await this.getMySoap(res?.data?.id)
                        this.active.section = destination
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.section = false
            }
        },
        
        async savePresc(exist, destination) {
            this.input_prescription.doctor_in_charge_id ||= this.regData.doctor_in_charge_id;
            this.input_prescription.registration_destination_id ||= this.regData.registration_destination_id
            this.input_prescription.pharmacy_depo_id = '297c44f0-12ef-40e6-b9a7-c831065b3372'

            let fixed_data = {
                exist: exist,
                data: this.input_prescription   
            }

            let inp_presc = this.input_prescription?.form_prescription 
            let inp_parent = this.input_prescription?.form_prescription_parent 
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
                        this.$store.dispatch('soap/putSections', fixed_data)
                        .then(
                            async res => {
                                this.notification('Success','CheckIcon','success','Berhasil menyimpan resep')
                                await this.getMySoap(res?.data?.id)
                                this.active.section = destination
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)                
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
        
        async saveHemodialysis(exist, destination) {
            this.form_hemodialysis.doctor_in_charge_id ||= this.regData.doctor_in_charge_id;
            this.form_hemodialysis.registration_destination_id ||= this.regData.registration_destination_id
            this.form_hemodialysis.is_hemodialysis = this.regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e' ? 1 : 0
            this.form_hemodialysis.is_medical_rehabilitation = this.regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a' ? 1 : 0

            let fixed_data = {
                exist: exist,
                data: this.form_hemodialysis   
            }

            try {
                this.active.disabled.section = true
                this.$store.dispatch('soap/putSections', fixed_data)
                .then(
                    async (res) => {
                        this.notification('Success','CheckIcon','success','Berhasil menyimpan anamnesa')
                        await this.getMySoap(res?.data?.id)
                        this.active.section = destination
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.section = false
            }
        },

        async saveRehabilitation(exist, destination) {
            this.form_rehabilitation.doctor_in_charge_id ||= this.regData.doctor_in_charge_id;
            this.form_rehabilitation.registration_destination_id ||= this.regData.registration_destination_id
            this.form_rehabilitation.is_hemodialysis = this.regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e' ? 1 : 0
            this.form_rehabilitation.is_medical_rehabilitation = this.regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a' ? 1 : 0

            let fixed_data = {
                exist: exist,
                data: this.form_rehabilitation   
            }

            try {
                this.active.disabled.section = true
                this.$store.dispatch('soap/putSections', fixed_data)
                .then(
                    async (res) => {
                        this.notification('Success','CheckIcon','success','Berhasil menyimpan anamnesa')
                        await this.getMySoap(res?.data?.id)
                        this.active.section = destination
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.section = false
            }
        },
        
        parentTrigger(message) {
            // presc-template
            this.presc_list_templating = message.presc_template
            this.presc_filtered = message.presc_template
            
            // anamn-template
            this.anamn_list_templating = message.anamn_template
            this.anamn_filtered = message.anamn_template

            // prescription
            this.catalog_list = message.catalog_list
            this.PackageOptions = message.stock_list

            // support-package
            this.supportPackage.lab_package_list = message.lab_list
            this.supportPackage.rad_package_list = message.rad_list
            this.supportPackage.phy_package_list = message.phy_list

            // registration-detail
            this.regData = message.regData

            // costing
            this.getCosting = message.costing_list

            // history
            this.cppt_history = message.cppt_history
            this.soap_history = message.soap_history
            this.nurse_history = message.nurse_history

            // modal-heder
            this.temp_header.alias_name = this.regData?.alias_name
            this.temp_header.name = this.regData?.name
            this.temp_header.norm = this.regData?.norm
            this.temp_header.birth_date = this.regData?.birth_date
            this.temp_header.weight = this.regData?.ttv?.weight
            this.temp_header.height = this.regData?.ttv?.height

            this.openMainModal(message.gate)
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

        getParentVariant(parent_id){
            if (parent_id == 1) return 'bg-primary-100'
            if (parent_id == 2) return 'bg-success-100'
            if (parent_id == 3) return 'bg-orange-100'
            if (parent_id == 4) return 'bg-danger-100'
            if (parent_id == 5) return 'bg-pink-100'
            return 'bg-white'
        },

        getParentName(parent_id){
            if (parent_id == 1) return 'OBAT SATUAN'
            if (parent_id == 2) return 'RACIK 1'
            if (parent_id == 3) return 'RACIK 2'
            if (parent_id == 4) return 'RACIK 3'
            if (parent_id == 5) return 'RACIK 4'
            return '-'
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        getRelation(code) {
            const relations = {
                '1': 'Ayah',
                '2': 'Ibu',
                '3': 'Kakak',
                '4': 'Adik',
            };
            return relations[code] || '-';
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

        closeMainModal(triggerSource) {
            if (triggerSource == 'save_button') {
                const formMapping = {
                    "form_anamnesa": "Anamnesa",
                    "form_vital": "Tanda Vital",
                    "form_generalis": "Status Generalis",
                    "form_diagnosis": "Diagnosa",
                    "form_procedure": "Prosedur",
                    "form_laboratory": "Laboratorium",
                    "form_radiology": "Radiologi",
                    "form_physiotherapy": "Fisioterapi",
                    "input_prescription": "Resep",
                };
    
                let emptyFields = {};
                let emptyObjects = [];
    
                Object.keys(formMapping).forEach(formKey => {
                    if (this[formKey]) {
                        let emptyProps = Object.keys(this[formKey]).filter(key => {
                            const value = this[formKey][key];
    
                            if (formKey === "form_anamnesa" && this[formKey].anamesa_type_id != 2) {
                                if (key === "family_relation_id" || key === "family_name") {
                                    return false;
                                }
                            }
    
                            return value === "" || (Array.isArray(value) && value.length === 0);
                        });
    
                        if (emptyProps.length > 0) {
                            emptyFields[formKey] = emptyProps;
                            emptyObjects.push(formMapping[formKey]);
                        }
                    }
                });
    
                if (Object.keys(emptyFields).length > 0) {
                    this.blankObjects = emptyObjects
                    this.$refs.confirmEmptyModal.show()
                    // console.log("Properti dengan nilai kosong ditemukan:", emptyFields);
                    // console.log("Nama objek yang memiliki properti kosong:", emptyObjects);
                } else {
                    // cek ada yang berubah atau ga di section yg lagi aktif, kl ada langsung disimpen
                    if (triggerSource == 'save_button' && this.hasChanges(this.active.section)) {
                        this.changeSection(this.active.section, this.active.section)
                    }
                    let haveSoap = this.global_soap_id ? true : false
                    let notCheckedOut = this.regData.is_checkout == 0 ? true : false
                    this.active.modal.main = false
        
                    if (haveSoap == true && notCheckedOut == true){
                        const data = { message: "bisa triger checkout, barangkali tar mau kirim data" };
                        this.$emit("childTrigger", data);
                    }
                }
            } else {
                let haveSoap = this.global_soap_id ? true : false
                let notCheckedOut = this.regData.is_checkout == 0 ? true : false
                this.active.modal.main = false
    
                if (haveSoap == true && notCheckedOut == true){
                    const data = { message: "bisa triger checkout, barangkali tar mau kirim data" };
                    this.$emit("childTrigger", data);
                }
            }

            // cek ada yang berubah atau ga di section yg lagi aktif, kl ada langsung disimpen
            // if (triggerSource == 'save_button' && this.hasChanges(this.active.section)) {
            //     this.changeSection(this.active.section, this.active.section)
            // }
            // let haveSoap = this.global_soap_id ? true : false
            // let notCheckedOut = this.regData.is_checkout == 0 ? true : false
            // this.active.modal.main = false

            // if (haveSoap == true && notCheckedOut == true){
            //     const data = { message: "bisa triger checkout, barangkali tar mau kirim data" };
            //     this.$emit("childTrigger", data);
            // }

        },
        
        async openMainModal(gate) {
            await this.resetSoapForm()
            await this.handlingOutpatient(gate)
            this.active.modal.main = true
        },

        async handlingOutpatient(gate) {            
            const params = {
                registration_destination_id: this.reg_des_id,
                is_booking: 0
            }

            const outpatient_id = "3cb626f5-9148-4fb1-8663-3d7f028b01a6"
            if (gate == 'main_gate' && this.regData.health_facility_id === outpatient_id) {
                this.$store.dispatch('soap/getDetail', params).then(async (response) => {                
                    if (response.length > 0) {
                        await this.fillSoapForm(response[0])
                    } else {  
                        // await this.saveGeneralis('GENERALIS', 'GENERALIS', 'initial')
                        await this.getNurseResume()
                    }
                })
            } else {
                this.fillSoapForm(gate)
            }
        },

        async fillSoapForm(obj) {
            await this.resetSoapForm()

            if (obj) {
                this.$nextTick(async () => {
                    this.global_soap_id = obj?.id || ''
                    // =============================================================================================
                    this.form_anamnesa.soap_id = obj?.id || ''
                    this.form_anamnesa.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id
                    this.form_anamnesa.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_anamnesa.is_initial_inpatient = 0
                    this.form_anamnesa.is_hemodialysis = this.regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e' ? 1 : 0
                    this.form_anamnesa.is_medical_rehabilitation = this.regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a' ? 1 : 0
                    this.form_anamnesa.anamesa_type_id = obj?.anamesa_type_id
                    this.form_anamnesa.family_relation_id = obj?.family_relation_id || ''
                    this.form_anamnesa.family_name = obj?.family_name || ''
                    this.form_anamnesa.main_complaint = obj?.main_complaint || ''
                    this.form_anamnesa.history_disease_now = obj?.history_disease_now || ''
                    this.form_anamnesa.history_disease_past = obj?.history_disease_past || ''
                    this.form_anamnesa.history_drugs = obj?.history_drugs || ''
                    if (obj?.allergies) {
                        for (let i = 0; i < obj.allergies.length; i++) {
                            const element = obj.allergies[i];
                            this.form_anamnesa.allergies.push({
                                name: element.name
                            })
                        }
                    }
                    // =============================================================================================
                    this.form_vital.soap_id = obj?.id || ''
                    this.form_vital.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id
                    this.form_vital.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_vital.is_initial_inpatient = 0
                    this.form_vital.is_hemodialysis = this.regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e' ? 1 : 0
                    this.form_vital.is_medical_rehabilitation = this.regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a' ? 1 : 0
                    this.form_vital.systolic_blood_pressure = obj?.systolic_blood_pressure
                    this.form_vital.diastolic_blood_pressure = obj?.diastolic_blood_pressure
                    this.form_vital.pulse_frequency = obj?.pulse_frequency
                    this.form_vital.breath_frequency = obj?.breath_frequency
                    this.form_vital.oximetry_frequency = obj?.oximetry_frequency
                    this.form_vital.temperature = obj?.temperature
                    this.form_vital.oxygen_type_id = obj?.oxygen_type_id || ''
                    this.form_vital.aware = obj?.aware
                    this.form_vital.eye_score = obj?.eye_score || ''
                    this.form_vital.verbal_score = obj?.verbal_score || ''
                    this.form_vital.motor_score = obj?.motor_score || ''
                    this.form_vital.weight = obj?.weight
                    this.form_vital.height = obj?.height
                    this.form_vital.imt = obj?.imt
                    this.form_vital.head_circumference = obj?.head_circumference
                    this.form_vital.chest_circumference = obj?.chest_circumference
                    this.form_vital.arm_circumference = obj?.arm_circumference
                    this.form_vital.stomach_circumference = obj?.stomach_circumference
                    this.form_vital.knee_circumference = obj?.knee_circumference

                    // =============================================================================================
                    this.form_generalis.soap_id = obj?.id || ''
                    this.form_generalis.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id
                    this.form_generalis.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_generalis.is_initial_inpatient = 0
                    this.form_generalis.is_hemodialysis = this.regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e' ? 1 : 0
                    this.form_generalis.is_medical_rehabilitation = this.regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a' ? 1 : 0
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
                    this.form_generalis.ophthal_status = obj?.ophthal_status || ''

                    this.form_generalis.ophthal_od.visus = obj?.ophthal_od?.visus || 12
                    this.form_generalis.ophthal_od.tio_digital = obj?.ophthal_od?.tio_digital || 1
                    this.form_generalis.ophthal_od.tio_tonometri_sciots = obj?.ophthal_od?.tio_tonometri_sciots || ''
                    this.form_generalis.ophthal_od.eye_position = obj?.ophthal_od?.eye_position || 1
                    this.form_generalis.ophthal_od.eye_movement = obj?.ophthal_od?.eye_movement || 1
                    this.form_generalis.ophthal_od.super_silia = obj?.ophthal_od?.super_silia || 1
                    this.form_generalis.ophthal_od.palpebra_edema = obj?.ophthal_od?.palpebra_edema || 1
                    this.form_generalis.ophthal_od.palpebra_ptosis = obj?.ophthal_od?.palpebra_ptosis || 1
                    this.form_generalis.ophthal_od.palpebra_lagophtalmus = obj?.ophthal_od?.palpebra_lagophtalmus || 1
                    this.form_generalis.ophthal_od.palpebra_tumor = obj?.ophthal_od?.palpebra_tumor || 1
                    this.form_generalis.ophthal_od.apparatus_lakrimalis = obj?.ophthal_od?.apparatus_lakrimalis || 1
                    this.form_generalis.ophthal_od.silia = obj?.ophthal_od?.silia || 1
                    this.form_generalis.ophthal_od.conjunctive_hiperemis = obj?.ophthal_od?.conjunctive_hiperemis || 1
                    this.form_generalis.ophthal_od.conjunctive_bleed = obj?.ophthal_od?.conjunctive_bleed || 1
                    this.form_generalis.ophthal_od.cornea = obj?.ophthal_od?.cornea || 1
                    this.form_generalis.ophthal_od.camera_oculi = obj?.ophthal_od?.camera_oculi || 1
                    this.form_generalis.ophthal_od.iris_color = obj?.ophthal_od?.iris_color || 2
                    this.form_generalis.ophthal_od.iris_cripte = obj?.ophthal_od?.iris_cripte || 1
                    this.form_generalis.ophthal_od.pupil_form = obj?.ophthal_od?.pupil_form || 1
                    this.form_generalis.ophthal_od.pupil_diameter = obj?.ophthal_od?.pupil_diameter || 4
                    this.form_generalis.ophthal_od.pupil_direct_light = obj?.ophthal_od?.pupil_direct_light || 1
                    this.form_generalis.ophthal_od.pupil_indirect_light = obj?.ophthal_od?.pupil_indirect_light || 1
                    this.form_generalis.ophthal_od.lens = obj?.ophthal_od?.lens || 1
                    this.form_generalis.ophthal_od.shadow_test = obj?.ophthal_od?.shadow_test || 1
                    this.form_generalis.ophthal_od.glandula = obj?.ophthal_od?.glandula || 1
                    this.form_generalis.ophthal_od.field_view = obj?.ophthal_od?.field_view || 1
                    this.form_generalis.ophthal_od.fundus = obj?.ophthal_od?.fundus || ''

                    this.form_generalis.ophthal_os.visus = obj?.ophthal_os?.visus || 12
                    this.form_generalis.ophthal_os.tio_digital = obj?.ophthal_os?.tio_digital || 1
                    this.form_generalis.ophthal_os.tio_tonometri_sciots = obj?.ophthal_os?.tio_tonometri_sciots || ''
                    this.form_generalis.ophthal_os.eye_position = obj?.ophthal_os?.eye_position || 1
                    this.form_generalis.ophthal_os.eye_movement = obj?.ophthal_os?.eye_movement || 1
                    this.form_generalis.ophthal_os.super_silia = obj?.ophthal_os?.super_silia || 1
                    this.form_generalis.ophthal_os.palpebra_edema = obj?.ophthal_os?.palpebra_edema || 1
                    this.form_generalis.ophthal_os.palpebra_ptosis = obj?.ophthal_os?.palpebra_ptosis || 1
                    this.form_generalis.ophthal_os.palpebra_lagophtalmus = obj?.ophthal_os?.palpebra_lagophtalmus || 1
                    this.form_generalis.ophthal_os.palpebra_tumor = obj?.ophthal_os?.palpebra_tumor || 1
                    this.form_generalis.ophthal_os.apparatus_lakrimalis = obj?.ophthal_os?.apparatus_lakrimalis || 1
                    this.form_generalis.ophthal_os.silia = obj?.ophthal_os?.silia || 1
                    this.form_generalis.ophthal_os.conjunctive_hiperemis = obj?.ophthal_os?.conjunctive_hiperemis || 1
                    this.form_generalis.ophthal_os.conjunctive_bleed = obj?.ophthal_os?.conjunctive_bleed || 1
                    this.form_generalis.ophthal_os.cornea = obj?.ophthal_os?.cornea || 1
                    this.form_generalis.ophthal_os.camera_oculi = obj?.ophthal_os?.camera_oculi || 1
                    this.form_generalis.ophthal_os.iris_color = obj?.ophthal_os?.iris_color || 2
                    this.form_generalis.ophthal_os.iris_cripte = obj?.ophthal_os?.iris_cripte || 1
                    this.form_generalis.ophthal_os.pupil_form = obj?.ophthal_os?.pupil_form || 1
                    this.form_generalis.ophthal_os.pupil_diameter = obj?.ophthal_os?.pupil_diameter || 4
                    this.form_generalis.ophthal_os.pupil_direct_light = obj?.ophthal_os?.pupil_direct_light || 1
                    this.form_generalis.ophthal_os.pupil_indirect_light = obj?.ophthal_os?.pupil_indirect_light || 1
                    this.form_generalis.ophthal_os.lens = obj?.ophthal_os?.lens || 1
                    this.form_generalis.ophthal_os.shadow_test = obj?.ophthal_os?.shadow_test || 1
                    this.form_generalis.ophthal_os.glandula = obj?.ophthal_os?.glandula || 1
                    this.form_generalis.ophthal_os.field_view = obj?.ophthal_os?.field_view || 1
                    this.form_generalis.ophthal_os.fundus = obj?.ophthal_os?.fundus || ''
        
                    // =============================================================================================
                    this.form_diagnosis.soap_id = obj?.id || ''
                    this.form_diagnosis.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id
                    this.form_diagnosis.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_diagnosis.is_initial_inpatient = 0
                    this.form_diagnosis.is_hemodialysis = this.regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e' ? 1 : 0
                    this.form_diagnosis.is_medical_rehabilitation = this.regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a' ? 1 : 0
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
                    } else {
                        this.form_diagnosis.icd10 = []
                    }
                    this.form_diagnosis.icd10_notes = obj?.icd10_notes || ''
                    this.form_diagnosis.non_icd10 = obj?.non_icd10 || []
        
                    // =============================================================================================
                    this.form_procedure.soap_id = obj?.id || ''
                    this.form_procedure.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id
                    this.form_procedure.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_procedure.is_initial_inpatient = 0
                    this.form_procedure.is_hemodialysis = this.regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e' ? 1 : 0
                    this.form_procedure.is_medical_rehabilitation = this.regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a' ? 1 : 0
                    this.form_procedure.transaction_doctor = obj.transaction_doctor ? obj.transaction_doctor : []
                    this.form_procedure.therapy_plan = obj.therapy_plan ? obj.therapy_plan : ''
                    if (obj.icd9cm) {
                        const mappedIcd9cm = obj.icd9cm.map(item => {
                            return {
                                icd9cm_id: item.icd9cm_id,
                                value: item.icd9cm_id,
                                title: `${item.code} ${item.name}`, 
                                action: "action"
                            };
                        });                
                        this.form_procedure.icd9cm = mappedIcd9cm;
                    } else {
                        this.form_procedure.icd9cm = []
                    }
                    this.form_procedure.advice = obj?.advice || ''

                    // =============================================================================================
                    this.form_laboratory.soap_id = obj?.id || ''
                    this.form_laboratory.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id
                    this.form_laboratory.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.lab_status = obj.laboratory_request ? obj.laboratory_request.status : 1
                    if (obj.laboratory_request_items) {
                        const mappedLab = obj.laboratory_request_items.map(item => {
                            return {
                                costing_name: item.costing_name,
                                laboratory_package_id: item.laboratory_package_id,
                                service_class_id: item.service_class_id
                            };
                        });                
                        this.form_laboratory.laboratory_request_items = mappedLab;
                    } else {
                        this.form_laboratory.laboratory_request_items = []
                    }
                    
                    // =============================================================================================
                    this.form_radiology.soap_id = obj?.id || ''
                    this.form_radiology.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id
                    this.form_radiology.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.rad_status = obj.radiology_request ? obj.radiology_request.status : 1
                    if (obj.radiology_request_items) {
                        const mappedRad = obj.radiology_request_items.map(item => {
                            return {
                                costing_name: item.costing_name,
                                radiology_package_id: item.radiology_package_id,
                                service_class_id: item.service_class_id
                            };
                        });                
                        this.form_radiology.radiology_request_items = mappedRad;
                    } else {
                        this.form_radiology.radiology_request_items = []
                    }
                    
                    // =============================================================================================
                    this.form_physiotherapy.soap_id = obj?.id || ''
                    this.form_physiotherapy.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id
                    this.form_physiotherapy.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    if (obj.physiotherapy_request && obj.physiotherapy_request.length > 0) {
                        const mappedPhy = obj.physiotherapy_request[0].physiotherapy_request_items.map(item => {
                            return {
                                costing_name: item.costing_name,
                                physiotherapy_package_id: item.physiotherapy_package_id,
                                service_class_id: item.service_class_id
                            };
                        });                
                        this.form_physiotherapy.physiotherapy_request_items = mappedPhy;
                    } else {
                        this.form_physiotherapy.physiotherapy_request_items = []
                    }
                    
                    // =============================================================================================
                    this.input_prescription.soap_id = obj?.id || ''
                    this.input_prescription.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id
                    this.input_prescription.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    if (obj.pharmacy_depo_id) {
                        this.input_prescription.pharmacy_depo_id = obj.pharmacy_depo_id 
                    } else {
                        const emergency_id = "444b3134-5b75-4b67-af72-a558d46716d7"
                        if (obj.health_facility_id === emergency_id) {
                            this.input_prescription.pharmacy_depo_id = '4105d8bc-5769-463e-bc67-aae2fc5adf10'
                            this.form_exclusive_presc.pharmacy_depo_id = '4105d8bc-5769-463e-bc67-aae2fc5adf10'
                        } else {
                            this.input_prescription.pharmacy_depo_id = '297c44f0-12ef-40e6-b9a7-c831065b3372'
                            this.form_exclusive_presc.pharmacy_depo_id = '297c44f0-12ef-40e6-b9a7-c831065b3372'
                        }  
                    }

                    // cukup ambil dari order yang paling akhir, buat nyimpen di soap
                    if (obj.prescription_new) {
                        let last_index = obj.prescription_new.length-1
                        let var_presc = obj.prescription_new[last_index].form_prescription
                        let var_parent = obj.prescription_new[last_index].form_prescription_parent
                        
                        this.presc_status = obj.prescription_new ? obj.prescription_new[last_index].flag : 0

                        if (var_parent) {
                            const mappedPrescParent = var_parent.map(item => {
                                let name = item.id ? this.getParentName(item.id) : ''
                                return {
                                    id: item.id,
                                    used: item.used,
                                    value: item.value,
                                    prescription: name,
                                };
                            });                
                            this.input_prescription.form_prescription_parent = mappedPrescParent;
                        } else {
                            this.input_prescription.form_prescription_parent = []
                        }

                        if (var_presc) {
                            const mappedPresc = var_presc.map(item => {
                                let name = item.id ? this.getParentName(item.id) : ''
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
                            this.input_prescription.form_prescription = mappedPresc;                                  
                        } else {
                            this.input_prescription.form_prescription = [];                                  
                        }
                    }

                    // Exc Prescription
                    this.presc_status != 0 ? this.getExcPresciption(obj.id, obj.doctor_in_charge_id) : this.getAssessment()
                    this.form_exclusive_presc.soap_id = obj.id
                    this.form_exclusive_presc.registration_destination_id = obj.registration_destination_id
                    this.form_exclusive_presc.doctor_in_charge_id = obj.doctor_in_charge_id
                    this.form_exclusive_presc.soap_p_id = obj.soap_p_id

                    // =============================================================================================
                    this.form_hemodialysis.soap_id = obj?.id || ''
                    this.form_hemodialysis.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id
                    this.form_hemodialysis.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_hemodialysis.is_initial_inpatient = 0
                    this.form_hemodialysis.is_hemodialysis = this.regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e' ? 1 : 0
                    this.form_hemodialysis.is_medical_rehabilitation = this.regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a' ? 1 : 0
                    this.form_hemodialysis.main_diagnosis = obj?.main_diagnosis || ''
                    this.form_hemodialysis.etiology = obj?.etiology || ''
                    this.form_hemodialysis.comorbidities = obj?.comorbidities || ''
                    this.form_hemodialysis.frequency_hd = obj?.frequency_hd || ''
                    this.form_hemodialysis.vascular_access = obj?.vascular_access || ''

                    // =============================================================================================
                    this.form_rehabilitation.soap_id = obj?.id || ''
                    this.form_rehabilitation.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id
                    this.form_rehabilitation.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_rehabilitation.is_initial_inpatient = 0
                    this.form_rehabilitation.is_hemodialysis = this.regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e' ? 1 : 0
                    this.form_rehabilitation.is_medical_rehabilitation = this.regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a' ? 1 : 0
                    this.form_rehabilitation.functional_testing = obj?.functional_testing || ''
                    this.form_rehabilitation.suspected_disease_type_id = obj?.suspected_disease_type_id || 0
                    this.form_rehabilitation.suspected_disease = obj?.suspected_disease || ''
                    this.form_rehabilitation.result = obj?.result || ''
                    this.form_rehabilitation.conclusion = obj?.conclusion || ''
                    this.form_rehabilitation.recommendation = obj?.recommendation || ''
                    this.form_rehabilitation.functional_diagnosis = obj?.functional_diagnosis || ''
                    this.form_rehabilitation.medical_diagnosis = obj?.medical_diagnosis || ''

                    await this.cloneAllState()
                });
            } else {
                await this.cloneAllState()
            }
        },
        
        async getNurseResume() {
            try {
                this.$store.dispatch('nurse/getAllResolve', this.reg_des_id).then(async (response) => {
                    let resume = response[0]

                    if (response.length > 0) {
                        // anamnesa
                        this.form_anamnesa.anamesa_type_id = resume.anamesa_type_id
                        this.form_anamnesa.family_relation_id = resume.family_relation_id
                        this.form_anamnesa.family_name = resume.family_name
                        this.form_anamnesa.main_complaint = resume.main_complaint
                        this.form_anamnesa.history_disease_now = resume.history_disease_now
                        this.form_anamnesa.history_disease_past = resume.history_disease_past
                        // TTV
                        this.form_vital.systolic_blood_pressure = resume.systolic_blood_pressure 
                        this.form_vital.diastolic_blood_pressure = resume.diastolic_blood_pressure 
                        this.form_vital.pulse_frequency = resume.pulse_frequency 
                        this.form_vital.breath_frequency = resume.breath_frequency 
                        this.form_vital.oximetry_frequency = resume.oximetry_frequency 
                        this.form_vital.temperature = resume.temperature 
                        this.form_vital.oxygen_type_id = resume.oxygen_type_id 
                        this.form_vital.aware = resume.aware 
                        this.form_vital.eye_score = resume.eye_score 
                        this.form_vital.verbal_score = resume.verbal_score 
                        this.form_vital.motor_score = resume.motor_score 
                        this.form_vital.weight = resume.weight
                        this.form_vital.height = resume.height
                        this.form_vital.imt = resume.imt
                        this.form_vital.head_circumference = resume.head_circumference
                        this.form_vital.chest_circumference = resume.chest_circumference
                        this.form_vital.arm_circumference = resume.arm_circumference
                        this.form_vital.stomach_circumference = resume.stomach_circumference
                        this.form_vital.knee_circumference = resume.knee_circumference

                        await this.saveVital('VITAL', 'VITAL', 'nurse')
                    } else {
                        await this.cloneAllState()
                    }
                })
                
            } catch (error) {
                console.error('Error while GET data from nurse/getAllResolve at: ', error)
            }
        },

        // ALLERGY                
        addAllergy(){
            this.form_anamnesa.allergies.push({
                name: '',
            })
        },

        removeAllergy(index) {
            this.form_anamnesa.allergies.splice(index, 1)
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
            this.prescription_type = event
            let index = this.input_prescription.form_prescription_parent.findIndex((item) => item.id == event.value);

            if (index !== -1) return console.log("Object already exists");

            if (event.value != 1) {
                this.temp_presc_parent.parent_id = event.value
                this.temp_presc_parent.parent_name = event.title
                this.temp_presc_parent.signatura = ''
                this.temp_presc_parent.other_signatura = ''
                this.temp_presc_parent.consume_time = ''
                this.temp_presc_parent.utilization = ''
                this.temp_presc_parent.other_utilization = ''
                this.active.modal.presc_parent = true
                return
            }

            this.input_prescription.form_prescription_parent.push({
                id: event.value,
                prescription: event.title,
                used: '',
                value: event.value == 1 ? 0 : 1
            })
        },

        closePreParent() {
            this.active.modal.presc_parent = false
            this.catalog = ''
            this.prescription = ''
            this.prescription_type = {}
        },

        addParent() {
            let con_signatura = this.temp_presc_parent.signatura == 'other' ? this.temp_presc_parent.other_signatura : this.temp_presc_parent.signatura
            let con_utilization = this.temp_presc_parent.utilization == 'other' ? this.temp_presc_parent.other_utilization : this.temp_presc_parent.utilization

            this.input_prescription.form_prescription_parent.push(
                {
                    id: this.temp_presc_parent.parent_id,
                    prescription: this.temp_presc_parent.parent_name,
                    used: `${con_signatura} ${this.temp_presc_parent.consume_time} ${con_utilization}`,
                    value: 1
                }
            ) 
            this.active.modal.presc_parent = false
        },

        onSearchCatalog(search, loading) {
            if (search.length >= 3) {
                loading(true);
                this.OptionsCatalog = this.catalog_list.filter(item => {
                    return item?.title && 
                        typeof item.title === 'string' && 
                        item.title.toLowerCase().includes(search.toLowerCase());
                });
                loading(false);
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

        openPreItem (event) {
            this.catalog = ''
            if (this.prescription === '') return alert("Silakan Pilih Sediaan Terlebih Dahulu")

            if (this.prescription.value != 1) {
                this.input_prescription.form_prescription.push({ 
                    code: event?.data_catalog?.code,
                    name: event?.data_catalog?.name,
                    pharmacy_catalog_id: event?.data_catalog?.id, 
                    package_id: event?.data_catalog?.package_id,
                    dose: event?.data_catalog?.dose,
                    sum_dose: event?.data_catalog?.dose+' '+event?.data_catalog?.dose_name, 
                    package_name: event?.data_catalog?.package_name,
                    prescription_type_id: this.prescription.value,
                    prescription_type_name: this.prescription.title,
                    value_doctor: 0,
                    value_package: 1,
                    used: '',
                    desc: '',
                    sort: this.input_prescription.form_prescription.length, 
                })
                return
            }

            this.temp_presc_items.parent_id = ''
            this.temp_presc_items.parent_name = ''
            this.temp_presc_items.catalog_name = ''
            this.temp_presc_items.signatura = ''
            this.temp_presc_items.other_signatura = ''
            this.temp_presc_items.consume_time = ''
            this.temp_presc_items.utilization = ''
            this.temp_presc_items.other_utilization = ''
            this.temp_presc_items.data_catalog = {}

            let temp_pharmacy_catalog_id = event?.data_catalog?.id
            try {                
                this.$store.dispatch('catalog/getCatalogReference', temp_pharmacy_catalog_id).then((response) => {
                    if (response?.length > 0) {
                        this.temp_presc_items.parent_id = this.prescription.value
                        this.temp_presc_items.parent_name = this.prescription.title
                        this.temp_presc_items.data_catalog = event?.data_catalog
                        this.temp_presc_items.catalog_name = event?.data_catalog?.name
                        this.temp_presc_items.consume_time = this.getConsumeTime({
                            morning: response[0]?.morning,
                            afternoon: response[0]?.afternoon,
                            evening: response[0]?.evening,
                        })

                        let res_signatura = response[0]?.signatura
                        if (res_signatura) {
                            const is_inOption = this.OptionsSignatura.find((item) => item.value == res_signatura);
                            this.temp_presc_items.signatura = is_inOption ? res_signatura : 'other'
                            this.temp_presc_items.other_signatura = is_inOption ? '' : res_signatura
                        } else {
                            this.temp_presc_items.signatura = ''
                        }
                    } else {
                        this.temp_presc_items.parent_id = this.prescription.value
                        this.temp_presc_items.parent_name = this.prescription.title
                        this.temp_presc_items.data_catalog = event?.data_catalog
                        this.temp_presc_items.catalog_name = event?.data_catalog?.name
                    }
                    
                    this.active.modal.presc_items = true
                    // console.log('this.temp_presc_items: ', this.temp_presc_items);
                }, error => {
                    console.error('Error while GET catalog/getCatalogReference at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET catalog/getCatalogReference at: ', error);
            }
        },

        addPrescItem() {
            let con_signatura = this.temp_presc_items.signatura == 'other' ? this.temp_presc_items.other_signatura : this.temp_presc_items.signatura
            let con_utilization = this.temp_presc_items.utilization == 'other' ? this.temp_presc_items.other_utilization : this.temp_presc_items.utilization

            this.input_prescription.form_prescription.push({ 
                code: this.temp_presc_items?.data_catalog?.code,
                name: this.temp_presc_items?.data_catalog?.name,
                pharmacy_catalog_id: this.temp_presc_items?.data_catalog?.id, 
                package_id: this.temp_presc_items?.data_catalog?.package_id,
                dose: this.temp_presc_items?.data_catalog?.dose,
                sum_dose: this.temp_presc_items?.data_catalog?.dose+' '+this.temp_presc_items?.data_catalog?.dose_name, 
                package_name: this.temp_presc_items?.data_catalog?.package_name,
                prescription_type_id: this.temp_presc_items.parent_id,
                prescription_type_name: this.temp_presc_items.parent_name,
                value_doctor: 0,
                value_package: 1,
                used: `${con_signatura}`,
                desc: `${this.temp_presc_items.consume_time} ${con_utilization}`,
                sort: this.input_prescription.form_prescription.length, 
            })
            this.active.modal.presc_items = false
        },

        deletePRES (index) {
            this.input_prescription.form_prescription.splice(index, 1)
        },
        
        // validationForm () {
        //     if (this.form_soap.id == '') {
        //         this.form_soap.doctor_in_charge_id = this.userData.employee_id
        //         this.form_soap.registration_destination_id = this.regData.registration_destination_id

        //         if (this.regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e') {
        //             this.form_soap.is_hemodialysis = 1
        //         }

        //         if (this.regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a') {
        //             this.form_soap.is_medical_rehabilitation = 1
        //         }

        //     } else {
        //         if (this.form_soap.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e') {
        //             this.form_soap.is_hemodialysis = 1
        //         }

        //         if (this.form_soap.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a') {
        //             this.form_soap.is_medical_rehabilitation = 1
        //         }
        //     }
        //     // console.log('form soap: ', this.form_soap);

        //     try {
        //         this.isDisabled = true
        //         const formData = this.form_soap
        //         this.$store.dispatch('soap/formPostPut', formData).then(res => {
        //             this.notification('Success','CheckIcon','success','You have successfully')
        //             this.active.modal.main = false

        //             localStorage.setItem("soap", '')
        //             this.isDisabled = false
        //             this.getAssessment()
        //             this.resetSoapForm()

        //             // this.socket.emit('SEND_LAB', []);
        //             // this.socket.emit('SEND_RAD', []);
        //             // this.socket.emit('SEND_PRS', []);
        //         }, 
        //         err => {
        //             this.notification('Failed','CloseIcon','danger','You have unsuccessful')
        //             this.isDisabled = false
        //         })
        //     } catch (error) {
        //         this.notification('Failed','CloseIcon','danger','You have unsuccessful')
        //         this.isDisabled = false
        //         console.error('Error at: ', error);
        //     }
        // },

        async resetSoapForm() {
            this.form_anamnesa.doctor_in_charge_id = ''
            this.form_anamnesa.registration_destination_id = ''
            this.form_anamnesa.is_initial_inpatient = 0
            this.form_anamnesa.anamesa_type_id = 1 
            this.form_anamnesa.family_relation_id = ''
            this.form_anamnesa.family_name = ''
            this.form_anamnesa.main_complaint = ''
            this.form_anamnesa.history_disease_now = ''
            this.form_anamnesa.history_disease_past = ''
            this.form_anamnesa.history_drugs = ''
            this.form_anamnesa.allergies =  []

            this.form_vital.doctor_in_charge_id = ''
            this.form_vital.registration_destination_id = ''
            this.form_vital.is_initial_inpatient = 0
            this.form_vital.systolic_blood_pressure = ''
            this.form_vital.diastolic_blood_pressure = ''
            this.form_vital.pulse_frequency = ''
            this.form_vital.breath_frequency = ''
            this.form_vital.oximetry_frequency = ''
            this.form_vital.temperature = ''
            this.form_vital.oxygen_type_id = ''
            this.form_vital.aware = ''
            this.form_vital.eye_score = ''
            this.form_vital.verbal_score = ''
            this.form_vital.motor_score = ''
            this.form_vital.weight = ''
            this.form_vital.height = ''
            this.form_vital.imt = ''
            this.form_vital.head_circumference = ''
            this.form_vital.chest_circumference = ''
            this.form_vital.arm_circumference = ''
            this.form_vital.stomach_circumference = ''
            this.form_vital.knee_circumference = ''

            this.form_generalis.doctor_in_charge_id = ''
            this.form_generalis.registration_destination_id = ''
            this.form_generalis.is_initial_inpatient = 0
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
            this.form_generalis.ophthal_status = ''

            this.form_generalis.ophthal_od.visus = 12
            this.form_generalis.ophthal_od.tio_digital = 1
            this.form_generalis.ophthal_od.tio_tonometri_sciots = ''
            this.form_generalis.ophthal_od.eye_position = 1
            this.form_generalis.ophthal_od.eye_movement = 1
            this.form_generalis.ophthal_od.super_silia = 1
            this.form_generalis.ophthal_od.palpebra_edema = 1
            this.form_generalis.ophthal_od.palpebra_ptosis = 1
            this.form_generalis.ophthal_od.palpebra_lagophtalmus = 1
            this.form_generalis.ophthal_od.palpebra_tumor = 1
            this.form_generalis.ophthal_od.apparatus_lakrimalis = 1
            this.form_generalis.ophthal_od.silia = 1
            this.form_generalis.ophthal_od.conjunctive_hiperemis = 1
            this.form_generalis.ophthal_od.conjunctive_bleed = 1
            this.form_generalis.ophthal_od.cornea = 1
            this.form_generalis.ophthal_od.camera_oculi = 1
            this.form_generalis.ophthal_od.iris_color = 2
            this.form_generalis.ophthal_od.iris_cripte = 1
            this.form_generalis.ophthal_od.pupil_form = 1
            this.form_generalis.ophthal_od.pupil_diameter = 4
            this.form_generalis.ophthal_od.pupil_direct_light = 1
            this.form_generalis.ophthal_od.pupil_indirect_light = 1
            this.form_generalis.ophthal_od.lens = 1
            this.form_generalis.ophthal_od.shadow_test = 1
            this.form_generalis.ophthal_od.glandula = 1
            this.form_generalis.ophthal_od.field_view = 1
            this.form_generalis.ophthal_od.fundus = ''

            this.form_generalis.ophthal_os.visus = 12
            this.form_generalis.ophthal_os.tio_digital = 1
            this.form_generalis.ophthal_os.tio_tonometri_sciots = ''
            this.form_generalis.ophthal_os.eye_position = 1
            this.form_generalis.ophthal_os.eye_movement = 1
            this.form_generalis.ophthal_os.super_silia = 1
            this.form_generalis.ophthal_os.palpebra_edema = 1
            this.form_generalis.ophthal_os.palpebra_ptosis = 1
            this.form_generalis.ophthal_os.palpebra_lagophtalmus = 1
            this.form_generalis.ophthal_os.palpebra_tumor = 1
            this.form_generalis.ophthal_os.apparatus_lakrimalis = 1
            this.form_generalis.ophthal_os.silia = 1
            this.form_generalis.ophthal_os.conjunctive_hiperemis = 1
            this.form_generalis.ophthal_os.conjunctive_bleed = 1
            this.form_generalis.ophthal_os.cornea = 1
            this.form_generalis.ophthal_os.camera_oculi = 1
            this.form_generalis.ophthal_os.iris_color = 2
            this.form_generalis.ophthal_os.iris_cripte = 1
            this.form_generalis.ophthal_os.pupil_form = 1
            this.form_generalis.ophthal_os.pupil_diameter = 4
            this.form_generalis.ophthal_os.pupil_direct_light = 1
            this.form_generalis.ophthal_os.pupil_indirect_light = 1
            this.form_generalis.ophthal_os.lens = 1
            this.form_generalis.ophthal_os.shadow_test = 1
            this.form_generalis.ophthal_os.glandula = 1
            this.form_generalis.ophthal_os.field_view = 1
            this.form_generalis.ophthal_os.fundus = ''

            this.form_diagnosis.doctor_in_charge_id = ''
            this.form_diagnosis.registration_destination_id = ''
            this.form_diagnosis.is_initial_inpatient = 0
            this.form_diagnosis.icd10 = []
            this.form_diagnosis.icd10_notes = ''
            this.form_diagnosis.non_icd10 =  []

            this.form_procedure.doctor_in_charge_id = ''
            this.form_procedure.registration_destination_id = ''
            this.form_procedure.is_initial_inpatient = 0
            this.form_procedure.icd9cm =  []
            this.form_procedure.therapy_plan = ''
            this.form_procedure.transaction_doctor =  []
            this.form_procedure.advice = ''

            this.form_laboratory.doctor_in_charge_id = ''
            this.form_laboratory.registration_destination_id = ''
            this.form_laboratory.laboratory_request_items =  []
            
            this.form_radiology.doctor_in_charge_id = ''
            this.form_radiology.registration_destination_id = ''
            this.form_radiology.radiology_request_items =  []
            
            this.form_physiotherapy.doctor_in_charge_id = ''
            this.form_physiotherapy.registration_destination_id = ''
            this.form_physiotherapy.physiotherapy_request_items =  []
            
            this.input_prescription.doctor_in_charge_id = ''
            this.input_prescription.registration_destination_id = ''
            this.input_prescription.pharmacy_depo_id = ''
            this.input_prescription.form_prescription_parent =  []
            this.input_prescription.form_prescription =  []

            this.form_hemodialysis.main_diagnosis = ''
            this.form_hemodialysis.etiology = ''
            this.form_hemodialysis.comorbidities = ''
            this.form_hemodialysis.frequency_hd = ''
            this.form_hemodialysis.vascular_access = ''

            this.form_rehabilitation.functional_testing = ''
            this.form_rehabilitation.suspected_disease_type_id = 0
            this.form_rehabilitation.suspected_disease = ''
            this.form_rehabilitation.result = ''
            this.form_rehabilitation.conclusion = ''
            this.form_rehabilitation.recommendation = ''
            this.form_rehabilitation.functional_diagnosis = ''
            this.form_rehabilitation.medical_diagnosis = ''

        },

        getExcPresciption(soap_id, doctor_in_charge_id) {
            try {
                this.$store.dispatch('prescription/getDetail', {soap_id: soap_id, doctor_in_charge_id: doctor_in_charge_id }).then((response) => {
                    this.exclusive_presc_list = response
                    this.getAssessment()
                })
                
            } catch (error) {
                console.error('Error while GET data from prescription/getDetail at: ', error);
            }
        },

        openModalExcPresc(){
            this.resetExcPresc()
            this.active.modal.exc_presc = !this.active.modal.exc_presc
            // this.form_exclusive_presc.id = ''
        },
        resetExcPresc() {
            this.form_exclusive_presc.id = ''
            this.form_exclusive_presc.form_prescription_parent = []
            this.form_exclusive_presc.form_prescription = []
            this.exc_prescription = ''
            this.exc_catalog = ''
        },
        onEditPresc(obj) {
            this.resetExcPresc()
            this.active.modal.exc_presc = true
            this.form_exclusive_presc.id = obj.id

            if (obj.form_prescription_parent) {
                const mappedPrescParent = obj.form_prescription_parent.map(item => {
                    let name = item.id ? this.getParentName(item.id) : ''

                    return {
                        id: item.id,
                        used: item.used,
                        value: item.value,
                        prescription: name,
                    };
                });                
                this.form_exclusive_presc.form_prescription_parent = mappedPrescParent;
            } else {
                this.form_exclusive_presc.form_prescription_parent = []
            }

            if (obj.form_prescription) {
                const mappedPresc = obj.form_prescription.map(item => {
                    let name = item.id ? this.getParentName(item.id) : ''

                    return {
                        pharmacy_catalog_id: item.pharmacy_catalog_id, 
                        prescription_type_id: item.prescription_item_type_id,
                        package_id: item.package_id,
                        package_name: item.package_name,
                        dose: item.dose,
                        sum_dose: `${item.dose} ${item.dose_name || ''}`, 
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
                this.form_exclusive_presc.form_prescription = mappedPresc;                                  
            } else {
                this.form_exclusive_presc.form_prescription = [];                                  
            }
        },

        removeExcPresc(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.no_order} ?`, {
                title: 'Please Confirm', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                this.$store.dispatch('prescription/removeOrder', obj.id)
                .then(response => {
                    if (response.status === 200 || response.status === 201) {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.getExcPresciption(this.form_exclusive_presc.soap_id, this.form_exclusive_presc.doctor_in_charge_id)
                        // this.socket.emit('SEND_PRS', []);
                    } else {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    }
                },
                error => {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                }) 
                }
            })
        },

        validationExcPrescription(){
            this.active.disabled.exc_presc = true
            this.form_exclusive_presc.pharmacy_depo_id = '297c44f0-12ef-40e6-b9a7-c831065b3372' // depo central

            let inp_presc = this.form_exclusive_presc?.form_prescription 
            let inp_parent = this.form_exclusive_presc?.form_prescription_parent 
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
                        this.$store.dispatch('prescription/formPostPut', this.form_exclusive_presc).then((response) => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.getExcPresciption(this.form_exclusive_presc.soap_id, this.form_exclusive_presc.doctor_in_charge_id)
                            
                            this.active.modal.exc_presc = false
                            this.form_exclusive_presc.id = ''
                            this.form_exclusive_presc.form_prescription_parent = []
                            this.form_exclusive_presc.form_prescription = []
                            this.exc_prescription = ''
                            this.exc_catalog = ''
                            this.active.disabled.exc_presc = false
                            // this.socket.emit('SEND_PRS', []);
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        this.active.disabled.exc_presc = false
                        console.error('Error at: ', error);
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

        hasItemsInCategory(items, categoryId) {
            if (items) {
                return items.some(item => item.prescription_item_type_id === categoryId);
            }
        },

        rowIGDPrescription(item, role) {
            if (!item || role !== 'row') return ''
            if (item.flag == 0) return ''
            if (item.flag == 1) return 'table-warning-400'
        },

        openEXCPreParent(event) {
            this.exc_prescription_type = event
            let index = this.form_exclusive_presc.form_prescription_parent.findIndex((item) => item.id == event.value);

            if (index !== -1) return console.log("Object already exists");

            if (event.value != 1) {
                this.temp_exc_parent.parent_id = event.value
                this.temp_exc_parent.parent_name = event.title
                this.temp_exc_parent.signatura = ''
                this.temp_exc_parent.other_signatura = ''
                this.temp_exc_parent.consume_time = ''
                this.temp_exc_parent.utilization = ''
                this.temp_exc_parent.other_utilization = ''
                this.active.modal.presc_exc_parent = true
                return
            }

            this.form_exclusive_presc.form_prescription_parent.push({
                id: event.value,
                prescription: event.title,
                used: '',
                value: event.value == 1 ? 0 : 1
            })
        },

        closeEXCPreParent() {
            this.active.modal.presc_exc_parent = false
            this.exc_catalog = ''
            this.exc_prescription = ''
            this.exc_prescription_type = {} 
        },

        addEXCParent() {
            let con_signatura = this.temp_exc_parent.signatura == 'other' ? this.temp_exc_parent.other_signatura : this.temp_exc_parent.signatura
            let con_utilization = this.temp_exc_parent.utilization == 'other' ? this.temp_exc_parent.other_utilization : this.temp_exc_parent.utilization

            this.form_exclusive_presc.form_prescription_parent.push(
                {
                    id: this.temp_exc_parent.parent_id,
                    prescription: this.temp_exc_parent.parent_name,
                    used: `${con_signatura} ${this.temp_exc_parent.consume_time} ${con_utilization}`,
                    value: 1
                }
            ) 
            this.active.modal.presc_exc_parent = false
        },

        onSearchEXCCatalog(search, loading) {
            if (search.length >= 3) {
                loading(true);
                this.ExcOptionsCatalog = this.catalog_list.filter(item => {
                    return item?.title && 
                        typeof item.title === 'string' && 
                        item.title.toLowerCase().includes(search.toLowerCase());
                });
                loading(false);
            }
        },

        openEXCPreItem (event) {
            this.exc_catalog = ''
            if (this.exc_prescription === '') return alert("Silakan Pilih Sediaan Terlebih Dahulu")

            if (this.exc_prescription.value != 1) {
                this.form_exclusive_presc.form_prescription.push({ 
                    code: event?.data_catalog?.code,
                    name: event?.data_catalog?.name,
                    pharmacy_catalog_id: event?.data_catalog?.id, 
                    package_id: event?.data_catalog?.package_id,
                    dose: event?.data_catalog?.dose,
                    sum_dose: event?.data_catalog?.dose+' '+event?.data_catalog?.dose_name, 
                    package_name: event?.data_catalog?.package_name,
                    prescription_type_id: this.exc_prescription.value,
                    prescription_type_name: this.exc_prescription.title,
                    value_doctor: 0,
                    value_package: 1,
                    used: '',
                    desc: '',
                    sort: this.form_exclusive_presc.form_prescription.length, 
                })
                return
            }

            this.temp_exc_items.parent_id = ''
            this.temp_exc_items.parent_name = ''
            this.temp_exc_items.catalog_name = ''
            this.temp_exc_items.signatura = ''
            this.temp_exc_items.other_signatura = ''
            this.temp_exc_items.consume_time = ''
            this.temp_exc_items.utilization = ''
            this.temp_exc_items.other_utilization = ''
            this.temp_exc_items.data_catalog = {}

            let temp_pharmacy_catalog_id = event?.data_catalog?.id
            try {                
                this.$store.dispatch('catalog/getCatalogReference', temp_pharmacy_catalog_id).then((response) => {
                    if (response?.length > 0) {
                        this.temp_exc_items.parent_id = this.exc_prescription.value
                        this.temp_exc_items.parent_name = this.exc_prescription.title
                        this.temp_exc_items.data_catalog = event?.data_catalog
                        this.temp_exc_items.catalog_name = event?.data_catalog?.name
                        this.temp_exc_items.consume_time = this.getConsumeTime({
                            morning: response[0]?.morning,
                            afternoon: response[0]?.afternoon,
                            evening: response[0]?.evening,
                        })

                        let res_signatura = response[0]?.signatura
                        if (res_signatura) {
                            const is_inOption = this.OptionsSignatura.find((item) => item.value == res_signatura);
                            this.temp_exc_items.signatura = is_inOption ? res_signatura : 'other'
                            this.temp_exc_items.other_signatura = is_inOption ? '' : res_signatura
                        } else {
                            this.temp_exc_items.signatura = ''
                        }
                    } else {
                        this.temp_exc_items.parent_id = this.exc_prescription.value
                        this.temp_exc_items.parent_name = this.exc_prescription.title
                        this.temp_exc_items.data_catalog = event?.data_catalog
                        this.temp_exc_items.catalog_name = event?.data_catalog?.name
                    }
                    
                    this.active.modal.presc_exc_items = true
                    // console.log('this.temp_exc_items: ', this.temp_exc_items);
                }, error => {
                    console.error('Error while GET catalog/getCatalogReference at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET catalog/getCatalogReference at: ', error);
            }
        },

        addEXCPrescItem() {
            let con_signatura = this.temp_exc_items.signatura == 'other' ? this.temp_exc_items.other_signatura : this.temp_exc_items.signatura
            let con_utilization = this.temp_exc_items.utilization == 'other' ? this.temp_exc_items.other_utilization : this.temp_exc_items.utilization

            this.form_exclusive_presc.form_prescription.push({ 
                code: this.temp_exc_items?.data_catalog?.code,
                name: this.temp_exc_items?.data_catalog?.name,
                pharmacy_catalog_id: this.temp_exc_items?.data_catalog?.id, 
                package_id: this.temp_exc_items?.data_catalog?.package_id,
                dose: this.temp_exc_items?.data_catalog?.dose,
                sum_dose: this.temp_exc_items?.data_catalog?.dose+' '+this.temp_exc_items?.data_catalog?.dose_name, 
                package_name: this.temp_exc_items?.data_catalog?.package_name,
                prescription_type_id: this.temp_exc_items.parent_id,
                prescription_type_name: this.temp_exc_items.parent_name,
                value_doctor: 0,
                value_package: 1,
                used: `${con_signatura}`,
                desc: `${this.temp_exc_items.consume_time} ${con_utilization}`,
                sort: this.form_exclusive_presc.form_prescription.length, 
            })
            this.active.modal.presc_exc_items = false
        },

        deleteEXCPRES (index) {
            this.form_exclusive_presc.form_prescription.splice(index, 1)
        },

        getAssessment() {
            let message = 'initial dulu'
            this.$emit("refreshAssessment", message);
        },
        
        // getData (reg_des_id) {
			// this.$store.dispatch('registration/getById', reg_des_id).then(data => {
            //     this.regData = data[0]
                                
                // this.getTransactionOptions()
                // this.getSupportPackage()

                // history
                // this.getCpptHistory(this.regData.norm)
                // this.getNurseHistory(this.regData.norm)
                // this.getSoapHistory(this.regData.norm)

                // modal-heder
                // this.temp_header.alias_name = this.regData.alias_name
                // this.temp_header.name = this.regData.name
                // this.temp_header.norm = this.regData.norm
                // this.temp_header.birth_date = this.regData.birth_date
                // this.temp_header.weight = this.regData?.ttv?.weight
                // this.temp_header.height = this.regData?.ttv?.height
            // })
        // },

        getMasterHemodialysis() {
            try {                
                this.$store.dispatch('hemodialysis/getListMaster', {})
                .then(
                    (response) => {
                        let data = response.rows
                        if(data && data.length > 0) {
                            for (let i = 0; i < data.length; i++) {
                                const el = data[i];
                                if (el.code == 'N') {
                                    this.OptionsDiagnose.push({ value: el.name, title: el.name })
                                } else if(el.code == 'E') {
                                    this.OptionsEtiology.push({ value: el.name, title: el.name })
                                } else if(el.code == 'P') {
                                    this.OptionsComorbidities.push({ value: el.name, title: el.name })                                    
                                }
                            }
                        }
                    }, error => {
                        console.error('Error while GET hemodialysis/getListMaster at: ', error);
                    }
                )
            } catch (error) {
                console.error('Error catch while GET hemodialysis/getListMaster at: ', error);
            }
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

        // copyAllSoap(event, obj) {
        //     const button = event.target;
        //     button.innerText = 'COPIED ALL';
            
        //     // cek dulu lab, rad, & presc nya udah diproses atau blm
        //     // func(obj);
        //     this.soapAnamnesa(obj)
        //     this.soapVitalSign(obj)
        //     this.soapDiagnosis(obj)
        //     this.soapProcedure(obj)
        //     this.soapLaboratory(obj)
        //     this.soapRadiology(obj)
        //     this.soapPhysiotherapy(obj)
        //     this.soapPrescription(obj)

        //     setTimeout(() => {
        //         button.innerText = 'COPY ALL';
        //     }, 1000);
        // },

        soapAnamnesa(obj) {            
            this.form_anamnesa.anamesa_type_id = obj.anamesa_type_id
            this.form_anamnesa.family_name = obj.family_name
            this.form_anamnesa.family_relation_id = obj.family_relation_id
            this.form_anamnesa.main_complaint = obj.main_complaint
            this.form_anamnesa.history_disease_now = obj.history_disease_now
            this.form_anamnesa.history_disease_past = obj.history_disease_past
            this.form_anamnesa.history_drugs = obj.history_drugs
        },

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
            this.form_vital.weight = obj?.weight
            this.form_vital.height = obj?.height
            this.form_vital.imt = obj?.imt
            this.form_vital.head_circumference = obj?.head_circumference
            this.form_vital.chest_circumference = obj?.chest_circumference
            this.form_vital.arm_circumference = obj?.arm_circumference
            this.form_vital.stomach_circumference = obj?.stomach_circumference
            this.form_vital.knee_circumference = obj?.knee_circumference
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

            this.form_procedure.advice = obj.advice;

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
            if (obj.prescription) {
                let seenParentIds = new Set();
                this.input_prescription.form_prescription_parent = obj.prescription.reduce((acc, req) => {
                    req.form_prescription_parent.forEach(item => {
                        if (!seenParentIds.has(item.id)) {
                            seenParentIds.add(item.id);
                            acc.push({
                                id: item.id,
                                used: item.used, 
                                value: item.value,
                                prescription: this.getParentName(item.id),
                            });
                        }
                    });
                    return acc;
                }, []);

                this.input_prescription.form_prescription = obj.prescription.reduce((acc, req) => {
                    req.form_prescription.forEach(item => {
                        acc.push({
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
                            prescription_type_name: this.getParentName(item.prescription_item_type_id),
                        });
                    });
                    return acc;
                }, []);
            }
        },

        // copyAllNurse(event, obj) {
        //     const button = event.target;
        //     button.innerText = 'COPIED ALL';
            
        //     this.nurseAnamnesa(obj)
        //     this.nurseVitalSign(obj)

        //     setTimeout(() => {
        //         button.innerText = 'COPY ALL';
        //     }, 1000);
        // },

        nurseAnamnesa(obj) {            
            this.form_anamnesa.anamesa_type_id = obj.anamesa_type_id
            this.form_anamnesa.family_name = obj.family_name
            this.form_anamnesa.family_relation_id = obj.family_relation_id
            this.form_anamnesa.main_complaint = obj.main_complaint
            this.form_anamnesa.history_disease_now = obj.history_disease_now
            this.form_anamnesa.history_disease_past = obj.history_disease_past
        },

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
            this.form_vital.weight = obj?.weight
            this.form_vital.height = obj?.height
            this.form_vital.imt = obj?.imt
            this.form_vital.head_circumference = obj?.head_circumference
            this.form_vital.chest_circumference = obj?.chest_circumference
            this.form_vital.arm_circumference = obj?.arm_circumference
            this.form_vital.stomach_circumference = obj?.stomach_circumference
            this.form_vital.knee_circumference = obj?.knee_circumference
        },

        // copyAllCppt(event, obj) {
        //     const button = event.target;
        //     button.innerText = 'COPIED ALL';
            
        //     // cek dulu lab, rad, & presc nya udah diproses atau blm
        //     this.cpptObjective(obj)
        //     this.cpptAssesment(obj)
        //     this.cpptLaboratory(obj)
        //     this.cpptRadiology(obj)
        //     this.cpptPhysiotherapy(obj)
        //     this.cpptPrescription(obj)

        //     setTimeout(() => {
        //         button.innerText = 'COPY ALL';
        //     }, 1000);
        // },

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
            this.form_vital.weight = obj?.weight
            this.form_vital.height = obj?.height
            this.form_vital.imt = obj?.imt
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
            if (obj.non_icd10) {
                for (let i = 0; i < obj.non_icd10.length; i++) {
                    const el = obj.non_icd10[i];
                    this.form_diagnosis.non_icd10.push(el)
                }
            }
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

        cpptPrescription(obj) {
            if (obj.prescription) {
                let seenParentIds = new Set();
                this.input_prescription.form_prescription_parent = obj.prescription.reduce((acc, req) => {
                    req.form_prescription_parent.forEach(item => {
                        if (!seenParentIds.has(item.id)) {
                            seenParentIds.add(item.id);
                            acc.push({
                                id: item.id,
                                used: item.used, 
                                value: item.value,
                                prescription: this.getParentName(item.id),
                            });
                        }
                    });
                    return acc;
                }, []);

                this.input_prescription.form_prescription = obj.prescription.reduce((acc, req) => {
                    req.form_prescription.forEach(item => {
                        acc.push({
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
                            prescription_type_name: this.getParentName(item.prescription_item_type_id),
                        });
                    });
                    return acc;
                }, []);
            }
        },

        templatePrescription(obj) {
            if (obj) {
                let var_presc = obj.form_prescription
                let var_parent = obj.form_prescription_parent
                
                if (var_parent) {
                    const mappedPrescParent = var_parent.map(item => {
                        return {
                            id: item?.id,
                            used: item?.used,
                            value: item?.value,
                            prescription: this.getParentName(item?.id || ''),
                        };
                    });                
                    this.input_prescription.form_prescription_parent = mappedPrescParent;
                } else {
                    this.input_prescription.form_prescription_parent = []
                }

                if (var_presc) {
                    const mappedPresc = var_presc.map(item => {
                        return {
                            pharmacy_catalog_id: item?.pharmacy_catalog_id, 
                            prescription_type_id: item?.prescription_item_type_id,
                            package_id: item?.package_id,
                            package_name: item?.package_name,
                            dose: item?.dose,
                            sum_dose: `${item?.dose} ${item?.dose_name || ''}`, 
                            value_package: item.qty,
                            sort: item?.sort, 
                            used: item?.used,
                            desc: item?.desc,

                            code: item?.pharmacy_catalog_code,
                            name: item?.pharmacy_catalog_name,
                            value_doctor: item?.qty_doctor || 0,
                            prescription_type_name: this.getParentName(item?.prescription_item_type_id || ''),
                        };
                    });      
                    this.input_prescription.form_prescription = mappedPresc;                                  
                } else {
                    this.input_prescription.form_prescription = [];                                  
                }
            }
        },

        templateAnamnesa(obj) {
            this.form_anamnesa.anamesa_type_id = obj?.anamesa_type_id
            this.form_anamnesa.family_name = obj?.family_name
            this.form_anamnesa.family_relation_id = obj?.family_relation_id
            this.form_anamnesa.main_complaint = obj?.main_complaint
            this.form_anamnesa.history_disease_now = obj?.history_disease_now
            this.form_anamnesa.history_disease_past = obj?.history_disease_past
            this.form_anamnesa.history_drugs = obj?.history_drugs
        },

        triggerRadResult(message) {
            this.$refs.radModalResult.seeRadiologyResult(message);
        },

        triggerLabResult(message) {
            this.$refs.labModalResult.seeLaboratoryResult(message);
        },

        openAsPrescTemplate(obj) {
            this.resetPrescTemplate()
            if (obj.parent) {
                const mappedPrescParent = obj.parent.map(item => {
                    return {
                        id: item.id,
                        used: item.used,
                        value: item.value,
                        prescription: this.getParentName(item?.id || ''),
                    };
                });                
                this.presc_inp_templating.form_prescription_parent = mappedPrescParent;
            } else {
                this.presc_inp_templating.form_prescription_parent = []
            }

            if (obj.item) {
                const mappedPresc = obj.item.map(unit => {
                    return {
                        pharmacy_catalog_id: unit.pharmacy_catalog_id, 
                        prescription_type_id: unit?.prescription_type_id || unit?.prescription_item_type_id,
                        package_id: unit?.package_id,
                        package_name: unit?.package_name,
                        dose: unit?.dose,
                        sum_dose: `${unit?.dose} ${unit?.dose_name || ''}`, 
                        value_package: unit?.qty || unit?.value_package,
                        sort: unit?.sort, 
                        used: unit?.used,
                        desc: unit?.desc,

                        code: unit?.code || unit?.pharmacy_catalog_code,
                        name: unit?.name || unit?.pharmacy_catalog_name,
                        value_doctor: unit.qty_doctor || 0,
                        prescription_type_name: this.getParentName(unit?.prescription_type_id || unit?.prescription_item_type_id || ''),
                    };
                });      
                this.presc_inp_templating.form_prescription = mappedPresc;                                  
            } else {
                this.presc_inp_templating.form_prescription = [];                                  
            }
                
            // console.log('this.presc_inp_templating:', this.presc_inp_templating)
            this.active.modal.mdl_presc_template = true
        },

        savePrescTemplate() {
            // console.log('save:', this.presc_inp_templating)
            try {
                this.active.disabled.dsb_presc_template = true
                this.$store.dispatch('templating/postPutPrescTemplate', this.presc_inp_templating)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal.mdl_presc_template = false
                        this.resetPrescTemplate()
                        this.getPrescTemplate()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.dsb_presc_template = false
            }
        },

        resetPrescTemplate() {
            this.presc_inp_templating.id = ''
            this.presc_inp_templating.name = ''
            this.presc_inp_templating.form_prescription_parent = []
            this.presc_inp_templating.form_prescription = []
        },

        getPrescTemplate() {
            try {                
                this.$store.dispatch('templating/getPrescTemplate').then((response) => {
                    this.presc_list_templating = response
                    this.presc_filtered = response
                    this.$emit("prescChildTemplate", response);
                }, error => {
                    console.error('Error while GET templating/getPrescTemplate at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET templating/getPrescTemplate at: ', error);
            }
        },

        openAsAnamnTemplate() {
            this.resetPrescTemplate()

            this.anamn_form_templating.anamesa_type_id = this.form_anamnesa.anamesa_type_id || 1
            this.anamn_form_templating.family_name = this.form_anamnesa.family_name || ''
            this.anamn_form_templating.family_relation_id = this.form_anamnesa.family_relation_id || ''
            this.anamn_form_templating.main_complaint = this.form_anamnesa.main_complaint || ''
            this.anamn_form_templating.history_disease_now = this.form_anamnesa.history_disease_now || ''
            this.anamn_form_templating.history_disease_past = this.form_anamnesa.history_disease_past || ''
            this.anamn_form_templating.history_drugs = this.form_anamnesa.history_drugs || ''

            this.active.modal.mdl_anamn_template = true
        },

        saveAnamnTemplate() {
            try {
                this.active.disabled.dsb_anamn_template = true
                this.$store.dispatch('templating/postPutAnamTemplate', this.anamn_form_templating)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal.mdl_anamn_template = false
                        this.resetPrescTemplate()
                        this.getAnamnTemplate()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.dsb_anamn_template = false
            }
        },

        resetPrescTemplate() {
            this.anamn_form_templating.id = ''
            this.anamn_form_templating.name = ''
            this.anamn_form_templating.anamesa_type_id = 1
            this.anamn_form_templating.family_name = ''
            this.anamn_form_templating.family_relation_id = ''
            this.anamn_form_templating.main_complaint = ''
            this.anamn_form_templating.history_disease_now = ''
            this.anamn_form_templating.history_disease_past = ''
            this.anamn_form_templating.history_drugs = ''
        },

        getAnamnTemplate() {
            try {                
                this.$store.dispatch('templating/getAnamTemplate').then((response) => {
                    this.anamn_list_templating = response
                    this.anamn_filtered = response
                    
                    // trigger refresh pake emit ke dr-stds
                    this.$emit("anamnChildTemplate", response);
                }, error => {
                    console.error('Error while GET templating/getAnamTemplate at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET templating/getAnamTemplate at: ', error);
            }
        },
    },
    watch: {
        // 'OptionsCosting': function(val){
        //     if (val) {
        //         this.getCosting = val
        //     }
        // },
        "form_anamnesa.anamesa_type_id"(newValue) {
            if (newValue == "1") {
                this.form_anamnesa.family_relation_id = ''
                this.form_anamnesa.family_name = ''
            }
        },
        "form_rehabilitation.suspected_disease_type_id"(newValue) {
            if (newValue != "1") {
                this.form_rehabilitation.suspected_disease = ""
            }
        },
        "temp_presc_parent.signatura"(newValue) {
            if (newValue != 'other') {
                this.temp_presc_parent.other_signatura = ''
            }
        },
        "temp_presc_parent.utilization"(newValue) {
            if (newValue != 'other') {
                this.temp_presc_parent.other_utilization = ''
            }
        },
        "temp_presc_items.signatura"(newValue) {
            if (newValue != 'other') {
                this.temp_presc_items.other_signatura = ''
            }
        },
        "temp_presc_items.utilization"(newValue) {
            if (newValue != 'other') {
                this.temp_presc_items.other_utilization = ''
            }
        },
        "temp_exc_parent.signatura"(newValue) {
            if (newValue != 'other') {
                this.temp_exc_parent.other_signatura = ''
            }
        },
        "temp_exc_parent.utilization"(newValue) {
            if (newValue != 'other') {
                this.temp_exc_parent.other_utilization = ''
            }
        },
        "temp_exc_items.signatura"(newValue) {
            if (newValue != 'other') {
                this.temp_exc_items.other_signatura = ''
            }
        },
        "temp_exc_items.utilization"(newValue) {
            if (newValue != 'other') {
                this.temp_exc_items.other_utilization = ''
            }
        },
    },
    created () {
        this.getMasterHemodialysis()

        if (this.$route.params.id) {
            this.reg_des_id = this.$route.params.id
            // this.getData(this.$route.params.id)
        }

        this.$store.dispatch('employee/getParamSpecialist')
    }
}
</script>
<style scoped>
    ::v-deep(.custom-tab-style .nav-link) {
        padding: 8px 10px 6px !important;
    }

    table.custom-border tr td {
        border: 0px;
        padding: 2px;
    }
</style>