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
                        <div class="text-bold-24 with-line">SURAT KETERANGAN</div>
                        <div class="text-regular-24">Nomor: {{ letterData?.no }}</div>
                    </div>
                    <div class="text-regular-18 mt-32" style="line-height: 140%;">
                        <div>Yang bertandatangan di bawah ini,
                            Dokter RS Taman Harapan Baru Bekasi,
                            dengan ini menerangkan bahwa :
                        </div>
                        <table class="mt-16 ml-3">
                            <tr>
                                <td style="width: 200px;" class="align-top pl-0">Nama</td>
                                <td class="px-12 text-center">:</td>
                                <td>{{ regData.name }}</td>
                            </tr>
                            <tr>
                                <td class="align-top pl-0">Jenis Kelamin</td>
                                <td class="px-12 text-center">:</td>
                                <td>{{ regData.sex_name }}</td>
                            </tr>
                            <tr>
                                <td class="align-top pl-0">Tempat, Tanggal Lahir/ Umur</td>
                                <td class="px-12 text-center">:</td>
                                <td>{{regData.birth_place}}, {{regData.birth_date}}/ {{parsed_age(regData.birth_date)}}</td>
                            </tr>
                            <tr>
                                <td class="align-top pl-0">Alamat</td>
                                <td class="px-12 text-center">:</td>
                                <td>{{ regData?.address }}</td>
                            </tr>
                        </table>
                        <div class="mt-4">Adalah benar pasien RS Taman Harapan Baru Bekasi.
                            Setelah dilakukan anamnesis, pemeriksaan fisik dan pemeriksaan penunjang di bagian paru
                            <strong>INI DINYATAKAN SEMBUH DARI TB (TUBERCULOSIS) PARU.</strong>
                        </div>
                        <div class="mt-2">Demikian surat keterangan ini dibuat agar dapat dipergunakan sebagai mana mestinya.</div>
                        <div class="mt-5">NB :</div>
                        <div class="mt-80">Saran:</div>
                        <div class="d-flex justify-content-end w-100 mt-80">
                            <div>
                                <div>Bekasi, {{ regData.date_added | convert_dateStr}}</div>
                                <div class="text-center" :class="regData?.doctor_signature ? '' : 'mb-64'">Dokter Pemeriksa</div>
                                <!-- <div :class="regData.doctor_signature ? '' : 'mb-64'">Nama Jelas Dan Tanda Tangan
                                </div> -->
                                <template v-if="regData?.doctor_signature">
                                    <b-img class="mx-auto d-block":src="urlbe + regData?.doctor_signature" style="width: 120px;" />
                                </template>
                                <div class="text-center">{{ regData?.doctor_name }}</div>
                                <div>Spesialis Paru (Pulmonologist)</div>
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
            soap_id: '',
            id: '',
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
        },
        convert_time(value) {
            return moment(new Date(value)).locale('id').format('LT')
        },
        convert_dateStr(value) {
            if(value) return moment(value).locale('id').format('LL')
        },
        convert_timeStr(value) {
            if(value) return moment(value, 'HH:mm:ss').locale('id').format('HH.mm');
        },
    },
    methods: {
        getData (soap_id) {
            this.$store.dispatch('soap/getBySoapId', soap_id).then(data => {
                this.soapData = data[0]

                this.$store.dispatch('registration/getById', this.soapData.registration_destination_id).then(data => {
                    this.regData = data[0]
                    // console.log('regData', this.regData);
                })
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

        //Helper Method
        parsed_age(input_date) {
            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            // return `${years} th ${months} bln ${days} hr`;
            return `${years} tahun`;
        },
    },
    mounted() {
        if (this.$route.params.soap_id) {
            this.getData(this.$route.params.soap_id)
        }
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
</style>
