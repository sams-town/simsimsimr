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
                            <img src="../../../../../@core/assets/images/logo-thb.jpg" width="300%" alt="rs-logo">
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
                                <td><img src="../../../../../@core/assets/images/lafki-logo.png" width="85%" alt="lafki-logo"
                                        align="right">
                                </td>
                            </tr>
                        </div>
                        <div class="col-2">
                            <tr>
                                <td><img src="../../../../../@core/assets/images/apexa.png" width="80%" alt="-logo"
                                        align="right">
                                </td>
                            </tr>
                        </div>
                    </div>
                    <div class="text-uppercase text-center mt-5">
                        <div class="text-bold-24 with-line">SURAT KETERANGAN BEBAS NARKOBA</div>
                        <div class="text-regular-24">NOMOR: {{regData.norm}}/MCU-KCI/VI/2025</div>
                    </div>
                    <div class="text-regular-18 mt-32" style="line-height: 140%;">
                        <div>Yang bertanda tangan dibawah ini menerangkan bahwa : </div>
                        <table class="mt-16"  style="margin-left: 20px;">
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Nama</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.fullname }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Usia</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{parsed_age(regData.birth_date)}}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">No. Laboratorium</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ orderData.no_request }}</td>
                            </tr>
                        </table>
                        <div class="mt-5">Berdasarkan hasil pemeriksaan tes narkoba yang dilakukan pada tanggal {{ localDate(orderData.date_result) }}, didapatkan
                            hasil.
                        </div>
                        <div class="px-3 mt-24">
                            <div class="text-right">
                                <i class="text-regular-12"><u>PJ Laboratorium : Novia Liyanti</u></i>
                            </div>
                            <b-table bordered responsive head-variant="light" hover small :fields="resultFields"
                                :items="result" tbody-tr-class="text-medium-14" class="mt-12 text-center">
                            </b-table>
                        </div>
                        <div class="mt-5">Demikianlah surat keterangan ini dibuat dengan Sebenar-benarnya, untuk dipergunakan
                            sebagaimana mestinya.
                        </div>
                        <div class="d-flex justify-content-start w-100 mt-64">
                            <div>
                                <div>Bekasi, {{ localDate(orderData.date_result) }}</div>
                                <div></div>
                                <!-- <div :class="soapData.employee_signature ? '' : 'mb-64'">Nama Jelas Dan Tanda Tangan
                                </div> -->
                                <template>
                                    <b-img :src="urlbe + 'assets/document/ttd/ttd-dr-andini.png'" style="width: 120px;" />
                                </template>
                                <div>dr. Andini Triasti Siregar, M.ked, Sp.PK<br>Dokter PJ Laboratorium</div>
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
            orderData:{},
            days: 1,
            soap_id: '',
            result:[],
            resultFields: [
                { key: 'Parameter', label: 'Pemeriksaan' },
                { key: 'hasil', label: 'Hasil' },
                // { key: 'flag', label: 'Flag' },
                { key: 'n_rujukan', label: 'Nilai Rujukan' },
                // { key: 'satuan', label: 'Satuan' },
                // { key: 'metoda', label: 'Metoda' },
            ],
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
        getData(id) {
            this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]
                this.$store.dispatch('laboratory/getByRegistrationId', data[0].registration_id).then(data => {
                    this.orderData = data[0]
                    this.$store.dispatch('laboratory/getResultById', data[0].id).then(result => {
                        let result_detail = result[0].result_detail
                        this.result = result_detail.filter((item)=> item.ParameterID == "9208")
                    })
                })
            })
        },
        getSoap(id) {
            this.$store.dispatch('soap/getBySoapId', id).then(data => {
                this.getData(data[0].registration_destination_id)
                this.soapData = data[0]
                console.log(data[0])
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
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
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
        if (this.$route.params.soap_id) {
            this.getSoap(this.$route.params.soap_id)
        }
        // this.geLetter(this.$route.params.letter_id)
        window.addEventListener('keydown', this.handleKeyPress);
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
};

</script>
<style>
@import '../../../../../@core/css/main/main.min.css';
@media print {
    @page {
        size: A4;
        margin: 16mm;
    }
}
/* table,
th,
td {
    border: 1px solid black;
} */
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

/* #canvas {
    width: 210mm;
    width: 21.2cm !important;
    height: 29.7cm !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
} */

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

.with-line {
    text-decoration: underline;
    text-decoration-thickness: 2px;
    text-underline-offset: 8px;
}
</style>
