<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper mt-16">
                <div id="canvas" ref="canvas" style="position: relative;">
                    <table class="orthopedi-form">
                        <!-- ===================== HEADER ===================== -->
                        <tr>
                            <td colspan="4" class="header-cell">
                                <div class="header-row">
                                    <img src="../../../../../@core/assets/images/logo-thb.jpg" class="header-logo" alt="rs-logo">
                                    <div class="header-text">
                                        <div class="hospital-name">SIMRS DUMMY</div>
                                        <div class="hospital-address">Jl. Kaliabang Tengah No.2, RT 004/023 Pejuang, Medan Satria, Bekasi, Jawa Barat</div>
                                        <div class="hospital-phone">{{ hospital.phone }}</div>
                                        <div class="hospital-email">E-mail : {{ hospital.email }}</div>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- ===================== TITLE BAR ===================== -->
                        <tr>
                            <td colspan="4" class="form-title-cell">
                                PENILAIAN AWAL MEDIS RAWAT JALAN ORTHOPEDI
                            </td>
                        </tr>

                        <!-- ===================== IDENTITAS PASIEN ===================== -->
                        <tr>
                            <td class="identity-cell" style="width:33%;">
                                <div class="id-row"><span class="id-label">No. RM</span><span class="id-colon">:</span><span class="id-value">{{ patient.norm }}</span></div>
                                <div class="id-row"><span class="id-label">Nama Pasien</span><span class="id-colon">:</span><span class="id-value">{{ patient.name }}</span></div>
                            </td>
                            <td class="identity-cell" style="width:33%;">
                                <div class="id-row"><span class="id-label">Jenis Kelamin</span><span class="id-colon">:</span><span class="id-value">{{ patient.sex_name }}</span></div>
                                <div class="id-row"><span class="id-label">Tanggal Lahir</span><span class="id-colon">:</span><span class="id-value">{{ patient.birth_date }}</span></div>
                            </td>
                            <td class="identity-cell" style="width:34%;">
                                <div class="id-row"><span class="id-label">Tanggal</span><span class="id-colon">:</span><span class="id-value">{{ result.assessment_date }}</span></div>
                                <div class="id-row"><span class="id-label">Anamnesis</span><span class="id-colon">:</span><span class="id-value">{{ result.anamnesis_type }}</span></div>
                            </td>
                        </tr>

                        <!-- ===================== I. RIWAYAT KESEHATAN ===================== -->
                        <tr>
                            <td colspan="4" class="section-header-cell">I. RIWAYAT KESEHATAN</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="field-cell field-tall">
                                <span class="field-label">Keluhan Utama</span><span class="field-colon">:</span> {{ result.main_complaint }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="field-cell field-tall field-split">
                                <span class="field-label">Riwayat Penyakit Sekarang</span><span class="field-colon">:</span> {{ result.history_disease_now }}
                            </td>
                            <td colspan="2" class="field-cell field-tall field-split">
                                <span class="field-label">Riwayat Penyakit Dahulu</span><span class="field-colon">:</span> {{ result.history_disease_past }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="field-cell field-tall field-split">
                                <span class="field-label">Riwayat Penggunaan Obat</span><span class="field-colon">:</span> {{ result.history_drugs }}
                            </td>
                            <td colspan="2" class="field-cell field-tall field-split">
                                <span class="field-label">Riwayat Alergi</span><span class="field-colon">:</span> {{ result.history_allergy }}
                            </td>
                        </tr>

                        <!-- ===================== II. PEMERIKSAAN FISIK ===================== -->
                        <tr>
                            <td colspan="4" class="section-header-cell">II. PEMERIKSAAN FISIK</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="field-cell vitals-line">
                                <span class="field-label">Kesadaran</span><span class="field-colon">:</span> {{ getAwareName(result.aware) }} &nbsp;&nbsp;
                                <span class="field-label">TD</span><span class="field-colon">:</span> {{ result.systolic_blood_pressure }} mmHg &nbsp;&nbsp;
                                <span class="field-label">Nadi</span><span class="field-colon">:</span> {{ result.pulse_frequency }} x/menit &nbsp;&nbsp;
                                <span class="field-label">Suhu</span><span class="field-colon">:</span> {{ result.temperature }} °C &nbsp;&nbsp;
                                <span class="field-label">RR</span><span class="field-colon">:</span> {{ result.breath_frequency }} x/menit &nbsp;&nbsp;
                                <span class="field-label">Status Nutrisi</span><span class="field-colon">:</span> {{ result.nutrition_status }} &nbsp;&nbsp;
                                <span class="field-label">BB</span><span class="field-colon">:</span> {{ result.weight }} Kg &nbsp;&nbsp;
                                <span class="field-label">Nyeri</span><span class="field-colon">:</span> {{ result.sore_score }}
                                <br>
                                <span class="field-label">GCS(E,V,M)</span><span class="field-colon">:</span> {{ result.eye_score }}{{ result.verbal_score }}{{ result.motor_score }}
                            </td>
                        </tr>
                        <tr>
                            <td class="field-cell physical-cell" style="width:25%;">
                                <div><span class="field-label">Kepala</span><span class="field-colon">:</span> {{ result.head_text }}</div>
                                <div><span class="field-label">Thoraks</span><span class="field-colon">:</span> {{ result.thorax }}</div>
                                <div><span class="field-label">Abdomen</span><span class="field-colon">:</span> {{ result.abdomen_other_physical_check }}</div>
                                <div><span class="field-label">Ekstremitas</span><span class="field-colon">:</span> {{ result.extremity }}</div>
                            </td>
                            <td class="field-cell physical-cell" style="width:25%;">
                                <div><span class="field-label">Columna Vertebralis</span><span class="field-colon">:</span> {{ result.columna_vertebralis }}</div>
                                <div><span class="field-label">Muskuloskeletal</span><span class="field-colon">:</span> {{ result.muskuloskeletal }}</div>
                                <div><span class="field-label">Genetalia Os Pubis</span><span class="field-colon">:</span> {{ result.genetalia_os_pubis }}</div>
                            </td>
                            <td colspan="2" class="field-cell physical-cell">
                                <div><span class="field-label">Lainnya</span><span class="field-colon">:</span> {{ result.physical_other }}</div>
                            </td>
                        </tr>

                        <!-- ===================== III. STATUS LOKALIS ===================== -->
                        <tr>
                            <td colspan="4" class="section-header-cell">
                                <div class="d-flex justify-content-between">
                                    <span>III. STATUS LOKALIS</span>
                                    <span class="font-weight-normal">Keterangan : {{ result.localis_note }}</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="field-cell localis-image-cell">
                                <div v-if="editable.musLaki == true" class="w-100">
                                    <MuskuloskeletalLakiPrintView :drawing="muskuloskeletal_laki"/>
                                </div>
                                <div v-if="editable.musPr == true" class="w-100">
                                    <MuskuloskeletalPrPrintView :drawing="muskuloskeletal_pr"/>
                                </div>
                            </td>
                        </tr>

                        <!-- ===================== IV. PEMERIKSAAN PENUNJANG ===================== -->
                        <tr>
                            <td colspan="4" class="section-header-cell">IV. PEMERIKSAAN PENUNJANG</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="field-cell field-tall">
                                <span class="field-label">Laboratorium</span><span class="field-colon">:</span> {{ result.laboratory_note }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="field-cell field-tall">
                                <span class="field-label">Radiologi</span><span class="field-colon">:</span> {{ result.radiology_note }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="field-cell field-tall">
                                <span class="field-label">Penunjang Lainnya</span><span class="field-colon">:</span> {{ result.other_support_note }}
                            </td>
                        </tr>

                        <!-- ===================== V. DIAGNOSIS ===================== -->
                        <tr>
                            <td colspan="4" class="section-header-cell">V. DIAGNOSIS</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="field-cell field-tall field-split">
                                <span class="field-label">Asesmen Kerja</span><span class="field-colon">:</span> {{ result.working_assessment }}
                            </td>
                            <td colspan="2" class="field-cell field-tall field-split">
                                <span class="field-label">Asesmen Banding</span><span class="field-colon">:</span> {{ result.differential_assessment }}
                            </td>
                        </tr>

                        <!-- ===================== VI. TATALAKSANA ===================== -->
                        <tr>
                            <td colspan="4" class="section-header-cell">VI. TATALAKSANA</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="field-cell field-tall field-split">
                                <span class="field-label">Permasalahan</span><span class="field-colon">:</span> {{ result.problem }}
                            </td>
                            <td colspan="2" class="field-cell field-tall field-split">
                                <span class="field-label">Terapi/Pengobatan</span><span class="field-colon">:</span> {{ result.therapy_plan }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="field-cell field-tall">
                                <span class="field-label">Tindakan/Rencana Tindakan</span><span class="field-colon">:</span> {{ result.action_plan }}
                            </td>
                        </tr>

                        <!-- ===================== VII. EDUKASI ===================== -->
                        <tr>
                            <td colspan="4" class="section-header-cell">VII. EDUKASI</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="field-cell field-tall">
                                {{ result.education }}
                            </td>
                        </tr>

                        <!-- ===================== FOOTER / TTD ===================== -->
                        <tr>
                            <td colspan="2" class="footer-header-cell">Tanggal dan Jam</td>
                            <td colspan="2" class="footer-header-cell">Nama Dokter dan Tanda Tangan</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="footer-value-cell">
                                {{ localDate(result.assessment_date) }}
                            </td>
                            <td colspan="2" class="footer-value-cell">
                                <template v-if="globalQRText">
                                    <div class="my-2" id="renderQR">
                                        <qrcode-vue id="qrcodeCanvas" :value="globalQRText" :size="80" level="H"></qrcode-vue>
                                    </div>
                                </template>
                                <div class="doctor-name">{{ result.employee_doctor_name }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </b-container>
    </div>
</template>

<script>
import moment from 'moment';
import QrcodeVue from 'qrcode.vue';
import html2canvas from 'html2canvas';
import MuskuloskeletalLakiPrintView from '@/views/pages/canvasing/jsView/MuskuloskeletalLakiPrintView.vue'
import MuskuloskeletalPrPrintView from '@/views/pages/canvasing/jsView/MuskuloskeletalPrPrintView.vue'

export default {
    components: {
        QrcodeVue, MuskuloskeletalLakiPrintView, MuskuloskeletalPrPrintView
    },
    data() {
        return {
            editable: {
				ekstrimAtas: false,
				ekstrimBawah: false,
				kepala: false,
				musLaki: false,
				musPr: false,
			},
            muskuloskeletal_laki:'',
            muskuloskeletal_pr:'',
            result: {},
            patient: {},
            globalQRText: '',
            hospital: {
                phone: '021-8898 1055',
                email: 'rs_thb@yahoo.com'
            },
            isPrinting: false,

            OptionsAware: [
                { value: 3, title: 'Tidak' },
                { value: 0, title: 'Compos Mentis' },
            ],
        }
    },
    mounted() {
        this.getData(this.$route.params.soap_id)
        window.addEventListener('keydown', this.handleKeyPress);
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
    methods: {
        getAwareName(value) {
            const result = this.OptionsAware.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD/MM/YYYY HH:mm:ss');
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
        async printCanvas() {
            if (this.isPrinting) return;
            this.isPrinting = true;

            try {
                const target = this.$refs.canvas;

                // screenshot seluruh area form (termasuk isi canvas Muskuloskeletal apapun
                // struktur internalnya) menjadi satu gambar flat, agar tidak bergantung
                // pada rendering <canvas> asli yang sering gagal saat print/PDF
                const rendered = await html2canvas(target, {
                    useCORS: true,
                    allowTaint: true,
                    scale: 2, // resolusi lebih tinggi supaya tidak pecah saat dicetak
                    backgroundColor: '#ffffff'
                });

                const dataUrl = rendered.toDataURL('image/png');

                const printWindow = window.open('', '_blank');
                if (!printWindow) {
                    alert('Popup diblokir browser. Mohon izinkan popup untuk mencetak.');
                    this.isPrinting = false;
                    return;
                }

                printWindow.document.write(`
                    <html>
                    <head>
                        <title>Print</title>
                        <style>
                            @page { size: A4; margin: 10mm; }
                            html, body { margin: 0; padding: 0; }
                            img { width: 100%; display: block; }
                        </style>
                    </head>
                    <body>
                        <img src="${dataUrl}" />
                    </body>
                    </html>
                `);
                printWindow.document.close();

                const img = printWindow.document.querySelector('img');
                const triggerPrint = () => {
                    printWindow.focus();
                    printWindow.print();
                };

                if (img.complete) {
                    triggerPrint();
                } else {
                    img.onload = triggerPrint;
                }

                printWindow.onafterprint = () => {
                    printWindow.close();
                };
            } catch (err) {
                console.error('Gagal mencetak:', err);
                alert('Gagal mencetak dokumen. Silakan coba lagi.');
            } finally {
                this.isPrinting = false;
            }
        },
        getData(soap_id) {
            this.$store.dispatch('soap/getBySoapId', soap_id).then(data => {
                this.result = data[0]
                this.muskuloskeletal_laki = data[0]?.canvas?.muskuloskeletal_laki || ''
                if (data[0]?.canvas?.muskuloskeletal_laki) {
                    this.editable.musLaki = true
                }
                this.muskuloskeletal_pr = data[0]?.canvas?.muskuloskeletal_pr || ''
                if (data[0]?.canvas?.muskuloskeletal_pr) {
                    this.editable.musPr = true
                }
                this.$store.dispatch('registration/getById', this.result.registration_destination_id).then(data => {
                    this.patient = data[0]
                    this.globalQRText = this.patient.norm
                })
            })
        },
    },
};
</script>

<style>
@import './../../../../../@core/css/main/main.min.css';

@media print {
    @page {
        size: A4;
        margin: 15mm;
    }

    html, body {
        margin: 0 !important;
        padding: 0 !important;
    }

    /* buang padding bawaan bootstrap container agar tabel full width persis lebar halaman */
    .container,
    .container-fluid,
    .main-content {
        margin: 0 !important;
        padding: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
    }

    .canvas-wrapper {
        padding: 0 !important;
        margin: 0 !important;
        display: block !important;
        justify-content: initial !important;
    }

    #canvas {
        width: 100% !important;
    }

    /* sembunyikan tombol Tutup/Print kalau print dipicu langsung via Ctrl+P browser,
       bukan lewat printCanvas() yang sudah membuang elemen ini dari body */
    .main-content > .d-flex:first-child {
        display: none !important;
    }

    .orthopedi-form {
        width: 100% !important;
    }
}

/* ===================== BASE FORM ===================== */
.orthopedi-form,
.orthopedi-form * {
    box-sizing: border-box;
    line-height: 1.25;
}

.orthopedi-form {
    width: 100%;
    border-collapse: collapse;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11px;
    color: #000;
}

.orthopedi-form p,
.orthopedi-form div,
.orthopedi-form span {
    margin: 0;
    padding: 0;
}

.orthopedi-form td {
    border: 1px solid #000;
    padding: 2px 5px;
    vertical-align: top;
}

/* ===================== HEADER ===================== */
.header-cell {
    border: none !important;
    padding: 4px 0 6px 0 !important;
    border-bottom: 2px solid #000 !important;
}

.header-row {
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    text-align: center;
}

.header-logo {
    position: absolute;
    left: 0;
    width: 55px;
}

.header-text {
    width: 100%;
}

.hospital-name {
    font-size: 16px;
    font-weight: bold;
}

.hospital-address,
.hospital-phone,
.hospital-email {
    font-size: 10px;
}

/* ===================== TITLE BAR ===================== */
.form-title-cell {
    text-align: center;
    font-weight: bold;
    font-size: 12px;
    border-left: 1px solid #000 !important;
    border-right: 1px solid #000 !important;
    padding: 3px 0 !important;
}

/* ===================== IDENTITY ===================== */
.identity-cell {
    font-size: 11px;
}

.id-row {
    display: flex;
}

.id-label {
    width: 85px;
    flex-shrink: 0;
}

.id-colon {
    width: 10px;
    flex-shrink: 0;
}

.id-value {
    flex: 1;
}

/* ===================== SECTION HEADERS ===================== */
.section-header-cell {
    font-weight: bold;
    font-size: 11px;
    padding: 2px 5px !important;
}

/* ===================== FIELD CELLS ===================== */
.field-cell {
    font-size: 11px;
}

.field-tall {
    height: 40px;
}

.field-split {
    width: 50%;
}

.field-label {
    display: inline;
}

.field-colon {
    margin: 0 4px 0 2px;
}

.vitals-line {
    height: auto;
    line-height: 1.5;
}

.physical-cell > div {
    margin-bottom: 1px;
}

/* ===================== STATUS LOKALIS ===================== */
.localis-image-cell {
    height: 160px;
    /* gambar tubuh (lokalis) sengaja dikosongkan sesuai permintaan */
}

/* ===================== FOOTER ===================== */
.footer-header-cell {
    text-align: center;
    font-weight: bold;
    font-size: 11px;
    padding: 4px !important;
}

.footer-value-cell {
    text-align: center;
    font-size: 11px;
    height: 85px;
    padding: 5px !important;
}

.footer-value-cell .doctor-name {
    margin-top: 45px;
}

/* ===================== MISC (kept from base layout) ===================== */
.main-content {
    margin-top: 32px;
    padding-bottom: 32px;
}

.canvas-wrapper {
    display: flex;
    justify-content: center;
    padding-bottom: 40px;
}
</style>