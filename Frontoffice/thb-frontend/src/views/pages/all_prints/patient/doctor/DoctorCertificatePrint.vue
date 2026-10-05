<template>
    <div class="d-flex w-100 justify-content-center">
        <div>
            <div class="d-flex justify-content-end py-20" ref="buttons">
                <b-button @click="closeWindow" size="sm" variant="danger-400" class="px-32">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" variant="success-500" class="px-32 ml-2">Print</b-button>
            </div>
            <section id="canvas" ref="canvas" class="p-20">
                <div class="d-flex align-items-center justify-content-center header-section">
                    <img src="../../../../../@core/assets/images/logo-thb.jpg" width="70px" alt="logo-thb" class="mr-16">
                    <div class="text-center">
                        <div class="hospital-name">RS Taman Harapan Baru</div>
                        <div class="hospital-address">Jl. Kali Abang Tengah No.2, RT 004/023 Pejuang, Medan Satria, Bekasi, Jawa</div>
                        <div class="hospital-address">Telp. 021-8898 1055 &nbsp;|&nbsp; E-mail : rs_thb@yahoo.com</div>
                    </div>
                </div>

                <div class="divider-line my-8"></div>

                <div class="text-center title-surat my-8">SURAT KETERANGAN</div>

                <table class="info-table">
                    <tr>
                        <td class="info-label">NO. SURAT</td>
                        <td class="info-colon">:</td>
                        <td class="info-value">{{ regData.no_surat }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">NO. RM</td>
                        <td class="info-colon">:</td>
                        <td class="info-value">{{ regData.norm }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">NAMA</td>
                        <td class="info-colon">:</td>
                        <td class="info-value">{{ regData.name }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">DIAGNOSA</td>
                        <td class="info-colon">:</td>
                        <td class="info-value">{{ soapData.medical_diagnosis }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">TERAPI</td>
                        <td class="info-colon">:</td>
                        <td class="info-value">{{ soapData.therapy_plan }}</td>
                    </tr>
                </table>

                <div class="body-text mt-12">
                    Tanggal surat rujukan {{ regData.tanggal_rujukan }}
                </div>
                <div class="body-text">
                    Belum dapat dikembalikan ke Fasilitas Perujuk dengan alasan
                </div>
                <div class="body-text">1. {{ regData.alasan_1 }}</div>
                <div class="body-text">2. {{ regData.alasan_2 }}</div>

                <div class="body-text mt-12">
                    Rencana tindak lanjut yang akan diberikan pada kunjungan berikutnya
                </div>
                <div class="body-text">1. {{ regData.rencana_1 }}</div>
                <div class="body-text">2. {{ regData.rencana_2 }}</div>

                <div class="body-text mt-12">
                    Surat keterangan ini digunakan untuk 1 (satu) kali kunjungan dengan diagnosa di atas pada :
                </div>
                <div class="body-text">
                    Tanggal {{ regData.tanggal_kunjungan }} di Rehab Medik
                </div>

                <div class="d-flex justify-content-between align-items-end mt-16">
                    <div class="text-left">
                        <div class="antrian-label">No.Antrian ({{ regData.code_queue }} - {{ regData.queue }})</div>
                        <div class="barcode-area mt-4">
                            <svg ref="barcodeSvg"></svg>
                        </div>
                    </div>

                    <div class="text-center sign-block">
                        <div class="body-text">Bekasi,</div>
                        <div class="body-text">Dokter</div>
                        <div class="mt-4">
                            <img v-if="qrDoctor" :src="qrDoctor" class="qr-img" alt="QR Dokter">
                            <div v-else class="qr-placeholder"></div>
                        </div>
                        <div class="body-text">( {{ regData.doctor_in_charge_name }} )</div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
import JsBarcode from 'jsbarcode';
import moment from 'moment';
import QRCode from 'qrcode';

export default {
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            qrDoctor: '',
            soapData: {},
            regData: {},
        }
    },

    methods: {
        formatDateTime(dt) {
            if (!dt) return '-';
            return moment(dt).isValid() ? moment(dt).format('DD-MM-YYYY HH:mm:ss') : dt;
        },

        async getData (soap_id) {
            this.$store.dispatch('soap/getBySoapId', soap_id).then((data) => {
                this.soapData = data[0]

                this.$store.dispatch('registration/getById', this.soapData.registration_destination_id).then(data => {
                    this.regData = data[0]
                })
                    this.$nextTick(async () => {
                        this.renderBarcode();
                        await this.generateAllQRCodes()
                    });
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
            if (this.regData) {
                try {
                    const payload = this.buildPayload(this.regData.doctor_in_charge_name)
                    this.qrDoctor = await this.generateQR(payload)
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

        printCanvas() {
            const canvasElement = document.querySelector('#canvas');
            const buttons = this.$refs.buttons;

            buttons.classList.add('d-none');

            const printContent = canvasElement.outerHTML;
            const originalContent = document.body.innerHTML;

            document.body.innerHTML = `
                <style>
                    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
                    body { margin: 0; padding: 0; }
                    #canvas { width: 148mm; min-height: 210mm; padding: 10mm; box-sizing: border-box; font-family: 'Inter', sans-serif; }
                    .hospital-name { font-size: 14px; font-weight: 700; }
                    .hospital-address { font-size: 8px; }
                    .title-surat { font-size: 13px; font-weight: 700; letter-spacing: 1px; }
                    .divider-line { border-top: 2px solid black; }
                    .info-table { width: 100%; font-size: 10px; border-collapse: collapse; }
                    .info-label { width: 80px; font-weight: 600; vertical-align: top; padding: 1px 0; }
                    .info-colon { width: 12px; vertical-align: top; padding: 1px 4px; }
                    .info-value { vertical-align: top; padding: 1px 0; }
                    .body-text { font-size: 10px; line-height: 1.6; }
                    .antrian-label { font-size: 22px; font-weight: 900; }
                    .sign-block { min-width: 120px; }
                    .qr-box { width: 70px; height: 70px; border: 1px solid #ccc; }
                    .mt-4 { margin-top: 4px; }
                    .mt-8 { margin-top: 8px; }
                    .mt-12 { margin-top: 12px; }
                    .mt-16 { margin-top: 16px; }
                    .my-8 { margin-top: 8px; margin-bottom: 8px; }
                    .mr-16 { margin-right: 16px; }
                    .mb-4 { margin-bottom: 4px; }
                    .d-flex { display: flex; }
                    .align-items-center { align-items: center; }
                    .align-items-end { align-items: flex-end; }
                    .justify-content-center { justify-content: center; }
                    .justify-content-between { justify-content: space-between; }
                    .text-center { text-align: center; }
                    .text-left { text-align: left; }
                    .header-section { border-bottom: 0; }
                </style>
                ${printContent}
            `;

            window.print();

            document.body.innerHTML = originalContent;

            this.$nextTick(async () => {
                buttons.classList.remove('d-none');
                this.renderBarcode();
                await this.generateAllQRCodes()
            });
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

#canvas {
    width: 148mm;
    min-height: 180mm;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.15);
    background-color: white;
    display: block;
    padding: 10mm;
    box-sizing: border-box;
}

.hospital-name {
    font-size: 16px;
    font-weight: 700;
    line-height: 1.3;
}

.hospital-address {
    font-size: 10px;
    line-height: 1.5;
}

.divider-line {
    border: none;
    border-top: 2px solid black;
}

.title-surat {
    font-size: 16px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-decoration: underline;
}

.info-table {
    width: 100%;
    border-collapse: collapse;
}

.info-label {
    font-size: 12px;
    font-weight: 600;
    width: 80px;
    vertical-align: top;
    padding: 2px 0;
}

.info-colon {
    font-size: 12px;
    width: 14px;
    padding: 2px 4px;
    vertical-align: top;
}

.info-value {
    font-size: 12px;
    vertical-align: top;
    padding: 2px 0;
}

.body-text {
    font-size: 12px;
    line-height: 1.7;
}

.antrian-label {
    font-size: 26px;
    font-weight: 900;
    letter-spacing: -0.5px;
}

.sign-block {
    min-width: 130px;
}

.qr-box {
    width: 70px;
    height: 70px;
    border: 1px solid #bbb;
    display: inline-block;
}

.barcode-area svg {
    display: block;
}
</style>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap');
</style>
