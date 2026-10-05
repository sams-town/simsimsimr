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
                                            <b-td>{{ this.result.main_complaint }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="col-3">Riwayat Penyakit Sekarang</b-td>
                                            <b-td class="px-8 text-center" style="width: 30px;">:</b-td>
                                            <b-td>{{ this.result.history_disease_now }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="col-3">Riwayat Penyakit Dahulu</b-td>
                                            <b-td class="px-8 text-center" style="width: 30px;">:</b-td>
                                            <b-td>{{ this.result.history_disease_past }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="col-3">Riwayat Alergi</b-td>
                                            <b-td class="px-8 text-center" style="width: 30px;">:</b-td>
                                            <b-td>{{ this.result.history_allergy }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td class="col-3">Riwayat Obat</b-td>
                                            <b-td class="px-8 text-center" style="width: 30px;">:</b-td>
                                            <b-td>{{ this.result.history_drugs }}</b-td>
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
                                                <div>{{ this.result.physical_check }}</div>
                                                <div :class="this.result.physical_check ? 'mt-16' : ''">
                                                    <b>Systolic: </b>{{ this.result.systolic_blood_pressure ? this.result.systolic_blood_pressure : '-' }} mmHg,
                                                    <b>Diastolic: </b>{{ this.result.diastolic_blood_pressure ? this.result.diastolic_blood_pressure : '-' }} mmHg,
                                                    <b>Frekuensi Nadi: </b>{{ this.result.pulse_frequency ? this.result.pulse_frequency : '-' }} x/mnt,
                                                    <b>Frekuensi Nafas: </b>{{ this.result.breath_frequency ? this.result.breath_frequency : '-' }} x/mnt,
                                                    <b>Saturasi Oksigen: </b>{{ this.result.oximetry_frequency ? this.result.oximetry_frequency : '-' }} %,
                                                    <b>Suhu: </b>{{ this.result.temperature ? this.result.temperature : '-' }} ᵒC,
                                                    <b>Oksigen: </b> {{ getOxygenName(result?.oxygen_type_id || '-') }},
                                                    <b>Kesadaran: </b> {{ getAwareName(result?.aware) }},
                                                    <b>GCS Mata: </b> {{ getGcsEyeName(result?.eye_score || '-') }},
                                                    <b>GCS Verbal: </b> {{ getGcsVerbalName(result?.verbal_score || '-') }},
                                                    <b>GCS Motorik: </b> {{ getGcsMotoricName(result?.motor_score || '-') }},
                                                    <!-- <b>GCS Pupil: </b> {{ getGcsPupilName(result?.pupil_score) }}, -->
                                                    <!-- <b>Skor Nyeri: </b>{{ this.result.sore_score ? this.result.sore_score : '-' }},
                                                    <b>EWS: </b>{{ this.result.ews ? this.result.ews : '-' }},
                                                    <b>Gula Darah: </b>{{ this.result.blood_sugar ? this.result.blood_sugar : '-' }} mg/dL, -->
                                                </div>
                                                <div class="mt-16">
                                                    <b>Berat Badan: </b>{{ this.result.weight ? this.result.weight : '-' }} kg,
                                                    <b>Tinggi Badan: </b>{{ this.result.height ? this.result.height : '-' }} cm,
                                                    <b>IMT: </b>{{ this.result.imt ? this.result.imt : '-' }} kg/m²,
                                                    <b>Lingkar Kepala: </b>{{ this.result.head_circumference ? this.result.head_circumference : '-' }} cm,
                                                    <b>Lingkar Dada: </b>{{ this.result.chest_circumference ? this.result.chest_circumference : '-' }} cm,
                                                    <b>Lingkar Lengan Atas: </b>{{ this.result.arm_circumference ? this.result.arm_circumference : '-' }} cm,
                                                    <b>Lingkar Perut: </b>{{ this.result.stomach_circumference ? this.result.stomach_circumference : '-' }} cm,
                                                    <b>Lingkar Lutut: </b>{{ this.result.knee_circumference ? this.result.knee_circumference : '-' }} cm,
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
                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
import optionMixin from '@/mixins/optionMixin';
export default {
    mixins: [optionMixin],
    data() {
        return {
            urlbe:process.env.VUE_APP_IMAGE,
            hfii_mcu: 'd46e2eb6-85a9-4bd4-b515-a0858cc50c8f',
            result: {},
            patient: {},

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
                // console.log('soap:', this.result)

                if (this.result && this.result.doctor_in_charge_id) {
                    let DPJP_param = {soap_id:this.result.id, doctor_in_charge_id:this.result.doctor_in_charge_id}
                    this.getDPJPStudies(DPJP_param)
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
