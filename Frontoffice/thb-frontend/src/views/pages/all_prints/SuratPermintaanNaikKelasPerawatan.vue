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
                        <div class="text-bold-24 with-line">SURAT PERMINTAAN NAIK KELAS PERAWATAN</div>
                        <div class="text-regular-24">{{ letterData.no }}</div>
                    </div>
                    <div class="text-regular-18 mt-32" style="line-height: 140%;">
                        <div>Saya yang bertanda tangan dibawah ini dengan PASIEN: <span>{{
                                regData.doctor_in_charge_name
                                }}</span>
                        </div>
                        <table class="mt-16">
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Nama Pasien</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.fullname }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Usia (tahun)</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.birth_date }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Jenis Kelamin</td>
                                <td class="col-1 text-right">:</td>
                                <td>
                            <tr>
                                <td class="col-1"><b-radio value="1" disabled>Laki-laki
                                    </b-radio></td>
                                <td class="col-1"><b-radio value="1" disabled>Perempuan
                                    </b-radio></td>
                            </tr>
                            </td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">No MR</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.sex_name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">No Kartu BPJS</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.sex_name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Kelas Keperawatan BPJS</td>
                                <td class="col-1 text-right">:</td>
                                <td>
                            <tr>
                                <td class="col-3"><b-radio value="1" disabled>Kelas 1
                                    </b-radio></td>
                                <td class="col-3"><b-radio value="1" disabled>Kelas 2
                                    </b-radio></td>
                                <td class="col-3"><b-radio value="1" disabled>Kelas 3
                                    </b-radio></td>
                            </tr>
                            </td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Kelas Ditempati</td>
                                <td class="col-1 text-right">:</td>
                                <td>
                            <tr>
                                <td class="col-3"><b-radio value="1" disabled>VIP
                                    </b-radio></td>
                                <td class="col-3"><b-radio value="1" disabled>Kelas 1
                                    </b-radio></td>
                                <td class="col-3"><b-radio value="1" disabled>Kelas 2
                                    </b-radio></td>
                                <td class="col-3"><b-radio value="1" disabled>Kelas 3
                                    </b-radio></td>
                            </tr>
                            </td>
                            </tr>
                        </table>
                        <div class="mt-5">Atas persetujuan pasien,dengan ini mewakili pasien tersebut diatas:</div>
                        <table class="mt-16">
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Nama</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.fullname }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Hubungan dengan Pasien</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.birth_date }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">No HP</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.sex_name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Alamat Rumah</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.sex_name }}</td>
                            </tr>
                        </table>

                        <div class="mt-5">Menyatakan dengan sesungguhnya bahwa saya mengetahui manfaat BPJS Kesehatan.
                            Dan saya menyetujui
                            terhadap ketentuan Biaya Naik Kelas Rawatan Pasien JKN Non PBI, dengan ketentuan sbb:
                        </div>

                        <b-table-simple class="w-100 spnkp mt-5">
                            <b-tr class="text-center text-bold-14">
                                <b-th style="width: 12%;">NO</b-th>
                                <b-th>KELAS HAK</b-th>
                                <b-th>KELAS KEPERAWATAN</b-th>
                                <b-th>BIAYA KELAS KEPERAWATAN</b-th>
                            </b-tr>
                            <template>
                                <b-tr>
                                    <b-td>1</b-td>
                                    <b-td>Kelas 1</b-td>
                                    <b-td>VIP</b-td>
                                    <b-td><b>75 % ( tujuh puluh lima perseratus)</b> dari tarif INA CBGs kelas 1</b-td>
                                </b-tr>
                            </template>
                            <template>
                                <b-tr>
                                    <b-td>2</b-td>
                                    <b-td>Kelas 2</b-td>
                                    <b-td>kelas 1</b-td>
                                    <b-td>Tarif INA CBGs kelas 1 dikurangi tarif INA CBGs kelas 2</b-td>
                                </b-tr>
                            </template>
                            <template>
                                <template>
                                    <b-tr>
                                        <b-td>3</b-td>
                                        <b-td>Kelas 3</b-td>
                                        <b-td>Kelas 2</b-td>
                                        <b-td>Tarif INA CBGs kelas 2 dikurangi tarif INA CBGs kelas 3</b-td>
                                    </b-tr>
                                </template>
                            </template>
                        </b-table-simple>
                        <div class="mt-5">Biaya Naik Kelas Perawatan sesuai dengan ketentuan diatas akan dibayarkan
                            oleh:
                        </div>
                        <table class="mt-16">
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;"> <b-radio value="1"
                                        disabled>Pribadi</b-radio></td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.fullname }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;"> <b-radio value="1"
                                        disabled>Perusahaan pemberi
                                        kerja</b-radio></td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.fullname }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;"> <b-radio value="1"
                                        disabled>Asuransi tambahan
                                        lainnya</b-radio></td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.fullname }}</td>
                            </tr>
                        </table>
                        <div class="mt-5">Demikian surat permintaan naik kelas ini saya tanda tangani tanpa adanya
                            tekanan dan paksaan dari pihak manapun,dan saya tidak akan melakukan tuntutan dikemudian
                            hari.
                        </div>
                        <div class="d-flex justify-content-end w-100 mt-64">
                            <div class="col-4 text-center">
                                <div>Bekasi .../.../20..</div>
                                <div :class="soapData.employee_signature ? '' : 'mb-64'">Yang menyatakan</div>
                                <div :class="soapData.employee_signature ? '' : 'mb-64'">Nama Jelas Dan Tanda Tangan
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
    /* height: 29.7cm !important; */
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
</style>
