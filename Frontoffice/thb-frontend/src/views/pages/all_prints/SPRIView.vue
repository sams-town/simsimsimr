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
                    </div>
                    <div class="text-uppercase text-center mt-24">
                        <div class="text-bold-24 with-line">SURAT PERINTAH RAWAT INAP</div>
                        <div class="text-regular-24">{{letterData.no}}</div>
                    </div>
                    <div class="text-regular-18 mt-32" style="line-height: 140%;">
                        <div>Mohon rawat inap kepada pasien:</div>
                        <table class="mt-16">
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Nama Pasien</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.fullname }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(No.RM : {{inpatientData.responsible_person.norm}})</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Tanggal Lahir</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.birth_date | convert_date }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Unit Pengirim</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.health_facility_item_name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Indikasi Rawat Inap</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ inpatientData.indication }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Diagnosa</td>
                                <td class="col-1 text-right" style="vertical-align: top;">:</td>
                                <td>
                                    <p v-for="(list, index) in soapData.icd10">
                                        {{ list.code }} - {{ list.name }}
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">DPJP Utama</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ inpatientData.employee_doctor_name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Rencana Tindakan</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ inpatientData.plan }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Hasil yang Diharapkan</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ inpatientData.result }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Keadaan Umum Pasien</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ inpatientData.condition }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Ruang Perawatan Pasien</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ inpatientData.type_room_name }}</td>
                            </tr>
                        </table>
                        <div class="mt-16">Informasi biaya perawatan, biaya, dan hasil yang diharapkan dapat berubah selama rawat inap sesuai dengan perkembangan dan kondisi pasien.</div>
                        <div class="row justify-content-between align-items-end mt-24 text-left">
                            <div class="col-4 text-center">
                                <div>{{ inpatientData.responsible_person.responsible_name }}</div>
                                <div class="mt-72">( {{ inpatientData.responsible_person.name }} )</div>
                            </div>
                            <div class="col-4 text-center">
                                <div :class="soapData.employee_signature ? '' : 'mb-64'">Bekasi, {{inpatientData.date_added | convert_date}} Pukul : {{inpatientData.date_added | convert_time}}<br>Dokter Pengirim</div>
                                <template v-if="soapData.employee_signature">
                                    <b-img :src="urlbe+soapData.employee_signature" style="width: 120px;"/>
                                </template>
                                <div>{{ soapData.employee_doctor_name }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="text-uppercase text-center mt-24">
                        <div class="text-bold-24 with-line">SURAT PERYATAAN PEMBAYARAN</div>
                        <div class="text-regular-24">{{letterData.no}}</div>
                    </div>
                    <div class="text-regular-18 mt-32" style="line-height: 140%;">
                        <div>Saya yang bertanda tangan di bawah ini:</div>
                        <table>
                            <tr>
                                <td class="col-3 pl-0" style="width:40%;vertical-align: top;">Nama</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ inpatientData.responsible_person.name }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Tanggal Lahir</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ inpatientData.responsible_person.birth_date | convert_date }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Alamat</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ inpatientData.responsible_person.address_id }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Telepon</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ inpatientData.responsible_person.phone }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Hubungan terhadap pasien</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ inpatientData.responsible_person.responsible_name }}</td>
                            </tr>
                            <tr>
                                <td colspan="3"><div>Dengan ini menyatakan JAMINAN PEMBIAYAAN terhadap saya :</div></td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="width:40%;vertical-align: top;">Nama Pasien</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.fullname }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(No.RM : {{inpatientData.responsible_person.norm}})</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Tanggal Lahir</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.birth_date | convert_date }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Telepon</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.phone }}</td>
                            </tr>
                            <tr>
                                <td class="col-3 pl-0" style="vertical-align: top;">Alamat</td>
                                <td class="col-1 text-right">:</td>
                                <td>{{ regData.address }}</td>
                            </tr>
                        </table>
                        <div>Dari awal masuk rawat inap di RS Taman Harapan Baru dengan menggunakan / tidak menggunakan* Jaminan Kesehatan sebagai:</div>
                        <div v-if="inpatientData.insurance[0].insurance_id === 1">*Pasien Umum / Regular, saya bersedia membayar seluruh biaya rawat inap di kelas</div>
                        <div v-if="inpatientData.insurance[0].company_id === '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8'">*Peserta BPJS Kesehatan, KELAS {{ inpatientData.insurance[0].service_class_name }} <template v-if="inpatientData.insurance[0].is_up === 1 ">NAIK KELAS {{ inpatientData.insurance[0].service_class_name_right }}, sesuai ketentuan BPJS Kesehatan</template></div>
                        <div v-if="inpatientData.insurance[0].company_id != '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8' && inpatientData.insurance[0].insurance_id === 2 || inpatientData.insurance[0].insurance_id === 3">*Asuransi {{ inpatientData.insurance[0].company_name }}</div>
                        <div>Demikian surat peryataan ini saya buat dengan kesadaran penuh dan tanpa paksaan dari pihak manapun</div>
                        <div class="row justify-content-between align-items-end mt-24 text-left">
                            <div class="col-4 text-center">
                                <div>Yang menyatakan,</div>
                                <div class="mt-72">( {{ inpatientData.responsible_person.name }} )</div>
                            </div>
                            <div class="col-4 text-center">
                                <div>Keluarga/Saksi,</div>
                                <div class="mt-72">( {{ inpatientData.responsible_person.name }} )</div>
                            </div>
                            <div class="col-4 text-center">
                                <div :class="soapData.employee_signature ? '' : 'mb-64'">Bekasi, {{inpatientData.date_added | convert_date}} Pukul : {{inpatientData.date_updated | convert_time}}<br>Petugas Admission,</div>
                                <template v-if="soapData.employee_signature">
                                    <b-img :src="urlbe+soapData.employee_signature" style="width: 120px;"/>
                                </template>
                                <div>{{ userData.employee_name }}</div>
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
            userData: JSON.parse(localStorage.getItem('userData')),
            letterData:{},
            inpatientData:{},
            datenow:moment().format('DD-MM-YYYY hh:mm:ss'),
            timenow:moment().format('DD-MM-YYYY hh:mm:ss'),
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
        convert_time(value) {
            return moment(new Date(value)).locale('id').format('LT')
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
            })
        },
        getInpatient (id) {
			this.$store.dispatch('soap/getInpatientByRegId', id).then(data => {
                this.inpatientData = data[0]
                console.log(this.inpatientData)
                this.getSoap(data[0].soap_id)
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
    },
    mounted() {
        this.getInpatient(this.$route.params.id)
        this.getData(this.$route.params.id)
        // this.geLetter(this.$route.params.letter_id)
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
