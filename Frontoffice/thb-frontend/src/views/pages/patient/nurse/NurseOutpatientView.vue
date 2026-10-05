<template>
    <div>
        <b-modal v-model="active.modal.main" scrollable size="xl" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ASESMEN PERAWAT POLIKLINIK
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
                                            <b-collapse :id="`accordion-`+index" accordion="my-accordion" role="tabpanel">
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
                            </b-tabs>
                        </section>
                        <div class="col-4 bg-primary rounded py-16 px-2 position-sticky" :style="{top: '0', height: `${dynamicHeight}`, overflowY: 'auto'}">
                            <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-0" @click="changeTab('ANAMNESA')" :variant="getMenusVariant('ANAMNESA')">ANAMNESA</b-button>
                            <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('VITAL')" :variant="getMenusVariant('VITAL')">TANDA VITAL</b-button>
                            <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-1" @click="changeTab('FUNCTIONAL')" :variant="getMenusVariant('FUNCTIONAL')">FUNGSIONAL</b-button>
                            <template v-if="regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e'">
                                <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-2" @click="changeTab('HEMODIALYSIS')" :variant="getMenusVariant('HEMODIALYSIS')">HEMODIALISA</b-button>
                            </template>
                            <template v-if="regData.health_facility_item_id == hfii_physiotherapy">
                                <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-2" @click="changeTab('PHYSIOTHERAPY')" :variant="getMenusVariant('PHYSIOTHERAPY')">FISIOTERAPI</b-button>
                            </template>
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
                                <template v-if="regData.health_facility_item_id == hfii_physiotherapy">
                                    <div class="col-12 mt-8">
                                        <label class="fs-13 mb-0">Objective</label>
                                        <b-form-textarea rows="3" size="sm" v-model="form_anamnesa.objective" autocomplete="off"></b-form-textarea>
                                    </div>
                                </template>
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
                                    <label class="fs-13 mb-0">Activity Daily Living <span class="text-danger">*</span></label>
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
                                <b-button @click="changeSection(active.section, getNextSection())" :disabled="active.disabled.section" variant="orange-500">
                                    <template v-if="regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e'">
                                        Simpan & lanjutkan
                                        <feather-icon icon="ChevronRightIcon" />
                                    </template>
                                    <template v-else>
                                        <feather-icon icon="SaveIcon" />
                                        Simpan
                                    </template>
                                </b-button>
                            </b-button-group>
                        </div>
                    </section>

                    <section v-if="active.section == 'HEMODIALYSIS'" class="card">
                        <div class="card-header bg-primary text-white fs-14 font-weight-bold">HEMODIALISA</div>
                        <div class="card-body px-20 py-8 position-sticky" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">

                            <b-tabs v-model="activeHdTab" class="mt-3 tab-title">
                                <b-tab title="Data Hemodialisa" />
                                <b-tab title="Observasi Intradialitik" />
                                <b-tab title="Perencanaan Pulang" />
                                <b-tab title="Formulir dan Surat Keterangan Hemodialisis" />
                                <b-tab title="Pemantauan Harian" />
                                <b-tab title="Resep HD" />
                            </b-tabs>

                            <template v-if="activeHdTab == 0">
                                <div class="fs-13 font-weight-bold mb-8 mt-12">Instruksi Program</div>
                                <section class="row">
                                    <div class="col-lg-4">
                                        <label class="fs-13 mb-0">Lama</label>
                                        <b-input-group append="Jam" size="sm">
                                            <b-form-input v-model="form_hemodialysis.duration" type="number" autocomplete="off" />
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-4 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Dialist</label>
                                        <b-form-input v-model="form_hemodialysis.dialist" size="sm" autocomplete="off" />
                                    </div>
                                    <div class="col-lg-4 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Penarikan Cairan</label>
                                        <b-input-group append="ml" size="sm">
                                            <b-form-input v-model="form_hemodialysis.liquid_withdraw" type="number" autocomplete="off" />
                                        </b-input-group>
                                    </div>
                                </section>
                                <section class="row mt-8">
                                    <div class="col-lg-3">
                                        <label class="fs-13 mb-0">Akses</label>
                                        <v-select
                                            v-model="form_hemodialysis.access"
                                            :options="optAccess"
                                            :reduce="item => item.value"
                                            label="title"
                                            placeholder="-- Pilih --"
                                            class="select-size-sm"
                                        />
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Transfusi</label>
                                        <b-input-group append="Kali/Durante HD" size="sm">
                                            <b-form-input v-model="form_hemodialysis.transfusion" type="number" autocomplete="off" />
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">QB</label>
                                        <b-form-input v-model="form_hemodialysis.qb" size="sm" type="number" autocomplete="off" />
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">QD</label>
                                        <b-form-input v-model="form_hemodialysis.qd" size="sm" type="number" autocomplete="off" />
                                    </div>
                                </section>

                                <hr>

                                <div class="fs-13 font-weight-bold mb-8">Hasil Laboratorium</div>
                                <section class="row">
                                    <div class="col-lg-4">
                                        <label class="fs-13 mb-0">Ureum</label>
                                        <b-form-input v-model="form_hemodialysis.ureum" size="sm" autocomplete="off" />
                                    </div>
                                    <div class="col-lg-4 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">HbsAg</label>
                                        <b-form-input v-model="form_hemodialysis.hbsag" size="sm" autocomplete="off" />
                                    </div>
                                    <div class="col-lg-4 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">HIV</label>
                                        <b-form-input v-model="form_hemodialysis.hiv" size="sm" autocomplete="off" />
                                    </div>
                                </section>
                                <section class="row mt-8">
                                    <div class="col-lg-4">
                                        <label class="fs-13 mb-0">Hb</label>
                                        <b-form-input v-model="form_hemodialysis.hb" size="sm" autocomplete="off" />
                                    </div>
                                    <div class="col-lg-4 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Creatinin</label>
                                        <b-form-input v-model="form_hemodialysis.creatinin" size="sm" autocomplete="off" />
                                    </div>
                                    <div class="col-lg-4 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">HCV</label>
                                        <b-form-input v-model="form_hemodialysis.hcv" size="sm" autocomplete="off" />
                                    </div>
                                </section>

                                <hr>

                                <section class="row mb-2">
                                    <div class="col-lg-4">
                                        <label class="fs-13 mb-0">Lain Lain</label>
                                        <b-form-input v-model="form_hemodialysis.other" size="sm" autocomplete="off" />
                                    </div>
                                </section>
                                <section class="row mb-2">
                                    <div class="col-lg-12 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0"> Nama Diagnosa </label>
                                        <v-select v-model="icd10_id_hd" @input="onAdd10DataHd($event)" aria-readonly="true" @search="onSearchIcd10DataHd" :options="OptionsIcd10DataHd" :reduce="item => item" label="title" placeholder="-- Diagnosa --" class="select-size-sm mt-12" />

                                        <template v-if="form_hemodialysis.icd10.length > 0">
                                            <b-table responsive bordered small :fields="icd10FieldsDataHd" :items="form_hemodialysis.icd10" class="mt-12 text-center" style="font-size: 14px;" >
                                                <template #cell(no)="row">{{ row.index+1 }}</template>
                                                <template #cell(title)="row">
                                                    <div class="text-left">{{ row.value }}</div>
                                                </template>
                                                <template #cell(action)="row">
                                                    <b-button variant="danger-600" size="sm" @click="delete10DataHd(row.index)" class="mr-2">
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </template>
                                            </b-table>
                                        </template>
                                    </div>
                                </section>

                            </template>

                            <template v-if="activeHdTab == 1">
                                <section class="row mt-2">
                                    <div class="col-lg-4">
                                        <label class="fs-13 mb-0">Pemantauan Jam Ke</label>
                                        <v-select
                                            v-model="form_observasi.hour_observation"
                                            :options="optMonitoringSession"
                                            :reduce="item => item.value"
                                            label="title"
                                            placeholder="-- Pilih --"
                                            class="select-size-sm"
                                        />
                                    </div>
                                </section>
                                <hr>
                                <section class="row">
                                    <div class="col-lg-4">
                                        <label class="fs-13 mb-0">Keluhan</label>
                                        <b-form-input v-model="form_observasi.complaint" size="sm" autocomplete="off" />
                                    </div>
                                    <div class="col-lg-4 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Kesadaran</label>
                                        <v-select
                                        v-model="form_observasi.awareness"
                                        :options="optConsciousness"
                                            :reduce="item => item.value"
                                            label="title"
                                            placeholder="-- Pilih --"
                                            class="select-size-sm"
                                            />
                                    </div>
                                    <div class="col-lg-2 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">BB</label>
                                            <b-input-group append="Kg" size="sm">
                                                <b-form-input v-model="form_observasi.bb" type="number" autocomplete="off" />
                                            </b-input-group>
                                        </div>
                                    <div class="col-lg-2 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">TD</label>
                                        <b-input-group append="mmHg" size="sm">
                                            <b-form-input v-model="form_observasi.td" size="sm" autocomplete="off" placeholder="120/80" />
                                        </b-input-group>
                                    </div>
                                </section>

                                <section class="row mt-8">
                                    <div class="col-lg-3">
                                        <label class="fs-13 mb-0">Nadi</label>
                                        <b-input-group append="x/menit" size="sm">
                                            <b-form-input v-model="form_observasi.pulse" type="number" autocomplete="off" />
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">RR</label>
                                        <b-input-group append="x/menit" size="sm">
                                            <b-form-input v-model="form_observasi.rr" type="number" autocomplete="off" />
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Suhu</label>
                                        <b-input-group append="°C" size="sm">
                                            <b-form-input v-model="form_observasi.temp" type="number" autocomplete="off" />
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Qb</label>
                                        <b-input-group append="ml/menit" size="sm">
                                            <b-form-input v-model="form_observasi.qb" type="number" autocomplete="off" />
                                        </b-input-group>
                                    </div>

                                </section>

                                <section class="row mt-8">
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Qd</label>
                                        <b-input-group append="ml/menit" size="sm">
                                            <b-form-input v-model="form_observasi.qd" type="number" autocomplete="off" />
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Tekanan Vena</label>
                                        <b-input-group append="mmHg" size="sm">
                                            <b-form-input v-model="form_observasi.venous_pressure" type="number" autocomplete="off" />
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="fs-13 mb-0">TMP</label>
                                        <b-input-group append="mmHg" size="sm">
                                            <b-form-input v-model="form_observasi.tmp" type="number" autocomplete="off" />
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Volume yang ditarik</label>
                                        <b-input-group append="mmHg" size="sm">
                                            <b-form-input v-model="form_observasi.drawn_volume" type="number" autocomplete="off" />
                                        </b-input-group>
                                    </div>

                                </section>
                                <section class="row mt-8">
                                    <div class="col-lg-8 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Asesmen/ Intervensi/ Keterangan</label>
                                        <b-form-textarea rows="3" size="sm" v-model="form_observasi.assessment" autocomplete="off"></b-form-textarea>
                                    </div>
                                </section>

                                <section class="row mt-8 mb-2">
                                    <div class="col-lg-6">
                                        <label class="fs-13 mb-0">Petugas</label>
                                        <v-select v-model="form_observasi.nurse_id" :options="nurseList" :reduce="item => item.value" label="title" class="select-size-sm" placeholder="pilih perawat"></v-select>
                                    </div>
                                </section>
                                <hr>
                                <div class="fs-13 font-weight-bold mb-8">Riwayat Observasi Intradialitik</div>
                                <b-table
                                    bordered
                                    responsive
                                    hover
                                    small
                                    show-empty
                                    head-variant="light"
                                    :fields="list_intra_fields"
                                    :items="list_intra"
                                    thead-class="text-bold-14"
                                    tbody-tr-class="text-regular-12"
                                    class="mt-8 text-center"
                                    empty-text="Belum ada data observasi"
                                >
                                    <template #cell(no)="row">
                                    {{ row.index + 1 }}
                                    </template>
                                    <template #cell(updated_at)="row">
                                    {{ localDate(row.item.updated_at) }}
                                    </template>
                                </b-table>
                            </template>

                            <template v-if="activeHdTab == 2">
                                    <section class="row mt-2">
                                        <div class="col-lg-4">
                                            <label class="fs-13 mb-0">Tanggal Tindakan HD Berikutnya</label>
                                            <b-form-input v-model="form_discharge.next_hd_date" size="sm" type="date" />
                                        </div>
                                    </section>

                                    <section class="row mt-8">
                                        <div class="col-lg-6">
                                            <label class="fs-13 mb-0">Edukasi</label>
                                            <b-form-textarea rows="3" size="sm" v-model="form_discharge.education" autocomplete="off"></b-form-textarea>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="fs-13 mb-0">Rencana Konsultasi</label>
                                            <b-form-textarea rows="3" size="sm" v-model="form_discharge.consult_planning" autocomplete="off"></b-form-textarea>
                                        </div>
                                    </section>

                                    <section class="row mt-8">
                                        <div class="col-lg-6">
                                            <label class="fs-13 mb-0">Lain-lain</label>
                                            <b-form-textarea rows="3" size="sm" v-model="form_discharge.other_2" autocomplete="off"></b-form-textarea>
                                        </div>
                                    </section>
                                    <section class="row mt-8">
                                        <div class="col-lg-12">
                                            <label class="fs-13 mb-0 font-weight-bold">Rencana Pemeriksaan Penunjang</label>

                                            <div v-if="form_discharge.lab_request.length || form_discharge.rad_request.length" class="mb-8 mt-1">
                                            <small class="text-muted">Terpilih:</small>
                                            <div class="d-flex flex-wrap mt-1" style="gap: 4px;">
                                                <b-badge
                                                    v-for="item in form_discharge.lab_request"
                                                    :key="'lab-' + item.laboratory_package_id"
                                                    variant="primary"
                                                    class="px-3 py-2"
                                                    >
                                                    {{ item.costing_name }}
                                                </b-badge>
                                                <b-badge
                                                v-for="item in form_discharge.rad_request"
                                                :key="'rad-' + item.radiology_package_id"
                                                variant="info"
                                                class="px-3 py-2"
                                                >
                                                {{ item.costing_name }}
                                                </b-badge>
                                            </div>
                                            </div>

                                            <b-tabs class="mt-8 tab-title" small>

                                            <!-- LABORATORIUM -->
                                            <b-tab title="Laboratorium">
                                                <b-form-input v-model="searchDischargedLab" placeholder="Pencarian" size="sm" class="mt-8" />
                                                <div class="mt-8 py-2 bg-neutral-100" style="height: 250px; overflow-y: scroll;">
                                                <b-form-checkbox-group size="sm" class="col-lg-12" v-model="form_discharge.lab_request">
                                                    <div v-for="(item, index) in filteredDischargedLab" :key="index" :class="index != 0 ? 'mt-10' : 'mt-0'">
                                                        <div class="fs-14 font-weight-bold text-uppercase">{{ item.category_name }}</div>
                                                        <div class="row mx-0">
                                                            <b-form-checkbox
                                                                v-for="(list, packIndex) in item.category_items"
                                                                :key="packIndex"
                                                                :value="{ laboratory_package_id: list.id, costing_name: list.costing_name }"
                                                                class="mr-0 col-lg-4"
                                                                >
                                                                {{ list.costing_name }}
                                                            </b-form-checkbox>
                                                        </div>
                                                    </div>
                                                </b-form-checkbox-group>
                                                </div>
                                            </b-tab>

                                            <!-- RADIOLOGI -->
                                            <b-tab title="Radiologi">
                                                <b-form-input v-model="searchDischargedRadiology" placeholder="Pencarian" size="sm" class="mt-8" />
                                                <div class="mt-8 py-2 bg-neutral-100" style="height: 250px; overflow-y: scroll;">
                                                <b-form-checkbox-group size="sm" class="col-lg-12" v-model="form_discharge.rad_request">
                                                    <div v-for="(item, index) in filteredDischargedRadiology" :key="index" :class="index != 0 ? 'mt-10' : 'mt-0'">
                                                    <div class="fs-14 font-weight-bold text-uppercase">{{ item.category_name }}</div>
                                                    <div class="row mx-0">
                                                        <b-form-checkbox
                                                        v-for="(list, packIndex) in item.category_items"
                                                        :key="packIndex"
                                                        :value="{ radiology_package_id: list.id, costing_name: list.costing_name }"
                                                        class="mr-0 col-lg-4"
                                                        >
                                                        {{ list.costing_name }}
                                                        </b-form-checkbox>
                                                    </div>
                                                    </div>
                                                </b-form-checkbox-group>
                                                </div>
                                            </b-tab>

                                            <!-- FISIOTERAPI -->
                                            <!-- <b-tab title="Fisioterapi">
                                                <b-form-input v-model="searchDischargedPhysio" placeholder="Pencarian" size="sm" class="mt-8" />
                                                <div class="mt-8 py-2 bg-neutral-100" style="height: 250px; overflow-y: scroll;">
                                                <b-form-checkbox-group size="sm" class="col-lg-12" v-model="form_discharge.physiotherapy_items">
                                                    <b-form-checkbox v-for="(ip, index) in filteredDischargedPhysio" v-bind:key="index" :value="{'service_class_id': ip.service_class_id,'physiotherapy_package_id':ip.id, 'costing_name': ip.costing_name}" class="mr-0 col-lg-4">{{ip.costing_name }}</b-form-checkbox>
                                                </b-form-checkbox-group>
                                                </div>
                                            </b-tab> -->

                                            </b-tabs>
                                        </div>
                                        </section>
                                    <hr>

                                    <section class="row mb-2">
                                            <div class="col-lg-6">
                                                <label class="fs-13 mb-0">Petugas</label>
                                                <v-select v-model="form_discharge.nurse_id" :options="nurseList" :reduce="item => item.value" label="title" class="select-size-sm" placeholder="pilih perawat"></v-select>
                                            </div>
                                    </section>
                            </template>

                            <template v-if="activeHdTab == 3">
                                <div class="fs-13 font-weight-bold mb-12 mt-2">SURAT KETERANGAN HEMODIALISIS RAWAT JALAN LEBIH DARI 2 KALI SEMINGGU</div>
                                <label class="fs-13 font-weight-bold mb-1">Diagnosis:</label>
                                <section class="row">
                                    <div class="col-12">
                                        <template v-if="form_surat_keterangan.icd10.length > 0">
                                             <b-table responsive bordered small :fields="icd10FieldsForm" :items="form_surat_keterangan.icd10" class="mt-12 text-center" style="font-size: 14px;" >
                                                <template #cell(no)="row">{{ row.index+1 }}</template>
                                                     <template #cell(title)="row">
                                                        <div class="text-left">{{ row.value }}</div>
                                                     </template>
                                                    <template #cell(action)="row">
                                                       <b-button variant="danger-600" size="sm" @click="delete10Form(row.index)" class="mr-2">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                </template>
                                            </b-table>
                                        </template>
                                        <v-select v-model="icd10_id_form" @input="onAdd10Form($event)" aria-readonly="true" @search="onSearchIcd10Form" :options="OptionsIcd10Form" :reduce="item => item" label="title" placeholder="-- Diagnosa --" class="select-size-sm mt-12" />
                                    </div>
                                </section>
                                <section class="row mt-2">
                                    <div class="col-6">
                                        <label class="fs-13 mb-0">Indikasi HD Lebih dari 2 kali seminggu</label>
                                            <v-select
                                                    v-model="form_surat_keterangan.hd_indication"
                                                    :options="optHdIndication"
                                                    :reduce="item => item.value"
                                                    label="title"
                                                    placeholder="-- Pilih --"
                                                    class="select-size-sm"
                                            />
                                    </div>
                                    <div class="col-lg-6">
                                            <label class="fs-13 mb-0">Berlaku</label>
                                                <v-select
                                                    v-model="form_surat_keterangan.happens_in"
                                                    :options="optValidPeriod"
                                                    :reduce="item => item.value"
                                                    label="title"
                                                    placeholder="-- Pilih --"
                                                    class="select-size-sm"
                                                />
                                    </div>
                                </section>
                                <section class="row">
                                    <div class="col-6 mt-8">
                                        <label class="fs-13 mb-0">Supervisor Penjamin Mutu</label>
                                            <v-select
                                            v-model="form_surat_keterangan.doctor_id"
                                            :options="OptionsDoctor"
                                            :reduce="item => item.value"
                                            label="title"
                                            placeholder="-- Pilih --"
                                            class="select-size-sm"
                                        />
                                    </div>
                                    <div class="col-6 mt-8">
                                        <label class="fs-13 mb-0"> Dokter Penanggung Jawab </label>
                                        <v-select
                                            v-model="form_surat_keterangan.doctor_physiotherapy_id "
                                            :options="OptionsPhyDoctor"
                                            :reduce="item => item.value"
                                            label="title"
                                            placeholder="-- Pilih --"
                                            class="select-size-sm"
                                        />
                                    </div>
                                </section>
                                <section class="row mb-2">
                                    <div class="col-6 mt-2">
                                        <label class="fs-13 mb-0">Sekunder 1 </label>
                                        <b-form-input
                                            v-model="form_surat_keterangan.secondary_1"
                                            size="sm"
                                            autocomplete="off"
                                        />
                                    </div>
                                    <div class="col-6 mt-2">
                                        <label class="fs-13 mb-0">Sekunder 2 </label>
                                        <b-form-input
                                            v-model="form_surat_keterangan.secondary_2"
                                            size="sm"
                                            autocomplete="off"
                                        />
                                    </div>
                                </section>
                                <section class="row mb-2">
                                    <div class="col-6 mt-2">
                                        <label class="fs-13 mb-0">Sekunder 3 </label>
                                        <b-form-input
                                            v-model="form_surat_keterangan.secondary_3"
                                            size="sm"
                                            autocomplete="off"
                                        />
                                    </div>
                                    <div class="col-6 mt-2">
                                        <label class="fs-13 mb-0">Sekunder 4 </label>
                                        <b-form-input
                                            v-model="form_surat_keterangan.secondary_4"
                                            size="sm"
                                            autocomplete="off"
                                        />
                                    </div>
                                </section>
                                <section class="row mb-2">
                                    <div class="col-6 mt-2">
                                        <label class="fs-13 mb-0">Sekunder 5 </label>
                                        <b-form-input
                                            v-model="form_surat_keterangan.secondary_5"
                                            size="sm"
                                            autocomplete="off"
                                        />
                                    </div>
                                </section>
                            </template>

                            <template v-if="activeHdTab == 4">
                                <section class="row mt-2">
                                    <div class="col-lg-3">
                                        <label class="fs-13 mb-0">Riwayat Alergi</label>
                                        <v-select
                                            v-model="form_pemantauan.allergy_historical"
                                            :options="optYesNo"
                                            :reduce="item => item.value"
                                            label="title"
                                            placeholder="-- Pilih --"
                                            class="select-size-sm"
                                        />
                                    </div>
                                    <div class="col-lg-4 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">&nbsp;</label>
                                        <b-form-input v-model="form_pemantauan.allergy_historical_desc" size="sm" autocomplete="off" />
                                    </div>
                                </section>

                                <hr>

                                <div class="fs-13 font-weight-bold mb-8">Akses Vaskular</div>
                                <section class="row">
                                    <div class="col-lg-3">
                                        <label class="fs-13 mb-0">Jenis</label>
                                        <v-select
                                            v-model="form_pemantauan.vascular_access_type"
                                            :options="optVascularType"
                                            :reduce="item => item.value"
                                            label="title"
                                            placeholder="-- Pilih --"
                                            class="select-size-sm"
                                        />
                                    </div>
                                    <div class="col-lg-4 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Lokasi</label>
                                        <b-form-input v-model="form_pemantauan.vascular_access_location" size="sm" autocomplete="off" />
                                    </div>
                                </section>

                                <div class="fs-13 font-weight-bold mt-10 mb-8">Kondisi</div>
                                <section class="row">
                                    <div class="col-lg-3">
                                        <label class="fs-13 mb-0">Tanda Infeksi</label>
                                        <v-select
                                            v-model="form_pemantauan.condition_infection_sign"
                                            :options="optYesNo"
                                            :reduce="item => item.value"
                                            label="title"
                                            placeholder="-- Pilih --"
                                            class="select-size-sm"
                                        />
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Aneurisma</label>
                                        <v-select
                                            v-model="form_pemantauan.condition_aneurisma"
                                            :options="optYesNo"
                                            :reduce="item => item.value"
                                            label="title"
                                            placeholder="-- Pilih --"
                                            class="select-size-sm"
                                        />
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Thrill (AV Fistula)</label>
                                        <v-select
                                            v-model="form_pemantauan.condition_thrill"
                                            :options="optThrillBruit"
                                            :reduce="item => item.value"
                                            label="title"
                                            placeholder="-- Pilih --"
                                            class="select-size-sm"
                                        />
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Bruit (AV Fistula)</label>
                                        <v-select
                                            v-model="form_pemantauan.condition_bruit"
                                            :options="optThrillBruit"
                                            :reduce="item => item.value"
                                            label="title"
                                            placeholder="-- Pilih --"
                                            class="select-size-sm"
                                        />
                                    </div>
                                </section>
                                <section class="row mt-8">
                                    <div class="col-lg-6">
                                        <label class="fs-13 mb-0">Lain-lain</label>
                                        <b-form-textarea rows="3" size="sm" v-model="form_pemantauan.condition_other" autocomplete="off"></b-form-textarea>
                                    </div>
                                </section>

                                <hr>

                                <div class="fs-13 font-weight-bold mb-8">Untuk Tunnel dan Double Lumen Catheter:</div>
                                <section class="row mb-1">
                                    <div class="col-lg-6">
                                        <span class="fs-13 mb-1">Ukuran Lumen</span>
                                        <div class="d-flex align-items-center" style="gap: 8px;">
                                            <div>
                                                <label class="fs-13 mb-0">Arteri:</label>
                                                <b-input-group append="cm" size="sm">
                                                    <b-form-input v-model="form_pemantauan.tunnel_lumen_size_arteri" type="number" autocomplete="off" />
                                                </b-input-group>
                                            </div>
                                            <div>
                                                <label class="fs-13 mb-0">Vena:</label>
                                                <b-input-group append="cm" size="sm">
                                                    <b-form-input v-model="form_pemantauan.tunnel_lumen_size_vena" type="number" autocomplete="off" />
                                                </b-input-group>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <span class="fs-13 mb-1">Heparin Lock</span>
                                        <div class="d-flex align-items-center" style="gap: 8px;">
                                            <div>
                                                <label class="fs-13 mb-0">Arteri:</label>
                                                <b-input-group append="cm" size="sm">
                                                    <b-form-input v-model="form_pemantauan.herparin_lock_arteri" type="number" autocomplete="off" />
                                                </b-input-group>
                                            </div>
                                            <div>
                                                <label class="fs-13 mb-0">Vena:</label>
                                                <b-input-group append="cm" size="sm">
                                                    <b-form-input v-model="form_pemantauan.herparin_lock_vena" type="number" autocomplete="off" />
                                                </b-input-group>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                <section class="row mb-1">
                                    <div class="col-lg-6">
                                        <span class="fs-13 mb-1">Antibiotik Lock</span>
                                        <div class="d-flex align-items-center" style="gap: 8px;">
                                            <div>
                                                <label class="fs-13 mb-0">Arteri:</label>
                                                <b-input-group append="cm" size="sm">
                                                    <b-form-input v-model="form_pemantauan.antibiotik_lock_arteri" type="number" autocomplete="off" />
                                                </b-input-group>
                                            </div>
                                            <div>
                                                <label class="fs-13 mb-0">Vena:</label>
                                                <b-input-group append="cm" size="sm">
                                                    <b-form-input v-model="form_pemantauan.antibiotik_lock_vena" type="number" autocomplete="off" />
                                                </b-input-group>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                <section class="row mb-1">
                                    <div class="col-lg-5">
                                        <label class="fs-13 mb-0">Dialiser Model</label>
                                        <b-form-input v-model="form_pemantauan.dialiser_model" size="sm" autocomplete="off" />
                                    </div>
                                    <div class="col-lg-4 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Flux</label>
                                            <v-select
                                                    v-model="form_pemantauan.flux"
                                                    :options="optFlux"
                                                    :reduce="item => item.value"
                                                    label="title"
                                                    placeholder="-- Pilih --"
                                                    class="select-size-sm"
                                            />
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">BB Kering</label>
                                            <b-form-input v-model="form_pemantauan.bb_dry" size="sm" type="number" autocomplete="off" />
                                        </div>
                                </section>
                                <section class="row mb-1">
                                    <div class="col-lg-3">
                                        <label class="fs-13 mb-0">Mesin HD</label>
                                        <v-select
                                                    v-model="form_pemantauan.hd_machine"
                                                    :options="optHdMachine"
                                                    :reduce="item => item.value"
                                                    label="title"
                                                    placeholder="-- Pilih --"
                                                    class="select-size-sm"
                                        />
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="fs-13 mb-0">No Mesin</label>
                                        <b-form-input v-model="form_pemantauan.hd_machine_number" size="sm" autocomplete="off" />
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="fs-13 mb-0">Dialisat</label>
                                        <v-select
                                                    v-model="form_pemantauan.dialisat"
                                                    :options="optDialisat"
                                                    :reduce="item => item.value"
                                                    label="title"
                                                    placeholder="-- Pilih --"
                                                    class="select-size-sm"
                                        />
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="fs-13 mb-0">Suhu</label>
                                        <b-form-input v-model="form_pemantauan.temp" size="sm" type="number" autocomplete="off" />
                                    </div>
                                </section>

                                <hr>

                                <section class="row mb-2">
                                            <div class="col-lg-6">
                                                <label class="fs-13 mb-0">Petugas</label>
                                                <v-select v-model="form_pemantauan.nurse_id_2" :options="nurseList" :reduce="item => item.value" label="title" class="select-size-sm" placeholder="pilih perawat"></v-select>
                                            </div>
                                    </section>
                            </template>

                            <template v-if="activeHdTab == 5">

                                <section class="row mt-2">
                                    <div class="col-lg-3">
                                        <label class="fs-13 mb-0">Lama HD</label>
                                        <b-input-group append="Jam" size="sm">
                                            <b-form-input v-model="form_resep.duration_2" type="number" autocomplete="off" />
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Ultrafiltration Goal (UFG)</label>
                                        <b-input-group append="ml" size="sm">
                                            <b-form-input v-model="form_resep.ufg" type="number" autocomplete="off" />
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Blood Flow Rate (QB)</label>
                                        <b-input-group append="ml/menit" size="sm">
                                            <b-form-input v-model="form_resep.qb_2" type="number" autocomplete="off" />
                                        </b-input-group>
                                    </div>
                                </section>

                                <section class="row mt-8">
                                    <div class="col-lg-3">
                                        <label class="fs-13 mb-0">Heparin</label>
                                        <v-select
                                            v-model="form_resep.heparin"
                                            :options="optHeparin"
                                            :reduce="item => item.value"
                                            label="title"
                                            placeholder="-- Pilih --"
                                            class="select-size-sm"
                                        />
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Total</label>
                                        <b-input-group append="IU" size="sm">
                                            <b-form-input v-model="form_resep.total" type="number" autocomplete="off" />
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Bolus Awal</label>
                                        <b-input-group append="IU" size="sm">
                                            <b-form-input v-model="form_resep.bolus_start" type="number" autocomplete="off" />
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Kontinyu</label>
                                        <b-input-group append="IU" size="sm">
                                            <b-form-input v-model="form_resep.continue" type="number" autocomplete="off" />
                                        </b-input-group>
                                    </div>
                                </section>
                                <hr>
                                <section class="row mt-8">
                                    <div class="col-lg-3">
                                        <label class="fs-13 mb-0">Perubahan Obat Rutin</label>
                                        <v-select
                                            v-model="form_resep.reguler_drug_change"
                                            :options="optYesNo"
                                            :reduce="item => item.value"
                                            label="title"
                                            placeholder="-- Pilih --"
                                            class="select-size-sm"
                                        />
                                    </div>
                                    <div class="col-6">
                                        <label class="fs-13 mb-0"> Dokter Penanggung Jawab </label>
                                        <v-select
                                            v-model="form_resep.doctor_pj_code"
                                            :options="OptionsPhyDoctor"
                                            :reduce="item => item.value"
                                            label="title"
                                            placeholder="-- Pilih --"
                                            class="select-size-sm"
                                            @input="onSelectPrescriptionDoctor"
                                        />
                                    </div>
                                </section>
                                <section class="row">
                                    <div class="col-lg-6">
                                        <label class="fs-13 mb-0">Lain-lain</label>
                                        <b-form-textarea rows="3" size="sm" v-model="form_resep.other_3" autocomplete="off"></b-form-textarea>
                                    </div>
                                </section>
                            </template>

                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <b-button-group size="sm" style="gap: 10px;">
                                <b-button @click="changeTab('FUNCTIONAL')" :disabled="active.disabled.section" variant="outline-neutral-700">
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button @click="saveHdSubTab()" :disabled="active.disabled.section" variant="primary">
                                    <feather-icon icon="SaveIcon" />
                                    Simpan {{ hdSubTabLabel }}
                                </b-button>
                                <b-button v-if="active.section !== 'HEMODIALYSIS'" @click="changeSection(active.section, 'HEMODIALYSIS')" :disabled="active.disabled.section" variant="orange-500">
                                    <feather-icon icon="SaveIcon" />
                                    Simpan
                                </b-button>
                            </b-button-group>
                        </div>
                    </section>

                    <section v-if="active.section == 'PHYSIOTHERAPY'" class="card">
                        <div class="card-header bg-primary text-white fs-14 font-weight-bold">FISIOTERAPI</div>
                        <div class="card-body px-20 py-8 position-sticky" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <div class="soap-reference-list mb-3">
                                <div class="fs-13 font-weight-bold text-neutral-600 mb-2">PILIH REFERENSI ASSESMEN:</div>

                                <div
                                    v-for="(item, index) in history.soap"
                                    :key="item.id"
                                    class="soap-reference-item d-flex align-items-center px-12 py-8"
                                    :class="{ 'active': form_physiotherapy.soap_id === item.id }"
                                    @click="selectSoapReference(item)"
                                >
                                    <feather-icon
                                        :icon="form_physiotherapy.soap_id === item.id ? 'CheckCircleIcon' : 'CircleIcon'"
                                        size="16"
                                        class="mr-2 flex-shrink-0"
                                    />
                                    <span class="fs-13">
                                        Referensi dari SOAP {{ item.doctor_in_charge_name }} waktu kunjungan {{ localDateTime(item.date_added) }}
                                    </span>
                                </div>
                            </div>
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
                                    @click="changeTab('FUNCTIONAL')"
                                    :disabled="active.disabled.section"
                                    variant="outline-neutral-700"
                                    class="rounded"
                                    size="sm"
                                >
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button
                                    @click="savePhysiotherapy(active.section, active.section)"
                                    :disabled="active.disabled.section"
                                    variant="w3green"
                                    class="ml-2 rounded"
                                    size="sm"
                                >
                                    <feather-icon icon="SaveIcon" />
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
                <div class="d-flex justify-content-center w-100" style="gap: 10px;">
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
import dateMixin from "@/mixins/dateMixin";
import optionMixin from '@/mixins/optionMixin';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue';
import moment from "moment";
import { mapState } from 'vuex';
import LabResultModalView from '../medical_support/LabResultModalView.vue';
import RadResultModalView from '../medical_support/RadResultModalView.vue';

export default {
    components: {LabResultModalView, RadResultModalView},
    mixins: [dateMixin, optionMixin],
    props: {
    lab_list: {
      type: Array,
      default: () => []
    },
    rad_list: {
      type: Array,
      default: () => []
    },
    phy_list: {
      type: Array,
      default: () => []
    },
  },
    data() {
        return {
            urlbe:process.env.VUE_APP_IMAGE,
            urlAsset:process.env.VUE_APP_ASSETS_BE,
            userData: JSON.parse(localStorage.getItem('userData')),
            dynamicHeight: "73vh",
            differenceHeight: "85px",
            hfii_physiotherapy: 'd350e55d-6f8a-4c42-b269-cfda717fbf8b',
            regData: {},
            nurseList: [],
            OptionsIcd10DataHd:[],
            icd10_id_hd:'',
            OptionsIcd10Form:[],
            icd10_id_form:'',
            global_nurse_id: '',
            activeHdTab: 0,
            active: {
                modal: {
                    main: false,
                    // rad_result_modal: false,
                },
                disabled: {
                    section: false,
                },
                advance: {
                    antopometri: false,
                },
                section: 'ANAMNESA'
            },
            skipVascularReset: false,
            isPews: false,
            nextTab: null,
            original_anamnesa: {},
            original_vital: {},
            original_functional: {},
            original_hemodialysis: {},
            searchDischargedLab: '',
            searchDischargedRadiology: '',
            searchDischargedPhysio: '',
            nurseSession: {
                id: '',
                nurse_hd_id: '',
                nurse_trx_id: '',
                nurse_hd_intra_id: '',
                nurse_phy_id: '',
            },
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
                objective: '',
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
            form_hemodialysis: {
                icd10: [],
                duration: '',
                dialist: '',
                liquid_withdraw: '',
                access: '',
                transfusion: '',
                qb: '',
                qd: '',
                ureum: '',
                hbsag: '',
                hiv: '',
                hb: '',
                creatinin: '',
                hcv: '',
                other: '',
            },
            form_observasi: {
                hour_observation: '',
                complaint: '',
                bb: '',
                awareness: '',
                td: '',
                pulse: '',
                rr: '',
                temp: '',
                qb: '',
                qd: '',
                venous_pressure: '',
                tmp: '',
                drawn_volume: '',
                assessment: '',
                pic:'',
            },
            form_surat_keterangan: {
                icd10: [],
                hd_indication: '',
                happens_in: '',
                doctor_id: '',
                doctor_physiotherapy_id: '',
                secondary_1: '',
                secondary_2: '',
                secondary_3: '',
                secondary_4: '',
                secondary_5: '',
            },
            form_discharge: {
                next_hd_date: '',
                education: '',
                other_2: '',
                consult_planning: '',
                nurse_id: '',
                lab_request: [],
                rad_request: [],
            },
            form_pemantauan: {
                allergy_historical: '', allergy_historical_desc: '',
                vascular_access_type: '', vascular_access_location: '',
                condition_infection_sign: '', condition_aneurisma: '', condition_thrill: '', condition_bruit: '', condition_other: '',
                tunnel_lumen_size_arteri: '', tunnel_lumen_size_vena: '',
                herparin_lock_arteri: '', herparin_lock_vena: '',
                antibiotik_lock_arteri: '', antibiotik_lock_vena: '',
                dialiser_model: '', flux: '', bb_dry: '',
                hd_machine: '', hd_machine_number: '', dialisat: '', temp: '',
                nurse_id_2: '',
            },
            form_resep: {
                duration_2: '',
                ufg: '',
                qb_2: '',
                heparin: '',
                total: '',
                bolus_start: '',
                continue: '',
                other_3: '',
                reguler_drug_change: '',
                doctor_pj_code: '',
                doctor_pj_name: '',
            },
            form_physiotherapy: {
                soap_id: '',
                nurse_id: '',
                doctor_in_charge_id: '',
                registration_destination_id: '',
                physiotherapy_request_items: [],
            },
            searchphy:'',
            live_sub: {
                neonatus: false,
                child: false,
                adult: false,
            },
            list_intra: [],
            list_intra_fields: [
                { key: 'hour_observation', label: 'Jam Ke' },
                { key: 'complaint', label: 'Keluhan' },
                { key: 'bb', label: 'BB (Kg)' },
                { key: 'awareness', label: 'Kesadaran' },
                { key: 'td', label: 'Tensi (mmHg)' },
                { key: 'pulse', label: 'Nadi (x/mnt)' },
                { key: 'rr', label: 'Respirasi (x/menit)' },
                { key: 'qb', label: 'Qb (ml/mnt)' },
                { key: 'qd', label: 'Qd (ml/mnt)' },
                { key: 'venous_pressure', label: 'Tek. Vena (mmHg)' },
                { key: 'tmp', label: 'TMP (mmHg)' },
                { key: 'drawn_volume', label: 'Vol. Ditarik' },
                { key: 'assessment', label: 'Asesmen' },
                { key: 'nurse_name', label: 'Nama Petugas' },
            ],
            icd10FieldsDataHd: [
                { key: 'action', label: 'Aksi', thStyle: 'width: 60px' },
                { key: 'no', label: 'No', thStyle: 'width: 60px' },
                { key: 'title', label: 'ICD10', thClass: 'text-left' },
            ],
            icd10FieldsForm: [
                { key: 'action', label: 'Aksi', thStyle: 'width: 60px' },
                { key: 'no', label: 'No', thStyle: 'width: 60px' },
                { key: 'title', label: 'ICD10', thClass: 'text-left' },
            ],
            history: {
                cppt: [],
                nurse: [],
                soap: [],
                laboratory: [],
                radiology: [],
                physiotherapy: [],
                resume: [],
                examination: [],
            },
            supportPackage: {
                lab_package_list: [],
                rad_package_list: [],
                phy_package_list: [],
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
            optAccess: [
                { title: 'Femoral', value: 'Femoral' },
                { title: 'Cimino', value: 'Cimino' },
                { title: 'Tunneling', value: 'Tunneling' },
                { title: 'CDL Temprorer', value: 'CDL Temprorer' },
            ],
            optMonitoringSession: [
                { title: 'Pre-HD', value: 'Pre-HD' },
                { title: '1', value: '1' },
                { title: '2', value: '2' },
                { title: '3', value: '3' },
                { title: '4', value: '4' },
                { title: '5', value: '5' },
                { title: '6', value: '6' },
                { title: '7', value: '7' },
                { title: '8', value: '8' },
                { title: 'Post-HD', value: 'Post-HD' },
            ],
            optConsciousness: [
                { title: 'Compos Mentis', value: 'Compos Mentis' },
                { title: 'Sopor', value: 'Sopor' },
                { title: 'Koma', value: 'Koma' },
                { title: 'Apatis', value: 'Apatis' },
            ],
            optHdIndication: [
                { title: 'Hemodinamik tidak stabil', value: 'Hemodinamik tidak stabil'},
                { title: 'Keperluan perioperatif', value: 'Keperluan perioperatif'},
                { title: 'Ibu hamil', value: 'Ibu hamil'},
                { title: 'Kondisi overhidrasi yang sulit dikendalikan', value: 'Kondisi overhidrasi yang sulit dikendalikan'},
                { title: 'Pasien dengan berat badan kering > 75 kg', value: 'Pasien dengan berat badan kering > 75 kg'},
                { title: 'Pasien transplantasi ginjal dengan rekasi rejeksi', value: 'Pasien transplantasi ginjal dengan rekasi rejeksi'},
                { title: 'Pasien transplantasi ginjal dengan delayed graft function', value: 'Pasien transplantasi ginjal dengan delayed graft function'}
            ],
            optValidPeriod: [
                { title: '1 Bulan', value: '1 Bulan' },
                { title: '3 Bulan', value: '3 Bulan' },
            ],
            optYesNo: [
                { title: 'Ya', value: 'Ya' },
                { title: 'Tidak', value: 'Tidak' },
            ],
            optVascularType: [
                { title: 'Fistula AV (Cimino)', value: 'Fistula AV (Cimino)' },
                { title: 'Tunnel Catheter', value: 'Tunnel Catheter' },
                { title: 'Femoral', value: 'Femoral' },
                { title: 'Double Lumen Catheter', value: 'Double LumenCatheter' },
            ],
            optStrength: [
                { title: 'Lemah', value: 'Lemah' },
                { title: 'Kuat', value: 'Kuat' },
            ],
            optFlux: [
                { title: 'Low flux', value: 'Low flux' },
                { title: 'High flux', value: 'High flux' },
                { title: 'Baru', value: 'Baru' },
                { title: 'Reuse', value: 'Reuse' },
            ],
            optHdMachine: [
                { title: 'Fresenius', value: 'Fresenius' },
                { title: 'Nipro', value: 'Nipro' },
                { title: 'DLL', value: 'DLL' },
            ],
            optDialisat: [
                { title: 'High Calcium (Ca > 1.3 mmol/L)', value: 'High Calcium' },
                { title: 'Low Calcium (Ca <= 1.3 mmol/L)', value: 'Low Calcium' },
            ],
            // optHeparinNew: [
            //     { title: 'Regular', value: 'Regular' },
            //     { title: 'Minimal', value: 'Minimal' },
            //     { title: 'Free', value: 'Free' },
            // ],
            today_appointment_filter: {
                day: '',
                health_facility_id: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
                health_facility_item_id: 'd350e55d-6f8a-4c42-b269-cfda717fbf8b',
                doctor_id: '',
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

        //  =============== HEMODIALISA ================
        "form_hemodialysis.dialysate_type_id"(newValue) {
            if (newValue != "3") {
                this.form_hemodialysis.dialysate_temperature = ""
            }
        },

        "form_hemodialysis.vascular_type_id"(newValue) {
            if (newValue != "4") {
                this.form_hemodialysis.vascular_jugularis = ""
            }
        },

        "form_hemodialysis.heparin_type_id"(newValue) {
            if (newValue != 1 && newValue != 2) {
                this.form_hemodialysis.initial_dose = ""
                this.form_hemodialysis.intermittent = ""
                this.form_hemodialysis.without_heparin = ""

            } else if (newValue == 1) {
                this.form_hemodialysis.without_heparin = ""

            } else if (newValue == 2) {
                this.form_hemodialysis.initial_dose = ""
                this.form_hemodialysis.intermittent = ""
            }
        },

        "form_hemodialysis.profiling_type_id"(newValue) {
            if (newValue != 1 && newValue != 2 && newValue != 3) {
                this.form_hemodialysis.ultrafiltration = ""
                this.form_hemodialysis.natrium = ""
                this.form_hemodialysis.bicarbonate = ""

            } else if (newValue == 1) {
                this.form_hemodialysis.natrium = ""
                this.form_hemodialysis.bicarbonate = ""

            } else if (newValue == 2) {
                this.form_hemodialysis.ultrafiltration = ""
                this.form_hemodialysis.bicarbonate = ""

            } else if (newValue == 3) {
                this.form_hemodialysis.ultrafiltration = ""
                this.form_hemodialysis.natrium = ""
            }
        },
        'form_pemantauan.vascular_access_type'(newValue) {
            if (this.skipVascularReset) return
            this.form_pemantauan.condition_thrill = ''
            this.form_pemantauan.condition_bruit = ''
        },
        // 'today_appointment_filter.health_facility_item_id' (value){
        //     if (value) {
        //         return this.$store.dispatch('employee/getParam', value)
        //     }
        // },
    },

    computed: {
        ...mapState({
            optAnamnesaType: state => state.frontoptions.nurse.assessment.anamnesa.anamnesa_type,
            optFamilyRelation: state => state.frontoptions.nurse.assessment.anamnesa.family_relation,
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,

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

            optHemoType: state => state.frontoptions.nurse.assessment.hemodialysis.hemo_type,
            optHemoFrequency: state => state.frontoptions.nurse.assessment.hemodialysis.hemo_frequency,
            optHemoDialysate: state => state.frontoptions.nurse.assessment.hemodialysis.hemo_dialysate,
            optHemoVascular: state => state.frontoptions.nurse.assessment.hemodialysis.hemo_vascular,
            optHeparin: state => state.frontoptions.nurse.assessment.hemodialysis.heparin,
            optProfiling: state => state.frontoptions.nurse.assessment.hemodialysis.profiling,

            OptionsDoctor: state => state.employee.options.employee,
            OptionsPhyDoctor: state => state.employee.options.physio_employee,
        }),
        hdSubTabLabel() {
            const labels = {
            0: 'Data Hemodialisa',
            1: 'Observasi Intradialitik',
            2: 'Perencanaan Pulang',
            3: 'Formulir & Surat Keterangan',
            4: 'Pemantauan Harian',
            5: 'Resep HD',
            }
            return labels[this.activeHdTab] || ''
        },
        filteredDischargedLab() {
            return this.lab_list.map((lab) => {
                const filteredCategoryItems = lab.category_items.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchDischargedLab.toLowerCase())
                );

                return {
                    ...lab,
                    category_items: filteredCategoryItems,
                };
            }).filter(lab => lab.category_items.length > 0);
        },
        filteredDischargedRadiology() {
            if (!this.searchDischargedRadiology) return this.rad_list
            return this.rad_list.filter(item =>
                item.category_name.toLowerCase().includes(this.searchDischargedRadiology.toLowerCase()) ||
                item.category_items.some(i => i.costing_name.toLowerCase().includes(this.searchDischargedRadiology.toLowerCase()))
            )
        },
        filteredDischargedPhysio() {
            return this.phy_list.filter((phy) => {
                const computedPhyObj = { ...phy}
                return Object.keys(computedPhyObj)
                    .some(key => ('' + computedPhyObj[key]).toLowerCase().includes(this.searchDischargedPhysio.toLowerCase()))
            })
        },
        filteredListPhy() {
            return this.phy_list.filter((phy) => {
                const computedPhyObj = { ...phy}
                return Object.keys(computedPhyObj)
                    .some(key => ('' + computedPhyObj[key]).toLowerCase().includes(this.searchphy.toLowerCase()))
            })
        },
        optThrillBruit() {
        const tunnelTypes = ['Tunnel Catheter', 'Double LumenCatheter']
            if (tunnelTypes.includes(this.form_pemantauan.vascular_access_type)) {
            return [
                { title: 'Lancar', value: 'Lancar' },
                { title: 'Tidak Lancar', value: 'Tidak Lancar' },
            ]
            }
            return this.optStrength
        },
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
        onSelectPrescriptionDoctor(selectedCode) {
            const found = this.OptionsPhyDoctor.find(item => item.value === selectedCode)
            this.form_resep.doctor_pj_name = found ? found.title : ''
        },
        onSearchIcd10Form(search, loading) {
            if(search.length >= 3) {
                loading(true);
                this.$store.dispatch('icd10/getParam', search).then(data => {
                    this.OptionsIcd10Form = []
                    for (let i = 0; i < data.length; i++) {
                        const option = []
                        for (const key in data[i]) {
                            if (key === 'id') { option['value'] = data[i][key] } else if (key === 'name') { option['title'] = data[i]['code']+' '+data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                        }
                        this.OptionsIcd10Form.push(Object.assign({}, option))
                    }
                    loading(false)
                })
            }
        },

        onAdd10Form(event) {
            this.icd10_id_form = ''

            if (this.form_surat_keterangan.icd10.some(item => item.icd10_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD10 yang sama')
            } else {
                this.form_surat_keterangan.icd10.push({ icd10_id:event.value, title:event.title, value:event.value, action: 'action'})
            }

        },
        delete10Form(index) {
            this.form_surat_keterangan.icd10.splice(index, 1)
        },

        onSearchIcd10DataHd(search, loading) {
            if(search.length >= 3) {
                loading(true);
                this.$store.dispatch('icd10/getParam', search).then(data => {
                    this.OptionsIcd10DataHd = []
                    for (let i = 0; i < data.length; i++) {
                        const option = []
                        for (const key in data[i]) {
                            if (key === 'id') { option['value'] = data[i][key] } else if (key === 'name') { option['title'] = data[i]['code']+' '+data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                        }
                        this.OptionsIcd10DataHd.push(Object.assign({}, option))
                    }
                    loading(false)
                })
            }
        },

        onAdd10DataHd(event) {
            this.icd10_id_hd = ''
            // console.log('Form state right now:', JSON.parse(JSON.stringify(this.form_hemodialysis)));

            if (this.form_hemodialysis.icd10.some(item => item.icd10_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD10 yang sama')
            } else {
                this.form_hemodialysis.icd10.push({ icd10_id:event.value, title:event.title, value:event.value, action: 'action'})
            }

        },
        delete10DataHd(index) {
            this.form_hemodialysis.icd10.splice(index, 1)
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

            this.history.examination = []
            this.exa_load.limit_display = 3
            this.getExaminationList(this.regData.norm)

            // this.supportPackage.lab_package_list = message.lab_list
            // this.supportPackage.rad_package_list = message.rad_list
            this.supportPackage.phy_package_list = message.phy_list
            // await this.getDoctorPhysiotherapy()

        },

        async openMainModal(obj) {
            // console.log('openMainModal', obj)
            this.active.modal.main = true
            if (obj.gate == 'edit_gate') {
                // await this.fillNurseForm(obj.nurseData)
                await this.getMyNurse(obj.nurseData.id)
            } else {
                await this.resetNurseForm()
                await this.resetHemodialysis()

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
            if (sectionKey == 'HEMODIALYSIS') return 'Hemodialisa'
            if (sectionKey == 'PHYSIOTHERAPY') return 'Fisioterapi'
        },

        async cloneAllState() {
            this.original_anamnesa = JSON.parse(JSON.stringify(this.form_anamnesa))
            this.original_vital = JSON.parse(JSON.stringify(this.form_vital))
            this.original_functional = JSON.parse(JSON.stringify(this.form_functional))
            this.original_hemodialysis = JSON.parse(JSON.stringify(this.form_hemodialysis))
            this.original_physiotherapy = JSON.parse(JSON.stringify(this.form_physiotherapy))
        },

        changeTab(destination) {
            this.activeHdTab = 0
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
                HEMODIALYSIS: ["form_hemodialysis", "original_hemodialysis"],
                PHYSIOTHERAPY: ["form_physiotherapy", "original_physiotherapy"],
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
                HEMODIALYSIS: ["form_hemodialysis", "original_hemodialysis"],
                PHYSIOTHERAPY: ["form_physiotherapy", "original_physiotherapy"],
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

        saveChanges() {
            this.active.disabled.section = true
            this.changeSection(this.active.section, this.nextTab)
            this.$refs.confirmModal.hide()

            // const formMapping = {
            //     ANAMNESA: ["form_anamnesa", "original_anamnesa"],
            //     VITAL: ["form_vital", "original_vital"],
            //     FUNCTIONAL: ["form_functional", "original_functional"],
            //     HEMODIALYSIS: ["form_hemodialysis", "original_hemodialysis"],
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
                // HEMODIALYSIS: this.saveHemodialysis,
                HEMODIALYSIS: this.saveHdSubTab,
                PHYSIOTHERAPY: this.savePhysiotherapy,
            };

            if (!saveFunctions[exist]) return;

            if (exist == 'VITAL') {
                saveFunctions[exist](exist, destination, 'main_section');
            } else {
                saveFunctions[exist](exist, destination);
            }
        },

        getNextSection(){
            if (this.regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e') return 'HEMODIALYSIS'
            return 'FUNCTIONAL'
        },

        getTypeNurse(hfii) {
            if (hfii == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e') return 13
            return 1
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
            this.form_anamnesa.type_nurse_id ||= this.getTypeNurse(this.regData.health_facility_item_id)

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
                        this.storeSessionFromResponse(res)
                        this.getMyNurse(res?.data?.id)
                        this.nurseSession.id = (res?.data?.id)
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
            this.form_vital.type_nurse_id ||= this.getTypeNurse(this.regData.health_facility_item_id)

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
                            this.storeSessionFromResponse(res)
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
            this.form_functional.type_nurse_id ||= this.getTypeNurse(this.regData.health_facility_item_id)

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
                        this.storeSessionFromResponse(res)
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

        storeSessionFromResponse(res) {
            if (!res?.data) {
                console.warn('storeSession: no res.data')
                return
            }

            const data = res.data
            const sessionMap = {
                id:                data?.id,
                nurse_hd_id:       data?.nurse_hd?.[0]?.id,
                nurse_trx_id:      data?.nurse_hd?.[0]?.nurse_trx_id ?? data?.id,
                nurse_hd_intra_id: data?.current_intra?.id,
            }

            Object.entries(sessionMap).forEach(([key, value]) => {
                if (value) this.nurseSession[key] = value
            })

        },

        async saveHdSubTab(exist, destination) {
            this.form_hemodialysis.employee_nurse_id ||= this.userData.employee_id;
            this.form_hemodialysis.registration_destination_id ||= this.regData.registration_destination_id;
            this.form_hemodialysis.type_nurse_id ||= this.getTypeNurse(this.regData.health_facility_item_id);

            try {
                this.active.disabled.section = true;

                const sharedAttr = {
                    registration_destination_id: this.regData.registration_destination_id,
                    employee_nurse_id: this.userData.employee_id,
                    type_nurse_id: this.getTypeNurse(this.regData.health_facility_item_id),
                    id:           this.nurseSession.nurse_hd_id || '',
                    nurse_trx_id: this.nurseSession.nurse_trx_id || '',
                };

                const intraPayload = this.activeHdTab === 1
                    ? { intra: { ...sharedAttr, ...this.form_observasi } }
                    : {};

                const masterPayload = {
                    exist: 'HEMODIALYSIS',
                    data: {
                        ...sharedAttr,
                        ...this.form_hemodialysis,

                        ...intraPayload,

                        ...this.form_discharge,
                        lab_request: this.form_discharge?.lab_request?.map(item => item.laboratory_package_id) || [],
                        rad_request: this.form_discharge?.rad_request?.map(item => item.radiology_package_id) || [],

                        ...this.form_surat_keterangan,

                        ...this.form_pemantauan,

                        ...this.form_resep,

                        diagnose_2: this.form_hemodialysis.icd10,
                        icd10: this.form_surat_keterangan.icd10,
                    }
                };

                // console.log('Master Payload:', masterPayload);

                await this.$store.dispatch('nurse/putSections', masterPayload)
                    .then(res => {
                        this.storeSessionFromResponse(res);
                        this.notification('Success', 'CheckIcon', 'success', `Berhasil menyimpan ${this.hdSubTabLabel}`);
                        this.getMyNurse(res?.data?.id);

                        if (this.activeHdTab === 1 && res?.data?.list_intra) {
                            this.list_intra = res.data.list_intra;
                        }
                    })
                    .catch(({ message, debug }) => {
                        this.notification(`Failed ${debug}`, 'CloseIcon', 'danger', `${message}`);
                    });

            } catch (error) {
                this.notification('Failed', 'CloseIcon', 'danger', 'Gagal menyimpan data');
                console.error('Error catch at: ', error);
            } finally {
                this.active.disabled.section = false;
            }
        },

        async savePhysiotherapy(exist, destination) {
            console.log(this.nurseSession)
            // this.form_physiotherapy.employee_nurse_id ||= this.userData.employee_id
            this.form_physiotherapy.doctor_in_charge_id ||= this.regData.doctor_in_charge_id
            this.form_physiotherapy.registration_destination_id ||= this.regData.registration_destination_id
            this.form_physiotherapy.type_nurse_id ||= this.getTypeNurse(this.regData.health_facility_item_id)

            const payload = {
                ...this.form_physiotherapy,
                id: this.nurseSession.nurse_phy_id || '',
                nurse_id: this.nurseSession.id || '',
            }

            let fixed_data = {
                exist: exist,
                data: payload
            }

            try {
                this.active.disabled.section = true
                await this.$store.dispatch('nurse/putSections', fixed_data)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','Berhasil menyimpan fisioterapi')
                        // this.resetNurseForm()
                        this.storeSessionFromResponse(res)
                        this.getMyNurse(res?.data?.id)
                        this.nurseSession.nurse_phy_id = (res?.data?.id)
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
            // console.log('fillNurseForm', obj)
            // await this.resetNurseForm()
            await this.resetHemodialysis()
            await this.resetPhysiotherapy()

            const [v_age, v_gender] = await Promise.all([
                this.getActualAge(),
                this.getActualGender()
            ]);

            if (obj) {
                this.$nextTick(() => {
                    this.global_nurse_id = obj?.id || '',
                    this.nurseSession.id = obj?.id || '',
                    // =============================================================================================
                    this.form_anamnesa.id = obj?.id || ''
                    this.form_anamnesa.employee_nurse_id = obj?.employee_nurse_id || this.userData.employee_id
                    this.form_anamnesa.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_anamnesa.type_nurse_id ||= this.getTypeNurse(this.regData.health_facility_item_id)
                    this.form_anamnesa.anamesa_type_id = obj?.anamesa_type_id || 1
                    this.form_anamnesa.family_relation_id = obj?.family_relation_id ||  0
                    this.form_anamnesa.family_name = obj?.family_name || ''
                    this.form_anamnesa.main_complaint = obj?.main_complaint || ''
                    this.form_anamnesa.history_disease_now = obj?.history_disease_now || ''
                    this.form_anamnesa.history_disease_past = obj?.history_disease_past || ''
                    this.form_anamnesa.objective = obj?.objective || ''

                    // =============================================================================================
                    this.form_vital.id = obj?.id || ''
                    this.form_vital.employee_nurse_id = obj?.employee_nurse_id || this.userData.employee_id
                    this.form_vital.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                    this.form_vital.type_nurse_id ||= this.getTypeNurse(this.regData.health_facility_item_id)
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
                    this.form_functional.type_nurse_id = obj?.type_nurse_id || this.getTypeNurse(this.regData.health_facility_item_id)
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

                    this.fillPhysiotherapyForm(obj.physiotherapy_request)
                    

                    if (obj.type_nurse_id == 13) {
                        this.fillHemodialysisForm(obj.hd)
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
            this.form_anamnesa.objective = ''
            this.nurseSession.id = ''
            this.nurseSession.nurse_hd_id = ''

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

        fillHemodialysisForm(hd_data) {
            // console.log('fillHemodialysisForm', hd_data)
            this.resetHemodialysis()

            if (!hd_data) return

            this.nurseSession.nurse_hd_id = hd_data.id
            this.nurseSession.nurse_trx_id = hd_data.nurse_trx_id

            this.skipVascularReset = true

            const assignOnly = (target, source) => {
                Object.keys(target).forEach(key => {
                    if (source[key] !== undefined) target[key] = source[key]
                })
            }

            const { icd10, diagnose, lab_item, rad_item, ...restData } = hd_data

            assignOnly(this.form_hemodialysis, restData)
            assignOnly(this.form_pemantauan, restData)
            assignOnly(this.form_surat_keterangan, restData)
            assignOnly(this.form_resep, restData)

            const { lab_item: _l, rad_item: _r, ...dischargeData } = restData
            assignOnly(this.form_discharge, dischargeData)

            this.$nextTick(() => {
                if (hd_data?.intra && hd_data.intra.length > 0) {
                    this.list_intra = hd_data.intra;
                } else {
                    this.list_intra = [];
                }

                // buat diagnosa (icd10) di formulir suket hd
                if (diagnose) {
                    const mappedIcd10 = diagnose.map(item => {
                        return {
                            icd10_id: item.icd10_id,
                            value: item.value,
                            title: item.title,
                            action: "action"
                        };
                    });
                    this.form_hemodialysis.icd10 = mappedIcd10;
                } else {
                    this.form_hemodialysis.icd10 = []
                }

                // buat diagnosa (icd10) di data hd
                if (icd10) {
                    const mappedIcd10 = icd10.map(item => {
                        return {
                            icd10_id: item.icd10_id,
                            value: item.value,
                            title: item.title,
                            action: "action"
                        };
                    });
                    this.form_surat_keterangan.icd10 = mappedIcd10;
                } else {
                    this.form_surat_keterangan.icd10 = []
                }

                if (lab_item?.length) {
                    this.form_discharge.lab_request = lab_item.map(item => ({
                        laboratory_package_id: item.laboratory_package_id,
                        costing_name:          item.name,
                    }))
                }

                if (rad_item?.length) {
                    this.form_discharge.rad_request = rad_item.map(item => ({
                        radiology_package_id: item.radiology_package_id,
                        costing_name: item.name,
                    }))
                }

                this.cloneAllState()
                this.skipVascularReset = false
            })
        },

        fillPhysiotherapyForm(obj) {
            this.resetPhysiotherapy()
            if (obj) {
                this.form_physiotherapy.soap_id = obj[0]?.soap_id || ''
                this.form_physiotherapy.doctor_in_charge_id = obj[0]?.doctor_in_charge_id || this.regData.doctor_in_charge_id
                this.form_physiotherapy.registration_destination_id = obj[0]?.registration_destination_id || this.regData.registration_destination_id
                if (obj && obj.length > 0) {
                    const mappedPhy = obj[0].physiotherapy_request_items.map(item => {
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
            }
        },

        async resetPhysiotherapy() {
            this.form_physiotherapy.physiotherapy_request_items = []
        },

        async resetHemodialysis() {
            this.list_intra = [];
            this.icd10_id_hd = '';
            this.icd10_id_form = '';
            this.form_hemodialysis = {
                duration: '', dialist: '', liquid_withdraw: '', access: '',
                transfusion: '', qb: '', qd: '', ureum: '', hbsag: '',
                hiv: '', hb: '', creatinin: '', hcv: '', other: '', icd10: [],
            };

            this.form_observasi = {
                hour_observation: '', complaint: '', bb: '', awareness: '',
                td: '', pulse: '', rr: '', temp: '', qb: '', qd: '',
                venous_pressure: '', tmp: '', drawn_volume: '', assessment: '',
                pic: '', nurse_id: '',
            };

            this.form_surat_keterangan = {
                icd10: [], hd_indication: '', happens_in: '',
                doctor_id: '', doctor_physiotherapy_id: '',
                secondary_1: '', secondary_2: '', secondary_3: '',
                secondary_4: '', secondary_5: '',
            };

            this.form_discharge = {
                next_hd_date: '', education: '', other_2: '', nurse_id: '', consult_planning: '',
                lab_request: [], rad_request: [],
            };

            this.form_pemantauan = {
                allergy_historical: '', allergy_historical_desc: '',
                vascular_access_type: '', vascular_access_location: '',
                condition_infection_sign: '', condition_aneurisma: '',
                condition_thrill: '', condition_bruit: '', condition_other: '',
                tunnel_lumen_size_arteri: '', tunnel_lumen_size_vena: '',
                herparin_lock_arteri: '', herparin_lock_vena: '',
                antibiotik_lock_arteri: '', antibiotik_lock_vena: '',
                dialiser_model: '', flux: '', bb_dry: '',
                hd_machine: '', hd_machine_number: '', dialisat: '', temp: '',
                nurse_id_2: '',
            };

            this.form_resep = {
                duration_2: '', ufg: '', qb_2: '', heparin: '', total: '',
                bolus_start: '', continue: '', other_3: '', reguler_drug_change: '',
                doctor_pj_code: '', doctor_pj_name: '',
            };
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
            this.form_anamnesa.objective = obj.objective
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
            this.form_anamnesa.objective = obj.objective
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
        selectSoapReference(item) {
            this.form_physiotherapy.soap_id = item.id;
        }
    },
    created() {
        this.getAllNurseList()
        this.$store.dispatch('employee/getParamPhysio', this.hfii_physiotherapy)
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

    .tab-title {
        font-size: 14px;
    }

    .load-text:hover{
        color: darkblue;
        text-decoration: underline;
        background-color: antiquewhite;
        width: 100%;
    }

    .soap-reference-item {
        cursor: pointer;
        border-radius: 6px;
        transition: background-color 0.15s ease, color 0.15s ease;
        color: #495057;
    }

    .soap-reference-item:hover {
        background-color: #F1F4FB;
    }

    .soap-reference-item.active {
        background-color: #648DE1;
        color: #FFFFFF;
        font-weight: 600;
    }

    .soap-reference-item.active .feather-icon {
        color: #FFFFFF;
    }
</style>