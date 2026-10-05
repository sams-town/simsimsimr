<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button v-if="(this.userData.user_group.id == this.doctor_group_id || this.userData.employee_id == this.admin_id)" @click="editResume" size="sm" class="px-32 mx-2" variant="warning-500">Edit</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <section ref="left-section">
                        <div class="row align-items-center pb-40" style="border-bottom: 3px solid black;">
                            <div class="col-2 text-center">
                                <img src="../../../../../@core/assets/images/logo-thb-small.png" width="60%" alt="rs-logo">
                            </div>
                            <div class="col text-center">
                                <p class="text-bold-18">RS Taman Harapan Baru</p>
                                <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                                <p>Info: +62 778 4161212 / 0853 7111 1212</p>
                            </div>
                            <div class="col-2 text-center mr-16">
                                <!-- <img src="../../../../../@core/assets/images/lafki-logo.png" width="50%" alt="lafki-logo"> -->
                                <!-- <img src="../../../../../@core/assets/images/apexa.png" width="50%" alt="lafki-logo"> -->
                            </div>
                        </div>
                        <!-- <div class="row" style="border-bottom: 3px solid black;">
                            <div class="col-12">
                                <div ref="header" class="d-flex pb-16" >
                                    <div class="d-flex align-items-center justify-content-center">
                                        <img src="../../../../../@core/assets/images/logo-thb-small.png" width="80px" alt="rs-logo"> <br>
                                        <div class="text-regular-12 pl-16">
                                            <p class="text-bold-18">RS Taman Harapan Baru</p>
                                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <section class="mt-32">
                            <div class="text-center">
                                <div class="font-weight-bold">RESUME MEDIS PASIEN PULANG</div>
                                <div class="text-regular-12"><i>(Discharge Summary)</i></div>
                            </div>
                            <div class="row mt-16">
                                <div class="col-6">
                                    <table class="mt-16">
                                        <tr>
                                            <td class="col-3" style="vertical-align: top; white-space: nowrap;">Tanggal Masuk (<i>Admission Date</i>)</td>
                                            <td class="col-1 text-right">:</td>
                                            <td>{{ regData.register_date }}</td>
                                        </tr>
                                        <tr>
                                            <td class="col-3" style="vertical-align: top;">Tanggal Keluar (<i>Discharge Date</i>)</td>
                                            <td class="col-1 text-right">:</td>
                                            <td>{{ regData.checkout_date }}</td>
                                        </tr>
                                        <tr>
                                            <td class="col-3" style="vertical-align: top; white-space: nowrap;">Ruang Rawat Terakhir (<i>Last Ward</i>)</td>
                                            <td class="col-1 text-right">:</td>
                                            <td>{{ regData.room_name }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-6">
                                    <table class="mt-16">
                                        <tr>
                                            <td class="col-3" style="vertical-align: top; white-space: nowrap;">Nama</td>
                                            <td class="col-1 text-right">:</td>
                                            <td>{{regData.alias_name}}. {{ regData.fullname }}</td>
                                        </tr>
                                        <tr>
                                            <td class="col-3" style="vertical-align: top;">No. RM</td>
                                            <td class="col-1 text-right">:</td>
                                            <td>{{ regData.norm }}</td>
                                        </tr>
                                        <tr>
                                            <td class="col-3" style="vertical-align: top; white-space: nowrap;">Tanggal Lahir</td>
                                            <td class="col-1 text-right">:</td>
                                            <td>{{ localDate(regData.birth_date) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </section>
                        <div class="mt-24 p-16" style="border: 4px solid black;">
                            <ol type="1">
                                <li class="mt-32">
                                    <div>
                                        <div class="font-weight-bold">Indikasi Rawat Inap</div>
                                        <div class="text-regular-14"><i>(Indications for Hospitalization)</i></div>
                                    </div>
                                    <div v-html="parsedBody(resumeData?.indication_for_hospitalization ? resumeData?.indication_for_hospitalization : '-')"class="mt-12"></div>
                                </li>

                                <li class="mt-32">
                                    <div>
                                        <div class="font-weight-bold">Riwayat Ringkasan Penyakit</div>
                                        <div class="text-regular-14"><i>(History of Desease - Summary)</i></div>
                                    </div>
                                    <div v-html="parsedBody(resumeData?.summary_medical_history ? resumeData?.summary_medical_history : '-')"class="mt-12"></div>
                                </li>
                                <li class="mt-32">
                                    <div>
                                        <div class="font-weight-bold">Hasil Pemeriksaan Fisik Penting dan Temuan Lain</div>
                                        <div class="text-regular-14"><i>(Significant physical and other findings)</i></div>
                                    </div>
                                    <div v-html="parsedBody(resumeData?.physical_check ? resumeData?.physical_check : '-')"class="mt-12"></div>
                                    <div v-if="meaningful?.length > 0" class="row mt-12">
                                        <!-- <div class="font-weight-bold text-w3green">ST.GENERALIS BERMAKNA:</div> -->
                                        <div v-for="(column, colIndex) in chunkedData" :key="colIndex" :class="`col-${12 / chunkedData.length}`">
                                            <div v-for="(item, index) in column" :key="index" class="d-flex mb-2">
                                                <div style="min-width: 140px;">{{ item.key }}</div>
                                                <div style="width: 10px;" class="text-center">:</div>
                                                <div class="flex-grow-1">{{ item.value }}</div>
                                            </div>
                                        </div>
                                        <!-- <template v-for="item in meaningful">
                                            <div class="col-4">
                                                <div class="d-flex">
                                                    <div class="text-danger-500" style="width: 125px;">{{ item.key }}</div>
                                                    <div style="width: 15px;" class="px-1">:</div>
                                                    <div class="text-danger-500">{{ item.value }}</div>
                                                </div>
                                            </div>
                                        </template> -->
                                    </div>
                                    <div v-if="resumeData?.abdomen_other_physical_check" class="mt-12">
                                        <div class="font-weight-bold">Pemeriksaan Abdomen Lainnya</div>
                                        <div class="text-regular-14"><i>(Other Abdominal Examinations)</i></div>
                                        <div v-html="parsedBody(resumeData?.abdomen_other_physical_check ? resumeData.abdomen_other_physical_check : '-')"class="mt-12"></div>
                                    </div>
                                </li>
                                 <li class="mt-32">
                                    <div>
                                        <div class="font-weight-bold">Diagnosis Utama (ICD10)</div>
                                        <div class="text-regular-14"><i>(Diagnose)</i></div>
                                    </div>
                                    <div class="mt-12">
                                        <template v-for="(item, index) in resumeData.icd10">
                                            <div :key="index" v-if="index == 0">
                                                <div>{{ item.icd10_code }} {{ item.icd10_name }}</div>
                                            </div>
                                        </template>
                                        <div>
                                            <div v-html="parsedBody(resumeData.icd10_notes ? resumeData.icd10_notes : '-')"class="mt-12"></div>
                                        </div>
                                    </div>
                                </li>
                                <li class="mt-32">
                                    <div>
                                        <div class="font-weight-bold">Diagnosis Tambahan (ICD10)</div>
                                        <div class="text-regular-14"><i>(Comorbidities)</i></div>
                                    </div>
                                    <div class="mt-12">
                                        <template v-for="(item, index) in resumeData.icd10">
                                            <div :key="index" v-if="index > 0">
                                                <div>{{ item.icd10_code }} {{ item.icd10_name }}</div>
                                            </div>
                                        </template>
                                    </div>
                                </li>
                                <li class="mt-32">
                                    <div>
                                        <div class="font-weight-bold">Prosedur Diagnostik dan Terapeutik yang dilakukan (ICD9CM)</div>
                                        <div class="text-regular-14"><i>(Diagnostic and Therapeutik procedures performed)</i></div>
                                    </div>
                                    <div class="mt-12">
                                        <template v-if="resumeData.icd9cm">
                                            <div v-for="(item, index) in resumeData.icd9cm" :key="index">{{ item.icd9cm_code }} {{ item.icd9cm_name }}</div>
                                        </template>
                                        <div>
                                            <div v-html="parsedBody(resumeData.therapy_plan ? resumeData.therapy_plan : '-')"class="mt-12"></div>
                                        </div>
                                    </div>
                                </li>
                                <li class="mt-32">
                                    <div>
                                        <div class="font-weight-bold">Penunjang</div>
                                        <!-- <div class="text-regular-14"><i>(Diagnostic and Therapeutik procedures performed)</i></div> -->
                                    </div>
                                    <div class="mt-12">
                                        <div class="text-bold-14">Laboratorium : <span>{{ !resumeData.laboratory_request_items ? '-' : '' }}</span></div>
                                        <template v-if="resumeData.laboratory_request_items">
                                            <div v-for="(item, index) in resumeData.laboratory_request_items" :key="index">
                                                <template v-if="item?.result.length > 0">
                                                    <div v-for="(data, index) in item.result" :key="index">
                                                        <template v-if="data.Parameter && data.hasil">
                                                            <div v-if="data.flag.toLowerCase() == 'low' || data.flag.toLowerCase() == 'high'">
                                                                {{ item.costing_name }}: {{ data.Parameter}} {{data.hasil}} {{data.satuan}}
                                                            </div>
                                                            <div v-else>
                                                                {{ item.costing_name }}: {{ data.Parameter}} Dalam Batas Normal (DBN)
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                                <div v-else>
                                                    {{ item.costing_name }}
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="mt-12">
                                        <div class="text-bold-14">Radiology : <span>{{ !resumeData.radiology_request_items ? '-' : '' }}</span></div>
                                        <template v-if="resumeData.radiology_request_items">
                                            <div v-for="(item, index) in resumeData.radiology_request_items" :key="index">{{ item.costing_name }}</div>
                                        </template>
                                    </div>
                                    <!-- <div class="mt-12">
                                        <div class="text-bold-14">Fisioterapi : <span>{{ !resumeData.physiotherapy_request_items ? '-' : '' }}</span></div>
                                        <template v-if="resumeData.physiotherapy_request_items">
                                            <div v-for="(item, index) in resumeData.physiotherapy_request_items" :key="index">{{ item.costing_name }}</div>
                                        </template>
                                    </div> -->
                                </li>
                                <li class="mt-32">
                                    <div>
                                        <div class="font-weight-bold">Obat-obatan Penting yang diberikan selama perawatan</div>
                                        <div class="text-regular-14"><i>(Significant administered medications during hospitalization)</i></div>
                                    </div>
                                    <div v-html="parsedBody(resumeData.significant_medications ? resumeData.significant_medications : '-')"class="mt-12"></div>
                                </li>
                                <li class="mt-32">
                                    <div>
                                        <div class="font-weight-bold">Obat yang dibawa pulang</div>
                                        <div class="text-regular-14"><i>(Take-home medications)</i></div>
                                    </div>
                                    <div v-html="parsedBody(resumeData.take_home_medications ? resumeData.take_home_medications : '-')"class="mt-12"></div>
                                </li>
                                <li class="mt-32">
                                    <div class="font-weight-bold">Cara pulang *</div>
                                    <div class="text-regular-14"><i>(Reason for Discharge)</i></div>

                                    <div class="d-flex mt-12 text-regular-16">
                                        <div class="d-flex align-items-center pr-3">
                                            <b-icon :icon="getRadioReasonIcon('4a6d28c4-e020-425f-91e1-d39699cf679b')" :variant="getRadioReasonIconVariant('4a6d28c4-e020-425f-91e1-d39699cf679b')" class="mr-8"/>
                                            <div class="lh-110">Meninggal</div>
                                        </div>
                                        <div class="d-flex align-items-center pr-3">
                                            <b-icon :icon="getRadioReasonIcon('807ca005-5827-4e19-9c97-0bc3d4e924ca')" :variant="getRadioReasonIconVariant('807ca005-5827-4e19-9c97-0bc3d4e924ca')" class="mr-8" />
                                            <div class="lh-110">Atas Persetujuan Dokter</div>
                                        </div>
                                        <div class="d-flex align-items-center pr-3">
                                            <b-icon :icon="getRadioReasonIcon('a1631acc-6a94-414d-95de-a9faea0f1c2f')" :variant="getRadioReasonIconVariant('a1631acc-6a94-414d-95de-a9faea0f1c2f')" class="mr-8" />
                                            <div class="lh-110">Dirujuk</div>
                                        </div>
                                        <div class="d-flex align-items-center pr-3">
                                            <b-icon :icon="getRadioReasonIcon('ae0aabfa-1600-443f-8a60-62f9e76b1608')" :variant="getRadioReasonIconVariant('ae0aabfa-1600-443f-8a60-62f9e76b1608')" class="mr-8" />
                                            <div class="lh-110">Permintaan Sendiri</div>
                                        </div>
                                        <div class="d-flex align-items-center pr-3">
                                            <b-icon :icon="getRadioReasonIcon('bbc9fdce-affb-4b32-a386-0ed7e91c1e89')" :variant="getRadioReasonIconVariant('bbc9fdce-affb-4b32-a386-0ed7e91c1e89')" class="mr-8" />
                                            <div class="lh-110">Pulang Non Ranap</div>
                                        </div>
                                        <div class="d-flex align-items-center pr-3">
                                            <b-icon :icon="getRadioReasonIcon('adb7ce0b-ede3-4fee-b273-ce159d286732')" :variant="getRadioReasonIconVariant('adb7ce0b-ede3-4fee-b273-ce159d286732')" class="mr-8" />
                                            <div class="lh-110">Lainnya: <span v-if="resumeData.exit_id == 'adb7ce0b-ede3-4fee-b273-ce159d286732'">{{ resumeData.exit_anythingelse }}</span></div>
                                        </div>
                                    </div>
                                </li>
                                <li class="mt-32">
                                    <div class="font-weight-bold">Kondisi saat pulang *</div>
                                    <div class="text-regular-14"><i>(Continuous Care Instruction)</i></div>

                                    <div class="d-flex mt-12 text-regular-16">
                                        <div class="d-flex pr-3">
                                            <b-icon :icon="getRadioConditionIcon('1')" :variant="getRadioConditionIconVariant('1')" class="mt-1 mr-8"/>
                                            <div>
                                                <div class="lh-110">Perbaikan</div>
                                                <div><i>(Discharged with improvement)</i></div>
                                            </div>
                                        </div>
                                        <div class="d-flex pr-3">
                                            <b-icon :icon="getRadioConditionIcon('2')" :variant="getRadioConditionIconVariant('2')" class="mt-1 mr-8" />
                                            <div>
                                                <div class="lh-110">Tidak Sembuh</div>
                                                <div><i>(Not Cured)</i></div>
                                            </div>
                                        </div>
                                        <div class="d-flex pr-3">
                                            <b-icon :icon="getRadioConditionIcon('3')" :variant="getRadioConditionIconVariant('3')" class="mt-1 mr-8" />
                                            <div>
                                                <div class="lh-110">Meninggal < 48 jam</div>
                                                <div><i>(Died < 48 hrs)</i></div>
                                            </div>
                                        </div>
                                        <div class="d-flex pr-3">
                                            <b-icon :icon="getRadioConditionIcon('5')" :variant="getRadioConditionIconVariant('5')" class="mt-1 mr-8" />
                                            <div>
                                                <div class="lh-110">Meninggal > 48 jam</div>
                                                <div><i>(Died > 48 hrs)</i></div>
                                            </div>
                                        </div>
                                        <div class="d-flex pr-3">
                                            <b-icon :icon="getRadioConditionIcon('4')" :variant="getRadioConditionIconVariant('4')" class="mt-1 mr-8" />
                                            <div>
                                                <div class="lh-110">Lainnya: <span v-if="resumeData.exit_condition_type_id == '4'">{{ resumeData.exit_condition_anythingelse }}</span></div>
                                                <div><i>(Others)</i></div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="mt-32">
                                    <div class="font-weight-bold">Instruksi Rawat Lanjutan</div>
                                    <div class="text-regular-14"><i>(Continuous Care Instruction)</i></div>

                                    <div class="row mt-24">
                                        <div class="col-6 text-regular-14">
                                            <div>
                                                <div class="font-weight-bold">Kontrol Pasca Rawat Inap:</div>
                                                <div><i>(Post-inpatient Control)</i></div>
                                            </div>

                                            <div class="mt-12 text-regular-16">
                                                <div class="d-flex py-1">
                                                    <b-icon :icon="getRadioControlIcon('1')" :variant="getRadioControlIconVariant('1')" class="mt-1 mr-8"/>
                                                    <div>
                                                        <div class="lh-110">RS Taman Harapan Baru</div>
                                                        <div v-if="resumeData.care_instruction_control == 1">Tanggal {{ resumeData.care_instruction_control_date }} {{ resumeData.health_facility_item_name_control ? 'di' : '' }} {{ resumeData.health_facility_item_name_control }}</div>
                                                    </div>
                                                </div>
                                                <div class="d-flex py-1">
                                                    <b-icon :icon="getRadioControlIcon('2')" :variant="getRadioControlIconVariant('2')" class="mt-1 mr-8" />
                                                    <div>
                                                        <div class="lh-110">Dokter Keluarga / Klinik Luar</div>
                                                        <div><i>(Family Phsician / Outer Clinic)</i></div>
                                                    </div>
                                                </div>
                                                <div class="d-flex py-1">
                                                    <b-icon :icon="getRadioControlIcon('3')" :variant="getRadioControlIconVariant('3')" class="mt-1 mr-8" />
                                                    <div>
                                                        <div class="lh-110">Puskesmas</div>
                                                        <div><i>(Primary Health Care)</i></div>
                                                    </div>
                                                </div>
                                                <div class="d-flex py-1">
                                                    <b-icon :icon="getRadioControlIcon('4')" :variant="getRadioControlIconVariant('4')" class="mt-1 mr-8" />
                                                    <div>
                                                        <div class="lh-110">Lainnya</div>
                                                        <div><i>(Others)</i></div>
                                                        <div v-if="resumeData.care_instruction_control == 4">{{ resumeData.care_instruction_anythingelse }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li class="mt-32">
                                    <div>
                                        <div class="font-weight-bold">Edukasi Selama Di Rumah</div>
                                    </div>
                                    <div v-html="parsedBody(resumeData.home_education ? resumeData.home_education : '-')"class="mt-12"></div>
                                </li>

                                <!-- <li class="mt-32">
                                    <div>
                                        <div class="font-weight-bold">Status Generalis</div>
                                        <div class="text-regular-14"><i>(Status Praesens Generalis)</i></div>
                                    </div>
                                    <div class="row mt-8 fs-11">
                                        <div class="col-4">
                                            <table class="w-100">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold fs-12">KEPALA</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.head)">
                                                        <td style="width: 100px;">Kepala</td>
                                                        <td class="px-1">:</td>
                                                        <td>{{ getHead(resumeData.head) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.head_hair_color)">
                                                        <td class="align-top">Warna Rambut</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getHairColor(resumeData.head_hair_color) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.head_hair_condition)">
                                                        <td class="align-top">Kondisi Rambut</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getHairCondition(resumeData.head_hair_condition) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold  fs-12">MATA</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.eye_conjunctive)">
                                                        <td class="align-top" >Konjungtiva</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getEyeConjunctive(resumeData.eye_conjunctive) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.eye_sclera)">
                                                        <td class="align-top">Sklera</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getEyeSclera(resumeData.eye_sclera) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.eye_pupil)">
                                                        <td class="align-top">Pupil</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getEyePupil(resumeData.eye_pupil) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.eye_light_reflex)">
                                                        <td class="align-top">Reflek Cahaya</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getEyeReflect(resumeData.eye_light_reflex) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold  fs-12">MULUT</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.mouth_mucosa)">
                                                        <td class="align-top">Mukosa</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getMouthMucosa(resumeData.mouth_mucosa) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.mouth_discoloration)">
                                                        <td class="align-top">Perubahan Warna</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getMouthDiscoloration(resumeData.mouth_discoloration) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.mouth_tongue)">
                                                        <td class="align-top">Lidah</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getMouthTongue(resumeData.mouth_tongue) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.mouth_caries)">
                                                        <td class="align-top">Karies</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getMouthCaries(resumeData.mouth_caries) }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-4">
                                            <table class="w-100">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold  fs-12">LEHER</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.neck_kgb)">
                                                        <td class="align-top" style="width: 100px;">KGB</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getNeckKGB(resumeData.neck_kgb) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.neck_trachea_position)">
                                                        <td class="align-top">Posisi Trakea</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getNeckTracheaPosition(resumeData.neck_trachea_position) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.neck_massa)">
                                                        <td class="align-top">Massa</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getNeckMassa(resumeData.neck_massa) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold  fs-12">EKSTREMITAS</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.extremity_acral)">
                                                        <td class="align-top">Akral</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getEkstremitasAkral(resumeData.extremity_acral) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.extremity_crt)">
                                                        <td class="align-top">CRT</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getEkstremitasCRT(resumeData.extremity_crt) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.extremity_edema)">
                                                        <td class="align-top">Edema</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getEkstremitasEdema(resumeData.extremity_edema) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold  fs-12">ABDOMEN</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.abdomen_supel)">
                                                        <td class="align-top">Supel</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getAbdomenSupel(resumeData.abdomen_supel) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.abdomen_bowel_sounds)">
                                                        <td class="align-top">Bising Usus</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getAbdomenBising(resumeData.abdomen_bowel_sounds) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.abdomen_bowel_sounds)">
                                                        <td class="align-top">Nyeri Tekan</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getAbdomenPress(resumeData.abdomen_tenderness) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.abdomen_hepar_lien)">
                                                        <td class="align-top">Hepar & Lien</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getAbdomenHepar(resumeData.abdomen_hepar_lien) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-top">Pemeriksaan Abdomen Lainnya</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td class="align-top">
                                                            <div v-html="parsedBody((resumeData.abdomen_other_physical_check) ? (resumeData.abdomen_other_physical_check) : '-')"></div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-4">
                                            <table class="w-100">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold  fs-12">THORAKS</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold">INSPEKSI</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.thorax_insp_chest)">
                                                        <td class="align-top" style="width: 120px;">Dada</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getInspectionChest(resumeData.thorax_insp_chest) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.thorax_insp_retraction)">
                                                        <td class="align-top">Retraksi</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getInspectionRetractiont(resumeData.thorax_insp_retraction) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold">PALPASI</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.thorax_insp_chest)">
                                                        <td class="align-top">Dada</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getPalpationChest(resumeData.thorax_insp_chest) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.thorax_palp_fremitus)">
                                                        <td class="align-top">Fremitus</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getPalpationFremitus(resumeData.thorax_palp_fremitus) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.thorax_palp_crepitation)">
                                                        <td class="align-top">Krepitasi</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getPalpationKrepitasi(resumeData.thorax_palp_crepitation) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold">AUSKULTASI</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.thorax_ausk_pul_vasiculer)">
                                                        <td class="align-top">Pulmo Vasikuler</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getAuskultationPulmoVasikuler(resumeData.thorax_ausk_pul_vasiculer) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.thorax_ausk_pul_rhonki)">
                                                        <td class="align-top">Pulmo Rhonki</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getAuskultationPulmoRhonki(resumeData.thorax_ausk_pul_rhonki) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.thorax_ausk_pul_wheezing)">
                                                        <td class="align-top">Pulmo Wheezing</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getAuskultationPulmoWheezing(resumeData.thorax_ausk_pul_wheezing) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.thorax_ausk_cor_heart_sound)">
                                                        <td class="align-top">COR Bunyi Jantung</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getAuskultationCORBJ(resumeData.thorax_ausk_cor_heart_sound) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.thorax_ausk_cor_murmur)">
                                                        <td class="align-top">COR Murmur</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getAuskultationCORMurmur(resumeData.thorax_ausk_cor_murmur) }}</td>
                                                    </tr>
                                                    <tr :class="getMeaningVariant(resumeData.thorax_ausk_cor_gallop)">
                                                        <td class="align-top">COR Gallop</td>
                                                        <td class="align-top px-1">:</td>
                                                        <td>{{ getAuskultationCORGallop(resumeData.thorax_ausk_cor_gallop) }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </li> -->
                            </ol>
                        </div>

                        <div class="d-flex justify-content-between align-items-end mt-24 text-left">
                            <div>
                                <div>Pasien / Keluarga</div>
                                <div class="mt-72">( &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; )</div>
                                <div>Tanda Tangan dan Nama Jelas</div>
                                <div><i>(Name & Signature)</i></div>
                            </div>
                            <div>
                                <div>Bekasi, {{ regData.checkout_date ? localDate(regData.checkout_date) : today }}</div>
                                <div>Dokter Penanggung Jawab Pelayanan</div>
                                <div :class="regData.employee_signature ? '' : 'mb-72'"><i>(Responsible Physician)</i></div>

                                <template v-if="regData.employee_signature">
                                    <b-img :src="urlbe+regData.employee_signature" style="width: 120px;"/>
                                </template>
                                <div>({{ regData.doctor_in_charge_name }})</div>
                                <div>Tanda Tangan dan Nama Jelas</div>
                                <div><i>(Name & Signature)</i></div>

                            </div>
                        </div>

                        <div class="mt-32">
                            <small class="d-block">* Beri tanda centang ( ✔ ) sesuai pilihan, Give check ( ✔ ) sign for suitable option</small>
                            <small class="d-block">Dibuat rangkap 3 ( 1 untuk rekam medis, 1 untuk penjamin dan 1 untuk pasien  )</small>
                        </div>
                    </section>


                </div>
            </div>
            <AddResumeView ref="childPrintResumeRef" @getResumePrint="getResume"/>
        </b-container>
    </div>
</template>
<script>
// import id from '@/@core/assets/plugins/fullcalendar/locale/id';
import optionMixin from '@/mixins/optionMixin';
import AddResumeView from '@/views/pages/patient/resume/AddResumeView.vue';
import moment from 'moment';
import { mapState } from 'vuex';

export default {
    mixins: [optionMixin],
    components: {
        AddResumeView,
    },
    data() {
        return {
            urlbe:process.env.VUE_APP_IMAGE,
            userData: JSON.parse(localStorage.getItem('userData')),
            admin_id: 'd77142c4-4777-47d8-bd0c-fd7dfd134af4',
            doctor_group_id: 'efb3fdff-fbe3-4954-a391-3729e544b62a',
            today: moment(new Date()).locale('id').format('LL'),
            id: '',
            regData: {},
            resumeData: {},
            // employeeData: {},
            meaningful: [],
            generalis_key: [
                { default: 1, label: 'Kepala', key: "head", name: 'getHead' },
                { default: 1, label: 'Warna Rambut', key: "head_hair_color", name: 'getHairColor' },
                { default: 1, label: 'Kondisi Rambut', key: "head_hair_condition", name: 'getHairCondition' },
                { default: 1, label: 'Conjunctiva', key: "eye_conjunctive", name: 'getEyeConjunctive' },
                { default: 1, label: 'Sklera', key: "eye_sclera", name: 'getEyeSclera' },
                { default: 1, label: 'Pupil', key: "eye_pupil", name: 'getEyePupil' },
                { default: 1, label: 'Reflek Cahaya', key: "eye_light_reflex", name: 'getEyeReflect' },
                { default: 1, label: 'Mukosa', key: "mouth_mucosa", name: 'getMouthMucosa' },
                { default: 1, label: 'Perubahan Warna', key: "mouth_discoloration", name: 'getMouthDiscoloration' },
                { default: 1, label: 'Lidah', key: "mouth_tongue", name: 'getMouthTongue' },
                { default: 1, label: 'Karies', key: "mouth_caries", name: 'getMouthCaries' },
                { default: 1, label: 'KGB', key: "neck_kgb", name: 'getNeckKGB' },
                { default: 1, label: 'Posisi Trakea', key: "neck_trachea_position", name: 'getNeckTracheaPosition' },
                { default: 1, label: 'Massa', key: "neck_massa", name: 'getNeckMassa' },
                { default: 1, label: 'JVP', key: "neck_jvp", name: 'getNeckJVP' },
                { default: 1, label: 'Inspeksi Dada', key: "thorax_insp_chest", name: 'getInspectionChest' },
                { default: 1, label: 'Retraksi', key: "thorax_insp_retraction", name: 'getInspectionRetractiont' },
                { default: 1, label: 'Palpasi Dada', key: "thorax_palp_chest", name: 'getPalpationChest' },
                { default: 1, label: 'Fremitus', key: "thorax_palp_fremitus", name: 'getPalpationFremitus' },
                { default: 1, label: 'Krepitasi', key: "thorax_palp_crepitation", name: 'getPalpationKrepitasi' },
                { default: 1, label: 'Pulmo Vasikuler', key: "thorax_ausk_pul_vasiculer", name: 'getAuskultationPulmoVasikuler' },
                { default: 1, label: 'Pulmo Rhonki', key: "thorax_ausk_pul_rhonki", name: 'getAuskultationPulmoRhonki' },
                { default: 1, label: 'Pulmo Wheezing', key: "thorax_ausk_pul_wheezing", name: 'getAuskultationPulmoWheezing' },
                { default: 1, label: 'Bunyi Jantung', key: "thorax_ausk_cor_heart_sound", name: 'getAuskultationCORBJ' },
                { default: 1, label: 'Murmur', key: "thorax_ausk_cor_murmur", name: 'getAuskultationCORMurmur' },
                { default: 1, label: 'Gallop', key: "thorax_ausk_cor_gallop", name: 'getAuskultationCORGallop' },
                { default: 1, label: 'Supel', key: "abdomen_supel", name: 'getAbdomenSupel' },
                { default: 1, label: 'Bising Usus', key: "abdomen_bowel_sounds", name: 'getAbdomenBising' },
                { default: 1, label: 'Nyeri Tekan', key: "abdomen_tenderness", name: 'getAbdomenPress' },
                { default: 1, label: 'Hepar & Lien', key: "abdomen_hepar_lien", name: 'getAbdomenHepar' },
                { default: 1, label: 'Akral', key: "extremity_acral", name: 'getEkstremitasAkral' },
                { default: 1, label: 'extremity_crt', key: "extremity_crt", name: 'getEkstremitasCRT' },
                { default: 1, label: 'extremity_edema', key: "extremity_edema", name: 'getEkstremitasEdema' },
            ]
        }
    },
    computed: {
        chunkedData() {
            let columns = 2;
                if (this.meaningful.length > 30) {
                    columns = 3;
            }
            const perColumn = Math.ceil(this.meaningful.length / columns);
            const chunks = [];

            for (let i = 0; i < this.meaningful.length; i += perColumn) {
                chunks.push(this.meaningful.slice(i, i + perColumn));
            }

            return chunks;
        }
    },
    methods: {
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },
        getRadioReasonIcon(value) {
            if (this.resumeData && this.resumeData && this.resumeData.exit_id) {
                return this.resumeData.exit_id == value ? "check-circle-fill" : "circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getRadioReasonIconVariant(value) {
            if (this.resumeData && this.resumeData && this.resumeData.exit_id) {
                return this.resumeData.exit_id == value ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getRadioConditionIcon(value) {
            if (this.resumeData && this.resumeData && this.resumeData.exit_condition_type_id) {
                return this.resumeData.exit_condition_type_id == value ? "check-circle-fill" : "circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getRadioConditionIconVariant(value) {
            if (this.resumeData && this.resumeData && this.resumeData.exit_condition_type_id) {
                return this.resumeData.exit_condition_type_id == value ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getRadioControlIcon(value) {
            if (this.resumeData && this.resumeData && this.resumeData.care_instruction_control) {
                return this.resumeData.care_instruction_control == value ? "check-circle-fill" : "circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getRadioControlIconVariant(value) {
            if (this.resumeData && this.resumeData && this.resumeData.care_instruction_control) {
                return this.resumeData.care_instruction_control == value ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },
        getRadioOtherIcon(value) {
            if (this.resumeData && this.resumeData && this.resumeData.care_instruction_anythingelse) {
                return this.resumeData.care_instruction_anythingelse == value ? "check-circle-fill" : "circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getRadioOtherIconVariant(value) {
            if (this.resumeData && this.resumeData && this.resumeData.care_instruction_anythingelse) {
                return this.resumeData.care_instruction_anythingelse == value ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },

        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                this.printCanvas();
            }
        },
        printCanvas() {
            const canvas = this.$refs.canvas;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = canvas.innerHTML;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload()
        },
        closeWindow(){
            window.close()
        },
        handleBack() {
            this.$router.go(-1);
        },
        getData (id) {
			this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]
                console.log('Data: ', this.regData);
            })
        },
        getResume (resume_id) {
            this.$store.dispatch('resume/getDetail', resume_id).then(data => {
                this.resumeData = data[0]
                // console.log('Dataaaaaaaaaaa: ', this.resumeData);
                this.getSTGeneralisMeaningful(this.resumeData)

                // this.$store.dispatch('employee/getDetailById', this.regData.doctor_in_charge_id).then(employee => {
                //     this.employeeData = employee[0]
                //     console.log('this.employeeData: ', this.employeeData);
                // })


            })
        },
        getMeaningVariant(value) {
            if (value && value != 1) return 'text-danger-500'
            return ''
        },
        getSTGeneralisMeaningful(obj){
            let all_key = this.generalis_key

            let temp_array = []
            for (let i = 0; i < all_key.length; i++) {
                const el = all_key[i];
                if (obj[el.key] && obj[el.key] != el.default ) {
                    const func = this[el.name]
                    const result = func(obj[el.key])
                    temp_array.push({
                        key: el.label,
                        value: result
                    });
                }
            }

            this.meaningful = temp_array
        },
        editResume (){
            this.triggerResumeModal(this.regData)
        },
        triggerResumeModal(obj){
            const message = obj
            this.$refs.childPrintResumeRef.parentTrigger(message,'print_edit_gate');
        },
    },

    mounted() {
        window.addEventListener('keydown', this.handleKeyPress);

    },
    async created() {
        if (this.$route.params.id) {
            this.id = this.$route.params.id
            await this.getData(this.$route.params.id)
        }

        if (this.$route.params.resume_id) {
            await this.getResume(this.$route.params.resume_id)
        }
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
};

</script>
<style>
@import './../../../../../@core/css/main/main.min.css';

@media print {
    @page {
        size: A4;
        margin: 16mm;
    }
}
td {
    padding: 2px 0;
}

p {
    margin: 0;
    padding: 0;
}

.main-content {
    margin-top: 32px;
    padding-bottom: 32px;
}

/* .canvas-wrapper {
    display: flex;
    justify-content: center;
    padding-bottom: 40px;
} */
/* #canvas {
    width: 21.2cm !important;
    height: 100% !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    padding: 40px;
} */

.with-line {
    text-decoration: underline;
    text-decoration-thickness: 2px;
    text-underline-offset: 8px;
}

.w-40 {
    width: 40%;
}

.canvas-footer {
    position: absolute;
    bottom: 0;
}
</style>
