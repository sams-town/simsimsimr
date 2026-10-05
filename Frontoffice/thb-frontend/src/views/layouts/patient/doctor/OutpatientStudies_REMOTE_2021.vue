<template>
    <div>
        <!-- <b-button @click="openMainModal('main_gate')" pill variant="info-600" size="sm" class="px-16 ml-lg-2 mt-8 mt-lg-0">
            <b-icon icon="plus" /> <span>Tambah Kajian Kedokteran</span>
        </b-button> -->
        <b-modal v-model="active.modal.main" scrollable size="xl" title="Pengkajian Dokter" hide-footer>
            <div class="row position-relative">
                <section class="col">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-indigo-200">
                                <span class="text-bold-18">Data Dasar</span>
                            </div>
                            <div class="card-body">
                                <div class="row" style="font-size: 14px">
                                    <div class="col-4">
                                        <div class="font-weight-bold">Nama</div>
                                        <div>{{ regData.name }} ({{ parsed_age(regData.birth_date) }})</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="font-weight-bold">Tanggal Lahir</div>
                                        <div>{{ localDate(regData.birth_date) }}</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="font-weight-bold">No RM</div>
                                        <div>{{ regData.norm }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /row 1 -->
                    <section class="px-3 mt-20 mt-lg-32">
                        <div class="card">
                            <div class="card-header bg-indigo-200">
                                <span class="text-bold-18">Subjective</span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-3 d-flex">
                                        <label class="my-auto text-semi-14">Anamnesa</label>
                                    </div>
                                    <div class="col">
                                        <v-select v-model="form_soap.anamesa_type_id" aria-readonly="true" :options="OptionsAnamesaType" :reduce="OptionsAnamesaType => OptionsAnamesaType.value" label="title" placeholder="-- Pilih Anamnesa --"></v-select>
                                    </div>
                                </div>
                                <div class="row" v-if="form_soap.anamesa_type_id === 2">
                                    <div class="col-lg-6 px-0">
                                        <div class="row mx-0 mt-10">
                                            <div class="col-lg-6 d-lg-flex">
                                                <label class="my-auto text-semi-14">Nama Keluarga</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <b-form-input type="text" v-model="form_soap.family_name" autocomplete="off"></b-form-input>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 px-0">
                                        <div class="row mx-0 mt-10">
                                            <div class="col-lg-6 d-lg-flex">
                                                <label class="m-lg-auto text-semi-14">Hubungan Keluarga</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <v-select v-model="form_soap.family_relation_id" aria-readonly="true" :options="OptionsFamilyRelation" :reduce="OptionsFamilyRelation => OptionsFamilyRelation.value" label="title" placeholder="-- Pilih Hubungan Keluarga --"></v-select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-10">
                                    <div class="col-lg-3 d-flex">
                                        <label class="my-auto text-semi-14">Keluhan Utama</label>
                                    </div>
                                    <div class="col">
                                        <b-form-input type="text" v-model="form_soap.main_complaint" autocomplete="off"></b-form-input>
                                    </div>
                                </div>
                                <div class="row mt-10">
                                    <div class="col-lg-3 d-flex">
                                        <label class="text-semi-14">Riwayat Penyakit Sekarang</label>
                                    </div>
                                    <div class="col">
                                        <textarea class="form-control" rows="3" v-model.lazy="form_soap.history_disease_now" autocomplete="off"></textarea>
                                    </div>
                                </div>
                                <div class="row mt-10">
                                    <div class="col-lg-3 d-flex">
                                        <label class="text-semi-14">Riwayat Penyakit Dahulu</label>
                                    </div>
                                    <div class="col">
                                        <textarea class="form-control" rows="3" v-model.lazy="form_soap.history_disease_past" autocomplete="off"></textarea>
                                    </div>
                                </div>
                                <div class="row mt-10">
                                    <div class="col-lg-3 d-flex">
                                        <label class="text-semi-14">Riwayat Obat</label>
                                    </div>
                                    <div class="col">
                                        <textarea class="form-control" rows="3" v-model.lazy="form_soap.history_drugs" autocomplete="off"></textarea>
                                    </div>
                                </div>
                                <div class="row mt-24">
                                    <div class="col-lg-3 d-flex">
                                        <label class="text-semi-14">Riwayat Alergi</label>
                                    </div>
                                    <div class="col">
                                        <div>
                                            <b-button @click="openModalAllergy()" pill size="sm" variant="outline-secondary" class="px-3">Tambah riwayat alergi</b-button>

                                            <template v-if="form_soap.allergies.length > 0">
                                                <b-table :fields="allergyFields" :items="form_soap.allergies" responsive bordered class="mt-12" style="font-size: 13px">
                                                    <template #cell(no)="row">
                                                        {{ row.index+1 }}
                                                    </template>
                                                    <template #cell(type_allergy_id)="data">
                                                        <div v-if="data.value == 0">-</div>
                                                        <div v-if="data.value == 1">Obat</div>
                                                        <div v-if="data.value == 2">Makanan</div>
                                                    </template>
                                                    <template #cell(Aksi)="row">
                                                        <b-button variant="danger-600" size="sm" @click="removeAllergy(row.index)" class="mr-2">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </template>
                                                </b-table>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- /row 2 -->
                    <section class="px-3 mt-20 mt-lg-32">
                        <div class="card">
                            <div class="card-header bg-indigo-200">
                                <span class="text-bold-18">Objective</span>
                            </div>
                            <div class="card-body">
                                <div>
                                    <div class="col px-0">
                                        <ModernLabel label="Tanda Vital" />
                                        <div class="row mt-12">
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label class="c-form-label">Systolic</label>
                                                    <b-input-group append="mmHg">
                                                        <b-form-input type="number" autocomplete="off" v-model="form_soap.systolic_blood_pressure"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label class="c-form-label">Diastolic</label>
                                                    <b-input-group append="mmHg">
                                                        <b-form-input type="number" autocomplete="off" v-model="form_soap.diastolic_blood_pressure"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label class="c-form-label">Frekuensi Nadi</label>
                                                    <b-input-group append="x/mnt">
                                                        <b-form-input type="number" autocomplete="off" v-model="form_soap.pulse_frequency"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 border border-top-0 border-bottom-0">
                                                <div class="mt-1">
                                                    <label class="c-form-label">Frekuensi Nafas</label>
                                                    <b-input-group append="x/mnt">
                                                        <b-form-input type="number" autocomplete="off" v-model="form_soap.breath_frequency"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label class="c-form-label">Saturasi Oksigen</label>
                                                    <b-input-group append="%">
                                                        <b-form-input type="number" autocomplete="off" v-model="form_soap.oximetry_frequency"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-1">
                                                    <label class="c-form-label">Suhu</label>
                                                    <b-input-group append="ᵒC">
                                                        <b-form-input type="number" autocomplete="off" v-model="form_soap.temperature"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="mt-1">
                                                    <label class="c-form-label">Skor Nyeri</label>
                                                    <b-form-input autocomplete="off" v-model="form_soap.sore_score"></b-form-input>
                                                </div>
                                                <div class="mt-1">
                                                    <label class="c-form-label">EWS</label>
                                                    <b-form-input autocomplete="off" v-model="form_soap.ews"></b-form-input>
                                                </div>
                                                <div class="mt-1">
                                                    <label class="c-form-label">Gula Darah</label>
                                                    <b-input-group append="mg/dL">
                                                        <b-form-input type="number" autocomplete="off" v-model="form_soap.blood_sugar"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col mt-32 px-0">
                                        <ModernLabel label="Antropometri" />

                                        <div class="row mt-12" :class="active.advance.antopometri ? '' : 'mb-8'">
                                            <div class="col-lg-4">
                                                <label class="c-form-label">Berat Badan</label>
                                                <b-input-group append="kg">
                                                    <b-form-input type="number" autocomplete="off" @input="imtChanged($event)" v-model="form_soap.weight"></b-form-input>
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-4 mt-10 mt-lg-0">
                                                <label class="c-form-label">Tinggi Badan</label>
                                                <b-input-group append="cm">
                                                    <b-form-input type="number" autocomplete="off" @input="imtChanged($event)" v-model="form_soap.height"></b-form-input>
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-4 mt-10 mt-lg-0">
                                                <label class="c-form-label">IMT</label>
                                                <b-input-group append="kg/m²">
                                                    <b-form-input type="number" autocomplete="off" v-model="form_soap.imt"></b-form-input>
                                                </b-input-group>
                                            </div>
                                        </div>
                                        <b-collapse id="collapse-anto" v-model="active.advance.antopometri" class="mb-8">
                                            <div class="row">
                                                <div class="col-lg-4 mt-10">
                                                    <label class="c-form-label">Lingkar Kepala</label>
                                                    <b-input-group append="cm">
                                                        <b-form-input type="number" autocomplete="off" v-model="form_soap.head_circumference"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="col-lg-4 mt-10">
                                                    <label class="c-form-label">Lingkar Dada</label>
                                                    <b-input-group append="cm">
                                                        <b-form-input type="number" autocomplete="off" v-model="form_soap.chest_circumference"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="col-lg-4 mt-10">
                                                    <label class="c-form-label">Lingkar Lengan Atas</label>
                                                    <b-input-group append="cm">
                                                        <b-form-input type="number" autocomplete="off" v-model="form_soap.arm_circumference"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="col-lg-4 mt-10">
                                                    <label class="c-form-label">Lingkar Perut</label>
                                                    <b-input-group append="cm">
                                                        <b-form-input type="number" autocomplete="off" v-model="form_soap.stomach_circumference"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                                <div class="col-lg-4 mt-10">
                                                    <label class="c-form-label">Lingkar Lutut</label>
                                                    <b-input-group append="cm">
                                                        <b-form-input type="number" autocomplete="off" v-model="form_soap.knee_circumference"></b-form-input>
                                                    </b-input-group>
                                                </div>
                                            </div>
                                        </b-collapse>
                                        <small @click="active.advance.antopometri = !active.advance.antopometri" class="font-weight-bold text-primary pointer" :class="active.advance.antopometri ? null : 'collapsed'" :aria-expanded="active.advance.antopometri ? 'true' : 'false'" aria-controls="collapse-anto">
                                            Advance Antopometri 
                                            <b-icon :icon="active.advance.antopometri == true ? 'chevron-up' : 'chevron-down'" />
                                        </small>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Pemeriksaan Fisik</label>
                                        <textarea class="form-control" rows="6" v-model.lazy="form_soap.physical_check" autocomplete="off" ></textarea>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Lainnya</label>
                                        <textarea class="form-control" rows="2" v-model.lazy="form_soap.soap_o_anythingelse" autocomplete="off" ></textarea>
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
                                                            <v-select v-model="form_soap.head" :options="OptionsHeadCircumference" :reduce="OptionsHeadCircumference => OptionsHeadCircumference.value" label="title" placeholder="-- Pilih --"></v-select>
                                                        </div>
                                                        <div class="col-lg-3 mt-12">
                                                            <label class="c-form-label">Warna Rambut</label>
                                                            <v-select v-model="form_soap.head_hair_color" :options="OptionsHeadHairColor" :reduce="OptionsHeadHairColor => OptionsHeadHairColor.value" label="title" placeholder="-- Pilih --"></v-select>
                                                        </div>
                                                        <div class="col-lg-3 mt-12">
                                                            <label class="c-form-label">Kondisi Rambut</label>
                                                            <v-select v-model="form_soap.head_hair_condition" :options="OptionsHeadHairCondition" :reduce="OptionsHeadHairCondition => OptionsHeadHairCondition.value" label="title" placeholder="-- Pilih --"></v-select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-32">
                                                    <ModernLabel label="Mata" />
                                                    <div class="row">
                                                        <div class="col-lg-3 mt-12">
                                                            <label class="c-form-label">Conjunctiva</label>
                                                            <v-select v-model="form_soap.eye_conjunctive" :options="OptionsEyeConjunctiva" :reduce="OptionsEyeConjunctiva => OptionsEyeConjunctiva.value" label="title" placeholder="-- Pilih --"></v-select>
                                                        </div>
                                                        <div class="col-lg-3 mt-12">
                                                            <label class="c-form-label">Sklera</label>
                                                            <v-select v-model="form_soap.eye_sclera" :options="OptionsEyeSclera" :reduce="OptionsEyeSclera => OptionsEyeSclera.value" label="title" placeholder="-- Pilih --"></v-select>
                                                        </div>
                                                        <div class="col-lg-3 mt-12">
                                                            <label class="c-form-label">Pupil</label>
                                                            <v-select v-model="form_soap.eye_pupil" :options="OptionsEyePupil" :reduce="OptionsEyePupil => OptionsEyePupil.value" label="title" placeholder="-- Pilih --"></v-select>
                                                        </div>
                                                        <div class="col-lg-3 mt-12">
                                                            <label class="c-form-label">Reflek Cahaya</label>
                                                            <v-select v-model="form_soap.eye_light_reflex" :options="OptionsEyeReflect" :reduce="OptionsEyeReflect => OptionsEyeReflect.value" label="title" placeholder="-- Pilih --"></v-select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <b-collapse id="collapse-general" v-model="active.advance.generalis">
                                                    <div class="mt-32">
                                                        <ModernLabel label="Mulut" />
                                                        <div class="row">
                                                            <div class="col-lg-3 mt-12">
                                                                <label class="c-form-label">Mukosa</label>
                                                                <v-select v-model="form_soap.mouth_mucosa" :options="OptionsMouthMukosa" :reduce="OptionsMouthMukosa => OptionsMouthMukosa.value" label="title" placeholder="-- Pilih --"></v-select>
                                                            </div>
                                                            <div class="col-lg-3 mt-12">
                                                                <label class="c-form-label">Perubahan Warna</label>
                                                                <v-select v-model="form_soap.mouth_discoloration" :options="OptionsMouthHiperemis" :reduce="OptionsMouthHiperemis => OptionsMouthHiperemis.value" label="title" placeholder="-- Pilih --"></v-select>
                                                            </div>
                                                            <div class="col-lg-3 mt-12">
                                                                <label class="c-form-label">Lidah</label>
                                                                <v-select v-model="form_soap.mouth_tongue" :options="OptionsMouthTongue" :reduce="OptionsMouthTongue => OptionsMouthTongue.value" label="title" placeholder="-- Pilih --"></v-select>
                                                            </div>
                                                            <div class="col-lg-3 mt-12">
                                                                <label class="c-form-label">Karies</label>
                                                                <v-select v-model="form_soap.mouth_caries" :options="OptionsMouthKaries" :reduce="OptionsMouthKaries => OptionsMouthKaries.value" label="title" placeholder="-- Pilih --"></v-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-32">
                                                        <ModernLabel label="Leher" />
                                                        <div class="row">
                                                            <div class="col-lg-3 mt-12">
                                                                <label class="c-form-label">KGB</label>
                                                                <v-select v-model="form_soap.neck_kgb" :options="OptionsNeckKGB" :reduce="OptionsNeckKGB => OptionsNeckKGB.value" label="title" placeholder="-- Pilih --"></v-select>
                                                            </div>
                                                            <div class="col-lg-3 mt-12">
                                                                <label class="c-form-label">Posisi Trakea</label>
                                                                <v-select v-model="form_soap.neck_trachea_position" :options="OptionsNeckTrakea" :reduce="OptionsNeckTrakea => OptionsNeckTrakea.value" label="title" placeholder="-- Pilih --"></v-select>
                                                            </div>
                                                            <div class="col-lg-3 mt-12">
                                                                <label class="c-form-label">Massa</label>
                                                                <v-select v-model="form_soap.neck_massa" :options="OptionsNeckMassa" :reduce="OptionsNeckMassa => OptionsNeckMassa.value" label="title" placeholder="-- Pilih --"></v-select>
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
                                                                    <v-select v-model="form_soap.thorax_insp_chest" :options="OptionsInspectionChest" :reduce="OptionsInspectionChest => OptionsInspectionChest.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                </div>
                                                                <div class="col-lg-3 mt-12 mt-lg-0">
                                                                    <label class="c-form-label">Retraksi</label>
                                                                    <v-select v-model="form_soap.thorax_insp_retraction" :options="OptionsInspectionRetraction" :reduce="OptionsInspectionRetraction => OptionsInspectionRetraction.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-24">
                                                            <div class="font-weight-bold" style="font-size: 14px">PALPASI</div>
                                                            <div class="row">
                                                                <div class="col-lg-3 mt-12 mt-lg-0">
                                                                    <label class="c-form-label">Dada</label>
                                                                    <v-select v-model="form_soap.thorax_palp_chest" :options="OptionsPalpationChest" :reduce="OptionsPalpationChest => OptionsPalpationChest.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                </div>
                                                                <div class="col-lg-3 mt-12 mt-lg-0">
                                                                    <label class="c-form-label">Fremitus</label>
                                                                    <v-select v-model="form_soap.thorax_palp_fremitus" :options="OptionsPalpationFremitus" :reduce="OptionsPalpationFremitus => OptionsPalpationFremitus.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                </div>
                                                                <div class="col-lg-3 mt-12 mt-lg-0">
                                                                    <label class="c-form-label">Krepitasi</label>
                                                                    <v-select v-model="form_soap.thorax_palp_crepitation" :options="OptionsPalpationKrepitasi" :reduce="OptionsPalpationKrepitasi => OptionsPalpationKrepitasi.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-32">
                                                            <div class="font-weight-bold" style="font-size: 14px">AUSKULTASI</div>
                                                            <div class="row">
                                                                <div class="col-lg-3 mt-12 mt-lg-0">
                                                                    <label class="c-form-label">Pulmo Vasikuler</label>
                                                                    <v-select v-model="form_soap.thorax_ausk_pul_vasiculer" :options="OptionsAuskultationPulmoVasikuler" :reduce="OptionsAuskultationPulmoVasikuler => OptionsAuskultationPulmoVasikuler.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                </div>
                                                                <div class="col-lg-3 mt-12 mt-lg-0">
                                                                    <label class="c-form-label">Pulmo Rhonki</label>
                                                                    <v-select v-model="form_soap.thorax_ausk_pul_rhonki" :options="OptionsAuskultationPulmoRhonki" :reduce="OptionsAuskultationPulmoRhonki => OptionsAuskultationPulmoRhonki.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                </div>
                                                                <div class="col-lg-3 mt-12 mt-lg-0">
                                                                    <label class="c-form-label">Pulmo Wheezing</label>
                                                                    <v-select v-model="form_soap.thorax_ausk_pul_wheezing" :options="OptionsAuskultationPulmoWheezing" :reduce="OptionsAuskultationPulmoWheezing => OptionsAuskultationPulmoWheezing.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-3 mt-12">
                                                                    <label class="c-form-label">COR Bunyi Jantung</label>
                                                                    <v-select v-model="form_soap.thorax_ausk_cor_heart_sound" :options="OptionsAuskultationCORBJ" :reduce="OptionsAuskultationCORBJ => OptionsAuskultationCORBJ.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                </div>
                                                                <div class="col-lg-3 mt-12">
                                                                    <label class="c-form-label">COR Murmur</label>
                                                                    <v-select v-model="form_soap.thorax_ausk_cor_murmur" :options="OptionsAuskultationCORMurmur" :reduce="OptionsAuskultationCORMurmur => OptionsAuskultationCORMurmur.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                </div>
                                                                <div class="col-lg-3 mt-12">
                                                                    <label class="c-form-label">COR Gallop</label>
                                                                    <v-select v-model="form_soap.thorax_ausk_cor_gallop" :options="OptionsAuskultationCORGallop" :reduce="OptionsAuskultationCORGallop => OptionsAuskultationCORGallop.value" label="title" placeholder="-- Pilih --"></v-select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-32">
                                                        <ModernLabel label="Abdomen" />
                                                        <div class="row">
                                                            <div class="col-lg-3 mt-12">
                                                                <label class="c-form-label">Supel</label>
                                                                <v-select v-model="form_soap.abdomen_supel" :options="OptionsAbdomenSupel" :reduce="OptionsAbdomenSupel => OptionsAbdomenSupel.value" label="title" placeholder="-- Pilih --"></v-select>
                                                            </div>
                                                            <div class="col-lg-3 mt-12">
                                                                <label class="c-form-label">Bising Usus</label>
                                                                <v-select v-model="form_soap.abdomen_bowel_sounds" :options="OptionsAbdomenBising" :reduce="OptionsAbdomenBising => OptionsAbdomenBising.value" label="title" placeholder="-- Pilih --"></v-select>
                                                            </div>
                                                            <div class="col-lg-3 mt-12">
                                                                <label class="c-form-label">Nyeri Tekan</label>
                                                                <v-select v-model="form_soap.abdomen_tenderness" :options="OptionsAbdomenPress" :reduce="OptionsAbdomenPress => OptionsAbdomenPress.value" label="title" placeholder="-- Pilih --"></v-select>
                                                            </div>
                                                            <div class="col-lg-3 mt-12">
                                                                <label class="c-form-label">Hepar & Lien</label>
                                                                <v-select v-model="form_soap.abdomen_hepar_lien" :options="OptionsAbdomenHepar" :reduce="OptionsAbdomenHepar => OptionsAbdomenHepar.value" label="title" placeholder="-- Pilih --"></v-select>
                                                            </div>
                                                            <div class="col-lg mt-12">
                                                                <label class="c-form-label">Pemeriksaan Abdomen Lainnya</label>
                                                                <textarea class="form-control" v-model="form_soap.abdomen_other_physical_check" rows="1" autocomplete="off" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-32">
                                                        <ModernLabel label="Ekstrimitas" />
                                                        <div class="row">
                                                            <div class="col-lg-3 mt-12">
                                                                <label class="c-form-label">Akral</label>
                                                                <v-select v-model="form_soap.extremity_acral" :options="OptionsEkstremitasAkral" :reduce="OptionsEkstremitasAkral => OptionsEkstremitasAkral.value" label="title" placeholder="-- Pilih --"></v-select>
                                                            </div>
                                                            <div class="col-lg-3 mt-12">
                                                                <label class="c-form-label">CRT</label>
                                                                <v-select v-model="form_soap.extremity_crt" :options="OptionsEkstremitasCRT" :reduce="OptionsEkstremitasCRT => OptionsEkstremitasCRT.value" label="title" placeholder="-- Pilih --"></v-select>
                                                            </div>
                                                            <div class="col-lg-3 mt-12">
                                                                <label class="c-form-label">Edema</label>
                                                                <v-select v-model="form_soap.extremity_edema" :options="OptionsEkstremitasEdema" :reduce="OptionsEkstremitasEdema => OptionsEkstremitasEdema.value" label="title" placeholder="-- Pilih --"></v-select>
                                                            </div>
                                                        </div>
                                                    </div>
    
                                                    <div class="mt-32">
                                                        <ModernLabel label="Genitalia" />
                                                        <div class="mt-12 mb-8">
                                                            <textarea class="form-control" v-model.lazy="form_soap.genitalia" rows="2" autocomplete="off" placeholder="(Tuliskan pemeriksaan genitalia)" />
                                                        </div>
                                                    </div>
                                                    <div class="mt-32">
                                                        <ModernLabel label="Status Lokalis" />
                                                        <div class="mt-12 mb-8">
                                                            <textarea class="form-control" v-model.lazy="form_soap.localist_status" rows="2" autocomplete="off" placeholder="(Tuliskan status lokalis di kolom ini secara lengkap)" />
                                                        </div>                                                    
                                                    </div>
                                                    <div class="mt-32">
                                                        <ModernLabel label="Status oftalmologis" />
                                                        <div class="mt-12 mb-8">
                                                            <textarea class="form-control" v-model.lazy="form_soap.ophthal_status" rows="2" autocomplete="off" />
                                                        </div>
                                                    </div>
                                                </b-collapse>

                                                <small @click="active.advance.generalis = !active.advance.generalis" class="font-weight-bold text-primary pointer mt-12" :class="active.advance.generalis ? null : 'collapsed'" :aria-expanded="active.advance.generalis ? 'true' : 'false'" aria-controls="collapse-general">
                                                    Advance status generalis
                                                    <b-icon :icon="active.advance.generalis == true ? 'chevron-up' : 'chevron-down'" />
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="mt-32">
                                    <ModernLabel label="Actions" />
                                    
                                    <div class="row mx-0 mt-20">
                                        <div class="px-2 my-1 my-lg-0">
                                            <b-button pill variant="outline-indigo" size="sm" class="px-3">Pemeriksaan Mata
                                                (Canvassing)</b-button>
                                        </div>
                                        <div class="px-2 my-1 my-lg-0">
                                            <b-button pill variant="outline-indigo" size="sm" class="px-3">Pemeriksaan Gigi
                                                (Canvassing)</b-button>
                                        </div>
                                        <div class="px-2 my-1 my-lg-0">
                                            <b-button pill variant="outline-indigo" size="sm" class="px-3">Pemeriksaan Fisik (Canvassing)</b-button>
                                            <CanvasContainer />
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </section>

                    <section class="px-3 mt-20 mt-lg-32">
                        <div class="card">
                            <div class="card-header bg-indigo-200">
                                <span class="text-bold-18">Assessment</span>
                            </div>
                            <div class="card-body">
                                <div class="w-100">
                                    <ModernLabel label="Diagnosa (ICD10)" />
                                    <template v-if="form_soap.icd10.length > 0">
                                        <b-table responsive bordered :fields="icd10Fields" :items="form_soap.icd10" class="mt-12 text-center" style="font-size: 14px;" >
                                            <template #cell(no)="row">{{ row.index+1 }}</template>
                                            <template #cell(title)="row">
                                                <div class="text-left">{{ row.value }}</div>
                                            </template>
                                            <template #cell(action)="row">
                                                <b-button variant="danger-600" size="sm" @click="delete10(row.index)" class="mr-2">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </template>
                                        </b-table>
                                    </template>                                    
                                    <v-select v-model="icd10_id" @input="onAdd10($event)" aria-readonly="true" @search="onSearchIcd10" :options="OptionsIcd10" :reduce="OptionsIcd10 => OptionsIcd10" label="title" placeholder="-- Diagnosa --" class="mt-12"></v-select>
                                </div>

                                <b-textarea v-model="form_soap.icd10_notes" autocomplete="off" placeholder="Catatan ICD10" class="mt-12" />

                                <div class="mt-24" >
                                    <ModernLabel label="Diagnosis Non-ICD10" />
                                    <b-table-simple bordered responsive class="mt-12" style="font-size: 14px">
                                        <b-tr>
                                            <b-th style="width: 60px;">No</b-th>
                                            <b-th>Non-ICD10</b-th>
                                            <b-th style="width: 100px;">Aksi</b-th>
                                        </b-tr>
                                        <template v-for="(item,index) in form_soap.non_icd10">
                                            <b-tr class="item">
                                                <b-td>{{ form_soap.icd10.length+index+1 }}</b-td>
                                                <b-td>
                                                    <div>{{ item }}</div>
                                                </b-td>
                                                <b-td>
                                                    <b-button variant="danger-600" size="sm" @click="removeOther10(index)" class="mr-2">
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

                                <div class="mt-12">
                                    <label class="text-semi-14">Assessment Lainnya</label>
                                    <b-textarea v-model="form_soap.soap_a_anythingelse" type="text" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="px-3 mt-20 mt-lg-32">
                        <div class="card">
                            <div class="card-header bg-indigo-200">
                                <span class="text-bold-18">Plan</span>
                            </div>
                            <div class="card-body">
                                <section class="w-100">
                                    <ModernLabel label="Tindakan Dokter" />
                                    <template v-if="form_soap.transaction_doctor.length > 0">
                                        <b-table responsive bordered :fields="transactionFields" :items="form_soap.transaction_doctor" class="mt-12 text-center" style="font-size: 14px;" >
                                            <template #cell(no)="row">{{ row.index+1 }}</template>
                                            <template #cell(title)="row">
                                                <div class="text-left">{{ row.item.costing_name }}</div>
                                                <v-select v-if="row.item.is_ref===1" v-model="row.item.doctor_ref_id" aria-readonly="true" :options="OptionsDoctorRef" :reduce="OptionsDoctorRef => OptionsDoctorRef.value" label="title" placeholder="-- Pilih Dokter --"></v-select>
                                            </template>
                                            <template #cell(var_2)="row">
                                                <b-form-input type="number" v-model="row.item.var_2" aria-readonly="true" />
                                            </template>
                                            <template #cell(action)="row">
                                                <b-button variant="danger-600" size="sm" @click="deleteSC(row.index)" class="mr-2">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </template>
                                        </b-table>
                                    </template>
                                    <v-select v-model="costing_id" @input="onAddSC($event)" aria-readonly="true" :options="OptionsCosting" :reduce="OptionsCosting => OptionsCosting" label="title" placeholder="-- Pemeriksaan & Tindakan --" class="mt-12"></v-select>                                    
                                </section>

                                <section class="mt-32" >
                                    <ModernLabel label="Tindakan (ICD9CM)" />
                                    <b-textarea v-model="form_soap.therapy_plan" autocomplete="off" placeholder="Catatan ICD9CM" class="mt-12" />
                                    <template v-if="form_soap.icd9cm.length > 0">
                                        <b-table responsive bordered :fields="icd9cmFields" :items="form_soap.icd9cm" class="mt-12 text-center" style="font-size: 14px;" >
                                            <template #cell(no)="row">{{ row.index+1 }}</template>
                                            <template #cell(title)="row">
                                                <div class="text-left">{{ row.value }}</div>
                                            </template>
                                            <template #cell(action)="row">
                                                <b-button variant="danger-600" size="sm" @click="delete9cm(row.index)" class="mr-2">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </template>
                                        </b-table>
                                    </template>
                                    <v-select v-model="icd9cm_id" @input="onAdd9cm($event)" aria-readonly="true" @search="onSearchIcd9cm" :options="OptionsIcd9cm" :reduce="OptionsIcd9cm => OptionsIcd9cm" label="title" placeholder="-- ICD 9 --" class="mt-12"></v-select>
                                </section>

                                <section class="mt-32">
                                    <ModernLabel label="Penunjang" />

                                    <b-card no-body class="mt-12">
                                        <b-tabs card>
                                            <b-tab title="Laboratorium" active>
                                                <section :class="lab_status != 1 ? 'bg-warning-100 pb-20': ''">
                                                    <div v-if="lab_status != 1" class="card-body bg-warning mb-16">
                                                        <div class="d-flex align-items-center">
                                                            <b-icon-exclamation-triangle-fill variant="neutral-800" style="width: 24px; height: 24px;"/>
                                                            <div class="ml-3 font-italic">Form penunjang laboratorium tidak dapat diubah karena sudah diproses oleh laboratorium</div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-12">
                                                        <b-form-input type="text" v-model="searchlab" placeholder="Pencarian"></b-form-input>
                                                        <div class="col-lg-12 mt-3">
                                                            <h5>Checked Items:</h5>
                                                            <b-form-checkbox-group v-model="form_soap.laboratory_request_items">
                                                                <b-form-checkbox
                                                                v-for="(item, index) in form_soap.laboratory_request_items"
                                                                :key="index"
                                                                :value="item"
                                                                :disabled="lab_status != 1"
                                                                >
                                                                {{ item.costing_name }}
                                                                </b-form-checkbox>
                                                            </b-form-checkbox-group>
                                                        </div>
                                                        <div class="mt-12 py-2 bg-neutral-100" style="height: 400px; overflow-y: scroll;">
                                                            <b-form-checkbox-group class="col-lg-12" v-model="form_soap.laboratory_request_items" :disabled="lab_status != 1">
                                                                <div v-for="(item, index) in filteredListLab" :key="index" :class="index != 0 ? 'mt-32' : 'mt-0'">
                                                                    <div class="font-weight-bold text-uppercase">{{ item.category_name }}</div>
                                                                    <div class="row mx-0"> 
                                                                        <b-form-checkbox v-for="(list, packIndex) in item.category_items" v-bind:key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'laboratory_package_id':list.id, 'costing_name':list.costing_name}" class="col-lg-5">{{list.costing_name }}</b-form-checkbox>
                                                                    </div>
                                                                </div>
                                                            </b-form-checkbox-group>
                                                        </div>
                                                        <!-- <div class="mt-20">
                                                            <label class="c-form-label">Indikasi Klinis Laboratorium</label>
                                                            <textarea class="form-control" rows="3" v-model.lazy="form_soap.laboratory_clinical_indications" autocomplete="off" />
                                                        </div> -->
                                                    </div>
                                                </section>
                                            </b-tab>

                                            <b-tab title="Radiologi">
                                                 <section :class="rad_status != 1 ? 'bg-warning-100 pb-20': ''">
                                                    <div v-if="rad_status != 1" class="card-body bg-warning mb-16">
                                                        <div class="d-flex align-items-center">
                                                            <b-icon-exclamation-triangle-fill variant="neutral-800" style="width: 24px; height: 24px;"/>
                                                            <div class="ml-3 font-italic">Form penunjang radiologi tidak dapat diubah karena sudah diproses oleh radiologi</div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-12">
                                                        <b-form-input type="text" v-model="searchrad" placeholder="Pencarian"></b-form-input>
                                                        <div class="col-lg-12 mt-3">
                                                            <h5>Checked Items:</h5>
                                                            <b-form-checkbox-group v-model="form_soap.radiology_request_items">
                                                                <b-form-checkbox
                                                                v-for="(item, index) in form_soap.radiology_request_items"
                                                                :key="index"
                                                                :value="item"
                                                                :disabled="rad_status != 1"
                                                                >
                                                                {{ item.costing_name }}
                                                                </b-form-checkbox>
                                                            </b-form-checkbox-group>
                                                        </div>
                                                        <div class="mt-12 py-2 bg-neutral-100" style="height: 400px; overflow-y: scroll;">
                                                            <b-form-checkbox-group class="col-lg-12" v-model="form_soap.radiology_request_items" :disabled="rad_status != 1">
                                                                <div v-for="(item, index) in filteredListRad" :key="index" :class="index != 0 ? 'mt-32' : 'mt-0'">
                                                                    <div class="font-weight-bold text-uppercase">{{ item.category_name }}</div>
                                                                    <div class="row mx-0"> 
                                                                        <b-form-checkbox v-for="(list, packIndex) in item.category_items" v-bind:key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'radiology_package_id':list.id, 'costing_name':list.costing_name}" class="col-lg-5">{{list.costing_name }}</b-form-checkbox>
                                                                    </div>
                                                                </div>
                                                            </b-form-checkbox-group>
                                                        </div>
                                                        <!-- <div class="mt-20">
                                                            <label class="c-form-label">Indikasi Klinis Radiologi</label>
                                                            <textarea class="form-control" rows="3" v-model.lazy="form_soap.radiology_clinical_indications" autocomplete="off" />
                                                        </div> -->
                                                    </div>
                                                </section>
                                            </b-tab>

                                            <b-tab title="Fisioterapi">
                                                <b-form-input type="text" v-model="searchphy" placeholder="Pencarian"></b-form-input>
                                                <div class="col-lg-12 mt-3">
                                                    <h5>Checked Items:</h5>
                                                    <b-form-checkbox-group v-model="form_soap.physiotherapy_request_items">
                                                        <b-form-checkbox
                                                        v-for="(item, index) in form_soap.physiotherapy_request_items"
                                                        :key="index"
                                                        :value="item"
                                                        >
                                                        {{ item.costing_name }}
                                                        </b-form-checkbox>
                                                    </b-form-checkbox-group>
                                                </div>
                                                <div class="mt-12 py-2 bg-neutral-100" style="height: 400px; overflow-y: scroll;">
                                                    <b-form-checkbox-group class="col-lg-12" v-model="form_soap.physiotherapy_request_items">
                                                        <b-form-checkbox v-for="(ip, index) in filteredListPhy" v-bind:key="index" :value="{'service_class_id':ip.service_class_id,'physiotherapy_package_id':ip.id, 'costing_name': ip.costing_name}" class="col-lg-5">{{ip.costing_name }}</b-form-checkbox>
                                                    </b-form-checkbox-group>
                                                </div>
                                            </b-tab>
                                        </b-tabs>
                                    </b-card>
                                </section>
                            </div>
                        </div>
                    </section>

                    <section v-if="presc_status == 0" class="px-3 mt-20 mt-lg-32">
                        <div class="card">
                            <div class="card-header bg-indigo-200">
                                <span class="text-bold-18">Rencana Terapi E-Resep</span>
                            </div>
                            <div class="card-body" :class="presc_status == 1 ? 'bg-warning-100' : ''">
                                <div v-if="presc_status == 1" class="card-body bg-warning">
                                    <div class="d-flex align-items-center">
                                        <b-icon-exclamation-triangle-fill variant="neutral-800" style="width: 24px; height: 24px;"/>
                                        <div class="ml-3 font-italic">Form resep tidak dapat diubah karena resep obat sudah diproses oleh farmasi.</div>
                                    </div>
                                </div>
                                <div>
                                    <div class="row mt-10 px-0">
                                        <div class="col">
                                            <table class="table">
                                                <thead class="text-center text-bold-14">
                                                    <tr>
                                                        <th rowspan="2">
                                                            <!-- <b-form-checkbox /> -->
                                                        </th>
                                                        <th rowspan="2" style="min-width: 300px;">NAMA BARANG</th>
                                                        <th colspan="4">ESTIMASI ORDER</th>
                                                    </tr>
                                                    <tr>
                                                        <th style="min-width: 50px;">SEDIAAN OBAT</th>
                                                        <th style="min-width: 50px;">DOSIS DOKTER</th>
                                                        <th style="min-width: 50px;">JUMLAH</th>
                                                        <th style="min-width: 140px;">KEMASAN</th>
                                                        <th style="min-width: 50px;">KETERANGAN&ATURAN PAKAI</th>
                                                    </tr>
                                                </thead>
                                                <template v-for="(item_p, index_p) in form_soap.form_prescription_parent">
                                                    <tr>
                                                        <td>
                                                            <label><b>{{ item_p.prescription }}</b></label>
                                                        </td>
                                                        <td v-if="item_p.id != 1">
                                                            <b-input-group append="Jumlah">
                                                                <b-form-input v-model="item_p.value" :disabled="presc_status == 1" style="width:150px" type="number" />
                                                            </b-input-group>
                                                            <b-input-group append="Aturan Pakai">
                                                                <b-form-input v-model="item_p.used" :disabled="presc_status == 1" style="width:150px" type="text"  />
                                                            </b-input-group>
                                                        </td>
                                                    </tr>
                                                    <tr class="item" v-for="(item, index) in form_soap.form_prescription" v-if="item_p.id == item.prescription_type_id">
                                                        <td>
                                                            <b-button @click="deletePRES(index)" :disabled="presc_status == 1" variant="danger-600" size="sm" class="mr-2">
                                                                <feather-icon icon="TrashIcon" />
                                                            </b-button>
                                                        </td>
                                                        <td>{{ item.name }}</td>
                                                        <td>
                                                            <b-form-input disabled style="width:100px" type="text" v-model="item.sum_dose" />
                                                        </td>
                                                        <td>
                                                            <b-form-input v-model="item.value_doctor" @change="vdChanged($event, index)" :disabled="presc_status == 1" :readonly="item.prescription_type_id == 1" style="width:100px" type="number"  />
                                                        </td>
                                                        <td>
                                                            <b-form-input :disabled="presc_status == 1" style="width:100px" type="number" v-model="item.value_package" />
                                                        </td>
                                                        <td>
                                                            <v-select v-model="item.package_id" :disabled="presc_status == 1" :options="PackageOptions" :reduce="PackageOptions => PackageOptions.value" label="title" placeholder="Kemasan"></v-select>
                                                        </td>
                                                        <td>
                                                            <b-input-group append="Aturan Pakai" v-if="item.prescription_type_id === 1">
                                                                <b-form-input v-model="item.used" :disabled="presc_status == 1" style="width:150px" type="text" />
                                                            </b-input-group>
                                                            <b-input-group append="Keterangan">
                                                                <b-form-input v-model="item.desc" :disabled="presc_status == 1" style="width:150px" type="text" />
                                                            </b-input-group>
                                                        </td>
                                                    </tr>
                                                </template>
                                                <tr>
                                                    <td></td>
                                                    <td colspan="2">
                                                        <!-- <template v-if="presc_status == 0 || typeof presc_obj == 'object'"> -->
                                                        <template v-if="presc_status == 0">
                                                            <v-select v-model="prescription" @input="onAddPRES($event)" :options="PrescriptionOptions" :reduce="PrescriptionOptions => PrescriptionOptions" label="title" placeholder="-- Racikan --"></v-select>
                                                            <v-select v-model="catalog" @input="onAddCAT($event)" @search="onSearchCatalog" :options="OptionsCatalog" :reduce="CatalogOptions => CatalogOptions" label="title" placeholder="-- Pilih Katalog --"></v-select>
                                                        </template>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                         
                    </section>

                    <section v-if="presc_status == 1" class="px-3 mt-32">
                        <div class="card">
                            <div class="card-header bg-indigo-200">
                                <span class="text-bold-18">Rencana Terapi E-Resep</span>
                            </div>
                            <div class="card-body">
                                <b-button @click="openModalExcPresc" pill variant="outline-neutral-400" size="sm" class="px-16">
                                    <b-icon icon="plus" /> <span>Tambah Resep</span>
                                </b-button>

                                <b-table :fields="precriptionFields" :items="exclusive_presc_list" :tbody-tr-class="rowIGDPrescription" bordered responsive class="mt-12" style="font-size: 14px;">
                                    <template #cell(no)="row">
                                        <div>{{ row.index+1 }}</div>
                                    </template>
                                    <template #cell(form_prescription)="row">
                                        <!-- <div v-for="(category, index) in PrescriptionOptions" :key="index" class="mt-8" v-if="hasItemsInCategory(row.value, category.value)"> -->
                                        <div v-for="(category, index) in row.item.form_prescription_parent" :key="index" class="mt-8" v-if="hasItemsInCategory(row.value, category.id)">
                                            <b-table-simple responsive small style="font-size: 13px;">
                                                <b-thead>
                                                    <b-tr>
                                                        <!-- <b-th colspan="5" class="text-uppercase bg-success-100">{{ category.title }}</b-th> -->
                                                        <b-th colspan="5" class="bg-success-100">
                                                            <div class="text-uppercase ">{{ getParentName(category.id) }}</div>
                                                            <div v-if="category.id != 1" class="text-italic" style="font-size: 12px; font-weight: 400">  
                                                                <span>Jumlah: {{ category.value }}, </span>
                                                                <span>Aturan pakai: {{ category.used }}</span>
                                                            </div>
                                                        </b-th>
                                                    </b-tr>
                                                    <b-tr>
                                                        <b-th>Nama</b-th>
                                                        <b-th style="width: 100px;">Dosis Dokter</b-th>
                                                        <b-th style="width: 100px;">Jumlah</b-th>
                                                        <b-th style="width: 100px;">Aturan Pakai</b-th>
                                                        <b-th style="width: 200px;">Keterangan</b-th>
                                                    </b-tr>
                                                </b-thead>
                                                <b-tbody>
                                                    <template v-for="(item, itemIndex) in row.value">
                                                        <b-tr :key="itemIndex" v-if="item.prescription_item_type_id == category.id">
                                                            <b-td>{{ item.pharmacy_catalog_name }}</b-td>
                                                            <b-td>{{ item.qty_doctor }}</b-td>
                                                            <b-td>{{ item.qty }} {{ item.package_name }}</b-td>
                                                            <b-td>{{ item.used }}</b-td>
                                                            <b-td>{{ item.desc }}</b-td>
                                                        </b-tr>
                                                    </template>
                                                </b-tbody>
                                            </b-table-simple>
                                        </div>
                                    </template>
                                    
                                    <template #cell(action)="row">
                                        <div v-if="row.item.flag == 0" class="d-flex justify-content-center">
                                            <b-button @click="removeExcPresc(row.item)" size="sm" variant="danger-700">
                                                <feather-icon icon="TrashIcon" />
                                            </b-button>
                                            <b-button @click="onEditPresc(row.item)" size="sm" variant="warning-500" class="ml-8">
                                                <feather-icon icon="EditIcon" />
                                            </b-button>
                                        </div>
                                        <div v-else>Resep telah diproses</div>
                                    </template>
                                </b-table>
                            </div>
                        </div>
                    </section> 

                    <section class="col mt-24">
                        <div>
                            <label class="c-form-label">Advice</label>
                            <textarea class="form-control" rows="3" v-model.lazy="form_soap.advice" autocomplete="off" />
                        </div>
                    </section>

                    <section v-if="regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e' || form_soap.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e'" class="col mt-24">
                        <ModernLabel label="Hemodialisa" class="mb-8" />

                        <div class="row">
                            <div class="col-lg-4">
                                <label class="c-form-label">Diagnosis Utama</label>
                                <v-select v-model="form_soap.main_diagnosis" :options="OptionsDiagnose" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                            </div>
                            <div class="col-lg-4 mt-1 mt-lg-0">
                                <label class="c-form-label">Etiologi</label>
                                <v-select v-model="form_soap.etiology" :options="OptionsEtiology" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                            </div>
                            <div class="col-lg-4 mt-1 mt-lg-0">
                                <label class="c-form-label">Penyakit Penyerta</label>
                                <v-select v-model="form_soap.comorbidities" :options="OptionsComorbidities" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                            </div>
                            <div class="col-lg-4 mt-1">
                                <label class="c-form-label">Frekuensi HD</label>
                                <b-input-group append="x/minggu">
                                    <b-form-input v-model="form_soap.frequency_hd" type="number" />
                                </b-input-group>
                            </div>
                        </div>
                        <!-- <div class="mt-1">
                            <label class="c-form-label">Resep HD</label>
                            <b-textarea v-model="form_soap.prescription_hd" />
                        </div> -->
                        <div class="mt-1">
                            <label class="c-form-label">Akses Vaskular</label>
                            <b-textarea v-model="form_soap.vascular_access" />
                        </div>
                    </section>

                    <section v-if="regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a' || form_soap.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a'" class="col mt-24">
                        <ModernLabel label="UJI FUNGSI/PROSEDUR KFR" class="mb-8" />

                        <div class="row">
                            <!-- <div class="col-lg-4">
                                <label class="c-form-label">Tanggal Pelayanan</label>
                                <b-form-input v-model="form_soap.service_date" type="date" />
                            </div> -->
                            <div class="col-lg-8 mt-10 mt-lg-0">
                                <label class="c-form-label">Suspek Penyakit Akibat</label>
                                <div class="row">
                                    <v-select v-model="form_soap.suspected_disease_type_id" :options="OptionsSuspected" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="col-lg-6"></v-select>
                                    <div class="col-lg-6 mt-10 mt-lg-0">
                                        <b-form-input v-model="form_soap.suspected_disease" placeholder="Suspek Penyakit Akibat" v-if="form_soap.suspected_disease_type_id == '1'" />
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-12 mt-10">
                                <label class="c-form-label">Anamnesa</label>
                                <b-textarea v-model="form_soap.anamnesa" />
                            </div> -->
                            <div class="col-12 mt-10">
                                <label class="c-form-label">Pemeriksaan Fisik & Uji Fungsi</label>
                                <b-textarea v-model="form_soap.functional_testing" />
                            </div>
                            <!-- <div class="col-12 mt-10">
                                <label class="c-form-label">Pemeriksaan Penunjang</label>
                                <b-textarea v-model="form_soap.support_examination" />
                            </div> -->
                            <!-- <div class="col-12 mt-10">
                                <label class="c-form-label">Anjuran</label>
                                <b-textarea v-model="form_soap.advice_rehab" />
                            </div> -->
                            <div class="col-12 mt-10">
                                <label class="c-form-label">Diagnosa Fungsional</label>
                                <b-textarea v-model="form_soap.functional_diagnosis" />
                            </div>
                            <div class="col-12 mt-10">
                                <label class="c-form-label">Diagnosa Medis</label>
                                <b-textarea v-model="form_soap.medical_diagnosis" />
                            </div>
                            <!-- <div class="col-12 mt-10">
                                <label class="c-form-label">Evaluasi</label>
                                <b-textarea v-model="form_soap.evaluation" />
                            </div> -->
                            <div class="col-12 mt-10">
                                <label class="c-form-label">Hasil Yang Didapat</label>
                                <b-textarea v-model="form_soap.result" />
                            </div>
                            <div class="col-12 mt-10">
                                <label class="c-form-label">Kesimpulan</label>
                                <b-textarea v-model="form_soap.conclusion" />
                            </div>
                            <div class="col-12 mt-10">
                                <label class="c-form-label">Rekomendasi</label>
                                <b-textarea v-model="form_soap.recommendation" />
                            </div>
                        </div>
                    </section>

                    <section class="px-3 mt-20 mt-lg-32">
                        <div class="row mx-0 text-center text-lg-left">
                            <div class="col-12 col-lg-2">
                                <!-- <img src="@/@core/assets/images/nurse/img-doctor.jpg" alt="your-photo" -->
                                <img src="../../../../@core/assets/images/nurse/img-doctor.jpg" alt="your-photo"
                                    class="rounded-circle shadow-sm c-img">
                            </div>
                            <div class="col-12 col-lg px-0 px-lg-3 mt-16 mt-lg-0 d-flex">
                                <div class="col d-flex flex-column justify-content-center">
                                    <div>
                                        <p class="text-regular-12 text-neutral-400">Nama Dokter</p>
                                        <p class="text-bold-24">{{this.regData.doctor_in_charge_name}}</p>
                                    </div>
                                    <div class="row mt-8 mt-lg-16">
                                        <div class="col-lg-3">
                                            <p class="text-regular-12 text-neutral-400">Username</p>
                                            <p class="text-semi-16">{{userData.employee_name}}</p>
                                        </div>
                                        <div class="col-lg-3 mt-8 mt-lg-0">
                                            <p class="text-regular-12 text-neutral-400">Tanggal & Jam Input</p>
                                            <p class="text-semi-16">{{dateModal}}</p>
                                        </div>
                                        <div class="col-lg-4 mt-8 mt-lg-0">
                                            <p class="text-regular-12 text-neutral-400">Poli</p>
                                            <p class="text-semi-16">{{regData.health_facility_item_name}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <div class="px-3 mt-40 d-flex justify-content-lg-between">
                        <b-button pill size="sm" variant="outline-neutral-500" class="px-3">Tutup</b-button>
                        <b-button pill size="sm" variant="indigo" class="px-3" :disabled="isDisabled"  @click.prevent="validationForm">
                            <!-- <i><img src="@/@core/assets/icons/nurse/save.svg" alt="save" class="mr-1"></i> -->
                            <feather-icon icon="SaveIcon" class="mr-1" />
                            <span>Simpan</span>
                        </b-button>
                    </div>
                </section>
                <section v-if="this.soap_list && this.soap_list.length > 0" class="col-lg-3 card position-sticky" :style="{top: '0', height: '100vh', overflowY: 'auto'}">
                    <div class="card-header row text-center font-weight-bold mb-16 justify-content-center">PROFIL RINGKAS MEDIS RAWAT JALAN</div>
                    <div v-for="(item, index) in this.soap_list" class="accordion" role="tablist">
                        <b-card no-body class="mb-1">
                            <b-card-header block v-b-toggle="'accordion-'+index" header-tag="header" class="pointer d-flex justify-content-between align-items-center" role="tab" onMouseOver="this.style.backgroundColor='#648DE1'; this.style.color='#FFFFFF'" onMouseOut="this.style.backgroundColor='#FFFFFF'; this.style.color='#212529'" >
                                <div>
                                    <div class="font-weight-bold">Kajian {{ localDateTime(item.date_added) }}</div>
                                    <div>{{item.employee_doctor_name}} | {{ item.health_facility_item_name ? item.health_facility_item_name : '-' }}</div>
                                </div>
                                <feather-icon icon="ChevronDownIcon" size="18" />
                            </b-card-header>
                            <b-collapse :id="`accordion-`+index" accordion="my-accordion" role="tabpanel">
                                <b-card-body>
                                    <!-- <ModernLabel :label="item.employee_doctor_name" /> -->
                                    <b-button @click="copyAllStudies($event, item)" variant="primary" class="btn-block mt-16">Copy All</b-button>
                                    <div class="card mt-16">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between">
                                                <div class="font-weight-bold">Subjective</div>
                                                <b-button @click="copySelected($event, copySubjective, item)" size="sm" variant="primary">Copy</b-button>
                                            </div>
                                        </div>
                                        <div class="card-body" style="font-size: 14px">
                                            <div>
                                                <div><b>Anamnesa: </b>{{ item.anamesa_type_id == '1' ? 'Auto Anamnesa' : 'Allo Anamnesa' }}</div>
                                                <div><b>Keluhan Utama: </b>{{ item.main_complaint ? item.main_complaint : '-' }}</div>
                                                <div><b>Nama Keluarga: </b>{{ item.family_name ? item.family_name : '-' }}</div>
                                                <div><b>Hubungan Keluarga: </b>{{ item.family_relation_id ? getRelation(item.family_relation_id) : '-' }}</div>
                                                <div><b>Riwayat Penyakit Sekarang: </b>{{ item.history_disease_now ? item.history_disease_now : '-' }}</div>
                                                <div><b>Riwayat Penyakit Dahulu: </b>{{ item.history_disease_past ? item.history_disease_past : '-' }}</div>
                                                <div><b>Riwayat Obat: </b>{{ item.history_drugs ? item.history_drugs : '-' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card mt-16">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between">
                                                <div class="font-weight-bold">Objective</div>
                                                <!-- <b-button @click="copySelected($event, copyObjective, item)" size="sm" variant="primary">Copy</b-button> -->
                                            </div>
                                        </div>
                                        <div class="card-body" style="font-size: 14px">
                                            <div>
                                                <b>Systolic: </b>{{ item.systolic_blood_pressure }},
                                                <b>Diastolic: </b>{{ item.diastolic_blood_pressure }},
                                                <b>Frekuensi Nadi: </b>{{ item.pulse_frequency }},
                                                <b>Frekuensi Nafas: </b>{{ item.breath_frequency }},
                                                <b>Saturasi Oksigen: </b>{{ item.oximetry_frequency }},
                                                <b>Suhu: </b>{{ item.temperature }},
                                                <b>Gula Darah: </b>{{ item.blood_sugar }},
                                            </div>
                                            <div class="mt-16">
                                                <b>Berat Badan: </b>{{ item.weight }},
                                                <b>Tinggi Badan: </b>{{ item.height }},
                                                <b>IMT: </b>{{ item.imt }},
                                                <b>Lingkar Kepala: </b>{{ item.head_circumference }},
                                                <b>Lingkar Dada: </b>{{ item.chest_circumference }},
                                                <b>Lingkar Lengan Atas: </b>{{ item.arm_circumference }},
                                                <b>Lingkar Perut: </b>{{ item.stomach_circumference }},
                                                <b>Lingkar Lutut: </b>{{ item.knee_circumference }},
                                                <b>Lainnya: </b>{{ item.soap_o_anythingelse ? item.soap_o_anythingelse : '-' }}
                                            </div>
                                            <div class="mt-16"><b>Pemeriksaan Fisik: </b> {{ item.physical_check ? item.physical_check : '-' }}</div>
                                            <div><b>Lainnya: </b> {{ item.soap_o_anythingelse ? item.soap_o_anythingelse : '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="card mt-16">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between">
                                                <div class="font-weight-bold">Assesment</div>
                                                <b-button @click="copySelected($event, copyAssesment, item)" size="sm" variant="primary">Copy</b-button>
                                            </div>
                                        </div>
                                        <div class="card-body" style="font-size: 14px">
                                            <div><b>Diagnosa (ICD10): </b><span v-if="!item.icd10 || item.icd10.length == 0">-</span></div>
                                            <template v-if="item.icd10 && item.icd10.length > 0">
                                                <li v-for="icd10 in item.icd10">{{ icd10.code }} {{ icd10.name }}</li>
                                            </template>
                                            <div><b>Catatan ICD10 : </b>{{ item.icd10_notes ? item.icd10_notes : '-' }}</div>
                                            <div><b>Lainnya : </b>{{ item.soap_a_anythingelse ? item.soap_a_anythingelse : '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="card mt-16">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between">
                                                <div class="font-weight-bold">Plan</div>
                                                <b-button @click="copySelected($event, copyPlan, item)" size="sm" variant="primary">Copy</b-button>
                                            </div>
                                        </div>
                                        <div class="card-body" style="font-size: 14px">
                                            <div>
                                                <div>
                                                    <b>Pemeriksaan: </b>
                                                    <span v-if="!item.transaction_doctor || item.transaction_doctor.length == 0">-</span>
                                                </div>
                                                <template v-if="item.transaction_doctor && item.transaction_doctor.length > 0">
                                                    <li v-for="trans_list in item.transaction_doctor">{{ trans_list.costing_name }}</li>
                                                </template>
                                            </div>
                                            <div class="mt-16">
                                                <div>
                                                    <div>
                                                        <b>Tindakan (ICD9): </b>
                                                        <span v-if="!item.icd9cm || item.icd9cm.length == 0">-</span>
                                                    </div>
                                                    <template v-if="item.icd9cm && item.icd9cm.length > 0">
                                                        <li v-for="icd9 in item.icd9cm">{{ icd9.code }} {{ icd9.name }}</li>
                                                    </template>
                                                </div>
                                                <div><b>Catatan ICD9CM : </b>{{ item.therapy_plan ? item.therapy_plan : '-' }}</div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card mt-16">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between">
                                                <div class="font-weight-bold">Penunjang</div>
                                                <!-- <b-button @click="copySelected($event, copyPenunjang, item)" size="sm" variant="primary">Copy</b-button> -->
                                            </div>
                                        </div>
                                        <div class="card-body" style="font-size: 14px">
                                            <div>
                                                <div>
                                                    <b>Laboratorium: </b>
                                                    <span v-if="!item.laboratory_request_items || item.laboratory_request_items.length == 0">-</span>
                                                </div>
                                                <template v-if="item.laboratory_request_items && item.laboratory_request_items.length > 0">
                                                    <li v-for="lab_list in item.laboratory_request_items">{{ lab_list.costing_name }}</li>
                                                </template>
                                            </div>
                                            <div class="mt-16">
                                                <div>
                                                    <b>Radiologi: </b>
                                                    <span v-if="!item.radiology_request_items || item.radiology_request_items.length == 0">-</span>
                                                </div>
                                                <template v-if="item.radiology_request_items && item.radiology_request_items.length > 0">
                                                    <li v-for="rad_list in item.radiology_request_items">{{ rad_list.costing_name }}</li>
                                                </template>
                                            </div>
                                            <div class="mt-16">
                                                <div>
                                                    <b>Fisioterapi: </b>
                                                    <span v-if="!item.physiotherapy_request || item.physiotherapy_request.length == 0">-</span>
                                                </div>
                                                <template v-if="item.physiotherapy_request && item.physiotherapy_request.length > 0">
                                                    <div v-for="rad_list in item.physiotherapy_request">
                                                        <li v-for="el in rad_list.physiotherapy_request_items">{{ el.costing_name }}</li>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <template v-if="item.prescription_new && item.prescription_new.length > 0">
                                        <div v-for="(list, j) in item.prescription_new" class="card mt-16">
                                            <div class="card-header">
                                                <div class="d-flex justify-content-between">
                                                    <div class="font-weight-bold">Resep {{ j+1 }}</div>
                                                    <b-button v-if="presc_status == 0" @click="copySelected($event, copyPrescription, list)" size="sm" variant="primary">Copy</b-button>
                                                </div>
                                            </div>
                                            <div class="card-body" style="font-size: 14px">
                                                <template v-if="list.form_prescription && list.form_prescription.length > 0">
                                                    <li v-for="iip in list.form_prescription">
                                                        {{ iip.pharmacy_catalog_name }}, {{ iip.qty }} {{ iip.package_name }}, {{ iip.used }}, {{ iip.desc }}
                                                    </li>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </b-card-body>
                            </b-collapse>
                        </b-card>
                    </div>
                </section>
            </div>

        </b-modal>

        <b-modal v-model="active.modal.allergy" scrollable size="lg" title="Riwayat Alergi">
            <div class="row mb-72">
                <div class="col-3 mt-12">
                    <label class="c-form-label">Jenis Alergi</label>
                    <v-select v-model="allergyInput.type_allergy_id" :options="OptionsTypeAllergy" :reduce="OptionsTypeAllergy => OptionsTypeAllergy.value" label="title" placeholder="--Pilih Jenis Alergi --"></v-select>
                </div>
                <div class="col-3 mt-12">
                    <label class="c-form-label">Alergi *</label>
                    <b-form-input autocomplete="off" type="text" v-model="allergyInput.name" required></b-form-input>
                </div>
                <div class="col-3 mt-12">
                    <label class="c-form-label">Reaksi</label>
                    <b-form-input autocomplete="off" type="text" v-model="allergyInput.reaction"></b-form-input>
                </div>
                <div class="col-3 mt-12">
                    <label class="c-form-label">Tingkat Keparahan</label>
                    <b-form-input autocomplete="off" type="text" v-model="allergyInput.severity"></b-form-input>
                </div>
                <div class="col-3 mt-12">
                    <label class="c-form-label">Efek Samping</label>
                    <b-form-input autocomplete="off" type="text" v-model="allergyInput.side_effect"></b-form-input>
                </div>
                <div class="col-3 mt-12">
                    <label class="c-form-label">Tanggal Kejadian</label>
                    <b-form-input autocomplete="off" type="date" v-model="allergyInput.date_tragedy"></b-form-input>
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click.prevent="addAllergy" variant="primary" class="px-3">Simpan</b-button>
                </div>
            </template>                
        </b-modal>

        <b-modal v-model="active.modal.exc_presc" size="xl" title="Order Resep" hide-footer no-close-on-backdrop>
            <table class="table">
                <thead class="text-center text-bold-14">
                    <tr>
                        <th rowspan="2">
                            <!-- <b-form-checkbox /> -->
                        </th>
                        <th rowspan="2" style="min-width: 300px;">NAMA BARANG</th>
                        <th colspan="4">ESTIMASI ORDER</th>
                    </tr>
                    <tr>
                        <th style="min-width: 50px;">SEDIAAN OBAT</th>
                        <th style="min-width: 50px;">DOSIS DOKTER</th>
                        <th style="min-width: 50px;">JUMLAH</th>
                        <th style="min-width: 140px;">KEMASAN</th>
                        <th style="min-width: 50px;">KETERANGAN&ATURAN PAKAI</th>
                    </tr>
                </thead>
                <template v-for="(item_p, index_p) in form_exclusive_presc.form_prescription_parent">
                    <tr>
                        <td>
                            <label><b>{{ item_p.prescription }}</b></label>
                        </td>
                        <td v-if="item_p.id !== 1">
                            <b-input-group append="Jumlah">
                                <b-form-input v-model="item_p.value" style="width:150px" type="number" />
                            </b-input-group>
                            <b-input-group append="Aturan Pakai">
                                <b-form-input v-model="item_p.used" style="width:150px" type="text"  />
                            </b-input-group>
                        </td>
                    </tr>
                    <tr class="item" v-for="(item, index) in form_exclusive_presc.form_prescription" v-if="item_p.id === item.prescription_type_id">
                        <td>
                            <b-button @click="deleteEXCPRES(index)" variant="danger-600" size="sm" class="mr-2">
                                <feather-icon icon="TrashIcon" />
                            </b-button>
                        </td>
                        <td>{{ item.name }}</td>
                        <td>
                            <b-form-input disabled style="width:100px" type="text" v-model="item.sum_dose" />
                        </td>
                        <td>
                            <b-form-input v-model="item.value_doctor" :readonly="item.prescription_type_id == 1" style="width:100px" type="number"  />
                        </td>
                        <td>
                            <b-form-input style="width:100px" type="number" v-model="item.value_package" />
                        </td>
                        <td>
                            <v-select v-model="item.package_id" :options="PackageOptions" :reduce="PackageOptions => PackageOptions.value" label="title" placeholder="Kemasan"></v-select>
                        </td>
                        <td>
                            <b-input-group append="Aturan Pakai" v-if="item.prescription_type_id === 1">
                                <b-form-input v-model="item.used" style="width:150px" type="text" />
                            </b-input-group>
                            <b-input-group append="Keterangan">
                                <b-form-input v-model="item.desc" style="width:150px" type="text" />
                            </b-input-group>
                        </td>
                    </tr>
                </template>
                <tr>
                    <td></td>
                    <td colspan="2">
                        <template>
                            <v-select v-model="exc_prescription" @input="onAddEXCPRES($event)" :options="PrescriptionOptions" :reduce="PrescriptionOptions => PrescriptionOptions" label="title" placeholder="-- Racikan --"></v-select>
                            <v-select v-model="exc_catalog" @input="onAddEXCCAT($event)" @search="onSearchEXCCatalog" :options="ExcOptionsCatalog" :reduce="ExcOptionsCatalog => ExcOptionsCatalog" label="title" placeholder="-- Pilih Katalog --"></v-select>
                        </template>
                    </td>
                </tr>
            </table>
            <div class="modal-footer">
                <b-button @click="validationExcPrescription" :disabled="active.disabled.ecx_presc" variant="primary">Simpan</b-button>
            </div>
            <div style="height: 40vh;"></div>
        </b-modal>
        
    </div>
</template>

<script>
import { mapState } from 'vuex'
import moment from 'moment'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import CanvasContainer from '@/views/pages/canvasing/CanvasContainer.vue';
import vSelect from 'vue-select'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
// import io from 'socket.io-client';

export default {
    components: { ModernLabel, CanvasContainer, vSelect },
    data() {
        return {
            // socket : io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"],withCredentials: true }),
            dateModal:moment(new Date()).format('YYYY/MM/DD HH:MM'),
            userData: JSON.parse(localStorage.getItem('userData')),
            reg_des_id: '',
            globalNorm: '',
            regData: {},
            soap_list: [],
            isDisabled: false,
            active: {
                modal: {
                    main: false,
                    allergy: false,
                    exc_presc: false,
                },
                advance: {
                    antopometri: false,
                    generalis: false,
                },
                disabled: {
                    ecx_presc: false,
                }
            },
            allergyFields: [
                { key: 'no', label: 'No' },
                { key: 'name', label: 'Alergi' },
                { key: 'type_allergy_id', label: 'Jenis Alergi' },
                { key: 'reaction', label: 'Reaksi' },
                { key: 'severity', label: 'Tingkat Keparahan' },
                { key: 'side_effect', label: 'Efek Samping' },
                { key: 'date_tragedy', label: 'Tgl Kejadian' },
                { key: 'Aksi' },
            ],
            allergyInput: {
                type_allergy_id: 0,
                name: '',
                reaction: '',
                severity: '',
                side_effect: '',
                date_tragedy: '',
            },
            transactionFields: [
                { key: 'no', label: 'No', thStyle: 'width: 60px' },
                { key: 'title', label: 'Nama Tindakan', thClass: 'text-left' },
                { key: 'var_2', label: 'Jumlah', thStyle: 'width: 120px' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px' },
            ],
            icd10Fields: [
                { key: 'no', label: 'No', thStyle: 'width: 60px' },
                { key: 'title', label: 'ICD10', thClass: 'text-left' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px' },
            ],
            icd9cmFields: [
                { key: 'no', label: 'No', thStyle: 'width: 60px' },
                { key: 'title', label: 'ICD9CM', thClass: 'text-left' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px' },
            ],
            precriptionFields: [
                { key: 'no', label: 'No', thStyle: 'width: 60px' },
                { key: 'no_order', label: 'Nomor Order', thStyle: 'width: 200px' },
                { key: 'form_prescription', label: 'Resep' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 140px' },
            ],
            OptionsAnamesaType:[
                { value: 1, title: 'Auto Anamnesa' },
                { value: 2, title: 'Allo Anamnesa' }
            ],
            OptionsFamilyRelation:[
                { value: 1, title: 'Ayah' },
                { value: 2, title: 'Ibu' },
                { value: 3, title: 'Kakak' },
                { value: 4, title: 'Adik' },
            ],
            // OptionsPregnant:[
            //     { value: 1, title: 'Tunggal' },
            //     { value: 2, title: 'Kembar' }
            // ],
            // OptionsSex:[
            //     { value: 1, title: 'Laki-Laki' },
            //     { value: 0, title: 'Perempuan' }
            // ],
            OptionsSizeUSG:[
                { value: 1, title: 'BPD' },
                { value: 2, title: 'GA' },
                { value: 3, title: 'EDD' },
                { value: 4, title: 'AC' }
            ],
            PrescriptionOptions:[
                { value: 1, title: 'OBAT SATUAN' },
                { value: 2, title: 'RACIK 1' },
                { value: 3, title: 'RACIK 2' },
                { value: 4, title: 'RACIK 3' },
                { value: 5, title: 'RACIK 4' }
            ],
            OptionsSuspected:[
                { value: 0, title: 'Tidak' },
                { value: 1, title: 'Ya' }
            ],
            OptionsDiagnose: [],
            OptionsEtiology: [],
            OptionsComorbidities: [],

            form_soap: {
                id:'', 
                doctor_in_charge_id: '',
                registration_destination_id: '',
                anamesa_type_id: 1, 
                family_relation_id: '',
                family_name: '',
                main_complaint: '',
                history_disease_now: '',
                history_disease_past: '',
                history_drugs: '',
                allergies: [],
                systolic_blood_pressure: '',
                diastolic_blood_pressure: '',
                pulse_frequency: '',
                breath_frequency: '',
                oximetry_frequency: '',
                temperature: '',
                blood_sugar: '',
                sore_score: '',
                ews: '',
                weight: '',
                height: '',
                imt: '',
                head_circumference: '',
                chest_circumference: '',
                arm_circumference: '',
                stomach_circumference: '',
                knee_circumference: '',
                physical_check: '',
                soap_o_anythingelse:'',
                icd10: [],
                icd10_notes:'',
                non_icd10: [],
                soap_a_anythingelse: '',
                icd9cm: [],
                therapy_plan: '',
                transaction_doctor: [],
                laboratory_request_items: [],
                radiology_request_items: [],
                pharmacy_depo_id: '',
                physiotherapy_request_items: [],
                form_prescription_parent: [],
                form_prescription: [],
                advice: '',
                // laboratory_clinical_indications: '',
                // radiology_clinical_indications: '',

                // HFII hemodialysis
                is_hemodialysis: 0,
                main_diagnosis: '',
                etiology: '',
                comorbidities: '',
                frequency_hd: '',
                vascular_access: '',
                // prescription_hd: '',

                // HFII medical_rehabilitation
                is_medical_rehabilitation: 0,
                functional_testing: '',
                // evaluation: '',
                suspected_disease_type_id: 0,
                suspected_disease: '',
                result: '',
                conclusion: '',
                recommendation: '',
                functional_diagnosis: '',
                medical_diagnosis: '',
                // service_date: moment(new Date()).format('YYYY-MM-DD'),
                // anamnesa: '',
                // support_examination: '',
                // advice_rehab: '',

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
                ophthal_status: '',
                
                // conditional but important
                health_facility_item_id: '',
            },
            input_other10: '',
            costing_id:'',
            icd10_id:'',
            OptionsIcd10:[],
            icd9cm_id:'',
            OptionsIcd9cm:[],

            supportPackage: {
                lab_package_list: [],
                rad_package_list: [],
            },
            searchlab:'',
            lab_status: 1,
            searchrad:'',
            rad_status: 1,
            searchphy:'',

            OptionsCatalog: [],
            catalog: '',
            prescription: '',
            prescription_type: {},
            presc_status: 0,
            // presc_obj: {},

            exclusive_presc_list: [],
            form_exclusive_presc: {
                id: '',
                soap_id: '',
                pharmacy_depo_id: '',
                soap_p_id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                form_prescription_parent: [],
                form_prescription: []
            },
            
            ExcOptionsCatalog: [],
            exc_catalog: '',
            exc_prescription: '',
            exc_prescription_type: {},

            prescription_name:'OBAT SATUAN',
            getCosting: [],
            
        }
    },
    computed: {
        ...mapState({ 
            itemsAllergy: state => state.patient.allergy.items, 
            fieldsAllergy: state => state.patient.allergy.fields, 
            OptionsCosting: state => state.costing.options.costing,
            PackageOptions: state => state.packages.options.packages,
            OptionsDoctorRef: state => state.employee.options.employee,
            itemsPhysiotherapy: state => state.physiotherapy.package.items,
            
            OptionsTypeAllergy: state => state.helper.options.typeallergy,

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
        }),
        filteredListLab() {
            return this.supportPackage.lab_package_list.map((lab) => {
                const filteredCategoryItems = lab.category_items.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchlab.toLowerCase())
                );

                return {
                    ...lab,
                    category_items: filteredCategoryItems,
                };
            }).filter(lab => lab.category_items.length > 0); 
        },
        filteredListRad() {
            return this.supportPackage.rad_package_list.map((rad) => {
                const filteredCategoryItems = rad.category_items.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchrad.toLowerCase())
                );

                return {
                    ...rad,
                    category_items: filteredCategoryItems,
                };
            }).filter(rad => rad.category_items.length > 0); 
        },
        filteredListPhy() {
            return this.itemsPhysiotherapy.filter((phy) => {
                const computedPhyObj = { ...phy}
                return Object.keys(computedPhyObj)
                    .some(key => ('' + computedPhyObj[key]).toLowerCase().includes(this.searchphy.toLowerCase()))
            })
        },
        firstRow() {
            return (this.Planningtable.currentPage - 1) * this.Planningtable.perPage + 1
        },
        lastRow() {
            return Math.min(this.Planningtable.currentPage * this.Planningtable.perPage, this.Planningtable.totalRows)
        }
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat("en-US", { style: "currency", currency: "IDR" }).format(value)
        },
        numberToDecimal(value) {
            let val = (value/1).toFixed(0).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },
        numberToDecimalCurr(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },
        decimalToNumber(value) {
            const val = value
                .replace(/(kr|\$|£|€)/g, '') // getting rid of currency
                .trim()
                .replace(/(.+)[.,](\d+)$/g, "$1x$2") // stripping number into $1: integer and $2: decimal part and putting it together with x as decimal point
                .replace(/[.,]/g, '') // getting rid of . AND ,
                .replace('x', '.'); // replacing x with .
            return parseFloat(val);
        }
    },
    methods: {
        parentTrigger(message) {
            this.openMainModal(message)
            // console.log('Message:', message);
        },
        getParentName(parent_id){
            let name = ""
            if (parent_id == 1) {
                name = 'OBAT SATUAN'
            } else if (parent_id == 2) {
                name = 'RACIK 1'
            } else if (parent_id == 3) {
                name = 'RACIK 2'
            } else if (parent_id == 4) {
                name = 'RACIK 3'
            } else if (parent_id == 5) {
                name = 'RACIK 4'
            } else {
                name = '-'
            }
            return name
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        parsed_age(input_date) {
            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            return `${years} th ${months} bln ${days} hr`;
        },
        
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
        localDateTime(input) {
            return moment(input).locale("id").format('DD MMMM YYYY HH:MM')
        },
        getRelation(code) {
            const relations = {
                '1': 'Ayah',
                '2': 'Ibu',
                '3': 'Kakak',
                '4': 'Adik',
            };
            return relations[code] || '-';
        },

        imtChanged($event){
            let brt_badan = 0
            if (this.form_soap.weight) {
                brt_badan = parseFloat(this.form_soap.weight);
            } else {
                brt_badan = 0
            }

            let tng_badan = 0
            if (this.form_soap.height) {
                tng_badan = parseFloat(this.form_soap.height);
            } else {
                tng_badan = 0
            }

            let imt = 0.0;    

            if (brt_badan && tng_badan) {
                imt = tng_badan * tng_badan;
                imt = (brt_badan/imt)*100*100;
                this.form_soap.imt = imt.toFixed(1);
            }
        },

        saveChangedSoap($event){
            localStorage.setItem("soap", JSON.stringify(this.form_soap))
        },
        
        openMainModal(gate) {
            this.resetSoapForm()
            this.active.modal.main = true
            this.getSoapHistory()
            this.handlingOutpatient(gate)

            // const outpatient_id = "3cb626f5-9148-4fb1-8663-3d7f028b01a6"
            // if (this.regData.health_facility_id === outpatient_id) {
            //     this.handlingOutpatient(gate)
            // }
        },

        getSoapHistory() {
            try {
                this.$store.dispatch('soap/getByNorm', this.globalNorm).then(data => {
                    this.soap_list = data
                })
            } catch (error) {
                // console.error('Error while GET data from soap/getByNorm at: ', error)
            }
        },

        handlingOutpatient(gate) {            
            const params = {
                registration_destination_id: this.reg_des_id,
                is_booking: 0
            }

            const outpatient_id = "3cb626f5-9148-4fb1-8663-3d7f028b01a6"
            if (gate == 'main_gate' && this.regData.health_facility_id === outpatient_id) {
                // console.log('form main');
                this.$store.dispatch('soap/getDetail', params).then((response) => {                
                    if (response.length > 0) {
                        this.fillSoapForm(response[0])
                    } else {                    
                        this.getLocalSoap()
                    }
                })
            } else {
                this.fillSoapForm(gate)
                // console.log('fr0m parent: ', gate);
            }
        },

        fillSoapForm(obj) {
            // console.log('going here: ', obj);
            this.resetSoapForm()

            if (obj) {
                this.$nextTick(() => {
                    this.form_soap.health_facility_item_id = obj.health_facility_item_id ? obj.health_facility_item_id : '' 

                    this.form_soap.id = obj.id ? obj.id : '' 
                    this.form_soap.doctor_in_charge_id = obj.doctor_in_charge_id ? obj.doctor_in_charge_id : ''
                    this.form_soap.registration_destination_id = obj.registration_destination_id ? obj.registration_destination_id : ''
                    this.form_soap.anamesa_type_id = obj.anamesa_type_id ? obj.anamesa_type_id : 1 
                    this.form_soap.family_relation_id = obj.family_relation_id ? obj.family_relation_id : 0
                    this.form_soap.family_name = obj.family_name ? obj.family_name : ''
                    this.form_soap.main_complaint = obj.main_complaint ? obj.main_complaint : ''
                    this.form_soap.history_disease_now = obj.history_disease_now ? obj.history_disease_now : ''
                    this.form_soap.history_disease_past = obj.history_disease_past ? obj.history_disease_past : ''
                    this.form_soap.history_drugs = obj.history_drugs ? obj.history_drugs : ''
                    this.form_soap.allergies = obj.allergies ? obj.allergies : []
                    
                    this.form_soap.systolic_blood_pressure = obj.systolic_blood_pressure ? obj.systolic_blood_pressure : ''
                    this.form_soap.diastolic_blood_pressure = obj.diastolic_blood_pressure ? obj.diastolic_blood_pressure : ''
                    this.form_soap.pulse_frequency = obj.pulse_frequency ? obj.pulse_frequency : ''
                    this.form_soap.breath_frequency = obj.breath_frequency ? obj.breath_frequency : ''
                    this.form_soap.oximetry_frequency = obj.oximetry_frequency ? obj.oximetry_frequency : ''
                    this.form_soap.temperature = obj.temperature ? obj.temperature : ''
                    this.form_soap.blood_sugar = obj.blood_sugar ? obj.blood_sugar : ''
                    this.form_soap.sore_score = obj.sore_score ? obj.sore_score : ''
                    this.form_soap.ews = obj.ews ? obj.ews : ''
        
                    this.form_soap.weight = obj.weight ? obj.weight : ''
                    this.form_soap.height = obj.height ? obj.height : ''
                    this.form_soap.imt = obj.imt ? obj.imt : ''
                    this.form_soap.head_circumference = obj.head_circumference ? obj.head_circumference : ''
                    this.form_soap.chest_circumference = obj.chest_circumference ? obj.chest_circumference : ''
                    this.form_soap.arm_circumference = obj.arm_circumference ? obj.arm_circumference : ''
                    this.form_soap.stomach_circumference = obj.stomach_circumference ? obj.stomach_circumference : ''
                    this.form_soap.knee_circumference = obj.knee_circumference ? obj.knee_circumference : ''
                    this.form_soap.physical_check = obj.physical_check ? obj.physical_check : ''
                    this.form_soap.soap_o_anythingelse = obj.soap_o_anythingelse ? obj.soap_o_anythingelse : ''
        
                    if (obj.icd10) {
                        const mappedIcd10 = obj.icd10.map(item => {
                            return {
                                icd10_id: item.icd10_id,
                                value: item.icd10_id,
                                title: `${item.code} ${item.name}`, 
                                action: "action"
                            };
                        });                
                        this.form_soap.icd10 = mappedIcd10;
                    } else {
                        this.form_soap.icd10 = []
                    }
        
                    this.form_soap.icd10_notes = obj.icd10_notes ? obj.icd10_notes : ''
                    this.form_soap.non_icd10 = obj.non_icd10 ? obj.non_icd10 : []
                    this.form_soap.soap_a_anythingelse = obj.soap_a_anythingelse ? obj.soap_a_anythingelse : ''
        
                    if (obj.icd9cm) {
                        const mappedIcd9cm = obj.icd9cm.map(item => {
                            return {
                                icd9cm_id: item.icd9cm_id,
                                value: item.icd9cm_id,
                                title: `${item.code} ${item.name}`, 
                                action: "action"
                            };
                        });                
                        this.form_soap.icd9cm = mappedIcd9cm;
                    } else {
                        this.form_soap.icd9cm = []
                    }
        
                    this.form_soap.therapy_plan = obj.therapy_plan ? obj.therapy_plan : ''
                    this.form_soap.transaction_doctor = obj.transaction_doctor ? obj.transaction_doctor : []
                                
                    this.lab_status = obj.laboratory_request ? obj.laboratory_request.status : 1
                    if (obj.laboratory_request_items) {
                        const mappedLab = obj.laboratory_request_items.map(item => {
                            return {
                                costing_name: item.costing_name,
                                laboratory_package_id: item.laboratory_package_id,
                                service_class_id: item.service_class_id
                            };
                        });                
                        this.form_soap.laboratory_request_items = mappedLab;
                    } else {
                        this.form_soap.laboratory_request_items = []
                    }
                    
                    this.rad_status = obj.radiology_request ? obj.radiology_request.status : 1
                    if (obj.radiology_request_items) {
                        const mappedRad = obj.radiology_request_items.map(item => {
                            return {
                                costing_name: item.costing_name,
                                radiology_package_id: item.radiology_package_id,
                                service_class_id: item.service_class_id
                            };
                        });                
                        this.form_soap.radiology_request_items = mappedRad;
                    } else {
                        this.form_soap.radiology_request_items = []
                    }
                    
                    if (obj.physiotherapy_request && obj.physiotherapy_request.length > 0) {
                        const mappedPhy = obj.physiotherapy_request[0].physiotherapy_request_items.map(item => {
                            return {
                                costing_name: item.costing_name,
                                physiotherapy_package_id: item.physiotherapy_package_id,
                                service_class_id:item.service_class_id
                            };
                        });                
                        this.form_soap.physiotherapy_request_items = mappedPhy;
                    } else {
                        this.form_soap.physiotherapy_request_items = []
                    }
                    
                    if (obj.pharmacy_depo_id) {
                        this.form_soap.pharmacy_depo_id = obj.pharmacy_depo_id 
                    } else {
                        const emergency_id = "444b3134-5b75-4b67-af72-a558d46716d7"
                        if (obj.health_facility_id === emergency_id) {
                            this.form_soap.pharmacy_depo_id = '4105d8bc-5769-463e-bc67-aae2fc5adf10'
                            this.form_exclusive_presc.pharmacy_depo_id = '4105d8bc-5769-463e-bc67-aae2fc5adf10'
                        } else {
                            this.form_soap.pharmacy_depo_id = '297c44f0-12ef-40e6-b9a7-c831065b3372'
                            this.form_exclusive_presc.pharmacy_depo_id = '297c44f0-12ef-40e6-b9a7-c831065b3372'
                        }  
                    }
                    
                    // cukup ambil dari order yang paling akhir, buat nyimpen di soap
                    if (obj.prescription_new) {
                        let last_index = obj.prescription_new.length-1
                        let var_presc = obj.prescription_new[last_index].form_prescription
                        let var_parent = obj.prescription_new[last_index].form_prescription_parent
                        
                        this.presc_status = obj.prescription_new ? obj.prescription_new[last_index].flag : 0
                        // this.presc_obj = obj.prescription_new ? obj.prescription_new[last_index] : {}
                        // console.log('presc_obj', this.presc_obj);

                        if (var_parent) {
                            const mappedPrescParent = var_parent.map(item => {
                                let name = item.id ? this.getParentName(item.id) : ''

                                return {
                                    id: item.id,
                                    used: item.used,
                                    value: item.value,
                                    prescription: name,
                                };
                            });                
                            this.form_soap.form_prescription_parent = mappedPrescParent;
                        } else {
                            this.form_soap.form_prescription_parent = []
                        }

                        if (var_presc) {
                            const mappedPresc = var_presc.map(item => {
                                let name = item.id ? this.getParentName(item.id) : ''

                                return {
                                    pharmacy_catalog_id: item.pharmacy_catalog_id, 
                                    prescription_type_id: item.prescription_item_type_id,
                                    package_id: item.package_id,
                                    package_name: item.package_name,
                                    dose: item.dose,
                                    sum_dose: item.dose+' '+ item.dose_name, 
                                    value_package: item.qty,
                                    sort: item.sort, 
                                    used: item.used,
                                    desc: item.desc,
    
                                    code: item.pharmacy_catalog_code,
                                    name: item.pharmacy_catalog_name,
                                    value_doctor: item.qty_doctor,
                                    prescription_type_name: name,
                                };
                            });      
                            this.form_soap.form_prescription = mappedPresc;                                  
                        } else {
                            this.form_soap.form_prescription = [];                                  
                        }
                    }

                    this.form_soap.advice = obj.advice ? obj.advice : ''

                    // Exc Prescription
                    this.getExcPresciption(obj.id, obj.doctor_in_charge_id)
                    this.form_exclusive_presc.soap_id = obj.id
                    this.form_exclusive_presc.registration_destination_id = obj.registration_destination_id
                    this.form_exclusive_presc.doctor_in_charge_id = obj.doctor_in_charge_id
                    this.form_exclusive_presc.soap_p_id = obj.soap_p_id

                    // HFII hemodialysis
                    this.form_soap.is_hemodialysis = obj.is_hemodialysis ? obj.is_hemodialysis : 0
                    this.form_soap.main_diagnosis = obj.main_diagnosis ? obj.main_diagnosis : ''
                    this.form_soap.etiology = obj.etiology ? obj.etiology : ''
                    this.form_soap.comorbidities = obj.comorbidities ? obj.comorbidities : ''
                    this.form_soap.frequency_hd = obj.frequency_hd ? obj.frequency_hd : ''
                    this.form_soap.vascular_access = obj.vascular_access ? obj.vascular_access : ''
                    // this.form_soap.prescription_hd = obj.prescription_hd ? obj.prescription_hd : ''

                    // HFII mediacal_rehabilition
                    this.form_soap.is_medical_rehabilitation = obj.is_medical_rehabilitation? obj.is_medical_rehabilitation : 0,
                    this.form_soap.functional_testing = obj.functional_testing? obj.functional_testing : ''
                    // this.form_soap.evaluation = obj.evaluation? obj.evaluation : ''
                    this.form_soap.suspected_disease_type_id = obj.suspected_disease_type_id? obj.suspected_disease_type_id : 0
                    this.form_soap.suspected_disease = obj.suspected_disease? obj.suspected_disease : ''
                    this.form_soap.result = obj.result? obj.result : ''
                    this.form_soap.conclusion = obj.conclusion? obj.conclusion : ''
                    this.form_soap.recommendation = obj.recommendation? obj.recommendation : ''
                    this.form_soap.functional_diagnosis = obj.functional_diagnosis? obj.functional_diagnosis : ''
                    this.form_soap.medical_diagnosis = obj.medical_diagnosis? obj.medical_diagnosis : ''
                    // this.form_soap.service_date = obj.service_date? obj.service_date : moment(new Date()).format('YYYY-MM-DD')
                    // this.form_soap.anamnesa = obj.anamnesa? obj.anamnesa : ''
                    // this.form_soap.support_examination = obj.support_examination? obj.support_examination : ''
                    // this.form_soap.advice_rehab = obj.advice_rehab? obj.advice_rehab : ''

                    this.form_soap.head = obj.head ? obj.head : 1
                    this.form_soap.head_hair_color = obj.head_hair_color ? obj.head_hair_color : 1
                    this.form_soap.head_hair_condition = obj.head_hair_condition ? obj.head_hair_condition : 1
                    this.form_soap.eye_conjunctive = obj.eye_conjunctive ? obj.eye_conjunctive : 1
                    this.form_soap.eye_sclera = obj.eye_sclera ? obj.eye_sclera : 1
                    this.form_soap.eye_pupil = obj.eye_pupil ? obj.eye_pupil : 1
                    this.form_soap.eye_light_reflex = obj.eye_light_reflex ? obj.eye_light_reflex : 1
                    this.form_soap.mouth_mucosa = obj.mouth_mucosa ? obj.mouth_mucosa : 1
                    this.form_soap.mouth_discoloration = obj.mouth_discoloration ? obj.mouth_discoloration : 1
                    this.form_soap.mouth_tongue = obj.mouth_tongue ? obj.mouth_tongue : 1
                    this.form_soap.mouth_caries = obj.mouth_caries ? obj.mouth_caries : 1
                    this.form_soap.neck_kgb = obj.neck_kgb ? obj.neck_kgb : 1
                    this.form_soap.neck_trachea_position = obj.neck_trachea_position ? obj.neck_trachea_position : 1
                    this.form_soap.neck_massa = obj.neck_massa ? obj.neck_massa : 1
                    this.form_soap.thorax_insp_chest = obj.thorax_insp_chest ? obj.thorax_insp_chest : 1
                    this.form_soap.thorax_insp_retraction = obj.thorax_insp_retraction ? obj.thorax_insp_retraction : 1
                    this.form_soap.thorax_palp_chest = obj.thorax_palp_chest ? obj.thorax_palp_chest : 1
                    this.form_soap.thorax_palp_fremitus = obj.thorax_palp_fremitus ? obj.thorax_palp_fremitus : 1
                    this.form_soap.thorax_palp_crepitation = obj.thorax_palp_crepitation ? obj.thorax_palp_crepitation : 1
                    this.form_soap.thorax_ausk_pul_vasiculer = obj.thorax_ausk_pul_vasiculer ? obj.thorax_ausk_pul_vasiculer : 1
                    this.form_soap.thorax_ausk_pul_rhonki = obj.thorax_ausk_pul_rhonki ? obj.thorax_ausk_pul_rhonki : 1
                    this.form_soap.thorax_ausk_pul_wheezing = obj.thorax_ausk_pul_wheezing ? obj.thorax_ausk_pul_wheezing : 1
                    this.form_soap.thorax_ausk_cor_heart_sound = obj.thorax_ausk_cor_heart_sound ? obj.thorax_ausk_cor_heart_sound : 1
                    this.form_soap.thorax_ausk_cor_murmur = obj.thorax_ausk_cor_murmur ? obj.thorax_ausk_cor_murmur : 1
                    this.form_soap.thorax_ausk_cor_gallop = obj.thorax_ausk_cor_gallop ? obj.thorax_ausk_cor_gallop : 1
                    this.form_soap.abdomen_supel = obj.abdomen_supel ? obj.abdomen_supel : 1
                    this.form_soap.abdomen_bowel_sounds = obj.abdomen_bowel_sounds ? obj.abdomen_bowel_sounds : 1
                    this.form_soap.abdomen_tenderness = obj.abdomen_tenderness ? obj.abdomen_tenderness : 1
                    this.form_soap.abdomen_hepar_lien = obj.abdomen_hepar_lien ? obj.abdomen_hepar_lien : 1
                    this.form_soap.abdomen_other_physical_check = obj.abdomen_other_physical_check ? obj.abdomen_other_physical_check : ''
                    this.form_soap.extremity_acral = obj.extremity_acral ? obj.extremity_acral : 1
                    this.form_soap.extremity_crt = obj.extremity_crt ? obj.extremity_crt : 1
                    this.form_soap.extremity_edema = obj.extremity_edema ? obj.extremity_edema : 1
                    this.form_soap.genitalia = obj.genitalia ? obj.genitalia : ''
                    this.form_soap.localist_status = obj.localist_status ? obj.localist_status : ''
                    this.form_soap.ophthal_status = obj.ophthal_status ? obj.ophthal_status : ''
                });
                
            }
        },

        getLocalSoap() {
            const storedSoap = localStorage.getItem('soap');
            this.form_soap.registration_destination_id = this.reg_des_id
    
            if (storedSoap) {
                const parsedSoap = JSON.parse(storedSoap);

                // nextTick buat mastiin kondisi terpenuhi abis di render
                this.$nextTick(() => {

                    if (this.reg_des_id === parsedSoap.registration_destination_id) {
                        // console.log('sama');
                        this.form_soap.anamesa_type_id = parsedSoap.anamesa_type_id  
                        this.form_soap.family_relation_id = parsedSoap.family_relation_id 
                        this.form_soap.family_name = parsedSoap.family_name 
                        this.form_soap.main_complaint = parsedSoap.main_complaint 
                        this.form_soap.history_disease_now = parsedSoap.history_disease_now 
                        this.form_soap.history_disease_past = parsedSoap.history_disease_past 
                        this.form_soap.history_drugs = parsedSoap.history_drugs 

                        this.form_soap.systolic_blood_pressure = parsedSoap.systolic_blood_pressure 
                        this.form_soap.diastolic_blood_pressure = parsedSoap.diastolic_blood_pressure 
                        this.form_soap.pulse_frequency = parsedSoap.pulse_frequency 
                        this.form_soap.breath_frequency = parsedSoap.breath_frequency 
                        this.form_soap.oximetry_frequency = parsedSoap.oximetry_frequency 
                        this.form_soap.temperature = parsedSoap.temperature 
                        this.form_soap.ews = parsedSoap.ews 
                        this.form_soap.sore_score = parsedSoap.sore_score 
                        this.form_soap.blood_sugar = parsedSoap.blood_sugar 

                        this.form_soap.weight = parsedSoap.weight 
                        this.form_soap.height = parsedSoap.height 
                        this.form_soap.imt = parsedSoap.imt 
                        this.form_soap.head_circumference = parsedSoap.head_circumference 
                        this.form_soap.chest_circumference = parsedSoap.chest_circumference 
                        this.form_soap.arm_circumference = parsedSoap.arm_circumference 
                        this.form_soap.stomach_circumference = parsedSoap.stomach_circumference 
                        this.form_soap.knee_circumference = parsedSoap.knee_circumference 
                        this.form_soap.physical_check = parsedSoap.physical_check
                        this.form_soap.soap_o_anythingelse = parsedSoap.soap_o_anythingelse 

                        this.form_soap.icd10 = parsedSoap.icd10 
                        this.form_soap.icd10_notes = parsedSoap.icd10_notes 
                        this.form_soap.non_icd10 = parsedSoap.non_icd10 
                        this.form_soap.soap_a_anythingelse = parsedSoap.soap_a_anythingelse 

                        this.form_soap.icd9cm = parsedSoap.icd9cm 
                        this.form_soap.therapy_plan = parsedSoap.therapy_plan

                        this.form_soap.laboratory_request_items = parsedSoap.laboratory_request_items 
                        this.form_soap.radiology_request_items = parsedSoap.radiology_request_items 
                        this.form_soap.physiotherapy_request_items = parsedSoap.physiotherapy_request_items 
                        this.form_soap.form_prescription_parent = parsedSoap.form_prescription_parent 
                        this.form_soap.form_prescription = parsedSoap.form_prescription 

                    } else {
                        localStorage.removeItem("soap");
                        this.getNurseResume()
                        // console.log('beda');
                    }
                });
            } else {
                this.getNurseResume()
                // console.log('localStorage soap is empty');
            }
        },

        getNurseResume() {
            // console.log('running nurse here');
            try {
                this.$store.dispatch('nurse/getAllResolve', this.reg_des_id).then(response => {
                    let resume = response[0]

                    if (response.length > 0) {
                        // anamnesa
                        this.form_soap.anamesa_type_id = resume.anamesa_type_id
                        this.form_soap.family_relation_id = resume.family_relation_id
                        this.form_soap.family_name = resume.family_name
                        this.form_soap.main_complaint = resume.main_complaint
                        this.form_soap.history_disease_now = resume.history_disease_now
                        this.form_soap.history_disease_past = resume.history_disease_past
                        // TTV
                        this.form_soap.systolic_blood_pressure = resume.systolic_blood_pressure 
                        this.form_soap.diastolic_blood_pressure = resume.diastolic_blood_pressure 
                        this.form_soap.pulse_frequency = resume.pulse_frequency 
                        this.form_soap.breath_frequency = resume.breath_frequency 
                        this.form_soap.oximetry_frequency = resume.oximetry_frequency 
                        this.form_soap.temperature = resume.temperature 
                        this.form_soap.ews = resume.ews 
                        this.form_soap.sore_score = resume.sore_score 
                        this.form_soap.blood_sugar = resume.blood_sugar
                        // ANtropometri
                        this.form_soap.weight = resume.weight
                        this.form_soap.height = resume.height
                        this.form_soap.imt = resume.imt
                        this.form_soap.head_circumference = resume.head_circumference
                        this.form_soap.chest_circumference = resume.chest_circumference
                        this.form_soap.arm_circumference = resume.arm_circumference
                        this.form_soap.stomach_circumference = resume.stomach_circumference
                        this.form_soap.knee_circumference = resume.knee_circumference
                    }

                    localStorage.setItem("soap", JSON.stringify({ ...this.form_soap }))
                })
                
            } catch (error) {
                // console.error('Error while GET data from nurse/getAllResolve at: ', error)
            }
        },

        // ALLERGY
        openModalAllergy(){
            this.active.modal.allergy = true
            this.resetAllergy()
        },
        
        resetAllergy(){
            this.allergyInput.type_allergy_id = 0
            this.allergyInput.name = ''
            this.allergyInput.reaction = ''
            this.allergyInput.severity = ''
            this.allergyInput.side_effect = ''
            this.allergyInput.date_tragedy = ''
        },
        
        addAllergy(){
            const input = {
                type_allergy_id: this.allergyInput.type_allergy_id,
                name: this.allergyInput.name,
                reaction: this.allergyInput.reaction,
                severity: this.allergyInput.severity,
                side_effect: this.allergyInput.side_effect,
                date_tragedy: this.allergyInput.date_tragedy
            }
            
            this.form_soap.allergies.push(input)
            this.active.modal.allergy = false
            this.resetAllergy()
        },

        removeAllergy(index) {
            this.form_soap.allergies.splice(index, 1)
        },


        // DIAGNOSIS ICD10
        onSearchIcd10(search, loading) {
            if(search.length >= 3) {
                loading(true);
                this.$store.dispatch('icd10/getParam', search).then(data => {
                    this.OptionsIcd10 = []
                    for (let i = 0; i < data.length; i++) {
                        const option = []
                        for (const key in data[i]) {
                            if (key === 'id') { option['value'] = data[i][key] } else if (key === 'name') { option['title'] = data[i]['code']+' '+data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                        }
                        this.OptionsIcd10.push(Object.assign({}, option))
                    }
                    loading(false)
                })
            }
        },

        onAdd10 (event) {
            localStorage.setItem("soap", JSON.stringify(this.form_soap))
            this.icd10_id = ''

            if (this.form_soap.icd10.some(item => item.icd10_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD10 yang sama')
            } else {
                this.form_soap.icd10.push({ icd10_id:event.value, title:event.title, value:event.value, action: 'action'})
            }

        },
        delete10 (index) {
            localStorage.setItem("soap", JSON.stringify(this.form_soap))
            this.form_soap.icd10.splice(index, 1)
        },

        addOther10() {
            let input = ''
            if (this.input_other10) {
                input = this.input_other10
                this.form_soap.non_icd10.push(input)
                this.input_other10 = ''
            }
        },

        removeOther10 (index) {
            this.form_soap.non_icd10.splice(index, 1)
        },


        // TINDAKAN ICD9CM
        onSearchIcd9cm(search, loading) {
            if(search.length >= 2) {
                loading(true);
                this.$store.dispatch('icd9cm/getParam', search).then(data => {
                    this.OptionsIcd9cm = []
                    for (let i = 0; i < data.length; i++) {
                        const option = []
                        for (const key in data[i]) {
                            if (key === 'id') { option['value'] = data[i][key] } else if (key === 'name') { option['title'] = data[i]['code']+' '+data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                        }
                        this.OptionsIcd9cm.push(Object.assign({}, option))
                    }
                    loading(false)
                })
            }
        },

        onAdd9cm (event) {
            localStorage.setItem("soap", JSON.stringify(this.form_soap))
            this.icd9cm_id = ''

            if (this.form_soap.icd9cm.some(item => item.icd9cm_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD9 yang sama')
            } else {
                this.form_soap.icd9cm.push({ icd9cm_id:event.value, title:event.title, value:event.value, action: 'action'})
            }
        },

        delete9cm (index) {
            this.form_soap.icd9cm.splice(index, 1)
        },
        

        // TRANSAKSI DOKTER
        onAddSC (event) {
            // console.log(event);
            localStorage.setItem("soap", JSON.stringify(this.form_soap))
            this.costing_id = ''

            // default ketika di push ke array
            this.form_soap.transaction_doctor.push(
                { 
                    costing_id: event.costing_id,
                    costing_name: event.title, 
                    is_ref: event.data.is_ref, 
                    value: event.value, 
                    var_1: event.costing, 
                    var_2: 1, 
                    doctor_ref_id: '', 
                }
            )
        },

        deleteSC (index) {
            this.form_soap.transaction_doctor.splice(index, 1)
        },


        // PRESCRIPTION
        onAddPRES (event) {
            // this.prescription = event.value
            this.prescription_type = event
            let index = this.form_soap.form_prescription_parent.findIndex((item) => item.id === event.value);
            // Check if the object with the specified property value exists in the array
            if (index === -1) {
                // If not found, push a new object with the desired properties
                this.form_soap.form_prescription_parent.push(
                    {
                        id: event.value,
                        prescription: event.title,
                        used: '',
                        value: 0
                    }
                ) 
            } else {
                // If found, log a message indicating that the object already exists
                // console.log("Object already exists");
            }
        },

        onSearchCatalog(search, loading) {
            if(search.length >= 3) {
                loading(true);
                this.$store.dispatch('catalog/getParamSearch', search).then(data => {
                    this.OptionsCatalog = []
                    for (let i = 0; i < data.length; i++) {
                        const option = []
                        for (const key in data[i]) {
                            if (key === 'id') { option['value'] = data[i][key] } else if (key === 'is_delete') { option['data_catalog'] = data[i] } else if (key === 'name') { option['title'] = data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                        }
                        this.OptionsCatalog.push(Object.assign({}, option))
                    }
                    loading(false)
                })
            }
        },

        onAddCAT (event) {
            localStorage.setItem("soap", JSON.stringify(this.form_soap))
            this.catalog = ''
            if (this.prescription === '') {
                alert("Pilih Racikan!");
            } else {
                this.form_soap.form_prescription.push(
                    { 
                        code: event.data_catalog.code,
                        name: event.data_catalog.name,
                        pharmacy_catalog_id: event.data_catalog.id, 
                        package_id: event.data_catalog.package_id,
                        dose: event.data_catalog.dose,
                        sum_dose: event.data_catalog.dose+' '+event.data_catalog.dose_name, 
                        package_name: event.data_catalog.package_name,
                        prescription_type_id: this.prescription_type.value,
                        prescription_type_name: this.prescription_type.title,
                        value_doctor: 0,
                        value_package: 0,
                        used: '',
                        desc: '',
                        sort: this.form_soap.form_prescription.length, 
                    }
                )
                // console.log(this.form_soap.form_prescription)
            }
        },

        deletePRES (index) {
            this.form_soap.form_prescription.splice(index, 1)
        },

        vdChanged($event, index){
            localStorage.setItem("soap", JSON.stringify(this.form_soap))
            let value_doctor = $event
            let value_prescription = 0
            for (let ipp = 0; ipp < this.form_soap.form_prescription_parent.length; ipp++) {
                if (this.form_soap.form_prescription[index].prescription_type_id === this.form_soap.form_prescription_parent[ipp].id) {
                    value_prescription = this.$options.filters.decimalToNumber(this.form_soap.form_prescription_parent[ipp].value)
                }
            }
            // console.log(value_prescription)
            let dose = this.form_soap.form_prescription[index].dose
            this.form_soap.form_prescription[index].value_package = this.$options.filters.numberToDecimal(value_doctor*value_prescription/dose)
        },
        
        validationForm () {
            if (this.form_soap.id == '') {
                this.form_soap.doctor_in_charge_id = this.userData.employee_id
                this.form_soap.registration_destination_id = this.regData.registration_destination_id

                if (this.regData.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e') {
                    this.form_soap.is_hemodialysis = 1
                }

                if (this.regData.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a') {
                    this.form_soap.is_medical_rehabilitation = 1
                }

            } else {
                if (this.form_soap.health_facility_item_id == 'ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e') {
                    this.form_soap.is_hemodialysis = 1
                }

                if (this.form_soap.health_facility_item_id == '5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a') {
                    this.form_soap.is_medical_rehabilitation = 1
                }
            }
            // console.log('form soap: ', this.form_soap);

            try {
                this.isDisabled = true
                const formData = this.form_soap
                this.$store.dispatch('soap/formPostPut', formData).then(res => {
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.active.modal.main = false

                    localStorage.setItem("soap", '')
                    this.isDisabled = false
                    this.getAssessment()
                    this.resetSoapForm()

                    // this.socket.emit('SEND_LAB', []);
                    // this.socket.emit('SEND_RAD', []);
                    // this.socket.emit('SEND_PRS', []);
                }, 
                err => {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    this.isDisabled = false
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                this.isDisabled = false
                console.error('Error at: ', error);
            }
        },

        resetSoapForm() {
            this.form_soap.health_facility_item_id = '',

            this.form_soap.id = '' 
            this.form_soap.doctor_in_charge_id = ''
            this.form_soap.registration_destination_id = ''
            this.form_soap.anamesa_type_id = 1 
            this.form_soap.family_relation_id = ''
            this.form_soap.family_name = ''
            this.form_soap.main_complaint = ''
            this.form_soap.history_disease_now = ''
            this.form_soap.history_disease_past = ''
            this.form_soap.history_drugs = ''
            this.form_soap.allergies =  []
            this.form_soap.systolic_blood_pressure = ''
            this.form_soap.diastolic_blood_pressure = ''
            this.form_soap.pulse_frequency = ''
            this.form_soap.breath_frequency = ''
            this.form_soap.oximetry_frequency = ''
            this.form_soap.temperature = ''
            this.form_soap.blood_sugar = ''
            this.form_soap.sore_score = ''
            this.form_soap.ews = ''
            this.form_soap.weight = ''
            this.form_soap.height = ''
            this.form_soap.imt = ''
            this.form_soap.head_circumference = ''
            this.form_soap.chest_circumference = ''
            this.form_soap.arm_circumference = ''
            this.form_soap.stomach_circumference = ''
            this.form_soap.knee_circumference = ''
            this.form_soap.physical_check = ''
            this.form_soap.soap_o_anythingelse = ''
            this.form_soap.icd10 =  []
            this.form_soap.icd10_notes = ''
            this.form_soap.non_icd10 =  []
            this.form_soap.soap_a_anythingelse = ''
            this.form_soap.icd9cm =  []
            this.form_soap.therapy_plan = ''
            this.form_soap.transaction_doctor =  []
            this.form_soap.laboratory_request_items =  []
            this.form_soap.radiology_request_items =  []
            this.form_soap.physiotherapy_request_items =  []
            this.form_soap.pharmacy_depo_id = ''
            this.form_soap.form_prescription_parent =  []
            this.form_soap.form_prescription =  []
            this.form_soap.advice = ''

            // HFII hemodialysis
            this.form_soap.is_hemodialysis = 0
            this.form_soap.main_diagnosis = ''
            this.form_soap.etiology = ''
            this.form_soap.comorbidities = ''
            // this.form_soap.prescription_hd = ''
            this.form_soap.frequency_hd = ''
            this.form_soap.vascular_access = ''

            // HFII medical_rehabilitation
            this.form_soap.is_medical_rehabilitation = 0,
            // this.form_soap.service_date = moment(new Date()).format('YYYY-MM-DD')
            // this.form_soap.anamnesa = ''
            this.form_soap.functional_testing = ''
            // this.form_soap.support_examination = ''
            // this.form_soap.advice_rehab = ''
            // this.form_soap.evaluation = ''
            this.form_soap.suspected_disease_type_id = 0
            this.form_soap.suspected_disease = ''
            this.form_soap.result = ''
            this.form_soap.conclusion = ''
            this.form_soap.recommendation = ''
            this.form_soap.functional_diagnosis = ''
            this.form_soap.medical_diagnosis = ''

            this.form_soap.head = 1
            this.form_soap.head_hair_color = 1
            this.form_soap.head_hair_condition = 1
            this.form_soap.eye_conjunctive = 1
            this.form_soap.eye_sclera = 1
            this.form_soap.eye_pupil = 1
            this.form_soap.eye_light_reflex = 1
            this.form_soap.mouth_mucosa = 1
            this.form_soap.mouth_discoloration = 1
            this.form_soap.mouth_tongue = 1
            this.form_soap.mouth_caries = 1
            this.form_soap.neck_kgb = 1
            this.form_soap.neck_trachea_position = 1
            this.form_soap.neck_massa = 1
            this.form_soap.thorax_insp_chest = 1
            this.form_soap.thorax_insp_retraction = 1
            this.form_soap.thorax_palp_chest = 1
            this.form_soap.thorax_palp_fremitus = 1
            this.form_soap.thorax_palp_crepitation = 1
            this.form_soap.thorax_ausk_pul_vasiculer = 1
            this.form_soap.thorax_ausk_pul_rhonki = 1
            this.form_soap.thorax_ausk_pul_wheezing = 1
            this.form_soap.thorax_ausk_cor_heart_sound = 1
            this.form_soap.thorax_ausk_cor_murmur = 1
            this.form_soap.thorax_ausk_cor_gallop = 1
            this.form_soap.abdomen_supel = 1
            this.form_soap.abdomen_bowel_sounds = 1
            this.form_soap.abdomen_tenderness = 1
            this.form_soap.abdomen_hepar_lien = 1
            this.form_soap.abdomen_other_physical_check = ''
            this.form_soap.extremity_acral = 1
            this.form_soap.extremity_crt = 1
            this.form_soap.extremity_edema = 1
            this.form_soap.genitalia = ''
            this.form_soap.localist_status = ''
            this.form_soap.ophthal_status = ''
        },

        getExcPresciption(soap_id, doctor_in_charge_id) {
            try {
                this.$store.dispatch('prescription/getDetail', {soap_id: soap_id, doctor_in_charge_id: doctor_in_charge_id }).then((response) => {
                    this.exclusive_presc_list = response
                    this.getAssessment()
                })
                
            } catch (error) {
                // console.error('Error while GET data from prescription/getDetail at: ', error);
            }
        },

        openModalExcPresc(){
            this.resetExcPresc()
            this.active.modal.exc_presc = !this.active.modal.exc_presc
            // this.form_exclusive_presc.id = ''
        },
        resetExcPresc() {
            this.form_exclusive_presc.id = ''
            this.form_exclusive_presc.form_prescription_parent = []
            this.form_exclusive_presc.form_prescription = []
            this.exc_prescription = ''
            this.catalog = ''
        },
        onEditPresc(obj) {
            this.resetExcPresc()
            this.active.modal.exc_presc = true
            this.form_exclusive_presc.id = obj.id

            if (obj.form_prescription_parent) {
                const mappedPrescParent = obj.form_prescription_parent.map(item => {
                    let name = item.id ? this.getParentName(item.id) : ''

                    return {
                        id: item.id,
                        used: item.used,
                        value: item.value,
                        prescription: name,
                    };
                });                
                this.form_exclusive_presc.form_prescription_parent = mappedPrescParent;
            } else {
                this.form_exclusive_presc.form_prescription_parent = []
            }

            if (obj.form_prescription) {
                const mappedPresc = obj.form_prescription.map(item => {
                    let name = item.id ? this.getParentName(item.id) : ''

                    return {
                        pharmacy_catalog_id: item.pharmacy_catalog_id, 
                        prescription_type_id: item.prescription_item_type_id,
                        package_id: item.package_id,
                        package_name: item.package_name,
                        dose: item.dose,
                        sum_dose: item.dose+' '+ item.dose_name, 
                        value_package: item.qty,
                        sort: item.sort, 
                        used: item.used,
                        desc: item.desc,

                        code: item.pharmacy_catalog_code,
                        name: item.pharmacy_catalog_name,
                        value_doctor: item.qty_doctor,
                        prescription_type_name: name,
                    };
                });      
                this.form_exclusive_presc.form_prescription = mappedPresc;                                  
            } else {
                this.form_exclusive_presc.form_prescription = [];                                  
            }
        },

        removeExcPresc(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.no_order} ?`, {
                title: 'Please Confirm', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                this.$store.dispatch('prescription/removeOrder', obj.id)
                .then(response => {
                    if (response.status === 200 || response.status === 201) {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.getExcPresciption(this.form_soap.id, this.form_soap.doctor_in_charge_id)
                        // this.socket.emit('SEND_PRS', []);
                    } else {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    }
                },
                error => {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                }) 
                }
            })
        },

        validationExcPrescription(){
            this.active.disabled.exc_presc = true
            try {
                this.$store.dispatch('prescription/formPostPut', this.form_exclusive_presc).then((response) => {
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.getExcPresciption(this.form_soap.id, this.form_soap.doctor_in_charge_id)
                    
                    this.active.modal.exc_presc = false
                    this.form_exclusive_presc.id = ''
                    this.form_exclusive_presc.form_prescription_parent = []
                    this.form_exclusive_presc.form_prescription = []
                    this.exc_prescription = ''
                    this.exc_catalog = ''
                    this.active.disabled.exc_presc = false
                    // this.socket.emit('SEND_PRS', []);
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                this.active.disabled.exc_presc = false
                console.error('Error at: ', error);
            }
        },

        hasItemsInCategory(items, categoryId) {
            if (items) {
                return items.some(item => item.prescription_item_type_id === categoryId);
            }
        },

        rowIGDPrescription(item, role) {
            if (!item || role !== 'row') return ''
            if (item.flag == 0) return ''
            if (item.flag == 1) return 'table-warning-400'
        },

        onAddEXCPRES (event) {
            this.exc_prescription_type = event
            let index = this.form_exclusive_presc.form_prescription_parent.findIndex((item) => item.id === event.value);

            if (index === -1) {
                this.form_exclusive_presc.form_prescription_parent.push(
                    {
                        id: event.value,
                        prescription: event.title,
                        used: '',
                        value: 0
                    }
                ) 
            } else {
                // console.log("Object already exists");
            }
        },

        onSearchEXCCatalog(search, loading) {
            if(search.length >= 3) {
                loading(true);
                this.$store.dispatch('catalog/getParamSearch', search).then(data => {
                    this.ExcOptionsCatalog = []
                    for (let i = 0; i < data.length; i++) {
                        const option = []
                        for (const key in data[i]) {
                            if (key === 'id') { option['value'] = data[i][key] } else if (key === 'is_delete') { option['data_catalog'] = data[i] } else if (key === 'name') { option['title'] = data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                        }
                        this.ExcOptionsCatalog.push(Object.assign({}, option))
                    }
                    loading(false)
                })
            }
        },

        onAddEXCCAT (event) {
            this.exc_catalog = ''
            if (this.exc_prescription === '') {
                alert("Pilih Racikan!");
            } else {
                this.form_exclusive_presc.form_prescription.push(
                    { 
                        code: event.data_catalog.code,
                        name: event.data_catalog.name,
                        pharmacy_catalog_id: event.data_catalog.id, 
                        package_id: event.data_catalog.package_id,
                        dose: event.data_catalog.dose,
                        sum_dose: event.data_catalog.dose+' '+event.data_catalog.dose_name, 
                        package_name: event.data_catalog.package_name,
                        prescription_type_id: this.exc_prescription_type.value,
                        prescription_type_name: this.exc_prescription_type.title,
                        value_doctor: 0,
                        value_package: 0,
                        used: '',
                        desc: '',
                        sort: this.form_exclusive_presc.form_prescription.length, 
                    }
                )
            }
        },

        deleteEXCPRES (index) {
            this.form_exclusive_presc.form_prescription.splice(index, 1)
        },


        
        getAssessment() {
            try {
                this.$store.dispatch('soap/getByRM', this.regData.norm)
            } catch (error) {
                // console.error('Error while GET data from soap/getByRM at: ', error)   
            }
        },

        getPackageListV2(paramsInsuranceServiceClassDoctor) {
            try {
                this.$store.dispatch('laboratory/getLaboratoryPackageV2',paramsInsuranceServiceClassDoctor).then((response) => {
                    this.supportPackage.lab_package_list = response
                })
    
                this.$store.dispatch('radiology/getRadiologyPackageV2',paramsInsuranceServiceClassDoctor).then((response) => {
                    this.supportPackage.rad_package_list = response
                })
            } catch (error) {
                // console.error('Error while GET data support_package at: ', error)   
            }
        },

        getData (reg_des_id) {
			this.$store.dispatch('registration/getById', reg_des_id).then(data => {
                this.regData = data[0]
                this.globalNorm = this.regData.norm                
                
                const emergency_id = "444b3134-5b75-4b67-af72-a558d46716d7"
                if (this.regData.health_facility_id === emergency_id) {
                    //INSTALASI IGD
                    this.form_soap.pharmacy_depo_id = '4105d8bc-5769-463e-bc67-aae2fc5adf10'
                    this.form_exclusive_presc.pharmacy_depo_id = '4105d8bc-5769-463e-bc67-aae2fc5adf10'
                } else {
                    //INSTALASI FARMASI
                    this.form_soap.pharmacy_depo_id = '297c44f0-12ef-40e6-b9a7-c831065b3372'
                    this.form_exclusive_presc.pharmacy_depo_id = '297c44f0-12ef-40e6-b9a7-c831065b3372'
                    let costing_id = '084bd2c3-aee7-485b-b9c0-27726c7dcba2'
                    let value_id = '3249cc9f-46e0-4718-944a-adaabc3ed193'
                }    
                
                this.getTransactionOptions(this.regData.health_facility_id)
            })
        },

        getTransactionOptions(health_facility_id) {
            const inpatient_id = "40f06534-07ec-4221-ad9d-dea08196079f"
            const inpatient_item_id = "d158a64e-6246-444a-97b7-8d0a2d90e53a"
            let company_insurance_id = "e5a1e978-af77-4901-9ef8-4ebaeee77dfa"
            let paramsInsuranceServiceClassDoctor = {}
            let paramsInsuranceServiceClass = {}

            if (this.regData.registration_insurance[0].company_id === '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8') {
                company_insurance_id = this.regData.registration_insurance[0].company_id
            } else if (this.regData.registration_insurance[0].company_id === 'bb0d633f-99e1-4990-901b-0417a14e3cb6') {
                company_insurance_id = this.regData.registration_insurance[0].company_id
            } else {
                company_insurance_id = "e5a1e978-af77-4901-9ef8-4ebaeee77dfa"
            }
            // list.is_primary === 1 && list.company_id === '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8'"

            if (health_facility_id === inpatient_id) {
                paramsInsuranceServiceClassDoctor = {
                    type_costing_id: 3,
                    health_facility_item_id: inpatient_item_id,
                    company_insurance_id: company_insurance_id,
                    service_class_id: this.regData.service_class_id
                }
                paramsInsuranceServiceClass = {
                    service_class_id: this.regData.service_class_id,
                    company_insurance_id: company_insurance_id,
                }
            } else {
                paramsInsuranceServiceClassDoctor = {
                    type_costing_id: 3,
                    company_insurance_id: company_insurance_id,
                    health_facility_item_id: this.regData.health_facility_item_id
                }
                paramsInsuranceServiceClass = {
                    service_class_id: 'e7ce65ba-3d51-41aa-add7-df8f6a40e2b4',
                    company_insurance_id: company_insurance_id,
                }
            }

            this.$store.dispatch('physiotherapy/getPackageAll', paramsInsuranceServiceClass)
            this.getPackageListV2(paramsInsuranceServiceClass)
            this.$store.dispatch('costing/getParam', paramsInsuranceServiceClassDoctor)
        },

        copyAllStudies(event, obj) {
            const button = event.target;
            button.innerText = 'Copied All';
            
            // func(obj);
            this.copySubjective(obj)
            // this.copyObjective(obj)
            this.copyAssesment(obj)
            this.copyPlan(obj)
            this.copyPrescription(obj)

            setTimeout(() => {
                button.innerText = 'Copy All';
            }, 1000);
        },
        copySelected(event, func, obj) {
            const button = event.target;
            button.innerText = 'Copied';
            
            func(obj);

            setTimeout(() => {
                button.innerText = 'Copy';
            }, 1000);
        },
        copySubjective(obj) {            
            this.form_soap.anamesa_type_id = obj.anamesa_type_id
            this.form_soap.family_name = obj.family_name
            this.form_soap.family_relation_id = obj.family_relation_id
            this.form_soap.main_complaint = obj.main_complaint
            this.form_soap.history_disease_now = obj.history_disease_now
            this.form_soap.history_disease_past = obj.history_disease_past
            this.form_soap.history_drugs = obj.history_drugs
        },
        // copyObjective(obj) {
        //     // tanda vital
        //     this.form_soap.systolic_blood_pressure = obj.systolic_blood_pressure 
        //     this.form_soap.diastolic_blood_pressure = obj.diastolic_blood_pressure 
        //     this.form_soap.pulse_frequency = obj.pulse_frequency 
        //     this.form_soap.breath_frequency = obj.breath_frequency 
        //     this.form_soap.oximetry_frequency = obj.oximetry_frequency 
        //     this.form_soap.temperature = obj.temperature 
        //     this.form_soap.blood_sugar = obj.blood_sugar 

        //     // antopometri
        //     this.form_soap.weight = obj.weight 
        //     this.form_soap.height = obj.height 
        //     this.form_soap.imt = obj.imt 
        //     this.form_soap.head_circumference = obj.head_circumference 
        //     this.form_soap.chest_circumference = obj.chest_circumference 
        //     this.form_soap.arm_circumference = obj.arm_circumference 
        //     this.form_soap.stomach_circumference = obj.stomach_circumference 
        //     this.form_soap.knee_circumference = obj.knee_circumference 
        //     this.form_soap.physical_check = obj.physical_check 
        //     this.form_soap.soap_o_anythingelse = obj.soap_o_anythingelse 
        // },
        copyAssesment(obj) {          
            if (obj.icd10) {
                const mappedIcd10 = obj.icd10.map(item => {
                    return {
                        icd10_id: item.icd10_id,
                        value: item.icd10_id,
                        title: `${item.code} ${item.name}`, 
                        action: "action"
                    };
                });                
                this.form_soap.icd10 = mappedIcd10;
            }       
            this.form_soap.soap_a_anythingelse = obj.soap_a_anythingelse;
            this.form_soap.icd10_notes = obj.icd10_notes;
        },
        copyPlan(obj) {
            if (obj.icd9cm) {
                const mappedIcd9cm = obj.icd9cm.map(item => {
                    return {
                        icd9cm_id: item.icd9cm_id,
                        value: item.icd9cm_id,
                        title: `${item.code} ${item.name}`, 
                        action: "action"
                    };
                });                
                this.form_soap.icd9cm = mappedIcd9cm;
            }
            this.form_soap.therapy_plan = obj.therapy_plan;

            if (obj.transaction_doctor) {
                let list = [];
                this.form_soap.transaction_doctor = []

                for (let i = 0; i < obj.transaction_doctor.length; i++) {
                    const el = obj.transaction_doctor[i];
                    let costingIndex = '';

                    costingIndex = this.getCosting.findIndex(item => item.costing_id === el.costing_id);
                    if (costingIndex !== -1) {
                        // console.log('selected item: ', this.getCosting[costingIndex]);
                        let data = {
                            // title: this.getCosting[costingIndex].title,
                            costing_id: this.getCosting[costingIndex].costing_id,
                            costing_name: this.getCosting[costingIndex].title,
                            is_ref: this.getCosting[costingIndex].data.is_ref,
                            value: this.getCosting[costingIndex].value,
                            var_1: this.getCosting[costingIndex].costing, 
                            var_2: 1,
                            doctor_ref_id:'',
                            action: 'action',
                        }
                        list.push(data);
                    }
                }
                this.form_soap.transaction_doctor = list;
                // console.log('this.form_soap.transaction_doctor', this.form_soap.transaction_doctor);
            }
        },
        // copyPenunjang(obj) {
        //     if (obj.laboratory_request) {
        //         this.form_soap.laboratory_request_items = obj.laboratory_request
        //     }
        // }
        copyPrescription(obj) {
            if (obj) {
                let var_presc = obj.form_prescription
                let var_parent = obj.form_prescription_parent
                
                if (var_parent) {
                    const mappedPrescParent = var_parent.map(item => {
                        let name = item.id ? this.getParentName(item.id) : ''
                        return {
                            id: item.id,
                            used: item.used,
                            value: item.value,
                            prescription: name,
                        };
                    });                
                    this.form_soap.form_prescription_parent = mappedPrescParent;
                } else {
                    this.form_soap.form_prescription_parent = []
                }
                
                if (var_presc) {
                    const mappedPresc = var_presc.map(item => {
                        let name = item.id ? this.getParentName(item.id) : ''

                        return {
                            pharmacy_catalog_id: item.pharmacy_catalog_id, 
                            prescription_type_id: item.prescription_item_type_id,
                            package_id: item.package_id,
                            package_name: item.package_name,
                            dose: item.dose,
                            sum_dose: item.dose+' '+ item.dose_name, 
                            value_package: item.qty,
                            sort: item.sort, 
                            used: item.used,
                            desc: item.desc,

                            code: item.pharmacy_catalog_code,
                            name: item.pharmacy_catalog_name,
                            value_doctor: item.qty_doctor,
                            prescription_type_name: name,
                        };
                    });      
                    this.form_soap.form_prescription = mappedPresc;                                  
                } else {
                    this.form_soap.form_prescription = [];                                  
                }
            }
        },
        getMasterHemodialysis() {
            try {                
                this.$store.dispatch('hemodialysis/getListMaster', {})
                .then(
                    (response) => {
                        let data = response.rows
                        if(data && data.length > 0) {
                            for (let i = 0; i < data.length; i++) {
                                const el = data[i];
                                if (el.code == 'N') {
                                    this.OptionsDiagnose.push({ value: el.name, title: el.name })
                                } else if(el.code == 'E') {
                                    this.OptionsEtiology.push({ value: el.name, title: el.name })
                                } else if(el.code == 'P') {
                                    this.OptionsComorbidities.push({ value: el.name, title: el.name })                                    
                                }
                            }
                        }
                    }, error => {
                        // console.error('Error while GET hemodialysis/getListMaster at: ', error);
                    }
                )
            } catch (error) {
                // console.error('Error catch while GET hemodialysis/getListMaster at: ', error);
            }
        },
    },
    watch: {
        'OptionsCosting': function(val){
            if (val) {
                this.getCosting = val
                // console.log('val:', val)
            }
        },
        "form_soap.anamesa_type_id"(newValue) {
            if (newValue == "1") {
                this.form_soap.family_relation_id = ''
                this.form_soap.family_name = ''
            }
        },
        "form_soap.suspected_disease_type_id"(newValue) {
            if (newValue != "1") {
                this.form_soap.suspected_disease = ""
            }
        },
    },
    created () {
        this.getMasterHemodialysis()

        if (this.$route.params.id) {
            this.reg_des_id = this.$route.params.id
            this.getData(this.$route.params.id)
        }

        this.$store.dispatch('employee/getParamSpecialist')
        // this.$store.dispatch('radiology/getPackageAll')
        // this.$store.dispatch('catalog/getParam')
        this.$store.dispatch('packages/getParam')
        this.$store.dispatch('soap/getExaminationCategory')
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>
<style scoped>
.c-img {
    aspect-ratio: 1/1;
    object-fit: cover;
    max-width: 150px;
    max-height: 150px;
    border: 10px solid white;
}
</style>