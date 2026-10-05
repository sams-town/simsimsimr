<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="p-4" style="border: 2px solid black;">
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
                                    <td><img src="../../../@core/assets/images/lafki-logo.png" width="85%"
                                            alt="lafki-logo" align="right">
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
                            <div class="text-bold-24 ">SURAT PERJANJIAN KUNJUNGAN</div>
                        </div>
                        <!-- <div class="mt-16">
                            <table class="mt-16">
                                <tr>
                                    <td class="col-3 pl-0" style="vertical-align: top; white-space: nowrap;">Nama</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{regData.alias_name}}. {{ regData.fullname }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3 pl-0" style="vertical-align: top;">No. RM</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ regData.norm }}</td>
                                </tr>
                                <tr>
                                    <td class="col-3 pl-0" style="vertical-align: top; white-space: nowrap;">Tanggal Lahir</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ localDate(regData.birth_date) }}</td>
                                </tr>
                            </table>
                        </div> -->
                        <div class="text-regular-18 mt-20" style="line-height: 140%;">
                            <table class="mt-16">
                                <tr>
                                    <td class="pl-0" style="vertical-align: top; width:220px; white-space: nowrap;">NAMA</td>
                                    <td class="text-right">:&nbsp;&nbsp;</td>
                                    <td>{{regData.alias_name}}. {{ regData.fullname }}</td>
                                </tr>
                                <tr>
                                    <td class="pl-0" style="vertical-align: top;">NO. RM</td>
                                    <td class="text-right">:&nbsp;&nbsp;</td>
                                    <td>{{ regData.norm }}</td>
                                </tr>
                                <tr>
                                    <td class="pl-0" style="vertical-align: top; white-space: nowrap;">TANGGAL LAHIR</td>
                                    <td class="text-right">:&nbsp;&nbsp;</td>
                                    <td>{{ localDate(regData.birth_date) }} / {{ parsedAge(regData.birth_date) }}</td>
                                </tr>
                                <tr>
                                    <td class="pl-0" style="vertical-align: top; white-space: nowrap;">JENIS KELAMIN</td>
                                    <td class="text-right">:&nbsp;&nbsp;</td>
                                    <td>{{ regData.sex_name }}</td>
                                </tr>
                            </table>
                            <table class="mt-16">
                                <tr>
                                    <td class="pl-0" style="vertical-align: top; width:220px;">DPJP UTAMA</td>
                                    <td class="text-right">:&nbsp;&nbsp;</td>
                                    <td>{{controlBPJSData.doctor_name}}</td>
                                </tr>
                                <tr>
                                    <td class="pl-0" style="vertical-align: top; white-space: nowrap;">TANGGAL
                                        KONTROL</td>
                                    <td class="text-right">:&nbsp;&nbsp;</td>
                                    <td style="white-space: nowrap;">{{ localDate(controlBPJSData.tglRencanaKontrol) }}</td>
                                </tr>
                                <tr>
                                    <td class="pl-0" style="vertical-align: top;">KLINIK TUJUAN</td>
                                    <td class="text-right">:&nbsp;&nbsp;</td>
                                    <td>
                                        <div class="lh-110">{{ controlBPJSData.health_facility_item_name }}</div>
                                    </td>
                                </tr>
                                <!-- <tr>
                                    <td class="col-3 pl-0" style="vertical-align: top;">TANGGAL</td>
                                    <td class="col-1 text-right">:</td>
                                    <td>{{ today }}</td>
                                </tr>

                                <tr>
                                    <td colspan="3" style="padding-top: 1rem;"></td>
                                </tr>
                                <tr>
                                    <td class="col-3 pl-0" style="vertical-align: top;">DPJP II</td>
                                    <td class="col-1 text-right">:</td>
                                    <td >
                                        <div class="d-flex py-1">
                                            <div>
                                                <div>{{ regData.doctor_in_charge_2_name || '........' }} KONTROL / TIDAK KONTROL</div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-3 pl-0" style="vertical-align: top;">DPJP III</td>
                                    <td class="col-1 text-right">:</td>
                                    <td >
                                        <div class="d-flex py-1">
                                            <div>
                                                <div>{{ regData.doctor_in_charge_3_name || '.................' }} KONTROL / TIDAK KONTROL</div>
                                            </div>
                                        </div>
                                    </td>
                                </tr> -->
                            </table>
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
                days: 1,
                today: moment(new Date()).locale('id').format('LL'),

                reg_des_id:'',
                regData: {},
                resumeData: {},
                employeeData:{},
                controlBPJSData:{},
            }
        },
        computed: {},
        filters: {
            convert_date(value) {
                return moment(new Date(value)).locale('id').format('LL')
            }
        },
        methods: {
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
            getData (regdesID) {
                this.$store.dispatch('registration/getById', regdesID).then(data => {
                    this.regData = data[0]
                    // console.log('Data: ', this.regData);
                })
                this.getControlBPJS(regdesID)
            },
            // getResume (resume_id) {
            //     this.$store.dispatch('resume/getDetail', resume_id).then(data => {
            //         this.resumeData = data[0]

            //         this.$store.dispatch('employee/getDetailById', this.resumeData.doctor_in_charge_id).then(employee => {
            //             this.employeeData = employee[0]
            //             // console.log('this.employeeData: ', this.employeeData);
            //         })


            //     })
            // },
            parsedAge(date) {
                if (!date || date === "0000-00-00") {
                    return "-";
                }

                const parsed = moment(date);
                if (!parsed.isValid()) {
                    return "";
                }

                const birthDate = moment(date, 'YYYY-MM-DD');
                const currentDate = moment();

                const years = currentDate.diff(birthDate, 'years');
                birthDate.add(years, 'years');

                return `${years} th`;
            },
            async getControlBPJS(regDesID){
                try {
                    await this.$store.dispatch('bed/getControlBPJSByRegistrationDesId', regDesID).then((response) => {
                        console.log('bed/getControlBPJSByRegistrationDesId', response.data.data)
                        if(response.data.data.length > 0){
                            this.controlBPJSData = response.data.data[0]
                        }
                    }, error => {
                        console.error('Error while bed/getControlBPJSByRegistrationDesId at: ', error);
                    })
                } catch (error) {
                    console.error('Error catch while GET bed/getControlBPJSByRegistrationDesId at: ', error);
                }
            }
        },
        mounted() {
            window.addEventListener('keydown', this.handleKeyPress);
        },
        created() {
            if (this.$route.params.reg_des_id) {
                this.reg_des_id = this.$route.params.reg_des_id
                this.getData(this.$route.params.reg_des_id)
            }

            // if (this.$route.params.resume_id) {
            //     this.getResume(this.$route.params.resume_id)
            // }
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