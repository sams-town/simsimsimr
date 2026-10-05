<template>
    <div>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0" style="margin-left:-16px; margin-right:-16px;">Data-Data Medis</div>
            <div class="row mt-20">
                <div class="col-lg">
                    <label class="c-form-label">Masalah Kebidanan</label>
                    <b-textarea v-model="form.medical_diagnosis" rows="5" />
                </div>
                <div class="col-lg">
                    <label class="c-form-label">Tujuan/Target Terukur</label>
                    <b-textarea v-model="form.support_diagnosis" rows="5" />
                </div>
            </div>
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0" style="margin-left:-16px; margin-right:-16px;">Pemeriksaan</div>
            <div class="mt-20">
                <ModernLabel label="A. UMUM" />
                <div class="row">
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">RR</label>
                        <b-input-group append="menit">
                            <b-form-input v-model="form.general_rr" type="number" />
                        </b-input-group>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg mt-10">
                        <label class="c-form-label">Inspeksi</label>
                        <b-textarea v-model="form.general_inspection" rows="5" />
                    </div>
                    <div class="col-lg mt-10">
                        <label class="c-form-label">Palpasi</label>
                        <b-textarea v-model="form.general_palpation" rows="5" />
                    </div>
                </div>
                <div class="mt-32">
                    <ModernLabel label="Pemeriksaan Fungsi Gerak Dasar" />
                    <div class="row">
                        <div class="col-lg-4 mt-10">
                            <label class="c-form-label">Aktif</label>
                            <b-form-input v-model="form.basic_active" />
                        </div>
                        <div class="col-lg-4 mt-10">
                            <label class="c-form-label">Pasif</label>
                            <b-form-input v-model="form.basic_passive" />
                        </div>
                        <div class="col-lg-4 mt-10">
                            <label class="c-form-label">Isometrik</label>
                            <b-form-input v-model="form.basic_isometric" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg mt-10">
                            <label class="c-form-label">Inspeksi</label>
                            <b-textarea v-model="form.basic_inspection" rows="5" />
                        </div>
                        <div class="col-lg mt-10">
                            <label class="c-form-label">Palpasi</label>
                            <b-textarea rows="5" v-model="form.basic_palpation" />
                        </div>
                    </div>
                </div>
                <div class="mt-32">
                    <ModernLabel label="B. KHUSUS" />
                    <div class="row c-form">
                        <div class="col-lg-4 mt-10">
                            <label class="c-form-label">Muscle Test</label>
                            <b-textarea v-model="form.special_muscle_test" rows="5" />
                        </div>
                        <div class="col-lg-4 mt-10">
                            <label class="c-form-label">ROM Test</label>
                            <b-textarea v-model="form.special_rom_test" rows="5" />
                        </div>
                        <div class="col-lg-4 mt-10">
                            <label class="c-form-label">Antropometri Test</label>
                            <b-textarea v-model="form.special_anthropometry_test" rows="5" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mt-10">
                            <label class="c-form-label">Skala nyeri</label>
                            <b-form-input v-model="form.special_sore_scale" />
                        </div>
                        <div class="col-lg-6 mt-10">
                            <label class="c-form-label">Spesifik test</label>
                            <b-textarea v-model="form.special_specific_test" rows="5" />
                        </div>
                    </div>
                </div>
                <div class="mt-32">
                    <ModernLabel label="C. KEMAMPUAN FUNGSIONAL" />
                    <div class="mt-20">
                        <b-textarea v-model="form.functional_abilities" rows="5" />
                    </div>
                </div>
            </div>
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0" style="margin-left:-16px; margin-right:-16px;">Diagnosis Fisioterapi</div>
            <div class="mt-20">
                <b-textarea v-model="form.physiotherapy_diagnosis" rows="5" />
            </div>
        </section>
        <section class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0" style="margin-left:-16px; margin-right:-16px;">Program Fisioterapi</div>
            <div class="mt-20">
                <b-textarea v-model="form.physiotherapy_program" rows="5" />
            </div>
        </section>
        <section id="Evaluasi-Lanjut" class="mt-32">
            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0" style="margin-left:-16px; margin-right:-16px;">Evaluasi dan Tindak Lanjut</div>
            <div class="mt-20">
                <b-textarea v-model="form.evaluation_follow_up" rows="5" />
            </div>
        </section>

    </div>
</template>
<script>
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';

export default {
    name: 'nurse_physiotherapy',
    components: { vSelect, ModernLabel },
    data() {
        return {
            // # TYPE_NURSE_ID X = FISIOTERAPI
            form: {
                nurse_id: "",
                medical_diagnosis: "",
                support_diagnosis: "",
                general_rr: "",
                general_inspection: "",
                general_palpation: "",
                basic_active: "",
                basic_passive: "",
                basic_isometric: "",
                basic_inspection: "",
                basic_palpation: "",
                special_muscle_test: "",
                special_rom_test: "",
                special_anthropometry_test: "",
                special_sore_scale: "",
                special_specific_test: "",
                functional_abilities: "",
                physiotherapy_diagnosis: "",
                physiotherapy_program: "",
                evaluation_follow_up: ""
            }
        }
    },
    methods: {
        // resetForm(){
        //     this.form.isolation_criteria = ''
        // },

    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>