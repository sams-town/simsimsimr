<template>
    <div>
        <b-modal v-model="active_act.modal_IGD" scrollable size="xl" hide-footer no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ASESMEN IGD
                    <span class="text-primary"> {{ temp_header.alias_name }}. {{ temp_header.name }} | {{ temp_header.norm }} | {{localDate(temp_header.birth_date)}} ({{ parsedAge(temp_header.birth_date) }}) | BB: {{temp_header.weight | number}} KG | TB: {{ temp_header.height | number}} CM</span>
                </div>
                <feather-icon icon="XIcon" @click="close" size="18" class="pointer" />
            </template>

            <ApexaSpinner :visible="isLoading" />
            <template v-if="!isLoading">
                <section class="card">
                    <div class="card-header bg-indigo-200">
                        <span class="text-bold-18">Subjective</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Anamnesa</label>
                            </div>
                            <div class="col">
                                <v-select v-model="form_igd.anamesa_type_id" aria-readonly="true" :options="optAnamnesaType" :reduce="item => item.value" label="title" placeholder="-- Pilih Anamnesa --"></v-select>
                            </div>
                        </div>
                        
                        <div class="row" v-if="form_igd.anamesa_type_id == 2">
                            <div class="col-lg-6 px-0">
                                <div class="row mx-0 mt-10">
                                    <div class="col-lg-6 d-lg-flex">
                                        <label class="my-auto text-semi-14">Nama Keluarga</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <b-form-input type="text" v-model="form_igd.family_name" autocomplete="off"></b-form-input>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 px-0">
                                <div class="row mx-0 mt-10">
                                    <div class="col-lg-6 d-lg-flex">
                                        <label class="m-lg-auto text-semi-14">Hubungan Keluarga</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <v-select v-model="form_igd.family_relation_id" aria-readonly="true" :options="optFamilyRelation" :reduce="item => item.value" label="title" placeholder="-- Pilih Hubungan Keluarga --"></v-select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Keluhan Utama</label>
                            </div>
                            <div class="col">
                                <b-form-input type="text" v-model="form_igd.main_complaint" autocomplete="off"></b-form-input>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="text-semi-14">Riwayat Penyakit Sekarang</label>
                            </div>
                            <div class="col">
                                <b-form-textarea rows="3" v-model="form_igd.history_disease_now" autocomplete="off"></b-form-textarea>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="text-semi-14">Riwayat Penyakit Dahulu</label>
                            </div>
                            <div class="col">
                                <b-form-textarea rows="3" v-model="form_igd.history_disease_past" autocomplete="off"></b-form-textarea>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="text-semi-14">Riwayat Obat</label>
                            </div>
                            <div class="col">
                                <b-form-textarea rows="3" v-model="form_igd.history_drugs" autocomplete="off"></b-form-textarea>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="card mt-20">
                    <div class="card-header bg-indigo-200">
                        <span class="text-bold-18">Objective</span>
                    </div>
                    <div class="card-body">
                        <div>
                            <div class="col px-0">
                                <ModernLabel label="Tanda Vital" />
                                <div class="row mt-12">
                                    <div class="col-lg-3">
                                        <label class="c-form-label">Systolic</label>
                                        <b-input-group append="mmHg">
                                            <b-form-input type="number" autocomplete="off" v-model="form_igd.systolic_blood_pressure"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="c-form-label">Diastolic</label>
                                        <b-input-group append="mmHg">
                                            <b-form-input type="number" autocomplete="off" v-model="form_igd.diastolic_blood_pressure"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="c-form-label">Frekuensi Nadi</label>
                                        <b-input-group append="x/mnt">
                                            <b-form-input type="number" autocomplete="off" v-model="form_igd.pulse_frequency"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="c-form-label">Frekuensi Nafas</label>
                                        <b-input-group append="x/mnt">
                                            <b-form-input type="number" autocomplete="off" v-model="form_igd.breath_frequency"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="c-form-label">Saturasi Oksigen</label>
                                        <b-input-group append="%">
                                            <b-form-input type="number" autocomplete="off" v-model="form_igd.oximetry_frequency"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="c-form-label">Suhu</label>
                                        <b-input-group append="ᵒC">
                                            <b-form-input type="number" autocomplete="off" v-model="form_igd.temperature"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="c-form-label">Oksigenasi</label>
                                        <v-select v-model="form_igd.oxygen_type_id" :options="OptionsOxygen" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="c-form-label">Kesadaran</label>
                                        <v-select v-model="form_igd.aware" :options="OptionsAware" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="c-form-label">GCS Mata</label>
                                        <v-select v-model="form_igd.eye_score" :options="OptionGcsEye" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="c-form-label">GCS verbal</label>
                                        <v-select v-model="form_igd.verbal_score" :options="OptionGcsVerbal" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                    </div>
                                    <div class="col-lg-3 mt-8">
                                        <label class="c-form-label">GCS Motorik</label>
                                        <v-select v-model="form_igd.motor_score" :options="OptionGcsMotoric" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                    </div>
                                    <template v-if="isPews">
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Warna Kulit</label>
                                            <v-select v-model="form_igd.skin_color" :options="optSkinColor" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="fs-13 mb-0">Perilaku</label>
                                            <v-select v-model="form_igd.behavior" :options="optBehavior" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                                        </div>
                                    </template> 
                                </div>
                            </div>
                            <div class="col mt-32 px-0">
                                <ModernLabel label="Antropometri" />

                                <div class="row mt-12">
                                    <div class="col-lg-3">
                                        <label class="c-form-label">Berat Badan</label>
                                        <b-input-group append="kg">
                                            <b-form-input @input="imtChanged($event)" type="number" autocomplete="off" v-model="form_igd.weight"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="c-form-label">Tinggi Badan</label>
                                        <b-input-group append="cm">
                                            <b-form-input @input="imtChanged($event)" type="number" autocomplete="off" v-model="form_igd.height"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="c-form-label">IMT</label>
                                        <b-input-group append="kg/m²">
                                            <b-form-input type="number" autocomplete="off" v-model="form_igd.imt"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-3 mt-8 mt-lg-0">
                                        <label class="c-form-label">Lingkar Kepala</label>
                                        <b-input-group append="cm">
                                            <b-form-input type="number" autocomplete="off" v-model="form_igd.head_circumference"></b-form-input>
                                        </b-input-group>
                                    </div>
                                </div>
                                <b-collapse id="collapse-anto" v-model="active.advance.antopometri" class="mb-8">
                                    <div class="row">
                                        <div class="col-lg-3 mt-8">
                                            <label class="c-form-label">Lingkar Dada</label>
                                            <b-input-group append="cm">
                                                <b-form-input type="number" autocomplete="off" v-model="form_igd.chest_circumference"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="c-form-label">Lingkar Lengan Atas</label>
                                            <b-input-group append="cm">
                                                <b-form-input type="number" autocomplete="off" v-model="form_igd.arm_circumference"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="c-form-label">Lingkar Perut</label>
                                            <b-input-group append="cm">
                                                <b-form-input type="number" autocomplete="off" v-model="form_igd.stomach_circumference"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-3 mt-8">
                                            <label class="c-form-label">Lingkar Lutut</label>
                                            <b-input-group append="cm">
                                                <b-form-input type="number" autocomplete="off" v-model="form_igd.knee_circumference"></b-form-input>
                                            </b-input-group>
                                        </div>
                                    </div>
                                </b-collapse>
                                <small @click="active.advance.antopometri = !active.advance.antopometri" class="font-weight-bold text-primary pointer" :class="active.advance.antopometri ? null : 'collapsed'" :aria-expanded="active.advance.antopometri ? 'true' : 'false'" aria-controls="collapse-anto">
                                    Advance Antopometri 
                                    <b-icon :icon="active.advance.antopometri == true ? 'chevron-up' : 'chevron-down'" />
                                </small>
                            </div>

                            <div class="mt-20">
                                <div class="card">
                                    <div class="card-header font-weight-bold">STATUS GENERALIS</div>
                                    <div class="card-body">
                                        <div>
                                            <ModernLabel label="Kepala" />
                                            <div class="row">
                                                <div class="col-lg-3 mt-12">
                                                    <label class="c-form-label">Kepala</label>
                                                    <v-select v-model="form_igd.head" :options="OptionsHeadCircumference" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                </div>
                                                <div class="col-lg-3 mt-12">
                                                    <label class="c-form-label">Warna Rambut</label>
                                                    <v-select v-model="form_igd.head_hair_color" :options="OptionsHeadHairColor" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                </div>
                                                <div class="col-lg-3 mt-12">
                                                    <label class="c-form-label">Kondisi Rambut</label>
                                                    <v-select v-model="form_igd.head_hair_condition" :options="OptionsHeadHairCondition" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-32">
                                            <ModernLabel label="Mata" />
                                            <div class="row">
                                                <div class="col-lg-3 mt-12">
                                                    <label class="c-form-label">Conjunctiva</label>
                                                    <v-select v-model="form_igd.eye_conjunctive" :options="OptionsEyeConjunctiva" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                </div>
                                                <div class="col-lg-3 mt-12">
                                                    <label class="c-form-label">Sklera</label>
                                                    <v-select v-model="form_igd.eye_sclera" :options="OptionsEyeSclera" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                </div>
                                                <div class="col-lg-3 mt-12">
                                                    <label class="c-form-label">Pupil</label>
                                                    <v-select v-model="form_igd.eye_pupil" :options="OptionsEyePupil" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                </div>
                                                <div class="col-lg-3 mt-12">
                                                    <label class="c-form-label">Reflek Cahaya</label>
                                                    <v-select v-model="form_igd.eye_light_reflex" :options="OptionsEyeReflect" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                </div>
                                            </div>
                                        </div>
                                        <b-collapse id="collapse-general" v-model="active.advance.generalis" class="mb-8">
                                            <div class="mt-32">
                                                <ModernLabel label="Mulut" />
                                                <div class="row">
                                                    <div class="col-lg-3 mt-12">
                                                        <label class="c-form-label">Mukosa</label>
                                                        <v-select v-model="form_igd.mouth_mucosa" :options="OptionsMouthMukosa" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="col-lg-3 mt-12">
                                                        <label class="c-form-label">Perubahan Warna</label>
                                                        <v-select v-model="form_igd.mouth_discoloration" :options="OptionsMouthHiperemis" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="col-lg-3 mt-12">
                                                        <label class="c-form-label">Lidah</label>
                                                        <v-select v-model="form_igd.mouth_tongue" :options="OptionsMouthTongue" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="col-lg-3 mt-12">
                                                        <label class="c-form-label">Karies</label>
                                                        <v-select v-model="form_igd.mouth_caries" :options="OptionsMouthKaries" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-32">
                                                <ModernLabel label="Leher" />
                                                <div class="row">
                                                    <div class="col-lg-3 mt-12">
                                                        <label class="c-form-label">KGB</label>
                                                        <v-select v-model="form_igd.neck_kgb" :options="OptionsNeckKGB" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="col-lg-3 mt-12">
                                                        <label class="c-form-label">Posisi Trakea</label>
                                                        <v-select v-model="form_igd.neck_trachea_position" :options="OptionsNeckTrakea" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="col-lg-3 mt-12">
                                                        <label class="c-form-label">Massa</label>
                                                        <v-select v-model="form_igd.neck_massa" :options="OptionsNeckMassa" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-32">
                                                <ModernLabel label="Thoraks" />
                                                <div class="mt-12">
                                                    <div class="font-weight-bold" style="font-size: 14px">INSPEKSI</div>
                                                    <div class="row">
                                                        <div class="col-lg-3 mt-12 mt-lg-0">
                                                            <label class="c-form-label">Dada</label>
                                                            <v-select v-model="form_igd.thorax_insp_chest" :options="OptionsInspectionChest" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                        </div>
                                                        <div class="col-lg-3 mt-12 mt-lg-0">
                                                            <label class="c-form-label">Retraksi</label>
                                                            <v-select v-model="form_igd.thorax_insp_retraction" :options="OptionsInspectionRetraction" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-24">
                                                    <div class="font-weight-bold" style="font-size: 14px">PALPASI</div>
                                                    <div class="row">
                                                        <div class="col-lg-3 mt-12 mt-lg-0">
                                                            <label class="c-form-label">Dada</label>
                                                            <v-select v-model="form_igd.thorax_palp_chest" :options="OptionsPalpationChest" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                        </div>
                                                        <div class="col-lg-3 mt-12 mt-lg-0">
                                                            <label class="c-form-label">Fremitus</label>
                                                            <v-select v-model="form_igd.thorax_palp_fremitus" :options="OptionsPalpationFremitus" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                        </div>
                                                        <div class="col-lg-3 mt-12 mt-lg-0">
                                                            <label class="c-form-label">Krepitasi</label>
                                                            <v-select v-model="form_igd.thorax_palp_crepitation" :options="OptionsPalpationKrepitasi" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-32">
                                                    <div class="font-weight-bold" style="font-size: 14px">AUSKULTASI</div>
                                                    <div class="row">
                                                        <div class="col-lg-3 mt-12 mt-lg-0">
                                                            <label class="c-form-label">Pulmo Vasikuler</label>
                                                            <v-select v-model="form_igd.thorax_ausk_pul_vasiculer" :options="OptionsAuskultationPulmoVasikuler" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                        </div>
                                                        <div class="col-lg-3 mt-12 mt-lg-0">
                                                            <label class="c-form-label">Pulmo Rhonki</label>
                                                            <v-select v-model="form_igd.thorax_ausk_pul_rhonki" :options="OptionsAuskultationPulmoRhonki" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                        </div>
                                                        <div class="col-lg-3 mt-12 mt-lg-0">
                                                            <label class="c-form-label">Pulmo Wheezing</label>
                                                            <v-select v-model="form_igd.thorax_ausk_pul_wheezing" :options="OptionsAuskultationPulmoWheezing" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-3 mt-12">
                                                            <label class="c-form-label">COR Bunyi Jantung</label>
                                                            <v-select v-model="form_igd.thorax_ausk_cor_heart_sound" :options="OptionsAuskultationCORBJ" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                        </div>
                                                        <div class="col-lg-3 mt-12">
                                                            <label class="c-form-label">COR Murmur</label>
                                                            <v-select v-model="form_igd.thorax_ausk_cor_murmur" :options="OptionsAuskultationCORMurmur" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                        </div>
                                                        <div class="col-lg-3 mt-12">
                                                            <label class="c-form-label">COR Gallop</label>
                                                            <v-select v-model="form_igd.thorax_ausk_cor_gallop" :options="OptionsAuskultationCORGallop" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-32">
                                                <ModernLabel label="Abdomen" />
                                                <div class="row">
                                                    <div class="col-lg-3 mt-12">
                                                        <label class="c-form-label">Supel</label>
                                                        <v-select v-model="form_igd.abdomen_supel" :options="OptionsAbdomenSupel" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="col-lg-3 mt-12">
                                                        <label class="c-form-label">Bising Usus</label>
                                                        <v-select v-model="form_igd.abdomen_bowel_sounds" :options="OptionsAbdomenBising" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="col-lg-3 mt-12">
                                                        <label class="c-form-label">Nyeri Tekan</label>
                                                        <v-select v-model="form_igd.abdomen_tenderness" :options="OptionsAbdomenPress" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="col-lg-3 mt-12">
                                                        <label class="c-form-label">Hepar & Lien</label>
                                                        <v-select v-model="form_igd.abdomen_hepar_lien" :options="OptionsAbdomenHepar" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="col-lg mt-12">
                                                        <label class="c-form-label">Pemeriksaan Abdomen Lainnya</label>
                                                        <b-form-textarea v-model="form_igd.abdomen_other_physical_check" rows="1" autocomplete="off" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-32">
                                                <ModernLabel label="Ekstremitas" />
                                                <div class="row">
                                                    <div class="col-lg-3 mt-12">
                                                        <label class="c-form-label">Akral</label>
                                                        <v-select v-model="form_igd.extremity_acral" :options="OptionsEkstremitasAkral" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="col-lg-3 mt-12">
                                                        <label class="c-form-label">CRT</label>
                                                        <v-select v-model="form_igd.extremity_crt" :options="OptionsEkstremitasCRT" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                    <div class="col-lg-3 mt-12">
                                                        <label class="c-form-label">Edema</label>
                                                        <v-select v-model="form_igd.extremity_edema" :options="OptionsEkstremitasEdema" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-32">
                                                <ModernLabel label="Genitalia" />
                                                <div class="mt-12 mb-8">
                                                    <b-form-textarea v-model="form_igd.genitalia" rows="2" autocomplete="off" placeholder="(Tuliskan pemeriksaan genitalia)" />
                                                </div>
                                            </div>
                                            <div class="mt-32">
                                                <ModernLabel label="Status Lokalis" />
                                                <div class="mt-12 mb-8">
                                                    <b-form-textarea v-model="form_igd.localist_status" rows="2" autocomplete="off" placeholder="(Tuliskan status lokalis di kolom ini secara lengkap)" />
                                                </div>
                                                
                                                <!-- <small class="mt-16">Advance status lokalis <b-icon icon="chevron-down" /></small> -->
                                            </div>
                                            <div class="mt-32">
                                                <ModernLabel label="Status Neurologis" />
                                                <div class="mt-12 mb-8">
                                                    <b-form-textarea v-model="form_igd.neurologist_status" rows="2" autocomplete="off" />
                                                </div>
                                            </div>
                                            <div class="mt-32">
                                                <ModernLabel label="Status oftalmologis" />
                                                <div class="mt-12 mb-8">
                                                    <b-form-textarea v-model="form_igd.ophthal_status" rows="2" autocomplete="off" />
                                                </div>

                                                <b-collapse id="collapse-ofta" v-model="active.advance.oftalmologis" class="mb-8">
                                                    <div>
                                                        <b-table-simple bordered responsive>
                                                            <b-thead>
                                                                <b-tr class="text-center">
                                                                    <b-th style="width: calc(100%/3)" class="bg-warning-200">OD</b-th>
                                                                    <b-th style="width: calc(100%/3)" class="bg-primary-100">Pemeriksaan</b-th>
                                                                    <b-th style="width: calc(100%/3)" class="bg-warning-200">OS</b-th>
                                                                </b-tr>
                                                            </b-thead>
                                                            <b-tbody>
                                                                <b-tr>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_od.visus" :options="OptionsOftalVisus" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                    <b-th class="text-center bg-primary-100">Visus</b-th>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_os.visus" :options="OptionsOftalVisus" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                </b-tr>
                                                                <b-tr>
                                                                    <b-td>
                                                                        <div>
                                                                            <label class="c-form-label">Digital</label>
                                                                            <v-select v-model="ophInput.ophthal_od.tio_digital" :options="OptionsOftalTIO" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                        <div class="mt-12">
                                                                            <label class="c-form-label">Tonometri Sciotz</label>
                                                                            <b-form-textarea v-model="ophInput.ophthal_od.tio_tonometri_sciots" rows="2" autocomplete="off" />
                                                                        </div>
                                                                    </b-td>
                                                                    <b-th class="text-center bg-primary-100">TIO</b-th>
                                                                    <b-td>
                                                                        <div>
                                                                            <label class="c-form-label">Digital</label>
                                                                            <v-select v-model="ophInput.ophthal_os.tio_digital" :options="OptionsOftalTIO" label="title" :reduce="item => item.value" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                        <div class="mt-12">
                                                                            <label class="c-form-label">Tonometri Sciotz</label>
                                                                            <b-form-textarea v-model="ophInput.ophthal_os.tio_tonometri_sciots" rows="2" autocomplete="off" />
                                                                        </div>
                                                                    </b-td>
                                                                </b-tr>
                                                                <b-tr>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_od.eye_position" :options="OptionsOftalEyePosition" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                    <b-th class="text-center bg-primary-100">Posisi Mata</b-th>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_os.eye_position" :options="OptionsOftalEyePosition" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                </b-tr>
                                                                <b-tr>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_od.eye_movement" :options="OptionsOftalMovement" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                    <b-th class="text-center bg-primary-100">Pergerakan Mata</b-th>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_os.eye_movement" :options="OptionsOftalMovement" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                </b-tr>
                                                                <b-tr>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_od.super_silia" :options="OptionsOftalSuperSilia" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                    <b-th class="text-center bg-primary-100">Super Silia</b-th>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_os.super_silia" :options="OptionsOftalSuperSilia" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                </b-tr>
                                                                <b-tr>
                                                                    <b-td>
                                                                        <div>
                                                                            <label class="c-form-label">Edema</label>
                                                                            <v-select v-model="ophInput.ophthal_od.palpebra_edema" :options="OptionsOftalPalpebraEdema" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                        <div class="mt-12">
                                                                            <label class="c-form-label">Ptosis</label>
                                                                            <v-select v-model="ophInput.ophthal_od.palpebra_ptosis" :options="OptionsOftalPalpebraPtosis" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                        <div class="mt-12">
                                                                            <label class="c-form-label">Lagophtalmus</label>
                                                                            <v-select v-model="ophInput.ophthal_od.palpebra_lagophtalmus" :options="OptionsOftalPalpebraLagophtalmus" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                        <div class="mt-12">
                                                                            <label class="c-form-label">Tumor</label>
                                                                            <v-select v-model="ophInput.ophthal_od.palpebra_tumor" :options="OptionsOftalPalpebraTumor" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                    </b-td>
                                                                    <b-th class="text-center bg-primary-100">Palpebra</b-th>
                                                                    <b-td>
                                                                        <div>
                                                                            <label class="c-form-label">Edema</label>
                                                                            <v-select v-model="ophInput.ophthal_os.palpebra_edema" :options="OptionsOftalPalpebraEdema" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                        <div class="mt-12">
                                                                            <label class="c-form-label">Ptosis</label>
                                                                            <v-select v-model="ophInput.ophthal_os.palpebra_ptosis" :options="OptionsOftalPalpebraPtosis" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                        <div class="mt-12">
                                                                            <label class="c-form-label">Lagophtalmus</label>
                                                                            <v-select v-model="ophInput.ophthal_os.palpebra_lagophtalmus" :options="OptionsOftalPalpebraLagophtalmus" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                        <div class="mt-12">
                                                                            <label class="c-form-label">Tumor</label>
                                                                            <v-select v-model="ophInput.ophthal_os.palpebra_tumor" :options="OptionsOftalPalpebraTumor" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                    </b-td>
                                                                </b-tr>
                                                                <b-tr>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_od.apparatus_lakrimalis" :options="OptionsOftalApparetus" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                    <b-th class="text-center bg-primary-100">Apparatus Lakrimalis</b-th>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_os.apparatus_lakrimalis" :options="OptionsOftalApparetus" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                </b-tr>
                                                                <b-tr>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_od.silia" :options="OptionsOftalSilia" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                    <b-th class="text-center bg-primary-100">Silia</b-th>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_os.silia" :options="OptionsOftalSilia" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>                                                                    
                                                                    </b-td>
                                                                </b-tr>
                                                                <b-tr>
                                                                    <b-td>
                                                                        <div>
                                                                            <label class="c-form-label">Hiperemis</label>
                                                                            <v-select v-model="ophInput.ophthal_od.conjunctive_hiperemis" :options="OptionsOftalConjHiperemis" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                        <div class="mt-12">
                                                                            <label class="c-form-label">Pendarahan</label>
                                                                            <v-select v-model="ophInput.ophthal_od.conjunctive_bleed" :options="OptionsOftalConjBleed" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                    </b-td>
                                                                    <b-th class="text-center bg-primary-100">Konjungtiva</b-th>
                                                                    <b-td>
                                                                        <div>
                                                                            <label class="c-form-label">Hiperemis</label>
                                                                            <v-select v-model="ophInput.ophthal_os.conjunctive_hiperemis" :options="OptionsOftalConjHiperemis" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                        <div class="mt-12">
                                                                            <label class="c-form-label">Pendarahan</label>
                                                                            <v-select v-model="ophInput.ophthal_os.conjunctive_bleed" :options="OptionsOftalConjBleed" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                    </b-td>
                                                                </b-tr>
                                                                <b-tr>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_od.cornea" :options="OptionsOftalKornea" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                    <b-th class="text-center bg-primary-100">Kornea</b-th>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_os.cornea" :options="OptionsOftalKornea" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                </b-tr>
                                                                <b-tr>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_od.camera_oculi" :options="OptionsOftalAnterior" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                    <b-th class="text-center bg-primary-100">Camera Oculi Anterior</b-th>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_os.camera_oculi" :options="OptionsOftalAnterior" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                </b-tr>
                                                                <b-tr>
                                                                    <b-td>
                                                                        <div>
                                                                            <label class="c-form-label">Warna Iris</label>
                                                                            <v-select v-model="ophInput.ophthal_od.iris_color" :options="OptionsOftalIrisColor" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                        <div class="mt-12">
                                                                            <label class="c-form-label">Kripte</label>
                                                                            <v-select v-model="ophInput.ophthal_od.iris_cripte" :options="OptionsOftalIrisKripte" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                    </b-td>
                                                                    <b-th class="text-center bg-primary-100">Iris</b-th>
                                                                    <b-td>
                                                                        <div>
                                                                            <label class="c-form-label">Warna Iris</label>
                                                                            <v-select v-model="ophInput.ophthal_os.iris_color" :options="OptionsOftalIrisColor" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                        <div class="mt-12">
                                                                            <label class="c-form-label">Kripte</label>
                                                                            <v-select v-model="ophInput.ophthal_os.iris_cripte" :options="OptionsOftalIrisKripte" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                    </b-td>
                                                                </b-tr>
                                                                <b-tr>
                                                                    <b-td>
                                                                        <div>
                                                                            <label class="c-form-label">Bentuk Pupil</label>
                                                                            <v-select v-model="ophInput.ophthal_od.pupil_form" :options="OptionsOftalPupilForm" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                        <div class="mt-12">
                                                                            <label class="c-form-label">Diameter Pupil</label>
                                                                            <v-select v-model="ophInput.ophthal_od.pupil_diameter" :options="OptionsOftalPupilDiameter" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                        <div class="mt-12">
                                                                            <label class="c-form-label">Refleksi Cahaya Langsung</label>
                                                                            <v-select v-model="ophInput.ophthal_od.pupil_direct_light" :options="OptionsOftalPupilDirectLight" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                        <div class="mt-12">
                                                                            <label class="c-form-label">Refleksi Cahaya Tak Langsung</label>
                                                                            <v-select v-model="ophInput.ophthal_od.pupil_indirect_light" :options="OptionsOftalPupilIndirectLight" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                    </b-td>
                                                                    <b-th class="text-center bg-primary-100">Pupil</b-th>
                                                                    <b-td>
                                                                        <div>
                                                                            <label class="c-form-label">Bentuk Pupil</label>
                                                                            <v-select v-model="ophInput.ophthal_os.pupil_form" :options="OptionsOftalPupilForm" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                        <div class="mt-12">
                                                                            <label class="c-form-label">Diameter Pupil</label>
                                                                            <v-select v-model="ophInput.ophthal_os.pupil_diameter" :options="OptionsOftalPupilDiameter" :reduce="item => item.value"label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                        <div class="mt-12">
                                                                            <label class="c-form-label">Refleksi Cahaya Langsung</label>
                                                                            <v-select v-model="ophInput.ophthal_os.pupil_direct_light" :options="OptionsOftalPupilDirectLight" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                        <div class="mt-12">
                                                                            <label class="c-form-label">Refleksi Cahaya Tak Langsung</label>
                                                                            <v-select v-model="ophInput.ophthal_os.pupil_indirect_light" :options="OptionsOftalPupilIndirectLight" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                        </div>
                                                                    </b-td>
                                                                </b-tr>
                                                                <b-tr>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_od.lens" :options="OptionsOftalLens" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                    <b-th class="text-center bg-primary-100">Lensa</b-th>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_os.lens" :options="OptionsOftalLens" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                </b-tr>
                                                                <b-tr>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_od.shadow_test" :options="OptionsOftalShadow" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                    <b-th class="text-center bg-primary-100">Shadow Test</b-th>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_os.shadow_test" :options="OptionsOftalShadow" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                </b-tr>
                                                                <b-tr>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_od.glandula" :options="OptionsOftalGlandula" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                    <b-th class="text-center bg-primary-100">Glandula pre-aurikuler</b-th>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_os.glandula" :options="OptionsOftalGlandula" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                </b-tr>
                                                                <b-tr>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_od.field_view" :options="OptionsOftalLapang" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                    <b-th class="text-center bg-primary-100">Lapang Pandang</b-th>
                                                                    <b-td>
                                                                        <v-select v-model="ophInput.ophthal_os.field_view" :options="OptionsOftalLapang" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                    </b-td>
                                                                </b-tr>
                                                                <b-tr>
                                                                    <b-td>
                                                                        <b-form-textarea v-model="ophInput.ophthal_od.fundus" rows="2" autocomplete="off" />
                                                                    </b-td>
                                                                    <b-th class="text-center bg-primary-100">Fundus</b-th>
                                                                    <b-td>
                                                                        <b-form-textarea v-model="ophInput.ophthal_os.fundus" rows="2" autocomplete="off" />                                                                    
                                                                    </b-td>
                                                                </b-tr>
                                                                <b-tr>
                                                                    <b-td colspan="3">
                                                                        <div class="d-flex justify-content-center">
                                                                            <!-- <b-button variant="outline-primary" class="mr-2">Reset</b-button> -->
                                                                            <b-button @click='validationOphtal' :disabled="disabledOphtal" variant="primary">Simpan Advance Oftalmologis</b-button>
                                                                        </div>
                                                                    </b-td>
                                                                </b-tr>
                                                            </b-tbody>
                                                        </b-table-simple>
                                                    </div>                                                
                                                </b-collapse>

                                                <small @click="active.advance.oftalmologis = !active.advance.oftalmologis" class="font-weight-bold text-primary pointer" :class="active.advance.oftalmologis ? null : 'collapsed'" :aria-expanded="active.advance.oftalmologis ? 'true' : 'false'" aria-controls="collapse-ofta">
                                                    Advance status oftalmologis 
                                                    <b-icon :icon="active.advance.oftalmologis == true ? 'chevron-up' : 'chevron-down'" />
                                                </small>
                                            </div>
                                        </b-collapse>
                                        <small @click="active.advance.generalis = !active.advance.generalis" class="font-weight-bold text-primary pointer" :class="active.advance.generalis ? null : 'collapsed'" :aria-expanded="active.advance.generalis ? 'true' : 'false'" aria-controls="collapse-general">
                                            Advance status generalis 
                                            <b-icon :icon="active.advance.generalis == true ? 'chevron-up' : 'chevron-down'" />
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="card mt-20">
                    <div class="card-header bg-indigo-200">
                        <span class="text-bold-18">Assessment</span>
                    </div>
                    <div class="card-body">
                        <div>
                            <ModernLabel label="Diagnosis (ICD10)" />
                            <b-table bordered responsive :fields="diagnoseField" :items="form_igd.icd10" class="mt-12 fs-14 text-center">
                                <template #cell(no)="row">{{ row.index+1 }}</template>
                                <template #cell(title)="row">
                                    <div class="text-left">{{ row.value }}</div>
                                </template>
                                <template #cell(action)="row">
                                    <b-button variant="danger-600" size="sm" @click="deleteIGD10(row.index)">
                                        <feather-icon icon="TrashIcon" />
                                    </b-button>
                                </template>
                            </b-table>

                            <div class="mt-10">
                                <label class="text-semi-14">Diagnosa</label>
                                <v-select v-model="IGDicd10_id" aria-readonly="true" @input="onIGDAdd10($event)" @search="onIGDSearchIcd10" :options="OptionsIGDIcd10" :reduce="OptionsIGDIcd10 => OptionsIGDIcd10" label="title" placeholder="-- Diagnosa --"></v-select>
                            </div>
                        </div>

                        <b-textarea rows="2" type="text" v-model="form_igd.icd10_notes" autocomplete="off" placeholder="Catatan ICD10" class="mt-12"></b-textarea>

                        <div class="mt-24" >
                            <ModernLabel label="Diagnosis Non-ICD10" />
                            <b-table-simple bordered responsive class="mt-12 fs-14 text-center">
                                <b-tr>
                                    <b-th style="width: 60px;">No</b-th>
                                    <b-th class="text-left">Non-ICD10</b-th>
                                    <b-th style="width: 150px;">Aksi</b-th>
                                </b-tr>
                                <template v-for="(item,index) in form_igd.non_icd10">
                                    <b-tr class="item">
                                        <b-td>{{ form_igd.icd10.length+index+1 }}</b-td>
                                        <b-td class="text-left">{{ item }}</b-td>
                                        <b-td>
                                            <b-button variant="danger-600" size="sm" @click="removeOther10(index)">
                                                <feather-icon icon="TrashIcon" />
                                            </b-button>
                                        </b-td>
                                    </b-tr>
                                </template>
                            </b-table-simple>
                            <div class="mt-10">
                                <label class="text-semi-14">Non-ICD10</label>
                                <b-form-input v-model="input_other10" />
                                <b-button @click="addOther10" size="sm" variant="outline-primary" class="mt-12">Tambah</b-button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Start: Dokter IGD -->
                <section class="mt-20 mt-lg-32">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <span class="text-bold-18">DOKTER IGD: {{ form_igd.doctor_in_charge_name }}</span>
                        </div>
                        <div class="card-body">
                            <section>
                                <div class="card">
                                    <div class="card-header bg-indigo-200">
                                        <span class="text-bold-18">Plan</span>
                                    </div>
                                    <div class="card-body">
                                        <div>
                                            <ModernLabel label="Tindakan (ICD9CM)" />
                                            <div>
                                                <textarea v-model.lazy="form_icd9cm.therapy_plan" autocomplete="off" placeholder="Catatan ICD9CM" class="form-control mt-12" />                                            
                                                <b-table :fields="icd9cmFields" :items="form_icd9cm.icd9cm" bordered responsive class="mt-12 fs-14 text-center">
                                                    <template #cell(no)="row">{{ row.index + 1 }}</template>
                                                    <template #cell(title)="row">
                                                        <div class="text-left">{{ row.value }}</div>
                                                    </template>
                                                    <template #cell(action)="row">
                                                        <b-button variant="danger-600" size="sm" @click="removeIGD9cm(row.index)">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </template>
                                                </b-table>

                                                <div class="mt-10">
                                                    <v-select v-model="IGDicd9cm_id" @input="onIGDAdd9cm($event)" @search="onIGDSearchIcd9cm" :options="OptionsIGDIcd9cm" :reduce="item => item" label="title" placeholder="pilih icd9cm" />
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end mt-24">
                                                <b-button @click="validationIGDSoapP" :disabled="IGDdisabled.soap_p" variant="primary" class="px-16">SIMPAN</b-button>
                                            </div>
                                        </div>

                                        <div class="mt-32">
                                            <ModernLabel label="PROSEDUR" />
                                            <b-button @click="openIGDProcedure()" pill variant="outline-neutral-400" size="sm" class="px-16 mt-12">
                                                <feather-icon icon="PlusIcon" /> <span>Tambah Prosedur</span>
                                            </b-button>
                                            <b-table bordered responsive :fields="procedureFields" :items="transaction_list" class="mt-12 fs-14 text-center">
                                                <template #cell(no)="row">{{ row.index + 1 }}</template>
                                                <template #cell(costing_name)="row">
                                                    <div class="text-left">{{ row.value }}</div>
                                                </template>
                                                <template #cell(notes)="row">
                                                    <div class="text-left">{{ row.value }}</div>
                                                </template>
                                                <template #cell(action)="row">
                                                    <b-button @click="onEditIGDProcedure(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                        <feather-icon icon="EditIcon" />
                                                    </b-button>
                                                    <b-button @click="removeIGDProcedure(row.item)" size="sm" variant="danger-700">
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </template>
                                            </b-table>
                                        </div>

                                        <hr class="my-32 w-100">

                                        <section>
                                            <ModernLabel label="Laboratorium" />
                                            <div>
                                                <b-button @click="openIGDLab()" pill variant="outline-neutral-400" size="sm" class="px-16 mt-12">
                                                    <feather-icon icon="PlusIcon" /> <span>Tambah Order Laboratorium</span>
                                                </b-button>

                                                <b-table :fields="laboratoryFields" :items="laboratory_igd_list" :tbody-tr-class="rowLabStatus" bordered responsive class="mt-12 fs-14 text-center">
                                                    <template #cell(no)="row">{{ row.index+1 }}</template>
                                                    <template #cell(laboratory_request_item)="row">
                                                        <li v-for="(item, index) in row.value" :key="index" class="text-left">{{ item.costing_name }}</li>
                                                    </template>
                                                    <template #cell(action)="row">
                                                        <div v-if="row.item.status == 2 || row.item.status == 3">Order telah diproses</div>
                                                        <div v-else class="d-flex justify-content-center">
                                                            <b-button @click="onEditIGDLab(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                                <feather-icon icon="EditIcon" />
                                                            </b-button>
                                                            <b-button @click="removeIGDLab(row.item)" size="sm" variant="danger-700">
                                                                <feather-icon icon="TrashIcon" />
                                                            </b-button>
                                                        </div>
                                                    </template>
                                                </b-table>
                                            </div>
                                        </section>

                                        <section class="mt-32">
                                            <ModernLabel label="Radiologi" />
                                            <div>
                                                <b-button @click="openIGDRad()" pill variant="outline-neutral-400" size="sm" class="px-16 mt-12">
                                                    <feather-icon icon="PlusIcon" /> <span>Tambah Order Radiologi</span>
                                                </b-button>

                                                <b-table :fields="radiologyFields" :items="radiology_igd_list" :tbody-tr-class="rowRadStatus" bordered responsive class="mt-12 fs-14 text-center">
                                                    <template #cell(no)="row">{{ row.index+1 }}</template>
                                                    <template #cell(radiology_request_item)="row">
                                                        <li v-for="(item, index) in row.value" :key="index" class="text-left">{{ item.costing_name }}</li>
                                                    </template>
                                                    <template #cell(clinical_indications)="row">
                                                        <div class="text-left">{{ row.item.clinical_indications }}</div>
                                                    </template>
                                                    <template #cell(action)="row">
                                                        <div v-if="row.item.status == 2 || row.item.status == 3">Order telah diproses</div>
                                                        <div v-else class="d-flex justify-content-center">
                                                            <b-button @click="onEditIGDRad(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                                <feather-icon icon="EditIcon" />
                                                            </b-button>
                                                            <b-button @click="removeIGDRad(row.item)" size="sm" variant="danger-700">
                                                                <feather-icon icon="TrashIcon" />
                                                            </b-button>
                                                        </div>
                                                    </template>
                                                </b-table>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </section>

                            <section class="card mt-32">
                                <div class="card-header bg-indigo-200">
                                    <span class="text-bold-18">Rencana Terapi E-Resep</span>
                                </div>
                                <div class="card-body">
                                    <b-button @click="openIGDPresc" pill variant="outline-neutral-400" size="sm" class="px-16">
                                        <feather-icon icon="PlusIcon" /> <span>Tambah Resep</span>
                                    </b-button>
                                    
                                    <b-table :fields="precriptionFields" :items="prescription_igd_list" :tbody-tr-class="rowPrescStatus" bordered responsive class="mt-12 fs-14 text-center">
                                        <template #cell(no)="row">{{ row.index+1 }}</template>
                                        <template #cell(form_prescription)="row">
                                            <div v-for="(category, index) in row.item.form_prescription_parent" :key="index" class="text-left" :class="{'mt-8' : index > 0 }" v-if="hasItemsInCategory(row.value, category.id)">
                                                <b-table-simple responsive small class="text-center fs-13 mb-0">
                                                    <b-thead>
                                                        <b-tr class="text-left">
                                                            <b-th colspan="7" :class="getParentVariant(category.id)">
                                                                <div class="text-uppercase">{{ getParentName(category.id) }}</div>
                                                                <div v-if="category.id != 1" class="fs-12 text-italic" style="font-weight: 400">  
                                                                    <span>
                                                                        Status: {{ getKardexItemStatusName(category?.status_kardex) }}, 
                                                                        Kemasan: {{ getParentPackageName(category?.package) }}, 
                                                                        {{ category?.value ?? '-' }}
                                                                        TIAP {{ getDynamicOptName(category?.signatura_times_id, optSignTime)}} 
                                                                        {{ getDynamicOptName(category?.administer_id, optAdminister) }}
                                                                        <span v-if="category.administer_id == other_administer">: {{ category.desc }}</span>
                                                                    </span>
                                                                </div>
                                                            </b-th>
                                                        </b-tr>
                                                        <b-tr>
                                                            <b-th class="text-left">NAMA OBAT</b-th>
                                                            <b-th style="width: 160px">STATUS</b-th>
                                                            <!-- <b-th style="width: 160px">KEMASAN / SATUAN</b-th> -->
                                                            <b-th style="width: 60px">JUMLAH</b-th>
                                                            <b-th style="width: 60px">DOSIS</b-th>
                                                            <b-th style="width: 110px">SATUAN DOSIS</b-th>
                                                            <b-th style="width: 90px">WAKTU</b-th>
                                                            <b-th style="width: 200px">ADMINISTER</b-th>
                                                        </b-tr>
                                                    </b-thead>
                                                    <b-tbody>
                                                        <template v-for="(item, itemIndex) in row.value">
                                                            <b-tr :key="itemIndex" v-if="item.prescription_item_type_id == category.id">
                                                                <b-td class="text-left">{{ item.pharmacy_catalog_name }}</b-td>
                                                                <b-td>{{ item.prescription_item_type_id == 1 ? getKardexItemStatusName(item?.status_kardex) : '-' }}</b-td>
                                                                <!-- <b-td>{{ item.package_name }}</b-td> -->
                                                                <b-td>{{ item.qty }}</b-td>
                                                                <b-td>{{ item.dose }}</b-td>
                                                                <b-td>{{ item.dose_unit_name }}</b-td>
                                                                <b-td>{{ item.prescription_item_type_id == 1 ? `TIAP ${item.signatura_times_name || '-'}` : '-' }}</b-td>
                                                                <b-td>
                                                                    <template v-if="item.prescription_item_type_id == 1">
                                                                        {{ item.administer_name }}
                                                                        <span v-if="item.administer_id == other_administer">: {{ item.desc }}</span>
                                                                    </template>
                                                                    <div v-else>-</div>
                                                                </b-td>
                                                            </b-tr>
                                                        </template>
                                                    </b-tbody>
                                                </b-table-simple>
                                            </div>
                                        </template>
                                        
                                        <template #cell(action)="row">
                                            <div v-if="row.item.flag == 0">
                                                <b-button @click="editIGDPresc(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                    <feather-icon icon="EditIcon" />
                                                </b-button>
                                                <b-button @click="removeIGDPresc(row.item)" size="sm" variant="danger-700">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </div>
                                            <div v-else>Resep telah diproses</div>
                                        </template>
                                    </b-table>                                    
                                </div>
                            </section>  
                        </div>
                    </div>
                </section>

                <!-- Start: Dokter spesialis 1 -->
                <section class="mt-20 mt-lg-32">
                    <div class="card">
                        <div class="card-header bg-indigo-200">
                            <span class="text-bold-18">Tambah Dokter Spesialis 1</span>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-lg-4">
                                    <label class="c-form-label">Dokter Spesialis 1</label>
                                    <v-select :disabled="specialist.disabled_doctor_1" @input="changeSpecialistDoctor(1)" v-model="specialist.doctor_1" :options="doctor_list" :reduce="item => item" label="title" placeholder="-- Dokter --" />
                                </div>
                                <div v-if="form_igd.doctor_in_charge_id_1" class="col-2">
                                    <b-button @click="enableSpecialistDoctor(1)" :variant="getToggleVariant(1)">
                                        <feather-icon :icon="getToggleIcon(1)" />
                                    </b-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section v-if="form_igd.doctor_in_charge_id_1" class="mt-20 mt-lg-32">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <span class="text-bold-18">Dokter Spesialis 1: {{ form_igd.doctor_in_charge_name_1 }}</span>
                        </div>
                        <div class="card-body">
                            <section class="card">
                                <div class="card-header bg-indigo-200">
                                    <span class="text-bold-18">Plan</span>
                                </div>
                                <div class="card-body">
                                    <div>
                                        <ModernLabel label="Tindakan (ICD9CM)" />
                                        <div>
                                            <textarea v-model.lazy="form_FIRST_icd9cm.therapy_plan" autocomplete="off" placeholder="Catatan ICD9CM" class="form-control mt-12" />                                            
                                            <b-table :fields="icd9cmFields" :items="form_FIRST_icd9cm.icd9cm" bordered responsive class="mt-12 fs-14 text-center">
                                                <template #cell(no)="row">{{ row.index + 1 }}</template>
                                                <template #cell(title)="row">
                                                    <div class="text-left">{{ row.value }}</div>
                                                </template>
                                                <template #cell(action)="row">
                                                    <b-button variant="danger-600" size="sm" @click="deleteFIRST9cm(row.index)">
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </template>
                                            </b-table>
                                            <div class="mt-10">
                                                <v-select v-model="FIRSTicd9cm_id" @input="onFIRSTAdd9cm($event)" @search="onFIRSTSearchIcd9cm" :options="OptionsFIRSTIcd9cm" :reduce="item => item" label="title" placeholder="pilih icd9cm" />
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mt-24">
                                            <b-button @click="validationFIRSTSoapP" :disabled="FIRSTdisabled.soap_p" variant="primary" class="px-16">SIMPAN</b-button>
                                        </div>
                                    </div>

                                    <div class="mt-32">
                                        <ModernLabel label="Prosedur" />
                                        <b-button @click="openFIRSTProcedure()" pill variant="outline-neutral-400" size="sm" class="px-16 mt-12">
                                            <feather-icon icon="PlusIcon" /> <span>Tambah Prosedur</span>
                                        </b-button>
                                        <b-table bordered responsive :fields="procedureFields" :items="transaction_FIRST_list" class="mt-12 fs-14 text-center">
                                            <template #cell(no)="row">{{ row.index + 1 }}</template>
                                            <template #cell(costing_name)="row">
                                                <div class="text-left">{{ row.value }}</div>
                                            </template>
                                            <template #cell(notes)="row">
                                                <div class="text-left">{{ row.value }}</div>
                                            </template>
                                            <template #cell(action)="row">
                                                <b-button @click="onEditFIRSTProcedure(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                    <feather-icon icon="EditIcon" />
                                                </b-button>
                                                <b-button @click="removeFIRSTProcedure(row.item)" size="sm" variant="danger-700">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </template>
                                        </b-table>
                                    </div>

                                    <section>
                                        <ModernLabel label="Laboratorium" />
                                        <div>
                                            <b-button @click="openFIRSTLab()" pill variant="outline-neutral-400" size="sm" class="px-16 mt-12">
                                                <feather-icon icon="PlusIcon" /> <span>Tambah Order Laboratorium</span>
                                            </b-button>

                                            <b-table :fields="laboratoryFields" :items="laboratory_FIRST_list" :tbody-tr-class="rowLabStatus" bordered responsive  class="mt-12 fs-14 text-center">
                                                <template #cell(no)="row">{{ row.index+1 }}</template>                                                
                                                <template #cell(laboratory_request_item)="row">
                                                    <li v-for="(item, index) in row.value" :key="index" class="text-left">{{ item.costing_name }}</li>
                                                </template>
                                                <template #cell(action)="row">
                                                    <div v-if="row.item.status == 2 || row.item.status == 3">Order telah diproses</div>
                                                    <div v-else class="d-flex justify-content-center">
                                                        <b-button @click="onEditFIRSTLab(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                            <feather-icon icon="EditIcon" />
                                                        </b-button>
                                                        <b-button @click="removeFIRSTLab(row.item)" size="sm" variant="danger-700">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </div>
                                                </template>
                                            </b-table>
                                        </div>
                                    </section>

                                    <section>
                                        <ModernLabel label="Radiologi" />
                                        <div>
                                            <b-button @click="openFIRSTRad()" pill variant="outline-neutral-400" size="sm" class="px-16 mt-12">
                                                <feather-icon icon="PlusIcon" /> <span>Tambah Order Radiologi</span>
                                            </b-button>

                                            <b-table :fields="radiologyFields" :items="radiology_FIRST_list" :tbody-tr-class="rowRadStatus" bordered responsive class="mt-12 fs-14 text-center">
                                                <template #cell(no)="row">{{ row.index+1 }}</template>
                                                <template #cell(radiology_request_item)="row">
                                                    <li v-for="(item, index) in row.value" :key="index" class="text-left">{{ item.costing_name }}</li>                                                    
                                                </template>
                                                <template #cell(action)="row">
                                                    <div v-if="row.item.status == 2 || row.item.status == 3">Order telah diproses</div>
                                                    <div v-else class="d-flex justify-content-center">
                                                        <b-button @click="onEditFIRSTRad(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                            <feather-icon icon="EditIcon" />
                                                        </b-button>
                                                        <b-button @click="removeFIRSTRad(row.item)" size="sm" variant="danger-700">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </div>
                                                </template>
                                            </b-table>
                                        </div>
                                    </section>
                                </div>
                            </section>

                            <section class="card mt-32">
                                <div class="card-header bg-indigo-200">
                                    <span class="text-bold-18">Rencana Terapi E-Resep</span>
                                </div>
                                <div class="card-body">
                                    <b-button @click="openFIRSTPresc" pill variant="outline-neutral-400" size="sm" class="px-16">
                                        <feather-icon icon="PlusIcon" /> <span>Tambah Resep</span>
                                    </b-button>
                                    
                                    <b-table :fields="precriptionFields" :items="prescription_FIRST_list" :tbody-tr-class="rowPrescStatus" bordered responsive class="mt-12 fs-14 text-center">
                                        <template #cell(no)="row">{{ row.index+1 }}</template>
                                        <template #cell(form_prescription)="row">
                                            <div v-for="(category, index) in row.item.form_prescription_parent" :key="index" class="text-left" :class="{'mt-8' : index > 0 }" v-if="hasItemsInCategory(row.value, category.id)">
                                                <b-table-simple responsive small class="text-center fs-13 mb-0">
                                                    <b-thead>
                                                        <b-tr class="text-left">
                                                            <b-th colspan="7" :class="getParentVariant(category.id)">
                                                                <div class="text-uppercase">{{ getParentName(category.id) }}</div>
                                                                <div v-if="category.id != 1" class="fs-12 text-italic" style="font-weight: 400">  
                                                                    <span>
                                                                        Status: {{ getKardexItemStatusName(category?.status_kardex) }}, 
                                                                        Kemasan: {{ getParentPackageName(category?.package) }}, 
                                                                        {{ category?.value ?? '-' }}
                                                                        TIAP {{ getDynamicOptName(category?.signatura_times_id, optSignTime)}} 
                                                                        {{ getDynamicOptName(category?.administer_id, optAdminister) }}
                                                                        <span v-if="category.administer_id == other_administer">: {{ category.desc }}</span>
                                                                    </span>
                                                                </div>
                                                            </b-th>
                                                        </b-tr>
                                                        <b-tr>
                                                            <b-th class="text-left">NAMA OBAT</b-th>
                                                            <b-th style="width: 160px">STATUS</b-th>
                                                            <!-- <b-th style="width: 160px">KEMASAN / SATUAN</b-th> -->
                                                            <b-th style="width: 60px">JUMLAH</b-th>
                                                            <b-th style="width: 60px">DOSIS</b-th>
                                                            <b-th style="width: 110px">SATUAN DOSIS</b-th>
                                                            <b-th style="width: 90px">WAKTU</b-th>
                                                            <b-th style="width: 200px">ADMINISTER</b-th>
                                                        </b-tr>
                                                    </b-thead>
                                                    <b-tbody>
                                                        <template v-for="(item, itemIndex) in row.value">
                                                            <b-tr :key="itemIndex" v-if="item.prescription_item_type_id == category.id">
                                                                <b-td class="text-left">{{ item.pharmacy_catalog_name }}</b-td>
                                                                <b-td>{{ item.prescription_item_type_id == 1 ? getKardexItemStatusName(item?.status_kardex) : '-' }}</b-td>
                                                                <!-- <b-td>{{ item.package_name }}</b-td> -->
                                                                <b-td>{{ item.qty }}</b-td>
                                                                <b-td>{{ item.dose }}</b-td>
                                                                <b-td>{{ item.dose_unit_name }}</b-td>
                                                                <b-td>{{ item.prescription_item_type_id == 1 ? `TIAP ${item.signatura_times_name || '-'}` : '-' }}</b-td>
                                                                <b-td>
                                                                    <template v-if="item.prescription_item_type_id == 1">
                                                                        {{ item.administer_name }}
                                                                        <span v-if="item.administer_id == other_administer">: {{ item.desc }}</span>
                                                                    </template>
                                                                    <div v-else>-</div>
                                                                </b-td>
                                                            </b-tr>
                                                        </template>
                                                    </b-tbody>
                                                </b-table-simple>
                                            </div>
                                        </template>
                                        
                                        <template #cell(action)="row">
                                            <div v-if="row.item.flag == 0">
                                                <b-button @click="editFIRSTPresc(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                    <feather-icon icon="EditIcon" />
                                                </b-button>
                                                <b-button @click="removeFIRSTPresc(row.item)" size="sm" variant="danger-700">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </div>
                                            <div v-else>Resep telah diproses</div>
                                        </template>
                                    </b-table>                                    
                                </div>
                            </section>                         
                        </div>
                    </div>
                </section>

                <!-- Start: Dokter spesialis 2 -->
                <section v-if="form_igd.doctor_in_charge_id_1" class="mt-20 mt-lg-32">
                    <div class="card">
                        <div class="card-header bg-indigo-200">
                            <span class="text-bold-18">Tambah Dokter Spesialis 2</span>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-lg-4">
                                    <label class="c-form-label">Dokter Spesialis 2</label>
                                    <v-select :disabled="specialist.disabled_doctor_2" v-model="specialist.doctor_2" :options="doctor_list" :reduce="item => item" label="title" placeholder="-- Dokter --" />
                                </div>
                                <div class="col-lg-2">
                                    <label class="c-form-label">Konsultasi yang diminta</label>
                                    <v-select :disabled="specialist.disabled_doctor_2" v-model="specialist.type_consultation_doctor_2" :options="optCunsultType" :reduce="item => item" label="title" placeholder="-- konsultasi --" />
                                </div>
                                <div class="px-16">
                                    <b-button :disabled="specialist.disabled_doctor_2" @click="changeSpecialistDoctor(2)" variant="primary">SIMPAN</b-button>
                                </div>
                                <div v-if="form_igd.doctor_in_charge_id_2">
                                    <b-button @click="enableSpecialistDoctor(2)" :variant="getToggleVariant(2)">
                                        <feather-icon :icon="getToggleIcon(2)" />
                                    </b-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section v-if="form_igd.doctor_in_charge_id_2" class="mt-20 mt-lg-32">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <span class="text-bold-18">Dokter Spesialis 2: {{ form_igd.doctor_in_charge_name_2 }}</span>
                        </div>
                        <div class="card-body">
                            <section class="card">
                                <div class="card-header bg-indigo-200">
                                    <span class="text-bold-18">Plan</span>
                                </div>
                                <div class="card-body">
                                    <div>
                                        <ModernLabel label="Tindakan (ICD9CM)" />
                                        <div>
                                            <textarea v-model.lazy="form_SECOND_icd9cm.therapy_plan" autocomplete="off" placeholder="Catatan ICD9CM" class="form-control mt-12" />                                            
                                            <b-table :fields="icd9cmFields" :items="form_SECOND_icd9cm.icd9cm" bordered responsive class="mt-12 fs-14 text-center">
                                                <template #cell(no)="row">{{ row.index + 1 }}</template>
                                                <template #cell(title)="row">
                                                    <div class="text-left">{{ row.value }}</div>
                                                </template>
                                                <template #cell(action)="row">
                                                    <b-button variant="danger-600" size="sm" @click="deleteSECOND9cm(row.index)">
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </template>
                                            </b-table>
                                            <div class="mt-10">
                                                <v-select v-model="SECONDicd9cm_id" @input="onSECONDAdd9cm($event)" @search="onSECONDSearchIcd9cm" :options="OptionsSECONDIcd9cm" :reduce="item => item" label="title" placeholder="pilih icd9cm" />
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mt-24">
                                            <b-button @click="validationSECONDSoapP" :disabled="SECONDdisabled.soap_p" variant="primary" class="px-16">SIMPAN</b-button>
                                        </div>
                                    </div>

                                    <div class="mt-32">
                                        <ModernLabel label="Prosedur" />
                                        <b-button @click="openSECONDProcedure()" pill variant="outline-neutral-400" size="sm" class="px-16 mt-12">
                                            <feather-icon icon="PlusIcon" /> <span>Tambah Prosedur</span>
                                        </b-button>
                                        <b-table bordered responsive :fields="procedureFields" :items="transaction_SECOND_list" class="mt-12 fs-14 text-center">
                                            <template #cell(no)="row">{{ row.index + 1 }}</template>
                                            <template #cell(costing_name)="row">
                                                <div class="text-left">{{ row.value }}</div>
                                            </template>
                                            <template #cell(notes)="row">
                                                <div class="text-left">{{ row.value }}</div>
                                            </template>
                                            <template #cell(action)="row">
                                                <b-button @click="onEditSECONDProcedure(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                    <feather-icon icon="EditIcon" />
                                                </b-button>
                                                <b-button @click="removeSECONDProcedure(row.item)" size="sm" variant="danger-700">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </template>
                                        </b-table>
                                    </div>

                                    <section>
                                        <ModernLabel label="Laboratorium" />
                                        <div>
                                            <b-button @click="openSECONDLab()" pill variant="outline-neutral-400" size="sm" class="px-16 mt-12">
                                                <feather-icon icon="PlusIcon" /> <span>Tambah Order Laboratorium</span>
                                            </b-button>

                                            <b-table :fields="laboratoryFields" :items="laboratory_SECOND_list" :tbody-tr-class="rowLabStatus" bordered responsive  class="mt-12 fs-14 text-center">
                                                <template #cell(no)="row">{{ row.index+1 }}</template>                                                
                                                <template #cell(laboratory_request_item)="row">
                                                    <li v-for="(item, index) in row.value" :key="index" class="text-left">{{ item.costing_name }}</li>
                                                </template>
                                                <template #cell(action)="row">
                                                    <div v-if="row.item.status == 2 || row.item.status == 3">Order telah diproses</div>
                                                    <div v-else class="d-flex justify-content-center">
                                                        <b-button @click="onEditSECONDLab(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                            <feather-icon icon="EditIcon" />
                                                        </b-button>
                                                        <b-button @click="removeSECONDLab(row.item)" size="sm" variant="danger-700">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </div>
                                                </template>
                                            </b-table>
                                        </div>
                                    </section>

                                    <section>
                                        <ModernLabel label="Radiologi" />
                                        <div>
                                            <b-button @click="openSECONDRad()" pill variant="outline-neutral-400" size="sm" class="px-16 mt-12">
                                                <feather-icon icon="PlusIcon" /> <span>Tambah Order Radiologi</span>
                                            </b-button>

                                            <b-table :fields="radiologyFields" :items="radiology_SECOND_list" :tbody-tr-class="rowRadStatus" bordered responsive class="mt-12 fs-14 text-center">
                                                <template #cell(no)="row">{{ row.index+1 }}</template>
                                                <template #cell(radiology_request_item)="row">
                                                    <li v-for="(item, index) in row.value" :key="index" class="text-left">{{ item.costing_name }}</li>                                                    
                                                </template>
                                                <template #cell(action)="row">
                                                    <div v-if="row.item.status == 2 || row.item.status == 3">Order telah diproses</div>
                                                    <div v-else class="d-flex justify-content-center">
                                                        <b-button @click="onEditSECONDRad(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                            <feather-icon icon="EditIcon" />
                                                        </b-button>
                                                        <b-button @click="removeSECONDRad(row.item)" size="sm" variant="danger-700">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </div>
                                                </template>
                                            </b-table>
                                        </div>
                                    </section>
                                </div>
                            </section>

                            <section class="card mt-32">
                                <div class="card-header bg-indigo-200">
                                    <span class="text-bold-18">Rencana Terapi E-Resep</span>
                                </div>
                                <div class="card-body">
                                    <b-button @click="openSECONDPresc" pill variant="outline-neutral-400" size="sm" class="px-16">
                                        <feather-icon icon="PlusIcon" /> <span>Tambah Resep</span>
                                    </b-button>

                                    <b-table :fields="precriptionFields" :items="prescription_SECOND_list" :tbody-tr-class="rowPrescStatus" bordered responsive class="mt-12 fs-14 text-center">
                                        <template #cell(no)="row">{{ row.index+1 }}</template>
                                        <template #cell(form_prescription)="row">
                                            <div v-for="(category, index) in row.item.form_prescription_parent" :key="index" class="text-left" :class="{'mt-8' : index > 0 }" v-if="hasItemsInCategory(row.value, category.id)">
                                                <b-table-simple responsive small class="text-center fs-13 mb-0">
                                                    <b-thead>
                                                        <b-tr class="text-left">
                                                            <b-th colspan="7" :class="getParentVariant(category.id)">
                                                                <div class="text-uppercase">{{ getParentName(category.id) }}</div>
                                                                <div v-if="category.id != 1" class="fs-12 text-italic" style="font-weight: 400">  
                                                                    <span>
                                                                        Status: {{ getKardexItemStatusName(category?.status_kardex) }}, 
                                                                        Kemasan: {{ getParentPackageName(category?.package) }}, 
                                                                        {{ category?.value ?? '-' }}
                                                                        TIAP {{ getDynamicOptName(category?.signatura_times_id, optSignTime)}} 
                                                                        {{ getDynamicOptName(category?.administer_id, optAdminister) }}
                                                                        <span v-if="category.administer_id == other_administer">: {{ category.desc }}</span>
                                                                    </span>
                                                                </div>
                                                            </b-th>
                                                        </b-tr>
                                                        <b-tr>
                                                            <b-th class="text-left">NAMA OBAT</b-th>
                                                            <b-th style="width: 160px">STATUS</b-th>
                                                            <!-- <b-th style="width: 160px">KEMASAN / SATUAN</b-th> -->
                                                            <b-th style="width: 60px">JUMLAH</b-th>
                                                            <b-th style="width: 60px">DOSIS</b-th>
                                                            <b-th style="width: 110px">SATUAN DOSIS</b-th>
                                                            <b-th style="width: 90px">WAKTU</b-th>
                                                            <b-th style="width: 200px">ADMINISTER</b-th>
                                                        </b-tr>
                                                    </b-thead>
                                                    <b-tbody>
                                                        <template v-for="(item, itemIndex) in row.value">
                                                            <b-tr :key="itemIndex" v-if="item.prescription_item_type_id == category.id">
                                                                <b-td class="text-left">{{ item.pharmacy_catalog_name }}</b-td>
                                                                <b-td>{{ item.prescription_item_type_id == 1 ? getKardexItemStatusName(item?.status_kardex) : '-' }}</b-td>
                                                                <!-- <b-td>{{ item.package_name }}</b-td> -->
                                                                <b-td>{{ item.qty }}</b-td>
                                                                <b-td>{{ item.dose }}</b-td>
                                                                <b-td>{{ item.dose_unit_name }}</b-td>
                                                                <b-td>{{ item.prescription_item_type_id == 1 ? `TIAP ${item.signatura_times_name || '-'}` : '-' }}</b-td>
                                                                <b-td>
                                                                    <template v-if="item.prescription_item_type_id == 1">
                                                                        {{ item.administer_name }}
                                                                        <span v-if="item.administer_id == other_administer">: {{ item.desc }}</span>
                                                                    </template>
                                                                    <div v-else>-</div>
                                                                </b-td>
                                                            </b-tr>
                                                        </template>
                                                    </b-tbody>
                                                </b-table-simple>
                                            </div>
                                        </template>
                                        
                                        <template #cell(action)="row">
                                            <div v-if="row.item.flag == 0">
                                                <b-button @click="editSECONDPresc(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                    <feather-icon icon="EditIcon" />
                                                </b-button>
                                                <b-button @click="removeSECONDPresc(row.item)" size="sm" variant="danger-700">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </div>
                                            <div v-else>Resep telah diproses</div>
                                        </template>
                                    </b-table>                                  
                                </div>
                            </section>  
                        </div>
                    </div>
                </section>

                <!-- Start: Dokter spesialis 3 -->
                <section v-if="form_igd.doctor_in_charge_id_2" class="mt-20 mt-lg-32">
                    <div class="card">
                        <div class="card-header bg-indigo-200">
                            <span class="text-bold-18">Tambah Dokter Spesialis 3</span>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-lg-4">
                                    <label class="c-form-label">Dokter Spesialis 3</label>
                                    <v-select :disabled="specialist.disabled_doctor_3" v-model="specialist.doctor_3" :options="doctor_list" :reduce="item => item" label="title" placeholder="-- Dokter --" />
                                </div>
                                <div class="col-lg-2">
                                    <label class="c-form-label">Konsultasi yang diminta</label>
                                    <v-select :disabled="specialist.disabled_doctor_3" v-model="specialist.type_consultation_doctor_3" :options="optCunsultType" :reduce="item => item" label="title" placeholder="-- konsultasi --" />
                                </div>
                                <div class="px-16">
                                    <b-button :disabled="specialist.disabled_doctor_3" @click="changeSpecialistDoctor(3)" variant="primary">SIMPAN</b-button>
                                </div>
                                <div v-if="form_igd.doctor_in_charge_id_3">
                                    <b-button @click="enableSpecialistDoctor(3)" :variant="getToggleVariant(3)">
                                        <feather-icon :icon="getToggleIcon(3)" />
                                    </b-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section v-if="form_igd.doctor_in_charge_id_3" class="mt-20 mt-lg-32">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <span class="text-bold-18">Dokter Spesialis 3: {{ form_igd.doctor_in_charge_name_3 }}</span>
                        </div>
                        <div class="card-body">
                            <section class="card">
                                <div class="card-header bg-indigo-200">
                                    <span class="text-bold-18">Plan</span>
                                </div>
                                <div class="card-body">
                                    <div>
                                        <ModernLabel label="Tindakan (ICD9CM)" />
                                        <div>
                                            <textarea v-model.lazy="form_THIRD_icd9cm.therapy_plan" autocomplete="off" placeholder="Catatan ICD9CM" class="form-control mt-12" />                                            
                                            <b-table :fields="icd9cmFields" :items="form_THIRD_icd9cm.icd9cm" bordered responsive class="mt-12 fs-14 text-center">
                                                <template #cell(no)="row">{{ row.index + 1 }}</template>
                                                <template #cell(title)="row">
                                                    <div class="text-left">{{ row.value }}</div>
                                                </template>
                                                <template #cell(action)="row">
                                                    <b-button variant="danger-600" size="sm" @click="deleteTHIRD9cm(row.index)">
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </template>
                                            </b-table>
                                            <div class="mt-10">
                                                <v-select v-model="THIRDicd9cm_id" @input="onTHIRDAdd9cm($event)" @search="onTHIRDSearchIcd9cm" :options="OptionsTHIRDIcd9cm" :reduce="item => item" label="title" placeholder="pilih icd9cm" />
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mt-24">
                                            <b-button @click="validationTHIRDSoapP" :disabled="THIRDdisabled.soap_p" variant="primary" class="px-16">SIMPAN</b-button>
                                        </div>
                                    </div>

                                    <div class="mt-32">
                                        <ModernLabel label="Prosedur" />
                                        <b-button @click="openTHIRDProcedure()" pill variant="outline-neutral-400" size="sm" class="px-16 mt-12">
                                            <feather-icon icon="PlusIcon" /> <span>Tambah Prosedur</span>
                                        </b-button>
                                        <b-table bordered responsive :fields="procedureFields" :items="transaction_THIRD_list" class="mt-12 fs-14 text-center">
                                            <template #cell(no)="row">{{ row.index + 1 }}</template>
                                            <template #cell(costing_name)="row">
                                                <div class="text-left">{{ row.value }}</div>
                                            </template>
                                            <template #cell(notes)="row">
                                                <div class="text-left">{{ row.value }}</div>
                                            </template>
                                            <template #cell(action)="row">
                                                <b-button @click="onEditTHIRDProcedure(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                    <feather-icon icon="EditIcon" />
                                                </b-button>
                                                <b-button @click="removeTHIRDProcedure(row.item)" size="sm" variant="danger-700">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </template>
                                        </b-table>
                                    </div>

                                    <section>
                                        <ModernLabel label="Laboratorium" />
                                        <div>
                                            <b-button @click="openTHIRDLab()" pill variant="outline-neutral-400" size="sm" class="px-16 mt-12">
                                                <feather-icon icon="PlusIcon" /> <span>Tambah Order Laboratorium</span>
                                            </b-button>

                                            <b-table :fields="laboratoryFields" :items="laboratory_THIRD_list" :tbody-tr-class="rowLabStatus" bordered responsive  class="mt-12 fs-14 text-center">
                                                <template #cell(no)="row">{{ row.index+1 }}</template>                                                
                                                <template #cell(laboratory_request_item)="row">
                                                    <li v-for="(item, index) in row.value" :key="index" class="text-left">{{ item.costing_name }}</li>
                                                </template>
                                                <template #cell(action)="row">
                                                    <div v-if="row.item.status == 2 || row.item.status == 3">Order telah diproses</div>
                                                    <div v-else class="d-flex justify-content-center">
                                                        <b-button @click="onEditTHIRDLab(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                            <feather-icon icon="EditIcon" />
                                                        </b-button>
                                                        <b-button @click="removeTHIRDLab(row.item)" size="sm" variant="danger-700">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </div>
                                                </template>
                                            </b-table>
                                        </div>
                                    </section>

                                    <section>
                                        <ModernLabel label="Radiologi" />
                                        <div>
                                            <b-button @click="openTHIRDRad()" pill variant="outline-neutral-400" size="sm" class="px-16 mt-12">
                                                <feather-icon icon="PlusIcon" /> <span>Tambah Order Radiologi</span>
                                            </b-button>

                                            <b-table :fields="radiologyFields" :items="radiology_THIRD_list" :tbody-tr-class="rowRadStatus" bordered responsive class="mt-12 fs-14 text-center">
                                                <template #cell(no)="row">{{ row.index+1 }}</template>
                                                <template #cell(radiology_request_item)="row">
                                                    <li v-for="(item, index) in row.value" :key="index" class="text-left">{{ item.costing_name }}</li>                                                    
                                                </template>
                                                <template #cell(action)="row">
                                                    <div v-if="row.item.status == 2 || row.item.status == 3">Order telah diproses</div>
                                                    <div v-else class="d-flex justify-content-center">
                                                        <b-button @click="onEditTHIRDRad(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                            <feather-icon icon="EditIcon" />
                                                        </b-button>
                                                        <b-button @click="removeTHIRDRad(row.item)" size="sm" variant="danger-700">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </div>
                                                </template>
                                            </b-table>
                                        </div>
                                    </section>
                                </div>
                            </section>

                            <section class="card mt-32">
                                <div class="card-header bg-indigo-200">
                                    <span class="text-bold-18">Rencana Terapi E-Resep</span>
                                </div>
                                <div class="card-body">
                                    <b-button @click="openTHIRDPresc" pill variant="outline-neutral-400" size="sm" class="px-16">
                                        <feather-icon icon="PlusIcon" /> <span>Tambah Resep</span>
                                    </b-button>
                                    
                                    <b-table :fields="precriptionFields" :items="prescription_THIRD_list" :tbody-tr-class="rowPrescStatus" bordered responsive class="mt-12 fs-14 text-center">
                                        <template #cell(no)="row">{{ row.index+1 }}</template>
                                        <template #cell(form_prescription)="row">
                                            <div v-for="(category, index) in row.item.form_prescription_parent" :key="index" class="text-left" :class="{'mt-8' : index > 0 }" v-if="hasItemsInCategory(row.value, category.id)">
                                                <b-table-simple responsive small class="text-center fs-13 mb-0">
                                                    <b-thead>
                                                        <b-tr class="text-left">
                                                            <b-th colspan="7" :class="getParentVariant(category.id)">
                                                                <div class="text-uppercase">{{ getParentName(category.id) }}</div>
                                                                <div v-if="category.id != 1" class="fs-12 text-italic" style="font-weight: 400">  
                                                                    <span>
                                                                        Status: {{ getKardexItemStatusName(category?.status_kardex) }}, 
                                                                        Kemasan: {{ getParentPackageName(category?.package) }}, 
                                                                        {{ category?.value ?? '-' }}
                                                                        TIAP {{ getDynamicOptName(category?.signatura_times_id, optSignTime)}} 
                                                                        {{ getDynamicOptName(category?.administer_id, optAdminister) }}
                                                                        <span v-if="category.administer_id == other_administer">: {{ category.desc }}</span>
                                                                    </span>
                                                                </div>
                                                            </b-th>
                                                        </b-tr>
                                                        <b-tr>
                                                            <b-th class="text-left">NAMA OBAT</b-th>
                                                            <b-th style="width: 160px">STATUS</b-th>
                                                            <!-- <b-th style="width: 160px">KEMASAN / SATUAN</b-th> -->
                                                            <b-th style="width: 60px">JUMLAH</b-th>
                                                            <b-th style="width: 60px">DOSIS</b-th>
                                                            <b-th style="width: 110px">SATUAN DOSIS</b-th>
                                                            <b-th style="width: 90px">WAKTU</b-th>
                                                            <b-th style="width: 200px">ADMINISTER</b-th>
                                                        </b-tr>
                                                    </b-thead>
                                                    <b-tbody>
                                                        <template v-for="(item, itemIndex) in row.value">
                                                            <b-tr :key="itemIndex" v-if="item.prescription_item_type_id == category.id">
                                                                <b-td class="text-left">{{ item.pharmacy_catalog_name }}</b-td>
                                                                <b-td>{{ item.prescription_item_type_id == 1 ? getKardexItemStatusName(item?.status_kardex) : '-' }}</b-td>
                                                                <!-- <b-td>{{ item.package_name }}</b-td> -->
                                                                <b-td>{{ item.qty }}</b-td>
                                                                <b-td>{{ item.dose }}</b-td>
                                                                <b-td>{{ item.dose_unit_name }}</b-td>
                                                                <b-td>{{ item.prescription_item_type_id == 1 ? `TIAP ${item.signatura_times_name || '-'}` : '-' }}</b-td>
                                                                <b-td>
                                                                    <template v-if="item.prescription_item_type_id == 1">
                                                                        {{ item.administer_name }}
                                                                        <span v-if="item.administer_id == other_administer">: {{ item.desc }}</span>
                                                                    </template>
                                                                    <div v-else>-</div>
                                                                </b-td>
                                                            </b-tr>
                                                        </template>
                                                    </b-tbody>
                                                </b-table-simple>
                                            </div>
                                        </template>
                                        
                                        <template #cell(action)="row">
                                            <div v-if="row.item.flag == 0">
                                                <b-button @click="editTHIRDPresc(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                    <feather-icon icon="EditIcon" />
                                                </b-button>
                                                <b-button @click="removeTHIRDPresc(row.item)" size="sm" variant="danger-700">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </div>
                                            <div v-else>Resep telah diproses</div>
                                        </template>
                                    </b-table>
                                </div>
                            </section>  
                        </div>
                    </div>
                </section>

                <!-- Start: Dokter spesialis 4 -->
                <section v-if="form_igd.doctor_in_charge_id_3" class="mt-20 mt-lg-32">
                    <div class="card">
                        <div class="card-header bg-indigo-200">
                            <span class="text-bold-18">Tambah Dokter Spesialis 4</span>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-lg-4">
                                    <label class="c-form-label">Dokter Spesialis 4</label>
                                    <v-select :disabled="specialist.disabled_doctor_4" v-model="specialist.doctor_4" :options="doctor_list" :reduce="item => item" label="title" placeholder="-- Dokter --" />
                                </div>
                                <div class="col-lg-2">
                                    <label class="c-form-label">Konsultasi yang diminta</label>
                                    <v-select :disabled="specialist.disabled_doctor_4" v-model="specialist.type_consultation_doctor_4" :options="optCunsultType" :reduce="item => item" label="title" placeholder="-- konsultasi --" />
                                </div>
                                <div class="px-16">
                                    <b-button :disabled="specialist.disabled_doctor_4" @click="changeSpecialistDoctor(4)" variant="primary">SIMPAN</b-button>
                                </div>
                                <div v-if="form_igd.doctor_in_charge_id_4">
                                    <b-button @click="enableSpecialistDoctor(4)" :variant="getToggleVariant(4)">
                                        <feather-icon :icon="getToggleIcon(4)" />
                                    </b-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section v-if="form_igd.doctor_in_charge_id_4" class="mt-20 mt-lg-32">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <span class="text-bold-18">Dokter Spesialis 4: {{ form_igd.doctor_in_charge_name_4 }}</span>
                        </div>
                        <div class="card-body">
                            <section class="card">
                                <div class="card-header bg-indigo-200">
                                    <span class="text-bold-18">Plan</span>
                                </div>
                                <div class="card-body">
                                    <div>
                                        <ModernLabel label="Tindakan (ICD9CM)" />
                                        <div>
                                            <textarea v-model.lazy="form_FOURTH_icd9cm.therapy_plan" autocomplete="off" placeholder="Catatan ICD9CM" class="form-control mt-12" />                                            
                                            <b-table :fields="icd9cmFields" :items="form_FOURTH_icd9cm.icd9cm" bordered responsive class="mt-12 fs-14 text-center">
                                                <template #cell(no)="row">{{ row.index + 1 }}</template>
                                                <template #cell(title)="row">
                                                    <div class="text-left">{{ row.value }}</div>
                                                </template>
                                                <template #cell(action)="row">
                                                    <b-button variant="danger-600" size="sm" @click="deleteFOURTH9cm(row.index)">
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </template>
                                            </b-table>
                                            <div class="mt-10">
                                                <v-select v-model="FOURTHicd9cm_id" @input="onFOURTHAdd9cm($event)" @search="onFOURTHSearchIcd9cm" :options="OptionsFOURTHIcd9cm" :reduce="item => item" label="title" placeholder="pilih icd9cm" />
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mt-24">
                                            <b-button @click="validationFOURTHSoapP" :disabled="FOURTHdisabled.soap_p" variant="primary" class="px-16">SIMPAN</b-button>
                                        </div>
                                    </div>

                                    <div class="mt-32">
                                        <ModernLabel label="Prosedur" />
                                        <b-button @click="openFOURTHProcedure()" pill variant="outline-neutral-400" size="sm" class="px-16 mt-12">
                                            <feather-icon icon="PlusIcon" /> <span>Tambah Prosedur</span>
                                        </b-button>
                                        <b-table bordered responsive :fields="procedureFields" :items="transaction_FOURTH_list" class="mt-12 fs-14 text-center">
                                            <template #cell(no)="row">{{ row.index + 1 }}</template>
                                            <template #cell(costing_name)="row">
                                                <div class="text-left">{{ row.value }}</div>
                                            </template>
                                            <template #cell(notes)="row">
                                                <div class="text-left">{{ row.value }}</div>
                                            </template>
                                            <template #cell(action)="row">
                                                <b-button @click="onEditFOURTHProcedure(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                    <feather-icon icon="EditIcon" />
                                                </b-button>
                                                <b-button @click="removeFOURTHProcedure(row.item)" size="sm" variant="danger-700">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </template>
                                        </b-table>
                                    </div>

                                    <section>
                                        <ModernLabel label="Laboratorium" />
                                        <div>
                                            <b-button @click="openFOURTHLab()" pill variant="outline-neutral-400" size="sm" class="px-16 mt-12">
                                                <feather-icon icon="PlusIcon" /> <span>Tambah Order Laboratorium</span>
                                            </b-button>

                                            <b-table :fields="laboratoryFields" :items="laboratory_FOURTH_list" :tbody-tr-class="rowLabStatus" bordered responsive  class="mt-12 fs-14 text-center">
                                                <template #cell(no)="row">{{ row.index+1 }}</template>                                                
                                                <template #cell(laboratory_request_item)="row">
                                                    <li v-for="(item, index) in row.value" :key="index" class="text-left">{{ item.costing_name }}</li>
                                                </template>
                                                <template #cell(action)="row">
                                                    <div v-if="row.item.status == 2 || row.item.status == 3">Order telah diproses</div>
                                                    <div v-else class="d-flex justify-content-center">
                                                        <b-button @click="onEditFOURTHLab(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                            <feather-icon icon="EditIcon" />
                                                        </b-button>
                                                        <b-button @click="removeFOURTHLab(row.item)" size="sm" variant="danger-700">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </div>
                                                </template>
                                            </b-table>
                                        </div>
                                    </section>

                                    <section>
                                        <ModernLabel label="Radiologi" />
                                        <div>
                                            <b-button @click="openFOURTHRad()" pill variant="outline-neutral-400" size="sm" class="px-16 mt-12">
                                                <feather-icon icon="PlusIcon" /> <span>Tambah Order Radiologi</span>
                                            </b-button>

                                            <b-table :fields="radiologyFields" :items="radiology_FOURTH_list" :tbody-tr-class="rowRadStatus" bordered responsive class="mt-12 fs-14 text-center">
                                                <template #cell(no)="row">{{ row.index+1 }}</template>
                                                <template #cell(radiology_request_item)="row">
                                                    <li v-for="(item, index) in row.value" :key="index" class="text-left">{{ item.costing_name }}</li>                                                    
                                                </template>
                                                <template #cell(action)="row">
                                                    <div v-if="row.item.status == 2 || row.item.status == 3">Order telah diproses</div>
                                                    <div v-else class="d-flex justify-content-center">
                                                        <b-button @click="onEditFOURTHRad(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                            <feather-icon icon="EditIcon" />
                                                        </b-button>
                                                        <b-button @click="removeFOURTHRad(row.item)" size="sm" variant="danger-700">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </div>
                                                </template>
                                            </b-table>
                                        </div>
                                    </section>
                                </div>
                            </section>

                            <section class="card mt-32">
                                <div class="card-header bg-indigo-200">
                                    <span class="text-bold-18">Rencana Terapi E-Resep</span>
                                </div>
                                <div class="card-body">
                                    <b-button @click="openFOURTHPresc" pill variant="outline-neutral-400" size="sm" class="px-16">
                                        <feather-icon icon="PlusIcon" /> <span>Tambah Resep</span>
                                    </b-button>
                                    
                                    <b-table :fields="precriptionFields" :items="prescription_FOURTH_list" :tbody-tr-class="rowPrescStatus" bordered responsive class="mt-12 fs-14 text-center">
                                        <template #cell(no)="row">{{ row.index+1 }}</template>
                                        <template #cell(form_prescription)="row">
                                            <div v-for="(category, index) in row.item.form_prescription_parent" :key="index" class="text-left" :class="{'mt-8' : index > 0 }" v-if="hasItemsInCategory(row.value, category.id)">
                                                <b-table-simple responsive small class="text-center fs-13 mb-0">
                                                    <b-thead>
                                                        <b-tr class="text-left">
                                                            <b-th colspan="7" :class="getParentVariant(category.id)">
                                                                <div class="text-uppercase">{{ getParentName(category.id) }}</div>
                                                                <div v-if="category.id != 1" class="fs-12 text-italic" style="font-weight: 400">  
                                                                    <span>
                                                                        Status: {{ getKardexItemStatusName(category?.status_kardex) }}, 
                                                                        Kemasan: {{ getParentPackageName(category?.package) }}, 
                                                                        {{ category?.value ?? '-' }}
                                                                        TIAP {{ getDynamicOptName(category?.signatura_times_id, optSignTime)}} 
                                                                        {{ getDynamicOptName(category?.administer_id, optAdminister) }}
                                                                        <span v-if="category.administer_id == other_administer">: {{ category.desc }}</span>
                                                                    </span>
                                                                </div>
                                                            </b-th>
                                                        </b-tr>
                                                        <b-tr>
                                                            <b-th class="text-left">NAMA OBAT</b-th>
                                                            <b-th style="width: 160px">STATUS</b-th>
                                                            <!-- <b-th style="width: 160px">KEMASAN / SATUAN</b-th> -->
                                                            <b-th style="width: 60px">JUMLAH</b-th>
                                                            <b-th style="width: 60px">DOSIS</b-th>
                                                            <b-th style="width: 110px">SATUAN DOSIS</b-th>
                                                            <b-th style="width: 90px">WAKTU</b-th>
                                                            <b-th style="width: 200px">ADMINISTER</b-th>
                                                        </b-tr>
                                                    </b-thead>
                                                    <b-tbody>
                                                        <template v-for="(item, itemIndex) in row.value">
                                                            <b-tr :key="itemIndex" v-if="item.prescription_item_type_id == category.id">
                                                                <b-td class="text-left">{{ item.pharmacy_catalog_name }}</b-td>
                                                                <b-td>{{ item.prescription_item_type_id == 1 ? getKardexItemStatusName(item?.status_kardex) : '-' }}</b-td>
                                                                <!-- <b-td>{{ item.package_name }}</b-td> -->
                                                                <b-td>{{ item.qty }}</b-td>
                                                                <b-td>{{ item.dose }}</b-td>
                                                                <b-td>{{ item.dose_unit_name }}</b-td>
                                                                <b-td>{{ item.prescription_item_type_id == 1 ? `TIAP ${item.signatura_times_name || '-'}` : '-' }}</b-td>
                                                                <b-td>
                                                                    <template v-if="item.prescription_item_type_id == 1">
                                                                        {{ item.administer_name }}
                                                                        <span v-if="item.administer_id == other_administer">: {{ item.desc }}</span>
                                                                    </template>
                                                                    <div v-else>-</div>
                                                                </b-td>
                                                            </b-tr>
                                                        </template>
                                                    </b-tbody>
                                                </b-table-simple>
                                            </div>
                                        </template>
                                        
                                        <template #cell(action)="row">
                                            <div v-if="row.item.flag == 0">
                                                <b-button @click="editFOURTHPresc(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                    <feather-icon icon="EditIcon" />
                                                </b-button>
                                                <b-button @click="removeFOURTHPresc(row.item)" size="sm" variant="danger-700">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </div>
                                            <div v-else>Resep telah diproses</div>
                                        </template>
                                    </b-table> 
                                </div>
                            </section>  
                        </div>
                    </div>
                </section>

                <!-- Start: Dokter spesialis 5 -->
                <section v-if="form_igd.doctor_in_charge_id_4" class="mt-20 mt-lg-32">
                    <div class="card">
                        <div class="card-header bg-indigo-200">
                            <span class="text-bold-18">Tambah Dokter Spesialis 5</span>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-lg-4">
                                    <label class="c-form-label">Dokter Spesialis 5</label>
                                    <v-select :disabled="specialist.disabled_doctor_5" v-model="specialist.doctor_5" :options="doctor_list" :reduce="item => item" label="title" placeholder="-- Dokter --" />
                                </div>
                                <div class="col-lg-2">
                                    <label class="c-form-label">Konsultasi yang diminta</label>
                                    <v-select :disabled="specialist.disabled_doctor_5" v-model="specialist.type_consultation_doctor_5" :options="optCunsultType" :reduce="item => item" label="title" placeholder="-- konsultasi --" />
                                </div>
                                <div class="px-16">
                                    <b-button :disabled="specialist.disabled_doctor_5" @click="changeSpecialistDoctor(5)" variant="primary">SIMPAN</b-button>
                                </div>
                                <div v-if="form_igd.doctor_in_charge_id_5">
                                    <b-button @click="enableSpecialistDoctor(5)" :variant="getToggleVariant(5)">
                                        <feather-icon :icon="getToggleIcon(5)" />
                                    </b-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section v-if="form_igd.doctor_in_charge_id_5" class="mt-20 mt-lg-32">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <span class="text-bold-18">Dokter Spesialis 5: {{ form_igd.doctor_in_charge_name_5 }}</span>
                        </div>
                        <div class="card-body">
                            <section class="card">
                                <div class="card-header bg-indigo-200">
                                    <span class="text-bold-18">Plan</span>
                                </div>
                                <div class="card-body">
                                    <div>
                                        <ModernLabel label="Tindakan (ICD9CM)" />
                                        <div>
                                            <textarea v-model.lazy="form_FIFTH_icd9cm.therapy_plan" autocomplete="off" placeholder="Catatan ICD9CM" class="form-control mt-12" />                                            
                                            <b-table :fields="icd9cmFields" :items="form_FIFTH_icd9cm.icd9cm" bordered responsive class="mt-12 fs-14 text-center">
                                                <template #cell(no)="row">{{ row.index + 1 }}</template>
                                                <template #cell(title)="row">
                                                    <div class="text-left">{{ row.value }}</div>
                                                </template>
                                                <template #cell(action)="row">
                                                    <b-button variant="danger-600" size="sm" @click="deleteFIFTH9cm(row.index)">
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </template>
                                            </b-table>
                                            <div class="mt-10">
                                                <v-select v-model="FIFTHicd9cm_id" @input="onFIFTHAdd9cm($event)" @search="onFIFTHSearchIcd9cm" :options="OptionsFIFTHIcd9cm" :reduce="item => item" label="title" placeholder="pilih icd9cm" />
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mt-24">
                                            <b-button @click="validationFIFTHSoapP" :disabled="FIFTHdisabled.soap_p" variant="primary" class="px-16">SIMPAN</b-button>
                                        </div>
                                    </div>

                                    <div class="mt-32">
                                        <ModernLabel label="Prosedur" />
                                        <b-button @click="openFIFTHProcedure()" pill variant="outline-neutral-400" size="sm" class="px-16 mt-12">
                                            <feather-icon icon="PlusIcon" /> <span>Tambah Prosedur</span>
                                        </b-button>
                                        <b-table bordered responsive :fields="procedureFields" :items="transaction_FIFTH_list" class="mt-12 fs-14 text-center">
                                            <template #cell(no)="row">{{ row.index + 1 }}</template>
                                            <template #cell(costing_name)="row">
                                                <div class="text-left">{{ row.value }}</div>
                                            </template>
                                            <template #cell(notes)="row">
                                                <div class="text-left">{{ row.value }}</div>
                                            </template>
                                            <template #cell(action)="row">
                                                <b-button @click="onEditFIFTHProcedure(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                    <feather-icon icon="EditIcon" />
                                                </b-button>
                                                <b-button @click="removeFIFTHProcedure(row.item)" size="sm" variant="danger-700">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </template>
                                        </b-table>
                                    </div>

                                    <section>
                                        <ModernLabel label="Laboratorium" />
                                        <div>
                                            <b-button @click="openFIFTHLab()" pill variant="outline-neutral-400" size="sm" class="px-16 mt-12">
                                                <feather-icon icon="PlusIcon" /> <span>Tambah Order Laboratorium</span>
                                            </b-button>

                                            <b-table :fields="laboratoryFields" :items="laboratory_FIFTH_list" :tbody-tr-class="rowLabStatus" bordered responsive  class="mt-12 fs-14 text-center">
                                                <template #cell(no)="row">{{ row.index+1 }}</template>                                                
                                                <template #cell(laboratory_request_item)="row">
                                                    <li v-for="(item, index) in row.value" :key="index" class="text-left">{{ item.costing_name }}</li>
                                                </template>
                                                <template #cell(action)="row">
                                                    <div v-if="row.item.status == 2 || row.item.status == 3">Order telah diproses</div>
                                                    <div v-else class="d-flex justify-content-center">
                                                        <b-button @click="onEditFIFTHLab(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                            <feather-icon icon="EditIcon" />
                                                        </b-button>
                                                        <b-button @click="removeFIFTHLab(row.item)" size="sm" variant="danger-700">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </div>
                                                </template>
                                            </b-table>
                                        </div>
                                    </section>

                                    <section>
                                        <ModernLabel label="Radiologi" />
                                        <div>
                                            <b-button @click="openFIFTHRad()" pill variant="outline-neutral-400" size="sm" class="px-16 mt-12">
                                                <feather-icon icon="PlusIcon" /> <span>Tambah Order Radiologi</span>
                                            </b-button>

                                            <b-table :fields="radiologyFields" :items="radiology_FIFTH_list" :tbody-tr-class="rowRadStatus" bordered responsive class="mt-12 fs-14 text-center">
                                                <template #cell(no)="row">{{ row.index+1 }}</template>
                                                <template #cell(radiology_request_item)="row">
                                                    <li v-for="(item, index) in row.value" :key="index" class="text-left">{{ item.costing_name }}</li>                                                    
                                                </template>
                                                <template #cell(action)="row">
                                                    <div v-if="row.item.status == 2 || row.item.status == 3">Order telah diproses</div>
                                                    <div v-else class="d-flex justify-content-center">
                                                        <b-button @click="onEditFIFTHRad(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                            <feather-icon icon="EditIcon" />
                                                        </b-button>
                                                        <b-button @click="removeFIFTHRad(row.item)" size="sm" variant="danger-700">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </div>
                                                </template>
                                            </b-table>
                                        </div>
                                    </section>
                                </div>
                            </section>

                            <section class="card mt-32">
                                <div class="card-header bg-indigo-200">
                                    <span class="text-bold-18">Rencana Terapi E-Resep</span>
                                </div>
                                <div class="card-body">
                                    <b-button @click="openFIFTHPresc" pill variant="outline-neutral-400" size="sm" class="px-16">
                                        <feather-icon icon="PlusIcon" /> <span>Tambah Resep</span>
                                    </b-button>
                                    
                                    <b-table :fields="precriptionFields" :items="prescription_FIFTH_list" :tbody-tr-class="rowPrescStatus" bordered responsive class="mt-12 fs-14 text-center">
                                        <template #cell(no)="row">{{ row.index+1 }}</template>
                                        <template #cell(form_prescription)="row">
                                            <div v-for="(category, index) in row.item.form_prescription_parent" :key="index" class="text-left" :class="{'mt-8' : index > 0 }" v-if="hasItemsInCategory(row.value, category.id)">
                                                <b-table-simple responsive small class="text-center fs-13 mb-0">
                                                    <b-thead>
                                                        <b-tr class="text-left">
                                                            <b-th colspan="7" :class="getParentVariant(category.id)">
                                                                <div class="text-uppercase">{{ getParentName(category.id) }}</div>
                                                                <div v-if="category.id != 1" class="fs-12 text-italic" style="font-weight: 400">  
                                                                    <span>
                                                                        Status: {{ getKardexItemStatusName(category?.status_kardex) }}, 
                                                                        Kemasan: {{ getParentPackageName(category?.package) }}, 
                                                                        {{ category?.value ?? '-' }}
                                                                        TIAP {{ getDynamicOptName(category?.signatura_times_id, optSignTime)}} 
                                                                        {{ getDynamicOptName(category?.administer_id, optAdminister) }}
                                                                        <span v-if="category.administer_id == other_administer">: {{ category.desc }}</span>
                                                                    </span>
                                                                </div>
                                                            </b-th>
                                                        </b-tr>
                                                        <b-tr>
                                                            <b-th class="text-left">NAMA OBAT</b-th>
                                                            <b-th style="width: 160px">STATUS</b-th>
                                                            <!-- <b-th style="width: 160px">KEMASAN / SATUAN</b-th> -->
                                                            <b-th style="width: 60px">JUMLAH</b-th>
                                                            <b-th style="width: 60px">DOSIS</b-th>
                                                            <b-th style="width: 110px">SATUAN DOSIS</b-th>
                                                            <b-th style="width: 90px">WAKTU</b-th>
                                                            <b-th style="width: 200px">ADMINISTER</b-th>
                                                        </b-tr>
                                                    </b-thead>
                                                    <b-tbody>
                                                        <template v-for="(item, itemIndex) in row.value">
                                                            <b-tr :key="itemIndex" v-if="item.prescription_item_type_id == category.id">
                                                                <b-td class="text-left">{{ item.pharmacy_catalog_name }}</b-td>
                                                                <b-td>{{ item.prescription_item_type_id == 1 ? getKardexItemStatusName(item?.status_kardex) : '-' }}</b-td>
                                                                <!-- <b-td>{{ item.package_name }}</b-td> -->
                                                                <b-td>{{ item.qty }}</b-td>
                                                                <b-td>{{ item.dose }}</b-td>
                                                                <b-td>{{ item.dose_unit_name }}</b-td>
                                                                <b-td>{{ item.prescription_item_type_id == 1 ? `TIAP ${item.signatura_times_name || '-'}` : '-' }}</b-td>
                                                                <b-td>
                                                                    <template v-if="item.prescription_item_type_id == 1">
                                                                        {{ item.administer_name }}
                                                                        <span v-if="item.administer_id == other_administer">: {{ item.desc }}</span>
                                                                    </template>
                                                                    <div v-else>-</div>
                                                                </b-td>
                                                            </b-tr>
                                                        </template>
                                                    </b-tbody>
                                                </b-table-simple>
                                            </div>
                                        </template>
                                        
                                        <template #cell(action)="row">
                                            <div v-if="row.item.flag == 0">
                                                <b-button @click="editFIFTHPresc(row.item)" size="sm" variant="warning-500" class="mr-1">
                                                    <feather-icon icon="EditIcon" />
                                                </b-button>
                                                <b-button @click="removeFIFTHPresc(row.item)" size="sm" variant="danger-700">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </div>
                                            <div v-else>Resep telah diproses</div>
                                        </template>
                                    </b-table>                                   
                                </div>
                            </section>  
                        </div>
                    </div>
                </section>
            </template>
            <div class="modal-footer mt-32">
                <div class="d-flex justify-content-center">
                    <b-button :disabled="isDisabledIGD" @click="validationIGD('MAIN')" variant="primary" class="px-32">SIMPAN</b-button>
                </div>
            </div>
        </b-modal>

        <!-- START: ALL MODAL IN IGD-DOCTOR -->
        <b-modal v-model="active_act.modal_procedure" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ORDER PROSEDUR PADA DOKTER IGD -  
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div>
                <div>
                    <label class="fs-13 mb-0">Tindakan Prosedur</label>
                    <v-select v-model="form_transaction.costing_id" :options="costing_list" :reduce="item => item" label="title" placeholder="pilih prosedur" class="select-size-sm" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Jumlah</label>
                    <b-form-input type="number" v-model="form_transaction.var_2" size="sm" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Keterangan</label>
                    <textarea v-model.lazy="form_transaction.notes" size="sm" autocomplete="off" class="form-control"></textarea>                                            
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <b-button @click.prevent="validationIGDProcedure" :disabled="IGDdisabled.transaction" size="sm" variant="primary" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>
        
        <b-modal v-model="active_act.modal_lab_igd" size="lg" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ORDER LABORATORIUM PADA DOKTER IGD -  
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Tanggal Permintaan</label>
                    <b-form-input v-model="form_laboratory_igd.date_request" size="sm" type="datetime-local" />
                </div>
            </div>
            <div class="mt-12">
                <div class="w-100">
                    <div class="fs-14 font-weight-bold">PEMERIKSAAN TERPILIH:</div>                    
                    <b-form-checkbox-group v-model="form_laboratory_igd.laboratory_request_items">
                        <b-form-checkbox
                        v-for="(item, index) in form_laboratory_igd.laboratory_request_items"
                        :key="index"
                        :value="item"
                        >
                        {{ item.costing_name }}
                        </b-form-checkbox>
                    </b-form-checkbox-group>
                </div>
                <b-form-input v-model="searchIGDlab" size="sm" placeholder="Pencarian" class="mt-8"></b-form-input>
                <div class="mt-8 py-2 bg-neutral-100" style="height: 350px; overflow-y: scroll;">
                    <b-form-checkbox-group size="sm" class="col-lg-12" v-model="form_laboratory_igd.laboratory_request_items">
                        <div v-for="(item, index) in filteredListIGDLab" :key="index" :class="index != 0 ? 'mt-10' : 'mt-0'">
                            <div class="fs-14 font-weight-bold text-uppercase">{{ item.category_name }}</div>
                            <div class="row mx-0"> 
                                <b-form-checkbox v-for="(list, packIndex) in item.category_items" :key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'laboratory_package_id':list.id, 'costing_name':list.costing_name}" class="mr-0 col-lg-4">{{list.costing_name }}</b-form-checkbox>
                            </div>
                        </div>
                    </b-form-checkbox-group>
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end m-0">
                    <b-button @click="validationIGDLab" :disabled="IGDdisabled.laboratory" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active_act.modal_rad_igd" size="lg" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ORDER RADIOLOGI PADA DOKTER IGD -  
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Tanggal Permintaan</label>
                    <b-form-input v-model="form_radiology_igd.date_request" size="sm" type="datetime-local" />
                </div>
            </div>
            <div class="mt-12">
                <div class="w-100">
                    <div class="fs-14 font-weight-bold">PEMERIKSAAN TERPILIH:</div>                    
                    <b-form-checkbox-group v-model="form_radiology_igd.radiology_request_items">
                        <b-form-checkbox
                        v-for="(item, index) in form_radiology_igd.radiology_request_items"
                        :key="index"
                        :value="item"
                        >
                        {{ item.costing_name }}
                        </b-form-checkbox>
                    </b-form-checkbox-group>
                </div>
                <b-form-input v-model="searchIGDrad" size="sm" placeholder="Pencarian" class="mt-8"></b-form-input>
                <div class="mt-8 py-2 bg-neutral-100" style="height: 350px; overflow-y: scroll;">
                    <b-form-checkbox-group size="sm" class="col-lg-12" v-model="form_radiology_igd.radiology_request_items">
                        <div v-for="(item, index) in filteredListIGDRad" :key="index" :class="index != 0 ? 'mt-10' : 'mt-0'">
                            <div class="fs-14 font-weight-bold text-uppercase">{{ item.category_name }}</div>
                            <div class="row mx-0"> 
                                <b-form-checkbox v-for="(list, packIndex) in item.category_items" :key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'radiology_package_id':list.id, 'costing_name':list.costing_name}" class="mr-0 col-lg-4">{{list.costing_name }}</b-form-checkbox>
                            </div>
                        </div>
                    </b-form-checkbox-group>
                </div>
            </div>
            <div class="mt-2">
                <label class="fs-14 font-weight-bold">Catatan Klinis</label>
                <b-form-textarea
                    v-model="form_radiology_igd.clinical_indications"
                    rows="2"
                />
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end m-0">
                    <b-button @click="validationIGDRad" :disabled="IGDdisabled.radiology" size="sm" variant="primary" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>
        
        <b-modal v-model="active.modal.presc_IGD_parent" size="sm" no-close-on-esc no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA AKAN MENAMBAHKAN SEDIAAN
                    <span class="text-primary">{{ temp_IGD_parent.parent_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="closeIGDPreParent()" size="18" class="pointer" />
            </template>
            <section>
                <div>
                    <label class="fs-13 mb-0">Kemasan</label>
                    <v-select v-model="temp_IGD_parent.package" :options="optParentPackage" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                    
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Signatura</label>
                    <div class="row">
                        <div class="col-lg-6">
                            <b-form-input v-model="temp_IGD_parent.value" type="number" size="sm" autocomplete="off" placeholder="jumlah"/>
                        </div>
                        <b-input-group size="sm" prepend="TIAP" class="col-lg-6 mt-8 mt-lg-0">                                
                            <b-form-select v-model="temp_IGD_parent.signatura_time" :options="optSignTime" text-field="title" size="sm" />
                        </b-input-group>
                    </div>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Administer</label>
                    <v-select v-model="temp_IGD_parent.administer" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                </div>
                <div v-if="temp_IGD_parent.administer == other_administer" class="mt-10">
                    <label class="fs-13 mb-0">Administer Lainnya</label>
                    <b-form-input v-model="temp_IGD_parent.desc" size="sm" autocomplete="off"/>                    
                </div>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="addIGDParent" :disabled="IGDdisabled.preParent" size="sm" variant="primary">TAMBAHKAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal.presc_IGD_items" size="sm" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA AKAN MENAMBAHKAN OBAT
                    <span class="text-primary">{{ temp_IGD_items.parent_name }}-{{ temp_IGD_items.catalog_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <section>
                <div>
                    <label class="fs-13 mb-0">Signatura</label>
                    <div class="row">
                        <div class="col-lg-4 pr-lg-0">
                            <b-form-input v-model="temp_IGD_items.signatura_dose" type="number" size="sm" autocomplete="off" placeholder="dosis"/>
                        </div>
                        <v-select v-model="temp_IGD_items.signatura_unit" :options="optItemSignUnits" :reduce="item => item.value" label="title" placeholder="satuan dosis" class="select-size-sm col-lg-4 mt-8 mt-lg-0"/>
                        <b-input-group v-if="temp_IGD_items.parent_id == 1" size="sm" prepend="TIAP" class="col-lg-4 pl-lg-0 mt-8 mt-lg-0">                                
                            <b-form-select v-model="temp_IGD_items.signatura_time" :options="optSignTime" text-field="title" size="sm" />
                        </b-input-group>
                    </div>
                </div>
                <template v-if="temp_IGD_items.parent_id == 1">
                    <div class="mt-10">
                        <label class="fs-13 mb-0">Administer</label>
                        <v-select v-model="temp_IGD_items.administer" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                    </div>
                    <div v-if="temp_IGD_items.administer == other_administer" class="mt-10">
                        <label class="fs-13 mb-0">Administer Lainnya</label>
                        <b-form-input v-model="temp_IGD_items.desc" size="sm" autocomplete="off"/>                    
                    </div>
                </template>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <b-button @click="addIGDPrescItem" :disabled="IGDdisabled.preItem" size="sm" variant="primary">TAMBAHKAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active_act.modal_presc_igd" size="lg" no-close-on-esc no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI RESEP PADA DOKTER IGD - 
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Sediaan</label>
                    <v-select v-model="IGDParentStock" @input="openIGDPreParent($event)" :options="optParentStock" :reduce="item => item" label="title" class="select-size-sm" placeholder="sediaan"></v-select>
                </div>
                <div class="col-lg">
                    <label class="fs-13 mb-0">Tambah Obat</label>
                    <v-select v-model="IGDCatalog" @input="openIGDPreItem($event)" :filterable="false" @search="onSearchIGDCatalog" :options="optIGDCatalog" :reduce="item => item" label="title" class="select-size-sm" placeholder="pilih obat"></v-select>
                </div>
            </div>

            <b-table-simple fixed small class="mt-12">
                <b-thead class="text-center fs-12">
                    <b-tr>
                        <th class="align-middle fs-10" style="width: 50px;">AKSI</th>
                        <th class="align-middle" style="width: 220px;">NAMA OBAT</th>
                        <th class="align-middle" style="width: 135px;">STATUS</th>                        
                        <th class="align-middle" style="width: 80px;">JUMLAH</th>
                        <th class="align-middle" style="width: 80px;">DOSIS</th>
                        <th class="align-middle fs-10" style="width: 80px;">SATUAN DOSIS / KEMASAN</th>
                        <th class="align-middle" style="width: 150px;">WAKTU</th>
                        <th class="align-middle" style="width: 220px;">ADMINISTER</th>
                    </b-tr>
                </b-thead>
                <template v-for="(item_p, index_p) in form_IGD_prescription.form_prescription_parent">
                    <b-tr class="fs-11" :class="getPrescParentVariant(item_p.id)">
                        <b-td :colspan="item_p.id == 1 ? 8 : 2" class="font-weight-bold align-middle" :class="item_p.id == 1? 'py-12' : ''">{{ getPrescParentName(item_p.id || '-') }}</b-td>
                        <template v-if="item_p.id != 1">
                            <td>
                                <v-select v-model="item_p.status_kardex" :options="optPrescItemStatus" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                            </td>
                            <b-td>
                                <b-form-input v-model="item_p.value" type="number" min="1" size="sm" autocomplete="off" placeholder="jumlah"/>
                            </b-td>
                            <b-td></b-td>
                            <b-td>
                                <v-select v-model="item_p.package" :options="optParentPackage" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                    
                            </b-td>
                            <b-td>
                                <b-input-group size="sm" prepend="TIAP">                                
                                    <b-form-select v-model="item_p.signatura_times_id" :options="optSignTime" text-field="title" size="sm" />
                                </b-input-group>
                            </b-td>
                            <b-td>
                                <v-select @input="updateAdminister(index_p, $event, 'IGD', 'parent')" v-model="item_p.administer_id" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                                <b-form-input v-if="item_p.administer_id == other_administer" v-model="item_p.desc" size="sm" autocomplete="off" class="mt-1"/>
                            </b-td>
                        </template>
                    </b-tr>
                    <template v-for="(item, index) in form_IGD_prescription.form_prescription" v-if="item_p.id == item.prescription_type_id">
                        <tr class="fs-11">
                            <b-td class="text-center">
                                <b-button @click="deletePresc(index)" variant="danger-600" size="sm">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </b-td>
                            <b-td>{{ item.name }}</b-td>
                            <td>
                                <v-select v-if="item.prescription_type_id == 1" v-model="item.status_kardex" :options="optPrescItemStatus" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                            </td>
                            <b-td>
                                <b-form-input v-model="item.value_package" type="number" min="1" size="sm" autocomplete="off"/>
                            </b-td>
                            <b-td>
                                <b-form-input v-model="item.dose" type="number" min="1" size="sm" autocomplete="off" placeholder="dosis" class="kx-input"/>
                            </b-td>
                            <b-td>
                                <v-select v-model="item.dose_unit_id" :options="item.dose_unit_options" :reduce="unit => unit.value" label="title" placeholder="pilih" class="select-size-sm"/>
                            </b-td>
                            <b-td>
                                <b-input-group v-if="item.prescription_type_id == 1" size="sm" prepend="TIAP">                                
                                    <b-form-select v-model="item.signatura_times_id" :options="optSignTime" text-field="title" size="sm"/>
                                </b-input-group>
                            </b-td>
                            <td>
                                <template v-if="item.prescription_type_id == 1">
                                    <v-select @input="updateAdminister(index, $event, 'IGD', null)" v-model="item.administer_id" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm"/>
                                    <b-form-input v-if="item.administer_id == other_administer" v-model="item.desc" size="sm" autocomplete="off" class="mt-1"/>
                                </template>
                            </td>
                        </tr>
                    </template>
                </template>
            </b-table-simple>
            <!-- <pre>{{form_IGD_prescription}}</pre> -->
            <template #modal-footer>
                <div class="w-100 d-flex justify-content-end m-0">
                    <b-button @click="validationIGDPresc" :disabled="IGDdisabled.prescription" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <!-- START: ALL MODAL IN SPECIALIST-DOCTOR 1 -->
        <b-modal v-model="active_FIRST.modal_procedure" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ORDER PROSEDUR PADA DOKTER SPESIALIS 1 -  
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name_1 }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div>
                <div>
                    <label class="fs-13 mb-0">Tindakan Prosedur</label>
                    <v-select v-model="form_FIRST_transaction.costing_id" aria-readonly="true" :options="costing_list" :reduce="item => item" label="title" placeholder="pilih procedure" class="select-size-sm" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Jumlah</label>
                    <b-form-input type="number" v-model="form_FIRST_transaction.var_2" size="sm" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Keterangan</label>
                    <textarea v-model.lazy="form_FIRST_transaction.notes" size="sm" autocomplete="off" class="form-control"></textarea>                                            
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <b-button @click.prevent="validationFIRSTProcedure" :disabled="FIRSTdisabled.transaction" size="sm" variant="primary" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active_FIRST.modal_lab" size="lg" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ORDER LABORATORIUM PADA DOKTER SPESIALIS 1 -  
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name_1 }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Tanggal Permintaan</label>
                    <b-form-input v-model="form_FIRST_laboratory.date_request" size="sm" type="datetime-local" />
                </div>
            </div>
            <div class="mt-12">
                <div class="w-100">
                    <div class="fs-14 font-weight-bold">PEMERIKSAAN TERPILIH:</div>                    
                    <b-form-checkbox-group v-model="form_FIRST_laboratory.laboratory_request_items">
                        <b-form-checkbox
                        v-for="(item, index) in form_FIRST_laboratory.laboratory_request_items"
                        :key="index"
                        :value="item"
                        >
                        {{ item.costing_name }}
                        </b-form-checkbox>
                    </b-form-checkbox-group>
                </div>
                <b-form-input v-model="searchFIRSTlab" size="sm" placeholder="Pencarian" class="mt-8"></b-form-input>
                <div class="mt-8 py-2 bg-neutral-100" style="height: 350px; overflow-y: scroll;">
                    <b-form-checkbox-group size="sm" class="col-lg-12" v-model="form_FIRST_laboratory.laboratory_request_items">
                        <div v-for="(item, index) in filteredListFIRSTLab" :key="index" :class="index != 0 ? 'mt-10' : 'mt-0'">
                            <div class="fs-14 font-weight-bold text-uppercase">{{ item.category_name }}</div>
                            <div class="row mx-0"> 
                                <b-form-checkbox v-for="(list, packIndex) in item.category_items" :key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'laboratory_package_id':list.id, 'costing_name':list.costing_name}" class="mr-0 col-lg-4">{{list.costing_name }}</b-form-checkbox>
                            </div>
                        </div>
                    </b-form-checkbox-group>
                </div>
            </div>
            
            <template #modal-footer>
                <div class="w-100 d-flex justify-content-end m-0">
                    <b-button @click="validationFIRSTLab" :disabled="FIRSTdisabled.laboratory" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active_FIRST.modal_rad" size="lg" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ORDER RADIOLOGI PADA DOKTER SPESIALIS 1 -  
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name_1 }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Tanggal Permintaan</label>
                    <b-form-input v-model="form_FIRST_radiology.date_request" size="sm" type="datetime-local" />
                </div>
            </div>
            <div class="mt-12">
                <div class="w-100">
                    <div class="fs-14 font-weight-bold">PEMERIKSAAN TERPILIH:</div>                    
                    <b-form-checkbox-group v-model="form_FIRST_radiology.radiology_request_items">
                        <b-form-checkbox
                        v-for="(item, index) in form_FIRST_radiology.radiology_request_items"
                        :key="index"
                        :value="item"
                        >
                        {{ item.costing_name }}
                        </b-form-checkbox>
                    </b-form-checkbox-group>
                </div>
                <b-form-input v-model="searchFIRSTrad" size="sm" placeholder="Pencarian" class="mt-8"></b-form-input>                
                <div class="mt-8 py-2 bg-neutral-100" style="height: 350px; overflow-y: scroll;">
                    <b-form-checkbox-group size="sm" class="col-lg-12" v-model="form_FIRST_radiology.radiology_request_items">
                        <div v-for="(item, index) in filteredListFIRSTRad" :key="index" :class="index != 0 ? 'mt-10' : 'mt-0'">
                            <div class="fs-14 font-weight-bold text-uppercase">{{ item.category_name }}</div>
                            <div class="row mx-0"> 
                                <b-form-checkbox v-for="(list, packIndex) in item.category_items" :key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'radiology_package_id': list.id, 'costing_name': list.costing_name}" class="mr-0 col-lg-4">{{list.costing_name }}</b-form-checkbox>
                            </div>
                        </div>
                    </b-form-checkbox-group>
                </div>
            </div>
            <div class="mt-2">
                <label class="fs-14 font-weight-bold">Catatan Klinis</label>
                <b-form-textarea
                    v-model="form_FIRST_radiology.clinical_indications"
                    rows="2"
                />
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end m-0">
                    <b-button @click="validationFIRSTRad" :disabled="FIRSTdisabled.radiology" size="sm" variant="primary" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal.presc_FIRST_parent" size="sm" no-close-on-esc no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA AKAN MENAMBAHKAN SEDIAAN
                    <span class="text-primary">{{ temp_FIRST_parent.parent_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="closeFIRSTPreParent()" size="18" class="pointer" />
            </template>
            <section>
                <div>
                    <label class="fs-13 mb-0">Kemasan</label>
                    <v-select v-model="temp_FIRST_parent.package" :options="optParentPackage" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                    
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Signatura</label>
                    <div class="row">
                        <div class="col-lg-6">
                            <b-form-input v-model="temp_FIRST_parent.value" type="number" size="sm" autocomplete="off" placeholder="jumlah"/>
                        </div>
                        <b-input-group size="sm" prepend="TIAP" class="col-lg-6 mt-8 mt-lg-0">                                
                            <b-form-select v-model="temp_FIRST_parent.signatura_time" :options="optSignTime" text-field="title" size="sm" />
                        </b-input-group>
                    </div>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Administer</label>
                    <v-select v-model="temp_FIRST_parent.administer" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                </div>
                <div v-if="temp_FIRST_parent.administer == other_administer" class="mt-10">
                    <label class="fs-13 mb-0">Administer Lainnya</label>
                    <b-form-input v-model="temp_FIRST_parent.desc" size="sm" autocomplete="off"/>                    
                </div>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="addFIRSTParent" :disabled="FIRSTdisabled.preParent" size="sm" variant="primary">TAMBAHKAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal.presc_FIRST_items" size="sm" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA AKAN MENAMBAHKAN OBAT
                    <span class="text-primary">{{ temp_FIRST_items.parent_name }}-{{ temp_FIRST_items.catalog_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <section>
                <div>
                    <label class="fs-13 mb-0">Signatura</label>
                    <div class="row">
                        <div class="col-lg-4 pr-lg-0">
                            <b-form-input v-model="temp_FIRST_items.signatura_dose" type="number" size="sm" autocomplete="off" placeholder="dosis"/>
                        </div>
                        <v-select v-model="temp_FIRST_items.signatura_unit" :options="optItemSignUnits" :reduce="item => item.value" label="title" placeholder="satuan dosis" class="select-size-sm col-lg-4 mt-8 mt-lg-0"/>
                        <b-input-group v-if="temp_FIRST_items.parent_id == 1" size="sm" prepend="TIAP" class="col-lg-4 pl-lg-0 mt-8 mt-lg-0">                                
                            <b-form-select v-model="temp_FIRST_items.signatura_time" :options="optSignTime" text-field="title" size="sm" />
                        </b-input-group>
                    </div>
                </div>
                <template v-if="temp_FIRST_items.parent_id == 1">
                    <div class="mt-10">
                        <label class="fs-13 mb-0">Administer</label>
                        <v-select v-model="temp_FIRST_items.administer" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                    </div>
                    <div v-if="temp_FIRST_items.administer == other_administer" class="mt-10">
                        <label class="fs-13 mb-0">Administer Lainnya</label>
                        <b-form-input v-model="temp_FIRST_items.desc" size="sm" autocomplete="off"/>                    
                    </div>
                </template>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <b-button @click="addFIRSTPrescItem" :disabled="FIRSTdisabled.preItem" size="sm" variant="primary">TAMBAHKAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active_FIRST.modal_presc" size="lg" no-close-on-esc no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI RESEP PADA DOKTER SPESIALIS 1 - 
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name_1 }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Sediaan</label>
                    <v-select v-model="FIRSTParentStock" @input="openFIRSTPreParent($event)" :options="optParentStock" :reduce="item => item" label="title" class="select-size-sm" placeholder="sediaan"></v-select>
                </div>
                <div class="col-lg">
                    <label class="fs-13 mb-0">Tambah Obat</label>
                    <v-select v-model="FIRSTCatalog" @input="openFIRSTPreItem($event)" :filterable="false" @search="onSearchFIRSTCatalog" :options="optFIRSTCatalog" :reduce="item => item" label="title" class="select-size-sm" placeholder="pilih obat"></v-select>
                </div>
            </div>

            <b-table-simple fixed small class="mt-12">
                <b-thead class="text-center fs-12">
                    <b-tr>
                        <th class="align-middle fs-10" style="width: 50px;">AKSI</th>
                        <th class="align-middle" style="width: 220px;">NAMA OBAT</th>
                        <th class="align-middle" style="width: 135px;">STATUS</th>
                        <th class="align-middle" style="width: 80px;">JUMLAH</th>
                        <th class="align-middle" style="width: 80px;">DOSIS</th>
                        <th class="align-middle fs-10" style="width: 80px;">SATUAN DOSIS / KEMASAN</th>
                        <th class="align-middle" style="width: 150px;">WAKTU</th>
                        <th class="align-middle" style="width: 220px;">ADMINISTER</th>
                    </b-tr>
                </b-thead>
                <template v-for="(item_p, index_p) in form_FIRST_prescription.form_prescription_parent">
                    <b-tr class="fs-11" :class="getPrescParentVariant(item_p.id)">
                        <b-td :colspan="item_p.id == 1 ? 8 : 2" class="font-weight-bold align-middle" :class="item_p.id == 1? 'py-12' : ''">{{ getPrescParentName(item_p.id || '-') }}</b-td>
                        <template v-if="item_p.id != 1">
                            <td>
                                <v-select v-model="item_p.status_kardex" :options="optPrescItemStatus" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                            </td>
                            <b-td>
                                <b-form-input v-model="item_p.value" type="number" min="1" size="sm" autocomplete="off" placeholder="jumlah"/>
                            </b-td>
                            <b-td></b-td>
                            <b-td>
                                <v-select v-model="item_p.package" :options="optParentPackage" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                    
                            </b-td>
                            <b-td>
                                <b-input-group size="sm" prepend="TIAP">                                
                                    <b-form-select v-model="item_p.signatura_times_id" :options="optSignTime" text-field="title" size="sm" />
                                </b-input-group>
                            </b-td>
                            <b-td>
                                <v-select @input="updateAdminister(index_p, $event, 'FIRST', 'parent')" v-model="item_p.administer_id" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                                <b-form-input v-if="item_p.administer_id == other_administer" v-model="item_p.desc" size="sm" autocomplete="off" class="mt-1"/>
                            </b-td>
                        </template>
                    </b-tr>
                    <template v-for="(item, index) in form_FIRST_prescription.form_prescription" v-if="item_p.id == item.prescription_type_id">
                        <tr class="fs-11">
                            <b-td class="text-center">
                                <b-button @click="deleteFIRSTPresc(index)" variant="danger-600" size="sm">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </b-td>
                            <b-td>{{ item.name }}</b-td>
                            <td>
                                <v-select v-if="item.prescription_type_id == 1" v-model="item.status_kardex" :options="optPrescItemStatus" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                            </td>
                            <b-td>
                                <b-form-input v-model="item.value_package" type="number" min="1" size="sm" autocomplete="off"/>
                            </b-td>
                            <b-td>
                                <b-form-input v-model="item.dose" type="number" min="1" size="sm" autocomplete="off" placeholder="dosis" class="kx-input"/>
                            </b-td>
                            <b-td>
                                <v-select v-model="item.dose_unit_id" :options="item.dose_unit_options" :reduce="unit => unit.value" label="title" placeholder="pilih" class="select-size-sm"/>
                            </b-td>
                            <b-td>
                                <b-input-group v-if="item.prescription_type_id == 1" size="sm" prepend="TIAP">                                
                                    <b-form-select v-model="item.signatura_times_id" :options="optSignTime" text-field="title" size="sm"/>
                                </b-input-group>
                            </b-td>
                            <td>
                                <template v-if="item.prescription_type_id == 1">
                                    <v-select @input="updateAdminister(index, $event, 'FIRST', null)" v-model="item.administer_id" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm"/>
                                    <b-form-input v-if="item.administer_id == other_administer" v-model="item.desc" size="sm" autocomplete="off" class="mt-1"/>
                                </template>
                            </td>
                        </tr>
                    </template>
                </template>
            </b-table-simple>
            <!-- <pre>{{form_FIRST_prescription}}</pre> -->
            <template #modal-footer>
                <div class="w-100 d-flex justify-content-end m-0">
                    <b-button @click="validationFIRSTPresc" :disabled="FIRSTdisabled.prescription" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <!-- START: ALL MODAL IN SPECIALIST-DOCTOR 2 -->
        <b-modal v-model="active_SECOND.modal_procedure" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ORDER PROSEDUR PADA DOKTER SPESIALIS 2 -  
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name_2 }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div>
                <div>
                    <label class="fs-13 mb-0">Tindakan Prosedur</label>
                    <v-select v-model="form_SECOND_transaction.costing_id" aria-readonly="true" :options="costing_list" :reduce="item => item" label="title" placeholder="pilih procedure" class="select-size-sm" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Jumlah</label>
                    <b-form-input type="number" v-model="form_SECOND_transaction.var_2" size="sm" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Keterangan</label>
                    <textarea v-model.lazy="form_SECOND_transaction.notes" size="sm" autocomplete="off" class="form-control"></textarea>                                            
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <b-button @click.prevent="validationSECONDProcedure" :disabled="SECONDdisabled.transaction" size="sm" variant="primary" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active_SECOND.modal_lab" size="lg" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ORDER LABORATORIUM PADA DOKTER SPESIALIS 2 -  
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name_2 }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Tanggal Permintaan</label>
                    <b-form-input v-model="form_SECOND_laboratory.date_request" size="sm" type="datetime-local" />
                </div>
            </div>
            <div class="mt-12">
                <div class="w-100">
                    <div class="fs-14 font-weight-bold">PEMERIKSAAN TERPILIH:</div>                    
                    <b-form-checkbox-group v-model="form_SECOND_laboratory.laboratory_request_items">
                        <b-form-checkbox
                        v-for="(item, index) in form_SECOND_laboratory.laboratory_request_items"
                        :key="index"
                        :value="item"
                        >
                        {{ item.costing_name }}
                        </b-form-checkbox>
                    </b-form-checkbox-group>
                </div>
                <b-form-input v-model="searchSECONDlab" size="sm" placeholder="Pencarian" class="mt-8"></b-form-input>
                <div class="mt-8 py-2 bg-neutral-100" style="height: 350px; overflow-y: scroll;">
                    <b-form-checkbox-group size="sm" class="col-lg-12" v-model="form_SECOND_laboratory.laboratory_request_items">
                        <div v-for="(item, index) in filteredListSECONDLab" :key="index" :class="index != 0 ? 'mt-10' : 'mt-0'">
                            <div class="fs-14 font-weight-bold text-uppercase">{{ item.category_name }}</div>
                            <div class="row mx-0"> 
                                <b-form-checkbox v-for="(list, packIndex) in item.category_items" :key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'laboratory_package_id':list.id, 'costing_name':list.costing_name}" class="mr-0 col-lg-4">{{list.costing_name }}</b-form-checkbox>
                            </div>
                        </div>
                    </b-form-checkbox-group>
                </div>
            </div>
            
            <template #modal-footer>
                <div class="w-100 d-flex justify-content-end m-0">
                    <b-button @click="validationSECONDLab" :disabled="SECONDdisabled.laboratory" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active_SECOND.modal_rad" size="lg" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ORDER RADIOLOGI PADA DOKTER SPESIALIS 2 -  
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name_2 }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Tanggal Permintaan</label>
                    <b-form-input v-model="form_SECOND_radiology.date_request" size="sm" type="datetime-local" />
                </div>
            </div>
            <div class="mt-12">
                <div class="w-100">
                    <div class="fs-14 font-weight-bold">PEMERIKSAAN TERPILIH:</div>                    
                    <b-form-checkbox-group v-model="form_SECOND_radiology.radiology_request_items">
                        <b-form-checkbox
                        v-for="(item, index) in form_SECOND_radiology.radiology_request_items"
                        :key="index"
                        :value="item"
                        >
                        {{ item.costing_name }}
                        </b-form-checkbox>
                    </b-form-checkbox-group>
                </div>
                <b-form-input v-model="searchSECONDrad" size="sm" placeholder="Pencarian" class="mt-8"></b-form-input>                
                <div class="mt-8 py-2 bg-neutral-100" style="height: 350px; overflow-y: scroll;">
                    <b-form-checkbox-group size="sm" class="col-lg-12" v-model="form_SECOND_radiology.radiology_request_items">
                        <div v-for="(item, index) in filteredListSECONDRad" :key="index" :class="index != 0 ? 'mt-10' : 'mt-0'">
                            <div class="fs-14 font-weight-bold text-uppercase">{{ item.category_name }}</div>
                            <div class="row mx-0"> 
                                <b-form-checkbox v-for="(list, packIndex) in item.category_items" :key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'radiology_package_id': list.id, 'costing_name': list.costing_name}" class="mr-0 col-lg-4">{{list.costing_name }}</b-form-checkbox>
                            </div>
                        </div>
                    </b-form-checkbox-group>
                </div>
            </div>
            <div class="mt-2">
                <label class="fs-14 font-weight-bold">Catatan Klinis</label>
                <b-form-textarea
                    v-model="form_SECOND_radiology.clinical_indications"
                    rows="2"
                />
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end m-0">
                    <b-button @click="validationSECONDRad" :disabled="SECONDdisabled.radiology" size="sm" variant="primary" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal.presc_SECOND_parent" size="sm" no-close-on-esc no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA AKAN MENAMBAHKAN SEDIAAN
                    <span class="text-primary">{{ temp_SECOND_parent.parent_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="closeSECONDPreParent()" size="18" class="pointer" />
            </template>
            <section>
                <div>
                    <label class="fs-13 mb-0">Kemasan</label>
                    <v-select v-model="temp_SECOND_parent.package" :options="optParentPackage" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                    
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Signatura</label>
                    <div class="row">
                        <div class="col-lg-6">
                            <b-form-input v-model="temp_SECOND_parent.value" type="number" size="sm" autocomplete="off" placeholder="jumlah"/>
                        </div>
                        <b-input-group size="sm" prepend="TIAP" class="col-lg-6 mt-8 mt-lg-0">                                
                            <b-form-select v-model="temp_SECOND_parent.signatura_time" :options="optSignTime" text-field="title" size="sm" />
                        </b-input-group>
                    </div>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Administer</label>
                    <v-select v-model="temp_SECOND_parent.administer" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                </div>
                <div v-if="temp_SECOND_parent.administer == other_administer" class="mt-10">
                    <label class="fs-13 mb-0">Administer Lainnya</label>
                    <b-form-input v-model="temp_SECOND_parent.desc" size="sm" autocomplete="off"/>                    
                </div>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="addSECONDParent" :disabled="SECONDdisabled.preParent" size="sm" variant="primary">TAMBAHKAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal.presc_SECOND_items" size="sm" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA AKAN MENAMBAHKAN OBAT
                    <span class="text-primary">{{ temp_SECOND_items.parent_name }}-{{ temp_SECOND_items.catalog_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <section>
                <div>
                    <label class="fs-13 mb-0">Signatura</label>
                    <div class="row">
                        <div class="col-lg-4 pr-lg-0">
                            <b-form-input v-model="temp_SECOND_items.signatura_dose" type="number" size="sm" autocomplete="off" placeholder="dosis"/>
                        </div>
                        <v-select v-model="temp_SECOND_items.signatura_unit" :options="optItemSignUnits" :reduce="item => item.value" label="title" placeholder="satuan dosis" class="select-size-sm col-lg-4 mt-8 mt-lg-0"/>
                        <b-input-group v-if="temp_SECOND_items.parent_id == 1" size="sm" prepend="TIAP" class="col-lg-4 pl-lg-0 mt-8 mt-lg-0">                                
                            <b-form-select v-model="temp_SECOND_items.signatura_time" :options="optSignTime" text-field="title" size="sm" />
                        </b-input-group>
                    </div>
                </div>
                <template v-if="temp_SECOND_items.parent_id == 1">
                    <div class="mt-10">
                        <label class="fs-13 mb-0">Administer</label>
                        <v-select v-model="temp_SECOND_items.administer" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                    </div>
                    <div v-if="temp_SECOND_items.administer == other_administer" class="mt-10">
                        <label class="fs-13 mb-0">Administer Lainnya</label>
                        <b-form-input v-model="temp_SECOND_items.desc" size="sm" autocomplete="off"/>                    
                    </div>
                </template>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="addSECONDPrescItem" :disabled="SECONDdisabled.preItem" size="sm" variant="primary">TAMBAHKAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active_SECOND.modal_presc" size="lg" no-close-on-esc no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI RESEP PADA DOKTER SPESIALIS 2 - 
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name_2 }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Sediaan</label>
                    <v-select v-model="SECONDParentStock" @input="openSECONDPreParent($event)" :options="optParentStock" :reduce="item => item" label="title" class="select-size-sm" placeholder="sediaan"></v-select>
                </div>
                <div class="col-lg">
                    <label class="fs-13 mb-0">Tambah Obat</label>
                    <v-select v-model="SECONDCatalog" @input="openSECONDPreItem($event)" :filterable="false" @search="onSearchSECONDCatalog" :options="optSECONDCatalog" :reduce="item => item" label="title" class="select-size-sm" placeholder="pilih obat"></v-select>
                </div>
            </div>

            <b-table-simple fixed small class="mt-12">
                <b-thead class="text-center fs-12">
                    <b-tr>
                        <th class="align-middle fs-10" style="width: 50px;">AKSI</th>
                        <th class="align-middle" style="width: 220px;">NAMA OBAT</th>
                        <th class="align-middle" style="width: 135px;">STATUS</th>
                        <th class="align-middle" style="width: 80px;">JUMLAH</th>
                        <th class="align-middle" style="width: 80px;">DOSIS</th>
                        <th class="align-middle fs-10" style="width: 80px;">SATUAN DOSIS / KEMASAN</th>
                        <th class="align-middle" style="width: 150px;">WAKTU</th>
                        <th class="align-middle" style="width: 220px;">ADMINISTER</th>
                    </b-tr>
                </b-thead>
                <template v-for="(item_p, index_p) in form_SECOND_prescription.form_prescription_parent">
                    <b-tr class="fs-11" :class="getPrescParentVariant(item_p.id)">
                        <b-td :colspan="item_p.id == 1 ? 8 : 2" class="font-weight-bold align-middle" :class="item_p.id == 1? 'py-12' : ''">{{ getPrescParentName(item_p.id || '-') }}</b-td>
                        <template v-if="item_p.id != 1">
                            <td>
                                <v-select v-model="item_p.status_kardex" :options="optPrescItemStatus" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                            </td>
                            <b-td>
                                <b-form-input v-model="item_p.value" type="number" min="1" size="sm" autocomplete="off" placeholder="jumlah"/>
                            </b-td>
                            <b-td></b-td>
                            <b-td>
                                <v-select v-model="item_p.package" :options="optParentPackage" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                    
                            </b-td>
                            <b-td>
                                <b-input-group size="sm" prepend="TIAP">                                
                                    <b-form-select v-model="item_p.signatura_times_id" :options="optSignTime" text-field="title" size="sm" />
                                </b-input-group>
                            </b-td>
                            <b-td>
                                <v-select @input="updateAdminister(index_p, $event, 'SECOND', 'parent')" v-model="item_p.administer_id" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                                <b-form-input v-if="item_p.administer_id == other_administer" v-model="item_p.desc" size="sm" autocomplete="off" class="mt-1"/>
                            </b-td>
                        </template>
                    </b-tr>
                    <template v-for="(item, index) in form_SECOND_prescription.form_prescription" v-if="item_p.id == item.prescription_type_id">
                        <tr class="fs-11">
                            <b-td class="text-center">
                                <b-button @click="deleteSECONDPresc(index)" variant="danger-600" size="sm">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </b-td>
                            <b-td>{{ item.name }}</b-td>
                            <td>
                                <v-select v-if="item.prescription_type_id == 1" v-model="item.status_kardex" :options="optPrescItemStatus" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                            </td>
                            <b-td>
                                <b-form-input v-model="item.value_package" type="number" min="1" size="sm" autocomplete="off"/>
                            </b-td>
                            <b-td>
                                <b-form-input v-model="item.dose" type="number" min="1" size="sm" autocomplete="off" placeholder="dosis" class="kx-input"/>
                            </b-td>
                            <b-td>
                                <v-select v-model="item.dose_unit_id" :options="item.dose_unit_options" :reduce="unit => unit.value" label="title" placeholder="pilih" class="select-size-sm"/>
                            </b-td>
                            <b-td>
                                <b-input-group v-if="item.prescription_type_id == 1" size="sm" prepend="TIAP">                                
                                    <b-form-select v-model="item.signatura_times_id" :options="optSignTime" text-field="title" size="sm"/>
                                </b-input-group>
                            </b-td>
                            <td>
                                <template v-if="item.prescription_type_id == 1">
                                    <v-select @input="updateAdminister(index, $event, 'SECOND', null)" v-model="item.administer_id" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm"/>
                                    <b-form-input v-if="item.administer_id == other_administer" v-model="item.desc" size="sm" autocomplete="off" class="mt-1"/>
                                </template>
                            </td>
                        </tr>
                    </template>
                </template>
            </b-table-simple>
            <!-- <pre>{{form_SECOND_prescription}}</pre> -->
            <template #modal-footer>
                <div class="w-100 d-flex justify-content-end m-0">
                    <b-button @click="validationSECONDPresc" :disabled="SECONDdisabled.prescription" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <!-- START: ALL MODAL IN SPECIALIST-DOCTOR 3 -->
        <b-modal v-model="active_THIRD.modal_procedure" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ORDER PROSEDUR PADA DOKTER SPESIALIS 3 -  
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name_3 }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div>
                <div>
                    <label class="fs-13 mb-0">Tindakan Prosedur</label>
                    <v-select v-model="form_THIRD_transaction.costing_id" aria-readonly="true" :options="costing_list" :reduce="item => item" label="title" placeholder="pilih procedure" class="select-size-sm" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Jumlah</label>
                    <b-form-input type="number" v-model="form_THIRD_transaction.var_2" size="sm" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Keterangan</label>
                    <textarea v-model.lazy="form_THIRD_transaction.notes" size="sm" autocomplete="off" class="form-control"></textarea>                                            
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <b-button @click.prevent="validationTHIRDProcedure" :disabled="THIRDdisabled.transaction" size="sm" variant="primary" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active_THIRD.modal_lab" size="lg" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ORDER LABORATORIUM PADA DOKTER SPESIALIS 3 -  
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name_3 }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Tanggal Permintaan</label>
                    <b-form-input v-model="form_THIRD_laboratory.date_request" size="sm" type="datetime-local" />
                </div>
            </div>
            <div class="mt-12">
                <div class="w-100">
                    <div class="fs-14 font-weight-bold">PEMERIKSAAN TERPILIH:</div>                    
                    <b-form-checkbox-group v-model="form_THIRD_laboratory.laboratory_request_items">
                        <b-form-checkbox
                        v-for="(item, index) in form_THIRD_laboratory.laboratory_request_items"
                        :key="index"
                        :value="item"
                        >
                        {{ item.costing_name }}
                        </b-form-checkbox>
                    </b-form-checkbox-group>
                </div>
                <b-form-input v-model="searchTHIRDlab" size="sm" placeholder="Pencarian" class="mt-8"></b-form-input>
                <div class="mt-8 py-2 bg-neutral-100" style="height: 350px; overflow-y: scroll;">
                    <b-form-checkbox-group size="sm" class="col-lg-12" v-model="form_THIRD_laboratory.laboratory_request_items">
                        <div v-for="(item, index) in filteredListTHIRDLab" :key="index" :class="index != 0 ? 'mt-10' : 'mt-0'">
                            <div class="fs-14 font-weight-bold text-uppercase">{{ item.category_name }}</div>
                            <div class="row mx-0"> 
                                <b-form-checkbox v-for="(list, packIndex) in item.category_items" :key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'laboratory_package_id':list.id, 'costing_name':list.costing_name}" class="mr-0 col-lg-4">{{list.costing_name }}</b-form-checkbox>
                            </div>
                        </div>
                    </b-form-checkbox-group>
                </div>
            </div>
            
            <template #modal-footer>
                <div class="w-100 d-flex justify-content-end m-0">
                    <b-button @click="validationTHIRDLab" :disabled="THIRDdisabled.laboratory" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active_THIRD.modal_rad" size="lg" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ORDER RADIOLOGI PADA DOKTER SPESIALIS 3 -  
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name_3 }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Tanggal Permintaan</label>
                    <b-form-input v-model="form_THIRD_radiology.date_request" size="sm" type="datetime-local" />
                </div>
            </div>
            <div class="mt-12">
                <div class="w-100">
                    <div class="fs-14 font-weight-bold">PEMERIKSAAN TERPILIH:</div>                    
                    <b-form-checkbox-group v-model="form_THIRD_radiology.radiology_request_items">
                        <b-form-checkbox
                        v-for="(item, index) in form_THIRD_radiology.radiology_request_items"
                        :key="index"
                        :value="item"
                        >
                        {{ item.costing_name }}
                        </b-form-checkbox>
                    </b-form-checkbox-group>
                </div>
                <b-form-input v-model="searchTHIRDrad" size="sm" placeholder="Pencarian" class="mt-8"></b-form-input>                
                <div class="mt-8 py-2 bg-neutral-100" style="height: 350px; overflow-y: scroll;">
                    <b-form-checkbox-group size="sm" class="col-lg-12" v-model="form_THIRD_radiology.radiology_request_items">
                        <div v-for="(item, index) in filteredListTHIRDRad" :key="index" :class="index != 0 ? 'mt-10' : 'mt-0'">
                            <div class="fs-14 font-weight-bold text-uppercase">{{ item.category_name }}</div>
                            <div class="row mx-0"> 
                                <b-form-checkbox v-for="(list, packIndex) in item.category_items" :key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'radiology_package_id': list.id, 'costing_name': list.costing_name}" class="mr-0 col-lg-4">{{list.costing_name }}</b-form-checkbox>
                            </div>
                        </div>
                    </b-form-checkbox-group>
                </div>
            </div>
            <div class="mt-2">
                <label class="fs-14 font-weight-bold">Catatan Klinis</label>
                <b-form-textarea
                    v-model="form_THIRD_radiology.clinical_indications"
                    rows="2"
                />
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end m-0">
                    <b-button @click="validationTHIRDRad" :disabled="THIRDdisabled.radiology" size="sm" variant="primary" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal.presc_THIRD_parent" size="sm" no-close-on-esc no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA AKAN MENAMBAHKAN SEDIAAN
                    <span class="text-primary">{{ temp_THIRD_parent.parent_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="closeTHIRDPreParent()" size="18" class="pointer" />
            </template>
            <section>
                <div>
                    <label class="fs-13 mb-0">Kemasan</label>
                    <v-select v-model="temp_THIRD_parent.package" :options="optParentPackage" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                    
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Signatura</label>
                    <div class="row">
                        <div class="col-lg-6">
                            <b-form-input v-model="temp_THIRD_parent.value" type="number" size="sm" autocomplete="off" placeholder="jumlah"/>
                        </div>
                        <b-input-group size="sm" prepend="TIAP" class="col-lg-6 mt-8 mt-lg-0">                                
                            <b-form-select v-model="temp_THIRD_parent.signatura_time" :options="optSignTime" text-field="title" size="sm" />
                        </b-input-group>
                    </div>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Administer</label>
                    <v-select v-model="temp_THIRD_parent.administer" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                </div>
                <div v-if="temp_THIRD_parent.administer == other_administer" class="mt-10">
                    <label class="fs-13 mb-0">Administer Lainnya</label>
                    <b-form-input v-model="temp_THIRD_parent.desc" size="sm" autocomplete="off"/>                    
                </div>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="addTHIRDParent" :disabled="THIRDdisabled.preParent" size="sm" variant="primary">TAMBAHKAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal.presc_THIRD_items" size="sm" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA AKAN MENAMBAHKAN OBAT
                    <span class="text-primary">{{ temp_THIRD_items.parent_name }}-{{ temp_THIRD_items.catalog_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <section>
                <div>
                    <label class="fs-13 mb-0">Signatura</label>
                    <div class="row">
                        <div class="col-lg-4 pr-lg-0">
                            <b-form-input v-model="temp_THIRD_items.signatura_dose" type="number" size="sm" autocomplete="off" placeholder="dosis"/>
                        </div>
                        <v-select v-model="temp_THIRD_items.signatura_unit" :options="optItemSignUnits" :reduce="item => item.value" label="title" placeholder="satuan dosis" class="select-size-sm col-lg-4 mt-8 mt-lg-0"/>
                        <b-input-group v-if="temp_THIRD_items.parent_id == 1" size="sm" prepend="TIAP" class="col-lg-4 pl-lg-0 mt-8 mt-lg-0">                                
                            <b-form-select v-model="temp_THIRD_items.signatura_time" :options="optSignTime" text-field="title" size="sm" />
                        </b-input-group>
                    </div>
                </div>
                <template v-if="temp_THIRD_items.parent_id == 1">
                    <div class="mt-10">
                        <label class="fs-13 mb-0">Administer</label>
                        <v-select v-model="temp_THIRD_items.administer" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                    </div>
                    <div v-if="temp_THIRD_items.administer == other_administer" class="mt-10">
                        <label class="fs-13 mb-0">Administer Lainnya</label>
                        <b-form-input v-model="temp_THIRD_items.desc" size="sm" autocomplete="off"/>                    
                    </div>
                </template>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="addTHIRDPrescItem" :disabled="THIRDdisabled.preItem" size="sm" variant="primary">TAMBAHKAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active_THIRD.modal_presc" size="lg" no-close-on-esc no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI RESEP PADA DOKTER SPESIALIS 3 - 
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name_3 }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Sediaan</label>
                    <v-select v-model="THIRDParentStock" @input="openTHIRDPreParent($event)" :options="optParentStock" :reduce="item => item" label="title" class="select-size-sm" placeholder="sediaan"></v-select>
                </div>
                <div class="col-lg">
                    <label class="fs-13 mb-0">Tambah Obat</label>
                    <v-select v-model="THIRDCatalog" @input="openTHIRDPreItem($event)" :filterable="false" @search="onSearchTHIRDCatalog" :options="optTHIRDCatalog" :reduce="item => item" label="title" class="select-size-sm" placeholder="pilih obat"></v-select>
                </div>
            </div>

            <b-table-simple fixed small class="mt-12">
                <b-thead class="text-center fs-12">
                    <b-tr>
                        <th class="align-middle fs-10" style="width: 50px;">AKSI</th>
                        <th class="align-middle" style="width: 220px;">NAMA OBAT</th>
                        <th class="align-middle" style="width: 135px;">STATUS</th>
                        <th class="align-middle" style="width: 80px;">JUMLAH</th>
                        <th class="align-middle" style="width: 80px;">DOSIS</th>
                        <th class="align-middle fs-10" style="width: 80px;">SATUAN DOSIS / KEMASAN</th>
                        <th class="align-middle" style="width: 150px;">WAKTU</th>
                        <th class="align-middle" style="width: 220px;">ADMINISTER</th>
                    </b-tr>
                </b-thead>
                <template v-for="(item_p, index_p) in form_THIRD_prescription.form_prescription_parent">
                    <b-tr class="fs-11" :class="getPrescParentVariant(item_p.id)">
                        <b-td :colspan="item_p.id == 1 ? 8 : 2" class="font-weight-bold align-middle" :class="item_p.id == 1? 'py-12' : ''">{{ getPrescParentName(item_p.id || '-') }}</b-td>
                        <template v-if="item_p.id != 1">
                            <td>
                                <v-select v-model="item_p.status_kardex" :options="optPrescItemStatus" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                            </td>
                            <b-td>
                                <b-form-input v-model="item_p.value" type="number" min="1" size="sm" autocomplete="off" placeholder="jumlah"/>
                            </b-td>
                            <b-td></b-td>
                            <b-td>
                                <v-select v-model="item_p.package" :options="optParentPackage" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                    
                            </b-td>
                            <b-td>
                                <b-input-group size="sm" prepend="TIAP">                                
                                    <b-form-select v-model="item_p.signatura_times_id" :options="optSignTime" text-field="title" size="sm" />
                                </b-input-group>
                            </b-td>
                            <b-td>
                                <v-select @input="updateAdminister(index_p, $event, 'THIRD', 'parent')" v-model="item_p.administer_id" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                                <b-form-input v-if="item_p.administer_id == other_administer" v-model="item_p.desc" size="sm" autocomplete="off" class="mt-1"/>
                            </b-td>
                        </template>
                    </b-tr>
                    <template v-for="(item, index) in form_THIRD_prescription.form_prescription" v-if="item_p.id == item.prescription_type_id">
                        <tr class="fs-11">
                            <b-td class="text-center">
                                <b-button @click="deleteTHIRDPresc(index)" variant="danger-600" size="sm">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </b-td>
                            <b-td>{{ item.name }}</b-td>
                            <td>
                                <v-select v-if="item.prescription_type_id == 1" v-model="item.status_kardex" :options="optPrescItemStatus" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                            </td>
                            <b-td>
                                <b-form-input v-model="item.value_package" type="number" min="1" size="sm" autocomplete="off"/>
                            </b-td>
                            <b-td>
                                <b-form-input v-model="item.dose" type="number" min="1" size="sm" autocomplete="off" placeholder="dosis" class="kx-input"/>
                            </b-td>
                            <b-td>
                                <v-select v-model="item.dose_unit_id" :options="item.dose_unit_options" :reduce="unit => unit.value" label="title" placeholder="pilih" class="select-size-sm"/>
                            </b-td>
                            <b-td>
                                <b-input-group v-if="item.prescription_type_id == 1" size="sm" prepend="TIAP">                                
                                    <b-form-select v-model="item.signatura_times_id" :options="optSignTime" text-field="title" size="sm"/>
                                </b-input-group>
                            </b-td>
                            <td>
                                <template v-if="item.prescription_type_id == 1">
                                    <v-select @input="updateAdminister(index, $event, 'THIRD', null)" v-model="item.administer_id" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm"/>
                                    <b-form-input v-if="item.administer_id == other_administer" v-model="item.desc" size="sm" autocomplete="off" class="mt-1"/>
                                </template>
                            </td>
                        </tr>
                    </template>
                </template>
            </b-table-simple>
            <!-- <pre>{{form_THIRD_prescription}}</pre> -->
            <template #modal-footer>
                <div class="w-100 d-flex justify-content-end m-0">
                    <b-button @click="validationTHIRDPresc" :disabled="THIRDdisabled.prescription" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <!-- START: ALL MODAL IN SPECIALIST-DOCTOR 4 -->
        <b-modal v-model="active_FOURTH.modal_procedure" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ORDER PROSEDUR PADA DOKTER SPESIALIS 4 -  
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name_4 }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div>
                <div>
                    <label class="fs-13 mb-0">Tindakan Prosedur</label>
                    <v-select v-model="form_FOURTH_transaction.costing_id" aria-readonly="true" :options="costing_list" :reduce="item => item" label="title" placeholder="pilih procedure" class="select-size-sm" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Jumlah</label>
                    <b-form-input type="number" v-model="form_FOURTH_transaction.var_2" size="sm" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Keterangan</label>
                    <textarea v-model.lazy="form_FOURTH_transaction.notes" size="sm" autocomplete="off" class="form-control"></textarea>                                            
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <b-button @click.prevent="validationFOURTHProcedure" :disabled="FOURTHdisabled.transaction" size="sm" variant="primary" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active_FOURTH.modal_lab" size="lg" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ORDER LABORATORIUM PADA DOKTER SPESIALIS 4 -  
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name_4 }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Tanggal Permintaan</label>
                    <b-form-input v-model="form_FOURTH_laboratory.date_request" size="sm" type="datetime-local" />
                </div>
            </div>
            <div class="mt-12">
                <div class="w-100">
                    <div class="fs-14 font-weight-bold">PEMERIKSAAN TERPILIH:</div>                    
                    <b-form-checkbox-group v-model="form_FOURTH_laboratory.laboratory_request_items">
                        <b-form-checkbox
                        v-for="(item, index) in form_FOURTH_laboratory.laboratory_request_items"
                        :key="index"
                        :value="item"
                        >
                        {{ item.costing_name }}
                        </b-form-checkbox>
                    </b-form-checkbox-group>
                </div>
                <b-form-input v-model="searchFOURTHlab" size="sm" placeholder="Pencarian" class="mt-8"></b-form-input>
                <div class="mt-8 py-2 bg-neutral-100" style="height: 350px; overflow-y: scroll;">
                    <b-form-checkbox-group size="sm" class="col-lg-12" v-model="form_FOURTH_laboratory.laboratory_request_items">
                        <div v-for="(item, index) in filteredListFOURTHLab" :key="index" :class="index != 0 ? 'mt-10' : 'mt-0'">
                            <div class="fs-14 font-weight-bold text-uppercase">{{ item.category_name }}</div>
                            <div class="row mx-0"> 
                                <b-form-checkbox v-for="(list, packIndex) in item.category_items" :key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'laboratory_package_id':list.id, 'costing_name':list.costing_name}" class="mr-0 col-lg-4">{{list.costing_name }}</b-form-checkbox>
                            </div>
                        </div>
                    </b-form-checkbox-group>
                </div>
            </div>
            
            <template #modal-footer>
                <div class="w-100 d-flex justify-content-end m-0">
                    <b-button @click="validationFOURTHLab" :disabled="FOURTHdisabled.laboratory" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active_FOURTH.modal_rad" size="lg" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ORDER RADIOLOGI PADA DOKTER SPESIALIS 4 -  
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name_4 }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Tanggal Permintaan</label>
                    <b-form-input v-model="form_FOURTH_radiology.date_request" size="sm" type="datetime-local" />
                </div>
            </div>
            <div class="mt-12">
                <div class="w-100">
                    <div class="fs-14 font-weight-bold">PEMERIKSAAN TERPILIH:</div>                    
                    <b-form-checkbox-group v-model="form_FOURTH_radiology.radiology_request_items">
                        <b-form-checkbox
                        v-for="(item, index) in form_FOURTH_radiology.radiology_request_items"
                        :key="index"
                        :value="item"
                        >
                        {{ item.costing_name }}
                        </b-form-checkbox>
                    </b-form-checkbox-group>
                </div>
                <b-form-input v-model="searchFOURTHrad" size="sm" placeholder="Pencarian" class="mt-8"></b-form-input>                
                <div class="mt-8 py-2 bg-neutral-100" style="height: 350px; overflow-y: scroll;">
                    <b-form-checkbox-group size="sm" class="col-lg-12" v-model="form_FOURTH_radiology.radiology_request_items">
                        <div v-for="(item, index) in filteredListFOURTHRad" :key="index" :class="index != 0 ? 'mt-10' : 'mt-0'">
                            <div class="fs-14 font-weight-bold text-uppercase">{{ item.category_name }}</div>
                            <div class="row mx-0"> 
                                <b-form-checkbox v-for="(list, packIndex) in item.category_items" :key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'radiology_package_id': list.id, 'costing_name': list.costing_name}" class="mr-0 col-lg-4">{{list.costing_name }}</b-form-checkbox>
                            </div>
                        </div>
                    </b-form-checkbox-group>
                </div>
            </div>
            <div class="mt-2">
                <label class="fs-14 font-weight-bold">Catatan Klinis</label>
                <b-form-textarea
                    v-model="form_FOURTH_radiology.clinical_indications"
                    rows="2"
                />
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end m-0">
                    <b-button @click="validationFOURTHRad" :disabled="FOURTHdisabled.radiology" size="sm" variant="primary" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal.presc_FOURTH_parent" size="sm" no-close-on-esc no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA AKAN MENAMBAHKAN SEDIAAN
                    <span class="text-primary">{{ temp_FOURTH_parent.parent_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="closeFOURTHPreParent()" size="18" class="pointer" />
            </template>
            <section>
                <div>
                    <label class="fs-13 mb-0">Kemasan</label>
                    <v-select v-model="temp_FOURTH_parent.package" :options="optParentPackage" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                    
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Signatura</label>
                    <div class="row">
                        <div class="col-lg-6">
                            <b-form-input v-model="temp_FOURTH_parent.value" type="number" size="sm" autocomplete="off" placeholder="jumlah"/>
                        </div>
                        <b-input-group size="sm" prepend="TIAP" class="col-lg-6 mt-8 mt-lg-0">                                
                            <b-form-select v-model="temp_FOURTH_parent.signatura_time" :options="optSignTime" text-field="title" size="sm" />
                        </b-input-group>
                    </div>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Administer</label>
                    <v-select v-model="temp_FOURTH_parent.administer" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                </div>
                <div v-if="temp_FOURTH_parent.administer == other_administer" class="mt-10">
                    <label class="fs-13 mb-0">Administer Lainnya</label>
                    <b-form-input v-model="temp_FOURTH_parent.desc" size="sm" autocomplete="off"/>                    
                </div>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="addFOURTHParent" :disabled="FOURTHdisabled.preParent" size="sm" variant="primary">TAMBAHKAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal.presc_FOURTH_items" size="sm" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA AKAN MENAMBAHKAN OBAT
                    <span class="text-primary">{{ temp_FOURTH_items.parent_name }}-{{ temp_FOURTH_items.catalog_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <section>
                <div>
                    <label class="fs-13 mb-0">Signatura</label>
                    <div class="row">
                        <div class="col-lg-4 pr-lg-0">
                            <b-form-input v-model="temp_FOURTH_items.signatura_dose" type="number" size="sm" autocomplete="off" placeholder="dosis"/>
                        </div>
                        <v-select v-model="temp_FOURTH_items.signatura_unit" :options="optItemSignUnits" :reduce="item => item.value" label="title" placeholder="satuan dosis" class="select-size-sm col-lg-4 mt-8 mt-lg-0"/>
                        <b-input-group v-if="temp_FOURTH_items.parent_id == 1" size="sm" prepend="TIAP" class="col-lg-4 pl-lg-0 mt-8 mt-lg-0">                                
                            <b-form-select v-model="temp_FOURTH_items.signatura_time" :options="optSignTime" text-field="title" size="sm" />
                        </b-input-group>
                    </div>
                </div>
                <template v-if="temp_FOURTH_items.parent_id == 1">
                    <div class="mt-10">
                        <label class="fs-13 mb-0">Administer</label>
                        <v-select v-model="temp_FOURTH_items.administer" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                    </div>
                    <div v-if="temp_FOURTH_items.administer == other_administer" class="mt-10">
                        <label class="fs-13 mb-0">Administer Lainnya</label>
                        <b-form-input v-model="temp_FOURTH_items.desc" size="sm" autocomplete="off"/>                    
                    </div>
                </template>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="addFOURTHPrescItem" :disabled="FOURTHdisabled.preItem" size="sm" variant="primary">TAMBAHKAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active_FOURTH.modal_presc" size="lg" no-close-on-esc no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI RESEP PADA DOKTER SPESIALIS 4 - 
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name_4 }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Sediaan</label>
                    <v-select v-model="FOURTHParentStock" @input="openFOURTHPreParent($event)" :options="optParentStock" :reduce="item => item" label="title" class="select-size-sm" placeholder="sediaan"></v-select>
                </div>
                <div class="col-lg">
                    <label class="fs-13 mb-0">Tambah Obat</label>
                    <v-select v-model="FOURTHCatalog" @input="openFOURTHPreItem($event)" :filterable="false" @search="onSearchFOURTHCatalog" :options="optFOURTHCatalog" :reduce="item => item" label="title" class="select-size-sm" placeholder="pilih obat"></v-select>
                </div>
            </div>

            <b-table-simple fixed small class="mt-12">
                <b-thead class="text-center fs-12">
                    <b-tr>
                        <th class="align-middle fs-10" style="width: 50px;">AKSI</th>
                        <th class="align-middle" style="width: 220px;">NAMA OBAT</th>
                        <th class="align-middle" style="width: 135px;">STATUS</th>
                        <th class="align-middle" style="width: 80px;">JUMLAH</th>
                        <th class="align-middle" style="width: 80px;">DOSIS</th>
                        <th class="align-middle fs-10" style="width: 80px;">SATUAN DOSIS / KEMASAN</th>
                        <th class="align-middle" style="width: 150px;">WAKTU</th>
                        <th class="align-middle" style="width: 220px;">ADMINISTER</th>
                    </b-tr>
                </b-thead>
                <template v-for="(item_p, index_p) in form_FOURTH_prescription.form_prescription_parent">
                    <b-tr class="fs-11" :class="getPrescParentVariant(item_p.id)">
                        <b-td :colspan="item_p.id == 1 ? 8 : 2" class="font-weight-bold align-middle" :class="item_p.id == 1? 'py-12' : ''">{{ getPrescParentName(item_p.id || '-') }}</b-td>
                        <template v-if="item_p.id != 1">
                            <td>
                                <v-select v-model="item_p.status_kardex" :options="optPrescItemStatus" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                            </td>
                            <b-td>
                                <b-form-input v-model="item_p.value" type="number" min="1" size="sm" autocomplete="off" placeholder="jumlah"/>
                            </b-td>
                            <b-td></b-td>
                            <b-td>
                                <v-select v-model="item_p.package" :options="optParentPackage" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                    
                            </b-td>
                            <b-td>
                                <b-input-group size="sm" prepend="TIAP">                                
                                    <b-form-select v-model="item_p.signatura_times_id" :options="optSignTime" text-field="title" size="sm" />
                                </b-input-group>
                            </b-td>
                            <b-td>
                                <v-select @input="updateAdminister(index_p, $event, 'FOURTH', 'parent')" v-model="item_p.administer_id" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                                <b-form-input v-if="item_p.administer_id == other_administer" v-model="item_p.desc" size="sm" autocomplete="off" class="mt-1"/>
                            </b-td>
                        </template>
                    </b-tr>
                    <template v-for="(item, index) in form_FOURTH_prescription.form_prescription" v-if="item_p.id == item.prescription_type_id">
                        <tr class="fs-11">
                            <b-td class="text-center">
                                <b-button @click="deleteFOURTHPresc(index)" variant="danger-600" size="sm">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </b-td>
                            <b-td>{{ item.name }}</b-td>
                            <td>
                                <v-select v-if="item.prescription_type_id == 1" v-model="item.status_kardex" :options="optPrescItemStatus" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                            </td>
                            <b-td>
                                <b-form-input v-model="item.value_package" type="number" min="1" size="sm" autocomplete="off"/>
                            </b-td>
                            <b-td>
                                <b-form-input v-model="item.dose" type="number" min="1" size="sm" autocomplete="off" placeholder="dosis" class="kx-input"/>
                            </b-td>
                            <b-td>
                                <v-select v-model="item.dose_unit_id" :options="item.dose_unit_options" :reduce="unit => unit.value" label="title" placeholder="pilih" class="select-size-sm"/>
                            </b-td>
                            <b-td>
                                <b-input-group v-if="item.prescription_type_id == 1" size="sm" prepend="TIAP">                                
                                    <b-form-select v-model="item.signatura_times_id" :options="optSignTime" text-field="title" size="sm"/>
                                </b-input-group>
                            </b-td>
                            <td>
                                <template v-if="item.prescription_type_id == 1">
                                    <v-select @input="updateAdminister(index, $event, 'FOURTH', null)" v-model="item.administer_id" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm"/>
                                    <b-form-input v-if="item.administer_id == other_administer" v-model="item.desc" size="sm" autocomplete="off" class="mt-1"/>
                                </template>
                            </td>
                        </tr>
                    </template>
                </template>
            </b-table-simple>
            <!-- <pre>{{form_FOURTH_prescription}}</pre> -->
            <template #modal-footer>
                <div class="w-100 d-flex justify-content-end m-0">
                    <b-button @click="validationFOURTHPresc" :disabled="FOURTHdisabled.prescription" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <!-- START: ALL MODAL IN SPECIALIST-DOCTOR 5 -->
        <b-modal v-model="active_FIFTH.modal_procedure" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ORDER PROSEDUR PADA DOKTER SPESIALIS 5 -  
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name_5 }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div>
                <div>
                    <label class="fs-13 mb-0">Tindakan Prosedur</label>
                    <v-select v-model="form_FIFTH_transaction.costing_id" aria-readonly="true" :options="costing_list" :reduce="item => item" label="title" placeholder="pilih procedure" class="select-size-sm" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Jumlah</label>
                    <b-form-input type="number" v-model="form_FIFTH_transaction.var_2" size="sm" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Keterangan</label>
                    <textarea v-model.lazy="form_FIFTH_transaction.notes" size="sm" autocomplete="off" class="form-control"></textarea>                                            
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <b-button @click.prevent="validationFIFTHProcedure" :disabled="FIFTHdisabled.transaction" size="sm" variant="primary" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active_FIFTH.modal_lab" size="lg" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ORDER LABORATORIUM PADA DOKTER SPESIALIS 5 -  
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name_5 }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Tanggal Permintaan</label>
                    <b-form-input v-model="form_FIFTH_laboratory.date_request" size="sm" type="datetime-local" />
                </div>
            </div>
            <div class="mt-12">
                <div class="w-100">
                    <div class="fs-14 font-weight-bold">PEMERIKSAAN TERPILIH:</div>                    
                    <b-form-checkbox-group v-model="form_FIFTH_laboratory.laboratory_request_items">
                        <b-form-checkbox
                        v-for="(item, index) in form_FIFTH_laboratory.laboratory_request_items"
                        :key="index"
                        :value="item"
                        >
                        {{ item.costing_name }}
                        </b-form-checkbox>
                    </b-form-checkbox-group>
                </div>
                <b-form-input v-model="searchFIFTHlab" size="sm" placeholder="Pencarian" class="mt-8"></b-form-input>
                <div class="mt-8 py-2 bg-neutral-100" style="height: 350px; overflow-y: scroll;">
                    <b-form-checkbox-group size="sm" class="col-lg-12" v-model="form_FIFTH_laboratory.laboratory_request_items">
                        <div v-for="(item, index) in filteredListFIFTHLab" :key="index" :class="index != 0 ? 'mt-10' : 'mt-0'">
                            <div class="fs-14 font-weight-bold text-uppercase">{{ item.category_name }}</div>
                            <div class="row mx-0"> 
                                <b-form-checkbox v-for="(list, packIndex) in item.category_items" :key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'laboratory_package_id':list.id, 'costing_name':list.costing_name}" class="mr-0 col-lg-4">{{list.costing_name }}</b-form-checkbox>
                            </div>
                        </div>
                    </b-form-checkbox-group>
                </div>
            </div>
            
            <template #modal-footer>
                <div class="w-100 d-flex justify-content-end m-0">
                    <b-button @click="validationFIFTHLab" :disabled="FIFTHdisabled.laboratory" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active_FIFTH.modal_rad" size="lg" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI ORDER RADIOLOGI PADA DOKTER SPESIALIS 5 -  
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name_5 }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Tanggal Permintaan</label>
                    <b-form-input v-model="form_FIFTH_radiology.date_request" size="sm" type="datetime-local" />
                </div>
            </div>
            <div class="mt-12">
                <div class="w-100">
                    <div class="fs-14 font-weight-bold">PEMERIKSAAN TERPILIH:</div>                    
                    <b-form-checkbox-group v-model="form_FIFTH_radiology.radiology_request_items">
                        <b-form-checkbox
                        v-for="(item, index) in form_FIFTH_radiology.radiology_request_items"
                        :key="index"
                        :value="item"
                        >
                        {{ item.costing_name }}
                        </b-form-checkbox>
                    </b-form-checkbox-group>
                </div>
                <b-form-input v-model="searchFIFTHrad" size="sm" placeholder="Pencarian" class="mt-8"></b-form-input>                
                <div class="mt-8 py-2 bg-neutral-100" style="height: 350px; overflow-y: scroll;">
                    <b-form-checkbox-group size="sm" class="col-lg-12" v-model="form_FIFTH_radiology.radiology_request_items">
                        <div v-for="(item, index) in filteredListFIFTHRad" :key="index" :class="index != 0 ? 'mt-10' : 'mt-0'">
                            <div class="fs-14 font-weight-bold text-uppercase">{{ item.category_name }}</div>
                            <div class="row mx-0"> 
                                <b-form-checkbox v-for="(list, packIndex) in item.category_items" :key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'radiology_package_id': list.id, 'costing_name': list.costing_name}" class="mr-0 col-lg-4">{{list.costing_name }}</b-form-checkbox>
                            </div>
                        </div>
                    </b-form-checkbox-group>
                </div>
            </div>
            <div class="mt-2">
                <label class="fs-14 font-weight-bold">Catatan Klinis</label>
                <b-form-textarea
                    v-model="form_FIFTH_radiology.clinical_indications"
                    rows="2"
                />
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end m-0">
                    <b-button @click="validationFIFTHRad" :disabled="FIFTHdisabled.radiology" size="sm" variant="primary" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal.presc_FIFTH_parent" size="sm" no-close-on-esc no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA AKAN MENAMBAHKAN SEDIAAN
                    <span class="text-primary">{{ temp_FIFTH_parent.parent_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="closeFIFTHPreParent()" size="18" class="pointer" />
            </template>
            <section>
                <div>
                    <label class="fs-13 mb-0">Kemasan</label>
                    <v-select v-model="temp_FIFTH_parent.package" :options="optParentPackage" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                    
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Signatura</label>
                    <div class="row">
                        <div class="col-lg-6">
                            <b-form-input v-model="temp_FIFTH_parent.value" type="number" size="sm" autocomplete="off" placeholder="jumlah"/>
                        </div>
                        <b-input-group size="sm" prepend="TIAP" class="col-lg-6 mt-8 mt-lg-0">                                
                            <b-form-select v-model="temp_FIFTH_parent.signatura_time" :options="optSignTime" text-field="title" size="sm" />
                        </b-input-group>
                    </div>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Administer</label>
                    <v-select v-model="temp_FIFTH_parent.administer" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                </div>
                <div v-if="temp_FIFTH_parent.administer == other_administer" class="mt-10">
                    <label class="fs-13 mb-0">Administer Lainnya</label>
                    <b-form-input v-model="temp_FIFTH_parent.desc" size="sm" autocomplete="off"/>                    
                </div>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="addFIFTHParent" :disabled="FIFTHdisabled.preParent" size="sm" variant="primary">TAMBAHKAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal.presc_FIFTH_items" size="sm" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA AKAN MENAMBAHKAN OBAT
                    <span class="text-primary">{{ temp_FIFTH_items.parent_name }}-{{ temp_FIFTH_items.catalog_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <section>
                <div>
                    <label class="fs-13 mb-0">Signatura</label>
                    <div class="row">
                        <div class="col-lg-4 pr-lg-0">
                            <b-form-input v-model="temp_FIFTH_items.signatura_dose" type="number" size="sm" autocomplete="off" placeholder="dosis"/>
                        </div>
                        <v-select v-model="temp_FIFTH_items.signatura_unit" :options="optItemSignUnits" :reduce="item => item.value" label="title" placeholder="satuan dosis" class="select-size-sm col-lg-4 mt-8 mt-lg-0"/>
                        <b-input-group v-if="temp_FIFTH_items.parent_id == 1" size="sm" prepend="TIAP" class="col-lg-4 pl-lg-0 mt-8 mt-lg-0">                                
                            <b-form-select v-model="temp_FIFTH_items.signatura_time" :options="optSignTime" text-field="title" size="sm" />
                        </b-input-group>
                    </div>
                </div>
                <template v-if="temp_FIFTH_items.parent_id == 1">
                    <div class="mt-10">
                        <label class="fs-13 mb-0">Administer</label>
                        <v-select v-model="temp_FIFTH_items.administer" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                    </div>
                    <div v-if="temp_FIFTH_items.administer == other_administer" class="mt-10">
                        <label class="fs-13 mb-0">Administer Lainnya</label>
                        <b-form-input v-model="temp_FIFTH_items.desc" size="sm" autocomplete="off"/>                    
                    </div>
                </template>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="addFIFTHPrescItem" :disabled="FIFTHdisabled.preItem" size="sm" variant="primary">TAMBAHKAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active_FIFTH.modal_presc" size="lg" no-close-on-esc no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI RESEP PADA DOKTER SPESIALIS 5 - 
                    <span class="text-primary">{{ form_igd.doctor_in_charge_name_5 }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <div class="row">
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Sediaan</label>
                    <v-select v-model="FIFTHParentStock" @input="openFIFTHPreParent($event)" :options="optParentStock" :reduce="item => item" label="title" class="select-size-sm" placeholder="sediaan"></v-select>
                </div>
                <div class="col-lg">
                    <label class="fs-13 mb-0">Tambah Obat</label>
                    <v-select v-model="FIFTHCatalog" @input="openFIFTHPreItem($event)" :filterable="false" @search="onSearchFIFTHCatalog" :options="optFIFTHCatalog" :reduce="item => item" label="title" class="select-size-sm" placeholder="pilih obat"></v-select>
                </div>
            </div>

            <b-table-simple fixed small class="mt-12">
                <b-thead class="text-center fs-12">
                    <b-tr>
                        <th class="align-middle fs-10" style="width: 50px;">AKSI</th>
                        <th class="align-middle" style="width: 220px;">NAMA OBAT</th>
                        <th class="align-middle" style="width: 135px;">STATUS</th>
                        <th class="align-middle" style="width: 80px;">JUMLAH</th>
                        <th class="align-middle" style="width: 80px;">DOSIS</th>
                        <th class="align-middle fs-10" style="width: 80px;">SATUAN DOSIS / KEMASAN</th>
                        <th class="align-middle" style="width: 150px;">WAKTU</th>
                        <th class="align-middle" style="width: 220px;">ADMINISTER</th>
                    </b-tr>
                </b-thead>
                <template v-for="(item_p, index_p) in form_FIFTH_prescription.form_prescription_parent">
                    <b-tr class="fs-11" :class="getPrescParentVariant(item_p.id)">
                        <b-td :colspan="item_p.id == 1 ? 8 : 2" class="font-weight-bold align-middle" :class="item_p.id == 1? 'py-12' : ''">{{ getPrescParentName(item_p.id || '-') }}</b-td>
                        <template v-if="item_p.id != 1">
                            <td>
                                <v-select v-model="item_p.status_kardex" :options="optPrescItemStatus" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                            </td>
                            <b-td>
                                <b-form-input v-model="item_p.value" type="number" min="1" size="sm" autocomplete="off" placeholder="jumlah"/>
                            </b-td>
                            <b-td></b-td>
                            <b-td>
                                <v-select v-model="item_p.package" :options="optParentPackage" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                    
                            </b-td>
                            <b-td>
                                <b-input-group size="sm" prepend="TIAP">                                
                                    <b-form-select v-model="item_p.signatura_times_id" :options="optSignTime" text-field="title" size="sm" />
                                </b-input-group>
                            </b-td>
                            <b-td>
                                <v-select @input="updateAdminister(index_p, $event, 'FIFTH', 'parent')" v-model="item_p.administer_id" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                                <b-form-input v-if="item_p.administer_id == other_administer" v-model="item_p.desc" size="sm" autocomplete="off" class="mt-1"/>
                            </b-td>
                        </template>
                    </b-tr>
                    <template v-for="(item, index) in form_FIFTH_prescription.form_prescription" v-if="item_p.id == item.prescription_type_id">
                        <tr class="fs-11">
                            <b-td class="text-center">
                                <b-button @click="deleteFIFTHPresc(index)" variant="danger-600" size="sm">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </b-td>
                            <b-td>{{ item.name }}</b-td>
                            <td>
                                <v-select v-if="item.prescription_type_id == 1" v-model="item.status_kardex" :options="optPrescItemStatus" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                            </td>
                            <b-td>
                                <b-form-input v-model="item.value_package" type="number" min="1" size="sm" autocomplete="off"/>
                            </b-td>
                            <b-td>
                                <b-form-input v-model="item.dose" type="number" min="1" size="sm" autocomplete="off" placeholder="dosis" class="kx-input"/>
                            </b-td>
                            <b-td>
                                <v-select v-model="item.dose_unit_id" :options="item.dose_unit_options" :reduce="unit => unit.value" label="title" placeholder="pilih" class="select-size-sm"/>
                            </b-td>
                            <b-td>
                                <b-input-group v-if="item.prescription_type_id == 1" size="sm" prepend="TIAP">                                
                                    <b-form-select v-model="item.signatura_times_id" :options="optSignTime" text-field="title" size="sm"/>
                                </b-input-group>
                            </b-td>
                            <td>
                                <template v-if="item.prescription_type_id == 1">
                                    <v-select @input="updateAdminister(index, $event, 'FIFTH', null)" v-model="item.administer_id" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm"/>
                                    <b-form-input v-if="item.administer_id == other_administer" v-model="item.desc" size="sm" autocomplete="off" class="mt-1"/>
                                </template>
                            </td>
                        </tr>
                    </template>
                </template>
            </b-table-simple>
            <!-- <pre>{{form_FIFTH_prescription}}</pre> -->
            <template #modal-footer>
                <div class="w-100 d-flex justify-content-end m-0">
                    <b-button @click="validationFIFTHPresc" :disabled="FIFTHdisabled.prescription" variant="primary" size="sm" class="px-32">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>

<script>
import { mapState } from 'vuex'
import moment from 'moment'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import dateMixin from "@/mixins/dateMixin";
import optionMixin from '@/mixins/optionMixin';
// import io from 'socket.io-client';

export default {
    mixins: [dateMixin, optionMixin],

    data() {
        return {
            // socket : io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"],withCredentials: true }),
            isLoading: false,
            depo_emergency: '4105d8bc-5769-463e-bc67-aae2fc5adf10',
            other_administer: 'ea3c2573-b9f5-428d-ada4-0ce6ea67c72f',
            isPews: false,
            temp_header: {
                alias_name: '',
                name: '',
                norm: '',
                birth_date: '',
                weight: '',
                height: '',
            },
            doctor_list: [],
            costing_list: [],
            catalog_list: [],
            PackageOptions: [],
            optSignTime: [],
            optSignDose: [],
            optSignUnits: [],
            optAdminister: [],
            optItemSignUnits: [],

            diagnoseField: [
                { key: 'no', label: 'No', thStyle: { width: '60px' }},
                { key: 'title', label: 'ICD10', thStyle: { textAlign: 'left' }},
                { key: 'action', label: 'Aksi', thStyle: { width: '150px' }},
            ],
            icd9cmFields: [
                { key: 'no', label: 'No', thStyle: { width: '60px' }},
                { key: 'title', label: 'ICD9CM', thStyle: { textAlign: 'left' }},
                { key: 'action', label: 'Aksi', thStyle: { width: '150px' }},
            ],
            procedureFields: [
                { key: 'no', label: 'No', thStyle: { width: '60px' }},
                { key: 'date_added', label: 'Tanggal & Jam', thStyle: { width: '200px' } },
                { key: 'costing_name', label: 'Tindakan', thStyle: { textAlign: 'left' }},
                { key: 'var_2', label: 'Jumlah'},
                { key: 'notes', label: 'Keterangan', thStyle: { textAlign: 'left' }},
                { key: 'action', label: 'Aksi', thStyle: { width: '150px' } },
            ],
            laboratoryFields: [
                { key: 'no', label: 'No', thStyle: { width: '60px' } },
                { key: 'no_request', label: 'Nomor Pemeriksaan', thStyle: { width: '200px' } },
                { key: 'date_request', label: 'Tanggal Pemeriksaan', thStyle: { width: '200px' } },
                { key: 'laboratory_request_item', label: 'Pemeriksaan', thStyle: { textAlign: 'left' } },
                { key: 'action', label: 'Aksi', thStyle: { width: '150px' } },
            ],
            radiologyFields: [
                { key: 'no', label: 'No', thStyle: { width: '60px' } },
                { key: 'no_request', label: 'Nomor Pemeriksaan', thStyle: { width: '200px' } },
                { key: 'date_request', label: 'Tanggal Pemeriksaan', thStyle: { width: '200px' } },
                { key: 'radiology_request_item', label: 'Pemeriksaan', thStyle: { textAlign: 'left' } },
                { key: 'clinical_indications', label: 'Catatan Klinis', thStyle: { textAlign: 'left' } },
                { key: 'action', label: 'Aksi', thStyle: { width: '150px' } },
            ],
            precriptionFields: [
                { key: 'no', label: 'NO', thStyle: { width: '60px' } },
                { key: 'no_order', label: 'NOMOR ORDER', thStyle: { width: '160px' } },
                { key: 'form_prescription', label: 'RESEP', thStyle: { textAlign: 'left' } },
                { key: 'action', label: 'AKSI', thStyle: { width: '130px' } },
            ],

            regData: {},
            soap_id: '',
            disabledOphtal : false,
            active: {
                advance: {
                    antopometri: false,
                    generalis: false,
                    oftalmologis: false,
                },
                modal: {
                    presc_IGD_parent: false,
                    presc_IGD_items: false,
                    presc_FIRST_parent: false,
                    presc_FIRST_items: false,
                    presc_SECOND_parent: false,
                    presc_SECOND_items: false,
                    presc_THIRD_parent: false,
                    presc_THIRD_items: false,
                    presc_FOURTH_parent: false,
                    presc_FOURTH_items: false,
                    presc_FIFTH_parent: false,
                    presc_FIFTH_items: false,
                },
            },
            active_act: {
                modal_IGD: false,
                modal_procedure: false,
                modal_lab_igd: false,
                modal_rad_igd: false,
                modal_presc_igd: false,
            },

            supportPackage: {
                lab_IGD_package_list: [],
                rad_IGD_package_list: [],
                lab_FIRST_package_list: [],
                rad_FIRST_package_list: [],
                lab_SECOND_package_list: [],
                rad_SECOND_package_list: [],
                lab_THIRD_package_list: [],
                rad_THIRD_package_list: [],
                lab_FOURTH_package_list: [],
                rad_FOURTH_package_list: [],
                lab_FIFTH_package_list: [],
                rad_FIFTH_package_list: [],
            },

            form_igd: {
                id: '', 
                doctor_in_charge_id: '',
                doctor_in_charge_id_1: '',
                doctor_in_charge_id_2: '',
                doctor_in_charge_id_3: '',
                doctor_in_charge_id_4: '',
                doctor_in_charge_id_5: '',
                doctor_in_charge_name: '',
                doctor_in_charge_name_1: '',
                doctor_in_charge_name_2: '',
                doctor_in_charge_name_3: '',
                doctor_in_charge_name_4: '',
                doctor_in_charge_name_5: '',
                type_consultation_doctor_2: '',
                type_consultation_doctor_3: '',
                type_consultation_doctor_4: '',
                type_consultation_doctor_5: '',
                registration_destination_id: '',
                anamesa_type_id: 1, 
                family_relation_id: '',
                family_name: '',
                main_complaint: '',
                history_disease_now: '',
                history_disease_past: '',
                history_drugs: '',
                systolic_blood_pressure: '',
                diastolic_blood_pressure: '',
                pulse_frequency: '',
                breath_frequency: '',
                oximetry_frequency: '',
                temperature: '',
                oxygen_type_id: '',
                aware: '',
                eye_score: '',
                verbal_score: '',
                motor_score: '',
                skin_color: '',
                behavior: '',
                weight: '',
                height: '',
                imt: '',
                head_circumference: '',
                chest_circumference: '',
                arm_circumference: '',
                stomach_circumference: '',
                knee_circumference: '',
                icd10: [],
                icd10_notes: '',
                non_icd10: [],
                therapy_plan: '',
                
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

            
            // IGD
            IGDicd10_id: '',
            OptionsIGDIcd10: [],
            IGDicd9cm_id: '',
            OptionsIGDIcd9cm: [],
            isDisabledIGD: false,
            
            ophInput: {
                ophthal_od: {
                    visus: 12,
                    tio_digital: 1,
                    tio_tonometri_sciots: '',
                    eye_position: 1,
                    eye_movement: 1,
                    super_silia: 1,
                    palpebra_edema: 1,
                    palpebra_ptosis: 1,
                    palpebra_lagophtalmus: 1,
                    palpebra_tumor: 1,
                    apparatus_lakrimalis: 1,
                    silia: 1,
                    conjunctive_hiperemis: 1,
                    conjunctive_bleed: 1,
                    cornea: 1,
                    camera_oculi: 1,
                    iris_color: 2,
                    iris_cripte: 1,
                    pupil_form: 1,
                    pupil_diameter: 4,
                    pupil_direct_light: 1,
                    pupil_indirect_light: 1,
                    lens: 1,
                    shadow_test: 1,
                    glandula: 1,
                    field_view: 1,
                    fundus: '',
                },
                ophthal_os: {
                    visus: 12,
                    tio_digital: 1,
                    tio_tonometri_sciots: '',
                    eye_position: 1,
                    eye_movement: 1,
                    super_silia: 1,
                    palpebra_edema: 1,
                    palpebra_ptosis: 1,
                    palpebra_lagophtalmus: 1,
                    palpebra_tumor: 1,
                    apparatus_lakrimalis: 1,
                    silia: 1,
                    conjunctive_hiperemis: 1,
                    conjunctive_bleed: 1,
                    cornea: 1,
                    camera_oculi: 1,
                    iris_color: 2,
                    iris_cripte: 1,
                    pupil_form: 1,
                    pupil_diameter: 4,
                    pupil_direct_light: 1,
                    pupil_indirect_light: 1,
                    lens: 1,
                    shadow_test: 1,
                    glandula: 1,
                    field_view: 1,
                    fundus: '',
                },
            },
            input_other10: '',
            form_icd9cm: {
                id: '',
                soap_id: '',
                icd9cm: [],
                therapy_plan: '',
                doctor_in_charge_id: '',
            },
            form_transaction: {
                id:'',
                soap_id: '',
                costing_id: '',
                var_1: '',
                var_2: 1,
                var_3: '',
                notes: '',
                doctor_in_charge_id: '',
            },
            transaction_list: [],
            searchIGDlab: '',
            form_laboratory_igd: {
                id: '',
                soap_id: '',
                doctor_in_charge_id: '',
                date_request: moment(new Date()).format('YYYY-MM-DD HH:mm'),
                laboratory_request_items: [],
            },
            laboratory_igd_list: [],
            
            searchIGDrad: '',
            form_radiology_igd: {
                id: '',
                soap_id: '',
                doctor_in_charge_id: '',
                date_request: moment(new Date()).format('YYYY-MM-DD HH:mm'),
                radiology_request_items: [],
                clinical_indications: '',
            },
            radiology_igd_list: [],

            form_IGD_prescription: {
                id: '',
                soap_id: '',
                pharmacy_depo_id: '',
                soap_p_id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                form_prescription_parent: [],
                form_prescription: []
            },

            optIGDCatalog: [],
            IGDParentStock:'',
            IGDCatalog:'',
            prescription_igd_list: [],

            temp_IGD_parent: {
                parent_id: '',
                parent_name: '',

                value: '',
                package: '',
                signatura_time: '',
                administer: '',
                desc: '',
            },

            temp_IGD_items: {
                parent_id: '',
                parent_name: '',
                catalog_name: '',
                data_catalog: {},

                signatura_dose: '',
                signatura_unit: '',
                signatura_time: '',
                administer: '',
                desc: '',
            },

            IGDdisabled: {
                transaction: false,
                soap_p: false,
                laboratory: false,
                radiology: false,
                prescription: false,
                preParent: false,
                preItem: false,
            },

            specialist: {
                doctor_1: '',
                disabled_doctor_1: false,
                doctor_2: '',
                disabled_doctor_2: false,
                type_consultation_doctor_2: '',
                doctor_3: '',
                disabled_doctor_3: false,
                type_consultation_doctor_3: '',
                doctor_4: '',
                disabled_doctor_4: false,
                type_consultation_doctor_4: '',
                doctor_5: '',
                disabled_doctor_5: false,
                type_consultation_doctor_5: '',
            },

            // SPECIALIST 1
            form_FIRST_icd9cm: {
                id: '',
                soap_id: '',
                icd9cm: [],
                therapy_plan: '',
                doctor_in_charge_id: '',
            },
            FIRSTicd9cm_id: '',
            OptionsFIRSTIcd9cm: [],

            form_FIRST_transaction: {
                id:'',
                soap_id: '',
                view:'add',
                costing_id: '',
                var_1: '',
                var_2: 1,
                var_3: '',
                notes: '',
                doctor_in_charge_id: '',
            },
            transaction_FIRST_list: [],

            form_FIRST_laboratory: {
                id: '',
                soap_id: '',
                doctor_in_charge_id: '',
                date_request: moment(new Date()).format('YYYY-MM-DD HH:mm'),
                laboratory_request_items: [],
            },
            laboratory_FIRST_list: [],
            searchFIRSTlab: '',

            form_FIRST_radiology: {
                id: '',
                soap_id: '',
                doctor_in_charge_id: '',
                date_request: moment(new Date()).format('YYYY-MM-DD HH:mm'),
                radiology_request_items: [],
                clinical_indications: '',
            },
            radiology_FIRST_list: [],
            searchFIRSTrad: '',

            form_FIRST_prescription: {
                id: '',
                soap_id: '',
                pharmacy_depo_id: '',
                soap_p_id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                form_prescription_parent: [],
                form_prescription: []
            },

            optFIRSTCatalog: [],
            FIRSTParentStock:'',
            FIRSTCatalog:'',
            prescription_FIRST_list: [],

            temp_FIRST_parent: {
                parent_id: '',
                parent_name: '',

                value: '',
                package: '',
                signatura_time: '',
                administer: '',
                desc: '',
            },
            temp_FIRST_items: {
                parent_id: '',
                parent_name: '',
                catalog_name: '',
                data_catalog: {},

                signatura_dose: '',
                signatura_unit: '',
                signatura_time: '',
                administer: '',
                desc: '',
            },

            active_FIRST: {
                modal_procedure: false,
                modal_lab: false,
                modal_rad: false,
                modal_presc: false,
            },

            FIRSTdisabled: {
                transaction: false,
                soap_p: false,
                laboratory: false,
                radiology: false,
                prescription: false,
                preParent: false,
                preItem: false,
            },

            // SPECIALIST 2
            form_SECOND_icd9cm: {
                id: '',
                soap_id: '',
                icd9cm: [],
                therapy_plan: '',
                doctor_in_charge_id: '',
            },
            SECONDicd9cm_id: '',
            OptionsSECONDIcd9cm: [],

            form_SECOND_transaction: {
                id:'',
                soap_id: '',
                view:'add',
                costing_id: '',
                var_1: '',
                var_2: 1,
                var_3: '',
                notes: '',
                doctor_in_charge_id: '',
            },
            transaction_SECOND_list: [],

            form_SECOND_laboratory: {
                id: '',
                soap_id: '',
                doctor_in_charge_id: '',
                date_request: moment(new Date()).format('YYYY-MM-DD HH:mm'),
                laboratory_request_items: [],
            },
            laboratory_SECOND_list: [],
            searchSECONDlab: '',

            form_SECOND_radiology: {
                id: '',
                soap_id: '',
                doctor_in_charge_id: '',
                date_request: moment(new Date()).format('YYYY-MM-DD HH:mm'),
                radiology_request_items: [],
                clinical_indications: '',
            },
            radiology_SECOND_list: [],
            searchSECONDrad: '',

            form_SECOND_prescription: {
                id: '',
                soap_id: '',
                pharmacy_depo_id: '',
                soap_p_id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                form_prescription_parent: [],
                form_prescription: []
            },

            optSECONDCatalog: [],
            SECONDParentStock:'',
            SECONDCatalog:'',
            prescription_SECOND_list: [],

            temp_SECOND_parent: {
                parent_id: '',
                parent_name: '',

                value: '',
                package: '',
                signatura_time: '',
                administer: '',
                desc: '',
            },
            temp_SECOND_items: {
                parent_id: '',
                parent_name: '',
                catalog_name: '',
                data_catalog: {},

                signatura_dose: '',
                signatura_unit: '',
                signatura_time: '',
                administer: '',
                desc: '',
            },

            active_SECOND: {
                modal_procedure: false,
                modal_lab: false,
                modal_rad: false,
                modal_presc: false,
            },

            SECONDdisabled: {
                transaction: false,
                soap_p: false,
                laboratory: false,
                radiology: false,
                prescription: false,
                preParent: false,
                preItem: false,
            },
            
            // SPECIALIST 3
            form_THIRD_icd9cm: {
                id: '',
                soap_id: '',
                icd9cm: [],
                therapy_plan: '',
                doctor_in_charge_id: '',
            },
            THIRDicd9cm_id: '',
            OptionsTHIRDIcd9cm: [],

            form_THIRD_transaction: {
                id:'',
                soap_id: '',
                view:'add',
                costing_id: '',
                var_1: '',
                var_2: 1,
                var_3: '',
                notes: '',
                doctor_in_charge_id: '',
            },
            transaction_THIRD_list: [],

            form_THIRD_laboratory: {
                id: '',
                soap_id: '',
                doctor_in_charge_id: '',
                date_request: moment(new Date()).format('YYYY-MM-DD HH:mm'),
                laboratory_request_items: [],
            },
            laboratory_THIRD_list: [],
            searchTHIRDlab: '',

            form_THIRD_radiology: {
                id: '',
                soap_id: '',
                doctor_in_charge_id: '',
                date_request: moment(new Date()).format('YYYY-MM-DD HH:mm'),
                radiology_request_items: [],
                clinical_indications: '',
            },
            radiology_THIRD_list: [],
            searchTHIRDrad: '',

            form_THIRD_prescription: {
                id: '',
                soap_id: '',
                pharmacy_depo_id: '',
                soap_p_id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                form_prescription_parent: [],
                form_prescription: []
            },

            optTHIRDCatalog: [],
            THIRDParentStock:'',
            THIRDCatalog:'',
            prescription_THIRD_list: [],

            temp_THIRD_parent: {
                parent_id: '',
                parent_name: '',

                value: '',
                package: '',
                signatura_time: '',
                administer: '',
                desc: '',
            },
            temp_THIRD_items: {
                parent_id: '',
                parent_name: '',
                catalog_name: '',
                data_catalog: {},

                signatura_dose: '',
                signatura_unit: '',
                signatura_time: '',
                administer: '',
                desc: '',
            },

            active_THIRD: {
                modal_procedure: false,
                modal_lab: false,
                modal_rad: false,
                modal_presc: false,
            },

            THIRDdisabled: {
                transaction: false,
                soap_p: false,
                laboratory: false,
                radiology: false,
                prescription: false,
                preParent: false,
                preItem: false,
            },
            
            // SPECIALIST 4
            form_FOURTH_icd9cm: {
                id: '',
                soap_id: '',
                icd9cm: [],
                therapy_plan: '',
                doctor_in_charge_id: '',
            },
            FOURTHicd9cm_id: '',
            OptionsFOURTHIcd9cm: [],

            form_FOURTH_transaction: {
                id:'',
                soap_id: '',
                view:'add',
                costing_id: '',
                var_1: '',
                var_2: 1,
                var_3: '',
                notes: '',
                doctor_in_charge_id: '',
            },
            transaction_FOURTH_list: [],

            form_FOURTH_laboratory: {
                id: '',
                soap_id: '',
                doctor_in_charge_id: '',
                date_request: moment(new Date()).format('YYYY-MM-DD HH:mm'),
                laboratory_request_items: [],
            },
            laboratory_FOURTH_list: [],
            searchFOURTHlab: '',

            form_FOURTH_radiology: {
                id: '',
                soap_id: '',
                doctor_in_charge_id: '',
                date_request: moment(new Date()).format('YYYY-MM-DD HH:mm'),
                radiology_request_items: [],
                clinical_indications: '',
            },
            radiology_FOURTH_list: [],
            searchFOURTHrad: '',

            form_FOURTH_prescription: {
                id: '',
                soap_id: '',
                pharmacy_depo_id: '',
                soap_p_id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                form_prescription_parent: [],
                form_prescription: []
            },

            optFOURTHCatalog: [],
            FOURTHParentStock:'',
            FOURTHCatalog:'',
            prescription_FOURTH_list: [],

            temp_FOURTH_parent: {
                parent_id: '',
                parent_name: '',

                value: '',
                package: '',
                signatura_time: '',
                administer: '',
                desc: '',
            },
            
            temp_FOURTH_items: {
                parent_id: '',
                parent_name: '',
                catalog_name: '',
                data_catalog: {},

                signatura_dose: '',
                signatura_unit: '',
                signatura_time: '',
                administer: '',
                desc: '',
            },

            active_FOURTH: {
                modal_procedure: false,
                modal_lab: false,
                modal_rad: false,
                modal_presc: false,
            },

            FOURTHdisabled: {
                transaction: false,
                soap_p: false,
                laboratory: false,
                radiology: false,
                prescription: false,
                preParent: false,
                preItem: false,
            },
            
            // SPECIALIST 5
            form_FIFTH_icd9cm: {
                id: '',
                soap_id: '',
                icd9cm: [],
                therapy_plan: '',
                doctor_in_charge_id: '',
            },
            FIFTHicd9cm_id: '',
            OptionsFIFTHIcd9cm: [],

            form_FIFTH_transaction: {
                id:'',
                soap_id: '',
                view:'add',
                costing_id: '',
                var_1: '',
                var_2: 1,
                var_3: '',
                notes: '',
                doctor_in_charge_id: '',
            },
            transaction_FIFTH_list: [],

            form_FIFTH_laboratory: {
                id: '',
                soap_id: '',
                doctor_in_charge_id: '',
                date_request: moment(new Date()).format('YYYY-MM-DD HH:mm'),
                laboratory_request_items: [],
            },
            laboratory_FIFTH_list: [],
            searchFIFTHlab: '',

            form_FIFTH_radiology: {
                id: '',
                soap_id: '',
                doctor_in_charge_id: '',
                date_request: moment(new Date()).format('YYYY-MM-DD HH:mm'),
                radiology_request_items: [],
                clinical_indications: '',
            },
            radiology_FIFTH_list: [],
            searchFIFTHrad: '',

            form_FIFTH_prescription: {
                id: '',
                soap_id: '',
                pharmacy_depo_id: '',
                soap_p_id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                form_prescription_parent: [],
                form_prescription: []
            },

            optFIFTHCatalog: [],
            FIFTHParentStock:'',
            FIFTHCatalog:'',
            prescription_FIFTH_list: [],

            temp_FIFTH_parent: {
                parent_id: '',
                parent_name: '',

                value: '',
                package: '',
                signatura_time: '',
                administer: '',
                desc: '',
            },
            temp_FIFTH_items: {
                parent_id: '',
                parent_name: '',
                catalog_name: '',
                data_catalog: {},

                signatura_dose: '',
                signatura_unit: '',
                signatura_time: '',
                administer: '',
                desc: '',
            },

            active_FIFTH: {
                modal_procedure: false,
                modal_lab: false,
                modal_rad: false,
                modal_presc: false,
            },

            FIFTHdisabled: {
                transaction: false,
                soap_p: false,
                laboratory: false,
                radiology: false,
                prescription: false,
                preParent: false,
                preItem: false,
            },
            combine_empty_found: [],
        }
    },
    computed: {
        ...mapState({ 
            optCunsultType: state => state.frontoptions.doctor.consultation.emergency_consultation_type,

            optAnamnesaType: state => state.frontoptions.nurse.assessment.anamnesa.anamnesa_type,
            optFamilyRelation: state => state.frontoptions.nurse.assessment.anamnesa.family_relation,

            // vital option
            OptionsOxygen: state => state.soap.vitals.OptionsOxygen,
            OptionsAware: state => state.soap.vitals.OptionsAware,
            OptionGcsEye: state => state.soap.vitals.OptionGcsEye,
            OptionGcsVerbal: state => state.soap.vitals.OptionGcsVerbal,
            OptionGcsMotoric: state => state.soap.vitals.OptionGcsMotoric,
            optSkinColor: state => state.frontoptions.general.vital_sign.skin_color,
            optBehavior: state => state.frontoptions.general.vital_sign.behavior,
                        
            // generalis option
            OptionsHeadCircumference: state => state.soap.generalis.OptionsHeadCircumference,
            OptionsHeadHairColor: state => state.soap.generalis.OptionsHeadHairColor,
            OptionsHeadHairCondition: state => state.soap.generalis.OptionsHeadHairCondition,
            OptionsEyeConjunctiva: state => state.soap.generalis.OptionsEyeConjunctiva,
            OptionsEyeSclera: state => state.soap.generalis.OptionsEyeSclera,
            OptionsEyePupil: state => state.soap.generalis.OptionsEyePupil,
            OptionsEyeReflect: state => state.soap.generalis.OptionsEyeReflect,
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

            // prescription options
            OptionsUtilization: state => state.frontoptions.doctor.assessment.prescription.utilization,
            OptionsConsumeTime: state => state.frontoptions.doctor.assessment.prescription.consume_time,
            OptionsSignatura: state => state.frontoptions.doctor.assessment.prescription.signatura,
            optParentPackage: state => state.frontoptions.doctor.assessment.prescription.parent_package,
            optParentStock: state => state.frontoptions.doctor.assessment.prescription.parent_stock,
            optPrescItemStatus: state => state.frontoptions.doctor.assessment.prescription.prescription_item_status,
        }),

        filteredListIGDLab() {
            return this.supportPackage.lab_IGD_package_list.map((lab) => {
                const filteredCategoryItems = lab.category_items.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchIGDlab.toLowerCase())
                );
                return {
                    ...lab,
                    category_items: filteredCategoryItems,
                };
            }).filter(lab => lab.category_items.length > 0); 
        },

        filteredListIGDRad() {
            return this.supportPackage?.rad_IGD_package_list.map((rad) => {
                const filteredCategoryItems = rad?.category_items?.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchIGDrad.toLowerCase())
                );
                return {
                    ...rad,
                    category_items: filteredCategoryItems,
                };
            }).filter(rad => rad?.category_items?.length > 0); 
        },

        filteredListFIRSTLab() {
            return this.supportPackage.lab_FIRST_package_list.map((lab) => {
                const filteredCategoryItems = lab.category_items.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchFIRSTlab.toLowerCase())
                );
                return {
                    ...lab,
                    category_items: filteredCategoryItems,
                };
            }).filter(lab => lab.category_items.length > 0); 
        },

        filteredListFIRSTRad() {
            return this.supportPackage?.rad_FIRST_package_list.map((rad) => {
                const filteredCategoryItems = rad?.category_items?.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchFIRSTrad.toLowerCase())
                );
                return {
                    ...rad,
                    category_items: filteredCategoryItems,
                };
            }).filter(rad => rad?.category_items?.length > 0); 
        },

        filteredListSECONDLab() {
            return this.supportPackage.lab_SECOND_package_list.map((lab) => {
                const filteredCategoryItems = lab.category_items.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchSECONDlab.toLowerCase())
                );
                return {
                    ...lab,
                    category_items: filteredCategoryItems,
                };
            }).filter(lab => lab.category_items.length > 0); 
        },

        filteredListSECONDRad() {
            return this.supportPackage?.rad_SECOND_package_list.map((rad) => {
                const filteredCategoryItems = rad?.category_items?.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchSECONDrad.toLowerCase())
                );
                return {
                    ...rad,
                    category_items: filteredCategoryItems,
                };
            }).filter(rad => rad?.category_items?.length > 0); 
        },

        filteredListTHIRDLab() {
            return this.supportPackage.lab_THIRD_package_list.map((lab) => {
                const filteredCategoryItems = lab.category_items.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchTHIRDlab.toLowerCase())
                );
                return {
                    ...lab,
                    category_items: filteredCategoryItems,
                };
            }).filter(lab => lab.category_items.length > 0); 
        },

        filteredListTHIRDRad() {
            return this.supportPackage?.rad_THIRD_package_list.map((rad) => {
                const filteredCategoryItems = rad?.category_items?.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchTHIRDrad.toLowerCase())
                );
                return {
                    ...rad,
                    category_items: filteredCategoryItems,
                };
            }).filter(rad => rad?.category_items?.length > 0); 
        },

        filteredListFOURTHLab() {
            return this.supportPackage.lab_FOURTH_package_list.map((lab) => {
                const filteredCategoryItems = lab.category_items.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchFOURTHlab.toLowerCase())
                );
                return {
                    ...lab,
                    category_items: filteredCategoryItems,
                };
            }).filter(lab => lab.category_items.length > 0); 
        },

        filteredListFOURTHRad() {
            return this.supportPackage?.rad_FOURTH_package_list.map((rad) => {
                const filteredCategoryItems = rad?.category_items?.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchFOURTHrad.toLowerCase())
                );
                return {
                    ...rad,
                    category_items: filteredCategoryItems,
                };
            }).filter(rad => rad?.category_items?.length > 0); 
        },

        filteredListFIFTHLab() {
            return this.supportPackage.lab_FIFTH_package_list.map((lab) => {
                const filteredCategoryItems = lab.category_items.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchFIFTHlab.toLowerCase())
                );
                return {
                    ...lab,
                    category_items: filteredCategoryItems,
                };
            }).filter(lab => lab.category_items.length > 0); 
        },

        filteredListFIFTHRad() {
            return this.supportPackage?.rad_FIFTH_package_list.map((rad) => {
                const filteredCategoryItems = rad?.category_items?.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchFIFTHrad.toLowerCase())
                );
                return {
                    ...rad,
                    category_items: filteredCategoryItems,
                };
            }).filter(rad => rad?.category_items?.length > 0); 
        },
    },

    filters:{
        number(value) {
            return Number(value)
        },
    },

    watch: {
        "form_igd.anamesa_type_id"(newValue) {
            if (newValue == "1") {
                this.form_igd.family_relation_id = ''
                this.form_igd.family_name = ''
            }
        },

        "temp_IGD_parent.administer"(newValue) {
            if (newValue != this.other_administer) {
                this.temp_IGD_parent.desc = ''
            }
        },
        "temp_IGD_items.administer"(newValue) {
            if (newValue != this.other_administer) {
                this.temp_IGD_items.desc = ''
            }
        },

        "temp_FIRST_parent.administer"(newValue) {
            if (newValue != this.other_administer) {
                this.temp_FIRST_parent.desc = ''
            }
        },
        "temp_FIRST_items.administer"(newValue) {
            if (newValue != this.other_administer) {
                this.temp_FIRST_items.desc = ''
            }
        },

        "temp_SECOND_parent.administer"(newValue) {
            if (newValue != this.other_administer) {
                this.temp_SECOND_parent.desc = ''
            }
        },
        "temp_SECOND_items.administer"(newValue) {
            if (newValue != this.other_administer) {
                this.temp_SECOND_items.desc = ''
            }
        },

        "temp_THIRD_parent.administer"(newValue) {
            if (newValue != this.other_administer) {
                this.temp_THIRD_parent.desc = ''
            }
        },
        "temp_THIRD_items.administer"(newValue) {
            if (newValue != this.other_administer) {
                this.temp_THIRD_items.desc = ''
            }
        },

        "temp_FOURTH_parent.administer"(newValue) {
            if (newValue != this.other_administer) {
                this.temp_FOURTH_parent.desc = ''
            }
        },
        "temp_FOURTH_items.administer"(newValue) {
            if (newValue != this.other_administer) {
                this.temp_FOURTH_items.desc = ''
            }
        },

        "temp_FIFTH_parent.administer"(newValue) {
            if (newValue != this.other_administer) {
                this.temp_FIFTH_parent.desc = ''
            }
        },
        "temp_FIFTH_items.administer"(newValue) {
            if (newValue != this.other_administer) {
                this.temp_FIFTH_items.desc = ''
            }
        },
    },
    methods: {
        parentTrigger(message) {
            this.doctor_list = message?.doctor_list
            this.costing_list = message.costing_list
            this.catalog_list = message.catalog_list
            this.PackageOptions = message.stock_list
            this.optSignTime = message.signature_time
            this.optSignDose = message.signature_dose
            this.optSignUnits = message.signature_units
            this.optAdminister = message.catalog_instruction
            this.regData = message.regData

            const properties = ['IGD', 'FIRST', 'SECOND', 'THIRD', 'FOURTH', 'FIFTH'];
            properties.forEach(prop => {
                this.supportPackage[`lab_${prop}_package_list`] = message?.lab_list;
                this.supportPackage[`rad_${prop}_package_list`] = message?.rad_list;
                this[`form_${prop}_prescription`].pharmacy_depo_id = this.depo_emergency
                this[`form_${prop}_prescription`].registration_destination_id = message.regData?.registration_destination_id
            });

            this.temp_header.alias_name = this.regData?.alias_name
            this.temp_header.name = this.regData?.name
            this.temp_header.norm = this.regData?.norm
            this.temp_header.birth_date = this.regData?.birth_date
            this.temp_header.weight = this.regData?.ttv?.weight
            this.temp_header.height = this.regData?.ttv?.height

            this.isPews = this.pewsCriteria(message.regData?.birth_date)

            this.openModalIGD()
        },

        imtChanged($event){
            let brt_badan = 0
            if (this.form_igd.weight) {
                brt_badan = parseFloat(this.form_igd.weight);
            } else {
                brt_badan = 0
            }

            let tng_badan = 0
            if (this.form_igd.height) {
                tng_badan = parseFloat(this.form_igd.height);
            } else {
                tng_badan = 0
            }

            let imt = 0.0;    

            if (brt_badan && tng_badan) {
                imt = tng_badan * tng_badan;
                imt = (brt_badan/imt)*100*100;
                this.form_igd.imt = imt.toFixed(1);
            }
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        getAssessment() {
            let message = 'initial dulu'
            this.$emit("refreshAssessment", message);
        },

        openModalIGD() {
            this.active_act.modal_IGD = true
            this.handlingIGD(this.regData?.registration_destination_id, 'ALL')
        },

        async handlingIGD(regDesId, request_refresh) {
            await this.resetIGD()
            try {
                this.isLoading = true
                this.isDisabledIGD = true
                this.disabledOphtal = true

                await this.$store.dispatch('soap/getByRegId', regDesId)
                .then (
                    async response => {
                        if (response.length > 0) {
                            const obj = response[0]

                            this.form_igd.id = obj?.id 
                            this.form_igd.doctor_in_charge_id = obj?.doctor_in_charge_id
                            this.form_igd.doctor_in_charge_id_1 = obj?.doctor_in_charge_id_1
                            this.form_igd.doctor_in_charge_id_2 = obj?.doctor_in_charge_id_2
                            this.form_igd.doctor_in_charge_id_3 = obj?.doctor_in_charge_id_3
                            this.form_igd.doctor_in_charge_id_4 = obj?.doctor_in_charge_id_4
                            this.form_igd.doctor_in_charge_id_5 = obj?.doctor_in_charge_id_5
                            this.form_igd.doctor_in_charge_name = obj?.employee_doctor_name
                            this.form_igd.doctor_in_charge_name_1 = obj?.doctor_in_charge_name_1
                            this.form_igd.doctor_in_charge_name_2 = obj?.doctor_in_charge_name_2
                            this.form_igd.doctor_in_charge_name_3 = obj?.doctor_in_charge_name_3
                            this.form_igd.doctor_in_charge_name_4 = obj?.doctor_in_charge_name_4
                            this.form_igd.doctor_in_charge_name_5 = obj?.doctor_in_charge_name_5
                            this.form_igd.type_consultation_doctor_2 = obj?.type_consultation_doctor_2
                            this.form_igd.type_consultation_doctor_3 = obj?.type_consultation_doctor_3
                            this.form_igd.type_consultation_doctor_4 = obj?.type_consultation_doctor_4
                            this.form_igd.type_consultation_doctor_5 = obj?.type_consultation_doctor_5
                            this.form_igd.registration_destination_id = obj?.registration_destination_id
                            this.form_igd.anamesa_type_id = obj?.anamesa_type_id, 
                            this.form_igd.family_relation_id = obj?.family_relation_id
                            this.form_igd.family_name = obj?.family_name
                            this.form_igd.main_complaint = obj?.main_complaint
                            this.form_igd.history_disease_now = obj?.history_disease_now
                            this.form_igd.history_disease_past = obj?.history_disease_past
                            this.form_igd.history_drugs = obj?.history_drugs
                            this.form_igd.systolic_blood_pressure = obj?.systolic_blood_pressure
                            this.form_igd.diastolic_blood_pressure = obj?.diastolic_blood_pressure
                            this.form_igd.pulse_frequency = obj?.pulse_frequency
                            this.form_igd.breath_frequency = obj?.breath_frequency
                            this.form_igd.oximetry_frequency = obj?.oximetry_frequency
                            this.form_igd.temperature = obj?.temperature
                            this.form_igd.oxygen_type_id = obj?.oxygen_type_id
                            this.form_igd.aware = obj?.aware
                            this.form_igd.eye_score = obj?.eye_score
                            this.form_igd.verbal_score = obj?.verbal_score
                            this.form_igd.motor_score = obj?.motor_score
                            this.form_igd.skin_color = obj?.skin_color
                            this.form_igd.behavior = obj?.behavior

                            this.form_igd.weight = obj?.weight
                            this.form_igd.height = obj?.height
                            this.form_igd.imt = obj?.imt
                            this.form_igd.head_circumference = obj?.head_circumference
                            this.form_igd.chest_circumference = obj?.chest_circumference
                            this.form_igd.arm_circumference = obj?.arm_circumference
                            this.form_igd.stomach_circumference = obj?.stomach_circumference
                            this.form_igd.knee_circumference = obj?.knee_circumference
                            this.form_igd.icd10_notes = obj?.icd10_notes
                            this.form_igd.non_icd10 = obj?.non_icd10 ? obj?.non_icd10 : []
        
                            this.form_igd.head = obj?.head || 1
                            this.form_igd.head_hair_color = obj?.head_hair_color || 1
                            this.form_igd.head_hair_condition = obj?.head_hair_condition || 1
                            this.form_igd.eye_conjunctive = obj?.eye_conjunctive || 1
                            this.form_igd.eye_sclera = obj?.eye_sclera || 1
                            this.form_igd.eye_pupil = obj?.eye_pupil || 1
                            this.form_igd.eye_light_reflex = obj?.eye_light_reflex || 1
                            this.form_igd.mouth_mucosa = obj?.mouth_mucosa || 1
                            this.form_igd.mouth_discoloration = obj?.mouth_discoloration || 1
                            this.form_igd.mouth_tongue = obj?.mouth_tongue || 1
                            this.form_igd.mouth_caries = obj?.mouth_caries || 1
                            this.form_igd.neck_kgb = obj?.neck_kgb || 1
                            this.form_igd.neck_trachea_position = obj?.neck_trachea_position || 1
                            this.form_igd.neck_massa = obj?.neck_massa || 1
                            this.form_igd.thorax_insp_chest = obj?.thorax_insp_chest || 1
                            this.form_igd.thorax_insp_retraction = obj?.thorax_insp_retraction || 1
                            this.form_igd.thorax_palp_chest = obj?.thorax_palp_chest || 1
                            this.form_igd.thorax_palp_fremitus = obj?.thorax_palp_fremitus || 1
                            this.form_igd.thorax_palp_crepitation = obj?.thorax_palp_crepitation || 1
                            this.form_igd.thorax_ausk_pul_vasiculer = obj?.thorax_ausk_pul_vasiculer || 1
                            this.form_igd.thorax_ausk_pul_rhonki = obj?.thorax_ausk_pul_rhonki || 1
                            this.form_igd.thorax_ausk_pul_wheezing = obj?.thorax_ausk_pul_wheezing || 1
                            this.form_igd.thorax_ausk_cor_heart_sound = obj?.thorax_ausk_cor_heart_sound || 1
                            this.form_igd.thorax_ausk_cor_murmur = obj?.thorax_ausk_cor_murmur || 1
                            this.form_igd.thorax_ausk_cor_gallop = obj?.thorax_ausk_cor_gallop || 1
                            this.form_igd.abdomen_supel = obj?.abdomen_supel || 1
                            this.form_igd.abdomen_bowel_sounds = obj?.abdomen_bowel_sounds || 1
                            this.form_igd.abdomen_tenderness = obj?.abdomen_tenderness || 1
                            this.form_igd.abdomen_hepar_lien = obj?.abdomen_hepar_lien || 1
                            this.form_igd.abdomen_other_physical_check = obj?.abdomen_other_physical_check || ''
                            this.form_igd.extremity_acral = obj?.extremity_acral || 1
                            this.form_igd.extremity_crt = obj?.extremity_crt || 1
                            this.form_igd.extremity_edema = obj?.extremity_edema || 1
                            this.form_igd.genitalia = obj?.genitalia || ''
                            this.form_igd.localist_status = obj?.localist_status || ''
                            this.form_igd.neurologist_status = obj?.neurologist_status || ''
                            this.form_igd.ophthal_status = obj?.ophthal_status || ''
        
                            this.ophInput.ophthal_od.visus = obj?.ophthal_od?.visus || 12
                            this.ophInput.ophthal_od.tio_digital = obj?.ophthal_od?.tio_digital || 1
                            this.ophInput.ophthal_od.tio_tonometri_sciots = obj?.ophthal_od?.tio_tonometri_sciots || ''
                            this.ophInput.ophthal_od.eye_position = obj?.ophthal_od?.eye_position || 1
                            this.ophInput.ophthal_od.eye_movement = obj?.ophthal_od?.eye_movement || 1
                            this.ophInput.ophthal_od.super_silia = obj?.ophthal_od?.super_silia || 1
                            this.ophInput.ophthal_od.palpebra_edema = obj?.ophthal_od?.palpebra_edema || 1
                            this.ophInput.ophthal_od.palpebra_ptosis = obj?.ophthal_od?.palpebra_ptosis || 1
                            this.ophInput.ophthal_od.palpebra_lagophtalmus = obj?.ophthal_od?.palpebra_lagophtalmus || 1
                            this.ophInput.ophthal_od.palpebra_tumor = obj?.ophthal_od?.palpebra_tumor || 1
                            this.ophInput.ophthal_od.apparatus_lakrimalis = obj?.ophthal_od?.apparatus_lakrimalis || 1
                            this.ophInput.ophthal_od.silia = obj?.ophthal_od?.silia || 1
                            this.ophInput.ophthal_od.conjunctive_hiperemis = obj?.ophthal_od?.conjunctive_hiperemis || 1
                            this.ophInput.ophthal_od.conjunctive_bleed = obj?.ophthal_od?.conjunctive_bleed || 1
                            this.ophInput.ophthal_od.cornea = obj?.ophthal_od?.cornea || 1
                            this.ophInput.ophthal_od.camera_oculi = obj?.ophthal_od?.camera_oculi || 1
                            this.ophInput.ophthal_od.iris_color = obj?.ophthal_od?.iris_color || 2
                            this.ophInput.ophthal_od.iris_cripte = obj?.ophthal_od?.iris_cripte || 1
                            this.ophInput.ophthal_od.pupil_form = obj?.ophthal_od?.pupil_form || 1
                            this.ophInput.ophthal_od.pupil_diameter = obj?.ophthal_od?.pupil_diameter || 4
                            this.ophInput.ophthal_od.pupil_direct_light = obj?.ophthal_od?.pupil_direct_light || 1
                            this.ophInput.ophthal_od.pupil_indirect_light = obj?.ophthal_od?.pupil_indirect_light || 1
                            this.ophInput.ophthal_od.lens = obj?.ophthal_od?.lens || 1
                            this.ophInput.ophthal_od.shadow_test = obj?.ophthal_od?.shadow_test || 1
                            this.ophInput.ophthal_od.glandula = obj?.ophthal_od?.glandula || 1
                            this.ophInput.ophthal_od.field_view = obj?.ophthal_od?.field_view || 1
                            this.ophInput.ophthal_od.fundus = obj?.ophthal_od?.fundus || ''
        
                            this.ophInput.ophthal_os.visus = obj?.ophthal_os?.visus || 12
                            this.ophInput.ophthal_os.tio_digital = obj?.ophthal_os?.tio_digital || 1
                            this.ophInput.ophthal_os.tio_tonometri_sciots = obj?.ophthal_os?.tio_tonometri_sciots || ''
                            this.ophInput.ophthal_os.eye_position = obj?.ophthal_os?.eye_position || 1
                            this.ophInput.ophthal_os.eye_movement = obj?.ophthal_os?.eye_movement || 1
                            this.ophInput.ophthal_os.super_silia = obj?.ophthal_os?.super_silia || 1
                            this.ophInput.ophthal_os.palpebra_edema = obj?.ophthal_os?.palpebra_edema || 1
                            this.ophInput.ophthal_os.palpebra_ptosis = obj?.ophthal_os?.palpebra_ptosis || 1
                            this.ophInput.ophthal_os.palpebra_lagophtalmus = obj?.ophthal_os?.palpebra_lagophtalmus || 1
                            this.ophInput.ophthal_os.palpebra_tumor = obj?.ophthal_os?.palpebra_tumor || 1
                            this.ophInput.ophthal_os.apparatus_lakrimalis = obj?.ophthal_os?.apparatus_lakrimalis || 1
                            this.ophInput.ophthal_os.silia = obj?.ophthal_os?.silia || 1
                            this.ophInput.ophthal_os.conjunctive_hiperemis = obj?.ophthal_os?.conjunctive_hiperemis || 1
                            this.ophInput.ophthal_os.conjunctive_bleed = obj?.ophthal_os?.conjunctive_bleed || 1
                            this.ophInput.ophthal_os.cornea = obj?.ophthal_os?.cornea || 1
                            this.ophInput.ophthal_os.camera_oculi = obj?.ophthal_os?.camera_oculi || 1
                            this.ophInput.ophthal_os.iris_color = obj?.ophthal_os?.iris_color || 2
                            this.ophInput.ophthal_os.iris_cripte = obj?.ophthal_os?.iris_cripte || 1
                            this.ophInput.ophthal_os.pupil_form = obj?.ophthal_os?.pupil_form || 1
                            this.ophInput.ophthal_os.pupil_diameter = obj?.ophthal_os?.pupil_diameter || 4
                            this.ophInput.ophthal_os.pupil_direct_light = obj?.ophthal_os?.pupil_direct_light || 1
                            this.ophInput.ophthal_os.pupil_indirect_light = obj?.ophthal_os?.pupil_indirect_light || 1
                            this.ophInput.ophthal_os.lens = obj?.ophthal_os?.lens || 1
                            this.ophInput.ophthal_os.shadow_test = obj?.ophthal_os?.shadow_test || 1
                            this.ophInput.ophthal_os.glandula = obj?.ophthal_os?.glandula || 1
                            this.ophInput.ophthal_os.field_view = obj?.ophthal_os?.field_view || 1
                            this.ophInput.ophthal_os.fundus = obj?.ophthal_os?.fundus || ''
        
                            this.soap_id = obj.id
        
                            if (obj.icd10) {
                                const mappedIcd10 = obj.icd10.map(item => {
                                    return {
                                        icd10_id: item.icd10_id,
                                        value: item.icd10_id,
                                        title: `${item.code} ${item.name}`, 
                                        action: "action"
                                    };
                                });                
                                this.form_igd.icd10 = mappedIcd10;
                            }  
                            
                            // ============================================= DOKTER IGD ============================================= 
                            if (obj.doctor_in_charge_id) {
                                // soap p
                                this.form_icd9cm.soap_id = obj.id
                                this.form_icd9cm.doctor_in_charge_id = obj.doctor_in_charge_id

                                // transaction doctor
                                this.form_transaction.soap_id = obj.id
                                this.form_transaction.doctor_in_charge_id = obj.doctor_in_charge_id
                                                            
                                // laboratory
                                this.form_laboratory_igd.soap_id = obj.id
                                this.form_laboratory_igd.doctor_in_charge_id = obj.doctor_in_charge_id
            
                                // Radiology
                                this.form_radiology_igd.soap_id = obj.id
                                this.form_radiology_igd.doctor_in_charge_id = obj.doctor_in_charge_id
            
                                // PRESCRIPTION
                                this.form_IGD_prescription.soap_id = obj.id
                                this.form_IGD_prescription.doctor_in_charge_id = obj.doctor_in_charge_id
                                

                                if (request_refresh === 'ALL') {
                                    this.getIGDSoapP(this.form_icd9cm.soap_id, this.form_icd9cm.doctor_in_charge_id)
                                    this.transaction_list = await this.getTransactionList(
                                        this.form_transaction.soap_id,
                                        this.form_transaction.doctor_in_charge_id
                                    )
                                    this.laboratory_igd_list = await this.getLaboratoryList(
                                        this.form_laboratory_igd.soap_id, 
                                        this.form_laboratory_igd.doctor_in_charge_id
                                    )
                                    this.radiology_igd_list = await this.getRadiologyList(
                                        this.form_radiology_igd.soap_id, 
                                        this.form_radiology_igd.doctor_in_charge_id
                                    )
                                    this.prescription_igd_list = await this.getPrescriptionList(
                                        this.form_IGD_prescription.soap_id, 
                                        this.form_IGD_prescription.doctor_in_charge_id
                                    )
                                    this.prescription_igd_list.forEach(item => {
                                        item.form_prescription_parent.sort((a, b) => a.id - b.id)
                                        item.form_prescription.sort((a, b) => a.sort - b.sort)
                                    })
                                }
                            }
        
                            // ======================================== DOKTER SPESIALIS 1 ======================================== 
        
                            this.specialist.doctor_1 = { value: obj.doctor_in_charge_id_1, title: obj.doctor_in_charge_name_1 }
        
                            if (obj.doctor_in_charge_id_1) {
                                this.specialist.disabled_doctor_1 = true
        
                                // soap p
                                this.form_FIRST_icd9cm.soap_id = obj.id
                                this.form_FIRST_icd9cm.doctor_in_charge_id = obj.doctor_in_charge_id_1
                                
                                // transaction doctor
                                this.form_FIRST_transaction.soap_id = obj.id
                                this.form_FIRST_transaction.doctor_in_charge_id = obj.doctor_in_charge_id_1
            
                                // laboratory
                                this.form_FIRST_laboratory.soap_id = obj.id
                                this.form_FIRST_laboratory.doctor_in_charge_id = obj.doctor_in_charge_id_1
            
                                // Radiology
                                this.form_FIRST_radiology.soap_id = obj.id
                                this.form_FIRST_radiology.doctor_in_charge_id = obj.doctor_in_charge_id_1
            
                                // PRESCRIPTION
                                this.form_FIRST_prescription.soap_id = obj.id
                                this.form_FIRST_prescription.doctor_in_charge_id = obj.doctor_in_charge_id_1
                                
                                if (request_refresh === 'ALL') {
                                    this.getFIRSTSoapP(this.form_FIRST_icd9cm.soap_id, this.form_FIRST_icd9cm.doctor_in_charge_id)
                                    this.transaction_FIRST_list = await this.getTransactionList(
                                        this.form_FIRST_transaction.soap_id,
                                        this.form_FIRST_transaction.doctor_in_charge_id
                                    )
                                    this.laboratory_FIRST_list = await this.getLaboratoryList(
                                        this.form_FIRST_laboratory.soap_id, 
                                        this.form_FIRST_laboratory.doctor_in_charge_id
                                    ) 
                                    this.radiology_FIRST_list = await this.getRadiologyList(
                                        this.form_FIRST_radiology.soap_id, 
                                        this.form_FIRST_radiology.doctor_in_charge_id
                                    )                                    
                                    this.prescription_FIRST_list = await this.getPrescriptionList(
                                        this.form_FIRST_prescription.soap_id, 
                                        this.form_FIRST_prescription.doctor_in_charge_id
                                    )
                                    this.prescription_FIRST_list.forEach(item => {
                                        item.form_prescription_parent.sort((a, b) => a.id - b.id)
                                        item.form_prescription.sort((a, b) => a.sort - b.sort)
                                    })
                                }
                            }
        
                            // ======================================== DOKTER SPESIALIS 2 ======================================== 
        
                            this.specialist.doctor_2 = { value: obj.doctor_in_charge_id_2, title: obj.doctor_in_charge_name_2 }
                            this.specialist.type_consultation_doctor_2 = { 
                                value: obj.type_consultation_doctor_2 || '', 
                                title: obj.type_consultation_doctor_2 ? this.getConsultationTypeName(obj.type_consultation_doctor_2) : ''
                            }
        
                            if (obj.doctor_in_charge_id_2) {
                                this.specialist.disabled_doctor_2 = true
        
                                // soap p
                                this.form_SECOND_icd9cm.soap_id = obj.id
                                this.form_SECOND_icd9cm.doctor_in_charge_id = obj.doctor_in_charge_id_2
                                
                                // transaction doctor
                                this.form_SECOND_transaction.soap_id = obj.id
                                this.form_SECOND_transaction.doctor_in_charge_id = obj.doctor_in_charge_id_2
            
                                // laboratory
                                this.form_SECOND_laboratory.soap_id = obj.id
                                this.form_SECOND_laboratory.doctor_in_charge_id = obj.doctor_in_charge_id_2
                                
                                // Radiology
                                this.form_SECOND_radiology.soap_id = obj.id
                                this.form_SECOND_radiology.doctor_in_charge_id = obj.doctor_in_charge_id_2
            
                                // PRESCRIPTION
                                this.form_SECOND_prescription.soap_id = obj.id
                                this.form_SECOND_prescription.doctor_in_charge_id = obj.doctor_in_charge_id_2
                                
                                if (request_refresh === 'ALL') {
                                    this.getSECONDSoapP(this.form_SECOND_icd9cm.soap_id, this.form_SECOND_icd9cm.doctor_in_charge_id)
                                    this.transaction_SECOND_list = await this.getTransactionList(
                                        this.form_SECOND_transaction.soap_id,
                                        this.form_SECOND_transaction.doctor_in_charge_id
                                    )
                                    this.laboratory_SECOND_list = await this.getLaboratoryList(
                                        this.form_SECOND_laboratory.soap_id, 
                                        this.form_SECOND_laboratory.doctor_in_charge_id
                                    )
                                    this.radiology_SECOND_list = await this.getRadiologyList(
                                        this.form_SECOND_radiology.soap_id, 
                                        this.form_SECOND_radiology.doctor_in_charge_id
                                    )
                                    this.prescription_SECOND_list = await this.getPrescriptionList(
                                        this.form_SECOND_prescription.soap_id, 
                                        this.form_SECOND_prescription.doctor_in_charge_id
                                    )
                                    this.prescription_SECOND_list.forEach(item => {
                                        item.form_prescription_parent.sort((a, b) => a.id - b.id)
                                        item.form_prescription.sort((a, b) => a.sort - b.sort)
                                    })
                                }
                            }
        
                            // ======================================== DOKTER SPESIALIS 3 ======================================== 
                            this.specialist.doctor_3 = { value: obj.doctor_in_charge_id_3, title: obj.doctor_in_charge_name_3 }
                            this.specialist.type_consultation_doctor_3 = { 
                                value: obj.type_consultation_doctor_3 || '', 
                                title: obj.type_consultation_doctor_3 ? this.getConsultationTypeName(obj.type_consultation_doctor_3) : ''
                            }
                            
                            if (obj.doctor_in_charge_id_3) {
                                this.specialist.disabled_doctor_3 = true
        
                                // soap p
                                this.form_THIRD_icd9cm.soap_id = obj.id
                                this.form_THIRD_icd9cm.doctor_in_charge_id = obj.doctor_in_charge_id_3
                                
                                // transaction doctor
                                this.form_THIRD_transaction.soap_id = obj.id
                                this.form_THIRD_transaction.doctor_in_charge_id = obj.doctor_in_charge_id_3
            
                                // laboratory
                                this.form_THIRD_laboratory.soap_id = obj.id
                                this.form_THIRD_laboratory.doctor_in_charge_id = obj.doctor_in_charge_id_3
            
                                // Radiology
                                this.form_THIRD_radiology.soap_id = obj.id
                                this.form_THIRD_radiology.doctor_in_charge_id = obj.doctor_in_charge_id_3
            
                                // PRESCRIPTION
                                this.form_THIRD_prescription.soap_id = obj.id
                                this.form_THIRD_prescription.doctor_in_charge_id = obj.doctor_in_charge_id_3

                                if (request_refresh === 'ALL') {
                                    this.getTHIRDSoapP(this.form_THIRD_icd9cm.soap_id, this.form_THIRD_icd9cm.doctor_in_charge_id)
                                    this.transaction_THIRD_list = await this.getTransactionList(
                                        this.form_THIRD_transaction.soap_id,
                                        this.form_THIRD_transaction.doctor_in_charge_id
                                    )
                                    this.laboratory_THIRD_list = await this.getLaboratoryList(
                                        this.form_THIRD_laboratory.soap_id, 
                                        this.form_THIRD_laboratory.doctor_in_charge_id
                                    ) 
                                    this.radiology_THIRD_list = await this.getRadiologyList(
                                        this.form_THIRD_radiology.soap_id, 
                                        this.form_THIRD_radiology.doctor_in_charge_id
                                    )
                                    this.prescription_THIRD_list = await this.getPrescriptionList(
                                        this.form_THIRD_prescription.soap_id, 
                                        this.form_THIRD_prescription.doctor_in_charge_id
                                    )
                                    this.prescription_THIRD_list.forEach(item => {
                                        item.form_prescription_parent.sort((a, b) => a.id - b.id)
                                        item.form_prescription.sort((a, b) => a.sort - b.sort)
                                    })
                                }
                            }
        
                            // ======================================== DOKTER SPESIALIS 4 ======================================== 
                            this.specialist.doctor_4 = { value: obj.doctor_in_charge_id_4, title: obj.doctor_in_charge_name_4 }
                            this.specialist.type_consultation_doctor_4 = { 
                                value: obj.type_consultation_doctor_4 || '', 
                                title: obj.type_consultation_doctor_4 ? this.getConsultationTypeName(obj.type_consultation_doctor_4) : ''
                            }
                            
                            if (obj.doctor_in_charge_id_4) {
                                this.specialist.disabled_doctor_4 = true
        
                                // soap p
                                this.form_FOURTH_icd9cm.soap_id = obj.id
                                this.form_FOURTH_icd9cm.doctor_in_charge_id = obj.doctor_in_charge_id_4
                                
                                // transaction doctor
                                this.form_FOURTH_transaction.soap_id = obj.id
                                this.form_FOURTH_transaction.doctor_in_charge_id = obj.doctor_in_charge_id_4
            
                                // laboratory
                                this.form_FOURTH_laboratory.soap_id = obj.id
                                this.form_FOURTH_laboratory.doctor_in_charge_id = obj.doctor_in_charge_id_4
            
                                // Radiology
                                this.form_FOURTH_radiology.soap_id = obj.id
                                this.form_FOURTH_radiology.doctor_in_charge_id = obj.doctor_in_charge_id_4
            
                                // PRESCRIPTION
                                this.form_FOURTH_prescription.soap_id = obj.id
                                this.form_FOURTH_prescription.doctor_in_charge_id = obj.doctor_in_charge_id_4

                                if (request_refresh === 'ALL') {
                                    this.getFOURTHSoapP(this.form_FOURTH_icd9cm.soap_id, this.form_FOURTH_icd9cm.doctor_in_charge_id)
                                    this.transaction_FOURTH_list = await this.getTransactionList(
                                        this.form_FOURTH_transaction.soap_id,
                                        this.form_FOURTH_transaction.doctor_in_charge_id
                                    )
                                    this.laboratory_FOURTH_list = await this.getLaboratoryList(
                                        this.form_FOURTH_laboratory.soap_id, 
                                        this.form_FOURTH_laboratory.doctor_in_charge_id
                                    )
                                    this.radiology_FOURTH_list = await this.getRadiologyList(
                                        this.form_FOURTH_radiology.soap_id, 
                                        this.form_FOURTH_radiology.doctor_in_charge_id
                                    )
                                    this.prescription_FOURTH_list = await this.getPrescriptionList(
                                        this.form_FOURTH_prescription.soap_id, 
                                        this.form_FOURTH_prescription.doctor_in_charge_id
                                    )
                                    this.prescription_FOURTH_list.forEach(item => {
                                        item.form_prescription_parent.sort((a, b) => a.id - b.id)
                                        item.form_prescription.sort((a, b) => a.sort - b.sort)
                                    })
                                }
                            }
        
                            // ======================================== DOKTER SPESIALIS 5 ======================================== 
                            this.specialist.doctor_5 = { value: obj.doctor_in_charge_id_5, title: obj.doctor_in_charge_name_5 }
                            this.specialist.type_consultation_doctor_5 = { 
                                value: obj.type_consultation_doctor_5 || '', 
                                title: obj.type_consultation_doctor_5 ? this.getConsultationTypeName(obj.type_consultation_doctor_5) : ''
                            }
                            
                            if (obj.doctor_in_charge_id_5) {
                                this.specialist.disabled_doctor_5 = true
        
                                // soap p
                                this.form_FIFTH_icd9cm.soap_id = obj.id
                                this.form_FIFTH_icd9cm.doctor_in_charge_id = obj.doctor_in_charge_id_5
                                
                                // transaction doctor
                                this.form_FIFTH_transaction.soap_id = obj.id
                                this.form_FIFTH_transaction.doctor_in_charge_id = obj.doctor_in_charge_id_5
            
                                // laboratory
                                this.form_FIFTH_laboratory.soap_id = obj.id
                                this.form_FIFTH_laboratory.doctor_in_charge_id = obj.doctor_in_charge_id_5
            
                                // Radiology
                                this.form_FIFTH_radiology.soap_id = obj.id
                                this.form_FIFTH_radiology.doctor_in_charge_id = obj.doctor_in_charge_id_5
            
                                // PRESCRIPTION
                                this.form_FIFTH_prescription.soap_id = obj.id
                                this.form_FIFTH_prescription.doctor_in_charge_id = obj.doctor_in_charge_id_5

                                if (request_refresh === 'ALL') {
                                    this.getFIFTHSoapP(this.form_FIFTH_icd9cm.soap_id, this.form_FIFTH_icd9cm.doctor_in_charge_id)
                                    this.transaction_FIFTH_list = await this.getTransactionList(
                                        this.form_FIFTH_transaction.soap_id,
                                        this.form_FIFTH_transaction.doctor_in_charge_id
                                    )
                                    this.laboratory_FIFTH_list = await this.getLaboratoryList(
                                        this.form_FIFTH_laboratory.soap_id, 
                                        this.form_FIFTH_laboratory.doctor_in_charge_id
                                    )
                                    this.radiology_FIFTH_list = await this.getRadiologyList(
                                        this.form_FIFTH_radiology.soap_id, 
                                        this.form_FIFTH_radiology.doctor_in_charge_id
                                    )
                                    this.prescription_FIFTH_list = await this.getPrescriptionList(
                                        this.form_FIFTH_prescription.soap_id, 
                                        this.form_FIFTH_prescription.doctor_in_charge_id
                                    )
                                    this.prescription_FIFTH_list.forEach(item => {
                                        item.form_prescription_parent.sort((a, b) => a.id - b.id)
                                        item.form_prescription.sort((a, b) => a.sort - b.sort)
                                    })
                                }
                            }
        
                        } else {
                            await this.$store.dispatch('nurse/getAllResolve', this.regData?.registration_destination_id)
                            .then(
                                async data => {
                                    let temp = data[0]
                                    if (temp) {
                                        this.form_igd.systolic_blood_pressure = temp.systolic_blood_pressure 
                                        this.form_igd.diastolic_blood_pressure = temp.diastolic_blood_pressure 
                                        this.form_igd.pulse_frequency = temp.pulse_frequency 
                                        this.form_igd.breath_frequency = temp.breath_frequency 
                                        this.form_igd.oximetry_frequency = temp.oximetry_frequency 
                                        this.form_igd.temperature = temp.temperature 
                                        this.form_igd.oxygen_type_id = temp.oxygen_type_id
                                        this.form_igd.aware = temp.aware
                                        this.form_igd.eye_score = temp.eye_score
                                        this.form_igd.verbal_score = temp.verbal_score
                                        this.form_igd.motor_score = temp.motor_score
                                        this.form_igd.skin_color = temp.skin_color
                                        this.form_igd.behavior = temp.behavior
                                        this.form_igd.weight = temp.weight
                                        this.form_igd.height = temp.height
                                        this.form_igd.imt = temp.imt
                                        this.form_igd.head_circumference = temp.head_circumference
                                        this.form_igd.chest_circumference = temp.chest_circumference
                                        this.form_igd.arm_circumference = temp.arm_circumference
                                        this.form_igd.stomach_circumference = temp.stomach_circumference
                                        this.form_igd.knee_circumference = temp.knee_circumference
                                    }
                                    await this.validationIGD('ALL')
                                }
                            )
                        }    
                    }
                )
            } catch (error) {
                console.error('Error at: ', error)
            } finally {
                this.isLoading = false
                this.isDisabledIGD = false
                this.disabledOphtal = false
            }
        },

        // general
        rowPrescStatus(item, role) {
            if (!item || role !== 'row') return ''
            if (item.flag == 0) return ''
            if (item.flag == 1) return 'table-warning-400'
        },

        rowLabStatus(item, role) {
            if (!item || role !== 'row') return ''
            if (item.status == 1) return ''
            if (item.status == 2 || item.status == 3) return 'table-warning-400'
        },

        rowRadStatus(item, role) {
            if (!item || role !== 'row') return ''
            if (item.status == 1) return ''
            if (item.status == 2 || item.status == 3) return 'table-warning-400'
        },

        hasItemsInCategory(items, categoryId) {
            if (items) {
                return items.some(item => item.prescription_item_type_id == categoryId);
            }
        },

        getParentVariant(parent_id){
            if (parent_id == 1) return 'bg-primary-100'
            if (parent_id == 2) return 'bg-success-100'
            if (parent_id == 3) return 'bg-orange-100'
            if (parent_id == 4) return 'bg-danger-100'
            if (parent_id == 5) return 'bg-pink-100'
            return 'bg-white'
        },

        getParentName(parent_id){
            if (parent_id == 1) return 'OBAT SATUAN'
            if (parent_id == 2) return 'RACIK 1'
            if (parent_id == 3) return 'RACIK 2'
            if (parent_id == 4) return 'RACIK 3'
            if (parent_id == 5) return 'RACIK 4'
            return '-'
        },
        
        async getTransactionList(soap_id, doctor_in_charge_id){
            try {
                const response = await this.$store.dispatch('soap/getTransaction', {
                    soap_id: soap_id,
                    doctor_in_charge_id: doctor_in_charge_id
                });
                return response?.length > 0 ? response : [];
            } catch (error) {
                console.error('Error fetching transactions:', error);
                return [];
            }
        },

        async getLaboratoryList(soap_id, doctor_in_charge_id) {
            try {
                const response = await this.$store.dispatch('laboratory/getOrderDetail', {
                    soap_id: soap_id,
                    doctor_in_charge_id: doctor_in_charge_id
                });
                return response?.length > 0 ? response : [];
            } catch (error) {
                console.error('Error fetching laboratory:', error);
                return [];
            }
        },

        async getRadiologyList(soap_id, doctor_in_charge_id) {
            try {
                const response = await this.$store.dispatch('radiology/getOrderDetail', {
                    soap_id: soap_id,
                    doctor_in_charge_id: doctor_in_charge_id
                });
                return response?.length > 0 ? response : [];
            } catch (error) {
                console.error('Error fetching radiology:', error);
                return [];
            }
        },

        async getPrescriptionList(soap_id, doctor_in_charge_id) {
            try {
                const response = await this.$store.dispatch('prescription/getDetail', {
                    soap_id: soap_id,
                    doctor_in_charge_id: doctor_in_charge_id
                });
                return response?.length > 0 ? response : [];
            } catch (error) {
                console.error('Error fetching prescription:', error);
                return [];
            }
        },

        changeSpecialistDoctor(selectedPos){
            const exist_doctor_id = this.form_igd[`doctor_in_charge_id_${selectedPos}`]
            const exist_consul_id = this.form_igd[`type_consultation_doctor_${selectedPos}`]

            const selected_doctor = this.specialist[`doctor_${selectedPos}`]
            const selected_consul = selectedPos != 1 ? this.specialist[`type_consultation_doctor_${selectedPos}`] : { value: '', title: '' }
            const is_not_empty = selectedPos != 1 ? selected_doctor?.value && selected_consul?.value : selected_doctor?.value

            if (is_not_empty) {
                const first_narrative = `Apakah anda yakin memilih ${selected_doctor.title} sebagai dokter spesialis ${selectedPos}`
                const second_narrative = `dengan jenis kosultasi ${selected_consul.title}`
                let confirmText = `${first_narrative} ${selectedPos != 1 ? second_narrative : ''}?`

                this.$bvModal
                    .msgBoxConfirm(confirmText, {
                    title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Ya, Saya Yakin', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
                })
                .then(async res => {
                    if (res === true) { // jadi ngubah
                        const is_same_doctor = selected_doctor.value == exist_doctor_id
                        const is_same_consul = selectedPos != 1 ? selected_consul.value == exist_consul_id : true

                        if (is_same_doctor && is_same_consul) return this.resetDoctorSelection(selectedPos) // kalo yang diisi sama aja
                        if (is_same_doctor && !is_same_consul) { // cuman mau ganti tipe konsultasi nya aja
                            this.form_igd[`type_consultation_doctor_${selectedPos}`] = selected_consul.value
                            return await this.validationIGD('MAIN')
                        }

                        if (selected_doctor.value == this.form_igd.doctor_in_charge_id) { // kalo dokter yg dipilih sama kyk dr.igd, seharusnya ga mungkin terjadi krn dr.igd selalu dr.umum
                            this.notification('Warning', 'CloseIcon', 'warning', `Dokter Spesialis ${selectedPos} Tidak Boleh Sama Dengan Dokter IGD`)
                            this.specialist[`doctor_${selectedPos}`] = ''
                            this.specialist[`disabled_doctor_${selectedPos}`] = false

                        // } else if (selected_doctor.value == this.form_igd[`doctor_in_charge_id_${selectedPos}`]) { // kl yang dipilih valuenya sama aja // ini di comment aja krn sama kyk kondisi is_same_doctor,
                        //     this.resetDoctorSelection(selectedPos)   

                        } else {
                            const positions = [1, 2, 3, 4, 5] // default nya kan ada 5 dokter spesialis
                            positions.splice(selectedPos-1, 1) // nge hapus nya pake index, kebetulan position nya berurutan
                            const matchedPos = positions.find(pos => this.form_igd[`doctor_in_charge_id_${pos}`] == selected_doctor.value) // karena selectedPos nya hilang dari positions, jadi ga perlu di bandingin

                            if (matchedPos) {
                                this.swapDoctorPositions(selectedPos, matchedPos, selected_doctor, selected_consul)
                            } else {
                                this.form_igd[`doctor_in_charge_id_${selectedPos}`] = selected_doctor.value
                                selectedPos != 1 ? this.form_igd[`type_consultation_doctor_${selectedPos}`] = selected_consul.value : false
                                await this.validationIGD('ALL')
                            }
                        }
                    } else { // ga jadi ngubah
                        this.resetDoctorSelection(selectedPos)
                    }
                })
            } else {
                let criteria = ['dokter spesialis']
                if (selectedPos != 1) criteria.push('konsultasi yang diminta')
                const message = `Silakan pilih ${criteria.join(' dan ')} terlebih dahulu`
                window.alert(message)
            }
        },

        swapDoctorPositions(fromPos, toPos, newDoctor, newConsul) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda yakin ingin menukar posisi ${newDoctor.title} sebagai dokter spesialis ${fromPos} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Setuju dan Lanjutkan', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async response => {
                if (response === true) {
                    if (toPos != 1 && fromPos != 1) {
                        this.form_igd[`type_consultation_doctor_${toPos}`] = this.form_igd[`type_consultation_doctor_${fromPos}`]
                    } else if (toPos != 1 && fromPos == 1) {
                        this.form_igd[`type_consultation_doctor_${toPos}`] = 1
                    }

                    if (fromPos != 1) this.form_igd[`type_consultation_doctor_${fromPos}`] = newConsul.value
                        
                    this.form_igd[`doctor_in_charge_id_${toPos}`] = this.form_igd[`doctor_in_charge_id_${fromPos}`]
                    this.form_igd[`doctor_in_charge_id_${fromPos}`] = newDoctor.value
                    await this.validationIGD('ALL')
                } else {
                    this.resetDoctorSelection(fromPos)
                }
            })
        },

        resetDoctorSelection(position) {
            const exist_doctor_id = this.form_igd[`doctor_in_charge_id_${position}`]
            const exist_doctor_name = this.form_igd[`doctor_in_charge_name_${position}`]
            const exist_consul_id = this.form_igd[`type_consultation_doctor_${position}`]

            if (exist_doctor_id) {
                this.specialist[`doctor_${position}`] = {
                    value: exist_doctor_id, 
                    title: exist_doctor_name 
                }
            } else { // else ini cuman handling barangkali propertinya ga ada, jadi valuenya undefined
                this.specialist[`disabled_doctor_${position}`] = false
                this.specialist[`doctor_${position}`] = ''
            }

            if (exist_consul_id && position != 1) {
                this.specialist[`type_consultation_doctor_${position}`] = {
                    value: exist_consul_id,
                    title: this.getConsultationTypeName(exist_consul_id)
                } 
            }

            this.specialist[`disabled_doctor_${position}`] = exist_doctor_id && exist_consul_id ? true : false
        },

        enableSpecialistDoctor(position, newVal){
            const exist_doctor_id = this.form_igd[`doctor_in_charge_id_${position}`]
            const exist_doctor_name = this.form_igd[`doctor_in_charge_name_${position}`]
            const exist_consul_id = this.form_igd[`type_consultation_doctor_${position}`]

            // kalo dari validation itu udah di set value newVal nya. sedangkan yg toggle ni ga di set, otomatis valuenya undefined
            this.specialist[`disabled_doctor_${position}`] = newVal ?? !this.specialist[`disabled_doctor_${position}`]
            
            if (this.specialist[`disabled_doctor_${position}`]) {
                this.specialist[`doctor_${position}`] = {
                    value: exist_doctor_id, 
                    title: exist_doctor_name,
                }
                this.specialist[`type_consultation_doctor_${position}`] = { 
                    value: exist_consul_id || '',
                    title: exist_consul_id ? this.getConsultationTypeName(exist_consul_id) : '',
                }
            }
        },

        getToggleVariant(position) {
            if (this.specialist[`disabled_doctor_${position}`] == true) return 'w3green'
            return 'warning'
        },

        getToggleIcon(position) {
            if (this.specialist[`disabled_doctor_${position}`] == true) return 'LockIcon'
            return 'UnlockIcon'
        },

        emptyChecking(obj) {
            this.combine_empty_found = []
            const naming = {
                package: "Kemasan/satuan",
                // package_id: "Kemasan/satuan",
                status_kardex: "Status",
                value: "Jumlah",
                value_package: "Jumlah",
                dose: "Dosis",
                dose_unit_id: "Satuan Dosis",
                signatura_times_id: "Waktu",
                administer_id: "Administer",
            };

            const parent_properties = ['status_kardex', 'value', 'package', 'signatura_times_id', 'administer_id']
            const item_unit_properties = ['status_kardex', 'value_package', 'dose', 'dose_unit_id', 'signatura_times_id', 'administer_id']
            const item_conc_properties = ['value_package', 'dose', 'dose_unit_id']

            let parent_criteria = []
            
            let parent_part = []
            let item_part = []

            let null_parents = []
            let null_items = []

            let data_items = obj.items || []
            let data_parents = []
            parent_criteria = [...new Set(data_items.map(item => item.prescription_type_id))] // buat dapetin parent mana aja yang punya items
            let compounded_medicine = obj.parents.filter(item => item.id != 1) // ambil yang racikan aja, krn parent obat satuan ga ada inputan dosis dll

            // tambahin `criteria` buat nentuin selanjutnya perlu di cek atau gak 
            for (let idx_c = 0; idx_c < compounded_medicine.length; idx_c++) {
                const el = compounded_medicine[idx_c];
                data_parents.push({
                    ...el,
                    criteria: parent_criteria.includes(el.id)
                })
            }
            
            if (data_parents) {
                for (let idx_p = 0; idx_p < data_parents.length; idx_p++) {
                    parent_part = []
                    const el = data_parents[idx_p]

                    if(el.criteria === true) {
                        for (let i = 0; i < parent_properties.length; i++) {
                            const pp = parent_properties[i]
                            const value = el[pp]
                            const isInvalid = pp === 'value'
                                ? !value || value <= 0 // null, undefined, '', atau <= 0
                                : !value || value == 0 // null, undefined, '', atau == 0
                            
                            if (isInvalid) {
                                parent_part.push(naming[pp])
                                // console.log('empty property found: ', pp, value);
                            }
                        }

                        // console.log('All empty property found: ', parent_part);
                        if (parent_part.length > 0) {
                            let narrative_parent = `${this.getPrescParentName(el.id)}: ${parent_part.join(', ')}`
                            null_parents.push(narrative_parent)                   
                        }
                    }
                }
                // console.log('null_parents: ', null_parents);
            }

            if (data_items) {
                for (let idx_i = 0; idx_i < data_items.length; idx_i++) {
                    item_part = []
                    const el = data_items[idx_i]
                    let properties = el.prescription_type_id == 1 ? item_unit_properties : item_conc_properties

                    for (let i = 0; i < properties.length; i++) {
                        const ip = properties[i]
                        const value = el[ip]
                        const isInvalid = ip === 'dose' || ip === 'value_package'
                            ? !value || value <= 0 // null, undefined, '', atau <= 0
                            : !value || value == 0 // null, undefined, '', atau == 0
                        
                        if (isInvalid) {
                            item_part.push(naming[ip])
                            // console.log('empty property found: ', ip, value);
                        }
                    }
                    
                    // console.log('All empty property found: ', parent_part);
                    if (item_part.length > 0) {
                        let narrative_item = `${el.name}: ${item_part.join(', ')}`
                        null_items.push(narrative_item)                   
                    }
                }
                // console.log('null_items: ', null_items);
            }
            
            this.combine_empty_found = [...null_parents, ...null_items]
            return this.combine_empty_found.length > 0 ? true : false
        },

        async getSignaturaUnits(package_id) {
            this.optItemSignUnits = []
            let param = {
                package_id: package_id
            }
            try {                
                await this.$store.dispatch('kardex/getOptUnit', param)
                .then(
                    async response => {
                        this.optItemSignUnits = response
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('Error GET kardex/getOptUnit at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET kardex/getOptUnit at: ', error);
            }
        },

        updateAdminister(index, value, position, key) {
            const arrayPos = `form_${position}_prescription`
            const arrayKey = key != null ? `form_prescription_${key}` : 'form_prescription'
            const targetArray = this[arrayPos][arrayKey]
            if(value != this.other_administer) targetArray[index].desc = ''
        },

        //  =========== DOKTER IGD
        
        async validationIGD(forward_refresh) {
            for (let i = 0; i < 5; i++) {
                this.enableSpecialistDoctor(i+1, this.form_igd[`doctor_in_charge_id_${i+1}`] ? true : false)
            }

            this.form_igd.registration_destination_id ||= this.regData.registration_destination_id
            this.form_igd.doctor_in_charge_id ||= this.regData.doctor_in_charge_id
            this.form_igd.therapy_plan = this.form_icd9cm.therapy_plan
            
            try {
                this.isDisabledIGD = true
                await this.$store.dispatch('soap/formPostPutDoang', this.form_igd)
                .then(
                    async response => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        await this.handlingIGD(this.regData?.registration_destination_id, forward_refresh)
                        this.getAssessment()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                console.error('Error catch at: ', error)
            } finally {
                this.isDisabledIGD = false
            }
        },

        validationOphtal() {
            try {
                this.disabledOphtal = true
                this.$store.dispatch('soap/putOphtal', { ophtal: this.ophInput, soap_id: this.soap_id })
                .then(
                    async res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.handlingIGD(this.regData?.registration_destination_id, 'MAIN')
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })                
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                this.disabledOphtal = false
                console.error('Error catch at: ', error);
            // } finally {
            //     this.disabledOphtal = false
            }
        },

        onIGDSearchIcd10(search, loading) {
            if(search.length >= 3) {
                loading(true);
                this.$store.dispatch('icd10/getParam', search).then(data => {
                    this.OptionsIGDIcd10 = []
                    for (let i = 0; i < data.length; i++) {
                        const el = data[i];
                        let temp = {
                            value: el.id,
                            title: `${el.code} ${el.name}`,
                        }
                        this.OptionsIGDIcd10.push(Object.assign({}, temp))
                    }
                    loading(false)
                })
            }
        },
        
        onIGDAdd10 (event) {
            this.IGDicd10_id = ''

            if (this.form_igd.icd10.some(item => item.icd10_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD10 yang sama')
            } else {
                this.form_igd.icd10.push({ icd10_id:event.value, title:event.title, value:event.value, action: 'action'})
            }
        },

        deleteIGD10 (index) {
            this.form_igd.icd10.splice(index, 1)
        },

        addOther10() {
            let input = ''
            if (this.input_other10) {
                input = this.input_other10
                this.form_igd.non_icd10.push(input)
                this.input_other10 = ''
            }
        },

        removeOther10 (index) {
            this.form_igd.non_icd10.splice(index, 1)
        },

        async resetIGD() {
            this.form_igd.anamesa_type_id = 1 
            this.form_igd.family_relation_id = ''
            this.form_igd.family_name = ''
            this.form_igd.main_complaint = ''
            this.form_igd.history_disease_now = ''
            this.form_igd.history_disease_past = ''
            this.form_igd.history_drugs = ''
            this.form_igd.systolic_blood_pressure = ''
            this.form_igd.diastolic_blood_pressure = ''
            this.form_igd.pulse_frequency = ''
            this.form_igd.breath_frequency = ''
            this.form_igd.oximetry_frequency = ''
            this.form_igd.temperature = ''
            this.form_igd.oxygen_type_id = ''
            this.form_igd.aware = ''
            this.form_igd.eye_score = ''
            this.form_igd.verbal_score = ''
            this.form_igd.motor_score = ''
            this.form_igd.skin_color = ''
            this.form_igd.behavior = ''

            this.form_igd.weight = ''
            this.form_igd.height = ''
            this.form_igd.imt = ''
            this.form_igd.head_circumference = ''
            this.form_igd.chest_circumference = ''
            this.form_igd.arm_circumference = ''
            this.form_igd.stomach_circumference = ''
            this.form_igd.knee_circumference = ''
            this.form_igd.icd10 = []
            this.form_igd.icd10_notes = ''
            this.form_igd.non_icd10 = []

            this.form_igd.head = 1
            this.form_igd.head_hair_color = 1
            this.form_igd.head_hair_condition = 1
            this.form_igd.eye_conjunctive = 1
            this.form_igd.eye_sclera = 1
            this.form_igd.eye_pupil = 1
            this.form_igd.eye_light_reflex = 1
            this.form_igd.mouth_mucosa = 1
            this.form_igd.mouth_discoloration = 1
            this.form_igd.mouth_tongue = 1
            this.form_igd.mouth_caries = 1
            this.form_igd.neck_kgb = 1
            this.form_igd.neck_trachea_position = 1
            this.form_igd.neck_massa = 1
            this.form_igd.thorax_insp_chest = 1
            this.form_igd.thorax_insp_retraction = 1
            this.form_igd.thorax_palp_chest = 1
            this.form_igd.thorax_palp_fremitus = 1
            this.form_igd.thorax_palp_crepitation = 1
            this.form_igd.thorax_ausk_pul_vasiculer = 1
            this.form_igd.thorax_ausk_pul_rhonki = 1
            this.form_igd.thorax_ausk_pul_wheezing = 1
            this.form_igd.thorax_ausk_cor_heart_sound = 1
            this.form_igd.thorax_ausk_cor_murmur = 1
            this.form_igd.thorax_ausk_cor_gallop = 1
            this.form_igd.abdomen_supel = 1
            this.form_igd.abdomen_bowel_sounds = 1
            this.form_igd.abdomen_tenderness = 1
            this.form_igd.abdomen_hepar_lien = 1
            this.form_igd.abdomen_other_physical_check = ''
            this.form_igd.extremity_acral = 1
            this.form_igd.extremity_crt = 1
            this.form_igd.extremity_edema = 1
            this.form_igd.genitalia = ''
            this.form_igd.localist_status = ''
            this.form_igd.neurologist_status = ''   
            this.form_igd.ophthal_status = ''
        },

        // ======================================================= IGD-DOCTOR

        async onIGDSearchIcd9cm(search, loading) {
            if(search.length >= 2) {
                loading(true);
                await this.$store.dispatch('icd9cm/getParam', search).then(data => {
                    this.OptionsIGDIcd9cm = []
                    for (let i = 0; i < data.length; i++) {
                        const el = data[i];
                        let temp = {
                            value: el.id,
                            title: `${el.code} ${el.name}`,
                        }
                        this.OptionsIGDIcd9cm.push(Object.assign({}, temp))
                    }
                    loading(false)
                })
            }
        },

        onIGDAdd9cm (event) {
            this.IGDicd9cm_id = ''
            if (this.form_icd9cm.icd9cm.some(item => item.icd9cm_id == event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD9 yang sama')
            } else {
                this.form_icd9cm.icd9cm.push({ 
                    icd9cm_id: event.value, 
                    title: event.title, 
                    value: event.value, 
                    action: 'action'
                })
            }
        },

        removeIGD9cm (index) {
            this.form_icd9cm.icd9cm.splice(index, 1)
        },

        async resetIGDSoapP() {
            this.form_icd9cm.id = ''
            this.form_icd9cm.therapy_plan = ''
            this.form_icd9cm.icd9cm = []
        },

        async validationIGDSoapP(){
            try {
                this.IGDdisabled.soap_p = true
                await this.$store.dispatch('soap/formPostPutSoapP', this.form_icd9cm)
                .then(
                    async res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        await this.getIGDSoapP(this.form_icd9cm.soap_id, this.form_icd9cm.doctor_in_charge_id)
                        this.getAssessment()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })                
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error);
            } finally {
                this.IGDdisabled.soap_p = false                    
            }
        },

        async getIGDSoapP(soap_id, doctor_in_charge_id){
            await this.resetIGDSoapP()
            try {
                await this.$store.dispatch('soap/getSoapP', {soap_id: soap_id, doctor_in_charge_id: doctor_in_charge_id })
                .then(
                    response => {
                        if (response?.length > 0) {
                            const data = response[0]
                            if (data.icd9cm) {
                                const mappedIcd9cm = data.icd9cm.map(item => {
                                    return {
                                        icd9cm_id: item.id,
                                        value: item.id,
                                        title: `${item.code} ${item.name}`, 
                                        action: "action"
                                    };
                                });                
                                this.form_icd9cm.icd9cm = mappedIcd9cm;
                            }
                            this.form_icd9cm.id = data.id
                            this.form_icd9cm.therapy_plan = data.therapy_plan
                            this.form_IGD_prescription.soap_p_id = data.id
                        }
                    }
                )
            } catch (error) {
                console.error('Error at: ', error)
            }
        },

        async resetIGDProcedure() {
            this.form_transaction.id = ''
            this.form_transaction.costing_id = ''
            this.form_transaction.var_1 = ''
            this.form_transaction.var_2 = 1
            this.form_transaction.var_3 = ''
            this.form_transaction.notes = ''
        },

        async openIGDProcedure() {
            await this.resetIGDProcedure()
            this.active_act.modal_procedure = true
            this.form_transaction.soap_id ||= this.form_igd.id
            this.form_transaction.doctor_in_charge_id ||= this.form_igd.doctor_in_charge_id
        },

        async onEditIGDProcedure(obj) {
            await this.openIGDProcedure()

            this.form_transaction.id = obj?.id
            this.form_transaction.costing_id = {
                value: obj?.costing_id,
                title: obj?.costing_name,
                costing: obj?.var_1
            }
            this.form_transaction.notes = obj?.notes
            this.form_transaction.var_1 = obj?.var_1
            this.form_transaction.var_2 = obj?.var_2
            this.form_transaction.var_3 = obj?.var_3
        },

        async validationIGDProcedure () {
            const quantity = Number(this.form_transaction?.var_2 || 0)
            const price = Number(this.form_transaction?.costing_id?.costing || 0)

            const formData = {
                id: this.form_transaction?.id,
                costing_id: this.form_transaction?.costing_id?.value || '',
                soap_id: this.form_transaction?.soap_id,
                notes: this.form_transaction?.notes,
                var_1: price,
                var_2: quantity,
                var_3: price*quantity,
                doctor_in_charge_id: this.form_transaction?.doctor_in_charge_id
            }

            if (Number.isInteger(quantity) && quantity > 0 && formData.costing_id != '' && formData.costing_id != null) {
                try {
                    this.IGDdisabled.transaction = true
                    await this.$store.dispatch('soap/postPutTransaction', formData)
                    .then(
                        async res => {
                            this.active_act.modal_procedure = false
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.transaction_list = await this.getTransactionList(
                                this.form_transaction.soap_id,
                                this.form_transaction.doctor_in_charge_id
                            );
                            await this.resetIGDProcedure()
                            this.getAssessment()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    console.error('Error catch at: ', error) 
                } finally {
                    this.IGDdisabled.transaction = false
                }
            } else {
                this.notification('Warning','CloseIcon','warning','Tindakan prosedur wajib diisi & jumlah minimal 1')
            }
        },

        removeIGDProcedure(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus prosedur ${obj.costing_name} dari ${this.form_igd.doctor_in_charge_name}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then( async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('soap/deleteTransaction', obj.id)
                        .then(
                            async res => {
                                this.transaction_list = await this.getTransactionList(
                                    this.form_transaction.soap_id,
                                    this.form_transaction.doctor_in_charge_id
                                );
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getAssessment()
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        async resetIGDLab(){
            this.form_laboratory_igd.id = ''
            this.form_laboratory_igd.date_request = moment(new Date()).format('YYYY-MM-DD HH:mm')
            this.form_laboratory_igd.laboratory_request_items = []
        },

        async openIGDLab(){
            await this.resetIGDLab()
            this.active_act.modal_lab_igd = true
            this.form_laboratory_igd.soap_id ||= this.form_igd.id
            this.form_laboratory_igd.doctor_in_charge_id ||= this.form_igd.doctor_in_charge_id
        },

        async onEditIGDLab(obj) {
            await this.openIGDLab()
            this.form_laboratory_igd.id = obj?.id
            this.form_laboratory_igd.date_request = obj?.date_request

            if (obj?.laboratory_request_item) {
                const mappedLab = obj.laboratory_request_item.map(item => {
                    return {
                        costing_name: item.costing_name,
                        laboratory_package_id: item.laboratory_package_id,
                        service_class_id: item.service_class_id
                    };
                });                
                this.form_laboratory_igd.laboratory_request_items = mappedLab;
            } else {
                this.form_laboratory_igd.laboratory_request_items = []
            }
        },

        async validationIGDLab(){
            try {
                this.IGDdisabled.laboratory = true
                await this.$store.dispatch('laboratory/formPostPut', this.form_laboratory_igd)
                .then(
                    async response => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active_act.modal_lab_igd = false
                        this.laboratory_igd_list = await this.getLaboratoryList(
                            this.form_laboratory_igd.soap_id, 
                            this.form_laboratory_igd.doctor_in_charge_id
                        )
                        await this.resetIGDLab()
                        this.getAssessment()
                        // this.socket.emit('SEND_LAB', []);
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error);
            } finally {
                this.IGDdisabled.laboratory = false
            }
        },

        removeIGDLab(obj){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.no_request} dari ${this.form_igd.doctor_in_charge_name}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('laboratory/removeRequest', obj.id)
                        .then(
                            async res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.laboratory_igd_list = await this.getLaboratoryList(
                                    this.form_laboratory_igd.soap_id, 
                                    this.form_laboratory_igd.doctor_in_charge_id
                                )
                                this.getAssessment()
                                // this.socket.emit('SEND_LAB', []);
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },
        
        async resetIGDRad(){
            this.form_radiology_igd.id = ''
            this.form_radiology_igd.date_request = moment(new Date()).format('YYYY-MM-DD HH:mm')
            this.form_radiology_igd.radiology_request_items = []
            this.form_radiology_igd.clinical_indications = ''
        },

        async openIGDRad(){
            await this.resetIGDRad()
            this.active_act.modal_rad_igd = true
            this.form_radiology_igd.soap_id ||= this.form_igd.id
            this.form_radiology_igd.doctor_in_charge_id ||= this.form_igd.doctor_in_charge_id
        },

        async onEditIGDRad(obj) {
            await this.openIGDRad()
            this.form_radiology_igd.id = obj.id
            this.form_radiology_igd.date_request = obj.date_request

            this.form_radiology_igd.clinical_indications = obj.clinical_indications || ''

            if (obj?.radiology_request_item) {
                const mappedRad = obj.radiology_request_item.map(item => {
                    return {
                        costing_name: item.costing_name,
                        radiology_package_id: item.radiology_package_id,
                        service_class_id: item.service_class_id
                    };
                });                
                this.form_radiology_igd.radiology_request_items = mappedRad;
            } else {
                this.form_radiology_igd.radiology_request_items = []
            }
        },

        async validationIGDRad(){
            try {
                this.IGDdisabled.radiology = true
                await this.$store.dispatch('radiology/formPostPut', this.form_radiology_igd)
                .then(
                    async response => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active_act.modal_rad_igd = false
                        this.radiology_igd_list = await this.getRadiologyList(
                            this.form_radiology_igd.soap_id, 
                            this.form_radiology_igd.doctor_in_charge_id
                        )
                        await this.resetIGDRad()
                        this.getAssessment()
                        // this.socket.emit('SEND_LAB', []);
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error);
            } finally {
                this.IGDdisabled.radiology = false
            }
        },

        removeIGDRad(obj){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.no_request} dari ${this.form_igd.doctor_in_charge_name}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('radiology/removeRequest', obj.id)
                        .then(
                            async res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.radiology_igd_list = await this.getRadiologyList(
                                    this.form_radiology_igd.soap_id, 
                                    this.form_radiology_igd.doctor_in_charge_id
                                )
                                this.getAssessment()
                                // this.socket.emit('SEND_RAD', []);                                
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        openIGDPresc(){
            this.resetIGDPresc()
            this.active_act.modal_presc_igd = true
            if (this.form_IGD_prescription.soap_p_id == '') {
                this.validationIGDSoapP()
            }
        },

        resetIGDPresc(){
            this.form_IGD_prescription.id = ''
            this.form_IGD_prescription.form_prescription_parent = []
            this.form_IGD_prescription.form_prescription = []
            this.IGDParentStock = ''
            this.IGDCatalog = ''
        },

        openIGDPreParent(event) {
            if (event == null) return

            let index = this.form_IGD_prescription.form_prescription_parent.findIndex((item) => item.id == event.value);
            if (index !== -1) return;
            
            // kl bukan obat satuan, alias racik
            if (event.value != 1) {
                this.temp_IGD_parent.parent_id = event.value
                this.temp_IGD_parent.parent_name = event.title
                this.temp_IGD_parent.value = ''
                this.temp_IGD_parent.signatura_time = ''
                this.temp_IGD_parent.administer = ''
                this.temp_IGD_parent.desc = ''
                this.temp_IGD_parent.package = ''
                this.active.modal.presc_IGD_parent = true
                return
            }

            // obat satuan
            this.form_IGD_prescription.form_prescription_parent.push({
                id: event.value,
            })
        },

        closeIGDPreParent() {
            this.active.modal.presc_IGD_parent = false
            this.IGDParentStock = ''
            this.IGDCatalog = ''
        },

        async addIGDParent() {
            try {
                this.IGDdisabled.preParent = true
                this.form_IGD_prescription.form_prescription_parent.push(
                    {
                        id: this.temp_IGD_parent.parent_id,
                        status_kardex: 3,
                        value: this.temp_IGD_parent.value,
                        signatura_times_id: this.temp_IGD_parent.signatura_time,
                        administer_id: this.temp_IGD_parent.administer,
                        desc: this.temp_IGD_parent.desc,
                        package: this.temp_IGD_parent.package
                    }
                ) 
                this.active.modal.presc_IGD_parent = false
            } catch (error) {
                console.error('Error at: ', error)
            } finally {
                setTimeout(() => {
                    this.IGDdisabled.preParent = false
                }, 1000);
            }
        },
        
        onSearchIGDCatalog(search, loading) {
            if (search.length >= 3) {
                loading(true)
                const searchTerm = search.toLowerCase();
                this.optIGDCatalog = this.catalog_list.filter(item => {
                    return item?.data_catalog?.composition.toLowerCase().includes(searchTerm) ||
                    item?.title.toLowerCase().includes(searchTerm)
                });
                loading(false)
            }
        },

        async openIGDPreItem(event) {
            this.IGDCatalog = ''
            if (this.IGDParentStock == '' || this.IGDParentStock == null) return alert("Silakan Pilih Sediaan Terlebih Dahulu")

            this.temp_IGD_items.parent_id = this.IGDParentStock?.value
            this.temp_IGD_items.parent_name = this.IGDParentStock?.title
            this.temp_IGD_items.catalog_name = event?.data_catalog?.name
            this.temp_IGD_items.data_catalog = event?.data_catalog
            this.temp_IGD_items.signatura_dose = event?.data_catalog?.signatura_dose
            this.temp_IGD_items.signatura_unit = event?.data_catalog?.signatura_unit_id
            this.temp_IGD_items.signatura_time = event?.data_catalog?.signatura_times_id
            this.temp_IGD_items.administer = event?.data_catalog?.instruction_id || ''
            this.temp_IGD_items.desc = ''
            await this.getSignaturaUnits(event?.data_catalog?.package_id)

            this.active.modal.presc_IGD_items = true
        },

        async addIGDPrescItem() {
            try {
                this.IGDdisabled.preItem = true
                this.form_IGD_prescription.form_prescription.push({ 
                    pharmacy_catalog_id: this.temp_IGD_items?.data_catalog?.id, 
                    package_id: this.temp_IGD_items?.data_catalog?.package_id,
                    prescription_type_id: this.temp_IGD_items.parent_id,
                    value_package: 1,
                    dose: this.temp_IGD_items.signatura_dose,
                    dose_unit_id: this.temp_IGD_items.signatura_unit,
                    signatura_times_id: this.temp_IGD_items.signatura_time,
                    administer_id: this.temp_IGD_items.administer,
                    desc: this.temp_IGD_items.desc,
                    sort: this.form_IGD_prescription.form_prescription.length, 
                    status_kardex: this.temp_IGD_items.parent_id == 1 ? 1 : null,

                    name: this.temp_IGD_items?.data_catalog?.name,
                    dose_unit_options: this.optItemSignUnits                
                })
                this.active.modal.presc_IGD_items = false
            } catch (error) {
                console.error('Error at: ', error)
            } finally {
                setTimeout(() => {
                    this.IGDdisabled.preItem = false
                }, 1000);
            }
        },
        
        deletePresc (index) {
            this.form_IGD_prescription.form_prescription.splice(index, 1)
        },

        async validationIGDPresc() {
            try {
                this.IGDdisabled.prescription = true 
                this.form_IGD_prescription.pharmacy_depo_id ||= this.depo_emergency
                
                let inp_items = this.form_IGD_prescription?.form_prescription 
                let inp_parent = this.form_IGD_prescription?.form_prescription_parent 

                if (inp_parent?.length > 0 && inp_items?.length > 0) {
                    if(this.emptyChecking({
                        parents: inp_parent,
                        items: inp_items
                    })) {
                        const message = "SILAKAN LENGKAPI:\n\n" + this.combine_empty_found.map(item => `- ${item}`).join('\n');
                        alert(message);
                    } else {
                        await this.$store.dispatch('prescription/formPostPut', this.form_IGD_prescription)
                        .then(
                            async res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.prescription_igd_list = await this.getPrescriptionList(
                                    this.form_IGD_prescription.soap_id, 
                                    this.form_IGD_prescription.doctor_in_charge_id
                                )
                                this.active_act.modal_presc_igd = false
                                this.form_IGD_prescription.form_prescription_parent = []
                                this.form_IGD_prescription.form_prescription = []
                                this.IGDParentStock = ''
                                this.IGDCatalog = ''

                                this.getAssessment()
                                // this.socket.emit('SEND_PRS', []);
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    }
                }  
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.IGDdisabled.prescription = false
            }
        },
        
        editIGDPresc(obj) {
            // console.log('obj: ', obj)
            this.resetIGDPresc()
            this.active_act.modal_presc_igd = true

            this.form_IGD_prescription.id = obj.id
            this.form_IGD_prescription.soap_id = this.soap_id
            this.form_IGD_prescription.pharmacy_depo_id = obj.pharmacy_depo_id
            this.form_IGD_prescription.soap_p_id = obj.soap_p_id
            this.form_IGD_prescription.registration_destination_id = obj.registration_destination_id
            this.form_IGD_prescription.doctor_in_charge_id = obj.doctor_in_charge_id

            let var_parent = obj?.form_prescription_parent || []
            let var_presc = obj?.form_prescription || []

            if (var_parent.length > 0) {
                const mappedPrescParent = var_parent.map(item => {
                    if (item.id != 1) {
                        return {
                            id: item.id,
                            status_kardex: item.status_kardex ?? 3,                            
                            value: Number(item.value ?? 0),
                            package: Number(item.package),
                            signatura_times_id: item.signatura_times_id ?? '',
                            administer_id: item.administer_id ?? '',
                            desc: item.administer_id == this.other_administer ? item?.desc : '',
                        };
                    } else {
                        return {
                            id: item.id,
                        };
                    }

                });
                mappedPrescParent.sort((a, b) => a.id - b.id);                
                this.form_IGD_prescription.form_prescription_parent = mappedPrescParent;
            } else {
                this.form_IGD_prescription.form_prescription_parent = [{
                    id: 1,
                }]
            }

            if (var_presc.length > 0) {
                const mappedPresc = var_presc.map(item => {
                    let temp_sign = []
                    if (item?.dose_unit_options.length > 0) {
                        const mappedSign = item.dose_unit_options.map(item => {
                            return {
                                value: item.id,
                                title: item.name, 
                            };
                        });                
                        temp_sign = mappedSign
                    }

                    return {
                        pharmacy_catalog_id: item.pharmacy_catalog_id, 
                        package_id: item.package_id,
                        prescription_type_id: item.prescription_item_type_id,
                        value_package: Number(item.qty),
                        dose: item.dose,
                        dose_unit_id: item.dose_unit_id,
                        signatura_times_id: item.signatura_times_id,
                        administer_id: item.administer_id,
                        desc: item.administer_id == this.other_administer ? item?.desc : '',
                        sort: item.sort, 
                        status_kardex: item.status_kardex ?? '',

                        name: item.pharmacy_catalog_name,
                        dose_unit_options: temp_sign,
                    };
                });
                mappedPresc.sort((a, b) => a.sort - b.sort);                
                this.form_IGD_prescription.form_prescription = mappedPresc;                                  
            } else {
                this.form_IGD_prescription.form_prescription = [];                                  
            }
        },

        removeIGDPresc(obj){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus order resep ${obj.no_order} dari ${this.form_igd.doctor_in_charge_name}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('prescription/removeOrder', obj.id)
                        .then(
                            async res => {
                                this.prescription_igd_list = await this.getPrescriptionList(
                                    this.form_IGD_prescription.soap_id, 
                                    this.form_IGD_prescription.doctor_in_charge_id
                                )
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getAssessment()     
                                // this.socket.emit('SEND_PRS', []);
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },
                
        // SPECIALIST-DOCTOR 1
        async onFIRSTSearchIcd9cm(search, loading) {
            if(search.length >= 2) {
                loading(true);
                await this.$store.dispatch('icd9cm/getParam', search).then(data => {
                    this.OptionsFIRSTIcd9cm = []
                    for (let i = 0; i < data.length; i++) {
                        const el = data[i];
                        let temp = {
                            value: el.id,
                            title: `${el.code} ${el.name}`,
                        }
                        this.OptionsFIRSTIcd9cm.push(Object.assign({}, temp))
                    }
                    loading(false)
                })
            }
        },
        onFIRSTAdd9cm (event) {
            this.FIRSTicd9cm_id = ''
            if (this.form_FIRST_icd9cm.icd9cm.some(item => item.icd9cm_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD9 yang sama')
            } else {
                this.form_FIRST_icd9cm.icd9cm.push({ icd9cm_id:event.value, title:event.title, value:event.value, action: 'action'})
            }
        },
        deleteFIRST9cm (index) {
            this.form_FIRST_icd9cm.icd9cm.splice(index, 1)
        },

        async resetFIRSTSoapP() {
            this.form_FIRST_icd9cm.id = ''
            this.form_FIRST_icd9cm.therapy_plan = ''
            this.form_FIRST_icd9cm.icd9cm = []
        },

        async validationFIRSTSoapP(){
            try {
                this.FIRSTdisabled.soap_p = true
                await this.$store.dispatch('soap/formPostPutSoapP', this.form_FIRST_icd9cm)
                .then(
                    async res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        await this.getFIRSTSoapP(this.form_FIRST_icd9cm.soap_id, this.form_FIRST_icd9cm.doctor_in_charge_id)
                        this.getAssessment()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })                
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error);
            } finally {
                this.FIRSTdisabled.soap_p = false                    
            }
        },

        async getFIRSTSoapP(soap_id, doctor_in_charge_id){
            await this.resetFIRSTSoapP()
            try {
                await this.$store.dispatch('soap/getSoapP', {soap_id: soap_id, doctor_in_charge_id: doctor_in_charge_id })
                .then(
                    response => {
                        if (response.length != 0) {
                            const data = response[0]
                            if (data.icd9cm) {
                                const mappedIcd9cm = data.icd9cm.map(item => {
                                    return {
                                        icd9cm_id: item.id,
                                        value: item.id,
                                        title: `${item.code} ${item.name}`, 
                                        action: "action"
                                    };
                                });                
                                this.form_FIRST_icd9cm.icd9cm = mappedIcd9cm;
                            }
                            this.form_FIRST_icd9cm.id = data.id
                            this.form_FIRST_icd9cm.therapy_plan = data.therapy_plan
                            this.form_FIRST_prescription.soap_p_id = data.id
                        }
                    }
                )
            } catch (error) {
                console.error('Error at: ', error)
            }            
        },

        async resetFIRSTProcedure() {
            this.form_FIRST_transaction.id = ''
            this.form_FIRST_transaction.costing_id = ''
            this.form_FIRST_transaction.var_1 = ''
            this.form_FIRST_transaction.var_2 = 1
            this.form_FIRST_transaction.var_3 = ''
            this.form_FIRST_transaction.notes = ''
        },

        async openFIRSTProcedure() {
            await this.resetFIRSTProcedure()
            this.active_FIRST.modal_procedure = true
            this.form_FIRST_transaction.soap_id ||= this.form_igd.id
            this.form_FIRST_transaction.doctor_in_charge_id ||= this.form_igd.doctor_in_charge_id
        },

        async onEditFIRSTProcedure(obj) {
            await this.openFIRSTProcedure()

            this.form_FIRST_transaction.id = obj?.id
            this.form_FIRST_transaction.costing_id = {
                value: obj?.costing_id,
                title: obj?.costing_name,
                costing: obj?.var_1
            }
            this.form_FIRST_transaction.notes = obj?.notes
            this.form_FIRST_transaction.var_1 = obj?.var_1
            this.form_FIRST_transaction.var_2 = obj?.var_2
            this.form_FIRST_transaction.var_3 = obj?.var_3
        },

        async validationFIRSTProcedure () {
            const quantity = Number(this.form_FIRST_transaction?.var_2 || 0)
            const price = Number(this.form_FIRST_transaction?.costing_id?.costing || 0)

            const formData = {
                id: this.form_FIRST_transaction?.id,
                costing_id: this.form_FIRST_transaction?.costing_id?.value || '',
                soap_id: this.form_FIRST_transaction?.soap_id,
                notes: this.form_FIRST_transaction?.notes,
                var_1: price,
                var_2: quantity,
                var_3: price*quantity,
                doctor_in_charge_id: this.form_FIRST_transaction?.doctor_in_charge_id
            }

            if (Number.isInteger(quantity) && quantity > 0 && formData.costing_id != '' && formData.costing_id != null) {
                try {
                    this.FIRSTdisabled.transaction = true
                    await this.$store.dispatch('soap/postPutTransaction', formData)
                    .then(
                        async res => {
                            this.active_FIRST.modal_procedure = false
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.transaction_FIRST_list = await this.getTransactionList(
                                this.form_FIRST_transaction.soap_id,
                                this.form_FIRST_transaction.doctor_in_charge_id
                            );
                            await this.resetFIRSTProcedure()
                            this.getAssessment()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    console.error('Error catch at: ', error) 
                } finally {
                    this.FIRSTdisabled.transaction = false
                }
            } else {
                this.notification('Warning','CloseIcon','warning','Tindakan prosedur wajib diisi & jumlah minimal 1')
            }
        },

        removeFIRSTProcedure(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus prosedur ${obj.costing_name} dari ${this.form_igd.doctor_in_charge_name_1}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then( async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('soap/deleteTransaction', obj.id)
                        .then(
                            async res => {
                                this.transaction_FIRST_list = await this.getTransactionList(
                                    this.form_FIRST_transaction.soap_id,
                                    this.form_FIRST_transaction.doctor_in_charge_id
                                );
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getAssessment()
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        async resetFIRSTLab(){
            this.form_FIRST_laboratory.id = ''
            this.form_FIRST_laboratory.date_request = moment(new Date()).format('YYYY-MM-DD HH:mm')
            this.form_FIRST_laboratory.laboratory_request_items = []
        },

        async openFIRSTLab(){
            await this.resetFIRSTLab()
            this.active_FIRST.modal_lab = true
            this.form_FIRST_laboratory.soap_id ||= this.form_igd.id
            this.form_FIRST_laboratory.doctor_in_charge_id ||= this.form_igd.doctor_in_charge_id
        },

        async onEditFIRSTLab(obj) {
            await this.openFIRSTLab()
            this.form_FIRST_laboratory.id = obj?.id
            this.form_FIRST_laboratory.date_request = obj?.date_request

            if (obj?.laboratory_request_item) {
                const mappedLab = obj.laboratory_request_item.map(item => {
                    return {
                        costing_name: item.costing_name,
                        laboratory_package_id: item.laboratory_package_id,
                        service_class_id: item.service_class_id
                    };
                });                
                this.form_FIRST_laboratory.laboratory_request_items = mappedLab;
            } else {
                this.form_FIRST_laboratory.laboratory_request_items = []
            }
        },

        async validationFIRSTLab(){
            try {
                this.FIRSTdisabled.laboratory = true
                await this.$store.dispatch('laboratory/formPostPut', this.form_FIRST_laboratory)
                .then(
                    async response => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active_FIRST.modal_lab = false
                        this.laboratory_FIRST_list = await this.getLaboratoryList(
                            this.form_FIRST_laboratory.soap_id, 
                            this.form_FIRST_laboratory.doctor_in_charge_id
                        ) 
                        await this.resetFIRSTLab()
                        this.getAssessment()
                        // this.socket.emit('SEND_LAB', []);
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error);
            } finally {
                this.FIRSTdisabled.laboratory = false
            }
        },

        removeFIRSTLab(obj){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.no_request} dari ${this.form_igd.doctor_in_charge_name_1}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('laboratory/removeRequest', obj.id)
                        .then(
                            async res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.laboratory_FIRST_list = await this.getLaboratoryList(
                                    this.form_FIRST_laboratory.soap_id, 
                                    this.form_FIRST_laboratory.doctor_in_charge_id
                                ) 
                                this.getAssessment()
                                // this.socket.emit('SEND_LAB', []);
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        async resetFIRSTRad(){
            this.form_FIRST_radiology.id = ''
            this.form_FIRST_radiology.date_request = moment(new Date()).format('YYYY-MM-DD HH:mm')
            this.form_FIRST_radiology.radiology_request_items = []
            this.form_FIRST_radiology.clinical_indications = ''
        },

        async openFIRSTRad(){
            await this.resetFIRSTRad()
            this.active_FIRST.modal_rad = true
            this.form_FIRST_radiology.soap_id ||= this.form_igd.id
            this.form_FIRST_radiology.doctor_in_charge_id ||= this.form_igd.doctor_in_charge_id
        },

        async onEditFIRSTRad(obj) {
            await this.openFIRSTRad()
            this.form_FIRST_radiology.id = obj.id
            this.form_FIRST_radiology.date_request = obj.date_request

            this.form_FIRST_radiology.clinical_indications = obj.clinical_indications || ''

            if (obj?.radiology_request_item) {
                const mappedRad = obj.radiology_request_item.map(item => {
                    return {
                        costing_name: item.costing_name,
                        radiology_package_id: item.radiology_package_id,
                        service_class_id: item.service_class_id
                    };
                });                
                this.form_FIRST_radiology.radiology_request_items = mappedRad;
            } else {
                this.form_FIRST_radiology.radiology_request_items = []
            }
        },

        async validationFIRSTRad(){
            try {
                this.FIRSTdisabled.radiology = true
                await this.$store.dispatch('radiology/formPostPut', this.form_FIRST_radiology)
                .then(
                    async response => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active_FIRST.modal_rad = false
                        this.radiology_FIRST_list = await this.getRadiologyList(
                            this.form_FIRST_radiology.soap_id, 
                            this.form_FIRST_radiology.doctor_in_charge_id
                        )
                        await this.resetFIRSTRad()
                        this.getAssessment()
                        // this.socket.emit('SEND_LAB', []);
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error);
            } finally {
                this.FIRSTdisabled.radiology = false
            }
        },

        removeFIRSTRad(obj){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.no_request} dari ${this.form_igd.doctor_in_charge_name_1}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('radiology/removeRequest', obj.id)
                        .then(
                            async res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.radiology_FIRST_list = await this.getRadiologyList(
                                    this.form_FIRST_radiology.soap_id, 
                                    this.form_FIRST_radiology.doctor_in_charge_id
                                )
                                this.getAssessment()
                                // this.socket.emit('SEND_RAD', []);                                
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        openFIRSTPresc() {
            this.resetFIRSTPresc()
            this.active_FIRST.modal_presc = true
            if (this.form_FIRST_prescription.soap_p_id == '') {
                this.validationFIRSTSoapP()
            }
        },

        resetFIRSTPresc() {
            this.form_FIRST_prescription.id = ''
            this.form_FIRST_prescription.form_prescription_parent = []
            this.form_FIRST_prescription.form_prescription = []
            this.FIRSTParentStock = ''
            this.FIRSTCatalog = ''
        },

        openFIRSTPreParent(event) {
            if (event == null) return

            let index = this.form_FIRST_prescription.form_prescription_parent.findIndex((item) => item.id == event.value);
            if (index !== -1) return;
            
            // kl bukan obat satuan, alias racik
            if (event.value != 1) {
                this.temp_FIRST_parent.parent_id = event.value
                this.temp_FIRST_parent.parent_name = event.title
                this.temp_FIRST_parent.value = ''
                this.temp_FIRST_parent.signatura_time = ''
                this.temp_FIRST_parent.administer = ''
                this.temp_FIRST_parent.desc = ''
                this.temp_FIRST_parent.package = ''
                this.active.modal.presc_FIRST_parent = true
                return
            }

            // obat satuan
            this.form_FIRST_prescription.form_prescription_parent.push({
                id: event.value,
            })
        },

        closeFIRSTPreParent() {
            this.active.modal.presc_FIRST_parent = false
            this.FIRSTParentStock = ''
            this.FIRSTCatalog = ''
        },

        async addFIRSTParent() {
            try {
                this.FIRSTdisabled.preParent = true
                this.form_FIRST_prescription.form_prescription_parent.push(
                    {
                        id: this.temp_FIRST_parent.parent_id,
                        status_kardex: 3,
                        value: this.temp_FIRST_parent.value,
                        signatura_times_id: this.temp_FIRST_parent.signatura_time,
                        administer_id: this.temp_FIRST_parent.administer,
                        desc: this.temp_FIRST_parent.desc,
                        package: this.temp_FIRST_parent.package
                    }
                ) 
                this.active.modal.presc_FIRST_parent = false
            } catch (error) {
                console.error('Error at: ', error)
            } finally {
                setTimeout(() => {
                    this.FIRSTdisabled.preParent = false
                }, 1000);
            }
        },

        onSearchFIRSTCatalog(search, loading) {
            if (search.length >= 3) {
                loading(true)
                const searchTerm = search.toLowerCase();
                this.optFIRSTCatalog = this.catalog_list.filter(item => {
                    return item?.data_catalog?.composition.toLowerCase().includes(searchTerm) ||
                    item?.title.toLowerCase().includes(searchTerm)
                });
                loading(false)
            }
        },

        async openFIRSTPreItem(event) {
            this.FIRSTCatalog = ''
            if (this.FIRSTParentStock == '' || this.FIRSTParentStock == null) return alert("Silakan Pilih Sediaan Terlebih Dahulu")

            this.temp_FIRST_items.parent_id = this.FIRSTParentStock?.value
            this.temp_FIRST_items.parent_name = this.FIRSTParentStock?.title
            this.temp_FIRST_items.catalog_name = event?.data_catalog?.name
            this.temp_FIRST_items.data_catalog = event?.data_catalog
            this.temp_FIRST_items.signatura_dose = event?.data_catalog?.signatura_dose
            this.temp_FIRST_items.signatura_unit = event?.data_catalog?.signatura_unit_id
            this.temp_FIRST_items.signatura_time = event?.data_catalog?.signatura_times_id
            this.temp_FIRST_items.administer = event?.data_catalog?.instruction_id || ''
            this.temp_FIRST_items.desc = ''
            await this.getSignaturaUnits(event?.data_catalog?.package_id)

            this.active.modal.presc_FIRST_items = true
        },

        async addFIRSTPrescItem() {
            try {
                this.FIRSTdisabled.preItem = true
                this.form_FIRST_prescription.form_prescription.push({ 
                    pharmacy_catalog_id: this.temp_FIRST_items?.data_catalog?.id, 
                    package_id: this.temp_FIRST_items?.data_catalog?.package_id,
                    prescription_type_id: this.temp_FIRST_items.parent_id,
                    value_package: 1,
                    dose: this.temp_FIRST_items.signatura_dose,
                    dose_unit_id: this.temp_FIRST_items.signatura_unit,
                    signatura_times_id: this.temp_FIRST_items.signatura_time,
                    administer_id: this.temp_FIRST_items.administer,
                    desc: this.temp_FIRST_items.desc,
                    sort: this.form_FIRST_prescription.form_prescription.length, 
                    status_kardex: this.temp_FIRST_items.parent_id == 1 ? 1 : null,

                    name: this.temp_FIRST_items?.data_catalog?.name,
                    dose_unit_options: this.optItemSignUnits                
                })
                this.active.modal.presc_FIRST_items = false
            } catch (error) {
                console.error('Error at: ', error)
            } finally {
                setTimeout(() => {
                    this.FIRSTdisabled.preItem = false
                }, 1000);
            }
        },

        deleteFIRSTPresc (index) {
            this.form_FIRST_prescription.form_prescription.splice(index, 1)
        },

        async validationFIRSTPresc() {
            try {
                this.FIRSTdisabled.prescription = true 
                this.form_FIRST_prescription.pharmacy_depo_id ||= this.depo_emergency
                
                let inp_items = this.form_FIRST_prescription?.form_prescription 
                let inp_parent = this.form_FIRST_prescription?.form_prescription_parent 

                if (inp_parent?.length > 0 && inp_items?.length > 0) {
                    if(this.emptyChecking({
                        parents: inp_parent,
                        items: inp_items
                    })) {
                        const message = "SILAKAN LENGKAPI:\n\n" + this.combine_empty_found.map(item => `- ${item}`).join('\n');
                        alert(message);
                    } else {
                        await this.$store.dispatch('prescription/formPostPut', this.form_FIRST_prescription)
                        .then(
                            async res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.prescription_FIRST_list = await this.getPrescriptionList(
                                    this.form_FIRST_prescription.soap_id, 
                                    this.form_FIRST_prescription.doctor_in_charge_id
                                )
                                this.active_FIRST.modal_presc = false
                                this.form_FIRST_prescription.form_prescription_parent = []
                                this.form_FIRST_prescription.form_prescription = []
                                this.FIRSTParentStock = ''
                                this.FIRSTCatalog = ''

                                this.getAssessment()
                                // this.socket.emit('SEND_PRS', []);
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    }
                }  
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.FIRSTdisabled.prescription = false
            }
        },

        editFIRSTPresc(obj) {
            this.resetFIRSTPresc()
            this.active_FIRST.modal_presc = true

            this.form_FIRST_prescription.id = obj.id
            this.form_FIRST_prescription.soap_id = this.soap_id
            this.form_FIRST_prescription.pharmacy_depo_id = obj.pharmacy_depo_id
            this.form_FIRST_prescription.soap_p_id = obj.soap_p_id
            this.form_FIRST_prescription.registration_destination_id = obj.registration_destination_id
            this.form_FIRST_prescription.doctor_in_charge_id = obj.doctor_in_charge_id

            let var_parent = obj?.form_prescription_parent || []
            let var_presc = obj?.form_prescription || []

            if (var_parent.length > 0) {
                const mappedPrescParent = var_parent.map(item => {
                    if (item.id != 1) {
                        return {
                            id: item.id,
                            status_kardex: item.status_kardex ?? 3,                            
                            value: Number(item.value ?? 0),
                            package: Number(item.package),
                            signatura_times_id: item.signatura_times_id ?? '',
                            administer_id: item.administer_id ?? '',
                            desc: item.administer_id == this.other_administer ? item?.desc : '',
                        };
                    } else {
                        return {
                            id: item.id,
                        };
                    }

                });
                mappedPrescParent.sort((a, b) => a.id - b.id);                
                this.form_FIRST_prescription.form_prescription_parent = mappedPrescParent;
            } else {
                this.form_FIRST_prescription.form_prescription_parent = [{
                    id: 1,
                }]
            }

            if (var_presc.length > 0) {
                const mappedPresc = var_presc.map(item => {
                    let temp_sign = []
                    if (item?.dose_unit_options.length > 0) {
                        const mappedSign = item.dose_unit_options.map(item => {
                            return {
                                value: item.id,
                                title: item.name, 
                            };
                        });                
                        temp_sign = mappedSign
                    }

                    return {
                        pharmacy_catalog_id: item.pharmacy_catalog_id, 
                        package_id: item.package_id,
                        prescription_type_id: item.prescription_item_type_id,
                        value_package: Number(item.qty),
                        dose: item.dose,
                        dose_unit_id: item.dose_unit_id,
                        signatura_times_id: item.signatura_times_id,
                        administer_id: item.administer_id,
                        desc: item.administer_id == this.other_administer ? item?.desc : '',
                        sort: item.sort, 
                        status_kardex: item.status_kardex ?? '',

                        name: item.pharmacy_catalog_name,
                        dose_unit_options: temp_sign,
                    };
                });
                mappedPresc.sort((a, b) => a.sort - b.sort);                
                this.form_FIRST_prescription.form_prescription = mappedPresc;                                  
            } else {
                this.form_FIRST_prescription.form_prescription = [];                                  
            }
        },

        removeFIRSTPresc(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus order resep ${obj.no_order} dari ${this.form_igd.doctor_in_charge_name_1}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('prescription/removeOrder', obj.id)
                        .then(
                            async res => {
                                this.prescription_FIRST_list = await this.getPrescriptionList(
                                    this.form_FIRST_prescription.soap_id, 
                                    this.form_FIRST_prescription.doctor_in_charge_id
                                )
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getAssessment()     
                                // this.socket.emit('SEND_PRS', []);
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        // SPECIALIST-DOCTOR 2
        async onSECONDSearchIcd9cm(search, loading) {
            if(search.length >= 2) {
                loading(true);
                await this.$store.dispatch('icd9cm/getParam', search).then(data => {
                    this.OptionsSECONDIcd9cm = []
                    for (let i = 0; i < data.length; i++) {
                        const el = data[i];
                        let temp = {
                            value: el.id,
                            title: `${el.code} ${el.name}`,
                        }
                        this.OptionsSECONDIcd9cm.push(Object.assign({}, temp))
                    }
                    loading(false)
                })
            }
        },
        onSECONDAdd9cm (event) {
            this.SECONDicd9cm_id = ''
            if (this.form_SECOND_icd9cm.icd9cm.some(item => item.icd9cm_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD9 yang sama')
            } else {
                this.form_SECOND_icd9cm.icd9cm.push({ icd9cm_id:event.value, title:event.title, value:event.value, action: 'action'})
            }
        },
        deleteSECOND9cm (index) {
            this.form_SECOND_icd9cm.icd9cm.splice(index, 1)
        },

        async resetSECONDSoapP() {
            this.form_SECOND_icd9cm.id = ''
            this.form_SECOND_icd9cm.therapy_plan = ''
            this.form_SECOND_icd9cm.icd9cm = []
        },

        async validationSECONDSoapP(){
            try {
                this.SECONDdisabled.soap_p = true
                await this.$store.dispatch('soap/formPostPutSoapP', this.form_SECOND_icd9cm)
                .then(
                    async res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        await this.getSECONDSoapP(this.form_SECOND_icd9cm.soap_id, this.form_SECOND_icd9cm.doctor_in_charge_id)
                        this.getAssessment()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })                
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error);
            } finally {
                this.SECONDdisabled.soap_p = false                    
            }
        },

        async getSECONDSoapP(soap_id, doctor_in_charge_id){
            await this.resetSECONDSoapP()
            try {
                await this.$store.dispatch('soap/getSoapP', {soap_id: soap_id, doctor_in_charge_id: doctor_in_charge_id })
                .then(
                    response => {
                        if (response.length != 0) {
                            const data = response[0]
                            if (data.icd9cm) {
                                const mappedIcd9cm = data.icd9cm.map(item => {
                                    return {
                                        icd9cm_id: item.id,
                                        value: item.id,
                                        title: `${item.code} ${item.name}`, 
                                        action: "action"
                                    };
                                });                
                                this.form_SECOND_icd9cm.icd9cm = mappedIcd9cm;
                            }
                            this.form_SECOND_icd9cm.id = data.id
                            this.form_SECOND_icd9cm.therapy_plan = data.therapy_plan
                            this.form_SECOND_prescription.soap_p_id = data.id
                        }
                    }
                )
            } catch (error) {
                console.error('Error at: ', error)
            }            
        },

        async resetSECONDProcedure() {
            this.form_SECOND_transaction.id = ''
            this.form_SECOND_transaction.costing_id = ''
            this.form_SECOND_transaction.var_1 = ''
            this.form_SECOND_transaction.var_2 = 1
            this.form_SECOND_transaction.var_3 = ''
            this.form_SECOND_transaction.notes = ''
        },

        async openSECONDProcedure() {
            await this.resetSECONDProcedure()
            this.active_SECOND.modal_procedure = true
            this.form_SECOND_transaction.soap_id ||= this.form_igd.id
            this.form_SECOND_transaction.doctor_in_charge_id ||= this.form_igd.doctor_in_charge_id
        },

        async onEditSECONDProcedure(obj) {
            await this.openSECONDProcedure()

            this.form_SECOND_transaction.id = obj?.id
            this.form_SECOND_transaction.costing_id = {
                value: obj?.costing_id,
                title: obj?.costing_name,
                costing: obj?.var_1
            }
            this.form_SECOND_transaction.notes = obj?.notes
            this.form_SECOND_transaction.var_1 = obj?.var_1
            this.form_SECOND_transaction.var_2 = obj?.var_2
            this.form_SECOND_transaction.var_3 = obj?.var_3
        },

        async validationSECONDProcedure () {
            const quantity = Number(this.form_SECOND_transaction?.var_2 || 0)
            const price = Number(this.form_SECOND_transaction?.costing_id?.costing || 0)

            const formData = {
                id: this.form_SECOND_transaction?.id,
                costing_id: this.form_SECOND_transaction?.costing_id?.value || '',
                soap_id: this.form_SECOND_transaction?.soap_id,
                notes: this.form_SECOND_transaction?.notes,
                var_1: price,
                var_2: quantity,
                var_3: price*quantity,
                doctor_in_charge_id: this.form_SECOND_transaction?.doctor_in_charge_id
            }

            if (Number.isInteger(quantity) && quantity > 0 && formData.costing_id != '' && formData.costing_id != null) {
                try {
                    this.SECONDdisabled.transaction = true
                    await this.$store.dispatch('soap/postPutTransaction', formData)
                    .then(
                        async res => {
                            this.active_SECOND.modal_procedure = false
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.transaction_SECOND_list = await this.getTransactionList(
                                this.form_SECOND_transaction.soap_id,
                                this.form_SECOND_transaction.doctor_in_charge_id
                            );
                            await this.resetSECONDProcedure()
                            this.getAssessment()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    console.error('Error catch at: ', error) 
                } finally {
                    this.SECONDdisabled.transaction = false
                }
            } else {
                this.notification('Warning','CloseIcon','warning','Tindakan prosedur wajib diisi & jumlah minimal 1')
            }
        },

        removeSECONDProcedure(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus prosedur ${obj.costing_name} dari ${this.form_igd.doctor_in_charge_name_2}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('soap/deleteTransaction', obj.id)
                        .then(
                            async res => {
                                this.transaction_SECOND_list = await this.getTransactionList(
                                    this.form_SECOND_transaction.soap_id,
                                    this.form_SECOND_transaction.doctor_in_charge_id
                                );
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getAssessment()
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        async resetSECONDLab(){
            this.form_SECOND_laboratory.id = ''
            this.form_SECOND_laboratory.date_request = moment(new Date()).format('YYYY-MM-DD HH:mm')
            this.form_SECOND_laboratory.laboratory_request_items = []
        },

        async openSECONDLab(){
            await this.resetSECONDLab()
            this.active_SECOND.modal_lab = true
            this.form_SECOND_laboratory.soap_id ||= this.form_igd.id
            this.form_SECOND_laboratory.doctor_in_charge_id ||= this.form_igd.doctor_in_charge_id
        },

        async onEditSECONDLab(obj) {
            await this.openSECONDLab()
            this.form_SECOND_laboratory.id = obj?.id
            this.form_SECOND_laboratory.date_request = obj?.date_request

            if (obj?.laboratory_request_item) {
                const mappedLab = obj.laboratory_request_item.map(item => {
                    return {
                        costing_name: item.costing_name,
                        laboratory_package_id: item.laboratory_package_id,
                        service_class_id: item.service_class_id
                    };
                });                
                this.form_SECOND_laboratory.laboratory_request_items = mappedLab;
            } else {
                this.form_SECOND_laboratory.laboratory_request_items = []
            }
        },

        async validationSECONDLab(){
            try {
                this.SECONDdisabled.laboratory = true
                await this.$store.dispatch('laboratory/formPostPut', this.form_SECOND_laboratory)
                .then(
                    async response => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active_SECOND.modal_lab = false
                        this.laboratory_SECOND_list = await this.getLaboratoryList(
                            this.form_SECOND_laboratory.soap_id, 
                            this.form_SECOND_laboratory.doctor_in_charge_id
                        ) 
                        await this.resetSECONDLab()
                        this.getAssessment()
                        // this.socket.emit('SEND_LAB', []);
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error);
            } finally {
                this.SECONDdisabled.laboratory = false
            }
        },

        removeSECONDLab(obj){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.no_request} dari ${this.form_igd.doctor_in_charge_name_2}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('laboratory/removeRequest', obj.id)
                        .then(
                            async res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.laboratory_SECOND_list = await this.getLaboratoryList(
                                    this.form_SECOND_laboratory.soap_id, 
                                    this.form_SECOND_laboratory.doctor_in_charge_id
                                ) 
                                this.getAssessment()
                                // this.socket.emit('SEND_LAB', []);
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        async resetSECONDRad(){
            this.form_SECOND_radiology.id = ''
            this.form_SECOND_radiology.date_request = moment(new Date()).format('YYYY-MM-DD HH:mm')
            this.form_SECOND_radiology.radiology_request_items = []
            this.form_SECOND_radiology.clinical_indications = ''
        },

        async openSECONDRad(){
            await this.resetSECONDRad()
            this.active_SECOND.modal_rad = true
            this.form_SECOND_radiology.soap_id ||= this.form_igd.id
            this.form_SECOND_radiology.doctor_in_charge_id ||= this.form_igd.doctor_in_charge_id
        },

        async onEditSECONDRad(obj) {
            await this.openSECONDRad()
            this.form_SECOND_radiology.id = obj.id
            this.form_SECOND_radiology.date_request = obj.date_request

            this.form_SECOND_radiology.clinical_indications = obj.clinical_indications || ''

            if (obj?.radiology_request_item) {
                const mappedRad = obj.radiology_request_item.map(item => {
                    return {
                        costing_name: item.costing_name,
                        radiology_package_id: item.radiology_package_id,
                        service_class_id: item.service_class_id
                    };
                });                
                this.form_SECOND_radiology.radiology_request_items = mappedRad;
            } else {
                this.form_SECOND_radiology.radiology_request_items = []
            }
        },

        async validationSECONDRad(){
            try {
                this.SECONDdisabled.radiology = true
                await this.$store.dispatch('radiology/formPostPut', this.form_SECOND_radiology)
                .then(
                    async response => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active_SECOND.modal_rad = false
                        this.radiology_SECOND_list = await this.getRadiologyList(
                            this.form_SECOND_radiology.soap_id, 
                            this.form_SECOND_radiology.doctor_in_charge_id
                        )
                        await this.resetSECONDRad()
                        this.getAssessment()
                        // this.socket.emit('SEND_LAB', []);
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error);
            } finally {
                this.SECONDdisabled.radiology = false
            }
        },

        removeSECONDRad(obj){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.no_request} dari ${this.form_igd.doctor_in_charge_name_2}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('radiology/removeRequest', obj.id)
                        .then(
                            async res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.radiology_SECOND_list = await this.getRadiologyList(
                                    this.form_SECOND_radiology.soap_id, 
                                    this.form_SECOND_radiology.doctor_in_charge_id
                                )
                                this.getAssessment()
                                // this.socket.emit('SEND_RAD', []);                                
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        openSECONDPresc() {
            this.resetSECONDPresc()
            this.active_SECOND.modal_presc = true
            if (this.form_SECOND_prescription.soap_p_id == '') {
                this.validationSECONDSoapP()
            }
        },

        resetSECONDPresc() {
            this.form_SECOND_prescription.id = ''
            this.form_SECOND_prescription.form_prescription_parent = []
            this.form_SECOND_prescription.form_prescription = []
            this.SECONDParentStock = ''
            this.SECONDCatalog = ''
        },

        openSECONDPreParent(event) {
            if (event == null) return

            let index = this.form_SECOND_prescription.form_prescription_parent.findIndex((item) => item.id == event.value);
            if (index !== -1) return;
            
            // kl bukan obat satuan, alias racik
            if (event.value != 1) {
                this.temp_SECOND_parent.parent_id = event.value
                this.temp_SECOND_parent.parent_name = event.title
                this.temp_SECOND_parent.value = ''
                this.temp_SECOND_parent.signatura_time = ''
                this.temp_SECOND_parent.administer = ''
                this.temp_SECOND_parent.desc = ''
                this.temp_SECOND_parent.package = ''
                this.active.modal.presc_SECOND_parent = true
                return
            }

            // obat satuan
            this.form_SECOND_prescription.form_prescription_parent.push({
                id: event.value,
            })
        },

        closeSECONDPreParent() {
            this.active.modal.presc_SECOND_parent = false
            this.SECONDParentStock = ''
            this.SECONDCatalog = ''
        },

        async addSECONDParent() {
            try {
                this.SECONDdisabled.preParent = true
                this.form_SECOND_prescription.form_prescription_parent.push(
                    {
                        id: this.temp_SECOND_parent.parent_id,
                        status_kardex: 3,
                        value: this.temp_SECOND_parent.value,
                        signatura_times_id: this.temp_SECOND_parent.signatura_time,
                        administer_id: this.temp_SECOND_parent.administer,
                        desc: this.temp_SECOND_parent.desc,
                        package: this.temp_SECOND_parent.package
                    }
                ) 
                this.active.modal.presc_SECOND_parent = false
            } catch (error) {
                console.error('Error at: ', error)
            } finally {
                setTimeout(() => {
                    this.SECONDdisabled.preParent = false
                }, 1000);
            }
        },

        onSearchSECONDCatalog(search, loading) {
            if (search.length >= 3) {
                loading(true)
                const searchTerm = search.toLowerCase();
                this.optSECONDCatalog = this.catalog_list.filter(item => {
                    return item?.data_catalog?.composition.toLowerCase().includes(searchTerm) ||
                    item?.title.toLowerCase().includes(searchTerm)
                });
                loading(false)
            }
        },

        async openSECONDPreItem(event) {
            this.SECONDCatalog = ''
            if (this.SECONDParentStock == '' || this.SECONDParentStock == null) return alert("Silakan Pilih Sediaan Terlebih Dahulu")

            this.temp_SECOND_items.parent_id = this.SECONDParentStock?.value
            this.temp_SECOND_items.parent_name = this.SECONDParentStock?.title
            this.temp_SECOND_items.catalog_name = event?.data_catalog?.name
            this.temp_SECOND_items.data_catalog = event?.data_catalog
            this.temp_SECOND_items.signatura_dose = event?.data_catalog?.signatura_dose
            this.temp_SECOND_items.signatura_unit = event?.data_catalog?.signatura_unit_id
            this.temp_SECOND_items.signatura_time = event?.data_catalog?.signatura_times_id
            this.temp_SECOND_items.administer = event?.data_catalog?.instruction_id || ''
            this.temp_SECOND_items.desc = ''
            await this.getSignaturaUnits(event?.data_catalog?.package_id)

            this.active.modal.presc_SECOND_items = true
        },

        async addSECONDPrescItem() {
            try {
                this.SECONDdisabled.preItem = true
                this.form_SECOND_prescription.form_prescription.push({ 
                    pharmacy_catalog_id: this.temp_SECOND_items?.data_catalog?.id, 
                    package_id: this.temp_SECOND_items?.data_catalog?.package_id,
                    prescription_type_id: this.temp_SECOND_items.parent_id,
                    value_package: 1,
                    dose: this.temp_SECOND_items.signatura_dose,
                    dose_unit_id: this.temp_SECOND_items.signatura_unit,
                    signatura_times_id: this.temp_SECOND_items.signatura_time,
                    administer_id: this.temp_SECOND_items.administer,
                    desc: this.temp_SECOND_items.desc,
                    sort: this.form_SECOND_prescription.form_prescription.length, 
                    status_kardex: this.temp_SECOND_items.parent_id == 1 ? 1 : null,

                    name: this.temp_SECOND_items?.data_catalog?.name,
                    dose_unit_options: this.optItemSignUnits                
                })
                this.active.modal.presc_SECOND_items = false
            } catch (error) {
                console.error('Error at: ', error)
            } finally {
                setTimeout(() => {
                    this.SECONDdisabled.preItem = false
                }, 1000);
            }
        },

        deleteSECONDPresc (index) {
            this.form_SECOND_prescription.form_prescription.splice(index, 1)
        },

        async validationSECONDPresc() {
            try {
                this.SECONDdisabled.prescription = true 
                this.form_SECOND_prescription.pharmacy_depo_id ||= this.depo_emergency
                
                let inp_items = this.form_SECOND_prescription?.form_prescription 
                let inp_parent = this.form_SECOND_prescription?.form_prescription_parent 

                if (inp_parent?.length > 0 && inp_items?.length > 0) {
                    if(this.emptyChecking({
                        parents: inp_parent,
                        items: inp_items
                    })) {
                        const message = "SILAKAN LENGKAPI:\n\n" + this.combine_empty_found.map(item => `- ${item}`).join('\n');
                        alert(message);
                    } else {
                        await this.$store.dispatch('prescription/formPostPut', this.form_SECOND_prescription)
                        .then(
                            async res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.prescription_SECOND_list = await this.getPrescriptionList(
                                    this.form_SECOND_prescription.soap_id, 
                                    this.form_SECOND_prescription.doctor_in_charge_id
                                )
                                this.active_SECOND.modal_presc = false
                                this.form_SECOND_prescription.form_prescription_parent = []
                                this.form_SECOND_prescription.form_prescription = []
                                this.SECONDParentStock = ''
                                this.SECONDCatalog = ''

                                this.getAssessment()
                                // this.socket.emit('SEND_PRS', []);
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    }
                }  
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.SECONDdisabled.prescription = false
            }
        },

        editSECONDPresc(obj) {
            this.resetSECONDPresc()
            this.active_SECOND.modal_presc = true

            this.form_SECOND_prescription.id = obj.id
            this.form_SECOND_prescription.soap_id = this.soap_id
            this.form_SECOND_prescription.pharmacy_depo_id = obj.pharmacy_depo_id
            this.form_SECOND_prescription.soap_p_id = obj.soap_p_id
            this.form_SECOND_prescription.registration_destination_id = obj.registration_destination_id
            this.form_SECOND_prescription.doctor_in_charge_id = obj.doctor_in_charge_id

            let var_parent = obj?.form_prescription_parent || []
            let var_presc = obj?.form_prescription || []

            if (var_parent.length > 0) {
                const mappedPrescParent = var_parent.map(item => {
                    if (item.id != 1) {
                        return {
                            id: item.id,
                            status_kardex: item.status_kardex ?? 3,
                            value: Number(item.value ?? 0),
                            package: Number(item.package),
                            signatura_times_id: item.signatura_times_id ?? '',
                            administer_id: item.administer_id ?? '',
                            desc: item.administer_id == this.other_administer ? item?.desc : '',
                        };
                    } else {
                        return {
                            id: item.id,
                        };
                    }

                });
                mappedPrescParent.sort((a, b) => a.id - b.id);                
                this.form_SECOND_prescription.form_prescription_parent = mappedPrescParent;
            } else {
                this.form_SECOND_prescription.form_prescription_parent = [{
                    id: 1,
                }]
            }

            if (var_presc.length > 0) {
                const mappedPresc = var_presc.map(item => {
                    let temp_sign = []
                    if (item?.dose_unit_options.length > 0) {
                        const mappedSign = item.dose_unit_options.map(item => {
                            return {
                                value: item.id,
                                title: item.name, 
                            };
                        });                
                        temp_sign = mappedSign
                    }

                    return {
                        pharmacy_catalog_id: item.pharmacy_catalog_id, 
                        package_id: item.package_id,
                        prescription_type_id: item.prescription_item_type_id,
                        value_package: Number(item.qty),
                        dose: item.dose,
                        dose_unit_id: item.dose_unit_id,
                        signatura_times_id: item.signatura_times_id,
                        administer_id: item.administer_id,
                        desc: item.administer_id == this.other_administer ? item?.desc : '',
                        sort: item.sort, 
                        status_kardex: item.status_kardex ?? '',

                        name: item.pharmacy_catalog_name,
                        id: item.id,
                        dose_unit_options: temp_sign,
                    };
                });
                mappedPresc.sort((a, b) => a.sort - b.sort);                
                this.form_SECOND_prescription.form_prescription = mappedPresc;                                  
            } else {
                this.form_SECOND_prescription.form_prescription = [];                                  
            }
        },

        removeSECONDPresc(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus order resep ${obj.no_order} dari ${this.form_igd.doctor_in_charge_name_2}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('prescription/removeOrder', obj.id)
                        .then(
                            async res => {
                                this.prescription_SECOND_list = await this.getPrescriptionList(
                                    this.form_SECOND_prescription.soap_id, 
                                    this.form_SECOND_prescription.doctor_in_charge_id
                                )
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getAssessment()     
                                // this.socket.emit('SEND_PRS', []);
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        // SPECIALIST-DOCTOR 3
        async onTHIRDSearchIcd9cm(search, loading) {
            if(search.length >= 2) {
                loading(true);
                await this.$store.dispatch('icd9cm/getParam', search).then(data => {
                    this.OptionsTHIRDIcd9cm = []
                    for (let i = 0; i < data.length; i++) {
                        const el = data[i];
                        let temp = {
                            value: el.id,
                            title: `${el.code} ${el.name}`,
                        }
                        this.OptionsTHIRDIcd9cm.push(Object.assign({}, temp))
                    }
                    loading(false)
                })
            }
        },
        onTHIRDAdd9cm (event) {
            this.THIRDicd9cm_id = ''
            if (this.form_THIRD_icd9cm.icd9cm.some(item => item.icd9cm_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD9 yang sama')
            } else {
                this.form_THIRD_icd9cm.icd9cm.push({ icd9cm_id:event.value, title:event.title, value:event.value, action: 'action'})
            }
        },
        deleteTHIRD9cm (index) {
            this.form_THIRD_icd9cm.icd9cm.splice(index, 1)
        },

        async resetTHIRDSoapP() {
            this.form_THIRD_icd9cm.id = ''
            this.form_THIRD_icd9cm.therapy_plan = ''
            this.form_THIRD_icd9cm.icd9cm = []
        },

        async validationTHIRDSoapP(){
            try {
                this.THIRDdisabled.soap_p = true
                await this.$store.dispatch('soap/formPostPutSoapP', this.form_THIRD_icd9cm)
                .then(
                    async res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        await this.getTHIRDSoapP(this.form_THIRD_icd9cm.soap_id, this.form_THIRD_icd9cm.doctor_in_charge_id)
                        this.getAssessment()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })                
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error);
            } finally {
                this.THIRDdisabled.soap_p = false                    
            }
        },

        async getTHIRDSoapP(soap_id, doctor_in_charge_id){
            await this.resetTHIRDSoapP()
            try {
                await this.$store.dispatch('soap/getSoapP', {soap_id: soap_id, doctor_in_charge_id: doctor_in_charge_id })
                .then(
                    response => {
                        if (response.length != 0) {
                            const data = response[0]
                            if (data.icd9cm) {
                                const mappedIcd9cm = data.icd9cm.map(item => {
                                    return {
                                        icd9cm_id: item.id,
                                        value: item.id,
                                        title: `${item.code} ${item.name}`, 
                                        action: "action"
                                    };
                                });                
                                this.form_THIRD_icd9cm.icd9cm = mappedIcd9cm;
                            }
                            this.form_THIRD_icd9cm.id = data.id
                            this.form_THIRD_icd9cm.therapy_plan = data.therapy_plan
                            this.form_THIRD_prescription.soap_p_id = data.id
                        }
                    }
                )
            } catch (error) {
                console.error('Error at: ', error)
            }            
        },

        async resetTHIRDProcedure() {
            this.form_THIRD_transaction.id = ''
            this.form_THIRD_transaction.costing_id = ''
            this.form_THIRD_transaction.var_1 = ''
            this.form_THIRD_transaction.var_2 = 1
            this.form_THIRD_transaction.var_3 = ''
            this.form_THIRD_transaction.notes = ''
        },

        async openTHIRDProcedure() {
            await this.resetTHIRDProcedure()
            this.active_THIRD.modal_procedure = true
            this.form_THIRD_transaction.soap_id ||= this.form_igd.id
            this.form_THIRD_transaction.doctor_in_charge_id ||= this.form_igd.doctor_in_charge_id
        },

        async onEditTHIRDProcedure(obj) {
            await this.openTHIRDProcedure()

            this.form_THIRD_transaction.id = obj?.id
            this.form_THIRD_transaction.costing_id = {
                value: obj?.costing_id,
                title: obj?.costing_name,
                costing: obj?.var_1
            }
            this.form_THIRD_transaction.notes = obj?.notes
            this.form_THIRD_transaction.var_1 = obj?.var_1
            this.form_THIRD_transaction.var_2 = obj?.var_2
            this.form_THIRD_transaction.var_3 = obj?.var_3
        },

        async validationTHIRDProcedure () {
            const quantity = Number(this.form_THIRD_transaction?.var_2 || 0)
            const price = Number(this.form_THIRD_transaction?.costing_id?.costing || 0)

            const formData = {
                id: this.form_THIRD_transaction?.id,
                costing_id: this.form_THIRD_transaction?.costing_id?.value || '',
                soap_id: this.form_THIRD_transaction?.soap_id,
                notes: this.form_THIRD_transaction?.notes,
                var_1: price,
                var_2: quantity,
                var_3: price*quantity,
                doctor_in_charge_id: this.form_THIRD_transaction?.doctor_in_charge_id
            }

            if (Number.isInteger(quantity) && quantity > 0 && formData.costing_id != '' && formData.costing_id != null) {
                try {
                    this.THIRDdisabled.transaction = true
                    await this.$store.dispatch('soap/postPutTransaction', formData)
                    .then(
                        async res => {
                            this.active_THIRD.modal_procedure = false
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.transaction_THIRD_list = await this.getTransactionList(
                                this.form_THIRD_transaction.soap_id,
                                this.form_THIRD_transaction.doctor_in_charge_id
                            );
                            await this.resetTHIRDProcedure()
                            this.getAssessment()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    console.error('Error catch at: ', error) 
                } finally {
                    this.THIRDdisabled.transaction = false
                }
            } else {
                this.notification('Warning','CloseIcon','warning','Tindakan prosedur wajib diisi & jumlah minimal 1')
            }
        },

        removeTHIRDProcedure(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus prosedur ${obj.costing_name} dari ${this.form_igd.doctor_in_charge_name_3}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('soap/deleteTransaction', obj.id)
                        .then(
                            async res => {
                                this.transaction_THIRD_list = await this.getTransactionList(
                                    this.form_THIRD_transaction.soap_id,
                                    this.form_THIRD_transaction.doctor_in_charge_id
                                );
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getAssessment()
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        async resetTHIRDLab(){
            this.form_THIRD_laboratory.id = ''
            this.form_THIRD_laboratory.date_request = moment(new Date()).format('YYYY-MM-DD HH:mm')
            this.form_THIRD_laboratory.laboratory_request_items = []
        },

        async openTHIRDLab(){
            await this.resetTHIRDLab()
            this.active_THIRD.modal_lab = true
            this.form_THIRD_laboratory.soap_id ||= this.form_igd.id
            this.form_THIRD_laboratory.doctor_in_charge_id ||= this.form_igd.doctor_in_charge_id
        },

        async onEditTHIRDLab(obj) {
            await this.openTHIRDLab()
            this.form_THIRD_laboratory.id = obj?.id
            this.form_THIRD_laboratory.date_request = obj?.date_request

            if (obj?.laboratory_request_item) {
                const mappedLab = obj.laboratory_request_item.map(item => {
                    return {
                        costing_name: item.costing_name,
                        laboratory_package_id: item.laboratory_package_id,
                        service_class_id: item.service_class_id
                    };
                });                
                this.form_THIRD_laboratory.laboratory_request_items = mappedLab;
            } else {
                this.form_THIRD_laboratory.laboratory_request_items = []
            }
        },

        async validationTHIRDLab(){
            try {
                this.THIRDdisabled.laboratory = true
                await this.$store.dispatch('laboratory/formPostPut', this.form_THIRD_laboratory)
                .then(
                    async response => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active_THIRD.modal_lab = false
                        this.laboratory_THIRD_list = await this.getLaboratoryList(
                            this.form_THIRD_laboratory.soap_id, 
                            this.form_THIRD_laboratory.doctor_in_charge_id
                        ) 
                        await this.resetTHIRDLab()
                        this.getAssessment()
                        // this.socket.emit('SEND_LAB', []);
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error);
            } finally {
                this.THIRDdisabled.laboratory = false
            }
        },

        removeTHIRDLab(obj){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.no_request} dari ${this.form_igd.doctor_in_charge_name_3}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('laboratory/removeRequest', obj.id)
                        .then(
                            async res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.laboratory_THIRD_list = await this.getLaboratoryList(
                                    this.form_THIRD_laboratory.soap_id, 
                                    this.form_THIRD_laboratory.doctor_in_charge_id
                                ) 
                                this.getAssessment()
                                // this.socket.emit('SEND_LAB', []);
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        async resetTHIRDRad(){
            this.form_THIRD_radiology.id = ''
            this.form_THIRD_radiology.date_request = moment(new Date()).format('YYYY-MM-DD HH:mm')
            this.form_THIRD_radiology.radiology_request_items = []
            this.form_THIRD_radiology.clinical_indications = ''
        },

        async openTHIRDRad(){
            await this.resetTHIRDRad()
            this.active_THIRD.modal_rad = true
            this.form_THIRD_radiology.soap_id ||= this.form_igd.id
            this.form_THIRD_radiology.doctor_in_charge_id ||= this.form_igd.doctor_in_charge_id
        },

        async onEditTHIRDRad(obj) {
            await this.openTHIRDRad()
            this.form_THIRD_radiology.id = obj.id
            this.form_THIRD_radiology.date_request = obj.date_request

            this.form_THIRD_radiology.clinical_indications = obj.clinical_indications

            if (obj?.radiology_request_item) {
                const mappedRad = obj.radiology_request_item.map(item => {
                    return {
                        costing_name: item.costing_name,
                        radiology_package_id: item.radiology_package_id,
                        service_class_id: item.service_class_id
                    };
                });                
                this.form_THIRD_radiology.radiology_request_items = mappedRad;
            } else {
                this.form_THIRD_radiology.radiology_request_items = []
            }
        },

        async validationTHIRDRad(){
            try {
                this.THIRDdisabled.radiology = true
                await this.$store.dispatch('radiology/formPostPut', this.form_THIRD_radiology)
                .then(
                    async response => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active_THIRD.modal_rad = false
                        this.radiology_THIRD_list = await this.getRadiologyList(
                            this.form_THIRD_radiology.soap_id, 
                            this.form_THIRD_radiology.doctor_in_charge_id
                        )
                        await this.resetTHIRDRad()
                        this.getAssessment()
                        // this.socket.emit('SEND_LAB', []);
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error);
            } finally {
                this.THIRDdisabled.radiology = false
            }
        },

        removeTHIRDRad(obj){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.no_request} dari ${this.form_igd.doctor_in_charge_name_3}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('radiology/removeRequest', obj.id)
                        .then(
                            async res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.radiology_THIRD_list = await this.getRadiologyList(
                                    this.form_THIRD_radiology.soap_id, 
                                    this.form_THIRD_radiology.doctor_in_charge_id
                                )
                                this.getAssessment()
                                // this.socket.emit('SEND_RAD', []);                                
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        openTHIRDPresc() {
            this.resetTHIRDPresc()
            this.active_THIRD.modal_presc = true
            if (this.form_THIRD_prescription.soap_p_id == '') {
                this.validationTHIRDSoapP()
            }
        },

        resetTHIRDPresc() {
            this.form_THIRD_prescription.id = ''
            this.form_THIRD_prescription.form_prescription_parent = []
            this.form_THIRD_prescription.form_prescription = []
            this.THIRDParentStock = ''
            this.THIRDCatalog = ''
        },

        openTHIRDPreParent(event) {
            if (event == null) return

            let index = this.form_THIRD_prescription.form_prescription_parent.findIndex((item) => item.id == event.value);
            if (index !== -1) return;
            
            // kl bukan obat satuan, alias racik
            if (event.value != 1) {
                this.temp_THIRD_parent.parent_id = event.value
                this.temp_THIRD_parent.parent_name = event.title
                this.temp_THIRD_parent.value = ''
                this.temp_THIRD_parent.signatura_time = ''
                this.temp_THIRD_parent.administer = ''
                this.temp_THIRD_parent.desc = ''
                this.temp_THIRD_parent.package = ''
                this.active.modal.presc_THIRD_parent = true
                return
            }

            // obat satuan
            this.form_THIRD_prescription.form_prescription_parent.push({
                id: event.value,
            })
        },

        closeTHIRDPreParent() {
            this.active.modal.presc_THIRD_parent = false
            this.THIRDParentStock = ''
            this.THIRDCatalog = ''
        },

        async addTHIRDParent() {
            try {
                this.THIRDdisabled.preParent = true
                this.form_THIRD_prescription.form_prescription_parent.push(
                    {
                        id: this.temp_THIRD_parent.parent_id,
                        status_kardex: 3,
                        value: this.temp_THIRD_parent.value,
                        signatura_times_id: this.temp_THIRD_parent.signatura_time,
                        administer_id: this.temp_THIRD_parent.administer,
                        desc: this.temp_THIRD_parent.desc,
                        package: this.temp_THIRD_parent.package
                    }
                ) 
                this.active.modal.presc_THIRD_parent = false
            } catch (error) {
                console.error('Error at: ', error)
            } finally {
                setTimeout(() => {
                    this.THIRDdisabled.preParent = false
                }, 1000);
            }
        },

        onSearchTHIRDCatalog(search, loading) {
            if (search.length >= 3) {
                loading(true)
                const searchTerm = search.toLowerCase();
                this.optTHIRDCatalog = this.catalog_list.filter(item => {
                    return item?.data_catalog?.composition.toLowerCase().includes(searchTerm) ||
                    item?.title.toLowerCase().includes(searchTerm)
                });
                loading(false)
            }
        },

        async openTHIRDPreItem(event) {
            this.THIRDCatalog = ''
            if (this.THIRDParentStock == '' || this.THIRDParentStock == null) return alert("Silakan Pilih Sediaan Terlebih Dahulu")

            this.temp_THIRD_items.parent_id = this.THIRDParentStock?.value
            this.temp_THIRD_items.parent_name = this.THIRDParentStock?.title
            this.temp_THIRD_items.catalog_name = event?.data_catalog?.name
            this.temp_THIRD_items.data_catalog = event?.data_catalog
            this.temp_THIRD_items.signatura_dose = event?.data_catalog?.signatura_dose
            this.temp_THIRD_items.signatura_unit = event?.data_catalog?.signatura_unit_id
            this.temp_THIRD_items.signatura_time = event?.data_catalog?.signatura_times_id
            this.temp_THIRD_items.administer = event?.data_catalog?.instruction_id || ''
            this.temp_THIRD_items.desc = ''
            await this.getSignaturaUnits(event?.data_catalog?.package_id)

            this.active.modal.presc_THIRD_items = true
        },

        async addTHIRDPrescItem() {
            try {
                this.THIRDdisabled.preItem = true
                this.form_THIRD_prescription.form_prescription.push({ 
                    pharmacy_catalog_id: this.temp_THIRD_items?.data_catalog?.id, 
                    package_id: this.temp_THIRD_items?.data_catalog?.package_id,
                    prescription_type_id: this.temp_THIRD_items.parent_id,
                    value_package: 1,
                    dose: this.temp_THIRD_items.signatura_dose,
                    dose_unit_id: this.temp_THIRD_items.signatura_unit,
                    signatura_times_id: this.temp_THIRD_items.signatura_time,
                    administer_id: this.temp_THIRD_items.administer,
                    desc: this.temp_THIRD_items.desc,
                    sort: this.form_THIRD_prescription.form_prescription.length, 
                    status_kardex: this.temp_THIRD_items.parent_id == 1 ? 1 : null,

                    name: this.temp_THIRD_items?.data_catalog?.name,
                    dose_unit_options: this.optItemSignUnits                
                })
                this.active.modal.presc_THIRD_items = false
            } catch (error) {
                console.error('Error at: ', error)
            } finally {
                setTimeout(() => {
                    this.THIRDdisabled.preItem = false
                }, 1000);
            }
        },

        deleteTHIRDPresc (index) {
            this.form_THIRD_prescription.form_prescription.splice(index, 1)
        },

        async validationTHIRDPresc() {
            try {
                this.THIRDdisabled.prescription = true 
                this.form_THIRD_prescription.pharmacy_depo_id ||= this.depo_emergency
                
                let inp_items = this.form_THIRD_prescription?.form_prescription 
                let inp_parent = this.form_THIRD_prescription?.form_prescription_parent 

                if (inp_parent?.length > 0 && inp_items?.length > 0) {
                    if(this.emptyChecking({
                        parents: inp_parent,
                        items: inp_items
                    })) {
                        const message = "SILAKAN LENGKAPI:\n\n" + this.combine_empty_found.map(item => `- ${item}`).join('\n');
                        alert(message);
                    } else {
                        await this.$store.dispatch('prescription/formPostPut', this.form_THIRD_prescription)
                        .then(
                            async res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.prescription_THIRD_list = await this.getPrescriptionList(
                                    this.form_THIRD_prescription.soap_id, 
                                    this.form_THIRD_prescription.doctor_in_charge_id
                                )
                                this.active_THIRD.modal_presc = false
                                this.form_THIRD_prescription.form_prescription_parent = []
                                this.form_THIRD_prescription.form_prescription = []
                                this.THIRDParentStock = ''
                                this.THIRDCatalog = ''

                                this.getAssessment()
                                // this.socket.emit('SEND_PRS', []);
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    }
                }  
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.THIRDdisabled.prescription = false
            }
        },

        editTHIRDPresc(obj) {
            this.resetTHIRDPresc()
            this.active_THIRD.modal_presc = true

            this.form_THIRD_prescription.id = obj.id
            this.form_THIRD_prescription.soap_id = this.soap_id
            this.form_THIRD_prescription.pharmacy_depo_id = obj.pharmacy_depo_id
            this.form_THIRD_prescription.soap_p_id = obj.soap_p_id
            this.form_THIRD_prescription.registration_destination_id = obj.registration_destination_id
            this.form_THIRD_prescription.doctor_in_charge_id = obj.doctor_in_charge_id

            let var_parent = obj?.form_prescription_parent || []
            let var_presc = obj?.form_prescription || []

            if (var_parent.length > 0) {
                const mappedPrescParent = var_parent.map(item => {
                    if (item.id != 1) {
                        return {
                            id: item.id,
                            status_kardex: item.status_kardex ?? 3,
                            value: Number(item.value ?? 0),
                            package: Number(item.package),
                            signatura_times_id: item.signatura_times_id ?? '',
                            administer_id: item.administer_id ?? '',
                            desc: item.administer_id == this.other_administer ? item?.desc : '',
                        };
                    } else {
                        return {
                            id: item.id,
                        };
                    }

                });
                mappedPrescParent.sort((a, b) => a.id - b.id);                
                this.form_THIRD_prescription.form_prescription_parent = mappedPrescParent;
            } else {
                this.form_THIRD_prescription.form_prescription_parent = [{
                    id: 1,
                }]
            }

            if (var_presc.length > 0) {
                const mappedPresc = var_presc.map(item => {
                    let temp_sign = []
                    if (item?.dose_unit_options.length > 0) {
                        const mappedSign = item.dose_unit_options.map(item => {
                            return {
                                value: item.id,
                                title: item.name, 
                            };
                        });                
                        temp_sign = mappedSign
                    }

                    return {
                        pharmacy_catalog_id: item.pharmacy_catalog_id, 
                        package_id: item.package_id,
                        prescription_type_id: item.prescription_item_type_id,
                        value_package: Number(item.qty),
                        dose: item.dose,
                        dose_unit_id: item.dose_unit_id,
                        signatura_times_id: item.signatura_times_id,
                        administer_id: item.administer_id,
                        desc: item.administer_id == this.other_administer ? item?.desc : '',
                        sort: item.sort, 
                        status_kardex: item.status_kardex ?? '',

                        name: item.pharmacy_catalog_name,
                        dose_unit_options: temp_sign,
                    };
                });
                mappedPresc.sort((a, b) => a.sort - b.sort);                
                this.form_THIRD_prescription.form_prescription = mappedPresc;                                  
            } else {
                this.form_THIRD_prescription.form_prescription = [];                                  
            }
        },

        removeTHIRDPresc(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus order resep ${obj.no_order} dari ${this.form_igd.doctor_in_charge_name_3}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('prescription/removeOrder', obj.id)
                        .then(
                            async res => {
                                this.prescription_THIRD_list = await this.getPrescriptionList(
                                    this.form_THIRD_prescription.soap_id, 
                                    this.form_THIRD_prescription.doctor_in_charge_id
                                )
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getAssessment()     
                                // this.socket.emit('SEND_PRS', []);
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        // SPECIALIST-DOCTOR 4
        async onFOURTHSearchIcd9cm(search, loading) {
            if(search.length >= 2) {
                loading(true);
                await this.$store.dispatch('icd9cm/getParam', search).then(data => {
                    this.OptionsFOURTHIcd9cm = []
                    for (let i = 0; i < data.length; i++) {
                        const el = data[i];
                        let temp = {
                            value: el.id,
                            title: `${el.code} ${el.name}`,
                        }
                        this.OptionsFOURTHIcd9cm.push(Object.assign({}, temp))
                    }
                    loading(false)
                })
            }
        },
        onFOURTHAdd9cm (event) {
            this.FOURTHicd9cm_id = ''
            if (this.form_FOURTH_icd9cm.icd9cm.some(item => item.icd9cm_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD9 yang sama')
            } else {
                this.form_FOURTH_icd9cm.icd9cm.push({ icd9cm_id:event.value, title:event.title, value:event.value, action: 'action'})
            }
        },
        deleteFOURTH9cm (index) {
            this.form_FOURTH_icd9cm.icd9cm.splice(index, 1)
        },

        async resetFOURTHSoapP() {
            this.form_FOURTH_icd9cm.id = ''
            this.form_FOURTH_icd9cm.therapy_plan = ''
            this.form_FOURTH_icd9cm.icd9cm = []
        },

        async validationFOURTHSoapP(){
            try {
                this.FOURTHdisabled.soap_p = true
                await this.$store.dispatch('soap/formPostPutSoapP', this.form_FOURTH_icd9cm)
                .then(
                    async res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        await this.getFOURTHSoapP(this.form_FOURTH_icd9cm.soap_id, this.form_FOURTH_icd9cm.doctor_in_charge_id)
                        this.getAssessment()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })                
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error);
            } finally {
                this.FOURTHdisabled.soap_p = false                    
            }
        },

        async getFOURTHSoapP(soap_id, doctor_in_charge_id){
            await this.resetFOURTHSoapP()
            try {
                await this.$store.dispatch('soap/getSoapP', {soap_id: soap_id, doctor_in_charge_id: doctor_in_charge_id })
                .then(
                    response => {
                        if (response.length != 0) {
                            const data = response[0]
                            if (data.icd9cm) {
                                const mappedIcd9cm = data.icd9cm.map(item => {
                                    return {
                                        icd9cm_id: item.id,
                                        value: item.id,
                                        title: `${item.code} ${item.name}`, 
                                        action: "action"
                                    };
                                });                
                                this.form_FOURTH_icd9cm.icd9cm = mappedIcd9cm;
                            }
                            this.form_FOURTH_icd9cm.id = data.id
                            this.form_FOURTH_icd9cm.therapy_plan = data.therapy_plan
                            this.form_FOURTH_prescription.soap_p_id = data.id
                        }
                    }
                )
            } catch (error) {
                console.error('Error at: ', error)
            }            
        },

        async resetFOURTHProcedure() {
            this.form_FOURTH_transaction.id = ''
            this.form_FOURTH_transaction.costing_id = ''
            this.form_FOURTH_transaction.var_1 = ''
            this.form_FOURTH_transaction.var_2 = 1
            this.form_FOURTH_transaction.var_3 = ''
            this.form_FOURTH_transaction.notes = ''
        },

        async openFOURTHProcedure() {
            await this.resetFOURTHProcedure()
            this.active_FOURTH.modal_procedure = true
            this.form_FOURTH_transaction.soap_id ||= this.form_igd.id
            this.form_FOURTH_transaction.doctor_in_charge_id ||= this.form_igd.doctor_in_charge_id
        },

        async onEditFOURTHProcedure(obj) {
            await this.openFOURTHProcedure()

            this.form_FOURTH_transaction.id = obj?.id
            this.form_FOURTH_transaction.costing_id = {
                value: obj?.costing_id,
                title: obj?.costing_name,
                costing: obj?.var_1
            }
            this.form_FOURTH_transaction.notes = obj?.notes
            this.form_FOURTH_transaction.var_1 = obj?.var_1
            this.form_FOURTH_transaction.var_2 = obj?.var_2
            this.form_FOURTH_transaction.var_3 = obj?.var_3
        },

        async validationFOURTHProcedure () {
            const quantity = Number(this.form_FOURTH_transaction?.var_2 || 0)
            const price = Number(this.form_FOURTH_transaction?.costing_id?.costing || 0)

            const formData = {
                id: this.form_FOURTH_transaction?.id,
                costing_id: this.form_FOURTH_transaction?.costing_id?.value || '',
                soap_id: this.form_FOURTH_transaction?.soap_id,
                notes: this.form_FOURTH_transaction?.notes,
                var_1: price,
                var_2: quantity,
                var_3: price*quantity,
                doctor_in_charge_id: this.form_FOURTH_transaction?.doctor_in_charge_id
            }

            if (Number.isInteger(quantity) && quantity > 0 && formData.costing_id != '' && formData.costing_id != null) {
                try {
                    this.FOURTHdisabled.transaction = true
                    await this.$store.dispatch('soap/postPutTransaction', formData)
                    .then(
                        async res => {
                            this.active_FOURTH.modal_procedure = false
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.transaction_FOURTH_list = await this.getTransactionList(
                                this.form_FOURTH_transaction.soap_id,
                                this.form_FOURTH_transaction.doctor_in_charge_id
                            );
                            await this.resetFOURTHProcedure()
                            this.getAssessment()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    console.error('Error catch at: ', error) 
                } finally {
                    this.FOURTHdisabled.transaction = false
                }
            } else {
                this.notification('Warning','CloseIcon','warning','Tindakan prosedur wajib diisi & jumlah minimal 1')
            }
        },

        removeFOURTHProcedure(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus prosedur ${obj.costing_name} dari ${this.form_igd.doctor_in_charge_name_4}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('soap/deleteTransaction', obj.id)
                        .then(
                            async res => {
                                this.transaction_FOURTH_list = await this.getTransactionList(
                                    this.form_FOURTH_transaction.soap_id,
                                    this.form_FOURTH_transaction.doctor_in_charge_id
                                );
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getAssessment()
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        async resetFOURTHLab(){
            this.form_FOURTH_laboratory.id = ''
            this.form_FOURTH_laboratory.date_request = moment(new Date()).format('YYYY-MM-DD HH:mm')
            this.form_FOURTH_laboratory.laboratory_request_items = []
        },

        async openFOURTHLab(){
            await this.resetFOURTHLab()
            this.active_FOURTH.modal_lab = true
            this.form_FOURTH_laboratory.soap_id ||= this.form_igd.id
            this.form_FOURTH_laboratory.doctor_in_charge_id ||= this.form_igd.doctor_in_charge_id
        },

        async onEditFOURTHLab(obj) {
            await this.openFOURTHLab()
            this.form_FOURTH_laboratory.id = obj?.id
            this.form_FOURTH_laboratory.date_request = obj?.date_request

            if (obj?.laboratory_request_item) {
                const mappedLab = obj.laboratory_request_item.map(item => {
                    return {
                        costing_name: item.costing_name,
                        laboratory_package_id: item.laboratory_package_id,
                        service_class_id: item.service_class_id
                    };
                });                
                this.form_FOURTH_laboratory.laboratory_request_items = mappedLab;
            } else {
                this.form_FOURTH_laboratory.laboratory_request_items = []
            }
        },

        async validationFOURTHLab(){
            try {
                this.FOURTHdisabled.laboratory = true
                await this.$store.dispatch('laboratory/formPostPut', this.form_FOURTH_laboratory)
                .then(
                    async response => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active_FOURTH.modal_lab = false
                        this.laboratory_FOURTH_list = await this.getLaboratoryList(
                            this.form_FOURTH_laboratory.soap_id, 
                            this.form_FOURTH_laboratory.doctor_in_charge_id
                        ) 
                        await this.resetFOURTHLab()
                        this.getAssessment()
                        // this.socket.emit('SEND_LAB', []);
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error);
            } finally {
                this.FOURTHdisabled.laboratory = false
            }
        },

        removeFOURTHLab(obj){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.no_request} dari ${this.form_igd.doctor_in_charge_name_4}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('laboratory/removeRequest', obj.id)
                        .then(
                            async res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.laboratory_FOURTH_list = await this.getLaboratoryList(
                                    this.form_FOURTH_laboratory.soap_id, 
                                    this.form_FOURTH_laboratory.doctor_in_charge_id
                                ) 
                                this.getAssessment()
                                // this.socket.emit('SEND_LAB', []);
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        async resetFOURTHRad(){
            this.form_FOURTH_radiology.id = ''
            this.form_FOURTH_radiology.date_request = moment(new Date()).format('YYYY-MM-DD HH:mm')
            this.form_FOURTH_radiology.radiology_request_items = []
            this.form_FOURTH_radiology.clinical_indications = ''
        },

        async openFOURTHRad(){
            await this.resetFOURTHRad()
            this.active_FOURTH.modal_rad = true
            this.form_FOURTH_radiology.soap_id ||= this.form_igd.id
            this.form_FOURTH_radiology.doctor_in_charge_id ||= this.form_igd.doctor_in_charge_id
        },

        async onEditFOURTHRad(obj) {
            await this.openFOURTHRad()
            this.form_FOURTH_radiology.id = obj.id
            this.form_FOURTH_radiology.date_request = obj.date_request

            this.form_FOURTH_radiology.clinical_indications = obj.clinical_indications

            if (obj?.radiology_request_item) {
                const mappedRad = obj.radiology_request_item.map(item => {
                    return {
                        costing_name: item.costing_name,
                        radiology_package_id: item.radiology_package_id,
                        service_class_id: item.service_class_id
                    };
                });                
                this.form_FOURTH_radiology.radiology_request_items = mappedRad;
            } else {
                this.form_FOURTH_radiology.radiology_request_items = []
            }
        },

        async validationFOURTHRad(){
            try {
                this.FOURTHdisabled.radiology = true
                await this.$store.dispatch('radiology/formPostPut', this.form_FOURTH_radiology)
                .then(
                    async response => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active_FOURTH.modal_rad = false
                        this.radiology_FOURTH_list = await this.getRadiologyList(
                            this.form_FOURTH_radiology.soap_id, 
                            this.form_FOURTH_radiology.doctor_in_charge_id
                        )
                        await this.resetFOURTHRad()
                        this.getAssessment()
                        // this.socket.emit('SEND_LAB', []);
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error);
            } finally {
                this.FOURTHdisabled.radiology = false
            }
        },

        removeFOURTHRad(obj){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.no_request} dari ${this.form_igd.doctor_in_charge_name_4}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('radiology/removeRequest', obj.id)
                        .then(
                            async res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.radiology_FOURTH_list = await this.getRadiologyList(
                                    this.form_FOURTH_radiology.soap_id, 
                                    this.form_FOURTH_radiology.doctor_in_charge_id
                                )
                                this.getAssessment()
                                // this.socket.emit('SEND_RAD', []);                                
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        openFOURTHPresc() {
            this.resetFOURTHPresc()
            this.active_FOURTH.modal_presc = true
            if (this.form_FOURTH_prescription.soap_p_id == '') {
                this.validationFOURTHSoapP()
            }
        },

        resetFOURTHPresc() {
            this.form_FOURTH_prescription.id = ''
            this.form_FOURTH_prescription.form_prescription_parent = []
            this.form_FOURTH_prescription.form_prescription = []
            this.FOURTHParentStock = ''
            this.FOURTHCatalog = ''
        },

        openFOURTHPreParent(event) {
            if (event == null) return

            let index = this.form_FOURTH_prescription.form_prescription_parent.findIndex((item) => item.id == event.value);
            if (index !== -1) return;
            
            // kl bukan obat satuan, alias racik
            if (event.value != 1) {
                this.temp_FOURTH_parent.parent_id = event.value
                this.temp_FOURTH_parent.parent_name = event.title
                this.temp_FOURTH_parent.value = ''
                this.temp_FOURTH_parent.signatura_time = ''
                this.temp_FOURTH_parent.administer = ''
                this.temp_FOURTH_parent.desc = ''
                this.temp_FOURTH_parent.package = ''
                this.active.modal.presc_FOURTH_parent = true
                return
            }

            // obat satuan
            this.form_FOURTH_prescription.form_prescription_parent.push({
                id: event.value,
            })
        },

        closeFOURTHPreParent() {
            this.active.modal.presc_FOURTH_parent = false
            this.FOURTHParentStock = ''
            this.FOURTHCatalog = ''
        },

        async addFOURTHParent() {
            try {
                this.FOURTHdisabled.preParent = true
                this.form_FOURTH_prescription.form_prescription_parent.push(
                    {
                        id: this.temp_FOURTH_parent.parent_id,
                        status_kardex: 3,
                        value: this.temp_FOURTH_parent.value,
                        signatura_times_id: this.temp_FOURTH_parent.signatura_time,
                        administer_id: this.temp_FOURTH_parent.administer,
                        desc: this.temp_FOURTH_parent.desc,
                        package: this.temp_FOURTH_parent.package
                    }
                ) 
                this.active.modal.presc_FOURTH_parent = false
            } catch (error) {
                console.error('Error at: ', error)
            } finally {
                setTimeout(() => {
                    this.FOURTHdisabled.preItem = false
                }, 1000);
            }
        },

        onSearchFOURTHCatalog(search, loading) {
            if (search.length >= 3) {
                loading(true)
                const searchTerm = search.toLowerCase();
                this.optFOURTHCatalog = this.catalog_list.filter(item => {
                    return item?.data_catalog?.composition.toLowerCase().includes(searchTerm) ||
                    item?.title.toLowerCase().includes(searchTerm)
                });
                loading(false)
            }
        },

        async openFOURTHPreItem(event) {
            this.FOURTHCatalog = ''
            if (this.FOURTHParentStock == '' || this.FOURTHParentStock == null) return alert("Silakan Pilih Sediaan Terlebih Dahulu")

            this.temp_FOURTH_items.parent_id = this.FOURTHParentStock?.value
            this.temp_FOURTH_items.parent_name = this.FOURTHParentStock?.title
            this.temp_FOURTH_items.catalog_name = event?.data_catalog?.name
            this.temp_FOURTH_items.data_catalog = event?.data_catalog
            this.temp_FOURTH_items.signatura_dose = event?.data_catalog?.signatura_dose
            this.temp_FOURTH_items.signatura_unit = event?.data_catalog?.signatura_unit_id
            this.temp_FOURTH_items.signatura_time = event?.data_catalog?.signatura_times_id
            this.temp_FOURTH_items.administer = event?.data_catalog?.instruction_id || ''
            this.temp_FOURTH_items.desc = ''
            await this.getSignaturaUnits(event?.data_catalog?.package_id)

            this.active.modal.presc_FOURTH_items = true
        },

        async addFOURTHPrescItem() {
            try {
                this.FOURTHdisabled.preItem = true
                this.form_FOURTH_prescription.form_prescription.push({ 
                    pharmacy_catalog_id: this.temp_FOURTH_items?.data_catalog?.id, 
                    package_id: this.temp_FOURTH_items?.data_catalog?.package_id,
                    prescription_type_id: this.temp_FOURTH_items.parent_id,
                    value_package: 1,
                    dose: this.temp_FOURTH_items.signatura_dose,
                    dose_unit_id: this.temp_FOURTH_items.signatura_unit,
                    signatura_times_id: this.temp_FOURTH_items.signatura_time,
                    administer_id: this.temp_FOURTH_items.administer,
                    desc: this.temp_FOURTH_items.desc,
                    sort: this.form_FOURTH_prescription.form_prescription.length, 
                    status_kardex: this.temp_FOURTH_items.parent_id == 1 ? 1 : null,

                    name: this.temp_FOURTH_items?.data_catalog?.name,
                    dose_unit_options: this.optItemSignUnits                
                })
                this.active.modal.presc_FOURTH_items = false
            } catch (error) {
                console.error('Error at: ', error)
            } finally {
                setTimeout(() => {
                    this.FOURTHdisabled.preItem = false
                }, 1000);
            }
        },

        deleteFOURTHPresc (index) {
            this.form_FOURTH_prescription.form_prescription.splice(index, 1)
        },

        async validationFOURTHPresc() {
            try {
                this.FOURTHdisabled.prescription = true 
                this.form_FOURTH_prescription.pharmacy_depo_id ||= this.depo_emergency
                
                let inp_items = this.form_FOURTH_prescription?.form_prescription 
                let inp_parent = this.form_FOURTH_prescription?.form_prescription_parent 

                if (inp_parent?.length > 0 && inp_items?.length > 0) {
                    if(this.emptyChecking({
                        parents: inp_parent,
                        items: inp_items
                    })) {
                        const message = "SILAKAN LENGKAPI:\n\n" + this.combine_empty_found.map(item => `- ${item}`).join('\n');
                        alert(message);
                    } else {
                        await this.$store.dispatch('prescription/formPostPut', this.form_FOURTH_prescription)
                        .then(
                            async res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.prescription_FOURTH_list = await this.getPrescriptionList(
                                    this.form_FOURTH_prescription.soap_id, 
                                    this.form_FOURTH_prescription.doctor_in_charge_id
                                )
                                this.active_FOURTH.modal_presc = false
                                this.form_FOURTH_prescription.form_prescription_parent = []
                                this.form_FOURTH_prescription.form_prescription = []
                                this.FOURTHParentStock = ''
                                this.FOURTHCatalog = ''

                                this.getAssessment()
                                // this.socket.emit('SEND_PRS', []);
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    }
                }  
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.FOURTHdisabled.prescription = false
            }
        },

        editFOURTHPresc(obj) {
            this.resetFOURTHPresc()
            this.active_FOURTH.modal_presc = true

            this.form_FOURTH_prescription.id = obj.id
            this.form_FOURTH_prescription.soap_id = this.soap_id
            this.form_FOURTH_prescription.pharmacy_depo_id = obj.pharmacy_depo_id
            this.form_FOURTH_prescription.soap_p_id = obj.soap_p_id
            this.form_FOURTH_prescription.registration_destination_id = obj.registration_destination_id
            this.form_FOURTH_prescription.doctor_in_charge_id = obj.doctor_in_charge_id

            let var_parent = obj?.form_prescription_parent || []
            let var_presc = obj?.form_prescription || []

            if (var_parent.length > 0) {
                const mappedPrescParent = var_parent.map(item => {
                    if (item.id != 1) {
                        return {
                            id: item.id,
                            status_kardex: item.status_kardex ?? 3,                            
                            value: Number(item.value ?? 0),
                            package: Number(item.package),
                            signatura_times_id: item.signatura_times_id ?? '',
                            administer_id: item.administer_id ?? '',
                            desc: item.administer_id == this.other_administer ? item?.desc : '',
                        };
                    } else {
                        return {
                            id: item.id,
                        };
                    }

                });
                mappedPrescParent.sort((a, b) => a.id - b.id);                
                this.form_FOURTH_prescription.form_prescription_parent = mappedPrescParent;
            } else {
                this.form_FOURTH_prescription.form_prescription_parent = [{
                    id: 1,
                }]
            }

            if (var_presc.length > 0) {
                const mappedPresc = var_presc.map(item => {
                    let temp_sign = []
                    if (item?.dose_unit_options.length > 0) {
                        const mappedSign = item.dose_unit_options.map(item => {
                            return {
                                value: item.id,
                                title: item.name, 
                            };
                        });                
                        temp_sign = mappedSign
                    }

                    return {
                        pharmacy_catalog_id: item.pharmacy_catalog_id, 
                        package_id: item.package_id,
                        prescription_type_id: item.prescription_item_type_id,
                        value_package: Number(item.qty),
                        dose: item.dose,
                        dose_unit_id: item.dose_unit_id,
                        signatura_times_id: item.signatura_times_id,
                        administer_id: item.administer_id,
                        desc: item.administer_id == this.other_administer ? item?.desc : '',
                        sort: item.sort, 
                        status_kardex: item.status_kardex ?? '',

                        name: item.pharmacy_catalog_name,
                        dose_unit_options: temp_sign,
                    };
                });
                mappedPresc.sort((a, b) => a.sort - b.sort);                
                this.form_FOURTH_prescription.form_prescription = mappedPresc;                                  
            } else {
                this.form_FOURTH_prescription.form_prescription = [];                                  
            }
        },

        removeFOURTHPresc(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus order resep ${obj.no_order} dari ${this.form_igd.doctor_in_charge_name_4}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('prescription/removeOrder', obj.id)
                        .then(
                            async res => {
                                this.prescription_FOURTH_list = await this.getPrescriptionList(
                                    this.form_FOURTH_prescription.soap_id, 
                                    this.form_FOURTH_prescription.doctor_in_charge_id
                                )
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getAssessment()     
                                // this.socket.emit('SEND_PRS', []);
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        // SPECIALIST-DOCTOR 5
        async onFIFTHSearchIcd9cm(search, loading) {
            if(search.length >= 2) {
                loading(true);
                await this.$store.dispatch('icd9cm/getParam', search).then(data => {
                    this.OptionsFIFTHIcd9cm = []
                    for (let i = 0; i < data.length; i++) {
                        const el = data[i];
                        let temp = {
                            value: el.id,
                            title: `${el.code} ${el.name}`,
                        }
                        this.OptionsFIFTHIcd9cm.push(Object.assign({}, temp))
                    }
                    loading(false)
                })
            }
        },
        onFIFTHAdd9cm (event) {
            this.FIFTHicd9cm_id = ''
            if (this.form_FIFTH_icd9cm.icd9cm.some(item => item.icd9cm_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD9 yang sama')
            } else {
                this.form_FIFTH_icd9cm.icd9cm.push({ icd9cm_id:event.value, title:event.title, value:event.value, action: 'action'})
            }
        },
        deleteFIFTH9cm (index) {
            this.form_FIFTH_icd9cm.icd9cm.splice(index, 1)
        },

        async resetFIFTHSoapP() {
            this.form_FIFTH_icd9cm.id = ''
            this.form_FIFTH_icd9cm.therapy_plan = ''
            this.form_FIFTH_icd9cm.icd9cm = []
        },

        async validationFIFTHSoapP(){
            try {
                this.FIFTHdisabled.soap_p = true
                await this.$store.dispatch('soap/formPostPutSoapP', this.form_FIFTH_icd9cm)
                .then(
                    async res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        await this.getFIFTHSoapP(this.form_FIFTH_icd9cm.soap_id, this.form_FIFTH_icd9cm.doctor_in_charge_id)
                        this.getAssessment()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })                
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error);
            } finally {
                this.FIFTHdisabled.soap_p = false                    
            }
        },

        async getFIFTHSoapP(soap_id, doctor_in_charge_id){
            await this.resetFIFTHSoapP()
            try {
                await this.$store.dispatch('soap/getSoapP', {soap_id: soap_id, doctor_in_charge_id: doctor_in_charge_id })
                .then(
                    response => {
                        if (response.length != 0) {
                            const data = response[0]
                            if (data.icd9cm) {
                                const mappedIcd9cm = data.icd9cm.map(item => {
                                    return {
                                        icd9cm_id: item.id,
                                        value: item.id,
                                        title: `${item.code} ${item.name}`, 
                                        action: "action"
                                    };
                                });                
                                this.form_FIFTH_icd9cm.icd9cm = mappedIcd9cm;
                            }
                            this.form_FIFTH_icd9cm.id = data.id
                            this.form_FIFTH_icd9cm.therapy_plan = data.therapy_plan
                            this.form_FIFTH_prescription.soap_p_id = data.id
                        }
                    }
                )
            } catch (error) {
                console.error('Error at: ', error)
            }            
        },

        async resetFIFTHProcedure() {
            this.form_FIFTH_transaction.id = ''
            this.form_FIFTH_transaction.costing_id = ''
            this.form_FIFTH_transaction.var_1 = ''
            this.form_FIFTH_transaction.var_2 = 1
            this.form_FIFTH_transaction.var_3 = ''
            this.form_FIFTH_transaction.notes = ''
        },

        async openFIFTHProcedure() {
            await this.resetFIFTHProcedure()
            this.active_FIFTH.modal_procedure = true
            this.form_FIFTH_transaction.soap_id ||= this.form_igd.id
            this.form_FIFTH_transaction.doctor_in_charge_id ||= this.form_igd.doctor_in_charge_id
        },

        async onEditFIFTHProcedure(obj) {
            await this.openFIFTHProcedure()

            this.form_FIFTH_transaction.id = obj?.id
            this.form_FIFTH_transaction.costing_id = {
                value: obj?.costing_id,
                title: obj?.costing_name,
                costing: obj?.var_1
            }
            this.form_FIFTH_transaction.notes = obj?.notes
            this.form_FIFTH_transaction.var_1 = obj?.var_1
            this.form_FIFTH_transaction.var_2 = obj?.var_2
            this.form_FIFTH_transaction.var_3 = obj?.var_3
        },

        async validationFIFTHProcedure () {
            const quantity = Number(this.form_FIFTH_transaction?.var_2 || 0)
            const price = Number(this.form_FIFTH_transaction?.costing_id?.costing || 0)

            const formData = {
                id: this.form_FIFTH_transaction?.id,
                costing_id: this.form_FIFTH_transaction?.costing_id?.value || '',
                soap_id: this.form_FIFTH_transaction?.soap_id,
                notes: this.form_FIFTH_transaction?.notes,
                var_1: price,
                var_2: quantity,
                var_3: price*quantity,
                doctor_in_charge_id: this.form_FIFTH_transaction?.doctor_in_charge_id
            }

            if (Number.isInteger(quantity) && quantity > 0 && formData.costing_id != '' && formData.costing_id != null) {
                try {
                    this.FIFTHdisabled.transaction = true
                    await this.$store.dispatch('soap/postPutTransaction', formData)
                    .then(
                        async res => {
                            this.active_FIFTH.modal_procedure = false
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.transaction_FIFTH_list = await this.getTransactionList(
                                this.form_FIFTH_transaction.soap_id,
                                this.form_FIFTH_transaction.doctor_in_charge_id
                            );
                            await this.resetFIFTHProcedure()
                            this.getAssessment()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    console.error('Error catch at: ', error) 
                } finally {
                    this.FIFTHdisabled.transaction = false
                }
            } else {
                this.notification('Warning','CloseIcon','warning','Tindakan prosedur wajib diisi & jumlah minimal 1')
            }
        },

        removeFIFTHProcedure(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus prosedur ${obj.costing_name} dari ${this.form_igd.doctor_in_charge_name_5}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('soap/deleteTransaction', obj.id)
                        .then(
                            async res => {
                                this.transaction_FIFTH_list = await this.getTransactionList(
                                    this.form_FIFTH_transaction.soap_id,
                                    this.form_FIFTH_transaction.doctor_in_charge_id
                                );
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getAssessment()
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        async resetFIFTHLab(){
            this.form_FIFTH_laboratory.id = ''
            this.form_FIFTH_laboratory.date_request = moment(new Date()).format('YYYY-MM-DD HH:mm')
            this.form_FIFTH_laboratory.laboratory_request_items = []
        },

        async openFIFTHLab(){
            await this.resetFIFTHLab()
            this.active_FIFTH.modal_lab = true
            this.form_FIFTH_laboratory.soap_id ||= this.form_igd.id
            this.form_FIFTH_laboratory.doctor_in_charge_id ||= this.form_igd.doctor_in_charge_id
        },

        async onEditFIFTHLab(obj) {
            await this.openFIFTHLab()
            this.form_FIFTH_laboratory.id = obj?.id
            this.form_FIFTH_laboratory.date_request = obj?.date_request

            if (obj?.laboratory_request_item) {
                const mappedLab = obj.laboratory_request_item.map(item => {
                    return {
                        costing_name: item.costing_name,
                        laboratory_package_id: item.laboratory_package_id,
                        service_class_id: item.service_class_id
                    };
                });                
                this.form_FIFTH_laboratory.laboratory_request_items = mappedLab;
            } else {
                this.form_FIFTH_laboratory.laboratory_request_items = []
            }
        },

        async validationFIFTHLab(){
            try {
                this.FIFTHdisabled.laboratory = true
                await this.$store.dispatch('laboratory/formPostPut', this.form_FIFTH_laboratory)
                .then(
                    async response => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active_FIFTH.modal_lab = false
                        this.laboratory_FIFTH_list = await this.getLaboratoryList(
                            this.form_FIFTH_laboratory.soap_id, 
                            this.form_FIFTH_laboratory.doctor_in_charge_id
                        ) 
                        await this.resetFIFTHLab()
                        this.getAssessment()
                        // this.socket.emit('SEND_LAB', []);
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error);
            } finally {
                this.FIFTHdisabled.laboratory = false
            }
        },

        removeFIFTHLab(obj){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.no_request} dari ${this.form_igd.doctor_in_charge_name_5}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('laboratory/removeRequest', obj.id)
                        .then(
                            async res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.laboratory_FIFTH_list = await this.getLaboratoryList(
                                    this.form_FIFTH_laboratory.soap_id, 
                                    this.form_FIFTH_laboratory.doctor_in_charge_id
                                ) 
                                this.getAssessment()
                                // this.socket.emit('SEND_LAB', []);
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        async resetFIFTHRad(){
            this.form_FIFTH_radiology.id = ''
            this.form_FIFTH_radiology.date_request = moment(new Date()).format('YYYY-MM-DD HH:mm')
            this.form_FIFTH_radiology.radiology_request_items = []
            this.form_FIFTH_radiology.clinical_indications = ''
        },

        async openFIFTHRad(){
            await this.resetFIFTHRad()
            this.active_FIFTH.modal_rad = true
            this.form_FIFTH_radiology.soap_id ||= this.form_igd.id
            this.form_FIFTH_radiology.doctor_in_charge_id ||= this.form_igd.doctor_in_charge_id
        },

        async onEditFIFTHRad(obj) {
            await this.openFIFTHRad()
            this.form_FIFTH_radiology.id = obj.id
            this.form_FIFTH_radiology.date_request = obj.date_request

            this.form_FIFTH_radiology.clinical_indications = obj.clinical_indications || ''

            if (obj?.radiology_request_item) {
                const mappedRad = obj.radiology_request_item.map(item => {
                    return {
                        costing_name: item.costing_name,
                        radiology_package_id: item.radiology_package_id,
                        service_class_id: item.service_class_id
                    };
                });                
                this.form_FIFTH_radiology.radiology_request_items = mappedRad;
            } else {
                this.form_FIFTH_radiology.radiology_request_items = []
            }
        },

        async validationFIFTHRad(){
            try {
                this.FIFTHdisabled.radiology = true
                await this.$store.dispatch('radiology/formPostPut', this.form_FIFTH_radiology)
                .then(
                    async response => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active_FIFTH.modal_rad = false
                        this.radiology_FIFTH_list = await this.getRadiologyList(
                            this.form_FIFTH_radiology.soap_id, 
                            this.form_FIFTH_radiology.doctor_in_charge_id
                        )
                        await this.resetFIFTHRad()
                        this.getAssessment()
                        // this.socket.emit('SEND_LAB', []);
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error);
            } finally {
                this.FIFTHdisabled.radiology = false
            }
        },

        removeFIFTHRad(obj){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.no_request} dari ${this.form_igd.doctor_in_charge_name_5}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('radiology/removeRequest', obj.id)
                        .then(
                            async res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.radiology_FIFTH_list = await this.getRadiologyList(
                                    this.form_FIFTH_radiology.soap_id, 
                                    this.form_FIFTH_radiology.doctor_in_charge_id
                                )
                                this.getAssessment()
                                // this.socket.emit('SEND_RAD', []);                                
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        openFIFTHPresc() {
            this.resetFIFTHPresc()
            this.active_FIFTH.modal_presc = true
            if (this.form_FIFTH_prescription.soap_p_id == '') {
                this.validationFIFTHSoapP()
            }
        },

        resetFIFTHPresc() {
            this.form_FIFTH_prescription.id = ''
            this.form_FIFTH_prescription.form_prescription_parent = []
            this.form_FIFTH_prescription.form_prescription = []
            this.FIFTHParentStock = ''
            this.FIFTHCatalog = ''
        },

        openFIFTHPreParent(event) {
            if (event == null) return

            let index = this.form_FIFTH_prescription.form_prescription_parent.findIndex((item) => item.id == event.value);
            if (index !== -1) return;
            
            // kl bukan obat satuan, alias racik
            if (event.value != 1) {
                this.temp_FIFTH_parent.parent_id = event.value
                this.temp_FIFTH_parent.parent_name = event.title
                this.temp_FIFTH_parent.value = ''
                this.temp_FIFTH_parent.signatura_time = ''
                this.temp_FIFTH_parent.administer = ''
                this.temp_FIFTH_parent.desc = ''
                this.temp_FIFTH_parent.package = ''
                this.active.modal.presc_FIFTH_parent = true
                return
            }

            // obat satuan
            this.form_FIFTH_prescription.form_prescription_parent.push({
                id: event.value,
            })
        },

        closeFIFTHPreParent() {
            this.active.modal.presc_FIFTH_parent = false
            this.FIFTHParentStock = ''
            this.FIFTHCatalog = ''
        },

        async addFIFTHParent() {
            try {
                this.FIFTHdisabled.preParent = true
                this.form_FIFTH_prescription.form_prescription_parent.push(
                {
                    id: this.temp_FIFTH_parent.parent_id,
                    status_kardex: 3,
                    value: this.temp_FIFTH_parent.value,
                    signatura_times_id: this.temp_FIFTH_parent.signatura_time,
                    administer_id: this.temp_FIFTH_parent.administer,
                    desc: this.temp_FIFTH_parent.desc,
                    package: this.temp_FIFTH_parent.package
                }
            ) 
            this.active.modal.presc_FIFTH_parent = false
            } catch (error) {
                console.error('Error at: ', error)
            } finally {
                setTimeout(() => {
                    this.FIFTHdisabled.preParent = false
                }, 1000);
            }
        },

        onSearchFIFTHCatalog(search, loading) {
            if (search.length >= 3) {
                loading(true)
                const searchTerm = search.toLowerCase();
                this.optFIFTHCatalog = this.catalog_list.filter(item => {
                    return item?.data_catalog?.composition.toLowerCase().includes(searchTerm) ||
                    item?.title.toLowerCase().includes(searchTerm)
                });
                loading(false)
            }
        },

        async openFIFTHPreItem(event) {
            this.FIFTHCatalog = ''
            if (this.FIFTHParentStock == '' || this.FIFTHParentStock == null) return alert("Silakan Pilih Sediaan Terlebih Dahulu")

            this.temp_FIFTH_items.parent_id = this.FIFTHParentStock?.value
            this.temp_FIFTH_items.parent_name = this.FIFTHParentStock?.title
            this.temp_FIFTH_items.catalog_name = event?.data_catalog?.name
            this.temp_FIFTH_items.data_catalog = event?.data_catalog
            this.temp_FIFTH_items.signatura_dose = event?.data_catalog?.signatura_dose
            this.temp_FIFTH_items.signatura_unit = event?.data_catalog?.signatura_unit_id
            this.temp_FIFTH_items.signatura_time = event?.data_catalog?.signatura_times_id
            this.temp_FIFTH_items.administer = event?.data_catalog?.instruction_id || ''
            this.temp_FIFTH_items.desc = ''
            await this.getSignaturaUnits(event?.data_catalog?.package_id)

            this.active.modal.presc_FIFTH_items = true
        },

        async addFIFTHPrescItem() {
            try {
                this.FIFTHdisabled.preItem = true
                this.form_FIFTH_prescription.form_prescription.push({ 
                    pharmacy_catalog_id: this.temp_FIFTH_items?.data_catalog?.id, 
                    package_id: this.temp_FIFTH_items?.data_catalog?.package_id,
                    prescription_type_id: this.temp_FIFTH_items.parent_id,
                    value_package: 1,
                    dose: this.temp_FIFTH_items.signatura_dose,
                    dose_unit_id: this.temp_FIFTH_items.signatura_unit,
                    signatura_times_id: this.temp_FIFTH_items.signatura_time,
                    administer_id: this.temp_FIFTH_items.administer,
                    desc: this.temp_FIFTH_items.desc,
                    sort: this.form_FIFTH_prescription.form_prescription.length, 
                    status_kardex: this.temp_FIFTH_items.parent_id == 1 ? 1 : null,
    
                    name: this.temp_FIFTH_items?.data_catalog?.name,
                    dose_unit_options: this.optItemSignUnits                
                })
                this.active.modal.presc_FIFTH_items = false
            } catch (error) {
                console.error('Error at: ', error)
            } finally {
                setTimeout(() => {
                    this.FIFTHdisabled.preItem = false
                }, 1000);
            }
        },

        deleteFIFTHPresc (index) {
            this.form_FIFTH_prescription.form_prescription.splice(index, 1)
        },

        async validationFIFTHPresc() {
            try {
                this.FIFTHdisabled.prescription = true 
                this.form_FIFTH_prescription.pharmacy_depo_id ||= this.depo_emergency
                
                let inp_items = this.form_FIFTH_prescription?.form_prescription 
                let inp_parent = this.form_FIFTH_prescription?.form_prescription_parent 

                if (inp_parent?.length > 0 && inp_items?.length > 0) {
                    if(this.emptyChecking({
                        parents: inp_parent,
                        items: inp_items
                    })) {
                        const message = "SILAKAN LENGKAPI:\n\n" + this.combine_empty_found.map(item => `- ${item}`).join('\n');
                        alert(message);
                    } else {
                        await this.$store.dispatch('prescription/formPostPut', this.form_FIFTH_prescription)
                        .then(
                            async res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.prescription_FIFTH_list = await this.getPrescriptionList(
                                    this.form_FIFTH_prescription.soap_id, 
                                    this.form_FIFTH_prescription.doctor_in_charge_id
                                )
                                this.active_FIFTH.modal_presc = false
                                this.form_FIFTH_prescription.form_prescription_parent = []
                                this.form_FIFTH_prescription.form_prescription = []
                                this.FIFTHParentStock = ''
                                this.FIFTHCatalog = ''

                                this.getAssessment()
                                // this.socket.emit('SEND_PRS', []);
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    }
                }  
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.FIFTHdisabled.prescription = false
            }
        },

        editFIFTHPresc(obj) {
            this.resetFIFTHPresc()
            this.active_FIFTH.modal_presc = true

            this.form_FIFTH_prescription.id = obj.id
            this.form_FIFTH_prescription.soap_id = this.soap_id
            this.form_FIFTH_prescription.pharmacy_depo_id = obj.pharmacy_depo_id
            this.form_FIFTH_prescription.soap_p_id = obj.soap_p_id
            this.form_FIFTH_prescription.registration_destination_id = obj.registration_destination_id
            this.form_FIFTH_prescription.doctor_in_charge_id = obj.doctor_in_charge_id

            let var_parent = obj?.form_prescription_parent || []
            let var_presc = obj?.form_prescription || []

            if (var_parent.length > 0) {
                const mappedPrescParent = var_parent.map(item => {
                    if (item.id != 1) {
                        return {
                            id: item.id,
                            status_kardex: item.status_kardex ?? 3,                            
                            value: Number(item.value ?? 0),
                            package: Number(item.package),
                            signatura_times_id: item.signatura_times_id ?? '',
                            administer_id: item.administer_id ?? '',
                            desc: item.administer_id == this.other_administer ? item?.desc : '',
                        };
                    } else {
                        return {
                            id: item.id,
                        };
                    }

                });
                mappedPrescParent.sort((a, b) => a.id - b.id);                
                this.form_FIFTH_prescription.form_prescription_parent = mappedPrescParent;
            } else {
                this.form_FIFTH_prescription.form_prescription_parent = [{
                    id: 1,
                }]
            }

            if (var_presc.length > 0) {
                const mappedPresc = var_presc.map(item => {
                    let temp_sign = []
                    if (item?.dose_unit_options.length > 0) {
                        const mappedSign = item.dose_unit_options.map(item => {
                            return {
                                value: item.id,
                                title: item.name, 
                            };
                        });                
                        temp_sign = mappedSign
                    }

                    return {
                        pharmacy_catalog_id: item.pharmacy_catalog_id, 
                        package_id: item.package_id,
                        prescription_type_id: item.prescription_item_type_id,
                        value_package: Number(item.qty),
                        dose: item.dose,
                        dose_unit_id: item.dose_unit_id,
                        signatura_times_id: item.signatura_times_id,
                        administer_id: item.administer_id,
                        desc: item.administer_id == this.other_administer ? item?.desc : '',
                        sort: item.sort, 
                        status_kardex: item.status_kardex ?? '',

                        name: item.pharmacy_catalog_name,
                        dose_unit_options: temp_sign,
                    };
                });
                mappedPresc.sort((a, b) => a.sort - b.sort);                
                this.form_FIFTH_prescription.form_prescription = mappedPresc;                                  
            } else {
                this.form_FIFTH_prescription.form_prescription = [];                                  
            }
        },

        removeFIFTHPresc(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus order resep ${obj.no_order} dari ${this.form_igd.doctor_in_charge_name_5}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger-500', okTitle: 'YA, HAPUS', cancelTitle: 'BATAL', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(async value => {
                if (value === true){
                    try {
                        await this.$store.dispatch('prescription/removeOrder', obj.id)
                        .then(
                            async res => {
                                this.prescription_FIFTH_list = await this.getPrescriptionList(
                                    this.form_FIFTH_prescription.soap_id, 
                                    this.form_FIFTH_prescription.doctor_in_charge_id
                                )
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getAssessment()     
                                // this.socket.emit('SEND_PRS', []);
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },
    },
}
</script>