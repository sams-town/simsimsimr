<template>
    <div>
        <b-modal v-model="active.modal.main" scrollable size="xl" no-close-on-esc no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase"> ANDA SEDANG MENGISI RESUME
                    <span class="text-primary">{{ temp_header.alias_name }}. {{ temp_header.name }} | {{ temp_header.norm }} | {{localDate(temp_header.birth_date)}} ({{ parsedAge(temp_header.birth_date) }}) | BB: {{temp_header.weight | number}} KG| TB: {{ temp_header.height | number}} CM</span>
                </div>
                <feather-icon icon="XIcon" @click="onCloseMainModal" size="18" class="pointer" />
            </template>
            <template v-if="regData.health_facility_id === '40f06534-07ec-4221-ad9d-dea08196079f'">
                <!-- <div v-if="items_resume.length > 0" class="fs-13 mb-3 d-flex justify-content-start">
                    <div v-for="(item, index) in items_resume" :key="item.id">
                        <b-button
                        :to="`/patient/resume/print/${reg_des_id}/${item.id}`"
                        target="_blank"
                        size="sm" variant="w3green">
                            <b-icon icon="printer" class="mr-1" /> Cetak Resume
                        </b-button>
                    </div>
                </div> -->
                <div v-if="items_resume.length > 0" class="fs-13 mb-3 d-flex justify-content-start">
                    <b-button
                    :to="`/patient/resume/print/${reg_des_id}/${items_resume[0].id}`"
                    target="_blank"
                    size="sm" variant="w3green">
                        <b-icon icon="printer" class="mr-1" /> Cetak Resume
                    </b-button>
                </div>
                <div class="row position-relative">
                    <section class="col-4 position-sticky px-1" :style="{top: '0', height: `${dynamicHeight}`, overflowY: 'auto'}">
                        <ModernLabel label="REFERENSI ASESMEN PASIEN" class="mb-2"/>
                        <b-tabs small content-class="mt-3 text-regular-12" nav-wrapper-class="fs-13" nav-class="custom-tab-style">
                            <b-tab title="DOKTER" active>
                                <!-- <div v-for="(item, index) in history.soap" class="accordion" role="tablist"> -->
                                <div v-for="(item, index) in soap_list" class="accordion" role="tablist">
                                    <b-card no-body class="mb-1">
                                        <b-card-header block v-b-toggle="'accordion-soap-'+item.id" header-tag="header" class="pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                            <div>
                                                <div class="font-weight-bold">ASESMEN {{ localDateTime(item.date_added) }}</div>
                                                <div>{{ item.employee_doctor_name }} | {{ item.health_facility_item_name }}</div>
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
                                                            <b-button size="sm" variant="primary" @click="copySelected($event, copyAnamnesa, item, 'ANAMNESA')" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                                            <b-button @click="copySelected($event, copySoapVital, item, 'VITAL')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                                            <b>IMT: </b>{{ item.imt }}
                                                            <!-- <b>L.Kepala: </b>{{ item.head_circumference }},
                                                            <b>L.Dada: </b>{{ item.chest_circumference }},
                                                            <b>L.Lengan Atas: </b>{{ item.arm_circumference }},
                                                            <b>L.Perut: </b>{{ item.stomach_circumference }},
                                                            <b>L.Lutut: </b>{{ item.knee_circumference }}, -->
                                                        </div>
                                                    </div>
                                                </section>
                                                <section class="card mt-16">
                                                    <div class="card-header px-12 py-8">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="text-bold-14">ST GENERALIS</div>
                                                            <b-button @click="copySelected($event, copySoapGeneralis, item, 'STGENERALIS')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-12">
                                                        <div>
                                                            <b>Kepala: </b>{{ getHead(item?.head) || '-' }}
                                                            <b>Warna Rambut: </b>{{ getHairColor(item?.head_hair_color) || '-' }}
                                                            <b>Kondisi Rambut: </b>{{ getHairCondition(item?.head_hair_condition) || '-' }}
                                                            <b>Conjunctiva: </b>{{ getEyeConjunctive(item?.eye_conjunctive) || '-' }}
                                                            <b>Sklera: </b>{{ getEyeSclera(item?.eye_sclera) || '-' }}
                                                            <b>Pupil: </b>{{ getEyePupil(item?.eye_pupil) || '-' }}
                                                            <b>Reflek Cahaya: </b>{{ getEyeReflect(item?.eye_light_reflex) || '-' }}
                                                            <b>Mukosa: </b>{{ getMouthMucosa(item?.mouth_mucosa) || '-' }}
                                                            <b>Perubahan Warna: </b>{{ getMouthDiscoloration(item?.mouth_discoloration) || '-' }}
                                                            <b>Lidah: </b>{{ getMouthTongue(item?.mouth_tongue) || '-' }}
                                                            <b>Karies: </b>{{ getMouthCaries(item?.mouth_caries) || '-' }}
                                                            <b>KGB: </b>{{ getNeckKGB(item?.neck_kgb) || '-' }}
                                                            <b>Posisi Trakea: </b>{{ getNeckTracheaPosition(item?.neck_trachea_position) || '-' }}
                                                            <b>Massa: </b>{{ getNeckMassa(item?.neck_massa) || '-' }}
                                                            <b>JVP: </b>{{ getNeckJVP(item?.neck_jvp) || '-' }}
                                                            <b>Dada: </b>{{ getInspectionChest(item?.thorax_insp_chest) || '-' }}
                                                            <b>Retraksi: </b>{{ getInspectionRetractiont(item?.thorax_insp_retraction) || '-' }}
                                                            <b>Dada: </b>{{ getPalpationChest(item?.thorax_palp_chest) || '-' }}
                                                            <b>Fremitus: </b>{{ getPalpationFremitus(item?.thorax_palp_fremitus) || '-' }}
                                                            <b>Krepitasi: </b>{{ getPalpationKrepitasi(item?.thorax_palp_crepitation) || '-' }}
                                                            <b>Pulmo Vasikuler: </b>{{ getAuskultationPulmoVasikuler(item?.thorax_ausk_pul_vasiculer) || '-' }}
                                                            <b>Pulmo Rhonki: </b>{{ getAuskultationPulmoRhonki(item?.thorax_ausk_pul_rhonki) || '-' }}
                                                            <b>Pulmo Wheezing: </b>{{ getAuskultationPulmoWheezing(item?.thorax_ausk_pul_wheezing) || '-' }}
                                                            <b>COR Bunyi Jantung: </b>{{ getAuskultationCORBJ(item?.thorax_ausk_cor_heart_sound) || '-' }}
                                                            <b>COR Murmur: </b>{{ getAuskultationCORMurmur(item?.thorax_ausk_cor_murmur) || '-' }}
                                                            <b>COR Gallop: </b>{{ getAuskultationCORGallop(item?.thorax_ausk_cor_gallop) || '-' }}
                                                            <b>Supel: </b>{{ getAbdomenSupel(item?.abdomen_supel) || '-' }}
                                                            <b>Bising Usus: </b>{{ getAbdomenBising(item?.abdomen_bowel_sounds) || '-' }}
                                                            <b>Nyeri Tekan: </b>{{ getAbdomenPress(item?.abdomen_tenderness) || '-' }}
                                                            <b>Hepar & Lien: </b>{{ getAbdomenHepar(item?.abdomen_hepar_lien) || '-' }}
                                                            <b>Pemeriksaan Abdomen Lainnya: </b>{{ item?.abdomen_other_physical_check || '-' }}
                                                            <b>Akral: </b>{{ getEkstremitasAkral(item?.extremity_acral) || '-' }}
                                                            <b>CRT: </b>{{ getEkstremitasCRT(item?.extremity_crt) || '-' }}
                                                            <b>Edema: </b>{{ getEkstremitasEdema(item?.extremity_edema) || '-' }}
                                                            <b>Genitalia: </b>{{ item?.genitalia || '-' }}
                                                            <b>Status Lokalis: </b>{{ item?.localist_status || '-' }}
                                                            <b>Status Neurologis: </b>{{ item?.neurologist_status || '-' }}
                                                            <b>Status Oftalmologis: </b>{{ item?.ophthal_status || '-' }}
                                                        </div>
                                                    </div>
                                                </section>
                                                <section class="card mt-16">
                                                    <div class="card-header px-12 py-8">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="text-bold-14">DIAGNOSA</div>
                                                            <b-button @click="copySelected($event, copySoapDiagnosis, item, 'DIAGNOSIS')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                                            <b-button @click="copySelected($event, copySoapProcedure, item, 'PROCEDURE')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                                            <!-- <template v-if="item.soap_p?.length > 0">
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
                                                            </template> -->
                                                            <template v-if="item.icd9cm?.length > 0">
                                                                <div v-for="(list, index_i) in item.icd9cm" :class="index_i == 0 ? 'mt-8' : 'mt-1'">
                                                                    <div>
                                                                        <div>
                                                                            <b>ICD9CM: </b>
                                                                        </div>
                                                                        <template v-if="list">
                                                                            <li>{{ list.code }} {{ list.name }}</li>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                            <div v-else class="mt-8">
                                                                <div><b>ICD9CM: -</b></div>
                                                                <div><b>CATATAN ICD9CM: -</b></div>
                                                            </div>
                                                        </div>
                                                        <div><b>Catatan ICD9CM : </b>{{ item.therapy_plan || '-' }}</div>
                                                        <div><b>Advice: </b>{{item?.advice || '-'}}</div>
                                                    </div>
                                                </section>
                                                <section class="card mt-16">
                                                    <div class="card-header px-12 py-8">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="text-bold-14">LABORATORIUM</div>
                                                            <b-button @click="copySelected($event, copySoapLaboratory, item, 'LABORATORY')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-12">
                                                        <div>
                                                            <b>LABORATORIUM: </b>
                                                            <!-- <span v-if="!item.laboratory_request || item.laboratory_request.length == 0">-</span> -->
                                                        </div>
                                                        <div>
                                                            <template
                                                                v-if="item.laboratory_request_new && item.laboratory_request_new.length > 0"
                                                            >
                                                                <template v-for="(il, i) in item.laboratory_request_new">
                                                                    <li v-for="(lab_list, index) in il.laboratory_request_items"
                                                                        :class="index == 0 ? 'mb-2' : 'my-2'"
                                                                        style="list-style-type: none;">
                                                                        <b-button @click="copyItemLaboratory(lab_list)"
                                                                            class="mr-2"
                                                                            size="sm"
                                                                            variant="outline-primary">
                                                                            <feather-icon icon="PlusIcon" />
                                                                        </b-button>

                                                                        <template v-if="lab_list.result?.length > 0">
                                                                            <feather-icon icon="EyeIcon" @click="triggerLabResult({ alias_name: regData.alias_name, name: regData.name, result: lab_list.result, costing_name: lab_list.costing_name, laboratory_request_id: lab_list.laboratory_request_id })" color="blue"/>
                                                                        </template>
                                                                        {{ lab_list.costing_name }}
                                                                        <!-- <b-table  show-empty :fields="laboratoryResultField" :items="lab_list?.result || []" bordered responsive small thead-tr-class="fs-10" tbody-tr-class="fs-10" class="mt-12">
                                                                            <template #cell(no)="row">
                                                                                <div class="text-center">{{ row.index+1 }}</div>
                                                                            </template>
                                                                            <template #empty="scope">
                                                                                <div class="text-center">Hasil Laboratorium sedang dalam Proses</div>
                                                                            </template>
                                                                        </b-table> -->
                                                                    </li>
                                                                </template>
                                                            </template>
                                                            <span
                                                                v-if="!item.laboratory_request_items || item.laboratory_request_items.length == 0">
                                                                -
                                                            </span>
                                                        </div>
                                                        <!-- <div v-if="item?.laboratory_request?.length > 0" class="mt-2">
                                                            <template v-for="list in item.laboratory_request">
                                                                <div>{{ list?.no_request }}</div>
                                                                <li v-for="unit in list.laboratory_request_items">
                                                                    <template v-if="unit.result?.length > 0">
                                                                        <feather-icon icon="EyeIcon" @click="triggerLabResult({ alias_name: regData.alias_name, name: regData.name, result: unit.result, costing_name: unit.laboratory_package_name, laboratory_request_id: unit.laboratory_request_id })" color="blue"/>
                                                                    </template>
                                                                    {{ unit.laboratory_package_name }}
                                                                </li>
                                                            </template>
                                                        </div> -->
                                                    </div>
                                                </section>
                                                <section class="card mt-16">
                                                    <div class="card-header px-12 py-8">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="text-bold-14">RADIOLOGI</div>
                                                            <b-button @click="copySelected($event, copySoapRadiology, item, 'RADIOLOGY')"  size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-12">
                                                        <div>
                                                            <b>RADIOLOGI:</b>
                                                            <!-- <span v-if="!item.radiology_request || item.radiology_request.length == 0">-</span> -->
                                                        </div>
                                                        <div class="">
                                                            <template
                                                                v-if="item.radiology_request_new && item.radiology_request_new.length > 0">
                                                                <template v-for="(il, i) in item.radiology_request_new">
                                                                    <li v-for="(rad_list, index) in il.radiology_request_items"
                                                                    :class="index == 0 ? 'mb-2' : 'my-2'"
                                                                    style="list-style-type: none;">
                                                                        <b-button @click="copyItemRadiology(rad_list)"
                                                                            class="mr-2"
                                                                            size="sm"
                                                                            variant="outline-primary">
                                                                            <feather-icon icon="PlusIcon" />
                                                                        </b-button>
                                                                        <template v-if="rad_list.url?.length > 0">
                                                                            <feather-icon icon="EyeIcon" @click="triggerRadResult({ alias_name: regData.alias_name, name: regData.name, url: rad_list.url, costing_name: rad_list.costing_name })" color="blue"/>
                                                                        </template>
                                                                        {{ rad_list.costing_name }}
                                                                    </li>
                                                                </template>
                                                            </template>
                                                            <span
                                                                v-if="!item.radiology_request_items || item.radiology_request_items.length == 0">-</span>
                                                        </div>
                                                        <!-- <div v-if="item?.radiology_request?.length > 0">
                                                            <template v-for="list in item.radiology_request">
                                                                <div class="mt-2">{{ list?.no_request }}</div>
                                                                <li v-for="unit in list.radiology_request_items">
                                                                    <template v-if="unit.url?.length > 0">
                                                                        <feather-icon icon="EyeIcon" @click="triggerRadResult({ alias_name: regData.alias_name, name: regData.name, url: unit.url, costing_name: unit.radiology_package_name })" color="blue"/>
                                                                    </template>
                                                                    {{ unit.radiology_package_name }}
                                                                </li>
                                                            </template>
                                                        </div> -->
                                                    </div>
                                                </section>
                                                <section class="card mt-16">
                                                    <div class="card-header px-12 py-8">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="text-bold-14">FISIOTERAPI</div>
                                                            <b-button @click="copySelected($event, copySoapPhysiotherapy, item, 'PHYSIOTHERAPY')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-12">
                                                        <div>
                                                            <b>FISIOTERAPI: </b>
                                                            <!-- <span v-if="!item.physiotherapy_request || item.physiotherapy_request.length == 0">-</span> -->
                                                        </div>
                                                        <div>
                                                            <template
                                                                v-if="item.physiotherapy_request_items && item.physiotherapy_request_items.length > 0">
                                                                <li v-for="(physio_list, index) in item.physiotherapy_request_items"
                                                                    :class="index == 0 ? 'mb-2' : 'my-2'"
                                                                    style="list-style-type: none;">
                                                                    <b-button @click="copyItemPhysiotherapy(physio_list)"
                                                                        size="sm"
                                                                        variant="outline-primary">
                                                                        <feather-icon icon="PlusIcon" />
                                                                    </b-button>
                                                                    {{ physio_list.costing_name }}
                                                                </li>
                                                            </template>
                                                            <span
                                                                v-if="!item.physiotherapy_request_items || item.physiotherapy_request_items.length == 0">
                                                                -
                                                            </span>
                                                        </div>
                                                        <!-- <div v-if="item?.physiotherapy_request?.length > 0">
                                                            <template v-for="list in item.physiotherapy_request">
                                                                <div class="mt-2">{{ list?.no_request }}</div>
                                                                <li v-for="unit in list.physiotherapy_request_items">{{ unit.physiotherapy_package_name }}</li>
                                                            </template>
                                                        </div> -->
                                                    </div>
                                                </section>
                                                <section class="card mt-16">
                                                    <div class="card-header px-12 py-8">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="text-bold-14">RESEP</div>
                                                            <b-button @click="copySelected($event, copyPrescription, item, 'PRESC')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-12">
                                                        <div>
                                                            <!-- <b>Resep: </b> -->
                                                            <!-- <span v-if="!item.prescription_new || item.prescription_new.length == 0">-</span> -->
                                                        </div>
                                                        <template v-if="item.prescription_new?.length > 0">
                                                            <template v-for="(list, index_i) in item.prescription_new">
                                                                <div class="mt-2">
                                                                    <b>{{list.type_prescription_name}} | {{list.no_order}} | {{
                                                                        list.date_0 }}</b>
                                                                </div>
                                                                <template v-if="list.form_prescription?.length > 0">
                                                                    <li v-for="iip in list.form_prescription">
                                                                        {{ iip.pharmacy_catalog_name }}, {{ iip.qty }} {{
                                                                        iip.package_name }}, {{ iip.used }}, {{ iip.desc }}
                                                                    </li>
                                                                </template>
                                                            </template>
                                                        </template>
                                                        <span v-if="!item.prescription_new || item.prescription_new.length == 0">-</span>
                                                        <!-- <div v-if="item?.prescription" class="mt-1">
                                                            <template v-for="(list, presc_i) in item.prescription">
                                                                <div :class="presc_i > 0 ? 'mt-2' : ''">{{ list?.no_order }}</div>
                                                                <li v-for="unit in list.form_prescription">{{ unit?.pharmacy_catalog_name }}</li>
                                                            </template>
                                                        </div>
                                                        <template v-else>-</template> -->
                                                    </div>
                                                </section>
                                            </b-card-body>
                                        </b-collapse>
                                    </b-card>
                                </div>
                                <!-- <template v-if="soap_load.limit_display < soap_load.count">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div @click="loadMoreSoap" class="font-weight-bold pointer fs-13 text-center py-1 load-text">
                                            <a class="mr-2">Load more</a>
                                            <feather-icon icon="ArrowDownIcon" size="14"/>
                                        </div>
                                    </div>
                                </template> -->
                            </b-tab>
                            <b-tab title="PERAWAT">
                                <div v-for="(item, index) in history.nurse" class="accordion" role="tablist">
                                    <b-card no-body class="mb-1">
                                        <b-card-header block v-b-toggle="'accordion-ns-'+item.id" header-tag="header" class="pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'">
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
                                                        </div>
                                                        <div class="mt-8">
                                                            <b>BB: </b>{{ item.weight }},
                                                            <b>TB: </b>{{ item.height }},
                                                            <b>IMT: </b>{{ item.imt }}
                                                            <!-- <b>L.Kepala: </b>{{ item.head_circumference }},
                                                            <b>L.Dada: </b>{{ item.chest_circumference }},
                                                            <b>L.Lengan Atas: </b>{{ item.arm_circumference }},
                                                            <b>L.Perut: </b>{{ item.stomach_circumference }},
                                                            <b>L.Lutut: </b>{{ item.knee_circumference }}, -->
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
                                                <section class="card mt-16">
                                                    <div class="card-header px-12 py-8">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="text-bold-14">SUBJECTIVE</div>
                                                            <!-- <b-button @click="copySelected($event, cpptSubjective, item)" size="sm" variant="w3green" style="font-size: 12px; line-height:120%">COPY</b-button> -->
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-12">
                                                        <div>{{ item?.subjective || '-' }}</div>
                                                    </div>
                                                </section>
                                                <section class="card mt-16">
                                                    <div class="card-header px-12 py-8">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="text-bold-14">TANDA VITAL</div>
                                                            <b-button @click="copySelected($event, copyCpptVital, item)" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-12">
                                                        <!-- <div>{{ item?.objective || '-' }}</div> -->
                                                        <div class="mt-8">
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
                                                            <b>BB: </b>{{ item?.weight || 0 }},
                                                            <b>TB: </b>{{ item?.height || 0 }},
                                                            <b>IMT: </b>{{ item?.imt || 0 }},
                                                        </div>
                                                    </div>
                                                </section>

                                                <template v-if="item.group_name == 'Doctor' || item.group_name == 'Super User'">
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">ST GENERALIS</div>
                                                                <template>
                                                                    <b-button size="sm" @click="copySelected($event, copyCpptLocalis, item, 'GENERALIS')" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                                                <template>
                                                                    <b-button @click="copySelected($event, copyCpptDiagnosis, item, 'DIAGNOSIS')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                                                <template>
                                                                    <b-button @click="copySelected($event, copyCpptProcedure, item, 'PROCEDURE')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
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
                                                                <template v-if="item.icd9cm?.length > 0">
                                                                    <div>
                                                                        <div>
                                                                            <b>ICD9CM: </b>
                                                                            <span v-if="!item.icd9cm || item.icd9cm.length == 0">-</span>
                                                                        </div>
                                                                        <template v-if="item.icd9cm?.length > 0">
                                                                            <li v-for="unit in item.icd9cm">{{ unit.icd9cm_code }} {{ unit.icd9cm_name }}</li>
                                                                        </template>
                                                                    </div>
                                                                    <div><b>Catatan ICD9CM : </b>{{ item?.therapy_plan || '-' }}</div>
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
                                                                <div class="text-bold-14">PLAN</div>
                                                                <template>
                                                                    <!-- <b-button size="sm" @click="copySelected($event, cpptPlan, item, 'PLAN')" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button> -->
                                                                </template>
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
                                                                <template>
                                                                    <b-button @click="copySelected($event, copyCpptLaboratory, item, 'LABORATORY')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div>
                                                                <template
                                                                    v-if="item.laboratory_request && item.laboratory_request.length > 0">
                                                                    <template v-for="list in item.laboratory_request">
                                                                        <li v-for="(lab_list, index) in list.laboratory_request_items"
                                                                        :class="index == 0 ? 'mb-2' : 'my-2'"
                                                                        style="list-style-type: none;">
                                                                            <b-button @click="copyCpptItemLaboratory(lab_list)"
                                                                                class="mr-2"
                                                                                size="sm"
                                                                                variant="outline-primary">
                                                                                <feather-icon icon="PlusIcon" />
                                                                            </b-button>
                                                                            <template v-if="lab_list.result?.length > 0">
                                                                                <feather-icon icon="EyeIcon" @click="triggerLabResult({ alias_name: regData.alias_name, name: regData.name, result: lab_list.result, costing_name: lab_list.laboratory_package_name, laboratory_request_id: lab_list.laboratory_request_id })" color="blue"/>
                                                                            </template>
                                                                            {{ lab_list.laboratory_package_name }}
                                                                            <!-- <b-table  show-empty :fields="laboratoryResultField" :items="lab_list?.result || []" bordered responsive small thead-tr-class="fs-10" tbody-tr-class="fs-10" class="mt-12">
                                                                                <template #cell(no)="row">
                                                                                    <div class="text-center">{{ row.index+1 }}</div>
                                                                                </template>
                                                                                <template #empty="scope">
                                                                                    <div class="text-center">Hasil Laboratorium sedang dalam Proses</div>
                                                                                </template>
                                                                            </b-table> -->
                                                                        </li>
                                                                    </template>
                                                                </template>
                                                                <span
                                                                    v-if="!item.laboratory_request || item.laboratory_request.length == 0">
                                                                    -
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </section>
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">RADIOLOGI</div>
                                                                <template>
                                                                    <b-button @click="copySelected($event, copyCpptRadiology, item, 'RADIOLOGY')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div>
                                                                <template
                                                                    v-if="item?.radiology_request && item?.radiology_request?.length > 0">
                                                                    <template v-for="list in item.radiology_request">
                                                                        <li v-for="(rad_list, index) in list.radiology_request_items"
                                                                        :class="index == 0 ? 'mb-2' : 'my-2'"
                                                                        style="list-style-type: none;">
                                                                            <b-button @click="copyCpptItemRadiology(rad_list)"
                                                                                class="mr-2"
                                                                                size="sm"
                                                                                variant="outline-primary">
                                                                                <feather-icon icon="PlusIcon" />
                                                                            </b-button>
                                                                            <template v-if="rad_list.url?.length > 0">
                                                                                <feather-icon icon="EyeIcon" @click="triggerRadResult({ alias_name: regData.alias_name, name: regData.name, url: rad_list.url, costing_name: rad_list.radiology_package_name })" color="blue"/>
                                                                            </template>
                                                                            {{ rad_list.radiology_package_name }}
                                                                        </li>
                                                                    </template>
                                                                </template>
                                                                <span
                                                                    v-if="!item.radiology_request || item.radiology_request.length == 0">
                                                                    -
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </section>
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">FISIOTERAPI</div>
                                                                <template>
                                                                    <b-button @click="copySelected($event, copyCpptPhysiotherapy, item, 'PHYSIOTHERAPY')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div>
                                                                <template
                                                                    v-if="item.physiotherapy_request && item.physiotherapy_request.length > 0">
                                                                    <template v-for="list in item.physiotherapy_request">
                                                                        <li v-for="(phy_list, index) in list.physiotherapy_request_items"
                                                                        :class="index == 0 ? 'mb-2' : 'my-2'"
                                                                        style="list-style-type: none;">
                                                                            <b-button @click="copyCpptItemPhysiotherapy(phy_list)"
                                                                                size="sm"
                                                                                variant="outline-primary">
                                                                                <feather-icon icon="PlusIcon" />
                                                                            </b-button>
                                                                            {{ phy_list.physiotherapy_package_name }}
                                                                        </li>
                                                                    </template>
                                                                </template>
                                                                <span
                                                                    v-if="!item.physiotherapy_request || item.physiotherapy_request.length == 0">
                                                                    -
                                                                </span>
                                                            </div>
                                                            <!-- <div>
                                                                <b>FISIOTERAPI: </b>
                                                                <span v-if="!item.physiotherapy_request || item.physiotherapy_request.length == 0">-</span>
                                                            </div>
                                                            <div v-if="item?.physiotherapy_request?.length > 0">
                                                                <template v-for="list in item.physiotherapy_request">
                                                                    <div class="mt-2">{{ list?.no_request }}</div>
                                                                    <li v-for="unit in list.physiotherapy_request_items">{{ unit.physiotherapy_package_name }}</li>
                                                                </template>
                                                            </div> -->
                                                        </div>
                                                    </section>
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">RESEP</div>
                                                                <template>
                                                                    <b-button @click="copySelected($event, copyCpptPrescription, item, 'PRESC')" size="sm" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button>
                                                                </template>
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
                                                </template>

                                                <template v-else>
                                                    <section class="card mt-16">
                                                        <div class="card-header px-12 py-8">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="text-bold-14">DIAGNOSA</div>
                                                                <template>
                                                                    <!-- <b-button size="sm" @click="copySelected($event, cpptNsDiagnosis, item, 'NSDIAGNOSIS')" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button> -->
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
                                                                <template>
                                                                    <!-- <b-button size="sm" @click="copySelected($event, cpptNsPlan, item, 'NSPLAN')" variant="primary" style="font-size: 12px; line-height:120%">COPY</b-button> -->
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-12">
                                                            <div><b>Plan: </b>{{ item?.nursing_plan || '-'}}</div>
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
                                                        <div class="font-weight-bold">{{ localDateTime(item?.date_request) }}</div>
                                                        <div>{{ item?.doctor_in_charge_name }} | {{ item.health_facility_item_name }}</div>
                                                    </div>
                                                    <b-button @click="copySelected($event, copyHLaboratory, item, 'LABORATORY')" size="sm" variant="primary" class="fs-12" style="line-height:120%">COPY</b-button>
                                                </div>
                                            </div>
                                            <div class="card-body p-12">
                                                <template v-if="item.laboratory_request_items">
                                                    <li v-for="(unit, index) in item.laboratory_request_items" :class="index == 0 ? 'mb-2' : 'my-2'" style="list-style-type: none;">
                                                        <b-button @click="copyItemHLaboratory(unit)" class="mr-2" size="sm" variant="outline-primary">
                                                            <feather-icon icon="PlusIcon" />
                                                        </b-button>

                                                        <template v-if="unit.result?.length > 0">
                                                            <feather-icon icon="EyeIcon" @click="triggerLabResult({ alias_name: regData.alias_name, name: regData.name, result: unit.result, costing_name: unit.laboratory_package_name, laboratory_request_id: unit.laboratory_request_id })" color="blue"/>
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
                                                        <div class="font-weight-bold">{{ localDateTime(item?.date_request) }}</div>
                                                        <div>{{ item?.doctor_in_charge_name }} | {{ item.health_facility_item_name }}</div>
                                                    </div>
                                                    <b-button @click="copySelected($event, copyHRadiology, item, 'RADIOLOGY')" size="sm" variant="primary" class="fs-12" style="line-height:120%">COPY</b-button>
                                                </div>
                                            </div>
                                            <div class="card-body p-12">
                                                <template v-if="item.radiology_request_items">
                                                    <li v-for="(unit, index) in item.radiology_request_items" :class="index == 0 ? 'mb-2' : 'my-2'" style="list-style-type: none;">
                                                        <b-button @click="copyItemHRadiology(unit)" class="mr-2" size="sm" variant="outline-primary">
                                                            <feather-icon icon="PlusIcon" />
                                                        </b-button>
                                                        <template v-if="unit.url?.length > 0">
                                                            <feather-icon icon="EyeIcon" @click="triggerRadResult({ alias_name: regData.alias_name, name: regData.name, url: unit.url, costing_name: unit.radiology_package_name })" color="blue"/>
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
                                                        <div class="font-weight-bold">{{ localDateTime(item?.date_request) }}</div>
                                                        <div>{{ item?.doctor_in_charge_name }} | {{ item.health_facility_item_name }}</div>
                                                    </div>
                                                    <b-button @click="copySelected($event, copyHPhysiotherapy, item, 'PHYSIOTHERAPY')" size="sm" variant="primary" class="fs-12" style="line-height:120%">COPY</b-button>
                                                </div>
                                            </div>
                                            <div class="card-body p-12">
                                                <li v-for="(unit, index) in item.physiotherapy_request_items" :class="index == 0 ? 'mb-2' : 'my-2'" style="list-style-type: none;">
                                                    <b-button @click="copyItemHPhysiotherapy(unit)" size="sm" variant="outline-primary">
                                                        <feather-icon icon="PlusIcon" />
                                                    </b-button>
                                                    {{ unit.physiotherapy_package_name }}
                                                </li>

                                                <!-- <template v-if="item.physiotherapy_request_items">
                                                    <li v-for="unit in item.physiotherapy_request_items">{{ unit.physiotherapy_package_name }}</li>
                                                </template> -->
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
                                                        {{ localDateTime(list.examination_date) }}
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
                            <!-- <b-tab title="TEMPLATE">
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
                            </b-tab> -->
                            <b-tab title="AI">
                                <div class="text-center" style="font-style:italic">under maintenance</div>
                            </b-tab>
                        </b-tabs>
                    </section>
                    <section class="col-8">
                        <div class="card">
                            <div class="card-header bg-primary font-weight-bold text-white">Form Resume</div>
                            <div class="card-body">
                                <div>
                                    <label class="c-form-label">Cara pulang *</label>
                                    <b-form-radio-group v-model="form.exit_id" @change="listenUserInput" class="text-regular-14">
                                        <b-form-radio value="4a6d28c4-e020-425f-91e1-d39699cf679b">
                                            <div>Meninggal</div>
                                        </b-form-radio>
                                        <b-form-radio value="807ca005-5827-4e19-9c97-0bc3d4e924ca">
                                            <div>Atas Persetujuan Dokter</div>
                                        </b-form-radio>
                                        <b-form-radio value="a1631acc-6a94-414d-95de-a9faea0f1c2f">
                                            <div>Dirujuk</div>
                                        </b-form-radio>
                                        <b-form-radio value="ae0aabfa-1600-443f-8a60-62f9e76b1608">
                                            <div>Permintaan Sendiri</div>
                                        </b-form-radio>
                                        <b-form-radio value="bbc9fdce-affb-4b32-a386-0ed7e91c1e89">
                                            <div>Pulang Non Ranap</div>
                                        </b-form-radio>
                                        <b-form-radio value="adb7ce0b-ede3-4fee-b273-ce159d286732">
                                            <div>Lainnya</div>
                                            <b-form-input type="text" v-model="form.exit_anythingelse" @change="listenUserInput" v-show="form.exit_id == 'adb7ce0b-ede3-4fee-b273-ce159d286732'" autocomplete="off"  />
                                        </b-form-radio>
                                    </b-form-radio-group>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Kondisi saat pulang *</label>
                                    <b-form-radio-group v-model="form.exit_condition_type_id" @change="listenUserInput" class="text-regular-14">
                                        <b-form-radio value="1">
                                            <div>Perbaikan</div>
                                            <div>
                                                <i>(Discharged with improvement)</i>
                                            </div>
                                        </b-form-radio>
                                        <b-form-radio value="2">
                                            <div>Tidak Sembuh</div>
                                            <div>
                                                <i>(Not Cured)</i>
                                            </div>
                                        </b-form-radio>
                                        <b-form-radio value="3">
                                            <div>Meninggal < 48 jam </div>
                                                    <div>
                                                        <i>(Died < 48 hrs) </i>
                                                    </div>
                                        </b-form-radio>
                                        <b-form-radio value="5">
                                            <div>Meninggal > 48 jam </div>
                                                    <div>
                                                        <i>(Died > 48 hrs) </i>
                                                    </div>
                                        </b-form-radio>
                                        <b-form-radio value="4">
                                            <div>Lainnya</div>
                                            <div>
                                                <i>(Other)</i>
                                            </div>
                                            <b-form-input type="text" v-model="form.exit_condition_anythingelse" @change="listenUserInput" v-show="form.exit_condition_type_id == 4" autocomplete="off"  />
                                        </b-form-radio>
                                    </b-form-radio-group>
                                </div>
                                <ModernLabel class="mt-12" label="Instruksi Rawat Lanjutan" small="(Continuous Care Instruction)"  />
                                <div class="mt-12">
                                    <label class="c-form-label">Kontrol Pasca Rawat Inap</label>
                                    <b-form-radio-group v-model="form.care_instruction_control" @change="listenUserInput" class="text-regular-14">
                                        <b-form-radio value="1">
                                            <div>RS Taman Harapan Baru</div>
                                        </b-form-radio>
                                        <b-form-radio value="2">
                                            <div>Dokter Keluarga / Klinik Luar</div>
                                            <div>
                                                <i>(Family Phsician / Outer Clinic)</i>
                                            </div>
                                        </b-form-radio>
                                        <b-form-radio value="3">
                                            <div>Puskesmas</div>
                                            <div>
                                                <i>(Primary Health Care)</i>
                                            </div>
                                        </b-form-radio>
                                        <b-form-radio value="4">
                                            <div>Lainnya</div>
                                            <div>
                                                <i>(Other)</i>
                                            </div>
                                            <b-form-input type="text" v-model="form.care_instruction_anythingelse" @change="listenUserInput" v-show="form.care_instruction_control == 4" autocomplete="off"  />
                                        </b-form-radio>
                                    </b-form-radio-group>
                                    <div v-if="form.care_instruction_control == '1'" class="row mt-12">
                                        <div class="col-lg-3">
                                            <label class="c-form-label">Kontrol di poliklinik: </label>
                                            <v-select v-model="form.health_facility_item_id" :options="OptionsHealthFacilityItem" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Poliklinik --" ></v-select>
                                        </div>
                                        <div class="col-lg-3">
                                            <label class="c-form-label">Tanggal Kontrol</label>
                                            <b-form-input v-model="form.care_instruction_control_date" @change="listenUserInput" type="date" autocomplete="off"  />
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Edukasi Selama Di Rumah</label>
                                    <!-- <suggestionBar
                                        @fillEducationResume="fillEducationResume"
                                        :items="education_resume_template_list"
                                    /> -->
                                    <b-form-textarea rows="3" v-model="form.home_education" @change="listenUserInput" autocomplete="off"  />
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Ringkasan Riwayat Penyakit</label>
                                    <b-form-textarea rows="5" v-model="form.summary_medical_history" @change="listenUserInput" autocomplete="off"  />
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Hasil Pemeriksaan Fisik</label>
                                    <b-form-textarea rows="5" v-model="form.physical_check" @change="listenUserInput" autocomplete="off"  />
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Indikasi Rawat Inap</label>
                                    <b-form-textarea v-model="form.indication_for_hospitalization" rows="3" @change="listenUserInput" autocomplete="off"  />
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Status Generalis</label>
                                    <div>
                                        <ModernLabel label="Kepala" />
                                        <div class="row">
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Kepala</label>
                                                <v-select v-model="form.head" :options="OptionsHeadCircumference" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Warna Rambut</label>
                                                <v-select v-model="form.head_hair_color" :options="OptionsHeadHairColor" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Kondisi Rambut</label>
                                                <v-select v-model="form.head_hair_condition" :options="OptionsHeadHairCondition" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-20">
                                        <ModernLabel label="Mata" />
                                        <div class="row">
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Conjunctiva</label>
                                                <v-select v-model="form.eye_conjunctive" :options="OptionsEyeConjunctiva" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Sklera</label>
                                                <v-select v-model="form.eye_sclera" :options="OptionsEyeSclera" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Pupil</label>
                                                <v-select v-model="form.eye_pupil" :options="OptionsEyePupil" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                            </div>
                                            <div class="col-lg-3 mt-8">
                                                <label class="fs-13 mb-0">Reflek Cahaya</label>
                                                <v-select v-model="form.eye_light_reflex" :options="OptionsEyeReflect" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                            </div>
                                        </div>
                                    </div>

                                    <b-collapse id="collapse-general" v-model="active.advance.generalis">
                                        <div class="mt-20">
                                            <ModernLabel label="Mulut" />
                                            <div class="row">
                                                <div class="col-lg-3 mt-8">
                                                    <label class="fs-13 mb-0">Mukosa</label>
                                                    <v-select v-model="form.mouth_mucosa" :options="OptionsMouthMukosa" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                                <div class="col-lg-3 mt-8">
                                                    <label class="fs-13 mb-0">Perubahan Warna</label>
                                                    <v-select v-model="form.mouth_discoloration" :options="OptionsMouthHiperemis" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                                <div class="col-lg-3 mt-8">
                                                    <label class="fs-13 mb-0">Lidah</label>
                                                    <v-select v-model="form.mouth_tongue" :options="OptionsMouthTongue" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                                <div class="col-lg-3 mt-8">
                                                    <label class="fs-13 mb-0">Karies</label>
                                                    <v-select v-model="form.mouth_caries" :options="OptionsMouthKaries" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-20">
                                            <ModernLabel label="Leher" />
                                            <div class="row">
                                                <div class="col-lg-3 mt-8">
                                                    <label class="fs-13 mb-0">KGB</label>
                                                    <v-select v-model="form.neck_kgb" :options="OptionsNeckKGB" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                                <div class="col-lg-3 mt-8">
                                                    <label class="fs-13 mb-0">Posisi Trakea</label>
                                                    <v-select v-model="form.neck_trachea_position" :options="OptionsNeckTrakea" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                                <div class="col-lg-3 mt-8">
                                                    <label class="fs-13 mb-0">Massa</label>
                                                    <v-select v-model="form.neck_massa" :options="OptionsNeckMassa" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                                <div class="col-lg-3 mt-8">
                                                    <label class="fs-13 mb-0">JVP</label>
                                                    <v-select
                                                        v-model="form.neck_jvp"
                                                        :options="OptionsNeckJVP"
                                                        :reduce="item => item.value"
                                                        @change="listenUserInput"
                                                        label="title"
                                                        placeholder="-- Pilih --"
                                                        class="select-size-sm"
                                                    />
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
                                                        <v-select v-model="form.thorax_insp_chest" :options="OptionsInspectionChest" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                    </div>
                                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                                        <label class="fs-13 mb-0">Retraksi</label>
                                                        <v-select v-model="form.thorax_insp_retraction" :options="OptionsInspectionRetraction" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-16">
                                                <div class="font-weight-bold" style="font-size: 14px">PALPASI</div>
                                                <div class="row">
                                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                                        <label class="fs-13 mb-0">Dada</label>
                                                        <v-select v-model="form.thorax_palp_chest" :options="OptionsPalpationChest" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                    </div>
                                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                                        <label class="fs-13 mb-0">Fremitus</label>
                                                        <v-select v-model="form.thorax_palp_fremitus" :options="OptionsPalpationFremitus" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                    </div>
                                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                                        <label class="fs-13 mb-0">Krepitasi</label>
                                                        <v-select v-model="form.thorax_palp_crepitation" :options="OptionsPalpationKrepitasi" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-16">
                                                <div class="font-weight-bold" style="font-size: 14px">AUSKULTASI</div>
                                                <div class="row">
                                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                                        <label class="fs-13 mb-0">Pulmo Vasikuler</label>
                                                        <v-select v-model="form.thorax_ausk_pul_vasiculer" :options="OptionsAuskultationPulmoVasikuler" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                    </div>
                                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                                        <label class="fs-13 mb-0">Pulmo Rhonki</label>
                                                        <v-select v-model="form.thorax_ausk_pul_rhonki" :options="OptionsAuskultationPulmoRhonki" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                    </div>
                                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                                        <label class="fs-13 mb-0">Pulmo Wheezing</label>
                                                        <v-select v-model="form.thorax_ausk_pul_wheezing" :options="OptionsAuskultationPulmoWheezing" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-3 mt-8">
                                                        <label class="fs-13 mb-0">COR Bunyi Jantung</label>
                                                        <v-select v-model="form.thorax_ausk_cor_heart_sound" :options="OptionsAuskultationCORBJ" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                    </div>
                                                    <div class="col-lg-3 mt-8">
                                                        <label class="fs-13 mb-0">COR Murmur</label>
                                                        <v-select v-model="form.thorax_ausk_cor_murmur" :options="OptionsAuskultationCORMurmur" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                    </div>
                                                    <div class="col-lg-3 mt-8">
                                                        <label class="fs-13 mb-0">COR Gallop</label>
                                                        <v-select v-model="form.thorax_ausk_cor_gallop" :options="OptionsAuskultationCORGallop" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-20">
                                            <ModernLabel label="Abdomen" />
                                            <div class="row">
                                                <div class="col-lg-3 mt-8">
                                                    <label class="fs-13 mb-0">Supel</label>
                                                    <v-select v-model="form.abdomen_supel" :options="OptionsAbdomenSupel" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                                <div class="col-lg-3 mt-8">
                                                    <label class="fs-13 mb-0">Bising Usus</label>
                                                    <v-select v-model="form.abdomen_bowel_sounds" :options="OptionsAbdomenBising" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                                <div class="col-lg-3 mt-8">
                                                    <label class="fs-13 mb-0">Nyeri Tekan</label>
                                                    <v-select v-model="form.abdomen_tenderness" :options="OptionsAbdomenPress" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                                <div class="col-lg-3 mt-8">
                                                    <label class="fs-13 mb-0">Hepar & Lien</label>
                                                    <v-select v-model="form.abdomen_hepar_lien" :options="OptionsAbdomenHepar" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                                <div class="col-lg mt-8">
                                                    <label class="fs-13 mb-0">Pemeriksaan Abdomen Lainnya</label>
                                                    <textarea v-model.lazy="form.abdomen_other_physical_check" @change="listenUserInput" class="form-control form-control-sm" autocomplete="off" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-20">
                                            <ModernLabel label="Ekstrimitas" />
                                            <div class="row">
                                                <div class="col-lg-3 mt-8">
                                                    <label class="fs-13 mb-0">Akral</label>
                                                    <v-select v-model="form.extremity_acral" :options="OptionsEkstremitasAkral" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                                <div class="col-lg-3 mt-8">
                                                    <label class="fs-13 mb-0">CRT</label>
                                                    <v-select v-model="form.extremity_crt" :options="OptionsEkstremitasCRT" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                                <div class="col-lg-3 mt-8">
                                                    <label class="fs-13 mb-0">Edema</label>
                                                    <v-select v-model="form.extremity_edema" :options="OptionsEkstremitasEdema" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-20">
                                            <ModernLabel label="Genitalia" />
                                            <div class="mt-8 mb-8">
                                                <textarea v-model.lazy="form.genitalia" @change="listenUserInput" class="form-control form-control-sm" autocomplete="off" placeholder="(Tuliskan pemeriksaan genitalia)" />
                                            </div>
                                        </div>
                                        <div class="mt-20">
                                            <ModernLabel label="Status Lokalis" />
                                            <div class="mt-8 mb-8">
                                                <textarea v-model.lazy="form.localist_status" @change="listenUserInput" class="form-control form-control-sm" autocomplete="off" placeholder="(Tuliskan status lokalis di kolom ini secara lengkap)" />
                                            </div>
                                        </div>
                                        <div class="mt-20">
                                            <ModernLabel label="Status Neurologis" />
                                            <div class="mt-8 mb-8">
                                                <textarea v-model.lazy="form.neurologist_status" @change="listenUserInput" class="form-control form-control-sm" autocomplete="off" />
                                            </div>
                                        </div>
                                        <div class="mt-20">
                                            <ModernLabel label="Status oftalmologis" />
                                            <div class="mt-8 mb-8">
                                                <textarea v-model.lazy="form.ophthal_status" @change="listenUserInput" class="form-control form-control-sm" autocomplete="off" />
                                            </div>
                                        </div>
                                    </b-collapse>
                                    <small @click="active.advance.generalis = !active.advance.generalis" class="font-weight-bold text-primary pointer fs-11 mt-12" :class="active.advance.oftalmologis ? null : 'collapsed'" :aria-expanded="active.advance.oftalmologis ? 'true' : 'false'" aria-controls="collapse-ofta">
                                        Advance status generalis
                                        <b-icon :icon="active.advance.generalis == true ? 'chevron-up' : 'chevron-down'" />
                                    </small>
                                </div>

                                <div class="mt-20">
                                    <label class="c-form-label">Diagnosis</label>
                                    <ModernLabel label="Diagnosa (ICD10)" small="ICD10 bisa diinput lebih dari satu" class="mt-1" />
                                    <div class="mt-8">
                                        <b-table :fields="ICD10TableFields" :items="form.icd10" responsive bordered class="mt-8" style="font-size: 13px;">
                                            <template #cell(no)="row">
                                                <div class="text-center">{{ row.index+1 }}</div>
                                            </template>
                                            <template #cell(action)="row">
                                                <div class="d-flex justify-content-center">
                                                    <b-button  variant="danger-600" size="sm" @click="delete10(row.index)">
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </div>
                                            </template>
                                        </b-table>
                                        <v-select v-model="icd10_id" @input="onAdd10($event)" @search="onSearchIcd10" :options="OptionsIcd10" :reduce="OptionsIcd10 => OptionsIcd10" label="title" placeholder="-- Pilih ICD10 --" aria-readonly="true"  class="mt-12"></v-select>
                                    </div>
                                    <b-textarea rows="2" type="text" v-model="form.icd10_notes" @change="listenUserInput" autocomplete="off" placeholder="Catatan ICD10" class="mt-12"></b-textarea>
                                </div>
                                <div class="mt-20">
                                    <label class="c-form-label">Prosedur Diagnostik dan Terapeutik yang dilakukan</label>
                                    <ModernLabel label="Tindakan (ICD9CM)" small="ICD9CM bisa diinput lebih dari satu" class="mt-1" />
                                    <div class="mt-8">
                                        <b-table :fields="ICD9TableFields" :items="form.icd9cm" responsive bordered class="mt-8" style="font-size: 13px;">
                                            <template #cell(no)="row">
                                                <div class="text-center">{{ row.index+1 }}</div>
                                            </template>
                                            <template #cell(action)="row">
                                                <div class="d-flex justify-content-center">
                                                    <b-button  variant="danger-600" size="sm" @click="delete9cm(row.index)">
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </div>
                                            </template>
                                        </b-table>
                                        <v-select v-model="icd9cm_id" @input="onAdd9cm($event)" @search="onSearchIcd9cm" :options="OptionsIcd9cm" :reduce="item => item" label="title" placeholder="-- Pilih ICD9CM --"  class="mt-12"></v-select>
                                    </div>
                                    <b-textarea rows="2" type="text" v-model="form.therapy_plan" @change="listenUserInput" autocomplete="off" placeholder="Catatan ICD9CM" class="mt-12"></b-textarea>
                                </div>
                                <div class="mt-20">
                                    <ModernLabel label="Penunjang Laboratorium" />
                                    <div class="mt-8">
                                        <template v-if="form.laboratory_request_items.length > 0">
                                            <b-table :fields="LaboratoryTableFields" :items="form.laboratory_request_items" responsive bordered class="mt-8" style="font-size: 13px;">
                                                <template #cell(no)="row">
                                                    <div class="text-center">{{ row.index+1 }}</div>
                                                </template>
                                                <template #cell(action)="row">
                                                    <div class="d-flex justify-content-center">
                                                        <b-button  variant="danger-600" size="sm" @click="deleteLabItem(row.index)">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </div>
                                                </template>
                                                <template #cell(laboratory_request_item_name)="row">
                                                    <template v-if="row.item?.laboratory_result && row.item?.laboratory_result.length > 0">
                                                        <feather-icon icon="EyeIcon" @click="triggerLabResult({ alias_name: regData.alias_name, name: regData.name, result: row.item?.laboratory_result, costing_name:  row.item?.laboratory_request_item_name, laboratory_request_id:  row.item?.laboratory_request_id })" color="blue"/>
                                                    </template>
                                                    {{ row.value }}
                                                    <!-- <b-table show-empty :fields="laboratoryResultField" :items="row.item?.laboratory_result || []" bordered responsive small thead-tr-class="fs-10" tbody-tr-class="fs-10" class="mt-12">
                                                        <template #cell(no)="subRow">
                                                            <div class="text-center">{{ subRow.index+1 }}</div>
                                                        </template>
                                                        <template #empty="scope">
                                                            <div class="text-center">Hasil Laboratorium sedang dalam Proses</div>
                                                        </template>
                                                    </b-table> -->
                                                </template>
                                            </b-table>
                                        </template>
                                        <div v-else class="col font-italic text-center">Silakan tambah item penunjang laboratorium dari riwayat kajian sebelumnya</div>
                                    </div>
                                </div>
                                <div class="mt-20">
                                    <ModernLabel label="Penunjang Radiologi" />
                                    <div class="mt-8">
                                        <template v-if="form.radiology_request_items.length > 0">
                                            <b-table :fields="RadiologyTableFields" :items="form.radiology_request_items" responsive bordered class="mt-8" style="font-size: 13px;">
                                                <template #cell(no)="row">
                                                    <div class="text-center">{{ row.index+1 }}</div>
                                                </template>
                                                <template #cell(action)="row">
                                                    <div class="d-flex justify-content-center">
                                                        <b-button  variant="danger-600" size="sm" @click="deleteRadItem(row.index)">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </div>
                                                </template>
                                                <template #cell(radiology_request_item_name)="row">
                                                    <template v-if="row.item.radiology_url?.length > 0">
                                                        <feather-icon icon="EyeIcon" @click="triggerRadResult({ alias_name: regData.alias_name, name: regData.name, url: row.item.radiology_url, costing_name: row.item.radiology_request_item_name })" color="blue"/>
                                                    </template>
                                                    {{ row.value }}
                                                </template>
                                            </b-table>
                                        </template>
                                        <div v-else class="col font-italic text-center">Silakan tambah item penunjang radiologi dari riwayat kajian sebelumnya</div>
                                    </div>
                                </div>
                                <div class="mt-20">
                                    <ModernLabel label="Penunjang Fisioterapi" />
                                    <div class="mt-8">
                                        <template v-if="form.physiotherapy_request_items.length > 0">
                                            <b-table :fields="PhysiotherapyTableFields" :items="form.physiotherapy_request_items" responsive bordered class="mt-8" style="font-size: 13px;">
                                                <template #cell(no)="row">
                                                    <div class="text-center">{{ row.index+1 }}</div>
                                                </template>
                                                <template #cell(action)="row">
                                                    <div class="d-flex justify-content-center">
                                                        <b-button  variant="danger-600" size="sm" @click="deletePhyItem(row.index)">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </div>
                                                </template>
                                            </b-table>
                                        </template>
                                        <div v-else class="col font-italic text-center">Silakan tambah item penunjang fisioterapi dari riwayat kajian sebelumnya</div>
                                    </div>
                                </div>
                                <div class="mt-20">
                                    <label class="c-form-label">Obat-obatan Penting yang diberikan selama perawatan</label>
                                    <b-form-textarea rows="3" v-model="form.significant_medications" @change="listenUserInput" autocomplete="off"  />
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Obat yang dibawa pulang</label>
                                    <b-form-textarea rows="3" v-model="form.take_home_medications" @change="listenUserInput" autocomplete="off"  />
                                </div>
                            </div>
                            <div class="card-footer mt-32">
                                <div class="d-flex justify-content-end">
                                    <b-button @click="hideResumeModal()" variant="outline-primary" class="px-24">Kembali</b-button>
                                    <template>
                                        <b-button :disabled="isDisabled" @click="validationResume" variant="primary" class="px-24 ml-8">Simpan</b-button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </template>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <div class="col-lg-2">
                        <!-- <b-button @click="closeMainModal" :disabled="isDisabled" size="sm" variant="orange-500" class="btn-block" :class="active.section == 'PRESC' ? 'visible' : 'invisible'"><feather-icon icon="SaveIcon" class="mr-1" /><span>Selesai Mengisi Asesmen</span></b-button> -->
                    </div>
                </div>
            </template>
        </b-modal>

        <b-modal ref="exitWarningModal" size="sm">
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase text-center w-100">PERUBAHAN BELUM DISIMPAN</div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="text-center fs-13">
                <p>Perubahan yang anda lakukan belum tersimpan. Apakah anda ingin menyimpan perubahan sebelum keluar?</p>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-center w-100">
                    <b-button @click="discardAndExit"size="sm" variant="outline-danger-500">Buang Perubahan</b-button>
                    <b-button @click="saveAndExit" :disabled="disabled_exit" size="sm" variant="w3green" class="ml-1">Simpan & keluar</b-button>
                </div>
            </template>
        </b-modal>

        <LabResultModalView ref="labModalResult"/>
        <RadResultModalView ref="radModalResult"/>
    </div>
</template>
<script>
// import suggestionBar from '@/@core/components/suggestion-bar/suggestionbar.vue';
import dateMixin from "@/mixins/dateMixin";
import optionMixin from '@/mixins/optionMixin';
import utilMixin from '@/mixins/utilMixin';
import LabResultModalView from '@/views/pages/patient/medical_support/LabResultModalView.vue';
import RadResultModalView from '@/views/pages/patient/medical_support/RadResultModalView.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue';
import { mapState } from 'vuex';

export default {
    name: 'Patient_Resume',
    components: { RadResultModalView, LabResultModalView},
    mixins: [dateMixin, optionMixin, utilMixin],
    data() {
        return {
            id: '',
            userData: JSON.parse(localStorage.getItem('userData')),
            reg_des_id: '',
            get_norm: '',
            regData: {},
            insuranceDatum:{},
            emergency_id: "444b3134-5b75-4b67-af72-a558d46716d7",
            inpatient_id: "40f06534-07ec-4221-ad9d-dea08196079f",
            outpatient_id:"3cb626f5-9148-4fb1-8663-3d7f028b01a6",
            urlbe:process.env.VUE_APP_IMAGE,
            urlAsset:process.env.VUE_APP_ASSETS_BE,

            original_resume: {},
            form: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                summary_medical_history: '',
                physical_check: '',
                icd10: [],
                icd10_notes:'',
                icd9cm: [],
                therapy_plan: '',
                significant_medications: '',
                take_home_medications: '-',
                exit_id: '807ca005-5827-4e19-9c97-0bc3d4e924ca',
                exit_anythingelse: '',
                exit_condition_type_id: 1,
                exit_condition_anythingelse: '',
                care_instruction_control: '',
                care_instruction_control_date: '0000-00-00',
                care_instruction_anythingelse: '',
                is_resume_exit: 1,
                laboratory_request_items: [],
                radiology_request_items: [],
                physiotherapy_request_items: [],
                home_education: '-',
                health_facility_item_id: '',
                indication_for_hospitalization: '',
                //Generalis section
                // id: '',
                // doctor_in_charge_id: '',
                // registration_destination_id: '',
                // is_initial_inpatient: 0,
                // is_hemodialysis: 0,
                // is_medical_rehabilitation: 0,
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
            searchlab: '',
            searchrad: '',
            searchphy: '',
            icd9cm_id: '',
            OptionsIcd9cm: [],
            icd10_id: '',
            OptionsIcd10: [],
            soap_list: [],
            // soap_reg_des: [],
            soap_rspi:[],
            active: {
                modal: {
                    main: false,
                },
                advance: {
                    // antopometri: false,
                    generalis: false,
                    // oftalmologis: false,
                    // template_presc: false,
                    template_anam: false,
                },
                // tab: 'LIST',
            },
            isPrintResumeView:false,
            temp_header: {
                alias_name: '',
                name: '',
                norm: '',
                birth_date: '',
                weight: '',
                height: '',
            },
            dynamicHeight: "73vh",

            //untuk history asessmen pasien
            // cppt_history: [],
            // nurse_history: [],
            // soap_history: [],

            //untuk get CPPT terakhir
            cppt_list:[],
            //untuk get TTV terakhir
            vital_list:[],
            //untuk templating resep
            presc_search: '',
            presc_filtered: [],
            presc_list_templating: [],
            presc_inp_templating: {
                id: '',
                name: '',
                form_prescription_parent: [],
                form_prescription: []
            },

            //untuk templating anamnesa
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
            // education_resume_template_list:[],
            tableFields: [
            { key: 'no', label: 'No', thStyle: 'width:50px; text-align: center' },
            { key: 'destination_health_facility_item_name', label: 'Poli/Ruangan' },
            { key: 'doctor_in_charge_name', label: 'Nama Dokter' },
            { key: 'date_added', label: 'Tanggal Resume Dibuat' },
            { key: 'action', label: 'Aksi' },
            ],
            laboratoryResultField: [
                { key: 'no', label: 'No', thStyle: { width: '40px', textAlign: 'center' }  },
                { key: 'Parameter', label: 'Parameter' },
                { key: 'hasil', label: 'Hasil' },
                { key: 'n_rujukan', label: 'Nilai Rujukan' },
                { key: 'satuan', label: 'Satuan' },
                { key: 'metoda', label: 'Metoda' },
            ],
            supportPackage: {
                lab_package_list: [],
                rad_package_list: [],
            },
            items_resume: [],
            paramsInsuranceServiceClassLab:{},
            isDisabled: false,
            ICD10TableFields: [
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px; text-align: center' },
                { key: 'no', label: 'No', thStyle: 'width: 50px; text-align: center' },
                { key: 'title', label: 'ICD10' },
            ],
            ICD9TableFields: [
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px; text-align: center' },
                { key: 'no', label: 'No', thStyle: 'width: 50px; text-align: center' },
                { key: 'title', label: 'ICD9CM' },
            ],
            LaboratoryTableFields: [
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px; text-align: center' },
                { key: 'no', label: 'No', thStyle: 'width: 50px; text-align: center' },
                { key: 'laboratory_request_item_name', label: 'Laboratorium' },
            ],
            RadiologyTableFields: [
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px; text-align: center' },
                { key: 'no', label: 'No', thStyle: 'width: 50px; text-align: center' },
                { key: 'radiology_request_item_name', label: 'Radiologi' },
            ],
            PhysiotherapyTableFields: [
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px; text-align: center' },
                { key: 'no', label: 'No', thStyle: 'width: 50px; text-align: center' },
                { key: 'physiotherapy_request_item_name', label: 'Fisioterapi' },
            ],

            history: {
                cppt: [],
                nurse: [],
                // soap: [],
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

            // soap_load: {
            //     count: 0,
            //     limit_display: 3,
            //     add_display: 3,
            // },

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

            disabled_exit: false,
            prescription_list:[],
        }
    },
    computed: {
        ...mapState({
            itemsLaboratory: state => state.laboratory.package.items,
            itemsRadiology: state => state.radiology.package.items,
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
        }),
    },
    filters:{
        number(value) {
            return Number(value)
        },
    },
    watch : {
        "form.exit_id"(newValue) {
            if (newValue != "adb7ce0b-ede3-4fee-b273-ce159d286732") {
                this.form.exit_anythingelse = ""
            }
        },
        "form.exit_condition_type_id"(newValue) {
            if (newValue != 4) {
                this.form.exit_condition_anythingelse = ""
            }
        },
        "form.care_instruction_control"(newValue) {
            if (newValue != 1) {
                this.form.care_instruction_control_date = "0000-00-00"
                this.form.health_facility_item_id = ""
            }
            if (newValue != 4){
                this.form.care_instruction_anythingelse = ""
            }
        },
    },
    methods: {
        async cloneState() {
            this.original_resume = JSON.parse(JSON.stringify(this.form))
        },

        hasChanges() {
            const formWithoutResumeId = { ...this.form };
            const originalWithoutResumeId = { ...this.original_resume };
            delete formWithoutResumeId.id;
            delete originalWithoutResumeId.id;

            let changes = {};

            Object.keys(formWithoutResumeId).forEach(key => {
                let before = originalWithoutResumeId[key];
                let after = formWithoutResumeId[key];

                // Ubah data null atau string kosong biar jadi sting kosong
                const beforeStr = before === null || before === undefined ? "" : String(before);
                const afterStr = after === null || after === undefined ? "" : String(after);

                // kalo value yang dibandingin sama (kasus tipe datanya beda), di abaikan
                if (beforeStr !== afterStr) {
                    changes[key] = { before, after };
                }
            });

            console.log("Perubahan yang signifikan:", changes);
            return Object.keys(changes).length > 0 ? changes : false;
        },

        onCloseMainModal() {
            if (this.hasChanges()) {
                this.$refs.exitWarningModal.show();
            } else {
                this.active.modal.main = false;
            }
        },

        discardAndExit() {
            // Discard changes and close modal
            const id = this.form.id
            this.form = JSON.parse(JSON.stringify(this.original_resume))
            this.form.id = id
            // console.log('this.form: ', this.form);

            this.$refs.exitWarningModal.hide();
            this.active.modal.main = false;
        },

        async saveAndExit() {
            try {
                this.disabled_exit = true
                await this.validationResume()
                this.$refs.exitWarningModal.hide();
                this.active.modal.main = false;
            } catch (error) {
                console.error('Error save & exit at: ', error);
            } finally {
                this.disabled_exit = false
            }
        },

        listenUserInput() {
            let pre_message = { input_from: 'main-resume', ...this.form}

            try {
                this.$store.dispatch('resume/postResumePreCreate', pre_message)
                .then(
                    async res => {
                        // console.log('successed linten: ', res);
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('error at: ', response)
                })
            } catch (error) {
                console.error('Error catch at: ', error)
            }
        },

        triggerLabResult(message) {
            // console.log('triggerlabresult', message)
            this.$refs.labModalResult.seeLaboratoryResult(message);
        },
        triggerRadResult(message) {
            // console.log('triggerRadResult', message)
            this.$refs.radModalResult.seeRadiologyResult(message);
        },
        getLabPackageListV2(){
            this.$store.dispatch('laboratory/getLaboratoryPackageV2').then((response) => {
                this.supportPackage.lab_package_list = response
            })
        },
        getRadPackageListV2(){
            this.$store.dispatch('radiology/getRadiologyPackageV2').then((response) => {
                this.supportPackage.rad_package_list = response
            })
        },
        hideResumeModal(){
            this.active.modal.main = false
        },
        async validationResume() {
            if (this.form.id == '') {
                this.form.registration_destination_id = this.reg_des_id
                this.form.doctor_in_charge_id = this.regData.doctor_in_charge_id
            }

            // if(Array.isArray(this.form.laboratory_request_items) && this.form.laboratory_request_items.length > 0 ){
            //     for (let i = 0; i < this.form.laboratory_request_items.length; i++) {
            //         const item = this.form.laboratory_request_items[i];
            //         if (item && item.hasOwnProperty('laboratory_result')) {
            //             this.deleteProperties(item,'laboratory_result')
            //         }
            //     }
            // }

            if (this.form.exit_id == 0 || this.form.exit_id == '') {
                this.notification('Warning','CloseIcon','warning','Cara pulang wajib diisi')
            } else {
                try {
                    this.isDisabled = true

                    await this.$store.dispatch('resume/postPutResume', this.form).then(async res => {
                        this.notification('Success','CheckIcon','success','Resume Berhasil Disimpan')
                        await this.resetForm()
                        await this.getResumeList(this.regData.registration_destination_id)
                        this.isDisabled = false
                        // ambil resume_id saat ke route print resume
                        if (this.isPrintResumeView) {
                            this.$emit("getResumePrint", this.$route.params.resume_id);
                        }
                    })
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','Resume Gagal Tersimpan')
                    this.isDisabled = false
                    console.error('Error at: ', error)
                }
            }
        },

        async getResumeList(regDesId) {
            try {
                const response = await this.$store.dispatch('resume/getAllList', { registration_destination_id: regDesId });
                // console.log('resume list response: ', response);

                if (response?.rows?.length > 0) {
                    // state sudah ada data resume
                    // resume hanya bisa sekali pada ranap
                    this.items_resume = response.rows;
                    // console.log('this.items_resume: ', this.items_resume);
                    await this.fillResume(this.items_resume[0]);
                } else {
                    await this.cloneState()
                }
            } catch (error) {
                console.error('Error fetching resume list:', error);
            }
        },

        async fillResume(obj=false) {
            await this.$nextTick(); // wait for any prior render
            this.resetForm()
            await this.$nextTick();
            if(obj){
                this.form.id = obj.id ? obj.id : ''
                this.form.registration_destination_id = obj.registration_destination_id ? obj.registration_destination_id : ''
                this.form.doctor_in_charge_id = obj.doctor_in_charge_id ? obj.doctor_in_charge_id : ''
                this.form.summary_medical_history = obj.summary_medical_history ? obj.summary_medical_history : ''
                this.form.physical_check = obj.physical_check ? obj.physical_check : ''

                // =============================================================================================
                // this.form.id = obj?.id || ''
                // this.form.registration_destination_id = obj?.registration_destination_id || this.regData.registration_destination_id
                // this.form.doctor_in_charge_id = obj?.doctor_in_charge_id || this.regData.doctor_in_charge_id
                this.form.head = obj?.head || 1
                this.form.head_hair_color = obj?.head_hair_color || 1
                this.form.head_hair_condition = obj?.head_hair_condition || 1
                this.form.eye_conjunctive = obj?.eye_conjunctive || 1
                this.form.eye_sclera = obj?.eye_sclera || 1
                this.form.eye_pupil = obj?.eye_pupil || 1
                this.form.eye_light_reflex = obj?.eye_light_reflex || 1
                this.form.mouth_mucosa = obj?.mouth_mucosa || 1
                this.form.mouth_discoloration = obj?.mouth_discoloration || 1
                this.form.mouth_tongue = obj?.mouth_tongue || 1
                this.form.mouth_caries = obj?.mouth_caries || 1
                this.form.neck_kgb = obj?.neck_kgb || 1
                this.form.neck_trachea_position = obj?.neck_trachea_position || 1
                this.form.neck_massa = obj?.neck_massa || 1
                this.form.neck_jvp = obj?.neck_jvp || 1
                this.form.thorax_insp_chest = obj?.thorax_insp_chest || 1
                this.form.thorax_insp_retraction = obj?.thorax_insp_retraction || 1
                this.form.thorax_palp_chest = obj?.thorax_palp_chest || 1
                this.form.thorax_palp_fremitus = obj?.thorax_palp_fremitus || 1
                this.form.thorax_palp_crepitation = obj?.thorax_palp_crepitation || 1
                this.form.thorax_ausk_pul_vasiculer = obj?.thorax_ausk_pul_vasiculer || 1
                this.form.thorax_ausk_pul_rhonki = obj?.thorax_ausk_pul_rhonki || 1
                this.form.thorax_ausk_pul_wheezing = obj?.thorax_ausk_pul_wheezing || 1
                this.form.thorax_ausk_cor_heart_sound = obj?.thorax_ausk_cor_heart_sound || 1
                this.form.thorax_ausk_cor_murmur = obj?.thorax_ausk_cor_murmur || 1
                this.form.thorax_ausk_cor_gallop = obj?.thorax_ausk_cor_gallop || 1
                this.form.abdomen_supel = obj?.abdomen_supel || 1
                this.form.abdomen_bowel_sounds = obj?.abdomen_bowel_sounds || 1
                this.form.abdomen_tenderness = obj?.abdomen_tenderness || 1
                this.form.abdomen_hepar_lien = obj?.abdomen_hepar_lien || 1
                this.form.abdomen_other_physical_check = obj?.abdomen_other_physical_check || ''
                this.form.extremity_acral = obj?.extremity_acral || 1
                this.form.extremity_crt = obj?.extremity_crt || 1
                this.form.extremity_edema = obj?.extremity_edema || 1
                this.form.genitalia = obj?.genitalia || ''
                this.form.localist_status = obj?.localist_status || ''
                this.form.neurologist_status = obj?.neurologist_status || ''
                this.form.ophthal_status = obj?.ophthal_status || ''

                if (obj.icd10) {
                    const mappedIcd10 = obj.icd10.map(item => {
                        return {
                            icd10_id: item.icd10_id,
                            value: item.icd10_id,
                            title: `${item.icd10_code} ${item.icd10_name}`,
                            action: "action"
                        };
                    });
                    this.form.icd10 = mappedIcd10;
                }

                this.form.icd10_notes = obj?.icd10_notes || ''

                if (obj.icd9cm) {
                    const mappedIcd9cm = obj.icd9cm.map(item => {
                        return {
                            icd9cm_id: item.icd9cm_id,
                            value: item.icd9cm_id,
                            title: `${item.icd9cm_code} ${item.icd9cm_name}`,
                            action: "action"
                        };
                    });
                    this.form.icd9cm = mappedIcd9cm;
                }

                this.form.therapy_plan = obj?.therapy_plan || ''

                this.form.significant_medications = obj.significant_medications ? obj.significant_medications : ''
                this.form.take_home_medications = obj.take_home_medications ? obj.take_home_medications : '-'
                this.form.exit_id = obj.exit_id ? obj.exit_id : ''
                this.form.exit_anythingelse = obj.exit_anythingelse ? obj.exit_anythingelse : ''
                this.form.exit_condition_type_id = obj.exit_condition_type_id ? obj.exit_condition_type_id : ''
                this.form.exit_condition_anythingelse = obj.exit_condition_anythingelse ? obj.exit_condition_anythingelse : ''
                this.form.care_instruction_control = obj.care_instruction_control ? obj.care_instruction_control : ''
                this.form.care_instruction_control_date = obj.care_instruction_control_date ? obj.care_instruction_control_date : '0000-00-00'
                this.form.care_instruction_anythingelse = obj.care_instruction_anythingelse ? obj.care_instruction_anythingelse : ''
                this.form.is_resume_exit =obj.is_resume_exit ? obj.is_resume_exit :  1

                if (obj.laboratory_request_items) {
                    obj.laboratory_request_items.map(item => {
                        let temp = {
                            laboratory_request_item_id: item?.item_id,
                            laboratory_request_item_name: item?.costing_name,
                            laboratory_package_id: item?.laboratory_package_id,
                            laboratory_result: item?.result,
                            laboratory_request_id: item?.laboratory_request_id
                        };

                        if (this.form.laboratory_request_items.some(item => item.laboratory_package_id === temp.laboratory_package_id)) {
                            this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan item laboratorium yang sama')
                        } else {
                            this.form.laboratory_request_items.push(temp)
                        }
                    });
                }

                if (obj.radiology_request_items) {
                    obj.radiology_request_items.map(item => {
                        let temp = {
                            radiology_request_item_id: item?.item_id,
                            radiology_request_item_name: item?.costing_name,
                            radiology_package_id: item?.radiology_package_id,
                            radiology_url: item?.url
                        };

                        if (this.form.radiology_request_items.some(item => item.radiology_package_id === temp.radiology_package_id)) {
                            this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan item radiologi yang sama')
                        } else {
                            this.form.radiology_request_items.push(temp)
                        }
                    });
                }

                if (obj.physiotherapy_request_items) {
                    obj.physiotherapy_request_items.map(item => {
                        let temp = {
                            physiotherapy_request_item_id: item?.item_id,
                            physiotherapy_request_item_name: item?.costing_name,
                            physiotherapy_package_id: item?.physiotherapy_package_id
                        };

                        if (this.form.physiotherapy_request_items.some(item => item.physiotherapy_package_id === temp.physiotherapy_package_id)) {
                            this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan item Fisioterapi yang sama')
                        } else {
                            this.form.physiotherapy_request_items.push(temp)
                        }
                    });
                }
                this.form.home_education = obj.home_education ? obj.home_education : '-'
                this.form.health_facility_item_id = obj.health_facility_item_id ? obj.health_facility_item_id : ''
                this.form.indication_for_hospitalization = obj.indication_for_hospitalization ? obj.indication_for_hospitalization : ''

                await this.cloneState()
            }
        },

        async resetForm(){
            this.form.id = ''
            this.form.registration_destination_id = ''
            this.form.doctor_in_charge_id = ''
            this.form.summary_medical_history = ''
            this.form.physical_check = ''
            this.form.icd10 = []
            this.form.icd10_notes = ''
            this.form.icd9cm = []
            this.form.therapy_plan = ''
            this.form.significant_medications = ''
            this.form.take_home_medications = '-'
            this.form.exit_id = '807ca005-5827-4e19-9c97-0bc3d4e924ca'
            this.form.exit_anythingelse = ''
            this.form.exit_condition_type_id = 1
            this.form.exit_condition_anythingelse = ''
            this.form.care_instruction_control = ''
            this.form.care_instruction_control_date = '0000-00-00'
            this.form.care_instruction_anythingelse = ''
            this.form.is_resume_exit = 1
            this.form.laboratory_request_items = []
            this.form.radiology_request_items = []
            this.form.physiotherapy_request_items = []
            this.form.home_education = '-'
            this.form.health_facility_item_id = ''
            this.form.indication_for_hospitalization = ''

            // this.isPrintResumeView = false
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        refresh () {
            router.go(router.currentRoute)
        },
        async autoFill(){
            //auto-fill hanya untuk resume yang di create pertama kali
            //auto-fill ringkasan riwayat penyakit
            await this.$store.dispatch('soap/getSoapDetail',
            { registration_destination_id: this.regData.registration_destination_id, is_initial_inpatient: 1 }).then( async data => {
                // this.soap_reg_des = data
                // console.log(data,'soap/getSoapDetail')
                //auto-fill ringkasan riwayat penyakit assesmen dokter terakhir
                await this.fillSummaryMedicalHistory(data)
                //auto-fill status generalis assesmen dokter terakhir
                await this.fillSTGeneralis(data)
                //auto-fill icd10
                await this.fillICD10(data)
                //auto-fill icd9cm
                await this.fillICD9CM(data)
                //auto-fill laboratorium
                await this.fillLaboratory(data)
                //auto-fill radiologi
                await this.fillRadiology(data)
                //auto-fill fisioterapi
                await this.fillPhysiotherapy(data)
            })

            //auto-fill indikasi rawat inap
            await this.$store.dispatch('soapinpatient/getDetailSoapInpatient',
            { registration_destination_ref_id: this.regData.registration_destination_ref_id }).then(data => {
                this.soap_spri = data
                this.fillIndicationPlan(this.soap_spri[0])
            })

            //auto-fill ttv terakhir
            await this.fillTTV(this.vital_list)

            //auto-fill obat yang dibawa pulang dari cppt terakhir
            // this.fillTakeHomeMedic(this.cppt_list)

            //auto-fill obat dari bangsal terakhir pasien di rawat
            this.prescription_list = await this.getPrescriptionList(this.regData.registration_destination_id)
            this.fillPrescription(this.prescription_list)
        },
        getData (id) {
            // console.log('masuk getdata', id)
			this.$store.dispatch('registration/getById', id).then(data => {
                // console.log('masuk getdataID', data)
                this.regData = data[0]
                let regId = this.regData.registration_id
                let regDesId = this.regData.registration_destination_id
                let regNorm = this.regData.norm

                this.paramsInsuranceServiceClassLab = {
                    type_costing_id:4,
                }
                this.$store.dispatch('laboratory/getPackageAll', this.paramsInsuranceServiceClassLab)

                this.$store.dispatch('soap/getSoapDetail', { registration_id: regId }).then(data => {
                    this.soap_list = data
                    // console.log('soap list data', this.soap_list)
                })

                // this.getResumeList(regDesId)
                this.assignTempHeader()

                this.getInsurance()

                //history
                // this.getCpptHistory(regNorm, regId)
                // this.getNurseHistory(regNorm, regId)

                this.getCpptHistory(regNorm)
                this.getNurseHistory(regNorm)
                this.getLaboratoryHistory(regNorm)
                this.getRadiologyHistory(regNorm)
                this.getPhysiotherapyHistory(regNorm)
                this.getHistoryResumeList(regNorm)
                this.getExaminationList(regNorm)

                //get Cppt list
                // this.getCpptList(regDesId)

                //get TTVlist
                this.getVitalList(regDesId)

                //sementara jgn pake soaphistory pakenya soap_list
                // this.getSoapHistory(regNorm)
            })
        },
        fillIndicationPlan(soap_data){
            this.form.indication_for_hospitalization = this.getIndicationName(soap_data?.indication)
        },

        async fillSummaryMedicalHistory(soap_data) {
            let generalNarrative = [];
            // console.log('ini fillsummarymedical history', soap_data)
            if (Array.isArray(soap_data) && soap_data.length > 0 ){ //keperluan auto-fill
                for (let i = 0; i < soap_data.length; i++) {
                    if (this.insuranceDatum.company_name != 'BPJS KETENAGAKERJAAN') {
                        let combineNarrative = `Pasien dirawat selama ${soap_data[i]?.los || '-'} di ${soap_data[i]?.health_facility_item_name || '-'}.`;
                        generalNarrative.push(combineNarrative);
                    }

                    // Tindakan narrative collected in one array
                    // let tindakanList = [];

                    // if (soap_data[i]?.transaction_doctor?.length > 0) {
                    //     for (let j = 0; j < soap_data[i].transaction_doctor.length; j++) {
                    //         const tindakan = soap_data[i].transaction_doctor[j];
                    //         if (tindakan?.costing_name) {
                    //             tindakanList.push(tindakan.costing_name);
                    //         }
                    //     }
                    // }

                    // Only push tindakan narrative once per SOAP entry
                    // if (tindakanList.length > 0) {
                    //     const tindakanNarrative = `dengan tindakan ${tindakanList.join(', ')}`;
                    //     generalNarrative.push(tindakanNarrative);
                    // }

                    const complaint = `${soap_data[i]?.main_complaint || '-'}`
                    generalNarrative.push(complaint)

                    const disease = `${soap_data[i]?.history_disease_now || '-'}`
                    generalNarrative.push(disease)
                }
            }
            else { // object bukan array (keperluan copy referensi)
                if (this.insuranceDatum.company_name != 'BPJS KETENAGAKERJAAN') {
                    let combineNarrative = `Pasien dirawat selama ${soap_data?.los || '-'} di ${soap_data?.health_facility_item_name || '-'}.`;
                    generalNarrative.push(combineNarrative);
                }

                // Tindakan narrative collected in one array
                // let tindakanList = [];

                // if (soap_data?.transaction_doctor?.length > 0) {
                //     for (let j = 0; j < soap_data.transaction_doctor.length; j++) {
                //         const tindakan = soap_data.transaction_doctor[j];
                //         if (tindakan?.costing_name) {
                //             tindakanList.push(tindakan.costing_name);
                //         }
                //     }
                // }

                // // Only push tindakan narrative once per SOAP entry
                // if (tindakanList.length > 0) {
                //     const tindakanNarrative = `dengan tindakan ${tindakanList.join(', ')}`;
                //     generalNarrative.push(tindakanNarrative);
                // }

                const complaint = `${soap_data?.main_complaint || '-'}`
                generalNarrative.push(complaint)

                const disease = `${soap_data?.history_disease_now || '-'}`
                generalNarrative.push(disease)
            }

            this.form.summary_medical_history = generalNarrative.join('\n');
        },

        async fillTTV(vital_data){
            let finalNarrative = '';
            //get TTV terakhir (array) || untuk copy ttv (object)
            let data = null
            if(Array.isArray(vital_data) && vital_data.length > 0 ){
                data = vital_data[0]
            } else {
                data = vital_data
            }
            // console.log('data vital terakhir', data)

            const systolic = data.systolic_blood_pressure || data.systolic || 0
            const diastolic = data.diastolic_blood_pressure || data.diastolic || 0
            const pulse = data.pulse_frequency || data.pulse || 0
            const respiration = data.breath_frequency || data.respiratory_rate || 0
            const oximetry = data.oximetry_frequency || data.oxygen_saturation || 0

            finalNarrative += `Systolic: ${systolic} mmHg, `
            finalNarrative += `Diastolic: ${diastolic} mmHg, `
            finalNarrative += `Frekuensi Nadi: ${pulse} x/mnt, `
            finalNarrative += `Frekuensi Nafas: ${respiration} x/mnt, `
            finalNarrative += `Saturasi Oksigen: ${oximetry} %, `

            finalNarrative += `Suhu: ${data?.temperature || 0 } ᵒC, `
            finalNarrative += `Oksigenasi: ${data?.oxygen_type_id ? this.getOxygenName(data.oxygen_type_id) : '-'}, `
            finalNarrative += `Kesadaran: ${data?.aware ? this.getAwareName(data.aware) : ''}, `
            finalNarrative += `GCS Mata: ${data?.eye_score ? this.getGcsEyeName(data.eye_score) : '-'}, `
            finalNarrative += `GCS Verbal: ${data?.verbal_score ? this.getGcsVerbalName(data.verbal_score) : '-'}, `
            finalNarrative += `GCS Motorik: ${data?.motor_score ? this.getGcsMotoricName(data.motor_score) : '-'}, `
            finalNarrative += `Berat Badan: ${data?.weight || 0} kg, `
            finalNarrative += `Tinggi Badan: ${data?.height || 0} cm, `

            if (data?.imt) {
            finalNarrative += `IMT: ${data.imt || 0} kg/m2, `
            } else {
                if (data?.weight && data?.height) {
                    const imt = this.imtCalculation(data.weight, data.height)
                    finalNarrative += `IMT: ${imt || 0} kg/m2, `
                } else {
                    finalNarrative += `IMT: 0 kg/m2, `
                }
            }

            // finalNarrative += `Lingkar Kepala: ${data?.head_circumference || 0} cm, `
            // finalNarrative += `Lingkar Dada: ${data?.chest_circumference || 0} cm, `
            // finalNarrative += `Lingkar Lengan Atas: ${data?.arm_circumference || 0} cm, `
            // finalNarrative += `Lingkar Perut: ${data?.stomach_circumference || 0} cm, `
            // finalNarrative += `Lingkar Lutut: ${data?.knee_circumference || 0} cm, `
            finalNarrative += '\n'

            // Remove trailing comma and space
            finalNarrative = finalNarrative.trim().replace(/,$/, '')

            // console.log('final narrative', finalNarrative)
            this.form.physical_check = finalNarrative;
        },
        async fillSTGeneralis(soap_data) {
            // console.log('fillSTGeneralis', soap_data);
            //get data asesmen dokter terakhir (array) || untuk copy st generalis (object)
            let data = null
            if(Array.isArray(soap_data) && soap_data.length > 0 ){
                data = soap_data[0]
            } else {
                data = soap_data
            }

            // this.form.id = data?.id || '';
            // this.form.registration_destination_id = data?.registration_destination_id || this.regData.registration_destination_id;
            // this.form.doctor_in_charge_id = data?.doctor_in_charge_id || this.regData.doctor_in_charge_id;

            this.form.head = data?.head || 1;
            this.form.head_hair_color = data?.head_hair_color || 1;
            this.form.head_hair_condition = data?.head_hair_condition || 1;
            this.form.eye_conjunctive = data?.eye_conjunctive || 1;
            this.form.eye_sclera = data?.eye_sclera || 1;
            this.form.eye_pupil = data?.eye_pupil || 1;
            this.form.eye_light_reflex = data?.eye_light_reflex || 1;
            this.form.mouth_mucosa = data?.mouth_mucosa || 1;
            this.form.mouth_discoloration = data?.mouth_discoloration || 1;
            this.form.mouth_tongue = data?.mouth_tongue || 1;
            this.form.mouth_caries = data?.mouth_caries || 1;
            this.form.neck_kgb = data?.neck_kgb || 1;
            this.form.neck_trachea_position = data?.neck_trachea_position || 1;
            this.form.neck_massa = data?.neck_massa || 1;
            this.form.neck_jvp = data?.neck_jvp || 1;
            this.form.thorax_insp_chest = data?.thorax_insp_chest || 1;
            this.form.thorax_insp_retraction = data?.thorax_insp_retraction || 1;
            this.form.thorax_palp_chest = data?.thorax_palp_chest || 1;
            this.form.thorax_palp_fremitus = data?.thorax_palp_fremitus || 1;
            this.form.thorax_palp_crepitation = data?.thorax_palp_crepitation || 1;
            this.form.thorax_ausk_pul_vasiculer = data?.thorax_ausk_pul_vasiculer || 1;
            this.form.thorax_ausk_pul_rhonki = data?.thorax_ausk_pul_rhonki || 1;
            this.form.thorax_ausk_pul_wheezing = data?.thorax_ausk_pul_wheezing || 1;
            this.form.thorax_ausk_cor_heart_sound = data?.thorax_ausk_cor_heart_sound || 1;
            this.form.thorax_ausk_cor_murmur = data?.thorax_ausk_cor_murmur || 1;
            this.form.thorax_ausk_cor_gallop = data?.thorax_ausk_cor_gallop || 1;
            this.form.abdomen_supel = data?.abdomen_supel || 1;
            this.form.abdomen_bowel_sounds = data?.abdomen_bowel_sounds || 1;
            this.form.abdomen_tenderness = data?.abdomen_tenderness || 1;
            this.form.abdomen_hepar_lien = data?.abdomen_hepar_lien || 1;
            this.form.abdomen_other_physical_check = data?.abdomen_other_physical_check || '';
            this.form.extremity_acral = data?.extremity_acral || 1;
            this.form.extremity_crt = data?.extremity_crt || 1;
            this.form.extremity_edema = data?.extremity_edema || 1;
            this.form.genitalia = data?.genitalia || '';
            this.form.localist_status = data?.localist_status || '';
            this.form.neurologist_status = data?.neurologist_status || '';
            this.form.ophthal_status = data?.ophthal_status || '';
        },

        // async fillTakeHomeMedic(cppt_list){
        //     // console.log('fillTakeHomeMedic',cppt_list)
        //     if(Array.isArray(cppt_list) && cppt_list.length > 0 ){
        //         let lastCppt = cppt_list[0]
        //         this.copyCpptPrescription(lastCppt)

        //     }
        // },

        fillPrescription(prescription_list){
            let obatPulangRaw = prescription_list.filter(item => item.type_prescription_id == 2)
            let obatSelamaDirawatRaw = prescription_list.filter(item => item.type_prescription_id != 2)

            let obatPulang = this.removeDuplicatesById(obatPulangRaw)
            let obatSelamaDirawat = this.removeDuplicatesById(obatSelamaDirawatRaw)

            if (obatPulang.length > 0) {
                let obatPulangArr = []
                for (let i = 0; i < obatPulang.length; i++) {
                    const el_i = obatPulang[i];

                    if (el_i.form_prescription?.length > 0) {
                        // let text = ''
                        for (let j = 0; j < el_i.form_prescription.length; j++) {
                            const el_j = el_i.form_prescription[j];
                            let temp = ''
                            temp = el_j.pharmacy_catalog_name
                            // text += temp + '\n'
                            obatPulangArr.push(temp)
                        }
                        this.form.take_home_medications = obatPulangArr.filter(Boolean).join('\n')
                    }
                }
            }

            if (obatSelamaDirawat.length > 0) {
                let obatSelamaDirawatArr = []
                for (let i = 0; i < obatSelamaDirawat.length; i++) {
                    const el_i = obatSelamaDirawat[i];

                    if (el_i.form_prescription?.length > 0) {
                        // let text = ''
                        for (let j = 0; j < el_i.form_prescription.length; j++) {
                            const el_j = el_i.form_prescription[j];
                            let temp = ''
                            temp = el_j.pharmacy_catalog_name
                            // text += temp + '\n'
                            obatSelamaDirawatArr.push(temp)
                        }
                        this.form.significant_medications = obatSelamaDirawatArr.filter(Boolean).join('\n')
                    }
                }
            }
        },

        removeDuplicatesById(list) {
            const seen = new Set();
            return list.filter(item => {
                if (seen.has(item.type_prescription_id)) return false;
                    seen.add(item.type_prescription_id);
                return true;
            });
        },

        // fillEducationResume(obj){
        //     if(obj){
        //         this.form.home_education = obj.education_while_at_home
        //     }
        // },

        async fillICD10(data) {
            const icdSource = Array.isArray(data) ? data[0] : data;

            if (icdSource?.icd10 && Array.isArray(icdSource.icd10)) {
                this.form.icd10 = icdSource.icd10.map(item => {
                    return {
                        icd10_id: item.icd10_id,
                        value: item.icd10_id,
                        title: `${item.code} ${item.name}`,
                        action: "action"
                    };
                });
            } else {
                this.form.icd10 = [];
            }

            if (icdSource?.icd10_notes){
                this.form.icd10_notes = icdSource.icd10_notes
            }
        },

        async fillICD9CM(data){
            const icdSource = Array.isArray(data) ? data[0] : data;

            if (icdSource?.icd9cm && Array.isArray(icdSource.icd9cm)) {
                this.form.icd9cm = icdSource.icd9cm.map(item => {
                    return {
                        icd9cm_id: item.icd9cm_id,
                        value: item.icd9cm_id,
                        title: `${item.code} ${item.name}`,
                        action: "action"
                    };
                });
            } else {
                this.form.icd9cm = [];
            }

            if (icdSource?.therapy_plan){
                this.form.therapy_plan = icdSource.therapy_plan
            }
        },

        async fillLaboratory(data) {
            if(Array.isArray(data)){
                const items = data[0]?.laboratory_request_new || [];
                items.forEach(item => {
                    if(Array.isArray(item.laboratory_request_items) && item.laboratory_request_items.length > 0){
                        const laboReqItems = item.laboratory_request_items
                        laboReqItems.forEach(item => {
                            const temp = {
                                laboratory_request_item_id: item.id,
                                laboratory_request_item_name: item.costing_name,
                                laboratory_package_id: item.laboratory_package_id,
                                laboratory_result: item?.result,
                                laboratory_request_id: item?.laboratory_request_id
                            };

                            const exists = this.form.laboratory_request_items.some(
                                i => i.laboratory_package_id === temp.laboratory_package_id
                            );

                            if (exists) {
                                this.notification('Warning', 'CloseIcon', 'warning', 'Tidak dapat menambahkan item laboratorium yang sama');
                            } else {
                                this.form.laboratory_request_items.push(temp);
                            }
                        })
                    }
                });
            }
        },
        async fillRadiology(data) {
            if(Array.isArray(data)){
                const items = data[0]?.radiology_request_new || [];
                items.forEach(item => {
                    if(Array.isArray(item.radiology_request_items) && item.radiology_request_items.length > 0){
                        const radReqItems = item.radiology_request_items
                        radReqItems.forEach(item => {
                            const temp = {
                                radiology_request_item_id: item.id,
                                radiology_request_item_name: item.costing_name,
                                radiology_package_id: item.radiology_package_id,
                                radiology_url: item?.url
                            };

                            const exists = this.form.radiology_request_items.some(
                                i => i.radiology_package_id === temp.radiology_package_id
                            );

                            if (exists) {
                                this.notification('Warning', 'CloseIcon', 'warning', 'Tidak dapat menambahkan item radiologi yang sama');
                            } else {
                                this.form.radiology_request_items.push(temp);
                            }
                        })
                    }
                });
            }
        },

        async fillPhysiotherapy(data) {
            if(Array.isArray(data)){
                const items = data[0]?.physiotherapy_request_items || [];
                items.forEach(item => {
                    const temp = {
                        physiotherapy_request_item_id: item.id,
                        physiotherapy_request_item_name: item.costing_name,
                        physiotherapy_package_id: item.physiotherapy_package_id
                    };

                    const exists = this.form.physiotherapy_request_items.some(
                        i => i.physiotherapy_package_id === temp.physiotherapy_package_id
                    );

                    if (exists) {
                        this.notification('Warning', 'CloseIcon', 'warning', 'Tidak dapat menambahkan item Fisioterapi yang sama');
                    } else {
                        this.form.physiotherapy_request_items.push(temp);
                    }
                });
            }
        },
        imtCalculation(weight, height) {
            const brt_badan = parseFloat(weight) || 0
            const tng_badan = parseFloat(height) || 0
            let imtResult = ''
            if (brt_badan && tng_badan) {
                const heightInMeters = tng_badan / 100
                const imt = brt_badan / (heightInMeters ** 2)
                imtResult = imt.toFixed(1)
            }
            return imtResult

        },
        onAdd10(event) {
            this.icd10_id = ''

            if (this.form.icd10.some(item => item.icd10_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD10 yang sama')
            } else {
                this.form.icd10.push({ icd10_id:event.value, title:event.title, value:event.value, action: 'action'})
                this.listenUserInput()
            }
        },
        delete10 (index) {
            this.form.icd10.splice(index, 1)
            this.listenUserInput()
        },
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
        onAdd9cm (event) {
            this.icd9cm_id = ''
            if (this.form.icd9cm.some(item => item.icd9cm_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD9CM yang sama')
            } else {
                this.form.icd9cm.push({ icd9cm_id:event.value, title:event.title, value:event.value, action: 'action'})
                this.listenUserInput()
            }
        },
        delete9cm (index) {
            this.form.icd9cm.splice(index, 1)
            this.listenUserInput()
        },
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

        async parentTrigger(message, gate='add_gate') {
            if (gate == 'add_gate') {
                await this.getResumeList(this.regData.registration_destination_id)
                if(this.items_resume.length == 0){
                    this.isPrintResumeView = false
                    await this.resetForm()
                    await this.autoFill()
                    await this.cloneState()
                }
            } else if (gate == 'print_edit_gate') {
                this.isPrintResumeView = true
                await this.getResumeList(this.regData.registration_destination_id)
                // console.log('print_edit_Gate')
            }

            this.active.modal.main = true
        },
        assignTempHeader() {
            this.temp_header.alias_name = this.regData?.alias_name
            this.temp_header.name = this.regData?.name
            this.temp_header.norm = this.regData?.norm
            this.temp_header.birth_date = this.regData?.birth_date
            this.temp_header.weight = this.regData?.ttv?.weight
            this.temp_header.height = this.regData?.ttv?.height
        },
        getInsurance() {
            if(Array.isArray(this.regData.registration_insurance) && this.regData.registration_insurance.length > 0 ) {
                let getFirstInsurance = this.regData.registration_insurance[0]
                this.insuranceDatum = getFirstInsurance
            }
        },
        filterItemAnamn() {
            if (!this.anamn_search) return this.anamn_filtered = [...this.anamn_list_templating]

            const query = this.anamn_search.toLowerCase()
            this.anamn_filtered = this.anamn_list_templating.filter(item => item.name.toLowerCase().includes(query))

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

        // async getSoapHistory(norm) {
        //     const param = {
        //         norm: norm,
        //         limit: this.soap_load.limit_display
        //     }
        //     try {
        //         await this.$store.dispatch('soap/getHistorySOAP', param)
        //         .then(
        //             response => {
        //                 this.history.soap = response.rows
        //                 this.soap_load.count = response.iTotal
        //             }
        //         )
        //         .catch(({message, debug, response}) => {
        //             console.error('Error GET soap/getHistorySOAP at: ', response);
        //         })
        //     } catch (error) {
        //         console.error('Error catch while GET soap/getHistorySOAP at: ', error);
        //     }
        // },

        // loadMoreSoap() {
        //     this.soap_load.limit_display += this.soap_load.add_display
        //     this.getSoapHistory(this.regData.norm)
        // },

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

        async getHistoryResumeList(norm) {
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
            this.getHistoryResumeList(this.regData.norm)
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

        // getCpptList(regdesID) {
        //     let param = { registration_destination_id: regdesID }
        //     try {
        //         this.$store.dispatch('cppt/getListCPPT', param).then((response) => {
        //             // console.log('getCpptList resp', response)
        //             this.cppt_list = response?.rows
        //         }, error => {
        //             console.error('Error while cppt/getListCPPT at: ', error);
        //         })
        //     } catch (error) {
        //         console.error('Error catch while GET cppt/getListCPPT at: ', error);
        //     }
        // },


        async getPrescriptionList(regdesID) {
            try {
                const response = await this.$store.dispatch('prescription/getDetail', {
                    registration_destination_id: regdesID,
                })
                return response?.length > 0 ? response : [];
            } catch (error) {
                console.error('Error fetching prescription:', error);
                return [];
            }
        },

        getVitalList(regdesID) {
            try {
                this.$store.dispatch('vitalsign/getListVitalSign', { registration_destination_id: regdesID }).then((response) => {
                    this.vital_list = response.rows
                }, error => {
                    console.error('Error while GET vitalsign/getListVitalSign at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET vitalsign/getListVitalSign at: ', error);
            }
        },

        copySoapLaboratory(arry){
            if (arry.laboratory_request_new && arry.laboratory_request_new.length > 0 ) {
                for (let i = 0; i < arry.laboratory_request_new.length; i++) {
                    arry.laboratory_request_new[i].laboratory_request_items.map(item => {
                        let temp = {
                            laboratory_request_item_id: item?.id,
                            laboratory_request_item_name: item?.costing_name,
                            laboratory_package_id: item?.laboratory_package_id,
                            laboratory_result: item?.result,
                            laboratory_request_id: item?.laboratory_request_id
                        };

                        if (this.form.laboratory_request_items.some(item => item.laboratory_package_id === temp.laboratory_package_id)) {
                            this.notification('Warning', 'CloseIcon', 'warning', 'Tidak dapat menambahkan item laboratorium yang sama')
                        } else {
                            this.form.laboratory_request_items.push(temp)
                            this.listenUserInput()
                        }
                    });
                }
            }
        },
        copySoapDiagnosis(obj){
            if (obj.icd10) {
                const mappedIcd10 = obj.icd10.map(item => {
                    return {
                        icd10_id: item.icd10_id,
                        value: item.icd10_id,
                        title: `${item.code} ${item.name}`,
                        action: "action"
                    };
                });
                this.form.icd10 = mappedIcd10;
            }
        },

        copySoapProcedure(obj){
            if (obj.icd9cm) {
                const mappedIcd9cm = obj.icd9cm.map(item => {
                    return {
                        icd9cm_id: item.icd9cm_id,
                        value: item.icd9cm_id,
                        title: `${item.code} ${item.name}`,
                        action: "action"
                    };
                });
                this.form.icd9cm = mappedIcd9cm;
            }
        },

        copySoapGeneralis(obj){
            this.fillSTGeneralis(obj)
        },

        copySoapVital(obj){
            this.fillTTV(obj)
        },

        copyAnamnesa(obj){
            this.fillSummaryMedicalHistory(obj)
        },
        nurseAnamnesa(obj){
            this.fillSummaryMedicalHistory(obj)
        },
        nurseVitalSign(obj){
            this.fillTTV(obj)
        },
        // copyAllStudies(event, obj) {
        //     this.copySubjective(obj)
        //     this.copyObjective(obj)
        //     this.copyAssesment(obj)
        //     this.copyPlan(obj)
        //     this.copyLaboratory(obj)
        //     this.copySoapRadiology(obj)
        //     this.copyPrescription(obj)

        //     if (event) {
        //         const button = event.target;
        //         button.innerText = 'Copied All';
        //         setTimeout(() => {
        //             button.innerText = 'Copy All';
        //         }, 1000);
        //     }
        // },
        copySelected(event, func, obj) {
            const button = event.target;
            button.innerText = 'Copied';

            func(obj);

            setTimeout(() => {
                button.innerText = 'Copy';
            }, 1000);
        },

        copyHLaboratory(obj) {
            if (obj.laboratory_request_items) {
                const mappedLab = obj.laboratory_request_items.map(item => {
                    return {
                        laboratory_request_item_name: item?.laboratory_package_name,
                        laboratory_package_id: item?.laboratory_package_id,
                        laboratory_request_item_id: item?.id,
                        laboratory_result: item?.result,
                        laboratory_request_id: item?.laboratory_request_id
                    };
                });
                this.form.laboratory_request_items = mappedLab;
                this.listenUserInput()
            }
        },

        copyItemHLaboratory(obj) {
            if (obj) {
                let temp = {
                    laboratory_request_item_id: obj?.id,
                    laboratory_request_item_name: obj?.laboratory_package_name,
                    laboratory_package_id: obj?.laboratory_package_id,
                    laboratory_result: obj?.result,
                    laboratory_request_id: obj?.laboratory_request_id
                };

                if (this.form.laboratory_request_items.some(item => item.laboratory_package_id === temp.laboratory_package_id)) {
                    this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan item laboratorium yang sama')
                } else {
                    this.form.laboratory_request_items.push(temp)
                    this.listenUserInput()
                }
            }
        },

        copyHRadiology(obj) {
            if (obj.radiology_request_items) {
                const mappedRad = obj.radiology_request_items.map(item => {
                    return {
                        radiology_request_item_id: item?.id,
                        radiology_request_item_name: item?.radiology_package_name,
                        radiology_package_id: item?.radiology_package_id,
                        radiology_url: item?.url,
                    };
                });
                this.form.radiology_request_items = mappedRad;
                this.listenUserInput()
            }
        },

        copyItemHRadiology(obj) {
            if (obj) {
                let temp = {
                    radiology_request_item_id: obj?.id,
                    radiology_request_item_name: obj?.radiology_package_name,
                    radiology_package_id: obj?.radiology_package_id,
                    radiology_url: obj?.url,
                };

                if (this.form.radiology_request_items.some(item => item.radiology_package_id === temp.radiology_package_id)) {
                    this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan item radiologi yang sama')
                } else {
                    this.form.radiology_request_items.push(temp)
                    this.listenUserInput()
                }
            }
        },

        copyHPhysiotherapy(obj) {
            if (obj.physiotherapy_request_items) {
                const mappedPhy = obj.physiotherapy_request_items.map(item => {
                    return {
                        physiotherapy_request_item_id: item.id,
                        physiotherapy_request_item_name: item.physiotherapy_package_name,
                        physiotherapy_package_id: item.physiotherapy_package_id
                    };
                });
                this.form.physiotherapy_request_items = mappedPhy;
                this.listenUserInput()
            }
        },

        copyItemHPhysiotherapy(obj) {
            if (obj) {
                let temp = {
                    physiotherapy_request_item_id: obj.id,
                    physiotherapy_request_item_name: obj.physiotherapy_package_name,
                    physiotherapy_package_id: obj.physiotherapy_package_id
                };

                if (this.form.physiotherapy_request_items.some(item => item.physiotherapy_package_id === temp.physiotherapy_package_id)) {
                    this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan item fisioterapi yang sama')
                } else {
                    this.form.physiotherapy_request_items.push(temp)
                    this.listenUserInput()
                }
            }
        },

        // copySubjective(obj) {
        //     this.form.summary_medical_history = obj.history_disease_now
        // },
        copyCpptVital(obj) {
            // if(obj.objective){
            //     this.form.physical_check = obj.objective
            // } else {
            //     this.fillTTV(obj)
            // }
            this.fillTTV(obj)
        },
        copyCpptLocalis(obj){
            this.form.localist_status = obj?.localist_status || '';
        },

        copyCpptDiagnosis(obj){
            if (obj.icd10) {
                const mappedIcd10 = obj.icd10.map(item => {
                    return {
                        icd10_id: item.icd10_id,
                        value: item.icd10_id,
                        title: `${item.icd10_code} ${item.icd10_name}`,
                        action: "action"
                    };
                });
                this.form.icd10 = mappedIcd10;
            }
        },

        copyCpptProcedure(obj){
            if (obj.icd9cm) {
                const mappedIcd9cm = obj.icd9cm.map(item => {
                    return {
                        icd9cm_id: item.icd9cm_id,
                        value: item.icd9cm_id,
                        title: `${item.icd9cm_code} ${item.icd9cm_name}`,
                        action: "action"
                    };
                });
                this.form.icd9cm = mappedIcd9cm;
            }
        },
        copyCpptLaboratory(arry) {
            if (arry.laboratory_request) {
                let seenLabIds = new Set();

                this.form.laboratory_request_items = arry.laboratory_request.reduce((acc, req) => {
                    req.laboratory_request_items.forEach(item => {
                        if (!seenLabIds.has(item.laboratory_package_id)) {
                            seenLabIds.add(item.laboratory_package_id);
                            acc.push({
                                laboratory_request_item_name: item?.laboratory_package_name,
                                laboratory_package_id: item?.laboratory_package_id,
                                laboratory_request_item_id: item?.id,
                                laboratory_result: item?.result,
                                laboratory_request_id: item?.laboratory_request_id
                            });
                        }
                    });
                    return acc;
                }, []);
                this.listenUserInput()
            }
        },

        copyCpptItemLaboratory(obj) {
            if (obj) {
                let temp = {
                    laboratory_request_item_id: obj?.id,
                    laboratory_request_item_name: obj?.laboratory_package_name,
                    laboratory_package_id: obj?.laboratory_package_id,
                    laboratory_result: obj?.result,
                    laboratory_request_id: obj?.laboratory_request_id

                };

                if (this.form.laboratory_request_items.some(item => item.laboratory_package_id === temp.laboratory_package_id)) {
                    this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan item laboratorium yang sama')
                } else {
                    this.form.laboratory_request_items.push(temp)
                    this.listenUserInput()
                }
            }
        },

        copyItemLaboratory(obj) {
            if (obj) {
                let temp = {
                    laboratory_request_item_id: obj?.id,
                    laboratory_request_item_name: obj?.costing_name,
                    laboratory_package_id: obj?.laboratory_package_id,
                    laboratory_result: obj?.result,
                    laboratory_request_id: obj?.laboratory_request_id
                };

                if (this.form.laboratory_request_items.some(item => item.laboratory_package_id === temp.laboratory_package_id)) {
                    this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan item laboratorium yang sama')
                } else {
                    this.form.laboratory_request_items.push(temp)
                    this.listenUserInput()
                }
            }
        },
        deleteLabItem (index) {
            this.form.laboratory_request_items.splice(index, 1)
            this.listenUserInput()
        },

        copySoapRadiology(arry) {
            if (arry.radiology_request_items) {
                arry.radiology_request_items.map(item => {
                    let temp = {
                        radiology_request_item_id: item?.id,
                        radiology_request_item_name: item?.costing_name,
                        radiology_package_id: item?.radiology_package_id,
                        radiology_url:item?.url,
                    };

                    if (this.form.radiology_request_items.some(item => item.radiology_package_id === temp.radiology_package_id)) {
                        this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan item radiologi yang sama')
                    } else {
                        this.form.radiology_request_items.push(temp)
                        this.listenUserInput()
                    }
                });
            }
        },

        copyItemRadiology(obj) {
            if (obj) {
                let temp = {
                    radiology_request_item_id: obj?.id,
                    radiology_request_item_name: obj?.costing_name,
                    radiology_package_id: obj?.radiology_package_id,
                    radiology_url:obj?.url,
                };

                if (this.form.radiology_request_items.some(item => item.radiology_package_id === temp.radiology_package_id)) {
                    this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan item radiologi yang sama')
                } else {
                    this.form.radiology_request_items.push(temp)
                    this.listenUserInput()
                }
            }
        },

        //radiology
        copyCpptRadiology(arry) {
            if (arry.radiology_request) {
                let seenRadIds = new Set();

                this.form.radiology_request_items = arry.radiology_request.reduce((acc, req) => {
                    req.radiology_request_items.forEach(item => {
                        if (!seenRadIds.has(item.radiology_package_id)) {
                            seenRadIds.add(item.radiology_package_id);
                            acc.push({
                                radiology_request_item_name: item?.radiology_package_name,
                                radiology_package_id: item?.radiology_package_id,
                                radiology_request_item_id: item?.id,
                                radiology_url:item?.url,
                            });
                        }
                    });
                    return acc;
                }, []);
                this.listenUserInput()
            }
        },

        copyCpptItemRadiology(obj) {
            if (obj) {
                let temp = {
                    radiology_request_item_id: obj?.id,
                    radiology_request_item_name: obj?.radiology_package_name,
                    radiology_package_id: obj?.radiology_package_id,
                    radiology_url:obj?.url,
                };

                if (this.form.radiology_request_items.some(item => item.radiology_package_id === temp.radiology_package_id)) {
                    this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan item radiologi yang sama')
                } else {
                    this.form.radiology_request_items.push(temp)
                    this.listenUserInput()
                }
            }
        },
        deleteRadItem (index) {
            this.form.radiology_request_items.splice(index, 1)
            this.listenUserInput()
        },

        copySoapPhysiotherapy(arry) {
            if (arry.physiotherapy_request_items) {
                arry.physiotherapy_request_items.map(item => {
                    let temp = {
                        physiotherapy_request_item_id: item.id,
                        physiotherapy_request_item_name: item.costing_name,
                        physiotherapy_package_id: item.physiotherapy_package_id
                    };

                    if (this.form.physiotherapy_request_items.some(item => item.physiotherapy_package_id === temp.physiotherapy_package_id)) {
                        this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan item fisioterapi yang sama')
                    } else {
                        this.form.physiotherapy_request_items.push(temp)
                        this.listenUserInput()
                    }
                });
            }
        },
        copyItemPhysiotherapy(obj) {
            if (obj) {
                let temp = {
                    physiotherapy_request_item_id: obj.id,
                    physiotherapy_request_item_name: obj.costing_name,
                    physiotherapy_package_id: obj.physiotherapy_package_id
                };

                if (this.form.physiotherapy_request_items.some(item => item.physiotherapy_package_id === temp.physiotherapy_package_id)) {
                    this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan item fisioterapi yang sama')
                } else {
                    this.form.physiotherapy_request_items.push(temp)
                    this.listenUserInput()
                }
            }
        },
        //physiotherapy
        copyCpptPhysiotherapy(arry) {
            if (arry.physiotherapy_request) {
                let seenPhyIds = new Set();

                this.form.physiotherapy_request_items = arry.physiotherapy_request.reduce((acc, req) => {
                    req.physiotherapy_request_items.forEach(item => {
                        if (!seenPhyIds.has(item.physiotherapy_package_id)) {
                            seenPhyIds.add(item.physiotherapy_package_id);
                            acc.push({
                                physiotherapy_request_item_name: item.physiotherapy_package_name,
                                physiotherapy_package_id: item.physiotherapy_package_id,
                                physiotherapy_request_item_id: item.id,
                            });
                        }
                    });
                    return acc;
                }, []);
                this.listenUserInput()
            }
        },

        copyCpptItemPhysiotherapy(obj) {
            if (obj) {
                let temp = {
                    physiotherapy_request_item_id: obj.id,
                    physiotherapy_request_item_name: obj.physiotherapy_package_name,
                    physiotherapy_package_id: obj.physiotherapy_package_id
                };

                if (this.form.physiotherapy_request_items.some(item => item.physiotherapy_package_id === temp.physiotherapy_package_id)) {
                    this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan item fisioterapi yang sama')
                } else {
                    this.form.physiotherapy_request_items.push(temp)
                    this.listenUserInput()
                }
            }
        },
        deletePhyItem (index) {
            this.form.physiotherapy_request_items.splice(index, 1)
            this.listenUserInput()
        },

        copyPrescription(obj) {
            if (obj.prescription_new?.length > 0) {
                let temp_arry = []
                let temp_arry2 = []
                for (let i = 0; i < obj.prescription_new.length; i++) {
                    const el_i = obj.prescription_new[i];

                    if (el_i.form_prescription?.length > 0) {
                        for (let j = 0; j < el_i.form_prescription.length; j++) {
                            const el_j = el_i.form_prescription[j];
                            let temp = ''
                            // console.log(el_j.type_prescription_id)
                            if (el_j.type_prescription_id === 2) {
                                temp = el_j.pharmacy_catalog_name
                                temp_arry.push(temp)
                            } else {
                                temp = el_j.pharmacy_catalog_name
                                temp_arry2.push(temp)
                            }
                        }
                        this.form.significant_medications += '\n' + temp_arry.filter(Boolean).join('\n')
                        this.form.take_home_medications += '\n' + temp_arry2.filter(Boolean).join('\n')
                    }
                }
            }
        },

        // copyPrescription1(obj) {
        //     // console.log('copyPrescription1', obj);

        //     if (!Array.isArray(obj.prescription_new) || obj.prescription_new.length === 0) return;

        //     obj.prescription_new.forEach(prescriptionBlock => {
        //         const prescriptions = prescriptionBlock.form_prescription || [];
        //         const instructionRefs = prescriptionBlock.form_prescription_parent || [];

        //         if (prescriptions.length === 0 || instructionRefs.length === 0) return;

        //         // Build instruction lookup for racikan
        //         const instructionMap = {};
        //         instructionRefs.forEach(ref => {
        //             instructionMap[ref.id] = ref.used || '';
        //         });

        //         // Helper: group meds by type ID, filter by take-home
        //         const groupMedsByTypeId = (items, isTakeHome) => {
        //             const grouped = {};
        //             items.forEach(item => {
        //                 const typeId = item.prescription_item_type_id;
        //                 const isType2 = item.type_prescription_id === "2"; // take-home
        //                 if ((isTakeHome && !isType2) || (!isTakeHome && isType2)) return;

        //                 if (!grouped[typeId]) grouped[typeId] = [];
        //                 grouped[typeId].push(item);
        //             });
        //             return grouped;
        //         };

        //         // Helper: format grouped meds
        //         const formatGroups = (grouped) => {
        //             let output = '';
        //             let racikanCount = 1;

        //             for (const [typeId, items] of Object.entries(grouped)) {
        //                 const isRacikan = Number(typeId) > 1;
        //                 output += isRacikan ? `Racikan ${racikanCount++}:\n` : `Obat Satuan:\n`;

        //                 if (isRacikan) {
        //                     items.forEach(item => {
        //                         const name = item.pharmacy_catalog_name || '';
        //                         const qty = item.qty || '';
        //                         const pkg = item.package_name || '';
        //                         output += `- ${name}, ${qty} ${pkg}\n`;
        //                     });

        //                     const instruction = items[0].prescription_item_type_used || instructionMap[typeId] || '';
        //                     if (instruction) output += `Aturan Pakai: ${instruction}\n`;
        //                 } else {
        //                     items.forEach(item => {
        //                         const name = item.pharmacy_catalog_name || '';
        //                         const qty = item.qty || '';
        //                         const pkg = item.package_name || '';
        //                         const used = item.used || '';
        //                         const desc = item.desc || '';
        //                         const instruction = [used, desc].filter(Boolean).join(' ');
        //                         output += `- ${name}, ${qty} ${pkg}`;
        //                         if (instruction) output += ` | Aturan Pakai: ${instruction}`;
        //                         output += '\n';
        //                     });
        //                 }

        //                 output += '';
        //             }

        //             return output;
        //         };

        //         const significantGroups = groupMedsByTypeId(prescriptions, false);
        //         const takeHomeGroups = groupMedsByTypeId(prescriptions, true);

        //         const significantText = formatGroups(significantGroups).trim();
        //         const takeHomeText = formatGroups(takeHomeGroups).trim();

        //         if (this.form.take_home_medications === '-') this.form.take_home_medications = '';
        //         if (this.form.significant_medications === '-') this.form.significant_medications = '';

        //         this.form.take_home_medications += (takeHomeText ? '\n' + takeHomeText : '');
        //         this.form.significant_medications += (significantText ? '\n' + significantText : '');

        //         // console.log('Formatted Take Home Text:', takeHomeText);
        //         // console.log('Formatted Significant Text:', significantText);
        //     });
        // },
        copyCpptPrescription(obj) {
            // console.log(obj, 'copycpptprescription')
            if (obj?.prescription?.length > 0) {
                let significantArr = [];
                let takeHomeArr = [];

                for (let i = 0; i < obj.prescription.length; i++) {
                    const el_i = obj.prescription[i];

                    if (el_i.form_prescription?.length > 0) {
                        for (let j = 0; j < el_i.form_prescription.length; j++) {
                            const prescription = el_i.form_prescription[j];
                            const name = prescription.pharmacy_catalog_name || '';
                            // const qty = prescription.qty || '';
                            // const package_name =  prescription.package_name || '';
                            // const used =  prescription.used ? prescription.used : prescription.prescription_item_type_used || '';
                            // const desc =  prescription.desc || '';

                            if (el_i.type_prescription_id === 2) {
                                takeHomeArr.push(name)
                                // takeHomeText += name + '\n';
                            } else {
                                significantArr.push(name)
                                // significantText += name + '\n';
                            }
                        }
                    }
                }

                // this.form.significant_medications += significantText;
                this.form.significant_medications += '\n' + significantArr.filter(Boolean).join('\n');

                // console.log('initakehometext', takeHomeText)
                if(this.form.take_home_medications == '-'){
                    this.form.take_home_medications = ''
                }

                this.form.take_home_medications += '\n' + takeHomeArr.filter(Boolean).join('\n');
                // this.form.take_home_medications += takeHomeText;
            }
        },
        // copyCpptPrescription1(obj) {
        //     // console.log(obj, 'copyCpptPrescription');

        //     if (!Array.isArray(obj?.prescription) || obj.prescription.length === 0) return;

        //     let instructionMap = {};
        //     if (Array.isArray(obj.form_prescription_parent)) {
        //         obj.form_prescription_parent.forEach(item => {
        //             instructionMap[item.id] = item.used || '';
        //         });
        //     }

        //     const groupMedsByTypeId = (prescriptions, isTakeHome) => {
        //         let grouped = {};
        //         for (const presc of prescriptions) {
        //             if ((isTakeHome && presc.type_prescription_id !== 2) ||
        //                 (!isTakeHome && presc.type_prescription_id === 2)) continue;

        //             for (const item of presc.form_prescription || []) {
        //                 const typeId = item.prescription_item_type_id;
        //                 if (!grouped[typeId]) grouped[typeId] = [];
        //                 grouped[typeId].push(item);
        //             }
        //         }
        //         return grouped;
        //     };

        //     const formatGroups = (grouped) => {
        //         let result = '';
        //         let racikanCount = 1;

        //         for (const [typeIdStr, items] of Object.entries(grouped)) {
        //             const typeId = Number(typeIdStr);
        //             const isRacikan = typeId > 1;

        //             // Label
        //             result += isRacikan ? `Racikan ${racikanCount++}:\n` : `Obat Satuan:\n`;

        //             if (!isRacikan) {
        //                 // Obat Satuan: Each item has its own instruction
        //                 for (const item of items) {
        //                     const name = item.pharmacy_catalog_name || '';
        //                     const qty = item.qty || '';
        //                     const packageName = item.package_name || '';
        //                     const used = item.used || '';
        //                     const desc = item.desc || '';
        //                     const instruction = [used, desc].filter(Boolean).join(' ');

        //                     result += `- ${name}, ${qty} ${packageName}`;
        //                     if (instruction) {
        //                         result += ` | Aturan Pakai: ${instruction}`;
        //                     }
        //                     result += '\n';
        //                 }
        //             } else {
        //                 // Racikan: Grouped under one instruction
        //                 for (const item of items) {
        //                     const name = item.pharmacy_catalog_name || '';
        //                     const qty = item.qty || '';
        //                     const packageName = item.package_name || '';
        //                     result += `- ${name}, ${qty} ${packageName}\n`;
        //                 }

        //                 const instruction = items[0].prescription_item_type_used || instructionMap[typeId] || '';
        //                 if (instruction) {
        //                     result += `Aturan Pakai: ${instruction}\n`;
        //                 }
        //             }

        //             result += '\n';
        //         }

        //         return result;
        //     };

        //     const significantGroups = groupMedsByTypeId(obj.prescription, false);
        //     const takeHomeGroups = groupMedsByTypeId(obj.prescription, true);

        //     const significantText = formatGroups(significantGroups);
        //     const takeHomeText = formatGroups(takeHomeGroups);

        //     if (this.form.take_home_medications === '-') this.form.take_home_medications = '';
        //     if (this.form.significant_medications === '-') this.form.significant_medications = '';

        //     this.form.take_home_medications += takeHomeText.trim();
        //     this.form.significant_medications += significantText.trim();

        //     // console.log('Formatted Take Home Text:', takeHomeText);
        //     // console.log('Formatted Significant Text:', significantText);
        // }


        // getEducationResumeTemplate() {
        //     try {
        //         this.$store.dispatch('templating/getEducationResumeTemplate').then((response) => {
        //             this.education_resume_template_list = response
        //             // console.log('this.education_resume_template_list', this.education_resume_template_list);
        //         }, error => {
        //             console.error('Error while GET templating/getEducationResumeTemplate at: ', error);
        //         })
        //     } catch (error) {
        //         console.error('Error catch while GET templating/getEducationResumeTemplate at: ', error);
        //     }
        // },
    },

    created () {
        if (this.$route.params.id) {
            this.id = this.$route.params.id
            this.reg_des_id = this.$route.params.id
            this.getData(this.$route.params.id)
        }

        this.$store.dispatch('employee/getParamSpecialist')
        this.$store.dispatch('radiology/getPackageAll')
        this.$store.dispatch('healthfacilityitem/getParam', '3cb626f5-9148-4fb1-8663-3d7f028b01a6')

        this.getLabPackageListV2()
        this.getRadPackageListV2()
        // this.getEducationResumeTemplate()
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
</style>
