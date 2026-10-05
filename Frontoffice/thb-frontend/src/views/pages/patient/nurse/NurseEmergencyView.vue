<template>
    <div>
        <b-modal v-model="active.modal.main" scrollable size="xl" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ASESMEN PERAWAT IGD
                    <span class="text-primary">{{ regData.alias_name }}. {{ regData.name }} | {{ regData.norm }} | {{localDate(regData.birth_date)}} ({{ parsedAge(regData.birth_date) }}) | BB: {{regData?.ttv?.weight | number}} | TB: {{ regData?.ttv?.height | number}}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <div class="row position-relative">
                <div class="col-4">
                    <div class="row">
                        <section class="col-8 position-sticky px-1" :style="{top: '0', height: `${dynamicHeight}`, overflowY: 'auto'}">
                            <ModernLabel label="REFERENSI ASESMEN PASIEN" class="mb-2"/>
                            <b-tabs small content-class="mt-3 text-regular-12" nav-wrapper-class="fs-13" nav-class="custom-tab-style">
                                <b-tab title="DOKTER">
                                    <div v-for="(item, index) in history.soap" class="accordion" role="tablist">
                                        <b-card no-body class="mb-1">
                                            <b-card-header block v-b-toggle="'accordion-soap-'+item.id" header-tag="header" class="pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                                <div>
                                                    <div class="font-weight-bold">ASESMEN {{ localDateTime(item.date_added) }}</div>
                                                    <div>{{ item.doctor_in_charge_name }} | {{ item.health_facility_item_name }}</div>
                                                </div>
                                                <feather-icon icon="ChevronDownIcon" size="18" />
                                            </b-card-header>
                                            <b-collapse :id="`accordion-soap-`+item.id" accordion="soap-accordion" role="tabpanel">
                                                <b-card-body class="p-12 bg-info-300">
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
                                                                <div class="text-bold-14">DIAGNOSA</div>
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
                                <b-tab title="PERAWAT" active>
                                    <div v-for="(item, index) in history.nurse" class="accordion" role="tablist">
                                        <b-card no-body class="mb-1">
                                            <b-card-header block v-b-toggle="'accordion-ns-'+item.id" header-tag="header" class="pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                                <div>
                                                    <div class="font-weight-bold">PERAWAT {{ localDateTime(item.date_added) }}</div>
                                                    <div>{{ item.employee_nurse_name }} | {{ item.health_facility_item_name }}</div>
                                                </div>
                                                <feather-icon icon="ChevronDownIcon" size="18" />
                                            </b-card-header>
                                            <b-collapse :id="`accordion-ns-`+item.id" accordion="ns-accordion" role="tabpanel">
                                                <b-card-body class="p-12 bg-info-300">
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
                                                            <div><b>Hubungan Keluarga: </b>{{ getFamilyName(item?.family_relation_id || 0) }}</div>
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
                                <b-tab title="CPPT">
                                    <div v-for="(item, index) in history.cppt" class="accordion" role="tablist">
                                        <b-card no-body class="mb-1">
                                            <b-card-header block v-b-toggle="'accordion-cppt-'+item.id" header-tag="header" class="pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                                <div>
                                                    <div class="font-weight-bold">CPPT {{ localDateTime(item.date_added) }}</div>
                                                    <div>{{ item.user_added_name }} | {{ item.user_added_role }}</div>
                                                </div>
                                                <feather-icon icon="ChevronDownIcon" size="18" />
                                            </b-card-header>
                                            <b-collapse :id="`accordion-cppt-`+item.id" accordion="cppt-accordion" role="tabpanel">
                                                <b-card-body class="p-12 bg-info-300">
                                                    <!-- <b-button @click="copyAllCppt($event, item)" size="sm" variant="primary" class="btn-block">COPY ALL</b-button> -->
                                                    <section class="card">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">SUBJECTIVE</div>
                                                                <!-- <b-button size="sm" @click="copySelected($event, cpptSubjective, item, 'ANAMNESA')" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button> -->
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div><b>Subjective: </b>{{ item?.subjective || '-'}}</div>
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
                                                                <b>Warna Kulit: </b>{{ getColorSkinName(item?.skin_color || '-') }},
                                                                <b>Perilaku: </b>{{ getBehaviorName(item?.behavior || '-') }},
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
                                                                <!-- <b-button size="sm" @click="copySelected($event, cpptLocalis, item, 'GENERALIS')" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button> -->
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
                                                                <!-- <b-button @click="copySelected($event, cpptDiagnosis, item, 'DIAGNOSIS')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button> -->
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
                                                                <!-- <b-button @click="copySelected($event, cpptProcedure, item, 'PROCEDURE')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button> -->
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
                                                                <!-- <b-button size="sm" @click="copySelected($event, cpptPlan, item, 'PLAN')" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button> -->
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
                                                                <!-- <b-button @click="copySelected($event, cpptLaboratory, item, 'LABORATORY')" v-if="lab_status == 1" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button> -->
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
                                                                <!-- <b-button @click="copySelected($event, cpptRadiology, item, 'RADIOLOGY')" v-if="rad_status == 1" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button> -->
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
                                                                <!-- <b-button @click="copySelected($event, cpptPhysiotherapy, item, 'PHYSIOTHERAPY')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button> -->
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
                                                                <!-- <b-button @click="copySelected($event, cpptPrescription, item, 'PRESC')" v-if="presc_status == 0" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button> -->
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
                                                        <!-- <b-button @click="copySelected($event, copyLaboratory, item, 'LABORATORY')" v-if="lab_status == 1" size="sm" variant="primary" class="fs-12" style="line-height:120%">COPY</b-button> -->
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
                                                        <!-- <b-button @click="copySelected($event, copyRadiology, item, 'RADIOLOGY')" v-if="rad_status == 1" size="sm" variant="primary" class="fs-12" style="line-height:120%">COPY</b-button> -->
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
                                                        <!-- <b-button @click="copySelected($event, copyPhysiotherapy, item, 'PHYSIOTHERAPY')" size="sm" variant="primary" class="fs-12" style="line-height:120%">COPY</b-button> -->
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
                            </b-tabs>  
                        </section>
                        <div class="col-4 bg-primary rounded py-16 px-2 position-sticky" :style="{top: '0', height: `${dynamicHeight}`, overflowY: 'auto'}">
                            <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-0" @click="changeTab('ANAMNESA')" :variant="getMenusVariant('ANAMNESA')">ANAMNESA</b-button>
                            <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('VITAL')" :variant="getMenusVariant('VITAL')">TANDA VITAL</b-button>
                            <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('FUNCTIONAL')" :variant="getMenusVariant('FUNCTIONAL')">FUNGSIONAL</b-button>
                            <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-2" @click="changeTab('EMERGENCY')" :variant="getMenusVariant('EMERGENCY')">IGD</b-button>
                        </div>
                    </div>
                </div>
                <div class="col-8 px-1">
                    <section v-if="active.section == 'ANAMNESA'" class="card">
                        <div class="card-header bg-primary text-white fs-14 font-weight-bold">ANAMNESA</div>
                        <div class="card-body px-20 py-8 position-sticky" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <div class="row">
                                <div class="col-lg-3">
                                    <label class="fs-13 mb-0">Anamnesa</label>
                                    <v-select class="select-size-sm" v-model="form_anamnesa.anamesa_type_id" :options="optAnamnesaType" :reduce="item => item.value" label="title" placeholder="-- Pilih Anamnesa --"></v-select>
                                </div>
                                <template v-if="form_anamnesa.anamesa_type_id == 2">
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Nama Keluarga</label>
                                        <b-form-input type="text" size="sm" v-model="form_anamnesa.family_name" autocomplete="off"></b-form-input>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Hubungan Keluarga</label>
                                        <v-select v-model="form_anamnesa.family_relation_id" class="select-size-sm" :options="optFamilyRelation" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                    </div>
                                </template>
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Keluhan Utama</label>
                                    <b-form-input size="sm" v-model="form_anamnesa.main_complaint" autocomplete="off"></b-form-input>
                                </div>
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Riwayat Penyakit Sekarang</label>
                                    <b-form-textarea rows="3" size="sm" v-model="form_anamnesa.history_disease_now" autocomplete="off"></b-form-textarea>
                                </div>
                                <div class="col-12 mt-8">
                                    <label class="fs-13 mb-0">Riwayat Penyakit Dahulu</label>
                                    <b-form-textarea rows="3" size="sm" v-model="form_anamnesa.history_disease_past" autocomplete="off"></b-form-textarea>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <b-button-group size="sm">
                                <b-button @click="changeSection(active.section, 'VITAL')" :disabled="active.disabled.section" variant="orange-500">
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
                                <b-button @click="changeSection(active.section, 'FUNCTIONAL')" :disabled="active.disabled.section" variant="orange-500">
                                    Simpan & lanjutkan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                            </b-button-group>
                        </div>
                    </section>

                    <section v-if="active.section == 'FUNCTIONAL'" class="card">
                        <div class="card-header bg-primary text-white fs-14 font-weight-bold">FUNGSIONAL</div>
                        <div class="card-body px-20 py-8 position-sticky" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <div class="row">
                                <div class="col-lg-3">
                                    <label class="fs-13 mb-0">Alat Bantu</label>
                                    <b-form-input v-model="form_functional.aids" size="sm" />
                                </div>
                                <div class="col-lg-3">
                                    <label class="fs-13 mb-0">Prothesa</label>
                                    <b-form-input v-model="form_functional.prothesa" size="sm" />
                                </div>
                                <div class="col-lg-3">
                                    <label class="fs-13 mb-0">Cacat Tubuh</label>
                                    <b-form-input v-model="form_functional.disabled" size="sm" />
                                </div>
                                <div class="col-lg-3">
                                    <label class="fs-13 mb-0">Activity Daily Living</label>
                                    <v-select v-model="form_functional.adl_type_id" :options="OptionAdl" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                </div>
                            </div>

                            <div class="mt-16">
                                <ModernLabel label="Skala Nyeri VAS (visual analog scale)" />
                                <b-form-radio-group v-model="form_functional.pain_score" size="sm" class="mt-8">
                                    <div class="row">
                                        <div class="col text-center">
                                            <b-form-radio id="val0" value="0" class="mr-0"></b-form-radio>
                                            <label for="val0" class="d-block" style="line-height: 100%">0</label>
                                            <div class="mt-1">
                                                <img src="@/@core/assets/images/nurse/wong_baker/0.png" width="50px">
                                                <div class="fs-13 mt-10">Tidak nyeri</div>
                                            </div>
                                        </div>
                                        <div class="col text-center">
                                            <b-form-radio value="1" class="mr-0"></b-form-radio>
                                            <label for="val1" class="d-block" style="line-height: 100%">1</label>
                                        </div>
                                        <div class="col text-center">
                                            <b-form-radio id="val2" value="2" class="mr-0"></b-form-radio>
                                            <label for="val2" class="d-block" style="line-height: 100%">2</label>
                                            <div class="mt-1">
                                                <img src="@/@core/assets/images/nurse/wong_baker/2.png" width="50px">
                                                <div class="fs-13 mt-10">Nyeri ringan</div>
                                            </div>
                                        </div>
                                        <div class="col text-center">
                                            <b-form-radio id="val3" value="3" class="mr-0"></b-form-radio>
                                            <label for="val3" class="d-block" style="line-height: 100%">3</label>
                                        </div>
                                        <div class="col text-center">
                                            <b-form-radio id="val4" value="4" class="mr-0"></b-form-radio>
                                            <label for="val4" class="d-block" style="line-height: 100%">4</label>
                                            <div class="mt-1">
                                                <img src="@/@core/assets/images/nurse/wong_baker/4.png" width="50px">
                                                <div class="fs-13 mt-10">Nyeri yang mengganggu</div>
                                            </div>
                                        </div>
                                        <div class="col text-center">
                                            <b-form-radio id="val5" value="5" class="mr-0"></b-form-radio>
                                            <label for="val5" class="d-block" style="line-height: 100%">5</label>
                                        </div>
                                        <div class="col text-center">
                                            <b-form-radio id="val6" value="6" class="mr-0"></b-form-radio>
                                            <label for="val6" class="d-block" style="line-height: 100%">6</label>
                                            <div class="mt-1">
                                                <img src="@/@core/assets/images/nurse/wong_baker/6.png" width="50px">
                                                <div class="fs-13 mt-10">Nyeri yang menyusahkan</div>
                                            </div>
                                        </div>
                                        <div class="col text-center">
                                            <b-form-radio id="val7" value="7" class="mr-0"></b-form-radio>
                                            <label for="val7" class="d-block" style="line-height: 100%">7</label>
                                        </div>
                                        <div class="col text-center">
                                            <b-form-radio id="val8" value="8" class="mr-0"></b-form-radio>
                                            <label for="val8" class="d-block" style="line-height: 100%">8</label>
                                            <div class="mt-1">
                                                <img src="@/@core/assets/images/nurse/wong_baker/8.png" width="50px">
                                                <div class="fs-13 mt-10">Nyeri hebat</div>
                                            </div>
                                        </div>
                                        <div class="col text-center">
                                            <b-form-radio id="val9" value="9" class="mr-0"></b-form-radio>
                                            <label for="val9" class="d-block" style="line-height: 100%">9</label>
                                        </div>
                                        <div class="col text-center">
                                            <b-form-radio id="val10" value="10" class="mr-0"></b-form-radio>
                                            <label for="val10" class="d-block" style="line-height: 100%">10</label>
                                            <div class="mt-1">
                                                <img src="@/@core/assets/images/nurse/wong_baker/10.png" width="50px">
                                                <div class="fs-13 mt-10">Nyeri sangat hebat</div>
                                            </div>
                                        </div>
                                    </div>
                                </b-form-radio-group>
                            </div>
                            
                            <template v-if="live_sub.neonatus || live_sub.child || (live_sub.obgyn && getCalcAge() < 18)">
                                <div class="mt-16">
                                    <ModernLabel label="Skala Jatuh Humpty Dumpty untuk anak (0-17 tahun)" />
                                    <b-table-simple small bordered class="mt-10">
                                        <b-tr>
                                            <b-td class="col-lg-4 px-12">
                                                <label class="fs-13">Usia</label>
                                                <b-form-radio-group
                                                    disabled
                                                    @input="calculateHumpty" stacked size="sm" class="fs-13"
                                                    v-model="form_functional.age"
                                                    :options="OptionAge"
                                                ></b-form-radio-group>
                                            </b-td>
                                            <b-td class="col-lg-4 px-12">
                                                <label class="fs-13">Jenis kelamin</label>
                                                <b-form-radio-group
                                                    disabled
                                                    @input="calculateHumpty" stacked size="sm" class="fs-13"
                                                    v-model="form_functional.gender"
                                                    :options="OptionGender"
                                                ></b-form-radio-group>
                                            </b-td>
                                            <b-td class="col-lg-4 px-12">
                                                <label class="fs-13">Gangguan kognitif</label>
                                                <b-form-radio-group
                                                    @input="calculateHumpty" stacked size="sm" class="fs-13"
                                                    v-model="form_functional.cognitive_impairment"
                                                    :options="OptionCognitiveImpairment"
                                                ></b-form-radio-group>
                                            </b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="col-lg-4 px-12">
                                                <label class="fs-13">Diagnosis</label>
                                                <b-form-radio-group
                                                    @input="calculateHumpty" stacked size="sm" class="fs-13"
                                                    v-model="form_functional.diagnosis"
                                                    :options="OptionDiagnosis"
                                                ></b-form-radio-group>
                                            </b-td>
                                            <b-td class="col-lg-4 px-12">
                                                <label class="fs-13">Faktor lingkungan</label>
                                                <b-form-radio-group
                                                    @input="calculateHumpty" stacked size="sm" class="fs-13"
                                                    v-model="form_functional.environmental_factors"
                                                    :options="OptionEnvironmentalFactor"
                                                ></b-form-radio-group>
                                            </b-td>
                                            <b-td class="col-lg-4 px-12">
                                                <label class="fs-13">Pembedahan sedasi / anestesi</label>
                                                <b-form-radio-group
                                                    @input="calculateHumpty" stacked size="sm" class="fs-13"
                                                    v-model="form_functional.surgery_sedation_anesthesia"
                                                    :options="OptionSedationAnesthesia"
                                                ></b-form-radio-group>
                                            </b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td colspan="3" class="px-12">
                                                <label class="fs-13">Penggunaan medika mentosa</label>
                                                <b-form-radio-group
                                                    @input="calculateHumpty" stacked size="sm" class="fs-13"
                                                    v-model="form_functional.use_of_medication"
                                                    :options="OptionMedicationUse"
                                                ></b-form-radio-group>
                                            </b-td>
                                        </b-tr>
                                    </b-table-simple>
                                </div>
                                <div>skor:  {{ form_functional.total_score }} {{ getHumptyName() }}</div>
                            </template>

                            <template v-if="live_sub.adult || (live_sub.obgyn && getCalcAge() >= 18)">
                                <div class="mt-16">
                                    <ModernLabel label="Skala Jatuh Morse untuk pasien dewasa (> 17 tahun)" />
                                    <b-table-simple small bordered class="mt-10">
                                        <b-tr>
                                            <b-td class="col-lg-4 px-12">
                                                <label class="fs-13">Mempunyai riwayat jatuh, baru atau 3 bulan terakhir</label>
                                                <b-form-radio-group
                                                    @input="calculateMorse" stacked size="sm" class="fs-13"
                                                    v-model="form_functional.history_of_falls"
                                                    :options="OptionFallHistory"
                                                ></b-form-radio-group>
                                            </b-td>
                                            <b-td class="col-lg-4 px-12">
                                                <label class="fs-13">Diagnosis sekunder > 1</label>
                                                <b-form-radio-group
                                                    @input="calculateMorse" stacked size="sm" class="fs-13"
                                                    v-model="form_functional.secondary_diagnosis"
                                                    :options="OptionSecondaryDiagnosis"
                                                ></b-form-radio-group>
                                            </b-td>
                                            <b-td class="col-lg-4 px-12">
                                                <label class="fs-13">Ambulasi berjalan</label>
                                                <b-form-radio-group
                                                    @input="calculateMorse" stacked size="sm" class="fs-13"
                                                    v-model="form_functional.walking_ambulation"
                                                    :options="OptionWalkingAmbulation"
                                                ></b-form-radio-group>
                                            </b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="col-lg-4 px-12">
                                                <label class="fs-13">Terpasang IV line / pemberian anti koagulan (Heparin) / obat lain yang digunakan mempunya side effect jatuh</label>
                                                <b-form-radio-group
                                                    @input="calculateMorse" stacked size="sm" class="fs-13"
                                                    v-model="form_functional.side_effects_of_falling"
                                                    :options="OptionFallingSideEffects"
                                                ></b-form-radio-group>
                                            </b-td>
                                            <b-td class="col-lg-4 px-12">
                                                <label class="fs-13">Cara berjalan / berpindah</label>
                                                <b-form-radio-group
                                                    @input="calculateMorse" stacked size="sm" class="fs-13"
                                                    v-model="form_functional.way_of_walking"
                                                    :options="OptionWalkingWay"
                                                ></b-form-radio-group>
                                            </b-td>
                                            <b-td class="col-lg-4 px-12">
                                                <label class="fs-13">Status mental</label>
                                                <b-form-radio-group
                                                    @input="calculateMorse" stacked size="sm" class="fs-13"
                                                    v-model="form_functional.mental_status"
                                                    :options="OptionMentalStatus"
                                                ></b-form-radio-group>
                                            </b-td>
                                        </b-tr>
                                    </b-table-simple>
                                </div>
                                <div>skor:  {{ form_functional.total_score }} {{ getMorseName() }}</div>
                            </template>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <b-button-group size="sm">
                                <b-button @click="changeTab('VITAL')" :disabled="active.disabled.section" variant="outline-neutral-700">
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button @click="changeSection(active.section, 'EMERGENCY')" :disabled="active.disabled.section" variant="orange-500">
                                    Simpan & lanjutkan
                                    <feather-icon icon="ChevronRightIcon" />
                                </b-button>
                            </b-button-group>
                        </div>
                    </section>

                    <section v-if="active.section == 'EMERGENCY'" class="card">
                        <div class="card-header bg-primary text-white fs-14 font-weight-bold">IGD</div>
                        <div class="card-body px-20 py-8 position-sticky" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <section>
                                <ModernLabel label="Riwayat Kesehatan" />
                                <div class="row">
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Pernah Dirawat sebelumnya</label>
                                        <v-select v-model="form_emergency.been_hospitalized_type_id" :options="optHospitalized" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                        <template v-if="form_emergency.been_hospitalized_type_id == '1'">
                                            <b-form-input v-model="form_emergency.been_hospitalized_answer" size="sm" class="mt-8" />
                                        </template>
                                    </div>
                                    
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Operasi Yang Pernah Di Alami</label>
                                        <v-select v-model="form_emergency.any_surgery_type_id" :options="optSurgery" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                        <template v-if="form_emergency.any_surgery_type_id == '1'">
                                            <b-form-input type="date" v-model="form_emergency.any_surgery_date" size="sm" class="mt-8" />
                                        </template>
                                    </div>

                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Riwayat Alergi</label>                                        
                                        <v-select v-model="form_emergency.history_allergy_type_id" :options="optAllergy" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                        <template v-if="form_emergency.history_allergy_type_id == '1'">
                                            <b-form-input v-model="form_emergency.history_allergy" size="sm" class="mt-8" />
                                        </template>
                                    </div>                                            

                                    <div class="col-3 mt-8">
                                        <label class="fs-13 mb-0">Riwayat Penyakit Keluarga</label>
                                        <v-select v-model="form_emergency.any_history_family_disease_type_id" :options="optFamilyDisease" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                        <template v-if="form_emergency.any_history_family_disease_type_id == '1'">
                                            <b-form-checkbox-group v-model="form_emergency.any_history_family_answer_arry" size="sm" class="mt-8" >
                                                <b-form-checkbox value="a">DM</b-form-checkbox>
                                                <b-form-checkbox value="b">Hipertensi</b-form-checkbox>
                                                <b-form-checkbox value="c">Cancer</b-form-checkbox>
                                                <b-form-checkbox value="d">Jantung</b-form-checkbox>
                                                <b-form-checkbox value="e">TBC</b-form-checkbox>
                                                <b-form-checkbox value="f">Anemia</b-form-checkbox>
                                                <b-form-checkbox value="g">Lain-lain, sebutkan:</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </template>
                                        <template v-if="form_emergency.any_history_family_disease_type_id == '1' && form_emergency.any_history_family_answer_arry.indexOf('g') !== -1">
                                            <b-form-input v-model="form_emergency.any_history_family_answer" class="mt-8" size="sm"/>
                                        </template>
                                    </div>

                                </div>
                            </section>

                            <section class="mt-20">
                                <ModernLabel label="Riwayat Psikologis" />
                                <b-form-checkbox-group v-model="form_emergency.history_psychological_arry" size="sm" class="mt-8">
                                    <b-form-checkbox value="a" class="col-lg-3 mr-0">Tidak Semangat</b-form-checkbox>
                                    <b-form-checkbox value="b" class="col-lg-3 mr-0">Cepat Lelah</b-form-checkbox>
                                    <b-form-checkbox value="c" class="col-lg-3 mr-0">Menggunakan Obat Penenang</b-form-checkbox>
                                    <b-form-checkbox value="d" class="col-lg-3 mr-0">Rasa Tertekan</b-form-checkbox>
                                    <b-form-checkbox value="e" class="col-lg-3 mr-0">Sulit Berbicara</b-form-checkbox>
                                    <b-form-checkbox value="f" class="col-lg-3 mr-0">Merasa Bersalah</b-form-checkbox>
                                    <b-form-checkbox value="g" class="col-lg-3 mr-0">Depresi</b-form-checkbox>
                                    <b-form-checkbox value="h" class="col-lg-3 mr-0">Kurang Nafsu Makan</b-form-checkbox>
                                    <b-form-checkbox value="i" class="col-lg-3 mr-0">Tenang</b-form-checkbox>
                                    <b-form-checkbox value="j" class="col-lg-3 mr-0">Sulit Tidur</b-form-checkbox>
                                    <b-form-checkbox value="k" class="col-lg-3 mr-0">Sulit Konsentrasi</b-form-checkbox>
                                    <b-form-checkbox value="l" class="col-lg-3 mr-0">Tidak Ada Keluhan</b-form-checkbox>
                                </b-form-checkbox-group>
                            </section>

                            <section class="mt-20">
                                <ModernLabel label="Skrining Gizi" />
                                <div class="row align-items-lg-end">
                                    <div class="col-lg-4 mt-8">
                                        <label class="fs-13 mb-0">Indeks Masa Tubuh</label>
                                        <div class="text-muted font-italic fs-10">(IMT = Berat Badan/ Tinggi Badan dalam meter)</div>
                                        <b-form-select @change="handleNutritionScore" v-model="form_emergency.body_mass_index" :options="optBodyMass" size="sm" />                                     
                                        <!-- <b-form-select @change="handleNutritionScore" disabled v-model="form_emergency.body_mass_index" :options="optBodyMass" size="sm" />                                      -->
                                    </div>
                                    <div class="col-lg-4 mt-8">
                                        <label class="fs-13 mb-0">Asupan Makan Pasien</label>
                                        <div class="text-muted font-italic fs-10">Dalam 5 Hari Terakhir</div>
                                        <b-form-select @change="handleNutritionScore" v-model="form_emergency.patient_food_intake" :options="optFoodIntake" size="sm" />
                                    </div>
                                    <div class="col-lg-4 mt-8">
                                        <label class="fs-13 mb-0">Kondisi Penyakit Pasien</label>
                                        <div class="text-muted font-italic fs-10">Yang Mempunyai Risiko Nutrisi</div>
                                        <b-form-select @change="handleNutritionScore" v-model="form_emergency.patient_disease_condition" :options="optDiseaseCondition" size="sm" />
                                    </div>
                                    <div class="col-lg-4 mt-8">
                                        <label class="fs-13 mb-0">Pasien Sedang Mendapat Diet Makanan Tertentu</label>
                                        <b-form-select @change="handleNutritionScore" v-model="form_emergency.patient_certain_diets" :options="optCertainDiets" size="sm" />
                                    </div>
                                    <div class="col-lg-4 mt-8">
                                        <label class="fs-13 mb-0">Kehilangan Berat Badan</label>
                                        <div class="text-muted font-italic fs-10">Dalam Waktu 6 Bulan Terakhir</div>
                                        <b-form-select @change="handleNutritionScore" v-model="form_emergency.six_month_lose_weight" :options="optSixMonths" size="sm" />
                                    </div>
                                    <div v-if="form_emergency.six_month_lose_weight == '3'" class="col-lg-4 mt-8">
                                        <label class="fs-13 mb-0">Kehilangan Berat Badan</label>
                                        <b-form-select @change="handleNutritionScore" v-model="form_emergency.lose_weight" :options="optLoseWeight" size="sm" />
                                    </div>
                                </div>
                                <div class="row mt-20">
                                    <div class="col-lg-4">
                                        <b-input-group prepend="TOTAL SKOR" size="sm">
                                            <b-form-input v-model="form_emergency.nutrition_total_score" disabled />
                                        </b-input-group>
                                    </div>
                                </div>
                            </section>

                            <section class="mt-20">
                                <ModernLabel label="Pengkajian Risiko Jatuh" />
                                <b-table-simple small bordered responsive class="mt-8 text-center">
                                    <b-thead class="fs-13">
                                        <b-tr>
                                            <b-th style="width: 70px;">Tidak</b-th>
                                            <b-th style="width: 70px;">Ya</b-th>
                                            <b-th class="text-left">Komponen Penilaian</b-th>
                                        </b-tr>
                                    </b-thead>
                                    <b-tbody class="fs-12">
                                        <b-tr>
                                            <b-td>
                                                <b-form-radio value="0" v-model="form_emergency.fall_risk_component_a"/>
                                            </b-td>
                                            <b-td>
                                                <b-form-radio value="1" v-model="form_emergency.fall_risk_component_a"/>
                                            </b-td>
                                            <b-td class="text-left">a. Perhatikan cara jalan pasien saat akan duduk di kursi. Apakah pasien tampak seimbang (Sempoyong/ seimbang) ?</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>
                                                <b-form-radio value="0" v-model="form_emergency.fall_risk_component_b"/>
                                            </b-td>
                                            <b-td>
                                                <b-form-radio value="1" v-model="form_emergency.fall_risk_component_b"/>
                                            </b-td>
                                            <b-td class="text-left">b. Apakah pasien memegang pinggiran kursi atau benda lain sebagai penopang saat akan duduk ?</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td colspan="3" class="text-left px-16">
                                                <b-form-radio-group v-model="form_emergency.fall_risk_type_id" stacked>
                                                    <b-form-radio value="1">Tidak Beresiko (Tidak ditemukan a dan b)</b-form-radio>
                                                    <b-form-radio value="2">Beresiko rendah (Ditemukan  a atau b)</b-form-radio>
                                                    <b-form-radio value="3">Beresiko Tinggi (Ditemukan a dan b)</b-form-radio>
                                                </b-form-radio-group>
                                            </b-td>
                                        </b-tr>
                                    </b-tbody>
                                </b-table-simple>
                            </section>
                            <section class="mt-20">
                                <ModernLabel label="Daftar Masalah Keperawatan" />
                                <div class="mt-8">
                                    <div class="fs-14 font-weight-bold">DIAGNOSA KEPERAWATAN</div>
                                    <b-form-checkbox-group v-model="form_emergency.nursing_diagnoses_arry" size="sm" class="mt-1">
                                        <b-form-checkbox value="a" class="col-lg-4 mr-0">Bersihkan jalan nafas</b-form-checkbox>
                                        <b-form-checkbox value="b" class="col-lg-4 mr-0">Cemas/ Ansietas</b-form-checkbox>
                                        <b-form-checkbox value="c" class="col-lg-4 mr-0">Ketidakefektifan jalan nafas</b-form-checkbox>
                                        <b-form-checkbox value="d" class="col-lg-4 mr-0">Hipertemi</b-form-checkbox>
                                        <b-form-checkbox value="e" class="col-lg-4 mr-0">Gangguan mobilitas fisik</b-form-checkbox>
                                        <b-form-checkbox value="f" class="col-lg-4 mr-0">Keterlambatan pertumbuhan dan perkembangan</b-form-checkbox>
                                        <b-form-checkbox value="g" class="col-lg-4 mr-0">Intoleransi aktifitas</b-form-checkbox>
                                        <b-form-checkbox value="h" class="col-lg-4 mr-0">Risiko tinggi infeksi</b-form-checkbox>
                                        <b-form-checkbox value="i" class="col-lg-4 mr-0">Gangguan persepsi sensori pendengaran</b-form-checkbox>
                                        <b-form-checkbox value="j" class="col-lg-4 mr-0">Risiko tinggi cedera</b-form-checkbox>
                                        <b-form-checkbox value="k" class="col-lg-4 mr-0">Gangguan persepsi sensori penglihatan</b-form-checkbox>
                                        <b-form-checkbox value="l" class="col-lg-4 mr-0">Risiko kekurangan cairan</b-form-checkbox>
                                        <b-form-checkbox value="m" class="col-lg-4 mr-0">Gangguan integritas kulit</b-form-checkbox>
                                        <b-form-checkbox value="n" class="col-lg-4 mr-0">Hipertensi</b-form-checkbox>
                                        <b-form-checkbox value="o" class="col-lg-4 mr-0">Kurang pengetahuan</b-form-checkbox>
                                    </b-form-checkbox-group>
                                    <b-textarea v-model="form_emergency.nursing_diagnoses" size="sm" placeholder="Catatan diagnosa keperawatan lainnya" class="mt-8" />
                                </div>
                                <div class="mt-12">
                                    <div class="fs-14 font-weight-bold">IMPLEMENTASI</div>
                                    <b-form-checkbox-group v-model="form_emergency.implementation_arry" size="sm" class="mt-1">
                                        <b-form-checkbox value="a" class="col-lg-4 mr-0">Menjelaskan cara pencegahan dan perawatan kulit</b-form-checkbox>
                                        <b-form-checkbox value="b" class="col-lg-4 mr-0">Sehat</b-form-checkbox>
                                        <b-form-checkbox value="c" class="col-lg-4 mr-0">Mengajarkan teknik nafas dalam</b-form-checkbox>
                                        <b-form-checkbox value="d" class="col-lg-4 mr-0">MCU</b-form-checkbox>
                                        <b-form-checkbox value="e" class="col-lg-4 mr-0">Mengajarkan batuk efektif</b-form-checkbox>
                                        <b-form-checkbox value="f" class="col-lg-4 mr-0">Menganjurkan untuk banyak makan buah segar dan kacang-kacangan</b-form-checkbox>
                                        <b-form-checkbox value="g" class="col-lg-4 mr-0">Menginformasikan tidak merokok dalam ruangan</b-form-checkbox>
                                        <b-form-checkbox value="h" class="col-lg-4 mr-0">SKPN dan Rapid test</b-form-checkbox>
                                        <b-form-checkbox value="i" class="col-lg-4 mr-0">Memantau tanda-tanda sianosis</b-form-checkbox>
                                        <b-form-checkbox value="j" class="col-lg-4 mr-0">MCU calon karyawan</b-form-checkbox>
                                        <b-form-checkbox value="k" class="col-lg-4 mr-0">Mengukur warna dan suhu kulit</b-form-checkbox>
                                        <b-form-checkbox value="l" class="col-lg-4 mr-0">Tidak menahan BAK</b-form-checkbox>
                                        <b-form-checkbox value="m" class="col-lg-4 mr-0">Mengatur posisi nyaman</b-form-checkbox>
                                        <b-form-checkbox value="n" class="col-lg-4 mr-0">Memberikan obat tetes mata</b-form-checkbox>
                                        <b-form-checkbox value="o" class="col-lg-4 mr-0">Menganjurkan banyak minum</b-form-checkbox>
                                        <b-form-checkbox value="p" class="col-lg-4 mr-0">Manganjurkan pola hidup sehat</b-form-checkbox>
                                        <b-form-checkbox value="q" class="col-lg-4 mr-0">Kompres air hangat pada daerah yang gatal</b-form-checkbox>
                                        <b-form-checkbox value="r" class="col-lg-4 mr-0">Swab PCR some day</b-form-checkbox>
                                        <b-form-checkbox value="s" class="col-lg-4 mr-0">Jaga kebersihan</b-form-checkbox>
                                        <b-form-checkbox value="t" class="col-lg-4 mr-0">Injeksi Vit. C</b-form-checkbox>
                                        <b-form-checkbox value="u" class="col-lg-4 mr-0">SKPN</b-form-checkbox>
                                        <b-form-checkbox value="v" class="col-lg-4 mr-0">Menganjurkan banyak istirahat</b-form-checkbox>
                                        <b-form-checkbox value="w" class="col-lg-4 mr-0">Anjurkan banyak minum air putih</b-form-checkbox>
                                        <b-form-checkbox value="x" class="col-lg-4 mr-0">Infus Vit. C</b-form-checkbox>
                                        <b-form-checkbox value="y" class="col-lg-4 mr-0">Makan porsi kecil tapi sering</b-form-checkbox>
                                        <b-form-checkbox value="z" class="col-lg-4 mr-0">Istirahatkan mata</b-form-checkbox>
                                        <b-form-checkbox value="aa" class="col-lg-4 mr-0">Nyeri</b-form-checkbox>
                                        <b-form-checkbox value="ab" class="col-lg-4 mr-0">Menganjurkan banyak makan sayur</b-form-checkbox>
                                        <b-form-checkbox value="ac" class="col-lg-4 mr-0">Anti HIV rapid</b-form-checkbox>
                                    </b-form-checkbox-group>
                                    <b-textarea v-model="form_emergency.implementation" size="sm" placeholder="Catatan implementasi lainnya" class="mt-8" />
                                </div>
                            </section>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <b-button-group size="sm">
                                <b-button @click="changeTab('FUNCTIONAL')" :disabled="active.disabled.section" variant="outline-neutral-700">
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button @click="changeSection(active.section, 'EMERGENCY')" :disabled="active.disabled.section" variant="orange-500">
                                    <template>
                                        <feather-icon icon="SaveIcon" />
                                        Simpan
                                    </template>
                                </b-button>
                            </b-button-group>
                        </div>
                    </section>
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <div class="col-lg-2">
                        <b-button @click="closeMainModal" size="sm" variant="primary" class="btn-block">
                            <feather-icon icon="SaveIcon" class="mr-1" />
                            <span>Selesai Mengisi Asesmen</span>
                        </b-button>
                    </div>
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

        <LabResultModalView ref="labModalResult"/>
        <RadResultModalView ref="radModalResult"/>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import LabResultModalView from '../medical_support/LabResultModalView.vue';
import RadResultModalView from '../medical_support/RadResultModalView.vue';
import dateMixin from "@/mixins/dateMixin";
import optionMixin from '@/mixins/optionMixin';
import moment from "moment";

export default {
    components: {LabResultModalView, RadResultModalView},
    mixins: [dateMixin, optionMixin],
    data() {
        return {
            urlbe:process.env.VUE_APP_IMAGE,
            userData: JSON.parse(localStorage.getItem('userData')),
            dynamicHeight: "73vh",
            differenceHeight: "85px",
            regData: {},
            global_nurse_id: '',
            active: {
                modal: {
                    main: false,
                },
                disabled: {
                    section: false,
                },
                advance: {
                    antopometri: false,
                },
                section: 'ANAMNESA'
            },

            isPews: false,
            nextTab: null,
            original_anamnesa: {},
            original_vital: {},
            original_functional: {},
            original_emergency: {},

            form_anamnesa: {
                id: '',
                employee_nurse_id: '',
                registration_destination_id: '',
                type_nurse_id: '',
                anamesa_type_id: 1,
                family_relation_id: 0,
                family_name: '',
                main_complaint: '',
                history_disease_now: '',
                history_disease_past: '',
            },

            form_vital: {
                id: '',
                employee_nurse_id: '',
                registration_destination_id: '',
                type_nurse_id: '',
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
                skin_color: '',
                behavior: '',
                weight: '',
                height: '',
                imt: '',
                head_circumference: '',
                chest_circumference: '',
                arm_circumference: '',
                stomach_circumference: '',
                knee_circumference: ''
            },

            form_functional: {
                id: '',
                employee_nurse_id: '',
                registration_destination_id: '',
                type_nurse_id: '',
                aids: '',
                prothesa: '',
                disabled: '',
                adl_type_id: '',
                pain_score: '',
                history_of_falls: '',
                secondary_diagnosis: '',
                walking_ambulation: '',
                side_effects_of_falling: '',
                way_of_walking: '',
                mental_status: '',
                age: '',
                gender: '',
                diagnosis: '',
                cognitive_impairment: '',
                environmental_factors: '',
                surgery_sedation_anesthesia: '',
                use_of_medication: '',
                total_score: '',
            },

            form_emergency: {
                id: '',
                registration_destination_id: '',
                employee_nurse_id: '',
                type_nurse_id: 4,
                been_hospitalized_type_id: 0,
                been_hospitalized_answer: '',
                any_history_family_disease_type_id: 0,
                any_history_family_answer_arry: [],
                any_history_family_answer: '',
                any_surgery: '',
                any_surgery_date: '',
                complication: '',
                history_allergy_type_id: 0,
                history_allergy: '',
                history_psychological_arry: [],
                body_mass_index: '',
                patient_food_intake: '',
                patient_certain_diets: '',
                six_month_lose_weight: '',
                lose_weight: '',
                patient_disease_condition: '',
                nutrition_total_score: '0',
                fall_risk_type_id: '', 
                nursing_diagnoses_arry: [],
                nursing_diagnoses: '',
                implementation_arry: [],
                implementation: '',
                any_surgery_type_id: 0,
                fall_risk_component_a: 0,
                fall_risk_component_b: 0,
                // fall_risk_method_type_id: '',
            },

            live_sub: {
                neonatus: false,
                child: false,
                adult: false,
            },

            history: {
                cppt: [],
                nurse: [],
                soap: [],
                laboratory: [],
                radiology: [],
                physiotherapy: [],
                resume: [],
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
        }
    },

    watch: {
        "form_anamnesa.anamesa_type_id"(newValue) {
            if (newValue == "1") {
                this.form_anamnesa.family_relation_id = ''
                this.form_anamnesa.family_name = ''
            }
        },
        "form_emergency.body_mass_index": function (value){
            if(value) {
                this.handleNutritionScore()
            }
        },
        "form_emergency.been_hospitalized_type_id"(newValue) {
            if (newValue != "1") {
                this.form_emergency.been_hospitalized_answer = ""
            }
        },
        "form_emergency.any_history_family_disease_type_id"(newValue) {
            if (newValue != "1") {
                this.form_emergency.any_history_family_answer_arry = []
                this.form_emergency.any_history_family_answer = ""
            }
        },

        "form_emergency.any_history_family_answer_arry"(newValues) {
            if (newValues && !newValues.includes("g")) {
                this.form_emergency.any_history_family_answer = ""
            }
        },

        "form_emergency.history_allergy_type_id"(newValue) {
            if (newValue != "1") {
                this.form_emergency.history_allergy = ""
            }
        },

        "form_emergency.six_month_lose_weight"(newValue) {
            if (newValue != "3") {
                this.form_emergency.lose_weight = ""
            }
        },

        "form_emergency.any_surgery_type_id"(newValue) {
            if (newValue != "1") {
                this.form_emergency.any_surgery_date = ""
            }
        },
    },

    computed: {
        ...mapState({ 
            optAnamnesaType: state => state.frontoptions.nurse.assessment.anamnesa.anamnesa_type,
            optFamilyRelation: state => state.frontoptions.nurse.assessment.anamnesa.family_relation,

            OptionsOxygen: state => state.soap.vitals.OptionsOxygen,
            OptionsAware: state => state.soap.vitals.OptionsAware,
            OptionGcsEye: state => state.soap.vitals.OptionGcsEye,
            OptionGcsVerbal: state => state.soap.vitals.OptionGcsVerbal,
            OptionGcsMotoric: state => state.soap.vitals.OptionGcsMotoric,
            optSkinColor: state => state.frontoptions.general.vital_sign.skin_color,
            optBehavior: state => state.frontoptions.general.vital_sign.behavior,

            OptionAdl: state => state.frontoptions.nurse.assessment.functional.adl,
            OptionFallHistory: state => state.frontoptions.nurse.assessment.functional.fall_history,
            OptionSecondaryDiagnosis: state => state.frontoptions.nurse.assessment.functional.secondary_diagnosis,
            OptionWalkingAmbulation: state => state.frontoptions.nurse.assessment.functional.walking_ambulation,
            OptionFallingSideEffects: state => state.frontoptions.nurse.assessment.functional.falling_side_effects,
            OptionWalkingWay: state => state.frontoptions.nurse.assessment.functional.walking_way,
            OptionMentalStatus: state => state.frontoptions.nurse.assessment.functional.mental_status,

            OptionAge: state => state.frontoptions.nurse.assessment.functional.age,
            OptionGender: state => state.frontoptions.nurse.assessment.functional.gender,
            OptionDiagnosis: state => state.frontoptions.nurse.assessment.functional.diagnosis,
            OptionCognitiveImpairment: state => state.frontoptions.nurse.assessment.functional.cognitive_impairement,
            OptionEnvironmentalFactor: state => state.frontoptions.nurse.assessment.functional.environtmental_factor,
            OptionSedationAnesthesia: state => state.frontoptions.nurse.assessment.functional.sedation_anesthesia,
            OptionMedicationUse: state => state.frontoptions.nurse.assessment.functional.medication_use,

            optHospitalized: state => state.frontoptions.nurse.assessment.emergency.hospitalized,
            optSurgery: state => state.frontoptions.nurse.assessment.emergency.surgery,
            optAllergy: state => state.frontoptions.nurse.assessment.emergency.allergy,
            optFamilyDisease: state => state.frontoptions.nurse.assessment.emergency.family_disease,
            optBodyMass: state => state.frontoptions.nurse.assessment.emergency.body_mass,
            optSixMonths: state => state.frontoptions.nurse.assessment.emergency.six_months,
            optLoseWeight: state => state.frontoptions.nurse.assessment.emergency.lose_weight,
            optFoodIntake: state => state.frontoptions.nurse.assessment.emergency.food_intake,
            optDiseaseCondition: state => state.frontoptions.nurse.assessment.emergency.disease_condition,
            optCertainDiets: state => state.frontoptions.nurse.assessment.emergency.certain_diets,  
        }),
    },

    filters:{
        number(value) {
            return Number(value)
        },
    },

    methods: {
        calculateHumpty() {
            let age = this.form_functional.age || 0 
            let gen = this.form_functional.gender || 0 
            let dia = this.form_functional.diagnosis || 0 
            let cog = this.form_functional.cognitive_impairment || 0 
            let env = this.form_functional.environmental_factors || 0 
            let sur = this.form_functional.surgery_sedation_anesthesia || 0 
            let use = this.form_functional.use_of_medication || 0 
            let temp_score = 0

            temp_score = age + gen + dia + cog + env + sur + use 
            this.form_functional.total_score = temp_score
        },

        getHumptyName() {
            let score = Number(this.form_functional.total_score)
            if (score >= 7 && score <= 11) return '(Risiko Rendah)'
            if (score >= 12) return '(Risiko Tinggi)'
            return ''
        },

        calculateMorse() {
            let his = this.form_functional.history_of_falls || 0 
            let sec = this.form_functional.secondary_diagnosis || 0 
            let wal = this.form_functional.walking_ambulation || 0 
            let sid = this.form_functional.side_effects_of_falling || 0 
            let way = this.form_functional.way_of_walking || 0 
            let men = this.form_functional.mental_status || 0 
            let temp_score = 0

            temp_score = his + sec + wal + sid + way + men
            this.form_functional.total_score = temp_score
        },

        getMorseName() {
            let score = Number(this.form_functional.total_score)
            if (score >= 0 && score <= 24) return '(Tidak berisiko, perawatan yang baik)'
            if (score >= 25 && score <= 50) return '(Risiko rendah, lakukan intervensi jatuh standar)'
            if (score >= 51) return '(Risiko tinggi, lakukan intervensi jatuh tinggi)'
            return ''
        },

        async liveSubNurse(obj) {
            const birthDate = moment(obj.birth_date, 'YYYY-MM-DD');
            const now = moment();
            const years = now.diff(birthDate, 'years');
            const days = now.diff(birthDate, 'days');

            this.live_sub.neonatus = days <= 28
            this.live_sub.child = days > 28 && years <= 17
            this.live_sub.adult = years >= 18            
        },

        async parentTrigger(message) {
            this.regData = message.regData
            await this.liveSubNurse(message.regData)
            this.isPews = this.pewsCriteria(message.regData?.birth_date)

            this.openMainModal(message)

            // history
            this.history.cppt = []
            this.cppt_load.limit_display = 3
            this.getCpptHistory(this.regData.norm)

            this.history.nurse = []
            this.nurse_load.limit_display = 3
            this.getNurseHistory(this.regData.norm)

            this.history.soap = []
            this.soap_load.limit_display = 3
            this.getSoapHistory(this.regData.norm)

            this.history.laboratory = []
            this.lab_load.limit_display = 3
            this.getLaboratoryHistory(this.regData.norm)

            this.history.radiology = []
            this.rad_load.limit_display = 3
            this.getRadiologyHistory(this.regData.norm)

            this.history.physiotherapy = []
            this.phy_load.limit_display = 3
            this.getPhysiotherapyHistory(this.regData.norm)

            this.history.resume = []
            this.rsm_load.limit_display = 3
            this.getResumeList(this.regData.norm)
        },

        async openMainModal(obj) {
            this.active.modal.main = true
            if (obj.gate == 'edit_gate') {
                await this.fillNurseForm(obj.nurseData)
            } else {
                await this.resetNurseForm()
                await this.resetEmergency()
                
                const [v_age, v_gender] = await Promise.all([
                    this.getActualAge(),
                    this.getActualGender()
                ]);
                
                if (this.live_sub.neonatus || this.live_sub.child) {
                    this.$set(this.form_functional, 'age', v_age)
                    this.$set(this.form_functional, 'gender', v_gender)
                    this.calculateHumpty()
                }

                await this.cloneAllState()
            }
        },

        getMenusVariant(value) {
            if (value == this.active.section) {
                return 'frOrange'
            }
            return 'outline-frOrange'
        },

        getSectionName(sectionKey){
            if (sectionKey == 'ANAMNESA') return 'Anamnesa'
            if (sectionKey == 'VITAL') return 'Tanda Vital'
            if (sectionKey == 'FUNCTIONAL') return 'Fungsional'
            if (sectionKey == 'EMERGENCY') return 'IGD'
        },

        async cloneAllState() {    
            // console.log('clone dari emergency');
            this.original_anamnesa = JSON.parse(JSON.stringify(this.form_anamnesa))
            this.original_vital = JSON.parse(JSON.stringify(this.form_vital))
            this.original_functional = JSON.parse(JSON.stringify(this.form_functional))
            this.original_emergency = JSON.parse(JSON.stringify(this.form_emergency))
        },

        changeTab(destination) {
            const changes = this.hasChanges(this.active.section);

            if (changes) {
                this.nextTab = destination;
                this.$refs.confirmModal.show();
            } else {
                this.active.section = destination;
            }
        },

        hasChanges(exist) {
            const formMapping = {
                ANAMNESA: ["form_anamnesa", "original_anamnesa"],
                VITAL: ["form_vital", "original_vital"],
                FUNCTIONAL: ["form_functional", "original_functional"],
                EMERGENCY: ["form_emergency", "original_emergency"],
            };

            if (!formMapping[exist]) return false;

            const [form, original] = formMapping[exist];

            const formWithoutId = { ...this[form] };
            const originalWithoutId = { ...this[original] };
            delete formWithoutId.id;
            delete originalWithoutId.id;

            let changes = {};

            Object.keys(formWithoutId).forEach(key => {
                let before = originalWithoutId[key];
                let after = formWithoutId[key];

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
                FUNCTIONAL: ["form_functional", "original_functional"],
                EMERGENCY: ["form_emergency", "original_emergency"],
            };

            if (formMapping[this.active.section]) {
                const [form, original] = formMapping[this.active.section]

                const id = this[form].id
                this[form] = JSON.parse(JSON.stringify(this[original]))
                this[form].id = id
            }

            this.active.section = this.nextTab
            this.$refs.confirmModal.hide()
        },

        saveChanges() {
            this.active.disabled.section = true
            this.changeSection(this.active.section, this.nextTab)
            this.$refs.confirmModal.hide()

            // const formMapping = {
            //     ANAMNESA: ["form_anamnesa", "original_anamnesa"],
            //     VITAL: ["form_vital", "original_vital"],
            //     FUNCTIONAL: ["form_functional", "original_functional"],
            //     EMERGENCY: ["form_emergency", "original_emergency"],
            // };

            // if (formMapping[this.active.section]) {
            //     const [form, original] = formMapping[this.active.section]

            //     const id = this[form].id
            //     this[original] = JSON.parse(JSON.stringify(this[form]))
            //     this[original].id = id
            //     this.changeSection(this.active.section, this.nextTab)
            // }
            
            // this.$refs.confirmModal.hide();
        },

        closeMainModal() {
            //required field
            const adl = this.form_functional.adl_type_id 
            if (!adl) {
                this.notification('Failed','CloseIcon','danger','Dimohon untuk mengisi Activity Daily Living terlebih dahulu pada Section "FUNGSIONAL" ')
                return
            }

            // cek ada yang berubah atau ga di section yg lagi aktif, kl ada langsung disimpen
            if (this.hasChanges(this.active.section)) {
                this.changeSection(this.active.section, this.active.section)
            }
            this.active.modal.main = false
        },

        changeSection(exist, destination) {
            const saveFunctions = {
                ANAMNESA: this.saveAnamnesa,
                VITAL: this.saveVital,
                FUNCTIONAL: this.saveFunctional,
                EMERGENCY: this.saveEmergancy,
            };

            if (!saveFunctions[exist]) return;
            
            if (exist == 'VITAL') {
                saveFunctions[exist](exist, destination, 'main_section');
            } else {
                saveFunctions[exist](exist, destination);
            }
        },

        getMyNurse(nurse_id){
            try {
                this.getAssessment()
                this.$store.dispatch('nurse/getNurseById', nurse_id).then((response) => {
                    if (response?.length > 0) {
                        this.fillNurseForm(response[0])
                    }
                }, error => {
                    console.error('Error while GET nurse/getNurseById at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET nurse/getNurseById at: ', error);
            }
        },
        
        getAssessment() {
            this.$emit("refreshNurse");
        },

        async saveAnamnesa(exist, destination) {
            this.form_anamnesa.employee_nurse_id ||= this.userData.employee_id
            this.form_anamnesa.registration_destination_id ||= this.regData.registration_destination_id
            // this.form_anamnesa.id ||= `REG-${this.regData.registration_destination_id}`
            this.form_anamnesa.type_nurse_id ||= 4

            let fixed_data = {
                exist: exist,
                data: this.form_anamnesa   
            }

            try {
                this.active.disabled.section = true
                await this.$store.dispatch('nurse/putSections', fixed_data)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','Berhasil menyimpan anamnesa')
                        // this.resetNurseForm()
                        this.getMyNurse(res?.data?.id)
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
            this.form_vital.employee_nurse_id ||= this.userData.employee_id
            this.form_vital.registration_destination_id ||= this.regData.registration_destination_id
            // this.form_vital.id ||= `REG-${this.regData.registration_destination_id}`
            this.form_vital.type_nurse_id ||= 4

            let fixed_data = {
                exist: exist,
                data: this.form_vital   
            }

            try {
                this.active.disabled.section = true
                await this.$store.dispatch('nurse/putSections', fixed_data)
                .then(
                    res => {
                        if(save_from == 'main_section') {
                            this.notification('Success','CheckIcon','success','Berhasil menyimpan tanda vital')
                            // this.resetNurseForm()
                            this.getMyNurse(res?.data?.id)
                            this.active.section = destination
                        }
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

        async saveFunctional(exist, destination) {
            // penjagaan harap isi activity daily living
            if(!this.form_functional.adl_type_id) {
                this.notification('Failed','CloseIcon','danger','Dimohon untuk mengisi Activity Daily Living terlebih dahulu')
                return
            }

            this.form_functional.employee_nurse_id ||= this.userData.employee_id
            this.form_functional.registration_destination_id ||= this.regData.registration_destination_id
            // this.form_functional.id ||= `REG-${this.regData.registration_destination_id}`
            this.form_functional.type_nurse_id ||= 4

            let fixed_data = {
                exist: exist,
                data: this.form_functional   
            }

            try {
                this.active.disabled.section = true
                await this.$store.dispatch('nurse/putSections', fixed_data)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','Berhasil menyimpan fungsional')
                        // this.resetNurseForm()
                        this.getMyNurse(res?.data?.id)
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
        
        async saveEmergancy(exist, destination) {
            this.form_emergency.employee_nurse_id ||= this.userData.employee_id
            this.form_emergency.registration_destination_id ||= this.regData.registration_destination_id
            this.form_emergency.type_nurse_id ||= 4

            let fixed_data = {
                exist: exist,
                data: this.form_emergency   
            }

            try {
                this.active.disabled.section = true
                await this.$store.dispatch('nurse/putSections', fixed_data)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','Berhasil menyimpan IGD')
                        // this.resetNurseForm()
                        this.getMyNurse(res?.data?.id)
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

        async fillNurseForm(obj) {
            await this.resetNurseForm()
            await this.resetEmergency()

            const [v_age, v_gender] = await Promise.all([
                this.getActualAge(),
                this.getActualGender()
            ]);

            if (obj) {
                this.$nextTick(() => {
                    this.global_nurse_id = obj?.id || ''
                    // =============================================================================================
                    this.form_anamnesa.id = obj?.id || ''
                    this.form_anamnesa.employee_nurse_id = obj?.employee_nurse_id || this.userData.employee_id
                    this.form_anamnesa.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_anamnesa.type_nurse_id ||= 4
                    this.form_anamnesa.anamesa_type_id = obj?.anamesa_type_id || 1 
                    this.form_anamnesa.family_relation_id = obj?.family_relation_id ||  0
                    this.form_anamnesa.family_name = obj?.family_name || ''
                    this.form_anamnesa.main_complaint = obj?.main_complaint || ''
                    this.form_anamnesa.history_disease_now = obj?.history_disease_now || ''
                    this.form_anamnesa.history_disease_past = obj?.history_disease_past || ''

                    // =============================================================================================
                    this.form_vital.id = obj?.id || ''
                    this.form_vital.employee_nurse_id = obj?.employee_nurse_id || this.userData.employee_id
                    this.form_vital.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_vital.type_nurse_id ||= 4
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
                    if (this.isPews) {
                        this.form_vital.skin_color = obj?.skin_color || ''
                        this.form_vital.behavior = obj?.behavior || ''
                    }
                    this.form_vital.weight = obj?.weight
                    this.form_vital.height = obj?.height
                    this.form_vital.imt = obj?.imt
                    this.form_vital.head_circumference = obj?.head_circumference
                    this.form_vital.chest_circumference = obj?.chest_circumference
                    this.form_vital.arm_circumference = obj?.arm_circumference
                    this.form_vital.stomach_circumference = obj?.stomach_circumference
                    this.form_vital.knee_circumference = obj?.knee_circumference

                    this.form_functional.id = obj?.id || ''
                    this.form_functional.employee_nurse_id = obj?.employee_nurse_id || this.userData.employee_id
                    this.form_functional.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_functional.type_nurse_id = obj?.type_nurse_id || 4
                    this.form_functional.aids = obj?.aids || ''
                    this.form_functional.prothesa = obj?.prothesa || ''
                    this.form_functional.disabled = obj?.disabled || ''
                    this.form_functional.adl_type_id = obj?.adl_type_id || ''
                    this.form_functional.pain_score = obj?.pain_score

                    if (this.live_sub.neonatus || this.live_sub.child) {
                        const humpty = obj?.humpty_dumpty
                        this.$set(this.form_functional, 'age', humpty?.age || v_age)
                        this.$set(this.form_functional, 'gender', humpty?.gender || v_gender)   
                        this.form_functional.diagnosis = humpty?.diagnosis
                        this.form_functional.cognitive_impairment = humpty?.cognitive_impairment
                        this.form_functional.environmental_factors = humpty?.environmental_factors
                        this.form_functional.surgery_sedation_anesthesia = humpty?.surgery_sedation_anesthesia
                        this.form_functional.use_of_medication = humpty?.use_of_medication
                        this.form_functional.total_score = humpty?.total_score
                        
                    } else if (this.live_sub.adult) {
                        const morse = obj?.morse
                        this.form_functional.history_of_falls = morse?.history_of_falls
                        this.form_functional.secondary_diagnosis = morse?.secondary_diagnosis
                        this.form_functional.walking_ambulation = morse?.walking_ambulation
                        this.form_functional.side_effects_of_falling = morse?.side_effects_of_falling
                        this.form_functional.way_of_walking = morse?.way_of_walking
                        this.form_functional.mental_status = morse?.mental_status
                        this.form_functional.total_score = morse?.total_score
                    }

                    if (obj.type_nurse_id == 4) {
                        this.fillEmergencyForm(obj.id)
                    } else {
                        this.cloneAllState()
                    }
                })
            }
        },

        async getActualAge() {
            const birthDate = moment(this.regData.birth_date, 'YYYY-MM-DD');
            const now = moment();
            const age = now.diff(birthDate, 'years')

            if (age < 3) return 4
            if (age >= 3 && age <= 6) return 3
            if (age >= 7 && age <= 12) return 2
            if (age > 12) return 1
        },

        async getActualGender() {
            const sex = this.regData.sex
            if (sex == 1) return 2 
            if (sex == 0) return 1 
        },

        async resetNurseForm() {
            this.form_anamnesa.id = ''
            this.form_anamnesa.employee_nurse_id = ''
            this.form_anamnesa.registration_destination_id = ''
            this.form_anamnesa.type_nurse_id = '' 
            this.form_anamnesa.anamesa_type_id = 1
            this.form_anamnesa.family_relation_id = 0
            this.form_anamnesa.family_name = ''
            this.form_anamnesa.main_complaint = ''
            this.form_anamnesa.history_disease_now = ''
            this.form_anamnesa.history_disease_past = ''

            this.form_vital.id = ''
            this.form_vital.employee_nurse_id = ''
            this.form_vital.registration_destination_id = ''
            this.form_vital.type_nurse_id = ''
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
            this.form_vital.skin_color = ''
            this.form_vital.behavior = ''
            this.form_vital.weight = ''
            this.form_vital.height = ''
            this.form_vital.imt = ''
            this.form_vital.head_circumference = ''
            this.form_vital.chest_circumference = ''
            this.form_vital.arm_circumference = ''
            this.form_vital.stomach_circumference = ''
            this.form_vital.knee_circumference = ''

            this.form_functional.id = ''
            this.form_functional.employee_nurse_id = ''
            this.form_functional.registration_destination_id = ''
            this.form_functional.type_nurse_id = ''
            this.form_functional.aids = ''
            this.form_functional.prothesa = ''
            this.form_functional.disabled = ''
            this.form_functional.adl_type_id = ''
            this.form_functional.pain_score = ''
            this.form_functional.history_of_falls = ''
            this.form_functional.secondary_diagnosis = ''
            this.form_functional.walking_ambulation = ''
            this.form_functional.side_effects_of_falling = ''
            this.form_functional.way_of_walking = ''
            this.form_functional.mental_status = ''
            this.form_functional.age = ''
            this.form_functional.gender = ''
            this.form_functional.diagnosis = ''
            this.form_functional.cognitive_impairment = ''
            this.form_functional.environmental_factors = ''
            this.form_functional.surgery_sedation_anesthesia = ''
            this.form_functional.use_of_medication = ''
            this.form_functional.total_score = ''
        },

        fillEmergencyForm(nurse_id) {
            if (nurse_id) {
                this.resetEmergency()
                this.$store.dispatch('nurse/getEmergencyDetail', { nurse_id: nurse_id }).then((response) => {
                    const res = response[0]

                    this.form_emergency.id = res?.nurse_id || nurse_id
                    this.form_emergency.registration_destination_id = res?.registration_destination_id || this.regData.registration_destination_id
                    this.form_emergency.employee_nurse_id = res?.employee_nurse_id || this.userData.employee_id
                    this.form_emergency.type_nurse_id = res?.type_nurse_id || 4
                    this.form_emergency.been_hospitalized_type_id = res?.been_hospitalized_type_id || 0
                    this.form_emergency.been_hospitalized_answer = res?.been_hospitalized_answer || ''
                    this.form_emergency.any_history_family_disease_type_id = res?.any_history_family_disease_type_id || 0
                    this.form_emergency.any_history_family_answer_arry = res?.any_history_family_answer_arry || []
                    this.form_emergency.any_history_family_answer = res?.any_history_family_answer || ''
                    this.form_emergency.any_surgery = res?.any_surgery || ''
                    this.form_emergency.any_surgery_date = res?.any_surgery_date || ''
                    this.form_emergency.complication = res?.complication || ''
                    this.form_emergency.history_allergy_type_id = res?.history_allergy_type_id || 0
                    this.form_emergency.history_allergy = res?.history_allergy || ''
                    this.form_emergency.history_psychological_arry = res?.history_psychological_arry || []
                    this.form_emergency.body_mass_index = res?.body_mass_index || ''
                    this.form_emergency.patient_food_intake = res?.patient_food_intake || ''
                    this.form_emergency.patient_certain_diets = res?.patient_certain_diets || ''
                    this.form_emergency.six_month_lose_weight = res?.six_month_lose_weight || ''
                    this.form_emergency.lose_weight = res?.lose_weight || ''
                    this.form_emergency.patient_disease_condition = res?.patient_disease_condition || ''
                    this.form_emergency.nutrition_total_score = res?.nutrition_total_score || 0
                    this.form_emergency.fall_risk_type_id = res?.fall_risk_type_id || ''
                    this.form_emergency.nursing_diagnoses_arry = res?.nursing_diagnoses_arry || []
                    this.form_emergency.nursing_diagnoses = res?.nursing_diagnoses || ''
                    this.form_emergency.implementation_arry = res?.implementation_arry || []
                    this.form_emergency.implementation = res?.implementation || ''
                    this.form_emergency.any_surgery_type_id = res?.any_surgery_type_id || 0
                    this.form_emergency.fall_risk_component_a = res?.fall_risk_component_a || 0
                    this.form_emergency.fall_risk_component_b = res?.fall_risk_component_b || 0
                    // this.form_emergency.fall_risk_method_type_id = res.fall_risk_method_type_id || ''
                
                    this.cloneAllState()
                })
            }
        },

        async resetEmergency() {
            this.form_emergency.id = ''
            this.form_emergency.registration_destination_id = ''
            this.form_emergency.employee_nurse_id = ''
            this.form_emergency.type_nurse_id = 4
            this.form_emergency.been_hospitalized_type_id = 0
            this.form_emergency.been_hospitalized_answer = ''
            this.form_emergency.any_history_family_disease_type_id = 0
            this.form_emergency.any_history_family_answer_arry = []
            this.form_emergency.any_history_family_answer = ''
            this.form_emergency.any_surgery = ''
            this.form_emergency.any_surgery_date = ''
            this.form_emergency.complication = ''
            this.form_emergency.history_allergy_type_id = 0
            this.form_emergency.history_allergy = ''
            this.form_emergency.history_psychological_arry = []
            this.form_emergency.body_mass_index = ''
            this.form_emergency.patient_food_intake = ''
            this.form_emergency.patient_certain_diets = ''
            this.form_emergency.six_month_lose_weight = ''
            this.form_emergency.lose_weight = ''
            this.form_emergency.patient_disease_condition = ''
            this.form_emergency.nutrition_total_score = 0
            this.form_emergency.fall_risk_type_id = ''
            this.form_emergency.nursing_diagnoses_arry = []
            this.form_emergency.nursing_diagnoses = ''
            this.form_emergency.implementation_arry = []
            this.form_emergency.implementation = ''
            this.form_emergency.any_surgery_type_id = 0
            this.form_emergency.fall_risk_component_a = 0
            this.form_emergency.fall_risk_component_b = 0
            // this.form_emergency.fall_risk_method_type_id = ''
        },

        handleNutritionScore() {
            const score_body_mass_index = this.getBodyMassIndexScore(this.form_emergency.body_mass_index)
            const score_six_month_lose_weight = this.getSixMonthLoseWeightScore(this.form_emergency.six_month_lose_weight)
            const score_lose_weight = this.getLoseWeightScore(this.form_emergency.lose_weight)
            const score_patient_food_intake = this.getPatientFoodIntakeScore(this.form_emergency.patient_food_intake)
            const score_patient_disease_condition = this.getPatientDiseaseCondition(this.form_emergency.patient_disease_condition)
            const score_patient_certain_diets = this.getPatientCertainDiets(this.form_emergency.patient_certain_diets)
            
            let result = 0
            result = score_body_mass_index + score_six_month_lose_weight + score_lose_weight + score_patient_food_intake + score_patient_disease_condition + score_patient_certain_diets
            this.form_emergency.nutrition_total_score = result.toString()
        },
        getBodyMassIndexScore(value){
            let score = 0
            if (value !== '') {
                if (value == '1') {
                    return score = 0
                } else if (value == '2') {
                    return score = 1
                } else if (value == '3') {
                    return score = 2
                }
            }
            return score = 0
        },
        getSixMonthLoseWeightScore(value){
            let score = 0
            if (value !== '') {
                if (value == '1') {
                    return score = 0
                } else if (value == '2') {
                    return score = 2
                } else if (value == '3') {
                    return score = 0
                }
            }
            return score = 0
        },
        getLoseWeightScore(value){
            let score = 0
            if (value !== '') {
                if (value == '1') {
                    return score = 1
                } else if (value == '2') {
                    return score = 2
                } else if (value == '3') {
                    return score = 3
                } else if (value == '4') {
                    return score = 4
                } else if (value == '5') {
                    return score = 2
                }
            }
            return score = 0
        },
        getPatientFoodIntakeScore(value){
            let score = 0
            if (value !== '') {
                if (value == '1') {
                    return score = 0
                } else if (value == '2') {
                    return score = 1
                } else if (value == '3') {
                    return score = 2
                }
            }
            return score = 0
        },
        getPatientDiseaseCondition(value){
            let score = 0
            if (value !== '') {
                if (value == '1') {
                    return score = 0
                } else if (value == '2') {
                    return score = 2
                }
            }
            return score = 0
        },
        getPatientCertainDiets(value){
            let score = 0
            if (value !== '') {
                if (value == '1') {
                    return score = 0
                } else if (value == '2') {
                    return score = 2
                }
            }
            return score = 0
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

        soapAnamnesa(obj) {            
            this.form_anamnesa.anamesa_type_id = obj.anamesa_type_id
            this.form_anamnesa.family_name = obj.family_name
            this.form_anamnesa.family_relation_id = obj.family_relation_id
            this.form_anamnesa.main_complaint = obj.main_complaint
            this.form_anamnesa.history_disease_now = obj.history_disease_now
            this.form_anamnesa.history_disease_past = obj.history_disease_past
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
            if (this.isPews) {
                this.form_vital.skin_color = obj?.skin_color 
                this.form_vital.behavior = obj?.behavior 
            }
            this.form_vital.weight = obj?.weight
            this.form_vital.height = obj?.height
            this.form_vital.imt = obj?.imt
            this.form_vital.head_circumference = obj?.head_circumference
            this.form_vital.chest_circumference = obj?.chest_circumference
            this.form_vital.arm_circumference = obj?.arm_circumference
            this.form_vital.stomach_circumference = obj?.stomach_circumference
            this.form_vital.knee_circumference = obj?.knee_circumference
        },

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
            if (this.isPews) {
                this.form_vital.skin_color = obj?.skin_color 
                this.form_vital.behavior = obj?.behavior 
            }
            this.form_vital.weight = obj?.weight
            this.form_vital.height = obj?.height
            this.form_vital.imt = obj?.imt
            this.form_vital.head_circumference = obj?.head_circumference
            this.form_vital.chest_circumference = obj?.chest_circumference
            this.form_vital.arm_circumference = obj?.arm_circumference
            this.form_vital.stomach_circumference = obj?.stomach_circumference
            this.form_vital.knee_circumference = obj?.knee_circumference
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
        },
        
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        triggerRadResult(message) {
            this.$refs.radModalResult.seeRadiologyResult(message);
        },

        triggerLabResult(message) {
            this.$refs.labModalResult.seeLaboratoryResult(message);
        },

        async getCpptHistory(norm) {
            const param = { 
                norm: norm,
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
    },
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
</style>