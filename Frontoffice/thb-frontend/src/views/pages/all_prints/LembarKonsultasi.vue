<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper mt-16">
                <div id="canvas" ref="canvas" style="position: relative;">
                    <b-table-simple class="w-100 sbpk">
                        <b-tr>
                            <b-td colspan="5" class="p-16">
                                <!-- <div class="row align-items-center">
                                    <div class="col-5">
                                        <img src="../../../@core/assets/images/logo-thb.jpg" width="240px"
                                            alt="rs-logo"> <br>
                                        <div class="text-regular-10 mt-12">
                                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                                        </div>
                                    </div>
                                    <div class="col text-center text-bold-24">LEMBAR KONSULTASI
                                    </div>
                                </div> -->
                                <div class="row align-items-center">
                                    <div class="col-2">
                                        <img src="../../../@core/assets/images/logo-thb.jpg" width="200%"
                                            alt="rs-logo">
                                        <br>
                                    </div>
                                    <div class="col text-center text-bold-28">
                                        <!-- <p class="text-bold-14">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>
                             -->
                                    </div>
                                    <div class="col text-center text-bold-24">LEMBAR KONSULTASI
                                    </div>
                                    <div class="col-2">
                                        <tr>
                                            <td><img src="../../../@core/assets/images/lafki-logo.png" width="50%"
                                                    alt="lafki-logo" align="right">
                                            </td>
                                        </tr>
                                    </div>
                                    <div class="col-2">
                                        <tr>
                                            <td><img src="../../../@core/assets/images/apexa.png" width="50%"
                                                    alt="-logo" align="right">
                                            </td>
                                        </tr>
                                    </div>
                                </div>
                            </b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="5" class="p-16">
                                <ol type="1">
                                    <li>
                                        <div class="row">
                                            <div class="col-5">Tanggal</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.fullname }}</div>
                                        </div>
                                    </li>
                                    <li class="mt-2">
                                        <div class="row">
                                            <div class="col-5">Jam</div>
                                            <div>:</div>
                                            <div class="col">{{ patient.norm }}</div>
                                        </div>
                                    </li>
                                </ol>
                            </b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="5" class="col-6">
                                <ol style="list-style-type:square">
                                    <!-- <li>
                                        <div class="row">
                                            <div class="col-2">Berat Lahir</div>
                                            <div>:</div>
                                            <div class="col">gram</div>
                                        </div>
                                    </li> -->
                                    <li class="mt-2">
                                        <div class="row">
                                            <div class="col-2">Kepada Yth. Dokter konsulen</div>
                                            <div>dr:.....</div>
                                            <!-- <div class="col">
                                                <b-radio-group v-model="form.exit_type_id" disabled>
                                                    <b-radio value="1">Poliklinik</b-radio>
                                                    <b-radio value="2">Sembuh</b-radio>
                                                    <b-radio value="3">Rawat</b-radio>
                                                    <b-radio value="4">Dirujuk</b-radio>
                                                    <b-radio value="5">PAPS</b-radio>
                                                    <b-radio value="6">Meninggal</b-radio>
                                                    <b-radio value="7">Kabur</b-radio>
                                                </b-radio-group>
                                            </div> -->
                                        </div>
                                    </li>
                                </ol>
                            </b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="5">
                                <div>
                                    <div class="font-weight-bold">Diganosa kerja :</div>
                                    <div>
                                        <ul type="square">
                                            <!-- <li>
                                                <b>Anamnesa : </b>
                                                <span>{{ result.anamnesa_type_id == 2 ? 'Allo Anamnesa' : 'Auto Anamnesa' }}</span>
                                            </li>
                                            <li>
                                                <b>Nama Keluarga : </b>
                                                <span class="text-capitalize">{{ result.family_name }} ({{ getFamilyRelation(result.family_relation_id) }})</span>
                                            </li> -->
                                            <li>
                                                <b>Riwayat klinik singkat: </b>
                                                <span>{{ result.main_complaint }}</span>
                                            </li>
                                            <li>
                                                <b>Konsultasi yang diterima : </b>
                                                <span>
                                                    <tr>
                                                        <td class="col-3"><b-radio value="1" disabled>Perawat Bersama
                                                            </b-radio></td>
                                                        <td class="col-3"><b-radio value="1" disabled>Ahli Rawat
                                                            </b-radio></td>
                                                        <td class="col-3"><b-radio value="1" disabled>Konsultasi
                                                            </b-radio></td>
                                                    </tr>
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end w-100">
                                    <div class="text-center px-32">
                                        <div> Bekasi, {{ today }}</div>
                                        <div :class="result.employee_signature ? '' : 'mb-64'">Nama dan tanda tangan
                                            dokter yang menerima</div>
                                        <template v-if="result.employee_signature">
                                            <b-img :src="urlbe + result.employee_signature" style="width: 120px;" />
                                        </template>
                                        <div>({{ result.employee_doctor_name }})</div>
                                    </div>
                                </div>
                            </b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="5"></b-td>
                        </b-tr>
                        <b-tr class="text-center text-bold-14">
                            <b-th style="width: 12%;">CATATAN KONSULTASI</b-th>
                        </b-tr>
                        <template>
                            <b-tr>
                                <b-td>......</b-td>
                            </b-tr>
                        </template>
                        <b-tr>
                            <b-td colspan="5"></b-td>
                        </b-tr>
                        <template>
                            <b-tr v-for="(item, index) in result.transaction_doctor">
                                <b-td colspan="5">{{ index + 1 }}. {{ item.costing_name }}</b-td>
                                <!-- <b-td></b-td> -->
                            </b-tr>
                        </template>
                        <b-tr>
                            <b-td colspan="5">
                                <div class="d-flex justify-content-end w-100">
                                    <div class="text-center px-32">
                                        <div> Bekasi, {{ today }}</div>
                                        <div :class="result.employee_signature ? '' : 'mb-64'">Nama dan tanda tangan
                                            dokter yang menerima</div>
                                        <template v-if="result.employee_signature">
                                            <b-img :src="urlbe + result.employee_signature" style="width: 120px;" />
                                        </template>
                                        <div>({{ result.employee_doctor_name }})</div>
                                    </div>
                                </div>
                                <div class="mt-24 text-uppercase text-semi-12">
                                    <div style="text-decoration: underline;">catatan:</div>
                                    <ol type="1">
                                        <li>Jawaban konsultasi: (Temuan,Diagnosa, dan Saran)</li>
                                        <li> Jawaban konsultasi DPJP utama awal terlampir di Formulir Asesmen Pasien
                                            Rawat Inap
                                        </li>
                                    </ol>
                                </div>
                            </b-td>
                        </b-tr>
                    </b-table-simple>
                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
import moment from 'moment';

export default {
    data() {
        return {
            urlbe: process.env.VUE_APP_IMAGE,
            result: {},
            patient: {},
            form: {
                exit_type_id: null
            },
            today: moment(new Date()).format('DD-MM-YYYY')
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
        getExitType() {
            const r_jalan = '3cb626f5-9148-4fb1-8663-3d7f028b01a6'
            this.form.exit_type_id = 0
            if (this.patient.health_facility_id == r_jalan) {
                this.form.exit_type_id = 1
            } else {
                if (this.patient.exit_id == '4a6d28c4-e020-425f-91e1-d39699cf679b') {
                    this.form.exit_type_id = 6
                } else if (this.patient.exit_id == 'a1631acc-6a94-414d-95de-a9faea0f1c2f') {
                    this.form.exit_type_id = 4
                } else {
                    this.form.exit_type_id = 0 // sementara, krn exit_id nya null semua
                }
                // exit:[
                //     { value: '807ca005-5827-4e19-9c97-0bc3d4e924ca', text: 'Atas Persetujuan Dokter' },
                //     { value: '4a6d28c4-e020-425f-91e1-d39699cf679b', text: 'Meninggal' },
                //     { value: 'a1631acc-6a94-414d-95de-a9faea0f1c2f', text: 'Dirujuk' },
                //     { value: 'ae0aabfa-1600-443f-8a60-62f9e76b1608', text: 'Permintaan Sendiri' }
                // ],
            }
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
        getData(soap_id) {
            this.$store.dispatch('soap/getBySoapId', soap_id).then(data => {
                this.result = data[0]
                console.log('soap:', this.result)

                this.$store.dispatch('registration/getById', this.result.registration_destination_id).then(data => {
                    this.patient = data[0]
                    // console.log('patient', this.patient);
                })
            })
        },
        // getFamilyRelation(id) {
        //     let data = ''
        //     if (id == 1) {
        //         return data = 'Ayah'

        //     } else if (id == 2) {
        //         return data = 'Ibu'

        //     } else if (id == 3) {
        //         return data = 'Kakak'

        //     } else if (id == 4) {
        //         return data = 'Adik'

        //     } else {
        //         return data = '-'

        //     }
        // },
    },

};

</script>
<style>
@import './../../../@core/css/main/main.min.css';

@media print {
    @page {
        size: A4;
        margin: 16mm;
    }

    .sbpk table,
    .sbpk th,
    .sbpk td {
        border: 2px solid black;
        border-collapse: collapse;
    }
}

.sbpk table,
.sbpk th,
.sbpk td {
    /* border: 0; */
    border: 2px solid black;
    border-collapse: collapse;
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
