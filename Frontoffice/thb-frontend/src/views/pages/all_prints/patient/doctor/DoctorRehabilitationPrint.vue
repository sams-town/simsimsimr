<template>
    <div>
        <b-container fluid="xl" class="mt-32 pb-32">
            <div class="d-flex justify-content-end">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="d-flex kustify-content-center w-100 pb-40 mt-16">
                <div id="canvas" ref="canvas" class="w-100" style="position: relative;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex">
                            <div>
                                <img src="../../../../../@core/assets/images/logo-thb-small.png" width="80px" alt="rs-logo"> <br>
                            </div>
                            <div class="text-regular-14 pl-16">
                                <p class="text-bold-18">RS Taman Harapan Baru</p>
                                <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                                <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                            </div>
                        </div>
                        <div>
                            <!-- <img src="../../../../../@core/assets/images/lafki-logo.png" width="114px" alt="lafki-logo"> -->
                        </div>
                    </div>
                    <div class="text-uppercase text-center font-weight-bold mt-32" style="font-size: 24px; line-height: 120%;">
                        <div>Lembar formulir rawat jalan</div>
                        <div>Layanan kedokteran fisik dan rehabilitasi medik</div>
                    </div>
                    <section class="mt-24" style="line-height: 140%; font-size: 13px;">
                        <div class="row">
                            <div class="col-6">
                                <table class="mt-16">
                                    <tr>
                                        <td class="align-top" style="width: 160px">Nomor RM</td>
                                        <td class="align-top text-right px-3">:</td>
                                        <td>{{ this.regData.norm }}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top" style="">Nama</td>
                                        <td class="align-top text-right px-3">:</td>
                                        <td>{{ this.regData.alias_name }} {{ this.regData.fullname }}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top" style="">Tanggal Lahir</td>
                                        <td class="align-top text-right px-3">:</td>
                                        <td>{{ localDate(this.regData.birth_date) }} ({{ parsed_age(regData.birth_date) }})</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top" style="">Jenis Kelamin</td>
                                        <td class="align-top text-right px-3">:</td>
                                        <td>{{ this.regData.sex_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top" style="">Tanggal Registrasi</td>
                                        <td class="align-top text-right px-3">:</td>
                                        <td>{{ localDate(this.regData.register_date) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                    </section>

                    <section class="card mt-32">
                        <div class="card-header text-left text-bold-14">Input Dokter SPKFR</div>
                        <div class="card-body">
                            <table class="custom" style="font-size: 13px;">
                                <tbody>
                                    <tr>
                                        <td class="align-top" style="width: 220px;">Anamnesa</td>
                                        <td class="align-top px-3" style="width: 30px;">:</td>
                                        <td class="align-top">{{ soapData.main_complaint }}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Pemeriksaan Fisik & Uji Fungsi</td>
                                        <td class="align-top px-3">:</td>
                                        <td class="align-top">
                                            <div v-html="parsedBody(soapData.functional_testing ? soapData.functional_testing : '-')"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Diagnosis ICD-10</td>
                                        <td class="align-top px-3">:</td>
                                        <td class="align-top list-unstyled">
                                            <template v-if="soapData.icd10 && soapData.icd10.length > 0">
                                                <li v-for="(item, index) in soapData.icd10" :key="index">{{ item.code }} {{ item.name }}</li>
                                            </template>
                                            <template v-else>-</template>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Catatan ICD-10</td>
                                        <td class="align-top px-3">:</td>
                                        <td class="align-top">
                                            <div v-html="parsedBody(soapData.icd10_notes ? soapData.icd10_notes : '-')"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Diagnosis Non ICD-10</td>
                                        <td class="align-top px-3">:</td>
                                        <td class="align-top">
                                            <template v-if="soapData.non_icd10 && soapData.non_icd10.length > 0">
                                                <li v-for="(item, index) in soapData.non_icd10" :key="index">{{ item }}</li>
                                            </template>
                                            <template v-else>-</template>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Pemeriksaan Fisioterapi</td>
                                        <td class="align-top px-3">:</td>
                                        <td class="align-top">
                                            <template v-if="soapData.physiotherapy_request && soapData.physiotherapy_request.length > 0">
                                                <div v-for="(item, index_i) in soapData.physiotherapy_request" :key="index_i">
                                                    <span class="font-weight-bold">{{ item.no_request }}: </span>
                                                    <template v-if="item.physiotherapy_request_items && item.physiotherapy_request_items.length > 0">
                                                        <span v-for="(list, index_j) in item.physiotherapy_request_items" :key="index_j">
                                                            {{ index_j + 1 == item.physiotherapy_request_items.length ? list.costing_name : `${list.costing_name}, ` }}
                                                        </span>
                                                    </template>
                                                </div>
                                            </template>
                                            <template v-else>-</template>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top"> Pemeriksaan Penunjang</td>
                                        <td class="align-top px-3">:</td>
                                        <td class="align-top">
                                            <div v-html="parsedBody(soapData.support_examination ? soapData.support_examination : '-')"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Tata Laksana KFR (ICD 9 CM)</td>
                                        <td class="align-top px-3">:</td>
                                        <td class="align-top">
                                            <div v-html="parsedBody(soapData.therapy_plan ? soapData.therapy_plan : '-')"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top"> Evaluasi </td>
                                        <td class="align-top px-3">:</td>
                                        <td class="align-top">
                                            <div v-html="parsedBody(soapData.evaluation ? soapData.evaluation : '-')"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top"> Goal of Treatment </td>
                                        <td class="align-top px-3">:</td>
                                        <td class="align-top">
                                            <div v-html="parsedBody(soapData.goal_of_treatment ? soapData.goal_of_treatment : '-')"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Anjuran</td>
                                        <td class="align-top px-3">:</td>
                                        <td class="align-top">
                                            <div v-html="parsedBody(soapData.advice ? soapData.advice : '-')"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Suspek Penyakit Akibat</td>
                                        <td class="align-top px-3">:</td>
                                        <td class="align-top">
                                            <span v-if="soapData.suspected_disease_type_id == 1">Ya, {{ soapData.suspected_disease ? soapData.suspected_disease : '-' }}</span>
                                            <span v-else-if="soapData.suspected_disease_type_id == 0">Tidak</span>
                                            <span v-else>-</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <div class="d-flex justify-content-end w-100 mt-32">
                        <div class="col-3 text-center">
                            <div class="text-bold-14">Dokter Penanggung Jawab</div>
                            <img v-if="qrDoctor" :src="qrDoctor" class="qr-img mt-8" alt="QR Dokter">
                            <div v-else class="qr-placeholder"></div>
                            <div class="text-regular-14"> ({{ soapData.employee_doctor_name }})</div>
                        </div>
                    </div>

                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
import moment from 'moment';
import QRCode from 'qrcode';

export default {
    data() {
        return {
            urlbe:process.env.VUE_APP_IMAGE,
            soapData: {},
            regData: {},
            qrDoctor: '',
        }
    },

    methods: {
        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },

        localDate(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "";
            }

            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },

        parsed_age(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "";
            }

            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            return `${years} th ${months} bln ${days} hr`;
        },

        async generateQR(text) {
            return await QRCode.toDataURL(text, {
                errorCorrectionLevel: 'M',
                type: 'image/png',
                width: 120,
                margin: 1,
                color: {
                    dark: '#000000',
                    light: '#ffffff',
                },
            })
        },

        buildPayload(name) {
            return `Ditandatangani secara elektronik oleh ${name}`
        },

        async getData (soap_id) {
            this.$store.dispatch('soap/getBySoapId', soap_id).then((data) => {
                this.soapData = data[0]

                this.$store.dispatch('registration/getById', this.soapData.registration_destination_id).then(data => {
                    this.regData = data[0]

                    this.$nextTick(async () => {
                        await this.generateAllQRCodes()
                    })
                })

            })
        },


        async generateAllQRCodes() {
            const doctorName = this.regData.doctor_in_charge_name || ''

            if (doctorName) {
                try {
                    this.qrDoctor = await this.generateQR(
                        this.buildPayload(doctorName)
                    )
                } catch (err) {
                    console.error('QR generation error:', err)
                }
            }
        },

        renderBarcode() {
            if (this.regData.barcode_value && this.$refs.barcodeSvg) {
                JsBarcode(this.$refs.barcodeSvg, this.regData.barcode_value, {
                    format: 'CODE128',
                    width: 2,
                    height: 40,
                    displayValue: false,
                    margin: 0,
                });
            }
        },

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
            this.$nextTick(async () => {
                buttons.classList.remove('d-none');
                this.renderBarcode();
                await this.generateAllQRCodes()
            });
            location.reload()
        },
    },

    mounted() {
        window.addEventListener('keydown', this.handleKeyPress);
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },

    created() {
        this.getData(this.$route.params.soap_id)
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

.custom td {
    padding: 4px 0;
}

.sign-role {
    font-size: 12px;
    font-weight: 700;
}


.mt-8-lg {
    width: 80px;
    height: 80px;
    object-fit: contain;
}

.qr-placeholder-lg {
    width: 80px;
    height: 80px;
    border: 1px solid #ccc;
    display: inline-block;
}

</style>
