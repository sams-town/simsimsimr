<template>
    <div>
        <b-modal ref="resumeModal" size="lg" scrollable no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase w-100">
                    ANDA AKAN MEMBUAT RESUME PASIEN
                    <span class="text-w3green">{{ regData?.alias_name }}. {{ regData?.name }} | {{ regData?.norm }} | {{localDate(regData?.birth_date)}} ({{ parsedAge(regData?.birth_date) }})</span>
                </div>
                <feather-icon icon="XIcon" @click="onCloseMainModal()" size="18" class="pointer" />
            </template>
            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Cara pulang</label>
                    <v-select v-model="form_resume.exit_id" :options="optExit" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="pilih" class="select-size-sm"/>
                </div>
                <div v-if="form_resume.exit_id == other_exit_id" class="col-lg-3 mt-8 mt-lg-0">
                    <label class="fs-13 mb-0">Cara pulang lainnya</label>
                    <b-form-input v-model="form_resume.exit_anythingelse" @change="listenUserInput" autocomplete="off" size="sm" />                    
                </div>
                <div class="col-lg-3 mt-8 mt-lg-0">
                    <label class="fs-13 mb-0">Kondisi saat pulang</label>
                    <v-select v-model="form_resume.exit_condition_type_id" :options="optExitCondition" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="pilih" class="select-size-sm"/>
                </div>
                <div v-if="form_resume.exit_condition_type_id == 4" class="col-lg-3 mt-8 mt-lg-0">
                    <label class="fs-13 mb-0">Kondisi lainnya</label>
                    <b-form-input v-model="form_resume.exit_condition_anythingelse" @change="listenUserInput" autocomplete="off" size="sm"/>                    
                </div>
            </div>
            <div class="mt-16">
                <ModernLabel label="Instruksi Rawat Lanjutan" small="(Continuous Care Instruction)"  />
                <div class="row mt-8">
                    <div class="col-lg-3">
                        <label class="fs-13 mb-0">Kontrol pasca rawat inap</label>
                        <v-select v-model="form_resume.care_instruction_control" :options="optControlInstruction" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="pilih" class="select-size-sm"/>
                    </div>
                    <template v-if="form_resume.care_instruction_control == 1">
                        <div class="col-lg-3 mt-8 mt-lg-0">
                            <label class="fs-13 mb-0">Kontrol di poliklinik</label>
                            <v-select v-model="form_resume.health_facility_item_id" :options="optHfi" :reduce="item => item.value" @input="listenUserInput" label="title" placeholder="pilih" class="select-size-sm" />
                        </div>
                        <div class="col-lg-3 mt-8 mt-lg-0">
                            <label class="fs-13 mb-0">Tanggal kontrol</label>
                            <b-form-input type="date" v-model="form_resume.care_instruction_control_date" @change="listenUserInput" autocomplete="off" size="sm"/>
                        </div>
                    </template>
                    <div v-if="form_resume.care_instruction_control == 4" class="col-lg-3 mt-8 mt-lg-0">
                        <label class="fs-13 mb-0">Lainnya</label>
                        <b-form-input v-model="form_resume.care_instruction_anythingelse" @change="listenUserInput" autocomplete="off" size="sm"/>                    
                    </div>
                    <div class="col-12 mt-8">
                        <label class="fs-13 mb-0">Edukasi selama di rumah</label>
                        <textarea v-model.lazy="form_resume.home_education" @change="listenUserInput" rows="3" class="form-control form-control-sm" autocomplete="off" />
                    </div>
                </div>
            </div>

            <div class="bg-info-100 fs-13 p-12 mt-32">
                <table>
                    <tr>
                        <td class="align-top" style="width: 150px;">Ringkasan Riwayat Penyakit</td>
                        <td class="align-top text-center" style="width: 20px;">:</td>
                        <td class="align-top">
                            <span v-html="parsedBody(form_resume.summary_medical_history)"></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="align-top">Hasil Pemeriksaan Fisik</td>
                        <td class="align-top text-center">:</td>
                        <td class="align-top">{{ form_resume.physical_check }}</td>
                    </tr>
                    <tr>
                        <td class="align-top">Indikasi Rawat Inap</td>
                        <td class="align-top text-center">:</td>
                        <td class="align-top">{{ form_resume.indication_for_hospitalization }}</td>
                    </tr>
                    <tr>
                        <td class="align-top">Diagnosa</td>
                        <td class="align-top text-center">:</td>
                        <td class="align-top">
                            <template v-if="form_resume?.icd10?.length">
                                <li v-for="(item, index) in form_resume?.icd10" :key="index">{{ item.title }}</li>
                            </template>
                        </td>
                    </tr>
                    <tr>
                        <td class="align-top">Catatan ICD10</td>
                        <td class="align-top text-center">:</td>
                        <td class="align-top">
                            <span v-html="parsedBody(form_resume.icd10_notes)"></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="align-top">Prosedur Diagnostik & Terapeutik</td>
                        <td class="align-top text-center">:</td>
                        <td class="align-top">
                            <template v-if="form_resume?.icd9cm?.length">
                                <li v-for="(item, index) in form_resume?.icd9cm" :key="index">{{ item.title }}</li>
                            </template>
                        </td>
                    </tr>
                    <tr>
                        <td class="align-top">Catatan ICD9CM</td>
                        <td class="align-top text-center">:</td>
                        <td class="align-top">
                            <span v-html="parsedBody(form_resume.therapy_plan)"></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="align-top">Laboratorium</td>
                        <td class="align-top text-center">:</td>
                        <td class="align-top">
                            <template v-if="form_resume?.laboratory_request_items?.length">
                                <li v-for="(item, index) in form_resume?.laboratory_request_items" :key="index">{{ item.laboratory_request_item_name }}</li>
                            </template>
                        </td>
                    </tr>
                    <tr>
                        <td class="align-top">Radiologi</td>
                        <td class="align-top text-center">:</td>
                        <td class="align-top">
                            <template v-if="form_resume?.radiology_request_items?.length">
                                <li v-for="(item, index) in form_resume?.radiology_request_items" :key="index">{{ item.radiology_request_item_name }}</li>
                            </template>
                        </td>
                    </tr>
                    <tr>
                        <td class="align-top">Fisioterapi</td>
                        <td class="align-top text-center">:</td>
                        <td class="align-top">
                            <template v-if="form_resume?.physiotherapy_request_items?.length">
                                <li v-for="(item, index) in form_resume?.physiotherapy_request_items" :key="index">{{ item.physiotherapy_request_item_name }}</li>
                            </template>
                        </td>
                    </tr>
                    <tr>
                        <td class="align-top">Obat-obatan penting selama perawatan</td>
                        <td class="align-top text-center">:</td>
                        <td class="align-top">
                            <span v-html="parsedBody(form_resume.significant_medications)"></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="align-top">Obat yang dibawa pulang</td>
                        <td class="align-top text-center">:</td>
                        <td class="align-top">
                            <span v-html="parsedBody(form_resume.take_home_medications)"></span>
                        </td>
                    </tr>
                </table>
                
                <div class="mt-16 fs-11">
                    <i class="text-danger-500">*teks merah untuk menandai pemeriksaan fisik bermakna</i>
                    <div class="row mt-1">
                        <div class="col-lg-4">
                            <table class="w-100">
                                <tbody>
                                    <tr>
                                        <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">KEPALA</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.head)">
                                        <td style="width: 100px;">Kepala</td>
                                        <td style="width: 15px;" class="px-1">:</td>
                                        <td>{{ getHead(form_resume?.head) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.head_hair_color)">
                                        <td class="align-top">Warna Rambut</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getHairColor(form_resume?.head_hair_color) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.head_hair_condition)">
                                        <td class="align-top">Kondisi Rambut</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getHairCondition(form_resume?.head_hair_condition) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">MATA</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.eye_conjunctive)">
                                        <td class="align-top" >Konjungtiva</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getEyeConjunctive(form_resume?.eye_conjunctive) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.eye_sclera)">
                                        <td class="align-top">Sklera</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getEyeSclera(form_resume?.eye_sclera) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.eye_pupil)">
                                        <td class="align-top">Pupil</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getEyePupil(form_resume?.eye_pupil) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.eye_light_reflex)">
                                        <td class="align-top">Reflek Cahaya</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getEyeReflect(form_resume?.eye_light_reflex) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">MULUT</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.mouth_mucosa)">
                                        <td class="align-top">Mukosa</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getMouthMucosa(form_resume?.mouth_mucosa) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.mouth_discoloration)">
                                        <td class="align-top">Perubahan Warna</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getMouthDiscoloration(form_resume?.mouth_discoloration) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.mouth_tongue)">
                                        <td class="align-top">Lidah</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getMouthTongue(form_resume?.mouth_tongue) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.mouth_caries)">
                                        <td class="align-top">Karies</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getMouthCaries(form_resume?.mouth_caries) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-4">
                            <table class="w-100">
                                <tbody>
                                    <tr>
                                        <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">LEHER</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.neck_kgb)">
                                        <td class="align-top" style="width: 100px;">KGB</td>
                                        <td style="width: 15px;" class="align-top px-1">:</td>
                                        <td>{{ getNeckKGB(form_resume?.neck_kgb) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.neck_trachea_position)">
                                        <td class="align-top">Posisi Trakea</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getNeckTracheaPosition(form_resume?.neck_trachea_position) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.neck_massa)">
                                        <td class="align-top">Massa</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getNeckMassa(form_resume?.neck_massa) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.neck_jvp)">
                                        <td class="align-top">JVP</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getNeckJVP(form_resume?.neck_jvp) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">EKSTREMITAS</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.extremity_acral)">
                                        <td class="align-top">Akral</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getEkstremitasAkral(form_resume?.extremity_acral) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.extremity_crt)">
                                        <td class="align-top">CRT</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getEkstremitasCRT(form_resume?.extremity_crt) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.extremity_edema)">
                                        <td class="align-top">Edema</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getEkstremitasEdema(form_resume?.extremity_edema) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">ABDOMEN</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.abdomen_supel)">
                                        <td class="align-top">Supel</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getAbdomenSupel(form_resume?.abdomen_supel) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.abdomen_bowel_sounds)">
                                        <td class="align-top">Bising Usus</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getAbdomenBising(form_resume?.abdomen_bowel_sounds) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.abdomen_bowel_sounds)">
                                        <td class="align-top">Nyeri Tekan</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getAbdomenPress(form_resume?.abdomen_tenderness) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.abdomen_hepar_lien)">
                                        <td class="align-top">Hepar & Lien</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getAbdomenHepar(form_resume?.abdomen_hepar_lien) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Pemeriksaan Abdomen Lainnya</td>
                                        <td class="align-top px-1">:</td>
                                        <td class="align-top">
                                            <div v-html="parsedBody(form_resume?.abdomen_other_physical_check)"></div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-4">
                            <table class="w-100">
                                <tbody>
                                    <tr>
                                        <td colspan="3" class="font-weight-bold text-ax-primary-500 fs-12">THORAKS</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="font-weight-bold">INSPEKSI</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.thorax_insp_chest)">
                                        <td class="align-top" style="width: 120px;">Dada</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getInspectionChest(form_resume?.thorax_insp_chest) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.thorax_insp_retraction)">
                                        <td class="align-top">Retraksi</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getInspectionRetractiont(form_resume?.thorax_insp_retraction) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="font-weight-bold">PALPASI</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.thorax_insp_chest)">
                                        <td class="align-top">Dada</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getPalpationChest(form_resume?.thorax_insp_chest) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.thorax_palp_fremitus)">
                                        <td class="align-top">Fremitus</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getPalpationFremitus(form_resume?.thorax_palp_fremitus) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.thorax_palp_crepitation)">
                                        <td class="align-top">Krepitasi</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getPalpationKrepitasi(form_resume?.thorax_palp_crepitation) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="font-weight-bold">AUSKULTASI</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.thorax_ausk_pul_vasiculer)">
                                        <td class="align-top">Pulmo Vasikuler</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getAuskultationPulmoVasikuler(form_resume?.thorax_ausk_pul_vasiculer) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.thorax_ausk_pul_rhonki)">
                                        <td class="align-top">Pulmo Rhonki</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getAuskultationPulmoRhonki(form_resume?.thorax_ausk_pul_rhonki) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.thorax_ausk_pul_wheezing)">
                                        <td class="align-top">Pulmo Wheezing</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getAuskultationPulmoWheezing(form_resume?.thorax_ausk_pul_wheezing) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.thorax_ausk_cor_heart_sound)">
                                        <td class="align-top">COR Bunyi Jantung</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getAuskultationCORBJ(form_resume?.thorax_ausk_cor_heart_sound) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.thorax_ausk_cor_murmur)">
                                        <td class="align-top">COR Murmur</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getAuskultationCORMurmur(form_resume?.thorax_ausk_cor_murmur) }}</td>
                                    </tr>
                                    <tr :class="getMeaningVariant(form_resume?.thorax_ausk_cor_gallop)">
                                        <td class="align-top">COR Gallop</td>
                                        <td class="align-top px-1">:</td>
                                        <td>{{ getAuskultationCORGallop(form_resume?.thorax_ausk_cor_gallop) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <table class="w-100 mt-1">
                        <tbody>
                            <tr>
                                <td style="width: 100px;">Genitalia</td>
                                <td style="width: 15px;" class="px-1">:</td>
                                <td>{{ form_resume?.genitalia }}</td>
                            </tr>
                            <tr>
                                <td class="align-top">Status Lokalis</td>
                                <td class="align-top px-1">:</td>
                                <td>{{ form_resume?.localist_status }}</td>
                            </tr>
                            <tr>
                                <td class="align-top">Status Neurologis</td>
                                <td class="align-top px-1">:</td>
                                <td>{{ form_resume?.neurologist_status }}</td>
                            </tr>
                            <tr>
                                <td class="align-top">Status Oftalmologis</td>
                                <td class="align-top px-1">:</td>
                                <td>{{ form_resume?.ophthal_status }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="validationResume" :disabled="isDisabled" size="sm" variant="w3green" class="ml-1">SIMPAN RESUME</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal ref="exitWarningModal" size="sm">
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase text-center w-100">PERUBAHAN BELUM DISIMPAN</div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="text-center fs-13">
                <p>Perubahan yang anda lakukan belum tersimpan. Apakah anda ingin menyimpan perubahan sebelum keluar?</p>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-center w-100">
                    <b-button @click="discardAndExit"size="sm" variant="outline-danger-500">Buang Perubahan</b-button>
                    <b-button @click="saveAndExit" :disabled="disabled_exit" size="sm" variant="w3green" class="ml-1">Simpan & keluar</b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>

<script>
import dateMixin from '@/mixins/dateMixin';
import optionMixin from '@/mixins/optionMixin';
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default {
    mixins: [dateMixin, optionMixin],
    name: 'ExternalResumeView',
    data() {
        return {
            other_exit_id: 'adb7ce0b-ede3-4fee-b273-ce159d286732',
            regData: {},
            cpptData: {},
            soapData: {},
            resumeData: {},
            insuranceDatum:{},
            vital_list: [],
            isDisabled: false,

            original_resume: {},
            form_resume: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                summary_medical_history: '',
                physical_check: '',
                icd10: [],
                icd10_notes:'',
                icd9cm: [],
                therapy_plan:'',
                significant_medications: '',
                take_home_medications: '-',
                exit_id: '807ca005-5827-4e19-9c97-0bc3d4e924ca',
                exit_anythingelse: '',
                exit_condition_type_id: 1,
                exit_condition_anythingelse: '',
                care_instruction_control: '',
                care_instruction_control_date: '',
                care_instruction_anythingelse: '',
                is_resume_exit: 1,
                laboratory_request_items: [],
                radiology_request_items: [],
                physiotherapy_request_items: [],
                home_education: '',
                health_facility_item_id: '',
                indication_for_hospitalization: '',
                head: 1,
                head_hair_color: 1,
                head_hair_condition: 1,
                eye_conjunctive: 1,
                eye_sclera: 1,
                eye_pupil: 1,
                eye_light_reflex: 1,
                mouth_mucosa: 1,
                mouth_discoloration: 1,
                mouth_tongue: 1,
                mouth_caries: 1,
                neck_kgb: 1,
                neck_trachea_position: 1,
                neck_massa: 1,
                neck_jvp: 1,
                thorax_insp_chest: 1,
                thorax_insp_retraction: 1,
                thorax_palp_chest: 1,
                thorax_palp_fremitus: 1,
                thorax_palp_crepitation: 1,
                thorax_ausk_pul_vasiculer: 1,
                thorax_ausk_pul_rhonki: 1,
                thorax_ausk_pul_wheezing: 1,
                thorax_ausk_cor_heart_sound: 1,
                thorax_ausk_cor_murmur: 1,
                thorax_ausk_cor_gallop: 1,
                abdomen_supel: 1,
                abdomen_bowel_sounds: 1,
                abdomen_tenderness: 1,
                abdomen_hepar_lien: 1,
                abdomen_other_physical_check: '',
                extremity_acral: 1,
                extremity_crt: 1,
                extremity_edema: 1,
                genitalia: '',
                localist_status: '',
                neurologist_status: '',
                ophthal_status: '',
            },

            disabled_exit: false,
        }
    },

    watch : {
        "form_resume.exit_id"(newValue) {
            if (newValue != this.other_exit_id) {
                this.form_resume.exit_anythingelse = ''
            }
        },
        "form_resume.exit_condition_type_id"(newValue) {
            if (newValue != 4) {
                this.form_resume.exit_condition_anythingelse = ''
            }
        },
        "form_resume.care_instruction_control"(newValue) {
            if (newValue != 1) {
                this.form_resume.care_instruction_control_date = ''
                this.form_resume.health_facility_item_id = ''
            }
            if (newValue != 4){
                this.form_resume.care_instruction_anythingelse = ''
            }
        },
    },

    computed: {
        ...mapState({ 
            optExit: state => state.frontoptions.doctor.resume.exit, 
            optExitCondition: state => state.frontoptions.doctor.resume.exit_condition, 
            optControlInstruction: state => state.frontoptions.doctor.resume.control_instruction, 
            optHfi: state => state.healthfacilityitem.options.healthfacilityitem,
            // generalis_key: state => state.frontoptions.doctor.assessment.generalis_key, 
        }),
    },

    methods: {
        async cloneState() {            
            this.original_resume = JSON.parse(JSON.stringify(this.form_resume))
        },

        hasChanges() {
            const formWithoutResumeId = { ...this.form_resume };
            const originalWithoutResumeId = { ...this.original_resume };
            delete formWithoutResumeId.id;
            delete originalWithoutResumeId.id;

            let changes = {};

            Object.keys(formWithoutResumeId).forEach(key => {
                let before = originalWithoutResumeId[key];
                let after = formWithoutResumeId[key];

                // Ubah data null atau string kosong biar jadi sting kosong 
                const beforeStr = before === null || before === undefined ? "" : String(before);
                const afterStr = after === null || after === undefined ? "" : String(after);

                // kalo value yang dibandingin sama (kasus tipe datanya beda), di abaikan
                if (beforeStr !== afterStr) {
                    changes[key] = { before, after };
                }
            });

            // console.log("Perubahan yang signifikan:", changes);
            return Object.keys(changes).length > 0 ? changes : false;
        },

        onCloseMainModal() {
            if (this.hasChanges()) {
                this.$refs.exitWarningModal.show();
            } else {
                this.$refs.resumeModal.hide();
            }
        },

        discardAndExit() {
            // Discard changes and close modal
            const id = this.form_resume.id
            this.form_resume = JSON.parse(JSON.stringify(this.original_resume))
            this.form_resume.id = id
            // console.log('this.form: ', this.form);

            this.$refs.exitWarningModal.hide();
            this.$refs.resumeModal.hide();
        },

        async saveAndExit() {
            try {
                this.disabled_exit = true
                await this.validationResume()
                this.$refs.exitWarningModal.hide();
                this.$refs.resumeModal.hide();
            } catch (error) {
                console.error('Error save & exit at: ', error);
            } finally {
                this.disabled_exit = false
            }
        },

        listenUserInput() {
            let message = { input_from: 'cppt', ...this.form_resume}
            try {
                this.$store.dispatch('resume/postResumePreCreate', message)
                .then(
                    async res => {
                        // console.log('successed linten: ', res);
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('error at: ', response)
                })
            } catch (error) {
                console.error('Error catch at: ', error)    
            }
        },

        async validationResume() {
            this.form_resume.doctor_in_charge_id ||= this.regData.doctor_in_charge_id
            this.form_resume.registration_destination_id ||= this.regData.registration_destination_id
            
            if (!this.form_resume.exit_id) return this.notification('Warning','CloseIcon','warning','Cara pulang wajib diisi')
            // console.log('clicked:', this.form_resume)
            
            try {
                this.isDisabled = true
                await this.$store.dispatch('resume/postPutResume', this.form_resume).then((res) => {
                    this.notification('Success','CheckIcon','success','Resume Berhasil Disimpan')
                    this.$refs.resumeModal.hide()
                    
                    // console.log('res', res)
                    setTimeout(() => {
                        const url = this.$router.resolve({ path: `/patient/resume/print/${this.regData.registration_destination_id}/${res?.data?.id}` }).href;
                        window.open(url, '_blank');
                    }, 1000);
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','Resume Gagal Tersimpan')
                console.error('Error at: ', error)
            } finally {
                this.isDisabled = false
            }
        },

        async createResume(message) {
            // console.log('message', message);
            this.regData = message?.regData
            this.cppt_id = message?.cppt_id
            this.getInsurance()
            
            await this.resetResume()
            await this.getResumeList()
            await this.getCppt()

            this.$refs.resumeModal.show()
        },        

        getInsurance() {
            if(Array.isArray(this.regData.registration_insurance) && this.regData.registration_insurance.length > 0 ) {
                let getFirstInsurance = this.regData.registration_insurance[0]
                this.insuranceDatum = getFirstInsurance 
            }
        },

        async getResumeList() {
            try {
                const response = await this.$store.dispatch('resume/getAllList', { registration_destination_id: this.regData.registration_destination_id });
                // console.log('response di get resume: ', response);
                if (response?.rows?.length > 0) {
                    this.resumeData =  response.rows[0]
                    await this.fillResumeForm(this.resumeData)
                } else {
                    await this.getAssessment()
                    await this.getVitalList()
                    await this.getSpri()
                }
                await this.cloneState()
            } catch (error) {
                console.error('Error fetching resume list:', error);
            }
        },

        async getAssessment() {
            const param = {
                registration_destination_id: this.regData.registration_destination_id,
                is_initial_inpatient: 1,
            }

            try {
                await this.$store.dispatch('soap/getSoapDetail', param)
                .then(async response => {
                    if (response?.length > 0) {
                        this.soapData = response[0]
                        await this.assignMedicalHistory(this.soapData)
                        await this.assignGeneralis(this.soapData)
                        await this.assignIcd10(this.soapData)
                        await this.assignIcd9cm(this.soapData)
                        await this.assignLaboratory(this.soapData)
                        await this.assignRadiology(this.soapData)
                        await this.assignPhysiotherapy(this.soapData)
                        await this.assignSignificantMed(this.soapData)
                    }
                })
            } catch (error) {
                console.error('Error fetching resume list:', error);
            }
        },

        async assignMedicalHistory(obj) {
            let generalNarrative = [];

            if (obj) {
                const hospitalized = `Pasien dirawat selama ${obj?.los || '-'} di ${obj?.health_facility_item_name || '-'}`
                
                // let temp_proc = []
                // if (obj?.transaction_doctor?.length > 0) {
                //     for (let i = 0; i < obj.transaction_doctor.length; i++) {
                //         let el = obj.transaction_doctor[i]
                //         temp_proc.push(el.costing_name)
                //     }
                // }

                // if (temp_proc?.length > 0) {
                //     const procedures = `dengan tindakan ${temp_proc.join(', ')},`;
                //     let combined = `${hospitalized} ${procedures}`
                //     generalNarrative.push(combined);
                // } else {
                //     generalNarrative.push(hospitalized)
                // }

                if (this.insuranceDatum.company_name != 'BPJS KETENAGAKERJAAN') {
                    generalNarrative.push(hospitalized)
                }

                const complaint = `${obj?.main_complaint || '-'}`
                generalNarrative.push(complaint)

                const disease = `${obj?.history_disease_now || '-'}`
                generalNarrative.push(disease)
            }
            this.form_resume.summary_medical_history = generalNarrative.join('\n');
        },

        async assignGeneralis(obj) {
            this.form_resume.head = obj?.head || 1
            this.form_resume.head_hair_color = obj?.head_hair_color || 1
            this.form_resume.head_hair_condition = obj?.head_hair_condition || 1
            this.form_resume.eye_conjunctive = obj?.eye_conjunctive || 1
            this.form_resume.eye_sclera = obj?.eye_sclera || 1
            this.form_resume.eye_pupil = obj?.eye_pupil || 1
            this.form_resume.eye_light_reflex = obj?.eye_light_reflex || 1
            this.form_resume.mouth_mucosa = obj?.mouth_mucosa || 1
            this.form_resume.mouth_discoloration = obj?.mouth_discoloration || 1
            this.form_resume.mouth_tongue = obj?.mouth_tongue || 1
            this.form_resume.mouth_caries = obj?.mouth_caries || 1
            this.form_resume.neck_kgb = obj?.neck_kgb || 1
            this.form_resume.neck_trachea_position = obj?.neck_trachea_position || 1
            this.form_resume.neck_massa = obj?.neck_massa || 1
            this.form_resume.neck_jvp = obj?.neck_jvp || 1
            this.form_resume.thorax_insp_chest = obj?.thorax_insp_chest || 1
            this.form_resume.thorax_insp_retraction = obj?.thorax_insp_retraction || 1
            this.form_resume.thorax_palp_chest = obj?.thorax_palp_chest || 1
            this.form_resume.thorax_palp_fremitus = obj?.thorax_palp_fremitus || 1
            this.form_resume.thorax_palp_crepitation = obj?.thorax_palp_crepitation || 1
            this.form_resume.thorax_ausk_pul_vasiculer = obj?.thorax_ausk_pul_vasiculer || 1
            this.form_resume.thorax_ausk_pul_rhonki = obj?.thorax_ausk_pul_rhonki || 1
            this.form_resume.thorax_ausk_pul_wheezing = obj?.thorax_ausk_pul_wheezing || 1
            this.form_resume.thorax_ausk_cor_heart_sound = obj?.thorax_ausk_cor_heart_sound || 1
            this.form_resume.thorax_ausk_cor_murmur = obj?.thorax_ausk_cor_murmur || 1
            this.form_resume.thorax_ausk_cor_gallop = obj?.thorax_ausk_cor_gallop || 1
            this.form_resume.abdomen_supel = obj?.abdomen_supel || 1
            this.form_resume.abdomen_bowel_sounds = obj?.abdomen_bowel_sounds || 1
            this.form_resume.abdomen_tenderness = obj?.abdomen_tenderness || 1
            this.form_resume.abdomen_hepar_lien = obj?.abdomen_hepar_lien || 1
            this.form_resume.abdomen_other_physical_check = obj?.abdomen_other_physical_check || ''
            this.form_resume.extremity_acral = obj?.extremity_acral || 1
            this.form_resume.extremity_crt = obj?.extremity_crt || 1
            this.form_resume.extremity_edema = obj?.extremity_edema || 1
            this.form_resume.genitalia = obj?.genitalia || ''
            this.form_resume.localist_status = obj?.localist_status || ''
            this.form_resume.neurologist_status = obj?.neurologist_status || ''
            this.form_resume.ophthal_status = obj?.ophthal_status || ''
        },

        async assignIcd10(obj) {
            if (obj?.icd10 && Array.isArray(obj.icd10)) {
                this.form_resume.icd10 = obj.icd10.map(item => {
                    return {
                        icd10_id: item.icd10_id,
                        value: item.icd10_id,
                        title: `${item.code} ${item.name}`, 
                        action: "action"
                    };
                });
            } else {
                this.form_resume.icd10 = [];
            }

            if (obj?.icd10_notes){
                this.form_resume.icd10_notes = obj.icd10_notes
            }
        },

        async assignIcd9cm(obj){
            if (obj?.icd9cm && Array.isArray(obj.icd9cm)) {
                this.form_resume.icd9cm = obj.icd9cm.map(item => {
                    return {
                        icd9cm_id: item.icd9cm_id,
                        value: item.icd9cm_id,
                        title: `${item.code} ${item.name}`, 
                        action: "action"
                    };
                });
            } else {
                this.form_resume.icd9cm = [];
            }

            if (obj?.therapy_plan){
                this.form_resume.therapy_plan = obj.therapy_plan
            }
        },

        async assignLaboratory(obj) {
            // console.log('lab', obj.laboratory_request_items);
            const init = obj.laboratory_request_items || [];
            init.forEach(item => {
                const temp = {
                    laboratory_request_item_id: item.id,
                    laboratory_request_item_name: item.costing_name,
                    laboratory_package_id: item.laboratory_package_id,
                    laboratory_result: item?.result,
                    laboratory_request_id: item?.laboratory_request_id
                };

                const exists = this.form_resume.laboratory_request_items.some(
                    i => i.laboratory_package_id === temp.laboratory_package_id
                );

                if (!exists) {
                    this.form_resume.laboratory_request_items.push(temp);
                }
            });
        },

        async assignRadiology(obj) {
            const init = obj.radiology_request_items || [];
            init.forEach(item => {
                const temp = {
                    radiology_request_item_id: item.id,
                    radiology_request_item_name: item.costing_name,
                    radiology_package_id: item.radiology_package_id,
                    radiology_url: item?.url
                };

                const exists = this.form_resume.radiology_request_items.some(
                    i => i.radiology_package_id === temp.radiology_package_id
                );

                if (!exists) {
                    this.form_resume.radiology_request_items.push(temp);
                }
            });
        },

        async assignPhysiotherapy(obj) {
            const init = obj.physiotherapy_request_items || [];
            init.forEach(item => {
                const temp = {
                    physiotherapy_request_item_id: item.id,
                    physiotherapy_request_item_name: item.costing_name,
                    physiotherapy_package_id: item.physiotherapy_package_id
                };

                const exists = this.form_resume.physiotherapy_request_items.some(
                    i => i.physiotherapy_package_id === temp.physiotherapy_package_id
                );

                if (!exists) {
                    this.form_resume.physiotherapy_request_items.push(temp);
                }
            });
        },

        async assignSignificantMed(obj) {
            // console.log('obj di significant:', obj);
            let temp_arry = []
            if (obj.prescription_new?.length > 0) {
                for (let i = 0; i < obj.prescription_new.length; i++) {
                    const el = obj.prescription_new[i];

                    for (let j = 0; j < el.form_prescription.length; j++) {
                        const unit = el.form_prescription[j];
                        temp_arry.push(unit.pharmacy_catalog_name)
                    }
                }
            }
            this.form_resume.significant_medications = temp_arry.filter(Boolean).join('\n')
        },

        async getVitalList() {
            try {                   
                await this.$store.dispatch('vitalsign/getListVitalSign', { registration_destination_id: this.regData.registration_destination_id })
                .then(async response => {
                    this.vital_list = response.rows
                    if (this.vital_list?.length > 0) {
                        await this.assignVital(this.vital_list[0])
                    }
                }, error => {
                    console.error('Error while GET vitalsign/getListVitalSign at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET vitalsign/getListVitalSign at: ', error);
            }
        },

        async assignVital(obj){
            let narrative = []
            if (obj) {
                narrative = [
                    `Systolic: ${obj?.systolic || '-'} mmHg`,
                    `Diastolic: ${obj?.diastolic || '-'} mmHg`,
                    `Frekuensi Nadi: ${obj?.pulse || '-'} x/mnt`,
                    `Frekuensi Nafas: ${obj?.respiration || '-'} x/mnt`,
                    `Saturasi Oksigen: ${obj?.oximetry || '-'} %`,
                    `Suhu: ${obj?.temperature || '-'} ᵒC`,
                    `Oksigenasi: ${this.getOxygenName?.(obj?.oxygen_type_id) || '-'}`,
                    `Kesadaran: ${this.getAwareName?.(obj?.aware)}`,
                    `GCS Mata: ${this.getGcsEyeName?.(obj?.eye_score)}`,
                    `GCS Verbal: ${this.getGcsVerbalName?.(obj?.verbal_score)}`,
                    `GCS Motorik: ${this.getGcsMotoricName?.(obj?.motor_score)}`,
                    `Berat Badan: ${obj?.weight} kg`,
                    `Tinggi Badan: ${obj?.height} cm`,
                    `IMT: ${this.imtCalculation(obj?.weight, obj?.height)} kg/m2`,
                ]
            }
            this.form_resume.physical_check = narrative.filter(Boolean).join(', ');
        },

        imtCalculation(weight, height) {
            const brt_badan = parseFloat(weight) || 0
            const tng_badan = parseFloat(height) || 0
            let imtResult = ''
            if (brt_badan && tng_badan) {
                const heightInMeters = tng_badan / 100
                const imt = brt_badan / (heightInMeters ** 2)
                imtResult = imt.toFixed(1)
            } 
            return imtResult
        },

        async getSpri() {
            try {                   
                this.$store.dispatch('soapinpatient/getDetailSoapInpatient', { registration_destination_id: this.regData.registration_destination_id })
                .then(response => {
                    this.form_resume.indication_for_hospitalization = response?.length > 0 ? this.getIndicationName?.(response[0]?.indication) || '-' : '-'
                }, error => {
                    console.error('Error while GET soapinpatient/getDetailSoapInpatient at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET soapinpatient/getDetailSoapInpatient at: ', error);
            }
        },

        async getCppt(){
            try {                
                this.$store.dispatch('cppt/getByCpptId', this.cppt_id).then((response) => {
                    // console.log('response cppt:', response);
                    if (response?.length > 0) {
                        this.assignHomeMedic(response[0])
                    }
                }, error => {
                    console.error('Error while GET cppt/getByCpptId at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET cppt/getByCpptId at: ', error);
            }
        },

        assignHomeMedic(obj) {
            let temp_arry = []
            const fix_presc = obj.prescription.find(item => item?.type_prescription_id == 2) || {}
            let var_presc = fix_presc?.form_prescription || []
            
            if (var_presc.length > 0) {
                for (let i = 0; i < var_presc.length; i++) {
                    const el = var_presc[i];
                    temp_arry.push(el.pharmacy_catalog_name)
                }
            }
            this.form_resume.take_home_medications = temp_arry.filter(Boolean).join('\n')
        },

        async fillResumeForm(obj) {
            if (obj) {
                this.$nextTick(async () => {
                    this.form_resume.id = obj?.id || ''
                    this.form_resume.registration_destination_id = obj?.registration_destination_id || ''
                    this.form_resume.doctor_in_charge_id = obj?.doctor_in_charge_id || ''
                    this.form_resume.summary_medical_history = obj?.summary_medical_history || ''
                    this.form_resume.physical_check = obj?.physical_check || ''

                    if (obj?.icd10 && Array.isArray(obj.icd10)) {
                        this.form_resume.icd10 = obj.icd10.map(item => {
                            return {
                                icd10_id: item.icd10_id,
                                value: item.icd10_id,
                                title: `${item.icd10_code} ${item.icd10_name}`, 
                                action: "action"
                            };
                        });
                    } else {
                        this.form_resume.icd10 = []
                    }

                    this.form_resume.icd10_notes = obj?.icd10_notes || ''

                    if (obj?.icd9cm && Array.isArray(obj.icd9cm)) {
                        this.form_resume.icd9cm = obj.icd9cm.map(item => {
                            return {
                                icd9cm_id: item.icd9cm_id,
                                value: item.icd9cm_id,
                                title: `${item.icd9cm_code} ${item.icd9cm_name}`, 
                                action: "action"
                            };
                        });
                    } else {
                        this.form_resume.icd9cm = []
                    }

                    this.form_resume.therapy_plan = obj?.therapy_plan || ''

                    this.form_resume.significant_medications = obj?.significant_medications || ''
                    this.form_resume.take_home_medications = obj?.take_home_medications || '-'
                    this.form_resume.exit_id = obj?.exit_id || '807ca005-5827-4e19-9c97-0bc3d4e924ca'
                    this.form_resume.exit_anythingelse = obj?.exit_anythingelse || ''
                    this.form_resume.exit_condition_type_id = obj?.exit_condition_type_id || 1
                    this.form_resume.exit_condition_anythingelse = obj?.exit_condition_anythingelse || ''
                    this.form_resume.care_instruction_control = obj?.care_instruction_control || ''
                    this.form_resume.care_instruction_control_date = obj?.care_instruction_control_date || ''
                    this.form_resume.care_instruction_anythingelse = obj?.care_instruction_anythingelse || ''
                    this.form_resume.is_resume_exit = obj?.is_resume_exit || 1

                    const initlab = obj?.laboratory_request_items || []
                    initlab.forEach(item => {
                        const templab = {
                            laboratory_request_item_id: item.item_id,
                            laboratory_request_item_name: item.costing_name,
                            laboratory_package_id: item.laboratory_package_id
                        };

                        const exists = this.form_resume.laboratory_request_items.some(
                            i => i.laboratory_package_id === templab.laboratory_package_id
                        );

                        if (!exists) {
                            this.form_resume.laboratory_request_items.push(templab);
                        }
                    });

                    const initrad = obj?.radiology_request_items || []
                    initrad.forEach(item => {
                        const temprad = {
                            radiology_request_item_id: item.item_id,
                            radiology_request_item_name: item.costing_name,
                            radiology_package_id: item.radiology_package_id
                        };

                        const exists = this.form_resume.radiology_request_items.some(
                            i => i.radiology_package_id === temprad.radiology_package_id
                        );

                        if (!exists) {
                            this.form_resume.radiology_request_items.push(temprad);
                        }
                    });

                    const initphy = obj?.physiotherapy_request_items || []
                    initphy.forEach(item => {
                        const tempphy = {
                            physiotherapy_request_item_id: item.item_id,
                            physiotherapy_request_item_name: item.costing_name,
                            physiotherapy_package_id: item.physiotherapy_package_id
                        };

                        const exists = this.form_resume.physiotherapy_request_items.some(
                            i => i.physiotherapy_package_id === tempphy.physiotherapy_package_id
                        );

                        if (!exists) {
                            this.form_resume.physiotherapy_request_items.push(tempphy);
                        }
                    });
                    
                    this.form_resume.home_education = obj?.home_education || ''
                    this.form_resume.health_facility_item_id = obj?.health_facility_item_id || ''
                    this.form_resume.indication_for_hospitalization = obj?.indication_for_hospitalization || ''
                    this.form_resume.head = obj?.head || 1
                    this.form_resume.head_hair_color = obj?.head_hair_color || 1
                    this.form_resume.head_hair_condition = obj?.head_hair_condition || 1
                    this.form_resume.eye_conjunctive = obj?.eye_conjunctive || 1
                    this.form_resume.eye_sclera = obj?.eye_sclera || 1
                    this.form_resume.eye_pupil = obj?.eye_pupil || 1
                    this.form_resume.eye_light_reflex = obj?.eye_light_reflex || 1
                    this.form_resume.mouth_mucosa = obj?.mouth_mucosa || 1
                    this.form_resume.mouth_discoloration = obj?.mouth_discoloration || 1
                    this.form_resume.mouth_tongue = obj?.mouth_tongue || 1
                    this.form_resume.mouth_caries = obj?.mouth_caries || 1
                    this.form_resume.neck_kgb = obj?.neck_kgb || 1
                    this.form_resume.neck_trachea_position = obj?.neck_trachea_position || 1
                    this.form_resume.neck_massa = obj?.neck_massa || 1
                    this.form_resume.neck_jvp = obj?.neck_jvp || 1
                    this.form_resume.thorax_insp_chest = obj?.thorax_insp_chest || 1
                    this.form_resume.thorax_insp_retraction = obj?.thorax_insp_retraction || 1
                    this.form_resume.thorax_palp_chest = obj?.thorax_palp_chest || 1
                    this.form_resume.thorax_palp_fremitus = obj?.thorax_palp_fremitus || 1
                    this.form_resume.thorax_palp_crepitation = obj?.thorax_palp_crepitation || 1
                    this.form_resume.thorax_ausk_pul_vasiculer = obj?.thorax_ausk_pul_vasiculer || 1
                    this.form_resume.thorax_ausk_pul_rhonki = obj?.thorax_ausk_pul_rhonki || 1
                    this.form_resume.thorax_ausk_pul_wheezing = obj?.thorax_ausk_pul_wheezing || 1
                    this.form_resume.thorax_ausk_cor_heart_sound = obj?.thorax_ausk_cor_heart_sound || 1
                    this.form_resume.thorax_ausk_cor_murmur = obj?.thorax_ausk_cor_murmur || 1
                    this.form_resume.thorax_ausk_cor_gallop = obj?.thorax_ausk_cor_gallop || 1
                    this.form_resume.abdomen_supel = obj?.abdomen_supel || 1
                    this.form_resume.abdomen_bowel_sounds = obj?.abdomen_bowel_sounds || 1
                    this.form_resume.abdomen_tenderness = obj?.abdomen_tenderness || 1
                    this.form_resume.abdomen_hepar_lien = obj?.abdomen_hepar_lien || 1
                    this.form_resume.abdomen_other_physical_check = obj?.abdomen_other_physical_check || ''
                    this.form_resume.extremity_acral = obj?.extremity_acral || 1
                    this.form_resume.extremity_crt = obj?.extremity_crt || 1
                    this.form_resume.extremity_edema = obj?.extremity_edema || 1
                    this.form_resume.genitalia = obj?.genitalia || ''
                    this.form_resume.localist_status = obj?.localist_status || ''
                    this.form_resume.neurologist_status = obj?.neurologist_status || ''
                    this.form_resume.ophthal_status = obj?.ophthal_status || ''
                })
            }
        },

        async resetResume() {
            this.form_resume.id = ''
            this.form_resume.registration_destination_id = ''
            this.form_resume.doctor_in_charge_id = ''
            this.form_resume.summary_medical_history = ''
            this.form_resume.physical_check = ''
            this.form_resume.icd10 = []
            this.form_resume.icd10_notes = ''
            this.form_resume.icd9cm = []
            this.form_resume.therapy_plan = ''
            this.form_resume.significant_medications = ''
            this.form_resume.take_home_medications = '-'
            this.form_resume.exit_id = '807ca005-5827-4e19-9c97-0bc3d4e924ca'
            this.form_resume.exit_anythingelse = ''
            this.form_resume.exit_condition_type_id = 1
            this.form_resume.exit_condition_anythingelse = ''
            this.form_resume.care_instruction_control = ''
            this.form_resume.care_instruction_control_date = ''
            this.form_resume.care_instruction_anythingelse = ''
            this.form_resume.is_resume_exit = 1
            this.form_resume.laboratory_request_items = []
            this.form_resume.radiology_request_items = []
            this.form_resume.physiotherapy_request_items = []
            this.form_resume.home_education = ''
            this.form_resume.health_facility_item_id = ''
            this.form_resume.indication_for_hospitalization = ''
            this.form_resume.head = 1
            this.form_resume.head_hair_color = 1
            this.form_resume.head_hair_condition = 1
            this.form_resume.eye_conjunctive = 1
            this.form_resume.eye_sclera = 1
            this.form_resume.eye_pupil = 1
            this.form_resume.eye_light_reflex = 1
            this.form_resume.mouth_mucosa = 1
            this.form_resume.mouth_discoloration = 1
            this.form_resume.mouth_tongue = 1
            this.form_resume.mouth_caries = 1
            this.form_resume.neck_kgb = 1
            this.form_resume.neck_trachea_position = 1
            this.form_resume.neck_massa = 1
            this.form_resume.neck_jvp = 1
            this.form_resume.thorax_insp_chest = 1
            this.form_resume.thorax_insp_retraction = 1
            this.form_resume.thorax_palp_chest = 1
            this.form_resume.thorax_palp_fremitus = 1
            this.form_resume.thorax_palp_crepitation = 1
            this.form_resume.thorax_ausk_pul_vasiculer = 1
            this.form_resume.thorax_ausk_pul_rhonki = 1
            this.form_resume.thorax_ausk_pul_wheezing = 1
            this.form_resume.thorax_ausk_cor_heart_sound = 1
            this.form_resume.thorax_ausk_cor_murmur = 1
            this.form_resume.thorax_ausk_cor_gallop = 1
            this.form_resume.abdomen_supel = 1
            this.form_resume.abdomen_bowel_sounds = 1
            this.form_resume.abdomen_tenderness = 1
            this.form_resume.abdomen_hepar_lien = 1
            this.form_resume.abdomen_other_physical_check = ''
            this.form_resume.extremity_acral = 1
            this.form_resume.extremity_crt = 1
            this.form_resume.extremity_edema = 1
            this.form_resume.genitalia = ''
            this.form_resume.localist_status = ''
            this.form_resume.neurologist_status = ''
            this.form_resume.ophthal_status = ''
        },

        parsedBody(text){
            return String(text || '-').replace(/\n/g, "<br>");
        },

        getMeaningVariant(value) {
            if (value && value != 1) return 'text-danger-500'
            return ''
        },

        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
    },
    created() {
        this.$store.dispatch('healthfacilityitem/getParam', '3cb626f5-9148-4fb1-8663-3d7f028b01a6')
    }
};
</script>
