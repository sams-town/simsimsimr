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
                                PENILAIAN AWAL MEDIS RAWAT JALAN MATA
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

                        <!-- ===================== III. STATUS OFTALMOLOGIS ===================== -->
                        <tr>
                            <td colspan="4" class="section-header-cell">III. STATUS OFTAMOLOGIS</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="field-cell localis-image-cell">
                                <div class="w-100 d-flex justify-content-end">
                                    <MataPrintView :drawing="mata"/>
                                </div>
                                <b-table-simple bordered responsive class="mt-2">
                                    <b-thead>
                                        <b-tr class="text-center">
                                            <b-th style="width: calc(100%/3)" class="bg-warning-200">OD (Oculus Dextra)</b-th>
                                            <b-th style="width: calc(100%/3)" class="bg-primary-100">STATUS OFTAMOLIGIS</b-th>
                                            <b-th style="width: calc(100%/3)" class="bg-warning-200">OS (Oculus Sinitra)</b-th>
                                        </b-tr>
                                    </b-thead>
                                    <b-tbody>
                                        <!-- Visus SC -->
                                        <b-tr>
                                            <b-td>{{ getOptionTitle(OptionsOftalVisus, result.ophthal_od.visus) }}</b-td>
                                            <b-th class="text-center bg-primary-100">Visus SC</b-th>
                                            <b-td>{{ getOptionTitle(OptionsOftalVisus, result.ophthal_os.visus) }}</b-td>
                                        </b-tr>
                                        <!-- CC -->
                                        <b-tr>
                                            <b-td>{{ result.ophthal_od.cc || '-' }}</b-td>
                                            <b-th class="text-center bg-primary-100">CC</b-th>
                                            <b-td>{{ result.ophthal_os.cc || '-' }}</b-td>
                                        </b-tr>
                                        <!-- Palpebra (dipisah per sub-field) -->
                                        <b-tr>
                                            <b-td>{{ getOptionTitle(OptionsOftalPalpebraEdema, result.ophthal_od.palpebra_edema) }}</b-td>
                                            <b-th class="text-center bg-primary-100">Palpebra - Edema</b-th>
                                            <b-td>{{ getOptionTitle(OptionsOftalPalpebraEdema, result.ophthal_os.palpebra_edema) }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>{{ getOptionTitle(OptionsOftalPalpebraPtosis, result.ophthal_od.palpebra_ptosis) }}</b-td>
                                            <b-th class="text-center bg-primary-100">Palpebra - Ptosis</b-th>
                                            <b-td>{{ getOptionTitle(OptionsOftalPalpebraPtosis, result.ophthal_os.palpebra_ptosis) }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>{{ getOptionTitle(OptionsOftalPalpebraLagophtalmus, result.ophthal_od.palpebra_lagophtalmus) }}</b-td>
                                            <b-th class="text-center bg-primary-100">Palpebra - Lagophtalmus</b-th>
                                            <b-td>{{ getOptionTitle(OptionsOftalPalpebraLagophtalmus, result.ophthal_os.palpebra_lagophtalmus) }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>{{ getOptionTitle(OptionsOftalPalpebraTumor, result.ophthal_od.palpebra_tumor) }}</b-td>
                                            <b-th class="text-center bg-primary-100">Palpebra - Tumor</b-th>
                                            <b-td>{{ getOptionTitle(OptionsOftalPalpebraTumor, result.ophthal_os.palpebra_tumor) }}</b-td>
                                        </b-tr>
                                        <!-- Conjungtiva (dipisah per sub-field) -->
                                        <b-tr>
                                            <b-td>{{ getOptionTitle(OptionsOftalConjHiperemis, result.ophthal_od.conjunctive_hiperemis) }}</b-td>
                                            <b-th class="text-center bg-primary-100">Conjungtiva - Hiperemis</b-th>
                                            <b-td>{{ getOptionTitle(OptionsOftalConjHiperemis, result.ophthal_os.conjunctive_hiperemis) }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>{{ getOptionTitle(OptionsOftalConjBleed, result.ophthal_od.conjunctive_bleed) }}</b-td>
                                            <b-th class="text-center bg-primary-100">Conjungtiva - Pendarahan</b-th>
                                            <b-td>{{ getOptionTitle(OptionsOftalConjBleed, result.ophthal_os.conjunctive_bleed) }}</b-td>
                                        </b-tr>
                                        <!-- Cornea -->
                                        <b-tr>
                                            <b-td>{{ getOptionTitle(OptionsOftalKornea, result.ophthal_od.cornea) }}</b-td>
                                            <b-th class="text-center bg-primary-100">Cornea</b-th>
                                            <b-td>{{ getOptionTitle(OptionsOftalKornea, result.ophthal_os.cornea) }}</b-td>
                                        </b-tr>
                                        <!-- COA -->
                                        <b-tr>
                                            <b-td>{{ getOptionTitle(OptionsOftalAnterior, result.ophthal_od.camera_oculi) }}</b-td>
                                            <b-th class="text-center bg-primary-100">COA</b-th>
                                            <b-td>{{ getOptionTitle(OptionsOftalAnterior, result.ophthal_os.camera_oculi) }}</b-td>
                                        </b-tr>
                                        <!-- Iris/Pupil (dipisah per sub-field) -->
                                        <b-tr>
                                            <b-td>{{ getOptionTitle(OptionsOftalIrisColor, result.ophthal_od.iris_color) }}</b-td>
                                            <b-th class="text-center bg-primary-100">Iris - Warna</b-th>
                                            <b-td>{{ getOptionTitle(OptionsOftalIrisColor, result.ophthal_os.iris_color) }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>{{ getOptionTitle(OptionsOftalIrisKripte, result.ophthal_od.iris_cripte) }}</b-td>
                                            <b-th class="text-center bg-primary-100">Iris - Kripte</b-th>
                                            <b-td>{{ getOptionTitle(OptionsOftalIrisKripte, result.ophthal_os.iris_cripte) }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>{{ getOptionTitle(OptionsOftalPupilForm, result.ophthal_od.pupil_form) }}</b-td>
                                            <b-th class="text-center bg-primary-100">Pupil - Bentuk</b-th>
                                            <b-td>{{ getOptionTitle(OptionsOftalPupilForm, result.ophthal_os.pupil_form) }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>{{ getOptionTitle(OptionsOftalPupilDiameter, result.ophthal_od.pupil_diameter) }}</b-td>
                                            <b-th class="text-center bg-primary-100">Pupil - Diameter</b-th>
                                            <b-td>{{ getOptionTitle(OptionsOftalPupilDiameter, result.ophthal_os.pupil_diameter) }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>{{ getOptionTitle(OptionsOftalPupilDirectLight, result.ophthal_od.pupil_direct_light) }}</b-td>
                                            <b-th class="text-center bg-primary-100">Pupil - Refleksi Langsung</b-th>
                                            <b-td>{{ getOptionTitle(OptionsOftalPupilDirectLight, result.ophthal_os.pupil_direct_light) }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>{{ getOptionTitle(OptionsOftalPupilIndirectLight, result.ophthal_od.pupil_indirect_light) }}</b-td>
                                            <b-th class="text-center bg-primary-100">Pupil - Refleksi Tak Langsung</b-th>
                                            <b-td>{{ getOptionTitle(OptionsOftalPupilIndirectLight, result.ophthal_os.pupil_indirect_light) }}</b-td>
                                        </b-tr>
                                        <!-- Lensa -->
                                        <b-tr>
                                            <b-td>{{ getOptionTitle(OptionsOftalLens, result.ophthal_od.lens) }}</b-td>
                                            <b-th class="text-center bg-primary-100">Lensa</b-th>
                                            <b-td>{{ getOptionTitle(OptionsOftalLens, result.ophthal_os.lens) }}</b-td>
                                        </b-tr>
                                        <!-- Fundus Media -->
                                        <b-tr>
                                            <b-td>{{ result.ophthal_od.fundus || '-' }}</b-td>
                                            <b-th class="text-center bg-primary-100">Fundus Media</b-th>
                                            <b-td>{{ result.ophthal_os.fundus || '-' }}</b-td>
                                        </b-tr>
                                        <!-- Papil (belum ada di data) -->
                                        <b-tr>
                                            <b-td>-</b-td>
                                            <b-th class="text-center bg-primary-100">Papil</b-th>
                                            <b-td>-</b-td>
                                        </b-tr>
                                        <!-- Retina (belum ada di data) -->
                                        <b-tr>
                                            <b-td>-</b-td>
                                            <b-th class="text-center bg-primary-100">Retina</b-th>
                                            <b-td>-</b-td>
                                        </b-tr>
                                        <!-- Makula (belum ada di data) -->
                                        <b-tr>
                                            <b-td>-</b-td>
                                            <b-th class="text-center bg-primary-100">Makula</b-th>
                                            <b-td>-</b-td>
                                        </b-tr>
                                        <!-- TIO (dipisah per sub-field) -->
                                        <b-tr>
                                            <b-td>{{ getOptionTitle(OptionsOftalTIO, result.ophthal_od.tio_digital) }}</b-td>
                                            <b-th class="text-center bg-primary-100">TIO - Digital</b-th>
                                            <b-td>{{ getOptionTitle(OptionsOftalTIO, result.ophthal_os.tio_digital) }}</b-td>
                                        </b-tr>
                                        <b-tr>
                                            <b-td>{{ result.ophthal_od.tio_tonometri_sciots || '-' }}</b-td>
                                            <b-th class="text-center bg-primary-100">TIO - Tonometri Sciotz</b-th>
                                            <b-td>{{ result.ophthal_os.tio_tonometri_sciots || '-' }}</b-td>
                                        </b-tr>
                                        <!-- MBO (belum ada di data) -->
                                        <b-tr>
                                            <b-td>-</b-td>
                                            <b-th class="text-center bg-primary-100">MBO</b-th>
                                            <b-td>-</b-td>
                                        </b-tr>
                                    </b-tbody>
                                </b-table-simple>
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
import { mapState } from 'vuex'
import moment from 'moment';
import QrcodeVue from 'qrcode.vue';
import html2canvas from 'html2canvas';
import MataPrintView from '@/views/pages/canvasing/jsView/MataPrintView.vue'

export default {
    components: {
        QrcodeVue, MataPrintView
    },
    data() {
        return {
            editable: {
				ekstrimAtas: false,
				ekstrimBawah: false,
				kepala: false,
				musLaki: false,
				musPr: false,
				eye: false,
			},
            mata:'',
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
    computed: {
        ...mapState({ 
            //SPRI
            TypeRoomOptions: state => state.helper.options.type_room,

            items: state => state.soap.items, 
            OptionsCosting: state => state.costing.options.costing,
            PackageOptions: state => state.packages.options.packages,
            OptionsDoctor: state => state.employee.options.employee,            
            OptionsSpecialistDoctor: state => state.employee.options.specialist,         
            OptionsTypeAllergy: state => state.helper.options.typeallergy,
                        
            // generalis option
            OptionsHeadCircumference: state => state.soap.generalis.OptionsHeadCircumference,
            OptionsHeadHairColor: state => state.soap.generalis.OptionsHeadHairColor,
            OptionsHeadHairCondition: state => state.soap.generalis.OptionsHeadHairCondition,
            OptionsEyeConjunctiva: state => state.soap.generalis.OptionsEyeConjunctiva,
            OptionsEyeSclera: state => state.soap.generalis.OptionsEyeSclera,
            OptionsEyePupil: state => state.soap.generalis.OptionsEyePupil,
            OptionsEyeReflect: state => state.soap.generalis.OptionsEyeReflect,
            OptionsMouthMukosa: state => state.soap.generalis.OptionsMouthMukosa,
            OptionsMouthHiperemis: state => state.soap.generalis.OptionsMouthHiperemis,
            OptionsMouthTongue: state => state.soap.generalis.OptionsMouthTongue,
            OptionsMouthKaries: state => state.soap.generalis.OptionsMouthKaries,
            OptionsNeckKGB: state => state.soap.generalis.OptionsNeckKGB,
            OptionsNeckTrakea: state => state.soap.generalis.OptionsNeckTrakea,
            OptionsNeckMassa: state => state.soap.generalis.OptionsNeckMassa,
            OptionsInspectionChest: state => state.soap.generalis.OptionsInspectionChest,
            OptionsInspectionRetraction: state => state.soap.generalis.OptionsInspectionRetraction,
            OptionsPalpationChest: state => state.soap.generalis.OptionsPalpationChest,
            OptionsPalpationFremitus: state => state.soap.generalis.OptionsPalpationFremitus,
            OptionsPalpationKrepitasi: state => state.soap.generalis.OptionsPalpationKrepitasi,
            OptionsAuskultationPulmoVasikuler: state => state.soap.generalis.OptionsAuskultationPulmoVasikuler,
            OptionsAuskultationPulmoRhonki: state => state.soap.generalis.OptionsAuskultationPulmoRhonki,
            OptionsAuskultationPulmoWheezing: state => state.soap.generalis.OptionsAuskultationPulmoWheezing,
            OptionsAuskultationCORBJ: state => state.soap.generalis.OptionsAuskultationCORBJ,
            OptionsAuskultationCORMurmur: state => state.soap.generalis.OptionsAuskultationCORMurmur,
            OptionsAuskultationCORGallop: state => state.soap.generalis.OptionsAuskultationCORGallop,
            OptionsAbdomenSupel: state => state.soap.generalis.OptionsAbdomenSupel,
            OptionsAbdomenBising: state => state.soap.generalis.OptionsAbdomenBising,
            OptionsAbdomenPress: state => state.soap.generalis.OptionsAbdomenPress,
            OptionsAbdomenHepar: state => state.soap.generalis.OptionsAbdomenHepar,
            OptionsEkstremitasAkral: state => state.soap.generalis.OptionsEkstremitasAkral,
            OptionsEkstremitasCRT: state => state.soap.generalis.OptionsEkstremitasCRT,
            OptionsEkstremitasEdema: state => state.soap.generalis.OptionsEkstremitasEdema,
            OptionsOftalVisus: state => state.soap.generalis.OptionsOftalVisus,
            OptionsOftalTIO: state => state.soap.generalis.OptionsOftalTIO,
            OptionsOftalEyePosition: state => state.soap.generalis.OptionsOftalEyePosition,
            OptionsOftalMovement: state => state.soap.generalis.OptionsOftalMovement,
            OptionsOftalSuperSilia: state => state.soap.generalis.OptionsOftalSuperSilia,
            OptionsOftalPalpebraEdema: state => state.soap.generalis.OptionsOftalPalpebraEdema,
            OptionsOftalPalpebraPtosis: state => state.soap.generalis.OptionsOftalPalpebraPtosis,
            OptionsOftalPalpebraLagophtalmus: state => state.soap.generalis.OptionsOftalPalpebraLagophtalmus,
            OptionsOftalPalpebraTumor: state => state.soap.generalis.OptionsOftalPalpebraTumor,
            OptionsOftalApparetus: state => state.soap.generalis.OptionsOftalApparetus,
            OptionsOftalSilia: state => state.soap.generalis.OptionsOftalSilia,
            OptionsOftalConjHiperemis: state => state.soap.generalis.OptionsOftalConjHiperemis,
            OptionsOftalConjBleed: state => state.soap.generalis.OptionsOftalConjBleed,
            OptionsOftalKornea: state => state.soap.generalis.OptionsOftalKornea,
            OptionsOftalAnterior: state => state.soap.generalis.OptionsOftalAnterior,
            OptionsOftalIrisColor: state => state.soap.generalis.OptionsOftalIrisColor,
            OptionsOftalIrisKripte: state => state.soap.generalis.OptionsOftalIrisKripte,
            OptionsOftalPupilForm: state => state.soap.generalis.OptionsOftalPupilForm,
            OptionsOftalPupilDiameter: state => state.soap.generalis.OptionsOftalPupilDiameter,
            OptionsOftalPupilDirectLight: state => state.soap.generalis.OptionsOftalPupilDirectLight,
            OptionsOftalPupilIndirectLight: state => state.soap.generalis.OptionsOftalPupilIndirectLight,
            OptionsOftalLens: state => state.soap.generalis.OptionsOftalLens,
            OptionsOftalGlandula: state => state.soap.generalis.OptionsOftalGlandula,
            OptionsOftalLapang: state => state.soap.generalis.OptionsOftalLapang,
            OptionsOftalShadow: state => state.soap.generalis.OptionsOftalShadow,
        }),
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
    methods: {
        getAwareName(value) {
            const result = this.OptionsAware.find((item) => item.value == value);
            return result ? result.title : '-';
        },
        // helper generik: cari 'title' dari sebuah Options array berdasar value tersimpan.
        // dipakai di tabel STATUS OFTAMOLOGIS supaya tampil sebagai teks, bukan dropdown.
        getOptionTitle(options, value) {
            if (value === null || value === undefined || value === '') return '-';
            const found = (options || []).find((item) => item.value == value);
            return found ? found.title : '-';
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

                const rendered = await html2canvas(target, {
                    useCORS: true,
                    allowTaint: true,
                    scale: 2,
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

                this.result.ophthal_od.visus = Number(data[0]?.ophthal_od?.visus) ?? 12
                this.result.ophthal_od.tio_digital = Number(data[0]?.ophthal_od?.tio_digital) ?? 1
                this.result.ophthal_od.tio_tonometri_sciots = data[0]?.ophthal_od?.tio_tonometri_sciots || ''
                this.result.ophthal_od.eye_position = Number(data[0]?.ophthal_od?.eye_position) ?? 1
                this.result.ophthal_od.eye_movement = Number(data[0]?.ophthal_od?.eye_movement) ?? 1
                this.result.ophthal_od.super_silia = Number(data[0]?.ophthal_od?.super_silia) ?? 1
                this.result.ophthal_od.palpebra_edema = Number(data[0]?.ophthal_od?.palpebra_edema) ?? 1
                this.result.ophthal_od.palpebra_ptosis = Number(data[0]?.ophthal_od?.palpebra_ptosis) ?? 1
                this.result.ophthal_od.palpebra_lagophtalmus = Number(data[0]?.ophthal_od?.palpebra_lagophtalmus) ?? 1
                this.result.ophthal_od.palpebra_tumor = Number(data[0]?.ophthal_od?.palpebra_tumor) ?? 1
                this.result.ophthal_od.apparatus_lakrimalis = Number(data[0]?.ophthal_od?.apparatus_lakrimalis) ?? 1
                this.result.ophthal_od.silia = Number(data[0]?.ophthal_od?.silia) ?? 1
                this.result.ophthal_od.conjunctive_hiperemis = Number(data[0]?.ophthal_od?.conjunctive_hiperemis) ?? 1
                this.result.ophthal_od.conjunctive_bleed = Number(data[0]?.ophthal_od?.conjunctive_bleed) ?? 1
                this.result.ophthal_od.cornea = Number(data[0]?.ophthal_od?.cornea) ?? 1
                this.result.ophthal_od.camera_oculi = Number(data[0]?.ophthal_od?.camera_oculi) ?? 1
                this.result.ophthal_od.iris_color = Number(data[0]?.ophthal_od?.iris_color) ?? 2
                this.result.ophthal_od.iris_cripte = Number(data[0]?.ophthal_od?.iris_cripte) ?? 1
                this.result.ophthal_od.pupil_form = Number(data[0]?.ophthal_od?.pupil_form) ?? 1
                this.result.ophthal_od.pupil_diameter = Number(data[0]?.ophthal_od?.pupil_diameter) ?? 4
                this.result.ophthal_od.pupil_direct_light = Number(data[0]?.ophthal_od?.pupil_direct_light) ?? 1
                this.result.ophthal_od.pupil_indirect_light = Number(data[0]?.ophthal_od?.pupil_indirect_light) ?? 1
                this.result.ophthal_od.lens = Number(data[0]?.ophthal_od?.lens) ?? 1
                this.result.ophthal_od.shadow_test = Number(data[0]?.ophthal_od?.shadow_test) ?? 1
                this.result.ophthal_od.glandula = Number(data[0]?.ophthal_od?.glandula) ?? 1
                this.result.ophthal_od.field_view = Number(data[0]?.ophthal_od?.field_view) ?? 1
                this.result.ophthal_od.fundus = data[0]?.ophthal_od?.fundus || ''
                this.result.ophthal_od.cc = data[0]?.ophthal_od?.cc || ''

                this.result.ophthal_os.visus = Number(data[0]?.ophthal_os?.visus) ?? 12
                this.result.ophthal_os.tio_digital = Number(data[0]?.ophthal_os?.tio_digital) ?? 1
                this.result.ophthal_os.tio_tonometri_sciots = data[0]?.ophthal_os?.tio_tonometri_sciots || ''
                this.result.ophthal_os.eye_position = Number(data[0]?.ophthal_os?.eye_position) ?? 1
                this.result.ophthal_os.eye_movement = Number(data[0]?.ophthal_os?.eye_movement) ?? 1
                this.result.ophthal_os.super_silia = Number(data[0]?.ophthal_os?.super_silia) ?? 1
                this.result.ophthal_os.palpebra_edema = Number(data[0]?.ophthal_os?.palpebra_edema) ?? 1
                this.result.ophthal_os.palpebra_ptosis = Number(data[0]?.ophthal_os?.palpebra_ptosis) ?? 1
                this.result.ophthal_os.palpebra_lagophtalmus = Number(data[0]?.ophthal_os?.palpebra_lagophtalmus) ?? 1
                this.result.ophthal_os.palpebra_tumor = Number(data[0]?.ophthal_os?.palpebra_tumor) ?? 1
                this.result.ophthal_os.apparatus_lakrimalis = Number(data[0]?.ophthal_os?.apparatus_lakrimalis) ?? 1
                this.result.ophthal_os.silia = Number(data[0]?.ophthal_os?.silia) ?? 1
                this.result.ophthal_os.conjunctive_hiperemis = Number(data[0]?.ophthal_os?.conjunctive_hiperemis) ?? 1
                this.result.ophthal_os.conjunctive_bleed = Number(data[0]?.ophthal_os?.conjunctive_bleed) ?? 1
                this.result.ophthal_os.cornea = Number(data[0]?.ophthal_os?.cornea) ?? 1
                this.result.ophthal_os.camera_oculi = Number(data[0]?.ophthal_os?.camera_oculi) ?? 1
                this.result.ophthal_os.iris_color = Number(data[0]?.ophthal_os?.iris_color) ?? 2
                this.result.ophthal_os.iris_cripte = Number(data[0]?.ophthal_os?.iris_cripte) ?? 1
                this.result.ophthal_os.pupil_form = Number(data[0]?.ophthal_os?.pupil_form) ?? 1
                this.result.ophthal_os.pupil_diameter = Number(data[0]?.ophthal_os?.pupil_diameter) ?? 4
                this.result.ophthal_os.pupil_direct_light = Number(data[0]?.ophthal_os?.pupil_direct_light) ?? 1
                this.result.ophthal_os.pupil_indirect_light = Number(data[0]?.ophthal_os?.pupil_indirect_light) ?? 1
                this.result.ophthal_os.lens = Number(data[0]?.ophthal_os?.lens) ?? 1
                this.result.ophthal_os.shadow_test = Number(data[0]?.ophthal_os?.shadow_test) ?? 1
                this.result.ophthal_os.glandula = Number(data[0]?.ophthal_os?.glandula) ?? 1
                this.result.ophthal_os.field_view = Number(data[0]?.ophthal_os?.field_view) ?? 1
                this.result.ophthal_os.fundus = data[0]?.ophthal_os?.fundus || ''
                this.result.ophthal_os.cc = data[0]?.ophthal_os?.cc || ''
                
                this.muskuloskeletal_laki = data[0]?.canvas?.muskuloskeletal_laki || ''
                if (data[0]?.canvas?.muskuloskeletal_laki) {
                    this.editable.musLaki = true
                }
                this.muskuloskeletal_pr = data[0]?.canvas?.muskuloskeletal_pr || ''
                if (data[0]?.canvas?.muskuloskeletal_pr) {
                    this.editable.musPr = true
                }
                this.mata = data[0]?.canvas?.mata || ''
                if (data[0]?.canvas?.mata) {
                    this.editable.eye = true
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
    /* tinggi menyesuaikan isi (ilustrasi mata + tabel), tidak lagi fixed 160px */
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

/* ===================== MISC ===================== */
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