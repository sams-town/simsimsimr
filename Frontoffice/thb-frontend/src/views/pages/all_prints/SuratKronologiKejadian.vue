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
                        <div class="text-bold-24 with-line">SURAT KRONOLOGI KEJADIAN</div>
                        <div class="text-regular-24">{{ letterData.no }}</div>
                    </div>
                    <div class="text-regular-18 mt-32" style="line-height: 140%;">
                        <!-- <div>Yang bertanda tangan dibawah ini:.., menerangkan dengan sesungguhnya,bahwa telah lahir
                            seorang bayi dengan jenis kelamin:.. <span>{{
                                regData.doctor_in_charge_name
                                }}</span>
                        </div> -->
                        <div class="mt-5"><b>Yang bertanda tangan dibawah ini</b>:</div>
                        <table class="mt-16">
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Nama</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.responsible_person_name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Tempat/Tanggal Lahir</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.responsible_person_address }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Hubungan Dengan Pasien</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.responsible_person_responsible_name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Nomor Telp/HP</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.responsible_person_phone }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Alamat</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.responsible_person_address }}</td>
                            </tr>
                        </table>
                        <div class="mt-5"><b>Adalah penanggung jawab pasien atas nama</b>:</div>
                        <table class="mt-16">
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Nama Pasien</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.fullname }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Tempat/Tanggal Lahir</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.birth_place }}, {{ convert_date(regData.birth_date) }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Alamat</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.address }}</td>
                            </tr>
                        </table>
                        <div class="mt-5 mb-400"><b>Kronologis Kejadian</b>:</div>

                        <div class="d-flex justify-content-end w-100 mt-64">
                            <div class="col-10 text-center">
                                <div>Mengetahui</div>
                                <div class="mb-120">Petugas rumah sakit</div>
                                {{ ('(.................................................)') }}
                                <div class="mb-120">tanda tangan dan nama jelas
                                </div>
                                <!-- <template v-if="soapData.employee_signature">
                                    <b-img :src="urlbe + soapData.employee_signature" style="width: 120px;" />
                                </template> -->
                                <!-- <div>{{ soapData.employee_doctor_name }}</div> -->
                            </div>
                            <div class="col-5 text-center">
                                <div>Bekasi, {{ convert_date(regData.birth_date, 'L') }}</div>
                                <div class="mb-120">Yang membuat pernyataan</div>
                                <!-- <div
                                    style="display: inline-block; border: 1px solid black; padding: 5px 10px;">
                                    Materai 10000
                                </div> -->
                                <!-- <div> {{ ('Materai 10000') }}</div> -->
                                {{ ('(.................................................)') }}
                                <div class="mb-120">tanda tangan dan nama jelas
                                </div>
                                <!-- <template v-if="soapData.employee_signature">
                                    <b-img :src="urlbe + soapData.employee_signature" style="width: 120px;" />
                                </template> -->
                                <!-- <div>{{ soapData.employee_doctor_name }}</div> -->
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
        // convert_date(value) {
        //     return moment(new Date(value)).locale('id').format('LL')
        // }
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
        convert_date(value, format = 'LL', addedDays = 0) {
            if (addedDays !== 0) {
                return moment(new Date(value)).locale('id').add(addedDays, 'days').format(format);
            } else {
                return moment(new Date(value)).locale('id').format(format);
            }
        }
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

.mb-400{
    margin-bottom:400px;
}

.canvas-footer {
    position: absolute;
    bottom: 0;
    /* right: 0; */
}
</style>
