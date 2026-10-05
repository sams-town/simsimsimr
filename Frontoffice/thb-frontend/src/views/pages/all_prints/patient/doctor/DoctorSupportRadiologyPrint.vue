<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper mt-16">
                <div id="canvas" ref="canvas" class="w-100" style="position: relative;">
                    <div class="row mx-0 align-items-center justify-content-between w-100 pb-32" style="border-bottom: 2px solid black">
                        <!-- <div class="col-2">
                            <img src="../../../../../@core/assets/images/logo-thb-small.png" width="60%" alt="rs-logo"> <br>
                        </div> -->
                        <div class="d-flex">
                            <img src="../../../../../@core/assets/images/logo-thb-small.png" width="80px" alt="rs-logo"> <br>
                            <div class="text-regular-14 pl-16">
                                <p class="text-bold-18">RS Taman Harapan Baru</p>
                                <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                                <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                            </div>
                        </div>
                        <div class="text-bold-24">
                            <div>PERMINTAAN</div>
                            <div>RADIOLOGI KLINIK</div>
                        </div>
                    </div>
                    <div class="text-regular-16 mt-24" style="line-height: 140%;">
                        <div class="row">
                            <div class="col-8">
                                <table class="mt-16">
                                    <tr>
                                        <td class="col-3" style="vertical-align: top;">Penunjang</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ patient.health_facility_item_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="col-3" style="vertical-align: top;">Nama</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ patient.alias_name }} {{ patient.name }} ({{ patient.sex_name }})</td>
                                    </tr>
                                    <tr>
                                        <td class="col-3" style="vertical-align: top;">Tanggal Lahir</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ localDate(patient.birth_date) }} ({{ parsed_age(patient.birth_date) }})</td>
                                    </tr>
                                    <tr>
                                        <td class="col-3" style="vertical-align: top;">No SEP</td>
                                        <td class="col-1 text-right">:</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="col-3" style="vertical-align: top;">Nomor RM</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ patient.norm }}</td>
                                    </tr>

                                    <tr>
                                        <td class="col-3" style="vertical-align: top;">Diagnosa</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>
                                            <template v-for="(item, index) in result.icd10">
                                                <div v-if="index == 0" :key="index">
                                                    {{ item.code }} {{ item.name }}
                                                </div>
                                            </template>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-3" style="vertical-align: top;">Anggota/Klg</td>
                                        <td class="col-1 text-right">:</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="col-3" style="vertical-align: top;">Tanggal Permintaan</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ localDate(radiology_request_list.date_request) }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-4">
                                <div class="col text-bold-18 text-center text-uppercase p-24" style="border: 4px solid black">{{ patient.health_facility_item_name }}</div>
                            </div>
                        </div>
                        <div class="mt-32">
                            <div class="col">
                                <div>Pemeriksaan/tindakan yang diminta :</div>
                                <template v-if="radiology_request_list.radiology_request_item">
                                    <li v-for="(list, index) in radiology_request_list.radiology_request_item">{{ list.costing_name }}</li>
                                </template>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end w-100 mt-20">
                            <div class="col-5 text-center">
                                <div>Bekasi, {{today}}</div>
                                <div :class="result.employee_signature ? '' : 'mb-64'" >Dokter yang meminta,</div>
                                <template v-if="result.employee_signature">
                                    <b-img :src="urlbe+result.employee_signature" style="width: 120px;"/>
                                </template>
                                <div>( {{ this.result.employee_doctor_name }} )</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
import router from '@/router/index.js';
import moment from 'moment';

export default {
    data() {
        return {
            urlbe:process.env.VUE_APP_IMAGE,
            result: {},
            patient: {},
            today: moment(new Date()).locale('id').format('LL'),
            radiology_request_list: {},
        }
    },
    methods: {
        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                this.printCanvas();
            }
        },
        closeWindow() {
            window.close()
        },
        printCanvas() {
            const canvas = this.$refs.canvas;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = canvas.innerHTML;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload()
        },
        getData (soap_id) {
            this.$store.dispatch('soap/getBySoapId', soap_id).then(data => {
                this.result = data[0]
                // console.log('soap:', this.result)

                if (this.result.doctor_in_charge_id) {
                    this.$store.dispatch('radiology/getOrderDetail', { soap_id: this.result.id, doctor_in_charge_id: this.result.doctor_in_charge_id }).then(response => {
                        this.radiology_request_list = response[0]
                        // console.log('radiology_request_list', this.radiology_request_list);
                    })
                }

                this.$store.dispatch('registration/getById', this.result.registration_destination_id).then(data => {
                    this.patient = data[0]
                    // console.log('this.patient', this.patient);
                })
            })
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
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
    },
    mounted() {
        window.addEventListener('keydown', this.handleKeyPress);
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
    created() {
        if (this.$route.params.soap_id) {
            // console.log('ada', this.$route.params.soap_id);
            this.getData(this.$route.params.soap_id)

        }
    },
};

</script>
<style>
@import './../../../../../@core/css/main/main.min.css';

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
    padding-bottom: 40px;
}
/* #canvas {
    width: 21.2cm !important;
    height: 100% !important;
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
}
</style>
