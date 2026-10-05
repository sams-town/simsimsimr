<template>
    <div class="d-flex w-100 justify-content-center">
        <div>
            <div class="d-flex justify-content-end py-20" ref="buttons">
                <b-button @click="closeWindow" size="sm" variant="danger-400" class="px-32">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" variant="success-500" class="px-32 ml-2">Print</b-button>
            </div>
            <section id="canvas" ref="canvas">

                <div class="d-flex align-items-center justify-content-center">
                    <img src="../../../../../@core/assets/images/logo-thb.jpg" width="70px" alt="logo-thb" class="mr-16">
                    <div class="text-center">
                        <div class="font-weight-bold" style="font-size: 13px; text-decoration: underline; line-height: 1.4;">
                            RS Taman Harapan Baru
                        </div>
                        <div style="font-size: 11px; line-height: 1.5;">
                            Jl. Kaliabang Tengah No.2, RT 004/023 Pejuang, Medan Satria, Bekasi, Jawa Barat
                        </div>
                        <div style="font-size: 11px; line-height: 1.5;">021-8898 1055</div>
                        <div style="font-size: 11px; line-height: 1.5;">E-mail : rs_thb@yahoo.com</div>
                    </div>
                </div>

                <hr style="border: none; border-top: 2px solid black;" class="my-10" />

                <table class="mt-6" style="border-collapse: collapse; font-size: 13px;">
                    <tr>
                        <td style="width: 120px; vertical-align: top; padding: 2px 0;">No. RM</td>
                        <td style="width: 12px; padding: 2px 4px; vertical-align: top;">:</td>
                        <td style="vertical-align: top; padding: 2px 0;">{{ regData.norm }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; padding: 2px 0;">Nama Pasien</td>
                        <td style="padding: 2px 4px; vertical-align: top;">:</td>
                        <td style="vertical-align: top; padding: 2px 0;">{{ regData.name }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; padding: 2px 0;">Tanggal Lahir</td>
                        <td style="padding: 2px 4px; vertical-align: top;">:</td>
                        <td style="vertical-align: top; padding: 2px 0;">{{ localDate(regData.birth_date, false) }} ({{ parsed_age(regData.birth_date) }})</td>
                    </tr>
                </table>

                <b-table-simple bordered small class="mt-16 mb-0" style="width: 100%; font-size: 13px;">
                    <b-tbody>
                        <b-tr>
                            <b-td style="width: 160px;" class="align-top">Diagnosa</b-td>
                            <b-td class="align-top">{{ hdData.diagnosa }}</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="align-top">Keterangan Lain</b-td>
                            <b-td class="align-top">{{ hdData.other_2 }}</b-td>
                        </b-tr>
                    </b-tbody>
                </b-table-simple>

                <ModernLabel class="mt-16" label="PERENCANAAN PULANG (DISCHARGE PLANNING)" />
                <b-table-simple bordered small class="mt-2 mb-0" style="width: 100%; font-size: 13px;">
                    <b-tbody>
                        <b-tr>
                            <b-td style="width: 160px;" class="align-top">1. Hari dan tanggal tindakan HD</b-td>
                            <b-td class="align-top">{{ localDate(hdData.next_hd_date) }} </b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="align-top">2. Edukasi</b-td>
                            <b-td class="align-top">{{ hdData.education }}</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="align-top">3. Rencana konsultasi</b-td>
                            <b-td class="align-top">{{ hdData.consult_planning }}</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="align-top">4. Rencana pemeriksaan</b-td>
                            <b-td class="align-top list-unstyled">
                                <template v-if="hdData.lab_item && hdData.lab_item.length > 0">
                                    <li v-for="(item, index) in hdData.lab_item" :key="index">{{ item.code }} {{ item.name }}</li>
                                </template>
                                <template v-else>-</template>
                            </b-td>
                        </b-tr>
                        <b-tr>
                            <b-td class="align-top">5. Lain - lain</b-td>
                            <b-td class="align-top">{{ hdData.other_2 }}</b-td>
                        </b-tr>
                    </b-tbody>
                </b-table-simple>

                <div class="text-center mt-20" style="font-size: 13px;">Bekasi, {{ signDate }}</div>

                <div class="d-flex justify-content-between mt-16" style="padding: 0 40px; font-size: 13px;">
                    <div class="text-center" style="min-width: 140px;">
                        <div class="font-weight-bold">Perawat,</div>
                        <div class="mt-8">
                            <img v-if="qrNurse" :src="qrNurse" width="80px" height="80px" style="object-fit: contain;" alt="QR Perawat">
                            <div v-else style="width: 80px; height: 80px; border: 1px solid #ccc; display: inline-block;"></div>
                        </div>
                        <div class="mt-6 font-weight-bold" style="text-decoration: underline;">{{ regData.nurse_name }}</div>
                    </div>
                    <div class="text-center" style="min-width: 140px;">
                        <div class="font-weight-bold">Dokter Penanggung Jawab,</div>
                        <div class="mt-8">
                            <img v-if="qrDoctor" :src="qrDoctor" width="80px" height="80px" style="object-fit: contain;" alt="QR Dokter">
                            <div v-else style="width: 80px; height: 80px; border: 1px solid #ccc; display: inline-block;"></div>
                        </div>
                        <div class="mt-6 font-weight-bold" style="text-decoration: underline;">{{ regData.doctor_in_charge_name }}</div>
                    </div>
                </div>

            </section>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import QRCode from 'qrcode';

export default {
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            qrDoctor: '',
            qrNurse: '',
            signDate: '',
            soapData: {},
            regData: {},
            hdData: {},
        }
    },

    methods: {
        parsedBody(text) {
            return String(text || '').replace(/\n/g, "<br>");
        },

        localDate(input_date, showDay = true) {
            if (!input_date || input_date === "0000-00-00") return "";
            const parsed = moment(input_date);
            if (!parsed.isValid()) return "";
            const formatString = showDay ? 'dddd, DD MMMM YYYY' : 'DD MMMM YYYY';
            return parsed.locale("id").format(formatString);
        },

        parsed_age(input_date) {
            if (!input_date || input_date === "0000-00-00") return "";
            const parsed = moment(input_date);
            if (!parsed.isValid()) return "";
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
                color: { dark: '#000000', light: '#ffffff' },
            })
        },

        buildPayload(name) {
            return `Ditandatangani secara elektronik oleh ${name}`
        },

        async generateAllQRCodes() {
            const doctorName = this.regData.doctor_in_charge_name || this.regData.doctor_name || ''
            const nurseName  = this.regData.nurse_name            || this.soapData.nurse_name  || ''

            if (doctorName) {
                try { this.qrDoctor = await this.generateQR(this.buildPayload(doctorName)) }
                catch (err) { console.error('QR Doctor error:', err) }
            }
            if (nurseName) {
                try { this.qrNurse = await this.generateQR(this.buildPayload(nurseName)) }
                catch (err) { console.error('QR Nurse error:', err) }
            }
        },

        async getData(soap_id) {
            this.$store.dispatch('soap/getBySoapId', soap_id).then(async data => {
                this.soapData = data[0]

                this.signDate = this.soapData.sign_date
                    ? moment(this.soapData.sign_date).format('DD MMMM YYYY')
                    : moment().format('DD MMMM YYYY')

                this.$store.dispatch('registration/getById', this.soapData.registration_destination_id).then(async data => {
                    this.regData = data[0]

                    this.$store.dispatch('nurse/getById', this.soapData.registration_destination_id).then((data) => {
                        this.hdData = data?.[0]?.hd
                    }, error => {
                        console.error('Error while GET nurse/getNurseById at: ', error);
                    })

                    this.$nextTick(async () => {
                        await this.generateAllQRCodes()
                    })
                })
            })
        },

        printCanvas() {
            const canvas = this.$refs.canvas;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = canvas.innerHTML;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        },

        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault()
                this.printCanvas()
            }
        },

        closeWindow() {
            window.close()
        },
    },

    created() {
        this.getData(this.$route.params.soap_id)
    },

    mounted() {
        window.addEventListener('keydown', this.handleKeyPress)
    },

    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress)
    },
}
</script>

<style>
@import './../../../../../@core/css/main/main.min.css';

@media print {
    @page {
        size: A4 portrait;
        margin: 16mm;
    }
}

#canvas {
    width: 210mm;
    min-height: 297mm;
    background-color: white;
    padding: 14mm;
    box-sizing: border-box;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.15);
}

td {
    padding: 2px 0;
}

p {
    margin: 0;
    padding: 0;
}
</style>