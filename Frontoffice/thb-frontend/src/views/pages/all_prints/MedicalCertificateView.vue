<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <!-- <b-button @click="closeWindow" v-if="print" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" v-if="print" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button> -->
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-36" style="position: relative;">
                    <div class="row align-items-center">
                        <div class="col-2">
                            <img src="../../../@core/assets/images/logo-thb.jpg" width="200%" alt="rs-logo">
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
                    <!-- <div class="row align-items-center">
                        <div class="col-2">
                            <img src="../../../@core/assets/images/logo-thb-small.png" width="60%" alt="rs-logo"> <br>
                        </div>
                        <div class="col text-center text-bold-28">
                            <p class="text-bold-14">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                        </div>
                        <div class="col-2">
                            <img src="../../../@core/assets/images/lafki-logo.png" width="60%" alt="lafki-logo"> <br>
                        </div>
                    </div> -->
                    <div class="text-uppercase text-center mt-24">
                        <div class="text-bold-24 with-line">SURAT KETERANGAN SAKIT</div>
                        <div class="text-regular-24">MEDICAL CERTIFICATE</div>
                        <div class="text-regular-24">{{letterData.no}}</div>
                    </div>
                    <div class="text-regular-18 mt-32" style="line-height: 140%;">
                        <div class="underline">Yang bertanda tangan dibawah ini, <span>{{ regData.doctor_in_charge_name }}</span> menerangkan bahwa:</div>
                        <div class="mt-8 italic">The undersigned, <span>{{ regData.doctor_in_charge_name }}</span> hereby certifies that:</div>

                        <table class="mt-24">
                            <tr>
                                <td width="20%" class="pl-0 underline" style="vertical-align: top;">Nama</td>
                                <td width="5%" class="text-right">:&nbsp;&nbsp;&nbsp;</td>
                                <td>{{ regData.fullname }}</td>
                            </tr>
                            <tr>
                                <td class="pl-0 italic" style="vertical-align: top;">Name</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="pl-0 underline" style="vertical-align: top;">Tanggal Lahir</td>
                                <td class="text-right">:&nbsp;&nbsp;&nbsp;</td>
                                <td>{{ regData.birth_date | convert_date }}</td>
                            </tr>
                            <tr>
                                <td class="pl-0 italic" style="vertical-align: top;">Date of Birth</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="pl-0 underline" style="vertical-align: top;">Jenis Kelamin</td>
                                <td class="text-right">:&nbsp;&nbsp;&nbsp;</td>
                                <td>{{ regData.sex_name }}
                                <i>{{ regData.sex_name === 'Perempuan' ?'(Female)': regData.sex_name === 'Laki-laki' ? '(Male)': regData.sex_name }}</i>
                                 </td>
                            </tr>
                            <tr>
                                <td class="pl-0 italic" style="vertical-align: top;">Sex</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="pl-0 underline" style="vertical-align: top;">Alamat</td>
                                <td class="text-right" style="vertical-align: top;">:&nbsp;&nbsp;&nbsp;</td>
                                <td>{{ regData.address }}</td>
                            </tr>
                            <tr>
                                <td class="pl-0 italic" style="vertical-align: top;">Address</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <!-- <tr>
                                <td class="pl-0" style="vertical-align: top;">Diagnosa</td>
                                <td class="text-right" style="vertical-align: top;">:</td>
                                <td>
                                    <p v-for="(list, index) in soapData.icd10">
                                        {{ list.code }} - {{ list.name }}
                                    </p>
                                </td>
                            </tr> -->
                        </table>
                        <div class="mt-24 underline">Perlu istirahat selama {{days}} hari, mulai dari tanggal {{letterData.start | convert_date}} s/d {{letterData.end | convert_date}}, karena sakit. Atas perhatian dan kerjasama Bapak/Ibu, kami mengucapkan terima kasih.</div>
                        <div class="mt-8 italic">Needs to rest for {{days}} days, from {{letterData.start | convert_date}} to {{letterData.end | convert_date}}, due to illness. We thank you for your attention and cooperation.</div>

                        <div class="d-flex justify-content-end w-100 mt-64">
                            <div class="col-4 text-center">
                                <div :class="regData.employee_signature ? '' : 'mb-64'">Bekasi, {{letterData.date | convert_date}}</div>

                                <template v-if="regData.employee_signature">
                                    <b-img :src="urlbe+regData.employee_signature" style="width: 120px;"/>
                                </template>
                                <div>{{ regData.doctor_in_charge_name }}</div>
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
            print:true,
            days:1,
            soap_id:''
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
        },
        convert_date_en(value){
            return moment(new Date(value)).locale('en').format('LL')
        }
    },
    methods: {
        getData (id) {
            this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]
            })
        },
        getSoap (id) {
			this.$store.dispatch('soap/getBySoapId', id).then(data => {
                this.soapData = data[0]
                console.log("soapData", this.soapData);
            })
        },
        geLetter (id) {
			this.$store.dispatch('letter/getById', id).then(data => {
                this.letterData = data[0]
                const dateB = moment(data[0].end);
                const dateC = moment(data[0].start);
                this.days = dateB.diff(dateC, 'days')+1
            })
        },
        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                this.printCanvas();
            }
        },
        printCanvas() {
            this.print = false
            setTimeout(() => {
                const canvas = this.$refs.canvas;
                const originalContents = document.body.innerHTML;
                document.body.innerHTML = canvas.innerHTML;
                window.print();
                document.body.innerHTML = originalContents;
            }, 5000);
            // setTimeout(() => {
            //     location.reload();
            // }, 1500);
        },
        closeWindow() {
            window.close()
        },
    },
    mounted() {
        this.printCanvas()
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
}

td {
    padding: 2px 0;
}

p {
    margin: 0;
    padding: 0;
}

.main-content {
}

.canvas-wrapper {
    display: flex;
    justify-content: center;
}

#canvas {
    /* width: 210mm; */
    width: 21.2cm !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}

.with-line {
    text-decoration: underline;
    text-decoration-thickness: 2px;
    text-underline-offset: 8px;
}
.underline{
    text-decoration: underline;;
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

.italic{
    font-style: italic;
}

.canvas-footer {
    position: absolute;
    bottom: 0;
    /* right: 0; */
}
</style>
