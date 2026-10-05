<template>
    <div class="d-flex w-100 justify-content-center">
        <div>
            <div class="d-flex justify-content-end py-20" ref="buttons">
                <b-button @click="closeWindow" size="sm" variant="danger-400" class="px-32">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" variant="success-500" class="px-32 ml-2">Print</b-button>
            </div>
            <section id="canvas" ref="canvas" class="p-20">

                <div class="d-flex align-items-center justify-content-center header-section">
                    <img src="../../../../../@core/assets/images/logo-thb-small.png" width="70px" alt="logo-thb" class="mr-16">
                    <div class="text-center">
                        <div class="hospital-name">RS Taman Harapan Baru</div>
                        <div class="hospital-address">Jl. Kaliabang Tengah No.2, RT 004/023 Pejuang, Medan Satria Bekasi Jawa Barat</div>
                        <div class="hospital-address">021-8898 1055</div>
                        <div class="hospital-address">E-mail : rs_thb@yahoo.com</div>
                    </div>
                </div>

                <div class="divider-line my-10"></div>

                <div class="text-center">
                    <div class="title-main">LEMBAR PROGRAM TERAPI</div>
                    <div class="title-date">Tanggal: {{ soapData.date_updated }}</div>
                </div>

                <table class="info-table mt-16">
                    <tr>
                        <td class="info-label">No. Rawat</td>
                        <td class="info-colon">:</td>
                        <td class="info-value">{{ regData.code }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">No. RM</td>
                        <td class="info-colon">:</td>
                        <td class="info-value">{{ regData.norm }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Nama Pasien</td>
                        <td class="info-colon">:</td>
                        <td class="info-value">{{ regData.name }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Jenis Kelamin / Umur</td>
                        <td class="info-colon">:</td>
                        <td class="info-value">{{ regData.sex_name }} / {{ parsed_age(regData.birth_date) }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Alamat</td>
                        <td class="info-colon">:</td>
                        <td class="info-value">{{ regData.address }}</td>
                    </tr>
                </table>

                <div class="section-title mt-20 mb-10">SOAP</div>

                <div class="soap-label mt-10">Subjective</div>
                <div class="soap-content mt-2">{{ soapData.main_complaint || '-'  }}</div>

                <div class="soap-label mt-16">Objective</div>
                <div class="soap-content mt-2">{{ rehabData.objective || '-'  }}</div>

                <div class="soap-content mt-3">
                <div class="row mb-3 print-section">
                    <div class="col-4">
                        <small class="text-muted fw-bold border-bottom d-block mb-1">Nyeri Tekan</small>
                        <div class="text-break">{{ soapData.pain_push || '-' }}</div>
                    </div>
                    <div class="col-4">
                        <small class="text-muted fw-bold border-bottom d-block mb-1">Nyeri Gerak</small>
                        <div class="text-break">{{ soapData.pain_move || '-' }}</div>
                    </div>
                    <div class="col-4">
                        <small class="text-muted fw-bold border-bottom d-block mb-1">Nyeri Diam</small>
                        <div class="text-break">{{ soapData.pain_still || '-' }}</div>
                    </div>
                </div>

                <div class="mb-3 print-section">
                    <small class="text-muted fw-bold border-bottom d-block mb-1">Kekuatan Otot</small>
                    <div class="text-break text-justify">{{ soapData.muscle_strength || '-' }}</div>
                </div>

                <div class="mb-3 print-section">
                    <small class="text-muted fw-bold border-bottom d-block mb-1">Lingkup Gerak Sendi (ROM)</small>
                    <div class="text-break text-justify">{{ soapData.range_of_motion || '-' }}</div>
                </div>

                <div class="mb-3 print-section">
                    <small class="text-muted fw-bold border-bottom d-block mb-1">Test Khusus</small>
                    <div class="text-break text-justify">{{ soapData.special_test || '-' }}</div>
                </div>

                <div class="mb-3 print-section">
                    <small class="text-muted fw-bold border-bottom d-block mb-1">Pemeriksaan Neurologis</small>
                    <div class="text-break text-justify">{{ soapData.neurologis_check || '-' }}</div>
                </div>
            </div>

                <div class="soap-label mt-16">Assessment</div>
                <div class="soap-content mt-2">
                    <template v-if="soapData.icd10 && soapData.icd10.length">
                        <div v-for="(item, index) in soapData.icd10" :key="item.id">
                            {{ index + 1 }}. {{ item.code }} - {{ item.name }}
                        </div>
                    </template>
                    <template v-else>-</template>
                    <div class="mt-10"> Catatan: {{ soapData.icd10_notes || '-' }}</div>
                </div>

                <div class="section-title fw-bold mt-4 mb-3">PROSEDUR / TERAPI</div>
                <div class="row print-section">
                    <div class="col-6 pe-3">
                        <small class="soap-label d-block mb-2">Prosedur</small>
                        <div class="soap-content">
                            <template v-if="soapData.transaction_doctor && soapData.transaction_doctor.length">
                                <div v-for="(item, index) in soapData.transaction_doctor" :key="item.id" class="text-break">
                                    {{ index + 1 }}. {{ item.costing_name }}
                                </div>
                            </template>
                            <template v-else>
                                <div>-</div>
                            </template>
                        </div>
                    </div>

                    <div class="col-6 ps-3 border-start">
                        <small class="soap-label d-block mb-2">Terapi</small>
                        <div class="soap-content">
                            <template v-if="soapData.physiotherapy_request_items && soapData.physiotherapy_request_items.length">
                                <div v-for="(item, index) in soapData.physiotherapy_request_items" :key="item.id" class="text-break">
                                    {{ index + 1 }}. {{ item.costing_name }}
                                </div>
                            </template>
                            <template v-else>
                                <div>-</div>
                            </template>

                        </div>
                    </div>
                </div>

                <div style="flex: 1; min-height: 60px;"></div>

                <div class="footer-section mt-24">
                    <div class="footer-print-date">Tanggal Cetak: {{ printedAt }}</div>
                    <div class="d-flex justify-content-between mt-10" style="padding: 0 40px;">
                        <div class="text-center">
                            <div class="sign-role">Petugas</div>
                            <div class="mt-8">
                                <img v-if="regData.officer_qr" :src="regData.officer_qr" class="qr-img-lg" alt="QR Petugas">
                                <div v-else class="qr-placeholder-lg"></div>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="sign-role">Dokter Penanggung Jawab</div>
                            <div class="mt-8">
                            <img v-if="qrDoctor" :src="qrDoctor" class="qr-img" alt="QR Dokter">
                            <div v-else class="qr-placeholder"></div>
                            <div> ({{ soapData.employee_doctor_name }})</div>
                        </div>
                        </div>
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
            printedAt: '',
            regData: {},
            soapData: {},
            rehabData: {},
        }
    },

    methods: {
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

        buildPayload(name) {
            return `Ditandatangani secara elektronik oleh ${name}`
        },

        parsed_age(input_date) {
            if (!input_date || input_date === '0000-00-00') return '-';
            const parsed = moment(input_date);
            if (!parsed.isValid()) return '-';
            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const now = moment();
            const years = now.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = now.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = now.diff(birthDate, 'days');
            return `${years} Th`;
        },

         async getData (soap_id) {
            this.$store.dispatch('soap/getBySoapId', soap_id).then((data) => {
                this.soapData = data[0]

                this.$store.dispatch('registration/getById', this.soapData.registration_destination_id).then(data => {
                    this.regData = data[0]
                    this.printedAt = moment().format('DD MMMM YYYY HH:mm')

                    this.$store.dispatch('nurse/getById', this.soapData.registration_destination_id).then((data) => {
                        this.rehabData = data?.[0]
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
                    location.reload()
                },

        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                this.printCanvas();
            }
        },

        closeWindow() {
            window.close();
        },
    },

    created() {
        this.getData(this.$route.params.soap_id);
    },

    mounted() {
        window.addEventListener('keydown', this.handleKeyPress);
    },

    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
};
</script>

<style scoped>
@import './../../../../../@core/css/main/main.min.css';

@media print {
    @page {
        size: A4;
        margin: 16mm;
    }
}

#canvas {
    width: 210mm;
    min-height: 297mm;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.15);
    background-color: white;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
}

.hospital-name {
    font-size: 16px;
    font-weight: 700;
    text-decoration: underline;
    line-height: 1.4;
}

.hospital-address {
    font-size: 12px;
    line-height: 1.5;
}

.divider-line {
    border: none;
    border-top: 2px solid black;
}

.title-main {
    font-size: 18px;
    font-weight: 700;
    letter-spacing: 1px;
    text-decoration: underline;
}

.title-date {
    font-size: 12px;
    margin-top: 2px;
}

.info-table {
    border-collapse: collapse;
}

.info-label {
    font-size: 14px;
    width: 150px;
    vertical-align: top;
    padding: 2px 0;
}

.info-colon {
    font-size: 14px;
    width: 14px;
    padding: 2px 6px;
    vertical-align: top;
}

.info-value {
    font-size: 14px;
    vertical-align: top;
    padding: 2px 0;
}

.section-title {
    font-size: 16px;
    font-weight: 700;
    text-align: center;
    letter-spacing: 1px;
    text-decoration: underline;
}

.soap-label {
    font-size: 14px;
    font-weight: 700;
    text-decoration: underline;
}

.soap-content {
    font-size: 14px;
    line-height: 1.6;
}

.print-section {
        page-break-inside: avoid;
        break-inside: avoid;
    }

.procedure-label {
    font-size: 14px;
    font-weight: 700;
    text-decoration: underline;
    width: 80px;
    flex-shrink: 0;
}

.footer-section {
    margin-top: auto;
    padding-top: 16px;
}

.footer-print-date {
    font-size: 12px;
}

.sign-role {
    font-size: 12px;
    font-weight: 700;
}

.qr-img-lg {
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

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
</style>