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
                        <div>Lembar Hasil Re-asesmen Rehabilitasi Medis </div>
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
                        <div class="card-header text-left text-bold-14">RE-ASESMEN</div>
                        <div class="card-body">
                            <table class="custom" style="font-size: 13px;">
                                <tbody>
                                    <tr>
                                        <td class="align-top" style="width: 200px;"> Objective</td>
                                        <td class="align-top px-3" style="width: 30px;">:</td>
                                        <td class="align-top">
                                            <div v-html="parsedBody(soapData.main_complaint ? soapData.main_complaint : '-')"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-top"> Asesmen </td>
                                        <td class="align-top px-3">:</td>
                                        <td class="align-top list-unstyled">
                                            <template v-if="soapData.icd10 && soapData.icd10.length > 0">
                                                <li v-for="(item, index) in soapData.icd10" :key="index">{{ item.code }} {{ item.name }}</li>
                                            </template>
                                            <template v-else>-</template>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="card mt-10">
                        <div class="card-header text-left text-bold-14"> PLANNING </div>
                        <div class="card-body">
                            <table class="custom" style="font-size: 13px;">
                              <tbody>
                                <tr>
                                    <td class="align-top" style="width: 200px;"> Goal of Treatment </td>
                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                    <td class="align-top">
                                        <div v-html="parsedBody(soapData.goal_of_treatment ? soapData.goal_of_treatment : '-')"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-top" style="width: 200px;"> Tindakan (ICD9CM)</td>
                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                    <td class="align-top">
                                        <div v-html="parsedBody(soapData.therapy_plan ? soapData.therapy_plan : '-')"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-top" style="width: 200px;"> Edukasi </td>
                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                    <td class="align-top">
                                        <div v-html="parsedBody(soapData.education ? soapData.education : '-')"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-top" style="width: 200px;"> Frekuensi Kunjungan </td>
                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                    <td class="align-top">
                                        <div v-html="parsedBody(soapData.visit_frequency ? soapData.visit_frequency : '-')"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-top" style="width: 200px;"> Instruksi </td>
                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                    <td class="align-top">
                                        <div v-html="parsedBody(soapData.instruction ? soapData.instruction : '-')"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-top" style="width: 200px;"> Evaluasi </td>
                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                    <td class="align-top">
                                        <div v-html="parsedBody(soapData.evaluation ? soapData.evaluation : '-')"></div>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="align-top" style="width: 200px;"> Anjuran </td>
                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                    <td class="align-top">
                                        <div v-html="parsedBody(soapData.advice ? soapData.advice : '-')"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-top" style="width: 200px;"> Rencana Tindak Lanjut </td>
                                    <td class="align-top px-3" style="width: 30px;">:</td>
                                    <!-- <td class="align-top">
                                        <div v-html="parsedBody(soapData.followup_plan ? soapData.followup_plan : '-')"></div>
                                    </td> -->
                                    <td class="align-top list-unstyled">
                                        <template v-if="soapData.follow_up_care_arry && soapData.follow_up_care_arry.length > 0">
                                            <li v-for="(item, index) in soapData.follow_up_care_arry" :key="index">{{ item.code }} {{ item.name }}</li>
                                        </template>
                                        <template v-else>-</template>
                                    </td>
                                </tr>

                              </tbody>
                            </table>
                          </div>
                      </section>
                      <div class="d-flex justify-content-end w-100 mt-32" style="font-size: 13px">
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
            qrDoctor: '',
            soapData: {},
            regData: {},
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
</style>
