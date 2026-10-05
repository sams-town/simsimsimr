<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="row align-items-center">
                        <div class="col-2">
                            <img src="../../../@core/assets/images/logo-thb.jpg" width="300%" alt="rs-logo">
                            <br>
                        </div>
                        <div class="col text-center text-bold-28">
                            <!-- <p class="text-bold-14">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>
                             -->
                        </div>
                        <div class="col-2">
                            <tr>
                                <td><img src="../../../@core/assets/images/lafki-logo.png" width="85%" alt="lafki-logo"
                                        align="right">
                                </td>
                            </tr>
                        </div>
                        <div class="col-2">
                            <tr>
                                <td><img src="../../../@core/assets/images/apexa.png" width="80%" alt="-logo"
                                        align="right">
                                </td>
                            </tr>
                        </div>
                    </div>
                    <div class="text-uppercase text-center mt-5">
                        <div class="text-bold-24 with-line">PERKIRAAN BIAYA OPERASI/TINDAKAN</div>
                        <div class="text-regular-24">{{ letterData.no }}</div>
                    </div>
                    <div class="text-regular-18 mt-32" style="line-height: 140%;">
                        <!-- <div>Yang bertanda tangan dibawah ini:.., menerangkan dengan sesungguhnya,bahwa telah lahir
                            seorang bayi dengan jenis kelamin:.. <span>{{
                                regData.doctor_in_charge_name
                                }}</span>
                        </div> -->
                        <div class="row">
                            <div class="col-9">
                                <div class="d-flex mb-2">
                                    <div style="width: 150px;">Diagnosa</div>
                                    <div class="mx-2">:</div>
                                    <div>{{ regData.fullname }}</div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div>Jenis Operasi / Tindakan yang akan
                                        dilakukan</div>
                                    <div class="mx-2">:</div>
                                    <div>{{ regData.fullname }}</div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div style="width: 150px">Dokter
                                        Operator</div>
                                    <div class="mx-2">:</div>
                                    <div>{{ regData.fullname }}</div>
                                </div>
                            </div>
                            <div class="col-3 d-flex align-items-start justify-content-end">
                                <div class="sticker-box">
                                    Stiker Pasien
                                </div>
                            </div>
                        </div>
                        <div class="mt-4"><b>Perkiraan biaya untuk kelas</b> :</div>
                        <div class="row">
                            <div class="ml-4 col-9">
                                <div class="d-flex mb-2">
                                    <div style="width: 150px;">Diagnosa</div>
                                    <div class="mx-2">:</div>
                                    <div>{{ regData.fullname }}</div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div>Jenis Operasi / Tindakan yang akan
                                        dilakukan</div>
                                    <div class="mx-2">:</div>
                                    <div>{{ regData.fullname }}</div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div style="width: 150px">Dokter
                                        Operator</div>
                                    <div class="mx-2">:</div>
                                    <div>{{ regData.fullname }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <strong>Perkiraan biaya untuk kelas :</strong>
                            <div class="mt-2">

                                <div class="d-flex mb-2">
                                    <div class="mr-2">1.</div>
                                    <div class="flex-grow-1 d-flex justify-content-between">
                                        <div>Kamar .................. / Hari</div>
                                        <div>..............</div>
                                    </div>
                                </div>

                                <div class="d-flex mb-2">
                                    <div class="mr-2">2.</div>
                                    <div class="flex-grow-1 d-flex justify-content-between">
                                        <div>Dokter Operator</div>
                                        <div>..............</div>
                                    </div>
                                </div>

                                <div class="d-flex mb-2">
                                    <div class="mr-2">3.</div>
                                    <div class="flex-grow-1 d-flex justify-content-between">
                                        <div>Dokter Anestresi</div>
                                        <div>..............</div>
                                    </div>
                                </div>

                                <div class="d-flex mb-2">
                                    <div class="mr-2">4.</div>
                                    <div class="flex-grow-1 d-flex justify-content-between">
                                        <div>Dokter Asisten</div>
                                        <div>..............</div>
                                    </div>
                                </div>

                                <div class="d-flex mb-2">
                                    <div class="mr-2">5.</div>
                                    <div class="flex-grow-1 d-flex justify-content-between">
                                        <div>Obat Anestesi</div>
                                        <div>..............</div>
                                    </div>
                                </div>

                                <div class="d-flex mb-2">
                                    <div class="mr-2">6.</div>
                                    <div class="flex-grow-1 d-flex justify-content-between">
                                        <div>Biaya Kamar Operasi / Tindakan</div>
                                        <div>..............</div>
                                    </div>
                                </div>

                                <div class="d-flex mb-2">
                                    <div class="mr-2">7.</div>
                                    <div class="flex-grow-1 d-flex justify-content-between">
                                        <div>
                                            Alat Kesehatan
                                            <span
                                                style="display: inline-block; border-bottom: 2px solid black; width: 100px; margin-left: 5px;"></span>
                                        </div>
                                        <div>..............</div>
                                    </div>
                                </div>

                            </div>

                            <div class="mt-2 d-flex align-items-center">
                                <strong>*** TOTAL BIAYA SEMENTARA</strong>
                                <span
                                    style="display: inline-block; border-top: 2px solid black; width: 150px; margin-left: 10px;"></span>
                            </div>
                        </div>
                        <table class="mt-1">
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Kamar</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.fullname }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Dokter Operator</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.birth_date }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Dokter Anestresi</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.sex_name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Dokter Asisten</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.sex_name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Obat Anestesi</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.sex_name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Biaya Kamar Operasi/Tindakan</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.sex_name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Alat Kesehatan</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.sex_name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;"><b>***Total Biaya Sementara</b></td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.sex_name }}</td>
                            </tr>
                        </table>
                        <div class="mt-4"><b>Keterangan</b>:</div>
                        <div class="mt-1">1.Biaya tersebut <u><i>diatas dilunasi atau minimal 72%.</i></u> dan total
                            biaya sebelum operasi dilaksanakan</div>
                        <div class="mt-1">2.Total biaya tersebut diatas belum termasuk biaya
                            laboratorium,Apotek,Rontgen,Visite Dopkter Konsulen dan lain-lain bila diperlukan.
                        </div>
                        <div class="mt-1">3.Perkiraan biaya akan berubah bila terjadi penyulit(Komplikasi) pada saat
                            operasi dan pasca operasi
                        </div>
                        <div class="mt-1">4.Untuk pasien jaminan perusahaan yang telah kontrak kerjasama harus
                            ditandatangan oleh perusahaan yang bersangkutan
                        </div>
                        <div class="mt-1">5.Untuk pasien jaminan BPJS Kesehatan tidak perlu rincian kecuali upgrade
                            kelas
                        </div>
                        <div class="mt-5"><b>Hubungan dengan pasien</b>:</div>
                        <div class="d-flex justify-content-end w-100 mt-64">
                            <div class="col-10 text-center">
                                <div>Bekasi .../.../20..</div>
                                <div :class="soapData.employee_signature ? '' : 'mb-80'">Yang Mengambil</div>
                                {{ ('(.................................................)') }}
                                <div :class="soapData.employee_signature ? '' : 'mb-80'">tanda tangan dan nama jelas
                                </div>
                                <!-- <template v-if="soapData.employee_signature">
                                    <b-img :src="urlbe + soapData.employee_signature" style="width: 120px;" />
                                </template> -->
                                <div>{{ soapData.employee_doctor_name }}</div>
                            </div>
                            <div class="col-5 text-center">
                                <div>Bekasi .../.../20..</div>
                                <div :class="soapData.employee_signature ? '' : 'mb-80'">Bagian Keuangan</div>
                                <!-- <div> {{ ('Materi 10000') }}</div> -->
                                {{ ('(.................................................)') }}
                                <div :class="soapData.employee_signature ? '' : 'mb-80'">tanda dan nama jelas
                                </div>
                                <!-- <template v-if="soapData.employee_signature">
                                    <b-img :src="urlbe + soapData.employee_signature" style="width: 120px;" />
                                </template> -->
                                <div>{{ soapData.employee_doctor_name }}</div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end w-100 mt-2">
                            <div class="col-10 text-center">
                                <div :class="soapData.employee_signature ? '' : 'mb-80'">Mengerti & Menyetujui</div>
                                {{ ('(.................................................)') }}
                                <div :class="soapData.employee_signature ? '' : 'mb-80'"><b>*</b>keluarga pasien
                                </div>
                                <!-- <template v-if="soapData.employee_signature">
                                    <b-img :src="urlbe + soapData.employee_signature" style="width: 120px;" />
                                </template> -->
                                <div>{{ soapData.employee_doctor_name }}</div>
                            </div>
                            <div class="col-5 text-center">
                                <div :class="soapData.employee_signature ? '' : 'mb-80'">Mengerti & Menyetujui</div>
                                {{ ('(.................................................)') }}
                                <div :class="soapData.employee_signature ? '' : 'mb-80'"><b>*</b>perusahaan penanggung
                                    jawab
                                    biaya
                                </div>
                                <!-- <template v-if="soapData.employee_signature">
                                    <b-img :src="urlbe + soapData.employee_signature" style="width: 120px;" />
                                </template> -->
                                <div>{{ soapData.employee_doctor_name }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
import moment from 'moment';
import { mapState } from 'vuex';

export default {
    data() {
        return {
            urlbe: process.env.VUE_APP_IMAGE,
            regData: {},
            soapData: {},
            letterData: {},
            days: 1,
            soap_id: ''
        }
    },
    computed: {
        ...mapState({
            items: state => state.soap.items,
        })
    },
    filters: {
        convert_date(value) {
            return moment(new Date(value)).locale('id').format('LL')
        }
    },
    methods: {
        getData(id) {
            this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]
            })
        },
        getSoap(id) {
            this.$store.dispatch('soap/getBySoapId', id).then(data => {
                this.soapData = data[0]
            })
        },
        geLetter(id) {
            this.$store.dispatch('letter/getById', id).then(data => {
                this.letterData = data[0]
                const dateB = moment(data[0].end);
                const dateC = moment(data[0].start);
                this.days = dateB.diff(dateC, 'days') + 1
            })
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
            location.reload();
        },
        closeWindow() {
            window.close()
        },
    },
    mounted() {
        this.getData(this.$route.params.id)
        this.getSoap(this.$route.params.soap_id)
        this.geLetter(this.$route.params.letter_id)
        window.addEventListener('keydown', this.handleKeyPress);
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
};

</script>
<style>
/* @import './../../../../@core/css/main/main.min.css'; */
@import './../../../@core/css/main/main.min.css';


@media print {
    @page {
        size: A4;
        margin: 16mm;
    }

    .spnkp table,
    .spnkp th,
    .spnkp td {
        border: 2px solid black;
        border-collapse: collapse;
    }
}

.spnkp table,
.spnkp th,
.spnkp td {
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
}

#canvas {
    /* width: 210mm; */
    width: 21.2cm !important;
    height: 29.7cm !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    padding: 40px;
}

.with-line {
    text-decoration: underline;
    text-decoration-thickness: 2px;
    text-underline-offset: 8px;
}

/* .canvas-header {
    width: 100%;
}

.canvas-header img:first-child {
    width: 80px;
}

.canvas-header img:last-child {
    width: 100%;
    height: 100%;
    display: block;
    margin-top: -30px;
} */

.w-40 {
    width: 40%;
}

.canvas-footer {
    position: absolute;
    bottom: 0;
    /* right: 0; */
}

.sticker-box {
    border: 1px solid black;
    width: 300px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
