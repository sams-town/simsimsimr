<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                <div class="row align-items-center mb-4">
                    <div class="col-2">
                    <img src="../../../../@core/assets/images/logo-thb.jpg" width="200%" alt="rs-logo">
                    </div>
                    <div class="col text-center text-bold-28"></div>
                    <div class="col-2">
                    <img src="../../../../@core/assets/images/lafki-logo.png" width="85%" alt="lafki-logo" class="float-right">
                    </div>
                    <div class="col-2">
                    <img src="../../../../@core/assets/images/apexa.png" width="80%" alt="-logo" class="float-right">
                    </div>
                </div>
                    <div class="mt-20">
                        <div class=" text-center mt-24">
                            <div class="text-uppercase text-bold-16 with-line">EXPERTISE RADIOLOGI</div>
                            <div class="text-regular-16 italic">Radiology Report</div>
                        </div>
                        <!-- <p class="text-center text-bold-16">EXPERTISE RADIOLOGI</p> -->
                        <div class="row mx-0 mt-28">
                            <div class="col-6">
                                <div class="text-regular-14 row mb-2 align-items-start">
                                    <div class="col-3">
                                    <span class="underline">No. Reg / RM</span><br>
                                    <span class="italic">Reg / RM No.</span>
                                    </div>
                                    <div class="col-auto text-right pr-0" style="width:10px;">:</div>
                                    <div class="col">{{regData.code}} / {{regData.norm}}</div>
                                </div>
                                <div class="text-regular-14 row mb-2 align-items-start">
                                    <div class="col-3">
                                    <span class="underline">Nama Pasien</span><br>
                                    <span class="italic">Patient Name</span>
                                    </div>
                                    <div class="col-auto text-right pr-0" style="width:10px;">:</div>
                                    <div class="col">{{ regData.fullname }}</div>
                                </div>
                                <div class="text-regular-14 row mb-2 align-items-start">
                                    <div class="col-3">
                                    <span class="underline">Tanggal Order</span><br>
                                    <span class="italic">Order Date</span>
                                    </div>
                                    <div class="col-auto text-right pr-0" style="width:10px;">:</div>
                                    <div class="col">
                                        {{ orderData.date_request ? localDate(orderData.date_request) : '' }}
                                        <template v-if="orderData.date_request">
                                            <template v-if="localDate(orderData.date_request) !== localDateEn(orderData.date_request)">
                                            <br>
                                            <i>{{ localDateEn(orderData.date_request) }}</i>
                                            </template>
                                        </template>
                                    </div>
                                </div>
                                <div class="text-regular-14 row mb-2 align-items-start">
                                    <div class="col-3">
                                    <span class="underline">Tanggal Hasil</span><br>
                                    <span class="italic">Report Date</span>
                                    </div>
                                    <div class="col-auto text-right pr-0" style="width:10px;">:</div>
                                    <div class="col">
                                        {{ urlData.date_result ? localDate(urlData.date_result) : '' }}
                                        <template v-if="urlData.date_result">
                                            <template v-if="localDate(urlData.date_result) !== localDateEn(urlData.date_result)">
                                            <br>
                                            <i>{{ localDateEn(urlData.date_result) }}</i>
                                            </template>
                                        </template>
                                    </div>
                                    <!-- <div class="col">{{urlData.date_result ? localDate(urlData.date_result) : ''}}<br>
                                    <i>{{urlData.date_result ? localDateEn(urlData.date_result) : ''}}</i></div> -->
                                </div>
                                <div class="text-regular-14 row mb-2 align-items-start">
                                    <div class="col-3">
                                    <span class="underline">Alamat</span><br>
                                    <span class="italic">Address</span>
                                    </div>
                                    <div class="col-auto text-right pr-0" style="width:10px;">:</div>
                                    <div class="col">{{ regData.address }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-regular-14 row mb-2 align-items-start">
                                    <div class="col-3">
                                    <span class="underline">No. Radiologi</span><br>
                                    <span class="italic">Radiology No.</span>
                                    </div>
                                    <div class="col-auto text-right pr-0" style="width:10px;">:</div>
                                    <div class="col">{{orderData.no_request}}</div>
                                </div>
                                <div class="text-regular-14 row mb-2 align-items-start">
                                    <div class="col-3">
                                    <span class="underline">Tanggal Lahir</span><br>
                                    <span class="italic">Date of Birth</span>
                                    </div>
                                    <div class="col-auto text-right pr-0" style="width:10px;">:</div>
                                    <div class="col">
                                        {{ regData.birth_date ? localDate(regData.birth_date) : '' }}
                                        <template v-if="regData.birth_date">
                                            <template v-if="localDate(regData.birth_date) !== localDateEn(regData.birth_date)">
                                            <br>
                                            <i>{{ localDateEn(regData.birth_date) }}</i>
                                            </template>
                                        </template>
                                    </div>
                                </div>
                                <div class="text-regular-14 row mb-2 align-items-start">
                                    <div class="col-3">
                                    <span class="underline">Umur</span><br>
                                    <span class="italic">Age</span>
                                    </div>
                                    <div class="col-auto text-right pr-0" style="width:10px;">:</div>
                                    <div class="col">{{ regData.birth_date ? parsed_age(regData.birth_date, 'id') : '' }}
                                        <template v-if="regData.birth_date">
                                            <template v-if="parsed_age(regData.birth_date, 'id') !== parsed_age(regData.birth_date, 'en')">
                                            <br>
                                            <i>{{ parsed_age(regData.birth_date, 'en') }}</i>
                                            </template>
                                        </template>
                                    </div>
                                </div>
                                <div class="text-regular-14 row mb-2 align-items-start">
                                    <div class="col-3">
                                    <span class="underline">Jenis Kelamin</span><br>
                                    <span class="italic">Sex</span>
                                    </div>
                                    <div class="col-auto text-right pr-0" style="width:10px;">:</div>
                                    <div class="col">
                                        {{ regData.sex_name }}<br>
                                        <i>{{ regData.sex_name === 'Perempuan' ? 'Female' : regData.sex_name === 'Laki-laki' ? 'Male' : regData.sex_name }}</i>
                                    </div>
                                    <!-- <div class="col">{{ regData.sex_name }}</div> -->
                                </div>
                                <div class="text-regular-14 row mb-2 align-items-start">
                                    <div class="col-3">
                                    <span class="underline">Dokter Pengirim</span><br>
                                    <span class="italic">Referring Physician</span>
                                    </div>
                                    <div class="col-auto text-right pr-0" style="width:10px;">:</div>
                                    <div class="col">{{ regData.doctor_in_charge_name }}</div>
                                </div>
                                <!-- <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell">No. Radiologi</td>
                                        <td class="px-3">:</td>
                                        <td>{{orderData.no_request}}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Tanggal Lahir</td>
                                        <td class="px-3">:</td>
                                        <td>{{regData.birth_date ? localDate(regData.birth_date) : ''}}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Umur</td>
                                        <td class="px-3">:</td>
                                        <td>{{parsed_age(regData.birth_date)}}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Kelamin</td>
                                        <td class="px-3">:</td>
                                        <td>{{regData.sex_name}}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell"> Dokter Pengirim</td>
                                        <td class="px-3">:</td>
                                        <td>{{ regData.doctor_in_charge_name }}</td>
                                    </tr>
                                </table> -->
                            </div>
                        </div>
                        <div class="mt-32 col">
                            <div>Yth.{{ regData.doctor_in_charge_name }}</div>
                            <div class="mt-12">{{ reqItemData.costing_name }}</div>
                            <div class="mb-2 row align-items-start">
                                <div class="col-2 align-top" style="min-width:150px;">
                                <span class="underline">Klinis</span><br>
                                <span class="italic">Clinical</span>
                                </div>
                                <div class="col-auto align-top text-center px-2" style="width:30px;">:</div>
                                <div class="col align-top">
                                <template v-if="assessmentData && assessmentData.icd10">
                                    <ul class="mb-0 pl-3">
                                    <li v-for="(item, index) in assessmentData.icd10" :key="index">{{ item.code }} {{ item.name }}</li>
                                    </ul>
                                </template>
                                </div>
                            </div>

                            <div class="mb-2 row align-items-start">
                                <div class="col-2 align-top">
                                <span class="underline">Expertise</span><br>
                                <span class="italic">Findings</span>
                                </div>
                                <div class="col-auto align-top text-center px-2" style="width:30px;">:</div>
                                <div class="col align-top">
                                <span v-html="parsedBody(urlData.conclusion ? urlData.conclusion : '-')"></span>
                                </div>
                            </div>

                            <div class="mb-2 row align-items-start font-weight-bold">
                                <div class="col-2 align-top pt-4">
                                <span class="underline">Kesan</span><br>
                                <span class="italic">Impression</span>
                                </div>
                                <div class="col-auto align-top text-center px-2 pt-4" style="width:30px;">:</div>
                                <div class="col align-top pt-4">
                                <span v-html="parsedBody(urlData.advice ? urlData.advice : '-')"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-32 d-flex justify-content-end text-center">
                        <div>
                            <div>Bekasi, {{ today }}</div>
                            <div :class="urlData.ttd_name ? '' : 'mb-72'">Salam Sejawat</div>
                            <b-img :src="urlbe+urlData.ttd_name" style="width: 220px;" />
                            <!-- <div>{{ urlData.ttd_name }}</div> -->
                            <div>{{ urlData.doctor_lab_name }}</div>
                        </div>
                    </div>
                    <!-- <div class="canvas-footer mt-32">
                        <div class="d-flex justify-content-center">
                            <img src="./../../../../@core/assets/images/nurse/vector-footer.png" style="width: 100%;"
                                alt="footer">
                        </div>
                    </div> -->
                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
import moment from 'moment';

export default {
    data() {
        return {
            urlbe:process.env.VUE_APP_IMAGE,
            order_id: '',
            radiology_request_item_id: '',
            image_id: '',

            orderData: {},
            regData: {},
            reqItemData: {},
            urlData: {},
            assessmentData: {},
            today: moment(new Date()).locale('id').format('LL')
        }
    },
    methods: {
        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },
        getData (id) {
			this.$store.dispatch('radiology/getByIdOrder', id).then(data => {
                this.orderData = data[0]
                this.regData = data[0].regData

                // console.log('this.orderData', this.orderData);

                let RRI_index = this.orderData.radiology_request_item.findIndex((item) => item.id == this.radiology_request_item_id)

                if (RRI_index !== -1) {
                    // console.log('ketemu');
                    this.reqItemData = this.orderData.radiology_request_item[RRI_index]
                    let url_index = this.orderData.radiology_request_item[RRI_index].url.findIndex((url) => url.id == this.image_id)

                    if (url_index !== -1) {
                        console.log('ketemu lagi');
                        this.urlData = this.orderData.radiology_request_item[RRI_index].url[url_index]
                        console.log('this.urlData: ', this.urlData);
                    } else {
                        console.warn('url.id is not found at radiology_request_item_id');
                    }

                } else {
                    console.warn('radiology_request_item_id is not found at this order');
                }
                // let index = this.form_transaction.form_prescription_parent.findIndex((item) => item.id === event.value);

                const emergency_id = "444b3134-5b75-4b67-af72-a558d46716d7"
                const inpatient_id = "40f06534-07ec-4221-ad9d-dea08196079f"
                const outpatient_id = "3cb626f5-9148-4fb1-8663-3d7f028b01a6"

                if (this.regData.health_facility_id == inpatient_id) {
                    this.$store.dispatch('cppt/getListCPPT', { registration_destination_id: this.regData.registration_destination_id }).then((data) => {
                        let cppt_data = data[0]

                        if (cppt_data) {
                            this.assessmentData = cppt_data
                            // console.log('from ccpt:', this.assessmentData);
                        }
                    })
                } else if (this.regData.health_facility_id == emergency_id || this.regData.health_facility_id == outpatient_id) {
                    this.$store.dispatch('soap/getByRegId', this.regData.registration_destination_id).then((data)=>{
                        let soap_data = data[0]

                        if (soap_data) {
                            this.assessmentData = soap_data
                            // console.log('from soap:', this.assessmentData);
                        }
                    })
                }

            })
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
        parsed_age(input_date, lang = 'id') {
            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            if (lang === 'en') {
                return `${years} yr ${months} mo ${days} d`;
            }
            // Default Indonesian
            return `${years} th ${months} bln ${days} hr`;
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
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
        localDateEn(input_date) {
            return moment(input_date).locale("en").format('DD MMMM YYYY')
        },
    },

    created() {
        if (this.$route.params.order_id) {
            this.order_id = this.$route.params.order_id
            this.getData(this.order_id)
        }
        if (this.$route.params.image_id) {
            this.image_id = this.$route.params.image_id
            // console.log(this.image_id);
        }
        if (this.$route.params.radiology_request_item_id) {
            this.radiology_request_item_id = this.$route.params.radiology_request_item_id
            // console.log(this.radiology_request_item_id);
        }
    },
    mounted() {
        window.addEventListener('keydown', this.handleKeyPress);
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
};

</script>
<style>
@import './../../../../@core/css/main/main.min.css';

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
}

.canvas-header {
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
table.custom-border tr td {
    border: 0px;
}
.italic{
    font-style: italic;
}
</style>
