<template>
    <div>
        <!-- <b-button @click="modalShow = !modalShow" pill variant="info-600" size="sm" class="px-16 ml-lg-2 mt-8 mt-lg-0">
            <b-icon icon="plus" /> <span>Tambah Kajian Kedokteran</span>
        </b-button> -->
        <b-modal v-model="modalShow" scrollable size="xl" title="Pengkajian Dokter" hide-footer>
            <div class="row mx-0">
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header bg-indigo-200">
                            <span class="text-bold-18">Data Pendaftaran</span>
                        </div>
                        <div class="card-body">
                            <b-table-simple>
                                <b-tbody>
                                    <b-tr>
                                        <b-td class="text-regular-14">Nomor Pendaftaran</b-td>
                                        <b-td class="text-bold-14">{{this.regData.code}}</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td class="text-regular-14">Cara Bayar</b-td>
                                        <b-td class="text-bold-14">
                                            <div v-if="this.regData.registration_insurance">
                                                <div v-for="list in this.regData.registration_insurance">
                                                    <template>
                                                        {{ list.insurance_name }} - {{ list.company_name }}
                                                    </template>
                                                    <b-badge v-if="list.is_cob == 1"class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1" variant="warning-200">COB</b-badge>
                                                </div>
                                            </div>
                                        </b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td class="text-regular-14">Cara Masuk</b-td>
                                        <b-td class="text-bold-14">Langsung</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td class="text-regular-14">No. RM</b-td>
                                        <b-td class="text-bold-14">{{this.regData.norm}}</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td class="text-regular-14">Nama</b-td>
                                        <b-td class="text-bold-14">({{this.regData.alias_name}}) {{this.regData.name}}</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td class="text-regular-14">Tanggal Lahir</b-td>
                                        <b-td class="text-bold-14">{{this.regData.birth_date}}</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td class="text-regular-14">Jenis Kelamin</b-td>
                                        <b-td class="text-bold-14">{{this.regData.sex_name}}</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td class="text-regular-14">Dokter</b-td>
                                        <b-td class="text-bold-14">{{this.regData.doctor_in_charge_name}}</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td class="text-regular-14">{{ this.regData.health_facility_name }}</b-td>
                                        <b-td class="text-bold-14">{{this.regData.health_facility_item_name}}</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td class="text-regular-14">Tanggal Daftar</b-td>
                                        <b-td class="text-bold-14">{{this.regData.register_date}}</b-td>
                                    </b-tr>
                                </b-tbody>
                            </b-table-simple>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 mt-20 mt-lg-0">
                    <!-- <div class="card">
                        <div class="card-header bg-indigo-200">
                            <span class="text-bold-18">History Kajian Dokter</span>
                        </div>
                        <div class="card-body">
                            <div class="bg-info-200 rounded p-16">
                                <p class="text-medium-14">Kajian Dokter Tanggal: 26-09-2019</p>
                                <p class="text-medium-14">Dokter: dr, dokter</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-16 mt-lg-28">
                        <b-button pill size="sm" variant="outline-indigo" class="px-3">Lihat Kajian Perawat</b-button>
                    </div> -->
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
                                <v-select @change="saveChangedSoap($event)" v-model="form_soap.anamesa_type_id" aria-readonly="true" :options="OptionsAnamesaType" :reduce="OptionsAnamesaType => OptionsAnamesaType.value" label="title" placeholder="-- Pilih Anamnesa --"></v-select>
                            </div>
                        </div>
                        <div class="row" v-if="form_soap.anamesa_type_id === 2">
                            <div class="col-lg-6 px-0">
                                <div class="row mx-0 mt-10">
                                    <div class="col-lg-6 d-lg-flex">
                                        <label class="my-auto text-semi-14">Nama Keluarga</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <b-form-input type="text" @change="saveChangedSoap($event)" v-model="form_soap.family_name" autocomplete="off"></b-form-input>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 px-0">
                                <div class="row mx-0 mt-10">
                                    <div class="col-lg-6 d-lg-flex">
                                        <label class="m-lg-auto text-semi-14">Hubungan Keluarga</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <v-select @change="saveChangedSoap($event)" v-model="form_soap.family_relation_id" aria-readonly="true" :options="OptionsFamilyRelation" :reduce="OptionsFamilyRelation => OptionsFamilyRelation.value" label="title" placeholder="-- Pilih Hubungan Keluarga --"></v-select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="my-auto text-semi-14">Keluhan Utama</label>
                            </div>
                            <div class="col">
                                <b-form-input type="text" @change="saveChangedSoap($event)" v-model="form_soap.main_complaint" autocomplete="off"></b-form-input>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="text-semi-14">Riwayat Penyakit Sekarang</label>
                            </div>
                            <div class="col">
                                <b-form-textarea rows="3" @change="saveChangedSoap($event)" v-model="form_soap.history_disease_now" autocomplete="off"></b-form-textarea>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="text-semi-14">Riwayat Penyakit Dahulu</label>
                            </div>
                            <div class="col">
                                <b-form-textarea rows="3" @change="saveChangedSoap($event)" v-model="form_soap.history_disease_past" autocomplete="off"></b-form-textarea>
                            </div>
                        </div>
                        <!-- <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="text-semi-14">Riwayat Tensi</label>
                            </div>
                            <div class="col">
                                <b-form-input type="text" @change="saveChangedSoap($event)" v-model="form_soap.history_tension" autocomplete="off"></b-form-input>
                            </div>
                        </div> -->
                        <div class="row mt-10">
                            <div class="col-lg-3 d-flex">
                                <label class="text-semi-14">Riwayat Obat</label>
                            </div>
                            <div class="col">
                                <b-form-textarea rows="3" @change="saveChangedSoap($event)" v-model="form_soap.history_drugs" autocomplete="off"></b-form-textarea>
                            </div>
                        </div>
                        <div class="row mt-24">
                            <div class="col-lg-3 d-flex">
                                <label class="text-semi-14">Riwayat Alergi</label>
                            </div>
                            <div class="col">
                                <b-button pill size="sm" variant="outline-secondary" @click="openModalAllergy()" >Tambah riwayat alergi</b-button>

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
                            <div class="col mt-32 px-0">
                                <ModernLabel label="Tanda Vital" />
                                
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="mt-1">
                                            <label class="c-form-label">Systolic</label>
                                            <b-input-group append="mmHg">
                                                <b-form-input type="number" autocomplete="off" @change="saveChangedSoap($event)" v-model="form_soap.systolic_blood_pressure"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label class="c-form-label">Diastolic</label>
                                            <b-input-group append="mmHg">
                                                <b-form-input type="number" autocomplete="off" @change="saveChangedSoap($event)" v-model="form_soap.diastolic_blood_pressure"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label class="c-form-label">Frekuensi Nadi</label>
                                            <b-input-group append="x/mnt">
                                                <b-form-input type="number" autocomplete="off" @change="saveChangedSoap($event)" v-model="form_soap.pulse_frequency"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <!-- <div class="mt-1">
                                            <label class="c-form-label">Klasifikasi</label>
                                            <v-select @change="saveChangedSoap($event)" v-model="form_soap.classification_blood_pressure_type_id" aria-readonly="true" :options="OptionsClassificationBloodPressureType" :reduce="OptionsClassificationBloodPressureType => OptionsClassificationBloodPressureType.value" label="title" placeholder="-- Pilih Klasifikasi --"></v-select>
                                        </div> -->
                                    </div>
                                    <div class="col-lg-4 border border-top-0 border-bottom-0">
                                        <div class="mt-1">
                                            <label class="c-form-label">Frekuensi Nafas</label>
                                            <b-input-group append="x/mnt">
                                                <b-form-input type="number" autocomplete="off" @change="saveChangedSoap($event)" v-model="form_soap.breath_frequency"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label class="c-form-label">Saturasi Oksigen</label>
                                            <b-input-group append="%">
                                                <b-form-input type="number" autocomplete="off" @change="saveChangedSoap($event)" v-model="form_soap.oximetry_frequency"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-1">
                                            <label class="c-form-label">Suhu</label>
                                            <b-input-group append="ᵒC">
                                                <b-form-input type="number" autocomplete="off" @change="saveChangedSoap($event)" v-model="form_soap.temperature"></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <!-- <div class="mt-1">
                                            <label class="c-form-label">Klasifikasi</label>
                                            <v-select @change="saveChangedSoap($event)" v-model="form_soap.classification_oximetry_type_id" aria-readonly="true" :options="OptionsClassificationOximetryType" :reduce="OptionsClassificationOximetryType => OptionsClassificationOximetryType.value" label="title" placeholder="-- Pilih Klasifikasi --"></v-select>
                                        </div> -->
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
                                                <b-form-input type="number" autocomplete="off" @change="saveChangedSoap($event)" v-model="form_soap.blood_sugar"></b-form-input>
                                            </b-input-group>
                                        </div>

                                        <!-- <div class="mt-1">
                                            <label class="c-form-label">Klasifikasi</label>
                                            <v-select @change="saveChangedSoap($event)" v-model="form_soap.classification_temperature_type_id" aria-readonly="true" :options="OptionsClassificationTemperatureType" :reduce="OptionsClassificationTemperatureType => OptionsClassificationTemperatureType.value" label="title" placeholder="-- Pilih Klasifikasi --"></v-select>
                                        </div> -->

                                    </div>
                                </div>
                            </div>
                            <div class="col mt-32 px-0">
                                <ModernLabel label="Antopometri" />

                                <div class="row mt-12" :class="active.advance.antopometri ? '' : 'mb-8'">
                                    <div class="col-lg-4">
                                        <label class="c-form-label">Berat Badan</label>
                                        <b-input-group append="kg">
                                            <b-form-input type="number" autocomplete="off" v-model="form_soap.weight"></b-form-input>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-4 mt-10 mt-lg-0">
                                        <label class="c-form-label">Tinggi Badan</label>
                                        <b-input-group append="cm">
                                            <b-form-input type="number" autocomplete="off" v-model="form_soap.height"></b-form-input>
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
                            <div class="col mt-32 px-0">
                                <div class="col-lg-3 d-flex">
                                    <label class="my-auto text-semi-14">Pemeriksaan Fisik</label>
                                </div>
                                <div class="col-lg-12 mct-10">
                                    <b-form-textarea rows="6" @change="saveChangedSoap($event)" v-model="form_soap.physical_check" autocomplete="off"></b-form-textarea>
                                </div>
                            </div>
                            <div class="col mt-32 px-0">
                                <div class="col-lg-3 d-flex">
                                    <label class="my-auto text-semi-14">Lainnya</label>
                                </div>
                                <div class="col-lg-12 mct-10">
                                    <b-form-textarea rows="2" @change="saveChangedSoap($event)" v-model="form_soap.soap_o_anythingelse" autocomplete="off"></b-form-textarea>
                                </div>
                            </div>
                        </div>
                        <div class="mt-32">
                            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                                style="margin-left:-16px; margin-right:-16px;">Actions</div>
                            <div class="row mx-0 mt-20">
                                <!-- <div class="px-2 my-1 my-lg-0">
                                    <b-button pill variant="outline-indigo" size="sm" class="px-3">Pemeriksaan Mata
                                        (Canvassing)</b-button>
                                </div>
                                <div class="px-2 my-1 my-lg-0">
                                    <b-button pill variant="outline-indigo" size="sm" class="px-3">Pemeriksaan Gigi
                                        (Canvassing)</b-button>
                                </div> -->
                                <div class="px-2 my-1 my-lg-0">
                                    <!-- <b-button pill variant="outline-indigo" size="sm" class="px-3">Pemeriksaan Fisik (Canvassing)</b-button> -->
                                    <CanvasContainer />
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end card-body -->
                </div>
                <!-- end card -->
            </section>
            <!-- /row-3 -->
            <section class="px-3 mt-20 mt-lg-32">
                <div class="card">
                    <div class="card-header bg-indigo-200">
                        <span class="text-bold-18">Asesment</span>
                    </div>
                    <div class="card-body">
                        <div>
                            <!-- <div class="row">
                                <div class="col-lg-3 d-flex">
                                    <label class="my-auto text-semi-14">Diagnosa</label>
                                </div>
                                <div class="col">
                                    <b-input-group class="mt-3">
                                        <b-form-input readonly></b-form-input>
                                        <b-input-group-append>
                                            <b-button variant="primary" @click="modal.diagnosa = !modal.diagnosa">
                                                <b-icon icon="search" />
                                            </b-button>
                                        </b-input-group-append>
                                    </b-input-group>
                                </div>
                            </div> -->
                            <div class="row mt-10" style="display:none;">
                                <div class="col-lg-3 d-flex">
                                    <label class="my-auto text-semi-14">Diagnosa</label>
                                </div>
                                <div class="col">
                                    <b-form-input type="text" @change="saveChangedSoap($event)" v-model="form_soap.development_notes" autocomplete="off"></b-form-input>
                                </div>
                            </div>
                            <div class="row mt-10">
                                <div class="col-lg-3 d-flex">
                                    <label class="text-semi-14">Diagnosa</label>
                                </div>
                                <div class="col">
                                    <div class="text-semi-14 card-header rounded bg-neutral-200 border-bottom-0">Diagnosa (ICD10)</div>

                                    <b-textarea rows="2" type="text" v-model="form_soap.icd10_notes" autocomplete="off" placeholder="Catatan ICD10" class="mt-10"></b-textarea>

                                    <table class="table mt-10">
                                        <tr>
                                            <th>ICD10</th>
                                            <th>Aksi</th>
                                        </tr>
                                        <tr class="item" v-for="(item,index) in form_soap.icd10">
                                            <td>
                                                <b-form-input style="display:none" v-model="item.id" />
                                                <b-form-input style="display:none" v-model="item.icd10_id" />
                                                <b-form-input type="text" v-model="item.name" aria-readonly="true" />
                                            </td>
                                            <td>
                                                <b-button variant="danger-600" size="sm" @click="delete10(index)" class="mr-2">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </td>
                                        </tr>
                                    </table>
                                    
                                    <v-select v-model="icd10_id" @input="onAdd10($event)" aria-readonly="true" @search="onSearchIcd10" :options="OptionsIcd10" :reduce="OptionsIcd10 => OptionsIcd10" label="title" placeholder="-- Diagnosa --"></v-select>
                                </div>
                            </div>
                            <div class="row mt-10">
                                <div class="col-lg-3 d-flex">
                                    <label class="my-auto text-semi-14">Lainnya</label>
                                </div>
                                <div class="col">
                                    <b-form-input type="text" @change="saveChangedSoap($event)" v-model="form_soap.soap_a_anythingelse" autocomplete="off"></b-form-input>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end card-body -->
                </div>
                <!-- end card -->
            </section>
            <section class="px-3 mt-20 mt-lg-32">
                <div class="card">
                    <div class="card-header bg-indigo-200">
                        <span class="text-bold-18">Unggah Berkas</span>
                    </div>
                    <div class="card-body">
                        <div>
                            <!-- <div class="row">
                                <div class="col-lg-3 d-flex">
                                    <label class="my-auto text-semi-14">Diagnosa</label>
                                </div>
                                <div class="col">
                                    <b-input-group class="mt-3">
                                        <b-form-input readonly></b-form-input>
                                        <b-input-group-append>
                                            <b-button variant="primary" @click="modal.diagnosa = !modal.diagnosa">
                                                <b-icon icon="search" />
                                            </b-button>
                                        </b-input-group-append>
                                    </b-input-group>
                                </div>
                            </div> -->
                            <div class="row mt-10">
                                <div class="col-lg-3 d-flex">
                                    <label class="text-semi-14">Data Pemeriksaan USG</label>
                                </div>
                            </div>
                            <div class="row mt-3 c-form">
                                <div class="col-lg-4 mt-1">
                                    <label>Tanggal Pemeriksaan</label>
                                    <b-form-input type="date" @change="saveChangedSoap($event)" v-model="form_soap.obgyn_diag_date" autocomplete="off"></b-form-input>
                                </div>
                                <div class="col-lg-4 mt-1">
                                    <label>Diagnosis Klinis</label>
                                    <b-form-input type="text" @change="saveChangedSoap($event)" v-model="form_soap.obgyn_diag_clinis" autocomplete="off"></b-form-input>
                                </div>
                                <div class="col-lg-4 mt-1">
                                    <label>Hari Pertama Haid Terakhir (HPHT)*</label>
                                    <b-form-input type="date" @change="saveChangedSoap($event)" v-model="form_soap.obgyn_diag_hpht" autocomplete="off"></b-form-input>
                                </div>
                                <div class="col-lg-4 mt-1">
                                    <label>Hamil</label>
                                    <v-select @change="saveChangedSoap($event)" v-model="form_soap.obgyn_s_d" aria-readonly="true" :options="OptionsPregnant" :reduce="OptionsPregnant => OptionsPregnant.value" label="title" placeholder="-- Pilih Kehamilan --"></v-select>
                                </div>
                                <div class="col-lg-4 mt-1">
                                    <label>Keadaan Janin</label>
                                    <b-form-input type="text" @change="saveChangedSoap($event)" v-model="form_soap.obgyn_j_exam" autocomplete="off"></b-form-input>
                                </div>
                                <div class="col-lg-4 mt-1">
                                    <label>Letak</label>
                                    <b-form-input type="text" @change="saveChangedSoap($event)" v-model="form_soap.obgyn_j_pos" autocomplete="off"></b-form-input>
                                </div>
                                <div class="col-lg-4 mt-1">
                                    <label>Plasenta</label>
                                    <b-form-input type="text" @change="saveChangedSoap($event)" v-model="form_soap.obgyn_j_plas" autocomplete="off"></b-form-input>
                                </div>
                                <div class="col-lg-4 mt-1">
                                    <label>Ketuban</label>
                                    <b-form-input type="text" @change="saveChangedSoap($event)" v-model="form_soap.obgyn_j_k" autocomplete="off"></b-form-input>
                                </div>
                                <div class="col-lg-4 mt-1">
                                    <label>Kelamin</label>
                                    <v-select @change="saveChangedSoap($event)" v-model="form_soap.obgyn_j_sex" aria-readonly="true" :options="OptionsSex" :reduce="OptionsSex => OptionsSex.value" label="title" placeholder="-- Pilih Jenis Kelamin --"></v-select>
                                </div>
                                <div class="col-lg-4 mt-1">
                                    <label>Berat</label>
                                    <b-input-group append="kg">
                                        <b-form-input type="text" @change="saveChangedSoap($event)" v-model="form_soap.obgyn_j_weight" autocomplete="off"></b-form-input>
                                    </b-input-group>
                                </div>
                                <div class="col-lg-4 mt-1">
                                    <label>Ukuran USG</label>
                                    <v-select @change="saveChangedSoap($event)" v-model="form_soap.obgyn_size_usg" aria-readonly="true" :options="OptionsSizeUSG" :reduce="OptionsSizeUSG => OptionsSizeUSG.value" label="title" placeholder="-- Pilih Ukuran USG --"></v-select>
                                </div>
                                <div class="col-lg-4 mt-1">
                                    <label>Foto</label>
                                    <b-form-file autocomplete="off"  @change="inputImageUSG"></b-form-file>
                                </div>
                                <div class="col-lg-12 mt-1">
                                    <b-img ref="previewEl" rounded :src="form_soap.obgyn_usg_file_url"/>
                                </div>
                                <div class="col-lg-12 mt-1">
                                    <label>Kesan</label>
                                    <b-textarea rows="2" type="text" @change="saveChangedSoap($event)" v-model="form_soap.obgyn_usg_desc" autocomplete="off"></b-textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end card-body -->
                </div>
                <!-- end card -->
            </section>
            <!-- /row-4 -->
            <section class="px-3 mt-20 mt-lg-32">
                <div class="card">
                    <div class="card-header bg-indigo-200">
                        <span class="text-bold-18">Plan</span>
                    </div>
                    <div class="card-body">
                        <div>
                            <div class="row mt-10">
                                <div class="col-lg-3 d-flex">
                                    <label class="text-semi-14">Pemeriksaan & Tindakan</label>
                                </div>
                                <div class="col">
                                    <ModernLabel label="Tindakan" />
                                    <table class="table">
                                        <tr>
                                            <th>Nama Tindakan</th>
                                            <th width="10%">Qty</th>
                                            <th>Aksi</th>
                                        </tr>
                                        <tr class="item" v-for="(item, index) in form_soap.transaction_doctor">
                                            <td>
                                                <b-form-input style="display:none" v-model="item.value" />
                                                <b-form-input style="display:none" v-model="item.var_1" />
                                                <b-form-input style="display:none" v-model="item.qty" />
                                                <!-- <b-form-input type="text" v-model="item.title" aria-readonly="true" /> -->
                                                 {{ item.costing_name }}
                                                <v-select v-if="item.is_ref===1" v-model="item.doctor_ref_id" aria-readonly="true" :options="OptionsDoctorRef" :reduce="OptionsDoctorRef => OptionsDoctorRef.value" label="title" placeholder="-- Pilih Dokter --"></v-select>
                                            </td>
                                            <!-- <td> {{ item.var_1 | currency }} </td> -->
                                            <td><b-form-input type="number" v-model="item.var_2" aria-readonly="true" /></td>
                                            <td>
                                                <b-button variant="danger-600" size="sm" @click="deleteSC(index)" class="mr-2">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </td>
                                        </tr>
                                    </table>
                                    
                                    <v-select v-model="costing_id" @input="onAddSC($event)" aria-readonly="true" :options="OptionsCosting" :reduce="OptionsCosting => OptionsCosting" label="title" placeholder="-- Pemeriksaan & Tindakan --"></v-select>

                                    <small class="text-muted"><b>*WAJIB</b> diisi. Boleh diisi lebih dari satu</small>
                                </div>
                            </div>
                            <div class="row mt-10">
                                <div class="col-lg-3 d-flex">
                                    <label class="text-semi-14">Rencana Terapi</label>
                                </div>
                                <div class="col">
                                    <div class="text-semi-14 card-header rounded bg-neutral-200 border-bottom-0">Tindakan (ICD9CM)</div>
                                    <div class="mt-10">
                                        <b-textarea rows="2" v-model="form_soap.therapy_plan" autocomplete="off" placeholder="Catatan ICD9CM"></b-textarea>
                                    </div>
                                    <table class="table mt-10">
                                        <tr>
                                            <th>ICD9CM</th>
                                            <th>Aksi</th>
                                        </tr>
                                        <tr class="item" v-for="(item, index) in form_soap.icd9cm">
                                            <td>
                                                <b-form-input style="display:none" v-model="item.icd9cm_id" />
                                                <b-form-input type="text" v-model="item.name" aria-readonly="true" />
                                            </td>
                                            <td>
                                                <b-button variant="danger-600" size="sm" @click="delete9cm(index)" class="mr-2">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </td>
                                        </tr>
                                    </table>
                                    
                                    <v-select v-model="icd9cm_id" @input="onAdd9cm($event)" aria-readonly="true" @search="onSearchIcd9cm" :options="OptionsIcd9cm" :reduce="OptionsIcd9cm => OptionsIcd9cm" label="title" placeholder="-- ICD 9 --"></v-select>

                                </div>
                            </div>
                        </div>
                        <section class="mt-32 d-flex flex-wrap">
                            <b-button pill size="sm" :variant="active.Tab === 'menu1' ? 'indigo' : 'outline-indigo'"
                                class="m-1 px-3 text-semi-14-on" @click="goToTab('menu1')">
                                Rencana Laboratorium
                            </b-button>
                            <b-button pill size="sm" :variant="active.Tab === 'menu2' ? 'indigo' : 'outline-indigo'"
                                class="m-1 px-3 text-semi-14-on" @click="goToTab('menu2')">
                                Rencana Radiologi
                            </b-button>
                            <b-button pill size="sm" :variant="active.Tab === 'menu3' ? 'indigo' : 'outline-indigo'"
                                class="m-1 px-3 text-semi-14-on" @click="goToTab('menu3')">
                                Rencana Fisioterapi
                            </b-button>
                            <!-- <b-button pill size="sm" :variant="active.Tab === 'menu4' ? 'indigo' : 'outline-indigo'"
                                class="m-1 px-3 text-semi-14-on" @click="goToTab('menu4')">
                                Konsul
                            </b-button>
                            <b-button pill size="sm" :variant="active.Tab === 'menu5' ? 'indigo' : 'outline-indigo'"
                                class="m-1 px-3 text-semi-14-on" @click="goToTab('menu5')">
                                Resep Obat
                            </b-button>
                            <b-button pill size="sm" :variant="active.Tab === 'menu6' ? 'indigo' : 'outline-indigo'"
                                class="m-1 px-3 text-semi-14-on" @click="goToTab('menu6')">
                                Order OK
                            </b-button> -->
                        </section>
                        <section v-if="active.Tab === 'menu1'" class="py-20 row" :class="form_soap.laboratory_request && form_soap.laboratory_request.status == 2 || form_soap.laboratory_request && form_soap.laboratory_request.status == 3 ? 'bg-warning-100': ''">
                            <div class="col">
                                <div v-if="form_soap.laboratory_request && form_soap.laboratory_request.status == 2 || form_soap.laboratory_request && form_soap.laboratory_request.status == 3" class="card-body bg-warning mb-16">
                                    <div class="d-flex align-items-center">
                                        <b-icon-exclamation-triangle-fill variant="neutral-800" style="width: 24px; height: 24px;"/>
                                        <div class="ml-3 font-italic">Form penunjang laboratorium tidak dapat diubah karena sudah diproses oleh laboratorium</div>
                                    </div>
                                </div>
                                <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0">Laboratorium</div>
                                <div class="mt-20">
                                    <b-form-input type="text" v-model="searchlab" placeholder="Pencarian"></b-form-input>
                                    <div class="col-lg-12 mt-3">
                                        <h5>Checked Items:</h5>
                                        <b-form-checkbox-group v-model="form_soap.laboratory_request_items" :disabled="form_soap.laboratory_request && form_soap.laboratory_request.status == 2 || form_soap.laboratory_request && form_soap.laboratory_request.status == 3" >
                                            <b-form-checkbox
                                            v-for="(item, index) in form_soap.laboratory_request_items"
                                            :key="index"
                                            :value="item"
                                            >
                                            {{ item.costing_name }}
                                            </b-form-checkbox>
                                        </b-form-checkbox-group>
                                    </div>
                                    <div class="col mt-12 py-2 bg-neutral-100" style="height: 400px; overflow-y: scroll;">
                                        <div class="row">
                                            <b-form-checkbox-group size="sm" stacked>
                                                <b-form-checkbox-group class="col-lg-12" v-model="form_soap.laboratory_request_items" :disabled="form_soap.laboratory_request && form_soap.laboratory_request.status == 2">
                                                    <div v-for="(item, index) in filteredListLab" :key="index" :class="index != 0 ? 'mt-32' : 'mt-0'">
                                                        <div class="font-weight-bold text-uppercase">{{ item.category_name }}</div>
                                                        <div class="row mx-0"> 
                                                            <b-form-checkbox v-for="(list, packIndex) in item.category_items" v-bind:key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'laboratory_package_id':list.id, 'costing_name':list.costing_name}" class="col-lg-5">{{list.costing_name }}</b-form-checkbox>
                                                        </div>
                                                    </div>
                                                </b-form-checkbox-group>
                                            </b-form-checkbox-group>
                                            <!-- <b-form-checkbox-group size="sm" stacked>
                                                <b-form-checkbox-group class="col-lg-12" v-model="form_soap.laboratory_request_items" :disabled="form_soap.laboratory_request && form_soap.laboratory_request.status == 2">
                                                    <b-form-checkbox v-for="(il, index) in filteredListLab" v-bind:key="index" :value="{'service_class_id':'9b702245-79c5-4beb-8218-53afb08647aa','laboratory_package_id':il.id, 'costing_name':il.costing_name}" class="col-lg-5">{{il.costing_name }}</b-form-checkbox>
                                                </b-form-checkbox-group>
                                            </b-form-checkbox-group> -->
                                        </div>
                                    </div>
                                    <!-- <div class="mt-20">
                                        <label class="c-form-label">Indikasi Klinis Laboratorium</label>
                                        <b-form-textarea rows="3" v-model="form_soap.laboratory_clinical_indications" autocomplete="off" />
                                    </div> -->
                                </div>
                            </div>
                        </section>
                        <section v-if="active.Tab === 'menu2'" class="py-20 row" :class="form_soap.radiology_request && form_soap.radiology_request.status == 2 || form_soap.radiology_request && form_soap.radiology_request.status == 3 ? 'bg-warning-100': ''">
                            <div class="col">
                                <div v-if="form_soap.radiology_request && form_soap.radiology_request.status == 2 || form_soap.radiology_request && form_soap.radiology_request.status == 3" class="card-body bg-warning mb-16">
                                    <div class="d-flex align-items-center">
                                        <b-icon-exclamation-triangle-fill variant="neutral-800" style="width: 24px; height: 24px;"/>
                                        <div class="ml-3 font-italic">Form penunjang radiologi tidak dapat diubah karena sudah diproses oleh radiologi</div>
                                    </div>
                                </div>
                                <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0">Radiologi</div>
                                <div class="mt-20">
                                    <b-form-input type="text" v-model="searchrad" placeholder="Pencarian"></b-form-input>
                                    <div class="col-lg-12 mt-3">
                                        <h5>Checked Items:</h5>
                                        <b-form-checkbox-group v-model="form_soap.radiology_request_items" :disabled="form_soap.radiology_request && form_soap.radiology_request.status == 2 || form_soap.radiology_request && form_soap.radiology_request.status == 3">
                                            <b-form-checkbox
                                            v-for="(item, index) in form_soap.radiology_request_items"
                                            :key="index"
                                            :value="item"
                                            >
                                            {{ item.costing_name ? item.costing_name : item.name }}
                                            </b-form-checkbox>
                                        </b-form-checkbox-group>
                                    </div>
                                    <div class="col mt-12 py-2 bg-neutral-100" style="height: 400px; overflow-y: scroll;">
                                        <div class="row">
                                            <b-form-checkbox-group size="sm" stacked>
                                                <b-form-checkbox-group class="col-lg-12" v-model="form_soap.radiology_request_items" :disabled="form_soap.radiology_request && form_soap.radiology_request.status == 2">
                                                    <div v-for="(item, index) in filteredListRad" :key="index" :class="index != 0 ? 'mt-32' : 'mt-0'">
                                                        <div class="font-weight-bold text-uppercase">{{ item.category_name }}</div>
                                                        <div class="row mx-0"> 
                                                            <b-form-checkbox v-for="(list, packIndex) in item.category_items" v-bind:key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'radiology_package_id':list.id, 'costing_name':list.costing_name}" class="col-lg-5">{{list.costing_name }}</b-form-checkbox>
                                                        </div>
                                                    </div>
                                                </b-form-checkbox-group>
                                            </b-form-checkbox-group>
                                            <!-- <b-form-checkbox-group size="sm" stacked>
                                                <b-form-checkbox-group class="col-lg-12" v-model="form_soap.radiology_request_items" :disabled="form_soap.radiology_request && form_soap.radiology_request.status == 2">
                                                        <b-form-checkbox v-for="(ir, index) in filteredListRad" v-bind:key="index" :value="{'service_class_id':'9b702245-79c5-4beb-8218-53afb08647aa','radiology_package_id':ir.id, 'name': ir.costing_name}" class="col-lg-5">{{ir.costing_name }}</b-form-checkbox>
                                                </b-form-checkbox-group>
                                            </b-form-checkbox-group> -->
                                        </div>
                                    </div>
                                    <!-- <div class="mt-20">
                                        <label class="c-form-label">Indikasi Klinis Radiologi</label>
                                        <b-form-textarea rows="3" v-model="form_soap.radiology_clinical_indications" autocomplete="off" />
                                    </div> -->
                                </div>
                            </div>
                        </section>
                        <section v-if="active.Tab === 'menu3'" class="mt-20">
                            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                                style="margin-left:-16px; margin-right:-16px;">Fisioterapi</div>
                            <div class="mt-20">
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
                                <!-- <b-form-input type="text" placeholder="cari laboratorium"></b-form-input> -->
                                <div class="col mt-12 py-2 bg-neutral-100" style="height: 200px; overflow-y: scroll;">
                                    <b-form-checkbox-group size="sm" stacked>
                                        <div class="row">
                                            <b-form-checkbox-group class="col-lg-12" v-model="form_soap.physiotherapy_request_items">
                                                    <b-form-checkbox v-for="(ip, index) in filteredListPhy" :key="index" :value="{'service_class_id':'9b702245-79c5-4beb-8218-53afb08647aa','physiotherapy_package_id': ip.id, 'costing_name': ip.costing_name}" class="col-lg-5">{{ip.costing_name }}</b-form-checkbox>
                                                    <!-- Kelas 2 -->
                                                    <!-- <br> Tarif: {{ ip.costing_services_class.var_1 | currency }} -->
                                            </b-form-checkbox-group>
                                            <!-- <template v-for="ip in filteredListPhy">
                                                <b-form-checkbox v-model="form_soap.physiotherapy_request_items" :key="ip.id" :value="{'service_class_id':'9b702245-79c5-4beb-8218-53afb08647aa','physiotherapy_package_id':ip.id}">{{ ip.name }} 
                                                </b-form-checkbox>
                                            </template> -->
                                        </div>
                                    </b-form-checkbox-group>
                                </div>
                            </div>
                        </section>
                        <section v-if="active.Tab === 'menu4'" class="mt-20">
                            <div class="d-flex flex-column flex-lg-row card-header rounded bg-neutral-200 border-bottom-0" style="margin-left:-16px; margin-right:-16px;">
                                <span class="text-semi-18">Konsul</span>
                                <b-form-checkbox-group size="sm" v-model="CheckConsult" class="ml-lg-auto">
                                    <b-form-checkbox value="poli">Konsultasi ke poli lain</b-form-checkbox>
                                    <b-form-checkbox value="selanjutnya">Konsultasi selanjutnya</b-form-checkbox>
                                </b-form-checkbox-group>
                            </div>                            
                            <!-- here -->
                            <div class="mt-20 row" v-if="CheckConsult.includes('poli')">
                                <div class="col-lg-6">
                                    <div class="mt-10 mt-lg-0">
                                        <label class="c-form-label">Tanggal & Jam Regis</label>
                                        <b-form-datepicker></b-form-datepicker>
                                    </div>
                                    <ModernLabel label="Konsultasi" class="mt-16 mt-lg-20" />
                                    <b-form-radio-group size="sm" class="mt-16">
                                        <b-form-radio value="1">Biasa</b-form-radio>
                                        <b-form-radio value="2">Emergensi</b-form-radio>
                                    </b-form-radio-group>
                                    <b-form-checkbox-group size="sm" class="mt-10">
                                        <b-form-checkbox value="1">Konsul Satu Kali</b-form-checkbox>
                                        <b-form-checkbox value="2">Rawat Bersama</b-form-checkbox>
                                        <b-form-checkbox value="3">Alih Rawat</b-form-checkbox>
                                        <b-form-checkbox value="4">Toleransi Operasi</b-form-checkbox>
                                    </b-form-checkbox-group>
                                    <div class="mt-10">
                                        <label class="c-form-label">Dokter</label>
                                        <b-form-select></b-form-select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mt-10 mt-lg-0">
                                        <label class="c-form-label">Tanggal & Jam Konsul</label>
                                        <b-form-datepicker></b-form-datepicker>
                                    </div>
                                    <div class="mt-10">
                                        <label class="c-form-label">Poli Tujuan</label>
                                        <b-form-select></b-form-select>
                                    </div>
                                    <div class="mt-10">
                                        <label class="c-form-label">Masalah</label>
                                        <b-textarea></b-textarea>
                                    </div>
                                </div>
                            </div>
                            <!-- here -->
                            <div class="mt-20 row" v-if="CheckConsult.includes('selanjutnya')">
                                <div class="col-lg-6">
                                    <div class="mt-10 mt-lg-0">
                                        <label class="c-form-label">Tanggal Konsul</label>
                                        <b-form-datepicker></b-form-datepicker>
                                    </div>
                                    <div class="mt-10">
                                        <label class="c-form-label">Dokter</label>
                                        <b-form-select></b-form-select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mt-10 mt-lg-0">
                                        <label class="c-form-label">Poli Tujuan</label>
                                        <b-form-select></b-form-select>
                                    </div>
                                    <div class="mt-10">
                                        <label class="c-form-label">Catatan</label>
                                        <b-textarea></b-textarea>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <section v-if="active.Tab === 'menu5'" class="mt-20">
                            <div>
                                <div class="card-header rounded bg-neutral-200 border-bottom-0" style="margin-left:-16px; margin-right:-16px;">
                                    <span class="text-semi-18">Resep</span>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 mt-10">
                                        <label class="c-form-label">Dokter</label>
                                        <b-form-select></b-form-select>
                                    </div>
                                    <div class="col-lg-6 mt-10">
                                        <label class="c-form-label">Tanggal Resep</label>
                                        <b-form-datepicker></b-form-datepicker>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-column py-1 flex-lg-row justify-content-lg-between mt-32 card-header rounded bg-neutral-200 border-bottom-0">
                                <span class="text-semi-16">Resep</span>
                                <div class="row mx-0">
                                    <b-button pill size="sm" variant="indigo">Obat Non Racik & Racik</b-button>
                                    <b-button pill size="sm" variant="outline-indigo" class="mx-2">
                                        <b-icon icon="camera" class="mr-1" /> Klik Untuk Obat Racik
                                    </b-button>
                                    <b-button pill size="sm" variant="outline-indigo">
                                        <b-icon icon="file-earmark-text" class="mr-1" /> Template Resep
                                    </b-button>
                                </div>
                            </div>
                            <div class="mt-20">
                                <div class="">
                                    <div ref="line-1" class="row">
                                        <div class="col-1 text-center">
                                            <label class="c-form-label">No</label>
                                            <p class="text-bold-14">1</p>
                                        </div>
                                        <div class="col px-0">
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <label class="c-form-label">Aturan pakai</label>
                                                        <p class="text-regular-14">Paracetamol 120/5 ml Syrup 60 ml</p>
                                                        <div class="d-flex mt-1">
                                                            <b-button size="sm" pill variant="warning-500" class="px-3 mr-2">Stock</b-button>
                                                            <b-button size="sm" pill variant="info-700" class="px-3 text-white">
                                                                <b-icon icon="arrow-repeat" /> <span>Katalog Pengganti</span>
                                                            </b-button>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg d-flex align-items-lg-end">
                                                        <div class="mt-10 mt-lg-0">
                                                            <label class="c-form-label">Aturan Pakai</label>
                                                            <div class="row">
                                                                <b-input-group class="col-lg">
                                                                    <b-input-group-prepend>
                                                                        <b-form-input type="number" style="border-radius: 4px 0 0 4px;"></b-form-input>
                                                                        <span class="input-group-text">X</span>
                                                                    <b-form-input type="number" style="border-radius: 0 4px 4px 0;"></b-form-input>
                                                                </b-input-group-prepend>
                                                                </b-input-group>
                                                                <b-input-group append="ML" class="col-lg mt-10 mt-lg-0">
                                                                    <b-form-input></b-form-input>
                                                                </b-input-group>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 mt-lg-20">
                                                <div class="row">
                                                    <!-- here -->
                                                    <div class="col-lg-3 mt-10 mt-lg-0">
                                                        <label class="c-form-label">AC/DC/PC</label>
                                                        <b-form-select></b-form-select>
                                                    </div>
                                                    <div class="col-lg-3 mt-10 mt-lg-0">
                                                        <label class="c-form-label">Keterangan</label>
                                                        <b-form-input></b-form-input>
                                                    </div>
                                                    <div class="col-lg-3 mt-10 mt-lg-0">
                                                        <label class="c-form-label">Jumlah</label>
                                                        <b-input-group append="BTL">
                                                            <b-form-input></b-form-input>
                                                        </b-input-group>
                                                    </div>
                                                    <div class="col-lg-3 mt-20 mt-lg-0 d-flex justify-content-end justify-content-lg-start align-items-lg-end">
                                                        <div class="row mx-0">
                                                            <b-button size="sm" variant="danger-500" class="mr-2">
                                                                <b-icon icon="trash" class="mr-1" /> <span>Hapus</span> 
                                                            </b-button>
                                                            <b-button size="sm" variant="warning-500">
                                                                <b-icon icon="pencil" class="mr-1" /> <span>Edit</span> 
                                                            </b-button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div ref="line-2" class="row mt-32">
                                        <div class="col-1 text-center">
                                            <label class="c-form-label">No</label>
                                            <p class="text-bold-14">2</p>
                                        </div>
                                        <div class="col d-flex align-items-lg-end">
                                            <b-form-select class="col-lg-4"></b-form-select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-32">
                                    <b-table-simple responsive small>
                                        <b-thead class="text-bold-14">
                                            <b-tr>
                                                <b-td>No</b-td>
                                                <b-td style="min-width: 300px; max-width: 400px;">katalog Obat Racik</b-td>
                                                <b-td style="min-width: 150px; max-width: 200px;" class="px-3">Dosis Satuan</b-td>
                                                <b-td class="px-3">Aksi</b-td>
                                            </b-tr>
                                        </b-thead>
                                        <b-tbody>
                                            <b-tr>
                                                <b-td>1</b-td>
                                                <b-td>
                                                    <div class="">
                                                        <span class="text-regular-14">Paracetamol 120/5 ml Syrup 60 ml</span>   
                                                        <div class="mt-2">
                                                            <b-button size="sm" pill variant="warning-500" class="px-3 mr-2">Stock</b-button>
                                                            <b-button size="sm" pill variant="info-700" class="px-3 text-white">
                                                                <b-icon icon="arrow-repeat" /> <span>Katalog Pengganti</span>
                                                            </b-button>
                                                        </div>
                                                    </div>
                                                </b-td>
                                                <b-td class="px-3">
                                                    <b-input-group append="Tab">
                                                        <b-form-input></b-form-input>
                                                    </b-input-group>
                                                </b-td>
                                                <b-td class="px-3">
                                                    <div class="d-flex">
                                                        <b-button size="sm" variant="danger-500" class="mr-2">
                                                            <b-icon icon="trash" class="mr-1" /> <span>Hapus</span> 
                                                        </b-button>
                                                        <b-button size="sm" variant="warning-500">
                                                            <b-icon icon="pencil" class="mr-1" /> <span>Edit</span> 
                                                        </b-button>
                                                    </div>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>2</b-td>
                                                <b-td>
                                                    <div class="">
                                                        <span class="text-regular-14">Amoxillin 500 mg tablet</span>   
                                                        <div class="mt-2">
                                                            <b-button size="sm" pill variant="warning-500" class="px-3 mr-2">Stock</b-button>
                                                            <b-button size="sm" pill variant="info-700" class="px-3 text-white">
                                                                <b-icon icon="arrow-repeat" /> <span>Katalog Pengganti</span>
                                                            </b-button>
                                                        </div>
                                                    </div>
                                                </b-td>
                                                <b-td class="px-3">
                                                    <b-input-group append="Tab">
                                                        <b-form-input></b-form-input>
                                                    </b-input-group>
                                                </b-td>
                                                <b-td class="px-3">
                                                    <div class="d-flex">
                                                        <b-button size="sm" variant="danger-500" class="mr-2">
                                                            <b-icon icon="trash" class="mr-1" /> <span>Hapus</span> 
                                                        </b-button>
                                                        <b-button size="sm" variant="warning-500">
                                                            <b-icon icon="pencil" class="mr-1" /> <span>Edit</span> 
                                                        </b-button>
                                                    </div>
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td>3</b-td>
                                                <b-td colspan="3">
                                                    <div class="col-4 px-0">
                                                        <b-form-select></b-form-select>
                                                    </div>
                                                </b-td>
                                            </b-tr>
                                        </b-tbody>
                                    </b-table-simple>
                                </div>
                                <div class="mt-32">
                                    <div>
                                        <label class="c-form-label">keterangan</label>
                                        <b-form-input placeholder="keterangan"></b-form-input>
                                    </div>
                                    <b-form-checkbox-group switches class="mt-12">
                                        <b-form-checkbox value="1">Simpan sebagai template</b-form-checkbox>
                                        <b-form-checkbox value="2">CITO</b-form-checkbox>
                                    </b-form-checkbox-group>
                                    <div class="col-lg-3 px-0 mt-12">
                                        <b-form-input></b-form-input>
                                    </div>
                                    <p class="text-semi-14 mt-40">Pastikan inputan Order Obat sudah benar. kesalahan inputan dapat mempengaruhi hasil pengamatan maupun tindakan yang diberikan kepada pasien.</p>
                                </div>
                                <div class="d-flex justify-content-lg-end mt-40">
                                    <b-button pill size="sm" variant="outline-indigo" class="col col-lg-2">
                                        <b-icon icon="x" /> Reset
                                    </b-button>
                                    <b-button pill size="sm" variant="indigo" class="col col-lg-2 ml-1">Update</b-button>
                                </div>
                            </div>
                        </section>
                        <section v-if="active.Tab === 'menu6'" class="mt-20">
                            <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0"
                                style="margin-left:-16px; margin-right:-16px;">Order OK</div>
                            <div class="row mt-20">
                                <div class="col-lg-6">
                                    <div class="mt-10 mt-lg-0">
                                        <label class="c-form-label">Rencana Operasi</label>
                                        <b-form-datepicker></b-form-datepicker>
                                    </div>
                                    <div class="mt-10">
                                        <label class="c-form-label">Kelas</label>
                                        <b-form-select></b-form-select>
                                    </div>
                                    <div class="mt-10">
                                        <label class="c-form-label">Operator</label>
                                        <b-form-tags input-id="tags-basic"></b-form-tags>
                                        <small class="text-success-500">*Bisa lebih dari satu</small>
                                    </div>
                                    <div class="mt-10">
                                        <label class="c-form-label">Permintaan Akomodasi</label>
                                        <b-form-input></b-form-input>
                                    </div>
                                    <div class="mt-10">
                                        <label class="c-form-label">Tanggal ACC Pre - OP</label>
                                        <b-form-datepicker></b-form-datepicker>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mt-10 mt-lg-0">
                                        <label class="c-form-label">Diagnosa</label>
                                        <b-form-input></b-form-input>
                                        <small class="text-success-500">*Bisa lebih dari satu</small>
                                    </div>
                                    <div class="mt-10">
                                        <label class="c-form-label">Jenis Operasi</label>
                                        <b-form-select></b-form-select>
                                    </div>
                                    <div class="mt-10">
                                        <label class="c-form-label">Prioritas</label>
                                        <b-form-select></b-form-select>
                                    </div>
                                    <div class="mt-10">
                                        <label class="c-form-label">Infectisous</label>
                                        <b-form-select></b-form-select>
                                    </div>
                                    <div class="mt-10">
                                        <label class="c-form-label">Rencana Tindakan Operasi</label>
                                        <b-form-select></b-form-select>
                                    </div>
                                    <div class="mt-10">
                                        <b-table-simple small>
                                            <b-thead class="bg-neutral-200 text-bold-14">
                                                <b-tr>
                                                    <b-td>Procedure</b-td>
                                                    <b-td>Action</b-td>
                                                </b-tr>
                                            </b-thead>
                                            <b-tbody class="text-regular-14">
                                                <b-tr>
                                                    <b-td>BEDAH UMUM - Abcases inci</b-td>
                                                    <b-td>
                                                        <b-button size="sm" variant="danger-500"><b-icon icon="x"/></b-button>
                                                    </b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>BEDAH UMUM - Abcases inci</b-td>
                                                    <b-td>
                                                        <b-button size="sm" variant="danger-500"><b-icon icon="x"/></b-button>
                                                    </b-td>
                                                </b-tr>
                                            </b-tbody>
                                        </b-table-simple>
                                    </div>
                                </div>

                            </div>
                        </section>
                    </div>
                    <!-- end card-body -->
                </div>
            </section>
            <section class="px-3 mt-20 mt-lg-32">
                <div class="card">
                    <div class="card-header bg-indigo-200">
                        <span class="text-bold-18">Resep</span>
                    </div>
                    <div class="card-body" :class="form_soap.prescription.flag == 1 ? 'bg-warning-100' : ''">
                        <div v-if="form_soap.prescription.flag == 1" class="card-body bg-warning">
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
                                                <td v-if="item_p.id !== 1">
                                                    <b-input-group append="Jumlah">
                                                        <b-form-input :disabled="form_soap.prescription.flag == 1" style="width:150px" type="number" v-model="item_p.value" />
                                                    </b-input-group>
                                                    <b-input-group append="Aturan Pakai">
                                                        <b-form-input :disabled="form_soap.prescription.flag == 1" style="width:150px" type="text" v-model="item_p.used" />
                                                    </b-input-group>
                                                </td>
                                            </tr>
                                            <!-- {{ item_p.prescription }} {{ item_p.id }} -->
                                            <!-- {{ form_soap.form_prescription }} -->
                                            <tr class="item" v-for="(item, index) in form_soap.form_prescription" v-if="item_p.id === item.prescription_type_id">
                                                <!-- <td>
                                                    <b-form-checkbox v-model="item.index" />
                                                </td> -->
                                                <td>
                                                    <!-- :disabled="form_soap.prescription.flag == 1" -->
                                                    <!-- {{form_soap.prescription.flag}} -->
                                                    <b-button :disabled="form_soap.prescription.flag == 1" variant="danger-600" size="sm" @click="deletePRES(index)" class="mr-2">
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </td>
                                                <td>
                                                    {{ item.name }}
                                                </td>
                                                <td>
                                                    <b-form-input disabled style="width:100px" type="text" v-model="item.sum_dose" />
                                                </td>
                                                <td>
                                                    <template v-if="item.prescription_type_id === 1">
                                                        <b-form-input :disabled="form_soap.prescription.flag == 1" style="width:100px" readonly type="number" v-model="item.value_doctor" @change="vdChanged($event, index)"/>
                                                    </template>
                                                    <template v-if="item.prescription_type_id !== 1">
                                                        <b-form-input :disabled="form_soap.prescription.flag == 1" style="width:100px" type="number" v-model="item.value_doctor" @change="vdChanged($event, index)"/>
                                                    </template>
                                                </td>
                                                <td>
                                                    <b-form-input :disabled="form_soap.prescription.flag == 1" style="width:100px" type="number" v-model="item.value_package" />
                                                </td>
                                                <td>
                                                    <v-select :disabled="form_soap.prescription.flag == 1" v-model="item.package_id" :options="PackageOptions" :reduce="PackageOptions => PackageOptions.value" label="title" placeholder="Kemasan"></v-select>
                                                </td>
                                                <td>
                                                    <b-input-group append="Aturan Pakai" v-if="item.prescription_type_id === 1">
                                                        <b-form-input :disabled="form_soap.prescription.flag == 1" style="width:150px" type="text" v-model="item.used" />
                                                    </b-input-group>
                                                    <b-input-group append="Keterangan">
                                                        <b-form-input :disabled="form_soap.prescription.flag == 1" style="width:150px" type="text" v-model="item.desc" />
                                                    </b-input-group>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr>
                                            <td></td>
                                            <td>
                                                <template v-if="form_soap.prescription.flag == 0 || typeof form_soap.prescription == 'object'">
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
                <!-- end card -->
            </section>
            <section class="col mt-24">
                <div>
                    <label class="c-form-label">Advice</label>
                    <b-form-textarea rows="3" v-model="form_soap.advice" autocomplete="off" />
                </div>
            </section>
            <section class="px-3 mt-20 mt-lg-32">
                <div class="row mx-0 text-center text-lg-left">
                    <div class="col-12 col-lg-2">
                        <img src="@/@core/assets/images/nurse/img-doctor.jpg" alt="your-photo"
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
                    <i><img src="@/@core/assets/icons/nurse/save.svg" alt="save" class="mr-1"></i>
                    <span>Simpan</span>
                </b-button>
            </div>
        </b-modal>
        <b-modal v-model="modal.allergy" scrollable size="lg" title="Riwayat Alergi">
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
    </div>
</template>

<script>
import { mapState } from 'vuex'
import moment from 'moment'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import CanvasContainer from '@/views/pages/canvasing/CanvasContainer.vue';
import vSelect from 'vue-select'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
export default {
    components: { ModernLabel, CanvasContainer, vSelect },
    props: {
        dataFromEdit: {},
    },
    data() {
        return {
            modal: {
                allergy: false,
            },
            allergyInput: {
                type_allergy_id: 0,
                name: '',
                reaction: '',
                severity: '',
                side_effect: '',
                date_tragedy: '',
            },
            getDataFromEdit:{},
            isDisabled: false,
            userData: JSON.parse(localStorage.getItem('userData')),
            regData: {}, nurseResume:{},
            modalShow: false,
            modal: {
                allergy: false,
                vital: false,
                antopometri: false,
                diagnosa: false,
            },
            active: {
                Tab: 'menu1',
                advance: {
                    antopometri: false,
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
            diagnosaTable: {
                fields: [
                    { key: 'name', label: 'Nama Obat/lainnya' },
                    { key: 'utama', label: 'Utama' },
                    { key: 'action', label: 'Aksi' },
                ],
                items: [
                    { name: 'A01.0 Typhoid Fever' },
                    { name: 'A01.2 Paratyphoid Fever B' },
                ],
            },
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
            OptionsPregnant:[
                { value: 1, title: 'Tunggal' },
                { value: 2, title: 'Kembar' }
            ],
            OptionsSex:[
                { value: 1, title: 'Laki-Laki' },
                { value: 0, title: 'Perempuan' }
            ],
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
            // OptionsClassificationBloodPressureType:[
            //     { value: 0, title: '-' },
            //     { value: 1, title: 'Normal' },
            //     { value: 2, title: 'Prephypertension' },
            //     { value: 3, title: 'Stage 1 HTN' },
            //     { value: 4, title: 'Stage 2 HTN' },
            // ],
            // OptionsClassificationOximetryType:[
            //     { value: 0, title: '-' },
            //     { value: 1, title: 'Normal' },
            //     { value: 2, title: 'Hipoksia Ringan Sampai Sedang' },
            //     { value: 3, title: 'Hipoksia Sedang Sampai Berat' },
            //     { value: 4, title: 'Hipoksia Berat Mengancam Jiwa' },
            // ],
            // OptionsClassificationTemperatureType:[
            //     { value: 0, title: '-' },
            //     { value: 1, title: 'Normal' },
            //     { value: 2, title: 'Febris' },
            //     { value: 3, title: 'Hiperpireksis' },
            //     { value: 4, title: 'Hipotermi' },
            //     { value: 5, title: 'Hipertermi' },
            //     { value: 6, title: 'Hipotensi' },
            // ],
            // OptionsClassificationWeightType:[
            //     { value: 1, title: 'BB Kurang' },
            //     { value: 2, title: 'Normal' },
            //     { value: 3, title: 'Febris' },
            //     { value: 4, title: 'BB Lebih (Overweight)' },
            //     { value: 5, title: 'Pre Obese' },
            //     { value: 6, title: 'Obesitas Tipe 1' },
            //     { value: 7, title: 'Obesitas Tipe 2' },
            //     { value: 8, title: 'Obesitas Tipe 3' },
            // ],
            form_soap: {
                id:'', 
                soap_s_id:'',
                soap_o_id:'',
                soap_a_id:'',
                soap_p_id:'',
                doctor_in_charge_id:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).doctor_in_charge_id : '',
                registration_destination_id:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).registration_destination_id : '',
                anamesa_type_id:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).anamesa_type_id : 1, 
                family_relation_id:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).family_relation_id : '',
                family_name:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).family_name : '',
                main_complaint:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).main_complaint : '',
                history_disease_now:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).history_disease_now : '',
                history_disease_past:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).history_disease_past : '',
                // history_tension:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).history_tension : '',
                history_drugs:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).history_drugs : '',
                systolic_blood_pressure_arry:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).systolic_blood_pressure_arry : [],
                general_state_arry:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).general_state_arry : [],
                systolic_blood_pressure:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).systolic_blood_pressure : '',
                diastolic_blood_pressure:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).diastolic_blood_pressure : '',
                classification_blood_pressure_type_id:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).classification_blood_pressure_type_id : '',
                pulse_frequency:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).pulse_frequency : '',
                breath_frequency:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).breath_frequency : '',
                oximetry_frequency:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).oximetry_frequency : '',
                classification_oximetry_type_id:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).classification_oximetry_type_id : '',
                temperature:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).temperature : '',
                classification_temperature_type_id:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).classification_temperature_type_id : '',
                blood_sugar:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).blood_sugar : '',
                been_hospitalized_type_id:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).been_hospitalized_type_id : '2',
                been_hospitalized_answer:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).been_hospitalized_answer : '',
                weight:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).weight : '',
                height:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).height : '',
                imt:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).imt : '',
                head_circumference:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).head_circumference : '',
                chest_circumference:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).chest_circumference : '',
                arm_circumference:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).arm_circumference : '',
                stomach_circumference:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).stomach_circumference : '',
                knee_circumference:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).knee_circumference : '',
                physical_check:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).physical_check : '',
                anythingelse:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).anythingelse : '',
                icd10:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).icd10 : [],
                icd10_notes: '',
                development_notes:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).development_notes : '',
                soap_a_anythingelse:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).soap_a_anythingelse : '',
                icd9cm:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).icd9cm : [],
                therapy_plan:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).therapy_plan : '',
                transaction_doctor:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).transaction_doctor : [],
                laboratory_request_items:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).laboratory_request_items : [],
                radiology_request_items:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).radiology_request_items : [],
                physiotherapy_request_items:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).physiotherapy_request_items : [],
                form_prescription_parent:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).form_prescription_parent : [],
                form_prescription:localStorage.getItem('soap') ? JSON.parse(localStorage.getItem('soap')).form_prescription : [],
                obgyn_diag_date:moment(new Date()).format('YYYY-MM-DD'),
                obgyn_diag_clinis:'',
                obgyn_diag_hpht: moment(new Date()).format('YYYY-MM-DD'),
                obgyn_s_d:1,
                obgyn_j_exam:'',
                obgyn_j_pos:'',
                obgyn_j_plas:'',
                obgyn_j_k:'',
                obgyn_j_sex:0,
                obgyn_j_weight:0,
                obgyn_size_usg:1,
                obgyn_usg_desc:'',
                obgyn_usg_file_url:'',obgyn_usg_file_set:'none',obgyn_usg_file:null,
                prescription: {},
                advice: '',
                // laboratory_clinical_indications: '',
                // radiology_clinical_indications: '',
                sore_score: '',
                ews: '',
                allergies: [],

            },
            costing_id:'',
            icd10_id:'',
            icd9cm_id:'',
            CheckConsult: ['poli', ],
            OptionsIcd10:[],
            OptionsIcd9cm:[],
            OptionsCatalog:[],
            catalog:'',
            prescription:'',
            prescription_type:{},
            prescription_name:'OBAT SATUAN',
            imageUSG:false,
            dateModal:moment(new Date()).format('YYYY/MM/DD HH:MM'),
            paramsInsuranceServiceClassLab:{},
            paramsInsuranceServiceClassDoctor:{},
            searchlab:'',
            searchrad:'',
            searchphy:'',
            supportPackage: {
                lab_package_list: [],
                rad_package_list: [],
            },
        }
    },
    computed: {
        ...mapState({ 
            itemsAllergy: state => state.patient.allergy.items, 
            fieldsAllergy: state => state.patient.allergy.fields, 
            OptionsCosting: state => state.costing.options.costing,
            itemsLaboratory: state => state.laboratory.package.items,
            PackageOptions: state => state.packages.options.packages,
            // CatalogOptions: state => state.catalog.options.catalog, 
            itemsRadiology: state => state.radiology.package.items,
            itemsPhysiotherapy: state => state.physiotherapy.package.items,
            OptionsTypeAllergy: state => state.helper.options.typeallergy,

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
            return this.supportPackage?.rad_package_list.map((rad) => {
                const filteredCategoryItems = rad?.category_items?.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchrad.toLowerCase())
                );

                return {
                    ...rad,
                    category_items: filteredCategoryItems,
                };
            }).filter(rad => rad?.category_items?.length > 0); 
        },
        filteredListPhy() {
            return this.itemsPhysiotherapy.filter((phy) => {
                // const computedPhyObj = { ...phy}
                const computedPhyObj = { 
                    costing_id:phy.costing_id,
                    costing_name:phy.costing_name,
                    id:'',
                    laboratory_package_id:phy.id,
                    var_1:phy.var_1
                }
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
        openModalAllergy(){
            this.modal.allergy = true
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
            this.modal.allergy = false
            this.resetAllergy()
        },
        removeAllergy(index) {
            this.form_soap.allergies.splice(index, 1)
        },
        saveChangedSoap($event){
            localStorage.setItem("soap", JSON.stringify(this.form_soap))
        },
        inputImageUSG (e) {
            this.imageUSG = true
            const file = e.target.files[0]
            const reader = new FileReader()
            reader.onload = (filez) => {
                this.form_soap.obgyn_usg_file = filez.target.result.replace("data:"+ file.type +";base64,", '');
            }
            reader.readAsDataURL(file)
            this.form_soap.obgyn_usg_file_set = 'upload'
            this.form_soap.obgyn_usg_file_url = URL.createObjectURL(e.target.files[0])
        },
        deletePRES (index) {
            this.form_soap.form_prescription.splice(index, 1)
        },
        onAddPRES (event) {
            // this.prescription = event.value
            this.prescription_type = event
            let index = this.form_soap.form_prescription_parent.findIndex((item) => item.id === event.value);
            // Check if the object with the specified property value exists in the array
            if (index === -1) {
                // If not found, push a new object with the desired properties
                this.form_soap.form_prescription_parent.push({
                    id:event.value,
                    prescription:event.title,
                    used:'',
                    value:0
                }) 
            } else {
                // If found, log a message indicating that the object already exists
                console.log("Object already exists");
            }
        },
        onAddCAT (event) {
            localStorage.setItem("soap", JSON.stringify(this.form_soap))
            this.catalog = ''
            this.collation = ''
            if (this.prescription === '') {
                alert("Pilih Racikan!");
            } else {
                this.form_soap.form_prescription.push(
                    { 
                        code:event.data_catalog.code,
                        name:event.data_catalog.name,
                        pharmacy_catalog_id:event.data_catalog.id, 
                        package_id:event.data_catalog.package_id,
                        dose:event.data_catalog.dose,
                        sum_dose:event.data_catalog.dose+' '+event.data_catalog.dose_name, 
                        package_name:event.data_catalog.package_name,
                        prescription_type_id:this.prescription_type.value,
                        prescription_type_name:this.prescription_type.title,
                        value_doctor:0,
                        value_package:0,
                        used:'',
                        desc:'',
                        sort:this.form_soap.form_prescription.length, 
                    }
                )
                console.log(this.form_soap.form_prescription)
            }
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
            console.log(value_prescription)
            let dose = this.form_soap.form_prescription[index].dose
            this.form_soap.form_prescription[index].value_package = this.$options.filters.numberToDecimal(value_doctor*value_prescription/dose)
        },
        onAdd10 (event) {
            localStorage.setItem("soap", JSON.stringify(this.form_soap))
            this.icd10_id = ''
            console.log(event)
            this.form_soap.icd10.push({ id:'', icd10_id:event.value, name:event.title, value:event.value, action: 'action'})
        },
        delete10 (index) {
            localStorage.setItem("soap", JSON.stringify(this.form_soap))
            this.form_soap.icd10.splice(index, 1)
        },
        onAdd9cm (event) {
            localStorage.setItem("soap", JSON.stringify(this.form_soap))
            this.icd9cm_id = ''
            console.log(event)
            this.form_soap.icd9cm.push({ icd9cm_id:event.value, name:event.title, value:event.value, action: 'action'})
        },
        delete9cm (index) {
            this.form_soap.icd9cm.splice(index, 1)
        },
        onAddSC (event) {
            localStorage.setItem("soap", JSON.stringify(this.form_soap))
            this.costing_id = ''
            this.collation = ''
            console.log(event)
            this.form_soap.transaction_doctor.push({ costing_id:event.costing_id, costing_name:event.title, value:event.value, var_1: event.costing, var_2:1,doctor_ref_id:'',notes:'', action: 'action'})
        },
        deleteSC (index) {
            this.form_soap.transaction_doctor.splice(index, 1)
        },
        validationForm () {
            this.isDisabled = true
            // this.$refs.simpleRules.validate()
            // .then(success => {
            //     if (success) {
                const formData = this.form_soap
                this.$store.dispatch('soap/formUpdate', formData)
                .then(res => {
                    localStorage.setItem("soap", '')
                    this.form_soap.id='' 
                    // this.form_soap.doctor_in_charge_id = ''
                    // this.form_soap.registration_destination_id = ''
                    this.form_soap.anamesa_type_id = 1 
                    this.form_soap.family_relation_id = ''
                    this.form_soap.family_name = ''
                    this.form_soap.main_complaint = ''
                    this.form_soap.history_disease_now = ''
                    this.form_soap.history_disease_past = ''
                    // this.form_soap.history_tension = ''
                    this.form_soap.history_drugs = ''
                    this.form_soap.systolic_blood_pressure_arry = []
                    this.form_soap.general_state_arry = []
                    this.form_soap.systolic_blood_pressure = ''
                    this.form_soap.diastolic_blood_pressure = ''
                    this.form_soap.classification_blood_pressure_type_id = ''
                    this.form_soap.pulse_frequency = ''
                    this.form_soap.breath_frequency = ''
                    this.form_soap.oximetry_frequency = ''
                    this.form_soap.classification_oximetry_type_id = ''
                    this.form_soap.temperature = ''
                    this.form_soap.classification_temperature_type_id = ''
                    this.form_soap.blood_sugar = ''
                    this.form_soap.been_hospitalized_type_id = '2'
                    this.form_soap.been_hospitalized_answer = ''
                    this.form_soap.weight = ''
                    this.form_soap.height = ''
                    this.form_soap.imt = ''
                    this.form_soap.head_circumference = ''
                    this.form_soap.chest_circumference = ''
                    this.form_soap.arm_circumference = ''
                    this.form_soap.stomach_circumference = ''
                    this.form_soap.knee_circumference = ''
                    this.form_soap.physical_check = ''
                    this.form_soap.anythingelse = ''
                    this.form_soap.icd10 = []
                    this.form_soap.icd10_notes = ''
                    this.form_soap.development_notes = ''
                    this.form_soap.soap_a_anythingelse = ''
                    this.form_soap.icd9cm = []
                    this.form_soap.therapy_plan = ''
                    this.form_soap.transaction_doctor = []
                    this.form_soap.laboratory_request_items=[]
                    this.form_soap.radiology_request_items=[]
                    this.form_soap.form_prescription_parent=[]
                    this.form_soap.form_prescription=[]
                    this.form_soap.obgyn_diag_date=moment(new Date()).format('YYYY-MM-DD')
                    this.form_soap.obgyn_diag_clinis=''
                    this.form_soap.obgyn_diag_hpht= moment(new Date()).format('YYYY-MM-DD')
                    this.form_soap.obgyn_s_d=1
                    this.form_soap.obgyn_j_exam=''
                    this.form_soap.obgyn_j_pos=''
                    this.form_soap.obgyn_j_plas=''
                    this.form_soap.obgyn_j_k=''
                    this.form_soap.obgyn_j_sex=0
                    this.form_soap.obgyn_j_weight=0
                    this.form_soap.obgyn_size_usg=1
                    this.form_soap.obgyn_usg_desc=''
                    this.form_soap.obgyn_usg_file_url=''
                    this.form_soap.obgyn_usg_file_set='none'
                    this.form_soap.obgyn_usg_file=null
                    this.modalShow = false
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.$store.dispatch('soap/getByRM', this.regData.norm)
                    this.isDisabled = false
                }, 
                err => {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    this.isDisabled = false
                })
                // } else {
                // this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                // }
            // })
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        getLabPackageListV2(){
            this.$store.dispatch('laboratory/getLaboratoryPackageV2').then((response) => {
                this.supportPackage.lab_package_list = response
            })
        },
        getRadPackageListV2(){
            this.$store.dispatch('radiology/getRadiologyPackageV2').then((response) => {
                this.supportPackage.rad_package_list = response
            })
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
        goToTab(tab) {
            this.active.Tab = tab;
        },
        getData (id) {
			this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]
                this.paramsInsuranceServiceClassLab = {
                    type_costing_id:4,
                    // company_id:this.regData.registration_insurance[0].company_id,
                    // service_class_id:this.regData.registration_insurance[0].service_class_id
                }
                this.paramsInsuranceServiceClassDoctor = {
                    type_costing_id:3,
                    health_facility_item_id:this.regData.health_facility_item_id
                    // company_id:this.regData.registration_insurance[0].company_id,
                    // service_class_id:this.regData.registration_insurance[0].service_class_id
                }
                // this.$store.dispatch('laboratory/getPackageAll', this.paramsInsuranceServiceClassLab)
                this.$store.dispatch('costing/getParam', this.paramsInsuranceServiceClassDoctor)
			    // this.$store.dispatch('patient/getAllergyByNorm', data[0].norm)
			    this.$store.dispatch('nurse/getAllResolve', id).then(data => {
                this.nurseResume = data[0]
                if (data[0]) {
                    this.form_soap.employee_nurse_id = data[0].employee_nurse_id
                    this.form_soap.registration_destination_id = data[0].registration_destination_id
                    this.form_soap.anamesa_type_id = data[0].anamesa_type_id
                    this.form_soap.family_relation_id = data[0].family_relation_id
                    this.form_soap.family_name = data[0].family_name
                    this.form_soap.main_complaint = data[0].main_complaint
                    this.form_soap.history_disease_now = data[0].history_disease_now
                    this.form_soap.history_disease_past = data[0].history_disease_past
                    this.form_soap.systolic_blood_pressure_arry = []
                    this.form_soap.general_state_arry = []
                    this.form_soap.systolic_blood_pressure = data[0].systolic_blood_pressure 
                    this.form_soap.diastolic_blood_pressure = data[0].diastolic_blood_pressure 
                    this.form_soap.classification_blood_pressure_type_id = data[0].classification_blood_pressure_type_id 
                    this.form_soap.pulse_frequency = data[0].pulse_frequency 
                    this.form_soap.breath_frequency = data[0].breath_frequency 
                    this.form_soap.oximetry_frequency = data[0].oximetry_frequency 
                    this.form_soap.classification_oximetry_type_id = data[0].classification_oximetry_type_id 
                    this.form_soap.temperature = data[0].temperature 
                    this.form_soap.classification_temperature_type_id = data[0].classification_temperature_type_id 
                    this.form_soap.blood_sugar = data[0].blood_sugar
                    this.form_soap.been_hospitalized_type_id = data[0].been_hospitalized_type_id
                    this.form_soap.been_hospitalized_answer = data[0].been_hospitalized_answer
                    this.form_soap.weight = data[0].weight
                    this.form_soap.height = data[0].height
                    this.form_soap.imt = data[0].imt
                    this.form_soap.head_circumference = data[0].head_circumference
                    this.form_soap.chest_circumference = data[0].chest_circumference
                    this.form_soap.arm_circumference = data[0].arm_circumference
                    this.form_soap.stomach_circumference = data[0].stomach_circumference
                    this.form_soap.knee_circumference = data[0].knee_circumference
                    this.form_soap.physical_check = data[0].physical_check
                    this.form_soap.anythingelse = data[0].anythingelse
                }
                })
                this.form_soap.doctor_in_charge_id = this.userData.employee_id
                this.form_soap.registration_destination_id = id
                // this.show = false
            })
        }
    },
    watch: {
        // 'itemsPhysiotherapy': function (val){
        //     console.log('this.itemsPhysiotherapy',val);
        // },
        dataFromEdit(data) {
            const value = data
            this.form_soap.id=value.id
            this.form_soap.soap_s_id=value.soap_s_id
            this.form_soap.soap_o_id=value.soap_o_id
            this.form_soap.soap_a_id=value.soap_a_id
            this.form_soap.soap_p_id=value.soap_p_id
            // this.form_soap.doctor_in_charge_id = value.doctor_in_charge_id
            // this.form_soap.registration_destination_id = ''
            this.form_soap.anamesa_type_id = value.anamesa_type_id 
            this.form_soap.family_relation_id = value.family_relation_id
            this.form_soap.family_name = value.family_name
            this.form_soap.main_complaint = value.main_complaint
            this.form_soap.history_disease_now = value.history_disease_now
            this.form_soap.history_disease_past = value.history_disease_past
            // this.form_soap.history_tension = ''
            this.form_soap.history_drugs = value.history_drugs
            this.form_soap.systolic_blood_pressure_arry = value.systolic_blood_pressure_arry
            this.form_soap.general_state_arry = value.general_state_arry
            this.form_soap.systolic_blood_pressure = value.systolic_blood_pressure
            this.form_soap.diastolic_blood_pressure = value.diastolic_blood_pressure
            this.form_soap.classification_blood_pressure_type_id = value.classification_blood_pressure_type_id
            this.form_soap.pulse_frequency = value.pulse_frequency
            this.form_soap.breath_frequency = value.breath_frequency
            this.form_soap.oximetry_frequency = value.oximetry_frequency
            this.form_soap.classification_oximetry_type_id = value.classification_oximetry_type_id
            this.form_soap.temperature = value.temperature
            this.form_soap.classification_temperature_type_id = value.classification_temperature_type_id
            this.form_soap.blood_sugar = value.blood_sugar
            this.form_soap.been_hospitalized_type_id = value.been_hospitalized_type_id
            this.form_soap.been_hospitalized_answer = value.been_hospitalized_answer
            this.form_soap.weight = value.weight
            this.form_soap.height = value.height
            this.form_soap.imt = value.imt
            this.form_soap.head_circumference = value.head_circumference
            this.form_soap.chest_circumference = value.chest_circumference
            this.form_soap.arm_circumference = value.arm_circumference
            this.form_soap.stomach_circumference = value.stomach_circumference
            this.form_soap.knee_circumference = value.knee_circumference
            this.form_soap.physical_check = value.physical_check
            this.form_soap.anythingelse = value.anythingelse
            this.form_soap.icd10 = value.icd10 ? value.icd10 : []
            this.form_soap.icd10_notes = value.icd10_notes ? value.icd10_notes : ''
            this.form_soap.development_notes = value.development_notes
            this.form_soap.soap_a_anythingelse = value.soap_a_anythingelse
            this.form_soap.icd9cm = value.icd9cm ? value.icd9cm : []
            this.form_soap.therapy_plan = value.therapy_plan
            this.form_soap.transaction_doctor = value.transaction_doctor ? value.transaction_doctor : [] 
            this.form_soap.laboratory_request = value.laboratory_request ? value.laboratory_request : []
            // this.form_soap.laboratory_clinical_indications = value.laboratory_request ? value.laboratory_request.clinical_indications : ''
            this.form_soap.laboratory_request_items = value.laboratory_request_items ? value.laboratory_request_items : []
            this.form_soap.radiology_request = value.radiology_request ? value.radiology_request : []
            // this.form_soap.radiology_clinical_indications = value.radiology_request ? value.radiology_request.clinical_indications : ''
            this.form_soap.radiology_request_items = value.radiology_request_items ? value.radiology_request_items : []
            this.form_soap.physiotherapy_request_items = value.physiotherapy_request ? value.physiotherapy_request : []
            this.form_soap.advice = value.advice ? value.advice : '' 
            this.form_soap.prescription = value.prescription ? value.prescription : {}
            let prescription_type = {value:1,title:'OBAT SATUAN'}
            this.prescription = prescription_type
            this.prescription_type = prescription_type
            this.prescription_name = prescription_type.title
            this.form_soap.form_prescription_parent = []
            this.form_soap.form_prescription_parent.push({
                id:1,
                prescription:'Obat Satuan',
                used:'',
                value:0
            }) 
            this.form_soap.form_prescription = []
            if (value.form_prescription) {
                for (let index = 0; index < value.form_prescription.length; index++) {
                    this.form_soap.form_prescription.push({ 
                        code:value.form_prescription[index].pharmacy_catalog_code,
                        name:value.form_prescription[index].pharmacy_catalog_name,
                        pharmacy_prescription_id:value.form_prescription[index].pharmacy_prescription_id, 
                        pharmacy_catalog_id:value.form_prescription[index].pharmacy_catalog_id, 
                        package_id:value.form_prescription[index].package_id,
                        dose:value.form_prescription[index].dose,
                        sum_dose:value.form_prescription[index].dose+' '+value.form_prescription[index].dose_name, 
                        package_name:value.form_prescription[index].package_name,
                        prescription_type_id:this.prescription_type.value,
                        prescription_type_name:this.prescription_type.title,
                        value_doctor:value.form_prescription[index].qty_doctor,
                        value_package:value.form_prescription[index].qty,
                        used:value.form_prescription[index].used,
                        desc:value.form_prescription[index].desc,
                        sort:value.form_prescription[index].sort, 
                    })
                }
                console.log(this.form_soap.form_prescription)
            }
            this.form_soap.obgyn_diag_date = value.obgyn_diag_date
            this.form_soap.obgyn_diag_clinis = value.obgyn_diag_clinis
            this.form_soap.obgyn_diag_hpht = value.obgyn_diag_hpht
            this.form_soap.obgyn_s_d = value.obgyn_s_d
            this.form_soap.obgyn_j_exam = value.obgyn_j_exam
            this.form_soap.obgyn_j_pos = value.obgyn_j_pos
            this.form_soap.obgyn_j_plas = value.obgyn_j_plas
            this.form_soap.obgyn_j_k = value.obgyn_j_k
            this.form_soap.obgyn_j_sex = value.obgyn_j_sex 
            this.form_soap.obgyn_j_weight = value.obgyn_j_weight 
            this.form_soap.obgyn_size_usg = value.obgyn_size_usg 
            this.form_soap.obgyn_usg_desc = value.obgyn_usg_desc
            this.form_soap.obgyn_usg_file_url = value.obgyn_usg_file_url
            this.form_soap.obgyn_usg_file_set = value.obgyn_usg_file_set
            this.form_soap.obgyn_usg_file = value.obgyn_usg_file
            this.form_soap.sore_score = value.sore_score
            this.form_soap.ews = value.ews
            this.form_soap.allergies = value.allergies
            this.modalShow = true
            if (this.regData.health_facility_id === '444b3134-5b75-4b67-af72-a558d46716d7') {
                //INSTALASI IGD
                this.form_soap.pharmacy_depo_id = '4105d8bc-5769-463e-bc67-aae2fc5adf10'
            } else {
                //INSTALASI FARMASI
                this.form_soap.pharmacy_depo_id = '297c44f0-12ef-40e6-b9a7-c831065b3372'
            }
        }
    },
    mounted () {
        if (this.$route.params.id) {
            // this.show = true
            this.id = this.$route.params.id
            this.getData(this.$route.params.id)
        }
    },
    created () {
        // this.$store.dispatch('radiology/getPackageAll')
        this.$store.dispatch('physiotherapy/getPackageAll')
        this.$store.dispatch('catalog/getParam')
        this.$store.dispatch('packages/getParam')

        this.getLabPackageListV2()
        this.getRadPackageListV2()
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