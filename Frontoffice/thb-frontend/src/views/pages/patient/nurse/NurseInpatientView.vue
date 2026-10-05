<template>
    <div>
        <b-modal v-model="active.modal.main" scrollable size="xl" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ASESMEN PERAWAT RAWAT INAP
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
                            <template v-if="live_sub.adult">
                                <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-2" @click="changeTab('ADULT')" :variant="getMenusVariant('ADULT')">DEWASA</b-button>
                            </template>
                            <template v-if="live_sub.child">
                                <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-2" @click="changeTab('CHILD')" :variant="getMenusVariant('CHILD')">ANAK</b-button>
                            </template>
                            <template v-if="live_sub.newbornbaby">
                                <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-2" @click="changeTab('NEWBORNBABY')" :variant="getMenusVariant('NEWBORNBABY')">BAYI BARU LAHIR</b-button>
                            </template>
                            <template v-if="live_sub.neonatus">
                                <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-2" @click="changeTab('NEONATE')" :variant="getMenusVariant('NEONATE')">NEONATUS</b-button>
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
                                    <v-select class="select-size-sm" v-model="form_anamnesa.anamesa_type_id" :options="optAnamnesaType" :reduce="item => item.value" label="title" placeholder="-- Pilih Anamnesa --" />
                                </div>
                                <template v-if="form_anamnesa.anamesa_type_id == 2">
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Nama Keluarga</label>
                                        <b-form-input type="text" size="sm" v-model="form_anamnesa.family_name" autocomplete="off"></b-form-input>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Hubungan Keluarga</label>
                                        <v-select v-model="form_anamnesa.family_relation_id" class="select-size-sm" :options="optFamilyRelation" :reduce="item => item.value" label="title" placeholder="pilih" />
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
                                        <v-select v-model="form_vital.oxygen_type_id" :options="OptionsOxygen" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Kesadaran</label>
                                        <v-select v-model="form_vital.aware" :options="OptionsAware" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">GCS Mata</label>
                                        <v-select v-model="form_vital.eye_score" :options="OptionGcsEye" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">GCS verbal</label>
                                        <v-select v-model="form_vital.verbal_score" :options="OptionGcsVerbal" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">GCS Motorik</label>
                                        <v-select v-model="form_vital.motor_score" :options="OptionGcsMotoric" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
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
                                <!-- <b-form-input v-model="form_functional.pain_score" type="range" variant="orange-500" class="w-100" min="0" max="10" step="1" /> -->
                                <!-- <input v-model="form_functional.pain_score" type="range" class="form-range w-100 bg-orange-500" min="0" max="10" step="1"> -->
                            </div>

                            <template v-if="live_sub.neonatus || live_sub.child">
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

                            <template v-if="live_sub.adult">
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

                    <section v-if="active.section == 'ADULT'" class="card">
                        <div class="card-header bg-primary text-white fs-14 font-weight-bold">DEWASA</div>
                        <div class="card-body px-20 py-8 position-sticky" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">

                            <section class="">
                                <ModernLabel label="Asesmen Keperawatan - Anamnesa" />
                                <div class="mt-8">
                                    <!-- <div class="col-lg-3">
                                        <label class="fs-13 mb-0">Alat kesehatan yang terpasang</label>
                                        <v-select v-model="form_adult.medical_devices_type_id" class="select-size-sm" :options="OptionsMedicalDevices" :reduce="item => item.value" label="title" placeholder="pilih" />
                                    </div> -->
                                    <label class="font-weight-bold fs-13">ALAT KESEHATAN YANG TERPASANG </label>
                                    <!-- <div v-if="form_adult.medical_devices_type_id == 1" class="col-12"> -->
                                        <b-table-simple bordered small fixed class="text-center">
                                            <b-tr class="fs-13 ">
                                                <b-th class="align-middle" style="width: 40px;">
                                                    <b-button @click="addAdultMedical" variant="primary" size="sm">
                                                        <feather-icon icon="PlusIcon" />
                                                    </b-button>
                                                </b-th>
                                                <b-th class="align-middle" style="width: 40px;">No</b-th>
                                                <b-th class="align-middle text-left">Jenis</b-th>
                                                <b-th class="align-middle text-left">Keterangan</b-th>
                                            </b-tr>
                                            <b-tr v-for="(item, index) in form_adult.medical_devices" class="fs-12">
                                                <b-td>
                                                    <b-button @click="remAdultMedical(index)" variant="outline-danger-500" size="sm">
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </b-td>
                                                <b-td class="align-middle">{{ index+1 }}</b-td>
                                                <b-td>
                                                    <b-form-input size="sm" v-model="item.type" autocomplete="off" />
                                                </b-td>
                                                <b-td>
                                                    <b-form-input size="sm" v-model="item.explanation" autocomplete="off" />
                                                </b-td>
                                            </b-tr>
                                        </b-table-simple>
                                    <!-- </div> -->

                                    <div class="mt-8">
                                        <label class="font-weight-bold fs-13">RIWAYAT PEMERIKSAAN YANG PERNAH DILAKUKAN</label>
                                        <b-table-simple bordered small fixed class="text-center">
                                            <b-tr class="fs-13 ">
                                                <b-th class="align-middle" style="width: 40px;">
                                                    <b-button @click="addAdultExamination" variant="primary" size="sm">
                                                        <feather-icon icon="PlusIcon" />
                                                    </b-button>
                                                </b-th>
                                                <b-th class="align-middle" style="width: 40px;">No</b-th>
                                                <b-th class="align-middle text-left">Jenis Pemeriksaan</b-th>
                                                <b-th class="align-middle text-left">Lembar</b-th>
                                                <b-th class="align-middle text-left">Keterangan</b-th>
                                            </b-tr>
                                            <b-tr v-for="(item, index) in form_adult.examination_history" class="fs-12">
                                                <b-td>
                                                    <b-button @click="remAdultExamination(index)" variant="outline-danger-500" size="sm">
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </b-td>
                                                <b-td class="align-middle">{{ index+1 }}</b-td>
                                                <b-td>
                                                    <b-form-input size="sm" v-model="item.type" autocomplete="off" />
                                                </b-td>
                                                <b-td>
                                                    <b-form-input size="sm" v-model="item.page" autocomplete="off" />
                                                </b-td>
                                                <b-td>
                                                    <b-form-input size="sm" v-model="item.explanation" autocomplete="off" />
                                                </b-td>
                                            </b-tr>
                                        </b-table-simple>
                                    </div>
                                </div>
                            </section>

                            <section class="mt-20">
                                <ModernLabel label="Penilaian Per Sistem" />
                                <div class="row mt-16">
                                    <div class="col-lg-3" style="border-right: 1px solid black;">
                                        <label class="text-uppercase font-weight-bold fs-13">Riwayat merokok: </label>
                                        <div >
                                            <v-select v-model="form_adult.evaluation_smoke_type_id" class="select-size-sm" :options="OptionsIsAny" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            <template v-if="form_adult.evaluation_smoke_type_id == 1">
                                                <div>
                                                    <label class="fs-13 mb-0">Jumlah per hari</label>
                                                    <b-input-group append="Batang" size="sm">
                                                        <b-form-input v-model="form_adult.evaluation_smoke_amount" type="number" autocomplete="off" />
                                                    </b-input-group>
                                                </div>
                                                <div>
                                                    <label class="fs-13 mb-0">Lamanya</label>
                                                    <b-input-group size="sm">
                                                        <b-form-input v-model="form_adult.evaluation_smoke_long" type="number" min="0" autocomplete="off" />
                                                        <b-input-group-append>
                                                            <b-form-select v-model="form_adult.evaluation_smoke_unit" :options="['Tahun', 'Bulan']" size="sm" />
                                                        </b-input-group-append>
                                                    </b-input-group>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 mt-8 mt-lg-0" style="border-right: 1px solid black;">
                                        <label class="text-uppercase font-weight-bold fs-13">Riwayat minuman keras: </label>
                                        <div >
                                            <v-select v-model="form_adult.evaluation_alcohol_type_id" class="select-size-sm" :options="OptionsIsAny" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            <template v-if="form_adult.evaluation_alcohol_type_id == 1">
                                                <div>
                                                    <label class="fs-13 mb-0">Jenis</label>
                                                    <b-form-input v-model="form_adult.evaluation_alcohol_type" size="sm" autocomplete="off" />
                                                </div>
                                                <div>
                                                    <label class="fs-13 mb-0">Jumlah per hari</label>
                                                    <b-input-group append="Botol" size="sm">
                                                        <b-form-input v-model="form_adult.evaluation_alcohol_amount" type="number" autocomplete="off" />
                                                    </b-input-group>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="col-lg-3" style="border-right: 1px solid black;">
                                        <label class="text-uppercase font-weight-bold fs-13">Riwayat obat penenang: </label>
                                        <div >
                                            <v-select v-model="form_adult.evaluation_sedative_type_id" class="select-size-sm" :options="OptionsIsAny" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            <template v-if="form_adult.evaluation_sedative_type_id == 1">
                                                <div>
                                                    <label class="fs-13 mb-0">Jenis</label>
                                                    <b-form-input v-model="form_adult.evaluation_sedative_type" size="sm" autocomplete="off" />
                                                </div>
                                                <div>
                                                    <label class="fs-13 mb-0">Jumlah per hari</label>
                                                    <b-input-group size="sm">
                                                        <b-form-input v-model="form_adult.evaluation_sedative_amount" type="number" min="0" autocomplete="off" />
                                                        <b-input-group-append>
                                                            <b-form-select v-model="form_adult.evaluation_sedative_unit" :options="['Tablet', 'Kapsul', 'Butir', 'mg', 'ml']" size="sm" />
                                                        </b-input-group-append>
                                                    </b-input-group>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="text-uppercase font-weight-bold fs-13">Gangguan pola tidur: </label>
                                        <div >
                                            <v-select v-model="form_adult.evaluation_sleep_type_id" class="select-size-sm" :options="OptionsIsAny" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            <template v-if="form_adult.evaluation_sleep_type_id == 1">
                                                <div>
                                                    <label class="fs-13 mb-0">Jenis</label>
                                                    <b-form-input v-model="form_adult.evaluation_sleep" size="sm" autocomplete="off" />
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-16">
                                        <label class="text-uppercase font-weight-bold fs-13 mb-0">Asesmen fungsi: </label>
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <label class="fs-13 mb-0">Kemampuan aktivitas harian</label>
                                                <v-select v-model="form_adult.evaluation_daily_type_id" class="select-size-sm" :options="OptionsEvalDaily" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Aktivitas</label>
                                                <v-select v-model="form_adult.evaluation_activity_type_id" class="select-size-sm" :options="OptionsEvalActivity" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Alat Ambulasi</label>
                                                <v-select v-model="form_adult.evaluation_ambulation_type_id" class="select-size-sm" :options="OptionsEvalAmbulation" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Ekstremitas Atas</label>
                                                <v-select v-model="form_adult.evaluation_extremities_top_type_id" class="select-size-sm" :options="OptionsEvalTopExtremity" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Ekstremitas Bawah</label>
                                                <v-select v-model="form_adult.evaluation_extremities_bottom_type_id" class="select-size-sm" :options="OptionsEvalBotExtremity" :reduce="item => item.value" label="title" placeholder="pilih" />
                                                <div v-if="form_adult.evaluation_extremities_bottom_type_id == 7" class="mt-1">
                                                    <b-form-input v-model="form_adult.evaluation_extremities_bottom_bone" size="sm" placeholder="isikan riwayat" />
                                                </div>
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Kemampuan Menggenggam</label>
                                                <v-select v-model="form_adult.evaluation_grip_type_id" class="select-size-sm" :options="OptionsEvalGrip" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div v-if="form_adult.evaluation_grip_type_id == 3" class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Menggenggam lainnya</label>
                                                <b-form-input v-model="form_adult.evaluation_grip_other" size="sm" placeholder="isikan lainnya" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Kemampuan koordinasi</label>
                                                <v-select v-model="form_adult.evaluation_coordination_type_id" class="select-size-sm" :options="OptionsEvalCoordination" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div v-if="form_adult.evaluation_coordination_type_id == 2" class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Masalah koordinasi</label>
                                                <b-form-input v-model="form_adult.evaluation_coordination_other" size="sm" placeholder="isikan masalah" />
                                            </div>
                                        </div>
                                    </div>

                                    <section class="col-12 mt-16">
                                        <label class="text-uppercase font-weight-bold fs-13 mb-0">Keadaan Mental dan Psikologis: </label>
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <label class="fs-13 mb-0">Status Psikologis</label>
                                                <b-form-checkbox-group v-model="form_adult.psychological_status_arry" stacked size="sm">
                                                    <b-form-checkbox value="a">Cemas</b-form-checkbox>
                                                    <b-form-checkbox value="b">Takut</b-form-checkbox>
                                                    <b-form-checkbox value="c">Marah</b-form-checkbox>
                                                    <b-form-checkbox value="d">Sedih</b-form-checkbox>
                                                    <b-form-checkbox value="e">Cenderung ingin bunuh diri</b-form-checkbox>
                                                    <b-form-checkbox value="f">Lain-lain</b-form-checkbox>
                                                </b-form-checkbox-group>
                                                <template v-if="form_adult.psychological_status_arry.indexOf('f') !== -1">
                                                    <b-form-input v-model="form_adult.psychological_status" size="sm" class="mt-1" placeholder="isikan psikologis lainnya"/>
                                                </template>
                                            </div>
                                            <div class="col-lg-4 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Status Mental</label>
                                                <b-form-checkbox-group v-model="form_adult.mental_status_arry" stacked size="sm">
                                                    <b-form-checkbox value="a" class="my-1">Sadar dan orientasi baik</b-form-checkbox>
                                                    <b-form-checkbox value="b" class="my-1 my-lg-auto">Ada masalah perilaku, sebutkan</b-form-checkbox>
                                                    <template v-if="form_adult.mental_status_arry.indexOf('b') !== -1">
                                                        <b-form-input v-model="form_adult.mental_status_problems" size="sm" class="mt-1" placeholder="isikan masalah perilaku"/>
                                                    </template>
                                                    <b-form-checkbox value="c" class="my-1">Perilaku kekerasan yang dialami pasien sebelumnya</b-form-checkbox>
                                                    <template v-if="form_adult.mental_status_arry.indexOf('c') !== -1">
                                                        <b-form-input v-model="form_adult.mental_status_violence" size="sm" class="mt-1" placeholder="isikan kekerasan yang dialami"/>
                                                    </template>
                                                </b-form-checkbox-group>
                                            </div>
                                        </div>
                                    </section>

                                    <section class="col-12 mt-16">
                                        <label class="text-uppercase font-weight-bold fs-13 mb-0">Eliminasi: </label>
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <label class="fs-13 mb-0">Frekuensi BAB</label>
                                                <b-input-group append="x/hari" size="sm">
                                                    <b-form-input v-model="form_adult.elimination_defecate" type="number" />
                                                </b-input-group>
                                            </div>
                                            <div class="col-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Konsistensi</label>
                                                <v-select v-model="form_adult.elimination_consistency_type_id" class="select-size-sm" :options="OptionsElimConsistency" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                        </div>
                                        <div class="row mt-8">
                                            <div class="col-lg-3">
                                                <label class="fs-13 mb-0">Frekuensi BAK</label>
                                                <b-input-group append="x/hari" size="sm">
                                                    <b-form-input v-model="form_adult.elimination_urination" type="number" />
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Warna</label>
                                                <v-select v-model="form_adult.elimination_color_type_id" class="select-size-sm" :options="OptionsElimColor" :reduce="item => item.value" label="title" placeholder="pilih" />
                                                <template v-if="form_adult.elimination_color_type_id == '3'">
                                                    <b-form-input v-model="form_adult.elimination_color" class="mt-1" size="sm" placeholder="isikan warna" />
                                                </template>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </section>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <b-button-group size="sm">
                                <b-button @click="changeTab('FUNCTIONAL')" :disabled="active.disabled.section" variant="outline-neutral-700">
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button @click="changeSection(active.section, 'ADULT')" :disabled="active.disabled.section" variant="orange-500">
                                    <template>
                                        <feather-icon icon="SaveIcon" />
                                        Simpan
                                    </template>
                                </b-button>
                            </b-button-group>
                        </div>
                    </section>

                    <section v-if="active.section == 'CHILD'" class="card">
                        <div class="card-header bg-primary text-white fs-14 font-weight-bold">ANAK</div>
                        <div class="card-body px-20 py-8 position-sticky" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <section class="">
                                <ModernLabel label="Asesmen Keperawatan - Anamnesa" />
                                <div class="mt-8">
                                    <section>
                                        <label class="font-weight-bold fs-13">ALAT KESEHATAN YANG TERPASANG </label>
                                        <!-- <div class="row">
                                            <div class="col-lg-3">
                                                <v-select v-model="form_child.medical_devices_type_id" class="select-size-sm" :options="OptionsMedicalDevices" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                        </div> -->
                                        <!-- <div v-if="form_child.medical_devices_type_id == 1" class="mt-10"> -->
                                            <b-table-simple bordered small fixed class="text-center">
                                                <b-tr class="fs-13 ">
                                                    <b-th class="align-middle" style="width: 40px;">
                                                        <b-button @click="addChildMedical" variant="primary" size="sm">
                                                            <feather-icon icon="PlusIcon" />
                                                        </b-button>
                                                    </b-th>
                                                    <b-th class="align-middle" style="width: 40px;">No</b-th>
                                                    <b-th class="align-middle text-left">Jenis</b-th>
                                                    <b-th class="align-middle text-left">Keterangan</b-th>
                                                </b-tr>
                                                <b-tr v-for="(item, index) in form_child.medical_devices" class="fs-12">
                                                    <b-td>
                                                        <b-button @click="remChildMedical(index)" variant="outline-danger-500" size="sm">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </b-td>
                                                    <b-td class="align-middle">{{ index+1 }}</b-td>
                                                    <b-td>
                                                        <b-form-input size="sm" v-model="item.type" autocomplete="off" />
                                                    </b-td>
                                                    <b-td>
                                                        <b-form-input size="sm" v-model="item.explanation" autocomplete="off" />
                                                    </b-td>
                                                </b-tr>
                                            </b-table-simple>
                                        <!-- </div> -->
                                    </section>

                                    <section class="mt-16">
                                        <label class="text-uppercase font-weight-bold fs-13">RIWAYAT PEMERIKSAAN YANG PERNAH DILAKUKAN</label>
                                        <b-table-simple bordered small fixed class="text-center">
                                            <b-tr class="fs-13 ">
                                                <b-th class="align-middle" style="width: 40px;">
                                                    <b-button @click="addChildExamination" variant="primary" size="sm">
                                                        <feather-icon icon="PlusIcon" />
                                                    </b-button>
                                                </b-th>
                                                <b-th class="align-middle" style="width: 40px;">No</b-th>
                                                <b-th class="align-middle text-left">Jenis Pemeriksaan</b-th>
                                                <b-th class="align-middle text-left">Lembar</b-th>
                                                <b-th class="align-middle text-left">Keterangan</b-th>
                                            </b-tr>
                                            <b-tr v-for="(item, index) in form_child.examination_history" class="fs-12">
                                                <b-td>
                                                    <b-button @click="remChildExamination(index)" variant="outline-danger-500" size="sm">
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </b-td>
                                                <b-td class="align-middle">{{ index+1 }}</b-td>
                                                <b-td>
                                                    <b-form-input size="sm" v-model="item.type" autocomplete="off" />
                                                </b-td>
                                                <b-td>
                                                    <b-form-input size="sm" v-model="item.page" autocomplete="off" />
                                                </b-td>
                                                <b-td>
                                                    <b-form-input size="sm" v-model="item.explanation" autocomplete="off" />
                                                </b-td>
                                            </b-tr>
                                        </b-table-simple>
                                    </section>

                                    <section class="mt-16">
                                        <label class="font-weight-bold fs-13">RIWAYAT TUMBUH KEMBANG</label>
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <label class="fs-13 mb-0">BB anak saat lahir</label>
                                                <b-input-group size="sm" append="gr">
                                                    <b-form-input type="number" autocomplete="off" v-model="form_child.growth_birth_weight" />
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">TB anak saat lahir</label>
                                                <b-input-group size="sm" append="cm">
                                                    <b-form-input type="number" autocomplete="off" v-model="form_child.growth_birth_height" />
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">ASI, sampai umur</label>
                                                <b-input-group size="sm">
                                                    <b-form-input v-model="form_child.growth_breast_milk_age" type="number" min="0" autocomplete="off" />
                                                    <b-input-group-append>
                                                        <b-form-select v-model="form_child.growth_breast_milk_unit" :options="['Tahun', 'Bulan']" size="sm" />
                                                    </b-input-group-append>
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Susu Formula, dimulai umur</label>
                                                <b-input-group size="sm">
                                                    <b-form-input v-model="form_child.growth_formula_milk_age" type="number" min="0" autocomplete="off" />
                                                    <b-input-group-append>
                                                        <b-form-select v-model="form_child.growth_formula_milk_unit" :options="['Tahun', 'Bulan']" size="sm" />
                                                    </b-input-group-append>
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Makanan padat, dimulai umur</label>
                                                <b-input-group size="sm">
                                                    <b-form-input v-model="form_child.growth_solid_food_age" type="number" min="0" autocomplete="off" />
                                                    <b-input-group-append>
                                                        <b-form-select v-model="form_child.growth_solid_food_unit" :options="['Tahun', 'Bulan']" size="sm" />
                                                    </b-input-group-append>
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Makanan tambahan, dimulai umur</label>
                                                <b-input-group size="sm">
                                                    <b-form-input v-model="form_child.growth_additional_food_age" type="number" min="0" autocomplete="off" />
                                                    <b-input-group-append>
                                                        <b-form-select v-model="form_child.growth_additional_food_unit" :options="['Tahun', 'Bulan']" size="sm" />
                                                    </b-input-group-append>
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Tengkurap</label>
                                                <b-input-group size="sm">
                                                    <b-form-input v-model="form_child.growth_prone_age" type="number" min="0" autocomplete="off" />
                                                    <b-input-group-append>
                                                        <b-form-select v-model="form_child.growth_prone_unit" :options="['Tahun', 'Bulan']" size="sm" />
                                                    </b-input-group-append>
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Duduk</label>
                                                <b-input-group size="sm">
                                                    <b-form-input v-model="form_child.growth_sit_age" type="number" min="0" autocomplete="off" />
                                                    <b-input-group-append>
                                                        <b-form-select v-model="form_child.growth_sit_unit" :options="['Tahun', 'Bulan']" size="sm" />
                                                    </b-input-group-append>
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Merangkak</label>
                                                <b-input-group size="sm">
                                                    <b-form-input v-model="form_child.growth_crawl_age" type="number" min="0" autocomplete="off" />
                                                    <b-input-group-append>
                                                        <b-form-select v-model="form_child.growth_crawl_unit" :options="['Tahun', 'Bulan']" size="sm" />
                                                    </b-input-group-append>
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Berdiri</label>
                                                <b-input-group size="sm">
                                                    <b-form-input v-model="form_child.growth_stand_age" type="number" min="0" autocomplete="off" />
                                                    <b-input-group-append>
                                                        <b-form-select v-model="form_child.growth_stand_unit" :options="['Tahun', 'Bulan']" size="sm" />
                                                    </b-input-group-append>
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Berjalan</label>
                                                <b-input-group size="sm">
                                                    <b-form-input v-model="form_child.growth_walk_age" type="number" min="0" autocomplete="off" />
                                                    <b-input-group-append>
                                                        <b-form-select v-model="form_child.growth_walk_unit" :options="['Tahun', 'Bulan']" size="sm" />
                                                    </b-input-group-append>
                                                </b-input-group>
                                            </div>
                                        </div>
                                    </section>

                                    <section class="mt-16">
                                        <label class="font-weight-bold fs-13">RIWAYAT IMUNISASI</label>
                                        <div class="row">
                                            <div v-for="(column, m) in arry_immuni" :key="m" class="col-lg-6">
                                                <b-table-simple small>
                                                    <b-thead class="fs-14 bg-primary text-white">
                                                        <b-tr>
                                                            <b-th>IMUNISASI</b-th>
                                                            <b-th v-for="n in 6" :key="n" class="text-center">{{ n }}</b-th>
                                                        </b-tr>
                                                    </b-thead>
                                                    <b-tbody class="fs-13">
                                                        <b-tr v-for="(item, i) in column.side" :key="i">
                                                            <b-td>{{ item.name }}</b-td>
                                                            <b-td v-for="(val, j) in arry_values" :key="j" class="text-center" :class="{'bg-warning-100': j <= item.mark}">
                                                                <b-form-checkbox :value="val" v-model="form_child[item.model]" />
                                                            </b-td>
                                                        </b-tr>
                                                    </b-tbody>
                                                </b-table-simple>
                                            </div>
                                        </div>
                                    </section>

                                    <section class="mt-16">
                                        <label class="font-weight-bold fs-13">KEBUTUHAN KOMUNIKASI & EDUKASI</label>
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div>
                                                    <label class="fs-13 mb-0">Bicara</label>
                                                    <v-select v-model="form_child.comm_needs_speech_type_id" :options="OptionsTalk" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                                </div>
                                                <template v-if="form_child.comm_needs_speech_type_id == 2">
                                                    <div class="mt-8">
                                                        <label class="fs-13 mb-0">Gangguan bicara sejak</label>
                                                        <b-form-input size="sm" v-model="form_child.comm_needs_speech_disorder" autocomplete="off" />
                                                    </div>
                                                </template>
                                            </div>
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Bahasa sehari-hari</label>
                                                <b-form-checkbox-group v-model="form_child.comm_needs_colloquial_arry" stacked size="sm">
                                                    <b-form-checkbox value="a" class="my-1">Indonesia, aktif / pasif</b-form-checkbox>
                                                    <b-form-checkbox value="b" class="my-1 my-lg-auto">Inggris, aktif / pasif</b-form-checkbox>
                                                    <b-form-checkbox value="c" class="my-1 my-lg-auto">Daerah, sebutkan</b-form-checkbox>
                                                    <template v-if="form_child.comm_needs_colloquial_arry.indexOf('c') !== -1">
                                                        <b-form-input v-model="form_child.comm_needs_colloquial_local" size="sm" class="mt-1" placeholder="sebutkan bahasa daerah"/>
                                                    </template>
                                                    <b-form-checkbox value="d" class="my-1">Lain-lain, sebutkan</b-form-checkbox>
                                                    <template v-if="form_child.comm_needs_colloquial_arry.indexOf('d') !== -1">
                                                        <b-form-input v-model="form_child.comm_needs_colloquial_other" size="sm" class="mt-1" placeholder="sebutkan bahasa lainnya"/>
                                                    </template>
                                                </b-form-checkbox-group>
                                            </div>
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Perlu penerjemah</label>
                                                <v-select v-model="form_child.comm_needs_translator_type_id" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                            </div>
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <div>
                                                    <label class="fs-13 mb-0">Tingkat pendidikan</label>
                                                    <v-select v-model="form_child.edu_needs_level_of_education_type_id" :options="OptionsEducationLevel" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                                </div>
                                                <template v-if="form_child.edu_needs_level_of_education_type_id == 5">
                                                    <div class="mt-8">
                                                        <label class="fs-13 mb-0">Pendidikan lainnya</label>
                                                        <b-form-input size="sm" v-model="form_child.edu_needs_level_of_education_other" autocomplete="off" />
                                                    </div>
                                                </template>
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <div>
                                                    <label class="fs-13 mb-0">Terdapat hambatan belajar</label>
                                                    <v-select v-model="form_child.edu_needs_barriers_type_id" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                                </div>
                                            </div>
                                            <template v-if="form_child.edu_needs_barriers_type_id == 1">
                                                <div class="col-lg-3 mt-8">
                                                    <label class="fs-13 mb-0">Hambatan belajar</label>
                                                    <b-form-checkbox-group v-model="form_child.edu_needs_barriers_arry" stacked size="sm">
                                                        <b-form-checkbox value="a" class="my-1">Bahasa</b-form-checkbox>
                                                        <b-form-checkbox value="b" class="my-1 my-lg-auto">Cemas</b-form-checkbox>
                                                        <b-form-checkbox value="c" class="my-1 my-lg-auto">Kognitif</b-form-checkbox>
                                                        <b-form-checkbox value="d" class="my-1 my-lg-auto">Pendengaran</b-form-checkbox>
                                                        <b-form-checkbox value="e" class="my-1 my-lg-auto">Emosi</b-form-checkbox>
                                                        <b-form-checkbox value="f" class="my-1 my-lg-auto">Hilang memori</b-form-checkbox>
                                                        <b-form-checkbox value="g" class="my-1 my-lg-auto">Motivasi buruk</b-form-checkbox>
                                                        <b-form-checkbox value="h" class="my-1 my-lg-auto">Masalah penglihatan</b-form-checkbox>
                                                        <b-form-checkbox value="i" class="my-1 my-lg-auto">Kesulitan bicara</b-form-checkbox>
                                                        <b-form-checkbox value="j" class="my-1 my-lg-auto">Secara fisiologis tidak mampu belajar</b-form-checkbox>
                                                        <b-form-checkbox value="k" class="my-1">Lain-lain</b-form-checkbox>
                                                        <template v-if="form_child.edu_needs_barriers_arry.indexOf('k') !== -1">
                                                            <b-form-input v-model="form_child.edu_needs_barriers_other" size="sm" class="mt-1" placeholder="sebutkan lainnya"/>
                                                        </template>
                                                    </b-form-checkbox-group>
                                                </div>
                                            </template>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Cara belajar yang disukai</label>
                                                <b-form-checkbox-group v-model="form_child.edu_needs_preferred_arry" stacked size="sm">
                                                    <b-form-checkbox value="a" class="my-1">Menulis</b-form-checkbox>
                                                    <b-form-checkbox value="b" class="my-1 my-lg-auto">Demintrasi</b-form-checkbox>
                                                    <b-form-checkbox value="c" class="my-1 my-lg-auto">Mendengar</b-form-checkbox>
                                                    <b-form-checkbox value="d" class="my-1 my-lg-auto">Membaca</b-form-checkbox>
                                                    <b-form-checkbox value="e" class="my-1 my-lg-auto">Diskusi</b-form-checkbox>
                                                    <b-form-checkbox value="f" class="my-1 my-lg-auto">Audio visual / gambar</b-form-checkbox>
                                                </b-form-checkbox-group>
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Pasien / keluarga menginginkan informasi tentang</label>
                                                <b-form-checkbox-group v-model="form_child.edu_needs_information_arry" stacked size="sm">
                                                    <b-form-checkbox value="a" class="my-1">Proses penyakit</b-form-checkbox>
                                                    <b-form-checkbox value="b" class="my-1 my-lg-auto">Terapi atau obat</b-form-checkbox>
                                                    <b-form-checkbox value="c" class="my-1">Lain-lain</b-form-checkbox>
                                                    <template v-if="form_child.edu_needs_information_arry.indexOf('c') !== -1">
                                                        <b-form-input v-model="form_child.edu_needs_information_other" size="sm" class="mt-1" placeholder="sebutkan lainnya"/>
                                                    </template>
                                                </b-form-checkbox-group>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </section>

                            <section class="mt-20">
                                <ModernLabel label="Penilaian Per Sistem" />
                                <div class="row mt-16">
                                    <div class="col-lg-3" style="border-right: 1px solid black;">
                                        <label class="text-uppercase font-weight-bold fs-13">Riwayat merokok: </label>
                                        <div >
                                            <v-select v-model="form_child.evaluation_smoke_type_id" class="select-size-sm" :options="OptionsIsAny" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            <template v-if="form_child.evaluation_smoke_type_id == 1">
                                                <div>
                                                    <label class="fs-13 mb-0">Jumlah per hari</label>
                                                    <b-input-group append="Batang" size="sm">
                                                        <b-form-input v-model="form_child.evaluation_smoke_amount" type="number" autocomplete="off" />
                                                    </b-input-group>
                                                </div>
                                                <div>
                                                    <label class="fs-13 mb-0">Lamanya</label>
                                                    <b-input-group size="sm">
                                                        <b-form-input v-model="form_child.evaluation_smoke_long" type="number" min="0" autocomplete="off" />
                                                        <b-input-group-append>
                                                            <b-form-select v-model="form_child.evaluation_smoke_unit" :options="['Tahun', 'Bulan']" size="sm" />
                                                        </b-input-group-append>
                                                    </b-input-group>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 mt-8 mt-lg-0" style="border-right: 1px solid black;">
                                        <label class="text-uppercase font-weight-bold fs-13">Riwayat minuman keras: </label>
                                        <div >
                                            <v-select v-model="form_child.evaluation_alcohol_type_id" class="select-size-sm" :options="OptionsIsAny" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            <template v-if="form_child.evaluation_alcohol_type_id == 1">
                                                <div>
                                                    <label class="fs-13 mb-0">Jenis</label>
                                                    <b-form-input v-model="form_child.evaluation_alcohol_type" size="sm" autocomplete="off" />
                                                </div>
                                                <div>
                                                    <label class="fs-13 mb-0">Jumlah per hari</label>
                                                    <b-input-group append="Botol" size="sm">
                                                        <b-form-input v-model="form_child.evaluation_alcohol_amount" type="number" autocomplete="off" />
                                                    </b-input-group>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="col-lg-3" style="border-right: 1px solid black;">
                                        <label class="text-uppercase font-weight-bold fs-13">Riwayat obat penenang: </label>
                                        <div >
                                            <v-select v-model="form_child.evaluation_sedative_type_id" class="select-size-sm" :options="OptionsIsAny" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            <template v-if="form_child.evaluation_sedative_type_id == 1">
                                                <div>
                                                    <label class="fs-13 mb-0">Jenis</label>
                                                    <b-form-input v-model="form_child.evaluation_sedative_type" size="sm" autocomplete="off" />
                                                </div>
                                                <div>
                                                    <label class="fs-13 mb-0">Jumlah per hari</label>
                                                    <b-input-group size="sm">
                                                        <b-form-input v-model="form_child.evaluation_sedative_amount" type="number" min="0" autocomplete="off" />
                                                        <b-input-group-append>
                                                            <b-form-select v-model="form_child.evaluation_sedative_unit" :options="['Tablet', 'Kapsul', 'Butir', 'mg', 'ml']" size="sm" />
                                                        </b-input-group-append>
                                                    </b-input-group>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="text-uppercase font-weight-bold fs-13">Gangguan pola tidur: </label>
                                        <div >
                                            <v-select v-model="form_child.evaluation_sleep_type_id" class="select-size-sm" :options="OptionsIsAny" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            <template v-if="form_child.evaluation_sleep_type_id == 1">
                                                <div>
                                                    <label class="fs-13 mb-0">Jenis</label>
                                                    <b-form-input v-model="form_child.evaluation_sleep" size="sm" autocomplete="off" />
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-16">
                                        <label class="text-uppercase font-weight-bold fs-13 mb-0">Asesmen fungsi: </label>
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <label class="fs-13 mb-0">Kemampuan aktivitas harian</label>
                                                <v-select v-model="form_child.evaluation_daily_type_id" class="select-size-sm" :options="OptionsEvalDaily" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Aktivitas</label>
                                                <v-select v-model="form_child.evaluation_activity_type_id" class="select-size-sm" :options="OptionsEvalActivity" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Alat Ambulasi</label>
                                                <v-select v-model="form_child.evaluation_ambulation_type_id" class="select-size-sm" :options="OptionsEvalAmbulation" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Ekstremitas Atas</label>
                                                <v-select v-model="form_child.evaluation_extremities_top_type_id" class="select-size-sm" :options="OptionsEvalTopExtremity" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Ekstremitas Bawah</label>
                                                <v-select v-model="form_child.evaluation_extremities_bottom_type_id" class="select-size-sm" :options="OptionsEvalBotExtremity" :reduce="item => item.value" label="title" placeholder="pilih" />
                                                <div v-if="form_child.evaluation_extremities_bottom_type_id == 7" class="mt-1">
                                                    <b-form-input v-model="form_child.evaluation_extremities_bottom_bone" size="sm" placeholder="isikan riwayat" />
                                                </div>
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Kemampuan Menggenggam</label>
                                                <v-select v-model="form_child.evaluation_grip_type_id" class="select-size-sm" :options="OptionsEvalGrip" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div v-if="form_child.evaluation_grip_type_id == 3" class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Menggenggam lainnya</label>
                                                <b-form-input v-model="form_child.evaluation_grip_other" size="sm" placeholder="isikan lainnya" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Kemampuan koordinasi</label>
                                                <v-select v-model="form_child.evaluation_coordination_type_id" class="select-size-sm" :options="OptionsEvalCoordination" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div v-if="form_child.evaluation_coordination_type_id == 2" class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Masalah koordinasi</label>
                                                <b-form-input v-model="form_child.evaluation_coordination_other" size="sm" placeholder="isikan masalah" />
                                            </div>
                                        </div>
                                    </div>

                                    <section class="col-12 mt-16">
                                        <label class="text-uppercase font-weight-bold fs-13 mb-0">Keadaan Mental dan Psikologis: </label>
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <label class="fs-13 mb-0">Status Mental</label>
                                                <v-select v-model="form_child.evaluation_mental_status_type_id" :options="OptionsChildMental" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                            </div>
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Status Psikologis</label>
                                                <v-select v-model="form_child.evaluation_psychological_status_type_id" :options="OptionsChildPsychological" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                            </div>
                                            <template v-if="form_child.evaluation_psychological_status_type_id == 7">
                                                <div class="col-lg-3 mt-8 mt-lg-0">
                                                    <label class="fs-13 mb-0">Status Psikologis Lain</label>
                                                    <b-form-input v-model="form_child.evaluation_psychological_status_other" size="sm" />
                                                </div>
                                            </template>
                                        </div>
                                    </section>

                                    <section class="col-12 mt-16">
                                        <label class="text-uppercase font-weight-bold fs-13 mb-0">Eliminasi: </label>
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <label class="fs-13 mb-0">BAK</label>
                                                <v-select v-model="form_child.evaluation_elimination_urination_type_id" :options="OptionsChildBak" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                            </div>
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">BAB</label>
                                                <v-select v-model="form_child.evaluation_elimination_defecate_type_id" :options="OptionsChildBab" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                            </div>
                                            <template v-if="form_child.evaluation_elimination_defecate_type_id == 7">
                                                <div class="col-lg-3 mt-8 mt-lg-0">
                                                    <label class="fs-13 mb-0">Frekuensi Diare</label>
                                                    <b-input-group size="sm" append="x/hari">
                                                        <b-form-input v-model="form_child.evaluation_elimination_defecate_freq" type="number" />
                                                    </b-input-group>
                                                </div>
                                            </template>
                                        </div>
                                    </section>

                                    <section class="col-12 mt-16 mb-64">
                                        <label class="text-uppercase font-weight-bold fs-13 mb-0">Seksual: </label>
                                        <div class="row">
                                            <template v-if="regData.sex == 0">
                                                <div class="col-lg-3">
                                                    <label class="fs-13 mb-0">Haid</label>
                                                    <v-select v-model="form_child.evaluation_sexual_menstruation_type_id" :options="OptionsIsDone" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                                </div>
                                            </template>
                                            <template v-if="regData.sex == 1">
                                                <div class="col-lg-3">
                                                    <label class="fs-13 mb-0">Sirkumsisi</label>
                                                    <v-select v-model="form_child.evaluation_sexual_circumcision_type_id" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                                </div>
                                                <template v-if="form_child.evaluation_sexual_circumcision_type_id == 1">
                                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                                        <label class="fs-13 mb-0">Isikan Sirkumsisi</label>
                                                        <b-form-input v-model="form_child.evaluation_sexual_circumcision" size="sm" />
                                                    </div>
                                                </template>
                                                <div class="col-lg-3 mt-8 mt-lg-0">
                                                    <label class="fs-13 mb-0">Terdapat Masalah Lain</label>
                                                    <v-select v-model="form_child.evaluation_sexual_problem_type_id" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                                </div>
                                                <template v-if="form_child.evaluation_sexual_problem_type_id == 1">
                                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                                        <label class="fs-13 mb-0">Isikan Masalah</label>
                                                        <b-form-input v-model="form_child.evaluation_sexual_problem" size="sm" />
                                                    </div>
                                                </template>
                                            </template>
                                        </div>
                                    </section>
                                </div>
                            </section>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <b-button-group size="sm">
                                <b-button @click="changeTab('FUNCTIONAL')" :disabled="active.disabled.section" variant="outline-neutral-700">
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button @click="changeSection(active.section, 'CHILD')" :disabled="active.disabled.section" variant="orange-500">
                                    <template>
                                        <feather-icon icon="SaveIcon" />
                                        Simpan
                                    </template>
                                </b-button>
                            </b-button-group>
                        </div>
                    </section>

                    <section v-if="active.section == 'NEONATE'" class="card">
                        <div class="card-header bg-primary text-white fs-14 font-weight-bold">NEONATUS</div>
                        <div class="card-body px-20 py-8 position-sticky" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <section>
                                <ModernLabel label="Asesmen Keperawatan - Anamnesa" />
                                <section class="mt-8">
                                    <label class="font-weight-bold fs-13 mb-0">ALAT KESEHATAN YANG TERPASANG</label>
                                    <b-table-simple bordered small fixed class="text-center">
                                        <b-tr class="fs-13 ">
                                            <b-th class="align-middle" style="width: 40px;">
                                                <b-button @click="addNeoMedical" variant="primary" size="sm">
                                                    <feather-icon icon="PlusIcon" />
                                                </b-button>
                                            </b-th>
                                            <b-th class="align-middle" style="width: 40px;">No</b-th>
                                            <b-th class="align-middle text-left">Jenis</b-th>
                                            <b-th class="align-middle text-left">Keterangan</b-th>
                                        </b-tr>
                                        <b-tr v-for="(item, index) in form_neona.medical_devices" class="fs-12">
                                            <b-td>
                                                <b-button @click="remNeoMedical(index)" variant="outline-danger-500" size="sm">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </b-td>
                                            <b-td class="align-middle">{{ index+1 }}</b-td>
                                            <b-td>
                                                <b-form-input size="sm" v-model="item.type" autocomplete="off" />
                                            </b-td>
                                            <b-td>
                                                <b-form-input size="sm" v-model="item.explanation" autocomplete="off" />
                                            </b-td>
                                        </b-tr>
                                    </b-table-simple>
                                </section>

                                <section class="mt-16">
                                    <label class="text-uppercase font-weight-bold fs-13 mb-0">RIWAYAT PEMERIKSAAN YANG PERNAH DILAKUKAN</label>
                                    <b-table-simple bordered small fixed class="text-center">
                                        <b-tr class="fs-13 ">
                                            <b-th class="align-middle" style="width: 40px;">
                                                <b-button @click="addNeoExamination" variant="primary" size="sm">
                                                    <feather-icon icon="PlusIcon" />
                                                </b-button>
                                            </b-th>
                                            <b-th class="align-middle" style="width: 40px;">No</b-th>
                                            <b-th class="align-middle text-left">Jenis Pemeriksaan</b-th>
                                            <b-th class="align-middle text-left">Lembar</b-th>
                                            <b-th class="align-middle text-left">Keterangan</b-th>
                                        </b-tr>
                                        <b-tr v-for="(item, index) in form_neona.examination_history" class="fs-12">
                                            <b-td>
                                                <b-button @click="remNeoExamination(index)" variant="outline-danger-500" size="sm">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </b-td>
                                            <b-td class="align-middle">{{ index+1 }}</b-td>
                                            <b-td>
                                                <b-form-input size="sm" v-model="item.type" autocomplete="off" />
                                            </b-td>
                                            <b-td>
                                                <b-form-input size="sm" v-model="item.page" autocomplete="off" />
                                            </b-td>
                                            <b-td>
                                                <b-form-input size="sm" v-model="item.explanation" autocomplete="off" />
                                            </b-td>
                                        </b-tr>
                                    </b-table-simple>
                                </section>

                                <section class="mt-16">
                                    <label class="font-weight-bold fs-13 mb-0">RIWAYAT KESEHATAN DAHULU PASIEN & IBU</label>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label class="fs-13 mb-0">Usia gestasi</label>
                                            <b-form-input v-model="form_neona.gestational_age" size="sm"/>
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Usia koreksi</label>
                                            <b-form-input v-model="form_neona.corrected_age" size="sm"/>
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Jenis persalinan</label>
                                            <v-select class="select-size-sm" v-model="form_neona.type_of_delivery" :options="optLaborType" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Golongan darah ibu</label>
                                            <v-select class="select-size-sm" v-model="form_neona.mother_blood_type_id" :options="optMotherBlood" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-12 mt-8">
                                            <label class="fs-13 mb-0">Diagnosa partus</label>
                                            <textarea v-model.lazy="form_neona.labor_diagnosis" class="form-control form-control-sm" autocomplete="off" />
                                        </div>
                                        <div class="col-12 mt-8">
                                            <label class="fs-13 mb-0">Riwayat penyakit ibu selama hamil</label>
                                            <textarea v-model.lazy="form_neona.maternal_medical_history" class="form-control form-control-sm" autocomplete="off" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Hasil laboratorium ibu</label>
                                            <b-form-checkbox-group v-model="form_neona.mother_lab_result_arry" stacked size="sm">
                                                <b-form-checkbox value="a" class="my-1">HB</b-form-checkbox>
                                                <b-form-checkbox value="b" class="my-1 my-lg-auto">HbsAg</b-form-checkbox>
                                                <b-form-checkbox value="c" class="my-1 my-lg-auto">Leukosit</b-form-checkbox>
                                                <b-form-checkbox value="d" class="my-1">Lainnya</b-form-checkbox>
                                                <template v-if="form_neona.mother_lab_result_arry.indexOf('d') !== -1">
                                                    <b-form-input v-model="form_neona.mother_lab_result" size="sm" class="mt-1" placeholder="sebutkan lainnya"/>
                                                </template>
                                            </b-form-checkbox-group>
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Jenis kelamin bayi</label>
                                            <v-select class="select-size-sm" v-model="form_neona.baby_gender" :options="OptionGender" :reduce="item => item.value" label="text" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <div>
                                                <label class="fs-13 mb-0">Riwayat kelahiran</label>
                                                <v-select class="select-size-sm" v-model="form_neona.birth_history_type_id" :options="optBirthHistory" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div v-if="form_neona.birth_history_type_id == 3" class="mt-1">
                                                <b-form-input v-model="form_neona.birth_history_lk" placeholder="Isikan LK" size="sm"/>
                                            </div>
                                            <div v-if="form_neona.birth_history_type_id == 4" class="mt-1">
                                                <b-form-input v-model="form_neona.birth_history_ld" placeholder="Isikan LD" size="sm"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Pemberian obat setelah lahir</label>
                                            <b-form-checkbox-group v-model="form_neona.postnatal_drug_arry" stacked size="sm">
                                                <b-form-checkbox value="a" class="my-1">Vit K</b-form-checkbox>
                                                <template v-if="form_neona.postnatal_drug_arry.indexOf('a') !== -1">
                                                    <b-form-input v-model="form_neona.postnatal_drug_vit_k" size="sm" class="mt-1" placeholder="sebutkan vit k"/>
                                                </template>
                                                <b-form-checkbox value="b" class="my-1 my-lg-auto">Salep mata</b-form-checkbox>
                                                <template v-if="form_neona.postnatal_drug_arry.indexOf('b') !== -1">
                                                    <b-form-input v-model="form_neona.postnatal_drug_eye" size="sm" class="mt-1" placeholder="sebutkan salep mata"/>
                                                </template>
                                                <b-form-checkbox value="c" class="my-1">Lainnya</b-form-checkbox>
                                                <template v-if="form_neona.postnatal_drug_arry.indexOf('c') !== -1">
                                                    <b-form-input v-model="form_neona.postnatal_drug_other" size="sm" class="mt-1" placeholder="sebutkan lainnya"/>
                                                </template>
                                            </b-form-checkbox-group>
                                        </div>
                                    </div>
                                </section>

                                <section class="mt-16">
                                    <label class="font-weight-bold fs-13 mb-0">KEBUTUHAN EDUKASI ORANG TUA</label>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div>
                                                <label class="fs-13 mb-0">Bicara</label>
                                                <v-select v-model="form_neona.comm_needs_speech_type_id" :options="OptionsTalk" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                            </div>
                                            <template v-if="form_neona.comm_needs_speech_type_id == 2">
                                                <div class="mt-8">
                                                    <label class="fs-13 mb-0">Gangguan bicara sejak</label>
                                                    <b-form-input size="sm" v-model="form_neona.comm_needs_speech_disorder" autocomplete="off" />
                                                </div>
                                            </template>
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Bahasa sehari-hari</label>
                                            <b-form-checkbox-group v-model="form_neona.comm_needs_colloquial_arry" stacked size="sm">
                                                <b-form-checkbox value="a" class="my-1">Indonesia, aktif / pasif</b-form-checkbox>
                                                <b-form-checkbox value="b" class="my-1 my-lg-auto">Inggris, aktif / pasif</b-form-checkbox>
                                                <b-form-checkbox value="c" class="my-1 my-lg-auto">Daerah, sebutkan</b-form-checkbox>
                                                <template v-if="form_neona.comm_needs_colloquial_arry.indexOf('c') !== -1">
                                                    <b-form-input v-model="form_neona.comm_needs_colloquial_local" size="sm" class="mt-1" placeholder="sebutkan bahasa daerah"/>
                                                </template>
                                                <b-form-checkbox value="d" class="my-1">Lain-lain, sebutkan</b-form-checkbox>
                                                <template v-if="form_neona.comm_needs_colloquial_arry.indexOf('d') !== -1">
                                                    <b-form-input v-model="form_neona.comm_needs_colloquial_other" size="sm" class="mt-1" placeholder="sebutkan bahasa lainnya"/>
                                                </template>
                                            </b-form-checkbox-group>
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Pasien / keluarga menginginkan informasi tentang</label>
                                            <b-form-checkbox-group v-model="form_neona.edu_needs_information_arry" stacked size="sm">
                                                <b-form-checkbox value="a" class="my-1">Proses penyakit</b-form-checkbox>
                                                <b-form-checkbox value="b" class="my-1 my-lg-auto">Terapi atau obat</b-form-checkbox>
                                                <b-form-checkbox value="c" class="my-1">Lain-lain</b-form-checkbox>
                                                <template v-if="form_neona.edu_needs_information_arry.indexOf('c') !== -1">
                                                    <b-form-input v-model="form_neona.edu_needs_information_other" size="sm" class="mt-1" placeholder="sebutkan lainnya"/>
                                                </template>
                                            </b-form-checkbox-group>
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <div>
                                                <label class="fs-13 mb-0">Tingkat pendidikan</label>
                                                <v-select v-model="form_neona.edu_needs_level_of_education_type_id" :options="OptionsEducationLevel" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                            </div>
                                            <template v-if="form_neona.edu_needs_level_of_education_type_id == 5">
                                                <div class="mt-8">
                                                    <label class="fs-13 mb-0">Pendidikan lainnya</label>
                                                    <b-form-input size="sm" v-model="form_neona.edu_needs_level_of_education_other" autocomplete="off" />
                                                </div>
                                            </template>
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Perlu penerjemah</label>
                                            <v-select v-model="form_neona.comm_needs_translator_type_id" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                        </div>
                                    </div>
                                </section>

                                <section class="mt-16">
                                    <label class="font-weight-bold fs-13 mb-0">SOSIAL BUDAYA</label>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label class="fs-13 mb-0">Suku bangsa</label>
                                            <b-form-input v-model="form_neona.socio_cultural_ethnic" size="sm"/>
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Warga negara</label>
                                            <b-form-input v-model="form_neona.socio_cultural_citizen" size="sm"/>
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Tempat tinggal</label>
                                            <v-select class="select-size-sm" v-model="form_neona.socio_cultural_residence_type_id" :options="optResidence" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Yang merawat pasien</label>
                                            <v-select class="select-size-sm" v-model="form_neona.socio_cultural_who_cares_type_id" :options="optCaringClient" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Orang tua</label>
                                            <b-form-input v-model="form_neona.socio_cultural_parent" size="sm"/>
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Berkunjung</label>
                                            <v-select class="select-size-sm" v-model="form_neona.socio_cultural_visit_type_id" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Menyentuh</label>
                                            <v-select class="select-size-sm" v-model="form_neona.socio_cultural_touch_type_id" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Menggendong</label>
                                            <v-select class="select-size-sm" v-model="form_neona.socio_cultural_carry_type_id" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Kontak mata</label>
                                            <v-select class="select-size-sm" v-model="form_neona.socio_cultural_eye_contact_type_id" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Berbicara</label>
                                            <v-select class="select-size-sm" v-model="form_neona.socio_cultural_speak_type_id" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Lain-lain</label>
                                            <b-form-input v-model="form_neona.socio_cultural_other" size="sm"/>
                                        </div>
                                    </div>
                                </section>
                            </section>

                            <section class="mt-20">
                                <ModernLabel label="Penilaian Per Sistem" />
                                <div>
                                    <label class="font-weight-bold fs-13 mb-0">RASA NYAMAN: </label>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label class="fs-13 mb-0">Pasien saat disentuh / tidak disentuh</label>
                                            <b-form-checkbox-group v-model="form_neona.evaluation_comfort_arry" stacked size="sm">
                                                <b-form-checkbox value="a">Tenang</b-form-checkbox>
                                                <b-form-checkbox value="b">Meringis</b-form-checkbox>
                                                <b-form-checkbox value="c">Menangis</b-form-checkbox>
                                                <b-form-checkbox value="d">Kesakitan</b-form-checkbox>
                                                <b-form-checkbox value="e">Ketakutan</b-form-checkbox>
                                                <b-form-checkbox value="f">Menarik diri</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Terpasang alat-alat invasiv</label>
                                            <v-select v-model="form_neona.evaluation_comfort_device_type_id" class="select-size-sm" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Lain-lain</label>
                                            <b-form-input v-model="form_neona.evaluation_comfort_other" size="sm" autocomplete="off" />
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-16">
                                    <label class="font-weight-bold fs-13 mb-0">POLA ISTIRAHAT / TIDUR: </label>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <v-select v-model="form_neona.evaluation_rest_pattern_type_id" class="select-size-sm" :options="optSleepPattern" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-16">
                                    <label class="font-weight-bold fs-13 mb-0">REPRODUKSI: </label>
                                    <template v-if="regData.sex == 0">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <label class="fs-13 mb-0">Vagina</label>
                                                <v-select v-model="form_neona.evaluation_vagina_type_id" class="select-size-sm" :options="optIsClean" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Pemasangan kateter</label>
                                                <v-select v-model="form_neona.evaluation_catheter_woman_type_id" class="select-size-sm" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Menstruasi</label>
                                                <v-select v-model="form_neona.evaluation_menstruation_type_id" class="select-size-sm" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Lain-lain</label>
                                                <b-form-input v-model="form_neona.evaluation_reproduction_woman_other" size="sm" autocomplete="off" />
                                            </div>
                                        </div>
                                    </template>
                                    <template v-if="regData.sex == 1">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <label class="fs-13 mb-0">Prepotium</label>
                                                <v-select v-model="form_neona.evaluation_prepuce_type_id" class="select-size-sm" :options="optIsClean" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Scrotum</label>
                                                <v-select v-model="form_neona.evaluation_scrotum_type_id" class="select-size-sm" :options="optScrotum" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Pemasangan kateter</label>
                                                <v-select v-model="form_neona.evaluation_catheter_man_type_id" class="select-size-sm" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Hipospadia</label>
                                                <v-select v-model="form_neona.evaluation_hyposdias_type_id" class="select-size-sm" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Lain-lain</label>
                                                <b-form-input v-model="form_neona.evaluation_reproduction_man_other" size="sm" autocomplete="off" />
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <div class="mt-16">
                                    <label class="font-weight-bold fs-13 mb-0">ELIMINASI: </label>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label class="fs-13 mb-0">Frekuensi BAK</label>
                                            <b-input-group size="sm" append="x/hari">
                                                <b-form-input v-model="form_neona.evaluation_urination_freq" type="number" autocomplete="off" />
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Produksi urin</label>
                                            <b-input-group size="sm" append="ml/kg">
                                                <b-form-input v-model="form_neona.evaluation_urine_production" type="number" autocomplete="off" />
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Urine</label>
                                            <v-select v-model="form_neona.evaluation_urine_type_id" class="select-size-sm" :options="optUrine" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Kateter</label>
                                            <v-select v-model="form_neona.evaluation_elimination_catheter_type_id" class="select-size-sm" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Disuria</label>
                                            <v-select v-model="form_neona.evaluation_dysuria_type_id" class="select-size-sm" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Lain-lain</label>
                                            <b-form-input v-model="form_neona.evaluation_elimination_other" size="sm" autocomplete="off" />
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-16">
                                    <label class="font-weight-bold fs-13 mb-0">KEAMANAN: </label>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label class="fs-13 mb-0">Gerakan pasien</label>
                                            <b-form-checkbox-group v-model="form_neona.evaluation_client_movement_arry" stacked size="sm">
                                                <b-form-checkbox value="a">Aktif</b-form-checkbox>
                                                <b-form-checkbox value="b">Tidak aktif</b-form-checkbox>
                                                <b-form-checkbox value="c">Kejang</b-form-checkbox>
                                                <b-form-checkbox value="d">Lemah</b-form-checkbox>
                                                <b-form-checkbox value="e">Paralise</b-form-checkbox>
                                                <b-form-checkbox value="f">Kelemahan otot</b-form-checkbox>
                                                <b-form-checkbox value="g">Gerakan terbatas</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Lain-lain</label>
                                            <b-form-input v-model="form_neona.evaluation_safety_other" size="sm" autocomplete="off" />
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <b-button-group size="sm">
                                <b-button @click="changeTab('NEWBORNBABY')" :disabled="active.disabled.section" variant="outline-neutral-700">
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button @click="changeSection(active.section, 'NEONATE')" :disabled="active.disabled.section" variant="orange-500">
                                    <template>
                                        <feather-icon icon="SaveIcon" />
                                        Simpan
                                    </template>
                                </b-button>
                            </b-button-group>
                        </div>
                    </section>

                    <section v-if="active.section == 'NEWBORNBABY'" class="card">
                        <div class="card-header bg-primary text-white fs-14 font-weight-bold">BAYI BARU LAHIR</div>
                        <div class="card-body px-20 py-8 position-sticky" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <section>
                                <ModernLabel label="Keadaan Bayi Baru Lahir" />
                                <section class="row mt-8">
                                    <div class="col-lg-3">
                                        <label class="fs-13 mb-0">Nama Bayi</label>
                                        <b-input-group size="sm">
                                            <b-form-input type="text" autocomplete="off" v-model="form_newborn.baby_name"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="fs-13 mb-0">Jam Lahir</label>
                                        <b-form-input v-model="form_newborn.birth_hour" type="time" size="sm"  />
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="fs-13 mb-0">Anak ke</label>
                                        <b-input-group size="sm">
                                            <b-form-input type="text" autocomplete="off" v-model="form_newborn.birth_order"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="fs-13 mb-0">Berat Badan</label>
                                        <b-input-group size="sm" append="g">
                                            <b-form-input type="number" autocomplete="off" v-model="form_newborn.weight"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">Tinggi Badan</label>
                                        <b-input-group size="sm" append="cm">
                                            <b-form-input type="number" autocomplete="off" v-model="form_newborn.height"></b-form-input>
                                        </b-input-group>
                                    </div>
                                </section>

                                <section class="mt-16">
                                    <label class="font-weight-bold fs-13 mb-0">Apgar Score</label>
                                    <b-table-simple bordered small fixed class="text-left">
                                        <b-tr class="fs-12 ">
                                            <!-- <b-th class="align-middle" style="width: 40px;">
                                                <b-button @click="" variant="primary" size="sm">
                                                    <feather-icon icon="PlusIcon" />
                                                </b-button>
                                            </b-th> -->
                                            <!-- <b-th class="align-middle" style="width: 40px;">No</b-th> -->
                                            <b-th class="align-middle text-left">Tindakan</b-th>
                                            <b-th class="align-middle text-center">I</b-th>
                                            <b-th class="align-middle text-center">II</b-th>
                                        </b-tr>
                                        <b-tr class="fs-12">
                                            <b-td>
                                                <label class="fs-13 mb-0">Frekuensi Jantung</label>
                                            </b-td>
                                            <b-td>
                                                <v-select class="select-size-sm" @input="updateCount('one')" v-model="tempApgarScore.apgar_score_one.apgar_score_heart_rate_i" :options="optApgarPulse" :reduce="item => item" label="title" placeholder="pilih" />
                                            </b-td>
                                            <b-td>
                                                <v-select class="select-size-sm" @input="updateCount('two')" v-model="tempApgarScore.apgar_score_two.apgar_score_heart_rate_ii" :options="optApgarPulse" :reduce="item => item" label="title" placeholder="pilih" />
                                            </b-td>
                                        </b-tr>
                                        <b-tr class="fs-12">
                                            <b-td>
                                                <label class="fs-13 mb-0">Usaha Pernafasan</label>
                                            </b-td>
                                            <b-td>
                                                <v-select class="select-size-sm" @input="updateCount('one')" v-model="tempApgarScore.apgar_score_one.apgar_score_respiratory_i" :options="optApgarRespiration" :reduce="item => item" label="title" placeholder="pilih" />
                                            </b-td>
                                            <b-td>
                                                <v-select class="select-size-sm" @input="updateCount('two')" v-model="tempApgarScore.apgar_score_two.apgar_score_respiratory_ii" :options="optApgarRespiration" :reduce="item => item" label="title" placeholder="pilih" />
                                            </b-td>
                                        </b-tr>
                                        <b-tr class="fs-12">
                                            <b-td>
                                                <label class="fs-13 mb-0">Tonus Otot</label>
                                            </b-td>
                                            <b-td>
                                                <v-select class="select-size-sm" @input="updateCount('one')" v-model="tempApgarScore.apgar_score_one.apgar_score_muscle_tone_i" :options="optApgarMuscleActivity" :reduce="item => item" label="title" placeholder="pilih" />
                                            </b-td>
                                            <b-td>
                                                <v-select class="select-size-sm" @input="updateCount('two')" v-model="tempApgarScore.apgar_score_two.apgar_score_muscle_tone_ii" :options="optApgarMuscleActivity" :reduce="item => item" label="title" placeholder="pilih" />
                                            </b-td>
                                        </b-tr>
                                        <b-tr class="fs-12">
                                            <b-td>
                                                <label class="fs-13 mb-0">Reaksi Refleks</label>
                                            </b-td>
                                            <b-td>
                                                <v-select class="select-size-sm" @input="updateCount('one')" v-model="tempApgarScore.apgar_score_one.apgar_score_reflex_i" :options="optApgarReflectReaction" :reduce="item => item" label="title" placeholder="pilih" />
                                            </b-td>
                                            <b-td>
                                                <v-select class="select-size-sm" @input="updateCount('two')" v-model="tempApgarScore.apgar_score_two.apgar_score_reflex_ii" :options="optApgarReflectReaction" :reduce="item => item" label="title" placeholder="pilih" />
                                            </b-td>
                                        </b-tr>
                                        <b-tr class="fs-12">
                                            <b-td>
                                                <label class="fs-13 mb-0">Warna Kulit</label>
                                            </b-td>
                                            <b-td>
                                                <v-select class="select-size-sm" @input="updateCount('one')" v-model="tempApgarScore.apgar_score_one.apgar_score_skin_color_i" :options="optApgarSkinColor" :reduce="item => item" label="title" placeholder="pilih" />
                                            </b-td>
                                            <b-td>
                                                <v-select class="select-size-sm" @input="updateCount('two')" v-model="tempApgarScore.apgar_score_two.apgar_score_skin_color_ii" :options="optApgarSkinColor" :reduce="item => item" label="title" placeholder="pilih" />
                                            </b-td>
                                        </b-tr>
                                        <b-tr class="fs-12">
                                            <b-td class="font-weight-bold">Jumlah</b-td>
                                            <b-td class="font-weight-bold text-center">{{ form_newborn.apgar_score_total_i }}</b-td>
                                            <b-td class="font-weight-bold text-center">{{ form_newborn.apgar_score_total_ii }}</b-td>
                                        </b-tr>
                                    </b-table-simple>
                                </section>
                                <section class="mt-16">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label class="fs-13">Kesimpulan</label>
                                            <textarea rows="2" v-model.lazy="form_newborn.conclution" class="form-control form-control-sm" autocomplete="off" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Alat Kelamin Luar</label>
                                            <b-form-input type="text" size="sm" v-model="form_newborn.external_genitalia" autocomplete="off"></b-form-input>
                                            <!-- <textarea v-model.lazy="form_newborn.external_genitalia" class="form-control form-control-sm" autocomplete="off" /> -->
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Anus</label>
                                            <b-form-input type="text" size="sm" v-model="form_newborn.anus" autocomplete="off"></b-form-input>
                                            <!-- <textarea v-model.lazy="form_newborn.anus" class="form-control form-control-sm" autocomplete="off" /> -->
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Meconium</label>
                                            <b-form-input type="text" size="sm" v-model="form_newborn.meconium" autocomplete="off"></b-form-input>
                                            <!-- <textarea v-model.lazy="form_newborn.meconium" class="form-control form-control-sm" autocomplete="off" /> -->
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Miksi</label>
                                            <b-form-input type="text" size="sm" v-model="form_newborn.miksi" autocomplete="off"></b-form-input>
                                            <!-- <textarea v-model.lazy="form_newborn.miksi" class="form-control form-control-sm" autocomplete="off" /> -->
                                        </div>
                                        <div class="col-lg-3  mt-8">
                                            <label class="fs-13 mb-0">Vernik Caseosa / Lanugo</label>
                                            <b-form-input type="text" size="sm" v-model="form_newborn.vernix" autocomplete="off"></b-form-input>
                                            <!-- <textarea v-model.lazy="form_newborn.vernix" class="form-control form-control-sm" autocomplete="off" /> -->
                                        </div>

                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Caput Succedanium</label>
                                            <v-select class="select-size-sm" v-model="form_newborn.caput_succedaneum_type_id" :options="optCaputSuccedanium" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Cephal Haematom</label>
                                            <v-select class="select-size-sm" v-model="form_newborn.cephalohematoma_type_id" :options="optCephalHaematom" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Kepala</label>
                                            <v-select class="select-size-sm" v-model="form_newborn.head_type_id" :options="optHeadBabyborn" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Sutura</label>
                                            <v-select class="select-size-sm" v-model="form_newborn.suture_type_id" :options="optSutura" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>

                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Ubun-ubun Besar</label>
                                            <b-form-input type="text" size="sm" v-model="form_newborn.large_fontanel" autocomplete="off"></b-form-input>
                                            <!-- <textarea v-model.lazy="form_newborn.large_fontanel" class="form-control form-control-sm" autocomplete="off" /> -->
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Ubun-ubun Kecil</label>
                                            <b-form-input type="text" size="sm" v-model="form_newborn.small_fontanel" autocomplete="off"></b-form-input>
                                            <!-- <textarea v-model.lazy="form_newborn.small_fontanel" class="form-control form-control-sm" autocomplete="off" /> -->
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Meninggal Dalam Kehamilan</label>
                                            <v-select class="select-size-sm" v-model="form_newborn.died_in_pregnancy_type_id" :options="optDiedInPregnancy" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Meninggal Dalam Persalinan</label>
                                            <v-select class="select-size-sm" v-model="form_newborn.died_in_childbirth_type_id" :options="optDiedInChildbirth" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Meninggal sesudah lahir beberapa menit / jam</label>
                                            <v-select class="select-size-sm" v-model="form_newborn.died_after_birth_type_id" :options="optDiedAfterBirth" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-12 mt-8">
                                            <label class="fs-13 mb-0">Penyulit</label>
                                            <textarea rows="2" v-model.lazy="form_newborn.complication" class="form-control form-control-sm" autocomplete="off" />
                                        </div>
                                        <div class="col-12 mt-8">
                                            <label class="fs-13 mb-0">Kelainan bawaan</label>
                                            <textarea rows="2" v-model.lazy="form_newborn.congenital_disorder" class="form-control form-control-sm" autocomplete="off" />
                                        </div>
                                        <div class="col-12 mt-8">
                                            <label class="fs-13 mb-0">Trauma Bayi</label>
                                            <textarea rows="2" v-model.lazy="form_newborn.baby_trauma" class="form-control form-control-sm" autocomplete="off" />
                                        </div>
                                    </div>
                                </section>
                                <div class="mt-16">
                                    <ModernLabel label="Pertolongan Terhadap Bayi" />
                                </div>
                                <section class="row mt-8">
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Pengisapan Lendir</label>
                                        <v-select class="select-size-sm" v-model="form_newborn.hfb_mucus_suction_type_id" :options="optHfbMucusSuction" :reduce="item => item.value" label="title" placeholder="pilih" />
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Rangsangan</label>
                                        <v-select class="select-size-sm" v-model="form_newborn.hfb_stimulus_type_id" :options="optHfbStimulus" :reduce="item => item.value" label="title" placeholder="pilih" />
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Pemberian Oksigen</label>
                                        <v-select class="select-size-sm" v-model="form_newborn.hfb_oxygen_type_id" :options="optHfbOxygen" :reduce="item => item.value" label="title" placeholder="pilih" />
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Resusitasi</label>
                                        <v-select class="select-size-sm" v-model="form_newborn.hfb_resuscitation_type_id" :options="optHfbResuscitation" :reduce="item => item.value" label="title" placeholder="pilih" />
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Mata Diolesi Dengan</label>
                                        <b-form-input type="text" size="sm" v-model="form_newborn.hfb_eye_application" autocomplete="off"></b-form-input>
                                        <!-- <textarea v-model.lazy="form_newborn.hfb_eye_application" class="form-control form-control-sm" autocomplete="off" /> -->
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Ujung Tali Pusat Diolesi Dengan</label>
                                        <b-form-input type="text" size="sm" v-model="form_newborn.hfb_umbilical_cord_application" autocomplete="off"></b-form-input>
                                        <!-- <textarea v-model.lazy="form_newborn.hfb_umbilical_cord_application" class="form-control form-control-sm" autocomplete="off" /> -->
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Tali Pusat Dibungkus Dengan</label>
                                        <b-form-input type="text" size="sm" v-model="form_newborn.hfb_umbilical_cord_wrapped" autocomplete="off"></b-form-input>
                                        <!-- <textarea v-model.lazy="form_newborn.hfb_umbilical_cord_wrapped" class="form-control form-control-sm" autocomplete="off" /> -->
                                    </div>
                                    <div class="col-lg-12 mt-8">
                                        <section class="row mt-6">
                                            <div class="col-lg-3 mt-8" style="border-right: 1px solid black;">
                                                <div>
                                                    <label class="fs-13 mb-0">Ambu</label>
                                                    <v-select v-model="form_newborn.hfb_ambu_type_id" :options="optAmbu" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                                </div>
                                                <template v-if="form_newborn.hfb_ambu_type_id == 2">
                                                    <div class="mt-8">
                                                        <label class="fs-13 mb-0">Lamanya Ambu</label>
                                                        <b-input-group size="sm" append="mnt">
                                                            <b-form-input type="number" autocomplete="off" v-model="form_newborn.hfb_ambu_long"></b-form-input>
                                                        </b-input-group>
                                                    </div>
                                                </template>
                                            </div>
                                            <div class="col-lg-3 mt-8" style="border-right: 1px solid black;">
                                                <div>
                                                    <label class="fs-13 mb-0">Massage Jantung</label>
                                                    <v-select v-model="form_newborn.hfb_heart_massage_type_id" :options="optHeartMassage" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                                </div>
                                                <template v-if="form_newborn.hfb_heart_massage_type_id == 2">
                                                    <div class="mt-8">
                                                        <label class="fs-13 mb-0">Lamanya Massage Jantung</label>
                                                        <b-input-group size="sm" append="mnt">
                                                            <b-form-input type="number" autocomplete="off" v-model="form_newborn.hfb_heart_massage_long"></b-form-input>
                                                        </b-input-group>
                                                    </div>
                                                </template>
                                            </div>
                                            <div class="col-lg-3 mt-8" style="border-right: 1px solid black;">
                                                <div>
                                                    <label class="fs-13 mb-0">Pasang ETT</label>
                                                    <v-select v-model="form_newborn.hfb_endotracheal_tube_type_id" :options="optEndotrachealTube" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                                </div>
                                                <template v-if="form_newborn.hfb_endotracheal_tube_type_id == 2">
                                                    <div class="mt-8">
                                                        <label class="fs-13 mb-0">Ukuran ETT</label>
                                                        <b-input-group size="sm" append="cm">
                                                            <b-form-input type="number" autocomplete="off" v-model="form_newborn.hfb_endotracheal_tube_size"></b-form-input>
                                                        </b-input-group>
                                                        <label class="fs-13 mb-0">Nomor ETT</label>
                                                        <b-form-input type="number" size="sm" v-model="form_newborn.hfb_endotracheal_tube_number" autocomplete="off"></b-form-input>
                                                        <!-- <b-input-group size="sm" append="mnt">
                                                            <b-form-input type="number" autocomplete="off" v-model="form_newborn.hfb_endotracheal_tube_number"></b-form-input>
                                                        </b-input-group> -->
                                                    </div>
                                                </template>
                                            </div>
                                        </section>
                                    </div>
                                    <div class="col-12 mt-8">
                                        <label class="fs-13 mb-0 font-weight-bold">Obat-obat yang sudah diberikan</label>
                                        <textarea v-model.lazy="form_newborn.hfb_given_medicines" rows="2" class="form-control form-control-sm" autocomplete="off" />
                                    </div>
                                </section>
                                <div class="mt-16">
                                    <ModernLabel label="Ukuran Kepala Bayi" />
                                </div>
                                <section class="row">
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Dist. Bitemporalis</label>
                                        <b-input-group size="sm" append="cm">
                                            <b-form-input type="number" autocomplete="off" v-model="form_newborn.hfb_head_size_bitemporalis"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Dist. Biparietalis</label>
                                        <b-input-group size="sm" append="cm">
                                            <b-form-input type="number" autocomplete="off" v-model="form_newborn.hfb_head_size_biparietalis"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Dist. Fronto Occipitalis</label>
                                        <b-input-group size="sm" append="cm">
                                            <b-form-input type="number" autocomplete="off" v-model="form_newborn.hfb_head_size_fronto_occipitalis"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Dist. Mento Occipitalis</label>
                                        <b-input-group size="sm" append="cm">
                                            <b-form-input type="number" autocomplete="off" v-model="form_newborn.hfb_head_size_mento_occipitalis"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Dist. Suboccipito Bregmatica</label>
                                        <b-input-group size="sm" append="cm">
                                            <b-form-input type="number" autocomplete="off" v-model="form_newborn.hfb_head_size_suboccipito_bregmatica"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Circ. Mento Occipitalis</label>
                                        <b-input-group size="sm" append="cm">
                                            <b-form-input type="number" autocomplete="off" v-model="form_newborn.hfb_circumference_mento_occipitalis"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Circ. Fronto Occipitalis (LK)</label>
                                        <b-input-group size="sm" append="cm">
                                            <b-form-input type="number" autocomplete="off" v-model="form_newborn.hfb_circumference_fronto_occipitalis"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Circ. Suboccipito Bregmatica</label>
                                        <b-input-group size="sm" append="cm">
                                            <b-form-input type="number" autocomplete="off" v-model="form_newborn.hfb_circumference_suboccipito_bregmatica"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Lingkar Dada</label>
                                        <b-input-group size="sm" append="cm">
                                            <b-form-input type="number" autocomplete="off" v-model="form_newborn.hfb_chest_circumference"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Lingkar Lengan Atas</label>
                                        <b-input-group size="sm" append="cm">
                                            <b-form-input type="number" autocomplete="off" v-model="form_newborn.hfb_upper_arm_circumference"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-12 mt-8">
                                        <section class="row mt-6">
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Ditolong Oleh</label>
                                                <v-select v-model="form_newborn.hfb_assisted_by" :options="doctorObygnList" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                                <!-- <b-form-input type="text" size="sm" v-model="form_newborn.hfb_assisted_by" autocomplete="off"></b-form-input> -->
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Bayi Lahir</label>
                                                <v-select class="select-size-sm" v-model="form_newborn.hfb_newborn_baby_type_id" :options="optHfbNewbornBaby" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Kondisi Bayi</label>
                                                <v-select class="select-size-sm" v-model="form_newborn.hfb_baby_condition_type_id" :options="optHfbBabyCondition" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Usia Kehamilan</label>
                                                <b-input-group size="sm" append="mgg">
                                                    <b-form-input type="number" autocomplete="off" v-model="form_newborn.gestational_age"></b-form-input>
                                                </b-input-group>
                                            </div>
                                        </section>
                                    </div>
                                    <div class="col-lg-12 mt-8">
                                        <section class="row mt-6">
                                            <div class="col-lg-3 mt-8" style="border-right: 1px solid black;">
                                                <div>
                                                    <label class="fs-13 mb-0">Jenis Persalinan</label>
                                                    <v-select v-model="form_newborn.hfb_delivery_type_id" :options="optHfbDelivery" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                                </div>
                                                <template v-if="form_newborn.hfb_delivery_type_id == 4">
                                                    <div class="mt-8">
                                                        <label class="fs-13 mb-0">Jenis Persalinan Lainnya</label>
                                                        <b-form-input type="text" size="sm" v-model="form_newborn.hfb_delivery_other" autocomplete="off"></b-form-input>
                                                    </div>
                                                </template>
                                            </div>
                                        </section>
                                    </div>
                                    <div class="col-lg-12 mt-8">
                                        <label class="fs-13 mb-0">Ketuban pecah</label>
                                        <section class="row mt-6">
                                            <div class="col-lg-3">
                                                <label class="fs-13 mb-0">Jam</label>
                                                <b-form-input v-model="form_newborn.water_breaks_hour" type="time" size="sm"  />
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="fs-13 mb-0">Warna</label>
                                                <b-form-input type="text" size="sm" autocomplete="off" v-model="form_newborn.water_breaks_color"></b-form-input>
                                            </div>
                                        </section>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Lilitan Tali Pusat</label>
                                        <v-select class="select-size-sm" v-model="form_newborn.umbilical_cord_entanglement_type_id" :options="optUmbilicalCord" :reduce="item => item.value" label="title" placeholder="pilih" />
                                    </div>
                                    <div class="col-lg-12 mt-8">
                                        <label class="fs-13 mb-0 font-weight-bold">Catatan Khusus</label>
                                        <textarea rows="3" v-model.lazy="form_newborn.special_note" class="form-control form-control-sm" autocomplete="off" />
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Dipindahkan ke kamar bayi</label>
                                        <b-form-input v-model="form_newborn.transferred_date" type="datetime-local" size="sm"  />
                                    </div>
                                </section>
                            </section>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <b-button-group size="sm">
                                <b-button @click="changeTab('NEWBORNBABY')" :disabled="active.disabled.section" variant="outline-neutral-700">
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button @click="changeSection(active.section, 'NEONATE')" :disabled="active.disabled.section" variant="orange-500">
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
            urlbe: process.env.VUE_APP_IMAGE,
            urlAsset:process.env.VUE_APP_ASSETS_BE,
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
            original_adult: {},
            original_child: {},
            original_newborn:{},
            original_neona: {},

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

            form_adult: {
                id: '',
                employee_nurse_id: '',
                registration_destination_id: '',
                type_nurse_id: '',
                psychological_status_arry: [],
                psychological_status: '',
                mental_status_arry: [],
                mental_status_problems: '',
                mental_status_violence: '',
                elimination_defecate: '',
                elimination_urination: '',
                elimination_consistency_type_id: '',
                elimination_color_type_id: '',
                elimination_color: '',
                evaluation_smoke_type_id: '',
                evaluation_smoke_amount: '',
                evaluation_smoke_long: '',
                evaluation_smoke_unit: 'Tahun',
                evaluation_alcohol_type_id: '',
                evaluation_alcohol_type: '',
                evaluation_alcohol_amount: '',
                evaluation_sedative_type_id: '',
                evaluation_sedative_type: '',
                evaluation_sedative_amount: '',
                evaluation_sedative_unit: 'Tablet',
                evaluation_sleep_type_id: '',
                evaluation_sleep: '',
                evaluation_daily_type_id: '',
                evaluation_activity_type_id: '',
                evaluation_extremities_top_type_id: '',
                evaluation_grip_type_id: '',
                evaluation_grip_other: '',
                evaluation_coordination_type_id: '',
                evaluation_coordination_other: '',
                evaluation_extremities_bottom_type_id: '',
                evaluation_extremities_bottom_bone: '',
                evaluation_ambulation_type_id: '',
                // medical_devices_type_id: '',
                medical_devices: [],
                discharge_treatment_duration: '',
                discharge_further_care_arry: [],
                examination_history: []
            },

            OptionsIsAny: [
                { value: 0, title: 'Tidak' },
                { value: 1, title: 'Ada' },
            ],

            OptionsMedicalDevices: [
                { value: 0, title: 'Tidak Ada' },
                { value: 1, title: 'Ya' },
            ],

            OptionsEvalDaily: [
                { value: 1, title: 'Mandiri' },
                { value: 2, title: 'Bantuan Minimal' },
                { value: 3, title: 'Bantuan Sebagian' },
                { value: 4, title: 'Ketergantungan' },
            ],

            OptionsEvalActivity: [
                { value: 1, title: 'Tirah Baring' },
                { value: 2, title: 'Duduk' },
                { value: 3, title: 'Berjalan' },
            ],

            OptionsEvalTopExtremity: [
                { value: 1, title: 'Tidak ada kelainan' },
                { value: 2, title: 'Lemah' },
            ],

            OptionsEvalBotExtremity: [
                { value: 1, title: 'Tidak ada kesulitan' },
                { value: 2, title: 'Penurunan kekuatan / Range of Motion' },
                { value: 3, title: 'Paralisis' },
                { value: 4, title: 'Sering jatuh' },
                { value: 5, title: 'Hilang keseimbangan' },
                { value: 6, title: 'Deformitas' },
                { value: 7, title: 'Riwayat patah tulang' },
            ],

            OptionsEvalGrip: [
                { value: 1, title: 'Tidak ada kesulitan' },
                { value: 2, title: 'Terkilir' },
                { value: 3, title: 'Lainnya' },
            ],

            OptionsEvalCoordination: [
                { value: 1, title: 'Tidak ada kesulitan' },
                { value: 2, title: 'Ada Masalah' },
            ],

            OptionsEvalAmbulation: [
                { value: 1, title: 'Walker' },
                { value: 2, title: 'Tongkat' },
                { value: 3, title: 'Kursi roda' },
            ],

            OptionsElimConsistency: [
                { value: 1, title: 'Lunak' },
                { value: 2, title: 'Keras' },
                { value: 3, title: 'Cair' },
                { value: 4, title: 'Ampas' },
                { value: 5, title: 'Tidak' },
            ],

            OptionsElimColor: [
                { value: 1, title: 'Kuning Jernih' },
                { value: 2, title: 'Kuning Keruh' },
                { value: 3, title: 'Lainnya' },
            ],

            arry_values: ['a', 'b', 'c', 'd', 'e', 'f'],
            arry_immuni: [
                {
                    side: [
                        { mark: 1, name: 'BCG', model: 'immunization_bcg_arry' },
                        { mark: 3, name: 'Hepatitis B', model: 'immunization_hepatitis_b_arry' },
                        { mark: 5, name: 'DPT', model: 'immunization_dpt_arry' },
                        { mark: 6, name: 'Polio', model: 'immunization_polio_arry' },
                        { mark: 2, name: 'Campak', model: 'immunization_measles_arry' },
                        { mark: 4, name: 'Hib', model: 'immunization_hib_arry' },
                        { mark: 4, name: 'IPD', model: 'immunization_ipd_arry' },
                    ]
                },
                {
                    side: [
                        { mark: 2, name: 'Flu', model: 'immunization_flu_arry' },
                        { mark: 2, name: 'Cacar Air', model: 'immunization_chickenpox_arry' },
                        { mark: 2, name: 'MMR', model: 'immunization_mmr_arry' },
                        { mark: 1, name: 'Typhoid', model: 'immunization_typhoid_arry' },
                        { mark: 2, name: 'Hepatitis A', model: 'immunization_hepatitis_a_arry' },
                        { mark: 3, name: 'HPV', model: 'immunization_hpv_arry' },
                        { mark: 1, name: 'Rotavirus', model: 'immunization_rotavirus_arry' },
                        { mark: 1, name: 'Influenza', model: 'immunization_influenza_arry' },
                    ]
                },
            ],

            OptionsTalk: [
                { value: 1, title: 'Normal' },
                { value: 2, title: 'Gangguan bicara' },
            ],

            OptionsIsYes: [
                { value: 0, title: 'Tidak' },
                { value: 1, title: 'Ya' },
            ],

            OptionsIsDone: [
                { value: 0, title: 'Belum' },
                { value: 1, title: 'Sudah' },
            ],

            OptionsEducationLevel: [
                { value: 1, title: 'SD' },
                { value: 2, title: 'SMP' },
                { value: 3, title: 'SMA' },
                { value: 4, title: 'Akademi / perguruan tinggi' },
                { value: 5, title: 'lainnya' },
            ],

            OptionsChildMental: [
                { value: 1, title: 'Normal' },
                { value: 2, title: 'Konstipasi' },
                { value: 3, title: 'Agitasi' },
                { value: 4, title: 'Kooperatif' },
                { value: 5, title: 'Letargi' },
                { value: 6, title: 'Marah' },
                { value: 7, title: 'Disorientasi' },
            ],

            OptionsChildPsychological: [
                { value: 1, title: 'Tenang' },
                { value: 2, title: 'Cemas' },
                { value: 3, title: 'Depresi' },
                { value: 4, title: 'Menyerang' },
                { value: 5, title: 'Hiperaktif' },
                { value: 6, title: 'Gangguang sekitar' },
                { value: 7, title: 'Lain-lain' },
            ],

            OptionsChildBak: [
                { value: 1, title: 'Normal' },
                { value: 2, title: 'Henmaturi' },
                { value: 3, title: 'Nokturia' },
                { value: 4, title: 'Disuria' },
                { value: 5, title: 'Uretrostomy' },
                { value: 6, title: 'Urine menetes' },
                { value: 7, title: 'Inkontinensia Uri' },
            ],

            OptionsChildBab: [
                { value: 1, title: 'Normal' },
                { value: 2, title: 'Konstipasi' },
                { value: 3, title: 'Melena' },
                { value: 4, title: 'Inkontinensia' },
                { value: 5, title: 'Ileustomy' },
                { value: 6, title: 'Colostomy' },
                { value: 7, title: 'Diare' },
            ],

            form_child: {
                id: '',
                registration_destination_id: '',
                employee_nurse_id: '',
                type_nurse_id: '',

                // 1.Alat kesehatan yang terpasang
                // medical_devices_type_id: '',
                medical_devices: [],

                // 2. riwayat tumbuh kembang
                growth_birth_weight: '',
                growth_birth_height: '',
                growth_breast_milk_age: '',
                growth_formula_milk_age: '',
                growth_solid_food_age: '',
                growth_additional_food_age: '',
                growth_prone_age: '',
                growth_sit_age: '',
                growth_crawl_age: '',
                growth_stand_age: '',
                growth_walk_age: '',
                growth_breast_milk_unit: 'Bulan',
                growth_formula_milk_unit: 'Bulan',
                growth_solid_food_unit: 'Bulan',
                growth_additional_food_unit: 'Bulan',
                growth_prone_unit: 'Bulan',
                growth_sit_unit: 'Bulan',
                growth_crawl_unit: 'Bulan',
                growth_stand_unit: 'Bulan',
                growth_walk_unit: 'Bulan',

                // 3. Riwayat imunisasi
                immunization_bcg_arry: [],
                immunization_hepatitis_b_arry: [],
                immunization_dpt_arry: [],
                immunization_polio_arry: [],
                immunization_measles_arry: [],
                immunization_hib_arry: [],
                immunization_ipd_arry: [],
                immunization_flu_arry: [],
                immunization_chickenpox_arry: [],
                immunization_mmr_arry: [],
                immunization_typhoid_arry: [],
                immunization_hepatitis_a_arry: [],
                immunization_hpv_arry: [],
                immunization_rotavirus_arry: [],
                immunization_influenza_arry: [],

                // 4. riwayat pemeriksaan yang pernah dilakukan
                examination_history: [],

                // 5. Kebutuhan komunikasi dan edukasi
                comm_needs_speech_type_id: '',
                comm_needs_speech_disorder: '',
                comm_needs_colloquial_arry: [],
                comm_needs_colloquial_local: '',
                comm_needs_colloquial_other: '',
                comm_needs_translator_type_id: '',
                edu_needs_barriers_type_id: '',
                edu_needs_barriers_arry: [],
                edu_needs_barriers_other: '',
                edu_needs_preferred_arry: [],
                edu_needs_level_of_education_type_id: '',
                edu_needs_level_of_education_other: '',
                edu_needs_information_arry: [],
                edu_needs_information_other: '',

                // penilaian per sistem
                evaluation_smoke_unit: 'Tahun',
                evaluation_sedative_unit: 'Tablet',
                evaluation_smoke_type_id: '',
                evaluation_smoke_amount: '',
                evaluation_smoke_long: '',
                evaluation_alcohol_type_id: '',
                evaluation_alcohol_type: '',
                evaluation_alcohol_amount: '',
                evaluation_sedative_type_id: '',
                evaluation_sedative_type: '',
                evaluation_sedative_amount: '',
                evaluation_sleep_type_id: '',
                evaluation_sleep: '',
                evaluation_daily_type_id: '',
                evaluation_activity_type_id: '',
                evaluation_extremities_top_type_id: '',
                evaluation_grip_type_id: '',
                evaluation_grip_other: '',
                evaluation_extremities_bottom_type_id: '',
                evaluation_extremities_bottom_bone: '',
                evaluation_ambulation_type_id: '',
                evaluation_mental_status_type_id: '',
                evaluation_psychological_status_type_id: '',

                evaluation_psychological_status_other: '',
                evaluation_elimination_defecate_type_id: '',
                evaluation_elimination_defecate_freq: '',
                evaluation_elimination_urination_type_id: '',
                evaluation_sexual_menstruation_type_id: '',
                evaluation_sexual_circumcision_type_id: '',
                evaluation_sexual_circumcision: '',
                evaluation_sexual_problem_type_id: '',
                evaluation_sexual_problem: ''
            },

            form_newborn: {
                id: '',
                registration_destination_id: '',
                employee_nurse_id: '',
                type_nurse_id: '',

                birth_date:'',
                birth_hour: null,
                birth_order:'',
                weight: '',
                height:'',
                apgar_score_heart_rate_i: null,
                apgar_score_respiratory_i: null,
                apgar_score_muscle_tone_i: null,
                apgar_score_reflex_i: null,
                apgar_score_skin_color_i: null,
                apgar_score_total_i: null,
                apgar_score_heart_rate_ii: null,
                apgar_score_respiratory_ii: null,
                apgar_score_muscle_tone_ii: null,
                apgar_score_reflex_ii: null,
                apgar_score_skin_color_ii: null,
                apgar_score_total_ii: null,
                conclution:'',
                external_genitalia: "",
                anus: "",
                meconium: "",
                miksi: "",
                vernix: "",
                caput_succedaneum_type_id: null,
                cephalohematoma_type_id: null,
                head_type_id: null,
                suture_type_id: null,
                large_fontanel: "",
                small_fontanel: "",
                died_in_pregnancy_type_id: null,
                died_in_childbirth_type_id: null,
                died_after_birth_type_id: null,
                complication:"",
                congenital_disorder: "",
                baby_trauma: "",
                hfb_mucus_suction_type_id: null,
                hfb_stimulus_type_id: null,
                hfb_ambu_type_id: null,
                hfb_ambu_long: "",
                hfb_heart_massage_type_id: null,
                hfb_heart_massage_long: "",
                hfb_endotracheal_tube_type_id: null,
                hfb_endotracheal_tube_number: "",
                hfb_endotracheal_tube_size: "",
                hfb_oxygen_type_id: null,
                hfb_resuscitation_type_id: null,
                hfb_eye_application: "",
                hfb_umbilical_cord_application: "",
                hfb_umbilical_cord_wrapped: "",
                hfb_given_medicines: "",
                hfb_head_size_bitemporalis: "",
                hfb_head_size_biparietalis: "",
                hfb_head_size_fronto_occipitalis: "",
                hfb_head_size_mento_occipitalis: "",
                hfb_head_size_suboccipito_bregmatica: "",
                hfb_circumference_fronto_occipitalis: "",
                hfb_circumference_mento_occipitalis: "",
                hfb_circumference_suboccipito_bregmatica: "",
                hfb_chest_circumference: "",
                hfb_upper_arm_circumference: "",
                hfb_delivery_type_id: null,
                hfb_delivery_other: "",
                hfb_assisted_by: "",
                hfb_newborn_baby_type_id: null,
                hfb_baby_condition_type_id: null,
                gestational_age: "",
                water_breaks_hour: "",
                water_breaks_color: "",
                umbilical_cord_entanglement_type_id: null,
                special_note: "",
                baby_name: "",
                transferred_date: ""
            },

            tempApgarScore:{
                apgar_score_one: {
                    apgar_score_heart_rate_i: null,
                    apgar_score_respiratory_i: null,
                    apgar_score_muscle_tone_i: null,
                    apgar_score_reflex_i: null,
                    apgar_score_skin_color_i: null,
                },
                apgar_score_two: {
                    apgar_score_heart_rate_ii: null,
                    apgar_score_respiratory_ii: null,
                    apgar_score_muscle_tone_ii: null,
                    apgar_score_reflex_ii: null,
                    apgar_score_skin_color_ii: null,
                },
            },

            form_neona: {
                id: '',
                registration_destination_id: '',
                employee_nurse_id: '',
                type_nurse_id: '',
                medical_devices: [],
                examination_history: [],

                gestational_age: '',
                corrected_age: '',
                type_of_delivery: '',
                labor_diagnosis: '',
                mother_blood_type_id: '',
                mother_lab_result_arry: [],
                mother_lab_result: '',
                maternal_medical_history: '',
                baby_gender: '',
                birth_history_type_id: '',
                birth_history_lk: '',
                birth_history_ld: '',
                postnatal_drug_arry: [],
                postnatal_drug_vit_k: '',
                postnatal_drug_eye: '',
                postnatal_drug_other: '',

                comm_needs_speech_type_id: '',
                comm_needs_speech_disorder: '',
                comm_needs_colloquial_arry: [],
                comm_needs_colloquial_local: '',
                comm_needs_colloquial_other: '',
                comm_needs_translator_type_id: '',
                edu_needs_level_of_education_type_id: '',
                edu_needs_level_of_education_other: '',
                edu_needs_information_arry: [],
                edu_needs_information_other: '',

                socio_cultural_ethnic: '',
                socio_cultural_citizen: '',
                socio_cultural_residence_type_id: '',
                socio_cultural_who_cares_type_id: '',
                socio_cultural_parent: '',
                socio_cultural_visit_type_id: '',
                socio_cultural_touch_type_id: '',
                socio_cultural_carry_type_id: '',
                socio_cultural_eye_contact_type_id: '',
                socio_cultural_speak_type_id: '',
                socio_cultural_other: '',

                evaluation_comfort_arry: [],
                evaluation_comfort_device_type_id: '',
                evaluation_comfort_other: '',
                evaluation_rest_pattern_type_id: '',
                evaluation_vagina_type_id: '',
                evaluation_catheter_woman_type_id: '',
                evaluation_menstruation_type_id: '',
                evaluation_reproduction_woman_other: '',
                evaluation_prepuce_type_id: '',
                evaluation_scrotum_type_id: '',
                evaluation_catheter_man_type_id: '',
                evaluation_hyposdias_type_id: '',
                evaluation_reproduction_man_other: '',
                evaluation_urination_freq: '',
                evaluation_urine_production: '',
                evaluation_urine_type_id: '',
                evaluation_elimination_catheter_type_id: '',
                evaluation_dysuria_type_id: '',
                evaluation_elimination_other: '',
                evaluation_client_movement_arry: [],
                evaluation_safety_other: ''
            },

            live_sub: {
                neonatus: false,
                newbornbaby: false,
                child: false,
                adult: false,
            },

            doctorObygnList:[],

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
        }
    },

    watch: {
        "form_anamnesa.anamesa_type_id"(newValue) {
            if (newValue == "1") {
                this.form_anamnesa.family_relation_id = ''
                this.form_anamnesa.family_name = ''
            }
        },

        //  =============== RANAP DEWASA ================
        // "form_adult.medical_devices_type_id"(newValue) {
        //     if (newValue != "1") {
        //         this.form_adult.medical_devices = []
        //     }
        // },

        "form_adult.evaluation_smoke_type_id"(newValue) {
            if (newValue != "1") {
                this.form_adult.evaluation_smoke_amount = ''
                this.form_adult.evaluation_smoke_long = ''
                this.form_adult.evaluation_smoke_unit = 'Tahun'
            }
        },

        "form_adult.evaluation_alcohol_type_id"(newValue) {
            if (newValue != "1") {
                this.form_adult.evaluation_alcohol_type = ''
                this.form_adult.evaluation_alcohol_amount = ''
            }
        },

        "form_adult.evaluation_sedative_type_id"(newValue) {
            if (newValue != "1") {
                this.form_adult.evaluation_sedative_type = ''
                this.form_adult.evaluation_sedative_amount = ''
                this.form_adult.evaluation_sedative_unit = 'Tablet'
            }
        },

        "form_adult.evaluation_sleep_type_id"(newValue) {
            if (newValue != "1") {
                this.form_adult.evaluation_sleep = ''
            }
        },

        "form_adult.evaluation_grip_type_id"(newValue) {
            if (newValue != "3") {
                this.form_adult.evaluation_grip_other = ''
            }
        },

        "form_adult.evaluation_coordination_type_id"(newValue) {
            if (newValue != "2") {
                this.form_adult.evaluation_coordination_other = ''
            }
        },

        "form_adult.evaluation_extremities_bottom_type_id"(newValue) {
            if (newValue != "7") {
                this.form_adult.evaluation_extremities_bottom_bone = ''
            }
        },

        "form_adult.psychological_status_arry"(newValues) {
            if (newValues && !newValues.includes("f")) {
                this.form_adult.psychological_status = ""
            }
        },
        "form_adult.mental_status_arry"(newValues) {
            if (newValues && !newValues.includes("b")) {
                this.form_adult.mental_status_problems = ""
            }
            if (newValues && !newValues.includes("c")) {
                this.form_adult.mental_status_violence = ""
            }
        },

        "form_adult.elimination_color_type_id"(newValues) {
            if (newValues != "3") {
                this.form_adult.elimination_color = ""
            }
        },

        //  =============== RANAP ANAK ================
        // "form_child.medical_devices_type_id"(newValue) {
        //     if (newValue != "1") {
        //         this.form_child.medical_devices = []
        //     }
        // },

        "form_child.comm_needs_speech_type_id"(newValue) {
            if (newValue != "2") {
                this.form_child.comm_needs_speech_disorder = ""
            }
        },

        "form_child.comm_needs_colloquial_arry"(newValues) {
            if (newValues && !newValues.includes("c")) {
                this.form_child.comm_needs_colloquial_local = ""
            }
            if (newValues && !newValues.includes("d")) {
                this.form_child.comm_needs_colloquial_other = ""
            }
        },

        "form_child.edu_needs_level_of_education_type_id"(newValue) {
            if (newValue != "5") {
                this.form_child.edu_needs_level_of_education_other = ""
            }
        },

        "form_child.edu_needs_barriers_type_id"(newValue) {
            if (newValue != "1") {
                this.form_child.edu_needs_barriers_arry = []
                this.form_child.edu_needs_barriers_other = ''
            }
        },

        "form_child.edu_needs_barriers_arry"(newValues) {
            if (newValues && !newValues.includes("k")) {
                this.form_child.edu_needs_barriers_other = ""
            }
        },

        "form_child.edu_needs_information_arry"(newValues) {
            if (newValues && !newValues.includes("c")) {
                this.form_child.edu_needs_information_other = ""
            }
        },

        "form_child.evaluation_smoke_type_id"(newValue) {
            if (newValue != "1") {
                this.form_child.evaluation_smoke_amount = ''
                this.form_child.evaluation_smoke_long = ''
                this.form_child.evaluation_smoke_unit = 'Tahun'
            }
        },

        "form_child.evaluation_alcohol_type_id"(newValue) {
            if (newValue != "1") {
                this.form_child.evaluation_alcohol_type = ''
                this.form_child.evaluation_alcohol_amount = ''
            }
        },

        "form_child.evaluation_sedative_type_id"(newValue) {
            if (newValue != "1") {
                this.form_child.evaluation_sedative_type = ''
                this.form_child.evaluation_sedative_amount = ''
                this.form_child.evaluation_sedative_unit = 'Tablet'
            }
        },

        "form_child.evaluation_sleep_type_id"(newValue) {
            if (newValue != "1") {
                this.form_child.evaluation_sleep = ''
            }
        },

        "form_child.evaluation_grip_type_id"(newValue) {
            if (newValue != "3") {
                this.form_child.evaluation_grip_other = ''
            }
        },

        "form_child.evaluation_coordination_type_id"(newValue) {
            if (newValue != "2") {
                this.form_child.evaluation_coordination_other = ''
            }
        },

        "form_child.evaluation_extremities_bottom_type_id"(newValue) {
            if (newValue != "7") {
                this.form_child.evaluation_extremities_bottom_bone = ''
            }
        },

        "form_child.evaluation_psychological_status_type_id"(newValue) {
            if (newValue != "7") {
                this.form_child.evaluation_psychological_status_other = ''
            }
        },

        "form_child.evaluation_elimination_defecate_type_id"(newValue) {
            if (newValue != "7") {
                this.form_child.evaluation_elimination_defecate_freq = ''
            }
        },

        "form_child.evaluation_sexual_circumcision_type_id"(newValue) {
            if (newValue != "1") {
                this.form_child.evaluation_sexual_circumcision = ''
            }
        },

        "form_child.evaluation_sexual_problem_type_id"(newValue) {
            if (newValue != "1") {
                this.form_child.evaluation_sexual_problem = ''
            }
        },

        //  =============== RANAP ANAK ================
        "form_newborn.hfb_ambu_type_id"(newValue) {
            if(newValue != 2){
                this.form_newborn.hfb_ambu_long = ''
            }
        },

        "form_newborn.hfb_heart_massage_type_id"(newValue) {
            if(newValue != 2){
                this.form_newborn.hfb_heart_massage_long = ''
            }
        },

        "form_newborn.hfb_endotracheal_tube_type_id"(newValue) {
            if(newValue != 2){
                this.form_newborn.hfb_endotracheal_tube_number = ''
                this.form_newborn.hfb_endotracheal_tube_size = ''
            }
        },

        "form_newborn.hfb_delivery_type_id"(newValue) {
            if(newValue != 4){
                this.form_newborn.hfb_delivery_other = ''
            }
        },

        "form_neona.mother_lab_result_arry"(newValues) {
            if (newValues && !newValues.includes("d")) {
                this.form_neona.mother_lab_result = ''
            }
        },

        "form_neona.birth_history_type_id"(newValue) {
            if (newValue != 3) {
                this.form_neona.birth_history_lk = ''
            }

            if (newValue != 4) {
                this.form_neona.birth_history_ld = ''
            }
        },

        "form_neona.postnatal_drug_arry"(newValues) {
            if (newValues && !newValues.includes("a")) {
                this.form_neona.postnatal_drug_vit_k = ''
            }

            if (newValues && !newValues.includes("b")) {
                this.form_neona.postnatal_drug_eye = ''
            }

            if (newValues && !newValues.includes("c")) {
                this.form_neona.postnatal_drug_other = ''
            }
        },

        "form_neona.comm_needs_speech_type_id"(newValue) {
            if (newValue != "2") {
                this.form_neona.comm_needs_speech_disorder = ""
            }
        },

        "form_neona.comm_needs_colloquial_arry"(newValues) {
            if (newValues && !newValues.includes("c")) {
                this.form_neona.comm_needs_colloquial_local = ""
            }
            if (newValues && !newValues.includes("d")) {
                this.form_neona.comm_needs_colloquial_other = ""
            }
        },

        "form_neona.edu_needs_level_of_education_type_id"(newValue) {
            if (newValue != "5") {
                this.form_neona.edu_needs_level_of_education_other = ""
            }
        },

        "form_neona.edu_needs_information_arry"(newValues) {
            if (newValues && !newValues.includes("c")) {
                this.form_neona.edu_needs_information_other = ""
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

            // neonate
            optLaborType: state => state.frontoptions.nurse.assessment.neonate.labor_type,
            optMotherBlood: state => state.frontoptions.nurse.assessment.neonate.mother_blood,
            optBirthHistory: state => state.frontoptions.nurse.assessment.neonate.birth_history,
            optResidence: state => state.frontoptions.nurse.assessment.neonate.residence,
            optCaringClient: state => state.frontoptions.nurse.assessment.neonate.caring_client,
            optSleepPattern: state => state.frontoptions.nurse.assessment.neonate.sleep_pattern,
            optIsClean: state => state.frontoptions.nurse.assessment.neonate.is_clean,
            optScrotum: state => state.frontoptions.nurse.assessment.neonate.scrotum,
            optUrine: state => state.frontoptions.nurse.assessment.neonate.urine,

            // babyborn
            optApgarPulse: state => state.frontoptions.nurse.assessment.baby_born.heart_frequency,
            optApgarRespiration: state => state.frontoptions.nurse.assessment.baby_born.respiration,
            optApgarMuscleActivity: state => state.frontoptions.nurse.assessment.baby_born.muscle_activity,
            optApgarReflectReaction: state => state.frontoptions.nurse.assessment.baby_born.reflect_reaction,
            optApgarSkinColor: state => state.frontoptions.nurse.assessment.baby_born.skin_color,
            optCaputSuccedanium: state => state.frontoptions.nurse.assessment.baby_born.caput_succedanium,
            optCephalHaematom: state => state.frontoptions.nurse.assessment.baby_born.cephal_haematom,
            optHeadBabyborn: state => state.frontoptions.nurse.assessment.baby_born.head,
            optSutura: state => state.frontoptions.nurse.assessment.baby_born.suture,
            optDiedInPregnancy: state => state.frontoptions.nurse.assessment.baby_born.died_in_pregnancy,
            optDiedInChildbirth: state => state.frontoptions.nurse.assessment.baby_born.died_in_childbirth,
            optDiedAfterBirth: state => state.frontoptions.nurse.assessment.baby_born.died_after_birth,
            optAmbu: state => state.frontoptions.nurse.assessment.baby_born.ambu,
            optHeartMassage: state => state.frontoptions.nurse.assessment.baby_born.heart_massage,
            optEndotrachealTube: state => state.frontoptions.nurse.assessment.baby_born.endotracheal_tube,
            optHfbDelivery: state => state.frontoptions.nurse.assessment.baby_born.hfb_delivery,
            optHfbMucusSuction: state => state.frontoptions.nurse.assessment.baby_born.hfb_mucus_suction,
            optHfbStimulus: state => state.frontoptions.nurse.assessment.baby_born.hfb_stimulus,
            optHfbOxygen: state => state.frontoptions.nurse.assessment.baby_born.hfb_oxygen,
            optHfbResuscitation: state => state.frontoptions.nurse.assessment.baby_born.hfb_resuscitation,
            optHfbNewbornBaby: state => state.frontoptions.nurse.assessment.baby_born.hfb_newborn_baby,
            optHfbBabyCondition: state => state.frontoptions.nurse.assessment.baby_born.hfb_baby_condition,
            optUmbilicalCord: state => state.frontoptions.nurse.assessment.baby_born.umbilical_cord_entanglement,
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

        getCalcAge(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return 18;
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return 18;
            }

            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const now = moment();
            const years = now.diff(birthDate, 'years');
            return years
        },

        async liveSubNurse(obj, gate, type_nurse_id) {
            // console.log('obj', obj);
            if (gate == 'main_gate') {
                let input_date = obj.birth_date
                if (!input_date || input_date === "0000-00-00") {
                    this.live_sub.adult = true
                    return
                }

                const parsed = moment(input_date);
                if (!parsed.isValid()) {
                    this.live_sub.adult = true
                    return
                }

                const birthDate = moment(input_date, 'YYYY-MM-DD');
                const now = moment();
                const years = now.diff(birthDate, 'years');
                const days = now.diff(birthDate, 'days');

                this.live_sub.neonatus = days <= 28
                    this.live_sub.newbornbaby = days <= 28
                this.live_sub.child = days > 28 && years <= 17
                this.live_sub.adult = years >= 18

            } else if (gate == 'edit_gate') {
                this.live_sub.neonatus = type_nurse_id == 12
                this.live_sub.newbornbaby = type_nurse_id == 12
                this.live_sub.child = type_nurse_id == 6
                this.live_sub.adult = type_nurse_id == 7
            }

            // console.log(`years: ${years}, days: ${days}`);
            // console.log(`this.live_sub:`, this.live_sub);
        },

        async parentTrigger(message) {
            this.regData = message.regData
            await this.liveSubNurse(message.regData, message.gate, message?.nurseData?.type_nurse_id)
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

            this.getObgynDoctorList()
        },

        async openMainModal(obj) {
            console.log('openMain', obj)
            if (obj.gate == 'edit_gate') {
                await this.fillNurseForm(obj.nurseData)
            } else {
                await this.resetNurseForm()
                await this.resetAdult()
                await this.resetChild()
                await this.resetNewborn()
                await this.resetNeona()

                const [v_age, v_gender] = await Promise.all([
                    this.getActualAge(),
                    this.getActualGender()
                ]);

                if (this.live_sub.neonatus || this.live_sub.child) {
                    this.$set(this.form_functional, 'age', v_age)
                    this.$set(this.form_functional, 'gender', v_gender)
                    this.calculateHumpty()
                    // console.log('Sesudah:', Object.keys(this.form_functional));
                }

                await this.cloneAllState()
            }

            this.active.section = 'ANAMNESA'
            this.active.modal.main = true
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
            if (sectionKey == 'ADULT') return 'Dewasa'
            if (sectionKey == 'CHILD') return 'Anak'
            if (sectionKey == 'NEWBORNBABY') return 'Bayi Baru Lahir'
            if (sectionKey == 'NEONATE') return 'Neonatus'
        },

        async cloneAllState() {
            this.original_anamnesa = JSON.parse(JSON.stringify(this.form_anamnesa))
            this.original_vital = JSON.parse(JSON.stringify(this.form_vital))
            this.original_functional = JSON.parse(JSON.stringify(this.form_functional))
            this.original_adult = JSON.parse(JSON.stringify(this.form_adult))
            this.original_child = JSON.parse(JSON.stringify(this.form_child))
            this.original_newborn = JSON.parse(JSON.stringify(this.form_newborn))
            this.original_neona = JSON.parse(JSON.stringify(this.form_neona))
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
                ADULT: ["form_adult", "original_adult"],
                CHILD: ["form_child", "original_child"],
                NEWBORNBABY: ["form_newborn", "original_newborn"],
                NEONATE: ["form_neona", "original_neona"],
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
                const beforeStr = before === null || before === undefined || before === '0000-00-00 00:00:00' ? "" : String(before);
                const afterStr = after === null || after === undefined || after === '0000-00-00 00:00:00' ? "" : String(after);

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
                ADULT: ["form_adult", "original_adult"],
                CHILD: ["form_child", "original_child"],
                NEWBORNBABY: ["form_newborn", "original_newborn"],
                NEONATE: ["form_neona", "original_neona"],
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
            //     ADULT: ["form_adult", "original_adult"],
            //     CHILD: ["form_child", "original_child"],
            //     NEWBORNBABY: ["form_newborn", "original_newborn"],
            //     NEONATE: ["form_neona", "original_neona"],
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
                ADULT: this.saveAdult,
                CHILD: this.saveChild,
                NEONATE: this.saveNeona,
                NEWBORNBABY: this.saveNewBornBaby,
            };

            if (!saveFunctions[exist]) return;

            if (exist == 'VITAL') {
                saveFunctions[exist](exist, destination, 'main_section');
            } else {
                saveFunctions[exist](exist, destination);
            }
        },

        getNextSection(){
            // [R] mesti direfactor kemungkinan bug
            if (this.live_sub.neonatus){
                if (this.live_sub.newbornbaby) {
                    return 'NEWBORNBABY'
                }
                return 'NEONATE'
            }
            if (this.live_sub.child) return 'CHILD'
            if (this.live_sub.adult) return 'ADULT'
            return 'FUNCTIONAL'
        },

        getTypeNurse() {
            if (this.live_sub.neonatus || this.live_sub.newbornbaby) return 12
            if (this.live_sub.child) return 6
            if (this.live_sub.adult) return 7
            return 7
        },

        getMyNurse(nurse_id){
            try {
                this.getAssessment()
                this.$store.dispatch('nurse/getNurseById', nurse_id).then((response) => {
                    console.log('getMyNurse', response)
                    if (response?.length > 0) {
                        this.fillNurseForm(response[0])
                        // console.log('response[0]', response[0]);
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
            this.form_anamnesa.type_nurse_id ||= this.getTypeNurse()

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
            this.form_vital.type_nurse_id ||= this.getTypeNurse()

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
            this.form_functional.type_nurse_id ||= this.getTypeNurse()

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

        async saveAdult(exist, destination) {
            this.form_adult.employee_nurse_id ||= this.userData.employee_id
            this.form_adult.registration_destination_id ||= this.regData.registration_destination_id
            this.form_adult.type_nurse_id ||= 7

            let fixed_data = {
                exist: exist,
                data: this.form_adult
            }

            try {
                this.active.disabled.section = true
                await this.$store.dispatch('nurse/putSections', fixed_data)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','Berhasil menyimpan asesmen dewasa')
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

        async saveChild(exist, destination) {
            this.form_child.employee_nurse_id ||= this.userData.employee_id
            this.form_child.registration_destination_id ||= this.regData.registration_destination_id
            this.form_child.type_nurse_id ||= 6

            let fixed_data = {
                exist: exist,
                data: this.form_child
            }

            try {
                this.active.disabled.section = true
                await this.$store.dispatch('nurse/putSections', fixed_data)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','Berhasil menyimpan asesmen anak')
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

        saveNewBornBaby(exist, destination) {
            this.form_newborn.employee_nurse_id ||= this.userData.employee_id
            this.form_newborn.registration_destination_id ||= this.regData.registration_destination_id
            this.form_newborn.type_nurse_id ||= 12

            let fixed_data = {
                exist: exist,
                data: this.form_newborn
            }

            try {
                this.active.disabled.section = true
                this.$store.dispatch('nurse/putSections', fixed_data)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','Berhasil menyimpan asesmen bayi baru lahir')
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

        async saveNeona(exist, destination) {
            this.form_neona.employee_nurse_id ||= this.userData.employee_id
            this.form_neona.registration_destination_id ||= this.regData.registration_destination_id
            this.form_neona.type_nurse_id ||= 12

            let fixed_data = {
                exist: exist,
                data: this.form_neona
            }

            try {
                this.active.disabled.section = true
                await this.$store.dispatch('nurse/putSections', fixed_data)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','Berhasil menyimpan asesmen neonatus')
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
            // console.log('obj nurse', obj);
            await this.resetNurseForm()
            await this.resetAdult()
            await this.resetChild()
            await this.resetNewborn()
            await this.resetNeona()

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
                    this.form_anamnesa.type_nurse_id = obj?.type_nurse_id || this.getTypeNurse()
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
                    this.form_vital.type_nurse_id = obj?.type_nurse_id || this.getTypeNurse()
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
                    this.form_functional.type_nurse_id = obj?.type_nurse_id || this.getTypeNurse()
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
                        this.calculateHumpty()
                        // console.log('this.form_functional', this.form_functional);

                    } else if (this.live_sub.adult) {
                        const morse = obj?.morse
                        this.form_functional.history_of_falls = morse?.history_of_falls
                        this.form_functional.secondary_diagnosis = morse?.secondary_diagnosis
                        this.form_functional.walking_ambulation = morse?.walking_ambulation
                        this.form_functional.side_effects_of_falling = morse?.side_effects_of_falling
                        this.form_functional.way_of_walking = morse?.way_of_walking
                        this.form_functional.mental_status = morse?.mental_status
                        this.form_functional.total_score = morse?.total_score
                        this.calculateMorse()
                    }

                    if (obj.type_nurse_id == 7) {
                        this.fillAdultForm(obj.id)
                    } else if (obj.type_nurse_id == 6) {
                        this.fillChildForm(obj.id)
                    } else if (obj.type_nurse_id == 12) {
                        this.fillNeonaForm(obj.id)
                        this.fillNewBornForm(obj.id)
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

        // ================== ADULT ==================

        fillAdultForm(nurse_id) {
            // console.log('masuk ke fill adult');
            if (nurse_id) {
                this.$store.dispatch('nurse/getAdultInpatientDetail', { nurse_id: nurse_id }).then((response) => {
                    const res = response[0]
                    this.form_adult.id = res?.nurse_id || nurse_id
                    this.form_adult.employee_nurse_id = res?.employee_nurse_id || this.userData.employee_id
                    this.form_adult.registration_destination_id = res?.registration_destination_id || this.regData.registration_destination_id
                    this.form_adult.type_nurse_id = res?.type_nurse_id || 7
                    this.form_adult.psychological_status_arry = res?.psychological_status_arry || []
                    this.form_adult.psychological_status = res?.psychological_status || ''
                    this.form_adult.mental_status_arry = res?.mental_status_arry || []
                    this.form_adult.mental_status_problems = res?.mental_status_problems || ''
                    this.form_adult.mental_status_violence = res?.mental_status_violence || ''
                    this.form_adult.elimination_defecate = res?.elimination_defecate || ''
                    this.form_adult.elimination_urination = res?.elimination_urination || ''
                    this.form_adult.elimination_consistency_type_id = res?.elimination_consistency_type_id || ''
                    this.form_adult.elimination_color_type_id = res?.elimination_color_type_id || ''
                    this.form_adult.elimination_color = res?.elimination_color || ''
                    this.form_adult.evaluation_smoke_type_id = res?.evaluation_smoke_type_id
                    this.form_adult.evaluation_smoke_amount = res?.evaluation_smoke_amount || ''
                    this.form_adult.evaluation_smoke_long = res?.evaluation_smoke_long || ''
                    this.form_adult.evaluation_smoke_unit = res?.evaluation_smoke_unit || 'Tahun'
                    this.form_adult.evaluation_alcohol_type_id = res?.evaluation_alcohol_type_id
                    this.form_adult.evaluation_alcohol_type = res?.evaluation_alcohol_type || ''
                    this.form_adult.evaluation_alcohol_amount = res?.evaluation_alcohol_amount || ''
                    this.form_adult.evaluation_sedative_type_id = res?.evaluation_sedative_type_id
                    this.form_adult.evaluation_sedative_type = res?.evaluation_sedative_type || ''
                    this.form_adult.evaluation_sedative_amount = res?.evaluation_sedative_amount || ''
                    this.form_adult.evaluation_sedative_unit = res?.evaluation_sedative_unit || 'Tablet'
                    this.form_adult.evaluation_sleep_type_id = res?.evaluation_sleep_type_id
                    this.form_adult.evaluation_sleep = res?.evaluation_sleep || ''
                    this.form_adult.evaluation_daily_type_id = res?.evaluation_daily_type_id || ''
                    this.form_adult.evaluation_activity_type_id = res?.evaluation_activity_type_id || ''
                    this.form_adult.evaluation_extremities_top_type_id = res?.evaluation_extremities_top_type_id || ''
                    this.form_adult.evaluation_grip_type_id = res?.evaluation_grip_type_id || ''
                    this.form_adult.evaluation_grip_other = res?.evaluation_grip_other || ''
                    this.form_adult.evaluation_coordination_type_id = res?.evaluation_coordination_type_id || ''
                    this.form_adult.evaluation_coordination_other = res?.evaluation_coordination_other || ''
                    this.form_adult.evaluation_extremities_bottom_type_id = res?.evaluation_extremities_bottom_type_id || ''
                    this.form_adult.evaluation_extremities_bottom_bone = res?.evaluation_extremities_bottom_bone || ''
                    this.form_adult.evaluation_ambulation_type_id = res?.evaluation_ambulation_type_id || ''
                    // this.form_adult.medical_devices_type_id = res?.medical_devices_type_id || ''
                    this.form_adult.medical_devices = res?.medical_devices || []
                    this.form_adult.discharge_treatment_duration = res?.discharge_treatment_duration || ''
                    this.form_adult.discharge_further_care_arry = res?.discharge_further_care_arry || []
                    this.form_adult.examination_history = res?.examination_history || []

                    this.cloneAllState()
                })
            }
        },

        async resetAdult() {
            this.form_adult.id = ''
            this.form_adult.employee_nurse_id = ''
            this.form_adult.registration_destination_id = ''
            this.form_adult.type_nurse_id = ''
            this.form_adult.psychological_status_arry = []
            this.form_adult.psychological_status = ''
            this.form_adult.mental_status_arry = []
            this.form_adult.mental_status_problems = ''
            this.form_adult.mental_status_violence = ''
            this.form_adult.elimination_defecate = ''
            this.form_adult.elimination_urination = ''
            this.form_adult.elimination_consistency_type_id = ''
            this.form_adult.elimination_color_type_id = ''
            this.form_adult.elimination_color = ''
            this.form_adult.evaluation_smoke_type_id = ''
            this.form_adult.evaluation_smoke_amount = ''
            this.form_adult.evaluation_smoke_long = ''
            this.form_adult.evaluation_smoke_unit = 'Tahun'
            this.form_adult.evaluation_alcohol_type_id = ''
            this.form_adult.evaluation_alcohol_type = ''
            this.form_adult.evaluation_alcohol_amount = ''
            this.form_adult.evaluation_sedative_type_id = ''
            this.form_adult.evaluation_sedative_type = ''
            this.form_adult.evaluation_sedative_amount = ''
            this.form_adult.evaluation_sedative_unit = 'Tablet'
            this.form_adult.evaluation_sleep_type_id = ''
            this.form_adult.evaluation_sleep = ''
            this.form_adult.evaluation_daily_type_id = ''
            this.form_adult.evaluation_activity_type_id = ''
            this.form_adult.evaluation_extremities_top_type_id = ''
            this.form_adult.evaluation_grip_type_id = ''
            this.form_adult.evaluation_grip_other = ''
            this.form_adult.evaluation_coordination_type_id = ''
            this.form_adult.evaluation_coordination_other = ''
            this.form_adult.evaluation_extremities_bottom_type_id = ''
            this.form_adult.evaluation_extremities_bottom_bone = ''
            this.form_adult.evaluation_ambulation_type_id = ''
            // this.form_adult.medical_devices_type_id = ''
            this.form_adult.medical_devices = []
            this.form_adult.discharge_treatment_duration = ''
            this.form_adult.discharge_further_care_arry = []
            this.form_adult.examination_history = []
        },

        addAdultMedical() {
            this.form_adult.medical_devices.push({
                type: '',
                explanation: '',
            })
        },

        remAdultMedical(index) {
            this.form_adult.medical_devices.splice(index, 1)
        },

        addAdultExamination() {
            this.form_adult.examination_history.push({
                type: '',
                page: '',
                explanation: '',
            })
        },

        remAdultExamination(index) {
            this.form_adult.examination_history.splice(index, 1)
        },

        // ================== CHILD ==================
        fillChildForm(nurse_id) {
            if (nurse_id) {
                this.$store.dispatch('nurse/getChildInpatientDetail', { nurse_id: nurse_id }).then((response) => {
                    const res = response[0]
                    this.form_child.id = res?.nurse_id || nurse_id
                    this.form_child.employee_nurse_id = res?.employee_nurse_id || this.userData.employee_id
                    this.form_child.registration_destination_id = res?.registration_destination_id || this.regData.registration_destination_id
                    this.form_child.type_nurse_id = res?.type_nurse_id || 6
                    // this.form_child.medical_devices_type_id = res?.medical_devices_type_id || ''
                    this.form_child.medical_devices = res?.medical_devices || []
                    this.form_child.growth_birth_weight = res?.growth_birth_weight || ''
                    this.form_child.growth_birth_height = res?.growth_birth_height || ''
                    this.form_child.growth_breast_milk_age = res?.growth_breast_milk_age || ''
                    this.form_child.growth_formula_milk_age = res?.growth_formula_milk_age || ''
                    this.form_child.growth_solid_food_age = res?.growth_solid_food_age || ''
                    this.form_child.growth_additional_food_age = res?.growth_additional_food_age || ''
                    this.form_child.growth_prone_age = res?.growth_prone_age || ''
                    this.form_child.growth_sit_age = res?.growth_sit_age || ''
                    this.form_child.growth_crawl_age = res?.growth_crawl_age || ''
                    this.form_child.growth_stand_age = res?.growth_stand_age || ''
                    this.form_child.growth_walk_age = res?.growth_walk_age || ''
                    this.form_child.growth_breast_milk_unit = res?.growth_breast_milk_unit || 'Bulan'
                    this.form_child.growth_formula_milk_unit = res?.growth_formula_milk_unit || 'Bulan'
                    this.form_child.growth_solid_food_unit = res?.growth_solid_food_unit || 'Bulan'
                    this.form_child.growth_additional_food_unit = res?.growth_additional_food_unit || 'Bulan'
                    this.form_child.growth_prone_unit = res?.growth_prone_unit || 'Bulan'
                    this.form_child.growth_sit_unit = res?.growth_sit_unit || 'Bulan'
                    this.form_child.growth_crawl_unit = res?.growth_crawl_unit || 'Bulan'
                    this.form_child.growth_stand_unit = res?.growth_stand_unit || 'Bulan'
                    this.form_child.growth_walk_unit = res?.growth_walk_unit || 'Bulan'
                    this.form_child.immunization_bcg_arry = res?.immunization_bcg_arry || []
                    this.form_child.immunization_hepatitis_b_arry = res?.immunization_hepatitis_b_arry || []
                    this.form_child.immunization_dpt_arry = res?.immunization_dpt_arry || []
                    this.form_child.immunization_polio_arry = res?.immunization_polio_arry || []
                    this.form_child.immunization_measles_arry = res?.immunization_measles_arry || []
                    this.form_child.immunization_hib_arry = res?.immunization_hib_arry || []
                    this.form_child.immunization_ipd_arry = res?.immunization_ipd_arry || []
                    this.form_child.immunization_flu_arry = res?.immunization_flu_arry || []
                    this.form_child.immunization_chickenpox_arry = res?.immunization_chickenpox_arry || []
                    this.form_child.immunization_mmr_arry = res?.immunization_mmr_arry || []
                    this.form_child.immunization_typhoid_arry = res?.immunization_typhoid_arry || []
                    this.form_child.immunization_hepatitis_a_arry = res?.immunization_hepatitis_a_arry || []
                    this.form_child.immunization_hpv_arry = res?.immunization_hpv_arry || []
                    this.form_child.immunization_rotavirus_arry = res?.immunization_rotavirus_arry || []
                    this.form_child.immunization_influenza_arry = res?.immunization_influenza_arry || []
                    this.form_child.examination_history = res?.examination_history || []
                    this.form_child.comm_needs_speech_type_id = res?.comm_needs_speech_type_id || ''
                    this.form_child.comm_needs_speech_disorder = res?.comm_needs_speech_disorder || ''
                    this.form_child.comm_needs_colloquial_arry = res?.comm_needs_colloquial_arry || []
                    this.form_child.comm_needs_colloquial_local = res?.comm_needs_colloquial_local || ''
                    this.form_child.comm_needs_colloquial_other = res?.comm_needs_colloquial_other || ''
                    this.form_child.comm_needs_translator_type_id = res?.comm_needs_translator_type_id
                    this.form_child.edu_needs_barriers_type_id = res?.edu_needs_barriers_type_id
                    this.form_child.edu_needs_barriers_arry = res?.edu_needs_barriers_arry || []
                    this.form_child.edu_needs_barriers_other = res?.edu_needs_barriers_other || ''
                    this.form_child.edu_needs_preferred_arry = res?.edu_needs_preferred_arry || []
                    this.form_child.edu_needs_level_of_education_type_id = res?.edu_needs_level_of_education_type_id || ''
                    this.form_child.edu_needs_level_of_education_other = res?.edu_needs_level_of_education_other || ''
                    this.form_child.edu_needs_information_arry = res?.edu_needs_information_arry || []
                    this.form_child.edu_needs_information_other = res?.edu_needs_information_other || ''
                    this.form_child.evaluation_smoke_unit = res?.evaluation_smoke_unit || 'Tahun'
                    this.form_child.evaluation_sedative_unit = res?.evaluation_sedative_unit || 'Tablet'
                    this.form_child.evaluation_smoke_type_id = res?.evaluation_smoke_type_id
                    this.form_child.evaluation_smoke_amount = res?.evaluation_smoke_amount || ''
                    this.form_child.evaluation_smoke_long = res?.evaluation_smoke_long || ''
                    this.form_child.evaluation_alcohol_type_id = res?.evaluation_alcohol_type_id
                    this.form_child.evaluation_alcohol_type = res?.evaluation_alcohol_type || ''
                    this.form_child.evaluation_alcohol_amount = res?.evaluation_alcohol_amount || ''
                    this.form_child.evaluation_sedative_type_id = res?.evaluation_sedative_type_id
                    this.form_child.evaluation_sedative_type = res?.evaluation_sedative_type || ''
                    this.form_child.evaluation_sedative_amount = res?.evaluation_sedative_amount || ''
                    this.form_child.evaluation_sleep_type_id = res?.evaluation_sleep_type_id
                    this.form_child.evaluation_sleep = res?.evaluation_sleep || ''
                    this.form_child.evaluation_daily_type_id = res?.evaluation_daily_type_id || ''
                    this.form_child.evaluation_activity_type_id = res?.evaluation_activity_type_id || ''
                    this.form_child.evaluation_extremities_top_type_id = res?.evaluation_extremities_top_type_id || ''
                    this.form_child.evaluation_grip_type_id = res?.evaluation_grip_type_id || ''
                    this.form_child.evaluation_grip_other = res?.evaluation_grip_other || ''
                    this.form_child.evaluation_extremities_bottom_type_id = res?.evaluation_extremities_bottom_type_id || ''
                    this.form_child.evaluation_extremities_bottom_bone = res?.evaluation_extremities_bottom_bone || ''
                    this.form_child.evaluation_ambulation_type_id = res?.evaluation_ambulation_type_id || ''
                    this.form_child.evaluation_mental_status_type_id = res?.evaluation_mental_status_type_id || ''
                    this.form_child.evaluation_psychological_status_type_id = res?.evaluation_psychological_status_type_id || ''
                    this.form_child.evaluation_psychological_status_other = res?.evaluation_psychological_status_other || ''
                    this.form_child.evaluation_elimination_defecate_type_id = res?.evaluation_elimination_defecate_type_id || ''
                    this.form_child.evaluation_elimination_defecate_freq = res?.evaluation_elimination_defecate_freq || ''
                    this.form_child.evaluation_elimination_urination_type_id = res?.evaluation_elimination_urination_type_id || ''
                    this.form_child.evaluation_sexual_menstruation_type_id = res?.evaluation_sexual_menstruation_type_id || ''
                    this.form_child.evaluation_sexual_circumcision_type_id = res?.evaluation_sexual_circumcision_type_id
                    this.form_child.evaluation_sexual_circumcision = res?.evaluation_sexual_circumcision || ''
                    this.form_child.evaluation_sexual_problem_type_id = res?.evaluation_sexual_problem_type_id
                    this.form_child.evaluation_sexual_problem = res?.evaluation_sexual_problem || ''

                    this.cloneAllState()
                })
            }
        },

        async resetChild() {
            this.form_child.id = ''
            this.form_child.registration_destination_id = ''
            this.form_child.employee_nurse_id = ''
            this.form_child.type_nurse_id = ''
            // this.form_child.medical_devices_type_id = ''
            this.form_child.medical_devices = []
            this.form_child.growth_birth_weight = ''
            this.form_child.growth_birth_height = ''
            this.form_child.growth_breast_milk_age = ''
            this.form_child.growth_formula_milk_age = ''
            this.form_child.growth_solid_food_age = ''
            this.form_child.growth_additional_food_age = ''
            this.form_child.growth_prone_age = ''
            this.form_child.growth_sit_age = ''
            this.form_child.growth_crawl_age = ''
            this.form_child.growth_stand_age = ''
            this.form_child.growth_walk_age = ''
            this.form_child.immunization_bcg_arry = []
            this.form_child.immunization_hepatitis_b_arry = []
            this.form_child.immunization_dpt_arry = []
            this.form_child.immunization_polio_arry = []
            this.form_child.immunization_measles_arry = []
            this.form_child.immunization_hib_arry = []
            this.form_child.immunization_ipd_arry = []
            this.form_child.immunization_flu_arry = []
            this.form_child.immunization_chickenpox_arry = []
            this.form_child.immunization_mmr_arry = []
            this.form_child.immunization_typhoid_arry = []
            this.form_child.immunization_hepatitis_a_arry = []
            this.form_child.immunization_hpv_arry = []
            this.form_child.immunization_rotavirus_arry = []
            this.form_child.immunization_influenza_arry = []
            this.form_child.examination_history = []
            this.form_child.comm_needs_speech_type_id = ''
            this.form_child.comm_needs_speech_disorder = ''
            this.form_child.comm_needs_colloquial_arry = []
            this.form_child.comm_needs_colloquial_local = ''
            this.form_child.comm_needs_colloquial_other = ''
            this.form_child.comm_needs_translator_type_id = ''
            this.form_child.edu_needs_barriers_type_id = ''
            this.form_child.edu_needs_barriers_arry = []
            this.form_child.edu_needs_barriers_other = ''
            this.form_child.edu_needs_preferred_arry = []
            this.form_child.edu_needs_level_of_education_type_id = ''
            this.form_child.edu_needs_level_of_education_other = ''
            this.form_child.edu_needs_information_arry = []
            this.form_child.edu_needs_information_other = ''
            this.form_child.evaluation_smoke_type_id = ''
            this.form_child.evaluation_smoke_amount = ''
            this.form_child.evaluation_smoke_long = ''
            this.form_child.evaluation_alcohol_type_id = ''
            this.form_child.evaluation_alcohol_type = ''
            this.form_child.evaluation_alcohol_amount = ''
            this.form_child.evaluation_sedative_type_id = ''
            this.form_child.evaluation_sedative_type = ''
            this.form_child.evaluation_sedative_amount = ''
            this.form_child.evaluation_sleep_type_id = ''
            this.form_child.evaluation_sleep = ''
            this.form_child.evaluation_daily_type_id = ''
            this.form_child.evaluation_activity_type_id = ''
            this.form_child.evaluation_extremities_top_type_id = ''
            this.form_child.evaluation_grip_type_id = ''
            this.form_child.evaluation_grip_other = ''
            this.form_child.evaluation_extremities_bottom_type_id = ''
            this.form_child.evaluation_extremities_bottom_bone = ''
            this.form_child.evaluation_ambulation_type_id = ''
            this.form_child.evaluation_mental_status_type_id = ''
            this.form_child.evaluation_psychological_status_type_id = ''
            this.form_child.evaluation_psychological_status_other = ''
            this.form_child.evaluation_elimination_defecate_type_id = ''
            this.form_child.evaluation_elimination_defecate_freq = ''
            this.form_child.evaluation_elimination_urination_type_id = ''
            this.form_child.evaluation_sexual_menstruation_type_id = ''
            this.form_child.evaluation_sexual_circumcision_type_id = ''
            this.form_child.evaluation_sexual_circumcision = ''
            this.form_child.evaluation_sexual_problem_type_id = ''
            this.form_child.evaluation_sexual_problem = ''
        },

        addChildMedical() {
            this.form_child.medical_devices.push({
                type: '',
                explanation: '',
            })
        },

        remChildMedical(index) {
            this.form_child.medical_devices.splice(index, 1)
        },

        addChildExamination() {
            this.form_child.examination_history.push({
                type: '',
                page: '',
                explanation: '',
            })
        },

        remChildExamination(index) {
            this.form_child.examination_history.splice(index, 1)
        },

        // ================== NEWBABYBORN ==================
        fillNewBornForm(nurse_id) {
            if (nurse_id) {
                this.$store.dispatch('nurse/getNewbornInpatientDetail', { nurse_id: nurse_id }).then((response) => {
                    const res = response[0]
                    // console.log(res, 'ini res fillnewbornform')
                    this.form_newborn.id = res?.nurse_id || nurse_id
                    this.form_newborn.employee_nurse_id = res?.employee_nurse_id || this.userData.employee_id
                    this.form_newborn.registration_destination_id = res?.registration_destination_id || this.regData.registration_destination_id
                    this.form_newborn.type_nurse_id = res?.type_nurse_id || 12

                    this.form_newborn.birth_order  = res?.birth_order  || ''
                    this.form_newborn.birth_hour = res?.birth_hour || ''
                    this.form_newborn.weight = res?.weight || 0
                    this.form_newborn.height  = res?.height || 0

                    this.form_newborn.apgar_score_heart_rate_i = res?.apgar_score_heart_rate_i || null
                    this.form_newborn.apgar_score_respiratory_i = res?.apgar_score_respiratory_i || null
                    this.form_newborn.apgar_score_muscle_tone_i = res?.apgar_score_muscle_tone_i || null
                    this.form_newborn.apgar_score_reflex_i = res?.apgar_score_reflex_i || null
                    this.form_newborn.apgar_score_skin_color_i = res?.apgar_score_skin_color_i || null
                    this.form_newborn.apgar_score_total_i = res?.apgar_score_total_i || null
                    this.form_newborn.apgar_score_heart_rate_ii = res?.apgar_score_heart_rate_ii || null
                    this.form_newborn.apgar_score_respiratory_ii = res?.apgar_score_respiratory_ii || null
                    this.form_newborn.apgar_score_muscle_tone_ii = res?.apgar_score_muscle_tone_ii || null
                    this.form_newborn.apgar_score_reflex_ii = res?.apgar_score_reflex_ii || null
                    this.form_newborn.apgar_score_skin_color_ii = res?.apgar_score_skin_color_ii || null
                    this.form_newborn.apgar_score_total_ii = res?.apgar_score_total_ii || null

                    this.form_newborn.conclution = res?.conclution || ''
                    this.form_newborn.external_genitalia = res?.external_genitalia || ''
                    this.form_newborn.anus = res?.anus || ''
                    this.form_newborn.meconium = res?.meconium || ''
                    this.form_newborn.miksi = res?.miksi || ''
                    this.form_newborn.vernix = res?.vernix || ''
                    this.form_newborn.caput_succedaneum_type_id = res?.caput_succedaneum_type_id || null
                    this.form_newborn.cephalohematoma_type_id = res?.cephalohematoma_type_id || null

                    this.form_newborn.head_type_id = res?.head_type_id || null
                    this.form_newborn.suture_type_id = res?.suture_type_id || null
                    this.form_newborn.large_fontanel = res?.large_fontanel || ''
                    this.form_newborn.small_fontanel = res?.small_fontanel || ''
                    this.form_newborn.died_in_pregnancy_type_id = res?.died_in_pregnancy_type_id || null
                    this.form_newborn.died_in_childbirth_type_id = res?.died_in_childbirth_type_id || null
                    this.form_newborn.died_after_birth_type_id = res?.died_after_birth_type_id || null
                    this.form_newborn.complication = res?.complication || ''
                    this.form_newborn.congenital_disorder = res?.congenital_disorder || ''
                    this.form_newborn.baby_trauma = res?.baby_trauma || ''
                    this.form_newborn.hfb_mucus_suction_type_id = res?.hfb_mucus_suction_type_id || null
                    this.form_newborn.hfb_stimulus_type_id = res?.hfb_stimulus_type_id || null
                    this.form_newborn.hfb_ambu_type_id = res?.hfb_ambu_type_id || null
                    this.form_newborn.hfb_ambu_long = res?.hfb_ambu_long || ''
                    this.form_newborn.hfb_heart_massage_type_id = res?.hfb_heart_massage_type_id || null
                    this.form_newborn.hfb_heart_massage_long = res?.hfb_heart_massage_long || ''
                    this.form_newborn.hfb_endotracheal_tube_type_id = res?.hfb_endotracheal_tube_type_id || null
                    this.form_newborn.hfb_endotracheal_tube_number = res?.hfb_endotracheal_tube_number || ''
                    this.form_newborn.hfb_endotracheal_tube_size = res?.hfb_endotracheal_tube_size || ''
                    this.form_newborn.hfb_oxygen_type_id = res?.hfb_oxygen_type_id || null
                    this.form_newborn.hfb_resuscitation_type_id = res?.hfb_resuscitation_type_id || null
                    this.form_newborn.hfb_eye_application = res?.hfb_eye_application || ''
                    this.form_newborn.hfb_umbilical_cord_application = res?.hfb_umbilical_cord_application || ''
                    this.form_newborn.hfb_umbilical_cord_wrapped = res?.hfb_umbilical_cord_wrapped || ''
                    this.form_newborn.hfb_given_medicines = res?.hfb_given_medicines || ''
                    this.form_newborn.hfb_head_size_bitemporalis = res?.hfb_head_size_bitemporalis || 0
                    this.form_newborn.hfb_head_size_biparietalis = res?.hfb_head_size_biparietalis || 0
                    this.form_newborn.hfb_head_size_fronto_occipitalis = res?.hfb_head_size_fronto_occipitalis || 0
                    this.form_newborn.hfb_head_size_mento_occipitalis = res?.hfb_head_size_mento_occipitalis || 0
                    this.form_newborn.hfb_head_size_suboccipito_bregmatica = res?.hfb_head_size_suboccipito_bregmatica || 0
                    this.form_newborn.hfb_circumference_fronto_occipitalis = res?.hfb_circumference_fronto_occipitalis || 0
                    this.form_newborn.hfb_circumference_mento_occipitalis = res?.hfb_circumference_mento_occipitalis || 0
                    this.form_newborn.hfb_circumference_suboccipito_bregmatica = res?.hfb_circumference_suboccipito_bregmatica || 0
                    this.form_newborn.hfb_chest_circumference = res?.hfb_chest_circumference || 0
                    this.form_newborn.hfb_upper_arm_circumference = res?.hfb_upper_arm_circumference || 0
                    this.form_newborn.hfb_delivery_type_id = res?.hfb_delivery_type_id || null
                    this.form_newborn.hfb_delivery_other = res?.hfb_delivery_other || ''
                    this.form_newborn.hfb_assisted_by = res?.hfb_assisted_by || ''
                    this.form_newborn.hfb_newborn_baby_type_id = res?.hfb_newborn_baby_type_id || null
                    this.form_newborn.hfb_baby_condition_type_id = res?.hfb_baby_condition_type_id || null
                    this.form_newborn.gestational_age = res?.gestational_age || 0
                    this.form_newborn.water_breaks_hour = res?.water_breaks_hour || ''
                    this.form_newborn.water_breaks_color = res?.water_breaks_color || ''
                    this.form_newborn.umbilical_cord_entanglement_type_id = res?.umbilical_cord_entanglement_type_id || null
                    this.form_newborn.special_note = res?.special_note || ''
                    this.form_newborn.baby_name = res?.baby_name || ''
                    this.form_newborn.transferred_date = res?.transferred_date || ''

                    this.getApgar('one')
                    this.getApgar('two')

                    this.cloneAllState()
                })
            }
        },

        async resetNewborn() {
            this.form_newborn.id = ''
            this.form_newborn.registration_destination_id = ''
            this.form_newborn.employee_nurse_id = ''
            this.form_newborn.type_nurse_id = ''

            this.form_newborn.birth_order = null
            this.form_newborn.birth_hour = null
            this.form_newborn.weight = ''
            this.form_newborn.height = ''

            this.form_newborn.apgar_score_heart_rate_i = null
            this.form_newborn.apgar_score_respiratory_i = null
            this.form_newborn.apgar_score_muscle_tone_i = null
            this.form_newborn.apgar_score_reflex_i = null
            this.form_newborn.apgar_score_skin_color_i = null
            this.form_newborn.apgar_score_total_i = null
            this.form_newborn.apgar_score_heart_rate_ii = null
            this.form_newborn.apgar_score_respiratory_ii = null
            this.form_newborn.apgar_score_muscle_tone_ii = null
            this.form_newborn.apgar_score_reflex_ii = null
            this.form_newborn.apgar_score_skin_color_ii = null
            this.form_newborn.apgar_score_total_ii = null

            //reset apgar score option
            this.tempApgarScore.apgar_score_one.apgar_score_heart_rate_i = null
            this.tempApgarScore.apgar_score_one.apgar_score_respiratory_i = null
            this.tempApgarScore.apgar_score_one.apgar_score_muscle_tone_i = null
            this.tempApgarScore.apgar_score_one.apgar_score_reflex_i = null
            this.tempApgarScore.apgar_score_one.apgar_score_skin_color_i = null
            this.tempApgarScore.apgar_score_two.apgar_score_heart_rate_ii = null
            this.tempApgarScore.apgar_score_two.apgar_score_respiratory_ii = null
            this.tempApgarScore.apgar_score_two.apgar_score_muscle_tone_ii = null
            this.tempApgarScore.apgar_score_two.apgar_score_reflex_ii = null
            this.tempApgarScore.apgar_score_two.apgar_score_skin_color_ii = null

            this.form_newborn.conclution = ""
            this.form_newborn.external_genitalia = ""
            this.form_newborn.anus = ""
            this.form_newborn.meconium = ""
            this.form_newborn.miksi = ""
            this.form_newborn.vernix = ""
            this.form_newborn.caput_succedaneum_type_id = null
            this.form_newborn.cephalohematoma_type_id = null
            this.form_newborn.head_type_id = null
            this.form_newborn.suture_type_id = null
            this.form_newborn.large_fontanel = ""
            this.form_newborn.small_fontanel = ""
            this.form_newborn.died_in_pregnancy_type_id = null
            this.form_newborn.died_in_childbirth_type_id = null
            this.form_newborn.died_after_birth_type_id = null
            this.form_newborn.complication = ""
            this.form_newborn.congenital_disorder = ""
            this.form_newborn.baby_trauma = ""
            this.form_newborn.hfb_mucus_suction_type_id = null
            this.form_newborn.hfb_stimulus_type_id = null
            this.form_newborn.hfb_ambu_type_id = null
            this.form_newborn.hfb_ambu_long = ""
            this.form_newborn.hfb_heart_massage_type_id = null
            this.form_newborn.hfb_heart_massage_long = ""
            this.form_newborn.hfb_endotracheal_tube_type_id = null
            this.form_newborn.hfb_endotracheal_tube_number = ""
            this.form_newborn.hfb_endotracheal_tube_size = ""
            this.form_newborn.hfb_oxygen_type_id = null
            this.form_newborn.hfb_resuscitation_type_id = null
            this.form_newborn.hfb_eye_application = ""
            this.form_newborn.hfb_umbilical_cord_application = ""
            this.form_newborn.hfb_umbilical_cord_wrapped = ""
            this.form_newborn.hfb_given_medicines = ""
            this.form_newborn.hfb_head_size_bitemporalis = ""
            this.form_newborn.hfb_head_size_biparietalis = ""
            this.form_newborn.hfb_head_size_fronto_occipitalis = ""
            this.form_newborn.hfb_head_size_mento_occipitalis = ""
            this.form_newborn.hfb_head_size_suboccipito_bregmatica = ""
            this.form_newborn.hfb_circumference_fronto_occipitalis = ""
            this.form_newborn.hfb_circumference_mento_occipitalis = ""
            this.form_newborn.hfb_circumference_suboccipito_bregmatica = ""
            this.form_newborn.hfb_chest_circumference = ""
            this.form_newborn.hfb_upper_arm_circumference = ""
            this.form_newborn.hfb_delivery_type_id = null
            this.form_newborn.hfb_delivery_other = ""
            this.form_newborn.hfb_assisted_by = ""
            this.form_newborn.hfb_newborn_baby_type_id = null
            this.form_newborn.hfb_baby_condition_type_id = null
            this.form_newborn.gestational_age = ""
            this.form_newborn.water_breaks_hour = ""
            this.form_newborn.water_breaks_color = ""
            this.form_newborn.umbilical_cord_entanglement_type_id = null
            this.form_newborn.special_note = ""
            this.form_newborn.baby_name = ""
            this.form_newborn.transferred_date = ""

        },

        // ================== NEONATUS ==================
        addNeoMedical() {
            this.form_neona.medical_devices.push({
                type: '',
                explanation: '',
            })
        },

        remNeoMedical(index) {
            this.form_neona.medical_devices.splice(index, 1)
        },

        addNeoExamination() {
            this.form_neona.examination_history.push({
                type: '',
                page: '',
                explanation: '',
            })
        },

        remNeoExamination(index) {
            this.form_neona.examination_history.splice(index, 1)
        },

        fillNeonaForm(nurse_id) {
            if (nurse_id) {
                this.$store.dispatch('nurse/getNeonatusInpatientDetail', { nurse_id: nurse_id }).then((response) => {
                    const res = response[0]
                    this.form_neona.id = res?.nurse_id || nurse_id
                    this.form_neona.employee_nurse_id = res?.employee_nurse_id || this.userData.employee_id
                    this.form_neona.registration_destination_id = res?.registration_destination_id || this.regData.registration_destination_id
                    this.form_neona.type_nurse_id = res?.type_nurse_id || 12
                    this.form_neona.medical_devices = res?.medical_devices || []
                    this.form_neona.examination_history = res?.examination_history || []
                    this.form_neona.gestational_age = res?.gestational_age || ''
                    this.form_neona.corrected_age = res?.corrected_age || ''
                    this.form_neona.type_of_delivery = res?.type_of_delivery || ''
                    this.form_neona.labor_diagnosis = res?.labor_diagnosis || ''
                    this.form_neona.mother_blood_type_id = res?.mother_blood_type_id || ''
                    this.form_neona.mother_lab_result_arry = res?.mother_lab_result_arry || []
                    this.form_neona.mother_lab_result = res?.mother_lab_result || ''
                    this.form_neona.maternal_medical_history = res?.maternal_medical_history || ''
                    this.form_neona.baby_gender = res?.baby_gender || ''
                    this.form_neona.birth_history_type_id = res?.birth_history_type_id || ''
                    this.form_neona.birth_history_lk = res?.birth_history_lk || ''
                    this.form_neona.birth_history_ld = res?.birth_history_ld || ''
                    this.form_neona.postnatal_drug_arry = res?.postnatal_drug_arry || []
                    this.form_neona.postnatal_drug_vit_k = res?.postnatal_drug_vit_k || ''
                    this.form_neona.postnatal_drug_eye = res?.postnatal_drug_eye || ''
                    this.form_neona.postnatal_drug_other = res?.postnatal_drug_other || ''
                    this.form_neona.comm_needs_speech_type_id = res?.comm_needs_speech_type_id || ''
                    this.form_neona.comm_needs_speech_disorder = res?.comm_needs_speech_disorder || ''
                    this.form_neona.comm_needs_colloquial_arry = res?.comm_needs_colloquial_arry || []
                    this.form_neona.comm_needs_colloquial_local = res?.comm_needs_colloquial_local || ''
                    this.form_neona.comm_needs_colloquial_other = res?.comm_needs_colloquial_other || ''
                    this.form_neona.comm_needs_translator_type_id = res?.comm_needs_translator_type_id
                    this.form_neona.edu_needs_level_of_education_type_id = res?.edu_needs_level_of_education_type_id || ''
                    this.form_neona.edu_needs_level_of_education_other = res?.edu_needs_level_of_education_other || ''
                    this.form_neona.edu_needs_information_arry = res?.edu_needs_information_arry || []
                    this.form_neona.edu_needs_information_other = res?.edu_needs_information_other || ''
                    this.form_neona.socio_cultural_ethnic = res?.socio_cultural_ethnic || ''
                    this.form_neona.socio_cultural_citizen = res?.socio_cultural_citizen || ''
                    this.form_neona.socio_cultural_residence_type_id = res?.socio_cultural_residence_type_id || ''
                    this.form_neona.socio_cultural_who_cares_type_id = res?.socio_cultural_who_cares_type_id || ''
                    this.form_neona.socio_cultural_parent = res?.socio_cultural_parent || ''
                    this.form_neona.socio_cultural_visit_type_id = res?.socio_cultural_visit_type_id
                    this.form_neona.socio_cultural_touch_type_id = res?.socio_cultural_touch_type_id
                    this.form_neona.socio_cultural_carry_type_id = res?.socio_cultural_carry_type_id
                    this.form_neona.socio_cultural_eye_contact_type_id = res?.socio_cultural_eye_contact_type_id
                    this.form_neona.socio_cultural_speak_type_id = res?.socio_cultural_speak_type_id
                    this.form_neona.socio_cultural_other = res?.socio_cultural_other || ''
                    this.form_neona.evaluation_comfort_arry = res?.evaluation_comfort_arry || []
                    this.form_neona.evaluation_comfort_device_type_id = res?.evaluation_comfort_device_type_id
                    this.form_neona.evaluation_comfort_other = res?.evaluation_comfort_other || ''
                    this.form_neona.evaluation_rest_pattern_type_id = res?.evaluation_rest_pattern_type_id || ''
                    this.form_neona.evaluation_vagina_type_id = res?.evaluation_vagina_type_id || ''
                    this.form_neona.evaluation_catheter_woman_type_id = res?.evaluation_catheter_woman_type_id
                    this.form_neona.evaluation_menstruation_type_id = res?.evaluation_menstruation_type_id
                    this.form_neona.evaluation_reproduction_woman_other = res?.evaluation_reproduction_woman_other || ''
                    this.form_neona.evaluation_prepuce_type_id = res?.evaluation_prepuce_type_id || ''
                    this.form_neona.evaluation_scrotum_type_id = res?.evaluation_scrotum_type_id || ''
                    this.form_neona.evaluation_catheter_man_type_id = res?.evaluation_catheter_man_type_id
                    this.form_neona.evaluation_hyposdias_type_id = res?.evaluation_hyposdias_type_id
                    this.form_neona.evaluation_reproduction_man_other = res?.evaluation_reproduction_man_other || ''
                    this.form_neona.evaluation_urination_freq = res?.evaluation_urination_freq || ''
                    this.form_neona.evaluation_urine_production = res?.evaluation_urine_production || ''
                    this.form_neona.evaluation_urine_type_id = res?.evaluation_urine_type_id || ''
                    this.form_neona.evaluation_elimination_catheter_type_id = res?.evaluation_elimination_catheter_type_id
                    this.form_neona.evaluation_dysuria_type_id = res?.evaluation_dysuria_type_id
                    this.form_neona.evaluation_elimination_other = res?.evaluation_elimination_other || ''
                    this.form_neona.evaluation_client_movement_arry = res?.evaluation_client_movement_arry || []
                    this.form_neona.evaluation_safety_other = res?.evaluation_safety_other || ''
                    this.cloneAllState()
                })
            }
        },

        async resetNeona() {
            this.form_neona.id = ''
            this.form_neona.registration_destination_id = ''
            this.form_neona.employee_nurse_id = ''
            this.form_neona.type_nurse_id = ''
            this.form_neona.medical_devices = []
            this.form_neona.examination_history = []
            this.form_neona.gestational_age = ''
            this.form_neona.corrected_age = ''
            this.form_neona.type_of_delivery = ''
            this.form_neona.labor_diagnosis = ''
            this.form_neona.mother_blood_type_id = ''
            this.form_neona.mother_lab_result_arry = []
            this.form_neona.mother_lab_result = ''
            this.form_neona.maternal_medical_history = ''
            this.form_neona.baby_gender = ''
            this.form_neona.birth_history_type_id = ''
            this.form_neona.birth_history_lk = ''
            this.form_neona.birth_history_ld = ''
            this.form_neona.postnatal_drug_arry = []
            this.form_neona.postnatal_drug_vit_k = ''
            this.form_neona.postnatal_drug_eye = ''
            this.form_neona.postnatal_drug_other = ''
            this.form_neona.comm_needs_speech_type_id = ''
            this.form_neona.comm_needs_speech_disorder = ''
            this.form_neona.comm_needs_colloquial_arry = []
            this.form_neona.comm_needs_colloquial_local = ''
            this.form_neona.comm_needs_colloquial_other = ''
            this.form_neona.comm_needs_translator_type_id = ''
            this.form_neona.edu_needs_level_of_education_type_id = ''
            this.form_neona.edu_needs_level_of_education_other = ''
            this.form_neona.edu_needs_information_arry = []
            this.form_neona.edu_needs_information_other = ''
            this.form_neona.socio_cultural_ethnic = ''
            this.form_neona.socio_cultural_citizen = ''
            this.form_neona.socio_cultural_residence_type_id = ''
            this.form_neona.socio_cultural_who_cares_type_id = ''
            this.form_neona.socio_cultural_parent = ''
            this.form_neona.socio_cultural_visit_type_id = ''
            this.form_neona.socio_cultural_touch_type_id = ''
            this.form_neona.socio_cultural_carry_type_id = ''
            this.form_neona.socio_cultural_eye_contact_type_id = ''
            this.form_neona.socio_cultural_speak_type_id = ''
            this.form_neona.socio_cultural_other = ''
            this.form_neona.evaluation_comfort_arry = []
            this.form_neona.evaluation_comfort_device_type_id = ''
            this.form_neona.evaluation_comfort_other = ''
            this.form_neona.evaluation_rest_pattern_type_id = ''
            this.form_neona.evaluation_vagina_type_id = ''
            this.form_neona.evaluation_catheter_woman_type_id = ''
            this.form_neona.evaluation_menstruation_type_id = ''
            this.form_neona.evaluation_reproduction_woman_other = ''
            this.form_neona.evaluation_prepuce_type_id = ''
            this.form_neona.evaluation_scrotum_type_id = ''
            this.form_neona.evaluation_catheter_man_type_id = ''
            this.form_neona.evaluation_hyposdias_type_id = ''
            this.form_neona.evaluation_reproduction_man_other = ''
            this.form_neona.evaluation_urination_freq = ''
            this.form_neona.evaluation_urine_production = ''
            this.form_neona.evaluation_urine_type_id = ''
            this.form_neona.evaluation_elimination_catheter_type_id = ''
            this.form_neona.evaluation_dysuria_type_id = ''
            this.form_neona.evaluation_elimination_other = ''
            this.form_neona.evaluation_client_movement_arry = []
            this.form_neona.evaluation_safety_other = ''
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
        getApgar(column){
            if (column == 'one') {
                for (let key in this.tempApgarScore.apgar_score_one) {
                    if (this.tempApgarScore.apgar_score_one.hasOwnProperty(key)) {
                        if(key == 'apgar_score_heart_rate_i'){
                            let getHeartRateI = this.optApgarPulse.filter((item => item.value == this.form_newborn[key]))
                            if(getHeartRateI.length > 0){
                                this.tempApgarScore.apgar_score_one[key] = getHeartRateI[0]
                            }
                            // console.log(getHeartRateI, 'getHeartRateI')
                        } else if(key == 'apgar_score_respiratory_i'){
                            let getRespiratoryI = this.optApgarRespiration.filter((item => item.value == this.form_newborn[key]))
                            if(getRespiratoryI.length > 0){
                                this.tempApgarScore.apgar_score_one[key] = getRespiratoryI[0]
                            }
                            // console.log(getRespiratoryI, 'getRespiratoryI')
                        } else if(key == 'apgar_score_muscle_tone_i'){
                            let getMuscleToneI = this.optApgarMuscleActivity.filter((item => item.value == this.form_newborn[key]))
                            if(getMuscleToneI.length > 0){
                                this.tempApgarScore.apgar_score_one[key] = getMuscleToneI[0]
                            }
                            // console.log(getMuscleToneI, 'getMuscleToneI')
                        } else if(key == 'apgar_score_reflex_i'){
                            let getReflexI = this.optApgarReflectReaction.filter((item => item.value == this.form_newborn[key]))
                            if(getReflexI.length > 0){
                                this.tempApgarScore.apgar_score_one[key] = getReflexI[0]
                            }
                            // console.log(getReflexI, 'getReflexI')
                        } else if(key == 'apgar_score_skin_color_i'){
                            let getSkinColorI = this.optApgarSkinColor.filter((item => item.value == this.form_newborn[key]))
                            if(getSkinColorI.length > 0){
                                this.tempApgarScore.apgar_score_one[key] = getSkinColorI[0]
                            }
                            // console.log(getSkinColorI, 'getSkinColorI')
                        }
                    }
                }
            } else if (column == 'two') {
                for (let key in this.tempApgarScore.apgar_score_two) {
                    if (this.tempApgarScore.apgar_score_two.hasOwnProperty(key)) {
                        if(key == 'apgar_score_heart_rate_ii'){
                            let getHeartRateII = this.optApgarPulse.filter((item => item.value == this.form_newborn[key]))
                            if(getHeartRateII.length > 0){
                                this.tempApgarScore.apgar_score_two[key] = getHeartRateII[0]
                            }
                            // console.log(getHeartRateII, 'getHeartRateII')
                        } else if(key == 'apgar_score_respiratory_ii'){
                            let getRespiratoryII = this.optApgarRespiration.filter((item => item.value == this.form_newborn[key]))
                            if(getRespiratoryII.length > 0){
                                this.tempApgarScore.apgar_score_two[key] = getRespiratoryII[0]
                            }
                            // console.log(getRespiratoryII, 'getRespiratoryII')
                        } else if(key == 'apgar_score_muscle_tone_ii'){
                            let getMuscleToneII = this.optApgarMuscleActivity.filter((item => item.value == this.form_newborn[key]))
                            if(getMuscleToneII.length > 0){
                                this.tempApgarScore.apgar_score_two[key] = getMuscleToneII[0]
                            }
                            // console.log(getRespiratoryII, 'getRespiratoryII')
                        } else if(key == 'apgar_score_reflex_ii'){
                            let getReflexII = this.optApgarReflectReaction.filter((item => item.value == this.form_newborn[key]))
                            if(getReflexII.length > 0){
                                this.tempApgarScore.apgar_score_two[key] = getReflexII[0]
                            }
                            // console.log(getReflexII, 'getReflexII')
                        } else if(key == 'apgar_score_skin_color_ii'){
                            let getSkinColorII = this.optApgarSkinColor.filter((item => item.value == this.form_newborn[key]))
                            if(getSkinColorII.length > 0){
                                this.tempApgarScore.apgar_score_two[key] = getSkinColorII[0]
                            }
                            // console.log(getSkinColorII, 'getSkinColorII')
                        }
                    }
                }
            }
        },
        updateCount(column) {
            if (column == 'one') {
                for (let key in this.tempApgarScore.apgar_score_one) {
                    if (this.tempApgarScore.apgar_score_one.hasOwnProperty(key)) {
                        this.form_newborn[key] = this.tempApgarScore.apgar_score_one[key]?.value;
                    }
                }
                this.form_newborn.apgar_score_total_i = this.countSelections(this.tempApgarScore.apgar_score_one);
            } else if (column == 'two') {
                for (let key in this.tempApgarScore.apgar_score_two) {
                    if (this.tempApgarScore.apgar_score_two.hasOwnProperty(key)) {
                        this.form_newborn[key] = this.tempApgarScore.apgar_score_two[key]?.value;
                    }
                }
                this.form_newborn.apgar_score_total_ii = this.countSelections(this.tempApgarScore.apgar_score_two);
            }
        },

        countSelections(apgarScore) {
            let count = 0;
            for (const key in apgarScore) {
                // console.log(apgarScore[key], 'updateCount')
                if (apgarScore[key]?.subValue != null) {
                    count += apgarScore[key]?.subValue;
                }
            }
            return count;
        },
        getObgynDoctorList() {
            try {
                this.$store.dispatch('employee/getDoctorOptions', { health_facility_item_id: "0fd51271-a0e5-4db2-bc6e-12064508e82b" }).then((response) => {
                    // console.log('getobgyndoctorlist', response)
                    this.doctorObygnList = response
                }, error => {
                    console.error('Error while GET employee/getDoctorList general at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET employee/getDoctorList general at: ', error);
            }
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