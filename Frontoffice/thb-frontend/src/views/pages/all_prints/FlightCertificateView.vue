<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper mt-32">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="row align-items-center pb-40" style="border-bottom: 2px solid black;">
                        <div class="col-2 text-center">
                            <img src="../../../@core/assets/images/logo-thb-small.png" width="60%" alt="rs-logo"> <br>
                        </div>
                        <div class="col text-center">
                            <p class="text-bold-18">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                        </div>
                        <div class="col-2 text-center">
                            <!-- <img src="../../../@core/assets/images/lafki-logo.png" width="60%" alt="lafki-logo"> <br> -->
                        </div>
                    </div>
                    <div class="mt-32">
                        <table>
                            <tr>
                                <td>No</td>
                                <td class="px-16">:</td>
                                <td>{{letterData.no}}</td>
                            </tr>
                            <tr>
                                <td>Hal</td>
                                <td class="px-16">:</td>
                                <td>Surat Izin Terbang</td>
                            </tr>
                        </table>
                    </div>
                    <div class="text-uppercase text-bold-24 text-center mt-24">
                        <div class="with-line">SURAT KETERANGAN</div>
                        <div >HEALTH CERTIFICATE</div>
                    </div>
                    <div class="text-regular-18 mt-32" style="line-height: 140%;">
                        <div>
                            <div>Yang Bertanda tangan dibawah ini :</div>
                            <table class="mt-16 w-100">
                                <tr>
                                    <td class="col-3">Nama / Name</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ soapData.employee_doctor_name }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Jabatan / Profesion</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ regData.health_facility_item_id === 'cca59ec4-d77a-45fd-b51f-aae069da22ea' ? 'Dokter Umum' : 'Dokter Spesialist' }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Instansi / Office</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>RS Taman Harapan Baru</td>
                                </tr>
                            </table>
                        </div>
                        <div class="mt-16">
                            <div>Dengan ini menyatakn bahwa :</div>
                            <table class="mt-16 w-100">
                                <tr>
                                    <td class="col-3">Nama / Name</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ regData.fullname }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Tempat tanggal lahir / Date of Birth</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ regData.birth_date }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3">Umur / Age</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ parsed_age(regData.birth_date) }}</td>
                                </tr>
                                <!-- <tr>
                                    <td class="col-3">Usia Kehamilan / Gestational Age</td>
                                    <td class="col-1 text-right">:</td>
                                    <td></td>
                                </tr> -->
                                <tr>
                                    <td class="col-3 align-top">Alamat / Address</td>
                                    <td class="col-1 text-right align-top">:</td>
                                    <td>{{ regData.address }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="mt-16">Dalam kondisi sehat dan dapat melakukan perjalanan dengan transportsi udara / I certify that {{ regData.sex == 1 ? 'he' : 'she' }} is fit to travel by plane and follow the procedure.</div>
                        <div>Demikian surat keterangan ini dibuat agar dapat dipergunakan sebagai mana mestinya. Terima Kasih</div>

                        <div class="d-flex justify-content-end w-100 mt-64">
                            <div class="col-4 text-center">
                                <!-- <div>Bekasi, {{letterData.date | convert_date}}</div> -->
                                <div :class="soapData.employee_signature ? '' : 'mb-64'">Bekasi, {{today}}</div>
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
            employeeData:{},
            letterData:{},
            days:1,
            soap_id:'',
            today: moment(new Date()).locale('id').format('LL')
        }
    },
    // computed: {
    //     ...mapState({
    //         items: state => state.soap.items,
    //     })
    // },
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
                    console.log('this.soapData', this.soapData)

                    //getEmployee
                    this.$store.dispatch('employee/getDetailById', this.soapData.doctor_in_charge_id).then(employee => {
                        this.employeeData = employee[0]
                        // console.log('this.employeeData: ', this.employeeData);
                    })
                })
            })
        },
        // getSoap (id) {
		// 	this.$store.dispatch('soap/getByRegId', id).then(data => {
        //         this.soapData = data[0]
        //         console.log('this.soapData', this.soapData)
        //     })
        // },
        geLetter (id) {
			this.$store.dispatch('letter/getById', id).then(data => {
                this.letterData = data[0]
                const dateB = moment(data[0].end);
                const dateC = moment(data[0].start);
                this.days = dateB.diff(dateC, 'days')+1
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

    },
    mounted() {
        this.getData(this.$route.params.reg_des_id)
        // this.getSoap(this.$route.params.soap_id)
        this.geLetter(this.$route.params.letter_id)
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
