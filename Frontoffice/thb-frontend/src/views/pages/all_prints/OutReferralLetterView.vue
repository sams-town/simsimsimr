<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper mt-32">
                <div id="canvas" ref="canvas" class="mt-16 w-100" style="position: relative;">
                    <div class="row mx-0 align-items-center justify-content-between pb-40" style="border-bottom: 2px solid black;">
                        <div class="text-center">
                            <img src="../../../@core/assets/images/logo-thb.jpg" width="200px" alt="rs-logo"> <br>
                        </div>
                        <div class="">
                            <div class="d-flex">
                                <div class="py-1 px-8 mr-24 d-flex align-items-center tex-center" style="border: 4px solid black;">KPWT/026/SKR/22/Rev.1</div>
                                <img src="../../../@core/assets/images/lafki-logo.png" width="87px" alt="lafki-logo"> <br>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-32">
                        <div class="text-uppercase text-bold-24 with-line">SURAT KONSULTASI / RUJUKAN</div>
                    </div>
                    <div class="mt-32">
                        <div>Yth dr.</div>
                        <div>Di </div>
                    </div>
                    <div class="text-regular-18 mt-32" style="line-height: 140%;">
                        <div>
                            <div>Mohon pemeriksaan / pengobatan lebih lanjut atas pasien :</div>
                            <table class="mt-16 w-100">
                                <tr>
                                    <td class="col-3">Nama</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ regData.fullname }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Tempat / Tanggal Lahir</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ localDate(regData.birth_date) }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Nomor Rekam Medis</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ regData.norm }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3 align-top">Diagnosa</td>
                                    <td class="col-1 text-right align-top">:</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="col-3 align-top">Terapi / tindakan yang telah diberikan</td>
                                    <td class="col-1 text-right align-top">:</td>
                                    <td></td>
                                </tr>

                            </table>
                        </div>

                        <div class="d-flex justify-content-end w-100 mt-64">
                            <div class="col-4 text-center">
                                <div>Bekasi, {{ today }} </div>
                                <div :class="soapData.employee_signature ? '' : 'mb-64'">Hormat Kami,</div>
                                <template v-if="soapData.employee_signature">
                                    <b-img :src="urlbe+soapData.employee_signature" style="width: 120px;"/>
                                </template>
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
            urlbe:process.env.VUE_APP_IMAGE,
            regData:{},
            soapData:{},
            letterData:{},
            days:1,
            soap_id:'',
            today: moment(new Date()).locale('id').format('LL')

        }
    },
    computed: {
        ...mapState({
            items: state => state.soap.items,
        })
    },
    filters:{
        convert_date(value) {
            return moment(new Date(value)).locale('id').format('LL')
        }
    },
    methods: {
        getData (id) {
            this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]
                // console.log('regdata:', this.regData);
                this.$store.dispatch('soap/getByNorm', this.regData.norm).then(data => {
                    this.soapData = data[0]
                    // console.log('this.soapData', this.soapData)
                })
            })
        },
        // getSoap (id) {
		// 	this.$store.dispatch('soap/getBySoapId', id).then(data => {
        //         this.soapData = data[0]
        //     })
        // },
        // geLetter (id) {
		// 	this.$store.dispatch('letter/getById', id).then(data => {
        //         this.letterData = data[0]
        //         const dateB = moment(data[0].end);
        //         const dateC = moment(data[0].start);
        //         this.days = dateB.diff(dateC, 'days')+1
        //     })
        // },
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
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },

        // parsed_age(input_date) {
        //     const birthDate = moment(input_date, 'YYYY-MM-DD');
        //     const currentDate = moment();

        //     const years = currentDate.diff(birthDate, 'years');
        //     birthDate.add(years, 'years');
        //     const months = currentDate.diff(birthDate, 'months');
        //     birthDate.add(months, 'months');
        //     const days = currentDate.diff(birthDate, 'days');

        //     return `${years} th ${months} bln ${days} hr`;
        // },

    },
    mounted() {
        this.getData(this.$route.params.reg_des_id)
        // this.getSoap(this.$route.params.soap_id)
        // this.geLetter(this.$route.params.letter_id)

        window.addEventListener('keydown', this.handleKeyPress);
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
};

</script>
<style>
@import './../../../@core/css/main/main.min.css';


@media print {
    @page {
        size: A4;
        margin: 16mm;
        /* size: 210mm 125mm; */
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
}

/* #canvas {
    width: 21.2cm !important;
    height: 29.7cm !important;
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
    /* right: 0; */
}
</style>
