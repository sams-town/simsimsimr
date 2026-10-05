<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper mt-16">
                <div id="canvas" ref="canvas" style="position: relative;">
                    <div class="row align-items-center justify-content-center">
                        <!-- <div class="col-2">
                            <img src="../../../../../@core/assets/images/logo-thb-small.png" width="60%" alt="rs-logo"> <br>
                        </div> -->
                        <img src="../../../../../@core/assets/images/logo-thb-small.png" width="80px" alt="rs-logo"> <br>
                        <div class="text-regular-14 pl-16">
                            <p class="text-bold-18">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                        </div>
                    </div>
                    <div class="text-regular-16 mt-24" style="line-height: 140%;">
                        <table class="mt-16">
                            <tr>
                                <td class="col-3" style="vertical-align: top;">Nomor RM</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ this.patient.norm }}</td>
                            </tr>
                            <tr>
                                <td class="col-3" style="vertical-align: top;">Nama</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ this.patient.alias_name }}.{{ this.patient.name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3" style="vertical-align: top;">Tanggal Lahir</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ this.patient.birth_date }}</td>
                            </tr>
                            <tr>
                                <td class="col-3" style="vertical-align: top;">Jenis Kelamin</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ this.patient.sex_name }}</td>
                            </tr>
                        </table>
                        <div class="mt-16 text-center text-bold-16">
                            Tanggal Pemeriksaan: {{ this.result.date_added }}
                        </div>

                        <div class="card mt-16">
                            <!-- <div class="card-header text-center font-weight-bold">PENGKAJIAN DOKTER UMUM</div> -->
                            <div class="card-body">
                                <b-table-simple>
                                    <b-tbody class="text-regular-16">
                                        <b-tr>
                                            <b-td class="col-3">Keluhan Utama</b-td>
                                            <b-td class="px-8 text-center" style="width: 30px;">:</b-td>
                                            <b-td>{{ this.result.main_complaint !== '' ? this.result.main_complaint : this.result_2.main_complaint }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="col-3">Riwayat Penyakit Sekarang</b-td>
                                            <b-td class="px-8 text-center" style="width: 30px;">:</b-td>
                                            <b-td>{{ this.result.history_disease_now !== '' ? this.result.history_disease_now : this.result_2.history_disease_now }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="col-3">Riwayat Penyakit Dahulu</b-td>
                                            <b-td class="px-8 text-center" style="width: 30px;">:</b-td>
                                            <b-td>{{ this.result.history_disease_past !== '' ? this.result.history_disease_past : this.result_2.history_disease_past }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="col-3">Riwayat Alergi</b-td>
                                            <b-td class="px-8 text-center" style="width: 30px;">:</b-td>
                                            <b-td>{{ this.result.history_allergy !== '' ? this.result.history_allergy : this.result_2.history_allergy }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="col-3">Riwayat Obat</b-td>
                                            <b-td class="px-8 text-center" style="width: 30px;">:</b-td>
                                            <b-td>{{ this.result.history_drugs !== '' ? this.result.history_drugs : this.result_2.history_drugs }}</b-td>
                                        </b-tr>
                                    </b-tbody>
                                </b-table-simple>
                            </div>
                        </div>

                        <div class="card mt-16">
                            <div class="card-header text-center font-weight-bold">PENGKAJIAN DOKTER</div>
                            <div class="card-body">
                                <b-table-simple>
                                    <b-tbody class="text-regular-16">
                                        <b-tr>
                                            <b-td class="col-3">Pemeriksaan Fisik</b-td>
                                            <b-td class="text-center px-8" style="width: 30px;">:</b-td>
                                            <b-td>
                                                <div>{{ this.result.physical_check !== '' ? this.result.physical_check : this.result_2.physical_check }}</div>
                                                <div :class="this.result.physical_check ? 'mt-16' : ''">
                                                    <b>Systolic: </b>{{ this.result.systolic_blood_pressure !== 0 ? this.result.systolic_blood_pressure : this.result_2.systolic_blood_pressure }} mmHg,
                                                    <b>Diastolic: </b>{{ this.result.diastolic_blood_pressure !== 0 ? this.result.diastolic_blood_pressure : this.result_2.diastolic_blood_pressure }} mmHg,
                                                    <b>Frekuensi Nadi: </b>{{ this.result.pulse_frequency !== 0 ? this.result.pulse_frequency : this.result_2.pulse_frequency }} x/mnt,
                                                    <b>Frekuensi Nafas: </b>{{ this.result.breath_frequency !== 0 ? this.result.breath_frequency : this.result_2.breath_frequency }} x/mnt,
                                                    <b>Saturasi Oksigen: </b>{{ this.result.oximetry_frequency !== 0 ? this.result.oximetry_frequency : this.result_2.oximetry_frequency }} %,
                                                    <b>Suhu: </b>{{ this.result.temperature !== 0 ? this.result.temperature : this.result_2.temperature }} ᵒC,
                                                    <b>Oksigen: </b> {{ getOxygenName(result?.oxygen_type_id || '-') }},
                                                    <b>Kesadaran: </b> {{ getAwareName(result?.aware) }},
                                                    <b>GCS Mata: </b> {{ getGcsEyeName(result?.eye_score || '-') }},
                                                    <b>GCS Verbal: </b> {{ getGcsVerbalName(result?.verbal_score || '-') }},
                                                    <b>GCS Motorik: </b> {{ getGcsMotoricName(result?.motor_score || '-') }},
                                                    <!-- <b>GCS Pupil: </b> {{ getGcsPupilName(result?.pupil_score) }}, -->
                                                    <!-- <b>Skor Nyeri: </b>{{ this.result.sore_score !== 0 ? this.result.sore_score : this.result_2.sore_score }},
                                                    <b>EWS: </b>{{ this.result.ews !== 0 ? this.result.ews : this.result_2.ews }},
                                                    <b>Gula Darah: </b>{{ this.result.blood_sugar !== 0 ? this.result.blood_sugar : this.result_2.blood_sugar }} mg/dL, -->
                                                </div>
                                                <div class="mt-16">
                                                    <b>Berat Badan: </b>{{ this.result.weight !== 0 ? this.result.weight : this.result_2.weight }} kg,
                                                    <b>Tinggi Badan: </b>{{ this.result.height !== 0 ? this.result.height : this.result_2.height }} cm,
                                                    <b>IMT: </b>{{ this.result.imt !== 0 ? this.result.imt : this.result_2.imt }} kg/m²,
                                                    <b>Lingkar Kepala: </b>{{ this.result.head_circumference !== 0 ? this.result.head_circumference : this.result_2.head_circumference }} cm,
                                                    <b>Lingkar Dada: </b>{{ this.result.chest_circumference !== 0 ? this.result.chest_circumference : this.result_2.chest_circumference }} cm,
                                                    <b>Lingkar Lengan Atas: </b>{{ this.result.arm_circumference !== 0 ? this.result.arm_circumference : this.result_2.arm_circumference }} cm,
                                                    <b>Lingkar Perut: </b>{{ this.result.stomach_circumference !== 0 ? this.result.stomach_circumference : this.result_2.stomach_circumference }} cm,
                                                    <b>Lingkar Lutut: </b>{{ this.result.knee_circumference !== 0 ? this.result.knee_circumference : this.result_2.knee_circumference }} cm,
                                                    <!-- <b>Lainnya: </b>{{ this.result.soap_o_anythingelse ? this.result.soap_o_anythingelse : '-' }} -->
                                                </div>
                                            </b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="col-3">Diagnosis Primer</b-td>
                                            <b-td class="text-center px-8" style="width: 30px;">:</b-td>
                                            <b-td>
                                                <template v-if="this.result?.icd10?.length > 0">
                                                    <li v-for="item in this.result?.icd10">{{ item.code }} {{ item.name }}</li>
                                                </template>
                                                <template v-else>-</template>
                                                <!-- <p><b>Lainnya: </b>{{ result.soap_a_anythingelse }}</p> -->
                                            </b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="col-3">Diagnosis Non-ICD10</b-td>
                                            <b-td class="text-center px-8" style="width: 30px;">:</b-td>
                                            <b-td>
                                                <template v-if="this.result?.non_icd10?.length > 0">
                                                    <li v-for="item in this.result?.non_icd10">{{ item }}</li>
                                                </template>
                                                <template v-else>-</template>
                                            </b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="col-3">Tindakan</b-td>
                                            <b-td class="text-center px-8" style="width: 30px;">:</b-td>
                                            <b-td>
                                                <li v-for="item in this.result.transaction_doctor">{{ item.costing_name }} ( {{ item.date_added }} )</li>
                                            </b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="col-3">Rencana Pemeriksaan Penunjang</b-td>
                                            <b-td class="text-center px-8" style="width: 30px;">:</b-td>
                                            <b-td>
                                                <div v-if="result.doctor_in_charge_id">
                                                    <div v-if="DPJP_doctor.laboratory_list.length != 0 || DPJP_doctor.radiology_list.length != 0" class="mt-12">
                                                        <div class="font-weight-bold" style="color: blue; text-decoration: underline;">{{ result.employee_doctor_name }} (Dokter DPJP)</div>
                                                        <section v-if="DPJP_doctor.laboratory_list.length != 0" class="mt-8">
                                                            <div class="font-weight-bold">Laboratorium {{ DPJP_doctor.laboratory_list ? '' : ': -' }}</div>
                                                            <div v-for="(item, i) in DPJP_doctor.laboratory_list" :key="i" :class="i == 0 ? '' : 'mt-8'">
                                                                <div>No Order: {{ item.no_request ? item.no_request : '-' }}</div>
                                                                <div v-if="item.laboratory_request_item">
                                                                    <li v-for="(list, j) in item.laboratory_request_item" :key="j">{{ list.costing_name }}</li>
                                                                </div>
                                                            </div>
                                                        </section>
                                                        <section class="mt-20" v-if="DPJP_doctor.radiology_list.length != 0">
                                                            <div class="font-weight-bold">Radiologi {{ DPJP_doctor.radiology_list ? '' : ': -' }}</div>
                                                            <div v-for="(item, i) in DPJP_doctor.radiology_list" :key="i" :class="i == 0 ? '' : 'mt-8'">
                                                                <div>No Order: {{ item.no_request ? item.no_request : '-' }}</div>
                                                                <div v-if="item.radiology_request_item">
                                                                    <li v-for="(list, j) in item.radiology_request_item" :key="j">{{ list.costing_name }}</li>
                                                                </div>
                                                            </div>
                                                        </section>
                                                    </div>
                                                </div>
                                                <div v-if="result.doctor_in_charge_id_1">
                                                    <div v-if="FIRST_doctor.laboratory_list.length != 0 || FIRST_doctor.radiology_list.length != 0" class="mt-12">
                                                        <div class="font-weight-bold" style="color: blue; text-decoration: underline;">{{ result.doctor_in_charge_name_1 }} (Dokter Spesialis)</div>
                                                        <section v-if="FIRST_doctor.laboratory_list.length != 0" class="mt-8">
                                                            <div class="font-weight-bold">Laboratorium {{ FIRST_doctor.laboratory_list ? '' : ': -' }}</div>
                                                            <div v-for="(item, i) in FIRST_doctor.laboratory_list" :key="i" :class="i == 0 ? '' : 'mt-8'">
                                                                <div>No Order: {{ item.no_request ? item.no_request : '-' }}</div>
                                                                <div v-if="item.laboratory_request_item">
                                                                    <li v-for="(list, j) in item.laboratory_request_item" :key="j">{{ list.costing_name }}</li>
                                                                </div>
                                                            </div>
                                                        </section>
                                                        <section class="mt-20" v-if="FIRST_doctor.radiology_list.length != 0">
                                                            <div class="font-weight-bold">Radiologi {{ FIRST_doctor.radiology_list ? '' : ': -' }}</div>
                                                            <div v-for="(item, i) in FIRST_doctor.radiology_list" :key="i" :class="i == 0 ? '' : 'mt-8'">
                                                                <div>No Order: {{ item.no_request ? item.no_request : '-' }}</div>
                                                                <div v-if="item.radiology_request_item">
                                                                    <li v-for="(list, j) in item.radiology_request_item" :key="j">{{ list.costing_name }}</li>
                                                                </div>
                                                            </div>
                                                        </section>
                                                    </div>
                                                </div>
                                                <div v-if="result.doctor_in_charge_id_2">
                                                    <div v-if="SECOND_doctor.laboratory_list.length != 0 || SECOND_doctor.radiology_list.length != 0" class="mt-12">
                                                        <div class="font-weight-bold" style="color: blue; text-decoration: underline;">{{ result.doctor_in_charge_name_2 }} (Dokter Spesialis)</div>
                                                        <section v-if="SECOND_doctor.laboratory_list.length != 0" class="mt-8">
                                                            <div class="font-weight-bold">Laboratorium {{ SECOND_doctor.laboratory_list ? '' : ': -' }}</div>
                                                            <div v-for="(item, i) in SECOND_doctor.laboratory_list" :key="i" :class="i == 0 ? '' : 'mt-8'">
                                                                <div>No Order: {{ item.no_request ? item.no_request : '-' }}</div>
                                                                <div v-if="item.laboratory_request_item">
                                                                    <li v-for="(list, j) in item.laboratory_request_item" :key="j">{{ list.costing_name }}</li>
                                                                </div>
                                                            </div>
                                                        </section>
                                                        <section class="mt-20" v-if="SECOND_doctor.radiology_list.length != 0">
                                                            <div class="font-weight-bold">Radiologi {{ SECOND_doctor.radiology_list ? '' : ': -' }}</div>
                                                            <div v-for="(item, i) in SECOND_doctor.radiology_list" :key="i" :class="i == 0 ? '' : 'mt-8'">
                                                                <div>No Order: {{ item.no_request ? item.no_request : '-' }}</div>
                                                                <div v-if="item.radiology_request_item">
                                                                    <li v-for="(list, j) in item.radiology_request_item" :key="j">{{ list.costing_name }}</li>
                                                                </div>
                                                            </div>
                                                        </section>
                                                    </div>
                                                </div>
                                                <div class="mt-24" v-if="this.result.physiotherapy_request">
                                                    <div><b>FISIOTERAPI</b></div>
                                                    <div v-for="item in this.result.physiotherapy_request">
                                                        <li v-for="(list, index) in item.physiotherapy_request_items">{{ list.costing_name }}</li>
                                                        <!-- <div v-for="(list, index) in item.physiotherapy_request_items">
                                                            <div class="d-flex align-items-center">
                                                                <input type="checkbox" disabled checked>
                                                                <div class="ml-2">{{ list.costing_name }}</div>
                                                            </div>
                                                        </div> -->
                                                    </div>
                                                </div>
                                            </b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="col-3">Terapi</b-td>
                                            <b-td class="text-center px-8" style="width: 30px;">:</b-td>
                                            <b-td>
                                                <div v-if="result.doctor_in_charge_id && DPJP_doctor.prescription_list.length != 0">
                                                    <section class="mt-8">
                                                        <div class="font-weight-bold" style="color: blue; text-decoration: underline;">{{ result.employee_doctor_name }} (Dokter DPJP)</div>
                                                        <div>
                                                            <template v-if="DPJP_doctor.prescription_list">
                                                                <div v-for="(item, i) in DPJP_doctor.prescription_list" :key="i" :class="i == 0 ? '' : 'mt-8'">
                                                                    <div>No Order: {{ item.no_order? item.no_order: '-' }}</div>
                                                                    <div v-if="item.form_prescription">
                                                                        <li v-for="(list, j) in item.form_prescription" :key="j">{{ list.pharmacy_catalog_name }}, {{ list.used }}</li>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </section>
                                                </div>

                                                <div v-if="result.doctor_in_charge_id_1 && FIRST_doctor.prescription_list.length != 0">
                                                    <section class="mt-8">
                                                        <div class="font-weight-bold" style="color: blue; text-decoration: underline;">{{ result.doctor_in_charge_name_1 }} (Dokter Spesialis)</div>
                                                        <div>
                                                            <template v-if="FIRST_doctor.prescription_list">
                                                                <div v-for="(item, i) in FIRST_doctor.prescription_list" :key="i" :class="i == 0 ? '' : 'mt-8'">
                                                                    <div>No Order: {{ item.no_order? item.no_order: '-' }}</div>
                                                                    <div v-if="item.form_prescription">
                                                                        <li v-for="(list, j) in item.form_prescription" :key="j">{{ list.pharmacy_catalog_name }}, {{ list.used }}</li>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </section>
                                                </div>

                                                <div v-if="result.doctor_in_charge_id_2 && SECOND_doctor.prescription_list.length != 0">
                                                    <section class="mt-8">
                                                        <div class="font-weight-bold" style="color: blue; text-decoration: underline;">{{ result.doctor_in_charge_name_2 }} (Dokter Spesialis)</div>
                                                        <div>
                                                            <template v-if="SECOND_doctor.prescription_list">
                                                                <div v-for="(item, i) in SECOND_doctor.prescription_list" :key="i" :class="i == 0 ? '' : 'mt-8'">
                                                                    <div>No Order: {{ item.no_order? item.no_order: '-' }}</div>
                                                                    <div v-if="item.form_prescription">
                                                                        <li v-for="(list, j) in item.form_prescription" :key="j">{{ list.pharmacy_catalog_name }}, {{ list.used }}</li>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </section>
                                                </div>


                                            </b-td>
                                        </b-tr>
                                        <b-tr v-if="result.health_facility_item_id == hfii_mcu">
                                            <b-td class="col-3">Kesimpulan</b-td>
                                            <b-td class="text-center px-8" style="width: 30px;">:</b-td>
                                            <b-td>{{ result?.fit_to_work || '-' }}</b-td>
                                        </b-tr>
                                    </b-tbody>
                                </b-table-simple>
                            </div>
                        </div>

                        <div class="card mt-16">
                            <div class="card-header text-center font-weight-bold">INFORMASI DOKTER</div>
                            <div class="card-body">
                                <b-table-simple>
                                    <b-tbody class="text-regular-16">
                                        <b-tr>
                                            <b-td class="col-3">Nama Dokter</b-td>
                                            <b-td class="text-center px-8" style="width: 30px;">:</b-td>
                                            <b-td>{{ this.result.employee_doctor_name }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="col-3">Tanggal & Jam Input </b-td>
                                            <b-td class="text-center px-8" style="width: 30px;">:</b-td>
                                            <b-td>{{ this.result.date_added }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="col-3">Poli</b-td>
                                            <b-td class="text-center px-8" style="width: 30px;">:</b-td>
                                            <b-td>{{ this.patient.health_facility_item_name }}</b-td>
                                        </b-tr>
                                    </b-tbody>
                                </b-table-simple>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end w-100 mt-20">
                            <div class="col-5 text-center">
                                <div :class="result.employee_signature ? '' : 'mb-64'" >DPJP</div>
                                <template v-if="result.employee_signature">
                                    <b-img :src="urlbe+result.employee_signature" style="width: 120px;"/>
                                </template>
                                <div>( {{ this.result.employee_doctor_name }} )</div>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center pb-40" style="border-bottom: 2px solid black;margin-top: 2000px;" v-if="resultlab.result_detail">
                        <div class="col-2 text-center">
                            <img src="../../../../../@core/assets/images/logo-thb-small.png" width="60%" alt="rs-logo"> <br>
                        </div>
                        <div class="col text-center">
                            <p class="text-bold-18">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                        </div>
                        <div class="col-2 text-center">
                            <img src="../../../../../@core/assets/images/lafki-logo.png" width="60%" alt="lafki-logo"> <br>
                        </div>
                    </div>
                    <div class="mt-20" v-if="resultlab.result_detail">
                        <p class="text-center text-bold-16">HASIL PEMERIKSAAN LABORATORIUM</p>
                        <div class="row mx-0 mt-28">
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td>No. Reg / RM <br> <i class="text-regular-12">Medical Record Number	</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{patient.code}} / {{patient.norm}}</td>
                                    </tr>
                                    <tr>
                                        <td>NIK <br> <i class="text-regular-12">Resident ID Number</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{patient.idcard}}</td>
                                    </tr>
                                    <tr>
                                        <td>Nama Pasien <br> <i class="text-regular-12">Pattient Name</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{patient.name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Alamat <br> <i class="text-regular-12">Address</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{patient.address}}</td>
                                    </tr>
                                    <tr>
                                        <td>Tgl. Permintaan <br> <i class="text-regular-12">Date Request</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{orderData.date_request}}</td>
                                    </tr>
                                    <tr>
                                        <td>Tgl. Hasil <br> <i class="text-regular-12">Date Reported</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{orderData.date_result}}</td>
                                    </tr>
                                    <!-- <tr>
                                        <td>Tgl. Cetak <br> <i class="text-regular-12">Print Out Date</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{orderData.date_request}}</td>
                                    </tr> -->
                                </table>
                            </div>
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td>No. Lab <br> <i class="text-regular-12">Lab Number</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{orderData.no_request}}</td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal Lahir <br> <i class="text-regular-12">Date of Birth</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{patient.birth_date}}</td>
                                    </tr>
                                    <tr>
                                        <td>Umur <br> <i class="text-regular-12">Age</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{parsed_age(this.patient.birth_date)}}</td>
                                    </tr>
                                    <tr>
                                        <td>Kelamin <br> <i class="text-regular-12">Sex</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{patient.sex_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Dokter Pengirim <br> <i class="text-regular-12">Reffering Doctor</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">{{orderData.doctor_in_charge_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Dokter Lab <br> <i class="text-regular-12">Lab Doctor</i></td>
                                        <td style="vertical-align:top;" class="px-3">:</td>
                                        <td style="vertical-align:top;">dr. Andini Triasti Siregar, M.ked, Sp.PK</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /info-table -->
                        <div class="px-3 mt-32">
                            <b-table bordered responsive head-variant="light" hover small :fields="resultFields"
                                :items="resultlab.result_detail" tbody-tr-class="text-medium-14" class="mt-12 text-center">
                            </b-table>
                        </div>
                    </div>

                    <div class="row align-items-center pb-40" style="border-bottom: 2px solid black;margin-top: 3000px;" v-if="orderDataRad.no_request && reqItemData">
                        <div class="col-2 text-center">
                            <img src="../../../../../@core/assets/images/logo-thb-small.png" width="60%" alt="rs-logo"> <br>
                        </div>
                        <div class="col text-center">
                            <p class="text-bold-18">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                        </div>
                        <div class="col-2 text-center">
                            <img src="../../../../../@core/assets/images/lafki-logo.png" width="60%" alt="lafki-logo"> <br>
                        </div>
                    </div>
                    <div class="mt-20" v-if="orderDataRad.no_request && reqItemData">
                        <p class="text-center text-bold-16">EXPERTISE RADIOLOGI</p>
                        <div class="row mx-0 mt-28">
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell">No. Reg / RM</td>
                                        <td class="px-3">:</td>
                                        <td>{{patient.code}} / {{patient.norm}}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Nama Pasien</td>
                                        <td class="px-3">:</td>
                                        <td>{{ patient.alias_name }} {{patient.name}}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Tanggal Order</td>
                                        <td class="px-3">:</td>
                                        <td>{{orderDataRad.date_request ? localDate(orderDataRad.date_request) : ''}}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Tanggal Hasil</td>
                                        <td class="px-3">:</td>
                                        <td>
                                        <template v-if="reqItemData[0].url">
                                            <template v-for="list in reqItemData[0].url">
                                                <template v-if="list.status !== 1"></template>
                                                {{list.date_result ? localDate(list.date_result) : ''}}
                                            </template>
                                        </template>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Alamat</td>
                                        <td class="px-3">:</td>
                                        <td>{{patient.address}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-6">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell">No. Radiologi</td>
                                        <td class="px-3">:</td>
                                        <td>{{orderDataRad.no_request}}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Tanggal Lahir</td>
                                        <td class="px-3">:</td>
                                        <td>{{patient.birth_date ? localDate(patient.birth_date) : ''}}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Umur</td>
                                        <td class="px-3">:</td>
                                        <td>{{parsed_age(patient.birth_date)}}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Kelamin</td>
                                        <td class="px-3">:</td>
                                        <td>{{patient.sex_name}}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell"> Dokter Pengirim</td>
                                        <td class="px-3">:</td>
                                        <td>{{ patient.doctor_in_charge_name }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="mt-32 col">
                            <div>Yth.{{ patient.doctor_in_charge_name }}</div>
                            <div class="mt-12">{{ reqItemData[0].costing_name }}</div>
                            <template v-if="reqItemData[0].url">
                                <template v-for="list in reqItemData[0].url">
                                    <template v-if="list.status !== 1">
                                        <table class="w-100 custom-border">
                                            <tr>
                                                <td class="align-top" style="width: 150px;">Klinis</td>
                                                <td class="align-top text-center px-8" style="width: 30px;">:</td>
                                                <td>
                                                    <template v-if="result.icd10">
                                                        <li v-for="(item, index) in result.icd10" :key="index">{{ item.code }} {{ item.name }}</li>
                                                    </template>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top">Expertise</td>
                                                <td class="align-top text-center px-8">:</td>
                                                <td><span v-html="parsedBody(list.conclusion ? list.conclusion : '-')"></span></td>
                                            </tr>
                                            <tr class="font-weight-bold">
                                                <td class="align-top pt-32">Kesan</td>
                                                <td class="align-top text-center px-8 pt-32">:</td>
                                                <td class="pt-32"><span v-html="parsedBody(list.advice ? list.advice : '-')"></span></td>
                                            </tr>
                                        </table>
                                        <div class="mt-32 d-flex justify-content-end text-center">
                                            <div>
                                                <div>Bekasi, {{ localDate(list.date_result) }}</div>
                                                <div :class="list.ttd_name ? '' : 'mb-72'">Salam Sejawat</div>
                                                <b-img :src="urlbe+list.ttd_name" style="width: 220px;" />
                                                <!-- <div>{{ list.ttd_name }}</div> -->
                                                <div>{{ list.doctor_lab_name }}</div>
                                            </div>
                                        </div>
                                    </template>
                                </template>
                            </template>
                        </div>
                    </div>

                    <!-- <iframe style="border: none" width="100%" height="1300" :src="`http://192.168.100.3:5000/${patient.registration_insurance[0].file_kronologis}`" v-if="patient.registration_insurance[0].file_kronologis" /> -->
                    <template v-for="(item,i) in examination_list" v-if="examination_list.length > 0">
                        <template v-for="(item_data,x) in item.data.files" v-if="item.data.files.length > 0">
                            <!-- <iframe style="width: 100vw; height: 100vh" :src="`http://192.168.100.3:5000/${item_data.url}`" v-if="item_data.url" /> -->
                            <img style="width:900px" :src="`http://192.168.100.3:5000/${item_data.url}`" v-if="item_data.url" />
                            {{ item_data.expertise }}
                        </template>
                    </template>
                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
import optionMixin from '@/mixins/optionMixin';
import moment from 'moment';
export default {
    mixins: [optionMixin],
    data() {
        return {
            examination_list:[],
            urlbe:process.env.VUE_APP_IMAGE,
            hfii_mcu: 'd46e2eb6-85a9-4bd4-b515-a0858cc50c8f',
            result: {},
            result_2: {},
            patient: {},
            resultlab: {},
            orderData:{},
            urlData:{},
            orderDataRad:{},
            reqItemData:{},

            resultFields: [
                { key: 'Parameter', label: 'Parameter' },
                { key: 'hasil', label: 'Hasil' },
                { key: 'flag', label: 'Flag' },
                { key: 'n_rujukan', label: 'Nilai Rujukan' },
                { key: 'satuan', label: 'Satuan' },
                { key: 'metoda', label: 'Metoda' },
            ],

            DPJP_doctor: {
                laboratory_list: [],
                radiology_list: [],
                prescription_list: [],
            },

            FIRST_doctor: {
                laboratory_list: [],
                radiology_list: [],
                prescription_list: [],
            },

            SECOND_doctor: {
                laboratory_list: [],
                radiology_list: [],
                prescription_list: [],
            },
        }
    },
    mounted() {
        this.getData(this.$route.params.soap_id)
        window.addEventListener('keydown', this.handleKeyPress);
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
    methods: {
        parsed_age(input_date) {
            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            return `${years} th ${months} bln ${days} hr`;
        },
        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                this.printCanvas();
            }
        },
        closeWindow() {
            window.close()
        },
        printCanvas() {
            const canvas = this.$refs.canvas;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = canvas.innerHTML;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload()
        },
        getData (soap_id) {
            this.$store.dispatch('soap/getBySoapId', soap_id).then(data => {
                this.result = data[0]
                let dataSoap = {}
                this.$store.dispatch('soap/getSoapDetail', {registration_destination_id:data[0].registration_destination_id}).then(data => {
                    console.log(data[0])
                    let dataSoap = data.find(item => item.id !== soap_id)
                    this.result_2 = dataSoap
                    if (this.result.laboratory_request_new && this.result.doctor_in_charge_id) {
                        let DPJP_param = {soap_id:this.result.id, doctor_in_charge_id:this.result.doctor_in_charge_id}
                        this.getDPJPStudies(DPJP_param)
                        this.getDataLab(this.result.laboratory_request.id)
                        this.getDataRad(this.result.radiology_request.id)
                        this.$store.dispatch('soap/getExamination', {soap_id: this.result.id}).then(response => {
                            this.examination_list = response
                            // console.log('this.examination_list: ', this.examination_list);
                        })
                    } else if (this.result_2.laboratory_request_new.length > 0 && this.result_2.doctor_in_charge_id) {
                        let DPJP_param = {soap_id:this.result_2.id, doctor_in_charge_id:this.result.doctor_in_charge_id}
                        this.getDPJPStudies(DPJP_param)
                        this.getDataLab(this.result_2.laboratory_request.id)
                        this.getDataRad(this.result_2.radiology_request.id)
                        this.$store.dispatch('soap/getExamination', {soap_id: this.result_2.id}).then(response => {
                            this.examination_list = response
                            // console.log('this.examination_list: ', this.examination_list);
                        })
                    }
                })
                if (this.result.laboratory_request_new && this.result.doctor_in_charge_id) {
                    let DPJP_param = {soap_id:this.result.id, doctor_in_charge_id:this.result.doctor_in_charge_id}
                    this.getDPJPStudies(DPJP_param)
                    this.getDataLab(this.result.laboratory_request.id)
                    this.getDataRad(this.result.radiology_request.id)
                    this.$store.dispatch('soap/getExamination', {soap_id: this.result.id}).then(response => {
                        this.examination_list = response
                        // console.log('this.examination_list: ', this.examination_list);
                    })
                }

                if (this.result && this.result.doctor_in_charge_id_1) {
                    let FIRST_param = {soap_id:this.result.id, doctor_in_charge_id:this.result.doctor_in_charge_id_1}
                    this.getFIRSTStudies(FIRST_param)
                }

                if (this.result && this.result.doctor_in_charge_id_2) {
                    let SECOND_param = {soap_id:this.result.id, doctor_in_charge_id:this.result.doctor_in_charge_id_2}
                    this.getSECONDStudies(SECOND_param)
                }

                this.$store.dispatch('registration/getById', this.result.registration_destination_id).then(data => {
                    this.patient = data[0]
                    // console.log('patient', this.patient);
                })
            })
        },

        getDataLab (id) {
			this.$store.dispatch('laboratory/getByIdOrder', id).then(data => {
                this.orderData = data[0]
                this.$store.dispatch('laboratory/getResultById', id).then(result => {
                    this.resultlab = result[0]
                    console.log('this.result', this.result);
                })
            })
        },
        getDataRad (id) {
			this.$store.dispatch('radiology/getByIdOrder', id).then(data => {
                this.orderDataRad = data[0]
                this.reqItemData = this.orderDataRad?.radiology_request_item
                // this.reqItemData = req_item.filter((item) => item.status !== 1)
            })
        },

        getDPJPStudies(param) {
            try {

                this.$store.dispatch('laboratory/getOrderDetail', param).then((response) => {
                    this.DPJP_doctor.laboratory_list = response
                    // console.log('this.DPJP_doctor.laboratory_list: ', this.DPJP_doctor.laboratory_list);
                })

                this.$store.dispatch('radiology/getOrderDetail', param).then((response) => {
                    this.DPJP_doctor.radiology_list = response
                    // console.log('this.DPJP_doctor.radiology_list: ', this.DPJP_doctor.radiology_list);
                })

                this.$store.dispatch('prescription/getDetail', param).then((response) => {
                    this.DPJP_doctor.prescription_list = response
                    // console.log('this.DPJP_doctor.prescription_list: ', this.DPJP_doctor.prescription_list);
                })

            } catch (error) {
                console.error('Error at: ', error);
            }
        },

        getFIRSTStudies(param) {
            try {
                this.$store.dispatch('laboratory/getOrderDetail', param).then((response) => {
                    this.FIRST_doctor.laboratory_list = response
                    // console.log('this.FIRST_doctor.laboratory_list: ', this.FIRST_doctor.laboratory_list);
                })

                this.$store.dispatch('radiology/getOrderDetail', param).then((response) => {
                    this.FIRST_doctor.radiology_list = response
                    // console.log('this.FIRST_doctor.radiology_list: ', this.FIRST_doctor.radiology_list);
                })

                this.$store.dispatch('prescription/getDetail', param).then((response) => {
                    this.FIRST_doctor.prescription_list = response
                    // console.log('this.FIRST_doctor.prescription_list: ', this.FIRST_doctor.prescription_list);
                })

            } catch (error) {
                console.error('Error at: ', error);
            }
        },

        getSECONDStudies(param) {
            try {
                this.$store.dispatch('laboratory/getOrderDetail', param).then((response) => {
                    this.SECOND_doctor.laboratory_list = response
                    // console.log('this.SECOND_doctor.laboratory_list: ', this.SECOND_doctor.laboratory_list);
                })

                this.$store.dispatch('radiology/getOrderDetail', param).then((response) => {
                    this.SECOND_doctor.radiology_list = response
                    // console.log('this.SECOND_doctor.radiology_list: ', this.SECOND_doctor.radiology_list);
                })

                this.$store.dispatch('prescription/getDetail', param).then((response) => {
                    this.SECOND_doctor.prescription_list = response
                    // console.log('this.SECOND_doctor.prescription_list: ', this.SECOND_doctor.prescription_list);
                })

            } catch (error) {
                console.error('Error at: ', error);
            }
        },
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },
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

.canvas-wrapper {
    display: flex;
    justify-content: center;
    padding-bottom: 40px;
}
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
