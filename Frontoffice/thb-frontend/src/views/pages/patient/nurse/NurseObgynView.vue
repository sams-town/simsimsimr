<template>
    <div>
        <b-modal v-model="active.modal.main" scrollable size="xl" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ASESMEN PERAWAT KEBIDANAN
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
                                            <b-collapse :id="`accordion-ns-`+item.id" accordion="my-accordion" role="tabpanel">
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
                            <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-2" @click="changeTab('OBGYN')" :variant="getMenusVariant('OBGYN')">KEBIDANAN</b-button>
                            <b-button style="font-size: 11px; border: 0px;" class="font-weight-bold btn-block text-white mt-2" @click="changeTab('SPECIALOBG')" :variant="getMenusVariant('SPECIALOBG')">KEHAMILAN</b-button>
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
                                <b-button @click="changeSection(active.section, 'OBGYN')" :disabled="active.disabled.section" variant="orange-500">
                                    <template>
                                        Simpan & lanjutkan
                                        <feather-icon icon="ChevronRightIcon" />
                                    </template>
                                </b-button>
                            </b-button-group>
                        </div>
                    </section>

                    <section v-if="active.section == 'OBGYN'" class="card">
                        <div class="card-header bg-primary text-white fs-14 font-weight-bold">KEBIDANAN</div>
                        <div class="card-body px-20 py-8 position-sticky" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <section>
                                <ModernLabel label="Asesmen Keperawatan - Anamnesa" />
                                <section class="mt-8">
                                    <label class="font-weight-bold fs-13 mb-0">ALAT KESEHATAN YANG TERPASANG</label>
                                    <b-table-simple bordered small fixed class="text-center">
                                        <b-tr class="fs-13 ">
                                            <b-th class="align-middle" style="width: 40px;">
                                                <b-button @click="addObgMedical" variant="primary" size="sm">
                                                    <feather-icon icon="PlusIcon" />
                                                </b-button>
                                            </b-th>
                                            <b-th class="align-middle" style="width: 40px;">No</b-th>
                                            <b-th class="align-middle text-left">Jenis</b-th>
                                            <b-th class="align-middle text-left">Keterangan</b-th>
                                        </b-tr>
                                        <b-tr v-for="(item, index) in form_obgyn.medical_devices" class="fs-12">
                                            <b-td>
                                                <b-button @click="remObgMedical(index)" variant="outline-danger-500" size="sm">
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
                                                <b-button @click="addObgExamination" variant="primary" size="sm">
                                                    <feather-icon icon="PlusIcon" />
                                                </b-button>
                                            </b-th>
                                            <b-th class="align-middle" style="width: 40px;">No</b-th>
                                            <b-th class="align-middle text-left">Jenis Pemeriksaan</b-th>
                                            <b-th class="align-middle text-left">Lembar</b-th>
                                            <b-th class="align-middle text-left">Keterangan</b-th>
                                        </b-tr>
                                        <b-tr v-for="(item, index) in form_obgyn.examination_history" class="fs-12">
                                            <b-td>
                                                <b-button @click="remObgExamination(index)" variant="outline-danger-500" size="sm">
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
                                    <label class="text-uppercase font-weight-bold fs-13 mb-0">KEBUTUHAN KOMUNIKASI & EDUKASI </label>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label class="fs-13 mb-0">Bahasa sehari-hari</label>
                                            <b-form-checkbox-group v-model="form_obgyn.comm_needs_colloquial_arry" stacked size="sm">
                                                <b-form-checkbox value="a" class="my-1">Indonesia, aktif / pasif</b-form-checkbox>
                                                <b-form-checkbox value="b" class="my-1 my-lg-auto">Inggris, aktif / pasif</b-form-checkbox>
                                                <b-form-checkbox value="c" class="my-1 my-lg-auto">Daerah, sebutkan</b-form-checkbox>
                                                <template v-if="form_obgyn.comm_needs_colloquial_arry.indexOf('c') !== -1">
                                                    <b-form-input v-model="form_obgyn.comm_needs_colloquial_local" size="sm" class="mt-1" placeholder="sebutkan bahasa daerah"/>
                                                </template>
                                                <b-form-checkbox value="d" class="my-1">Lain-lain, sebutkan</b-form-checkbox>
                                                <template v-if="form_obgyn.comm_needs_colloquial_arry.indexOf('d') !== -1">
                                                    <b-form-input v-model="form_obgyn.comm_needs_colloquial_other" size="sm" class="mt-1" placeholder="sebutkan bahasa lainnya"/>
                                                </template>
                                            </b-form-checkbox-group>
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <div>
                                                <label class="fs-13 mb-0">Bicara</label>
                                                <v-select v-model="form_obgyn.comm_needs_speech_type_id" :options="OptionsTalk" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                            </div>
                                            <template v-if="form_obgyn.comm_needs_speech_type_id == 2">
                                                <div class="mt-8">
                                                    <label class="fs-13 mb-0">Gangguan bicara sejak</label>
                                                    <b-form-input size="sm" v-model="form_obgyn.comm_needs_speech_disorder" autocomplete="off" />                                                
                                                </div>
                                            </template>
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Perlu penerjemah</label>
                                            <v-select v-model="form_obgyn.comm_needs_translator_type_id" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>
                                    </div>
                                </section>
                            </section>

                            <section class="mt-20">
                                <ModernLabel label="Penilaian Per Sistem" />
                                <section class="row mt-8">
                                    <div class="col-lg-3" style="border-right: 1px solid black;">
                                        <label class="text-uppercase font-weight-bold fs-13">Riwayat merokok: </label>
                                        <div >
                                            <v-select v-model="form_obgyn.evaluation_smoke_type_id" class="select-size-sm" :options="OptionsIsAny" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            <template v-if="form_obgyn.evaluation_smoke_type_id == 1">
                                                <div>
                                                    <label class="fs-13 mb-0">Jumlah per hari</label>
                                                    <b-input-group append="Batang" size="sm">
                                                        <b-form-input v-model="form_obgyn.evaluation_smoke_amount" type="number" autocomplete="off" />
                                                    </b-input-group>
                                                </div>
                                                <div>
                                                    <label class="fs-13 mb-0">Lamanya</label>
                                                    <b-input-group size="sm">
                                                        <b-form-input v-model="form_obgyn.evaluation_smoke_long" type="number" min="0" autocomplete="off" />
                                                        <b-input-group-append>
                                                            <b-form-select v-model="form_obgyn.evaluation_smoke_unit" :options="['Tahun', 'Bulan']" size="sm" />
                                                        </b-input-group-append>
                                                    </b-input-group>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 mt-8 mt-lg-0" style="border-right: 1px solid black;">
                                        <label class="text-uppercase font-weight-bold fs-13">Riwayat minuman keras: </label>
                                        <div >
                                            <v-select v-model="form_obgyn.evaluation_alcohol_type_id" class="select-size-sm" :options="OptionsIsAny" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            <template v-if="form_obgyn.evaluation_alcohol_type_id == 1">
                                                <div>
                                                    <label class="fs-13 mb-0">Jenis</label>
                                                    <b-form-input v-model="form_obgyn.evaluation_alcohol_type" size="sm" autocomplete="off" />
                                                </div>
                                                <div>
                                                    <label class="fs-13 mb-0">Jumlah per hari</label>
                                                    <b-input-group append="Botol" size="sm">
                                                        <b-form-input v-model="form_obgyn.evaluation_alcohol_amount" type="number" autocomplete="off" />
                                                    </b-input-group>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="col-lg-3" style="border-right: 1px solid black;">
                                        <label class="text-uppercase font-weight-bold fs-13">Riwayat obat penenang: </label>
                                        <div >
                                            <v-select v-model="form_obgyn.evaluation_sedative_type_id" class="select-size-sm" :options="OptionsIsAny" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            <template v-if="form_obgyn.evaluation_sedative_type_id == 1">
                                                <div>
                                                    <label class="fs-13 mb-0">Jenis</label>
                                                    <b-form-input v-model="form_obgyn.evaluation_sedative_type" size="sm" autocomplete="off" />
                                                </div>
                                                <div>
                                                    <label class="fs-13 mb-0">Jumlah per hari</label>
                                                    <b-input-group size="sm">
                                                        <b-form-input v-model="form_obgyn.evaluation_sedative_amount" type="number" min="0" autocomplete="off" />
                                                        <b-input-group-append>
                                                            <b-form-select v-model="form_obgyn.evaluation_sedative_unit" :options="['Tablet', 'Kapsul', 'Butir', 'mg', 'ml']" size="sm" />
                                                        </b-input-group-append>
                                                    </b-input-group>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="text-uppercase font-weight-bold fs-13">Gangguan pola tidur: </label>
                                        <div >
                                            <v-select v-model="form_obgyn.evaluation_sleep_type_id" class="select-size-sm" :options="OptionsIsAny" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            <template v-if="form_obgyn.evaluation_sleep_type_id == 1">
                                                <div>
                                                    <label class="fs-13 mb-0">Jenis</label>
                                                    <b-form-input v-model="form_obgyn.evaluation_sleep" size="sm" autocomplete="off" />
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </section>

                                <div class="mt-16">
                                    <label class="text-uppercase font-weight-bold fs-13 mb-0">Asesmen fungsi: </label>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label class="fs-13 mb-0">Kemampuan aktivitas harian</label>
                                            <v-select v-model="form_obgyn.evaluation_daily_type_id" class="select-size-sm" :options="OptionsEvalDaily" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Aktivitas</label>
                                            <v-select v-model="form_obgyn.evaluation_activity_type_id" class="select-size-sm" :options="OptionsEvalActivity" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Alat Ambulasi</label>
                                            <v-select v-model="form_obgyn.evaluation_ambulation_type_id" class="select-size-sm" :options="OptionsEvalAmbulation" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Ekstremitas Atas</label>
                                            <v-select v-model="form_obgyn.evaluation_extremities_top_type_id" class="select-size-sm" :options="OptionsEvalTopExtremity" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Ekstremitas Bawah</label>
                                            <v-select v-model="form_obgyn.evaluation_extremities_bottom_type_id" class="select-size-sm" :options="OptionsEvalBotExtremity" :reduce="item => item.value" label="title" placeholder="pilih" />
                                            <div v-if="form_obgyn.evaluation_extremities_bottom_type_id == 7" class="mt-1">
                                                <b-form-input v-model="form_obgyn.evaluation_extremities_bottom_bone" size="sm" placeholder="isikan riwayat" />
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Kemampuan Menggenggam</label>
                                            <v-select v-model="form_obgyn.evaluation_grip_type_id" class="select-size-sm" :options="OptionsEvalGrip" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div v-if="form_obgyn.evaluation_grip_type_id == 3" class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Menggenggam lainnya</label>
                                            <b-form-input v-model="form_obgyn.evaluation_grip_other" size="sm" placeholder="isikan lainnya" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Kemampuan koordinasi</label>
                                            <v-select v-model="form_obgyn.evaluation_coordination_type_id" class="select-size-sm" :options="OptionsEvalCoordination" :reduce="item => item.value" label="title" placeholder="pilih" />
                                        </div>
                                        <div v-if="form_obgyn.evaluation_coordination_type_id == 2" class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Masalah koordinasi</label>
                                            <b-form-input v-model="form_obgyn.evaluation_coordination_other" size="sm" placeholder="isikan masalah" />
                                        </div>
                                    </div>                                    
                                </div>

                                <section class="mt-16">
                                    <label class="text-uppercase font-weight-bold fs-13 mb-0">Keadaan Mental dan Psikologis: </label>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label class="fs-13 mb-0">Status Mental</label>
                                            <v-select v-model="form_obgyn.evaluation_mental_status_type_id" :options="OptionsChildMental" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Status Psikologis</label>
                                            <v-select v-model="form_obgyn.evaluation_psychological_status_type_id" :options="OptionsChildPsychological" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>
                                        <template v-if="form_obgyn.evaluation_psychological_status_type_id == 7">
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Status Psikologis Lain</label>
                                                <b-form-input v-model="form_obgyn.evaluation_psychological_status_other" size="sm" />
                                            </div>
                                        </template>
                                    </div>
                                </section>

                                <section class="mt-16">
                                    <label class="text-uppercase font-weight-bold fs-13 mb-0">Eliminasi: </label>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label class="fs-13 mb-0">BAK</label>
                                            <v-select v-model="form_obgyn.evaluation_elimination_urination_type_id" :options="OptionsChildBak" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">BAB</label>
                                            <v-select v-model="form_obgyn.evaluation_elimination_defecate_type_id" :options="OptionsChildBab" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>
                                        <template v-if="form_obgyn.evaluation_elimination_defecate_type_id == 7">
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Frekuensi Diare</label>
                                                <b-input-group size="sm" append="x/hari">
                                                    <b-form-input v-model="form_obgyn.evaluation_elimination_defecate_freq" type="number" />
                                                </b-input-group>
                                            </div>
                                        </template>
                                    </div>
                                </section>

                                <section class="mt-16">
                                    <label class="text-uppercase font-weight-bold fs-13 mb-0">Seksual / Reproduksi: </label>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label class="fs-13 mb-0">Haid</label>
                                            <v-select v-model="form_obgyn.evaluation_sexual_menstruation_type_id" :options="OptionsIsDone" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Sirkumsisi</label>
                                            <v-select v-model="form_obgyn.evaluation_sexual_circumcision_type_id" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>
                                        <template v-if="form_obgyn.evaluation_sexual_circumcision_type_id == 1">
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Isikan Sirkumsisi</label>
                                                <b-form-input v-model="form_obgyn.evaluation_sexual_circumcision" size="sm" />
                                            </div>
                                        </template>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Riwayat obstetic & ginekologi lain</label>
                                            <b-form-input v-model="form_obgyn.evaluation_sexual_obstetrics_gynecology" size="sm" />
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Riwayat kontrasepsi</label>
                                            <b-form-input v-model="form_obgyn.evaluation_sexual_history_contraception" size="sm" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Status perkawinan</label>
                                            <v-select v-model="form_obgyn.evaluation_sexual_marital_status_type_id" :options="optMarried" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>
                                        <div v-if="form_obgyn.evaluation_sexual_marital_status_type_id == 1" class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Pernikahan ke</label>
                                            <b-form-input v-model="form_obgyn.evaluation_sexual_marital_status" size="sm" type="number" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Ada Masalah lain</label>
                                            <v-select v-model="form_obgyn.evaluation_sexual_problem_type_id" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>
                                        <div v-if="form_obgyn.evaluation_sexual_problem_type_id == 1" class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Masalah lain</label>
                                            <b-form-input v-model="form_obgyn.evaluation_sexual_problem" size="sm" placeholder="isikan masalah lainnya" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Kebiasaan lain</label>
                                            <b-form-input v-model="form_obgyn.evaluation_sexual_other_habits" size="sm" />
                                        </div>

                                        <div class="col-12 mt-12">
                                            <b-table-simple bordered small fixed class="text-center">
                                                <b-tr class="fs-12 ">
                                                    <b-th rowspan="2" class="align-middle" style="width: 40px;">
                                                        <b-button @click="addObgBirth" variant="primary" size="sm">
                                                            <feather-icon icon="PlusIcon" />
                                                        </b-button>
                                                    </b-th>
                                                    <b-th rowspan="2" class="align-middle" style="width: 40px;">No</b-th>
                                                    <b-th rowspan="2" class="align-middle">Tahun</b-th>
                                                    <b-th rowspan="2" class="align-middle">lama kelahiran</b-th>
                                                    <b-th rowspan="2" class="align-middle">Komplikasi kehamilan</b-th>
                                                    <b-th colspan="3" class="align-middle">Persalinan</b-th>
                                                    <b-th colspan="3" class="align-middle">Bayi / anak</b-th>
                                                    <b-th rowspan="2" class="align-middle">Pemberian asi</b-th>
                                                </b-tr>
                                                <b-tr class="fs-12 ">
                                                    <b-th class="align-middle">Spontan</b-th>
                                                    <b-th class="align-middle">Tindakan</b-th>
                                                    <b-th class="align-middle">Komplikasi</b-th>
                                                    <b-th class="align-middle">Kelamin</b-th>
                                                    <b-th class="align-middle">Berat</b-th>
                                                    <b-th class="align-middle">keaadaan</b-th>
                                                </b-tr>
                                                <b-tr v-for="(item, index) in form_obgyn.birth_history" class="fs-12">
                                                    <b-td>
                                                        <b-button @click="remObgBirth(index)" variant="outline-danger-500" size="sm">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </b-td>
                                                    <b-td class="align-middle">{{ index+1 }}</b-td>
                                                    <b-td>
                                                        <b-form-input size="sm" v-model="item.year" autocomplete="off" />                                                
                                                    </b-td>
                                                    <b-td>
                                                        <b-form-input size="sm" v-model="item.birth_time" autocomplete="off" />                                                
                                                    </b-td>
                                                    <b-td>
                                                        <b-form-input size="sm" v-model="item.pregnancy_complications" autocomplete="off" />                                                
                                                    </b-td>
                                                    <b-td>
                                                        <b-form-input size="sm" v-model="item.spontaneous" autocomplete="off" />                                                
                                                    </b-td>
                                                    <b-td>
                                                        <b-form-input size="sm" v-model="item.action" autocomplete="off" />                                                
                                                    </b-td>
                                                    <b-td>
                                                        <b-form-input size="sm" v-model="item.complications" autocomplete="off" />                                                
                                                    </b-td>
                                                    <b-td>
                                                        <b-form-input size="sm" v-model="item.sex" autocomplete="off" />                                                
                                                    </b-td>
                                                    <b-td>
                                                        <b-form-input size="sm" v-model="item.weight" autocomplete="off" />                                                
                                                    </b-td>
                                                    <b-td>
                                                        <b-form-input size="sm" v-model="item.condition" autocomplete="off" />                                                
                                                    </b-td>
                                                    <b-td>
                                                        <b-form-input size="sm" v-model="item.breastfeeding" autocomplete="off" />                                                
                                                    </b-td>
                                                </b-tr>
                                            </b-table-simple>
                                        </div>
                                    </div>
                                </section>
                            </section>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <b-button-group size="sm">
                                <b-button @click="changeTab('FUNCTIONAL')" :disabled="active.disabled.section" variant="outline-neutral-700">
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button @click="changeSection(active.section, 'SPECIALOBG')" :disabled="active.disabled.section" variant="orange-500">
                                    <template>
                                        Simpan & lanjutkan
                                        <feather-icon icon="ChevronRightIcon" />
                                    </template>
                                </b-button>
                            </b-button-group>
                        </div>
                    </section>

                    <section v-if="active.section == 'SPECIALOBG'" class="card">
                        <div class="card-header bg-primary text-white fs-14 font-weight-bold">KEHAMILAN & AWAL PERSALINAN</div>
                        <div class="card-body px-20 py-8 position-sticky" :style="{top: '0', height: `calc(${dynamicHeight} - ${differenceHeight})`, overflowY: 'auto'}">
                            <section>
                                <ModernLabel label="Anamnesis (kehamilan sekarang)" />
                                <div class="row mt-8">
                                    <div class="col-lg-3">
                                        <label class="fs-13 mb-0">G</label>
                                        <b-form-input v-model="form_specialobg.anamnesis_g" size="sm" />
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">P</label>
                                        <b-form-input v-model="form_specialobg.anamnesis_p" size="sm" />
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">A</label>
                                        <b-form-input v-model="form_specialobg.anamnesis_a" size="sm" />
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="fs-13 mb-0">H</label>
                                        <b-form-input v-model="form_specialobg.anamnesis_h" size="sm" />
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Hari pertama haid terakhir</label>
                                        <b-form-input v-model="form_specialobg.anamnesis_period" size="sm" />
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Pemeriksaan antenatal</label>
                                        <v-select v-model="form_specialobg.anamnesis_antenatal_type_id" :options="optAntenatal" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                    </div> 
                                    <div class="col-lg-3 mt-8">
                                        <div>
                                            <label class="fs-13 mb-0">Ketuban</label>
                                            <v-select v-model="form_specialobg.anamnesis_amniotic_fluid_type_id" :options="optAmniotic" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>
                                        <template v-if="form_specialobg.anamnesis_amniotic_fluid_date == 1">
                                            <div class="mt-1">
                                                <label class="fs-13 mb-0">Sejak tanggal</label>
                                                <b-form-input v-model="form_specialobg.anamnesis_his_date" type="datetime-local" size="sm" />                                            
                                            </div>
                                            <div class="mt-1">
                                                <label class="fs-13 mb-0">Warna</label>
                                                <v-select v-model="form_specialobg.anamnesis_amniotic_fluid_color_type_id" :options="optAmnioticColor" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                            </div>
                                            <div v-if="form_specialobg.anamnesis_amniotic_fluid_color_type_id == 5" class="mt-1">
                                                <label class="fs-13 mb-0">Sejak tanggal</label>
                                                <b-form-input v-model="form_specialobg.anamnesis_amniotic_fluid_color_other" size="sm" />                                            
                                            </div>
                                        </template>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="fs-13 mb-0">Blood slym</label>
                                        <v-select v-model="form_specialobg.anamnesis_blood_slym_type_id" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                    </div>
                                    <div class="col-lg-3 mt-8" style="border-right: 1px solid black;">
                                        <div>
                                            <label class="fs-13 mb-0">His</label>
                                            <v-select v-model="form_specialobg.anamnesis_his_type_id" :options="optHis" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>
                                        <div v-if="form_specialobg.anamnesis_his_type_id == 1" class="mt-1">
                                            <label class="fs-13 mb-0">Sejak tanggal</label>
                                            <b-form-input v-model="form_specialobg.anamnesis_his_date" type="datetime-local" size="sm" />                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mt-8" style="border-right: 1px solid black;">
                                        <div>
                                            <label class="fs-13 mb-0">Kejang </label>
                                            <v-select v-model="form_specialobg.anamnesis_seizure_type_id" :options="optSeizure" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>
                                        <template v-if="form_specialobg.anamnesis_seizure_type_id == 4">
                                            <div class="mt-1">
                                                <label class="fs-13 mb-0">Lamanya</label>
                                                <b-form-input v-model="form_specialobg.anamnesis_seizure_clonic_long"  size="sm" />                                            
                                            </div>
                                        </template>
                                    </div>
                                    <div class="col-lg-3 mt-8" style="border-right: 1px solid black;">
                                        <div>
                                            <label class="fs-13 mb-0">Rasa mengejan</label>
                                            <v-select v-model="form_specialobg.anamnesis_straining_type_id" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>
                                        <template v-if="form_specialobg.anamnesis_straining_type_id == 1">
                                            <div class="mt-1">
                                                <label class="fs-13 mb-0">Sejak tanggal</label>
                                                <b-form-input v-model="form_specialobg.anamnesis_straining_date" type="datetime-local" size="sm" />                                            
                                            </div>
                                            <div class="mt-1">
                                                <label class="fs-13 mb-0">Banyaknya</label>
                                                <b-form-input v-model="form_specialobg.anamnesis_straining_amount" size="sm" />                                            
                                            </div>
                                        </template>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <div>
                                            <label class="fs-13 mb-0">Pendarahan antepartum</label>
                                            <v-select v-model="form_specialobg.anamnesis_antepartum_type_id" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>
                                        <template v-if="form_specialobg.anamnesis_antepartum_type_id == 1">
                                            <div class="mt-1">
                                                <label class="fs-13 mb-0">Tanggal</label>
                                                <b-form-input v-model="form_specialobg.anamnesis_antepartum_date" type="datetime-local" size="sm" />                                            
                                            </div>
                                            <div class="mt-1">
                                                <label class="fs-13 mb-0">Banyaknya</label>
                                                <b-form-input v-model="form_specialobg.anamnesis_antepartum_amount" size="sm" />                                            
                                            </div>
                                        </template>
                                    </div>
                                    <div class="col-12 mt-8">
                                        <label class="fs-13 mb-0">Penyakit selama kehamilan</label>
                                        <b-form-checkbox-group v-model="form_specialobg.anamnesis_disease_pregnancy_arry" size="sm">
                                            <b-form-checkbox value="a" class="my-1">Anemia</b-form-checkbox>
                                            <b-form-checkbox value="b" class="my-1 my-lg-auto">Hipertensi</b-form-checkbox>
                                            <b-form-checkbox value="c" class="my-1 my-lg-auto">Kelainan jantung</b-form-checkbox>
                                            <b-form-checkbox value="d" class="my-1 my-lg-auto">Diabetes</b-form-checkbox>
                                            <b-form-checkbox value="e" class="my-1">
                                                <div>Lain-lain</div>
                                                <template v-if="form_specialobg.anamnesis_disease_pregnancy_arry.indexOf('e') !== -1">
                                                    <b-form-input v-model="form_specialobg.anamnesis_disease_pregnancy_other" size="sm" class="mt-1" placeholder="sebutkan lainnya"/>
                                                </template>
                                            </b-form-checkbox>
                                        </b-form-checkbox-group>
                                    </div>
                                    <div class="col-12 mt-8">
                                        <label class="fs-13 mb-0">Masalah lain</label>
                                        <textarea v-model.lazy="form_specialobg.anamnesis_other_problem" class="form-control form-control-sm" autocomplete="off" />
                                    </div>
                                </div>
                            </section>

                            <section class="mt-16">
                                <ModernLabel label="Pemeriksaan Fisik" />
                                <div class="mt-8">
                                    <label class="font-weight-bold fs-13 mb-0">STATUS OBSTETRIK: </label>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label class="fs-13 mb-0">Leopold 1</label>
                                            <b-form-input v-model="form_specialobg.physical_leopold_one" size="sm" />
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Leopold 2</label>
                                            <v-select v-model="form_specialobg.physical_leopold_two_type_id" :options="optLeopold2" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Leopold 3</label>
                                            <v-select v-model="form_specialobg.physical_leopold_three_type_id" :options="optLeopold3" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>
                                        <template v-if="form_specialobg.physical_leopold_three_type_id == 3">
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <b-form-input v-model="form_specialobg.physical_leopold_three_other" size="sm" placeholder="isikan lainnya"/>
                                            </div>
                                        </template>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Leopold 4</label>
                                            <v-select v-model="form_specialobg.physical_leopold_four_type_id" :options="optLeopold4" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-16">
                                    <label class="font-weight-bold fs-13 mb-0">DENYUT JANTUNG JANIN: </label>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label class="fs-13 mb-0">Frekuensi</label>
                                            <b-input-group size="sm" append="x/mnt">
                                                <b-form-input v-model="form_specialobg.physical_fetal_heart_rate_freq" type="number" autocomplete="off" />
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Irama</label>
                                            <v-select v-model="form_specialobg.physical_fetal_heart_rate_rhythm_type_id" :options="optHeartRhythm" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="mt-16">
                                    <label class="font-weight-bold fs-13 mb-0">VULVA: </label>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label class="fs-13 mb-0">Vulva</label>
                                            <v-select v-model="form_specialobg.physical_vulva_type_id" :options="optVulva" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>  
                                        <div v-if="form_specialobg.physical_vulva_type_id == 4" class="col-lg-3 mt-lg-0">
                                            <label class="fs-13 mb-0">Lain-lain</label>
                                            <b-form-input v-model="form_specialobg.physical_vulva_other" size="sm" />
                                        </div>
                                    </div>
                                </div> 
                                <div class="mt-16">
                                    <label class="font-weight-bold fs-13 mb-0">PEMERIKSAAN DALAM (bila terjadi tanda-tanda impart): </label>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label class="fs-13 mb-0">Portio</label>
                                            <v-select v-model="form_specialobg.physical_internal_portio_type_id" :options="optPortio" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>  
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Pembukaan</label>
                                            <v-select v-model="form_specialobg.physical_internal_opening_type_id" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div> 
                                        <div v-if="form_specialobg.physical_internal_opening_type_id == 1" class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Lebar pembukaan</label>
                                            <b-input-group size="sm" append="cm">
                                                <b-form-input v-model="form_specialobg.physical_internal_opening_cm" type="number" autocomplete="off" />
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Penipisan</label>
                                            <b-input-group size="sm" append="%">
                                                <b-form-input v-model="form_specialobg.physical_internal_depletion_percent" type="number" autocomplete="off" />
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Penurunan bagian terendah</label>
                                            <v-select v-model="form_specialobg.physical_internal_lowest_part_drop_type_id" :options="optLowestDrop" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div> 
                                        <template v-if="form_specialobg.physical_internal_lowest_part_drop_type_id == 1">
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Hodge 1</label>
                                                <b-form-input v-model="form_specialobg.physical_internal_lowest_part_drop_hodge_one" size="sm" />
                                            </div>
                                        </template>
                                        <template v-if="form_specialobg.physical_internal_lowest_part_drop_type_id == 2">
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Hodge 2</label>
                                                <b-form-input v-model="form_specialobg.physical_internal_lowest_part_drop_hodge_two" size="sm" />
                                            </div>
                                        </template>
                                        <template v-if="form_specialobg.physical_internal_lowest_part_drop_type_id == 3">
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Hodge 3</label>
                                                <b-form-input v-model="form_specialobg.physical_internal_lowest_part_drop_hodge_three" size="sm" />
                                            </div>
                                        </template>
                                        <template v-if="form_specialobg.physical_internal_lowest_part_drop_type_id == 4">
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Hodge 4</label>
                                                <b-form-input v-model="form_specialobg.physical_internal_lowest_part_drop_hodge_four" size="sm" />
                                            </div>
                                        </template>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Ketuban</label>
                                            <v-select v-model="form_specialobg.physical_internal_amniotic_type_id" :options="optAmnioticInternal" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div> 
                                        <template v-if="form_specialobg.physical_internal_amniotic_type_id == 1">
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Warna Ketuban</label>
                                                <b-form-input v-model="form_specialobg.physical_internal_amniotic_color" size="sm" />
                                            </div>
                                        </template>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Presentasi</label>
                                            <v-select v-model="form_specialobg.physical_internal_presentation_type_id" :options="optPresentation" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div> 
                                        <template v-if="form_specialobg.physical_internal_presentation_type_id == 3">
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Presentasi muka</label>
                                                <b-form-input v-model="form_specialobg.physical_internal_presentation_face" size="sm" />
                                            </div>
                                        </template>
                                        <template v-if="form_specialobg.physical_internal_presentation_type_id == 4">
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Presentasi lain-lain</label>
                                                <b-form-input v-model="form_specialobg.physical_internal_presentation_other" size="sm" />
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <div class="mt-16">
                                    <label class="font-weight-bold fs-13 mb-0">PEMERIKSAAN KARDIOTOGRAFI (CTG): </label>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label class="fs-13 mb-0">Base line</label>
                                            <b-input-group size="sm" append="Bpm">
                                                <b-form-input v-model="form_specialobg.physical_ctg_baseline" type="number" autocomplete="off" />
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Variabilitas</label>
                                            <v-select v-model="form_specialobg.physical_ctg_variability_type_id" :options="optCtgVariability" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div> 
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Akselerasi</label>
                                            <v-select v-model="form_specialobg.physical_ctg_acceleration_type_id" :options="OptionsIsAny" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div> 
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Gerakan janin</label>
                                            <b-input-group size="sm" append="x/mnt">
                                                <b-form-input v-model="form_specialobg.physical_ctg_fetal_movement" type="number" autocomplete="off" />
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Deselerasi</label>
                                            <v-select v-model="form_specialobg.physical_ctg_deceleration_type_id" :options="OptionsIsAny" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                                                            
                                        </div> 
                                        <template v-if="form_specialobg.physical_ctg_deceleration_type_id == 1">
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Jenis Deselerasi</label>
                                                <v-select v-model="form_specialobg.physical_ctg_deceleration_type_type_id" :options="optCtgDecelerationType" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                            </div>
                                        </template>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Kontraksi uterus</label>
                                            <v-select v-model="form_specialobg.physical_ctg_contractions_type_id" :options="OptionsIsYes" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div> 
                                        <template v-if="form_specialobg.physical_ctg_contractions_type_id == 1">
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Frekuensi</label>
                                                <b-input-group size="sm" append="x/10mnt">
                                                    <b-form-input v-model="form_specialobg.physical_ctg_contractions_freq" type="number" autocomplete="off" />
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Durasi</label>
                                                <b-input-group size="sm" append="detik">
                                                    <b-form-input v-model="form_specialobg.physical_ctg_contractions_duration" type="number" autocomplete="off" />
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Kekuatan</label>
                                                <b-input-group size="sm" append="mmHg">
                                                    <b-form-input v-model="form_specialobg.physical_ctg_contractions_strength" type="number" autocomplete="off" />
                                                </b-input-group>
                                            </div>
                                        </template>
                                        <div class="col-12 mt-8">
                                            <label class="fs-13 mb-0">Intepretasi CTG</label>
                                            <textarea v-model.lazy="form_specialobg.physical_ctg_interpretation" class="form-control form-control-sm" autocomplete="off" />
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-16">
                                    <label class="font-weight-bold fs-13 mb-0">DIAGNOSIS: </label>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <label class="fs-13 mb-0">Ibu</label>
                                            <textarea v-model.lazy="form_specialobg.diagnosis_mother" class="form-control form-control-sm" autocomplete="off" />
                                        </div>
                                        <div class="col-lg-6 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Bayi</label>
                                            <textarea v-model.lazy="form_specialobg.diagnosis_baby" class="form-control form-control-sm" autocomplete="off" />
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-16">
                                    <label class="font-weight-bold fs-13 mb-0">PROGNOSIS: </label>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label class="fs-13 mb-0">Ibu</label>
                                            <v-select v-model="form_specialobg.prognosis_mother_type_id" :options="optPrognosis" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>
                                        <template v-if="form_specialobg.prognosis_mother_type_id == 2">
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Prognosis ibu - malam</label>
                                                <b-form-input v-model="form_specialobg.prognosis_mother_night" size="sm" autocomplete="off" />
                                            </div>
                                        </template>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <label class="fs-13 mb-0">Bayi</label>
                                            <v-select v-model="form_specialobg.prognosis_baby_type_id" :options="optPrognosis" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>
                                        <template v-if="form_specialobg.prognosis_baby_type_id == 2">
                                            <div class="col-lg-3 mt-8 mt-lg-0">
                                                <label class="fs-13 mb-0">Prognosis anak - malam</label>
                                                <b-form-input v-model="form_specialobg.prognosis_baby_night" size="sm" autocomplete="off" />
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <div class="mt-16">
                                    <label class="font-weight-bold fs-13 mb-0">RENCANA: </label>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <v-select v-model="form_specialobg.plan_type_id" :options="optPartusPlan" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                                                
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-16">
                                    <label class="font-weight-bold fs-13 mb-0">MASALAH KEBIDANAN: </label>
                                    <textarea v-model.lazy="form_specialobg.obstetric_problems" class="form-control form-control-sm" autocomplete="off" />
                                </div>
                            </section>
                        </div>
                        <div class="card-footer py-1 px-8 d-flex w-100 justify-content-start">
                            <b-button-group size="sm">
                                <b-button @click="changeTab('OBGYN')" :disabled="active.disabled.section" variant="outline-neutral-700">
                                    <feather-icon icon="ChevronLeftIcon" />
                                    Kembali
                                </b-button>
                                <b-button @click="changeSection(active.section, 'SPECIALOBG')" :disabled="active.disabled.section" variant="orange-500">
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
            urlAsset:process.env.VUE_APP_ASSETS_BE,
            userData: JSON.parse(localStorage.getItem('userData')),
            dynamicHeight: "73vh",
            differenceHeight: "85px",
            regData: {},
            global_nurse_id: '',
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

            isPews: false,
            nextTab: null,
            original_anamnesa: {},
            original_vital: {},
            original_functional: {},
            original_obgyn: {},
            original_special: {},

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
            
            form_obgyn: {
                id: '',
                registration_destination_id: '',
                employee_nurse_id: '',
                type_nurse_id: '',
                medical_devices: [],
                examination_history: [],
                comm_needs_speech_type_id: '',
                comm_needs_speech_disorder: '',
                comm_needs_colloquial_arry: [],
                comm_needs_colloquial_local: '',
                comm_needs_colloquial_other: '',
                comm_needs_translator_type_id: '',
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

                evaluation_sexual_marital_status_type_id: '',
                evaluation_sexual_marital_status: '',
                evaluation_sexual_menstruation_type_id: '',
                evaluation_sexual_obstetrics_gynecology: '',
                evaluation_sexual_other_habits: '',
                evaluation_sexual_history_contraception: '',
                evaluation_sexual_circumcision_type_id: '',
                evaluation_sexual_circumcision: '',
                evaluation_sexual_problem_type_id: '',
                evaluation_sexual_problem: '',
                birth_history: []
            },

            form_specialobg: {
                id: '',
                registration_destination_id: '',
                employee_nurse_id: '',
                type_nurse_id: '',
                anamnesis_g: '',
                anamnesis_p: '',
                anamnesis_a: '',
                anamnesis_h: '',
                anamnesis_period: '',
                anamnesis_antenatal_type_id: '',
                anamnesis_disease_pregnancy_arry: [],
                anamnesis_disease_pregnancy_other: '',
                anamnesis_his_type_id: '',
                anamnesis_his_date: '',
                anamnesis_his_hour: '',
                anamnesis_amniotic_fluid_type_id: '',
                anamnesis_amniotic_fluid_date: '',
                anamnesis_amniotic_fluid_hour: '',
                anamnesis_amniotic_fluid_color_type_id: '',
                anamnesis_amniotic_fluid_color_other: '',
                anamnesis_blood_slym_type_id: '',
                anamnesis_antepartum_type_id: '',
                anamnesis_antepartum_date: '',
                anamnesis_antepartum_hour: '',
                anamnesis_antepartum_amount: '',
                anamnesis_seizure_type_id: '',
                anamnesis_seizure_clonic_long: '',
                anamnesis_straining_type_id: '',
                anamnesis_straining_date: '',
                anamnesis_straining_hour: '',
                anamnesis_straining_amount: '',
                anamnesis_other_problem: '',
                physical_leopold_one: '',
                physical_leopold_two_type_id: '',
                physical_leopold_three_type_id: '',
                physical_leopold_three_other: '',
                physical_leopold_four_type_id: '',
                physical_fetal_heart_rate_freq: '',
                physical_fetal_heart_rate_rhythm_type_id: '',
                physical_vulva_type_id: '',
                physical_vulva_other: '',
                physical_internal_portio_type_id: '',
                physical_internal_opening_type_id: '',
                physical_internal_opening_cm: '',
                physical_internal_depletion_percent: '',
                physical_internal_amniotic_type_id: '',
                physical_internal_amniotic_color: '',
                physical_internal_presentation_type_id: '',
                physical_internal_presentation_face: '',
                physical_internal_presentation_other: '',
                physical_internal_lowest_part_drop_type_id: '',
                physical_internal_lowest_part_drop_hodge_one: '',
                physical_internal_lowest_part_drop_hodge_two: '',
                physical_internal_lowest_part_drop_hodge_three: '',
                physical_internal_lowest_part_drop_hodge_four: '',
                physical_ctg_baseline: '',
                physical_ctg_variability_type_id: '',
                physical_ctg_acceleration_type_id: '',
                physical_ctg_deceleration_type_id: '',
                physical_ctg_deceleration_type_type_id: '',
                physical_ctg_contractions_type_id: '',
                physical_ctg_contractions_freq: '',
                physical_ctg_contractions_duration: '',
                physical_ctg_contractions_strength: '',
                physical_ctg_fetal_movement: '',
                physical_ctg_interpretation: '',
                diagnosis_mother: '',
                diagnosis_baby: '',
                prognosis_mother_type_id: '',
                prognosis_mother_night: '',
                prognosis_baby_type_id: '',
                prognosis_baby_night: '',
                plan_type_id: '',
                obstetric_problems: ''
            },

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
            
            OptionsIsAny: [
                { value: 0, title: 'Tidak' },
                { value: 1, title: 'Ada' },
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

        //  =============== RANAP KEBIDANAN ================

        "form_obgyn.comm_needs_speech_type_id"(newValue) {
            if (newValue != "2") {
                this.form_obgyn.comm_needs_speech_disorder = ""
            }
        },

        "form_obgyn.comm_needs_colloquial_arry"(newValues) {
            if (newValues && !newValues.includes("c")) {
                this.form_obgyn.comm_needs_colloquial_local = ""
            }
            if (newValues && !newValues.includes("d")) {
                this.form_obgyn.comm_needs_colloquial_other = ""
            }
        },

        "form_obgyn.evaluation_smoke_type_id"(newValue) {
            if (newValue != "1") {
                this.form_obgyn.evaluation_smoke_amount = ''
                this.form_obgyn.evaluation_smoke_long = ''
                this.form_obgyn.evaluation_smoke_unit = 'Tahun'
            }
        },

        "form_obgyn.evaluation_alcohol_type_id"(newValue) {
            if (newValue != "1") {
                this.form_obgyn.evaluation_alcohol_type = ''
                this.form_obgyn.evaluation_alcohol_amount = ''
            }
        },

        "form_obgyn.evaluation_sedative_type_id"(newValue) {
            if (newValue != "1") {
                this.form_obgyn.evaluation_sedative_type = ''
                this.form_obgyn.evaluation_sedative_amount = ''
                this.form_obgyn.evaluation_sedative_unit = 'Tablet'
            }
        },

        "form_obgyn.evaluation_sleep_type_id"(newValue) {
            if (newValue != "1") {
                this.form_obgyn.evaluation_sleep = ''
            }
        },

        "form_obgyn.evaluation_grip_type_id"(newValue) {
            if (newValue != "3") {
                this.form_obgyn.evaluation_grip_other = ''
            }
        },

        "form_obgyn.evaluation_coordination_type_id"(newValue) {
            if (newValue != "2") {
                this.form_obgyn.evaluation_coordination_other = ''
            }
        },

        "form_obgyn.evaluation_extremities_bottom_type_id"(newValue) {
            if (newValue != "7") {
                this.form_obgyn.evaluation_extremities_bottom_bone = ''
            }
        },

        "form_obgyn.evaluation_psychological_status_type_id"(newValue) {
            if (newValue != "7") {
                this.form_obgyn.evaluation_psychological_status_other = ''
            }
        },

        "form_obgyn.evaluation_elimination_defecate_type_id"(newValue) {
            if (newValue != "7") {
                this.form_obgyn.evaluation_elimination_defecate_freq = ''
            }
        },
        
        "form_obgyn.evaluation_sexual_marital_status_type_id"(newValue) {
            if (newValue != 1) {
                this.form_obgyn.evaluation_sexual_marital_status = ''
            }
        },

        "form_obgyn.evaluation_sexual_problem_type_id"(newValue) {
            if (newValue != 1) {
                this.form_obgyn.evaluation_sexual_problem = ''
            }
        },
        
        "form_obgyn.evaluation_sexual_circumcision_type_id"(newValue) {
            if (newValue != 1) {
                this.form_obgyn.evaluation_sexual_circumcision = ''
            }
        },
        
        //  =============== RANAP KEBIDANAN - KHUSUS ================        

        "form_specialobg.anamnesis_his_type_id"(newValue) {
            if (newValue != 1) {
                this.form_specialobg.anamnesis_his_date = ''
            }
        },

        "form_specialobg.anamnesis_seizure_type_id"(newValue) {
            if (newValue != 4) {
                this.form_specialobg.anamnesis_seizure_clonic_long = ''
            }
        },

        "form_specialobg.anamnesis_straining_type_id"(newValue) {
            if (newValue != 1) {
                this.form_specialobg.anamnesis_straining_date = ''
                this.form_specialobg.anamnesis_straining_amount = ''
            }
        },

        "form_specialobg.anamnesis_antepartum_type_id"(newValue) {
            if (newValue != 1) {
                this.form_specialobg.anamnesis_antepartum_date = ''
                this.form_specialobg.anamnesis_antepartum_amount = ''
            }
        },
        
        "form_specialobg.anamnesis_disease_pregnancy_arry"(newValues) {
            if (newValues && !newValues.includes("e")) {
                this.form_specialobg.anamnesis_disease_pregnancy_other = ""
            }
        },

        "form_specialobg.physical_leopold_three_type_id"(newValue) {
            if (newValue != 3) {
                this.form_specialobg.physical_leopold_three_other = ''
            }
        },
        
        "form_specialobg.physical_vulva_type_id"(newValue) {
            if (newValue != 4) {
                this.form_specialobg.physical_vulva_other = ''
            }
        },

        "form_specialobg.physical_internal_amniotic_type_id"(newValue) {
            if (newValue != 1) {
                this.form_specialobg.physical_internal_amniotic_color = ''
            }
        },

        "form_specialobg.physical_internal_presentation_type_id"(newValue) {
            if (newValue != 3) {
                this.form_specialobg.physical_internal_presentation_face = ''
            }

            if (newValue != 4) {
                this.form_specialobg.physical_internal_presentation_other = ''
            }
        },

        "form_specialobg.physical_internal_lowest_part_drop_type_id"(newValue) {
            if (newValue != 1) {
                this.form_specialobg.physical_internal_lowest_part_drop_hodge_one = ''
            }

            if (newValue != 2) {
                this.form_specialobg.physical_internal_lowest_part_drop_hodge_two = ''
            }

            if (newValue != 3) {
                this.form_specialobg.physical_internal_lowest_part_drop_hodge_three = ''
            }

            if (newValue != 4) {
                this.form_specialobg.physical_internal_lowest_part_drop_hodge_four = ''
            }
        },

        "form_specialobg.physical_ctg_deceleration_type_id"(newValue) {
            if (newValue != 1) {
                this.form_specialobg.physical_ctg_deceleration_type_type_id = ''
            }
        },

        "form_specialobg.physical_ctg_contractions_type_id"(newValue) {
            if (newValue != 1) {
                this.form_specialobg.physical_ctg_contractions_freq = ''
                this.form_specialobg.physical_ctg_contractions_duration = ''
                this.form_specialobg.physical_ctg_contractions_strength = ''
            }
        },

        "form_specialobg.prognosis_mother_type_id"(newValue) {
            if (newValue != 2) {
                this.form_specialobg.prognosis_mother_night = ''
            }
        },

        "form_specialobg.prognosis_baby_type_id"(newValue) {
            if (newValue != 2) {
                this.form_specialobg.prognosis_baby_night = ''
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

            optMarried: state => state.frontoptions.nurse.assessment.obgyn.married,
            optAntenatal: state => state.frontoptions.nurse.assessment.obgyn.antenatal,
            optHis: state => state.frontoptions.nurse.assessment.obgyn.his,
            optAmniotic: state => state.frontoptions.nurse.assessment.obgyn.amniotic,
            optAmnioticColor: state => state.frontoptions.nurse.assessment.obgyn.amniotic_color,
            optSeizure: state => state.frontoptions.nurse.assessment.obgyn.seizure,
            optLeopold2: state => state.frontoptions.nurse.assessment.obgyn.leopold_2,
            optLeopold3: state => state.frontoptions.nurse.assessment.obgyn.leopold_3,
            optLeopold4: state => state.frontoptions.nurse.assessment.obgyn.leopold_4,
            optHeartRhythm: state => state.frontoptions.nurse.assessment.obgyn.heart_rhythm,
            optVulva: state => state.frontoptions.nurse.assessment.obgyn.vulva,
            optPortio: state => state.frontoptions.nurse.assessment.obgyn.portio,
            optAmnioticInternal: state => state.frontoptions.nurse.assessment.obgyn.amniotic_internal,
            optPresentation: state => state.frontoptions.nurse.assessment.obgyn.presentation,
            optCtgVariability: state => state.frontoptions.nurse.assessment.obgyn.ctg_variability,
            optCtgDecelerationType: state => state.frontoptions.nurse.assessment.obgyn.ctg_deceleration_type,
            optPrognosis: state => state.frontoptions.nurse.assessment.obgyn.prognosis,
            optPartusPlan: state => state.frontoptions.nurse.assessment.obgyn.partus_plan,
            optLowestDrop: state => state.frontoptions.nurse.assessment.obgyn.lowest_drop,
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

            this.history.examination = []
            this.exa_load.limit_display = 3
            this.getExaminationList(this.regData.norm)
        },

        async openMainModal(obj) {
            this.active.modal.main = true
            if (obj.gate == 'edit_gate') {
                await this.fillNurseForm(obj.nurseData)
            } else {
                await this.resetNurseForm()
                await this.resetObgyn()
                await this.resetSpecial()
                
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
            if (sectionKey == 'OBGYN') return 'Kebidanan'
            if (sectionKey == 'SPECIALOBG') return 'Kebidanan'
        },

        async cloneAllState() {            
            this.original_anamnesa = JSON.parse(JSON.stringify(this.form_anamnesa))
            this.original_vital = JSON.parse(JSON.stringify(this.form_vital))
            this.original_functional = JSON.parse(JSON.stringify(this.form_functional))
            this.original_obgyn = JSON.parse(JSON.stringify(this.form_obgyn))
            this.original_special = JSON.parse(JSON.stringify(this.form_specialobg))
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
                OBGYN: ["form_obgyn", "original_obgyn"],
                SPECIALOBG: ["form_specialobg", "original_special"],

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
                OBGYN: ["form_obgyn", "original_obgyn"],
                SPECIALOBG: ["form_specialobg", "original_special"],
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
            //     OBGYN: ["form_obgyn", "original_obgyn"],
            //     SPECIALOBG: ["form_specialobg", "original_special"],
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
                OBGYN: this.saveObgyn,
                SPECIALOBG: this.saveSpecObgyn,
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
            this.form_anamnesa.type_nurse_id ||= 3

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
            this.form_vital.type_nurse_id ||= 3

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
            this.form_functional.type_nurse_id ||= 3

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
        
        async saveObgyn(exist, destination) {
            this.form_obgyn.employee_nurse_id ||= this.userData.employee_id
            this.form_obgyn.registration_destination_id ||= this.regData.registration_destination_id
            this.form_obgyn.type_nurse_id ||= 3

            let fixed_data = {
                exist: exist,
                data: this.form_obgyn   
            }

            try {
                this.active.disabled.section = true
                await this.$store.dispatch('nurse/putSections', fixed_data)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','Berhasil menyimpan asesmen kebidanan')
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

        async saveSpecObgyn(exist, destination) {
            this.form_specialobg.employee_nurse_id ||= this.userData.employee_id
            this.form_specialobg.registration_destination_id ||= this.regData.registration_destination_id
            this.form_specialobg.type_nurse_id ||= 3

            let fixed_data = {
                exist: exist,
                data: this.form_specialobg   
            }

            try {
                this.active.disabled.section = true
                await this.$store.dispatch('nurse/putSections', fixed_data)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','Berhasil menyimpan asesmen kebidanan')
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
            await this.resetObgyn()
            await this.resetSpecial()

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
                    this.form_anamnesa.type_nurse_id ||= 3
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
                    this.form_vital.type_nurse_id ||= 3
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
                    this.form_functional.type_nurse_id = obj?.type_nurse_id || 3
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

                    if (obj.type_nurse_id == 3) {
                        this.fillObgynForm(obj.id)
                        this.fillSpecialForm(obj.id)
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

        // ================== OBGYN ==================
        async resetObgyn() {
            this.form_obgyn.id = ''
            this.form_obgyn.registration_destination_id = ''
            this.form_obgyn.employee_nurse_id = ''
            this.form_obgyn.type_nurse_id = ''
            this.form_obgyn.medical_devices = []
            this.form_obgyn.examination_history = []
            this.form_obgyn.comm_needs_speech_type_id = ''
            this.form_obgyn.comm_needs_speech_disorder = ''
            this.form_obgyn.comm_needs_colloquial_arry = []
            this.form_obgyn.comm_needs_colloquial_local = ''
            this.form_obgyn.comm_needs_colloquial_other = ''
            this.form_obgyn.comm_needs_translator_type_id = ''
            this.form_obgyn.evaluation_smoke_unit = 'Tahun'
            this.form_obgyn.evaluation_sedative_unit = 'Tablet'
            this.form_obgyn.evaluation_smoke_type_id = ''
            this.form_obgyn.evaluation_smoke_amount = ''
            this.form_obgyn.evaluation_smoke_long = ''
            this.form_obgyn.evaluation_alcohol_type_id = ''
            this.form_obgyn.evaluation_alcohol_type = ''
            this.form_obgyn.evaluation_alcohol_amount = ''
            this.form_obgyn.evaluation_sedative_type_id = ''
            this.form_obgyn.evaluation_sedative_type = ''
            this.form_obgyn.evaluation_sedative_amount = ''
            this.form_obgyn.evaluation_sleep_type_id = ''
            this.form_obgyn.evaluation_sleep = ''
            this.form_obgyn.evaluation_daily_type_id = ''
            this.form_obgyn.evaluation_activity_type_id = ''
            this.form_obgyn.evaluation_extremities_top_type_id = ''
            this.form_obgyn.evaluation_grip_type_id = ''
            this.form_obgyn.evaluation_grip_other = ''
            this.form_obgyn.evaluation_extremities_bottom_type_id = ''
            this.form_obgyn.evaluation_extremities_bottom_bone = ''
            this.form_obgyn.evaluation_ambulation_type_id = ''
            this.form_obgyn.evaluation_mental_status_type_id = ''
            this.form_obgyn.evaluation_psychological_status_type_id = ''
            this.form_obgyn.evaluation_psychological_status_other = ''
            this.form_obgyn.evaluation_elimination_defecate_type_id = ''
            this.form_obgyn.evaluation_elimination_defecate_freq = ''
            this.form_obgyn.evaluation_elimination_urination_type_id = ''
            this.form_obgyn.evaluation_sexual_marital_status_type_id = ''
            this.form_obgyn.evaluation_sexual_marital_status = ''
            this.form_obgyn.evaluation_sexual_menstruation_type_id = ''
            this.form_obgyn.evaluation_sexual_obstetrics_gynecology = ''
            this.form_obgyn.evaluation_sexual_other_habits = ''
            this.form_obgyn.evaluation_sexual_history_contraception = ''
            this.form_obgyn.evaluation_sexual_circumcision_type_id = ''
            this.form_obgyn.evaluation_sexual_circumcision = ''
            this.form_obgyn.evaluation_sexual_problem_type_id = ''
            this.form_obgyn.evaluation_sexual_problem = ''
            this.form_obgyn.birth_history = []
        },

        fillObgynForm(nurse_id) {
            if (nurse_id) {
                this.$store.dispatch('nurse/getObgynDetail', { nurse_id: nurse_id }).then((response) => {
                    const res = response[0]
                    // console.log('res obgyn: ', res)
                    this.form_obgyn.id = res?.nurse_id || nurse_id
                    this.form_obgyn.employee_nurse_id = res?.employee_nurse_id || this.userData.employee_id
                    this.form_obgyn.registration_destination_id = res?.registration_destination_id || this.regData.registration_destination_id
                    this.form_obgyn.type_nurse_id = res?.type_nurse_id || 3
                    this.form_obgyn.medical_devices = res?.medical_devices || []
                    this.form_obgyn.examination_history = res?.examination_history || []
                    this.form_obgyn.comm_needs_speech_type_id = res?.comm_needs_speech_type_id || ''
                    this.form_obgyn.comm_needs_speech_disorder = res?.comm_needs_speech_disorder || ''
                    this.form_obgyn.comm_needs_colloquial_arry = res?.comm_needs_colloquial_arry || []
                    this.form_obgyn.comm_needs_colloquial_local = res?.comm_needs_colloquial_local || ''
                    this.form_obgyn.comm_needs_colloquial_other = res?.comm_needs_colloquial_other || ''
                    this.form_obgyn.comm_needs_translator_type_id = res?.comm_needs_translator_type_id
                    this.form_obgyn.evaluation_smoke_unit = res?.evaluation_smoke_unit || 'Tahun'
                    this.form_obgyn.evaluation_sedative_unit = res?.evaluation_sedative_unit || 'Tablet'
                    this.form_obgyn.evaluation_smoke_type_id = res?.evaluation_smoke_type_id || ''
                    this.form_obgyn.evaluation_smoke_amount = res?.evaluation_smoke_amount || ''
                    this.form_obgyn.evaluation_smoke_long = res?.evaluation_smoke_long || ''
                    this.form_obgyn.evaluation_alcohol_type_id = res?.evaluation_alcohol_type_id || ''
                    this.form_obgyn.evaluation_alcohol_type = res?.evaluation_alcohol_type || ''
                    this.form_obgyn.evaluation_alcohol_amount = res?.evaluation_alcohol_amount || ''
                    this.form_obgyn.evaluation_sedative_type_id = res?.evaluation_sedative_type_id || ''
                    this.form_obgyn.evaluation_sedative_type = res?.evaluation_sedative_type || ''
                    this.form_obgyn.evaluation_sedative_amount = res?.evaluation_sedative_amount || ''
                    this.form_obgyn.evaluation_sleep_type_id = res?.evaluation_sleep_type_id || ''
                    this.form_obgyn.evaluation_sleep = res?.evaluation_sleep || ''
                    this.form_obgyn.evaluation_daily_type_id = res?.evaluation_daily_type_id || ''
                    this.form_obgyn.evaluation_activity_type_id = res?.evaluation_activity_type_id || ''
                    this.form_obgyn.evaluation_extremities_top_type_id = res?.evaluation_extremities_top_type_id || ''
                    this.form_obgyn.evaluation_grip_type_id = res?.evaluation_grip_type_id || ''
                    this.form_obgyn.evaluation_grip_other = res?.evaluation_grip_other || ''
                    this.form_obgyn.evaluation_extremities_bottom_type_id = res?.evaluation_extremities_bottom_type_id || ''
                    this.form_obgyn.evaluation_extremities_bottom_bone = res?.evaluation_extremities_bottom_bone || ''
                    this.form_obgyn.evaluation_ambulation_type_id = res?.evaluation_ambulation_type_id || ''
                    this.form_obgyn.evaluation_mental_status_type_id = res?.evaluation_mental_status_type_id || ''
                    this.form_obgyn.evaluation_psychological_status_type_id = res?.evaluation_psychological_status_type_id || ''
                    this.form_obgyn.evaluation_psychological_status_other = res?.evaluation_psychological_status_other || ''
                    this.form_obgyn.evaluation_elimination_defecate_type_id = res?.evaluation_elimination_defecate_type_id || ''
                    this.form_obgyn.evaluation_elimination_defecate_freq = res?.evaluation_elimination_defecate_freq || ''
                    this.form_obgyn.evaluation_elimination_urination_type_id = res?.evaluation_elimination_urination_type_id || ''
                    this.form_obgyn.evaluation_sexual_marital_status_type_id = res?.evaluation_sexual_marital_status_type_id || ''
                    this.form_obgyn.evaluation_sexual_marital_status = res?.evaluation_sexual_marital_status || ''
                    this.form_obgyn.evaluation_sexual_menstruation_type_id = res?.evaluation_sexual_menstruation_type_id || ''
                    this.form_obgyn.evaluation_sexual_obstetrics_gynecology = res?.evaluation_sexual_obstetrics_gynecology || ''
                    this.form_obgyn.evaluation_sexual_other_habits = res?.evaluation_sexual_other_habits || ''
                    this.form_obgyn.evaluation_sexual_history_contraception = res?.evaluation_sexual_history_contraception || ''
                    this.form_obgyn.evaluation_sexual_circumcision_type_id = res?.evaluation_sexual_circumcision_type_id
                    this.form_obgyn.evaluation_sexual_circumcision = res?.evaluation_sexual_circumcision || ''
                    this.form_obgyn.evaluation_sexual_problem_type_id = res?.evaluation_sexual_problem_type_id
                    this.form_obgyn.evaluation_sexual_problem = res?.evaluation_sexual_problem || ''
                    this.form_obgyn.birth_history = res?.birth_history || []
                    this.cloneAllState()
                })
            }
        },

        addObgMedical() {
            this.form_obgyn.medical_devices.push({
                type: '', 
                explanation: '', 
            })
        },

        remObgMedical(index) {
            this.form_obgyn.medical_devices.splice(index, 1)
        },

        addObgExamination() {
            this.form_obgyn.examination_history.push({
                type: '', 
                page: '', 
                explanation: '', 
            })
        },

        remObgExamination(index) {
            this.form_obgyn.examination_history.splice(index, 1)
        },

        addObgBirth() {
            this.form_obgyn.birth_history.push({
                year: '',
                birth_time: '',
                pregnancy_complications: '',
                spontaneous: '',
                action: '',
                complications: '',
                sex: '',
                weight: '',
                condition: '',
                breastfeeding: '', 
            })
        },

        remObgBirth(index) {
            this.form_obgyn.birth_history.splice(index, 1)
        },

        // ================== SCPEC-OBGYN ==================
        async resetSpecial() {
            this.form_specialobg.id = ''
            this.form_specialobg.registration_destination_id = ''
            this.form_specialobg.employee_nurse_id = ''
            this.form_specialobg.type_nurse_id = ''
            this.form_specialobg.anamnesis_g = ''
            this.form_specialobg.anamnesis_p = ''
            this.form_specialobg.anamnesis_a = ''
            this.form_specialobg.anamnesis_h = ''
            this.form_specialobg.anamnesis_period = ''
            this.form_specialobg.anamnesis_antenatal_type_id = ''
            this.form_specialobg.anamnesis_disease_pregnancy_arry = []
            this.form_specialobg.anamnesis_disease_pregnancy_other = ''
            this.form_specialobg.anamnesis_his_type_id = ''
            this.form_specialobg.anamnesis_his_date = ''
            this.form_specialobg.anamnesis_his_hour = ''
            this.form_specialobg.anamnesis_amniotic_fluid_type_id = ''
            this.form_specialobg.anamnesis_amniotic_fluid_date = ''
            this.form_specialobg.anamnesis_amniotic_fluid_hour = ''
            this.form_specialobg.anamnesis_amniotic_fluid_color_type_id = ''
            this.form_specialobg.anamnesis_amniotic_fluid_color_other = ''
            this.form_specialobg.anamnesis_blood_slym_type_id = ''
            this.form_specialobg.anamnesis_antepartum_type_id = ''
            this.form_specialobg.anamnesis_antepartum_date = ''
            this.form_specialobg.anamnesis_antepartum_hour = ''
            this.form_specialobg.anamnesis_antepartum_amount = ''
            this.form_specialobg.anamnesis_seizure_type_id = ''
            this.form_specialobg.anamnesis_seizure_clonic_long = ''
            this.form_specialobg.anamnesis_straining_type_id = ''
            this.form_specialobg.anamnesis_straining_date = ''
            this.form_specialobg.anamnesis_straining_hour = ''
            this.form_specialobg.anamnesis_straining_amount = ''
            this.form_specialobg.anamnesis_other_problem = ''
            this.form_specialobg.physical_leopold_one = ''
            this.form_specialobg.physical_leopold_two_type_id = ''
            this.form_specialobg.physical_leopold_three_type_id = ''
            this.form_specialobg.physical_leopold_three_other = ''
            this.form_specialobg.physical_leopold_four_type_id = ''
            this.form_specialobg.physical_fetal_heart_rate_freq = ''
            this.form_specialobg.physical_fetal_heart_rate_rhythm_type_id = ''
            this.form_specialobg.physical_vulva_type_id = ''
            this.form_specialobg.physical_vulva_other = ''
            this.form_specialobg.physical_internal_portio_type_id = ''
            this.form_specialobg.physical_internal_opening_type_id = ''
            this.form_specialobg.physical_internal_opening_cm = ''
            this.form_specialobg.physical_internal_depletion_percent = ''
            this.form_specialobg.physical_internal_amniotic_type_id = ''
            this.form_specialobg.physical_internal_amniotic_color = ''
            this.form_specialobg.physical_internal_presentation_type_id = ''
            this.form_specialobg.physical_internal_presentation_face = ''
            this.form_specialobg.physical_internal_presentation_other = ''
            this.form_specialobg.physical_internal_lowest_part_drop_type_id = ''
            this.form_specialobg.physical_internal_lowest_part_drop_hodge_one = ''
            this.form_specialobg.physical_internal_lowest_part_drop_hodge_two = ''
            this.form_specialobg.physical_internal_lowest_part_drop_hodge_three = ''
            this.form_specialobg.physical_internal_lowest_part_drop_hodge_four = ''
            this.form_specialobg.physical_ctg_baseline = ''
            this.form_specialobg.physical_ctg_variability_type_id = ''
            this.form_specialobg.physical_ctg_acceleration_type_id = ''
            this.form_specialobg.physical_ctg_deceleration_type_id = ''
            this.form_specialobg.physical_ctg_deceleration_type_type_id = ''
            this.form_specialobg.physical_ctg_contractions_type_id = ''
            this.form_specialobg.physical_ctg_contractions_freq = ''
            this.form_specialobg.physical_ctg_contractions_duration = ''
            this.form_specialobg.physical_ctg_contractions_strength = ''
            this.form_specialobg.physical_ctg_fetal_movement = ''
            this.form_specialobg.physical_ctg_interpretation = ''
            this.form_specialobg.diagnosis_mother = ''
            this.form_specialobg.diagnosis_baby = ''
            this.form_specialobg.prognosis_mother_type_id = ''
            this.form_specialobg.prognosis_mother_night = ''
            this.form_specialobg.prognosis_baby_type_id = ''
            this.form_specialobg.prognosis_baby_night = ''
            this.form_specialobg.plan_type_id = ''
            this.form_specialobg.obstetric_problems = ''
        },

        fillSpecialForm(nurse_id) {
            if (nurse_id) {
                this.$store.dispatch('nurse/getSpecialObgynDetail', { nurse_id: nurse_id }).then((response) => {
                    const res = response[0]
                    // console.log('res special: ', res)
                    this.form_specialobg.id = res?.nurse_id || nurse_id
                    this.form_specialobg.employee_nurse_id = res?.employee_nurse_id || this.userData.employee_id
                    this.form_specialobg.registration_destination_id = res?.registration_destination_id || this.regData.registration_destination_id
                    this.form_specialobg.type_nurse_id = res?.type_nurse_id || 3
                    this.form_specialobg.anamnesis_g = res?.anamnesis_g || ''
                    this.form_specialobg.anamnesis_p = res?.anamnesis_p || ''
                    this.form_specialobg.anamnesis_a = res?.anamnesis_a || ''
                    this.form_specialobg.anamnesis_h = res?.anamnesis_h || ''
                    this.form_specialobg.anamnesis_period = res?.anamnesis_period || ''
                    this.form_specialobg.anamnesis_antenatal_type_id = res?.anamnesis_antenatal_type_id || ''
                    this.form_specialobg.anamnesis_disease_pregnancy_arry = res?.anamnesis_disease_pregnancy_arry || []
                    this.form_specialobg.anamnesis_disease_pregnancy_other = res?.anamnesis_disease_pregnancy_other || ''
                    this.form_specialobg.anamnesis_his_type_id = res?.anamnesis_his_type_id || ''
                    this.form_specialobg.anamnesis_his_date = res?.anamnesis_his_date || ''
                    this.form_specialobg.anamnesis_his_hour = res?.anamnesis_his_hour || ''
                    this.form_specialobg.anamnesis_amniotic_fluid_type_id = res?.anamnesis_amniotic_fluid_type_id || ''
                    this.form_specialobg.anamnesis_amniotic_fluid_date = res?.anamnesis_amniotic_fluid_date || ''
                    this.form_specialobg.anamnesis_amniotic_fluid_hour = res?.anamnesis_amniotic_fluid_hour || ''
                    this.form_specialobg.anamnesis_amniotic_fluid_color_type_id = res?.anamnesis_amniotic_fluid_color_type_id || ''
                    this.form_specialobg.anamnesis_amniotic_fluid_color_other = res?.anamnesis_amniotic_fluid_color_other || ''
                    this.form_specialobg.anamnesis_blood_slym_type_id = res?.anamnesis_blood_slym_type_id
                    this.form_specialobg.anamnesis_antepartum_type_id = res?.anamnesis_antepartum_type_id
                    this.form_specialobg.anamnesis_antepartum_date = res?.anamnesis_antepartum_date || ''
                    this.form_specialobg.anamnesis_antepartum_hour = res?.anamnesis_antepartum_hour || ''
                    this.form_specialobg.anamnesis_antepartum_amount = res?.anamnesis_antepartum_amount || ''
                    this.form_specialobg.anamnesis_seizure_type_id = res?.anamnesis_seizure_type_id || ''
                    this.form_specialobg.anamnesis_seizure_clonic_long = res?.anamnesis_seizure_clonic_long || ''
                    this.form_specialobg.anamnesis_straining_type_id = res?.anamnesis_straining_type_id
                    this.form_specialobg.anamnesis_straining_date = res?.anamnesis_straining_date || ''
                    this.form_specialobg.anamnesis_straining_hour = res?.anamnesis_straining_hour || ''
                    this.form_specialobg.anamnesis_straining_amount = res?.anamnesis_straining_amount || ''
                    this.form_specialobg.anamnesis_other_problem = res?.anamnesis_other_problem || ''
                    this.form_specialobg.physical_leopold_one = res?.physical_leopold_one || ''
                    this.form_specialobg.physical_leopold_two_type_id = res?.physical_leopold_two_type_id || ''
                    this.form_specialobg.physical_leopold_three_type_id = res?.physical_leopold_three_type_id || ''
                    this.form_specialobg.physical_leopold_three_other = res?.physical_leopold_three_other || ''
                    this.form_specialobg.physical_leopold_four_type_id = res?.physical_leopold_four_type_id || ''
                    this.form_specialobg.physical_fetal_heart_rate_freq = res?.physical_fetal_heart_rate_freq || ''
                    this.form_specialobg.physical_fetal_heart_rate_rhythm_type_id = res?.physical_fetal_heart_rate_rhythm_type_id || ''
                    this.form_specialobg.physical_vulva_type_id = res?.physical_vulva_type_id || ''
                    this.form_specialobg.physical_vulva_other = res?.physical_vulva_other || ''
                    this.form_specialobg.physical_internal_portio_type_id = res?.physical_internal_portio_type_id || ''
                    this.form_specialobg.physical_internal_opening_type_id = res?.physical_internal_opening_type_id
                    this.form_specialobg.physical_internal_opening_cm = res?.physical_internal_opening_cm
                    this.form_specialobg.physical_internal_depletion_percent = res?.physical_internal_depletion_percent || ''
                    this.form_specialobg.physical_internal_amniotic_type_id = res?.physical_internal_amniotic_type_id || ''
                    this.form_specialobg.physical_internal_amniotic_color = res?.physical_internal_amniotic_color || ''
                    this.form_specialobg.physical_internal_presentation_type_id = res?.physical_internal_presentation_type_id || ''
                    this.form_specialobg.physical_internal_presentation_face = res?.physical_internal_presentation_face || ''
                    this.form_specialobg.physical_internal_presentation_other = res?.physical_internal_presentation_other || ''
                    this.form_specialobg.physical_internal_lowest_part_drop_type_id = res?.physical_internal_lowest_part_drop_type_id || ''
                    this.form_specialobg.physical_internal_lowest_part_drop_hodge_one = res?.physical_internal_lowest_part_drop_hodge_one || ''
                    this.form_specialobg.physical_internal_lowest_part_drop_hodge_two = res?.physical_internal_lowest_part_drop_hodge_two || ''
                    this.form_specialobg.physical_internal_lowest_part_drop_hodge_three = res?.physical_internal_lowest_part_drop_hodge_three || ''
                    this.form_specialobg.physical_internal_lowest_part_drop_hodge_four = res?.physical_internal_lowest_part_drop_hodge_four || ''
                    this.form_specialobg.physical_ctg_baseline = res?.physical_ctg_baseline || ''
                    this.form_specialobg.physical_ctg_variability_type_id = res?.physical_ctg_variability_type_id || ''
                    this.form_specialobg.physical_ctg_acceleration_type_id = res?.physical_ctg_acceleration_type_id || ''
                    this.form_specialobg.physical_ctg_deceleration_type_id = res?.physical_ctg_deceleration_type_id || ''
                    this.form_specialobg.physical_ctg_deceleration_type_type_id = res?.physical_ctg_deceleration_type_type_id || ''
                    this.form_specialobg.physical_ctg_contractions_type_id = res?.physical_ctg_contractions_type_id
                    this.form_specialobg.physical_ctg_contractions_freq = res?.physical_ctg_contractions_freq || ''
                    this.form_specialobg.physical_ctg_contractions_duration = res?.physical_ctg_contractions_duration || ''
                    this.form_specialobg.physical_ctg_contractions_strength = res?.physical_ctg_contractions_strength || ''
                    this.form_specialobg.physical_ctg_fetal_movement = res?.physical_ctg_fetal_movement || ''
                    this.form_specialobg.physical_ctg_interpretation = res?.physical_ctg_interpretation || ''
                    this.form_specialobg.diagnosis_mother = res?.diagnosis_mother || ''
                    this.form_specialobg.diagnosis_baby = res?.diagnosis_baby || ''
                    this.form_specialobg.prognosis_mother_type_id = res?.prognosis_mother_type_id || ''
                    this.form_specialobg.prognosis_mother_night = res?.prognosis_mother_night || ''
                    this.form_specialobg.prognosis_baby_type_id = res?.prognosis_baby_type_id || ''
                    this.form_specialobg.prognosis_baby_night = res?.prognosis_baby_night || ''
                    this.form_specialobg.plan_type_id = res?.plan_type_id || ''
                    this.form_specialobg.obstetric_problems = res?.obstetric_problems || ''
                    this.cloneAllState()
                })
            }
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